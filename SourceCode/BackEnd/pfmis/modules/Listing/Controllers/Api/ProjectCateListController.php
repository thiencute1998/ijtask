<?php

namespace Module\Listing\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Project;
use Module\Listing\Models\ProjectCateList;
use Module\Listing\Models\ProjectCateValue;
use Illuminate\Support\Facades\Validator;

class ProjectCateListController extends Controller
{
    //
    public function list(Request $request){
        $data = ProjectCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $projectCateList = ProjectCateList::all();
        $json['data']['projectCateList'] = $projectCateList;
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }

    public function store(Request $request){

        $master = (object)$request->master;
        $validator = Validator::make($request->master,[
            'CateNo' => 'required|unique:project_cate_list',
            'CateName' => 'required',
        ],[
            'CateNo.required' => 'Mã loại dự án không được để trống',
            'CateNo.unique' => 'Mã loại dự án đã tồn tại',
            'CateName.required' => 'Tên loại dự án không được để trống',
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
            $query = ProjectCateList::query();
            $parent = $query->find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level +1;
            $path = $parent->Path . '#' . $master->CateNo;
        }
        $projectCateList = new ProjectCateList([
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
        $projectCateList->save();
        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $projectCateValue = '';
            if($item->DataType == 3){
                $projectCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $projectCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $projectCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $projectCateList->CateID,
                'CateName'=> $projectCateList->CateName,
                'CateNo'=> $projectCateList->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $projectCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('project_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $projectCateList->CateID;
        return json_encode($json);
    }

    public function view($id){
        $data = ProjectCateList::find($id);
        $projectCateList = ProjectCateList::all();
        $projectCateValue = ProjectCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['projectCateList'] = $projectCateList;
        $json['data']['projectCateValue'] = $projectCateValue;

        return json_encode($json);
    }

    public function edit($id){
        $projectCateList = ProjectCateList::all();
        $data = ProjectCateList::find($id);
        $projectCateValue = ProjectCateValue::where('CateID',$id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['projectCateList'] = $projectCateList;
        $json['data']['projectCateValue'] = $projectCateValue;

        return json_encode($json);
    }

    public function update(Request $request,$id){




        $master = (object)$request->master;

        $detail = $request->detail;
        $projectCateItem = ProjectCateList::find($id);
        if($projectCateItem->CateNo != $master->CateNo || $master->CateName == null){
            $validator = Validator::make($request->master,[
                'CateName'=>'required',
                'CateNo' => 'required|unique:project_cate_list',
            ],[
                'CateName.required' => 'Tên loại dự án không được để trống',
                'CateNo.required' => 'Mã loại dự án không được để trống',
                'CateNo.unique' => 'Mã loại dự án đã tồn tại',

            ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Thất bại';
                $json['data'] = $validator->errors();
                return json_encode($json);
            }
        }
        // Không sửa No, Parent, CateValue của bản ghi tổng hợp
        if($projectCateItem->Detail == 0){
            if($projectCateItem->CateNo != $master->CateNo || $projectCateItem->ParentID != $master->ParentID || $master->ProjectCateValue != null ){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }

        // set for newParent
        if($master->ParentID){
            $Parent = ProjectCateList::find($master->ParentID);
            $Parent->Detail = 0;
            $level = $Parent->Level + 1;
            $path = $Parent->Path . '#' . $master->CateNo;
            $Parent->save();
        } else {
            $level = 1;
            $path = $master->CateNo;
        }
        // set for old Parent
        if($projectCateItem->ParentID != $master->ParentID && $projectCateItem->ParentID){
            $ParentOld = ProjectCateList::find($projectCateItem->ParentID);
            $count = ProjectCateList::where('ParentID', $ParentOld->CateID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }

        $projectCateItem->CateName = $master->CateName;
        $projectCateItem->CateNo = $master->CateNo;
        $projectCateItem->ParentID = $master->ParentID;
        $projectCateItem->ParentNo = $master->ParentNo;
        $projectCateItem->Level = $level;
        $projectCateItem->Path = $path;
        $projectCateItem->Inactive = $master->Inactive;
        $projectCateItem->save();
        ProjectCateValue::where('CateID',$id)->delete();

        $arrayInsert = [];
        foreach($detail as $key=> $item){
            $item = (object)$item;
            $projectCateValue = '';
            if($item->DataType == 3){
                $projectCateValue = date('Y-m-d',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else if($item->DataType == 4){
                $projectCateValue = date('Y-m-d H:i',strtotime(str_replace('/','-',$item->CateValue)));
            }
            else{
                $projectCateValue = $item->CateValue;
            }
            $arrayInsert[] = [
                'CateID'=> $id,
                'CateName'=> $projectCateItem->CateName,
                'CateNo'=> $projectCateItem->CateNo,
                'DataType'=> $item->DataType,
                'CateValue'=> $projectCateValue,
                'Description'=> $item->Description
            ];
        }
        if(!empty($arrayInsert)){
            DB::table('project_cate_value')->insert($arrayInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $projectCateItem->CateID;
        return json_encode($json);
    }

    public function delete($id){
        ProjectCateValue::where('CateID', $id)->delete();
        $data = ProjectCateList::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('project_cate_list')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = ProjectCateList::find($data->ParentID);
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
        $projectCateList =  DB::table('project_cate_list')->whereIn('CateID', $IDs)->get();
        foreach ($projectCateList as $projectCate ){
            if($projectCate->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            ProjectCateValue::whereIn('CateID',$IDs)->delete();
            ProjectCateList::destroy($IDs);
            $sql = "UPDATE project_cate_list
                SET Detail = 1
                WHERE CateID NOT IN (SELECT ParentID FROM (select ParentID from project_cate_list WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = ProjectCateList::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('CateID');
        $data = $query->orderBy('CateNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
