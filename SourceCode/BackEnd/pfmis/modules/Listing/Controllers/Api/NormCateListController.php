<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Norm;
use Module\Listing\Models\NormCateList;
use Module\Listing\Models\NormCateValue;
use Illuminate\Support\Facades\Validator;

class NormCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = NormCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $normCateList = NormCateList::all();
        $json['data']['normCateList'] = $normCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:norm_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại chỉ tiêu định mực dự toán không được để trống',
            'CateNo.unique' => 'Mã loại chỉ tiêu định mực dự toán đã tồn tại',
            'CateName.required' => 'Tên loại chỉ tiêu định mực dự toán không được để trống',
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
            $query = NormCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $normCateList = new NormCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $normCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $normCateValue = '';
            if($item->DataType == 3){
                $normCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $normCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $normCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $normCateList->CateID,
                'CateName'=> $normCateList->CateName,
                'CateNo'=> $normCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $normCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('norm_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $normCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = NormCateList::find($id);
        $normCateList = NormCateList::all();
        $normCateValue = NormCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['normCateList'] = $normCateList;
        $json['data']['normCateValue'] = $normCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $normCateList = NormCateList::all();
        $data = NormCateList::find($id);
        $normCateValue = NormCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['normCateList'] = $normCateList;
        $json['data']['normCateValue'] = $normCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $normCateItem = NormCateList::find($id);
        if($normCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:norm_cate_list',
            ],[
                'CateName.required' => 'Tên loại chỉ tiêu định mực dự toán không được để trống',
                'CateNo.required' => 'Mã loại chỉ tiêu định mực dự toán không được để trống',
                'CateNo.unique' => 'Mã loại chỉ tiêu định mực dự toán đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($normCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = NormCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($normCateItem->ParentID != $master->ParentID && $normCateItem->ParentID){
            $ParentOld = NormCateList::find($normCateItem->ParentID);
            $count = NormCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $normCateItem->CateName = $master->CateName;
        $normCateItem->CateNo = $master->CateNo;
        $normCateItem->ParentID = $master->ParentID;
        $normCateItem->ParentNo = $master->ParentNo;
        $normCateItem->Level = $level;
        $normCateItem->Inactive = $master->Inactive;
        $normCateItem->save();
        NormCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $normCateValue = '';
            if($item->DataType == 3){
                $normCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $normCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $normCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $normCateItem->CateName,
                'CateNo'=> $normCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $normCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('norm_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $normCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        NormCateValue::where('CateID', $id)->delete();
        $data = NormCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('norm_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = NormCateList::find($data->ParentID);
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
        $normCateList =  DB::table('norm_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($normCateList as $normCate ){
            if($normCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            NormCateValue::whereIn('CateID',$IDs)->delete();
            NormCateList::destroy($IDs);
            $sql = "UPDATE norm_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from norm_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = NormCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
