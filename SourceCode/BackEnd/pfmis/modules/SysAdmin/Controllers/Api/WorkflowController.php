<?php
namespace Module\SysAdmin\Controllers;
use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Module\Task\Models\TaskDataflow;
use function foo\func;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysFeatureStatus;
use Module\SysAdmin\Models\SysFeatureStatusItem;
use Module\SysAdmin\Models\SysFeature;
use Module\SysAdmin\Models\Workflow;
use Module\SysAdmin\Models\WorkflowConstraint;
use Module\SysAdmin\Models\WorkflowItem;

class WorkflowController extends Controller
{
    public function list(Request $request){
        $data = Workflow::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('company', ['select' => ['CompanyNo', 'CompanyName'], 'conditions' => []]), 'donvi.xlsx');
    }

    public function create(){
        $data = Workflow::all();
        $feature = SysFeature::all();
        $auto = AutoNo::genNewNumberNo('task_workflow');
        $ConstraintCondition = [
            '1' => 'Gộp',
            '2' => 'Hoặc',
            '3' => 'Nếu'
        ];
        $ConstraintType = [
            '1' => 'Kết thúc – Bắt đầu',
            '2' => 'Kết thúc – Kết thúc',
            '3' => 'Bắt đầu – Bắt đầu',
            '4' => 'Bắt đầu – Kết thúc',
        ];
        $ProcessType = [
            '1' => 'Bên trong',
            '2' => 'Bên ngoài',
            '3' => 'Điều kiện',
            '4' => 'Bắt đầu',
            '5' => 'Kết thúc',
        ];
        $employee = Employee::select('employee.UserID', 'employee.EmployeeID', 'employee.EmployeeName', 'sys_user.Avata')
            ->join('sys_user', 'sys_user.UserID', '=', 'employee.UserID')
            ->get();
        $SysFeatureStatus = SysFeatureStatus::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['feature'] = $feature;
        $json['data']['auto'] = $auto;
        $json['data']['ConstraintType'] = $ConstraintType;
        $json['data']['ConstraintCondition'] = $ConstraintCondition;
        $json['data']['ProcessType'] = $ProcessType;
        $json['data']['SysFeatureStatus'] = $SysFeatureStatus;
        $json['data']['Employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){

        $master = $request->master;
        $detail = $request->detail;
        $constraint = $request->constraint;
        $validator = Validator::make($master,
            [
                'WFNo' => 'required|unique:task_workflow'
            ],
            [
                'required' => 'Mã quy trình công việc không được để trống',
                'unique' => 'Mã quy trình công việc đã tồn tại',
            ]
        );
        $autoModel = SysAutoNumber::where('NumberKey', 'task_workflow')->first();
        $master = (object) $master;
//        AutoNo::storeValidateIDNo($validator, $master, 'task_workflow', 'WFID', 'WFNo', $autoModel, 'quy trình công việc');
//
//        if($validator->fails()){
//            $json['status'] = 2;
//            $json['msg'] = 'Lỗi!';
//            $json['data'] = $validator->errors();
//            return $json;
//        }
        $data = new Workflow([
            'WFNo' => $master->WFNo,
            'WFName' => $master->WFName,
            'JsonFlowchart' => $master->JsonFlowchart,
            'Inactive' => $master->Inactive
        ]);
        $data->save();
        $arrInsert = [];
        $id = $data->WFID;
        foreach ($detail as $item){
            $item = (object) $item;
//            if(!empty($item->WFItemName)){
                $arrInsert[] = [
                    'WFID' => $id,
                    'WFItemName' => $item->WFItemName,
                    'FeatureID' => $item->FeatureID,
                    'FeatureKey' => (!empty($item->FeatureKey)) ? $item->FeatureKey : null,
                    'FeatureName' => (!empty($item->FeatureName)) ? $item->FeatureName : null,
                    'ProcessType' => $item->ProcessType,
                    'IsTaskFeature' => $item->IsTaskFeature,
                    'FeatureStatusID' => $item->FeatureStatusID,
                    'NOrder' => $item->NOrder,
                    'LineIDTemp' => $item->LineIDTemp,
                ];
//            }
        }


        if(!empty($arrInsert)){
            DB::table('task_workflow_item')->insert($arrInsert);
            $WorkflowItem = WorkflowItem::where(['WFID' => $id])->get();
            $arrTemp = [];
            foreach ($WorkflowItem as $item){
                $arrTemp[$item->LineIDTemp] = $item->WFItemID;
            }
            $arrInsert = [];
            foreach ($constraint as $item){
                $item = (object) $item;
                    $arrInsert[] = [
                        'WFID' => $id,
                        'WFItemID' => isset($arrTemp[$item->WFItemID])?$arrTemp[$item->WFItemID]:"",
                        'WFPreItemID' => isset($arrTemp[$item->WFPreItemID])?$arrTemp[$item->WFPreItemID]:"",
                        'WFPreItemValue' => $item->WFPreItemValue,
                        'ConstraintType' => $item->ConstraintType,
                        'ConstraintLabel' => $item->ConstraintLabel,
                        'ConstraintCondition' => $item->ConstraintCondition,
                        'SourceAnchors' => $item->SourceAnchors,
                        'TargetAnchors' => $item->TargetAnchors,
                    ];
            }

            if(!empty($arrInsert)){
                DB::table('task_workflow_constraint')->insert($arrInsert);
            }

            $jsonFlowchart = $data->JsonFlowchart;
            $jsonFlowchart = json_decode($jsonFlowchart, true);

            foreach ($jsonFlowchart['node'] as $key => $node) {
                $jsonFlowchart['node'][$key]['WFItemID'] = $arrTemp[$node['WFItemID']];
            }

            foreach ($jsonFlowchart['constraint'] as $key => $constraint) {
                $jsonFlowchart['constraint'][$key]['WFItemID'] = $arrTemp[$constraint['WFItemID']];
                $jsonFlowchart['constraint'][$key]['WFPreItemID'] = $arrTemp[$constraint['WFPreItemID']];
            }

            $jsonFlowchart = json_encode($jsonFlowchart);
            $data->JsonFlowchart = $jsonFlowchart;
            $data->save();

            // insert table task_workflow_employee
            $arrInsertEmployee = [];
            foreach ($detail as $item) {
                $item = (object)$item;
                if (!empty($item->Employee)) {
                    foreach ($item->Employee as $value) {
                        $arrInsertEmployee[] = [
                            'WFID' => $id,
                            'WFItemID' => isset($arrTemp[$item->LineIDTemp]) ? $arrTemp[$item->LineIDTemp] : '',
                            'EmployeeID' => $value,
                            'IsMainResponsiblePerson' => (isset($item->ResponseEmployee) && $value == $item->ResponseEmployee) ? 1 : 0
                        ];
                    }
                }
            }

            if (!empty($arrInsertEmployee)) {
                DB::table('task_workflow_employee')->insert($arrInsertEmployee);
            }

        }


        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->WFID;
        return json_encode($json);
    }
    public function edit($id){
        $WorkflowItem = WorkflowItem::where('WFID', $id)->get();
        $data = Workflow::find($id);
        $feature = SysFeature::all();
        $WorkflowConstraint = WorkflowConstraint::where('WFID', $id)->orderBy('WFItemID')->get();
        $ConstraintCondition = [
            '1' => 'Gộp',
            '2' => 'Hoặc',
            '3' => 'Nếu',
            '4' => 'Rẽ nhánh',
        ];
        $ConstraintType = [
            '1' => 'Kết thúc – Bắt đầu',
            '2' => 'Kết thúc – Kết thúc',
            '3' => 'Bắt đầu – Bắt đầu',
            '4' => 'Bắt đầu – Kết thúc',
        ];
        $ProcessType = [
            '1' => 'Bên trong',
            '2' => 'Bên ngoài',
            '3' => 'Điều kiện',
            '4' => 'Bắt đầu',
            '5' => 'Kết thúc',
        ];
        $employee = Employee::select('employee.UserID', 'employee.EmployeeID', 'employee.EmployeeName', 'sys_user.Avata')
            ->join('sys_user', 'sys_user.UserID', '=', 'employee.UserID')
            ->get();
//        $workflowEmployee = DB::table('task_workflow_employee')
//            ->where('WFID', '=', $id)
//            ->get();
        $SysFeatureStatus = SysFeatureStatus::all();
        $data->CompanyNo = $data->Prefix.$data->WFNo.$data->Suffix;
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['feature'] = $feature;
        $json['data']['ProcessType'] = $ProcessType;
        $json['data']['ConstraintType'] = $ConstraintType;
        $json['data']['ConstraintCondition'] = $ConstraintCondition;
        $json['data']['WorkflowItem'] = $WorkflowItem;
        $json['data']['WorkflowConstraint'] = $WorkflowConstraint;
        $json['data']['SysFeatureStatus'] = $SysFeatureStatus;
        $json['data']['Employee'] = $employee;
//        $json['data']['WorkflowEmployee'] = $workflowEmployee;

        return json_encode($json);
    }
    public function update(Request $request, $id){
        $master = $request->master;
        $detail = $request->detail;
        $constraint = $request->constraint;
        $master = (object) $master;
        $Workflow = Workflow::find($id);

        if($Workflow->Prefix.$Workflow->WFNo.$Workflow->Suffix != $master->WFNo){
            $validator = Validator::make($master->all(), [
                'WFNo' => 'required|unique:task_workflow'
            ],
                [
                    'WFNo.required' => 'Mã quy trình công việc không được để trống',
                    'WFNo.unique' => 'Mã quy trình công việc đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'task_workflow')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $master, $Workflow,'task_workflow', $autoModel, 'quy trình công việc');

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $Workflow->WFNo = $master->WFNo;
        }
        $Workflow->WFName = $master->WFName;
        $Workflow->JsonFlowchart = $master->JsonFlowchart;
        $Workflow->Inactive = $master->Inactive;
        $Workflow->save();

        // check have dataflow
        $checkData = TaskDataflow::where('WFID', '=', $id)->exists();
        if ($checkData) {
            $json['status'] = 1;
            $json['msg'] = 'Quy trình đã có dữ liệu. Chỉ được sửa tên quy trình và giao diện sơ đồ!';
            $json['data'] = [];
            return $json;
        }

        $arrInsert = [];
        if ($detail) {
            foreach ($detail as $item){
                $item = (object) $item;
                $arrInsert[] = [
                    'WFID' => $id,
                    'WFItemName' => $item->WFItemName,
                    'FeatureID' => $item->FeatureID,
                    'FeatureKey' => (!empty($item->FeatureKey)) ? $item->FeatureKey : null,
                    'FeatureName' => (!empty($item->FeatureName)) ? $item->FeatureName : null,
                    'ProcessType' => $item->ProcessType,
                    'IsTaskFeature' => $item->IsTaskFeature,
                    'FeatureStatusID' => $item->FeatureStatusID,
                    'NOrder' => $item->NOrder,
                    'LineIDTemp' => $item->LineIDTemp,
                ];
            }

            if(!empty($arrInsert)){
                DB::table('task_workflow_item')->where(['WFID' => $id])->delete();
                DB::table('task_workflow_item')->insert($arrInsert);
                $WorkflowItem = WorkflowItem::where(['WFID' => $id])->get();
                $arrTemp = [];
                foreach ($WorkflowItem as $item){
                    $arrTemp[$item->LineIDTemp] = $item->WFItemID;
                }
                $arrInsert = [];
                foreach ($constraint as $item){
                    $item = (object) $item;
                    $arrInsert[] = [
                        'WFID' => $id,
                        'WFItemID' => isset($arrTemp[$item->WFItemID]) ? $arrTemp[$item->WFItemID] : "",
                        'WFPreItemID' => isset($arrTemp[$item->WFPreItemID]) ? $arrTemp[$item->WFPreItemID] : "",
                        'WFPreItemValue' => $item->WFPreItemValue,
                        'ConstraintType' => $item->ConstraintType,
                        'ConstraintLabel' => $item->ConstraintLabel,
                        'ConstraintCondition' => $item->ConstraintCondition,
                        'SourceAnchors' => $item->SourceAnchors,
                        'TargetAnchors' => $item->TargetAnchors,
                    ];
                }

                if(!empty($arrInsert)){
                    DB::table('task_workflow_constraint')->where(['WFID' => $id])->delete();
                    DB::table('task_workflow_constraint')->insert($arrInsert);
                }

                $jsonFlowchart = $Workflow->JsonFlowchart;
                $jsonFlowchart = json_decode($jsonFlowchart, true);

                foreach ($jsonFlowchart['node'] as $key => $node) {
                    $jsonFlowchart['node'][$key]['WFItemID'] = $arrTemp[$node['WFItemID']];
                }
                foreach ($jsonFlowchart['constraint'] as $key => $constraint) {
                    $jsonFlowchart['constraint'][$key]['WFItemID'] = $arrTemp[$constraint['WFItemID']];
                    $jsonFlowchart['constraint'][$key]['WFPreItemID'] = $arrTemp[$constraint['WFPreItemID']];
                }

                $jsonFlowchart = json_encode($jsonFlowchart);
                $Workflow->JsonFlowchart = $jsonFlowchart;
                $Workflow->save();

                // insert table task_workflow_employee
                $arrInsertEmployee = [];
                foreach ($detail as $item) {
                    $item = (object)$item;
                    if (!empty($item->Employee)) {
                        foreach ($item->Employee as $value) {
                            $arrInsertEmployee[] = [
                                'WFID' => $id,
                                'WFItemID' => isset($arrTemp[$item->LineIDTemp]) ? $arrTemp[$item->LineIDTemp] : '',
                                'EmployeeID' => $value,
                                'IsMainResponsiblePerson' => (isset($item->ResponseEmployee) && $value == $item->ResponseEmployee) ? 1 : 0
                            ];
                        }
                    }
                }

                if (!empty($arrInsertEmployee)) {
                    DB::table('task_workflow_employee')->where(['WFID' => $id])->delete();
                    DB::table('task_workflow_employee')->insert($arrInsertEmployee);
                }
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }
    public function view($id){
        $WorkflowItem = WorkflowItem::where('WFID', $id)->get();
        $WorkflowConstraint = WorkflowConstraint::where('WFID', $id)->get();
        $feature = SysFeature::all();
        $data = Workflow::find($id);
        $ConstraintCondition = [
            '1' => 'Gộp',
            '2' => 'Hoặc',
            '3' => 'Nếu',
            '4' => 'Rẽ nhánh',
        ];
        $ConstraintType = [
            '1' => 'Kết thúc – Bắt đầu',
            '2' => 'Kết thúc – Kết thúc',
            '3' => 'Bắt đầu – Bắt đầu',
            '4' => 'Bắt đầu – Kết thúc',
        ];
        $ProcessType = [
            '1' => 'Bên trong',
            '2' => 'Bên ngoài',
            '3' => 'Điều kiện',
            '4' => 'Bắt đầu',
            '5' => 'Kết thúc',
        ];
        $SysFeatureStatus = SysFeatureStatus::all();
        $workflowEmployee = DB::table('task_workflow_employee')
            ->select('task_workflow_employee.*', 'sys_user.Avata', 'employee.EmployeeName')
            ->join('employee', 'task_workflow_employee.EmployeeID', '=', 'employee.EmployeeID')
            ->join('sys_user', 'sys_user.UserID', '=', 'employee.UserID')
            ->where('task_workflow_employee.WFID', '=', $id)
            ->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['feature'] = $feature;
        $json['data']['data'] = $data;
        $json['data']['ConstraintType'] = $ConstraintType;
        $json['data']['ConstraintCondition'] = $ConstraintCondition;
        $json['data']['ProcessType'] = $ProcessType;
        $json['data']['WorkflowItem'] = $WorkflowItem;
        $json['data']['WorkflowConstraint'] = $WorkflowConstraint;
        $json['data']['SysFeatureStatus'] = $SysFeatureStatus;
        $json['data']['WorkflowEmployee'] = $workflowEmployee;

        return json_encode($json);
    }
    public function delete($id){
        WorkflowItem::where('WFID', $id)->delete();
        WorkflowConstraint::where('WFID', $id)->delete();
        $data = Workflow::find($id);
        if($data){
            $data->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = $id;

            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = $id;

            return json_encode($json);
        }
    }
    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        WorkflowItem::whereIn('WFID', $IDs)->delete();
        WorkflowConstraint::whereIn('WFID', $IDs)->delete();
        Workflow::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
