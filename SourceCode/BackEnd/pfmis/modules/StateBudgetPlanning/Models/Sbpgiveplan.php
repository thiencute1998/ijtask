<?php
namespace Module\Sbpgiveplan\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUser;
use phpDocumentor\Reflection\Types\Self_;

class Sbpgiveplan extends Model{
    use IjFullTextSearch;
    protected $table = 'act_gvouc_trans';
    protected $primaryKey = 'TransID';

    protected static $searchable = ['TransNo', 'TransDate', 'PostDate', 'Comment'];

    public static function search(Request $request){
        $data = null;
        $query = Sbpgiveplan::query();
        $where = [];
        $auth = Auth::user();
            $query->where('TransTypeID', '=', 7);
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if(is_numeric($request->post('LCTotalAmount'))){
            $query->where('LCTotalAmount', 'like', '%'.$request->LCTotalAmount.'%');
        }
        if($request->post('Comment')){
            $query->where('Comment', 'like', '%'.$request->Comment.'%');
        }
        if($request->post('TransNo')){
            $query->where('TransNo', 'like', '%'.$request->TransNo.'%');
        }
        if($request->post('PostDate')){
            $query->whereDate('PostDate', date('Y-m-d',strtotime(str_replace('/','-',$request->post('PostDate')))));
        }
        if($request->post('TransDate')){
            $query->whereDate('TransDate', date('Y-m-d',strtotime(str_replace('/','-',$request->post('TransDate')))));
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
        $query->groupBy('act_gvouc_trans.TransNo');

        if ($request->has('exportData')) {
            $query->select('act_gvouc_trans.TransID', 'act_gvouc_trans.TransNo', DB::raw("DATE_FORMAT(TransDate,'%d/%m/%Y') AS TransDate"),
                DB::raw("DATE_FORMAT(PostDate,'%d/%m/%Y') AS PostDate"), 'act_gvouc_trans.Comment', 'act_gvouc_trans.LCTotalDebitAmount');
            return $query->orderBy(DB::raw('CONCAT(TransNo, TransDate)'))->get();
        }

        //return $query->orderBy(DB::raw('CONCAT(TransNo, TransDate)'))->paginate($per_page);
        return $query->orderBy('act_gvouc_trans.TransDate', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'TransNo',
        'TransDate',
        'PostDate',
        'eTransNo',
        'eTransDate',
        'TransTypeID',
        'TransTypeName',
        'CoaTypeID',
        'CoaTypeNo',
        'CoaTypeName',
        'Comment',
        'FCTotalAmount',
        'LCTotalAmount',
        'FCTotalDebitAmount',
        'LCTotalDebitAmount',
        'FCTotalCreditAmount',
        'LCTotalCreditAmount',
        'FCTotalTaxAmount',
        'LCTotalTaxAmount',
        'CompanyID',
        'CompanyNo',
        'CompanyMOFNo',
        'CompanyLocationNo',
        'CompanyName',
        'CompanyAddress',
        'CompanyBankAccount',
        'CompanyBankName',
        'CompanyContactName',
        'CompanyContactPosition',
        'CompanyDeparttmentName',
        'ParentCompanyID',
        'ParentCompanyNo',
        'ParentCompanyMOFNo',
        'ParentCompanyLocationNo',
        'ParentCompanyName',
        'ParentCompanyAddress',
        'ParentCompanyBankAccount',
        'ParentCompanyBankName',
        'ParentCompanyContactName',
        'ParentCompanyContactPosition',
        'ParentCompanyDeparttmentName',
        'EmployeeID',
        'EmployeeNo',
        'EmployeeName',
        'EmployeeCitizenIdNo',
        'EmployeeCitizenIdDate',
        'EmployeeCitizenIdAt',
        'EmployeeDeparttmentName',
        'EmployeePosition',
        'EmployeeBankAccount',
        'EmployeeBankName',
        'VendorID',
        'VendorNo',
        'VendorName',
        'VendorTaxcode',
        'VendorLocationNo',
        'VendorAddress',
        'VendorBankAccount',
        'VendorBankName',
        'VendorContactName',
        'VendorContactPosition',
        'CustomerID',
        'CustomerNo',
        'CustomerName',
        'CustomerTaxcode',
        'CustomerLocationNo',
        'CustomerAddress',
        'CustomerBankAccount',
        'CustomerBankName',
        'CustomerContactName',
        'CustomerContactPosition',
        'PartnerID',
        'PartnerName',
        'PartnerTaxcode',
        'PartnerLocationNo',
        'PartnerAddress',
        'PartnerBankAccount',
        'PartnerBankName',
        'PartnerContactName',
        'PartnerContactPosition',
        'PartnerCitizenIdNo',
        'PartnerCitizenIdDate',
        'PartnerCitizenIdAt',
        'NormTableID',
        'NormTableNo',
        'NormTableName',
        'DirectionID',
        'DirectionNo',
        'DirectionName',
        'Posted',
        'PostedDate',
        'PostedUserID',
        'Locked',
        'LockedDate',
        'LockedUserID',
        'StatusID',
        'StatusValue',
        'StatusDescription',
        'ArrCoaChecked',
        'Inactive',
        'PeriodID',
        'PeriodType',
        'PeriodName',
        'PeriodFromDate',
        'PeriodToDate',
        'InputCompanyID',
        'InputCompanyNo',
        'InputCompanyName',
        'DFID',
        'DFKey',
        'WFItemName',
        'WFItemID',
        'WFName',
        'WFID'
    ];
    public $timestamps = false;


}

?>
