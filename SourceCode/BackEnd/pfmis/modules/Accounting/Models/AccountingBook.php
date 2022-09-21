<?php
namespace Module\Accounting\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class AccountingBook extends Model{
    protected $table = 'act_gl_books';
    protected $primaryKey = 'TransID';

    public static function search(Request $request, $TransID){
        return 1;
    }
    protected $fillable = [
        'TransID',
        'PostType',
        'CoaTypeID',
        'CoaTypeNo',
        'CoaTypeName',
        'AccountID',
        'AccountNo',
        'CoAccountID',
        'CoAccountNo',
        'Description',
        'CcyID',
        'CcyNo',
        'CcyName',
        'ExchangeRate',
        'FCDebitAmount',
        'LCDebitAmount',
        'FCCreditAmount',
        'LCCreditAmount',
        'FCAmount',
        'LCAmount',
        'InTransTypeID',
        'InTransTypeName',
        'InvoiceSerialNo',
        'InvoiceFormNo',
        'InvoiceSecurityCode',
        'InvoiceLookupAddress',
        'Quantity',
        'FCUnitPrice',
        'LCUnitPrice',
        'EmployeeID',
        'EmployeeNo',
        'EmployeeName',
        'CustomerID',
        'CustomerNo',
        'CustomerName',
        'VendorID',
        'VendorNo',
        'VendorName',
        'PartnerID',
        'PartnerNo',
        'FullName',
        'RevenueID',
        'RevenueNo',
        'RevenueName',
        'ExpenseID',
        'ExpenseNo',
        'ExpenseName',
        'FundID',
        'FundNo',
        'FundName',
        'CapitalID',
        'CapitalNo',
        'CapitalName',
        'FiscalPeriod',
        'FiscalYear',
        'SbiChapterID',
        'SbiChapterNo',
        'SbiChapterName',
        'SbiCategoryID',
        'SbiCategoryNo',
        'SbiCategoryName',
        'SbiItemID',
        'SbiItemNo',
        'SbiItemName',
        'ProjectID',
        'ProjectNo',
        'TabmisNo',
        'ProjectName',
        'ContractID',
        'ContractNo',
        'ContractName',
        'FixedAssetID',
        'FixedAssetNo',
        'FixedAssetName',
        'ToolID',
        'ToolNo',
        'ToolName',
        'InvestAssetID',
        'InvestAssetNo',
        'InvestAssetName',
        'ItemID',
        'ItemNo',
        'ItemName',
        'SectorID',
        'SectorNo',
        'SectorName',
        'SbrSectorID',
        'SbrSectorNo',
        'SbrSectorName',
    ];
    public $timestamps = false;

}

?>
