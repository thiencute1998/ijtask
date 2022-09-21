<?php
namespace Module\Customer\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CustomerQuotationTrans extends Model{
    protected $table = 'customer_quotation_trans';
    protected $primaryKey = 'TransID';

    public static function search(Request $request, $CustomerID){
        return 1;
    }
    protected $fillable = [
        'TransDate',
        'TransNo',
        'TransComment',
        'CcyID',
        'CcyNo',
        'ExchangeRate',
        'CompanyID',
        'CompanyName',
        'CompanyAddress',
        'CompanyTaxCode',
        'CompanyTel',
        'CompanyFax',
        'CustomerID',
        'CustomerName',
        'CustomerAddress',
        'CustomerTaxCode',
        'CustomerBankAccount',
        'CustomerBankName',
        'CustomerTel',
        'CustomerFax',
        'CustomerEmail',
        'CustomerBuyer',
        'FileID',
        'FileName',
        'StatusID',
        'StatusDescription',
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
