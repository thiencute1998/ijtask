<?php
namespace Module\Extensions\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class NotificationUser extends Model{
    protected $table = 'notification_user';
    protected $primaryKey = 'LineID';


    public static function getUserReceive(Request $request){
        $query = NotificationUser::query();
        $NotificationID = $request->post('NotificationID');

        $query->where([
            'NotificationID' => $NotificationID,
            'Type' => 2
        ]);

        $query->orderBy('ReadDate', 'DESC');

        if($request->post('UserName')){
            $query->where('UserName', 'like', '%' . $request->post('UserName') . '%');
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->paginate($per_page);
    }

    protected $fillable = [
        'NotificationID',
        'UserID',
        'CompanyID',
        'Type',
        'UserName',
        'Status',
        'ReadDate'
    ];
    public $timestamps = false;


}

?>
