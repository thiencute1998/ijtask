<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\CoaType;

class CoaTypeController extends Controller{

    public function list(Request $request){
        $data = CoaType::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;

        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('coa_type', ['select' => ['CoaTypeNo', 'CoaTypeName'], 'conditions' => []]), 'tiente.xlsx');
    }
    public function create(){
       $CoaType = CoaType::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $CoaType;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'CoaTypeNo' => 'required|unique:coa_type'
        ],
            [
                'CoaTypeNo.required' => 'Mã hệ thống tài khoản không được để trống',
                'CoaTypeNo.unique' => 'Mã hệ thống tài khoản đã tồn tại',
            ]);


        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new CoaType([
            'CoaTypeNo' => $request->CoaTypeNo,
            'CoaTypeName' => $request->CoaTypeName,
            'Comment' => $request->Comment,
            'Norder' => $request->Norder,
            'Inactive' => $request->Inactive,
        ]);
        $data->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->CoaTypeID;
        return json_encode($json);
    }

    public function view($id){
        $data = CoaType::find($id);
        $data->CoaTypeNo = $data->Prefix.$data->CoaTypeNo.$data->Suffix;

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        return json_encode($json);
    }

    public function edit($id){
        $data = CoaType::find($id);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
//        $validate = [
//            'nOrder' => 'required|integer',
//            'password' => 'required|confirmed',
//            'password_confirmation' => 'required'
//        ];
        $validate = [];
        $data = CoaType::find($id);
        if($data->Prefix.$data->CoaTypeNo.$data->Suffix != $request->CoaTypeNo){
            $validator = Validator::make($request->all(), [
                'CoaTypeNo' => 'required|unique:expense'
            ],
                [
                    'CoaTypeNo.required' => 'Mã hệ thống tài khoản không được để trống',
                    'CoaTypeNo.unique' => 'Mã hệ thống tài khoản đã tồn tại',
                ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->CoaTypeNo = $request->CoaTypeNo;
        }
        $request->validate($validate);
        $data->CoaTypeName = $request->CoaTypeName;
        $data->Norder = $request->Norder;
        $data->Comment = $request->Comment;
        $data->Inactive = $request->Inactive;
        $data->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }

    public function delete($id){
        $data = CoaType::find($id);
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
        CoaType::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
