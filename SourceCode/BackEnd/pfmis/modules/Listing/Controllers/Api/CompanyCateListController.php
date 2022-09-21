<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Company;
use Module\Listing\Models\CompanyCateList;
use Module\Listing\Models\CompanyCateValue;
use Illuminate\Support\Facades\Validator;

class CompanyCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CompanyCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $companyCateList = CompanyCateList::all();
        $json['data']['companyCateList'] = $companyCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:company_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại đơn vị không được để trống',
            'CateNo.unique' => 'Mã loại đơn vị đã tồn tại',
            'CateName.required' => 'Tên loại đơn vị không được để trống',
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
            $query = CompanyCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $companyCateList = new CompanyCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $companyCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $companyCateValue = '';
            if($item->DataType == 3){
                $companyCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $companyCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $companyCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $companyCateList->CateID,
                'CateName'=> $companyCateList->CateName,
                'CateNo'=> $companyCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $companyCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('company_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $companyCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CompanyCateList::find($id);
        $companyCateList = CompanyCateList::all();
        $companyCateValue = CompanyCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['companyCateList'] = $companyCateList;
        $json['data']['companyCateValue'] = $companyCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $companyCateList = CompanyCateList::all();
        $data = CompanyCateList::find($id);
        $companyCateValue = CompanyCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['companyCateList'] = $companyCateList;
        $json['data']['companyCateValue'] = $companyCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $companyCateItem = CompanyCateList::find($id);
        if($companyCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:company_cate_list',
            ],[
                'CateName.required' => 'Tên loại đơn vị không được để trống',
                'CateNo.required' => 'Mã loại đơn vị không được để trống',
                'CateNo.unique' => 'Mã loại đơn vị đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($companyCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CompanyCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($companyCateItem->ParentID != $master->ParentID && $companyCateItem->ParentID){
            $ParentOld = CompanyCateList::find($companyCateItem->ParentID);
            $count = CompanyCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $companyCateItem->CateName = $master->CateName;
        $companyCateItem->CateNo = $master->CateNo;
        $companyCateItem->ParentID = $master->ParentID;
        $companyCateItem->ParentNo = $master->ParentNo;
        $companyCateItem->Level = $level;
        $companyCateItem->Inactive = $master->Inactive;
        $companyCateItem->save();
        CompanyCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $companyCateValue = '';
            if($item->DataType == 3){
                $companyCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $companyCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $companyCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $companyCateItem->CateName,
                'CateNo'=> $companyCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $companyCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('company_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $companyCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CompanyCateValue::where('CateID', $id)->delete();
        $data = CompanyCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('company_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CompanyCateList::find($data->ParentID);
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
        $companyCateList =  DB::table('company_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($companyCateList as $companyCate ){
            if($companyCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CompanyCateValue::whereIn('CateID',$IDs)->delete();
            CompanyCateList::destroy($IDs);
            $sql = "UPDATE company_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from company_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CompanyCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
