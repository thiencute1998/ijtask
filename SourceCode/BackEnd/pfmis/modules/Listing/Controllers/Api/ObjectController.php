<?php
namespace Module\Listing\Controllers;

use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\ObjectModel;
use Module\Listing\Models\SysAutoNumber;

class ObjectController extends Controller{

    public function list(Request $request){
        $data = ObjectModel::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('object', ['select' => ['ObjectNo', 'ObjectName'], 'conditions' => []]), 'doituongkhac.xlsx');
    }
    public function create(){
        if(old('ObjectNo')){
            $auto = old('ObjectNo');
        }else{
            $auto = AutoNo::genNewNumberNo('object');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'ObjectNo' => 'required|unique:object'
        ],
            [
                'ObjectNo.required' => 'Mã đối tượng khác không được để trống',
                'ObjectNo.unique' => 'Mã đối tượng khác đã tồn tại',
            ]);

        $autoModel = SysAutoNumber::where('NumberKey', 'object')->first();
        AutoNo::storeValidate($validator, $request, 'object', $autoModel, 'đối tượng khác');

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new ObjectModel([
            'ObjectNo' => $request->ObjectNo,
            'ObjectName' => $request->ObjectName,
            'Address' => $request->Address,
            'OfficePhone' => $request->OfficePhone,
            'Fax' => $request->Fax,
            'Email' => $request->Email,
            'Website' => $request->Website,
            'Note' => $request->Note,
            'Prefix' => $autoModel->Prefix,
            'Suffix' => $autoModel->Suffix,
            'Inactive' => $request->Inactive,
        ]);
        $data->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->ObjectID;

        return json_encode($json);
    }

    public function view($id){
        $data = ObjectModel::find($id);
        $data->ObjectNo = $data->Prefix.$data->ObjectNo.$data->Suffix;

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function edit($id){
        $data = ObjectModel::find($id);
        if(old('ObjectNo')){
            $data->ObjectNo = old('ObjectNo');
        }else{
            $data->ObjectNo = $data->Prefix.$data->ObjectNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
//        $validate = [
//            'nOrder' => 'required|integer',
//            'password' => 'required|confirmed',
//            'password_confirmation' => 'required'
//        ];
        $validate = [];
        $data = ObjectModel::find($id);
        if($data->Prefix.$data->ObjectNo.$data->Suffix != $request->ObjectNo){
            $validator = Validator::make($request->all(), [
                'ObjectNo' => 'required|unique:object'
            ],
                [
                    'ObjectNo.required' => 'Mã đối tượng khác không được để trống',
                    'ObjectNo.unique' => 'Mã đối tượng khác đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'object')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data,'object', $autoModel, 'đối tượng khác');

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->ObjectNo = $request->ObjectNo;
        }
        $data->ObjectName = $request->ObjectName;
        $data->Address = $request->Address;
        $data->OfficePhone = $request->OfficePhone;
        $data->Fax = $request->Fax;
        $data->Email = $request->Email;
        $data->Website = $request->Website;
        $data->Note = $request->Note;
        $data->Inactive = $request->Inactive;
        $data->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }

    public function delete($id){
        $data = ObjectModel::find($id);
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
        ObjectModel::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
