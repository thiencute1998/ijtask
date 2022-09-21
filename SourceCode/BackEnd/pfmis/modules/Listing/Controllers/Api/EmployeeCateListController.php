<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\EmployeeCateList;
use Module\Listing\Models\EmployeeCateValue;
use Illuminate\Support\Facades\Validator;

class EmployeeCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = EmployeeCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $employeeCateList = EmployeeCateList::all();
        $json['data']['employeeCateList'] = $employeeCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:employee_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại nhân viên không được để trống',
            'CateNo.unique' => 'Mã loại nhân viên đã tồn tại',
            'CateName.required' => 'Tên loại nhân viên không được để trống',
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
            $query = EmployeeCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $employeeCateList = new EmployeeCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $employeeCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $employeeCateValue = '';
            if($item->DataType == 3){
                $employeeCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $employeeCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $employeeCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $employeeCateList->CateID,
                'CateName'=> $employeeCateList->CateName,
                'CateNo'=> $employeeCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $employeeCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('employee_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $employeeCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = EmployeeCateList::find($id);
        $employeeCateList = EmployeeCateList::all();
        $employeeCateValue = EmployeeCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['employeeCateList'] = $employeeCateList;
        $json['data']['employeeCateValue'] = $employeeCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $employeeCateList = EmployeeCateList::all();
        $data = EmployeeCateList::find($id);
        $employeeCateValue = EmployeeCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['employeeCateList'] = $employeeCateList;
        $json['data']['employeeCateValue'] = $employeeCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $employeeCateItem = EmployeeCateList::find($id);
        if($employeeCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:employee_cate_list',
            ],[
                'CateName.required' => 'Tên loại nhân viên không được để trống',
                'CateNo.required' => 'Mã loại nhân viên không được để trống',
                'CateNo.unique' => 'Mã loại nhân viên đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($employeeCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = EmployeeCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($employeeCateItem->ParentID != $master->ParentID && $employeeCateItem->ParentID){
            $ParentOld = EmployeeCateList::find($employeeCateItem->ParentID);
            $count = EmployeeCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $employeeCateItem->CateName = $master->CateName;
        $employeeCateItem->CateNo = $master->CateNo;
        $employeeCateItem->ParentID = $master->ParentID;
        $employeeCateItem->ParentNo = $master->ParentNo;
        $employeeCateItem->Level = $level;
        $employeeCateItem->Inactive = $master->Inactive;
        $employeeCateItem->save();
        EmployeeCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $employeeCateValue = '';
            if($item->DataType == 3){
                $employeeCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $employeeCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $employeeCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $employeeCateItem->CateName,
                'CateNo'=> $employeeCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $employeeCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('employee_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $employeeCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        EmployeeCateValue::where('CateID', $id)->delete();
        $data = EmployeeCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('employee_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = EmployeeCateList::find($data->ParentID);
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
        $employeeCateList =  DB::table('employee_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($employeeCateList as $employeeCate ){
            if($employeeCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            EmployeeCateValue::whereIn('CateID',$IDs)->delete();
            EmployeeCateList::destroy($IDs);
            $sql = "UPDATE employee_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from employee_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = EmployeeCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
