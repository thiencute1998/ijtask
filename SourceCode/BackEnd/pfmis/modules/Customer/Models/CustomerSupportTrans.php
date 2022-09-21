<?php
namespace Module\Customer\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CustomerSupportTrans extends Model{
    protected $table = 'customer_support_trans';
    protected $primaryKey = 'TransID';

    public static function search(Request $request, $CustomerID){
        return 1;
    }
    protected $fillable = [
        'TransDate',
        'TransComment',
        'EmployeeID',
        'EmployeeName',
        'CustomerID',
        'CustomerName',
        'ContactID',
        'ContactName',
        'CustomerInfo',
        'Time',
        'FileID',
        'FileName',
        'ItemID',
        'ItemName',
        'StatusID',
        'StatusDescription',
        'StatusName',
        'StatusValue',
        'CreatedDate',
        'CreatedUserID',
        'UpdatedDate',
        'UpdatedUserID',
        'Locked',
        'LockedDate',
        'LockedUserID',
    ];
    public $timestamps = false;

}

?>
