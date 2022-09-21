<?php
namespace Module\Extensions\Controllers;

use Illuminate\Http\Request;
use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\Extensions\Models\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Module\Extensions\Models\NotificationUser;
use Module\Listing\Models\Company;
use Module\Listing\Models\Employee;
use Module\SysAdmin\Models\SysUser;
use Module\Task\Models\Task;
use Module\Task\Models\TaskAssign;
use Module\Task\Models\TaskDataflow;

class NotificationController extends Controller {

    public function index(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $data = Notification::search($request);


        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function storeNotice(){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        return json_encode($json);
    }
    public function storeTaskNotice(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $TaskID = $request->post('TaskID');
        $type = $request->post('TypeAction');

        $task = Task::find($TaskID);
        if($task){
            $employee = Employee::where(['UserID' => Auth::user()->UserID])->first();

            $link = '';
            $description = '';
            $TypeAction = 0;
            switch ($type){
                case 'comment':
                    $TypeAction = 1;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</span> đã cập nhật trao đổi trong công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                case 'addnew':
                    $TypeAction = 2;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</span> đã tạo công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                case 'edit':
                    $TypeAction = 3;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</span> đã cập nhật công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                case 'assign':
                    $TypeAction = 4;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</span> đã cập nhật nội dung giao việc của công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                case 'execute':
                    $TypeAction = 5;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</span> đã cập nhật nội dung thực hiện của công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                case 'check':
                    $TypeAction = 6;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</> đã cập nhật nội dung kiểm tra của công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                case 'expense':
                    $TypeAction = 7;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</span> đã cập nhật nội dung chi phí của công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                case 'taskPer':
                    $TypeAction = 8;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</span> đã cập nhật nội dung phân quyền của công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                case 'updateFile':
                    $TypeAction = 9;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</span> đã cập nhật nội dung tệp đính kèm của công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                case 'updateVideo':
                    $TypeAction = 10;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</span> đã cập nhật nội dung phim của công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                case 'taskcode':
                    $TypeAction = 11;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</span> đã cập nhật nội dung loại công việc của công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                case 'indicator':
                    $TypeAction = 12;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</span> đã cập nhật nội dung chỉ tiêu đánh giá của công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                case 'evaluation':
                    $TypeAction = 13;
                    $description = '<span class="notice-username">'.$employee->EmployeeName.'</span> đã cập nhật nội đánh giá của công việc: '.$task->TaskName;
                    $link = "/task/task/view/$TaskID";
                    break;
                default:
                    break;
            }

            $CreateDate = date('Y-m-d H:i:s');
            $notification = Notification::where(['CategoryID' => $TaskID, 'TypeAction' => $TypeAction, 'UserID' => Auth::user()->UserID, 'TypeCategory' => 1])->first();
            if(!$notification){
                $notification = new Notification([
                    'Description' => $description,
                    'Link' => $link,
                    'CreateDate' => $CreateDate,
                    'CategoryID' => $TaskID,
                    'UserID' => Auth::user()->UserID,
                    'UserName' => Auth::user()->FullName,
                    'TypeAction' => $TypeAction,
                    'TypeCategory' => 1
                ]);
            }
            $notification->CreateDate = $CreateDate;
            $notification->save();

            NotificationUser::where('NotificationID', $notification->NotificationID)->delete();

            $userIDReceives = [];
            if($task->AccessType == 2){
                $arrInsert = [];
                $allUsers = [];
                if($task->PublicCompanyID){
                    $allUserIDs = [];

                    $usersQuery = TaskAssign::query();
                    $usersQuery->select('employee.*')
                        ->join('employee', 'employee.EmployeeID', '=', 'task_assign.EmployeeID')
                        ->where(['task_assign.TaskID' => $TaskID])
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
                foreach ($allUsers as $item){
                    if($item->UserID && $item->UserID != Auth::user()->UserID){
                        $arrInsert[] = [
                            'NotificationID' => $notification->NotificationID,
                            'UserID' => $item->UserID,
                            'Type' => 2,
                            'UserName' => $item->EmployeeName,
                            'Status' => 0,
                        ];

                        $userIDReceives[] = $item->UserID;
                    }
                }
                $arrInsert[] = [
                    'NotificationID' => $notification->NotificationID,
                    'UserID' => Auth::user()->UserID,
                    'Type' => 1,
                    'UserName' => Auth::user()->FullName,
                    'Status' => 0,
                ];
                DB::table('notification_user')->insert($arrInsert);

            }elseif ($task->AccessType == 1){

                $usersQuery = TaskAssign::query();
                $usersQuery->select('employee.*')
                    ->join('employee', 'employee.EmployeeID', '=', 'task_assign.EmployeeID')
                    ->where(['task_assign.TaskID' => $TaskID])
                    ->groupBy('employee.EmployeeID');
                $usersAssign = $usersQuery->get();

                $arrInsert = [];
                foreach ($usersAssign as $item){
                    if($item->UserID && $item->UserID != Auth::user()->UserID) {
                        $arrInsert[] = [
                            'NotificationID' => $notification->NotificationID,
                            'UserID' => $item->UserID,
                            'Type' => 2,
                            'UserName' => $item->EmployeeName,
                            'Status' => 0,
                        ];

                        $userIDReceives[] = $item->UserID;
                    }
                }
                $arrInsert[] = [
                    'NotificationID' => $notification->NotificationID,
                    'UserID' => Auth::user()->UserID,
                    'Type' => 1,
                    'UserName' => Auth::user()->FullName,
                    'Status' => 0,
                ];
                DB::table('notification_user')->insert($arrInsert);
            }

            $notification->Type = 2;
            $notification->Status = 0;
            $notification->Avata = Auth::user()->Avata;


            $json['data']['data'] = $notification;
            $json['data']['UserReceives'] = $userIDReceives;
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        return json_encode($json);
    }
    public function storeTaskDataflowNotice(Request $request)
    {
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $TaskID = $request->post('TaskID');
        $type = $request->post('TypeAction');
        $task = Task::find($TaskID);
        if ($task && $task->Type == 2) {
            $taskIndataflow = TaskDataflow::where([
                'TableName' => 'task',
                'ConstraintFieldValue' => $TaskID
            ])->first();

            if (!$taskIndataflow) {
                $json['msg'] = 'Luồng công việc không tồn tại';
                return json_encode($json);
            }

            $isTaskInDataflow = ($taskIndataflow->DFID != $taskIndataflow->DFKey) ? true : false;
            $WFID = $taskIndataflow->WFID;
            $DFKey = $taskIndataflow->DFKey;

            $taskDataflow = Task::join('task_dataflow', 'Task.TaskID', '=', 'task_dataflow.ConstraintFieldValue')
                ->where([
                    'task_dataflow.DFID' => $taskIndataflow->DFKey
                ])->first();

            $link = '';
            $description = '';
            $TypeAction = 0;
            $employee = Employee::where(['UserID' => Auth::user()->UserID])->first();
            switch ($type){
                case 'comment':
                    $TypeAction = 1;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật trao đổi trong công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật trao đổi trong quy trình công việc: ' . $taskDataflow->TaskName;
                    }
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'addnew':
                    $TypeAction = 2;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã tạo công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã tạo quy trình công việc: ' . $taskDataflow->TaskName;
                    }
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'edit':
                    $TypeAction = 3;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật quy trình công việc: ' . $taskDataflow->TaskName;
                    }
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'assign':
                    $TypeAction = 4;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung giao việc trong công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung giao việc trong quy trình công việc: ' . $taskDataflow->TaskName;
                    }
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'execute':
                    $TypeAction = 5;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung thực hiện trong công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung thực hiện trong quy trình công việc: ' . $taskDataflow->TaskName;
                    }
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'check':
                    $TypeAction = 6;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung kiểm tra trong công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung kiểm tra trong quy trình công việc: ' . $taskDataflow->TaskName;
                    }

                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'expense':
                    $TypeAction = 7;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung chi phí trong công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung chi phí trong quy trình công việc: ' . $taskDataflow->TaskName;
                    }
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'taskPer':
                    $TypeAction = 8;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội phân quyền hiện trong công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung phân quyền trong quy trình công việc: ' . $taskDataflow->TaskName;
                    }
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'updateFile':
                    $TypeAction = 9;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung tệp đính kèm trong công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung tệp đính kèm trong quy trình công việc: ' . $taskDataflow->TaskName;
                    }
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'updateVideo':
                    $TypeAction = 10;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung phim trong công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung phim trong quy trình công việc: ' . $taskDataflow->TaskName;
                    }
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'taskcode':
                    $TypeAction = 11;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung loại công việc trong công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung loại công việc trong quy trình công việc: ' . $taskDataflow->TaskName;
                    }
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'indicator':
                    $TypeAction = 12;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung chỉ tiêu đánh giá trong công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung chỉ tiêu đánh giá trong quy trình công việc: ' . $taskDataflow->TaskName;
                    }
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'evaluation':
                    $TypeAction = 13;
                    if ($isTaskInDataflow) {
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung đánh giá trong công việc "' . $task->TaskName . '" của quy trình công việc "' . $taskDataflow->TaskName . '"';
                    }else{
                        $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã cập nhật nội dung đánh giá trong quy trình công việc: ' . $taskDataflow->TaskName;
                    }
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                case 'autoNewTask':
                    $TypeAction = 14;
                    $description = '<span class="notice-username">' . $employee->EmployeeName . '</span> đã tạo công việc mới trong quy trình công việc: ' . $taskDataflow->TaskName;
                    $link = "/task/dataflow/$WFID/$DFKey";
                    break;
                default:
                    break;
            }
            $CreateDate = date('Y-m-d H:i:s');
            $notification = Notification::where(['CategoryID' => $TaskID, 'TypeAction' => $TypeAction, 'UserID' => Auth::user()->UserID, 'TypeCategory' => 2])->first();
            if(!$notification){
                $notification = new Notification([
                    'Description' => $description,
                    'Link' => $link,
                    'CreateDate' => $CreateDate,
                    'CategoryID' => $TaskID,
                    'UserID' => $employee->UserID,
                    'UserName' => $employee->EmployeeName,
                    'TypeAction' => $TypeAction,
                    'TypeCategory' => 2
                ]);
            }
            $notification->CreateDate = $CreateDate;
            $notification->save();

            NotificationUser::where('NotificationID', $notification->NotificationID)->delete();

            // get all employee in workflow
            $arrInsert = [];
            $userIDReceives = [];
            $usersAssign = TaskAssign::select('employee.*')
                ->join('employee', 'task_assign.EmployeeID', '=', 'employee.EmployeeID')
                ->whereIn('task_assign.TaskID', function ($q) use ($taskIndataflow) {
                    $q->select('task_dataflow.ConstraintFieldValue')
                        ->from('task_dataflow')
                        ->where([
                            'TableName' => 'task',
                            'DFKey' => $taskIndataflow->DFKey
                    ]);
                })
                ->groupBy('employee.EmployeeID')
                ->get();

            $arrInsert[] = [
                'NotificationID' => $notification->NotificationID,
                'UserID' => $employee->UserID,
                'Type' => 1,
                'UserName' => $employee->EmployeeName,
                'Status' => 0,
            ];
            foreach ($usersAssign as $userAssign) {
                if($userAssign->UserID && $userAssign->UserID != Auth::user()->UserID) {
                    $arrInsert[] = [
                        'NotificationID' => $notification->NotificationID,
                        'UserID' => $userAssign->UserID,
                        'Type' => 2,
                        'UserName' => $userAssign->EmployeeName,
                        'Status' => 0,
                    ];
                    $userIDReceives[] = $userAssign->UserID;
                }
            }
            DB::table('notification_user')->insert($arrInsert);

            $notification->Type = 2;
            $notification->Status = 0;
            $notification->Avata = Auth::user()->Avata;

            $json['data']['data'] = $notification;
            $json['data']['UserReceives'] = $userIDReceives;
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        return json_encode($json);

    }
    public function updateStatusNotice(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $NotificationID = $request->post('NotificationID');
        $Status = $request->post('Status');

        if ($NotificationID) {
            $notificationUser = NotificationUser::where([
                'UserID' => Auth::user()->UserID,
                'NotificationID' => $NotificationID,
                'Type' => 2
            ])->first();

            if ($notificationUser) {
                $notificationUser->Status = $Status;
                if ($Status == 1) {
                    $notificationUser->ReadDate = date('Y-m-d H:i:s');
                }
                $notificationUser->save();
                $json['data'] = $notificationUser;
            } else {
                return json_encode($json);
            }
        }else{
            if ($request->has('TypeUpdate') && $request->post('TypeUpdate') === 'all') {
                if ($Status == 1) {
                    DB::table('notification_user')->where([
                        'UserID' => Auth::user()->UserID,
                        'Type' => 2,
                        'Status' => 0
                    ])->update(['Status' => $Status, 'ReadDate' => date('Y-m-d H:i:s')]);
                }else{
                    DB::table('notification_user')->where([
                        'UserID' => Auth::user()->UserID,
                        'Type' => 2,
                        'Status' => 0
                    ])->update(['Status' => $Status]);
                }
            }

            if ($request->has('array_id')) {
                $array_id = $request->post('array_id');
                if (count($array_id)) {
                    if ($Status == 1) {
                        DB::table('notification_user')
                            ->where([
                                'UserID' => Auth::user()->UserID,
                                'Type' => 2,
                                'Status' => 0])
                            ->whereIn('NotificationID', $array_id)
                            ->update(['Status' => $Status, 'ReadDate' => date('Y-m-d H:i:s')]);
                    }else{
                        DB::table('notification_user')
                            ->where([
                                'UserID' => Auth::user()->UserID,
                                'Type' => 2,
                                'Status' => 0])
                            ->whereIn('NotificationID', $array_id)
                            ->update(['Status' => $Status]);
                    }
                }
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        return json_encode($json);
    }
    public function getUserReceive(Request $request){
        $userReceive = NotificationUser::getUserReceive($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $userReceive;
        return json_encode($json);
    }

}
