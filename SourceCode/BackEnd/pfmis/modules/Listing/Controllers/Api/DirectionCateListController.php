<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Direction;
use Module\Listing\Models\DirectionCateList;
use Module\Listing\Models\DirectionCateValue;
use Illuminate\Support\Facades\Validator;

class DirectionCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = DirectionCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $directionCateList = DirectionCateList::all();
        $json['data']['directionCateList'] = $directionCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:direction_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại chỉ thị không được để trống',
            'CateNo.unique' => 'Mã loại chỉ thị đã tồn tại',
            'CateName.required' => 'Tên loại chỉ thị không được để trống',
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
            $query = DirectionCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $directionCateList = new DirectionCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $directionCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $directionCateValue = '';
            if($item->DataType == 3){
                $directionCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $directionCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $directionCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $directionCateList->CateID,
                'CateName'=> $directionCateList->CateName,
                'CateNo'=> $directionCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $directionCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('direction_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $directionCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = DirectionCateList::find($id);
        $directionCateList = DirectionCateList::all();
        $directionCateValue = DirectionCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['directionCateList'] = $directionCateList;
        $json['data']['directionCateValue'] = $directionCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $directionCateList = DirectionCateList::all();
        $data = DirectionCateList::find($id);
        $directionCateValue = DirectionCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['directionCateList'] = $directionCateList;
        $json['data']['directionCateValue'] = $directionCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $directionCateItem = DirectionCateList::find($id);
        if($directionCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:direction_cate_list',
            ],[
                'CateName.required' => 'Tên loại chỉ thị không được để trống',
                'CateNo.required' => 'Mã loại chỉ thị không được để trống',
                'CateNo.unique' => 'Mã loại chỉ thị đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($directionCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if(empty($master->ParentID)){
            $level = 1;
        } else {
            $Parent =DirectionCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $Parent->save();
            $level = $Parent->Level+ 1;
        }
        // set for old Parent
        if($directionCateItem->ParentID != $request->ParentID && $directionCateItem->ParentID != null){
            $ParentOld = DirectionCateList::find($directionCateItem->ParentID);
            $count = DirectionCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $directionCateItem->CateName = $master->CateName;
        $directionCateItem->CateNo = $master->CateNo;
        $directionCateItem->ParentID = $master->ParentID;
        $directionCateItem->ParentNo = $master->ParentNo;
        $directionCateItem->Level = $level;
        $directionCateItem->Inactive = $master->Inactive;
        $directionCateItem->save();
        DirectionCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $directionCateValue = '';
            if($item->DataType == 3){
                $directionCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $directionCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $directionCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $directionCateItem->CateName,
                'CateNo'=> $directionCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $directionCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('direction_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $id;
        return json_encode($json);
    }

    public function delete($id){
        DirectionCateValue::where('CateID', $id)->delete();
        $data = DirectionCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            $count = DB::table('direction_cate_list')->where('ParentID', '=', $data->ParentID)->count();
            if($count < 2){
                $Parent = DirectionCateList::find($data->ParentID);
                $Parent->Detail = 1;
                $Parent->save();
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
        $directionCateList =  DB::table('direction_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($directionCateList as $directionCate ){
            if($directionCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            DirectionCateValue::whereIn('CateID',$IDs)->delete();
            DirectionCateList::destroy($IDs);
            $sql = "UPDATE direction_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from direction_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = DirectionCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
