<?php
namespace Module\Accounting\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class AccountingTrans extends Model{
    protected $table = 'act_gvouc_trans_item';
    protected $primaryKey = 'TransID';

    public static function search(Request $request, $TransID){
        return 1;
    }
    protected $fillable = [
        'TransID',
        'AutoactID',
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
        'BudgetLevel',
        'ProgramType',
        'ProgramID',
        'ProgramNo',
        'ProgramName',
        'SectorID',
        'SectorNo',
        'SectorName',
        'SbrSectorID',
        'SbrSectorNo',
        'SbrSectorName',
        'NormTableID',
        'NormTableItemID',
        'NormTableItemNo',
        'NormTableItemName',
        'NormID',
        'NormNo',
        'NormName',
        'isCCAccount',
    ];
    public $timestamps = false;

    public function actautoact(){
        return $this->belongsTo('Module\Sbpmakeplan\Models\Actautoact','AutoactID','AutoactID');
    }

}

?>
