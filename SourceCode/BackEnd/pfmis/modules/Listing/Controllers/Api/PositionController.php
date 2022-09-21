<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use IjCore\IjFile;
use IjCore\IjString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Ccy;
use Module\Listing\Models\Company;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\Position;
use Module\Listing\Models\province;
use Module\Listing\Models\SysAutoNumber;

use Module\Listing\Models\Employee;
use Module\Listing\Models\PositionCate;
use Module\Listing\Models\PositionCateList;
use Module\Listing\Models\PositionCateValue;
use Module\Listing\Models\PositionFile;
use Module\Listing\Models\PositionPer;
use Module\Listing\Models\PositionVideo;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\PositionLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class PositionController extends Controller{

    public function list(Request $request){
        $data = Position::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('position', ['select' => ['PositionNo', 'PositionName'], 'conditions' => []]), 'nganh.xlsx');
    }

    public function create(){
        if(old('PositionNo')){
            $auto = old('PositionNo');
        }else{
            $auto = AutoNo::genNewNumberNo('position');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'PositionNo' => 'required',
            'PositionName'=> 'required',
        ],
            [
                'PositionNo.required' => 'Mã chức vụ không được để trống',
                'PositionName.required' => 'Tên chức vụ không được để trống',
            ]
        );
        $autoModel = SysAutoNumber::where('NumberKey', 'position')->first();
        AutoNo::storeValidate($validator, $request, 'position', $autoModel, 'ngành');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }


        $position = new Position([
            'PositionNo' => $request->PositionNo,
            'PositionName' => $request->PositionName,
            'Note' => $request->Note,

        ]);

        $position->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $position->PositionID;

        return json_encode($json);
    }

    public function view($id){
        $data = Position::find($id);

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

    public function edit($id){
        $data = Position::find($id);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $position = Position::find($id);
        if(!$position){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }

        $position->PositionName = $request->PositionName;
        $position->PositionNo = $request->PositionNo;
        $position->Note = $request->Note;
        $position->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;

    }

    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        Position::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function delete($id){
        $data = Position::find($id);
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
