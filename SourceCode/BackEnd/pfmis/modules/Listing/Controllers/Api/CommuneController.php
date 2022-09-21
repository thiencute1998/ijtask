<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Commune;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;

class CommuneController extends Controller{
    public function list(Request $request){
        $data = Commune::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'CommuneNo' => 'required',
            'CommuneName'=> 'required',
        ],
            [
                'CommuneNo.required' => 'Mã tỉnh không được để trống',
                'CommuneName.required' => 'Tên tỉnh không được để trống',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $commune = new Commune([
            'CommuneNo' => $request->CommuneNo,
            'CommuneName' => $request->CommuneName,
            'DistrictID' => $request->DistrictID,
            'DistrictName' => $request->DistrictName,
            'ProvinceID' => $request->ProvinceID,
            'ProvinceName' => $request->ProvinceName
        ]);

        $commune->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $commune->CommuneID;

        return json_encode($json);
    }

    public function edit($id){

        $data = Commune::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;

        return json_encode($json);
    }

    public function view($id){
        $data = Commune::find($id);

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

        $commune = Commune::find($id);
        if(!$commune){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }

        $commune->CommuneName = $request->CommuneName;
        $commune->CommuneNo = $request->CommuneNo;
        $commune->DistrictID = $request->DistrictID;
        $commune->DistrictName = $request->DistrictName;
        $commune->ProvinceID = $request->ProvinceID;
        $commune->ProvinceName = $request->ProvinceName;
        $commune->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;

    }


    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        commune::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function delete($id){
        $data = commune::find($id);
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
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
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = Commune::search($request);
//        $Uom = Uom::select('UomID', 'UomName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
?>

