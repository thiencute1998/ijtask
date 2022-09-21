<?php
namespace Module\Task\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Module\Task\Models\IndicatorCateList;
use Module\Task\Models\IndicatorCateValue;

class IndicatorCateListController extends Controller{

    public function list(Request $request){
        $data = IndicatorCateList::search($request);
        $json['status'] = 1;
        $json['data'] = $data;
        $json['msg'] = 'Thành công!';
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('task_indicator_Cate_list', ['select' => ['CateID', 'CateName'], 'conditions' => []]), 'loaichitieu.xlsx');
    }

    public function create(){
        $indicatorCateList = IndicatorCateList::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['IndicatorCateList'] = $indicatorCateList;
        return json_encode($json);
    }
    public function store(Request $request){

        $master = (object)$request->master;
        $detail = $request->detail;

        $icatelist = new IndicatorCateList([
            'CateName' => $master->CateName,
            'Inactive' => $master->Inactive
        ]);
        $icatelist->save();

        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->Description)){
                $IndicatorCateValue = '';
                switch ($item->DataType){
                    case 1:
                        $IndicatorCateValue = $item->CateValue;
                        break;
                    case 2:
                        $IndicatorCateValue = $item->CateValue;
                        break;
                    case 3:
                        $IndicatorCateValue = date('Y-m-d', strtotime(str_replace('/', '-', $item->CateValue)));
                        break;
                    case 4:
                        $IndicatorCateValue = date('Y-m-d H:i', strtotime(str_replace('/', '-', $item->CateValue)));
                        break;
                    case 5:
                        $IndicatorCateValue = $item->CateValue;
                        break;
                    case 6:
                        $IndicatorCateValue = $item->CateValue;
                        break;
                    default:
                        break;
                }
                $IndicatorConvertedValue = '';
                switch ($item->DataType){
                    case 1:
                        $IndicatorConvertedValue = $item->ConvertedValue;
                        break;
                    case 2:
                        $IndicatorConvertedValue = $item->ConvertedValue;
                        break;
                    case 3:
                        $IndicatorConvertedValue = date('Y-m-d', strtotime(str_replace('/', '-', $item->ConvertedValue)));
                        break;
                    case 4:
                        $IndicatorConvertedValue = date('Y-m-d H:i', strtotime(str_replace('/', '-', $item->ConvertedValue)));
                        break;
                    case 5:
                        $IndicatorConvertedValue = $item->ConvertedValue;
                        break;
                    case 6:
                        $IndicatorConvertedValue = $item->ConvertedValue;
                        break;
                    default:
                        break;
                }
                $arrInsert[] = [
                    'CateID' => $icatelist->CateID,
                    'Description' => $item->Description,
                    'DataType' => $item->DataType,
                    'CateValue' => $IndicatorCateValue,
                    'ConvertedValue' => $IndicatorConvertedValue
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('task_indicator_cate_value')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $icatelist->CateID;
        return json_encode($json);
    }


    public function edit($id){
        $indicatorCateList = IndicatorCateList::all();
        $indicatorCateValue = IndicatorCateValue::where('CateID', $id)->get();
        $data = IndicatorCateList::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['IndicatorCateList'] = $indicatorCateList;
        $json['data']['IndicatorCateValue'] = $indicatorCateValue;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $master = $request->master;
        $detail = $request->detail;
        $master = (object) $master;
        $validate = [];
        $ccatelist = IndicatorCateList::find($id);
        $ccatelist->CateName = $master->CateName;
        $ccatelist->ParentID = $master->ParentID;
        $ccatelist->Inactive = $master->Inactive;
        $ccatelist->save();
        IndicatorCateValue::where('CateID', $id)->delete();
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->Description)){
                $IndicatorCateValue = '';
                switch ($item->DataType){
                    case 1:
                        $IndicatorCateValue = $item->CateValue;
                        break;
                    case 2:
                        $IndicatorCateValue = $item->CateValue;
                        break;
                    case 3:
                        $IndicatorCateValue = date('Y-m-d', strtotime(str_replace('/', '-', $item->CateValue)));
                        break;
                    case 4:
                        $IndicatorCateValue = date('Y-m-d H:i', strtotime(str_replace('/', '-', $item->CateValue)));
                        break;
                    case 5:
                        $IndicatorCateValue = $item->CateValue;
                        break;
                    case 6:
                        $IndicatorCateValue = $item->CateValue;
                        break;
                    default:
                        break;
                }
                $arrInsert[] = [
                    'CateID' => $ccatelist->CateID,
                    'Description' => $item->Description,
                    'DataType' => $item->DataType,
                    'CateValue' => $IndicatorCateValue,
                    'ConvertedValue' => $IndicatorConvertedValue,
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('task_indicator_cate_value')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }

    public function view($id){
        $indicatorCateList = IndicatorCateList::all();
        $indicatorCateValue = IndicatorCateValue::where('CateID', $id)->get();
        $data = IndicatorCateList::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['IndicatorCateList'] = $indicatorCateList;
        $json['data']['IndicatorCateValue'] = $indicatorCateValue;
        return json_encode($json);
    }
    public function delete($id){
        IndicatorCateValue::where('CateID', $id)->delete();
        $data = IndicatorCateList::find($id);
        if($data){
            $data->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        IndicatorCateValue::whereIn('CateID', $IDs)->delete();
        IndicatorCateList::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
