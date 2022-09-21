<?php
namespace Module\Extensions\Models;

use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Notification extends Model{
    protected $table = 'notification';
    protected $primaryKey = 'NotificationID';

    public static function search(Request $request){
        $query = Notification::query();
        $query->select(DB::raw('notification.*, notification_user.LineID, notification_user.Type, notification_user.Status, sys_user.Avata'))
            ->join('notification_user', 'notification.NotificationID', '=', 'notification_user.NotificationID')
            ->leftJoin('sys_user', 'sys_user.UserID', '=', 'notification.UserID');

        $query->where(['notification_user.UserID' => Auth::user()->UserID]);

        if ($request->has('Type')) {
            $query->where('notification_user.Type', '=', $request->post('Type'));
        }else{
            $query->where('notification_user.Type', '=', 2);
        }


        if ($request->has('Status')) {
            $query->where(['notification_user.Status' => $request->post('Status')]);
        }

        if ($request->post('Description')) {
            $query->where('notification.Description', 'like', '%' . $request->post('Description') . '%');
        }

        // Ngày tạo
        $objCreateDate = $request->post('CreateDate');
        if (isset($objCreateDate) && !empty($objCreateDate) && !empty($objCreateDate['dateTime'])) {
            $createDate = Date::StringToDate($objCreateDate['dateTime']);
            $query->where(DB::raw('DATE_FORMAT(notification.CreateDate, "%Y-%m-%d")'), $objCreateDate['operator'], $createDate);
        }

        if ($request->post('UserName')) {
            $query->where('notification.UserName', 'like', '%' . $request->post('UserName') . '%');
        }

        if($request->post('CategoryID')){
            $query->where('CategoryID', '=', $request->CategoryID);
        }
        if($request->post('UserCreate')){
            $query->where('notification.UserID', '=', $request->post('UserCreate'));
        }
        if($request->post('fromDate')){
            $fromDate = Date::StringToDate($request->post('fromDate'));
            $query->where(DB::raw('DATE_FORMAT(notification.CreateDate, "%Y-%m-%d")'), '>=', $fromDate);
        }

        if ($request->post('toDate')) {
            $toDate = Date::StringToDate($request->post('toDate'));
            $query->where(DB::raw('DATE_FORMAT(notification.CreateDate, "%Y-%m-%d")'), '<=', $toDate);
        }

        $query->groupBy('notification.NotificationID');

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }

        $query->orderBy('CreateDate', 'DESC');

        return $query->paginate($per_page);
    }

    protected $fillable = [
        'Description',
        'Link',
        'CreateDate',
        'UserID',
        'UserName',
        'CategoryID',
        'TypeAction',
        'TypeCategory'
    ];
    public $timestamps = false;


}

?>
