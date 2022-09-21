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
use Module\Listing\Models\Province;
use Module\Listing\Models\Area;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;

class ProvinceController extends Controller{
    public function list(Request $request){
        $data = Province::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('province', ['select' => ['ProvinceID', 'ProvinceName'], 'conditions' => []]), 'tinh.xlsx');
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'ProvinceNo' => 'required',
            'ProvinceName'=> 'required',
        ],
            [
                'ProvinceNo.required' => 'Mã tỉnh không được để trống',
                'ProvinceName.required' => 'Tên tỉnh không được để trống',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $province = new Province([
            'ProvinceNo' => $request->ProvinceNo,
            'ProvinceName' => $request->ProvinceName,
            'ProvinceID' => $request->ProvinceID,
            'ProvinceName' => $request->ProvinceName,
            'CountryID' => $request->CountryID,
            'CountryName' => $request->CountryName,
            'AreaID' => $request->AreaID,
            'AreaName' => $request->AreaName,
        ]);

        $province->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $province->ProvinceID;

        return json_encode($json);
    }

    public function edit($id){

        $data = Province::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;

        return json_encode($json);
    }

    public function view($id){
        $data = Province::find($id);

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

        $province = Province::find($id);
        if($province->ProvinceID == null){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        $province->ProvinceNo = $request->ProvinceNo;
        $province->ProvinceName = $request->ProvinceName;
        $province->AreaID = $request->AreaID;
        $province->AreaName = $request->AreaName;
        $province->CountryID = $request->CountryID;
        $province->CountryName = $request->CountryName;
        $province->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $province;
        return $json;

    }


    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        province::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function delete($id){
        $data = province::find($id);
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getProvince(Request $request)
    {
        $ProvinceName = $request->post('ProvinceName');
        $CountryID = $request->post('CountryID');
        $provinceQuery = DB::table('province');
        if ($ProvinceName) {
            $provinceQuery->where('ProvinceName', 'like', '%' . $ProvinceName . '%');
        }
        if ($CountryID) {
            $provinceQuery->where('CountryID', '=', $CountryID);
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $province = $provinceQuery->paginate($per_page);

        if ($province) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $province;
            return json_encode($json);
        }
        $json['status'] = 0;
        $json['msg'] = '';
        return json_encode($json);
    }
    public function getArea(Request  $request){
        $area = Area::query();
        if($request->post('AreaName')){
            $area->where('AreaName', '=', $request->post('AreaName'));
        }
        $data = $area->paginate($request->post('per_page'));
        $json['data'] = $data;
        $json['status'] = 0;
        $json['msg'] = '';
        return json_encode($json);
    }

    public function getCountry(Request $request)
    {
        $CountryName = $request->post('CountryName');
        $countryQuery = DB::table('country');
        if ($CountryName) {
            $countryQuery->where('CountryName', 'like', '%' . $CountryName . '%');
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $country = $countryQuery->paginate($per_page);

        if ($country) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $country;
            return json_encode($json);
        }
        $json['status'] = 0;
        $json['msg'] = '';
        return json_encode($json);
    }
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = Province::search($request);
//        $Uom = Uom::select('UomID', 'UomName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
?>
