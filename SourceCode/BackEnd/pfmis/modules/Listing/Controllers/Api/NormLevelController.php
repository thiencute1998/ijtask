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
use Module\Listing\Models\NormLevel;
use Module\Listing\Models\NormLevelCate;
use Module\Listing\Models\NormLevelCateList;
use Module\Listing\Models\NormLevelCateValue;
use Module\Listing\Models\NormLevelFile;
use Module\Listing\Models\NormLevelVideo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\Uom;
use phpDocumentor\Reflection\Types\Null_;

class NormLevelController extends Controller{

    public function list(Request $request){
        $data = NormLevel::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('normLevel', ['select' => ['NormLevelNo', 'NormLevelName'], 'conditions' => []]), 'donvi.xlsx');
    }

    public function create(){
        $normLevel = NormLevel::all();
        $json['status'] = 1;


        $json['msg'] = 'Thành công!';
        $json['data']['normLevel'] = $normLevel;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'NormLevelNo' => 'required|unique:norm_level',
            'NormLevelName'=> 'required',
        ],
            [
                'NormLevelNo.required' => 'Mã định mức phân bổ dự toán không được để trống',
                'NormLevelNo.unique' => 'Mã định mức phân bổ dự toán đã tồn tại',
                'NormLevelName.required' => 'Tên định mức phân bổ dự toán không được để trống',
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
            $parent = NormLevel::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
        } else {
            $level = 1;
        }
        $normLevel = new NormLevel([
            'NormLevelNo' => $request->NormLevelNo,
            'NormLevelName' => $request->NormLevelName,
            'ParentID' => $request->ParentID,
            'Level' => $level,
            'ParentNo' => $request->ParentNo,
            'Detail' => 1,
            'ParentID' => $request->ParentID,
            'Inactive' => $request->Inactive,
            'EffectiveDate' => $request->EffectiveDate,
            'ExpirationDate' => $request->ExpirationDate,
            'UomID' => $request->UomID,
            'UomNo' => $request->UomNo,
            'UomName' => $request->UomName,
            'CcyID' => $request->CcyID,
            'CcyNo' => $request->CcyNo,
            'CcyName' => $request->CcyName,
            'Formula' => $request->Formula,
            'ExchangeRate' => $request->ExchangeRate,
            'Quantity'=> $request->Quantity,
            'FCMinUnitPrice'=> $request->FCMinUnitPrice,
            'LCMinUnitPrice' => $request->LCMinUnitPrice,
        ]);

        $normLevel->save();

        //normLevel cate
        $arrInsert = [];
        if($request->post('NormLevelCate')){
            foreach($request->post('NormLevelCate') as $key=> $item){
                if($item['CateID']) {
                    $arrInsert[] = [
                        'NormLevelID' => $normLevel->NormLevelID,
                        'CateID' => $item['CateID'],
                        'CateNo' => $item['CateNo'],
                        'CateValue' => $item['CateValue']
                    ];
                }
            }

            DB::table('norm_level_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $normLevel->NormLevelID;

        return json_encode($json);
    }

    public function view($id){
        $data = NormLevel::find($id);

        if($data){

            $arrParent = [];
            if($data->ParentID){
                $parent = NormLevel::find($data->ParentID);
                if(isset($parent->NormLevelNo) && isset($parent->NormLevelName)){
                    $arrParent = [
                        "ParentNo" => $parent->NormLevelNo,
                        "ParentName" => $parent->NormLevelName
                    ];
                }
            }

            $NormLevelCate = NormLevelCate::where('NormLevelID',$id)->get();
            $arrCate = [];
            foreach($NormLevelCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $NormLevelCateList = NormLevelCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $NormLevelCateValue = NormLevelCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;
            $json['data']['Parent'] = $arrParent;

            $json['data']['NormLevelCate'] = $NormLevelCate;
            $json['data']['NormLevelCateList'] = $NormLevelCateList;
            $json['data']['NormLevelCateValue'] = $NormLevelCateValue;
            return json_encode($json);
        }else{

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id){
        $data = NormLevel::find($id);
        $normLevel = NormLevel::all();
        if(old('NormLevelNo')){
            $data->NormLevelNo = old('NormLevelNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['normLevel'] = $normLevel;
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $normLevel = NormLevel::find($id);
        if(!$normLevel){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($normLevel->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        if($normLevel->NormLevelNo !== $request->NormLevelNo){
            $validator = Validator::make($request->all(), [
                'NormLevelNo' => 'required',
                'NormLevelName'=> 'required',
            ],
                [
                    'NormLevelNo.required' => 'Mã định mức phân bổ dự toán không được để trống',
                    'NormLevelName.required' => 'Tên định mức phân bổ dự toán không được để trống',
                ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $normLevel->NormLevelNo = $request->NormLevelNo;
        }
        // set for new Parent
            if($request->ParentID){
                $parent = NormLevel::find($request->ParentID);
                $parent->Detail = 0;
                $parent->save();
                $level = $parent->Level + 1;
            }else{
                $level = 1;
            }
         // set for old Parents
            if($normLevel->ParentID){
                $parentOld = NormLevel::find($normLevel->ParentID);
                $count = DB::table('norm_level')->where('ParentID','=', $parentOld->NormLevelID)->count();
                if($count < 2){
                    $parentOld->Detail = 1;
                    $parentOld->save();
                }
            }
        // set Detail, Level for ParentNew
        if(empty($request->ParentID)){
            $level = 1;

        } else {
            $Parent = NormLevel::find($request->ParentID);
            $Parent->Detail = 0;
            $Parent->save();
            $level = $Parent->Level+ 1;
        }

        // set ParentOld
        if($normLevel->ParentID != $request->ParentID && $normLevel->ParentID != null){
            $ParentOld = NormLevel::find($normLevel->ParentID);
            $count = NormLevel::where('ParentID', $ParentOld->NormLevelID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }

        // set new Level Child
        $exists = DB::table('norm_level')->where('ParentID', '=', $normLevel->NormLevelID)->exists();
        if($exists){
            $oldLevel = $normLevel->Level;
            if($oldLevel == 1){
                $search = substr($normLevel->NormLevelNo, 1);
            }
            else {
                $stt = ($oldLevel - 1) * 2;
                $search = substr($normLevel->NormLevelNo,0, $stt);
            }

            $upDateLevelChild = $level-$oldLevel;
            DB::table('norm_level')->where('NormLevelNo', 'like', $search.'%')
                                        ->where('NormLevelNo', '!=', $normLevel->NormLevelNo)->increment('Level', $upDateLevelChild);
        }


        $normLevel->NormLevelName = $request->NormLevelName;
        $normLevel->ParentID = $request->ParentID;
        $normLevel->Note  = $request->Note;
        $normLevel->Level = $level;
        $normLevel->NormLevelInOut = $request->NormLevelInOut;
        $normLevel->BudgetStateType = $request->BudgetStateType;
        $normLevel->save();

        // normLevel cate
        $normLevelCate = $request->post('NormLevelCate');
        if(isset($normLevelCate)){
            $NormLevelCate = $request->post('NormLevelCate');
            NormLevelCate::where('NormLevelID',$id)->delete();
            $arrInsert = [];
            foreach($NormLevelCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "NormLevelID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateNo"=> $cate['CateNo'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('norm_level_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $normLevel->NormLevelID;
        return $json;


    }

    public function delete($id)
    {
        $data = NormLevel::find($id);
        if ($data) {
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
            // set for old Parent
            if($data->ParentID){
                $parent = NormLevel::find($data->ParentID);
                $count = DB::table('norm_level')->where('ParentID', '=', $parent->NormLevelID)->count();
                if($count < 2){
                    $parent->Detail = 1;
                    $parent->save();
                }
            }
            DB::table('norm_level_cate')->where('NormLevelId', '=', $data->NormLevelID)->delete();
            $data->delete();

        }
        $normLevelCate = NormLevelCate::find($id);
        if($normLevelCate){
            $normLevelCate->delete();
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public function deleteArray(Request $request)
    {
        $IDs = $request->post('array_id');
        foreach ($IDs as $id){
            $data = NormLevel::find($id);
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        DB::table('norm_level_cate')->whereIn('NormLevelID', $IDs)->delete();
        NormLevel::destroy($IDs);
        $sql = "UPDATE norm_level
                SET Detail = 1
                WHERE NormLevelID NOT IN (SELECT ParentID FROM (select ParentID from norm_level WHERE ParentID is not null) t)";
        DB::statement($sql);
        NormLevelCate::whereIn('NormLevelID',$IDs)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }


    public function getNormLevelCateList(){
        $normLevelCateList = NormLevelCateList::where('Inactive',0)->where('Detail', 1)->get();
        $normLevelCateValue = NormLevelCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['NormLevelCateList'] = $normLevelCateList;
        $json['data']['NormLevelCateValue'] = $normLevelCateValue;
        return json_encode($json);
    }


    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = NormLevel::query();
        $query->where('norm_level.ParentID', $ParentID);
        $query->groupBy('norm_level.NormLevelID');
        $data = $query->orderBy('norm_level.NormLevelNo', 'desc')->get();
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
        $data = NormLevel::search($request);
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
