<?php
namespace Module\Task\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\Listing\Controllers\CommonController;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Employee;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysStatusItem;
use Module\SysAdmin\Models\WorkflowConstraint;
use Module\SysAdmin\Models\WorkflowItem;
use Module\Task\Controllers\TaskController;
use Zend\Diactoros\Request;

class TaskDataflow extends Model{
    protected $table = 'task_dataflow';
    protected $primaryKey = 'DFID';

    protected $fillable = [
        'WFID',
        'WFItemID',
        'FeatureID',
        'FeatureKey',
        'FeatureName',
        'isTaskFeature',
        'Step',
        'TableID',
        'TableName',
        'ConstraintFieldName',
        'ConstraintFieldValue',
        'DFKey',
        'FeatureStatusID',
        'FeatureStatusValue',
        'FeatureStatusTitle',
        'Finished',
        'StatusCompleted',
        'Locked'
    ];

    public $timestamps = false;

    public static function getAllDataflow($request){
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        $query->select('task.TaskID', 'task.TaskName', 'task_dataflow.DFID', 'task_dataflow.DFKey')
            ->join('task', 'task_dataflow.ConstraintFieldValue', '=', 'task.TaskID')
            ->whereColumn('task_dataflow.DFID', '=', 'task_dataflow.DFKey');

        if ($request->get('TaskName')) {
            $taskName = $request->get('TaskName');
            $query->where('task.TaskName', 'like', '%' . $taskName . '%');
        }

        if ($request->get('WFID')) {
            $query->where('task_dataflow.WFID', '=', $request->get('WFID'));
        }
        $query->orderBy('task_dataflow.DFID', 'DESC');

        $dataflow = $query->paginate($perPage);
        return $dataflow;
    }

    /**
     * @desc: create dataflow
     * @param $request: [TaskID, WFID]
     */
    public static function createDataflow($args){
        $result = false;
        $taskID = $args['TaskID'];
        $wfID = $args['WFID'];
        if ($taskID) {
            $newTaskDataflow = new TaskDataflow([
                'WFID' => $wfID,
                'TableID' => '',
                'TableName' => 'task',
                'ConstraintFieldName' => 'TaskID',
                'ConstraintFieldValue' => $taskID
            ]);
            if ($newTaskDataflow->save()) {
                $newTaskDataflow->DFKey = $newTaskDataflow->DFID;
                $newTaskDataflow->save();
            }

            // Lấy chức năng bắt đầu
            $startWorkflowItem = WorkflowItem::where('WFID', '=', $wfID)
                ->where('ProcessType', '=', 4)
                ->first();

            if ($startWorkflowItem) {
                // lấy các chức năng tiếp theo
                $newFeatures = WorkflowConstraint::select('task_workflow_constraint.WFItemID', 'WFPreItemID', 'WFPreItemValue', 'ConstraintCondition', 'WFItemName', 'FeatureStatusID')
                    ->leftJoin('task_workflow_item', 'task_workflow_constraint.WFItemID', '=', 'task_workflow_item.WFItemID')
                    ->where('WFPreItemID', '=', $startWorkflowItem->WFItemID)
                    ->get();

                foreach ($newFeatures as $newFeature) {
                    // kiểm tra nếu là điều kiện rẽ nhánh thì thoát
                    if ($newFeature->ConstraintCondition === 3) {
                        continue;
                    }

                    $params = [
                        'TaskIDParent' => $taskID,
                        'TaskName' => $newFeature->WFItemName,
                        'WFID' => $wfID,
                        'WFItemID' => $newFeature->WFItemID,
                        'DFKey' => $newTaskDataflow->DFKey,
                        'FeatureStatusID' => $newFeature->FeatureStatusID
                    ];
//                    return TaskDataflow::createNewTask($params);
                    if (TaskDataflow::createNewTask($params)) {
                        return true;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $params [TaskIDParent, TaskName, WFID, WFItemID,DFKey, FeatureStatusID]
     * @return bool
     */
    public static function createNewTask($params){
        // tạo mới task
        $request = new \Illuminate\Http\Request();
        $request->setMethod('post');
        $request->request->add([
            'table' => 'task',
            'ParentID' => $params['TaskIDParent']
        ]);

        $common = new CommonController();
        $response = $common->getAutoNoChild($request);
        $response = json_decode($response, true);

        if ($response['status'] === 1) {
            $taskParent = Task::find($params['TaskIDParent']);
            $level = 2;
            if ($taskParent) {
                $level = $taskParent->Level + 1;
            }else{
                return false;
            }
            if ($taskParent->Detail === 1) {
                $taskParent->Detail = 0;
                $taskParent->save();
            }
            $newTask = new Task([
                'TaskName' => $params['TaskName'],
                'ParentID' => $params['TaskIDParent'],
                'ParentNo' => $taskParent->TaskNo,
                'ParentName' => $taskParent->TaskName,
                'TaskNo' => $response['data'],
                'AccessType' => 1,
                'Level' => $level,
                'Detail' => 1,
                'CreateDate' => date('Y-m-d h:i:s'),
                'StartDate' => date('Y-m-d'),
                'StatusID' => ($params['FeatureStatusID']) ? $params['FeatureStatusID'] : 1,
                'Type' => 2
            ]);

            $sysStatus = SysStatus::find($params['FeatureStatusID']);
            if ($sysStatus) {
                $newTask->StatusName = $sysStatus->StatusName;
            }
            $sysStatusItem = SysStatusItem::where('StatusID', '=', $params['FeatureStatusID'])->first();
            if ($sysStatusItem) {
                $newTask->StatusValue = $sysStatusItem->StatusValue;
                $newTask->StatusDescription = $sysStatusItem->StatusDescription;
                $newTask->Status = $sysStatusItem->ExecutionStatus;
            }

            if ($newTask->save()) {
                // task_per and task_assign for creator
                $AuthEmployee = Employee::where(['UserID' => Auth::user()->UserID])->first();

                $creatorTaskAssign = new TaskAssign([
                    'TaskID' => $newTask->TaskID,
                    'TaskName' => $newTask->TaskName,
                    'EmployeeID' => $AuthEmployee->EmployeeID,
                    'EmployeeName' => $AuthEmployee->EmployeeName,
                    'IsCreator' => 1
                ]);
                $creatorTaskAssign->save();
                Task::genPer($newTask->TaskID, $AuthEmployee->EmployeeID);

                // task_per and task_assign for assign
                $taskWorkflowEmployee = DB::table('task_workflow_employee')
                    ->select('task_workflow_employee.*', 'employee.EmployeeName')
                    ->join('employee', 'task_workflow_employee.EmployeeID', '=', 'employee.EmployeeID')
                    ->where('WFID', '=', $params['WFID'])
                    ->where('WFItemID', '=', $params['WFItemID'])
                    ->get();
                $insertArr = [];

                foreach ($taskWorkflowEmployee as $workflowEmployee) {
                    if ($workflowEmployee->EmployeeID != $creatorTaskAssign->EmployeeID) {
                        $insertArr[] = [
                            'TaskID' => $newTask->TaskID,
                            'TaskName' => $newTask->TaskName,
                            'EmployeeID' => $workflowEmployee->EmployeeID,
                            'EmployeeName' => $workflowEmployee->EmployeeName,
                            'IsMainResponsiblePerson' => $workflowEmployee->IsMainResponsiblePerson,
                            'IsExecutor' => 1,
                            'IsAssignee' => $workflowEmployee->IsMainResponsiblePerson
                        ];
                    }else{
                        $creatorTaskAssign->IsExecutor = 1;
                        $creatorTaskAssign->IsMainResponsiblePerson = $workflowEmployee->IsMainResponsiblePerson;
                        $creatorTaskAssign->IsAssignee = $workflowEmployee->IsMainResponsiblePerson;
                        $creatorTaskAssign->save();
                    }

                }
                DB::table('task_assign')->insert($insertArr);

                foreach ($taskWorkflowEmployee as $workflowEmployee) {
                    Task::genPer($newTask->TaskID, $workflowEmployee->EmployeeID);

                }

                $workflowItem = WorkflowItem::where([
                    'WFID' => $params['WFID'],
                    'WFItemID' => $params['WFItemID']
                ])->first();
                // tạo dataflow
                $newTaskDataflow = new TaskDataflow([
                    'WFID' => $params['WFID'],
                    'WFItemID' => $params['WFItemID'],
                    'FeatureID' => $workflowItem->FeatureID,
                    'FeatureKey' => $workflowItem->FeatureKey,
                    'FeatureName' => $workflowItem->FeatureName,
                    'TableID' => '',
                    'TableName' => 'task',
                    'ConstraintFieldName' => 'TaskID',
                    'ConstraintFieldValue' => $newTask->TaskID,
                    'DFKey' => $params['DFKey'],
                    'FeatureStatusID' => $params['FeatureStatusID']
                ]);
                $newTaskDataflow->save();
                return true;
            }
        }
        return false;

    }

    /**
     * @param $params [WFID, WFItemID, DFID, DFKey]
     */
    public static function redoDataflow($params){
        // kiểm tra quy trình đã bị khóa
        $locked = TaskDataflow::where([
            'WFID' => $params['WFID'],
            'DFKey' => $params['DFKey'],
            'Locked' => 1
        ])->whereColumn('DFKey', '=', 'DFID')->exists();

        if ($locked) {
            return false;
        }

        $constraintsQuery = WorkflowConstraint::query();
        $constraintsQuery->select('WFItemID', 'WFPreItemID', 'ConstraintCondition')
            ->where('WFID', '=', $params['WFID']);
        $constraints = $constraintsQuery->get();
        $nextWFItemsID = self::populateChildrenWFItem($params['WFItemID'],  $constraints);

        $redoQuery = DB::table('task_dataflow')
            ->where('WFID', '=', $params['WFID'])
            ->where('DFKey', '=', $params['DFKey'])
            ->where('DFID', '>', $params['DFID']);
        if (count($nextWFItemsID)) {
            $redoQuery->whereIn('WFItemID', $nextWFItemsID);
        }
        $redoQuery->update(['Deleted' => 1]);

        TaskDataflow::where('WFID', '=', $params['WFID'])
            ->where('DFKey', '=', $params['DFKey'])
            ->where('DFID', '=', $params['DFID'])
            ->update(['StatusCompleted' => 0]);

        return true;
    }

    /**
     * @param $WFItemID
     * @param $constraints
     * @return array
     */
    public static function populateChildrenWFItem($WFItemID, $constraints){
        $listChildNode = [];
        for ($i = 0; $i < count($constraints); $i++) {
            if (!$constraints[$i]->isCheck) {
                if ($constraints[$i]->WFPreItemID == $WFItemID) {
                    $constraints[$i]->isCheck = true;
                    $listChildNode[] = $constraints[$i]->WFItemID;
                    $childNodes = self::populateChildrenWFItem($constraints[$i]->WFItemID, $constraints);
                    $listChildNode = array_merge($listChildNode, $childNodes);
                }
            }
        }
        return array_unique($listChildNode);
    }

}

?>
