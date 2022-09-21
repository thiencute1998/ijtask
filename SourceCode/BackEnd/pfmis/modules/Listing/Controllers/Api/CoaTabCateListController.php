<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaTab;
use Module\Listing\Models\CoaTabCateList;
use Module\Listing\Models\CoaTabCateValue;
use Illuminate\Support\Facades\Validator;

class CoaTabCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CoaTabCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $coaTabCateList = CoaTabCateList::all();
        $json['data']['coaTabCateList'] = $coaTabCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:coa_tab_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại HTTK kho bạc nhà nước không được để trống',
            'CateNo.unique' => 'Mã loại HTTK kho bạc nhà nước đã tồn tại',
            'CateName.required' => 'Tên loại HTTK kho bạc nhà nước không được để trống',
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
            $query = CoaTabCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $coaTabCateList = new CoaTabCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $coaTabCateList->save();
        $arrayInsert = [];
        $detail = $request->detail;
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaTabCateValue = '';
            if($item->DataType == 3){
                $coaTabCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaTabCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaTabCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $coaTabCateList->CateID,
                'CateName'=> $coaTabCateList->CateName,
                'CateNo'=> $coaTabCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaTabCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_tab_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaTabCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CoaTabCateList::find($id);
        $coaTabCateList = CoaTabCateList::all();
        $coaTabCateValue = CoaTabCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaTabCateList'] = $coaTabCateList;
        $json['data']['coaTabCateValue'] = $coaTabCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $coaTabCateList = CoaTabCateList::all();
        $data = CoaTabCateList::find($id);
        $coaTabCateValue = CoaTabCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaTabCateList'] = $coaTabCateList;
        $json['data']['coaTabCateValue'] = $coaTabCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;
        $detail = $request->detail;
        $coaTabCateItem = CoaTabCateList::find($id);
        if($coaTabCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:coa_tab_cate_list',
            ],[
                'CateName.required' => 'Tên loại HTTK kho bạc nhà nước không được để trống',
                'CateNo.required' => 'Mã loại HTTK kho bạc nhà nước không được để trống',
                'CateNo.unique' => 'Mã loại HTTK kho bạc nhà nước đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($coaTabCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CoaTabCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($coaTabCateItem->ParentID != $master->ParentID && $coaTabCateItem->ParentID){
            $ParentOld = CoaTabCateList::find($coaTabCateItem->ParentID);
            $count = CoaTabCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $coaTabCateItem->CateName = $master->CateName;
        $coaTabCateItem->CateNo = $master->CateNo;
        $coaTabCateItem->ParentID = $master->ParentID;
        $coaTabCateItem->ParentNo = $master->ParentNo;
        $coaTabCateItem->Level = $level;
        $coaTabCateItem->Inactive = $master->Inactive;
        $coaTabCateItem->save();
        CoaTabCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaTabCateValue = '';
            if($item->DataType == 3){
                $coaTabCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaTabCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaTabCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $coaTabCateItem->CateName,
                'CateNo'=> $coaTabCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaTabCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_tab_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaTabCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CoaTabCateValue::where('CateID', $id)->delete();
        $data = CoaTabCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('coa_tab_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CoaTabCateList::find($data->ParentID);
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
        $coaTabCateList =  DB::table('coa_tab_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($coaTabCateList as $coaTabCate ){
            if($coaTabCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CoaTabCateValue::whereIn('CateID',$IDs)->delete();
            CoaTabCateList::destroy($IDs);
            $sql = "UPDATE coa_tab_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from coa_tab_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CoaTabCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
