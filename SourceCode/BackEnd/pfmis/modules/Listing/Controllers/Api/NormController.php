<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\NormAllotMap;
use Module\Listing\Models\NormTable;
use Module\Listing\Models\NormTableItem;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Norm;
use Module\Listing\Models\NormCate;
use Module\Listing\Models\NormCateList;
use Module\Listing\Models\NormCateValue;
use Module\Listing\Models\Uom;

class NormController extends Controller{

    public function list(Request $request){
        $data = Norm::search($request);
        $uom = Uom::where('Inactive','!=',1)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        $json['uom'] = $uom;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('norm', ['select' => ['NormNo', 'NormName'], 'conditions' => []]), 'dinhmucthu.xlsx');
    }

    public function create(){
        $norm = Norm::all();
        if (old('NormNo')) {
            $auto = old('NormNo');
        } else {
            $auto = AutoNo::genNewNumberNo('norm');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['norm'] = $norm;
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NormNo' => 'required|unique:norm',
            'NormName' => 'required|unique:norm',
        ],
            [
                'NormNo.required' => 'Mã chỉ tiêu định mức dự toán không được để trống',
                'NormNo.unique' => 'Mã chỉ tiêu định mức dự toán đã tồn tại',
                'NormName.required' => 'Tên chỉ tiêu định mức dự toán không được để trống',
                'NormName.unique' => 'Tên chỉ tiêu định mức dự toán đã tồn tại',
            ]
        );

        if ($validator->fails()) {
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        // set Parent and Level
        if($request->ParentID){
            $parent = Norm::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
        } else {
            $level = 1;
        }

        $norm = new norm([
            'NormNo' => $request->NormNo,
            'NormName' => $request->NormName,
            'ParentID' => $request->ParentID,
            'ParentNo' => $request->ParentNo,
            'ExpenseNo' => $request->ExpenseNo,
            'ExpenseID' => $request->ExpenseID,
            'RevenueID' => $request->RevenueID,
            'ExpenseName' => $request->ExpenseName,
            'RevenueNo' => $request->RevenueNo,
            'RevenueName' => $request->RevenueName,
            'UomID' => $request->UomID,
            'UomNo' => $request->UomNo,
            'UomName' => $request->UomName,
            'Comment' => $request->Comment,
            'NOrder' => $request->NOrder,
            'Level' => $level,
            'Inactive' => $request->Inactive,
            'Detail' => 1,

        ]);

        $norm->save();
        //norm cate
        $arrInsert = [];
        if ($request->post('NormCate')) {
            foreach ($request->post('NormCate') as $key => $item) {
                if ($item['CateID']) {
                    $arrInsert[] = [
                        'NormID' => $norm->NormID,
                        'CateID' => $item['CateID'],
                        'CateNo' => $item['CateNo'],
                        'CateValue' => $item['CateValue']
                    ];
                }
            }

            DB::table('norm_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $norm->NormID;

        return json_encode($json);
    }

    public function view($id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $Norm = Norm::find($id);
        $arrParent = [];
        if ($Norm->ParentID) {
            $parent = Norm::find($Norm->ParentID);
            if (isset($parent->NormNo) && isset($parent->NormName)) {
                $arrParent = [
                    "ParentID" => $Norm->ParentID,
                    "ParentNo" => $parent->NormNo,
                    "ParentName" => $parent->NormName
                ];
            }
        }
        $NormCate = NormCate::where('NormID', $id)->get();
        $arrCateID = [];
        foreach ($NormCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $NormCateList = NormCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $NormCateValue = NormCateValue::whereIn('CateID', $arrCateID)->get();
         $NormTable = NormTable::where('Inactive','!=',1)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $Norm;
        $json['data']['Parent'] = $arrParent;
        $json['data']['NormCate'] = $NormCate;
        $json['data']['NormCateList'] = $NormCateList;
        $json['data']['NormCateValue'] = $NormCateValue;
        $json['data']['NormTable'] = $NormTable;
        return json_encode($json);
    }

    public function edit($id){
        $data = Norm::find($id);
        $NormTable = NormTable::where('Inactive','!=',1)->get();
        $NormCate = NormCate::where('NormID',$id)->get();
        $arrCate = [];
        foreach($NormCate as $Cate){
            $arrCate[] = $Cate->CateID;
        }
        $NormCateList = NormCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
        $NormCateValue = NormCateValue::whereIn('CateID',$arrCate)->get();

        if(old('NormNo')){
            $data->NormNo = old('NormNo');
        }else{
            $data->NormNo = $data->Prefix.$data->NormNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['NormTable'] = $NormTable;
        $json['data']['NormCate'] = $NormCate;
        $json['data']['NormCateList'] = $NormCateList;
        $json['data']['NormCateValue'] = $NormCateValue;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $norm = Norm::find($id);
        if (!$norm) {

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($norm->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        if ($norm->NormNo !== $request->NormNo) {
            $validator = Validator::make($request->all(), [
                'NormNo' => 'required',
                'NormName' => 'required',
            ],
                [
                    'NormNo.required' => 'Mã chỉ tiêu định mức dự toán không được để trống',
                    'NormName.required' => 'Tên chỉ tiêu định mức dự toán không được để trống',
                ]);


            $autoModel = SysAutoNumber::where('NumberKey', 'norm')->first();
            AutoNo::UpdateValidate($validator, $request, $norm, 'norm', $autoModel, 'chỉ tiêu định mức dự toán');

            if ($validator->fails()) {
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $norm->NormNo = $request->NormNo;

            // set for new Parent
            if($request->ParentID){
                $parent = Norm::find($request->ParentID);
                $parent->Detail = 0;
                $parent->save();
                $level = $parent->Level + 1;
            }else{
                $level = 1;
            }
            $norm->Level = $level;
            $norm->Detail = 1;
            // set for old Parents
            if($norm->ParentID){
                $parentOld = Norm::find($norm->ParentID);
                $count = DB::table('norm')->where('ParentID','=', $parentOld->NormID)->count();
                if($count < 2){
                    $parentOld->Detail = 1;
                    $parentOld->save();
                }
            }
        }

        $norm->NormName = $request->NormName;
        $norm->ParentID = $request->ParentID;
        $norm->ParentNo = $request->ParentNo;
        $norm->Inactive = $request->Inactive;
        $norm->ExpenseNo = $request->ExpenseNo;
        $norm->ExpenseID = $request->ExpenseID;
        $norm->ExpenseName = $request->ExpenseName;
        $norm->RevenueID = $request->RevenueID;
        $norm->RevenueNo = $request->RevenueNo;
        $norm->RevenueName = $request->RevenueName;
        $norm->UomID = $request->UomID;
        $norm->UomNo = $request->UomNo;
        $norm->UomName = $request->UomName;
        $norm->Comment = $request->Comment;
        $norm->NOrder = $request->NOrder;
        $norm->UomName = $request->UomName;
        $norm->UomName = $request->UomName;
        $norm->Inactive = $request->Inactive;
        $norm->save();

        // norm cate
        if ($request->has('NormCate')) {
            $NormCate = $request->post('NormCate');
            DB::table('norm_cate')->where('NormID', $id)->delete();
            $arrInsertCate = [];
            foreach ($NormCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'NormID' => $id,
                        'CateID' => ($cate['CateID']) ? $cate['CateID'] : null,
                        'CateNo' => ($cate['CateNo']) ? $cate['CateID'] : null,
                        'CateValue' => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('norm_cate')->insert($arrInsertCate);
            }

        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $norm->NormID;
        return $json;
    }

    public function getCate(Request $request){
        $normCateList = NormCateList::where('Inactive', '=', 0)->get();
        $normCateValue = NormCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['NormCateList'] = $normCateList;
        $json['data']['NormCateValue'] = $normCateValue;
        return json_encode($json);
    }

    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = Norm::search($request);
//        $Norm = Norm::select('NormID', 'NormName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function delete($id)
    {
        $data = Norm::find($id);
        if ($data) {
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
            // set for old Parent
            if($data->ParentID){
                $parent = Norm::find($data->ParentID);
                $count = DB::table('norm')->where('ParentID', '=', $parent->NormID)->count();
                if($count < 2){
                    $parent->Detail = 1;
                    $parent->save();
                }
            }
            DB::table('norm_cate')->where('NormId', '=', $data->NormID)->delete();
            $data->delete();

        }
        NormCate::find($id)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public function deleteArray(Request $request)
    {
        $IDs = $request->post('array_id');
        foreach ($IDs as $id){
            $data = Norm::find($id);
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        DB::table('norm_cate')->whereIn('NormID', $IDs)->delete();
        Norm::destroy($IDs);
        $sql = "UPDATE norm
                SET Detail = 1
                WHERE NormID NOT IN (SELECT ParentID FROM (select ParentID from norm WHERE ParentID is not null) t)";
        DB::statement($sql);
        NormCate::whereIn('NormID',$IDs)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }


    public function getTableItem(Request $request){
        $query = NormTableItem::query();
        if($request->post('detail')){
            $query->where('detail',$request->post('detail'));
        }

        $query->where('NormTableID','=',$request->post('NormTableID'));
        $query->orderBy('NormTableItemID','ASC');
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $query->get();
        return json_encode($json);
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = Norm::query();
        $query->where('norm.ParentID', $ParentID);
        $query->groupBy('norm.NormID');
        $data = $query->orderBy('norm.NormNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function getNormAllot($id){
        $data = NormAllotMap::select('norm_allot.*', 'norm_allot_map.NormID', 'norm_allot_map.NormNo', 'norm_allot_map.NormName')
            ->join('norm_allot', 'norm_allot_map.NormAllotID', '=', 'norm_allot.NormAllotID')
            ->groupBy('norm_allot_map.NormAllotID')
            ->where('norm_allot_map.NormID', $id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function updateNormAllot(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $NormID = $request->post('NormID');
        $normAllotMap = $request->post('NormAllotMap');
        if ($NormID) {
            DB::beginTransaction();
            try {
                DB::table('norm_allot_map')->where('NormID', $NormID)->delete();
                $arrInsert = [];
                foreach ($normAllotMap as $map) {
                    $arrInsert[] = [
                        'NormAllotID' => $map['NormAllotID'],
                        'NormAllotNo' => $map['NormAllotNo'],
                        'NormAllotName' => $map['NormAllotName'],
                        'NormName' => $map['NormName'],
                        'NormNo' => $map['NormNo'],
                        'NormID' => $map['NormID']
                    ];
                }
                DB::table('norm_allot_map')->insert($arrInsert);
                DB::commit();
                $json['status'] = 1;
                $json['msg'] = 'Thành công';
                $json['data'] = $NormID;
            } catch (Exception $e) {
                DB::rollBack();
                $json['msg'] = $e->getMessage();
                throw new Exception($e->getMessage());
            }
        }

        return json_encode($json);
    }

}
