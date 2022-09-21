<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\Company;
use Module\Listing\Models\Employee;
use Module\Listing\Models\TaskCateList;
use Module\Listing\Models\TaskCateValue;

class TaskCateListController extends Controller{

    public function list(Request $request){
        $data = TaskCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('company', ['select' => ['CompanyNo', 'CompanyName'], 'conditions' => []]), 'donvi.xlsx');
    }

    public function create(){
        $taskCateList = TaskCateList::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $taskCateList;
        return json_encode($json);
    }
    public function store(Request $request){
        $master = (object)$request->master;
        $detail = $request->detail;
        $tcatelist = new TaskCateList([
            'CateName' => $master->CateName,
            'ParentID' => $master->ParentID,
            'Inactive' => $master->Inactive
        ]);
        $tcatelist->save();
        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->Description)){
                $TaskCateValue = '';
                switch ($item->DataType){
                    case 1:
                        $TaskCateValue = $item->CateValue;
                        break;
                    case 2:
                        $TaskCateValue = $item->CateValue;
                        break;
                    case 3:
                        $TaskCateValue = date('Y-m-d', strtotime(str_replace('/', '-', $item->CateValue)));
                        break;
                    case 4:
                        $TaskCateValue = date('Y-m-d H:i', strtotime(str_replace('/', '-', $item->CateValue)));
                        break;
                    case 5:
                        $TaskCateValue = $item->CateValue;
                        break;
                    case 6:
                        $TaskCateValue = $item->CateValue;
                        break;
                    default:
                        break;
                }
                $arrInsert[] = [
                    'CateID' => $tcatelist->CateID,
                    'Description' => $item->Description,
                    'DataType' => $item->DataType,
                    'CateValue' => $TaskCateValue
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('task_cate_value')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $tcatelist->CateID;
        return json_encode($json);
    }


    public function edit($id){
        $taskCateList = TaskCateList::all();
        $taskCateValue = TaskCateValue::where('CateID', $id)->get();
        $data = TaskCateList::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['taskCateList'] = $taskCateList;
        $json['data']['taskCateValue'] = $taskCateValue;

        return json_encode($json);
    }


    public function update(Request $request, $id){
        $master = $request->master;
        $detail = $request->detail;
        $validate = [];
        $master = (object) $master;
        $tcatelist = TaskCateList::find($id);
        $tcatelist->CateName = $master->CateName;
        $tcatelist->ParentID = $master->ParentID;
        $tcatelist->Inactive = $master->Inactive;
        $tcatelist->save();
        TaskCateValue::where('CateID', $id)->delete();
        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->Description)){
                $TaskCateValue = '';
                switch ($item->DataType){
                    case 1:
                        $TaskCateValue = $item->CateValue;
                        break;
                    case 2:
                        $TaskCateValue = $item->CateValue;
                        break;
                    case 3:
                        $TaskCateValue = date('Y-m-d', strtotime(str_replace('/', '-', $item->CateValue)));
                        break;
                    case 4:
                        $TaskCateValue = date('Y-m-d H:i', strtotime(str_replace('/', '-', $item->CateValue)));
                        break;
                    case 5:
                        $TaskCateValue = $item->CateValue;
                        break;
                    case 6:
                        $TaskCateValue = $item->CateValue;
                        break;
                    default:
                        break;
                }
                $arrInsert[] = [
                    'CateID' => $tcatelist->CateID,
                    'Description' => $item->Description,
                    'DataType' => $item->DataType,
                    'CateValue' => $TaskCateValue
                ];
            }
        }
        if(!empty($arrInsert)){
            DB::table('task_cate_value')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;

        return json_encode($json);
    }

    public function view($id){
        $taskCateList = TaskCateList::all();
        $taskCateValue = TaskCateValue::where('CateID', $id)->get();
        $data = TaskCateList::find($id);

        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['taskCateList'] = $taskCateList;
        $json['data']['taskCateValue'] = $taskCateValue;

        return json_encode($json);
    }
    public function delete($id){
        TaskCateValue::where('CateID', $id)->delete();
        $data = TaskCateList::find($id);
        if($data){
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
        TaskCateValue::whereIn('CateID', $IDs)->delete();
        TaskCateList::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
