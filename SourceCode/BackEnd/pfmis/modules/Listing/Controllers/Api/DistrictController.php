<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\District;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;

class DistrictController extends Controller{
    public function list(Request $request){
        $data = District::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'DistrictNo' => 'required',
            'DistrictName'=> 'required',
        ],
            [
                'DistrictNo.required' => 'Mã huyện không được để trống',
                'DistrictName.required' => 'Tên huyện không được để trống',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $district = new District([
            'DistrictNo' => $request->DistrictNo,
            'DistrictName' => $request->DistrictName,
            'ProvinceID' => $request->ProvinceID,
            'ProvinceName' => $request->ProvinceName
        ]);

        $district->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $district->DistrictID;

        return json_encode($json);
    }

    public function edit($id){

        $data = District::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;

        return json_encode($json);
    }

    public function view($id){
        $data = District::find($id);

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

        $district = District::find($id);
        if(!$district){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }

        $district->DistrictName = $request->DistrictName;
        $district->DistrictNo = $request->DistrictNo;
        $district->ProvinceID = $request->ProvinceID;
        $district->ProvinceName = $request->ProvinceName;
        $district->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;

    }


    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        district::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function delete($id){
        $data = district::find($id);
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getArea(Request $request)
    {
        $AreaName = $request->post('AreaName');
        $ProvinceID = $request->post('ProvinceID');
        $areaQuery = DB::table('area');
        if ($AreaName) {
            $areaQuery->where('AreaName', 'like', '%' . $AreaName . '%');
        }
        if ($ProvinceID) {
            $areaQuery->where('ProvinceID', '=', $ProvinceID);
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $area = $areaQuery->paginate($per_page);

        if ($area) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $area;
            return json_encode($json);
        }
        $json['status'] = 0;
        $json['msg'] = '';
        return json_encode($json);
    }

    public function getProvince(Request $request)
    {
        $ProvinceName = $request->post('ProvinceName');
        $AreaID = $request->post('AreaID');
        $provinceQuery = DB::table('province');
        if ($ProvinceName) {
            $provinceQuery->where('ProvinceName', 'like', '%' . $ProvinceName . '%');
        }
        if ($AreaID) {
            $provinceQuery->where('AreaID', '=', $AreaID);
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
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = District::search($request);
//        $Uom = Uom::select('UomID', 'UomName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
?>
