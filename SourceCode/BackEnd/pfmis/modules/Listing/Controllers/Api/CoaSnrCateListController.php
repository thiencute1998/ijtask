<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaSnr;
use Module\Listing\Models\CoaSnrCateList;
use Module\Listing\Models\CoaSnrCateValue;
use Illuminate\Support\Facades\Validator;

class CoaSnrCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CoaSnrCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $coaSnrCateList = CoaSnrCateList::all();
        $json['data']['coaSnrCateList'] = $coaSnrCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:coa_snr_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại HTTK dự trữ quốc gia không được để trống',
            'CateNo.unique' => 'Mã loại HTTK dự trữ quốc gia đã tồn tại',
            'CateName.required' => 'Tên loại HTTK dự trữ quốc gia không được để trống',
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
            $query = CoaSnrCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $coaSnrCateList = new CoaSnrCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $coaSnrCateList->save();
        $arrayInsert = [];
        $detail = $request->detail;
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaSnrCateValue = '';
            if($item->DataType == 3){
                $coaSnrCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaSnrCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaSnrCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $coaSnrCateList->CateID,
                'CateName'=> $coaSnrCateList->CateName,
                'CateNo'=> $coaSnrCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaSnrCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_snr_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaSnrCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CoaSnrCateList::find($id);
        $coaSnrCateList = CoaSnrCateList::all();
        $coaSnrCateValue = CoaSnrCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaSnrCateList'] = $coaSnrCateList;
        $json['data']['coaSnrCateValue'] = $coaSnrCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $coaSnrCateList = CoaSnrCateList::all();
        $data = CoaSnrCateList::find($id);
        $coaSnrCateValue = CoaSnrCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['coaSnrCateList'] = $coaSnrCateList;
        $json['data']['coaSnrCateValue'] = $coaSnrCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;
        $detail = $request->detail;
        $coaSnrCateItem = CoaSnrCateList::find($id);
        if($coaSnrCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:coa_snr_cate_list',
            ],[
                'CateName.required' => 'Tên loại HTTK dự trữ quốc gia không được để trống',
                'CateNo.required' => 'Mã loại HTTK dự trữ quốc gia không được để trống',
                'CateNo.unique' => 'Mã loại HTTK dự trữ quốc gia đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($coaSnrCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CoaSnrCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($coaSnrCateItem->ParentID != $master->ParentID && $coaSnrCateItem->ParentID){
            $ParentOld = CoaSnrCateList::find($coaSnrCateItem->ParentID);
            $count = CoaSnrCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $coaSnrCateItem->CateName = $master->CateName;
        $coaSnrCateItem->CateNo = $master->CateNo;
        $coaSnrCateItem->ParentID = $master->ParentID;
        $coaSnrCateItem->ParentNo = $master->ParentNo;
        $coaSnrCateItem->Level = $level;
        $coaSnrCateItem->Inactive = $master->Inactive;
        $coaSnrCateItem->save();
        CoaSnrCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $coaSnrCateValue = '';
            if($item->DataType == 3){
                $coaSnrCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $coaSnrCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $coaSnrCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $coaSnrCateItem->CateName,
                'CateNo'=> $coaSnrCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $coaSnrCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('coa_snr_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $coaSnrCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CoaSnrCateValue::where('CateID', $id)->delete();
        $data = CoaSnrCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('coa_snr_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CoaSnrCateList::find($data->ParentID);
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
        $coaSnrCateList =  DB::table('coa_snr_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($coaSnrCateList as $coaSnrCate ){
            if($coaSnrCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CoaSnrCateValue::whereIn('CateID',$IDs)->delete();
            CoaSnrCateList::destroy($IDs);
            $sql = "UPDATE coa_snr_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from coa_snr_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CoaSnrCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
