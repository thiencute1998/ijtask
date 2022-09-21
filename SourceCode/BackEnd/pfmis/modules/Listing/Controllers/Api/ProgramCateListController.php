<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Program;
use Module\Listing\Models\ProgramCateList;
use Module\Listing\Models\ProgramCateValue;
use Illuminate\Support\Facades\Validator;

class ProgramCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = ProgramCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $programCateList = ProgramCateList::all();
        $json['data']['programCateList'] = $programCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:program_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại chương trình mục tiêu không được để trống',
            'CateNo.unique' => 'Mã loại chương trình mục tiêu đã tồn tại',
            'CateName.required' => 'Tên loại chương trình mục tiêu không được để trống',
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
            $query = ProgramCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
        }
        $programCateList = new ProgramCateList([
            'CateName'=> $master->CateName,
            'CateNo'=> $master->CateNo,
            'ParentID'=> $master->ParentID,
            'ParentNo'=> $master->ParentNo,
            'Inactive'=> $master->Inactive,
            'Level' => $level,
            'Detail' => 1,
            'isSystemSetting'=> 0
        ]);
        $programCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $programCateValue = '';
            if($item->DataType == 3){
                $programCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $programCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $programCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $programCateList->CateID,
                'CateName'=> $programCateList->CateName,
                'CateNo'=> $programCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $programCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('program_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $programCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = ProgramCateList::find($id);
        $programCateList = ProgramCateList::all();
        $programCateValue = ProgramCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['programCateList'] = $programCateList;
        $json['data']['programCateValue'] = $programCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $programCateList = ProgramCateList::all();
        $data = ProgramCateList::find($id);
        $programCateValue = ProgramCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['programCateList'] = $programCateList;
        $json['data']['programCateValue'] = $programCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){
        $master = (object)$request->master;

        $detail = $request->detail;
        $programCateItem = ProgramCateList::find($id);
        if($programCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:program_cate_list',
            ],[
                'CateName.required' => 'Tên loại chương trình mục tiêu không được để trống',
                'CateNo.required' => 'Mã loại chương trình mục tiêu không được để trống',
                'CateNo.unique' => 'Mã loại chương trình mục tiêu đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        if($programCateItem->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        // set for newParent
        if($master->ParentID){
            $Parent = ProgramCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $Parent->save();
        } else {
            $level = 1;
        }
        // set for old Parent
        if($programCateItem->ParentID != $master->ParentID && $programCateItem->ParentID){
            $ParentOld = ProgramCateList::find($programCateItem->ParentID);
            $count = ProgramCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }
        //  path old

        $programCateItem->CateName = $master->CateName;
        $programCateItem->CateNo = $master->CateNo;
        $programCateItem->ParentID = $master->ParentID;
        $programCateItem->ParentNo = $master->ParentNo;
        $programCateItem->Level = $level;
        $programCateItem->Inactive = $master->Inactive;
        $programCateItem->save();
        ProgramCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $programCateValue = '';
            if($item->DataType == 3){
                $programCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $programCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $programCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $programCateItem->CateName,
                'CateNo'=> $programCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $programCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('program_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $programCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        ProgramCateValue::where('CateID', $id)->delete();
        $data = ProgramCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('program_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = ProgramCateList::find($data->ParentID);
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
        $programCateList =  DB::table('program_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($programCateList as $programCate ){
            if($programCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            ProgramCateValue::whereIn('CateID',$IDs)->delete();
            ProgramCateList::destroy($IDs);
            $sql = "UPDATE program_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from program_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = ProgramCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
