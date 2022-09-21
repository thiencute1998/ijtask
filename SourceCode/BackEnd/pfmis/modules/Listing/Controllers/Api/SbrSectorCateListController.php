<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\SbrSector;
use Module\Listing\Models\SbrSectorCateList;
use Module\Listing\Models\SbrSectorCateValue;
use Illuminate\Support\Facades\Validator;

class SbrSectorCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = SbrSectorCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $sbrSectorCateList = SbrSectorCateList::all();
        $json['data']['sbrSectorCateList'] = $sbrSectorCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:sbr_sector_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại lĩnh vực không được để trống',
            'CateNo.unique' => 'Mã loại lĩnh vực đã tồn tại',
            'CateName.required' => 'Tên loại lĩnh vực không được để trống',
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
            $query = SbrSectorCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $sbrSectorCateList = new SbrSectorCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $sbrSectorCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $sbrSectorCateValue = '';
            if($item->DataType == 3){
                $sbrSectorCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $sbrSectorCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $sbrSectorCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $sbrSectorCateList->CateID,
                'CateName'=> $sbrSectorCateList->CateName,
                'CateNo'=> $sbrSectorCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $sbrSectorCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('sbr_sector_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $sbrSectorCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = SbrSectorCateList::find($id);
        $sbrSectorCateList = SbrSectorCateList::all();
        $sbrSectorCateValue = SbrSectorCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['sbrSectorCateList'] = $sbrSectorCateList;
        $json['data']['sbrSectorCateValue'] = $sbrSectorCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $sbrSectorCateList = SbrSectorCateList::all();
        $data = SbrSectorCateList::find($id);
        $sbrSectorCateValue = SbrSectorCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['sbrSectorCateList'] = $sbrSectorCateList;
        $json['data']['sbrSectorCateValue'] = $sbrSectorCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $sbrSectorCateItem = SbrSectorCateList::find($id);
        if($sbrSectorCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:sbr_sector_cate_list',
            ],[
                'CateName.required' => 'Tên loại lĩnh vực không được để trống',
                'CateNo.required' => 'Mã loại lĩnh vực không được để trống',
                'CateNo.unique' => 'Mã loại lĩnh vực đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($sbrSectorCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = SbrSectorCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($sbrSectorCateItem->ParentID != $master->ParentID && $sbrSectorCateItem->ParentID){
            $ParentOld = SbrSectorCateList::find($sbrSectorCateItem->ParentID);
            $count = SbrSectorCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $sbrSectorCateItem->CateName = $master->CateName;
        $sbrSectorCateItem->CateNo = $master->CateNo;
        $sbrSectorCateItem->ParentID = $master->ParentID;
        $sbrSectorCateItem->ParentNo = $master->ParentNo;
        $sbrSectorCateItem->Level = $level;
        $sbrSectorCateItem->Inactive = $master->Inactive;
        $sbrSectorCateItem->save();
        SbrSectorCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $sbrSectorCateValue = '';
            if($item->DataType == 3){
                $sbrSectorCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $sbrSectorCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $sbrSectorCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $sbrSectorCateItem->CateName,
                'CateNo'=> $sbrSectorCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $sbrSectorCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('sbr_sector_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $sbrSectorCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        SbrSectorCateValue::where('CateID', $id)->delete();
        $data = SbrSectorCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('sbr_sector_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = SbrSectorCateList::find($data->ParentID);
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
        $sbrSectorCateList =  DB::table('sbr_sector_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($sbrSectorCateList as $sbrSectorCate ){
            if($sbrSectorCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            SbrSectorCateValue::whereIn('CateID',$IDs)->delete();
            SbrSectorCateList::destroy($IDs);
            $sql = "UPDATE sbr_sector_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from sbr_sector_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = SbrSectorCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
