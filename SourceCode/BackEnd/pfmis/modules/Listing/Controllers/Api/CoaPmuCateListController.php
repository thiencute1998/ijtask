<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaPmu;
use Module\Listing\Models\CoaPmuCateList;
use Module\Listing\Models\CoaPmuCateValue;
use Illuminate\Support\Facades\Validator;

class CoaPmuCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CoaPmuCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $coaPmuCateList = CoaPmuCateList::all();
        $json['data']['coaPmuCateList'] = $coaPmuCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:coa_pmu_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại HTTK ban quản lý dự án không được để trống',
            'CateNo.unique' => 'Mã loại HTTK ban quản lý dự án đã tồn tại',
            'CateName.required' => 'Tên loại HTTK ban quản lý dự án không được để trống',
        ]);
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Thất bại';
            $json['data'] = $validator->errors();
            return json_encode($json);
        }
        if(empty($master->ParentID)){
            $level = 1;
        } else {
            $query = CoaPmuCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $coaPmuCateList = new CoaPmuCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $coaPmuCateList->save();
        $arrayInsert = [];
        $detail = $request->detail;
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaPmuCateValue = '';
            if($item->DataType == 3){
                $coaPmuCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaPmuCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaPmuCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $coaPmuCateList->CateID,
                'CateName'=> $coaPmuCateList->CateName,
                'CateNo'=> $coaPmuCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaPmuCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_pmu_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaPmuCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CoaPmuCateList::find($id);
        $coaPmuCateList = CoaPmuCateList::all();
        $coaPmuCateValue = CoaPmuCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaPmuCateList'] = $coaPmuCateList;
        $json['data']['coaPmuCateValue'] = $coaPmuCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $coaPmuCateList = CoaPmuCateList::all();
        $data = CoaPmuCateList::find($id);
        $coaPmuCateValue = CoaPmuCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaPmuCateList'] = $coaPmuCateList;
        $json['data']['coaPmuCateValue'] = $coaPmuCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;
        $detail = $request->detail;
        $coaPmuCateItem = CoaPmuCateList::find($id);
        if($coaPmuCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:coa_pmu_cate_list',
            ],[
                'CateName.required' => 'Tên loại HTTK ban quản lý dự án không được để trống',
                'CateNo.required' => 'Mã loại HTTK ban quản lý dự án không được để trống',
                'CateNo.unique' => 'Mã loại HTTK ban quản lý dự án đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($coaPmuCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CoaPmuCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($coaPmuCateItem->ParentID != $master->ParentID && $coaPmuCateItem->ParentID){
            $ParentOld = CoaPmuCateList::find($coaPmuCateItem->ParentID);
            $count = CoaPmuCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $coaPmuCateItem->CateName = $master->CateName;
        $coaPmuCateItem->CateNo = $master->CateNo;
        $coaPmuCateItem->ParentID = $master->ParentID;
        $coaPmuCateItem->ParentNo = $master->ParentNo;
        $coaPmuCateItem->Level = $level;
        $coaPmuCateItem->Inactive = $master->Inactive;
        $coaPmuCateItem->save();
        CoaPmuCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaPmuCateValue = '';
            if($item->DataType == 3){
                $coaPmuCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaPmuCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaPmuCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $coaPmuCateItem->CateName,
                'CateNo'=> $coaPmuCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaPmuCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_pmu_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaPmuCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CoaPmuCateValue::where('CateID', $id)->delete();
        $data = CoaPmuCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('coa_pmu_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CoaPmuCateList::find($data->ParentID);
                    $Parent->Detail = 1;
                    $Parent->save();
                }
            }
            $data->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = $id;

            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = $id;

            return json_encode($json);
        }
    }

    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        $coaPmuCateList =  DB::table('coa_pmu_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($coaPmuCateList as $coaPmuCate ){
            if($coaPmuCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CoaPmuCateValue::whereIn('CateID',$IDs)->delete();
            CoaPmuCateList::destroy($IDs);
            $sql = "UPDATE coa_pmu_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from coa_pmu_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CoaPmuCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
