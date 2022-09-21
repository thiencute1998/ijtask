<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaAnu;
use Module\Listing\Models\CoaAnuCateList;
use Module\Listing\Models\CoaAnuCateValue;
use Illuminate\Support\Facades\Validator;

class CoaAnuCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CoaAnuCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $coaAnuCateList = CoaAnuCateList::all();
        $json['data']['coaAnuCateList'] = $coaAnuCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:coa_anu_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại HTTK hành chính sự nghiệp không được để trống',
            'CateNo.unique' => 'Mã loại HTTK hành chính sự nghiệp đã tồn tại',
            'CateName.required' => 'Tên loại HTTK hành chính sự nghiệp không được để trống',
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
            $query = CoaAnuCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $coaAnuCateList = new CoaAnuCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $coaAnuCateList->save();
        $arrayInsert = [];
        $detail = $request->detail;
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaAnuCateValue = '';
            if($item->DataType == 3){
                $coaAnuCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaAnuCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaAnuCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $coaAnuCateList->CateID,
                'CateName'=> $coaAnuCateList->CateName,
                'CateNo'=> $coaAnuCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaAnuCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_anu_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaAnuCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CoaAnuCateList::find($id);
        $coaAnuCateList = CoaAnuCateList::all();
        $coaAnuCateValue = CoaAnuCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaAnuCateList'] = $coaAnuCateList;
        $json['data']['coaAnuCateValue'] = $coaAnuCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $coaAnuCateList = CoaAnuCateList::all();
        $data = CoaAnuCateList::find($id);
        $coaAnuCateValue = CoaAnuCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaAnuCateList'] = $coaAnuCateList;
        $json['data']['coaAnuCateValue'] = $coaAnuCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;
        $detail = $request->detail;
        $coaAnuCateItem = CoaAnuCateList::find($id);
        if($coaAnuCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:coa_anu_cate_list',
            ],[
                'CateName.required' => 'Tên loại HTTK hành chính sự nghiệp không được để trống',
                'CateNo.required' => 'Mã loại HTTK hành chính sự nghiệp không được để trống',
                'CateNo.unique' => 'Mã loại HTTK hành chính sự nghiệp đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($coaAnuCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CoaAnuCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($coaAnuCateItem->ParentID != $master->ParentID && $coaAnuCateItem->ParentID){
            $ParentOld = CoaAnuCateList::find($coaAnuCateItem->ParentID);
            $count = CoaAnuCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $coaAnuCateItem->CateName = $master->CateName;
        $coaAnuCateItem->CateNo = $master->CateNo;
        $coaAnuCateItem->ParentID = $master->ParentID;
        $coaAnuCateItem->ParentNo = $master->ParentNo;
        $coaAnuCateItem->Level = $level;
        $coaAnuCateItem->Inactive = $master->Inactive;
        $coaAnuCateItem->save();
        CoaAnuCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaAnuCateValue = '';
            if($item->DataType == 3){
                $coaAnuCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaAnuCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaAnuCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $coaAnuCateItem->CateName,
                'CateNo'=> $coaAnuCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaAnuCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_anu_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaAnuCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CoaAnuCateValue::where('CateID', $id)->delete();
        $data = CoaAnuCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('coa_anu_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CoaAnuCateList::find($data->ParentID);
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
        $coaAnuCateList =  DB::table('coa_anu_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($coaAnuCateList as $coaAnuCate ){
            if($coaAnuCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CoaAnuCateValue::whereIn('CateID',$IDs)->delete();
            CoaAnuCateList::destroy($IDs);
            $sql = "UPDATE coa_anu_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from coa_anu_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CoaAnuCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
