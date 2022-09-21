<?php
namespace Module\Customer\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Module\SysAdmin\Models\SysSetup;

class CommonController extends Controller{
    public function index(){}

    public function getProvince(Request $request)
    {
        $ProvinceName = $request->post('ProvinceName');
        $provinceQuery = DB::table('province');
        if ($ProvinceName) {
            $provinceQuery->where('ProvinceName', 'like', '%' . $ProvinceName . '%');
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

    public function getDistrict(Request $request)
    {
        $DistrictName = $request->post('DistrictName');
        $ProvinceID = $request->post('ProvinceID');
        $districtQuery = DB::table('district');
        if ($DistrictName) {
            $districtQuery->where('DistrictName', 'like', '%' . $DistrictName . '%');
        }

        if ($ProvinceID) {
            $districtQuery->where('ProvinceID', '=', $ProvinceID);
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $district = $districtQuery->paginate($per_page);

        if ($district) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $district;
            return json_encode($json);
        }
        $json['status'] = 0;
        $json['msg'] = '';
        return json_encode($json);
    }

    public function getCommune(Request $request){
        $CommuneName = $request->post('CommuneName');
        $ProvinceID = $request->post('ProvinceID');
        $DistrictID = $request->post('DistrictID');
        $communeQuery = DB::table('commune');
        if ($CommuneName) {
            $communeQuery->where('CommuneName', 'like', '%' . $CommuneName . '%');
        }
        if ($ProvinceID) {
            $communeQuery->where('ProvinceID', '=', $ProvinceID);
        }
        if ($DistrictID) {
            $communeQuery->where('DistrictID', '=', $DistrictID);
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $commune = $communeQuery->paginate($per_page);

        if ($commune) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $commune;
            return json_encode($json);
        }
        $json['status'] = 0;
        $json['msg'] = '';
        return json_encode($json);
    }


}
