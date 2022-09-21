<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use IjCore\IjFile;
use IjCore\IjString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Department;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;

class DepartmentController extends Controller{

    public function list(Request $request){
        $data = Department::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('department', ['select' => ['DepartmentNo', 'DepartmentName'], 'conditions' => []]), 'phongban.xlsx');
    }

    public function create(){
        $department = Department::all();
        if(old('DepartmentNo')){
            $auto = old('DepartmentNo');
        }else{
            $auto = AutoNo::genNewNumberNo('department');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['department'] = $department;
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'DepartmentNo' => 'required|unique:department',
            'DepartmentName'=> 'required|unique:department',
        ],
            [
                'DepartmentNo.required' => 'Mã phòng ban không được để trống',
                'DepartmentNo.unique' => 'Mã phòng ban đã tồn tại',
                'DepartmentName.required' => 'Tên phòng ban không được để trống',
                'DepartmentName.unique' => 'Tên phòng ban đã tồn tại',
            ]
        );

        $autoModel = SysAutoNumber::where('NumberKey', 'department')->first();
        AutoNo::storeValidate($validator, $request, 'department', $autoModel, 'phòng ban');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $department = new Department([
            'DepartmentNo' => $request->DepartmentNo,
            'DepartmentName' => $request->DepartmentName,
            'ParentID' => $request->ParentID,
            'Level' => empty($request->ParentID)? 1 : $validator->ParentLevel + 1,
            'Note' => $request->Note,
            'Prefix' => "",
            'Suffix' => "",
            'NumberValue'=> $request->NumberValue,
            'Inactive' => $request->Inactive,
        ]);

        $department->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $department->DepartmentID;

        return json_encode($json);
    }

    public function view($id){
        $data = Department::find($id);
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $parent = Department::find($data->ParentID);
                if(isset($parent->DepartmentNo) && isset($parent->DepartmentName)){
                    $arrParent = [
                        "ParentNo" => $parent->DepartmentNo,
                        "ParentName" => $parent->DepartmentName
                    ];
                }
            }

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;
            $json['data']['Parent'] = $arrParent;
            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id){
        $data = Department::find($id);
        if($data){
            if(old('DepartmentNo')){
                $data->DepartmentNo = old('DepartmentNo');
            }
            if($data->ParentID){
                $parent = Department::find($data->ParentID);
                if(isset($parent->DepartmentNo) && isset($parent->DepartmentName)){
                    $arrParent = [
                        "ParentNo" => $parent->DepartmentNo,
                        "ParentName" => $parent->DepartmentName
                    ];
                }
            }
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;
            $json['data']['Parent'] = $arrParent;
            return json_encode($json);
        }
        else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }


    }

    public function update(Request $request, $id){

        $department = Department::find($id);
        if(!$department){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($department->DepartmentNo !== $request->DepartmentNo){
            $validator = Validator::make($request->all(), [
                'DepartmentNo' => 'required',
                'DepartmentName'=> 'required',
            ],
                [
                    'DepartmentNo.required' => 'Mã phòng ban không được để trống',
                    'DepartmentName.required' => 'Tên phòng ban không được để trống',
                ]);


            $autoModel = SysAutoNumber::where('NumberKey', 'department')->first();
            AutoNo::UpdateValidate($validator, $request, $department,'department', $autoModel, 'phòng ban');

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $department->DepartmentNo = $request->DepartmentNo;
            $department->Level = empty($request->ParentID)? 1 : $validator->ParentLevel + 1;

        }
        $department->DepartmentName = $request->DepartmentName;
        $department->ParentID = $request->ParentID;
        $department->Note = $request->Note;
        $department->Inactive = $request->Inactive;
        $department->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;


    }

    public function delete($id){
        $data = Department::find($id);
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
        Department::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = Department::search($request);
//        $Uom = Uom::select('UomID', 'UomName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }

}
