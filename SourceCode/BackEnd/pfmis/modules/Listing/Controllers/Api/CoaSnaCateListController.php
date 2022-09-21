<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaSna;
use Module\Listing\Models\CoaSnaCateList;
use Module\Listing\Models\CoaSnaCateValue;
use Illuminate\Support\Facades\Validator;

class CoaSnaCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CoaSnaCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $coaSnaCateList = CoaSnaCateList::all();
        $json['data']['coaSnaCateList'] = $coaSnaCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:coa_sna_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại HTTK quốc gia không được để trống',
            'CateNo.unique' => 'Mã loại HTTK quốc gia đã tồn tại',
            'CateName.required' => 'Tên loại HTTK quốc gia không được để trống',
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
            $query = CoaSnaCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $coaSnaCateList = new CoaSnaCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $coaSnaCateList->save();
        $arrayInsert = [];
        $detail = $request->detail;
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaSnaCateValue = '';
            if($item->DataType == 3){
                $coaSnaCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaSnaCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaSnaCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $coaSnaCateList->CateID,
                'CateName'=> $coaSnaCateList->CateName,
                'CateNo'=> $coaSnaCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaSnaCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_sna_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaSnaCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CoaSnaCateList::find($id);
        $coaSnaCateList = CoaSnaCateList::all();
        $coaSnaCateValue = CoaSnaCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaSnaCateList'] = $coaSnaCateList;
        $json['data']['coaSnaCateValue'] = $coaSnaCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $coaSnaCateList = CoaSnaCateList::all();
        $data = CoaSnaCateList::find($id);
        $coaSnaCateValue = CoaSnaCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaSnaCateList'] = $coaSnaCateList;
        $json['data']['coaSnaCateValue'] = $coaSnaCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;
        $detail = $request->detail;
        $coaSnaCateItem = CoaSnaCateList::find($id);
        if($coaSnaCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:coa_sna_cate_list',
            ],[
                'CateName.required' => 'Tên loại HTTK quốc gia không được để trống',
                'CateNo.required' => 'Mã loại HTTK quốc gia không được để trống',
                'CateNo.unique' => 'Mã loại HTTK quốc gia đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($coaSnaCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CoaSnaCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($coaSnaCateItem->ParentID != $master->ParentID && $coaSnaCateItem->ParentID){
            $ParentOld = CoaSnaCateList::find($coaSnaCateItem->ParentID);
            $count = CoaSnaCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $coaSnaCateItem->CateName = $master->CateName;
        $coaSnaCateItem->CateNo = $master->CateNo;
        $coaSnaCateItem->ParentID = $master->ParentID;
        $coaSnaCateItem->ParentNo = $master->ParentNo;
        $coaSnaCateItem->Level = $level;
        $coaSnaCateItem->Inactive = $master->Inactive;
        $coaSnaCateItem->save();
        CoaSnaCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaSnaCateValue = '';
            if($item->DataType == 3){
                $coaSnaCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaSnaCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaSnaCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $coaSnaCateItem->CateName,
                'CateNo'=> $coaSnaCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaSnaCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_sna_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaSnaCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CoaSnaCateValue::where('CateID', $id)->delete();
        $data = CoaSnaCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('coa_sna_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CoaSnaCateList::find($data->ParentID);
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
        $coaSnaCateList =  DB::table('coa_sna_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($coaSnaCateList as $coaSnaCate ){
            if($coaSnaCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CoaSnaCateValue::whereIn('CateID',$IDs)->delete();
            CoaSnaCateList::destroy($IDs);
            $sql = "UPDATE coa_sna_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from coa_sna_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CoaSnaCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
