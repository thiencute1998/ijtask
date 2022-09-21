<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Project extends Model{
    use IjFullTextSearch;
    protected $table = 'project';
    protected $primaryKey = 'ProjectID';

    protected static $searchable = ['ProjectNo', 'ProjectName'];

    public static function search(Request $request){
        $data = null;
        $query = Project::query();
        $auth = Auth::user();
        $where = [];
        if($request->post('ProjectNo')){
            $query->where('ProjectNo', 'like', '%'.$request->ProjectNo.'%');
        }
        if($request->post('TabmisNo')){
            $query->where('TabmisNo', 'like', '%'.$request->TabmisNo.'%');
        }
        if($request->post('ProjectName')){
            $query->where('ProjectName', 'like', '%'.$request->ProjectName.'%');
        }
        if($request->post('StartedDate') && $request->post('StartedDate')['dateTime']){
            $query->whereDate('StartedDate', date('Y-m-d',strtotime(str_replace('/','-',$request->post('StartedDate')['dateTime']))));
        }
        if($request->post('FinishedDate') && $request->post('FinishedDate')['dateTime']){
            $query->whereDate('FinishedDate', date('Y-m-d',strtotime(str_replace('/','-',$request->post('FinishedDate')['dateTime']))));
        }
        if($request->post('Status')){
            $query->where('Status', $request->Status);
        }
        if($request->post('PercentCompleted')){
            $query->where('PercentCompleted', $request->PercentCompleted);
        }
        if($request->post('InvestorID')){
            $query->where('InvestorID', $request->InvestorID);
        }
        if($request->post('StateOrganID')){
            $query->where('StateOrganID', $request->StateOrganID);
        }
        if($request->post('InvestDecisionOrganID')){
            $query->where('InvestDecisionOrganID', $request->InvestDecisionOrganID);
        }
        if($request->post('ManagementLevel')){
            $query->where('ManagementLevel', $request->ManagementLevel);
        }
        if($request->post('SectorID')){
            $query->where('SectorID', $request->SectorID);
        }
        if($request->post('ProgramID')){
            $query->where('ProgramID', $request->ProgramID);
        }
        if($request->post('BuildAddress')){
            $query->where('BuildAddress', 'like', '%'.$request->BuildAddress.'%');
        }
        if($request->post('CapableDesign')){
            $query->where('CapableDesign', 'like', '%'.$request->CapableDesign.'%');
        }
        if($request->post('CapableFulfilling')){
            $query->where('CapableFulfilling', 'like', '%'.$request->CapableFulfilling.'%');
        }
        // DateRange
        if(isset($request->DateRangeStart['fromDate']) && $request->DateRangeStart['fromDate'] !== ''){
            $fromDate = Date::StringToDate($request->DateRangeStart['fromDate']);
            $query->where(DB::raw('DATE_FORMAT(project.StartedDate, "%Y-%m-%d")'), '>=', $fromDate);
        }
        if(isset($request->DateRangeStart['toDate']) && $request->DateRangeStart['toDate'] !== ''){
            $toDate = Date::StringToDate($request->DateRangeStart['toDate']);
            $query->where(DB::raw('DATE_FORMAT(project.StartedDate, "%Y-%m-%d")'), '<=', $toDate);
        }
        if(isset($request->DateRangeFinish['fromDate']) && $request->DateRangeFinish['fromDate'] !== ''){
            $fromDate = Date::StringToDate($request->DateRangeFinish['fromDate']);
            $query->where(DB::raw('DATE_FORMAT(project.FinishedDate, "%Y-%m-%d")'), '>=', $fromDate);
        }
        if(isset($request->DateRangeFinish['toDate']) && $request->DateRangeFinish['toDate'] !== ''){
            $toDate = Date::StringToDate($request->DateRangeFinish['toDate']);
            $query->where(DB::raw('DATE_FORMAT(project.FinishedDate, "%Y-%m-%d")'), '<=', $toDate);
        }

        // Loại dự án
        if($request->post('ProjectCate')){
            $query->join('project_cate','project.ProjectID','=','project_cate.ProjectID')
                ->where(function ($q) use ($request){
                    foreach ($request->ProjectCate as $ProjectCate){
                        $q->orWhere(function ($q) use ($ProjectCate){
                            if($ProjectCate['CateID']){
                                $q->Where('project_cate.CateID','=',$ProjectCate['CateID']);
                            }
                            if($ProjectCate['CateValue']){
                                $q->Where('project_cate.CateValue','=',$ProjectCate['CateValue']);
                            }
                        });
                    }
                });
        }

        // Quyền xem dự án
        $query->select('project.*')->leftJoin('project_per','project_per.ProjectID','=','project.ProjectID');
        $query->where(function ($q) use ($auth){
            $q->where('project_per.EmployeeID',$auth->EmployeeID)
                ->orWhere('project.AccessType',2);
        });

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

        if ($request->has('exportData')) {
            $query->select('project.ProjectID','project.ProjectNo', 'project.TabmisNo','project.STT', 'project.ProjectName', 'project.StartedDate','sys_status_item.StatusDescription as Status', 'project.FinishedDate', 'project.PercentCompleted','doc.DocName as DocName');
            $query->leftJoin('doc','project.ProjectID', '=','doc.ProjectID');
            $query->leftJoin('sys_status_item','project.Status', '=','sys_status_item.StatusValue')
            ->where('sys_status_item.StatusID','=',2);
            return $query->orderBy('ProjectID', 'asc')->paginate(10);

        }

        return $query->groupBy('project.ProjectID')->orderBy('project.ProjectID','desc')->paginate($per_page);
    }

    protected $fillable = [
        'ProjectNo', 'ProjectName','TabmisNo','TabmisDate','ParentID', 'Level',
        'MPeriodID', 'ManagementLevel', 'SectorID', 'SectorName', 'ProgramID', 'ProgramName', 'Group', 'InvestDecisionOrganID',
        'InvestDecisionOrganName', 'InvestorID', 'InvestorName', 'StateOrganID', 'StateOrganName','SbiChapterID', 'SbiChapterNo',
        'SbiChapterName', 'SbiCategoryID', 'SbiCategoryNo', 'SbiCategoryName', 'ProvinceID', 'ProvinceName', 'DistrictID', 'DistrictName',
        'CommuneID', 'CommuneName', 'Status','PercentCompleted', 'BuildAddress', 'CapableFulfilling', 'Tarnget', 'InvestScale', 'CapableDesign', 'ExpectedStartDate', 'InvestdocNo', 'InvestdocDate', 'PacttdocNo', 'PacttdocDate',
        'ExpectedFinishDate', 'ExpectedHandoverDate', 'StartedDate', 'HandoverDate', 'FinishedDate', 'SettlementDate', 'ClosedDate','NOrder', 'Inactive', 'Prefix', 'Suffix','Note', 'NumberValue',
        'AccessType','UserIDCreated','AuthorizedPerson', 'UseCapital'
    ];

    public $timestamps = false;

    public function doc(){
        return $this->hasMany('Module\Listing\Models\Doc','ProjectID','ProjectID');
    }
}

?>
