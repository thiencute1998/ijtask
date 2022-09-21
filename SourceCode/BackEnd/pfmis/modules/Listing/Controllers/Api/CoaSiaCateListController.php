<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaSia;
use Module\Listing\Models\CoaSiaCateList;
use Module\Listing\Models\CoaSiaCateValue;
use Illuminate\Support\Facades\Validator;

class CoaSiaCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CoaSiaCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $coaSiaCateList = CoaSiaCateList::all();
        $json['data']['coaSiaCateList'] = $coaSiaCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:coa_sia_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại HTTK bảo hiểm xã hội không được để trống',
            'CateNo.unique' => 'Mã loại HTTK bảo hiểm xã hội đã tồn tại',
            'CateName.required' => 'Tên loại HTTK bảo hiểm xã hội không được để trống',
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
            $query = CoaSiaCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $coaSiaCateList = new CoaSiaCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $coaSiaCateList->save();
        $arrayInsert = [];
        $detail = $request->detail;
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaSiaCateValue = '';
            if($item->DataType == 3){
                $coaSiaCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaSiaCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaSiaCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $coaSiaCateList->CateID,
                'CateName'=> $coaSiaCateList->CateName,
                'CateNo'=> $coaSiaCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaSiaCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_sia_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaSiaCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CoaSiaCateList::find($id);
        $coaSiaCateList = CoaSiaCateList::all();
        $coaSiaCateValue = CoaSiaCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaSiaCateList'] = $coaSiaCateList;
        $json['data']['coaSiaCateValue'] = $coaSiaCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $coaSiaCateList = CoaSiaCateList::all();
        $data = CoaSiaCateList::find($id);
        $coaSiaCateValue = CoaSiaCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaSiaCateList'] = $coaSiaCateList;
        $json['data']['coaSiaCateValue'] = $coaSiaCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;
        $detail = $request->detail;
        $coaSiaCateItem = CoaSiaCateList::find($id);
        if($coaSiaCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:coa_sia_cate_list',
            ],[
                'CateName.required' => 'Tên loại HTTK bảo hiểm xã hội không được để trống',
                'CateNo.required' => 'Mã loại HTTK bảo hiểm xã hội không được để trống',
                'CateNo.unique' => 'Mã loại HTTK bảo hiểm xã hội đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($coaSiaCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CoaSiaCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($coaSiaCateItem->ParentID != $master->ParentID && $coaSiaCateItem->ParentID){
            $ParentOld = CoaSiaCateList::find($coaSiaCateItem->ParentID);
            $count = CoaSiaCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $coaSiaCateItem->CateName = $master->CateName;
        $coaSiaCateItem->CateNo = $master->CateNo;
        $coaSiaCateItem->ParentID = $master->ParentID;
        $coaSiaCateItem->ParentNo = $master->ParentNo;
        $coaSiaCateItem->Level = $level;
        $coaSiaCateItem->Inactive = $master->Inactive;
        $coaSiaCateItem->save();
        CoaSiaCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaSiaCateValue = '';
            if($item->DataType == 3){
                $coaSiaCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaSiaCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaSiaCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $coaSiaCateItem->CateName,
                'CateNo'=> $coaSiaCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaSiaCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_sia_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaSiaCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CoaSiaCateValue::where('CateID', $id)->delete();
        $data = CoaSiaCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('coa_sia_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CoaSiaCateList::find($data->ParentID);
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
        $coaSiaCateList =  DB::table('coa_sia_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($coaSiaCateList as $coaSiaCate ){
            if($coaSiaCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CoaSiaCateValue::whereIn('CateID',$IDs)->delete();
            CoaSiaCateList::destroy($IDs);
            $sql = "UPDATE coa_sia_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from coa_sia_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CoaSiaCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
