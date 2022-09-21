<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaPcf;
use Module\Listing\Models\CoaPcfCateList;
use Module\Listing\Models\CoaPcfCateValue;
use Illuminate\Support\Facades\Validator;

class CoaPcfCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CoaPcfCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $coaPcfCateList = CoaPcfCateList::all();
        $json['data']['coaPcfCateList'] = $coaPcfCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:coa_pcf_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại HTTK quỹ tín dụng nhân dân không được để trống',
            'CateNo.unique' => 'Mã loại HTTK quỹ tín dụng nhân dân đã tồn tại',
            'CateName.required' => 'Tên loại HTTK quỹ tín dụng nhân dân không được để trống',
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
            $query = CoaPcfCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $coaPcfCateList = new CoaPcfCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $coaPcfCateList->save();
        $arrayInsert = [];
        $detail = $request->detail;
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaPcfCateValue = '';
            if($item->DataType == 3){
                $coaPcfCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaPcfCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaPcfCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $coaPcfCateList->CateID,
                'CateName'=> $coaPcfCateList->CateName,
                'CateNo'=> $coaPcfCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaPcfCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_pcf_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaPcfCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CoaPcfCateList::find($id);
        $coaPcfCateList = CoaPcfCateList::all();
        $coaPcfCateValue = CoaPcfCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaPcfCateList'] = $coaPcfCateList;
        $json['data']['coaPcfCateValue'] = $coaPcfCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $coaPcfCateList = CoaPcfCateList::all();
        $data = CoaPcfCateList::find($id);
        $coaPcfCateValue = CoaPcfCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaPcfCateList'] = $coaPcfCateList;
        $json['data']['coaPcfCateValue'] = $coaPcfCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;
        $detail = $request->detail;
        $coaPcfCateItem = CoaPcfCateList::find($id);
        if($coaPcfCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:coa_pcf_cate_list',
            ],[
                'CateName.required' => 'Tên loại HTTK quỹ tín dụng nhân dân không được để trống',
                'CateNo.required' => 'Mã loại HTTK quỹ tín dụng nhân dân không được để trống',
                'CateNo.unique' => 'Mã loại HTTK quỹ tín dụng nhân dân đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($coaPcfCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CoaPcfCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($coaPcfCateItem->ParentID != $master->ParentID && $coaPcfCateItem->ParentID){
            $ParentOld = CoaPcfCateList::find($coaPcfCateItem->ParentID);
            $count = CoaPcfCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $coaPcfCateItem->CateName = $master->CateName;
        $coaPcfCateItem->CateNo = $master->CateNo;
        $coaPcfCateItem->ParentID = $master->ParentID;
        $coaPcfCateItem->ParentNo = $master->ParentNo;
        $coaPcfCateItem->Level = $level;
        $coaPcfCateItem->Inactive = $master->Inactive;
        $coaPcfCateItem->save();
        CoaPcfCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaPcfCateValue = '';
            if($item->DataType == 3){
                $coaPcfCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaPcfCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaPcfCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $coaPcfCateItem->CateName,
                'CateNo'=> $coaPcfCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaPcfCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_pcf_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaPcfCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CoaPcfCateValue::where('CateID', $id)->delete();
        $data = CoaPcfCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('coa_pcf_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CoaPcfCateList::find($data->ParentID);
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
        $coaPcfCateList =  DB::table('coa_pcf_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($coaPcfCateList as $coaPcfCate ){
            if($coaPcfCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CoaPcfCateValue::whereIn('CateID',$IDs)->delete();
            CoaPcfCateList::destroy($IDs);
            $sql = "UPDATE coa_pcf_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from coa_pcf_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CoaPcfCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
