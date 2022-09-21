<?php
namespace Module\Customer\Models;

use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CustomerContractTrans extends Model{
    protected $table = 'customer_contract_trans';
    protected $primaryKey = 'TransID';

    public static function search(Request $request){ //return($request->TransComment);
        $data = null;
        $query = CustomerContractTrans::query();
        $where = [];

        if($request->post('TransDate')){
            $startDate = Date::StringToDate($request->TransDate);
            $query->where('DATE_FORMAT(customer_contract_trans.TransDate, "%Y-%m-%d")', '=', $startDate);
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
        return $query->orderBy('TransID')->paginate($per_page);
    }
    protected $fillable = [
        'TransDate',
        'TransComment',
        'CustomerID',
        'CustomerName',
        'ContractID',
        'ContractName',
        'CustomerInfo',
        'ContractInfo',
        'ContactID',
        'ContactName',
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
        'InvoiceDate',
        'InvoiceNo',
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
