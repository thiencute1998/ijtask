<?php
namespace Module\Dashboard\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\district;

class DashboardController extends Controller{

    public function index(Request $request){
        $data = [];
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function getDistrict(Request $request){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        $ProvinceID = $request->post('ProvinceID');
        if (!$ProvinceID) {
            return json_encode($json);
        }
        $data = DB::table('district')->where(['ProvinceID' => $ProvinceID])->get();
        Log::debug($data);
        if ($data) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $data;
        }
        return json_encode($json);
    }
    public function getCommune(Request $request){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        $DistrictID = $request->post('DistrictID');
        if (!$DistrictID) {
            return json_encode($json);
        }
        $data = DB::table('commune')->where(['DistrictID' => $DistrictID])->get();
        Log::debug($data);
        if ($data) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $data;
        }
        return json_encode($json);
    }

    public function getRevenueCate(Request $request){
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $revenueCateList = '';
        if($request->RevenueCateID){
            $revenueCateList = DB::table('revenue_cate_list')->where('CateID', $request->RevenueCateID)->first();
            if($revenueCateList->Detail == 1){
                $revenueCateValue = DB::table('revenue_cate_value')->where('CateID',$request->RevenueCateID)->get();
                $json['data']['catevalue'] = $revenueCateValue;
            }
            else{
                $revenueCateList = DB::table('revenue_cate_list')->where('ParentID',$request->RevenueCateID)->get();
                $json['data']['catelist'] = $revenueCateList;
            }
        }
        else{
            $revenueCateList = DB::table('revenue_cate_list')->where('Level','=',2)->orderBy('CateNo')->get();
            $json['data']['catelist'] = $revenueCateList;
        }

        return json_encode($json);

    }
}
