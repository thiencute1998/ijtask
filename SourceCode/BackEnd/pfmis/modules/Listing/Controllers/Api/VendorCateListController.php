<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Vendor;
use Module\Listing\Models\VendorCateList;
use Module\Listing\Models\VendorCateValue;
use Illuminate\Support\Facades\Validator;

class VendorCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = VendorCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $vendorCateList = VendorCateList::all();
        $json['data']['vendorCateList'] = $vendorCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:vendor_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại nhà cung cấp không được để trống',
            'CateNo.unique' => 'Mã loại nhà cung cấp đã tồn tại',
            'CateName.required' => 'Tên loại nhà cung cấp không được để trống',
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
            $path = $master->CateNo;
        } else {
            $query = VendorCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
            $path = $parent->Path . '#' . $master->CateNo;
        }
        $vendorCateList = new VendorCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Path' => $path,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $vendorCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $vendorCateValue = '';
            if($item->DataType == 3){
                $vendorCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $vendorCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $vendorCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $vendorCateList->CateID,
                'CateName'=> $vendorCateList->CateName,
                'CateNo'=> $vendorCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $vendorCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('vendor_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $vendorCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = VendorCateList::find($id);
        $vendorCateList = VendorCateList::all();
        $vendorCateValue = VendorCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['vendorCateList'] = $vendorCateList;
        $json['data']['vendorCateValue'] = $vendorCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $vendorCateList = VendorCateList::all();
        $data = VendorCateList::find($id);
        $vendorCateValue = VendorCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['vendorCateList'] = $vendorCateList;
        $json['data']['vendorCateValue'] = $vendorCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){




        $master = (object)$request->master;

        $detail = $request->detail;
        $vendorCateItem = VendorCateList::find($id);
        if($vendorCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:vendor_cate_list',
            ],[
                'CateName.required' => 'Tên loại nhà cung cấp không được để trống',
                'CateNo.required' => 'Mã loại nhà cung cấp không được để trống',
                'CateNo.unique' => 'Mã loại nhà cung cấp đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        // Không sửa No, Parent, CateValue của bản ghi tổng hợp
        if($vendorCateItem->Detail == 0){
            if($vendorCateItem->CateNo != $master->CateNo || $vendorCateItem->ParentID != $master->ParentID || $master->VendorCateValue != null ){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }

        // set for newParent
        if($master->ParentID){
            $Parent = VendorCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $path = $Parent->Path . '#' . $master->CateNo;
            $Parent->save();
        } else {
            $level = 1;
            $path = $master->CateNo;
        }
        // set for old Parent
        if($vendorCateItem->ParentID != $master->ParentID && $vendorCateItem->ParentID){
            $ParentOld = VendorCateList::find($vendorCateItem->ParentID);
            $count = VendorCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }

        $vendorCateItem->CateName = $master->CateName;
        $vendorCateItem->CateNo = $master->CateNo;
        $vendorCateItem->ParentID = $master->ParentID;
        $vendorCateItem->ParentNo = $master->ParentNo;
        $vendorCateItem->Level = $level;
        $vendorCateItem->Path = $path;
        $vendorCateItem->Inactive = $master->Inactive;
        $vendorCateItem->save();
        VendorCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $vendorCateValue = '';
            if($item->DataType == 3){
                $vendorCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $vendorCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $vendorCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $vendorCateItem->CateName,
                'CateNo'=> $vendorCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $vendorCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('vendor_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $vendorCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        VendorCateValue::where('CateID', $id)->delete();
        $data = VendorCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('vendor_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = VendorCateList::find($data->ParentID);
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
        $vendorCateList =  DB::table('vendor_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($vendorCateList as $vendorCate ){
            if($vendorCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            VendorCateValue::whereIn('CateID',$IDs)->delete();
            VendorCateList::destroy($IDs);
            $sql = "UPDATE vendor_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from vendor_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = VendorCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
