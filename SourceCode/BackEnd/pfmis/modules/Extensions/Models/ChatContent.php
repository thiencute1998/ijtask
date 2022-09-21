<?php
namespace Module\Extensions\Models;

use IjCore\IjFullTextSearch;
use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;
use function foo\func;

class ChatContent extends Model{
    use IjFullTextSearch;
    protected $table = 'chat_content';
    protected $primaryKey = 'LineID';

    protected static $searchable = ['GroupNo', 'UserName'];

    public static function search(Request $request){
        $query = ChatContent::query();
        $query->select('chat_content.*', 'chat_group.GroupType', 'sys_user.Avata');
        $query->leftJoin('sys_user', 'sys_user.UserID', '=', 'chat_content.UserID');
        $query->leftJoin('chat_group', 'chat_content.GroupID', '=', 'chat_group.GroupID');
        $GroupID = $request->post('GroupID');
        if ($GroupID) {
            $query->where('chat_content.GroupID', '=', $GroupID);
        }

        if ($request->post('search')) {
            $search = $request->post('search');
            $query->where(function ($q) use ($search) {
                $q->where('chat_content.Content', 'like', '%' . $search . '%');
                if (strpos($search, '#') !== false) {
                    $q->orWhere('chat_content.CategoryKey', 'like', '%' . $search . '%');
                }
            });
        }

        if ($request->post('CategoryKey') && count($request->post('CategoryKey'))) {
            $categoryKey = $request->post('CategoryKey');
            foreach ($categoryKey as $value) {
                $query->where('chat_content.CategoryKey', 'like', '%' . $value . '%');
            }
        }

        if ($request->post('type') && $request->post('type') === 'social') {
            // get total comment
            $query->select('chat_content.*', 'tmp.TotalComment', 'chat_group.GroupType', 'sys_user.Avata');
            $query->leftJoin(DB::raw("(
            SELECT COUNT(*) AS TotalComment, ParentID
            FROM chat_content
            WHERE (ParentID != NULL OR ParentID != 0)
            AND Deleted != 1
            GROUP BY ParentID
            ) AS tmp"), function ($join){
                $join->on('tmp.ParentID', '=', 'chat_content.LineID');
            });

            $objectType = $request->post('objectType');
            if ($objectType === 'home') {
                $query->where(function ($query){
                    $query->whereIn('chat_content.GroupID', function ($q) {
                        $q->select('chat_group.GroupID')
                            ->from('chat_group')
                            ->join('chat_member', 'chat_member.GroupID', '=', 'chat_group.GroupID')
                            ->where('GroupType', '!=', 1)
                            ->where(['UserID' => Auth::user()->UserID])
                            ->groupBy('chat_group.GroupID');
                        })
                        ->orWhere('chat_content.GroupID', '=', 0)
                        ->orWhereNull('chat_content.GroupID');
                });
            }

            if ($objectType === 'group' && !$GroupID) {
                $query->select('chat_content.*', 'tmp.TotalComment', 'chat_group.GroupType', 'chat_group.GroupName', 'chat_group.GroupDescription', 'sys_user.Avata');
                $query->whereIn('chat_content.GroupID', function ($q) {
                    $q->select('chat_group.GroupID')
                        ->from('chat_group')
                        ->join('chat_member', 'chat_member.GroupID', '=', 'chat_group.GroupID')
                        ->where('GroupType', '!=', 1)
                        ->where(['UserID' => Auth::user()->UserID])
                        ->groupBy('chat_group.GroupID');
                });
            }

            if (!$request->post('search')) {
                $query->where(function ($q) {
                    $q->whereNull('chat_content.ParentID');
                    $q->orWhere('chat_content.ParentID', '=', 0);
                });
            }
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        if ($request->post('type') && $request->post('type') === 'social') {
            $query->orderBy('chat_content.UpdatedDate', 'DESC');
        }else {
            $query->orderBy('chat_content.LineID', 'DESC');
        }

        $query->where('chat_content.Deleted', '!=', 1);
        return $query->paginate($per_page);
    }

    public static function getCategoryComment(Request $request){

        $CategoryKeyArr = $request->post('CategoryKey');
        $CategoryKey = self::getCategoryKey($CategoryKeyArr);

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }

        $query = ChatContent::query();
        $query->select('chat_content.*', DB::raw('(IF(tmp1.TotalComment, tmp1.TotalComment, 0)  + IF(tmp2.TotalComment, tmp2.TotalComment, 0)) AS TotalComment'), 'chat_group.GroupType', 'sys_user.Avata')
            ->leftJoin(DB::raw("(
                SELECT COUNT(*) AS TotalComment, T1.ParentID
	            FROM (SELECT LineId, ParentID FROM chat_content WHERE (ParentID != NULL OR ParentID != 0) AND Deleted != 1) AS T1
	            GROUP BY T1.ParentID
            ) AS tmp1"), function ($join) {
                $join->on('tmp1.ParentID', '=', 'chat_content.LineID');
            })
            ->leftJoin(DB::raw("(
                SELECT COUNT(*) AS TotalComment, T1.ParentID
	            FROM (SELECT LineId, ParentID FROM chat_content WHERE (ParentID != NULL OR ParentID != 0) AND Deleted != 1) AS T1
	            INNER JOIN chat_content AS T2 ON T1.LineID = T2.ParentID
	            GROUP BY T1.ParentID
            ) AS tmp2"), function ($join) {
                $join->on('tmp2.ParentID', '=', 'chat_content.LineID');
            })
            ->leftJoin('sys_user', 'sys_user.UserID', '=', 'chat_content.UserID')
            ->leftJoin('chat_group', 'chat_group.GroupID', '=', 'chat_content.GroupID')
            ->where(function($q) use ($CategoryKey){
                $q->where('chat_content.CategoryKey', '=', $CategoryKey);
                $q->orWhere('chat_content.CategoryKey', 'like', $CategoryKey . '\_%');
                $q->orWhere('chat_content.CategoryKey', 'like', '%\_' . $CategoryKey . '\_%');
                $q->orWhere('chat_content.CategoryKey', 'like', '%\_' . $CategoryKey);
            })
            ->where(function ($q) {
                $q->whereNull('chat_content.ParentID');
                $q->orWhere('chat_content.ParentID', '=', 0);
            })
            ->where('chat_content.Deleted', '!=', 1)
            ->limit($per_page);

        if ($request->has('DateType')) {
            $DateType = $request->post('DateType');
            if ($DateType == 1) {
                $query->orderBy('chat_content.UpdatedDate', 'DESC');
            }else{
                $query->orderBy('chat_content.LineID', 'DESC');
            }
        }else{
            $query->orderBy('chat_content.UpdatedDate', 'DESC');
        }

        if($request->has('fromDate')){
            $fromDate = Date::StringToDate($request->post('fromDate'));
            $query->where(DB::raw('DATE_FORMAT(chat_content.CreatedDate, "%Y-%m-%d")'), '>=', $fromDate);
        }

        if ($request->has('toDate')) {
            $toDate = Date::StringToDate($request->post('toDate'));
            $query->where(DB::raw('DATE_FORMAT(chat_content.CreatedDate, "%Y-%m-%d")'), '<=', $toDate);
        }

        if ($request->has('UserIDs')) {
            $UserIDs = $request->post('UserIDs');
            if (count($UserIDs)) {
                $query->whereIn('chat_content.UserID', $UserIDs);
            }

        }

        return $query->paginate($per_page);
    }

    public static function getCategoryReply(Request $request){
        $ParentID = $request->post('ParentID');

        $query1 = ChatContent::select('chat_content.*', 'chat_group.GroupType', 'sys_user.Avata')
            ->leftJoin('chat_group', 'chat_group.GroupID', '=', 'chat_content.GroupID')
            ->leftJoin('sys_user', 'sys_user.UserID', '=', 'chat_content.UserID')
            ->where('chat_content.ParentID', $ParentID)
            ->where('chat_content.Deleted', '!=', 1);

        $query = DB::table('chat_content AS T1')
            ->select('T2.*', 'chat_group.GroupType', 'sys_user.Avata')
            ->join(DB::raw('chat_content as T2'), 'T1.LineID', '=', 'T2.ParentID')
            ->leftJoin('sys_user', 'T2.UserID', '=', 'sys_user.UserID')
            ->leftJoin('chat_group', 'chat_group.GroupID', '=', 'T2.GroupID')
            ->where('T1.ParentID', $ParentID)
            ->where('T2.Deleted', '!=', 1)
            ->union($query1)
            ->orderBy('LineID', 'DESC');

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }

        return $query->paginate($per_page);
    }

    /**
     * @param $params [task: 1, custommer: 2], [task: [1,2,3], custommer: 3]
     * @return string
     */
    public static function getCategoryKey($params){
        $CategoryKey = '';
        $lastCategory = end($params);
        foreach ($params as $key => $value) {
            $tmpString = '';
            if (is_array($value)) {
                sort($value);
                foreach ($value as $k => $item) {
                    $tmpString .= $key . ':' . $item;
                    if ($k !== (count($value) - 1)) {
                        $tmpString .= '_';
                    }
                }
            }else{
                $tmpString = $key . ':' . $value;
            }

            $CategoryKey .= $tmpString;
            if ($value !== $lastCategory) {
                $CategoryKey .= '_';
            }

        }

        return $CategoryKey;
    }

    protected $fillable = [
        'GroupID', 'UserName', 'UserID', 'CategoryKey', 'CreatedDate', 'UpdatedDate','Content',
        'ParentID', 'UpdatedDateParent', 'Deleted'
    ];
    public $timestamps = false;


}

?>
