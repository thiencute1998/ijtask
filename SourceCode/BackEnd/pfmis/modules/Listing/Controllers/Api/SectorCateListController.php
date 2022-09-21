<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Sector;
use Module\Listing\Models\SectorCateList;
use Module\Listing\Models\SectorCateValue;
use Illuminate\Support\Facades\Validator;

class SectorCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = SectorCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $sectorCateList = SectorCateList::all();
        $json['data']['sectorCateList'] = $sectorCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:sector_cate_list',
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
            $query = SectorCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $sectorCateList = new SectorCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $sectorCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $sectorCateValue = '';
            if($item->DataType == 3){
                $sectorCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $sectorCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $sectorCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $sectorCateList->CateID,
                'CateName'=> $sectorCateList->CateName,
                'CateNo'=> $sectorCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $sectorCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('sector_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $sectorCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = SectorCateList::find($id);
        $sectorCateList = SectorCateList::all();
        $sectorCateValue = SectorCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['sectorCateList'] = $sectorCateList;
        $json['data']['sectorCateValue'] = $sectorCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $sectorCateList = SectorCateList::all();
        $data = SectorCateList::find($id);
        $sectorCateValue = SectorCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['sectorCateList'] = $sectorCateList;
        $json['data']['sectorCateValue'] = $sectorCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $sectorCateItem = SectorCateList::find($id);
        if($sectorCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:sector_cate_list',
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
        if($sectorCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = SectorCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($sectorCateItem->ParentID != $master->ParentID && $sectorCateItem->ParentID){
            $ParentOld = SectorCateList::find($sectorCateItem->ParentID);
            $count = SectorCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $sectorCateItem->CateName = $master->CateName;
        $sectorCateItem->CateNo = $master->CateNo;
        $sectorCateItem->ParentID = $master->ParentID;
        $sectorCateItem->ParentNo = $master->ParentNo;
        $sectorCateItem->Level = $level;
        $sectorCateItem->Inactive = $master->Inactive;
        $sectorCateItem->save();
        SectorCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $sectorCateValue = '';
            if($item->DataType == 3){
                $sectorCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $sectorCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $sectorCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $sectorCateItem->CateName,
                'CateNo'=> $sectorCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $sectorCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('sector_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $sectorCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        SectorCateValue::where('CateID', $id)->delete();
        $data = SectorCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('sector_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = SectorCateList::find($data->ParentID);
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
        $sectorCateList =  DB::table('sector_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($sectorCateList as $sectorCate ){
            if($sectorCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            SectorCateValue::whereIn('CateID',$IDs)->delete();
            SectorCateList::destroy($IDs);
            $sql = "UPDATE sector_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from sector_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = SectorCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
