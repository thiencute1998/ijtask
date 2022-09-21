<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaCon;
use Module\Listing\Models\CoaConCateList;
use Module\Listing\Models\CoaConCateValue;
use Illuminate\Support\Facades\Validator;

class CoaConCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CoaConCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $coaConCateList = CoaConCateList::all();
        $json['data']['coaConCateList'] = $coaConCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:coa_con_cate_list',
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
            $query = CoaConCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $coaConCateList = new CoaConCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $coaConCateList->save();
        $arrayInsert = [];
        $detail = $request->detail;
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaConCateValue = '';
            if($item->DataType == 3){
                $coaConCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaConCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaConCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $coaConCateList->CateID,
                'CateName'=> $coaConCateList->CateName,
                'CateNo'=> $coaConCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaConCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_con_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaConCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CoaConCateList::find($id);
        $coaConCateList = CoaConCateList::all();
        $coaConCateValue = CoaConCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaConCateList'] = $coaConCateList;
        $json['data']['coaConCateValue'] = $coaConCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $coaConCateList = CoaConCateList::all();
        $data = CoaConCateList::find($id);
        $coaConCateValue = CoaConCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaConCateList'] = $coaConCateList;
        $json['data']['coaConCateValue'] = $coaConCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;
        $detail = $request->detail;
        $coaConCateItem = CoaConCateList::find($id);
        if($coaConCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:coa_con_cate_list',
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
        if($coaConCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CoaConCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($coaConCateItem->ParentID != $master->ParentID && $coaConCateItem->ParentID){
            $ParentOld = CoaConCateList::find($coaConCateItem->ParentID);
            $count = CoaConCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $coaConCateItem->CateName = $master->CateName;
        $coaConCateItem->CateNo = $master->CateNo;
        $coaConCateItem->ParentID = $master->ParentID;
        $coaConCateItem->ParentNo = $master->ParentNo;
        $coaConCateItem->Level = $level;
        $coaConCateItem->Inactive = $master->Inactive;
        $coaConCateItem->save();
        CoaConCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaConCateValue = '';
            if($item->DataType == 3){
                $coaConCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaConCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaConCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $coaConCateItem->CateName,
                'CateNo'=> $coaConCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaConCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_con_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaConCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CoaConCateValue::where('CateID', $id)->delete();
        $data = CoaConCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('coa_con_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CoaConCateList::find($data->ParentID);
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
        $coaConCateList =  DB::table('coa_con_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($coaConCateList as $coaConCate ){
            if($coaConCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CoaConCateValue::whereIn('CateID',$IDs)->delete();
            CoaConCateList::destroy($IDs);
            $sql = "UPDATE coa_con_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from coa_con_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CoaConCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
