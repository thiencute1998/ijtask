<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Contract;
use Module\Listing\Models\ContractCateList;
use Module\Listing\Models\ContractCateValue;
use Illuminate\Support\Facades\Validator;

class ContractCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = ContractCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $contractCateList = ContractCateList::all();
        $json['data']['contractCateList'] = $contractCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:contract_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại hợp đồng không được để trống',
            'CateNo.unique' => 'Mã loại hợp đồng đã tồn tại',
            'CateName.required' => 'Tên loại hợp đồng không được để trống',
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
            $query = ContractCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $contractCateList = new ContractCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $contractCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $contractCateValue = '';
            if($item->DataType == 3){
                $contractCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $contractCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $contractCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $contractCateList->CateID,
                'CateName'=> $contractCateList->CateName,
                'CateNo'=> $contractCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $contractCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('contract_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $contractCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = ContractCateList::find($id);
        $contractCateList = ContractCateList::all();
        $contractCateValue = ContractCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['contractCateList'] = $contractCateList;
        $json['data']['contractCateValue'] = $contractCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $contractCateList = ContractCateList::all();
        $data = ContractCateList::find($id);
        $contractCateValue = ContractCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['contractCateList'] = $contractCateList;
        $json['data']['contractCateValue'] = $contractCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $contractCateItem = ContractCateList::find($id);
        if($contractCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:contract_cate_list',
            ],[
                'CateName.required' => 'Tên loại hợp đồng không được để trống',
                'CateNo.required' => 'Mã loại hợp đồng không được để trống',
                'CateNo.unique' => 'Mã loại hợp đồng đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($contractCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = ContractCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($contractCateItem->ParentID != $master->ParentID && $contractCateItem->ParentID){
            $ParentOld = ContractCateList::find($contractCateItem->ParentID);
            $count = ContractCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $contractCateItem->CateName = $master->CateName;
        $contractCateItem->CateNo = $master->CateNo;
        $contractCateItem->ParentID = $master->ParentID;
        $contractCateItem->ParentNo = $master->ParentNo;
        $contractCateItem->Level = $level;
        $contractCateItem->Inactive = $master->Inactive;
        $contractCateItem->save();
        ContractCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $contractCateValue = '';
            if($item->DataType == 3){
                $contractCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $contractCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $contractCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $contractCateItem->CateName,
                'CateNo'=> $contractCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $contractCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('contract_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $contractCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        ContractCateValue::where('CateID', $id)->delete();
        $data = ContractCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('contract_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = ContractCateList::find($data->ParentID);
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
        $contractCateList =  DB::table('contract_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($contractCateList as $contractCate ){
            if($contractCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            ContractCateValue::whereIn('CateID',$IDs)->delete();
            ContractCateList::destroy($IDs);
            $sql = "UPDATE contract_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from contract_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = ContractCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
