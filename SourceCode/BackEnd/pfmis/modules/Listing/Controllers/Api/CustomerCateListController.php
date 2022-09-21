<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Customer;
use Module\Listing\Models\CustomerCateList;
use Module\Listing\Models\CustomerCateValue;
use Illuminate\Support\Facades\Validator;

class CustomerCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = CustomerCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $customerCateList = CustomerCateList::all();
        $json['data']['customerCateList'] = $customerCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:customer_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại khách hàng không được để trống',
            'CateNo.unique' => 'Mã loại khách hàng đã tồn tại',
            'CateName.required' => 'Tên loại khách hàng không được để trống',
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
            $query = CustomerCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $customerCateList = new CustomerCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $customerCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $customerCateValue = '';
            if($item->DataType == 3){
                $customerCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $customerCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $customerCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $customerCateList->CateID,
                'CateName'=> $customerCateList->CateName,
                'CateNo'=> $customerCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $customerCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('customer_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $customerCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = CustomerCateList::find($id);
        $customerCateList = CustomerCateList::all();
        $customerCateValue = CustomerCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['customerCateList'] = $customerCateList;
        $json['data']['customerCateValue'] = $customerCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $customerCateList = CustomerCateList::all();
        $data = CustomerCateList::find($id);
        $customerCateValue = CustomerCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['customerCateList'] = $customerCateList;
        $json['data']['customerCateValue'] = $customerCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $customerCateItem = CustomerCateList::find($id);
        if($customerCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:customer_cate_list',
            ],[
                'CateName.required' => 'Tên loại khách hàng không được để trống',
                'CateNo.required' => 'Mã loại khách hàng không được để trống',
                'CateNo.unique' => 'Mã loại khách hàng đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($customerCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = CustomerCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($customerCateItem->ParentID != $master->ParentID && $customerCateItem->ParentID){
            $ParentOld = CustomerCateList::find($customerCateItem->ParentID);
            $count = CustomerCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $customerCateItem->CateName = $master->CateName;
        $customerCateItem->CateNo = $master->CateNo;
        $customerCateItem->ParentID = $master->ParentID;
        $customerCateItem->ParentNo = $master->ParentNo;
        $customerCateItem->Level = $level;
        $customerCateItem->Inactive = $master->Inactive;
        $customerCateItem->save();
        CustomerCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $customerCateValue = '';
            if($item->DataType == 3){
                $customerCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $customerCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $customerCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $customerCateItem->CateName,
                'CateNo'=> $customerCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $customerCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('customer_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $customerCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        CustomerCateValue::where('CateID', $id)->delete();
        $data = CustomerCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('customer_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = CustomerCateList::find($data->ParentID);
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
        $customerCateList =  DB::table('customer_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($customerCateList as $customerCate ){
            if($customerCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            CustomerCateValue::whereIn('CateID',$IDs)->delete();
            CustomerCateList::destroy($IDs);
            $sql = "UPDATE customer_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from customer_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = CustomerCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
