<?php
namespace Module\Contract\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Vendor;
use Module\Listing\Models\Employee;

class VendorController extends Controller{

    public function index(Request $request){
        $data = Vendor::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('vendor', ['select' => ['VendorNo', 'VendorName'], 'conditions' => []]), 'nhacungcap.xlsx');
    }

    public function addnew(){
        if(old('VendorNo')){
            $auto = old('VendorNo');
        }else{
            $auto = AutoNo::genNewNumberNo('vendor');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'VendorNo' => 'required|unique:vendor'
        ],
            [
                'VendorNo.required' => 'Mã nhà cung cấp không được để trống',
                'VendorNo.unique' => 'Mã nhà cung cấp đã tồn tại',
            ]);

        $autoModel = SysAutoNumber::where('NumberKey', 'vendor')->first();
        AutoNo::storeValidate($validator, $request, 'vendor', $autoModel, 'nhà cung cấp');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new Vendor([
            'VendorNo' => $request->VendorNo,
            'VendorName' => $request->VendorName,
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
        $json['data'] = $data->VendorID;

        return json_encode($json);
    }

    public function view($id){
        $data = Vendor::find($id);
        $data->VendorNo = $data->Prefix.$data->VendorNo.$data->Suffix;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function edit($id){
        $data = Vendor::find($id);
        if(old('VendorNo')){
            $data->VendorNo = old('VendorNo');
        }else{
            $data->VendorNo = $data->Prefix.$data->VendorNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = Vendor::find($id);
        if($data->Prefix.$data->VendorNo.$data->Suffix != $request->VendorNo){
            $validator = Validator::make($request->all(), [
                'VendorNo' => 'required|unique:vendor'
            ],
                [
                    'VendorNo.required' => 'Mã nhà cung cấp không được để trống',
                    'VendorNo.unique' => 'Mã nhà cung cấp đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'vendor')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data,'vendor', $autoModel, 'nhà cung cấp');
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->VendorNo = $request->VendorNo;
        }
        $data->VendorName = $request->VendorName;
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
        $json['data'] = null;

        return json_encode($json);
    }

    public function delete($id){
        $data = Vendor::find($id);
        if($data){
            $data->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        Vendor::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
