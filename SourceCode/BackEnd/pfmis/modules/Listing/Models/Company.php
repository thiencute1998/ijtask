<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Module\SysAdmin\Models\SysSetup;

class Company extends Model{
    use IjFullTextSearch;
    protected $table = 'company';
    protected $primaryKey = 'CompanyID';

    protected static $searchable = ['CompanyNo', 'CompanyName', 'Address', 'Tel', 'Fax', 'Email', 'Note'];

    public static function search(Request $request){
        $data = null;
        $query = Company::query();
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('CompanyName')){
            $query->where('CompanyName', 'like', '%'.$request->CompanyName.'%');
        }
        if($request->post('CompanyNo')){
            $query->whereRaw('concat(Prefix, CompanyNo, Suffix) like("%'.$request->CompanyNo.'%")');
        }
        if($request->post('Fax')){
            $query->where('Fax', 'like', '%'.$request->Fax.'%');
        }
        if($request->post('Tel')){
            $query->where('Tel', 'like', '%'.$request->Tel.'%');
        }
        if($request->post('Email')){
            $query->where('Email', 'like', '%'.$request->Email.'%');
        }

        //Danh mục liên kết
        $CompanyLink = $request->post('CompanyLink');
        if($CompanyLink && count($CompanyLink) > 0){
            $query->leftJoin('company_link','company.CompanyID','=','company_link.CompanyID');
            foreach($CompanyLink as $companyLink){
                if ($companyLink['LinkTable']) {
                    $query->where('company_link.LinkTable', '=', $companyLink['LinkTable']);
                }
                if ($companyLink['LinkID']) {
                    $query->where('company_link.LinkID', '=', $companyLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Quyền xem đơn vị
        $query->select('company.*')->leftJoin('company_per','company_per.CompanyID','=','company.CompanyID');
        $query->where(function ($q) use ($auth){
            $q->where('company_per.EmployeeID',$auth->EmployeeID)
            ->orWhere('company.AccessType',2);
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('company.CompanyID');

        if ($request->has('exportData')) {
            $query->select('company.CompanyID','company.CompanyNo', 'company.CompanyName','company.STT', 'company.Tel', 'company.Fax', 'company.Email');
            return $query->orderBy('CompanyID', 'asc')->paginate($request->post('per_page'));
        }
        if($request->post('viewType')==='tree'){
            $query->where('Level', '=', 1)->groupBy('company.CompanyID')->orderBy('company.CompanyNo', 'asc');
            return $query->paginate($per_page);
        }
        return $query->orderBy('company.CompanyID', 'desc')->paginate($per_page);
    }

    public static function getCompany($request)
    {
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        if ($request->get('CompanyName')) {
            $companyName = $request->get('CompanyName');
            $query->where('CompanyName', 'like', '%' . $companyName . '%');
        }

        $query->orderBy('CompanyID', 'DESC');
        $company = $query->paginate($perPage);
        return $company;
    }

    public static function getAllChildCompany($listID){
        $result = [];
        $companies = Company::whereIn('ParentID', $listID)->get();
        if (count($companies)) {
            $companyIDs = [];
            foreach ($companies as $company) {
                $companyIDs[] = $company->CompanyID;
                $result[] = $company;
            }
            $records = Company::getAllChildCompany($companyIDs);
            $result = array_merge($result, $records);
        }

        return $result;
    }

    protected $fillable = [
        'CompanyNo', 'CompanyName', 'ParentID','ParentNo', 'Level', 'Detail', 'Address', 'Tel', 'Fax',
        'Email', 'EmployeeID', 'ContactName', 'ContactTel','ProvinceID', 'ProvinceName',
        'DistrictID', 'DistrictName', 'CommuneID', 'CommuneName', 'Locked', 'Note', 'Inactive',
        'Prefix', 'Suffix','NumberValue','AccessType','UserIDCreated','AuthorizedPerson', 'IsFinancialCompany','Path', 'PathNo',
        'ManagementLevel', 'CenterID', 'CenterNo', 'CenterName', 'SbiChapterID', 'SbiChapterNo', 'isAutOrg', 'AutOrgID', 'AutOrgNo', 'AutOrgName',
        'AutOrgChapterID', 'AutOrgChapterNo', 'AutOrgAddress', 'AutOrgContactName', 'AutOrgContactPosittion', 'AutOrgContactOfficePhone',
        'AutOrgContactHandPhone', 'AutOrgContactMail', 'isFinOrg', 'FinMofID', 'FinMofNo', 'FinMofName', 'FinDofID', 'FinDofNo', 'FinDofName',
        'FinDfpID', 'FinDfpNo', 'FinDfpName', 'isTreOrg', 'TreMofID', 'TreMofNo', 'TreMofName', 'TreDofID', 'TreDofNo', 'TreDofName',
        'TreDfpID', 'TreDfpNo', 'TreDfpName',
    ];
    public $timestamps = false;


}

?>
