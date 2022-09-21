<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Partner;
use Module\Listing\Models\PartnerCateList;
use Module\Listing\Models\PartnerCateValue;
use Illuminate\Support\Facades\Validator;

class PartnerCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = PartnerCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $partnerCateList = PartnerCateList::all();
        $json['data']['partnerCateList'] = $partnerCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:partner_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại đối tác không được để trống',
            'CateNo.unique' => 'Mã loại đối tác đã tồn tại',
            'CateName.required' => 'Tên loại đối tác không được để trống',
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
            $query = PartnerCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $partnerCateList = new PartnerCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $partnerCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $partnerCateValue = '';
            if($item->DataType == 3){
                $partnerCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $partnerCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $partnerCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $partnerCateList->CateID,
                'CateName'=> $partnerCateList->CateName,
                'CateNo'=> $partnerCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $partnerCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('partner_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $partnerCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = PartnerCateList::find($id);
        $partnerCateList = PartnerCateList::all();
        $partnerCateValue = PartnerCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['partnerCateList'] = $partnerCateList;
        $json['data']['partnerCateValue'] = $partnerCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $partnerCateList = PartnerCateList::all();
        $data = PartnerCateList::find($id);
        $partnerCateValue = PartnerCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['partnerCateList'] = $partnerCateList;
        $json['data']['partnerCateValue'] = $partnerCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $partnerCateItem = PartnerCateList::find($id);
        if($partnerCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:partner_cate_list',
            ],[
                'CateName.required' => 'Tên loại đối tác không được để trống',
                'CateNo.required' => 'Mã loại đối tác không được để trống',
                'CateNo.unique' => 'Mã loại đối tác đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($partnerCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = PartnerCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($partnerCateItem->ParentID != $master->ParentID && $partnerCateItem->ParentID){
            $ParentOld = PartnerCateList::find($partnerCateItem->ParentID);
            $count = PartnerCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $partnerCateItem->CateName = $master->CateName;
        $partnerCateItem->CateNo = $master->CateNo;
        $partnerCateItem->ParentID = $master->ParentID;
        $partnerCateItem->ParentNo = $master->ParentNo;
        $partnerCateItem->Level = $level;
        $partnerCateItem->Inactive = $master->Inactive;
        $partnerCateItem->save();
        PartnerCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $partnerCateValue = '';
            if($item->DataType == 3){
                $partnerCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $partnerCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $partnerCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $partnerCateItem->CateName,
                'CateNo'=> $partnerCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $partnerCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('partner_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $partnerCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        PartnerCateValue::where('CateID', $id)->delete();
        $data = PartnerCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('partner_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = PartnerCateList::find($data->ParentID);
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
        $partnerCateList =  DB::table('partner_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($partnerCateList as $partnerCate ){
            if($partnerCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            PartnerCateValue::whereIn('CateID',$IDs)->delete();
            PartnerCateList::destroy($IDs);
            $sql = "UPDATE partner_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from partner_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = PartnerCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
