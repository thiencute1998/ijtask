<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaEas;
use Module\Listing\Models\CoaEasCateList;
use Module\Listing\Models\CoaEasCateValue;
use Illuminate\Support\Facades\Validator;

class CoaEasCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CoaEasCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $coaEasCateList = CoaEasCateList::all();
        $json['data']['coaEasCateList'] = $coaEasCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:coa_eas_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại HTTK doanh nghiệp không được để trống',
            'CateNo.unique' => 'Mã loại HTTK doanh nghiệp đã tồn tại',
            'CateName.required' => 'Tên loại HTTK doanh nghiệp không được để trống',
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
            $query = CoaEasCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $coaEasCateList = new CoaEasCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $coaEasCateList->save();
        $arrayInsert = [];
        $detail = $request->detail;
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaEasCateValue = '';
            if($item->DataType == 3){
                $coaEasCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaEasCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaEasCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $coaEasCateList->CateID,
                'CateName'=> $coaEasCateList->CateName,
                'CateNo'=> $coaEasCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaEasCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_eas_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaEasCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CoaEasCateList::find($id);
        $coaEasCateList = CoaEasCateList::all();
        $coaEasCateValue = CoaEasCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaEasCateList'] = $coaEasCateList;
        $json['data']['coaEasCateValue'] = $coaEasCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $coaEasCateList = CoaEasCateList::all();
        $data = CoaEasCateList::find($id);
        $coaEasCateValue = CoaEasCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaEasCateList'] = $coaEasCateList;
        $json['data']['coaEasCateValue'] = $coaEasCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;
        $detail = $request->detail;
        $coaEasCateItem = CoaEasCateList::find($id);
        if($coaEasCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:coa_eas_cate_list',
            ],[
                'CateName.required' => 'Tên loại HTTK doanh nghiệp không được để trống',
                'CateNo.required' => 'Mã loại HTTK doanh nghiệp không được để trống',
                'CateNo.unique' => 'Mã loại HTTK doanh nghiệp đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($coaEasCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CoaEasCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($coaEasCateItem->ParentID != $master->ParentID && $coaEasCateItem->ParentID){
            $ParentOld = CoaEasCateList::find($coaEasCateItem->ParentID);
            $count = CoaEasCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $coaEasCateItem->CateName = $master->CateName;
        $coaEasCateItem->CateNo = $master->CateNo;
        $coaEasCateItem->ParentID = $master->ParentID;
        $coaEasCateItem->ParentNo = $master->ParentNo;
        $coaEasCateItem->Level = $level;
        $coaEasCateItem->Inactive = $master->Inactive;
        $coaEasCateItem->save();
        CoaEasCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaEasCateValue = '';
            if($item->DataType == 3){
                $coaEasCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaEasCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaEasCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $coaEasCateItem->CateName,
                'CateNo'=> $coaEasCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaEasCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_eas_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaEasCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CoaEasCateValue::where('CateID', $id)->delete();
        $data = CoaEasCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('coa_eas_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CoaEasCateList::find($data->ParentID);
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
        $coaEasCateList =  DB::table('coa_eas_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($coaEasCateList as $coaEasCate ){
            if($coaEasCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CoaEasCateValue::whereIn('CateID',$IDs)->delete();
            CoaEasCateList::destroy($IDs);
            $sql = "UPDATE coa_eas_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from coa_eas_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CoaEasCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
