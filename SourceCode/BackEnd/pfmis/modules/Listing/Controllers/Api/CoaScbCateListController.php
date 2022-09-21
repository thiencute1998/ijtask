<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaScb;
use Module\Listing\Models\CoaScbCateList;
use Module\Listing\Models\CoaScbCateValue;
use Illuminate\Support\Facades\Validator;

class CoaScbCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CoaScbCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $coaScbCateList = CoaScbCateList::all();
        $json['data']['coaScbCateList'] = $coaScbCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:coa_scb_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại HTTK xã hội không được để trống',
            'CateNo.unique' => 'Mã loại HTTK xã hội đã tồn tại',
            'CateName.required' => 'Tên loại HTTK xã hội không được để trống',
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
            $query = CoaScbCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $coaScbCateList = new CoaScbCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $coaScbCateList->save();
        $arrayInsert = [];
        $detail = $request->detail;
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaScbCateValue = '';
            if($item->DataType == 3){
                $coaScbCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaScbCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaScbCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $coaScbCateList->CateID,
                'CateName'=> $coaScbCateList->CateName,
                'CateNo'=> $coaScbCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaScbCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_scb_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaScbCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CoaScbCateList::find($id);
        $coaScbCateList = CoaScbCateList::all();
        $coaScbCateValue = CoaScbCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaScbCateList'] = $coaScbCateList;
        $json['data']['coaScbCateValue'] = $coaScbCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $coaScbCateList = CoaScbCateList::all();
        $data = CoaScbCateList::find($id);
        $coaScbCateValue = CoaScbCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaScbCateList'] = $coaScbCateList;
        $json['data']['coaScbCateValue'] = $coaScbCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;
        $detail = $request->detail;
        $coaScbCateItem = CoaScbCateList::find($id);
        if($coaScbCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:coa_scb_cate_list',
            ],[
                'CateName.required' => 'Tên loại HTTK xã hội không được để trống',
                'CateNo.required' => 'Mã loại HTTK xã hội không được để trống',
                'CateNo.unique' => 'Mã loại HTTK xã hội đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($coaScbCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CoaScbCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($coaScbCateItem->ParentID != $master->ParentID && $coaScbCateItem->ParentID){
            $ParentOld = CoaScbCateList::find($coaScbCateItem->ParentID);
            $count = CoaScbCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $coaScbCateItem->CateName = $master->CateName;
        $coaScbCateItem->CateNo = $master->CateNo;
        $coaScbCateItem->ParentID = $master->ParentID;
        $coaScbCateItem->ParentNo = $master->ParentNo;
        $coaScbCateItem->Level = $level;
        $coaScbCateItem->Inactive = $master->Inactive;
        $coaScbCateItem->save();
        CoaScbCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaScbCateValue = '';
            if($item->DataType == 3){
                $coaScbCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaScbCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaScbCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $coaScbCateItem->CateName,
                'CateNo'=> $coaScbCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaScbCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_scb_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaScbCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CoaScbCateValue::where('CateID', $id)->delete();
        $data = CoaScbCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('coa_scb_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CoaScbCateList::find($data->ParentID);
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
        $coaScbCateList =  DB::table('coa_scb_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($coaScbCateList as $coaScbCate ){
            if($coaScbCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CoaScbCateValue::whereIn('CateID',$IDs)->delete();
            CoaScbCateList::destroy($IDs);
            $sql = "UPDATE coa_scb_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from coa_scb_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CoaScbCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
