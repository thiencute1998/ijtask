<?php
namespace Module\Listing\Controllers;

use App\Exports\ModelExport;
use IjCore\IjAutoNo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Company;
use Module\Listing\Models\Employee;
use Module\Listing\Models\Project;
use Module\Listing\Models\SysAutoNumber;

class ProjectdemoController extends Controller{

    public function index(Request $request){
        $data = Projectdemo::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function export(Request $request){
        return Excel::download(new ModelExport('project', ['select' => ['ProjectNo', 'ProjectName'], 'conditions' => []]), 'duan.xlsx');
    }
    public function addnew(){
        $project = Project::all();
        $taskCompany = Company::all();
        $employeeUser = Employee::where(['UserID' => Auth::user()->UserID])->first();
        $EmployeeID = isset($employeeUser->EmployeeID)? $employeeUser->EmployeeID : '-1';
        if(old('ProjectNo')){
            $auto = old('ProjectNo');
        }else{
            $auto = AutoNo::genNewNumberNo('project');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        $json['data']['project'] = $project;
        $json['data']['company'] = $taskCompany;
        $json['data']['employeeUser'] = $employeeUser;
        return json_encode($json);

    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'ProjectNo' => 'required|unique:project'
        ],
            [
                'ProjectNo.required' => 'Mã dự án không được để trống',
                'ProjectNo.unique' => 'Mã dự án đã tồn tại',
            ]);

        $autoModel = SysAutoNumber::where('NumberKey', 'project')->first();
        AutoNo::storeValidate($validator, $request, 'project', $autoModel, 'dự án');

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $project = new Project([
            'ProjectName' => $request->ProjectName,
            'ProjectNo' => $request->ProjectNo,
            'ParentID' => $request->ParentID,
            'CompanyID' => $request->CompanyID,
            'Level' => empty($request->ParentID)? 1 : $validator->ParentLevel + 1,
            'Prefix' => $autoModel->Prefix,
            'Suffix' => $autoModel->Suffix,
            'Inactive' => $request->Inactive,
        ]);
        $project->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $project->ProjectID;

        return json_encode($json);
    }

    public function view($id){
        $data = Project::find($id);
        $Parent = Project::find($data->ParentID);
        $ParentName = isset($Parent->ProjectName) ? $Parent->ProjectName : '';
        $taskCompany = Company::find($data->CompanyID);
        $CompanyName = isset($taskCompany->CompanyName) ? $taskCompany->CompanyName : '';
        $data->ProjectNo = $data->Prefix.$data->ProjectNo.$data->Suffix;

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['ParentName'] = $ParentName;
        $json['data']['CompanyName'] = $CompanyName;
        return json_encode($json);
    }

    public function edit($id){
        $data = Project::find($id);
        $project = Project::all();
        $taskCompany = Company::all();
        $employeeUser = Employee::where(['UserID' => Auth::user()->UserID])->first();
        $EmployeeID = isset($employeeUser->EmployeeID)? $employeeUser->EmployeeID : '-1';
        if(old('ProjectNo')){
            $data->ProjectNo = old('ProjectNo');
        }else{
            $data->ProjectNo = $data->Prefix.$data->ProjectNo.$data->Suffix;
        }
        $json['msg'] = 'Thành công!';
        $json['status'] = 1;
        $json['data']['data'] = $data;
        $json['data']['employeeUser'] = $employeeUser;
        $json['data']['company'] = $taskCompany;
        $json['data']['project'] = $project;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $project = Project::find($id);
        if($project->Prefix.$project->ProjectNo.$project->Suffix != $request->ProjectNo){
            $validator = Validator::make($request->all(), [
                'ProjectNo' => 'required|unique:project'
            ],
            [
                'ProjectNo.required' => 'Mã dự án không được để trống',
                'ProjectNo.unique' => 'Mã dự án đã tồn tại',
            ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'project')->first();
            AutoNo::UpdateValidate($validator, $request, $project,'project', $autoModel, 'dự án');

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $project->ProjectNo = $request->ProjectNo;
            $project->Level = empty($request->ParentID)? 1 : $validator->ParentLevel + 1;
        }
        $project->ProjectName = $request->ProjectName;
        $project->ParentID = $request->ParentID;
        $project->CompanyID = $request->CompanyID;
        $project->Inactive = $request->Inactive;
        $project->save();

        $project->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }

    public function delete($id){
        $data = Project::find($id);
        if($data){
            $data->delete();
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        Project::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
