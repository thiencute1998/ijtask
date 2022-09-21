<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Bank;
use Module\Listing\Models\BankLink;
use Module\Listing\Models\Coacon;
use Module\Listing\Models\CoaconCate;
use Module\Listing\Models\CoaconCateList;
use Module\Listing\Models\CoaconCateValue;
use Module\Listing\Models\Country;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;

class CountryController extends Controller{
    public function list(Request $request){
        $data = Country::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('country', ['select' => ['AreaID', 'CountryName'], 'conditions' => []]), 'tinh.xlsx');
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'CountryNo' => 'required|unique:country',
            'CountryName'=> 'required',
        ],
            [
                'CountryNo.required' => 'Mã quốc gia không được để trống',
                'CountryNo.unique' => 'Mã quốc gia đã tồn tại',
                'CountryName.required' => 'Tên quốc gia không được để trống',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $country = new Country([
            'CountryNo' => $request->CountryNo,
            'CountryName' => $request->CountryName,
            'Inactive' => $request->Inactive
        ]);

        $country->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $country->CountryID;

        return json_encode($json);
    }

    public function edit($id){

        $data = Country::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;

        return json_encode($json);
    }

    public function view($id){
        $data = Country::find($id);

        if($data){
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;

            return json_encode($json);
        }else{

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function update(Request $request, $id){

        $country = Country::find($id);
        if(!$country){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }

        $country->CountryName = $request->CountryName;
        $country->CountryNo = $request->CountryNo;
        $country->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;

    }


    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        country::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function delete($id){
        $data = country::find($id);
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

}
?>
