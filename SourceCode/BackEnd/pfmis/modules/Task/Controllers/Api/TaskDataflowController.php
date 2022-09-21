<?php
namespace Module\Task\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\TaskCate;
use Module\Task\Models\Evaluation1job;
use Module\Task\Models\TaskCheckList;
use Module\Task\Models\TaskExpense;
use Module\Task\Models\TaskExpenseTrans;
use Module\Task\Models\TaskExpenseTransItem;
use Module\Task\Models\TaskFile;
use Module\Task\Models\TaskLink;
use Module\Task\Models\TaskPerAssign;
use Module\Task\Models\TaskPerCate;
use Module\Task\Models\TaskPerChecklist;
use Module\Task\Models\TaskPerExecution;
use Module\Task\Models\TaskPerExpense;
use Module\Task\Models\TaskPerExpenseDetail;
use Module\Task\Models\TaskPerFile;
use Module\Task\Models\TaskPerLink;
use Module\Task\Models\TaskPerRequest;
use Module\Task\Models\TaskPerValuation;
use Module\Task\Models\TaskPerVideo;
use Module\Task\Models\TaskRequest;
use Module\Task\Models\TaskVideo;
use function foo\func;
use function GuzzleHttp\Psr7\_parse_request_uri;
use IjCore\IjDate;
use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\Employee;
use Module\Listing\Models\FileModel;
use Module\SysAdmin\Models\SysFeatureStatus;
use Module\SysAdmin\Models\SysFeatureStatusItem;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysStatusItem;
use Module\SysAdmin\Models\SysUser;
use Module\SysAdmin\Models\User;
use Module\SysAdmin\Models\Workflow;
use Module\SysAdmin\Models\WorkflowConstraint;
use Module\SysAdmin\Models\WorkflowItem;
use Module\Task\Models\Task;
use Module\Task\Models\TaskAssign;
use Module\Task\Models\TaskComment;
use Module\Task\Models\TaskDataflow;
use Module\Task\Models\TaskDataflowSchedule;
use Module\Task\Models\TaskDataflowUser;
use Module\Task\Models\TaskExecutionTrans;
use Module\Task\Models\TaskPer;
use test\Mockery\ReturnTypeObjectTypeHint;

class TaskDataflowController extends Controller{

    public function list(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $workflowID = ($request->get('WFID')) ? $request->get('WFID') : 0;
        $authEmployee = Employee::where(['UserID' => Auth::user()->UserID])->first();
        if (!$workflowID) return json_encode($json);

        $getJsonFlowchart = ($request->get('jsonFlowchart')) ? true : false;
        if ($getJsonFlowchart) {
            $jsonFlowchart = Workflow::select('JsonFlowchart')
                ->where('Inactive', '=', 0)
                ->where('WFID', '=', $workflowID)
                ->first();
            $json['data']['JsonFlowchart'] = $jsonFlowchart['JsonFlowchart'];
        }

        //
        $taskQuery = TaskDataflow::query();
        $taskQuery->select('task_dataflow.DFID', 'task_dataflow.StatusCompleted', 'task_dataflow.DFKey', 'ConstraintFieldValue', 'task.TaskName', 'task.Status', 'task.TaskID', 'WFID', 'task_dataflow.Locked', 'task.PercentCompleted')
            ->join('task', 'task_dataflow.ConstraintFieldValue', '=', 'task.TaskID')
            ->leftJoin('task_assign', 'task.TaskID', '=', 'task_assign.TaskID')
            ->whereColumn('DFID', '=', 'DFKey')
            ->where('task_dataflow.WFID', '=', $workflowID);


        // filter dataflow
        // filter by name
        if ($request->get('DFKey')) {
            $taskQuery->where('task_dataflow.DFKey', '=', $request->get('DFKey'));
        }

        // filter by date
        if ($request->get('dateRange')) {
            $dateRange = $request->get('dateRange');
            if ($dateRange['fromDate']) {
                $fromDate = IjDate::StringToDate($dateRange['fromDate']);
                $taskQuery->where('task.CreateDate', '>=', $fromDate);
            }
            if ($dateRange['toDate']) {
                $toDate = IjDate::StringToDate($dateRange['toDate']);
                $taskQuery->where('task.CreateDate', '<=', $toDate);
            }
        }

        // filter by status
        if ($request->get('Status')) {
            $status = $request->get('Status');
            $taskQuery->where('task.Status', '=', $status);
        }

        if ($request->get('EmployeeID')) {
            $employeeID = $request->get('EmployeeID');
            $checkEmployee = DB::table('task_workflow_employee')->where([
                'EmployeeID' => $employeeID,
                'WFID' => $workflowID
            ])->exists();
            if (!$checkEmployee) {
                $taskQuery->whereIn('task.TaskID', function ($q) use ($employeeID){
                    $q->select('task.ParentID')
                        ->from('task')
                        ->join('task_assign', 'task.TaskID', '=', 'task_assign.TaskID')
                        ->where('task_assign.EmployeeID', '=', $employeeID)
                        ->groupBy('task.TaskID');
                });
            }
        }

        // Quyền xem quy trình
        if (Auth::user()->UserType !== 1) {
            // kiểm tra user có trong giao việc
            $checkEmployee = DB::table('task_workflow_employee')->where([
                'EmployeeID' => $authEmployee->EmployeeID,
                'WFID' => $workflowID
            ])->exists();
            if (!$checkEmployee) {
                $taskQuery->whereIn('task.TaskID', function ($q) use ($authEmployee){
                    $q->select(DB::raw('IF(task.ParentID > 0, task.ParentID, task.TaskID)'))
                        ->from('task')
                        ->join('task_assign', 'task.TaskID', '=', 'task_assign.TaskID')
                        ->where('task_assign.EmployeeID', '=', $authEmployee->EmployeeID)
                        ->groupBy('task.TaskID');
                });
            }
        }

        $taskQuery->groupBy('DFKey');
        $taskQuery->orderBy('TaskID', 'DESC');
        $tasks = $taskQuery->paginate($perPage);

        if ($tasks) {
            $dfKeyArr = [];
            foreach ($tasks->items() as $key => $task) {
                $dfKeyArr[] = $task['DFKey'];
            }

            $dataflowQuery = TaskDataflow::query();
            $dataflowQuery->select('task_dataflow.*',
                'task_workflow_item.WFItemName', 'task_workflow_item.LineIDTemp',
                'task_workflow_item.ProcessType',
                'task.TaskNo', 'task.ParentID', 'task.ParentName', 'task.TaskDescription',
                'task.CreateEmployeeID', 'task.AssignEmployeeID', 'task.ResponEmployeeID', 'task.CheckEmployeeID',
                'task.Duration', 'task.EstimatedQuantity', 'task.ActualCompletedDate', 'task.TotalActualHour',
                'task.TotalActualQuantity', 'task.TotalActualConvertQuantity', 'task.CalMethod', 'task.PerformingDescription',
                'task.TaskID', 'task.TaskName', 'task.Status', 'task.StatusID', 'task.StatusName', 'task.StatusValue', 'task.StatusDescription',
                'task.PercentCompleted', 'task.StartDate', 'task.DueDate',
                'task.Duration', 'task.EstimatedQuantity', 'task.UomID',
                'uom.UomName', 'act_gvouc_trans.TransID', 'act_gvouc_trans.TransNo', 'act_gvouc_trans.Comment AS TransComment')
                ->join('task', 'task_dataflow.ConstraintFieldValue', '=', 'task.TaskID')
                ->leftJoin('task_workflow_item', 'task_workflow_item.WFItemID', '=', 'task_dataflow.WFItemID')
                ->leftJoin('uom', 'task.UomID', '=', 'uom.UomID')
                ->leftJoin('act_gvouc_trans', 'act_gvouc_trans.DFID', '=', 'task_dataflow.DFID')
                ->where('task_dataflow.WFID', '=', $workflowID)
                ->where('task_dataflow.Deleted', '=', 0)
                ->whereColumn('task_dataflow.DFID', '!=', 'task_dataflow.DFKey')
                ->whereIn('task_dataflow.DFKey', $dfKeyArr)
                ->groupBy('task_dataflow.DFKey', 'task_dataflow.DFID');

            $dataflow = $dataflowQuery->get();
            $json['data']['dataflow'] = $dataflow;

            $taskIDdataflow = [];
            foreach ($dataflow as $item) {
                $taskIDdataflow[] = $item->TaskID;
            }

            $taskAssignQuery = TaskAssign::query();
            $taskAssignQuery->select('TaskID', 'TaskName', 'task_assign.*', 'Avata', 'sys_user.UserID')
                ->join('employee', 'employee.EmployeeID', '=', 'task_assign.EmployeeID')
                ->join('sys_user', 'employee.UserID', '=', 'sys_user.UserID')
                ->where(function ($q){
                    $q->where('task_assign.IsMainResponsiblePerson', '=', 1);
                    $q->orWhere('task_assign.IsResponsiblePerson', '=', 1);
                    $q->orWhere('task_assign.IsExecutor', '=', 1);
                })
                ->whereIn('TaskID', $taskIDdataflow);
            $taskAssignUsers = $taskAssignQuery->get();
            $json['data']['taskAssignUsers'] = $taskAssignUsers;
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['task'] = $tasks;
        return json_encode($json);
    }

    public function getListDataflow(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $authEmployee = Employee::where(['UserID' => Auth::user()->UserID])->first();

        $taskQuery = TaskDataflow::query();
        $taskQuery->select('task_dataflow.DFID', 'task_dataflow.StatusCompleted', 'task_dataflow.DFKey', 'ConstraintFieldValue', 'task.TaskName',
            'task.Status', 'task.TaskID', 'WFID', 'task_dataflow.Locked', 'task.PercentCompleted', 'task.Priority', DB::raw("IF(task.Status = 2, '1', '0') AS StatusWait"))
            ->join('task', 'task_dataflow.ConstraintFieldValue', '=', 'task.TaskID')
            ->leftJoin('task_assign', 'task.TaskID', '=', 'task_assign.TaskID')
            ->whereColumn('DFID', '=', 'DFKey');

        // filter by date

        if ($request->get('sortBy')) {
            $sortBy = $request->get('sortBy');
            if ($sortBy === 1) {
                $taskQuery->orderBy('task.TaskID', 'DESC');
            } elseif ($sortBy == 2) {
                $taskQuery->orderBy('task.TaskID', 'ASC');
            } elseif ($sortBy == 3) {
                $taskQuery->orderBy('task.Priority', 'ASC');
            } elseif ($sortBy == 4) {
                $taskQuery->orderBy('StatusWait', 'DESC');
            }
        }else {
            $taskQuery->orderBy('task.TaskID', 'DESC');
        }

        if ($request->post('TaskName')) {
            $taskName = $request->post('TaskName');
            $taskQuery->where('task.TaskName', 'like', '%' . $taskName . '%');
        }

        if ($request->get('dateRange')) {
            $dateRange = $request->get('dateRange');
            if ($dateRange['fromDate']) {
                $fromDate = IjDate::StringToDate($dateRange['fromDate']);
                $taskQuery->where('task.CreateDate', '>=', $fromDate);
            }
            if ($dateRange['toDate']) {
                $toDate = IjDate::StringToDate($dateRange['toDate']);
                $taskQuery->where('task.CreateDate', '<=', $toDate);
            }
        }

        // filter by status
        if ($request->get('Status')) {
            $status = $request->get('Status');
            $taskQuery->where('task.Status', '=', $status);
        }

        // filter by employee
        if ($request->get('EmployeeID')) {
            $employeeID = $request->get('EmployeeID');
            $checkEmployee = DB::table('task_workflow_employee')->where([
                'EmployeeID' => $employeeID
            ])->exists();
            if (!$checkEmployee) {
                $taskQuery->whereIn('task.TaskID', function ($q) use ($employeeID){
                    $q->select('task.ParentID')
                        ->from('task')
                        ->join('task_assign', 'task.TaskID', '=', 'task_assign.TaskID')
                        ->where('task_assign.EmployeeID', '=', $employeeID)
                        ->groupBy('task.TaskID');
                });
            }
        }

        // Quyền xem quy trình
        if (Auth::user()->UserType !== 1) {
            // kiểm tra user có trong giao việc
            $taskQuery->where(function ($q) use ($authEmployee) {

                $q->whereIn('task_dataflow.WFID', function ($q) use ($authEmployee) {
                    $q->select('WFID')
                        ->from('task_workflow_employee')
                        ->where('EmployeeID', '=', $authEmployee->EmployeeID)
                        ->groupBy('WFID');
                });

                $q->orWhereIn('task.TaskID', function ($q) use ($authEmployee) {
                    $q->select(DB::raw('IF(task.ParentID > 0, task.ParentID, task.TaskID)'))
                        ->from('task')
                        ->join('task_assign', 'task.TaskID', '=', 'task_assign.TaskID')
                        ->where('task_assign.EmployeeID', '=', $authEmployee->EmployeeID)
                        ->groupBy('task.TaskID');
                });

            });
        }

        $taskQuery->groupBy('DFKey');
        $taskQuery->orderBy('task.TaskID', 'DESC');
        $tasks = $taskQuery->paginate($perPage);

        if ($tasks) {
            $dfKeyArr = [];
            foreach ($tasks->items() as $key => $task) {
                $dfKeyArr[] = $task['DFKey'];
            }

            $dataflowQuery = TaskDataflow::query();
            $dataflowQuery->select('task_dataflow.*',
                'task_workflow_item.WFItemName', 'task_workflow_item.LineIDTemp',
                'task_workflow_item.ProcessType',
                'task.TaskNo', 'task.ParentID', 'task.ParentName', 'task.TaskDescription',
                'task.CreateEmployeeID', 'task.AssignEmployeeID', 'task.ResponEmployeeID', 'task.CheckEmployeeID',
                'task.Duration', 'task.EstimatedQuantity', 'task.ActualCompletedDate', 'task.TotalActualHour',
                'task.TotalActualQuantity', 'task.TotalActualConvertQuantity', 'task.CalMethod', 'task.PerformingDescription',
                'task.TaskID', 'task.TaskName', 'task.Status', 'task.StatusID', 'task.StatusName', 'task.StatusValue', 'task.StatusDescription',
                'task.PercentCompleted', 'task.StartDate', 'task.DueDate',
                'task.Duration', 'task.EstimatedQuantity', 'task.UomID',
                'uom.UomName', 'act_gvouc_trans.TransID', 'act_gvouc_trans.TransNo', 'act_gvouc_trans.Comment AS TransComment')
                ->join('task', 'task_dataflow.ConstraintFieldValue', '=', 'task.TaskID')
                ->leftJoin('task_workflow_item', 'task_workflow_item.WFItemID', '=', 'task_dataflow.WFItemID')
                ->leftJoin('uom', 'task.UomID', '=', 'uom.UomID')
                ->leftJoin('act_gvouc_trans', 'act_gvouc_trans.DFID', '=', 'task_dataflow.DFID')
                ->where('task_dataflow.Deleted', '=', 0)
                ->whereColumn('task_dataflow.DFID', '!=', 'task_dataflow.DFKey')
                ->whereIn('task_dataflow.DFKey', $dfKeyArr)
                ->groupBy('task_dataflow.DFKey', 'task_dataflow.DFID');

            $dataflow = $dataflowQuery->get();
            $json['data']['dataflow'] = $dataflow;

            $taskIDdataflow = [];
            foreach ($dataflow as $item) {
                $taskIDdataflow[] = $item->TaskID;
            }

            $taskAssignQuery = TaskAssign::query();
            $taskAssignQuery->select('TaskID', 'TaskName', 'task_assign.*', 'Avata', 'sys_user.UserID')
                ->join('employee', 'employee.EmployeeID', '=', 'task_assign.EmployeeID')
                ->join('sys_user', 'employee.UserID', '=', 'sys_user.UserID')
                ->where(function ($q){
                    $q->where('task_assign.IsMainResponsiblePerson', '=', 1);
                    $q->orWhere('task_assign.IsResponsiblePerson', '=', 1);
                    $q->orWhere('task_assign.IsExecutor', '=', 1);
                })
                ->whereIn('TaskID', $taskIDdataflow);
            $taskAssignUsers = $taskAssignQuery->get();
            $json['data']['taskAssignUsers'] = $taskAssignUsers;
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['task'] = $tasks;
        return json_encode($json);
    }

    public function getAllDataflow(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $dataflow = TaskDataflow::getAllDataflow($request);

        if ($dataflow) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $dataflow;
        }

        return json_encode($json);

    }
    public function workflow(){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $workflow = [];
        $auth = Auth::user();
        if ($auth->UserType == 1) {
            $workflow = Workflow::select('WFID', 'WFNo', 'WFName')
                ->where('Inactive', '=', 0)
                ->get();
        }else{
            // lay quy trinh ma nguoi dung co tham gia
            $workflowUnion = Workflow::select('task_workflow.WFID', 'task_workflow.WFNo', 'task_workflow.WFName')
                ->join('task_workflow_employee', 'task_workflow_employee.WFID', '=', 'task_workflow.WFID')
                ->where('task_workflow.Inactive', '!=', 1)
                ->where('task_workflow_employee.EmployeeID', '=', $auth->EmployeeID)
                ->groupBy('task_workflow.WFID');

            // lay quy trinh ma nguoi dung duoc giao viec
            $workflow = Workflow::select('task_workflow.WFID', 'task_workflow.WFNo', 'task_workflow.WFName')
                ->join('task_dataflow', 'task_dataflow.WFID', '=', 'task_workflow.WFID')
                ->join('task_assign', 'task_assign.TaskID', '=', 'task_dataflow.ConstraintFieldValue')
                ->where('task_assign.EmployeeID', '=', $auth->EmployeeID)
                ->where('task_workflow.Inactive', '!=', 1)
                ->union($workflowUnion)
                ->groupBy('task_workflow.WFID')
                ->get();
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['workflow'] = $workflow;
        return json_encode($json);
    }
    public function taskDetail(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $dfKey = ($request->DFKey) ? $request->DFKey : null;

        if(!$dfKey) return json_encode($json);

        $taskData = TaskDataflow::select('task_dataflow.DFID', 'task_dataflow.DFKey', 'task.TaskID','task.TaskName', 'task.Priority', 'task.CreateDate', 'task.StartDate', 'task.DueDate', 'Status', 'PercentCompleted', 'task_workflow_item.ProcessType')
            ->join('task', 'task.TaskID', '=', 'task_dataflow.ConstraintFieldValue')
            ->leftJoin('task_workflow_item', 'task_dataflow.WFItemID', '=', 'task_workflow_item.WFItemID')
            ->where('task_dataflow.DFKey', '=', $dfKey)
            ->where('task_dataflow.Deleted', '=', 0)
            ->orderBy('task_dataflow.DFID', 'ASC')
            ->get();

        $taskIDArray = [];
        foreach ($taskData as $key => $task) {
            $taskIDArray[] = $task['TaskID'];
        }

//        $taskAssignQuery = TaskAssign::query();
//        $taskAssignQuery->select('TaskID', 'TaskName', 'task_assign.*', 'Avata', 'sys_user.UserID')
//            ->join('employee', 'employee.EmployeeID', '=', 'task_assign.EmployeeID')
//            ->join('sys_user', 'employee.UserID', '=', 'sys_user.UserID')
//            ->where(function ($q){
//                $q->where('task_assign.IsMainResponsiblePerson', '=', 1);
//                $q->orWhere('task_assign.IsResponsiblePerson', '=', 1);
//                $q->orWhere('task_assign.IsExecutor', '=', 1);
//            })
//            ->whereIn('TaskID', $taskIDArray);
//        $taskAssignUsers = $taskAssignQuery->get();

        if ($taskData) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data']['taskDetail'] = $taskData;
        }

//        if ($taskAssignUsers) {
//            $json['data']['taskAssignUsers'] = $taskAssignUsers;
//        }
        return json_encode($json);
    }
    public function taskSchedule(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $taskID = $request->get('TaskID');
        $dfKey = $request->get('DFKey');

        if (!$taskID || !$dfKey) return $json;

        $taskExecutionTransQuery = TaskExecutionTrans::query();
        $taskExecutionTransQuery->select('task_execution_trans.*', 'task_workflow_item.WFItemName', 'sys_user.Avata')
            ->join('task_dataflow', 'task_dataflow.ConstraintFieldValue', '=', 'task_execution_trans.TaskID')
            ->join('task_workflow_item', 'task_workflow_item.WFItemID', '=', 'task_dataflow.WFItemID')
            ->leftJoin('employee', 'employee.EmployeeID', '=', 'task_execution_trans.EmployeeID')
            ->leftJoin('sys_user', 'employee.UserID', '=', 'sys_user.UserID')
            ->where('task_dataflow.DFKey', '=', $dfKey)
            ->groupBy('task_execution_trans.TransDate');
        $taskExecutionTrans = $taskExecutionTransQuery->get();
        if ($taskExecutionTrans) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data']['taskSchedule'] = $taskExecutionTrans;
        }

        $taskIDs = [];
        foreach ($taskExecutionTrans as $taskExecutionTran) {
            if (!in_array($taskExecutionTran->TaskID, $taskIDs)) {
                $taskIDs[] = $taskExecutionTran->TaskID;
            }
        }

        $taskAssign = TaskAssign::select('task_assign.*', 'sys_user.UserID', 'sys_user.Avata')
            ->join('employee', 'employee.EmployeeID', '=', 'task_assign.EmployeeID')
            ->leftJoin('sys_user', 'sys_user.UserID', '=', 'employee.UserID')
            ->where(function ($q){
                $q->where('task_assign.IsMainResponsiblePerson', '=', 1);
                $q->orWhere('task_assign.IsResponsiblePerson', '=', 1);
                $q->orWhere('task_assign.IsExecutor', '=', 1);
            })
            ->whereIn('TaskID', $taskIDs)->get();

        if ($taskAssign) {
            $json['data']['taskScheduleUsers'] = $taskAssign;
        }
        return json_encode($json);
    }
    public function featureStatus(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $statusID = $request->get('StatusID');
        $taskID = $request->get('TaskID');

        $taskExecutionTrans = TaskExecutionTrans::where(['TaskID' => $taskID])
            ->orderBy('TransID', 'DESC')
            ->limit(1)
            ->first();

        if ($taskExecutionTrans) {
            $json['data']['TaskExecutionTrans'] = $taskExecutionTrans;
        }

        // lấy trạng thái
        $statusItems = SysStatusItem::getItemsByStatusID($statusID);

        // lấy tất cả người thực hiện chức năng
        if ($statusItems) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data']['status'] = $statusItems;
//            $json['data']['usersAssign'] = $taskAssignUsers;
        }


        return json_encode($json);
    }


    /**
     * @param Request $request
     * @return false|string
     */
    public function updateDataflow($args){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $dfID = $args['DFID'];
        $dfKey = $args['DFKey'];
        $wfID = $args['WFID'];
        $wfItemID = $args['WFItemID'];
        $taskIDParent = $args['TaskIDParent'];
        $statusValue = $args['FeatureStatusValue'];
        $statusTitle = $args['FeatureStatusTitle'];
        $percentCompleted = $args['PercentCompleted'];
        $statusCompleted = ($percentCompleted >= 100) ? 1 : 0;

        $redoParams = [
            'WFID' => $wfID,
            'WFItemID' => $wfItemID,
            'DFKey' => $dfKey,
            'DFID' => $dfID
        ];
        TaskDataflow::redoDataflow($redoParams);

        // ================ update task dataflow ===========================
        $taskDataflow = TaskDataflow::find($dfID);
        $taskDataflow->FeatureStatusValue = $statusValue;
        $taskDataflow->FeatureStatusTitle = $statusTitle;
        $taskDataflow->StatusCompleted = $statusCompleted;
        $taskDataflow->save();

        if ($statusCompleted) {
            // lấy các chức năng tiếp theo
            $newFeatures = WorkflowConstraint::select('task_workflow_constraint.WFItemID', 'WFPreItemID', 'WFPreItemValue', 'ConstraintCondition', 'WFItemName', 'FeatureStatusID')
                ->leftJoin('task_workflow_item', 'task_workflow_constraint.WFItemID', '=', 'task_workflow_item.WFItemID')
                ->where('WFPreItemID', '=', $wfItemID)
                ->get();

            foreach ($newFeatures as $newFeature) {

                // kiểm tra nếu là điều kiện rẽ nhánh thì thoát
                if ($newFeature->ConstraintCondition === 3) {
                    continue;
                }

                // Kiểm tra nếu là điều kiện hoặc thì xóa những chức năng còn lại
                if ($newFeature->ConstraintCondition === 2) {
                    $allConstraint = WorkflowConstraint::where('WFItemID', $newFeature->WFItemID)
                        ->get();
                    $otherItem = [];
                    foreach ($allConstraint as $otherConstraint) {
                        if ($otherConstraint->WFPreItemID !== $wfItemID) {
                            $otherItem[] = $otherConstraint->WFPreItemID;
                        }
                    }
                    if (count($otherItem)) {
                        $redoQuery = DB::table('task_dataflow')
                            ->where('WFID', '=', $wfID)
                            ->where('DFKey', '=', $dfKey);
                        $redoQuery->whereIn('WFItemID', $otherItem);
                        $redoQuery->update(['Deleted' => 1]);
                    }
                }

                $args = [
                    'WFID' => $wfID,
                    'WFItemID' => $newFeature->WFItemID,
                    'TaskIDParent' => $taskIDParent,
                    'WFItemName' => $newFeature->WFItemName,
                    'DFKey' => $dfKey,
                    'FeatureStatusID' => $newFeature->FeatureStatusID
                ];
                if ($this->createNewTask($args)) {
                    $json['msg'] = 'Tạo mới thành công';
                }
            }
        }

        $json['status'] = 1;
        return json_encode($json);

    }

    public function redoDataflow(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $params = [
            'WFID' => $request->post('WFID'),
            'WFItemID' => $request->post('WFItemID'),
            'DFID' => $request->post('DFID'),
            'DFKey' => $request->post('DFKey')
        ];
        if (TaskDataflow::redoDataflow($params)) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Quy trình đã bị khóa';
        }

        return json_encode($json);
    }


    /**
     * @param Request $request [WFID, DFKey, TaskIDParent, LineIDTemp]
     * @return false|string
     */
    public function createFeature(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $wfID = $request->get('WFID');
        $dfKey = $request->get('DFKey');
        $taskIDParent = $request->get('TaskIDParent');
        $lineIDTemp = $request->get('LineIDTemp');
        $WFItemID = $request->get('WFItemID');

        // kiểm tra thỏa mãn điều kiện tạo mới
        $workflowItem = WorkflowItem::where('WFItemID', '=', $WFItemID)
            ->where('WFID', '=', $wfID)
            ->first();
        if ($workflowItem) {
            $args = [
                'WFID' => $wfID,
                'WFItemID' => $workflowItem->WFItemID,
                'TaskIDParent' => $taskIDParent,
                'WFItemName' => $workflowItem->WFItemName,
                'DFKey' => $dfKey,
                'FeatureStatusID' => $workflowItem->FeatureStatusID
            ];

//            return $this->createNewTask($args);
            if ($this->createNewTask($args)) {
                $json['status'] = 1;
                $json['msg'] = 'Thành công';
            }
        }

        return json_encode($json);

    }

    /**
     * @param $args = [WFID, WFItemID, TaskIDParent, WFItemName, DFKey, FeatureStatusID]
     *
     */
    public function createNewTask($args){
        $checkAdd = true;
        $checkOr = true;
        $checkIf = true;

        $itemIDAnd = [];
        $itemIDOr = [];
        $itemIDIf = [];

        // lấy tất cả liên kết
        $allConstraintsQuery = WorkflowConstraint::from('task_workflow_constraint as twc')
            ->select('twc.*', 'task_dataflow.StatusCompleted', 'task_workflow_item.ProcessType')
            ->join('task_workflow_item', 'task_workflow_item.WFItemID', '=', 'twc.WFPreItemID')
            ->leftJoin('task_dataflow', 'task_dataflow.WFItemID', '=', 'twc.WFPreItemID')
            ->where('twc.WFID', '=', $args['WFID'])
            ->where('twc.WFItemID', '=', $args['WFItemID'])
            ->where(function ($query) use ($args) {
                $query->where('task_dataflow.DFID', function ($querySub) use ($args) {
                    $querySub->select(DB::raw('MAX(DFID)'))
                        ->from('task_dataflow')
                        ->where('WFID', '=', $args['WFID'])
                        ->whereColumn('WFItemID', '=', 'twc.WFPreItemID')
                        ->where('Deleted', '=', 0);
                });
                $query->orWhereNull('task_dataflow.DFID');
            });
        $allConstraints = $allConstraintsQuery->get();

        if (count($allConstraints) > 1) {
            $constraintsAnd = [];
            $constraintsOr = [];
            $constraintsIf = [];


            foreach ($allConstraints as $constraint) {
                if ($constraint->ConstraintCondition == 1) {
                    $constraintsAnd[] = $constraint;
                    $itemIDAnd[] = $constraint->WFPreItemID;
                }
                if ($constraint->ConstraintCondition == 2) {
                    $constraintsOr[] = $constraint;
                    $itemIDOr[] = $constraint->WFPreItemID;
                }
                if ($constraint->ConstraintCondition == 3) {
                    $constraintsIf[] = $constraint;
                    $itemIDIf[] = $constraint->WFPreItemID;
                }
            }

            // TODO: check
            if (count($constraintsAnd)){
                foreach ($constraintsAnd as $constraintAnd) {
                    if ($constraintAnd->StatusCompleted) {
                        $checkAdd = true;
                    } else {
                        if ($constraintAnd->ProcessType != 3){
                            $checkAdd = false;
                            break;
                        }
                    }
                }
            }else{
                $checkAdd = false;
            }


            if (count($constraintsOr)) {
                foreach ($constraintsOr as $constraintOr) {
                    if ($constraintOr->StatusCompleted) {
                        $checkOr = true;
                        break;
                    } else {
                        $checkOr = false;
                    }
                }
            }else{
                $checkOr = false;
            }

            if (count($constraintsIf)) {
                foreach ($constraintsIf as $constraintIf) {
                    if ($constraintIf->StatusCompleted) {
                        $checkIf = true;
                        break;
                    } else {
                        $checkIf = false;
                    }
                }
            }else{
                $checkIf = false;
            }


            if ($checkAdd || $checkIf || $checkOr) {
                $params = [
                    'TaskIDParent' => $args['TaskIDParent'],
                    'TaskName' => $args['WFItemName'],
                    'WFID' => $args['WFID'],
                    'WFItemID' => $args['WFItemID'],
                    'DFKey' => $args['DFKey'],
                    'FeatureStatusID' => $args['FeatureStatusID']
                ];

                // Kiểm tra nếu tất cả là liên kết and thì disable các liên kết or khác
                if ($checkAdd) {
                    if (count($constraintsOr)) {
                        $redoQuery = DB::table('task_dataflow')
                            ->where('WFID', '=', $args['WFID'])
                            ->where('DFKey', '=', $args['DFKey']);
                        $redoQuery->whereIn('WFItemID', $itemIDOr);
                        $redoQuery->update(['Deleted' => 1]);
                    }

                }


                if (TaskDataflow::createNewTask($params)) {
                    return true;
                }
            }
        } else {
            $params = [
                'TaskIDParent' => $args['TaskIDParent'],
                'TaskName' => $args['WFItemName'],
                'WFID' => $args['WFID'],
                'WFItemID' => $args['WFItemID'],
                'DFKey' => $args['DFKey'],
                'FeatureStatusID' => $args['FeatureStatusID']
            ];
            if (TaskDataflow::createNewTask($params)) {
                return true;
            }

        }

        return false;
    }

    public function getJsonWorkflow($id){
        $jsonWorkflow = Workflow::select('JsonFlowchart')->where(['WFID' => $id])->first();
        $workflowItem = WorkflowItem::where(['WFID' => $id])->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['jsonWorkflow'] = $jsonWorkflow;
        $json['data']['workflowItem'] = $workflowItem;
        return json_encode($json);
    }

    public function endDataflow(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $wfID = $request->get('WFID');
        $dfKey = $request->get('DFKey');
        $taskIDParent = $request->get('TaskIDParent');
        $lineIDTemp = $request->get('LineIDTemp');
        $WFItemID = $request->get('WFItemID');

        //kiểm tra đã có trong dataflow hay chưa
        $endFeatureQuery = TaskDataflow::query();
        $endFeatureQuery->join('task_workflow_item', 'task_workflow_item.WFItemID', '=', 'task_dataflow.WFItemID')
            ->where('task_dataflow.DFKey', '=', $dfKey)
            ->where('task_dataflow.WFID', '=', $wfID)
            ->where('task_workflow_item.WFItemID', '=', $WFItemID)
            ->where('task_dataflow.Deleted', '=', 0);

        if ($endFeatureQuery->count()) {
            // cập nhật trạng thái luồng công việc hoàn thành
            DB::table('task_dataflow')
                ->where('DFKey', '=', $dfKey)
                ->where('WFID', '=', $wfID)
                ->where('DFID', '=', $dfKey)
                ->update(['Locked' => 1]);

            DB::table('task')
                ->where('TaskID', '=', $taskIDParent)
                ->update([
                    'Status' => 6,
                    'PercentCompleted' => 100
                ]);

            $json['status'] = 1;
            $json['msg'] = 'Thành công';
        }else{
            $workflowItem = WorkflowItem::where('WFItemID', '=', $WFItemID)
                ->where('WFID', '=', $wfID)
                ->first();

            if ($workflowItem) {
                $args = [
                    'WFID' => $wfID,
                    'WFItemID' => $workflowItem->WFItemID,
                    'TaskIDParent' => $taskIDParent,
                    'WFItemName' => $workflowItem->WFItemName,
                    'DFKey' => $dfKey,
                    'FeatureStatusID' => $workflowItem->FeatureStatusID
                ];

                if ($this->createNewTask($args)) {
                    // cập nhật trạng thái luồng công việc hoàn thành
                    DB::table('task_dataflow')
                        ->where('DFKey', '=', $dfKey)
                        ->where('WFID', '=', $wfID)
                        ->where('DFID', '=', $dfKey)
                        ->update(['Locked' => 1]);

                    DB::table('task')
                        ->where('TaskID', '=', $taskIDParent)
                        ->update([
                            'Status' => 6,
                            'PercentCompleted' => 100
                        ]);

                    $json['status'] = 1;
                    $json['msg'] = 'Thành công';
                }
            }
        }

        // Todo: viết tringer xóa chức năng có Deleted = 1 - xóa schedule and dataflow_user

        return json_encode($json);
    }

    public function getTrans(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $TransTypeID = $request->post('TransTypeID');
        if (empty($TransTypeID)) {
            return json_encode($json);
        }
        $query = DB::table('act_gvouc_trans');
        $query->where('TransTypeID', '=', $TransTypeID);

        if($request->post('search')){
            $search = $request->post('search');
            $query->where(function ($q) use ($search) {
                $q->where('Comment', 'like', '%' . $search . '%');
                $q->orWhere('TransNo', 'like', $search . '%');
            });
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $data = $query->paginate($per_page);

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function updateTrans(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $TransID = $request->post('TransID');
        $TransIDOld = $request->post('TransIDOld');
        if (!$TransID) {
            $json['msg'] = 'Chứng từ không tồn tại';
            return json_encode($json);
        }

        DB::table('act_gvouc_trans')
            ->where('TransID', '=', $TransID)
            ->update([
                'WFID' => $request->WFID,
                'WFNo' => $request->WFNo,
                'WFName' => $request->WFName,
                'WFItemID' => $request->WFItemID,
                'WFItemName' => $request->WFItemName,
                'DFID' => $request->DFID,
                'DFKey' => $request->DFKey
            ]);

        DB::table('act_gvouc_trans')
            ->where('TransID', '=', $TransIDOld)
            ->update([
                'WFID' => null,
                'WFNo' => null,
                'WFName' => null,
                'WFItemID' => null,
                'WFItemName' => null,
                'DFID' => null,
                'DFKey' => null
            ]);

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = [];
        return json_encode($json);
    }

    public static function handleDelete($idArray){
        $dataflow = TaskDataflow::whereIn('DFKey', $idArray)->get();
        $taskID = [];
        foreach ($dataflow as $item) {
            $taskID[] = $item->ConstraintFieldValue;
        }

        Task::destroy($taskID);
        TaskDataflow::whereIn('DFKey', $idArray)->delete();
        TaskAssign::whereIn('TaskID', $taskID)->delete();
        TaskCate::whereIn('TaskID', $taskID)->delete();
        TaskCheckList::whereIn('TaskID', $taskID)->delete();
        TaskComment::whereIn('TaskID', $taskID)->delete();
        TaskDataflow::where('ConstraintFieldName', '=', 'TaskID')
            ->whereIn('ConstraintFieldValue', $taskID)->delete();
        TaskLink::whereIn('TaskID', $taskID)->delete();
        Evaluation1job::whereIn('TaskID', $taskID)->delete();
        TaskExpense::whereIn('TaskID', $taskID)->delete();
        TaskExpenseTrans::whereIn('TaskID', $taskID)->delete();
        TaskExpenseTransItem::whereIn('TaskID', $taskID)->delete();
        TaskFile::whereIn('TaskID', $taskID)->delete();
        TaskPer::whereIn('TaskID', $taskID)->delete();
        TaskPerAssign::whereIn('TaskID', $taskID)->delete();
        TaskPerCate::whereIn('TaskID', $taskID)->delete();
        TaskPerChecklist::whereIn('TaskID', $taskID)->delete();
        TaskPerLink::whereIn('TaskID', $taskID)->delete();
        TaskPerExecution::whereIn('TaskID', $taskID)->delete();
        TaskPerExpense::whereIn('TaskID', $taskID)->delete();
        TaskPerExpenseDetail::whereIn('TaskID', $taskID)->delete();
        TaskPerFile::whereIn('TaskID', $taskID)->delete();
        TaskPerRequest::whereIn('TaskID', $taskID)->delete();
        TaskPerValuation::whereIn('TaskID', $taskID)->delete();
        TaskPerVideo::whereIn('TaskID', $taskID)->delete();
        TaskRequest::whereIn('TaskID', $taskID)->delete();
        TaskVideo::whereIn('TaskID', $taskID)->delete();
    }


    public function deleteArray(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $user = Auth::user();
        $arrayID = $request->post('array_id');
        if ($arrayID && count($arrayID) > 0) {
            if ($user->UserType === 1) {
                self::handleDelete($arrayID);
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {

                $taskID = [];
                $dataflow = TaskDataflow::whereIn('DFID', $arrayID)->get();
                foreach ($dataflow as $item) {
                    $taskID[] = $item->ConstraintFieldValue;
                }

                // Kiểm tra là người tạo
                $taskAssigns = TaskAssign::whereIn('TaskID', $taskID)
                    ->where('EmployeeID', '=', $user->EmployeeID)
                    ->get();
                $isCreator = false;
                foreach ($taskAssigns as $taskAssign) {
                    if ($taskAssign->IsCreator === 1) {
                        $isCreator = true;
                    } else {
                        $isCreator = false;
                        break;
                    }
                }

                $isPer = false;
                // Kiểm tra có quyền xóa
                $tasksPer = TaskPer::whereIn('LineID', function ($q) use ($taskID, $user) {
                    $q->select(DB::raw('MAX(LineID)'))
                        ->from('task_per')
                        ->whereIn('TaskID', $taskID)
                        ->where('EmployeeID', $user->EmployeeID)
                        ->groupBy('TaskID');
                })->get();

                foreach ($tasksPer as $taskPer) {
                    if ($taskPer->Delete === 1) {
                        $isPer = true;
                    } else {
                        $isPer = false;
                        break;
                    }
                }

                if ($isCreator || $isPer) {
                    self::deleteArray($arrayID);
                    $json['status'] = 1;
                    $json['msg'] = 'Thành công!';
                    $json['data'] = [];
                    return json_encode($json);
                } else {
                    $json['status'] = 0;
                    $json['msg'] = 'Bạn không có quyền xóa';
                    $json['data'] = [];
                    return json_encode($json);
                }

            }
        }

        return json_encode($json);
    }
}
