<?php
namespace Module\SysAdmin\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\SysAdmin\Models\BinaryData;
use Module\Listing\Models\SysAutoNumber;

class BinaryDataController extends Controller{

    public function list(Request $request){
        $data = BinaryData::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('sys_binary_data', ['select' => ['BinaryDataNo', 'BinaryDataName'], 'conditions' => []]), 'kieudulieunhiphan.xlsx');
    }
    public function create(){
        if(old('BinaryDataNo')){
            $auto = old('BinaryDataNo');
        }else{
            $auto = AutoNo::genNewNumberNo('sys_binary_data');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'BinaryDataNo' => 'required|unique:sys_binary_data'
        ],
        [
            'BinaryDataNo.required' => 'Mã kiểu dữ liệu không được để trống',
            'BinaryDataNo.unique' => 'Mã kiểu dữ liệu đã tồn tại',
        ]);

        $autoModel = SysAutoNumber::where('NumberKey', 'sys_binary_data')->first();
        //AutoNo::storeValidate($validator, $request, 'sys_binary_data', $autoModel, 'Kiểu nhị phân');
        AutoNo::storeValidateIDNo($validator, $request, 'sys_binary_data', 'BinaryDataID', 'BinaryDataNo', $autoModel, 'Kiểu nhị phân');

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new BinaryData([
            'BinaryDataNo' => $request->BinaryDataNo,
            'BinaryDataName' => $request->BinaryDataName,
            'BinaryDataKey' => $request->BinaryDataKey,
            'BinaryData1' => $request->BinaryData1,
            'BinaryData0' => $request->BinaryData0,
            'Prefix' => $autoModel->Prefix,
            'Suffix' => $autoModel->Suffix,
            'Inactive' => $request->Inactive,
        ]);
        $data->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->BinaryDataID;
        return json_encode($json);
    }

    public function view($id){
        $data = BinaryData::find($id);
        $data->BinaryDataNo = $data->Prefix.$data->BinaryDataNo.$data->Suffix;

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        return json_encode($json);
    }

    public function edit($id){
        $data = BinaryData::find($id);
        if(old('BinaryDataNo')){
            $data->BinaryDataNo = old('BinaryDataNo');
        }else{
            $data->BinaryDataNo = $data->Prefix.$data->BinaryDataNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $validate = [];
        $data = BinaryData::find($id);
        if($data->Prefix.$data->BinaryDataNo.$data->Suffix != $request->BinaryDataNo){
            $validator = Validator::make($request->all(), [
                'BinaryDataNo' => 'required|unique:sys_binary_data'
            ],
                [
                    'BinaryDataNo.required' => 'Mã kiểu dữ liệu không được để trống',
                    'BinaryDataNo.unique' => 'Mã kiểu dữ liệu đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'sys_binary_data')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data,'sys_binary_data', $autoModel, 'Nhị phân');

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->BinaryDataNo = $request->BinaryDataNo;
        }
        $request->validate($validate);
        $data->BinaryDataName = $request->BinaryDataName;
        $data->BinaryDataKey = $request->BinaryDataKey;
        $data->BinaryData1 = $request->BinaryData1;
        $data->BinaryData0 = $request->BinaryData0;
        $data->Inactive = $request->Inactive;
        $data->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }

    public function delete($id){
        $data = BinaryData::find($id);
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
        BinaryData::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
