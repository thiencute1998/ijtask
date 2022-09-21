<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaLdi;
use Module\Listing\Models\CoaLdiCateList;
use Module\Listing\Models\CoaLdiCateValue;
use Illuminate\Support\Facades\Validator;

class CoaLdiCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CoaLdiCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $coaLdiCateList = CoaLdiCateList::all();
        $json['data']['coaLdiCateList'] = $coaLdiCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:coa_ldi_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại HTTK quỹ đầu tư phát triển địa phương không được để trống',
            'CateNo.unique' => 'Mã loại HTTK quỹ đầu tư phát triển địa phương đã tồn tại',
            'CateName.required' => 'Tên loại HTTK quỹ đầu tư phát triển địa phương không được để trống',
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
            $query = CoaLdiCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $coaLdiCateList = new CoaLdiCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $coaLdiCateList->save();
        $arrayInsert = [];
        $detail = $request->detail;
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaLdiCateValue = '';
            if($item->DataType == 3){
                $coaLdiCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaLdiCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaLdiCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $coaLdiCateList->CateID,
                'CateName'=> $coaLdiCateList->CateName,
                'CateNo'=> $coaLdiCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaLdiCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_ldi_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaLdiCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CoaLdiCateList::find($id);
        $coaLdiCateList = CoaLdiCateList::all();
        $coaLdiCateValue = CoaLdiCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaLdiCateList'] = $coaLdiCateList;
        $json['data']['coaLdiCateValue'] = $coaLdiCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $coaLdiCateList = CoaLdiCateList::all();
        $data = CoaLdiCateList::find($id);
        $coaLdiCateValue = CoaLdiCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaLdiCateList'] = $coaLdiCateList;
        $json['data']['coaLdiCateValue'] = $coaLdiCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;
        $detail = $request->detail;
        $coaLdiCateItem = CoaLdiCateList::find($id);
        if($coaLdiCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:coa_ldi_cate_list',
            ],[
                'CateName.required' => 'Tên loại HTTK quỹ đầu tư phát triển địa phương không được để trống',
                'CateNo.required' => 'Mã loại HTTK quỹ đầu tư phát triển địa phương không được để trống',
                'CateNo.unique' => 'Mã loại HTTK quỹ đầu tư phát triển địa phương đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($coaLdiCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CoaLdiCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($coaLdiCateItem->ParentID != $master->ParentID && $coaLdiCateItem->ParentID){
            $ParentOld = CoaLdiCateList::find($coaLdiCateItem->ParentID);
            $count = CoaLdiCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $coaLdiCateItem->CateName = $master->CateName;
        $coaLdiCateItem->CateNo = $master->CateNo;
        $coaLdiCateItem->ParentID = $master->ParentID;
        $coaLdiCateItem->ParentNo = $master->ParentNo;
        $coaLdiCateItem->Level = $level;
        $coaLdiCateItem->Inactive = $master->Inactive;
        $coaLdiCateItem->save();
        CoaLdiCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaLdiCateValue = '';
            if($item->DataType == 3){
                $coaLdiCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaLdiCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaLdiCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $coaLdiCateItem->CateName,
                'CateNo'=> $coaLdiCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaLdiCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_ldi_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaLdiCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CoaLdiCateValue::where('CateID', $id)->delete();
        $data = CoaLdiCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('coa_ldi_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CoaLdiCateList::find($data->ParentID);
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
        $coaLdiCateList =  DB::table('coa_ldi_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($coaLdiCateList as $coaLdiCate ){
            if($coaLdiCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CoaLdiCateValue::whereIn('CateID',$IDs)->delete();
            CoaLdiCateList::destroy($IDs);
            $sql = "UPDATE coa_ldi_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from coa_ldi_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CoaLdiCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
