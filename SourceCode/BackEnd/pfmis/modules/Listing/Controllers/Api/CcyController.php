<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Ccy;
use Module\Listing\Models\SysAutoNumber;

class CcyController extends Controller{

    public function list(Request $request){
        $data = Ccy::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;

        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('ccy', ['select' => ['CcyNo', 'CcyName'], 'conditions' => []]), 'tiente.xlsx');
    }
    public function create(){
        if(old('CcyNo')){
            $auto = old('CcyNo');
        }else{
            $auto = AutoNo::genNewNumberNo('ccy');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'CcyNo' => 'required|unique:ccy'
        ],
            [
                'CcyNo.required' => 'Mã tiền tệ không được để trống',
                'CcyNo.unique' => 'Mã tiền tệ đã tồn tại',
            ]);

        $autoModel = SysAutoNumber::where('NumberKey', 'ccy')->first();
        AutoNo::storeValidate($validator, $request, 'ccy', $autoModel, 'tiền tệ');

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new Ccy([
            'CcyNo' => $request->CcyNo,
            'CcyName' => $request->CcyName,
            'Inactive' => $request->Inactive,
        ]);
        $data->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->CcyID;
        return json_encode($json);
    }

    public function view($id){
        $data = Ccy::find($id);
        $data->CcyNo = $data->Prefix.$data->CcyNo.$data->Suffix;

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        return json_encode($json);
    }

    public function edit($id){
        $data = Ccy::find($id);
        if(old('CcyNo')){
            $data->CcyNo = old('CcyNo');
        }else{
            $data->CcyNo = $data->Prefix.$data->CcyNo.$data->Suffix;
        }
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
        $data = Ccy::find($id);
        if($data->Prefix.$data->CcyNo.$data->Suffix != $request->CcyNo){
            $validator = Validator::make($request->all(), [
                'CcyNo' => 'required|unique:expense'
            ],
                [
                    'CcyNo.required' => 'Mã tiền tệ không được để trống',
                    'CcyNo.unique' => 'Mã tiền tệ đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'expense')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data,'ccy', $autoModel, 'tiền tệ');

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->CcyNo = $request->CcyNo;
        }
        $request->validate($validate);
        $data->CcyName = $request->CcyName;
        $data->Inactive = $request->Inactive;
        $data->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }

    public function delete($id){
        $data = Ccy::find($id);
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
        Ccy::destroy($IDs);
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
        $data = Ccy::search($request);
//        $Uom = Uom::select('UomID', 'UomName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
