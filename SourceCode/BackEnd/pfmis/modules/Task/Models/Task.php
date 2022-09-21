<?php
namespace Module\Task\Models;

use Illuminate\Support\Facades\Log;
use function foo\func;
use IjCore\IjFullTextSearch;
use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Listing\Models\Employee;
use Module\SysAdmin\Models\SysSetup;
use function GuzzleHttp\Psr7\_parse_request_uri;

class Task extends Model{
    use IjFullTextSearch;
    protected $table = 'task';
    protected $primaryKey = 'TaskID';
    protected static $searchable = ['task.TaskNo', 'task.TaskName', 'task.StatusName', 'task.StatusDescription'];

    public static function search(Request $request)
    {
        $filter = $request->get('filter');
        $searchUserAssign = false;
        $authEmployee = Employee::where(['UserID' => Auth::user()->UserID])->first();
        if (!$authEmployee) return false;

        $query = Task::query();
        $query->select(DB::raw('task.*, IF(task.ParentID > 0, task.ParentID, task.TaskID) AS parent_id'));
//        $query->leftJoin('task_assign', 'task.TaskID', '=', 'task_assign.TaskID');
        $per_page = SysSetup::getOption('NumberRowOnPage');
        if($request->get('per_page')){
            $per_page = $request->get('per_page');
        }

        // loại công việc
        if (isset($filter['Type']) && $filter['Type'] != 9) {
            if ($filter['Type'] == 3) {
                $query->where('task.Type', '=', 1);
                $query->where('task.DoneNowType', '=', 1);
            } elseif ($filter['Type'] == 4) {
                $query->where('task.Type', '=', 1);
                $query->where('task.DoneNowType', '=', 2);
            }else{
                $query->where('task.Type', '=', $filter['Type']);
            }
        }

        // tên công việc
        if (isset($filter['TaskName'])) {
            $query->where('task.TaskName', 'Like', '%'.$filter['TaskName'].'%');
        }
        // ưu tiên
        if (isset($filter['Priority'])) {
            $query->where('task.Priority', '=', $filter['Priority']);
        }

        // Ngày tạo
        if (isset($filter['CreateDate']) && !empty($filter['CreateDate']) && !empty($filter['CreateDate']['dateTime'])) {
            $createDate = Date::StringToDate($filter['CreateDate']['dateTime']);
            $query->where(DB::raw('DATE_FORMAT(task.CreateDate, "%Y-%m-%d")'), $filter['CreateDate']['operator'], $createDate);
        }

        // Ngày bắt đầu
        if (isset($filter['StartDate']) && !empty($filter['StartDate']) && !empty($filter['StartDate']['dateTime'])) {
            $startDate = Date::StringToDate($filter['StartDate']['dateTime']);
            $query->where(DB::raw('DATE_FORMAT(task.StartDate, "%Y-%m-%d")'), $filter['StartDate']['operator'], $startDate);
        }

        // Ngày hoàn thành
        if (isset($filter['DueDate']) && !empty($filter['DueDate']) && !empty($filter['DueDate']['dateTime'])) {
            $dueDate = Date::StringToDate($filter['DueDate']['dateTime']);
            $query->where(DB::raw('DATE_FORMAT(task.DueDate, "%Y-%m-%d")'), $filter['DueDate']['operator'], $dueDate);
        }

        // Tình trạng
        if (isset($filter['Status']) && count($filter['Status']) > 0) {
            $query->where(function ($q) use ($filter) {
                foreach ($filter['Status'] as $status) {
                    $q->orWhere(function($q) use ($status) {
                        if (isset($status['StatusID']) && $status['StatusID']) {
                            $q->Where('task.StatusID', '=', $status['StatusID']);
                        }
                        if (isset($status['StatusValue']) && $status['StatusValue']) {
                            $q->Where('task.StatusValue', '=', $status['StatusValue']);
                        }
                    });
                }
            });
        }

        // Công việc trễ hạn
        if (isset($filter['Expired']) && $filter['Expired']) {
            $currentDate = date('Y-m-d');
            $query->where(function ($q) {
                $q->where('task.Status', '=', 1);
                $q->orWhere('task.Status', '=', 2);
                $q->orWhere('task.Status', '=', 3);
                $q->orWhere('task.Status', '=', 4);
            });
            $query->where(DB::raw('DATE_FORMAT(task.DueDate, "%Y-%m-%d")'), '<', $currentDate);
        }

        // Tình trạng thực hiện
        if (isset($filter['ExecutionStatus']) && $filter['ExecutionStatus']) {
            $query->where('task.Status', '=', $filter['ExecutionStatus']);
        }

        // Phần trăm hoàn thành
        if (isset($filter['PercentCompleted']) && $filter['PercentCompleted']['number'] != '') {
            $query->where('PercentCompleted', $filter['PercentCompleted']['operator'], $filter['PercentCompleted']['number']);
        }

        // Đơn vị
        if(isset($filter['CompanyID'])){
            $company = Company::find($filter['CompanyID']);
            $companyLevel = 0;
            if($company){
                $companyLevel = $company->Level;
            }
            switch ($companyLevel){
                case 1:
                    $query->where(function($q) use ($company) {
                        $q->where('task.CompanyID', '=', $company->CompanyID);
                    });
                    break;
                case 2:
                    $query->where(function($q) use ($company) {
                        $q->where('task.CompanyID', '=', $company->CompanyID);
                    });
                    break;
                case 3:
                    $query->where(function($q) use ($company) {
                        $q->where('task.CompanyID', '=', $company->CompanyID);
                    });
                    break;
                default:
                    $query->where('task.CompanyID', '=', $company->CompanyID);
                    break;
            }
        }

        // Tài liệu
        if(isset($filter['DocID'])){
            $query->leftJoin(DB::raw('task_file TF'), 'task.TaskID', '=', 'TF.TaskID');
            $query->leftJoin(DB::raw('task_video TV'), 'task.TaskID', '=', 'TV.TaskID');
            $query->where(function($q) use ($filter) {
                $q->where('TF.DocID', '=', $filter['DocID']);
                $q->orWhere('TV.DocID', '=', $filter['DocID']);
            });
        }

        // DateRange
        if(isset($filter['fromDate']) && $filter['fromDate'] !== ''){
            $fromDate = Date::StringToDate($filter['fromDate']);
            $query->where(DB::raw('DATE_FORMAT(task.CreateDate, "%Y-%m-%d")'), '>=', $fromDate);
        }
        if(isset($filter['toDate']) && $filter['toDate'] !== ''){
            $toDate = Date::StringToDate($filter['toDate']);
            $query->where(DB::raw('DATE_FORMAT(task.CreateDate, "%Y-%m-%d")'), '<=', $toDate);
        }

        // Danh mục liên kết
        if (isset($filter['TaskLink']) && (count($filter['TaskLink']) > 0)) {
            $query->join('task_link', 'task.TaskID', '=', 'task_link.TaskID');
            foreach ($filter['TaskLink'] as $taskLink) {
                if ($taskLink['LinkTable']) {
                    $query->where('LinkTable', '=', $taskLink['LinkTable']);
                }
                if ($taskLink['LinkID']) {
                    $query->where('LinkID', '=', $taskLink['LinkID']);
                }
            }
        }

        // loại công việc khác
        if (isset($filter['Type']) && $filter['Type'] == 9 && isset($filter['TaskCateList'])) {
            $sqlRaw = '(SELECT T.* FROM (SELECT task_cate.* FROM task_cate WHERE ';
            foreach ($filter['TaskCateList'] as $key => $taskCateList) {
                if ($taskCateList['CateID'] || $taskCateList['CateValue']) {
                    $sqlRaw .= '(';
                    if ($taskCateList['CateID']) {
                        $sqlRaw .= 'CateID = ' . $taskCateList['CateID'];
                    }
                    if ($taskCateList['CateValue']) {
                        $sqlRaw .= ' AND ';
                        $sqlRaw .= 'CateValue = ' . $taskCateList['CateValue'];
                    }
                    $sqlRaw .= ')';
                }
                if ((count($filter['TaskCateList']) - 1) > $key) {
                    $sqlRaw .= ' OR ';
                }
            }
            $sqlRaw .= ' GROUP BY TaskID, CateID, CateValue ) AS T GROUP BY T.TaskID HAVING COUNT(*) = ' . count($filter['TaskCateList']);
            $sqlRaw .= ') AS TC';
            $query->join(DB::raw($sqlRaw), 'task.TaskID', '=', 'TC.TaskID');
        }

        // Inactive
        if (isset($filter['Inactive'])) {
            $query->where('task.Inactive', '=', $filter['Inactive']);
        }

        // full text search
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Quyền truy cập
        if (isset($filter['AccessType'])) {
            $query->where('task.AccessType', '=', $filter['AccessType']);
        }

        // Người tạo
        if (isset($filter['CreateEmployeeID']) && $filter['CreateEmployeeID']) {
            if (!$searchUserAssign) {
                $query->join('task_assign', 'task.TaskID', '=', 'task_assign.TaskID');
            }
            $query->where('task_assign.EmployeeID', '=', $filter['CreateEmployeeID']);
            $query->where('task_assign.IsCreator', '=', 1);
            $searchUserAssign = true;
        }

        //Người giao việc
        if (isset($filter['AssignEmployeeID']) && $filter['AssignEmployeeID']) {
            if (!$searchUserAssign) {
                $query->join('task_assign', 'task.TaskID', '=', 'task_assign.TaskID');
            }
            $query->where('task_assign.EmployeeID', '=', $filter['AssignEmployeeID']);
            $query->where('task_assign.IsAssignee', '=', 1);
            $searchUserAssign = true;
        }

        // Người nhận việc
        if(isset($filter['ReceiveEmployeeID']) && $filter['ReceiveEmployeeID']){
            if (!$searchUserAssign) {
                $query->join('task_assign', 'task.TaskID', '=', 'task_assign.TaskID');
            }
            $query->where('task_assign.EmployeeID', '=', $filter['ReceiveEmployeeID']);
            $query->where(function($q) use ($request) {
                $q->Where('task_assign.IsMainResponsiblePerson', '=', 1);
                $q->orWhere('task_assign.IsResponsiblePerson', '=', 1);
            });
            $searchUserAssign = true;
        }

        // Người chịu trách nhiệm
        if(isset($filter['ResponEmployeeID']) && $filter['ResponEmployeeID']){
            if (!$searchUserAssign) {
                $query->join('task_assign', 'task.TaskID', '=', 'task_assign.TaskID');
            }
            $query->where('task_assign.EmployeeID', '=', $filter['ResponEmployeeID']);
            $query->where('IsResponsiblePerson', '=', 1);
            $searchUserAssign = true;
        }

        // Người thực hiện
        if (isset($filter['ImplementEmployeeID']) && $filter['ImplementEmployeeID']) {
            if (!$searchUserAssign) {
                $query->join('task_assign', 'task.TaskID', '=', 'task_assign.TaskID');
            }
            $query->where('task_assign.EmployeeID', '=', $filter['ImplementEmployeeID']);
            $query->where('task_assign.IsExecutor', '=', 1);
            $searchUserAssign = true;
        }

        // Quyền được xem công việc
//        if (Auth::user()->UserType !== 1) {}
        if (!$searchUserAssign) {
            $queryShare = clone $query;
            $query->where(function ($q) use ($filter, $authEmployee) {
                $q->where(function ($q){
                    $q->where('task.AccessType', '=', 2);
                    $q->whereNull('task.PublicCompanyID');
                });
                $q->orWhere(function ($q) use ($filter, $authEmployee) {
                    $q->where('task.AccessType', '=', 2);
                    $q->whereNotNull('task.PublicCompanyID');
                    $q->where('task.PublicCompanyID', '=', $authEmployee->CompanyID);
                });
            });

            $queryShare->join('task_assign', 'task.TaskID', '=', 'task_assign.TaskID');
            $queryShare->where('task_assign.EmployeeID', '=', $authEmployee->EmployeeID);


            $query->union($queryShare);
            $query = DB::query()->fromSub($query, 'TU');
        }

        // loại hiển thị
        if ($request->get('viewType')) {
            $viewType = $request->get('viewType');
            switch ($viewType) {
                case 'list':
                    if (!$searchUserAssign) {
                        $query->groupBy('TU.TaskID');
                        $query->orderBy('TU.TaskID', 'DESC');
                    }else{
                        $query->groupBy('task.TaskID');
                        $query->orderBy('task.TaskID', 'DESC');
                    }
                    return $query->paginate($per_page);
                    break;
                case 'tree':
                    $Level = ($filter && !empty($filter['Level'])) ? $filter['Level'] : 1;
                    if (!$searchUserAssign) {
                        $query->where('TU.Level', '=', $Level);
                        $query->groupBy('TU.TaskID');
                        $query->orderBy('TU.TaskID', 'DESC');
                    }else{
                        $query->where('task.TaskID', '=', $Level);
                        $query->groupBy('task.TaskID');
                        $query->orderBy('task.TaskID', 'DESC');
                    }
                    $results = $query->paginate($per_page);
                    return $results;
                    break;
                case 'kanban':
                    if (!$searchUserAssign) {
                        $query->groupBy('TU.TaskID');
                        $query->orderBy('TU.TaskNo', 'DESC');
                    }else{
                        $query->groupBy('task.TaskID');
                        $query->orderBy('task.TaskNo', 'DESC');
                    }
                    return $query->paginate($per_page);
                    break;
                default:
                    $query->orderBy('TaskNo', 'DESC');
                    return $query->paginate($per_page);
                    break;
            }
        }

        return $query->paginate($per_page);
    }

    public static function countTask($request, $type = 1){
        $query = Task::query();
        $authEmployee = Auth::user();
        $filter = $request->get('filter');
        $total = 0;
        $query->leftJoin('task_assign', 'task.TaskID', '=', 'task_assign.TaskID');

        // Quyền được xem công việc
//        if (Auth::user()->UserType !== 1) {
            $query->where(function ($q) use ($authEmployee){
                $q->where('task_assign.EmployeeID', '=', $authEmployee->EmployeeID);
                $q->orWhere(function ($q) {
                    $q->where('task.AccessType', '=', 2);
                    $q->whereNull('task.PublicCompanyID');
                });
                $q->orWhere(function ($q) use ($authEmployee) {
                    $q->where('task.AccessType', '=', 2);
                    $q->whereNotNull('task.PublicCompanyID');
                    $q->where('task.PublicCompanyID', '=', $authEmployee->CompanyID);
                });
            });
//        }

        // DateRange
        if(isset($filter['fromDate']) && $filter['fromDate'] !== ''){
            $fromDate = Date::StringToDate($filter['fromDate']);
            $query->where(DB::raw('DATE_FORMAT(task.CreateDate, "%Y-%m-%d")'), '>=', $fromDate);
        }
        if(isset($filter['toDate']) && $filter['toDate'] !== ''){
            $toDate = Date::StringToDate($filter['toDate']);
            $query->where(DB::raw('DATE_FORMAT(task.CreateDate, "%Y-%m-%d")'), '<=', $toDate);
        }

        switch ($type) {
            case 1:
                $total = $query->where('Status', '=', 1)->count(DB::raw('DISTINCT task.TaskID'));
                break;

            case 2:
                $total = $query->where('Status', '=', 2)->count(DB::raw('DISTINCT task.TaskID'));
                break;
            case 3:
                $total = $query->where('Status', '=', 5)->count(DB::raw('DISTINCT task.TaskID'));
                break;
            case 4:
                $total = $query->where('Status', '=', 3)->count(DB::raw('DISTINCT task.TaskID'));
                break;
            case 5:
                $total = $query->where('Status', '=', 6)->count(DB::raw('DISTINCT task.TaskID'));
                break;
            case 6:
                $total = $query->where('Priority', '=', 1)->count(DB::raw('DISTINCT task.TaskID'));
                break;
            case 7:
                $currentDate = date('Y-m-d');
                $query->where(function ($q) {
                    $q->where('task.Status', '=', 1);
                    $q->orWhere('task.Status', '=', 2);
                    $q->orWhere('task.Status', '=', 3);
                    $q->orWhere('task.Status', '=', 4);
                });
                $query->where(DB::raw('DATE_FORMAT(task.DueDate, "%Y-%m-%d")'), '<', $currentDate);
                $total = $query->count(DB::raw('DISTINCT task.TaskID'));
                break;
            case 8:
                $total = 0;
                break;
            case 9:
                $query->where('task_assign.EmployeeID', '=', $authEmployee->EmployeeID);
                $query->where('task_assign.IsAssignee', '=', 1);
                $total = $query->count(DB::raw('DISTINCT task.TaskID'));
                break;
            case 10:
                $query->where('task_assign.EmployeeID', '=', $authEmployee->EmployeeID);
                $query->where('task_assign.IsResponsiblePerson', '=', 1);
                $total = $query->count(DB::raw('DISTINCT task.TaskID'));
                break;
            case 11:
                $query->where('task_assign.EmployeeID', '=', $authEmployee->EmployeeID);
                $query->where('task_assign.IsExecutor', '=', 1);
//                $query->where(function ($q) {
//                    $q->where('task_assign.IsMainResponsiblePerson', '=', 1);
//                    $q->orWhere('task_assign.IsResponsiblePerson', '=', 1);
//                    $q->orWhere('task_assign.IsExecutor', '=', 1);
//                });
                $total = $query->count(DB::raw('DISTINCT task.TaskID'));
                break;
            default:
                $total = 0;
                break;
        }
        return $total;
    }

    public static function populateChildrenTasks($tasks, &$listTaskChild = array()){

        $taskIDArr = [];
        foreach ($tasks as $task) {
            $taskIDArr[] = $task->TaskID;
        }
        if (count($taskIDArr)) {
            $query = Task::query();
            $query->whereIn('ParentID', $taskIDArr);
            $tasksChild = $query->get();
            if (count($tasksChild)) {
                foreach ($tasksChild as $taskChild) {
                    $listTaskChild[] = $taskChild;
                }
                self::populateChildrenTasks($tasksChild, $listTaskChild);
            }else{
                return $listTaskChild;
            }
        }else{
            return $listTaskChild;
        }

    }
    public static function populateParentTasks($tasks){
        $listTaskParent = [];
        $taskIDArr = [];
        foreach ($tasks as $task) {
            if ($task->ParentID) {
                $taskIDArr[] = $task->ParentID;
            }
        }
        if (count($taskIDArr)) {
            $query = Task::query();
            $query->whereIn('TaskID', $taskIDArr);
            $tasksParent = $query->get();
            if (count($tasksParent)) {
                self::populateChildrenTasks($tasksParent);
                foreach ($tasksParent as $taskParent) {
                    $listTaskParent[] = $taskParent;
                }
            }
        }

        return $listTaskParent;

    }
    protected $fillable = [
        'TaskNo', 'TaskName', 'ParentID', 'ParentName', 'ParentNo', 'Level', 'Detail', 'UomID', 'TaskDescription', 'Priority',
        'Status', 'AccessType', 'CreateDate', 'CreateEmployeeID', 'AssignEmployeeID', 'ResponEmployeeID',
        'StartDate', 'DueDate', 'Duration', 'EstimatedQuantity', 'ActualCompletedDate', 'ActualWork',
        'ActualQuantity', 'NethodCompleted', 'PercentCompleted', 'ProjectID', 'ProjectName', 'ProjectNo', 'CustomerID', 'CustomerName',
        'CustomerNo', 'VendorID', 'VendorName', 'VendorNo', 'ObjectID', 'ObjectName', 'ObjectNo', 'ContractID', 'ContractNo', 'ContractName', 'RemiderPeriod',
        'RemiderDate', 'RemiderHour', 'RemiderMinute', 'RemiderDayOfWeek', 'RemiderDayOfMonth', 'RemiderDayOfQuater',
        'RemiderDayOfYear', 'RemiderContent', 'RemiderOverDue', 'RemiderOverDueDateNo', 'RemiderOverDueDateHour',
        'RemiderOverDueDateMinute', 'RemiderOverDueContent', 'NOrder', 'Inactive', 'CalendarTypeID', 'CheckEmployeeID',
        'CompanyID', 'PerformingDescription', 'PublicCompanyID', 'Prefix', 'Suffix', 'NumberValue', 'WorkFlowStep',
        'ItemID', 'ItemNo', 'ItemName', 'ParentCompanyID1', 'ParentCompanyID2',
        'ParentCompanyID3', 'StatusID', 'StatusName', 'StatusValue', 'StatusDescription', 'Type', 'DoneNowType'
    ];
    public $timestamps = false;

    public static function getHourWork($fromdate, $todate){
        $fromday = date('Y/m/d', strtotime($fromdate));
        $today = date('Y/m/d', strtotime($todate));
        $fromdate = date('Y/m/d h:i', strtotime($fromdate));
        $todate = date('Y/m/d h:i', strtotime($todate));
        if($fromday >= $today){
            return $fromdate;
        }else{
            $diff = $today->diff($fromday)->format("%a");
            $hourInDay = 8;
            $fromHourMorning = '08:00';
            $endHourMorning = '12:00';
            $fromHourAfter = '13:30';
            $endHourAfter = '17:30';
            return $diff;
        }
    }

    public static function copyTask($taskID, $taskIDCopy, $userExcept){
        $task = Task::find($taskID);
        // Task Assign
        $taskAssignsCopy = TaskAssign::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskAssignArrInsert = [];
        foreach ($taskAssignsCopy as $assignCopy) {
            $taskAssignArrInsert[] = [
                'TaskID' => $task->TaskID,
                'TaskName' => $task->TaskName,
                'EmployeeID' => $assignCopy->EmployeeID,
                'EmployeeName' => $assignCopy->EmployeeName,
                'StartDate' => $assignCopy->StartDate,
                'DueDate' => $assignCopy->DueDate,
                'EstimatedHour' => $assignCopy->EstimatedHour,
                'EstimatedQuantity' => $assignCopy->EstimatedQuantity,
                'EstimatedQuantityRate' => $assignCopy->EstimatedQuantityRate,
                'Description' => $assignCopy->Description,
                'IsChecker' => $assignCopy->IsChecker,
                'IsMainResponsiblePerson' => $assignCopy->IsMainResponsiblePerson,
                'IsResponsiblePerson' => $assignCopy->IsResponsiblePerson,
                'IsAssignee' => $assignCopy->IsAssignee,
                'IsCreator' => $assignCopy->IsCreator,
                'IsFollower' => $assignCopy->IsFollower,
                'IsExecutor' => $assignCopy->IsExecutor
            ];
        }
        if (count($taskAssignArrInsert)) {
            DB::table('task_assign')->insert($taskAssignArrInsert);
        }

        // TaskPer
        $taskPersCopy = TaskPer::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskPerArrInsert = [];
        foreach ($taskPersCopy as $perCopy) {
            $taskPerArrInsert[] = [
                'TaskID' => $task->TaskID,
                'EmployeeID' => $perCopy->EmployeeID,
                'Access' => $perCopy->Access,
                'Edit' => $perCopy->Edit,
                'Delete' => $perCopy->Delete,
                'AccessField' => $perCopy->AccessField,
                'EditField' => $perCopy->EditField,
            ];
        }
        if (count($taskPerArrInsert)) {
            DB::table('task_per')->insert($taskPerArrInsert);
        }

        // TaskPerCate
        $taskPerCateCopy = TaskPerCate::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskPerCateArrInsert = [];
        foreach ($taskPerCateCopy as $perCateCopy) {
            $taskPerCateArrInsert[] = [
                'TaskID' => $taskID,
                'EmployeeID' => $perCateCopy->EmployeeID,
                'Access' => $perCateCopy->Access,
                'Edit' => $perCateCopy->Edit,
                'Delete' => $perCateCopy->Delete,
                'AccessField' => $perCateCopy->AccessField,
                'EditField' => $perCateCopy->EditField,
            ];
        }
        if (count($taskPerCateArrInsert)) {
            DB::table('task_per_cate')->insert($taskPerCateArrInsert);
        }

        // TaskPerAssign
        $taskPerAssignCopy = TaskPerAssign::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskPerAssignArrInsert = [];
        foreach ($taskPerAssignCopy as $perAssignCopy) {
            $taskPerAssignArrInsert[] = [
                'TaskID' => $taskID,
                'EmployeeID' => $perAssignCopy->EmployeeID,
                'Access' => $perAssignCopy->Access,
                'Edit' => $perAssignCopy->Edit,
                'Delete' => $perAssignCopy->Delete,
                'AccessField' => $perAssignCopy->AccessField,
                'EditField' => $perAssignCopy->EditField,
            ];
        }
        if (count($taskPerAssignArrInsert)) {
            DB::table('task_per_assign')->insert($taskPerAssignArrInsert);
        }

        // TaskPerLink
        $taskPerLinkCopy = TaskPerLink::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskPerLinkArrInsert = [];
        foreach ($taskPerLinkCopy as $perLinkCopy) {
            $taskPerLinkArrInsert[] = [
                'TaskID' => $taskID,
                'EmployeeID' => $perLinkCopy->EmployeeID,
                'Access' => $perLinkCopy->Access,
                'Edit' => $perLinkCopy->Edit,
                'Delete' => $perLinkCopy->Delete,
                'AccessField' => $perLinkCopy->AccessField,
                'EditField' => $perLinkCopy->EditField,
            ];
        }
        if (count($taskPerLinkArrInsert)) {
            DB::table('task_per_link')->insert($taskPerLinkArrInsert);
        }

        // TaskPerExecution
        $taskPerExecutionCopy = TaskPerExecution::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskPerExecutionArrInsert = [];
        foreach ($taskPerExecutionCopy as $perExecutionCopy) {
            $taskPerExecutionArrInsert[] = [
                'TaskID' => $taskID,
                'EmployeeID' => $perExecutionCopy->EmployeeID,
                'Access' => $perExecutionCopy->Access,
                'Edit' => $perExecutionCopy->Edit,
                'Delete' => $perExecutionCopy->Delete,
                'AccessField' => $perExecutionCopy->AccessField,
                'EditField' => $perExecutionCopy->EditField,
            ];
        }
        if (count($taskPerExecutionArrInsert)) {
            DB::table('task_per_execution')->insert($taskPerExecutionArrInsert);
        }

        // TaskPerChecklist
        $taskPerChecklistCopy = TaskPerChecklist::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskPerChecklistArrInsert = [];
        foreach ($taskPerChecklistCopy as $perChecklistCopy) {
            $taskPerChecklistArrInsert[] = [
                'TaskID' => $taskID,
                'EmployeeID' => $perChecklistCopy->EmployeeID,
                'Access' => $perChecklistCopy->Access,
                'Edit' => $perChecklistCopy->Edit,
                'Delete' => $perChecklistCopy->Delete,
                'AccessField' => $perChecklistCopy->AccessField,
                'EditField' => $perChecklistCopy->EditField,
            ];
        }
        if (count($taskPerChecklistArrInsert)) {
            DB::table('task_per_checklist')->insert($taskPerChecklistArrInsert);
        }

        // TaskPerValuation
        $taskPerValuationCopy = TaskPerValuation::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskPerValuationArrInsert = [];
        foreach ($taskPerValuationCopy as $perValuationCopy) {
            $taskPerValuationArrInsert[] = [
                'TaskID' => $taskID,
                'EmployeeID' => $perValuationCopy->EmployeeID,
                'Access' => $perValuationCopy->Access,
                'Edit' => $perValuationCopy->Edit,
                'Delete' => $perValuationCopy->Delete,
                'AccessField' => $perValuationCopy->AccessField,
                'EditField' => $perValuationCopy->EditField,
            ];
        }
        if (count($taskPerValuationArrInsert)) {
            DB::table('task_per_valuation')->insert($taskPerValuationArrInsert);
        }

        // TaskPerExpense
        $taskPerExpenseCopy = TaskPerExpense::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskPerExpenseArrInsert = [];
        foreach ($taskPerExpenseCopy as $perExpenseCopy) {
            $taskPerExpenseArrInsert[] = [
                'TaskID' => $taskID,
                'EmployeeID' => $perExpenseCopy->EmployeeID,
                'Access' => $perExpenseCopy->Access,
                'Edit' => $perExpenseCopy->Edit,
                'Delete' => $perExpenseCopy->Delete,
                'AccessField' => $perExpenseCopy->AccessField,
                'EditField' => $perExpenseCopy->EditField,
            ];
        }
        if (count($taskPerExpenseArrInsert)) {
            DB::table('task_per_expense')->insert($taskPerExpenseArrInsert);
        }

        // TaskPerExpenseDetail
        $taskPerExpenseDetailCopy = TaskPerExpenseDetail::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskPerExpenseDetailArrInsert = [];
        foreach ($taskPerExpenseDetailCopy as $perExpenseDetailCopy) {
            $taskPerExpenseDetailArrInsert[] = [
                'TaskID' => $taskID,
                'EmployeeID' => $perExpenseDetailCopy->EmployeeID,
                'Access' => $perExpenseDetailCopy->Access,
                'Edit' => $perExpenseDetailCopy->Edit,
                'Delete' => $perExpenseDetailCopy->Delete,
                'AccessField' => $perExpenseDetailCopy->AccessField,
                'EditField' => $perExpenseDetailCopy->EditField,
            ];
        }
        if (count($taskPerExpenseDetailArrInsert)) {
            DB::table('task_per_expense_detail')->insert($taskPerExpenseDetailArrInsert);
        }

        // TaskPerFile
        $taskPerFileCopy = TaskPerFile::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskPerFileArrInsert = [];
        foreach ($taskPerFileCopy as $perFileCopy) {
            $taskPerFileArrInsert[] = [
                'TaskID' => $taskID,
                'EmployeeID' => $perFileCopy->EmployeeID,
                'Access' => $perFileCopy->Access,
                'Edit' => $perFileCopy->Edit,
                'Delete' => $perFileCopy->Delete,
                'AccessField' => $perFileCopy->AccessField,
                'EditField' => $perFileCopy->EditField,
            ];
        }
        if (count($taskPerFileArrInsert)) {
            DB::table('task_per_file')->insert($taskPerFileArrInsert);
        }

        // TaskPerRequest
        $taskPerRequestCopy = TaskPerRequest::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskPerRequestArrInsert = [];
        foreach ($taskPerRequestCopy as $perRequestCopy) {
            $taskPerRequestArrInsert[] = [
                'TaskID' => $taskID,
                'EmployeeID' => $perRequestCopy->EmployeeID,
                'Access' => $perRequestCopy->Access,
                'Edit' => $perRequestCopy->Edit,
                'Delete' => $perRequestCopy->Delete,
                'AccessField' => $perRequestCopy->AccessField,
                'EditField' => $perRequestCopy->EditField,
            ];
        }
        if (count($taskPerRequestArrInsert)) {
            DB::table('task_per_request')->insert($taskPerRequestArrInsert);
        }

        // TaskPerVideo
        $taskPerVideoCopy = TaskPerVideo::where('TaskID', '=', $taskIDCopy)
            ->whereNotIn('EmployeeID', $userExcept)
            ->get();
        $taskPerVideoArrInsert = [];
        foreach ($taskPerVideoCopy as $perVideoCopy) {
            $taskPerVideoArrInsert[] = [
                'TaskID' => $taskID,
                'EmployeeID' => $perVideoCopy->EmployeeID,
                'Access' => $perVideoCopy->Access,
                'Edit' => $perVideoCopy->Edit,
                'Delete' => $perVideoCopy->Delete,
                'AccessField' => $perVideoCopy->AccessField,
                'EditField' => $perVideoCopy->EditField,
            ];
        }
        if (count($taskPerVideoArrInsert)) {
            DB::table('task_per_video')->insert($taskPerVideoArrInsert);
        }

    }
    //TaskID mã công việc
    //TablePer Bảng cần lấy dữ liệu phân quyền
    //PermissionRole Mảng phân quyền cho vai trò người được giao việc
    public static function Permission($TaskID, $TablePer, $PermissionRole){
        $Permission = DB::table($TablePer)->select(['Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['TaskID' => $TaskID, 'EmployeeID' => Auth::user()->EmployeeID])->first();
        return $Permission;
    }

    public static function PermissionRole($TaskID){
        $TaskAssign = TaskAssign::where(['TaskID' => $TaskID, 'EmployeeID' => Auth::user()->EmployeeID])->first();
        $TaskPer = '';
        $TaskPerCate = '';
        $TaskPerComment = '';
        $TaskPerLink = '';
        $TaskPerExecution = '';
        $TaskPerExpense = '';
        $TaskPerFile = '';
        $TaskPerRequest = '';
        $TaskPerVideo = '';
        if($TaskAssign){
            if($TaskAssign->IsChecker){

            }
            if($TaskAssign->IsMainResponsiblePerson){

            }
            if($TaskAssign->IsResponsiblePerson){

            }
            if($TaskAssign->IsAssignee){

            }
            if($TaskAssign->IsCreator){

            }
            if($TaskAssign->IsFollower){

            }
            if($TaskAssign->IsExecution){

            }
        }else{
            return null;
        }
    }

    public static function genPer($TaskID, $EmployeeID){
        $TaskAssign = TaskAssign::where(['TaskID' => $TaskID, 'EmployeeID' => $EmployeeID])->first();
        Task::deleteAllPer($TaskID, $EmployeeID);
        if($TaskAssign){
            $AccessGen = 1;
            $EditGen = 0;
            $DeleteGen = 0;
            $FieldGenAccess = 'all';
            $FieldGenEdit = '';

            $AccessAssign = 1;
            $EditAssign = 0;
            $DeleteAssign = 0;
            $FieldAssignAccess = 'all';
            $FieldAssignEdit = '';

            $AccessCate = 1;
            $EditCate = 1;
            $DeleteCate = 1;
            $FieldCateAccess = 'all';
            $FieldCateEdit = 'all';

            $AccessLink = 1;
            $EditLink = 1;
            $DeleteLink = 1;
            $FieldLinkAccess = 'all';
            $FieldLinkEdit = 'all';

            $AccessExecution = 1;
            $EditExecution = 0;
            $DeleteExecution = 0;
            $FieldExecutionAccess = 'all';
            $FieldExecutionEdit = '';

            $AccessChecklist = 1;
            $EditChecklist = 0;
            $DeleteChecklist = 0;
            $FieldChecklistAccess = 'all';
            $FieldChecklistEdit = '';

            $AccessValuation = 1;
            $EditValuation = 0;
            $DeleteValuation = 0;
            $FieldValuationAccess = 'all';
            $FieldValuationEdit = '';

            $AccessExpense = 1;
            $EditExpense = 1;
            $DeleteExpense = 1;
            $FieldExpenseAccess = 'all';
            $FieldExpenseEdit = 'all';

            $AccessExpenseDetail = 1;
            $EditExpenseDetail = 1;
            $DeleteExpenseDetail = 1;
            $FieldExpenseDetailAccess = 'all';
            $FieldExpenseDetailEdit = 'all';

            $AccessFile = 1;
            $EditFile = 1;
            $DeleteFile = 0;
            $FieldFileAccess = '';
            $FieldFileEdit = '';

            $AccessRequest = 1;
            $EditRequest = 0;
            $DeleteRequest = 0;
            $FieldRequestAccess = 'all';
            $FieldRequestEdit = '';

            $AccessVideo = 1;
            $EditVideo = 1;
            $DeleteVideo = 0;
            $FieldVideoAccess = '';
            $FieldVideoEdit = '';

            if($TaskAssign->IsChecker == 1){
                $EditChecklist = 1;
                $DeleteChecklist = 1;
                $FieldChecklistEdit = 'all';

                $EditValuation = 1;
                $DeleteValuation = 1;
                $FieldValuationEdit = 'all';
            }
            if($TaskAssign->IsMainResponsiblePerson == 1){
                $AccessGen = 1;
                $EditGen = 1;
                $DeleteGen = 1;
                $FieldGenAccess = 'all';
                $FieldGenEdit = 'all';

                $AccessLink = 1;
                $EditLink = 1;
                $DeleteLink = 1;
                $FieldLinkAccess = 'all';
                $FieldLinkEdit = 'all';

                $AccessExecution = 1;
                $FieldExecutionAccess = 'all';

                $AccessChecklist = 1;
                $FieldChecklistAccess = 'all';

                $AccessFile = 1;
                $EditFile = 1;
                $DeleteFile = 1;
                $FieldFileAccess = 'all';
                $FieldFileEdit = 'all';

                $AccessRequest = 1;
                $EditRequest = 1;
                $DeleteRequest = 1;
                $FieldRequestAccess = 'all';
                $FieldRequestEdit = 'all';

                $AccessVideo = 1;
                $EditVideo = 1;
                $DeleteVideo = 1;
                $FieldVideoAccess = 'all';
                $FieldVideoEdit = 'all';
            }
            if($TaskAssign->IsResponsiblePerson == 1){
                $AccessGen = 1;
                $EditGen = 1;
                $DeleteGen = 1;
                $FieldGenAccess = 'all';
                $FieldGenEdit = 'all';

                $AccessFile = 1;
                $EditFile = 1;
                $DeleteFile = 1;
                $FieldFileAccess = 'ạll';
                $FieldFileEdit = 'all';

                $AccessRequest = 1;
                $EditRequest = 1;
                $DeleteRequest = 1;
                $FieldRequestAccess = 'all';
                $FieldRequestEdit = 'all';

                $AccessVideo = 1;
                $EditVideo = 1;
                $DeleteVideo = 1;
                $FieldVideoAccess = 'all';
                $FieldVideoEdit = 'all';
            }
            if($TaskAssign->IsAssignee == 1){
                $AccessGen = 1;
                $EditGen = 1;
                $DeleteGen = 1;
                $FieldGenAccess = 'all';
                $FieldGenEdit = 'all';

                $AccessAssign = 1;
                $EditAssign = 1;
                $DeleteAssign = 1;
                $FieldAssignAccess = 'all';
                $FieldAssignEdit = 'all';

                $AccessFile = 1;
                $EditFile = 1;
                $DeleteFile = 1;
                $FieldFileAccess = 'all';
                $FieldFileEdit = 'all';

                $AccessRequest = 1;
                $EditRequest = 1;
                $DeleteRequest = 1;
                $FieldRequestAccess = 'all';
                $FieldRequestEdit = 'all';

                $AccessVideo = 1;
                $EditVideo = 1;
                $DeleteVideo = 1;
                $FieldVideoAccess = 'all';
                $FieldVideoEdit = 'all';
            }
            if($TaskAssign->IsCreator == 1){
                $AccessGen = 1;
                $EditGen = 1;
                $DeleteGen = 1;
                $FieldGenAccess = 'all';
                $FieldGenEdit = 'all';

                $AccessExecution = 1;
                $EditExecution = 1;
                $DeleteExecution = 1;
                $FieldExecutionAccess = 'all';
                $FieldExecutionEdit = 'all';

                $AccessFile = 1;
                $EditFile = 1;
                $DeleteFile = 1;
                $FieldFileAccess = 'all';
                $FieldFileEdit = 'all';

                $AccessRequest = 1;
                $EditRequest = 1;
                $DeleteRequest = 1;
                $FieldRequestAccess = 'all';
                $FieldRequestEdit = 'all';

                $AccessVideo = 1;
                $EditVideo = 1;
                $DeleteVideo = 1;
                $FieldVideoAccess = 'all';
                $FieldVideoEdit = 'all';
            }
            if($TaskAssign->IsFollower == 1){
                $AccessFile = 1;
                $EditFile = 1;
                $DeleteFile = 1;
                $FieldFileAccess = 'all';
                $FieldFileEdit = 'all';

                $AccessVideo = 1;
                $EditVideo = 1;
                $DeleteVideo = 1;
                $FieldVideoAccess = 'all';
                $FieldVideoEdit = 'all';
            }
            if ($TaskAssign->IsExecutor == 1) {
                $AccessGen = 1;
                $EditGen = 1;
                $DeleteGen = 1;
                $FieldGenAccess = 'all';
                $FieldGenEdit = 'all';

                $AccessExecution = 1;
                $EditExecution = 1;
                $DeleteExecution = 1;
                $FieldExecutionAccess = 'all';
                $FieldExecutionEdit = 'all';

                $AccessFile = 1;
                $EditFile = 1;
                $DeleteFile = 1;
                $FieldFileAccess = 'all';
                $FieldFileEdit = 'all';

                $AccessRequest = 1;
                $EditRequest = 1;
                $DeleteRequest = 1;
                $FieldRequestAccess = 'all';
                $FieldRequestEdit = 'all';

                $AccessVideo = 1;
                $EditVideo = 1;
                $DeleteVideo = 1;
                $FieldVideoAccess = 'all';
                $FieldVideoEdit = 'all';
            }

            $TaskPer = new TaskPer([
                'TaskID' => $TaskID,
                'EmployeeID' => $EmployeeID,
                'Access' => $AccessGen,
                'Edit' => $EditGen,
                'Delete' => $DeleteGen,
                'AccessField' => $FieldGenAccess,
                'EditField' => $FieldGenEdit,
            ]);
            $TaskPer->save();

            $TaskPerCate = new TaskPerCate([
                'TaskID' => $TaskID,
                'EmployeeID' => $EmployeeID,
                'Access' => $AccessCate,
                'Edit' => $EditCate,
                'Delete' => $DeleteCate,
                'AccessField' => $FieldCateAccess,
                'EditField' => $FieldCateEdit,
            ]);
            $TaskPerCate->save();

            $TaskPerAssign = new TaskPerAssign([
                'TaskID' => $TaskID,
                'EmployeeID' => $EmployeeID,
                'Access' => $AccessAssign,
                'Edit' => $EditAssign,
                'Delete' => $DeleteAssign,
                'AccessField' => $FieldAssignAccess,
                'EditField' => $FieldAssignEdit,
            ]);
            $TaskPerAssign->save();

            $TaskPerLink = new TaskPerLink([
                'TaskID' => $TaskID,
                'EmployeeID' => $EmployeeID,
                'Access' => $AccessLink,
                'Edit' => $EditLink,
                'Delete' => $DeleteLink,
                'AccessField' => $FieldLinkAccess,
                'EditField' => $FieldLinkEdit,
            ]);
            $TaskPerLink->save();

            $TaskPerExecution = new TaskPerExecution([
                'TaskID' => $TaskID,
                'EmployeeID' => $EmployeeID,
                'Access' => $AccessExecution,
                'Edit' => $EditExecution,
                'Delete' => $DeleteExecution,
                'AccessField' => $FieldExecutionAccess,
                'EditField' => $FieldExecutionEdit,
            ]);
            $TaskPerExecution->save();


            $TaskPerChecklist = new TaskPerChecklist([
                'TaskID' => $TaskID,
                'EmployeeID' => $EmployeeID,
                'Access' => $AccessChecklist,
                'Edit' => $EditChecklist,
                'Delete' => $DeleteChecklist,
                'AccessField' => $FieldChecklistAccess,
                'EditField' => $FieldChecklistEdit,
            ]);
            $TaskPerChecklist->save();


            $TaskPerValuation = new TaskPerValuation([
                'TaskID' => $TaskID,
                'EmployeeID' => $EmployeeID,
                'Access' => $AccessValuation,
                'Edit' => $EditValuation,
                'Delete' => $DeleteValuation,
                'AccessField' => $FieldValuationAccess,
                'EditField' => $FieldValuationEdit,
            ]);
            $TaskPerValuation->save();

            $TaskPerExpense = new TaskPerExpense([
                'TaskID' => $TaskID,
                'EmployeeID' => $EmployeeID,
                'Access' => $AccessExpense,
                'Edit' => $EditExpense,
                'Delete' => $DeleteExpense,
                'AccessField' => $FieldExpenseAccess,
                'EditField' => $FieldExpenseEdit,
            ]);
            $TaskPerExpense->save();

            $TaskPerExpenseDetail = new TaskPerExpenseDetail([
                'TaskID' => $TaskID,
                'EmployeeID' => $EmployeeID,
                'Access' => $AccessExpenseDetail,
                'Edit' => $EditExpenseDetail,
                'Delete' => $DeleteExpenseDetail,
                'AccessField' => $FieldExpenseDetailAccess,
                'EditField' => $FieldExpenseDetailEdit,
            ]);
            $TaskPerExpenseDetail->save();

            $TaskPerFile = new TaskPerFile([
                'TaskID' => $TaskID,
                'EmployeeID' => $EmployeeID,
                'Access' => $AccessFile,
                'Edit' => $EditFile,
                'Delete' => $DeleteFile,
                'AccessField' => $FieldFileAccess,
                'EditField' => $FieldFileEdit,
            ]);
            $TaskPerFile->save();


            $TaskPerRequest = new TaskPerRequest([
                'TaskID' => $TaskID,
                'EmployeeID' => $EmployeeID,
                'Access' => $AccessRequest,
                'Edit' => $EditRequest,
                'Delete' => $DeleteRequest,
                'AccessField' => $FieldRequestAccess,
                'EditField' => $FieldRequestEdit,
            ]);
            $TaskPerRequest->save();

            $TaskPerVideo = new TaskPerVideo([
                'TaskID' => $TaskID,
                'EmployeeID' => $EmployeeID,
                'Access' => $AccessVideo,
                'Edit' => $EditVideo,
                'Delete' => $DeleteVideo,
                'AccessField' => $FieldVideoAccess,
                'EditField' => $FieldVideoEdit,
            ]);
            $TaskPerVideo->save();
            return [
                'TaskPer' => $TaskPer,
                'TaskPerAssign' => $TaskPerAssign,
                'TaskPerCate' => $TaskPerCate,
                'TaskPerLink' => $TaskPerLink,
                'TaskPerExecution' => $TaskPerExecution,
                'TaskPerChecklist' => $TaskPerChecklist,
                'TaskPerValuation' => $TaskPerValuation,
                'TaskPerExpense' => $TaskPerExpense,
                'TaskPerExpenseDetail' => $TaskPerExpenseDetail,
                'TaskPerFile' => $TaskPerFile,
                'TaskPerRequest' => $TaskPerRequest,
                'TaskPerVideo' => $TaskPerVideo,
            ];
        }else{
            return false;
        }
    }

    public static function getPerAll($Task, $EmployeeID){
        $TaskID = $Task->TaskID;
        $TaskPer = self::getPerByModule($TaskID, $EmployeeID, 'task_per');//TaskPer::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
        if(!$TaskPer){
            $GenPer = self::genPer($TaskID, $EmployeeID);
            if($GenPer){
                return $GenPer;
            }else{
                if($Task->AccessType == 2){
                    $TaskPer = new TaskPer([
                        'TaskID' => $TaskID,
                        'EmployeeID' => $EmployeeID,
                        'Access' => 1,
                        'AccessField' => 'all',
                        'Edit' => 0,
                        'EditField' => '',
                        'Delete' => '0',
                    ]);
                    if($Task->CreateEmployeeID == $EmployeeID){
                        $TaskPer->Edit = 1;
                        $TaskPer->EditField = 'all';
                        $TaskPer->Delete = 1;
                    }
					// File
                    $TaskPerFile = new TaskPerFile([
                        'TaskID' => $TaskID,
                        'EmployeeID' => $EmployeeID,
                        'Access' => 1,
                        'Edit' => 0,
                        'Delete' => 0,
                        'AccessField' => 'all',
                        'EditField' => 'all',
                    ]);

                    // video
                    $TaskPerVideo = new TaskPerVideo([
                        'TaskID' => $TaskID,
                        'EmployeeID' => $EmployeeID,
                        'Access' => 1,
                        'Edit' => 0,
                        'Delete' => 0,
                        'AccessField' => 'all',
                        'EditField' => 'all',
                    ]);
                    return [
                        'TaskPer' => $TaskPer,
                        'TaskPerAssign' => [],
                        'TaskPerCate' => [],
                        'TaskPerLink' => [],
                        'TaskPerExecution' => [],
                        'TaskPerChecklist' => [],
                        'TaskPerValuation' => [],
                        'TaskPerExpense' => [],
                        'TaskPerExpenseDetail' => [],
                        'TaskPerFile' => $TaskPerFile,
                        'TaskPerRequest' => [],
                        'TaskPerVideo' => $TaskPerVideo,
                    ];
                }

                return false;
            }
        }else{
            $TaskPerAssign = self::getPerByModule($TaskID, $EmployeeID, 'task_per_assign');
            $TaskPerCate = self::getPerByModule($TaskID, $EmployeeID, 'task_per_cate');
            $TaskPerLink = self::getPerByModule($TaskID, $EmployeeID, 'task_per_link');
            $TaskPerExecution = self::getPerByModule($TaskID, $EmployeeID, 'task_per_execution');
            $TaskPerChecklist = self::getPerByModule($TaskID, $EmployeeID, 'task_per_checklist');
            $TaskPerValuation = self::getPerByModule($TaskID, $EmployeeID, 'task_per_valuation');
            $TaskPerExpense = self::getPerByModule($TaskID, $EmployeeID, 'task_per_expense');
            $TaskPerExpenseDetail = self::getPerByModule($TaskID, $EmployeeID, 'task_per_expense_detail');
            $TaskPerFile = self::getPerByModule($TaskID, $EmployeeID, 'task_per_file');
            $TaskPerRequest = self::getPerByModule($TaskID, $EmployeeID, 'task_per_request');
            $TaskPerVideo = self::getPerByModule($TaskID, $EmployeeID, 'task_per_video');
            return [
                'TaskPer' => $TaskPer,
                'TaskPerAssign' => $TaskPerAssign,
                'TaskPerCate' => $TaskPerCate,
                'TaskPerLink' => $TaskPerLink,
                'TaskPerExecution' => $TaskPerExecution,
                'TaskPerChecklist' => $TaskPerChecklist,
                'TaskPerValuation' => $TaskPerValuation,
                'TaskPerExpense' => $TaskPerExpense,
                'TaskPerExpenseDetail' => $TaskPerExpenseDetail,
                'TaskPerFile' => $TaskPerFile,
                'TaskPerRequest' => $TaskPerRequest,
                'TaskPerVideo' => $TaskPerVideo,
            ];
        }
    }

    public static function getPerByModule($TaskID, $EmployeeID, $Table){
        $Per = DB::select(DB::raw('SELECT TaskID, EmployeeID, GROUP_CONCAT(EditField) EditField,
            GROUP_CONCAT(AccessField) AccessField, Max(Access) Access, Max(Edit) Edit, Max(`Delete`) `Delete`
            from '.$Table.' where EmployeeID = '.$EmployeeID.' AND TaskID = '.$TaskID.' GROUP BY EmployeeID'));
        if(isset($Per[0])){
            $Per = (object)$Per[0];
            $allEdit = strpos($Per->EditField, 'all');
            if($allEdit >= 0){
                $Per->EditField = 'all';
            }
            $allAccess = strpos($Per->AccessField, 'all');
            if($allAccess >= 0){
                $Per->AccessField = 'all';
            }
        }
        return $Per;
        //return DB::table($Table)->select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
    }

    public static function deleteAllPer($TaskID, $EmployeeID){
        TaskPer::where(['TaskID' => $TaskID, 'EmployeeID' => $EmployeeID])->delete();
        TaskPerAssign::where(['TaskID' => $TaskID, 'EmployeeID' => $EmployeeID])->delete();
        TaskPerCate::where(['TaskID' => $TaskID, 'EmployeeID' => $EmployeeID])->delete();
        TaskPerChecklist::where(['TaskID' => $TaskID, 'EmployeeID' => $EmployeeID])->delete();
        TaskPerExecution::where(['TaskID' => $TaskID, 'EmployeeID' => $EmployeeID])->delete();
        TaskPerExpenseDetail::where(['TaskID' => $TaskID, 'EmployeeID' => $EmployeeID])->delete();
        TaskPerFile::where(['TaskID' => $TaskID, 'EmployeeID' => $EmployeeID])->delete();
        TaskPerLink::where(['TaskID' => $TaskID, 'EmployeeID' => $EmployeeID])->delete();
        TaskPerRequest::where(['TaskID' => $TaskID, 'EmployeeID' => $EmployeeID])->delete();
        TaskPerValuation::where(['TaskID' => $TaskID, 'EmployeeID' => $EmployeeID])->delete();
        TaskPerVideo::where(['TaskID' => $TaskID, 'EmployeeID' => $EmployeeID])->delete();
    }
}

?>
