<?php
namespace Module\Extensions\Controllers;

use Carbon\Traits\Date;
use Defuse\Crypto\File;
use IjCore\IjString;
use IjSmartBooks\IjCore\IjFunction\Number;
use Illuminate\Http\Request;
use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
use Maatwebsite\Excel\Facades\Excel;
use Module\Extensions\Models\ChatContent;
use Module\Extensions\Models\ChatContentFile;
use Module\Extensions\Models\ChatContentTag;
use Module\Extensions\Models\ChatContentUserRead;
use Module\Extensions\Models\ChatGroup;
use Module\Extensions\Models\ChatMember;
use Module\Extensions\Models\ChatTag;
use Module\Listing\Models\Company;
use Module\Listing\Models\Employee;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysStatusItem;
use Module\SysAdmin\Models\SysTable;
use Module\SysAdmin\Models\SysUser;
use Illuminate\Support\Facades\Validator;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Task\Controllers\TaskDataflowController;
use Module\Task\Models\FileModel;
use Module\Task\Models\Task;
use Module\Task\Models\TaskAssign;
use Module\Task\Models\TaskDataflow;
use Module\Task\Models\TaskPer;
use function GuzzleHttp\Psr7\_parse_request_uri;

class ChatController extends Controller {

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $view = ($request->post('view')) ? $request->post('view') : 'classic';

        // Lấy tất cả nhóm mà user tham gia
        $user = Auth::user();
//        $groups = ChatGroup::select('chat_group.*', 'chat_content.LineID')
//            ->join('chat_member', 'chat_group.GroupID', '=', 'chat_member.GroupID')
//            ->join('chat_content', 'chat_group.GroupID', '=', 'chat_content.GroupID')
//            ->where('chat_member.UserID', '=', $user->UserID)
//            ->where('chat_group.Deleted', '!=', 1)
//            ->orderBy('chat_content.LineID', 'desc')
//            ->groupBy('chat_group.GroupID')
//            ->get();

        $groups = ChatGroup::select('chat_group.*', 'tmp.LineID')
            ->join(DB::raw("
                (SELECT MAX(chat_content.LineID) AS LineID, chat_group.GroupID
                FROM chat_group
                INNER JOIN chat_member ON chat_member.GroupID = chat_group.GroupID
                LEFT JOIN chat_content ON chat_content.GroupID = chat_group.GroupID
                WHERE chat_member.UserID = $user->UserID
                AND chat_group.Deleted != 1
                AND chat_group.HideGroupChat = 0
                GROUP BY chat_group.GroupID) AS tmp"), function ($join){
                $join->on('chat_group.GroupID', '=', 'tmp.GroupID');
            })
            ->where('chat_group.Deleted', '!=', 1)
            ->where('chat_group.HideGroupChat', '=', 0)
            ->orderBy('tmp.LineID', 'DESC')
            ->get();


        $json['data']['AllGroups'] = $groups;

        $groupIDs = [];
        foreach ($groups as $group) {
            $groupIDs[] = $group->GroupID;
        }
        if (count($groupIDs)) {
            // chat last message
            $lastMessageQuery = ChatContent::query();
            $lastMessageQuery->select('chat_content.*')
                ->whereIn('chat_content.LineID', function ($q) use ($groupIDs) {
                    $q->select(DB::raw('MAX(LineID)'))
                        ->from('chat_content')
                        ->whereIn('GroupID', $groupIDs)
                        ->where('chat_content.Deleted', '!=', 1)
                        ->groupBy('GroupID');
                });
            $lastMessage = $lastMessageQuery->get();
            $json['data']['LastMessageGroup'] = $lastMessage;

            // chat content user read
            $messageIDs = [];
            foreach ($lastMessage as $message) {
                $messageIDs[] = $message->LineID;
            }
            if (count($messageIDs)) {
                $userRead = ChatContentUserRead::whereIn('ChatContentID', $messageIDs)->get();
                $json['data']['UserRead'] = $userRead;
            }

            // chat member in group
            $membersInGroup = ChatMember::select('chat_member.*', 'chat_group.GroupType', 'sys_user.Avata')
                ->join('chat_group', 'chat_member.GroupID', '=', 'chat_group.GroupID')
                ->leftJoin('sys_user', 'sys_user.UserID', '=', 'chat_member.UserID')
                ->whereIn('chat_member.GroupID', $groupIDs)
                ->get();
            $json['data']['MembersInGroup'] = $membersInGroup;
        }

        $allUsers = SysUser::where('UserID', '!=', $user->UserID)->where('Inactive', '!=', 1)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $allUsers;
        $json['data']['CurrentUser'] = $user;
        return json_encode($json);
    }
    public function loadMessage(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $data = ChatContent::search($request);
        // content file
        $contents = $data->items();
        $contentIDs = [];
        $contentParentIDs = [];
        $categoryIDArr = [];
        foreach ($contents as $content) {
            $contentIDs[] = $content->LineID;
            if ($content->ParentID) {
                $contentParentIDs[] = $content->ParentID;
            }

            if ($content->CategoryKey) {
                $categories = explode('_', $content->CategoryKey);
                foreach ($categories as $category) {
                    $pieces = explode(':', $category);
                    $table = $pieces[0];
                    $tableID = $pieces[1];
                    if ($table !== 'dataflow') {
                        $categoryIDArr[$table][] = $tableID;
                    }
                }
            }
        }

        $datalist = [];
        if (count($categoryIDArr)) {
            foreach ($categoryIDArr as $table => $categoryID) {
                if ($table !== 'tag') {
                    $query = DB::table($table);
                    $tableName = ucfirst($table);
                    if ($table == 'task' || $table == 'Task') {
                        $query->select('TaskID', 'TaskNo', 'TaskName', 'Status', 'StatusID',
                            'StatusName', 'StatusValue', 'StatusDescription', 'Type', 'ParentID', 'ParentNo', 'ParentName');
                    }else{
                        $query->select($tableName . 'ID', $tableName . 'No', $tableName . 'Name');
                    }
                    $query->whereIn($tableName . 'ID', $categoryID);
                    $records = $query->get();
                    $datalist[$table] = $records;
                } else {
                    $datalist[$table] = $categoryID;
                }
            }
        }

        $contentFile = ChatContentFile::whereIn('ChatContentID', $contentIDs)->get();
        $contentParent = ChatContent::whereIn('LineID', $contentParentIDs)->get();
        $contentFileParent = ChatContentFile::whereIn('ChatContentID', $contentParentIDs)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['ContentFile'] = $contentFile;
        $json['data']['ContentParent'] = $contentParent;
        $json['data']['ContentFileParent'] = $contentFileParent;
        $json['data']['Datalist'] = $datalist;

        return json_encode($json);
    }
    public function readMessage(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null,
        ];
        $groupID = $request->post('GroupID');
        $results = $this->storeUserRead($groupID);
        if ($results['status']) {
            $json['status'] = $results['status'];
            $json['msg'] = 'Thành công';
            $json['data'] = $results['data'];
        }

        return json_encode($json);
    }
    public function storeUserRead($groupID){
        $response = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $lastMessage = ChatContent::select('chat_content.*')
            ->where('chat_content.LineID', function ($q) use ($groupID) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('chat_content')
                    ->where('GroupID', '=', $groupID)
                    ->where('Deleted', '!=', 1)
                    ->groupBy('GroupID');
            })->first();
        if ($lastMessage) {
            $userReadExists = ChatContentUserRead::where([
                'ChatContentID' => $lastMessage->LineID,
                'UserID' => Auth::user()->UserID
            ])->exists();

            if (!$userReadExists) {
                $user = SysUser::select('sys_user.*', 'employee.EmployeeName')
                    ->leftJoin('employee', 'sys_user.UserID', '=', 'employee.UserID')
                    ->where('sys_user.UserID', '=', Auth::user()->UserID)
                    ->first();
                $userRead = new ChatContentUserRead([
                    'ChatContentID' => $lastMessage->LineID,
                    'UserID' => $user->UserID,
                    'username' => $user->username,
                    'EmployeeID' => $user->EmployeeUD,
                    'EmployeeName' => $user->EmployeeName,
                    'ReadDate' => date('Y-m-d H:i:s')
                ]);
                $userRead->save();

                $response['status'] = 1;
                $response['msg'] = 'Chưa tồn tại';
                $response['data'] = $userRead;
                return $response;
//                return $userRead;
            } else {
                $userRead = ChatContentUserRead::where([
                    'ChatContentID' => $lastMessage->LineID,
                    'UserID' => Auth::user()->UserID
                ])->first();

                $response['status'] = 2;
                $response['msg'] = 'Đã tồn tại';
                $response['data'] = $userRead;
                return $response;
//                return $userRead;
            }
        }
        return $response;
    }
    public function storeMessage(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $auth = Auth::user();
        $GroupID = (int) $request->post('GroupID');
        $message = ($request->post('message')) ? $request->post('message') : '';
        $GroupType = $request->post('GroupType');
        $ParentID = $request->post('ParentID');
        $files = $request->file('Files');
        $typeStore = ($request->has('TypeStore')) ? $request->post('TypeStore') : 'default';
        $GroupName = $request->post('GroupName');

        $tags = [];
        if (strpos($message, '#') !== false) {
            $pieces = explode(' ', $message);
            foreach ($pieces as $piece) {
                if (strpos($piece,'#') !== false) {
                    $tags[] = $piece;
                }
            }
        }

        if ($typeStore == 'default') {
            if ($GroupID) {
                $group = ChatGroup::find($GroupID);
                $content = new ChatContent([
                    'GroupID' => $GroupID,
                    'UserName' => $auth->FullName,
                    'UserID' => $auth->UserID,
                    'CreatedDate' => date('Y-m-d H:i:s'),
                    'UpdatedDate' => date('Y-m-d H:i:s'),
                    'Content' => $message,
                    'ParentID' => ($ParentID) ? $ParentID : 0
                ]);
                $content->save();
                if ($group->CategoryKey) {
                    $content->CategoryKey = $group->CategoryKey;
                    $content->save();
                }
                $content->Avata = $auth->Avata;
                $content->GroupType = $group->GroupType;
                $contentFiles = $this->storeContentFile($files, $content->LineID);
                $this->storeTags($tags, $content->LineID);

                $json['status'] = 1;
                $json['msg'] = 'Thành công';
                $json['data']['data'] = $content;
                $json['data']['Group'] = $group;
                $json['data']['ContentFile'] = $contentFiles;

            }else{
                if ($GroupType == 1) {
                    $UserID = $request->post('UserID');
                    $UserName = $request->post('UserName');

                    // kiem tra da ton tai group
                    $groupExists = ChatMember::join('chat_group', 'chat_group.GroupID', '=', 'chat_member.GroupID')
                        ->whereIn('chat_member.GroupID', function ($q) use ($auth) {
                            $q->select('GroupID')
                                ->from('chat_member')
                                ->where('UserID', '=', $auth->UserID);})
                        ->where('UserID', '=', $UserID)
                        ->where('GroupType', '=', 1)
                        ->first();

                    if (!$groupExists) {
                        // create new group
                        $group = new ChatGroup([
                            'GroupName' => 'Private',
                            'GroupDescription' => 'Private',
                            'GroupType' => $GroupType,
                            'Created' => $auth->UserID,
                            'CreatedDate' => date('Y-m-d H:i:s'),
                            'UpdatedDate' => date('Y-m-d H:i:s')
                        ]);
                        $group->save();

                        // create chat member
                        $member1 = new ChatMember([
                            'UserName' => $auth->FullName,
                            'UserID' => $auth->UserID,
                            'GroupID' => $group->GroupID,
                            'DateJoin' => date('Y-m-d H:i:s')
                        ]);
                        $member1->save();

                        $member2 = new ChatMember([
                            'UserName' => $UserName,
                            'UserID' => $UserID,
                            'GroupID' => $group->GroupID,
                            'DateJoin' => date('Y-m-d H:i:s')
                        ]);
                        $member2->save();

                        // create content
                        $content = new ChatContent([
                            'GroupID' => $group->GroupID,
                            'UserName' => $auth->FullName,
                            'UserID' => $auth->UserID,
                            'CreatedDate' => date('Y-m-d H:i:s'),
                            'UpdatedDate' => date('Y-m-d H:i:s'),
                            'Content' => $message,
                            'ParentID' => ($ParentID) ? $ParentID : 0
                        ]);
                        $content->save();
                        $content->GroupType = $group->GroupType;
                        $content->Avata = $auth->Avata;

                        $contentFiles = $this->storeContentFile($files, $content->LineID);
                        $this->storeTags($tags, $content->LineID);

                        $json['status'] = 1;
                        $json['msg'] = 'Thành công';
                        $json['data']['data'] = $content;
                        $json['data']['Group'] = $group;
                        $json['data']['ContentFile'] = $contentFiles;
                    } else {
                        // create content
                        $content = new ChatContent([
                            'GroupID' => $GroupID,
                            'UserName' => $auth->FullName,
                            'UserID' => $auth->UserID,
                            'CreatedDate' => date('Y-m-d H:i:s'),
                            'UpdatedDate' => date('Y-m-d H:i:s'),
                            'Content' => $message,
                            'ParentID' => ($ParentID) ? $ParentID : 0
                        ]);
                        $content->save();
                        $content->Avata = $auth->Avata;
                        $content->GroupType = $groupExists->GroupType;

                        $contentFiles = $this->storeContentFile($files, $content->LineID);
                        $this->storeTags($tags, $content->LineID);

                        $json['status'] = 1;
                        $json['msg'] = 'Thành công';
                        $json['data']['data'] = $content;
                        $json['data']['ContentFile'] = $contentFiles;
                    }
                }
            }
        }elseif ($typeStore == 'social'){
            $content = new ChatContent([
                'GroupID' => 0,
                'UserName' => $auth->FullName,
                'UserID' => $auth->UserID,
                'CreatedDate' => date('Y-m-d H:i:s'),
                'UpdatedDate' => date('Y-m-d H:i:s'),
                'Content' => $message,
                'ParentID' => ($ParentID) ? $ParentID : 0
            ]);
            $content->save();
            $content->Avata = $auth->Avata;
            $contentFiles = $this->storeContentFile($files, $content->LineID);
            $this->storeTags($tags, $content->LineID);

            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data']['data'] = $content;
            $json['data']['ContentFile'] = $contentFiles;
        } elseif ($typeStore == 'category') {

            $CategoryKey = json_decode($request->post('CategoryKey'), true);
            $Category = $request->post('Category');
            $CategoryID = $request->post('CategoryID');

            $task = null;
            $taskDataflow = null;
            $CategoryKey = ChatContent::getCategoryKey($CategoryKey);
            $GroupCategoryKey = $CategoryKey;
            if ($Category === 'task') {
                $task = Task::find($CategoryID);
                // is dataflow
                if ($task && $task->Type == 2) {
                    $taskDataflow = TaskDataflow::where([
                        'TableName' => 'task',
                        'ConstraintFieldValue' => $CategoryID
                    ])->first();

                    if ($taskDataflow->DFID != $taskDataflow->DFKey) {
                        $GroupCategoryKey = 'task:' . $task->ParentID;
                        $CategoryKey = 'task:' . $task->ParentID . '_' . $CategoryKey;
                    }
                }
            }
            $groupExist = ChatGroup::where([
                'GroupType' => 3,
                'CategoryKey' => $GroupCategoryKey
            ])->first();

            if ($groupExist) {
                // insert content
                $content = new ChatContent([
                    'GroupID' => $groupExist->GroupID,
                    'UserName' => $auth->FullName,
                    'UserID' => $auth->UserID,
                    'CategoryKey' => $CategoryKey,
                    'CreatedDate' => date('Y-m-d H:i:s'),
                    'UpdatedDate' => date('Y-m-d H:i:s'),
                    'Content' => $message,
                    'ParentID' => ($ParentID) ? $ParentID : 0
                ]);
                $content->save();
                $content->Avata = $auth->Avata;
                $content->GroupType = 3;

                $contentFiles = $this->storeContentFile($files, $content->LineID);

                if ($task) {
                    if ($task->Type == 2 && !$taskDataflow) {
                        $usersQuery = TaskAssign::query();
                        $usersQuery->select('employee.*')
                            ->join('employee', 'employee.EmployeeID', '=', 'task_assign.EmployeeID')
                            ->whereIn('task_assign.TaskID', function ($q) use ($taskDataflow){
                                $q->select('ConstraintFieldValue')
                                    ->from('task_dataflow')
                                    ->where('DFKey', $taskDataflow->DFKey);
                            })
                            ->groupBy('employee.EmployeeID');
                        $usersAssign = $usersQuery->get();
                        $json['data']['IsDataflow'] = 1;
                        $json['data']['Members'] = $usersAssign;
                    }
                }else{
                    $employee = Employee::select('EmployeeID', 'UserID', 'EmployeeName', 'EmployeeNo')->where('Inactive', '!=', 1)->get();
                    $json['data']['HideGroupChat'] = 1;
                    $json['data']['Members'] = $employee;
                }

                $json['status'] = 1;
                $json['msg'] = 'Thành công';
                $json['data']['data'] = $content;
                $json['data']['ContentFile'] = $contentFiles;

            }else{
                if ($Category == 'task') {
                    if ($task && $task->Type != 2) {
                        $GroupName = $task->TaskName;
                    }else{
                        $dataflow = Task::join('task_dataflow', 'task.TaskID', '=', 'task_dataflow.ConstraintFieldValue')
                            ->where([
                                'TableName' => 'task',
                                'DFKey' => $taskDataflow->DFKey
                            ])
                            ->whereColumn('task_dataflow.DFID', 'task_dataflow.DFKey')
                            ->first();
                        $GroupName = $dataflow->TaskName;
//                        $json['data']['IsDataflow'] = 1;
                    }

                    // insert group
                    $group = new ChatGroup([
                        'GroupName' => $GroupName,
                        'GroupDescription' => 'Nhóm công việc có gắn với đối tượng',
                        'GroupType' => 3,
                        'CategoryKey' => $GroupCategoryKey,
                        'Created' => $auth->UserID,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'UpdatedDate' => date('Y-m-d H:i:s')
                    ]);
                    $group->save();

                    // insert members
                    $allUsers = [];
                    if ($task->Type != 2) {
                        if ($task->AccessType == 2) {
                            if($task->PublicCompanyID){
                                $allUserIDs = [];

                                $usersQuery = TaskAssign::query();
                                $usersQuery->select('employee.*')
                                    ->join('employee', 'employee.EmployeeID', '=', 'task_assign.EmployeeID')
                                    ->where(['task_assign.TaskID' => $CategoryID])
                                    ->groupBy('employee.EmployeeID');
                                $usersAssign = $usersQuery->get();

                                foreach ($usersAssign as $user) {
                                    $allUsers[] = $user;
                                    $allUserIDs[] = $user->UserID;
                                }

                                $arrCompanyID = [$task->PublicCompanyID];
                                $allChildCompany = Company::getAllChildCompany($arrCompanyID);
                                foreach ($allChildCompany as $company) {
                                    $arrCompanyID[] = $company->CompanyID;
                                }

                                $usersCompany = Employee::whereIn('CompanyID', $arrCompanyID)->get();
                                foreach ($usersCompany as $user) {
                                    if (!in_array($user->UserID, $allUserIDs)) {
                                        $allUsers[] = $user;
                                    }
                                }
                            }else{
                                $allUsers = Employee::all();
                            }
                        } elseif ($task->AccessType == 1) {
                            $usersQuery = TaskAssign::query();
                            $usersQuery->select('employee.*')
                                ->join('employee', 'employee.EmployeeID', '=', 'task_assign.EmployeeID')
                                ->where(['task_assign.TaskID' => $CategoryID])
                                ->groupBy('employee.EmployeeID');
                            $allUsers = $usersQuery->get();
                        } elseif ($task->AccessType == 3) {
                            $allUsers[] = Employee::where(['UserID' => $auth->UserID])->first();
                        }
                    }else{
                        // dataflow
                        $usersQuery = TaskAssign::query();
                        $usersQuery->select('employee.*')
                            ->join('employee', 'employee.EmployeeID', '=', 'task_assign.EmployeeID')
                            ->whereIn('task_assign.TaskID', function ($q) use ($taskDataflow) {
                                $q->select('ConstraintFieldValue')
                                    ->from('task_dataflow')
                                    ->where('DFKey', $taskDataflow->DFKey);
                            })
                            ->groupBy('employee.EmployeeID');
                        $allUsers = $usersQuery->get();
                    }

                    $insertArr = [];
                    $insertArr[] = [
                        'UserName' => $auth->FullName,
                        'UserID' => $auth->UserID,
                        'GroupID' => $group->GroupID,
                        'DateJoin' => date('Y-m-d H:i:s'),
                        'Type' => 1
                    ];
                    foreach ($allUsers as $user) {
                        if ($user->UserID != $auth->UserID) {
                            $insertArr[] = [
                                'UserName' => $user->EmployeeName,
                                'UserID' => $user->UserID,
                                'GroupID' => $group->GroupID,
                                'DateJoin' => date('Y-m-d H:i:s'),
                                'Type' => 2
                            ];
                        }
                    }

                    DB::table('chat_member')->insert($insertArr);

                    // insert content
                    $content = new ChatContent([
                        'GroupID' => $group->GroupID,
                        'UserName' => $auth->FullName,
                        'UserID' => $auth->UserID,
                        'CategoryKey' => $CategoryKey,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'UpdatedDate' => date('Y-m-d H:i:s'),
                        'Content' => $message,
                        'ParentID' => ($ParentID) ? $ParentID : 0
                    ]);
                    $content->save();
                    $content->Avata = $auth->Avata;
                    $content->GroupType = 3;

                    $contentFiles = $this->storeContentFile($files, $content->LineID);

                    $membersInGroup = ChatMember::select('chat_member.*', 'chat_group.GroupType', 'sys_user.Avata')
                        ->join('chat_group', 'chat_member.GroupID', '=', 'chat_group.GroupID')
                        ->leftJoin('sys_user', 'sys_user.UserID', '=', 'chat_member.UserID')
                        ->where('chat_member.GroupID', '=', $group->GroupID)
                        ->get();

                    $json['status'] = 1;
                    $json['msg'] = 'Thành công';
                    $json['data']['data'] = $content;
                    $json['data']['ContentFile'] = $contentFiles;
                    $json['data']['Group'] = $group;
                    $json['data']['Members'] = $membersInGroup;
                } else {

                    // insert group
                    $group = new ChatGroup([
                        'GroupName' => $GroupName,
                        'GroupDescription' => 'Nhóm công việc có gắn với đối tượng',
                        'GroupType' => 3,
                        'CategoryKey' => $GroupCategoryKey,
                        'Created' => $auth->UserID,
                        'HideGroupChat' => 1,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'UpdatedDate' => date('Y-m-d H:i:s')
                    ]);
                    $group->save();

                    // insert member
                    $member = new ChatMember([
                        'UserName' => $auth->FullName,
                        'UserID' => $auth->UserID,
                        'GroupID' => $group->GroupID,
                        'DateJoin' => date('Y-m-d H:i:s'),
                        'Type' => 1
                    ]);
                    $member->save();

                    // insert content
                    $content = new ChatContent([
                        'GroupID' => $group->GroupID,
                        'UserName' => $auth->FullName,
                        'UserID' => $auth->UserID,
                        'CategoryKey' => $CategoryKey,
                        'CreatedDate' => date('Y-m-d H:i:s'),
                        'UpdatedDate' => date('Y-m-d H:i:s'),
                        'Content' => $message,
                        'ParentID' => ($ParentID) ? $ParentID : 0
                    ]);
                    $content->save();
                    $content->Avata = $auth->Avata;
                    $content->GroupType = 3;

                    $contentFiles = $this->storeContentFile($files, $content->LineID);
                    $membersInGroup = Employee::select('EmployeeID', 'UserID', 'EmployeeName', 'EmployeeNo')->where('Inactive', '!=', 1)->get();

                    $json['status'] = 1;
                    $json['msg'] = 'Thành công';
                    $json['data']['data'] = $content;
                    $json['data']['ContentFile'] = $contentFiles;
                    $json['data']['Group'] = $group;
                    $json['data']['Members'] = $membersInGroup;
                    $json['data']['HideGroupChat'] = 1;

                }
            }
        }

        // update date of parent
        if ($ParentID) {
            $this->updateDateParent($ParentID);
            $ContentParent = ChatContent::find($ParentID);
            $json['data']['ContentParent'] = $ContentParent;
        }
        return json_encode($json);
    }
    public function updateMessage(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => []
        ];
        $auth = Auth::user();
        $message = ($request->post('message')) ? $request->post('message') : '';
        $LineID = $request->post('LineID');
        $files = $request->file('Files');

        if (!$LineID) {
            return json_encode($json);
        }

        $tags = [];
        if (strpos($message, '#') !== false) {
            $pieces = explode(' ', $message);
            foreach ($pieces as $piece) {
                if (strpos($piece,'#') !== false) {
                    $tags[] = $piece;
                }
            }
        }

        $content = ChatContent::find($LineID);
        if ($content) {
            $content->Content = $message;
            $content->UpdatedDate = date('Y-m-d H:i:s');
            $content->save();
            $this->storeTags($tags, $content->LineID);
            $contentFiles = $this->storeContentFile($files, $content->LineID);
            $json['data']['ContentFile'] = $contentFiles;
        }

        // update date of parent
        if ($content->ParentID) {
            $this->updateDateParent($content->ParentID);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $content;
        return json_encode($json);

    }
    public function updateDateParent($ParentID){
        if ($ParentID) {
            $contentParent = ChatContent::find($ParentID);
            if ($contentParent) {
                $contentParent->UpdatedDate = date('Y-m-d H:i:s');
                $contentParent->save();
                $this->updateDateParent($contentParent->ParentID);
            }
        }

        return;
    }
    public function storeContentFile($files, $ContentID){
        /**
         *  FileType: 1: image, 2: file, 3: audio, 4: video
         *
         */
        if ($files && count($files)) {

            $contentFileExists = ChatContentFile::where(['ChatContentID' => $ContentID])->get();
            if (count($contentFileExists)) {
                $fileIDs = [];
                foreach ($contentFileExists as $contentFile) {
                    $fileIDs[] = $contentFile->FileID;
                    unlink(public_path() . $contentFile->FieldAttach);
                }
                DB::table('file')->whereIn('FileID', $fileIDs)->delete();
                DB::table('chat_content_file')->where(['ChatContentID' => $ContentID])->delete();
            }

            foreach ($files as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                $size = $file->getSize();
                $name = $file->getClientOriginalName();
                if($size <= 400000000){
                    $arr_ext_img = ["gif", "jpeg", "jpg", "png", "ico", "psd", "ai"];
                    $arr_ext_doc = ["pptx", "ppt", "pps", "xls", "xlsx", "csv", "doc", "docx", "pdf", "txt", "zip", "rar", "cif"];
                    $arr_ext_audio = ['pcm', 'wav', 'aiff', 'mp3', 'aac', 'ogg', 'wma', 'flac', 'alac', 'wma'];
                    $arr_ext_video = ['webm', 'mpg', 'mp2', 'mpeg', 'mpe', 'mpv', 'ogg', 'mp4', 'm4p', 'm4v', 'avi', 'wmv', 'mov', 'qt', 'flv', 'swf', 'avchd', 'm4a'];
                    $is_doc = in_array($ext, $arr_ext_doc);
                    $is_img = in_array($ext, $arr_ext_img);
                    $is_audio = in_array($ext, $arr_ext_audio);
                    $is_video = in_array($ext, $arr_ext_video);
                    $fileType = 0;

                    if ($is_img) {
                        $fileType = 1;
                    } elseif ($is_doc) {
                        $fileType = 2;
                    } elseif ($is_audio) {
                        $fileType = 3;
                    } elseif ($is_video) {
                        $fileType = 4;
                    }

                    if($is_doc || $is_img || $is_audio || $is_video){
                        $nameFile = Auth::user()->username . '_' . (date('YmdHis') . gettimeofday()['usec']) . "." . $ext;
                        $linkFileAttach = '/files-attach/'.$nameFile;
                        $fileModel = new FileModel(
                            [
                                'FileName' => $name,
                                'FileType' => $ext,
                                'FileSize' => $size,
                                'DateModified' => date('Y-m-d H:i:s'),
                                'UserModified' => Auth::user()->UserID,
                                'Link' => $linkFileAttach,
                            ]
                        );
                        $fileModel->save();
                        $file->move('files-attach', $nameFile);

                        // content file
                        $contentFile = new ChatContentFile([
                            'ChatContentID' => $ContentID,
                            'FileID' => $fileModel->FileID,
                            'FieldAttach' => $fileModel->Link,
                            'FileAttachName' => $fileModel->FileName,
                            'FileType' => $fileType,
                        ]);
                        $contentFile->save();
                    }
                }
            }
        }
        $allContentFile = ChatContentFile::where('ChatContentID', '=', $ContentID)->get();
        return $allContentFile;
    }
    public function storeTags($tags, $ContentID){
        if (!count($tags)) return false;
        foreach ($tags as $tag) {
            $tagSlug = IjString::RemoveAccents($tag);
            $tagExist = ChatTag::where(['TagName' => $tag])->first();
            if (!$tagExist) {
                $tagModel = new ChatTag([
                    'TagName' => $tag,
                    'TagSlug' => $tagSlug
                ]);
                $tagModel->save();

                $contentTag = new ChatContentTag([
                    'ChatContentID' => $ContentID,
                    'TagID' => $tagModel->TagID
                ]);
                $contentTag->save();
            } else {
                $contentTag = new ChatContentTag([
                    'ChatContentID' => $ContentID,
                    'TagID' => $tagExist->TagID
                ]);
                $contentTag->save();
            }

        }
        return true;
    }
    public function createGroup(){
        $Users = SysUser::where('UserID', '!=', Auth::user()->UserID)
            ->where('Inactive', '!=', 1)
            ->get();
        $UserGroup = SysUserGroup::where('Inactive', '!=', 1)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['AllUsers'] = $Users;
        $json['data']['UserGroup'] = $UserGroup;
        return json_encode($json);
    }

    /**
     * @param Request $request
     * @return false|string
     * @desc chat_member: Type[1 => 'admin', 2 => 'member']
     */
    public function storeGroup(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        // validator
        $rules = [
            'GroupName' => 'required',
        ];
        $messages = [
            'GroupName.required' => 'Tên nhóm không được để trống',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $json['status'] = 0;
            $json['msg'] = 'Có lỗi';
            $json['data'] = $validator->errors();
            return json_encode($json);
        }

        // store
        $TypeMemberSelected = ($request->has('TypeMemberSelected')) ? $request->post('TypeMemberSelected') : 3;
        $auth = Auth::user();
        $group = new ChatGroup([
            'GroupName' => $request->post('GroupName'),
            'GroupDescription' => $request->post('GroupDescription'),
            'GroupType' => 2,
            'Created' => $auth->UserID,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'Locked' => 0
        ]);
        $group->save();

        $insertArr = [];
        $insertArr[] = [
            'UserName' => $auth->FullName,
            'UserID' => $auth->UserID,
            'GroupID' => $group->GroupID,
            'DateJoin' => date('Y-m-d H:i:s'),
            'Type' => 1
        ];

        if ($TypeMemberSelected == 1) {
            // all
            $AllUsers = SysUser::where('UserID', '!=', $auth->UserID)
                ->where('Inactive', '!=', 1)
                ->get();

            foreach ($AllUsers as $user) {
                $insertArr[] = [
                    'UserName' => $user->FullName,
                    'UserID' => $user->UserID,
                    'GroupID' => $group->GroupID,
                    'DateJoin' => date('Y-m-d H:i:s'),
                    'Type' => 2,
                ];
            }

        } elseif ($TypeMemberSelected == 2) {
            // group
            $UserGroup = $request->post('UserGroup');
            if ($UserGroup && count($UserGroup)) {
                $members = SysUser::select('sys_user.*')
                    ->join('sys_group_member', 'sys_group_member.UserID', '=', 'sys_user.UserID')
                    ->whereIn('sys_group_member.UserGroupID', $UserGroup)
                    ->where('sys_user.Inactive', '!=', 1)
                    ->where('sys_user.UserID', '!=', $auth->UserID)
                    ->groupBy('sys_user.UserID')
                    ->get();

                foreach ($members as $member) {
                    $insertArr[] = [
                        'UserName' => $member->FullName,
                        'UserID' => $member->UserID,
                        'GroupID' => $group->GroupID,
                        'DateJoin' => date('Y-m-d H:i:s'),
                        'Type' => 2,
                    ];
                }
            }
        }
        else {
            foreach ($request->post('Members') as $member) {
                $insertArr[] = [
                    'UserName' => $member['UserName'],
                    'UserID' => $member['UserID'],
                    'GroupID' => $group->GroupID,
                    'DateJoin' => date('Y-m-d H:i:s'),
                    'Type' => 2,
                ];
            }
        }

        DB::table('chat_member')->insert($insertArr);
        $membersInGroup = ChatMember::select('chat_member.*', 'chat_group.GroupType', 'sys_user.Avata')
            ->join('chat_group', 'chat_member.GroupID', '=', 'chat_group.GroupID')
            ->leftJoin('sys_user', 'sys_user.UserID', '=', 'chat_member.UserID')
            ->where('chat_member.GroupID', '=', $group->GroupID)
            ->get();

        $json['status'] = 1;
        $json['msg'] = 'Thàn công';
        $json['data']['Group'] = $group;
        $json['data']['Members'] = $membersInGroup;
        return json_encode($json);
    }
    public function getAllTable(){
        $table = SysTable::where(['isTaskLink' => 1])
            ->orWhere(['TableName' => 'task'])
            ->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $table;
        return json_encode($json);
    }
    public function updateCategoryKey(Request $request){
        $lineID = $request->post('LineID');
        if (!$lineID) {
            $json['status'] = 0;
            $json['msg'] = '';
            $json['data'] = '';
            return json_encode($json);
        }
        $categoryKey = $request->post('CategoryKey');
        $message = ChatContent::find($lineID);
        if ($message->UserID !== Auth::user()->UserID) {
            $json['status'] = 0;
            $json['msg'] = '';
            $json['data'] = '';
            return json_encode($json);
        }
        $message->CategoryKey = $categoryKey;
        $message->save();

        DB::table('chat_content_tag')->where(['ChatContentId' => $message->LineID])->delete();

        // add new tag
        $pieces = explode('_', $categoryKey);
        foreach ($pieces as $piece) {
            if (strpos($piece, 'tag') !== false) {
                $tagString = explode(':', $piece)[1];
                $tags = explode(' ', $tagString);
                foreach ($tags as $tag) {
                    if (strpos($tag, '#') !== false) {
                        $tagSlug = IjString::RemoveAccents($tag);
                        $tagExist = ChatTag::where(['TagName' => $tag])->first();
                        if (!$tagExist) {
                            $tagModel = new ChatTag([
                                'TagName' => $tag,
                                'TagSlug' => $tagSlug
                            ]);
                            $tagModel->save();
                            $contentTag = new ChatContentTag([
                                'ChatContentID' => $message->LineID,
                                'TagID' => $tagModel->TagID
                            ]);
                            $contentTag->save();
                        } else {
                            $contentTag = new ChatContentTag([
                                'ChatContentID' => $message->LineID,
                                'TagID' => $tagExist->TagID
                            ]);
                            $contentTag->save();
                        }
                    }
                }
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $message;
        return json_encode($json);
    }
    public function addMembers(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $GroupID = (int) $request->post('GroupID');
        $members = $request->post('members');
        if (!$GroupID || !count($members)) {
            return json_encode($json);
        }

        // kiểm tra có quyền admin
        $isAdmin = ChatMember::where([
            'GroupID' => $GroupID,
            'UserID' => Auth::user()->UserID,
            'Type' => 1
        ])->exists();

        if (!$isAdmin) {
            $json['msg'] = 'Bạn không phải quản trị viên';
            return json_encode($json);
        }

        $insertArr = [];
        $userIDs = [];
        $listIDs = '';
        foreach ($members as $key => $member) {
            $insertArr[] = [
                'UserName' => $member['UserName'],
                'UserID' => $member['UserID'],
                'GroupID' => $GroupID,
                'DateJoin' => date('Y-m-d H:i:s'),
                'Type' => 2
            ];
            $userIDs[] = $member['UserID'];
            $listIDs .= $member['UserID'];
            if ($key !== (count($members) - 1)) {
                $listIDs .= ',';
            }
        }

        DB::table('chat_member')->where(['GroupID' => $GroupID])->whereIn('UserID', $userIDs)->delete();
        DB::table('chat_member')->insert($insertArr);
        $data = ChatMember::select('chat_member.*', 'chat_group.GroupType', 'sys_user.Avata')
            ->join('chat_group', 'chat_group.GroupID', '=', 'chat_member.GroupID')
            ->leftJoin('sys_user', 'sys_user.UserID', '=', 'chat_member.UserID')
            ->where(['chat_member.GroupID' => $GroupID])
            ->whereIn('chat_member.UserID', $userIDs)
            ->get();

        // insert message
        $message = new ChatContent([
            'GroupID' => $GroupID,
            'UserName' => Auth::user()->FullName,
            'UserID' => Auth::user()->UserID,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'UpdatedDate' => date('Y-m-d H:i:s'),
            'Content' => ':sb-action-member_add_' . $listIDs
        ]);

        $message->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['Content'] = $message;
        return json_encode($json);
    }
    public function removeMember(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $GroupID = (int) $request->post('GroupID');
        $UserID = $request->post('UserID');

        if (!$GroupID || !$UserID) {
            return json_encode($json);
        }
        // check user per
        $exists = ChatMember::where([
            'GroupID' => $GroupID,
            'UserID' => Auth::user()->UserID,
            'Type' => 1
        ])->exists();
        if (!$exists) {
            $json['status'] = 2;
            $json['msg'] = 'Bạn không có quyền quản trị';
            return json_encode($json);
        }

        DB::table('chat_member')->where([
            'GroupID' => $GroupID,
            'UserID' => $UserID
        ])->delete();

        // insert message
        $message = new ChatContent([
            'GroupID' => $GroupID,
            'UserName' => Auth::user()->FullName,
            'UserID' => Auth::user()->UserID,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'UpdatedDate' => date('Y-m-d H:i:s'),
            'Content' => ':sb-action-member_remove_' . $UserID
        ]);

        $message->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['Content'] = $message;
        return json_encode($json);
    }
    public function leaveGroup(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $GroupID = (int) $request->post('GroupID');
        if (!$GroupID) {
            return json_encode($json);
        }

        $isAdmin = ChatMember::where([
            'GroupID' => $GroupID,
            'UserID' => Auth::user()->UserID,
            'Type' => 1
        ])->exists();

        $countAdmin = ChatMember::where([
            'GroupID' => $GroupID,
            'Type' => 1
        ])->count();

        if ($isAdmin && $countAdmin < 2) {
            $json['status'] = 2;
            $json['msg'] = 'Bạn không thể rời nhóm!';
            return json_encode($json);
        }else {
            DB::table('chat_member')->where([
                'GroupID' => $GroupID,
                'UserID' => Auth::user()->UserID,
            ])->delete();

            // insert message
            $message = new ChatContent([
                'GroupID' => $GroupID,
                'UserName' => Auth::user()->FullName,
                'UserID' => Auth::user()->UserID,
                'CreatedDate' => date('Y-m-d H:i:s'),
                'UpdatedDate' => date('Y-m-d H:i:s'),
                'Content' => ':sb-action-member_leave_' . Auth::user()->UserID
            ]);
            $message->save();
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data']['Content'] = $message;
        }

        return json_encode($json);

    }
    public function setMemberRole(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $GroupID = (int) $request->post('GroupID');
        $UserID = $request->post('UserID');
        $Type = (int) $request->post('Type');

        if (!$GroupID || !$UserID) {
            return json_encode($json);
        }

        $countAdmin = ChatMember::where([
            'GroupID' => $GroupID,
            'Type' => 1
        ])->count();

        if ($Type === 2 && $countAdmin < 2) {
            $json['status'] = 2;
            $json['msg'] = 'Không thể thay đổi quyền thành viên';
            $json['data'] = $countAdmin;
            return json_encode($json);
        }else{
            $member = ChatMember::where([
                'GroupID' => $GroupID,
                'UserID' => $UserID
            ])->first();
            $member->Type = $Type;
            $member->save();

            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $member;
        }

        return json_encode($json);
    }
    public function getMembers(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $GroupID = (int)$request->post('GroupID');
        if (!$GroupID) return json_encode($json);
        $membersInGroup = ChatMember::select('chat_member.*', 'chat_group.GroupType', 'sys_user.Avata')
            ->join('chat_group', 'chat_member.GroupID', '=', 'chat_group.GroupID')
            ->leftJoin('sys_user', 'sys_user.UserID', '=', 'chat_member.UserID')
            ->where(['chat_member.GroupID' => $GroupID])
            ->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $membersInGroup;

        return json_encode($json);
    }
    public function updateGroup(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $GroupID = (int)$request->post('GroupID');
        $GroupName = $request->post('GroupName');

        $group = ChatGroup::find($GroupID);
        if ($GroupName) {
            $group->GroupName = $GroupName;
        }
        $group->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $group;
        return json_encode($json);
    }
    public function deleteGroup(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $GroupID = (int)$request->post('GroupID');
        if (!$GroupID) {
            return json_encode($json);
        }

        $isAdmin = ChatMember::where([
            'GroupID' => $GroupID,
            'UserID' => Auth::user()->UserID,
            'Type' => 1
        ])->exists();

        if ($isAdmin) {
            $group = ChatGroup::find($GroupID);
            $group->Deleted = 1;
            $group->save();

            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $group;
        } else {
            $json['status'] = 2;
            $json['msg'] = 'Bạn không có quyền xóa';
        }

        return json_encode($json);

    }
    public function deleteMessage(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $LineID = (int)$request->post('LineID');
        if (!$LineID) {
            return json_encode($json);
        }

        $content = ChatContent::find($LineID);
        if ($content->UserID === Auth::user()->UserID) {
            $content->Deleted = 1;
            $content->save();

            $message = ChatContent::select('chat_content.*', 'chat_group.GroupType', 'sys_user.Avata')
                ->join('chat_group', 'chat_group.GroupID', '=', 'chat_content.GroupID')
                ->leftJoin('sys_user', 'sys_user.UserID', '=', 'chat_content.UserID')
                ->where(['chat_content.LineID' => $LineID])
                ->first();
            if ($message->ParentID) {
                $parent = ChatContent::find($message->ParentID);
                if ($parent && $parent->ParentID) {
                    $message->GrandParentID = $parent->ParentID;
                }
            }
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $message;
        }

        return json_encode($json);
    }

    // category [comment, customer, contract,....]
    public function getCategoryComment(Request $request)
    {
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $data = ChatContent::getCategoryComment($request);

        $commentIDs = [];
        foreach ($data->items() as $comment) {
            $commentIDs[] = $comment->LineID;
        }
        $commentFile = ChatContentFile::whereIn('ChatContentID', $commentIDs)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['CommentFile'] = $commentFile;

        return json_encode($json);

    }

    public function getCategoryReply(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $data = ChatContent::getCategoryReply($request);

        $commentIDs = [];
        foreach ($data->items() as $comment) {
            $commentIDs[] = $comment->LineID;
        }
        $commentFile = ChatContentFile::whereIn('ChatContentID', $commentIDs)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['CommentFile'] = $commentFile;

        return json_encode($json);
    }

    public function downloadFile(Request $request, $id){
        $file = FileModel::find($id);
        return response()->download(public_path($file->Link));
    }

    public function getMessage(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $LineID = $request->post('LineID');
        $data = ChatContent::find($LineID);

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function getTaskStatusValue(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null,
        ];
        $TaskID = $request->post('TaskID');
        $task = Task::find($TaskID);
        $data = SysStatusItem::where('StatusID', $task->StatusID)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $data;

        return json_encode($json);
    }
    public function updateTaskStatusValue(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null,
        ];
        $TaskID = $request->post('TaskID');
        $taskAssign = TaskAssign::where([
            'TaskID' => $TaskID,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->first();

        $checkPer = false;
        if ($taskAssign) {
            if ($taskAssign->IsCreator || $taskAssign->IsExecutor || $taskAssign->IsMainResponsiblePerson || $taskAssign->IsResponsiblePerson) {
                $checkPer = true;
            }else{
                $taskPer = TaskPer::where([
                    'TaskID' => $TaskID,
                    'EmployeeID' => Auth::user()->EmployeeID
                ])->first();
                if ($taskPer && $taskPer->Edit) {
                    $checkPer = true;
                }
            }
        }

        if ($checkPer) {
            $StatusValue = $request->post('StatusValue');
            $StatusDescription = $request->post('StatusDescription');
            $task = Task::find($TaskID);
            $task->StatusValue = ($StatusValue) ? $StatusValue : $task->StatusValue;
            $task->StatusDescription = ($StatusDescription) ? $StatusDescription : $task->StatusDescription;

            $statusItem = SysStatusItem::where([
                'StatusID' => $task->StatusID,
                'StatusValue' => $StatusValue,
            ])->first();

            if ($statusItem && $statusItem->ExecutionStatus) {
                $task->Status = $statusItem->ExecutionStatus;
                if ($statusItem->ExecutionStatus == 1) {
                    $task->PercentCompleted = 0;
                }
                if ($statusItem->ExecutionStatus == 6) {
                    if ((int) $task->PercentCompleted < 100) {
                        $task->TotalActualQuantity = $task->EstimatedQuantity;
                        $task->TotalActualHour = $task->EstimatedQuantity;
                        $task->PercentCompleted = 100;
                    }

                    if ($task->Type == 2) {
                        $taskDataflow = TaskDataflow::where([
                            'TableName' => 'task',
                            'ConstraintFieldValue' => $TaskID
                        ])->whereColumn('DFKey', '!=', 'DFID')
                            ->where('Deleted', '!=', 1)
                            ->where('Locked', '!=', 1)
                            ->first();

                        if ($taskDataflow) {
                            $arrDataflow = [
                                'DFID' => $taskDataflow->DFID,
                                'DFKey' => $taskDataflow->DFKey,
                                'WFID' => $taskDataflow->WFID,
                                'WFItemID' => $taskDataflow->WFItemID,
                                'TaskID' => $task->TaskID,
                                'TaskIDParent' => $task->ParentID,
                                'FeatureStatusID' => $task->StatusID,
                                'FeatureStatusValue' => $StatusValue,
                                'FeatureStatusTitle' => $StatusDescription,
                                'PercentCompleted' => ($task->PercentCompleted) ? $task->PercentCompleted : 0,
                            ];

                            $taskDataflowController = new TaskDataflowController();
                            $taskDataflowController->updateDataflow($arrDataflow);
                            if ($task->PercentCompleted && ($task->PercentCompleted >= 100)) {
                                $json['data']['AutoNewTask'] = 1;
                            }
                        }
                    }
                }
            }


            if ($task->save()) {
                $json['status'] = 1;
                $json['msg'] = 'Cập nhật thành công';
                $json['data']['Task'] = $task;
            }
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Bạn không có quyền cập nhật trạng thái';
        }

        return json_encode($json);
    }
    public function getAllEmployee(){
        $employee = Employee::select('employee.EmployeeID', 'employee.EmployeeName', 'employee.UserID',
            'employee.EmployeeNo', DB::raw('sys_user.Avata AS Avatar'), 'sys_user.FullName')
            ->join('sys_user', 'sys_user.UserID', '=', 'employee.UserID')
            ->where('sys_user.Inactive', '!=', 1)->get();
        $json['data'] = $employee;
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        return json_encode($json);
    }
}
