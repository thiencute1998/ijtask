<?php
namespace Module\Customer\Models;

use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CustomerSalesTrans extends Model{
    protected $table = 'customer_sales_trans';
    protected $primaryKey = 'TransID';

    public static function search(Request $request){ //return($request->TransComment);
        $data = null;
        $query = CustomerSalesTrans::query();
        $where = [];

        if($request->post('TransDate')){
            $startDate = Date::StringToDate($request->TransDate);
            $query->where('DATE_FORMAT(customer_sales_trans.TransDate, "%Y-%m-%d")', '=', $startDate);
        }
        if($request->post('EmployeeName')){
            $query->where('EmployeeName', 'like', $request->EmployeeName.'%');
        }
        if($request->post('CustomerInfo')){
            $query->where('CustomerInfo', 'like', $request->CustomerInfo.'%');
        }
        if($request->post('TransComment')){
            $query->where('TransComment', 'like', $request->TransComment.'%');
        }
        if($request->post('FCAmount')){
            $query->where('FCAmount', 'like', $request->FCAmount.'%');
        }
        if($request->post('PercentSuccess')){
            $query->where('PercentSuccess', 'like', $request->PercentSuccess.'%');
        }
        if($request->post('StatusDescription')){
            $query->where('StatusDescription', 'like', $request->StatusDescription.'%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('TransID','DESC')->paginate($per_page);
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
        'CcyID',
        'CcyNo',
        'CcyName',
        'ExchangeRate',
        'FCAmount',
        'LCAmount',
        'ExpectedEndDate',
        'PercentSuccess',
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
