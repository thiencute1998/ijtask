<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\NormAllot;
use Module\Listing\Models\NormAllotCateList;
use Module\Listing\Models\NormAllotCateValue;
use Illuminate\Support\Facades\Validator;

class NormAllotCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = NormAllotCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $normAllotCateList = NormAllotCateList::all();
        $json['data']['normAllotCateList'] = $normAllotCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:norm_allot_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại HTTK hợp nhất không được để trống',
            'CateNo.unique' => 'Mã loại HTTK hợp nhất đã tồn tại',
            'CateName.required' => 'Tên loại HTTK hợp nhất không được để trống',
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
            $query = NormAllotCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $normAllotCateList = new NormAllotCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $normAllotCateList->save();
        $arrayInsert = [];
        $detail = $request->detail;
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $normAllotCateValue = '';
            if($item->DataType == 3){
                $normAllotCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $normAllotCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $normAllotCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $normAllotCateList->CateID,
                'CateName'=> $normAllotCateList->CateName,
                'CateNo'=> $normAllotCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $normAllotCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('norm_allot_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $normAllotCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = NormAllotCateList::find($id);
        $normAllotCateList = NormAllotCateList::all();
        $normAllotCateValue = NormAllotCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['normAllotCateList'] = $normAllotCateList;
        $json['data']['normAllotCateValue'] = $normAllotCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $normAllotCateList = NormAllotCateList::all();
        $data = NormAllotCateList::find($id);
        $normAllotCateValue = NormAllotCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['normAllotCateList'] = $normAllotCateList;
        $json['data']['normAllotCateValue'] = $normAllotCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;
        $detail = $request->detail;
        $normAllotCateItem = NormAllotCateList::find($id);
        if($normAllotCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:norm_allot_cate_list',
            ],[
                'CateName.required' => 'Tên loại HTTK hợp nhất không được để trống',
                'CateNo.required' => 'Mã loại HTTK hợp nhất không được để trống',
                'CateNo.unique' => 'Mã loại HTTK hợp nhất đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($normAllotCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = NormAllotCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($normAllotCateItem->ParentID != $master->ParentID && $normAllotCateItem->ParentID){
            $ParentOld = NormAllotCateList::find($normAllotCateItem->ParentID);
            $count = NormAllotCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $normAllotCateItem->CateName = $master->CateName;
        $normAllotCateItem->CateNo = $master->CateNo;
        $normAllotCateItem->ParentID = $master->ParentID;
        $normAllotCateItem->ParentNo = $master->ParentNo;
        $normAllotCateItem->Level = $level;
        $normAllotCateItem->Inactive = $master->Inactive;
        $normAllotCateItem->save();
        NormAllotCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $normAllotCateValue = '';
            if($item->DataType == 3){
                $normAllotCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $normAllotCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $normAllotCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $normAllotCateItem->CateName,
                'CateNo'=> $normAllotCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $normAllotCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('norm_allot_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $normAllotCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        NormAllotCateValue::where('CateID', $id)->delete();
        $data = NormAllotCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('norm_allot_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = NormAllotCateList::find($data->ParentID);
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
        $normAllotCateList =  DB::table('norm_allot_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($normAllotCateList as $normAllotCate ){
            if($normAllotCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            NormAllotCateValue::whereIn('CateID',$IDs)->delete();
            NormAllotCateList::destroy($IDs);
            $sql = "UPDATE norm_allot_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from norm_allot_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = NormAllotCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
