<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\NormLevel;
use Module\Listing\Models\NormLevelCateList;
use Module\Listing\Models\NormLevelCateValue;
use Illuminate\Support\Facades\Validator;

class NormLevelCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = NormLevelCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $normLevelCateList = NormLevelCateList::all();
        $json['data']['normLevelCateList'] = $normLevelCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:norm_level_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại nguồn vốn không được để trống',
            'CateNo.unique' => 'Mã loại nguồn vốn đã tồn tại',
            'CateName.required' => 'Tên loại nguồn vốn không được để trống',
        ]);
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Thất bại';
            $json['data'] = $validator->errors();
            return json_encode($json);
        }
        $detail = $request->detail;
        if(empty($master->ParentID)){
            $level = 1;
        } else {
            $query = NormLevelCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $normLevelCateList = new NormLevelCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $normLevelCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $normLevelCateValue = '';
            if($item->DataType == 3){
                $normLevelCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $normLevelCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $normLevelCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $normLevelCateList->CateID,
                'CateName'=> $normLevelCateList->CateName,
                'CateNo'=> $normLevelCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $normLevelCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('norm_level_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $normLevelCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = NormLevelCateList::find($id);
        $normLevelCateList = NormLevelCateList::all();
        $normLevelCateValue = NormLevelCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['normLevelCateList'] = $normLevelCateList;
        $json['data']['normLevelCateValue'] = $normLevelCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $normLevelCateList = NormLevelCateList::all();
        $data = NormLevelCateList::find($id);
        $normLevelCateValue = NormLevelCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['normLevelCateList'] = $normLevelCateList;
        $json['data']['normLevelCateValue'] = $normLevelCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $normLevelCateItem = NormLevelCateList::find($id);
        if($normLevelCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:normLevel_cate_list',
            ],[
                'CateName.required' => 'Tên loại nguồn vốn không được để trống',
                'CateNo.required' => 'Mã loại nguồn vốn không được để trống',
                'CateNo.unique' => 'Mã loại nguồn vốn đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($normLevelCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = NormLevelCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($normLevelCateItem->ParentID != $master->ParentID && $normLevelCateItem->ParentID){
            $ParentOld = NormLevelCateList::find($normLevelCateItem->ParentID);
            $count = NormLevelCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $normLevelCateItem->CateName = $master->CateName;
        $normLevelCateItem->CateNo = $master->CateNo;
        $normLevelCateItem->ParentID = $master->ParentID;
        $normLevelCateItem->ParentNo = $master->ParentNo;
        $normLevelCateItem->Level = $level;
        $normLevelCateItem->Inactive = $master->Inactive;
        $normLevelCateItem->save();
        NormLevelCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $normLevelCateValue = '';
            if($item->DataType == 3){
                $normLevelCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $normLevelCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $normLevelCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $normLevelCateItem->CateName,
                'CateNo'=> $normLevelCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $normLevelCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('norm_level_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $normLevelCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        NormLevelCateValue::where('CateID', $id)->delete();
        $data = NormLevelCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('norm_level_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = NormLevelCateList::find($data->ParentID);
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
        $normLevelCateList =  DB::table('norm_level_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($normLevelCateList as $normLevelCate ){
            if($normLevelCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            NormLevelCateValue::whereIn('CateID',$IDs)->delete();
            NormLevelCateList::destroy($IDs);
            $sql = "UPDATE norm_level_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from norm_level_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = NormLevelCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
