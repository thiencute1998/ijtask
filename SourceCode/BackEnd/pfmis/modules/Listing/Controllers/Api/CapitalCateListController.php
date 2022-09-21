<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Capital;
use Module\Listing\Models\CapitalCateList;
use Module\Listing\Models\CapitalCateValue;
use Illuminate\Support\Facades\Validator;

class CapitalCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CapitalCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $capitalCateList = CapitalCateList::all();
        $json['data']['capitalCateList'] = $capitalCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:capital_cate_list',
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
            $path = $master->CateNo;
        } else {
            $query = CapitalCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
            $path = $parent->Path . '#' . $master->CateNo;
        }
        $capitalCateList = new CapitalCateList([
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
        $capitalCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $capitalCateValue = '';
            if($item->DataType == 3){
                $capitalCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $capitalCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $capitalCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $capitalCateList->CateID,
                'CateName'=> $capitalCateList->CateName,
                'CateNo'=> $capitalCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $capitalCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('capital_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $capitalCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CapitalCateList::find($id);
        $capitalCateList = CapitalCateList::all();
        $capitalCateValue = CapitalCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['capitalCateList'] = $capitalCateList;
        $json['data']['capitalCateValue'] = $capitalCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $capitalCateList = CapitalCateList::all();
        $data = CapitalCateList::find($id);
        $capitalCateValue = CapitalCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['capitalCateList'] = $capitalCateList;
        $json['data']['capitalCateValue'] = $capitalCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){




        $master = (object)$request->master;

        $detail = $request->detail;
        $capitalCateItem = CapitalCateList::find($id);
        if($capitalCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:capital_cate_list',
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
        // Không sửa No, Parent, CateValue của bản ghi tổng hợp
        if($capitalCateItem->Detail == 0){
            if($capitalCateItem->CateNo != $master->CateNo || $capitalCateItem->ParentID != $master->ParentID || $master->CapitalCateValue != null ){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }

        // set for newParent
        if($master->ParentID){
            $Parent = CapitalCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $path = $Parent->Path . '#' . $master->CateNo;
            $Parent->save();
        } else {
            $level = 1;
            $path = $master->CateNo;
        }
        // set for old Parent
        if($capitalCateItem->ParentID != $master->ParentID && $capitalCateItem->ParentID){
            $ParentOld = CapitalCateList::find($capitalCateItem->ParentID);
            $count = CapitalCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }

        $capitalCateItem->CateName = $master->CateName;
        $capitalCateItem->CateNo = $master->CateNo;
        $capitalCateItem->ParentID = $master->ParentID;
        $capitalCateItem->ParentNo = $master->ParentNo;
        $capitalCateItem->Level = $level;
        $capitalCateItem->Path = $path;
        $capitalCateItem->Inactive = $master->Inactive;
        $capitalCateItem->save();
        CapitalCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $capitalCateValue = '';
            if($item->DataType == 3){
                $capitalCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $capitalCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $capitalCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $capitalCateItem->CateName,
                'CateNo'=> $capitalCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $capitalCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('capital_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $capitalCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CapitalCateValue::where('CateID', $id)->delete();
        $data = CapitalCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('capital_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CapitalCateList::find($data->ParentID);
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
        $capitalCateList =  DB::table('capital_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($capitalCateList as $capitalCate ){
            if($capitalCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CapitalCateValue::whereIn('CateID',$IDs)->delete();
            CapitalCateList::destroy($IDs);
            $sql = "UPDATE capital_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from capital_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CapitalCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
