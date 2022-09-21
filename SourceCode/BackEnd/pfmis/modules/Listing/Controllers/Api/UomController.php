<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Uom;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SysAutoNumber;

class UomController extends Controller{

    public function list(Request $request){
        $data = Uom::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('uom', ['select' => ['UomNo', 'UomName'], 'conditions' => []]), 'donvitinh.xlsx');
    }
    public function create(){
        if(old('UomNo')){
            $auto = old('UomNo');
        }else{
            $auto = AutoNo::genNewNumberNo('uom');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'UomNo' => 'required|unique:uom',
            'UomName' => 'required'
        ],
        [
            'UomName.required' => 'Tên đơn vị tính không được để trống',
            'UomNo.required' => 'Mã đơn vị tính không được để trống',
            'UomNo.unique' => 'Mã đơn vị tính đã tồn tại',
        ]);

        $autoModel = SysAutoNumber::where('NumberKey', 'uom')->first();
        AutoNo::storeValidate($validator, $request, 'uom', $autoModel, 'đơn vị tính');

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new Uom([
            'UomNo' => $request->UomNo,
            'UomName' => $request->UomName,
            'UomShortName' => $request->UomShortName,
            'UomType' => $request->UomType,
            'UomTime' => $request->UomTime,
            'UomCurrency' => $request->UomCurrency,
            'UomPoint' => $request->UomPoint,
            'Note' => $request->Note,
            'Prefix' => $autoModel->Prefix,
            'Suffix' => $autoModel->Suffix,
            'Inactive' => $request->Inactive,
        ]);
        $data->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->UomID;

        return json_encode($json);
    }

    public function view($id){
        $data = Uom::find($id);
        $data->UomNo = $data->Prefix.$data->UomNo.$data->Suffix;

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function edit($id){
        $data = Uom::find($id);
        if(old('UomNo')){
            $data->UomNo = old('UomNo');
        }else{
            $data->UomNo = $data->Prefix.$data->UomNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = Uom::find($id);
        if($data->Prefix.$data->UomNo.$data->Suffix != $request->UomNo){
            $validator = Validator::make($request->all(), [
                'UomNo' => 'required|unique:uom'
            ],
            [
                'UomNo.required' => 'Mã đơn vị tính không được để trống',
                'UomNo.unique' => 'Mã đơn vị tính đã tồn tại',
            ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'uom')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data,'uom', $autoModel, 'đơn vị tính');

            $validator->after(function ($validator) use ($request) {
                if(empty($request->UomName)){
                    $validator->errors()->add('UomName', 'Tên đơn vị tính không không được để trống');
                }
            });

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->UomNo = $request->UomNo;
        }
        $data->UomName = $request->UomName;
        $data->Note = $request->Note;
        $data->UomShortName = $request->UomShortName;
        $data->UomType = $request->UomType;
        $data->UomTime = $request->UomTime;
        $data->UomCurrency = $request->UomCurrency;
        $data->UomPoint = $request->UomPoint;
        $data->Inactive = $request->Inactive;
        $data->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }

    public function delete($id){
        $data = Uom::find($id);
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
        Uom::destroy($IDs);
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
        $data = Uom::search($request);
//        $Uom = Uom::select('UomID', 'UomName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
