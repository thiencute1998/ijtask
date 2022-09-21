<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Expense;
use Module\Listing\Models\SbiCategoryCateList;
use Module\Listing\Models\SbiCategoryCateValue;
use Illuminate\Support\Facades\Validator;

class SbiCategoryCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = SbiCategoryCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $sbiCategoryCateList = SbiCategoryCateList::all();
        $json['data']['sbiCategoryCateList'] = $sbiCategoryCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:sbi_category_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại -loại khoản không được để trống',
            'CateNo.unique' => 'Mã loại -loại khoản đã tồn tại',
            'CateName.required' => 'Tên loại -loại khoản không được để trống',
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
            $query = SbiCategoryCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $sbiCategoryCateList = new SbiCategoryCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $sbiCategoryCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $sbiCategoryCateValue = '';
            if($item->DataType == 3){
                $sbiCategoryCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $sbiCategoryCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $sbiCategoryCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $sbiCategoryCateList->CateID,
                'CateName'=> $sbiCategoryCateList->CateName,
                'CateNo'=> $sbiCategoryCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $sbiCategoryCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('sbi_category_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $sbiCategoryCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = SbiCategoryCateList::find($id);
        $sbiCategoryCateList = SbiCategoryCateList::all();
        $sbiCategoryCateValue = SbiCategoryCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['sbiCategoryCateList'] = $sbiCategoryCateList;
        $json['data']['sbiCategoryCateValue'] = $sbiCategoryCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $sbiCategoryCateList = SbiCategoryCateList::all();
        $data = SbiCategoryCateList::find($id);
        $sbiCategoryCateValue = SbiCategoryCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['sbiCategoryCateList'] = $sbiCategoryCateList;
        $json['data']['sbiCategoryCateValue'] = $sbiCategoryCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $sbiCategoryCateItem = SbiCategoryCateList::find($id);
        if($sbiCategoryCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:sbi_category_cate_list',
            ],[
                'CateName.required' => 'Tên loại -loại khoản không được để trống',
                'CateNo.required' => 'Mã loại -loại khoản không được để trống',
                'CateNo.unique' => 'Mã loại -loại khoản đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($sbiCategoryCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = SbiCategoryCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($sbiCategoryCateItem->ParentID != $master->ParentID && $sbiCategoryCateItem->ParentID){
            $ParentOld = SbiCategoryCateList::find($sbiCategoryCateItem->ParentID);
            $count = SbiCategoryCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $sbiCategoryCateItem->CateName = $master->CateName;
        $sbiCategoryCateItem->CateNo = $master->CateNo;
        $sbiCategoryCateItem->ParentID = $master->ParentID;
        $sbiCategoryCateItem->ParentNo = $master->ParentNo;
        $sbiCategoryCateItem->Level = $level;
        $sbiCategoryCateItem->Inactive = $master->Inactive;
        $sbiCategoryCateItem->save();
        SbiCategoryCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $sbiCategoryCateValue = '';
            if($item->DataType == 3){
                $sbiCategoryCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $sbiCategoryCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $sbiCategoryCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $sbiCategoryCateItem->CateName,
                'CateNo'=> $sbiCategoryCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $sbiCategoryCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('sbi_category_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $sbiCategoryCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        SbiCategoryCateValue::where('CateID', $id)->delete();
        $data = SbiCategoryCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('sbi_category_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = SbiCategoryCateList::find($data->ParentID);
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
        $sbiCategoryCateList =  DB::table('sbi_category_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($sbiCategoryCateList as $sbiCategoryCate ){
            if($sbiCategoryCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            SbiCategoryCateValue::whereIn('CateID',$IDs)->delete();
            SbiCategoryCateList::destroy($IDs);
            $sql = "UPDATE sbi_category_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from sbi_category_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = SbiCategoryCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
