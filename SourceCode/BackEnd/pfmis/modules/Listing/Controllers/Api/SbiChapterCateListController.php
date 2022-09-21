<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Expense;
use Module\Listing\Models\SbiChapterCateList;
use Module\Listing\Models\SbiChapterCateValue;
use Illuminate\Support\Facades\Validator;

class SbiChapterCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = SbiChapterCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $sbiChapterCateList = SbiChapterCateList::all();
        $json['data']['sbiChapterCateList'] = $sbiChapterCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:sbi_chapter_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại -loại khoản không được để trống',
            'CateNo.unique' => 'Mã loại -loại khoản đã tồn tại',
            'CateName.required' => 'Tên loại -loại khoản không được để trống',
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
            $query = SbiChapterCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $sbiChapterCateList = new SbiChapterCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $sbiChapterCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $sbiChapterCateValue = '';
            if($item->DataType == 3){
                $sbiChapterCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $sbiChapterCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $sbiChapterCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $sbiChapterCateList->CateID,
                'CateName'=> $sbiChapterCateList->CateName,
                'CateNo'=> $sbiChapterCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $sbiChapterCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('sbi_chapter_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $sbiChapterCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = SbiChapterCateList::find($id);
        $sbiChapterCateList = SbiChapterCateList::all();
        $sbiChapterCateValue = SbiChapterCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['sbiChapterCateList'] = $sbiChapterCateList;
        $json['data']['sbiChapterCateValue'] = $sbiChapterCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $sbiChapterCateList = SbiChapterCateList::all();
        $data = SbiChapterCateList::find($id);
        $sbiChapterCateValue = SbiChapterCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['sbiChapterCateList'] = $sbiChapterCateList;
        $json['data']['sbiChapterCateValue'] = $sbiChapterCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $sbiChapterCateItem = SbiChapterCateList::find($id);
        if($sbiChapterCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:sbi_chapter_cate_list',
            ],[
                'CateName.required' => 'Tên loại -loại khoản không được để trống',
                'CateNo.required' => 'Mã loại -loại khoản không được để trống',
                'CateNo.unique' => 'Mã loại -loại khoản đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($sbiChapterCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = SbiChapterCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($sbiChapterCateItem->ParentID != $master->ParentID && $sbiChapterCateItem->ParentID){
            $ParentOld = SbiChapterCateList::find($sbiChapterCateItem->ParentID);
            $count = SbiChapterCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $sbiChapterCateItem->CateName = $master->CateName;
        $sbiChapterCateItem->CateNo = $master->CateNo;
        $sbiChapterCateItem->ParentID = $master->ParentID;
        $sbiChapterCateItem->ParentNo = $master->ParentNo;
        $sbiChapterCateItem->Level = $level;
        $sbiChapterCateItem->Inactive = $master->Inactive;
        $sbiChapterCateItem->save();
        SbiChapterCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $sbiChapterCateValue = '';
            if($item->DataType == 3){
                $sbiChapterCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $sbiChapterCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $sbiChapterCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $sbiChapterCateItem->CateName,
                'CateNo'=> $sbiChapterCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $sbiChapterCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('sbi_chapter_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $sbiChapterCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        SbiChapterCateValue::where('CateID', $id)->delete();
        $data = SbiChapterCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('sbi_chapter_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = SbiChapterCateList::find($data->ParentID);
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
        $sbiChapterCateList =  DB::table('sbi_chapter_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($sbiChapterCateList as $sbiChapterCate ){
            if($sbiChapterCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            SbiChapterCateValue::whereIn('CateID',$IDs)->delete();
            SbiChapterCateList::destroy($IDs);
            $sql = "UPDATE sbi_chapter_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from sbi_chapter_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = SbiChapterCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
