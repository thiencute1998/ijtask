<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Area;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;

class AreaController extends Controller{
    public function list(Request $request){
        $data = Area::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'AreaNo' => 'required|unique:area',
            'AreaName'=> 'required',
        ],
            [
                'AreaNo.required' => 'Mã vùng không được để trống',
                'AreaNo.unique' => 'Mã vùng đã tồn tại',
                'AreaName.required' => 'Tên vùng không được để trống',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $area = new Area([
            'AreaNo' => $request->AreaNo,
            'AreaName' => $request->AreaName,
            'CountryID' => $request->CountryID,
            'CountryName' => $request->CountryName,
            'Inactive' => $request->Inactive
        ]);

        $area->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $area->AreaID;

        return json_encode($json);
    }

    public function edit($id){

        $data = Area::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;

        return json_encode($json);
    }

    public function view($id){
        $data = Area::find($id);

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

        $area = Area::find($id);
        if(!$area){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }

        $area->AreaName = $request->AreaName;
        $area->AreaNo = $request->AreaNo;
        $area->CountryID = $request->CountryID;
        $area->CountryName = $request->CountryName;
        $area->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;

    }


    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        area::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function delete($id){
        $data = area::find($id);
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
        $CountryID = $request->post('CountryID');
        $areaQuery = DB::table('area');
        if ($AreaName) {
            $areaQuery->where('AreaName', 'like', '%' . $AreaName . '%');
        }
        if ($CountryID) {
            $areaQuery->where('CountryID', '=', $CountryID);
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
        $data = Area::search($request);
//        $Uom = Uom::select('UomID', 'UomName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
?>


