<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Item;
use Module\Listing\Models\ItemCateList;
use Module\Listing\Models\ItemCateValue;
use Illuminate\Support\Facades\Validator;

class ItemCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = ItemCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $itemCateList = ItemCateList::all();
        $json['data']['itemCateList'] = $itemCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:item_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại vật tư - hàng hóa - dịch vụ không được để trống',
            'CateNo.unique' => 'Mã loại vật tư - hàng hóa - dịch vụ đã tồn tại',
            'CateName.required' => 'Tên loại vật tư - hàng hóa - dịch vụ không được để trống',
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
            $query = ItemCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $itemCateList = new ItemCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $itemCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $itemCateValue = '';
            if($item->DataType == 3){
                $itemCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $itemCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $itemCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $itemCateList->CateID,
                'CateName'=> $itemCateList->CateName,
                'CateNo'=> $itemCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $itemCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('item_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $itemCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = ItemCateList::find($id);
        $itemCateList = ItemCateList::all();
        $itemCateValue = ItemCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['itemCateList'] = $itemCateList;
        $json['data']['itemCateValue'] = $itemCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $itemCateList = ItemCateList::all();
        $data = ItemCateList::find($id);
        $itemCateValue = ItemCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['itemCateList'] = $itemCateList;
        $json['data']['itemCateValue'] = $itemCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $itemCateItem = ItemCateList::find($id);
        if($itemCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:item_cate_list',
            ],[
                'CateName.required' => 'Tên loại vật tư - hàng hóa - dịch vụ không được để trống',
                'CateNo.required' => 'Mã loại vật tư - hàng hóa - dịch vụ không được để trống',
                'CateNo.unique' => 'Mã loại vật tư - hàng hóa - dịch vụ đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($itemCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = ItemCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($itemCateItem->ParentID != $master->ParentID && $itemCateItem->ParentID){
            $ParentOld = ItemCateList::find($itemCateItem->ParentID);
            $count = ItemCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $itemCateItem->CateName = $master->CateName;
        $itemCateItem->CateNo = $master->CateNo;
        $itemCateItem->ParentID = $master->ParentID;
        $itemCateItem->ParentNo = $master->ParentNo;
        $itemCateItem->Level = $level;
        $itemCateItem->Inactive = $master->Inactive;
        $itemCateItem->save();
        ItemCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $itemCateValue = '';
            if($item->DataType == 3){
                $itemCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $itemCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $itemCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $itemCateItem->CateName,
                'CateNo'=> $itemCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $itemCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('item_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $itemCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        ItemCateValue::where('CateID', $id)->delete();
        $data = ItemCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('item_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = ItemCateList::find($data->ParentID);
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
        $itemCateList =  DB::table('item_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($itemCateList as $itemCate ){
            if($itemCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            ItemCateValue::whereIn('CateID',$IDs)->delete();
            ItemCateList::destroy($IDs);
            $sql = "UPDATE item_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from item_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = ItemCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
