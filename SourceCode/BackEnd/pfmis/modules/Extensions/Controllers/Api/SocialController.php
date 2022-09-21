<?php
namespace Module\Extensions\Controllers;

use Carbon\Traits\Date;
use IjCore\IjString;
use IjSmartBooks\IjCore\IjFunction\Number;
use Illuminate\Http\Request;
use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Module\Extensions\Models\ChatContent;
use Module\Extensions\Models\ChatContentFile;
use Module\Extensions\Models\ChatContentTag;
use Module\Extensions\Models\ChatContentUserRead;
use Module\Extensions\Models\ChatGroup;
use Module\Extensions\Models\ChatMember;
use Module\Extensions\Models\ChatTag;
use Module\Listing\Models\Employee;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysTable;
use Module\SysAdmin\Models\SysUser;
use Illuminate\Support\Facades\Validator;
use Module\Task\Models\FileModel;
use function foo\func;

class SocialController extends Controller {

    public function index(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        // Lấy tất cả nhóm mà user tham gia
        $user = Auth::user();
//        $groups = ChatGroup::select('chat_group.*')
//            ->join('chat_member', 'chat_group.GroupID', '=', 'chat_member.GroupID')
//            ->where('chat_member.UserID', '=', $user->UserID)
//            ->where('chat_group.Deleted', '!=', 1)
//            ->where('chat_group.GroupType', '!=', 1)
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
                GROUP BY chat_group.GroupID) AS tmp"), function ($join){
                $join->on('chat_group.GroupID', '=', 'tmp.GroupID');
            })
            ->where('chat_group.Deleted', '!=', 1)
            ->orderBy('tmp.LineID', 'DESC')
            ->get();

        $groupIDs = [];
        foreach ($groups as $group) {
            $groupIDs[] = $group->GroupID;
        }

        // chat last message
        $lastMessageQuery = ChatContent::query();
        $lastMessageQuery->select('chat_content.*')
            ->whereIn('chat_content.LineID', function ($q) use ($groupIDs) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('chat_content')
                    ->whereIn('GroupID', $groupIDs)
                    ->groupBy('GroupID');
            })
            ->where('chat_content.Deleted', '!=', 1);
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

        $membersInGroup = ChatMember::select('chat_member.*', 'chat_group.GroupType')
            ->join('chat_group', 'chat_member.GroupID', '=', 'chat_group.GroupID')
            ->whereIn('chat_member.GroupID', $groupIDs)
            ->get();
        $json['data']['MembersInGroup'] = $membersInGroup;

        $allUsers = SysUser::all();
        $json['data']['AllUsers'] = $allUsers;

        $json['data']['AllGroups'] = $groups;
        $json['data']['CurrentUser'] = $user;
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        return json_encode($json);
    }
    public function loadPost(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $data = ChatContent::search($request);

        // content file
        $posts = $data->items();
        $postIDs = [];
        $categoryIDArr = [];
        foreach ($posts as $post) {
            $postIDs[] = $post->LineID;

            if ($post->CategoryKey) {
                $categories = explode('_', $post->CategoryKey);
                foreach ($categories as $category) {
                    $pieces = explode(':', $category);
                    $table = $pieces[0];
                    $tableID = $pieces[1];
                    $categoryIDArr[$table][] = $tableID;
                }
            }
        }
        $postFile = ChatContentFile::whereIn('ChatContentID', $postIDs)->get();

        $per_page = SysSetup::getOption('NumberRowOnPage');
//        $per_page = 3;

        $comments = ChatContent::select('chat_content.*', 'tmp.TotalComment', 'chat_group.GroupType', 'sys_user.Avata')
            ->leftJoin(DB::raw("(
            SELECT COUNT(*) AS TotalComment, ParentID
            FROM chat_content
            WHERE (ParentID != NULL OR ParentID != 0)
            AND Deleted != 1
            GROUP BY ParentID
            ) AS tmp"), function ($join){
                $join->on('tmp.ParentID', '=', 'chat_content.LineID');
            })
            ->leftJoin('chat_group', 'chat_group.GroupID', '=', 'chat_content.GroupID')
            ->leftJoin('sys_user', 'chat_content.UserID', '=', 'sys_user.UserID')
            ->whereIn('chat_content.ParentID', $postIDs)
            ->where('chat_content.Deleted', '!=', 1)
            ->inRandomOrder()
            ->limit($per_page)
            ->get();

        $commentIDs = [];
        foreach ($comments as $comment) {
            $commentIDs[] = $comment->LineID;

            if ($comment->CategoryKey) {
                $categories = explode('_', $comment->CategoryKey);
                foreach ($categories as $category) {
                    $pieces = explode(':', $category);
                    $table = $pieces[0];
                    $tableID = $pieces[1];
                    $categoryIDArr[$table][] = $tableID;
                }
            }
        }
        $commentFile = ChatContentFile::whereIn('ChatContentID', $commentIDs)->get();

//        $commentReplies = ChatContent::whereIn('ParentID', $commentIDs)->get();
//        $commentReplies = ChatContent::whereIn('ParentID', $commentIDs)->limit(1)->get();
//        $commentReplyIDs = [];
//        foreach ($commentReplies as $commentReply) {
//            $commentReplyIDs[] = $commentReply->LineID;
//
//            if ($commentReply->CategoryKey) {
//                $categories = explode('_', $commentReply->CategoryKey);
//                foreach ($categories as $category) {
//                    $pieces = explode(':', $category);
//                    $table = $pieces[0];
//                    $tableID = $pieces[1];
//                    $categoryIDArr[$table][] = $tableID;
//                }
//            }
//        }
//        $commentReplyFile = ChatContentFile::whereIn('ChatContentID', $commentReplyIDs)->get();

        $datalist = [];
        if (count($categoryIDArr)) {
            foreach ($categoryIDArr as $table => $categoryID) {
                if ($table !== 'tag' && $table !== 'dataflow') {
                    $query = DB::table($table);
                    $tableName = ucfirst($table);
                    $query->select($tableName . 'ID', $tableName . 'No', $tableName . 'Name');
                    $query->whereIn($tableName . 'ID', $categoryID);
                    $records = $query->get();
                    $datalist[$table] = $records;
                } else {
                    $datalist[$table] = $categoryID;
                }
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['PostFile'] = $postFile;
        $json['data']['Comments'] = $comments;
        $json['data']['CommentFile'] = $commentFile;
//        $json['data']['CommentReplies'] = $commentReplies;
//        $json['data']['CommentReplyFile'] = $commentReplyFile;
        $json['data']['Datalist'] = $datalist;

        return json_encode($json);
    }
    public function loadMoreComment(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $ParentID = $request->post('ParentID');
        $page = ($request->post('page')) ? $request->post('page') : 1;
        $per_page = SysSetup::getOption('NumberRowOnPage');
//        $per_page = 3;
        if (!$ParentID) {
            return json_encode($json);
        }

        $comments = ChatContent::select('chat_content.*', 'tmp.TotalComment', 'chat_group.GroupType', 'sys_user.Avata')
            ->leftJoin(DB::raw("(
            SELECT COUNT(*) AS TotalComment, ParentID
            FROM chat_content
            WHERE (ParentID != NULL OR ParentID != 0)
            AND Deleted != 1
            GROUP BY ParentID
            ) AS tmp"), function ($join) {
                $join->on('tmp.ParentID', '=', 'chat_content.LineID');
            })
            ->leftJoin('chat_group', 'chat_group.GroupID', '=', 'chat_content.GroupID')
            ->leftJoin('sys_user', 'chat_content.UserID', '=', 'sys_user.UserID')
            ->where('chat_content.ParentID', $ParentID)
            ->where('chat_content.Deleted', '!=', 1)
            ->orderBy('chat_content.LineID', 'DESC')
            ->paginate($per_page, ['*'], 'page', $page);

        $categoryIDArr = [];
        $commentIDs = [];
        foreach ($comments as $comment) {
            $commentIDs[] = $comment->LineID;

            if ($comment->CategoryKey) {
                $categories = explode('_', $comment->CategoryKey);
                foreach ($categories as $category) {
                    $pieces = explode(':', $category);
                    $table = $pieces[0];
                    $tableID = $pieces[1];
                    $categoryIDArr[$table][] = $tableID;
                }
            }
        }
        $commentFile = ChatContentFile::whereIn('ChatContentID', $commentIDs)->get();

        $datalist = [];
        if (count($categoryIDArr)) {
            foreach ($categoryIDArr as $table => $categoryID) {
                if ($table !== 'tag') {
                    $query = DB::table($table);
                    $tableName = ucfirst($table);
                    $query->select($tableName . 'ID', $tableName . 'No', $tableName . 'Name');
                    $query->whereIn($tableName . 'ID', $categoryID);
                    $records = $query->get();
                    $datalist[$table] = $records;
                } else {
                    $datalist[$table] = $categoryID;
                }
            }
        }


        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $comments;
        $json['data']['CommentFile'] = $commentFile;
        $json['data']['Datalist'] = $datalist;
        return json_encode($json);
    }
}
