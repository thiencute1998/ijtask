<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Expense;
use Module\Listing\Models\ExpenseCateList;
use Module\Listing\Models\ExpenseCateValue;
use Illuminate\Support\Facades\Validator;

class ExpenseCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = ExpenseCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $expenseCateList = ExpenseCateList::all();
        $json['data']['expenseCateList'] = $expenseCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:expense_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại khoản chi không được để trống',
            'CateNo.unique' => 'Mã loại khoản chi đã tồn tại',
            'CateName.required' => 'Tên loại khoản chi không được để trống',
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
            $query = ExpenseCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
            $path = $parent->Path . '#' . $master->CateNo;
        }
        $expenseCateList = new ExpenseCateList([
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
        $expenseCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $expenseCateValue = '';
            if($item->DataType == 3){
                $expenseCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $expenseCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $expenseCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $expenseCateList->CateID,
                'CateName'=> $expenseCateList->CateName,
                'CateNo'=> $expenseCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $expenseCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('expense_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $expenseCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = ExpenseCateList::find($id);
        $expenseCateList = ExpenseCateList::all();
        $expenseCateValue = ExpenseCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['expenseCateList'] = $expenseCateList;
        $json['data']['expenseCateValue'] = $expenseCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $expenseCateList = ExpenseCateList::all();
        $data = ExpenseCateList::find($id);
        $expenseCateValue = ExpenseCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['expenseCateList'] = $expenseCateList;
        $json['data']['expenseCateValue'] = $expenseCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){




        $master = (object)$request->master;

        $detail = $request->detail;
        $expenseCateItem = ExpenseCateList::find($id);
        if($expenseCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:expense_cate_list',
            ],[
                'CateName.required' => 'Tên loại khoản chi không được để trống',
                'CateNo.required' => 'Mã loại khoản chi không được để trống',
                'CateNo.unique' => 'Mã loại khoản chi đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        // Không sửa No, Parent, CateValue của bản ghi tổng hợp
        if($expenseCateItem->Detail == 0){
            if($expenseCateItem->CateNo != $master->CateNo || $expenseCateItem->ParentID != $master->ParentID || $master->ExpenseCateValue != null ){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }

        // set for newParent
        if($master->ParentID){
            $Parent = ExpenseCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $path = $Parent->Path . '#' . $master->CateNo;
            $Parent->save();
        } else {
            $level = 1;
            $path = $master->CateNo;
        }
        // set for old Parent
        if($expenseCateItem->ParentID != $master->ParentID && $expenseCateItem->ParentID){
            $ParentOld = ExpenseCateList::find($expenseCateItem->ParentID);
            $count = ExpenseCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }

        $expenseCateItem->CateName = $master->CateName;
        $expenseCateItem->CateNo = $master->CateNo;
        $expenseCateItem->ParentID = $master->ParentID;
        $expenseCateItem->ParentNo = $master->ParentNo;
        $expenseCateItem->Level = $level;
        $expenseCateItem->Path = $path;
        $expenseCateItem->Inactive = $master->Inactive;
        $expenseCateItem->save();
        ExpenseCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $expenseCateValue = '';
            if($item->DataType == 3){
                $expenseCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $expenseCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $expenseCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $expenseCateItem->CateName,
                'CateNo'=> $expenseCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $expenseCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('expense_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $expenseCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        ExpenseCateValue::where('CateID', $id)->delete();
        $data = ExpenseCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('expense_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = ExpenseCateList::find($data->ParentID);
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
        $expenseCateList =  DB::table('expense_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($expenseCateList as $expenseCate ){
            if($expenseCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            ExpenseCateValue::whereIn('CateID',$IDs)->delete();
            ExpenseCateList::destroy($IDs);
            $sql = "UPDATE expense_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from expense_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = ExpenseCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
