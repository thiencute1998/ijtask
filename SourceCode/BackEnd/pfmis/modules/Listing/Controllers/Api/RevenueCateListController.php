<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Revenue;
use Module\Listing\Models\RevenueCateList;
use Module\Listing\Models\RevenueCateValue;
use Illuminate\Support\Facades\Validator;

class RevenueCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = RevenueCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $revenueCateList = RevenueCateList::all();
        $json['data']['revenueCateList'] = $revenueCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:revenue_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại khoản thu không được để trống',
            'CateNo.unique' => 'Mã loại khoản thu đã tồn tại',
            'CateName.required' => 'Tên loại khoản thu không được để trống',
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
            $path = $master->CateNo;
        } else {
            $query = RevenueCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
            $path = $parent->Path . '#' . $master->CateNo;
        }
        $revenueCateList = new RevenueCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Path' => $path,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $revenueCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $revenueCateValue = '';
            if($item->DataType == 3){
                $revenueCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $revenueCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $revenueCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $revenueCateList->CateID,
                'CateName'=> $revenueCateList->CateName,
                'CateNo'=> $revenueCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $revenueCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('revenue_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $revenueCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = RevenueCateList::find($id);
        $revenueCateList = RevenueCateList::all();
        $revenueCateValue = RevenueCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['revenueCateList'] = $revenueCateList;
        $json['data']['revenueCateValue'] = $revenueCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $revenueCateList = RevenueCateList::all();
        $data = RevenueCateList::find($id);
        $revenueCateValue = RevenueCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['revenueCateList'] = $revenueCateList;
        $json['data']['revenueCateValue'] = $revenueCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){




        $master = (object)$request->master;

        $detail = $request->detail;
        $revenueCateItem = RevenueCateList::find($id);
        if($revenueCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:revenue_cate_list',
            ],[
                'CateName.required' => 'Tên loại khoản thu không được để trống',
                'CateNo.required' => 'Mã loại khoản thu không được để trống',
                'CateNo.unique' => 'Mã loại khoản thu đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        // Không sửa No, Parent, CateValue của bản ghi tổng hợp
        if($revenueCateItem->Detail == 0){
            if($revenueCateItem->CateNo != $master->CateNo || $revenueCateItem->ParentID != $master->ParentID || $master->RevenueCateValue != null ){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }

        // set for newParent
        if($master->ParentID){
            $Parent = RevenueCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $path = $Parent->Path . '#' . $master->CateNo;
            $Parent->save();
        } else {
            $level = 1;
            $path = $master->CateNo;
        }
        // set for old Parent
        if($revenueCateItem->ParentID != $master->ParentID && $revenueCateItem->ParentID){
            $ParentOld = RevenueCateList::find($revenueCateItem->ParentID);
            $count = RevenueCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }

        $revenueCateItem->CateName = $master->CateName;
        $revenueCateItem->CateNo = $master->CateNo;
        $revenueCateItem->ParentID = $master->ParentID;
        $revenueCateItem->ParentNo = $master->ParentNo;
        $revenueCateItem->Level = $level;
        $revenueCateItem->Path = $path;
        $revenueCateItem->Inactive = $master->Inactive;
        $revenueCateItem->save();
        RevenueCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $revenueCateValue = '';
            if($item->DataType == 3){
                $revenueCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $revenueCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $revenueCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $revenueCateItem->CateName,
                'CateNo'=> $revenueCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $revenueCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('revenue_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $revenueCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        RevenueCateValue::where('CateID', $id)->delete();
        $data = RevenueCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('revenue_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = RevenueCateList::find($data->ParentID);
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
        $revenueCateList =  DB::table('revenue_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($revenueCateList as $revenueCate ){
            if($revenueCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            RevenueCateValue::whereIn('CateID',$IDs)->delete();
            RevenueCateList::destroy($IDs);
            $sql = "UPDATE revenue_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from revenue_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = RevenueCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
