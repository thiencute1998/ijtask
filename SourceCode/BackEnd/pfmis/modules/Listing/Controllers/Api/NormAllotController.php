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
use Module\Listing\Models\NormAllot;
use Module\Listing\Models\NormAllotCate;
use Module\Listing\Models\NormAllotCateList;
use Module\Listing\Models\NormAllotCateValue;
use Module\Listing\Models\NormAllotFile;
use Module\Listing\Models\NormAllotVideo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\Uom;
use phpDocumentor\Reflection\Types\Null_;

class NormAllotController extends Controller{

    public function list(Request $request){
        $data = NormAllot::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('normAllot', ['select' => ['NormAllotNo', 'NormAllotName'], 'conditions' => []]), 'donvi.xlsx');
    }

    public function create(){
        $normAllot = NormAllot::all();
        $json['status'] = 1;


        $json['msg'] = 'Thành công!';
        $json['data']['normAllot'] = $normAllot;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'NormAllotNo' => 'required|unique:norm_allot',
            'NormAllotName'=> 'required',
        ],
            [
                'NormAllotNo.required' => 'Mã tiêu chí phân bổ dự toán không được để trống',
                'NormAllotNo.unique' => 'Mã tiêu chí phân bổ dự toán đã tồn tại',
                'NormAllotName.required' => 'Tên tiêu chí phân bổ dự toán không được để trống',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        // set Parent and Level
        if($request->ParentID){
            $parent = NormAllot::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
        } else {
            $level = 1;
        }

        if($request->post('EffectiveDate')){
            $request['EffectiveDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('EffectiveDate'))));
        }

        if($request->post('ExpirationDate')){
            $request['ExpirationDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('ExpirationDate'))));
        }
        $normAllot = new NormAllot([
            'NormAllotNo' => $request->NormAllotNo,
            'NormAllotName' => $request->NormAllotName,
            'ParentID' => $request->ParentID,
            'Level' => $level,
            'ParentNo' => $request->ParentNo,
            'Detail' => 1,
            'ParentID' => $request->ParentID,
            'UomID' => $request->UomID,
            'UomNo' => $request->UomNo,
            'UomName'=>$request->UomName,
            'EffectiveDate' => $request['EffectiveDate'],
            'ExpirationDate' => $request['ExpirationDate'],
            'Inactive' => $request->Inactive,
        ]);

        $normAllot->save();

        //normAllot cate
        $arrInsert = [];
        if($request->post('NormAllotCate')){
            foreach($request->post('NormAllotCate') as $key=> $item){
                if($item['CateID']) {
                    $arrInsert[] = [
                        'NormAllotID' => $normAllot->NormAllotID,
                        'CateID' => $item['CateID'],
                        'CateNo' => $item['CateNo'],
                        'CateValue' => $item['CateValue']
                    ];
                }
            }

            DB::table('norm_allot_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $normAllot->NormAllotID;

        return json_encode($json);
    }

    public function view($id){
        $data = NormAllot::find($id);

        if($data){

            $arrParent = [];
            if($data->ParentID){
                $parent = NormAllot::find($data->ParentID);
                if(isset($parent->NormAllotNo) && isset($parent->NormAllotName)){
                    $arrParent = [
                        "ParentNo" => $parent->NormAllotNo,
                        "ParentName" => $parent->NormAllotName
                    ];
                }
            }

            $NormAllotCate = NormAllotCate::where('NormAllotID',$id)->get();
            $arrCate = [];
            foreach($NormAllotCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $NormAllotCateList = NormAllotCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $NormAllotCateValue = NormAllotCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;
            $json['data']['Parent'] = $arrParent;
            $json['data']['NormAllotCate'] = $NormAllotCate;
            $json['data']['NormAllotCateList'] = $NormAllotCateList;
            $json['data']['NormAllotCateValue'] = $NormAllotCateValue;
            return json_encode($json);
        }else{

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id){
        $data = NormAllot::find($id);
        $normAllot = NormAllot::all();
        if(old('NormAllotNo')){
            $data->NormAllotNo = old('NormAllotNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['normAllot'] = $normAllot;
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $normAllot = NormAllot::find($id);
        if(!$normAllot){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($normAllot->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        if($normAllot->NormAllotNo !== $request->NormAllotNo){
            $validator = Validator::make($request->all(), [
                'NormAllotNo' => 'required',
                'NormAllotName'=> 'required',
            ],
                [
                    'NormAllotNo.required' => 'Mã tiêu chí phân bổ dự toán không được để trống',
                    'NormAllotName.required' => 'Tên tiêu chí phân bổ dự toán không được để trống',
                ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $normAllot->NormAllotNo = $request->NormAllotNo;
        }
        // set detail new Parent
        if($request->ParentID){
            $parent = NormAllot::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
        } else $level = 1;
        // set detail parentOld
        if($normAllot->ParentID != null && $normAllot->ParentID != $request->ParentID ){
            $parentOld = NormAllot::find($normAllot->ParentID);
            $count = DB::table('norm_allot')->where('ParentID', '=', $request->ParentID)->count();
            if($count < 2){
                $parentOld->Detail =1;
                $parentOld->save();
            }
        }

        // set ParentOld
        if($normAllot->ParentID != $request->ParentID && $normAllot->ParentID != null){
            $ParentOld = NormAllot::find($normAllot->ParentID);
            $count = NormAllot::where('ParentID', $ParentOld->NormAllotID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        if($request->post('EffectiveDate')){
            $request['EffectiveDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('EffectiveDate'))));
        }

        if($request->post('ExpirationDate')){
            $request['ExpirationDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('ExpirationDate'))));
        }
        $normAllot->NormAllotName = $request->NormAllotName;
        $normAllot->ParentID = $request->ParentID;
        $normAllot->ParentNo = $request->ParentNo;
        $normAllot->Level = $level;
        $normAllot->UomID = $request->UomID;
        $normAllot->UomNo = $request->UomNo;
        $normAllot->UomName = $request->UomName;
        $normAllot->EffectiveDate =  $request['EffectiveDate'];
        $normAllot->ExpirationDate =  $request['ExpirationDate'];
        $normAllot->save();

        // normAllot cate
        $normAllotCate = $request->post('NormAllotCate');
        if(isset($normAllotCate)){
            $NormAllotCate = $request->post('NormAllotCate');
            NormAllotCate::where('NormAllotID',$id)->delete();
            $arrInsert = [];
            foreach($NormAllotCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "NormAllotID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateNo"=> $cate['CateNo'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('norm_allot_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $normAllot->NormAllotID;
        return $json;


    }

    public function delete($id)
    {
        $data = NormAllot::find($id);
        if ($data) {
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
            // set for old Parent
            if($data->ParentID){
                $parent = NormAllot::find($data->ParentID);
                $count = DB::table('norm_allot')->where('ParentID', '=', $parent->NormAllotID)->count();
                if($count < 2){
                    $parent->Detail = 1;
                    $parent->save();
                }
            }
            DB::table('norm_allot_cate')->where('NormAllotId', '=', $data->NormAllotID)->delete();
            $data->delete();

        }
        DB::table('norm_allot_cate')->where('NormAllotID', '=', $id);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public function deleteArray(Request $request)
    {
        $IDs = $request->post('array_id');
        foreach ($IDs as $id){
            $data = NormAllot::find($id);
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        DB::table('norm_allot_cate')->whereIn('NormAllotID', $IDs)->delete();
        NormAllot::destroy($IDs);
        $sql = "UPDATE norm_allot
                SET Detail = 1
                WHERE NormAllotID NOT IN (SELECT ParentID FROM (select ParentID from norm_allot WHERE ParentID is not null) t)";
        DB::statement($sql);
        NormAllotCate::whereIn('NormAllotID',$IDs)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }


    public function getNormAllotCateList(){
        $normAllotCateList = NormAllotCateList::where('Inactive',0)->where('Detail', 1)->get();
        $normAllotCateValue = NormAllotCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['NormAllotCateList'] = $normAllotCateList;
        $json['data']['NormAllotCateValue'] = $normAllotCateValue;
        return json_encode($json);
    }


    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = NormAllot::query();
        $query->where('norm_allot.ParentID', $ParentID);
        $query->groupBy('norm_allot.NormAllotID');
        $data = $query->orderBy('norm_allot.NormAllotNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = NormAllot::search($request);
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
