<?php
namespace Module\Task\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use IjCore\IjDate;
use IjCore\IjFile;
use IjCore\IjNumber;
use IjCore\IjString;
use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CalendarType;
use Module\Listing\Models\Company;
use Module\Listing\Models\Employee;
use Module\Listing\Models\EmployeeManager;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\TaskCate;
use Module\Listing\Models\TaskCateList;
use Module\Listing\Models\TaskCateValue;
use Module\Listing\Models\Uom;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysStatusItem;
use Module\SysAdmin\Models\SysTable;
use Module\SysAdmin\Models\SysTableField;
use Module\SysAdmin\Models\Workflow;
use Module\Task\Models\Evaluation1job;
use Module\Task\Models\Evaluation1jobItem;
use Module\Task\Models\Evaluation1jobSubItem;
use Module\Task\Models\FileModel;
use Module\Task\Models\Indicator;
use Module\Task\Models\IndicatorTableEmployeeMap;
use Module\Task\Models\IndicatorTableMap;
use Module\Task\Models\ScaleRate;
use Module\Task\Models\ScaleRateItem;
use Module\Task\Models\Task;
use Module\Task\Models\TaskAssign;
use Module\Task\Models\TaskCheckList;
use Module\Task\Models\TaskComment;
use Module\Task\Models\TaskDataflow;
use Module\Task\Models\TaskLink;
use Module\Task\Models\TaskExecutionTrans;
use Module\Task\Models\TaskExpense;
use Module\Task\Models\TaskExpenseTrans;
use Module\Task\Models\TaskExpenseTransItem;
use Module\Task\Models\TaskFile;
use Module\Task\Models\TaskPer;
use Module\Task\Models\TaskPerAssign;
use Module\Task\Models\TaskPerCate;
use Module\Task\Models\TaskPerChecklist;
use Module\Task\Models\TaskPerLink;
use Module\Task\Models\TaskPerExecution;
use Module\Task\Models\TaskPerExpense;
use Module\Task\Models\TaskPerExpenseDetail;
use Module\Task\Models\TaskPerFile;
use Module\Task\Models\TaskPerRequest;
use Module\Task\Models\TaskPerValuation;
use Module\Task\Models\TaskPerVideo;
use Module\Task\Models\TaskPlanTrans;
use Module\Task\Models\TaskPlanTransItem;
use Module\Task\Models\TaskPlanTransSubItem;
use Module\Task\Models\TaskRequest;
use Module\Task\Models\TaskVideo;

class TaskController extends Controller{

    public function list(Request $request){
        $viewType = $request->get('viewType');
        $data = Task::search($request);
        if (!$data) {
            return json_encode([
                'status' => 0,
                'msg' => 'Không tồn tại nhân viên',
            ]);
        }
        $Priority = [
            '1' => 'Khẩn cấp',
            '2' => 'Cao',
            '3' => 'Bình thường',
            '4' => 'Thấp',
        ];
        $AccessType = [
            '1' => 'Chia sẻ',
            '2' => 'Công khai',
            '3' => 'Riêng tư'
        ];

        $Type = [
            '1' => 'Độc lập',
            '2' => 'Quy trình',
            '3' => 'Thông báo',
            '4' => 'Nhắc nhở',
            '9' => 'Khác'
        ];


        $taskIDArray = [];
        $tasks = $data->items();
//        if ($viewType !== 'tree') {
//            $tasks = $data->items();
//        }else{
//            $tasks = $data['data'];
//        }

        foreach ($tasks as $key => $task) {
            if (is_object($task)) {
                $taskIDArray[] = $task->TaskID;
            } elseif (is_array($task)) {
                $taskIDArray[] = $task['TaskID'];
            } else {
                $taskIDArray[] = $task['TaskID'];
            }
        }
        $taskIDArray = array_unique($taskIDArray);

        $taskAssign = [];
        if (count($taskIDArray)) {
            $taskAssignQuery = TaskAssign::query();
            $taskAssignQuery->select('TaskName', 'task_assign.*', 'Avata', 'employee.UserID', 'employee.EmployeeName', 'employee.EmployeeID')
                ->join('employee', 'employee.EmployeeID', '=', 'task_assign.EmployeeID')
                ->join('sys_user', 'employee.UserID', '=', 'sys_user.UserID')
                ->where(function ($q) {
                    $q->where('task_assign.IsMainResponsiblePerson', '=', 1)
                        ->orWhere('task_assign.IsResponsiblePerson', '=', 1)
                        ->orWhere('task_assign.IsExecutor', '=', 1);
                })
                ->whereIn('TaskID', $taskIDArray)
                ->groupBy('task_assign.TaskID', 'task_assign.EmployeeID');
            $taskAssign = $taskAssignQuery->get();
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['taskAssign'] = $taskAssign;
        $json['data']['Priority'] = $Priority;
        $json['data']['AccessType'] = $AccessType;
        $json['data']['Type'] = $Type;
        return json_encode($json);
    }

    public function dashboardStatistic(Request $request)
    {
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        if ($request->has('dashboard')) {
            $total1 = Task::countTask($request, 1);
            $total2 = Task::countTask($request, 2);
            $total3 = Task::countTask($request, 3);
            $total4 = Task::countTask($request, 4);
            $total5 = Task::countTask($request, 5);
            $total6 = Task::countTask($request, 6);
            $total7 = Task::countTask($request, 7);
            $total8 = Task::countTask($request, 8);
            $total9 = Task::countTask($request, 9);
            $total10 = Task::countTask($request, 10);
            $total11 = Task::countTask($request, 11);

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['Total1'] = $total1;
            $json['data']['Total2'] = $total2;
            $json['data']['Total3'] = $total3;
            $json['data']['Total4'] = $total4;
            $json['data']['Total5'] = $total5;
            $json['data']['Total6'] = $total6;
            $json['data']['Total7'] = $total7;
            $json['data']['Total8'] = $total8;
            $json['data']['Total9'] = $total9;
            $json['data']['Total10'] = $total10;
            $json['data']['Total11'] = $total11;
        }

        return json_encode($json);
    }

    public function create(Request $request){
        $taskIDCopy = $request->post('CopyID');
        $Priority = [
            '1' => 'Khẩn cấp',
            '2' => 'Cao',
            '3' => 'Bình thường',
            '4' => 'Thấp',
        ];
        $AccessType = [
            '1' => 'Chia sẻ',
            '2' => 'Công khai',
            '3' => 'Riêng tư'
        ];
        $Company = Company::all();
        $Calendar = CalendarType::all();
        $Uom = Uom::all();
        $auto = AutoNo::genNewNumberNo('task');
        $Workflow = Workflow::all();
        $json['data']['TaskWorkflow'] = $Workflow;

        if ($taskIDCopy) {
            $taskDataflow = TaskDataflow::where([
                'TableName' => 'task',
                'ConstraintFieldValue' => $taskIDCopy
            ])->first();
            if ($taskDataflow) {
                $json['data']['TaskDataflow'] = $taskDataflow;
            }
        }

//        $employeeUser = Employee::where(['UserID' => Auth::user()->UserID])->first();
//        $employeeUser = Employee::where(['UserID' => Auth::user()->UserID])->first();
//        $EmployeeID = isset($employeeUser->EmployeeID)? $employeeUser->EmployeeID : '-1';
//        $Task = DB::select( DB::raw("
//                SELECT T.* FROM task_per TP
//                LEFT JOIN task T
//                ON TP.TaskID = T.TaskID
//                WHERE TP.EmployeeID = '".$EmployeeID."' AND T.ParentID IS NULL
//            "));

        $Status = SysStatus::where('Inactive', '!=', 1)->get();
        $StatusItem = SysStatusItem::all();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['Priority'] = $Priority;
        $json['data']['AccessType'] = $AccessType;
        $json['data']['Company'] = $Company;
        $json['data']['Calendar'] = $Calendar;
        $json['data']['Uom'] = $Uom;
        $json['data']['auto'] = $auto;
//        $json['data']['Task'] = $Task;
        $json['data']['Status'] = $Status;
        $json['data']['StatusItem'] = $StatusItem;
        return json_encode($json);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),
            [
                'StartDate' => 'required',
                'TaskName' => 'required',
                'DueDate' => 'required',
                'CalendarTypeID' => 'required',
                'TaskNo' => 'required',
                'StatusID' => 'required',
                'StatusValue' => 'required'
            ],
            [
                'TaskName.required' => 'Bạn phải nhập tên công việc',
                'StartDate.required' => 'Bạn phải nhập ngày bắt đầu',
                'DueDate.required' => 'Bạn phải nhập hạn hoàn thành',
                'CalendarTypeID.required' => 'Bạn phải nhập kiểu lịch',
//                'TaskNo.required' => 'Mã công việc không được để trống',
//                'TaskNo.unique' => 'Mã công việc đã tồn tại',
                'StatusID' => 'Loại trạng thái không được để trống',
                'StatusValue' => 'Trạng thái không được để trống'
            ]);

        $autoModel = SysAutoNumber::where('NumberKey', 'task')->first();
        if (!empty($autoModel->NumberMask)) {
            AutoNo::storeValidate($validator, $request, 'task', $autoModel, 'công việc');
        }

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $Duration = 0;
        if($request->CalendarTypeID && $request->StartDate && $request->DueDate) {
            $Duration = $request->Duration;
        }
        $employeeAuth = Employee::where(['UserID' => Auth::user()->UserID])->first();

        if($request->AccessType != 2){
            $request->PublicCompanyID = '';
        }
        $parent = null;
        if($request->ParentID){
            $parent = Task::find($request->ParentID);
        }
        $task = new Task([
            'TaskName' => $request->TaskName,
            'ParentID' => $request->ParentID,
            'ParentName' => $request->ParentName,
            'ParentNo' => $request->ParentNo,
            'TaskDescription' => $request->TaskDescription,
            'AccessType' => $request->AccessType,
            'PublicCompanyID' => $request->PublicCompanyID,
            'Priority' => $request->Priority,
            'UomID' => $request->UomID,
            'StartDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $request->StartDate))),
            'DueDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $request->DueDate))),
            'CreateDate' => date('Y-m-d H:i'),
            'Duration' => $Duration,
            'EstimatedQuantity' => $request->EstimatedQuantity,
            'CompanyID' => Session('CompanyID'),
            'TaskNo' => $request->TaskNo,
            'CalendarTypeID' => $request->CalendarTypeID,
            'CreateEmployeeID' => $employeeAuth->EmployeeID,
            'Status' => 1,
            'StatusID' => $request->StatusID,
            'StatusName' => $request->StatusName,
            'StatusValue' => (isset($request->StatusValue)) ? $request->StatusValue : 1,
            'StatusDescription' => (isset($request->StatusDescription)) ? $request->StatusDescription : '',
            'Prefix' => $autoModel->Prefix,
            'Suffix' => $autoModel->Suffix,
            'Level' => empty($request->ParentID) ? 1 : $parent->Level + 1,
            'Detail' => 1,
            'WorkFlowStep' => 1,
            'Type' => $request->Type,
            'DoneNowType' => $request->DoneNowType
        ]);

        // Kiểm tra là thông báo
        if ($request->DoneNowType > 0) {
            $task->TotalActualQuantity = $task->EstimatedQuantity;
            $task->TotalActualHour = $task->EstimatedQuantity;
            $task->ActualCompletedDate = $task->StartDate;
            $task->PercentCompleted = 100;
        }

        if($request->ParentID){
            if ($parent) {
                if($parent->Detail == 1){
                    $parent->Detail = 0;
                    $parent->save();
                }
                if (!$request->ParentName) {
                    $task->ParentName = $parent->TaskName;
                    $task->ParentNo = $parent->ParentNo;
                }
            }
        }
        if($task->ParentID){
            $TaskCateParent = TaskCate::where(['TaskID' => $task->ParentID])->get();
            if($TaskCateParent){
                if($TaskCateParent) {
                    $arrInsert = [];
                    foreach ($TaskCateParent as $item){
                        $arrInsert[] = [
                            'TaskID' => $task->TaskID,
                            'CateID' => $item->CateID,
                            'CateValue' => $item->CateValue
                        ];
                    }
                    if (!empty($arrInsert)) {
                        DB::table('task_cate')->insert($arrInsert);
                    }
                }
            }
        }

        // update ExecutionStatus
        if ($request->StatusID && $request->StatusValue) {
            $statusItem = SysStatusItem::where([
                'StatusID' => $request->StatusID,
                'StatusValue' => $request->StatusValue,
            ])->first();
            if ($statusItem && $statusItem->ExecutionStatus) {
                $task->Status = $statusItem->ExecutionStatus;
                if ($statusItem->ExecutionStatus == 1) {
                    $task->PercentCompleted = 0;
                }
                if ($statusItem->ExecutionStatus == 6) {
                    $task->TotalActualQuantity = $task->EstimatedQuantity;
                    $task->TotalActualHour = $task->EstimatedQuantity;
                    $task->PercentCompleted = 100;
                }
            }
        }

        $task->save();

        // cate list
        if ($request->has('TaskCate')) {
            $TaskCate = $request->post('TaskCate');
            $arrInsertCate = [];
            foreach ($TaskCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'TaskID' => $task->TaskID,
                        'CateID' => $cate['CateID'],
                        'CateValue' => $cate['CateValue']
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('task_cate')->insert($arrInsertCate);
            }
        }

        // ========== task_assign ================

        $userAssigned = [];

        // Người tạo
        $taskAssignCreator = new TaskAssign([
            'TaskID' => $task->TaskID,
            'TaskName' => $task->TaskName,
            'EmployeeID' => $employeeAuth->EmployeeID,
            'EmployeeName' => $employeeAuth->EmployeeName,
            'StartDate' => $task->StartDate,
            'DueDate' => $task->DueDate,
            'EstimateHour' => $Duration,
            'Description' => 'Giao việc, Người tạo',
            'IsCreator' => 1,
            'IsMainResponsiblePerson' => 0,
            'IsResponsiblePerson' => 0,
            'IsAssignee' => 1
        ]);
        $taskAssignCreator->save();
        $userAssigned[] = $employeeAuth->EmployeeID;

        // Người quản lý trực tiếp ~ người giao việc
        $employeeManager = EmployeeManager::where([
            'EmployeeID' => $employeeAuth->EmployeeID,
            'IsDirectManager' => 1
        ])->first();
        if ($employeeManager && ($employeeManager->ManagerID !== $employeeAuth->EmployeeID)) {
            $taskAssignManager = new TaskAssign([
                'TaskID' => $task->TaskID,
                'TaskName' => $task->TaskName,
                'EmployeeID' => $employeeManager->ManagerID,
                'EmployeeName' => $employeeManager->ManagerName,
                'StartDate' => $task->StartDate,
                'DueDate' => $task->DueDate,
                'EstimateHour' => $Duration,
                'Description' => 'Kiểm tra',
                'IsAssignee' => 0,
                'IsChecker' => 1
            ]);
            $taskAssignManager->save();
            Task::genPer($task->TaskID, $employeeManager->ManagerID);
            $userAssigned[] = $employeeManager->ManagerID;
        }else{
            $taskAssignCreator->IsAssignee = 1;
            $taskAssignCreator->save();
        }

        // Người kiểm tra
        $employeeChecker = EmployeeManager::where([
            'EmployeeID' => $employeeAuth->EmployeeID,
            'IsChecker' => 1
        ])->first();
        if ($employeeChecker && ($employeeChecker->ManagerID !== $employeeAuth->EmployeeID)) {
            $taskAssignChecker = new TaskAssign([
                'TaskID' => $task->TaskID,
                'TaskName' => $task->TaskName,
                'EmployeeID' => $employeeChecker->ManagerID,
                'EmployeeName' => $employeeChecker->ManagerName,
                'StartDate' => $task->StartDate,
                'Description' => 'Kiểm tra',
                'DueDate' => $task->DueDate,
                'EstimateHour' => $Duration,
                'IsChecker' => 1
            ]);
            $taskAssignChecker->save();
            Task::genPer($task->TaskID, $employeeChecker->ManagerID);
            $userAssigned[] = $employeeChecker->EmployeeID;
        }else{
            $taskAssignCreator->IsChecker = 0;
            $taskAssignCreator->save();
        }

        Task::genPer($task->TaskID, $taskAssignCreator->EmployeeID);

        // check is copy task
        if ($request->post('CopyID')) {
            $taskIDCopy = $request->post('CopyID');
            Task::copyTask($task->TaskID, $taskIDCopy, $userAssigned);
        }else{
            // clone from parent
            if ($task->ParentID) {
                Task::copyTask($task->TaskID, $task->ParentID, $userAssigned);
            }
        }

        // xu ly logic cho dataflow
        if ($task->Type === 2 && $request->WFID) {
            $params = [
                'TaskID' => $task->TaskID,
                'WFID' => $request->WFID
            ];
            TaskDataflow::createDataflow($params);
            $json['IsDataflow'] = 1;
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $task->TaskID;
        return json_encode($json);
    }

    public function update(Request $request, $id){
        $validate = [];
        $validateTitle = [];
        $task = Task::find($id);

        $employeeUser = Employee::where(['UserID' => Auth::user()->UserID])->first();
        $taskPer = TaskPer::where(['TaskID' => $id, 'EmployeeID' => $employeeUser->EmployeeID])->get();
        $FieldEdit = '';
        $arrFieldEdit = null;
        if($taskPer){
            foreach ($taskPer as $item){
                if($FieldEdit){
                    $FieldEdit .= ','.$item->EditField;
                }else{
                    $FieldEdit = $item->AccessField;
                }
            }
            $arrFieldEdit = array_unique(explode(',', $FieldEdit));
        }
        if(in_array('CalendarTypeID', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $validate['CalendarTypeID'] = 'required';
            $validateTitle['CalendarTypeID.required'] = 'Bạn phải nhập kiểu lịch';
            $task->CalendarTypeID = $request->CalendarTypeID;
        }
        if(in_array('AccessType', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $task->AccessType = $request->AccessType;
            if($request->AccessType != 3){
                $request->PublicCompanyID = '';
            }
            if(in_array('PublicAccessType', $arrFieldEdit)){
                $task->PublicAccessType = $request->PublicAccessType;
            }
        }
        if(in_array('TaskName', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $validate['TaskName'] = 'required';
            $validateTitle['TaskName.required'] = 'Bạn phải nhập tên công việc';
            $task->TaskName = $request->TaskName;
        }
        if(in_array('ParentID', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $task->ParentID = $request->ParentID;
			$task->ParentNo = $request->ParentNo;
            $task->ParentName = $request->ParentName;
        }
        if(in_array('TaskDescription', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $task->TaskDescription = $request->TaskDescription;
        }
        if(in_array('UomID', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $task->UomID = $request->UomID;
        }
        if(in_array('TaskCodeID', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $task->TaskCodeID = $request->TaskCodeID;
        }
        if(in_array('StartDate', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $validate['StartDate'] = 'required';
            $validateTitle['StartDate.required'] = 'Bạn phải nhập ngày bắt đầu';
            $task->StartDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $request->StartDate)));
        }
        if(in_array('DueDate', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $validate['DueDate'] = 'required';
            $validateTitle['DueDate.required'] = 'Bạn phải nhập hạn hoàn thành';
            $task->DueDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $request->DueDate)));
        }
        if(in_array('Duration', $arrFieldEdit) || empty($task->Duration) || in_array('all', $arrFieldEdit)){
//            if($task->StartDate != $request->StartDate || $task->DueDate != $request->DueDate){
//                $Duration = 0;
//                if($request->CalendarTypeID && $request->StartDate && $request->DueDate) {
//                    $Duration = $this->getNumberHour($request->CalendarTypeID, $request->StartDate, $request->DueDate);
//                }
//                $task->Duration = $Duration;
//            }
            if ($request->Duration) {
                $task->Duration = $request->Duration;
            }
        }
        if(in_array('EstimatedQuantity', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $task->EstimatedQuantity = $request->EstimatedQuantity;
        }
        if(in_array('Priority', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $task->Priority = $request->Priority;
        }
        if(in_array('Inactive', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $task->Inactive = !empty($request->Inactive)? 1 : 0;
        }

        if(in_array('DoneNowType', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $task->DoneNowType = $request->DoneNowType;
        }
        if(in_array('StatusID', $arrFieldEdit) || in_array('all', $arrFieldEdit)){
            $task->StatusValue = $request->StatusValue;
            $task->StatusDescription = $request->StatusDescription;

            $statusItem = SysStatusItem::where([
                'StatusID' => $task->StatusID,
                'StatusValue' => $request->StatusValue,
            ])->first();

            if ($statusItem && $statusItem->ExecutionStatus) {
                $task->Status = $statusItem->ExecutionStatus;
                if ($statusItem->ExecutionStatus == 1) {
                    $task->PercentCompleted = 0;
                }
                if ($statusItem->ExecutionStatus == 6) {
                    if ((int) $task->PercentCompleted < 100) {
                        $task->TotalActualQuantity = $task->EstimatedQuantity;
                        $task->TotalActualHour = $task->EstimatedQuantity;
                        $task->PercentCompleted = 100;
                    }
                    if ($request->isDataflow) {
                        $arrDataflow = [
                            'DFID' => $request->DFID,
                            'DFKey' => $request->DFKey,
                            'WFID' => $request->WFID,
                            'WFItemID' => $request->WFItemID,
                            'TaskID' => $task->TaskID,
                            'TaskIDParent' => $request->TaskIDParent,
                            'FeatureStatusID' => $request->StatusID,
                            'FeatureStatusValue' => $request->StatusValue,
                            'FeatureStatusTitle' => $request->StatusDescription,
                            'PercentCompleted' => ($task->PercentCompleted) ? $task->PercentCompleted : 0,
                        ];

                        $taskDataflow = new TaskDataflowController();
                        $taskDataflow->updateDataflow($arrDataflow);
                        if ($task->PercentCompleted && ($task->PercentCompleted >= 100)) {
                            $json['data']['AutoNewTask'] = 1;
                        }
                    }
                }
            }
        }

        $validator = Validator::make($request->all(), $validate, $validateTitle);
        if($task->Prefix.$task->TaskNo.$task->Suffix != $request->TaskNo){
            $validator = Validator::make($request->all(), [
                'TaskNo' => 'required|unique:task'
            ],
                [
                    'TaskNo.required' => 'Mã công việc không được để trống',
                    'TaskNo.unique' => 'Mã công việc đã tồn tại',
                ]);

            $validator = Validator::make($request->all(), $validate, $validateTitle);

            $validate['TaskNo'] = 'required|unique:task';
            $validateTitle['TaskNo.required'] = 'Mã công việc không được để trống';
            $validateTitle['TaskNo.unique'] = 'Mã công việc đã tồn tại';

            $autoModel = SysAutoNumber::where('NumberKey', 'task')->first();
            AutoNo::UpdateValidate($validator, $request, $task,'task', $autoModel, 'công việc');

            $task->TaskNo = $request->TaskNo;
            if (!$request->ParentID) {
                $task->Level = 1;
            } else {
                $taskParent = Task::find($request->ParentID);
                $task->Level = (int)$taskParent->Level + 1;
            }
        }

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        // task cate
        if ($request->has('TaskCate')) {
            $TaskCate = $request->post('TaskCate');
            DB::table('task_cate')->where('TaskID', $id)->delete();
            $arrInsertCate = [];
            foreach ($TaskCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'TaskID' => $id,
                        'CateID' => ($cate['CateID']) ? $cate['CateID'] : null,
                        'CateValue' => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('task_cate')->insert($arrInsertCate);
            }

        }

        if($request->ParentID){
            $parent = Task::find($request->ParentID);
            if($parent->Detail == 1){
                $parent->Detail = 0;
                $parent->save();
            }
        }
        $task->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        return $json;
    }

    public static function deleteArray($idArray){
        Task::destroy($idArray);
        TaskAssign::whereIn('TaskID', $idArray)->delete();
        TaskCate::whereIn('TaskID', $idArray)->delete();
        TaskCheckList::whereIn('TaskID', $idArray)->delete();
        TaskComment::whereIn('TaskID', $idArray)->delete();
        TaskDataflow::where('ConstraintFieldName', '=', 'TaskID')
            ->whereIn('ConstraintFieldValue', $idArray)->delete();
        TaskLink::whereIn('TaskID', $idArray)->delete();
        Evaluation1job::whereIn('TaskID', $idArray)->delete();
        TaskExpense::whereIn('TaskID', $idArray)->delete();
        TaskExpenseTrans::whereIn('TaskID', $idArray)->delete();
        TaskExpenseTransItem::whereIn('TaskID', $idArray)->delete();
        TaskFile::whereIn('TaskID', $idArray)->delete();
        TaskPer::whereIn('TaskID', $idArray)->delete();
        TaskPerAssign::whereIn('TaskID', $idArray)->delete();
        TaskPerCate::whereIn('TaskID', $idArray)->delete();
        TaskPerChecklist::whereIn('TaskID', $idArray)->delete();
        TaskPerLink::whereIn('TaskID', $idArray)->delete();
        TaskPerExecution::whereIn('TaskID', $idArray)->delete();
        TaskPerExpense::whereIn('TaskID', $idArray)->delete();
        TaskPerExpenseDetail::whereIn('TaskID', $idArray)->delete();
        TaskPerFile::whereIn('TaskID', $idArray)->delete();
        TaskPerRequest::whereIn('TaskID', $idArray)->delete();
        TaskPerValuation::whereIn('TaskID', $idArray)->delete();
        TaskPerVideo::whereIn('TaskID', $idArray)->delete();
        TaskRequest::whereIn('TaskID', $idArray)->delete();
        TaskVideo::whereIn('TaskID', $idArray)->delete();
    }

    public function delete(Request $request){
        $user = Auth::user();
        $idArray = $request->post('array_id');
        if ($idArray && count($idArray) > 0) {
            // Kiểm tra nếu có con thì không được xóa
            $allChild = Task::whereIn('ParentID', $idArray)->get();
            if (count($allChild) > 0) {
                $json['status'] = 0;
                $json['msg'] = 'Yêu cầu xóa công việc con';
                $json['data'] = [];
                return json_encode($json);
            }

            if ($user->UserType === 1) {
                self::deleteArray($idArray);
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                // Kiểm tra là người tạo
                $taskAssigns = TaskAssign::whereIn('TaskID', $idArray)
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
                $tasksPer = TaskPer::whereIn('LineID', function ($q) use ($idArray, $user) {
                    $q->select(DB::raw('MAX(LineID)'))
                        ->from('task_per')
                        ->whereIn('TaskID', $idArray)
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
                    self::deleteArray($idArray);
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

        $json['status'] = 0;
        $json['msg'] = 'Không có bản ghi';
        $json['data'] = [];
        return json_encode($json);

    }

    public function view($id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        $Task = Task::find($id);
        if (!$Task) {
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại công việc';
            return json_encode($json);
        }

        $TaskAssignLogin = TaskAssign::where([
            'TaskID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->exists();

        if (!$TaskAssignLogin) {
            // delete per
            Task::deleteAllPer($id, Auth::user()->EmployeeID);
            if ($Task->AccessType == 1) {
                $json['status'] = 2;
                $json['msg'] = 'Bạn không có quyền truy cập công việc này!';
                $json['data'] = '';
                return json_encode($json);
            }
        }

        $PerAll = Task::getPerAll($Task, Auth::user()->EmployeeID);
        if(!$PerAll){
            $json['status'] = 2;
            $json['msg'] = 'Bạn không có quyền truy cập công việc này!';
            $json['data'] = '';
            return json_encode($json);
        }else {
            $TaskLink = TaskLink::where(['TaskID' => $id])->get();
            $TaskAssign = TaskAssign::where(['TaskID' => $id])->get();
            $TaskRequest = TaskRequest::where(['TaskID' => $id])->get();
            $TaskExecutionTrans = TaskExecutionTrans::where(['TaskID' => $id])->get();
            $TaskPlanTrans = TaskPlanTrans::where(['TaskID' => $id])->orderBy('TransDate')->get();
            $TaskCheckList = TaskCheckList::where(['TaskID' => $id])->get();
            $TaskEvaluation1Job = DB::select(DB::raw('SELECT P.TransID, P.TransDate, P.TaskID,
                P.EmployeeID, P.EmployeeName, P.LevelInt LevelIntP, P.LevelResult LevelResultP,
                C.TransID, C.ScaleRateID, C.ScaleRateName, C.IndicatorID, C.IndicatorNo, C.IndicatorName,
                C.FrequencyType, C.IndicatorCalMethod, C.LevelResult LevelResultP, C.LevelInt100  LevelInt100P, C.EstimatedQuantity EstimatedQuantityP,
                S.LevelInt, S.LevelText, S.LevelChar, S.LevelInt100, S.EstimatedQuantity, S.EvaluatorID, S.EvaluatorName, true showChild, true showChildTransDate
                FROM task_evaluation_1job_trans P
                LEFT JOIN task_evaluation_1job_trans_item C
                ON P.TransID = C.TransID
                LEFT JOIN task_evaluation_1job_trans_subitem S
                ON C.TransID = S.TransID AND C.LineID = S.LineID
                WHERE P.TaskID = '.$id.' AND P.FrequencyType = 6
                ORDER BY P.EmployeeID, P.TransDate, C.IndicatorID'));
            $ScaleRateItem = ScaleRateItem::all();
            $TaskExpenseTrans = TaskExpenseTrans::where(['TaskID' => $id])->get();
            $TaskExpenseTransItem = TaskExpenseTransItem::where(['TaskID' => $id])->orderBy('TransType')->orderBy('TransDate')->orderBy('TransID')->get();
            $TaskFile = TaskFile::where(['TaskID' => $id])->get();
            $TaskVideo = TaskVideo::where(['TaskID' => $id])->get();
            $TaskStatus = SysStatusItem::where(['StatusID' => $Task->StatusID])->get();
            $UomName = '';
            $CalendarName = '';
            if ($Task) {
                $Priority = [
                    '1' => 'Khẩn cấp',
                    '2' => 'Cao',
                    '3' => 'Bình thường',
                    '4' => 'Thấp',
                ];
                $Uom = Uom::find($Task->UomID);
                $CalendarType = CalendarType::find($Task->CalendarTypeID);
                $UomName = isset($Uom->UomName) ? $Uom->UomName : '';
                $CalendarName = isset($CalendarType->CalendarName) ? $CalendarType->CalendarName : '';
                $Task->PriorityName = $Priority[$Task->Priority];
            }
            $AccessType = [
                '1' => 'Chia sẻ',
                '2' => 'Công khai',
                '3' => 'Riêng tư'
            ];

            $TaskCate = TaskCate::where('TaskID', $id)->get();
            $arrCateID = [];
            foreach ($TaskCate as $Cate) {
                $arrCateID[] = $Cate->CateID;
            }
            $TaskCateList = TaskCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
            $TaskCateValue = TaskCateValue::whereIn('CateID', $arrCateID)->get();

            $Company = Company::all();
            $Calendar = CalendarType::all();
            $Uom = Uom::all();
            $StatusValue = [];
            if ($Task->StatusID) {
                $StatusValue = SysStatusItem::where(['StatusID' => $Task->StatusID])->get();
            }
            $Indicator = IndicatorTableMap::where('TaskID', '=', $id)->get();
            $IndicatorEmployee = DB::table('task_indicator_table_employee_map')
                ->join('task_indicator_table_map', 'task_indicator_table_employee_map.MapID', '=', 'task_indicator_table_map.MapID')
                ->where('task_indicator_table_map.TaskID', '=', $id)
                ->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['Task'] = $Task;
            $json['data']['TaskCate'] = $TaskCate;
            $json['data']['TaskCateList'] = $TaskCateList;
            $json['data']['TaskCateValue'] = $TaskCateValue;
            $json['data']['TaskLink'] = $TaskLink;
            $json['data']['TaskAssign'] = $TaskAssign;
            $json['data']['TaskRequest'] = $TaskRequest;
            $json['data']['TaskExecutionTrans'] = $TaskExecutionTrans;
            $json['data']['TaskPlanTrans'] = $TaskPlanTrans;
            $json['data']['TaskCheckList'] = $TaskCheckList;
//            $json['data']['TaskValuation'] = $TaskValuation;
            $json['data']['TaskEvaluation1Job'] = $TaskEvaluation1Job;
            $json['data']['ScaleRateItem'] = $ScaleRateItem;
            $json['data']['TaskExpenseTrans'] = $TaskExpenseTrans;
            $json['data']['TaskExpenseTransItem'] = $TaskExpenseTransItem;
            $json['data']['TaskFile'] = $TaskFile;
            $json['data']['TaskVideo'] = $TaskVideo;
            $json['data']['UomName'] = $UomName;
            $json['data']['CalendarName'] = $CalendarName;

            $json['data']['PriorityOptions'] = $Priority;
            $json['data']['AccessTypeOptions'] = $AccessType;
            $json['data']['CompanyOptions'] = $Company;
            $json['data']['CalendarOptions'] = $Calendar;
            $json['data']['UomOptions'] = $Uom;
            $json['data']['StatusValueOption'] = $StatusValue;
            $json['data']['TaskStatus'] = $TaskStatus;
            $json['data']['PerAll'] = $PerAll;

            $json['data']['Indicator'] = $Indicator;
            $json['data']['IndicatorEmployee'] = $IndicatorEmployee;
            return json_encode($json);
        }
	}

    public function getListChild(Request $request){
        $ParentID = $request->ParentID;
        $data = Task::where('ParentID', '=', $ParentID)->get();

        $taskAssignQuery = TaskAssign::query();
        $taskAssignQuery->select('task.TaskName', 'task_assign.*', 'sys_user.Avata', 'employee.UserID', 'employee.EmployeeName', 'employee.EmployeeID')
            ->join('employee', 'employee.EmployeeID', '=', 'task_assign.EmployeeID')
            ->join('task', 'task.TaskID', '=', 'task_assign.TaskID')
            ->join('sys_user', 'employee.UserID', '=', 'sys_user.UserID')
            ->where(function ($q) {
                $q->where('task_assign.IsMainResponsiblePerson', '=', 1)
                    ->orWhere('task_assign.IsResponsiblePerson', '=', 1)
                    ->orWhere('task_assign.IsExecutor', '=', 1);
            })
            ->where('task.ParentID', $ParentID)
            ->groupBy('task_assign.TaskID', 'task_assign.EmployeeID');
        $taskAssign = $taskAssignQuery->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        $json['TaskAssign'] = $taskAssign;
        return json_encode($json);
    }

    public function listModal(Request $request){
        $data = null;
        $table = $request->post('table');
        $class = "Module\Task\Models\\".$table;
        $model = new $class();
        $query = $model::query();
        $where = [];
        if($request->post('search')){
            if ($table == 'task' || $table == 'Task') {
                $query->where('task.TaskName', 'like', '%' . $request->search . '%');
            }else{
                $query->where($table . 'Name', 'like', '%' . $request->search . '%');
            }
        }
        if ($table == 'task' || $table == 'Task') {
            $authEmployee = Employee::where(['UserID' => Auth::user()->UserID])->first();
            if (Auth::user()->UserType !== 1) {
                $query->select('task.*');

                $queryShare = clone $query;

                $query->where(function ($q) use ($authEmployee) {
                    $q->where(function ($q){
                        $q->where('task.AccessType', '=', 2);
                        $q->whereNull('task.PublicCompanyID');
                    });
                    $q->orWhere(function ($q) use ($authEmployee) {
                        $q->where('task.AccessType', '=', 2);
                        $q->whereNotNull('task.PublicCompanyID');
                        $q->where('task.PublicCompanyID', '=', $authEmployee->CompanyID);
                    });
                });

                $queryShare->join('task_assign', 'task.TaskID', '=', 'task_assign.TaskID');
                $queryShare->where('task_assign.EmployeeID', '=', $authEmployee->EmployeeID);

                $query->unionAll($queryShare);
                $query = DB::query()->fromSub($query, 'TU');
            }
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->orderBy($table . 'ID', 'DESC');
        $data = $query->paginate($per_page);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function getHour(Request $request){
        $StartDate = $request->post('StartDate');
        $DueDate = $request->post('DueDate');
        $CalendarTypeID = $request->post('CalendarTypeID');
        $data = $this->getNumberHour($CalendarTypeID, $StartDate, $DueDate);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function getNumberHour($calendarID, $fromdate, $todate){
        $fromdateC = date('Y-m-d', strtotime(str_replace('/', '-', $fromdate)));
        $todateC = date('Y-m-d', strtotime(str_replace('/', '-', $todate)));
        $fromdateC = new \DateTime($fromdateC);
        $todateC = new \DateTime($todateC);
        $diff =date_diff($fromdateC, $todateC);
        $days = (int)$diff->format("%R%a");
        if($days < 0){
            return 0;
        }else{
            $days = $days + 1;
        }

        //Tính số ngày chủ nhật
        $FromDateInWeek = (int)date('N', strtotime(str_replace('/', '-', $fromdate)));
        $ToDateInWeek = (int)date('N', strtotime(str_replace('/', '-', $todate)));
        $numberSunday = $days - 1 - (7 - $FromDateInWeek);
        $numberSunday = floor($numberSunday/7) + 1;
        $numberSatday = $numberSunday;
        if($FromDateInWeek == 7){
            $numberSatday = $numberSunday - 1;
        }
        if($ToDateInWeek == 6){
            $numberSatday = $numberSunday + 1;
        }
        $calendarType = CalendarType::find($calendarID);
        $numberHol = DB::select( DB::raw("
            SELECT sum(if(DAYOFWEEK(SunDate) = 1, 0, if(DAYOFWEEK(SunDate) = 7, ".$calendarType->HoursSaturday.", ".$calendarType->HoursDay."))) as numberHour
            FROM calendar_type_hour
            WHERE SunDate >= '".date('Y-m-d H:i', strtotime(str_replace('/', '-', $fromdate)))."'
            AND SunDate <= '".date('Y-m-d H:i', strtotime(str_replace('/', '-', $todate)))."'
        "));
        $numberHol = (int)$numberHol[0]->numberHour;
        return $days * 8 - $numberSatday * (8 - 4) - 8 * $numberSunday - $numberHol;

    }

    // Indicator
    public function updateIndicator(Request $request){
        $taskID = $request->post('TaskID');
        $taskNo = $request->post('TaskNo');
        $indicators = $request->post('Indicator');

        DB::table('task_indicator_table_employee_map')->where('TaskID', '=', $taskID)->delete();
        IndicatorTableMap::where('TaskID', '=', $taskID)->delete();

        if (count($indicators) > 0) {
            foreach ($indicators as $indicator) {
                if ($indicator['Indicator']) {
                    $indicatorTableMap = new IndicatorTableMap([
                        'TaskID' => $taskID,
                        'TaskNo' => $taskNo,
                        'IndicatorID' => $indicator['Indicator']['IndicatorID'],
                        'IndicatorNo' => $indicator['Indicator']['IndicatorNo'],
                        'IndicatorName' => $indicator['Indicator']['IndicatorName'],
                        'IndicatorCalMethod' => $indicator['Indicator']['IndicatorCalMethod'],
                        'ScaleRateID' => $indicator['Indicator']['ScaleRateID'],
                        'ScaleRateName' => $indicator['Indicator']['ScaleRateName'],
                    ]);
                    $indicatorTableMap->save();

                    $arrInsert = [];
                    foreach ($indicator['Employee'] as $employee) {
                        $arrInsert[] = [
                            'MapID' => $indicatorTableMap->MapID,
                            'TaskID' => $taskID,
                            'EmployeeID' => $employee['EmployeeID'],
                            'EmployeeName' => $employee['EmployeeName']
                        ];
                    }
                    if (count($arrInsert) > 0) {
                        DB::table('task_indicator_table_employee_map')->insert($arrInsert);
                    }
                }
            }
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = '';
        return json_encode($json);
    }

    //Assign
    public function updateAssign(Request $request, $id){
        $TaskAssign = $request->post('TaskAssign');
        $arrTaskAssign = [];
        $arrEmployee = [];
        foreach ($TaskAssign as $item){
            if($item['EmployeeID']){
                $arrTaskAssign[] = [
                    'TaskID' => $id,
                    'EmployeeID' => $item['EmployeeID'],
                    'EmployeeName' => $item['EmployeeName'],
                    'StartDate' => (isset($item['StartDate'])) ? date('Y-m-d', strtotime(str_replace('/', '-', $item['StartDate']))) : null,
                    'DueDate' => isset($item['DueDate']) ? date('Y-m-d', strtotime(str_replace('/', '-', $item['DueDate']))) : null,
                    'EstimatedHour' => $item['EstimatedHour'],
                    'EstimatedQuantity' => $item['EstimatedQuantity'],
                    'EstimatedQuantityRate' => $item['EstimatedQuantityRate'],
                    'Description' => $item['Description'],
                    'IsChecker' => $item['IsChecker'],
                    'IsMainResponsiblePerson' => $item['IsMainResponsiblePerson'],
                    'IsResponsiblePerson' => $item['IsResponsiblePerson'],
                    'IsAssignee' => $item['IsAssignee'],
                    'IsCreator' => $item['IsCreator'],
                    'IsFollower' => $item['IsFollower'],
                    'IsExecutor' => $item['IsExecutor'],
                ];
                $arrEmployee[] = $item['EmployeeID'];
            }
        }
        TaskAssign::where(['TaskID' => $id])->delete();
        TaskPer::where(['TaskID' => $id])->delete();
        TaskPerAssign::where(['TaskID' => $id])->delete();
        TaskPerCate::where(['TaskID' => $id])->delete();
        TaskPerChecklist::where(['TaskID' => $id])->delete();
        TaskPerExecution::where(['TaskID' => $id])->delete();
        TaskPerExpenseDetail::where(['TaskID' => $id])->delete();
        TaskPerFile::where(['TaskID' => $id])->delete();
        TaskPerLink::where(['TaskID' => $id])->delete();
        TaskPerRequest::where(['TaskID' => $id])->delete();
        TaskPerValuation::where(['TaskID' => $id])->delete();
        TaskPerVideo::where(['TaskID' => $id])->delete();
        if(!empty($arrTaskAssign)){
            DB::table('task_assign')->insert($arrTaskAssign);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $arrTaskAssign;
        return json_encode($json);
    }
    //Plan
    public function updatePlan(Request $request, $id){
        $PeriodArray = $request->post('PeriodArray');
        $TaskPlanTrans = $request->post('TaskPlanTrans');
        $TaskPlanTransItem = $request->post('TaskPlanTransItem');
        $TaskPlanTransSubItem = $request->post('TaskPlanTransSubItem');
        $TaskPlanTrans['TransDate'] = IjDate::ConvertClientDateToServerDate($TaskPlanTrans['TransDate']);
        $TaskPlanTrans['DueDate'] = IjDate::ConvertClientDateToServerDate($TaskPlanTrans['DueDate']);
        $TaskPlanTrans['StartDate'] = IjDate::ConvertClientDateToServerDate($TaskPlanTrans['StartDate']);
        $planTrans = TaskPlanTrans::where(['TaskID' => $id, 'TransDate' => $TaskPlanTrans['TransDate']])->first();

        if(!$planTrans){
            $planTrans = new TaskPlanTrans([
                'TaskID' => $id,
                'TransDate' => $TaskPlanTrans['TransDate'],
                'StartDate' => $TaskPlanTrans['StartDate'],
                'DueDate' => $TaskPlanTrans['DueDate'],
                'EstimatedQuantity' => $TaskPlanTrans['EstimatedQuantity'],
                'TransComment' => $TaskPlanTrans['TransComment'],
            ]);
        }else{
            $planTrans->TransDate = $TaskPlanTrans['TransDate'];
            $planTrans->StartDate = $TaskPlanTrans['StartDate'];
            $planTrans->DueDate = $TaskPlanTrans['DueDate'];
            $planTrans->EstimatedQuantity = $TaskPlanTrans['EstimatedQuantity'];
            $planTrans->TransComment = $TaskPlanTrans['TransComment'];
        }
        $planTrans->save();
        $arrTaskPlanTransItem = [];
        foreach ($TaskPlanTransItem as $item){
            $arrTaskPlanTransItem[] = [
                    'TaskID' => $id,
                    'TransID' => $planTrans->TransID,
                    'EmployeeID' => $item['EmployeeID'],
                    'EmployeeName' => $item['EmployeeName'],
                    'EstimatedQuantity' => $item['EstimatedQuantity'],
                    'EstimatedQuantityRate' => $item['EstimatedQuantityRate'],
                    'TransDate' => $planTrans['TransDate']
                ];
        }
        TaskPlanTransItem::where(['TaskID' => $id, 'TransDate' => $planTrans['TransDate']])->delete();
        if(!empty($arrTaskPlanTransItem)){
            DB::table('task_plan_trans_item')->insert($arrTaskPlanTransItem);
        }

        $arrTaskPlanTransSubItem = [];
        $TaskPlanTransItem = TaskPlanTransItem::where(['TaskID' => $id, 'TransDate' => $planTrans['TransDate']])->get();
        $ArrIDPlanTransItem = [];
        foreach ($TaskPlanTransItem as $item){
            $ArrIDPlanTransItem[$item->EmployeeID] = $item->TransItemID;
        }

        foreach ($TaskPlanTransSubItem as $keyS=>$itemS){
            $ArrKey = explode('_', $keyS);
            $arrTaskPlanTransSubItem[] = [
                'TaskID' => $id,
                'TransID' => $planTrans['TransID'],
                'TransItemID' => $ArrIDPlanTransItem[$ArrKey[0]],
                'EmployeeID' => $ArrKey[0],
                'EstimatedQuantity' => $itemS['EstimatedQuantity'],
                'PeriodValue' => $ArrKey[1],
                'PeriodType' => $itemS['PeriodType'],
                'FromDate' => $ArrKey[1],
                'ToDate' => $ArrKey[1],
                'TransDate' => $planTrans->TransDate,
            ];
        }

        TaskPlanTransSubItem::where(['TaskID' => $id, 'TransDate' => $planTrans->TransDate])->delete();
        if(!empty($arrTaskPlanTransSubItem)){
            DB::table('task_plan_trans_subitem')->insert($arrTaskPlanTransSubItem);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $planTrans;
        return json_encode($json);
    }
    //Plan
    public function getPlan(Request $request, $id){
        $TransDate = $request->post('TransDate');
        $TransDate = IjDate::ConvertClientDateToServerDate($TransDate);
        $TaskPlanTrans = TaskPlanTrans::where(['TaskID' => $id, 'TransDate' => $TransDate])->first();
        if($TaskPlanTrans){
            $json['TaskPlanTrans'] = $TaskPlanTrans;
            $TaskPlanTransItem = TaskPlanTransItem::where(['TaskID' => $id, 'TransDate' => $TransDate])->get();
            $TaskPlanTransSubItem = TaskPlanTransSubItem::where(['TaskID' => $id, 'TransDate' => $TransDate])->get();
            $json['TaskPlanTransSubItem'] = $TaskPlanTransSubItem;
            $json['TaskPlanTransItem'] = $TaskPlanTransItem;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = '';
        return json_encode($json);
    }
    //Request
    public function updateRequest(Request $request, $id){
        $RequestArray = $request->post('TaskRequest');
        $arrRequest = [];
        foreach ($RequestArray as $item){
            if($item['RequestDate']){
                $arrRequest[] = [
                    'TaskID' => $id,
                    'RequestDate' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $item['RequestDate']))),
                    'RequestDueDate' => date('Y-m-d', strtotime(str_replace('/', '-', $item['RequestDueDate']))),
//                    'RequestName' => $item['RequestName'],
                    'Description' => $item['Description']
                ];
            }
        }
        TaskRequest::where(['TaskID' => $id])->delete();
        if(!empty($arrRequest)){
            DB::table('task_request')->insert($arrRequest);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }


    //Check List
    public function updateCheckList(Request $request, $id){
        $RequestArray = $request->post('TaskCheckList');
        $arrRequest = [];
        foreach ($RequestArray as $item){
            if($item['CheckListName']){
                $arrRequest[] = [
                    'TaskID' => $id,
                    'CompletedDate' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $item['CompletedDate']))),
                    'CheckListName' => $item['CheckListName'],
                    'Status' => $item['Status']
                ];
            }
        }
        TaskCheckList::where(['TaskID' => $id])->delete();
        if(!empty($arrRequest)){
            DB::table('task_check_list')->insert($arrRequest);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    //Check List
    public function updateExpense(Request $request, $id){
        $RequestArray = $request->post('TaskExpense');
        $master = $RequestArray['master'];
        $detail = $RequestArray['detail'];
        $arrRequest = [];
        if(isset($master['TransID'])){
            $trans = TaskExpenseTrans::find($master['TransID']);
            if($trans){
                $trans->TransType = $master['TransType'];
                $trans->TransDate = date('Y-m-d', strtotime(str_replace('/', '-', $master['TransDate'])));
                $trans->Comment = $master['TransType'];
                $trans->save();
            }
        }else{
            $trans = new TaskExpenseTrans([
                'TaskID' => $id,
                'TransType' => $master['TransType'],
                'TransDate' => date('Y-m-d', strtotime(str_replace('/', '-', $master['TransDate']))),
                'Comment' => $master['Comment'],

            ]);

            $trans->save();
        }
        if(isset($trans->TransID)) {
            foreach ($detail as $item) {
                if ($item['Description']) {
                    $arrRequest[] = [
                        'TransID' => $trans->TransID,
                        'TransDate' => date('Y-m-d', strtotime(str_replace('/', '-', $master['TransDate']))),
                        'TransType' => $master['TransType'],
                        'TaskID' => $id,
                        'ExpenseNo' => (!empty($item['ExpenseNo'])) ? $item['ExpenseNo'] : null,
                        'ExpenseName' => (!empty($item['ExpenseName']) ? $item['ExpenseName'] : null),
                        'ExpenseID' => (!empty($item['ExpenseID']) ? $item['ExpenseID'] : null),
                        'Description' => (!empty($item['Description']) ? $item['Description'] : null),
                        'UomID' => (!empty($item['UomID']) ? $item['UomID'] : null),
                        'UomName' => (!empty($item['UomName']) ? $item['UomName'] : null),
                        'Quantity' => $item['Quantity'],
                        'UnitPrice' => $item['UnitPrice'],
                        'Amount' => $item['Amount'],
                        'TaxRate' => $item['TaxRate'],
                        'TaxAmount' => $item['TaxAmount'],
                    ];
                }
            }
            TaskExpenseTransItem::where(['TransID' => $trans->TransID])->delete();
            if (!empty($arrRequest)) {
                DB::table('task_expense_trans_item')->insert($arrRequest);
            }
            $TaskExpenseTrans = TaskExpenseTrans::where(['TaskID' => $id])->get();
            $TaskExpenseTransItem = TaskExpenseTransItem::where(['TaskID' => $id])->orderBy('TransType')->orderBy('TransDate')->orderBy('TransID')->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['TransID'] = $trans->TransID;
            $json['data']['TaskExpenseTrans'] = $TaskExpenseTrans;
            $json['data']['TaskExpenseTransItem'] = $TaskExpenseTransItem;
            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = [];
            return json_encode($json);
        }
    }


    //Check Execution_bk
    public function updateExecution_bk(Request $request, $id){
        $RequestArray = $request->post('TaskExecution');
        $arrRequest = [];
        foreach ($RequestArray as $item){
            if($item['TransDate']){
                $arrRequest[] = [
                    'TaskID' => $id,
                    'TransDate' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $item['TransDate']))),
                    'ActualHour' => $item['ActualHour'],
                    'ActualQuantity' => $item['ActualQuantity'],
                    'CalMethod' => $item['CalMethod'],
                    'PercentCompleted' => $item['PercentCompleted'],
                    'Description' => $item['Description'],
                    'EmployeeID' => $item['EmployeeID'],
                    'EmployeeName' => $item['EmployeeName'],
                    'StatusID' => $item['StatusID'],
                    'StatusName' => $item['StatusName'],
                    'StatusValue' => $item['StatusValue'],
                    'StatusDescription' => $item['StatusDescription'],
                ];
            }
        }
        if(!empty($arrRequest)){
            TaskExecutionTrans::where(['TaskID' => $id])->delete();
            DB::table('task_execution_trans')->insert($arrRequest);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    //Check Execution
    public function updateExecution(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $param = $request->post('TaskExecution');
        $task = Task::find($id);
        $task->TotalActualQuantity = $task->TotalActualQuantity + $param['ActualQuantity'] - $param['OldActualQuantity'];
        $task->TotalActualConvertQuantity = $task->TotalActualQuantity;
//        if ($task->EstimatedQuantity) $task->PercentCompleted = $task->TotalActualQuantity * 100 / $task->EstimatedQuantity;
        $task->PercentCompleted = ($request->post('PercentCompleted')) ? $request->post('PercentCompleted') : 0;

        $TaskExecutionEnd = TaskExecutionTrans::where(['TaskID' => $id])->max('TransDate');
        if(($TaskExecutionEnd && date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $param['TransDate']))) >= $TaskExecutionEnd) || !$TaskExecutionEnd){
            $task->StatusValue = $param['StatusValue'];
            $task->StatusDescription = $param['StatusDescription'];
            if (isset($param['ExecutionStatus'])) {
                $task->Status = $param['ExecutionStatus'];
            }
        }

        $task->save();
        if(isset($param['TransID']) && !empty($param['TransID'])){
            $TaskExecution = TaskExecutionTrans::find($param['TransID']);
            $TaskExecution->TaskID = $id;
            $TaskExecution->TransDate = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $param['TransDate'])));
            $TaskExecution->ActualHour = $param['ActualHour'];
            $TaskExecution->ActualQuantity = $param['ActualQuantity'];
            $TaskExecution->Description = $param['Description'];
            $TaskExecution->EmployeeID = $param['EmployeeID'];
            $TaskExecution->EmployeeName = $param['EmployeeName'];
            $TaskExecution->StatusID = $param['StatusID'];
            $TaskExecution->StatusName = $param['StatusName'];
            $TaskExecution->StatusValue = $param['StatusValue'];
            $TaskExecution->StatusDescription = $param['StatusDescription'];
            $TaskExecution->save();

            if (isset($param['isDataflow']) && $param['isDataflow']) {
                $arrDataflow = [
                    'DFID' => $param['DFID'],
                    'DFKey' => $param['DFKey'],
                    'WFID' => $param['WFID'],
                    'WFItemID' => $param['WFItemID'],
                    'TaskID' => $param['TaskID'],
                    'TaskIDParent' => $param['TaskIDParent'],
                    'FeatureStatusID' => $param['StatusID'],
                    'FeatureStatusValue' => $param['StatusValue'],
                    'FeatureStatusTitle' => $param['StatusDescription'],
                    'PercentCompleted' => ($task->PercentCompleted) ? $task->PercentCompleted : 0,
                ];

                $taskDataflow = new TaskDataflowController();
                $taskDataflow->updateDataflow($arrDataflow);
                if ($task->PercentCompleted && ($task->PercentCompleted >= 100)) {
                    $json['data']['AutoNewTask'] = 1;
                }
            }

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['TaskExcution'] = $TaskExecution;
            $json['data']['Task'] = $task;
            return json_encode($json);

        }else{
            $TaskExcution = new TaskExecutionTrans([
                'TaskID' => $id,
                'TransDate' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $param['TransDate']))),
                'ActualHour' => $param['ActualHour'],
                'ActualQuantity' => $param['ActualQuantity'],
                'Description' => $param['Description'],
                'EmployeeID' => $param['EmployeeID'],
                'EmployeeName' => $param['EmployeeName'],
                'StatusID' => $param['StatusID'],
                'StatusName' => $param['StatusName'],
                'StatusValue' => $param['StatusValue'],
                'StatusDescription' => $param['StatusDescription'],
            ]);
            $TaskExcution->save();
            if (isset($param['isDataflow']) && $param['isDataflow']) {
                $arrDataflow = [
                    'DFID' => $param['DFID'],
                    'DFKey' => $param['DFKey'],
                    'WFID' => $param['WFID'],
                    'WFItemID' => $param['WFItemID'],
                    'TaskID' => $param['TaskID'],
                    'TaskIDParent' => $param['TaskIDParent'],
                    'FeatureStatusID' => $param['StatusID'],
                    'FeatureStatusValue' => $param['StatusValue'],
                    'FeatureStatusTitle' => $param['StatusName'],
                    'PercentCompleted' => ($task->PercentCompleted) ? $task->PercentCompleted : 0,
                ];

                $taskDataflow = new TaskDataflowController();
                $taskDataflow->updateDataflow($arrDataflow);
            }



            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['TaskExcution'] = $TaskExcution;
            $json['data']['Task'] = $task;
            return json_encode($json);

        }

    }
    //Delete Excution

    public function deleteExecution(Request $request){
        $param = $request->post('TransID');
        $data = TaskExecutionTrans::find($param);
        $task = Task::find($data->TaskID);
        $task->TotalActualQuantity = $task->TotalActualQuantity - $data->ActualQuantity;
        $task->TotalActualConvertQuantity = $task->TotalActualQuantity;
        if ($task->EstimatedQuantity) {
            $task->PercentCompleted = $task->TotalActualQuantity * 100 / $task->EstimatedQuantity;
        }
        $task->save();
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['param'] = $param;
        $json['data']['Task'] = $task;
        return json_encode($json);
    }

    //Get list Table
    public function getAllTable(){
        $table = SysTable::where(['isTaskLink' => 1])->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $table;
        return json_encode($json);
    }


    //Get list Uom
    public function getAllUom(){
        $data = Uom::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    //Link
    public function updateLink(Request $request, $id){
        $LinkArray = $request->post('TaskLink');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'TaskID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        TaskLink::where(['TaskID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('task_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }


    //Upload File
    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $TaskFile = TaskFile::find($request->LineID);
        }else{
            $TaskFile = new TaskFile();
        }
        $TaskFile->FileName = $request->FileName;
        $TaskFile->FileNameRoot = $request->FileName;
        $TaskFile->Description = $request->Description;
        $TaskFile->FileType = $request->FileType;
        $TaskFile->FileSize = $request->FileSize;
        $TaskFile->TaskID = $id;
        $TaskFile->DocNo = $request->DocNo;
        $TaskFile->DocName = $request->DocName;
        $TaskFile->DocID = $request->DocID;
        $TaskFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $TaskFile->UserModified = Auth::user()->username;
        $fileattach = $request->FileUpload;

        if ($fileattach && $request->changeFile == 1) {
            $ext = strtolower($fileattach->getClientOriginalExtension());
            $size = $fileattach->getSize();
            $name = $fileattach->getClientOriginalName();
            if ($size <= 400000000) {
                $arr_ext_img = ['gif', 'jpeg', 'jpg', 'png', 'ico', 'psd', 'ai'];
                $arr_ext_doc = ['pptx', 'ppt', 'pps', 'xls', 'xlsx', 'csv', 'doc', 'docx', 'pdf', 'txt', 'zip', 'rar', 'cif'];
                $arr_ext_other = ['aif', 'cda', 'mid', 'midi', 'mp3', 'mpa', 'ogg', 'wav', 'wma', 'wpl', '7z', 'arj', 'deb', 'pkg', 'rar', 'rpm', 'tar', '.gz', 'zip', 'z',
                    'bin', 'dmg', 'iso', 'toast', 'vcd', 'csv', 'dat', 'db', 'dbf', 'log', 'mdb', 'sav', 'sql', 'tar', 'xml', 'email', 'eml', 'emlx', 'msg', 'oft', 'ost',
                    'pst', 'vcf', 'fnt', 'fon', 'otf', 'ttf', 'ai', 'bmp', 'gif', 'ico', 'ps', 'psd', 'svg', 'tif', 'tiff', '3g2', '3gp', 'avi', 'flv', 'h264', 'm4v', 'mkv', 'mov',
                    'mp4', 'mpg', 'mpeg', 'rm', 'swf', 'vob', 'wmv', 'm4a'
                ];
                $is_doc = in_array($ext, $arr_ext_doc);
                $is_img = in_array($ext, $arr_ext_img);
                $is_other = in_array($ext, $arr_ext_other);
                if ($is_doc || $is_img || $is_other) {
                    $nameFile = Auth::user()->username . '_' . (date('YmdHis') . gettimeofday()['usec']) . "." . $ext;
                    $linkFileAttach = '/files-attach/' . $nameFile;
                    $fileModel = new FileModel(
                        [
                            'FileName' => $name,
                            'FileType' => $ext,
                            'FileSize' => $size,
                            'DateModified' => date('Y-m-d h:i:s'),
                            'UserModified' => $userId,
                            'Link' => $linkFileAttach,
                        ]
                    );

                    $fileModel->save();
                    $fileattach->move('files-attach', $nameFile);

                    $TaskFile->Link = $linkFileAttach;
                    $TaskFile->DateModifiedRoot = $fileModel->DateModified;
                    $TaskFile->FileID = $fileModel->FileID;

                } else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            } else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $TaskFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $TaskFile;
        return json_encode($json);
    }


    //Delete File
    public function deleteFile(Request $request, $id){
        $tFile = TaskFile::where(['LineID' => $id])->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }
    //Download File
    public function downloadFile(Request $request, $id){
        $file = FileModel::find($id);
        return response()->download(public_path($file->Link));
    }
    //Download File
    public function downloadAllFile(Request $request, $id){

        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        if (!$id) {
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại tài liệu';
            return json_encode($json);
        }
        $files = TaskFile::where('TaskID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }

        if (count($arrLink)) {
            $task = Task::find($id);
            $zipName = '';
            if ($task) {
                $zipName = IjString::CreateSlug($task->TaskName, '-');
            }
            $zipLink = IjFile::CreateZipArchive($arrLink, true, $zipName);
            if ($zipLink) {
                $json['status'] = 1;
                $json['msg'] = 'Thành công';
                $json['data'] = $zipLink;
                return json_encode($json);
//                return response()->download(public_path($zipLink));
            }
        }

        $json['msg'] = 'Không tồn tại file zip';
        return json_encode($json);
    }


    //Upload Video
    public function uploadVideo(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $TaskVideo = TaskVideo::find($request->LineID);
        }else{
            $TaskVideo = new TaskVideo();
        }
        $TaskVideo->VideoName = $request->VideoName;
        $TaskVideo->FileNameRoot = $request->VideoName;
        $TaskVideo->Description = $request->Description;
        $TaskVideo->VideoType = $request->VideoType;
        $TaskVideo->VideoSize = $request->VideoSize;
        $TaskVideo->TaskID = $id;
        $TaskVideo->DocNo = $request->DocNo;
        $TaskVideo->DocName = $request->DocName;
        $TaskVideo->DocID = $request->DocID;
        $TaskVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $TaskVideo->UserModified = Auth::user()->username;
        $fileattach = $request->VideoUpload;

        if ($fileattach && $request->changeVideo == 1) {
            $ext = strtolower($fileattach->getClientOriginalExtension());
            $size = $fileattach->getSize();
            $name = $fileattach->getClientOriginalName();
            if ($size <= 400000000) {
                $arr_ext_video = ['mpg', 'mpeg', 'avi', 'wmv', 'mov', 'rm', 'ram', 'swf', 'flv', 'ogg', 'webm', '3g2', '3gp', 'avi', 'h264', 'm4v', 'mkv', 'vob',
                    'mp4', 'aif', 'cda', 'mid', 'midi', 'mp3', 'mpa', 'ogg', 'wav', 'wma', 'wpl', 'm4a'];
                $is_video = in_array($ext, $arr_ext_video);
                if ($is_video) {
                    $nameVideo = Auth::user()->username . '_' . (date('YmdHis') . gettimeofday()['usec']) . "." . $ext;
                    $linkFileAttach = '/files-attach/' . $nameVideo;
                    $fileModel = new FileModel(
                        [
                            'FileName' => $name,
                            'FileType' => $ext,
                            'FileSize' => $size,
                            'DateModified' => date('Y-m-d h:i:s'),
                            'UserModified' => $userId,
                            'Link' => $linkFileAttach,
                        ]
                    );

                    $fileModel->save();
                    $fileattach->move('files-attach', $nameVideo);

                    $TaskVideo->Link = $linkFileAttach;
                    $TaskVideo->DateModifiedRoot = $fileModel->DateModified;
                    $TaskVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }

            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }

        }
        $TaskVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $TaskVideo;
        return json_encode($json);
    }


    //Delete File
    public function deleteVideo(Request $request, $id){
        $tVideo = TaskVideo::where(['LineID' => $id])->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }
    //Download File
    public function downloadVideo(Request $request, $id){
        $file = FileModel::find($id);
        return response()->download(public_path($file->Link));
    }


    public function taskPerContent(Request $request){
        $EmployeeID = $request->EmployeeID;
        $TaskID = $request->TaskID;
        $TaskPer = TaskPer::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
        if(!$TaskPer){
            $GenPer = Task::genPer($TaskID, $EmployeeID);
            if($GenPer){
                $TaskPer = $GenPer['TaskPer'];
                $TaskPerAssign = $GenPer['TaskPerAssign'];
                $TaskPerCate = $GenPer['TaskPerCate'];
                $TaskPerLink = $GenPer['TaskPerLink'];
                $TaskPerExecution = $GenPer['TaskPerExecution'];
                $TaskPerChecklist = $GenPer['TaskPerChecklist'];
                $TaskPerValuation = $GenPer['TaskPerValuation'];
                $TaskPerExpense = $GenPer['TaskPerExpense'];
                $TaskPerExpenseDetail = $GenPer['TaskPerExpenseDetail'];
                $TaskPerFile = $GenPer['TaskPerFile'];
                $TaskPerRequest = $GenPer['TaskPerRequest'];
                $TaskPerVideo = $GenPer['TaskPerVideo'];
            }else{
                $TaskPer = null;
                $TaskPerAssign = null;
                $TaskPerCate = null;
                $TaskPerLink = null;
                $TaskPerExecution = null;
                $TaskPerChecklist = null;
                $TaskPerValuation = null;
                $TaskPerExpense = null;
                $TaskPerExpenseDetail = null;
                $TaskPerFile = null;
                $TaskPerRequest = null;
                $TaskPerVideo = null;
            }
        }else{

            $TaskPerAssign = TaskPerAssign::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
            $TaskPerCate = TaskPerCate::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
            $TaskPerLink = TaskPerLink::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
            $TaskPerExecution = TaskPerExecution::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
            $TaskPerChecklist = TaskPerChecklist::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
            $TaskPerValuation = TaskPerValuation::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
            $TaskPerExpense = TaskPerExpense::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
            $TaskPerExpenseDetail = TaskPerExpenseDetail::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
            $TaskPerFile = TaskPerFile::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
            $TaskPerRequest = TaskPerRequest::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
            $TaskPerVideo = TaskPerVideo::select(['LineID', 'Access', 'AccessField', 'Edit', 'EditField', 'Delete'])->where(['EmployeeID' => $EmployeeID, 'TaskID' => $TaskID])->first();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['TaskPer'] = $TaskPer;
        $json['data']['TaskPerAssign'] = $TaskPerAssign;
        $json['data']['TaskPerCate'] = $TaskPerCate;
        $json['data']['TaskPerLink'] = $TaskPerLink;
        $json['data']['TaskPerExecution'] = $TaskPerExecution;
        $json['data']['TaskPerChecklist'] = $TaskPerChecklist;
        $json['data']['TaskPerValuation'] = $TaskPerValuation;
        $json['data']['TaskPerExpense'] = $TaskPerExpense;
        $json['data']['TaskPerExpenseDetail'] = $TaskPerExpenseDetail;
        $json['data']['TaskPerFile'] = $TaskPerFile;
        $json['data']['TaskPerRequest'] = $TaskPerRequest;
        $json['data']['TaskPerVideo'] = $TaskPerVideo;
        return json_encode($json);
    }

    public function tableFieldPer(Request $request){
        $TableName = $request->TableName;
        $data = SysTableField::where(['TableName' => $TableName])->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function taskPerUpdate(Request $request){
        if($request->LineIDTaskPer){
            $TaskPer = TaskPer::find($request->LineIDTaskPer);

            if($TaskPer){
                $TaskPer->TaskID = $request->TaskID;
                $TaskPer->EmployeeID = $request->EmployeeID;
                $TaskPer->Access = $request->AccessGen;
                $TaskPer->Edit = $request->EditGen;
                $TaskPer->Delete = $request->DeleteGen;
                $TaskPer->AccessField = $request->FieldGenAccess;
                $TaskPer->EditField = $request->FieldGenEdit;
                $TaskPer->save();
            }
        }else{
            $TaskPer = new TaskPer([
                'TaskID' => $request->TaskID,
                'EmployeeID' => $request->EmployeeID,
                'Access' => $request->AccessGen,
                'Edit' => $request->EditGen,
                'Delete' => $request->DeleteGen,
                'AccessField' => $request->FieldGenAccess,
                'EditField' => $request->FieldGenEdit,
            ]);
            $TaskPer->save();
        }

        if($request->LineIDTaskPerAssign){
            $TaskPerAssign = TaskPerAssign::find($request->LineIDTaskPerAssign);

            if($TaskPerAssign){
                $TaskPerAssign->TaskID = $request->TaskID;
                $TaskPerAssign->EmployeeID = $request->EmployeeID;
                $TaskPerAssign->Access = $request->AccessAssign;
                $TaskPerAssign->Edit = $request->EditAssign;
                $TaskPerAssign->Delete = $request->DeleteAssign;
                $TaskPerAssign->AccessField = $request->FieldAssignAccess;
                $TaskPerAssign->EditField = $request->FieldAssignEdit;
                $TaskPerAssign->save();
            }
        }else{
            $TaskPerAssign = new TaskPerAssign([
                'TaskID' => $request->TaskID,
                'EmployeeID' => $request->EmployeeID,
                'Access' => $request->AccessAssign,
                'Edit' => $request->EditAssign,
                'Delete' => $request->DeleteAssign,
                'AccessField' => $request->FieldAssignAccess,
                'EditField' => $request->FieldAssignEdit,
            ]);
            $TaskPerAssign->save();
        }

        if($request->LineIDTaskPerCate){
            $TaskPerCate = TaskPerCate::find($request->LineIDTaskPerCate);

            if($TaskPerCate){
                $TaskPerCate->TaskID = $request->TaskID;
                $TaskPerCate->EmployeeID = $request->EmployeeID;
                $TaskPerCate->Access = $request->AccessCate;
                $TaskPerCate->Edit = $request->EditCate;
                $TaskPerCate->Delete = $request->DeleteCate;
                $TaskPerCate->AccessField = $request->FieldCateAccess;
                $TaskPerCate->EditField = $request->FieldCateEdit;
                $TaskPerCate->save();
            }
        }else{
            $TaskPerCate = new TaskPerCate([
                'TaskID' => $request->TaskID,
                'EmployeeID' => $request->EmployeeID,
                'Access' => $request->AccessCate,
                'Edit' => $request->EditCate,
                'Delete' => $request->DeleteCate,
                'AccessField' => $request->FieldCateAccess,
                'EditField' => $request->FieldCateEdit,
            ]);
            $TaskPerCate->save();
        }

        if($request->LineIDTaskPerLink){

            $TaskPerLink = TaskPerLink::find($request->LineIDTaskPerLink);

            if($TaskPerLink){
                $TaskPerLink->TaskID = $request->TaskID;
                $TaskPerLink->EmployeeID = $request->EmployeeID;
                $TaskPerLink->Access = $request->AccessLink;
                $TaskPerLink->Edit = $request->EditLink;
                $TaskPerLink->Delete = $request->DeleteLink;
                $TaskPerLink->AccessField = $request->FieldLinkAccess;
                $TaskPerLink->EditField = $request->FieldLinkEdit;
                $TaskPerLink->save();
            }
        }else{
            $TaskPerLink = new TaskPerLink([
                'TaskID' => $request->TaskID,
                'EmployeeID' => $request->EmployeeID,
                'Access' => $request->AccessLink,
                'Edit' => $request->EditLink,
                'Delete' => $request->DeleteLink,
                'AccessField' => $request->FieldLinkAccess,
                'EditField' => $request->FieldLinkEdit,
            ]);
            $TaskPerLink->save();
        }

        if($request->LineIDTaskPerExecution){
            $TaskPerExecution = TaskPerExecution::find($request->LineIDTaskPerExecution);

            if($TaskPerExecution){
                $TaskPerExecution->TaskID = $request->TaskID;
                $TaskPerExecution->EmployeeID = $request->EmployeeID;
                $TaskPerExecution->Access = $request->AccessExecution;
                $TaskPerExecution->Edit = $request->EditExecution;
                $TaskPerExecution->Delete = $request->DeleteExecution;
                $TaskPerExecution->AccessField = $request->FieldExecutionAccess;
                $TaskPerExecution->EditField = $request->FieldExecutionEdit;
                $TaskPerExecution->save();
            }
        }else{
            $TaskPerExecution = new TaskPerExecution([
                'TaskID' => $request->TaskID,
                'EmployeeID' => $request->EmployeeID,
                'Access' => $request->AccessExecution,
                'Edit' => $request->EditExecution,
                'Delete' => $request->DeleteExecution,
                'AccessField' => $request->FieldExecutionAccess,
                'EditField' => $request->FieldExecutionEdit,
            ]);
            $TaskPerExecution->save();
        }

        if($request->LineIDTaskPerChecklist){
            $TaskPerChecklist = TaskPerChecklist::find($request->LineIDTaskPerChecklist);

            if($TaskPerChecklist){
                $TaskPerChecklist->TaskID = $request->TaskID;
                $TaskPerChecklist->EmployeeID = $request->EmployeeID;
                $TaskPerChecklist->Access = $request->AccessChecklist;
                $TaskPerChecklist->Edit = $request->EditChecklist;
                $TaskPerChecklist->Delete = $request->DeleteChecklist;
                $TaskPerChecklist->AccessField = $request->FieldChecklistAccess;
                $TaskPerChecklist->EditField = $request->FieldChecklistEdit;
                $TaskPerChecklist->save();
            }
        }else{
            $TaskPerChecklist = new TaskPerChecklist([
                'TaskID' => $request->TaskID,
                'EmployeeID' => $request->EmployeeID,
                'Access' => $request->AccessChecklist,
                'Edit' => $request->EditChecklist,
                'Delete' => $request->DeleteChecklist,
                'AccessField' => $request->FieldChecklistAccess,
                'EditField' => $request->FieldChecklistEdit,
            ]);
            $TaskPerChecklist->save();
        }

        if($request->LineIDTaskPerValuation){
            $TaskPerValuation = TaskPerValuation::find($request->LineIDTaskPerValuation);

            if($TaskPerValuation){
                $TaskPerValuation->TaskID = $request->TaskID;
                $TaskPerValuation->EmployeeID = $request->EmployeeID;
                $TaskPerValuation->Access = $request->AccessValuation;
                $TaskPerValuation->Edit = $request->EditValuation;
                $TaskPerValuation->Delete = $request->DeleteValuation;
                $TaskPerValuation->AccessField = $request->FieldValuationAccess;
                $TaskPerValuation->EditField = $request->FieldValuationEdit;
                $TaskPerValuation->save();
            }
        }else{
            $TaskPerValuation = new TaskPerValuation([
                'TaskID' => $request->TaskID,
                'EmployeeID' => $request->EmployeeID,
                'Access' => $request->AccessValuation,
                'Edit' => $request->EditValuation,
                'Delete' => $request->DeleteValuation,
                'AccessField' => $request->FieldValuationAccess,
                'EditField' => $request->FieldValuationEdit,
            ]);
            $TaskPerValuation->save();
        }

        if($request->LineIDTaskPerExpense){
            $TaskPerExpense = TaskPerExpense::find($request->LineIDTaskPerExpense);

            if($TaskPerExpense){
                $TaskPerExpense->TaskID = $request->TaskID;
                $TaskPerExpense->EmployeeID = $request->EmployeeID;
                $TaskPerExpense->Access = $request->AccessExpense;
                $TaskPerExpense->Edit = $request->EditExpense;
                $TaskPerExpense->Delete = $request->DeleteExpense;
                $TaskPerExpense->AccessField = $request->FieldExpenseAccess;
                $TaskPerExpense->EditField = $request->FieldExpenseEdit;
                $TaskPerExpense->save();
            }
        }else{
            $TaskPerExpense = new TaskPerExpense([
                'TaskID' => $request->TaskID,
                'EmployeeID' => $request->EmployeeID,
                'Access' => $request->AccessExpense,
                'Edit' => $request->EditExpense,
                'Delete' => $request->DeleteExpense,
                'AccessField' => $request->FieldExpenseAccess,
                'EditField' => $request->FieldExpenseEdit,
            ]);
            $TaskPerExpense->save();
        }

        if($request->LineIDTaskPerExpenseDetail){
            $TaskPerExpenseDetail = TaskPerExpenseDetail::find($request->LineIDTaskPerExpenseDetail);

            if($TaskPerExpenseDetail){
                $TaskPerExpenseDetail->TaskID = $request->TaskID;
                $TaskPerExpenseDetail->EmployeeID = $request->EmployeeID;
                $TaskPerExpenseDetail->Access = $request->AccessExpenseDetail;
                $TaskPerExpenseDetail->Edit = $request->EditExpenseDetail;
                $TaskPerExpenseDetail->Delete = $request->DeleteExpenseDetail;
                $TaskPerExpenseDetail->AccessField = $request->FieldExpenseDetailAccess;
                $TaskPerExpenseDetail->EditField = $request->FieldExpenseDetailEdit;
                $TaskPerExpenseDetail->save();
            }
        }else{
            $TaskPerExpenseDetail = new TaskPerExpenseDetail([
                'TaskID' => $request->TaskID,
                'EmployeeID' => $request->EmployeeID,
                'Access' => $request->AccessExpenseDetail,
                'Edit' => $request->EditExpenseDetail,
                'Delete' => $request->DeleteExpenseDetail,
                'AccessField' => $request->FieldExpenseDetailAccess,
                'EditField' => $request->FieldExpenseDetailEdit,
            ]);
            $TaskPerExpenseDetail->save();
        }

        if($request->LineIDTaskPerFile){
            $TaskPerFile = TaskPerFile::find($request->LineIDTaskPerFile);

            if($TaskPerFile){
                $TaskPerFile->TaskID = $request->TaskID;
                $TaskPerFile->EmployeeID = $request->EmployeeID;
                $TaskPerFile->Access = $request->AccessFile;
                $TaskPerFile->Edit = $request->EditFile;
                $TaskPerFile->Delete = $request->DeleteFile;
                $TaskPerFile->AccessField = $request->FieldFileAccess;
                $TaskPerFile->EditField = $request->FieldFileEdit;
                $TaskPerFile->save();
            }
        }else{
            $TaskPerFile = new TaskPerFile([
                'TaskID' => $request->TaskID,
                'EmployeeID' => $request->EmployeeID,
                'Access' => $request->AccessFile,
                'Edit' => $request->EditFile,
                'Delete' => $request->DeleteFile,
                'AccessField' => $request->FieldFileAccess,
                'EditField' => $request->FieldFileEdit,
            ]);
            $TaskPerFile->save();
        }

        if($request->LineIDTaskPerRequest){
            $TaskPerRequest = TaskPerRequest::find($request->LineIDTaskPerRequest);

            if($TaskPerRequest){
                $TaskPerRequest->TaskID = $request->TaskID;
                $TaskPerRequest->EmployeeID = $request->EmployeeID;
                $TaskPerRequest->Access = $request->AccessRequest;
                $TaskPerRequest->Edit = $request->EditRequest;
                $TaskPerRequest->Delete = $request->DeleteRequest;
                $TaskPerRequest->AccessField = $request->FieldRequestAccess;
                $TaskPerRequest->EditField = $request->FieldRequestEdit;
                $TaskPerRequest->save();
            }
        }else{
            $TaskPerRequest = new TaskPerRequest([
                'TaskID' => $request->TaskID,
                'EmployeeID' => $request->EmployeeID,
                'Access' => $request->AccessRequest,
                'Edit' => $request->EditRequest,
                'Delete' => $request->DeleteRequest,
                'AccessField' => $request->FieldRequestAccess,
                'EditField' => $request->FieldRequestEdit,
            ]);
            $TaskPerRequest->save();
        }

        if($request->LineIDTaskPerVideo){
            $TaskPerVideo = TaskPerVideo::find($request->LineIDTaskPerVideo);

            if($TaskPerVideo){
                $TaskPerVideo->TaskID = $request->TaskID;
                $TaskPerVideo->EmployeeID = $request->EmployeeID;
                $TaskPerVideo->Access = $request->AccessVideo;
                $TaskPerVideo->Edit = $request->EditVideo;
                $TaskPerVideo->Delete = $request->DeleteVideo;
                $TaskPerVideo->AccessField = $request->FieldVideoAccess;
                $TaskPerVideo->EditField = $request->FieldVideoEdit;
                $TaskPerVideo->save();
            }
        }else{
            $TaskPerVideo = new TaskPerVideo([
                'TaskID' => $request->TaskID,
                'EmployeeID' => $request->EmployeeID,
                'Access' => $request->AccessVideo,
                'Edit' => $request->EditVideo,
                'Delete' => $request->DeleteVideo,
                'AccessField' => $request->FieldVideoAccess,
                'EditField' => $request->FieldVideoEdit,
            ]);
            $TaskPerVideo->save();
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $TaskPer->LineID;
        return json_encode($json);
    }

    public function getEmployeeExecutor(Request $request){
        $TaskID = $request->post('TaskID');
        $EmployeeID = $request->post('EmployeeID');
        $TransDate = $request->post('TransDate');
        $Executor = TaskAssign::where(['TaskID' => $TaskID, 'IsExecutor' => 1])->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['Executor'] = $Executor;
        return json_encode($json);
    }

    public function getEvaluation1Job(Request $request){
        $TaskID = $request->post('TaskID');
        $EmployeeID = $request->post('EmployeeID');
        $TransDate = $request->post('TransDate');
        $FrequencyType = $request->post('FrequencyType');
        $AllowWhenNotEvaluation = $request->post('AllowWhenNotEvaluation');
        $TaskEvaluation1Job = null;
        $TaskIndicator1Job = null;
        $ScaleRateItem = null;
        $TransDateArr = explode('/', $TransDate);
        if($TaskID && $EmployeeID && $TransDate){
            $TransDate = IjDate::ConvertClientDateToServerDate($TransDate);
            $TaskEvaluation1Job = DB::select(DB::raw('SELECT P.TransID, P.TransDate, P.TaskID,
                P.EmployeeID, P.EmployeeName, P.LevelInt LevelIntP,
                C.TransID, C.ScaleRateID, C.ScaleRateName, C.IndicatorID, C.IndicatorNo, C.IndicatorName,
                C.FrequencyType, C.IndicatorCalMethod, C.LevelResult LevelResultP, C.LevelInt100  LevelInt100P, C.EstimatedQuantity EstimatedQuantityP,
                S.LevelInt, S.LevelText, S.LevelChar, S.LevelInt100, S.EstimatedQuantity, S.EvaluatorID, S.EvaluatorName, true showChild, true showChildTransDate
                FROM task_evaluation_1job_trans P
                LEFT JOIN task_evaluation_1job_trans_item C
                ON P.TransID = C.TransID
                LEFT JOIN task_evaluation_1job_trans_subitem S
                ON C.TransID = S.TransID AND C.LineID = S.LineID
                WHERE P.TaskID = '.$TaskID.' AND P.EmployeeID = '.$EmployeeID.' AND P.TransDate = "'.$TransDate.'" AND P.FrequencyType = '.$FrequencyType.'
                ORDER BY P.EmployeeID, P.TransDate, C.IndicatorID'));
        }
        if($TaskID && $EmployeeID){
            $TaskIndicator1Job = DB::select(DB::raw('SELECT T2.IndicatorID, T2.IndicatorName, T2.ScaleRateID, T2.ScaleRateName,
                T1.LevelInt100, T1.LevelResult, T2.IndicatorCalMethod FROM task_indicator_table_employee_map T1
                LEFT JOIN task_indicator_table_map T2
                ON T1.MapID = T2.MapID
                WHERE T1.TaskID = '.$TaskID.' AND T1.EmployeeID = '.$EmployeeID));
            $ScaleRateItem = ScaleRateItem::orderBy('LevelInt', 'DESC')->get();
        }


        if(!$EmployeeID){
            $EmployeeArr = IndicatorTableEmployeeMap::select(['EmployeeID', 'EmployeeName'])->where(['TaskID' => $TaskID])->distinct('EmployeeID')->get();
            $json['data']['EmployeeArr'] = $EmployeeArr;
        }else{
            if(!$TransDate){
                $TransDateArr = [];
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['ScaleRateItem'] = $ScaleRateItem;
        $json['data']['TaskEvaluation1Job'] = $TaskEvaluation1Job;
        $json['data']['TaskIndicator1Job'] = $TaskIndicator1Job;
        return json_encode($json);
    }

    public function getPlanEstimatedQuantity(Request $request){
        $TaskID = $request->post('TaskID');
        $FromDate = $request->post('FromDate');
        $ToDate = $request->post('ToDate');
        $EmployeeID = $request->post('EmployeeID');
        $FromDate = IjDate::ConvertClientDateToServerDate($FromDate);
        $Value = $ToDate;
        $ToDate = IjDate::ConvertClientDateToServerDate($ToDate);
        $data = DB::select(DB::raw('SELECT SUM(EstimatedQuantity) Quantity FROM task_plan_trans_subitem
            WHERE TaskID = "'.$TaskID.'" AND PeriodValue >= "'.$FromDate.'" AND PeriodValue <= "'.$ToDate.'"
            AND EmployeeID = "'.$EmployeeID.'"
        '));
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function calFromChildPeriod($TaskID, $EmployeeID, $FrequencyType, $Fromdate, $Todate){
        if($FrequencyType > 6){
            return null;
        }
        $TaskEvaluation1Job = DB::select(DB::raw('SELECT C.IndicatorID, C.IndicatorName, P.EmployeeID, P.EmployeeName,
                if(C.IndicatorCalMethod = 1, AVG(S.LevelInt100), if(C.IndicatorCalMethod = 2, AVG(S.LevelInt100), "null")) LevelInt100Temp,
                if(C.IndicatorCalMethod = 1, AVG(S.EstimatedQuantity), if(C.IndicatorCalMethod = 2, AVG(S.EstimatedQuantity), "null")) EstimatedQuantityTemp,
                S.EvaluatorID, S.EvaluatorName, true showChild, true showChildTransDate, C.ScaleRateID
                FROM task_evaluation_1job_trans P
                LEFT JOIN task_evaluation_1job_trans_item C
                ON P.TransID = C.TransID
                LEFT JOIN task_evaluation_1job_trans_subitem S
                ON C.LineID = S.LineID
                WHERE P.TaskID = '.$TaskID.' AND P.EmployeeID = '.$EmployeeID.'
                AND P.TransDate >= "'.$Fromdate.'"  AND P.TransDate <= "'.$Todate.'"
                AND P.FrequencyType = '.$FrequencyType.' AND S.EvaluatorID = '.Auth::user()->EmployeeID.'
                GROUP BY C.IndicatorID, P.EmployeeID, S.EvaluatorID
                ORDER BY P.EmployeeID, P.TransDate, C.IndicatorID'));
        $TaskEvaluation1JobTemp = DB::select(DB::raw('
                SELECT  C.IndicatorID, P.EmployeeID, S.LevelInt100, S.EstimatedQuantity, S.EvaluatorID, P.TransDate
                FROM task_evaluation_1job_trans P
                LEFT JOIN task_evaluation_1job_trans_item C
                ON P.TransID = C.TransID
                LEFT JOIN task_evaluation_1job_trans_subitem s
                ON C.LineID = s.LineID
                WHERE P.TaskID = '.$TaskID.' AND P.EmployeeID = '.$EmployeeID.'
                AND P.TransDate >= "'.$Fromdate.'"  AND P.TransDate <= "'.$Todate.'"
                AND P.FrequencyType = '.$FrequencyType.' AND C.IndicatorCalMethod = 3
                AND S.EvaluatorID = '.Auth::user()->EmployeeID.'
                GROUP BY C.IndicatorID, P.EmployeeID, S.EvaluatorID'));
        $TaskEvaluation1JobTempArr = [];
        foreach ($TaskEvaluation1JobTemp as $key=>$value){
            if(isset($TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID])){
                if($TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['TransDate'] < $value->TransDate){
                    $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['TransDate'] = $value->TransDate;
                    $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['LevelInt100'] = $value->LevelInt100;
                    $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['EstimatedQuantity'] = $value->EstimatedQuantity;
                }
            }else{
                $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['TransDate'] = $value->TransDate;
                $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['LevelInt100'] = $value->LevelInt100;
                $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['EstimatedQuantity'] = $value->EstimatedQuantity;
            }
        }

        foreach ($TaskEvaluation1Job as $key=>$value){
            if($value->LevelInt100Temp == "null"){
                $TaskEvaluation1Job[$key]->LevelInt100 = $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['LevelInt100'];
                $TaskEvaluation1Job[$key]->EstimatedQuantity = $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['EstimatedQuantity'];
            }else{
                $TaskEvaluation1Job[$key]->LevelInt100 = $TaskEvaluation1Job[$key]->LevelInt100Temp;
                $TaskEvaluation1Job[$key]->EstimatedQuantity = $TaskEvaluation1Job[$key]->EstimatedQuantityTemp;
            }
        }
        if($TaskEvaluation1Job){
            return $TaskEvaluation1Job;
        }else{
            if($FrequencyType == 4){
                $FrequencyType = 6;
            }else{
                $FrequencyType++;
            }
            return $this->calFromChildPeriod($TaskID, $EmployeeID, $FrequencyType, $Fromdate, $Todate);
        }
    }
    public function fetchEvaluation1Job(Request $request){
        $TaskID = $request->post('TaskID');
        $EmployeeID = $request->post('EmployeeID');
        $TransDate = $request->post('TransDate');
        $FrequencyType = $request->post('FrequencyType');
        $where = '';
        $TransDateArr = explode('/', $TransDate);
        $TransDate = IjDate::ConvertClientDateToServerDate($TransDate);
        switch ($FrequencyType){
            case 1:
                if($request->post('FrequencyYear')){
                    $where .= ' AND P.TransDate >= "'.$request->post('FrequencyYear').'-01-01" AND P.TransDate <= "'.$request->post('FrequencyYear').'-12-31"';
                }
                break;
            case 2:
                if($request->post('Frequency6Month') == 1){
                    $where .= ' AND P.TransDate >= "'.$request->post('FrequencyYear').'-01-01" AND P.TransDate < "'.$request->post('FrequencyYear').'-07-01"';
                }else{
                    $where .= ' AND P.TransDate >= "'.$request->post('FrequencyYear').'-07-01" AND P.TransDate <= "'.$request->post('FrequencyYear').'-12-31"';
                }
                break;
            case 3:
                if($request->post('FrequencyQuarter') == 1){
                    $where .= ' AND P.TransDate >= "'.$request->post('FrequencyYear').'-01-01" AND P.TransDate < "'.$request->post('FrequencyYear').'-04-01"';
                }elseif ($request->post('FrequencyQuarter') == 2){
                    $where .= ' AND P.TransDate >= "'.$request->post('FrequencyYear').'-04-01" AND P.TransDate < "'.$request->post('FrequencyYear').'-07-01"';
                }elseif ($request->post('FrequencyQuarter') == 3){
                    $where .= ' AND P.TransDate >= "'.$request->post('FrequencyYear').'-07-01" AND P.TransDate < "'.$request->post('FrequencyYear').'-10-01"';
                }else {
                    $where .= ' AND P.TransDate >= "'.$request->post('FrequencyYear').'-10-01" AND P.TransDate <= "'.$request->post('FrequencyYear').'-12-31"';
                }
                break;
            case 4:
                if($request->post('FrequencyMonth')){
                    $where .= ' ';
                }
                if($request->post('FrequencyMonth') == 12){
                    $where .= ' AND P.TransDate >= "'.$request->post('FrequencyYear').'-12-01" AND P.TransDate <= "'.$request->post('FrequencyYear').'-12-31"';
                }else {
                    $FrequencyMonth = $request->post('FrequencyMonth');
                    $FrequencyMonthTemp = $FrequencyMonth + 1;
                    if($FrequencyMonth < 10){
                        $FrequencyMonth = '0'.$FrequencyMonth;
                    }
                    if($FrequencyMonthTemp < 10){
                        $FrequencyMonthTemp = '0'.$FrequencyMonthTemp;
                    }
                    $where .= ' AND P.TransDate >= "'.$request->post('FrequencyYear').'-'.$FrequencyMonth.'-01" AND P.TransDate < "'.$request->post('FrequencyYear').'-'.$FrequencyMonthTemp.'-01"';
                }
                break;
            case 5:
                    $where .= ' AND P.TransDate = "'.$TransDate.'"';
                break;
            case 6:
                if($request->post('FrequencyFromDate') && $request->post('FrequencyFromDate') != '__/__/____'){
                    $where .= ' AND P.TransDate >= "'.IjDate::StringToDate($request->post('FrequencyFromDate')).'"';
                }
                if($request->post('FrequencyToDate') && $request->post('FrequencyToDate') != '__/__/____'){
                    $where .= ' AND P.TransDate <= "'.IjDate::StringToDate($request->post('FrequencyToDate')).'"';
                }
                break;
            case 7:
                break;
            default:
                break;
        }
        $FrequencyTypeParent = 0;
        $FrequencyTypeParent = 0;
        $AllowWhenNotEvaluation = 1;
        if($AllowWhenNotEvaluation == 1){
            $Todate = $TransDate;
            $Fromdate = $TransDate;
            switch ($FrequencyType){
                case 1:
                    $Fromdate = $TransDateArr[2].'-'.'01-01';
                    break;
                case 2:
                    if($TransDateArr[1] == 6){
                        $Fromdate = $TransDateArr[2].'-'.'01-01';
                    }else{
                        $Fromdate = $TransDateArr[2].'-'.'07-01';
                    }
                    break;
                case 3:
                    switch ($TransDateArr[1]){
                        case 3:
                            $Fromdate = $TransDateArr[2].'-'.'01-01';
                            break;
                        case 6:
                            $Fromdate = $TransDateArr[2].'-'.'04-01';
                            break;
                        case 9:
                            $Fromdate = $TransDateArr[2].'-'.'07-01';
                            break;
                        case 12:
                            $Fromdate = $TransDateArr[2].'-'.'10-01';
                            break;
                        default:
                            break;
                    }
                    break;
                case 4:
                    $Fromdate = $TransDateArr[2].'-'.$TransDateArr[1].'-01';
                    break;
                case 5:
                    if($TransDateArr[0] < '06' && $TransDateArr[1] == '01'){
                        $Fromdate = $TransDateArr[2].'-01-01';
                    }else{
                        $date=date_create($TransDateArr[2]."-".$TransDateArr[1]."-".$TransDateArr[0]);
                        date_add($date,date_interval_create_from_date_string("-6 days"));
                        $Fromdate = date_format($date,"Y-m-d");
                    }
                    break;
                default:
                    break;
            }

            $TaskEvaluation1Job = $this->calFromChildPeriod($TaskID, $EmployeeID, $FrequencyType + 1, $Fromdate, $Todate);
            if(!$TaskEvaluation1Job){
                $TotalEstimatedQuantityExcute = DB::select(DB::raw('
                    SELECT sum(ActualQuantity) ActualQuantityP FROM task_execution_trans WHERE TransDate >= "'.$Fromdate.' 00:00:00" AND TransDate <= "'.$Todate.' 23:59:59"
                    AND EmployeeID = "'.$EmployeeID.'" AND TaskID = "'.$TaskID.'"
                '));

                $TotalPlanEstimatedQuantity = DB::select(DB::raw('SELECT SUM(EstimatedQuantity) Quantity FROM task_plan_trans_subitem
                    WHERE TaskID = "'.$TaskID.'" AND PeriodValue >= "'.$Fromdate.'" AND PeriodValue <= "'.$Todate.'"
                    AND EmployeeID = "'.$EmployeeID.'"
                '));
                if($TotalEstimatedQuantityExcute[0]->ActualQuantityP){
                    $json['TotalEstimatedQuantity'] = $TotalEstimatedQuantityExcute[0]->ActualQuantityP;
                }else{
                    $json['TotalEstimatedQuantity'] = 0;
                }

                if($TotalPlanEstimatedQuantity[0]->Quantity){
                    $json['TotalPlanEstimatedQuantity'] = $TotalPlanEstimatedQuantity[0]->Quantity;
                }else{
                    $json['TotalPlanEstimatedQuantity'] = 0;
                }
            }
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Bạn chưa đánh giá theo!';
            return json_encode($json);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['TaskEvaluation1Job'] = $TaskEvaluation1Job;
        return json_encode($json);
    }

    public function fetchEvaluation1JobContent(Request $request)
    {
        $TaskID = $request->post('TaskID');
        $TransDate = $request->post('TransDate');
        $FrequencyType = $request->post('FrequencyType');
        $where = '';
        $TransDateArr = explode('/', $TransDate);
        $TransDate = IjDate::ConvertClientDateToServerDate($TransDate);
        switch ($FrequencyType) {
            case 1:
                if ($request->post('FrequencyYear')) {
                    $where .= ' AND P.TransDate >= "' . $request->post('FrequencyYear') . '-01-01" AND P.TransDate <= "' . $request->post('FrequencyYear') . '-12-31"';
                }
                break;
            case 2:
                if ($request->post('Frequency6Month') == 1) {
                    $where .= ' AND P.TransDate >= "' . $request->post('FrequencyYear') . '-01-01" AND P.TransDate < "' . $request->post('FrequencyYear') . '-07-01"';
                } else {
                    $where .= ' AND P.TransDate >= "' . $request->post('FrequencyYear') . '-07-01" AND P.TransDate <= "' . $request->post('FrequencyYear') . '-12-31"';
                }
                break;
            case 3:
                if ($request->post('FrequencyQuarter') == 1) {
                    $where .= ' AND P.TransDate >= "' . $request->post('FrequencyYear') . '-01-01" AND P.TransDate < "' . $request->post('FrequencyYear') . '-04-01"';
                } elseif ($request->post('FrequencyQuarter') == 2) {
                    $where .= ' AND P.TransDate >= "' . $request->post('FrequencyYear') . '-04-01" AND P.TransDate < "' . $request->post('FrequencyYear') . '-07-01"';
                } elseif ($request->post('FrequencyQuarter') == 3) {
                    $where .= ' AND P.TransDate >= "' . $request->post('FrequencyYear') . '-07-01" AND P.TransDate < "' . $request->post('FrequencyYear') . '-10-01"';
                } else {
                    $where .= ' AND P.TransDate >= "' . $request->post('FrequencyYear') . '-10-01" AND P.TransDate <= "' . $request->post('FrequencyYear') . '-12-31"';
                }
                break;
            case 4:
                if ($request->post('FrequencyMonth')) {
                    $where .= ' ';
                }
                if ($request->post('FrequencyMonth') == 12) {
                    $where .= ' AND P.TransDate >= "' . $request->post('FrequencyYear') . '-12-01" AND P.TransDate <= "' . $request->post('FrequencyYear') . '-12-31"';
                } else {
                    $FrequencyMonth = $request->post('FrequencyMonth');
                    $FrequencyMonthTemp = $FrequencyMonth + 1;
                    if ($FrequencyMonth < 10) {
                        $FrequencyMonth = '0' . $FrequencyMonth;
                    }
                    if ($FrequencyMonthTemp < 10) {
                        $FrequencyMonthTemp = '0' . $FrequencyMonthTemp;
                    }
                    $where .= ' AND P.TransDate >= "' . $request->post('FrequencyYear') . '-' . $FrequencyMonth . '-01" AND P.TransDate < "' . $request->post('FrequencyYear') . '-' . $FrequencyMonthTemp . '-01"';
                }
                break;
            case 5:
                $where .= ' AND P.TransDate = "'.$TransDate.'"';
                break;
            case 6:
                if ($request->post('FrequencyFromDate') && $request->post('FrequencyFromDate') != '__/__/____') {
                    $where .= ' AND P.TransDate >= "' . IjDate::StringToDate($request->post('FrequencyFromDate')) . '"';
                }
                if ($request->post('FrequencyToDate') && $request->post('FrequencyToDate') != '__/__/____') {
                    $where .= ' AND P.TransDate <= "' . IjDate::StringToDate($request->post('FrequencyToDate')) . '"';
                }
                break;
            case 7:
                break;
            default:
                break;
        }
        $TaskEvaluation1Job = DB::select(DB::raw('SELECT P.TransID, P.TransDate, P.TaskID,
                P.EmployeeID, P.EmployeeName, P.LevelInt LevelIntP,
                C.TransID, C.ScaleRateID, C.ScaleRateName, C.IndicatorID, C.IndicatorNo, C.IndicatorName,
                C.FrequencyType, C.IndicatorCalMethod, C.LevelResult LevelResultP, C.LevelInt100  LevelInt100P, C.EstimatedQuantity EstimatedQuantityP,
                S.LevelInt, S.LevelText, S.LevelChar, S.LevelInt100, S.EstimatedQuantity, S.EvaluatorID, S.EvaluatorName, true showChild, true showChildTransDate
                FROM task_evaluation_1job_trans P
                LEFT JOIN task_evaluation_1job_trans_item C
                ON P.TransID = C.TransID
                LEFT JOIN task_evaluation_1job_trans_subitem S
                ON C.LineID = S.LineID
                WHERE P.TaskID = ' . $TaskID . ' AND P.FrequencyType = ' . $FrequencyType . ' ' . $where . '
                ORDER BY P.EmployeeID, P.TransDate, C.IndicatorID'));
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['TaskEvaluation1Job'] = $TaskEvaluation1Job;
        return json_encode($json);
    }
    //Update Evaluation 1 Job with transdate, Indicator, Task, Employee, Evaluator
    public function updateEvaluation(Request $request, $id){
        $TransDate = IjDate::StringToDate($request->post('TransDate'));
        $EmployeeID = $request->post('EmployeeID');
        $EmployeeName = $request->post('EmployeeName');
        $EvaluatorID = $request->post('EvaluatorID');
        $EvaluatorName = $request->post('EvaluatorName');
        $TaskEvaluation1JobArr = $request->post('TaskEvaluation1JobArr');
        $TaskIndicator1Job = $request->post('TaskIndicator1Job');
        $FrequencyType = $request->post('FrequencyType');
        $arrTaskEvaluation = [];
        $arrTaskEvaluationItem = [];
        $Evaluation1jobTrans = Evaluation1job::where(['TaskID' => $id, 'EmployeeID' => $EmployeeID, 'TransDate' => $TransDate, 'FrequencyType' => $FrequencyType])->first();
        if(!$Evaluation1jobTrans){
            $Evaluation1jobTrans = new Evaluation1job([
                'TransDate' => $TransDate,
                'TaskID' => $id,
                'EmployeeID' => $EmployeeID,
                'EmployeeName' => $EmployeeName,
                'FrequencyType' => $FrequencyType
            ]);
            $Evaluation1jobTrans->save();
        }

        foreach ($TaskIndicator1Job as $item){
            $arrTaskEvaluationItem[] = [
                'TransID' => $Evaluation1jobTrans->TransID,
                'IndicatorID' => $item['IndicatorID'],
                'IndicatorName' => $item['IndicatorName'],
                'LevelInt100' => $item['LevelInt100'],
                'EstimatedQuantity' => isset($item['EstimatedQuantity'])? $item['EstimatedQuantity'] : 0,
                'LevelResult' => $item['LevelResult'],
                'ScaleRateID' => $item['ScaleRateID'],
                'ScaleRateName' => $item['ScaleRateName'],
                'IndicatorCalMethod' => $item['IndicatorCalMethod'],
                'FrequencyType' => $FrequencyType,
            ];
        }

        Evaluation1jobItem::where(['TransID' => $Evaluation1jobTrans->TransID])->delete();
        if(!empty($arrTaskEvaluationItem)){
            DB::table('task_evaluation_1job_trans_item')->insert($arrTaskEvaluationItem);
        }

        $Evaluation1jobItemTemp = Evaluation1jobItem::where(['TransID' => $Evaluation1jobTrans->TransID])->get();
        $ArrEvaluation1jobItemTemp = [];
        foreach ($Evaluation1jobItemTemp as $item){
            $ArrEvaluation1jobItemTemp[$item->IndicatorID] = $item->LineID;
        }
        $aaa = '';
        foreach ($TaskEvaluation1JobArr as $item){
            $arrTaskEvaluation[] = [
                'LineID' => $ArrEvaluation1jobItemTemp[$item['IndicatorID']],
                'TransID' => $Evaluation1jobTrans->TransID,
                'EvaluatorID' => $item['EvaluatorID'],
                'EvaluatorName' => $item['EvaluatorName'],
                'LevelInt' => $item['LevelInt'],
                'LevelInt100' => $item['LevelInt100'],
                'EstimatedQuantity' => $item['EstimatedQuantity'],
                'LevelChar' => $item['LevelChar'],
                'LevelText' => $item['LevelText'],
            ];
        }
        Evaluation1jobSubItem::where(['TransID' => $Evaluation1jobTrans->TransID])->delete();
        if(!empty($arrTaskEvaluation)){
            DB::table('task_evaluation_1job_trans_subitem')->insert($arrTaskEvaluation);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

}
