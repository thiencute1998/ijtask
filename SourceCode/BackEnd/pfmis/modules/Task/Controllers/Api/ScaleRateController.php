<?php
namespace Module\Task\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\SysAutoNumber;
use Module\Task\Models\ScaleRate;
use Module\Task\Models\ScaleRateItem;

class ScaleRateController extends Controller{

    public function list(Request $request){
        $data = ScaleRate::search($request);
        $json['status'] = 1;
        $json['data'] = $data;
        $json['msg'] = 'Thành công!';
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('task_scale_rate', ['select' => ['ScaleRateID', 'ScaleRateName'], 'conditions' => []]), 'bangdiem.xlsx');
    }

    public function create(){
        $ScaleRate = ScaleRate::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['ScaleRate'] = $ScaleRate;
        return json_encode($json);
    }
    public function store(Request $request){
//        $validator = Validator::make($request->all(), [
//            'ScaleRateName' => 'required|unique:task_scale_rate'
//        ],
//            [
//                'ScaleRateName.unique' => 'Mã bảng điểm đã tồn tại',
//            ]
//        );
//        if($validator->fails()){
//            $json['status'] = 2;
//            $json['msg'] = 'Lỗi!';
//            $json['data'] = $validator->errors();
//            return $json;
//        }

        $master = (object)$request->master;
        $detail = $request->detail;
        $iScaleRate = new ScaleRate([
            'ScaleRateName' => $master->ScaleRateName,
            'MaxLevel' => $master->MaxLevel,
            'UsingEvaluationTask' => $master->UsingEvaluationTask,
            'isDefault' => $master->isDefault,
            'Comment' => $master->Comment,
            'Inactive' => $master->Inactive,
        ]);
        $iScaleRate->save();

        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->LevelText)){
                $arrInsert[] = [
                    'ScaleRateID' => $iScaleRate->ScaleRateID,
                    'LevelInt' => $item->LevelInt,
                    'LevelChar' => $item->LevelChar,
                    'FromPoint' => $item->FromPoint,
                    'ToPoint' => $item->ToPoint,
                    'FromPoint100' => $item->FromPoint100,
                    'ToPoint100' => $item->ToPoint100,
                    'LevelInt100' => $item->LevelInt100,
                    'LevelText' => $item->LevelText
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('task_scale_rate_item')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $iScaleRate->ScaleRateID;
        return json_encode($json);
    }

    public function edit($id){
        $ScaleRate = ScaleRate::all();
        $ScaleRateItem = ScaleRateItem::where('ScaleRateID', $id)->get();
        $data = ScaleRate::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['ScaleRate'] = $ScaleRate;
        $json['data']['ScaleRateItem'] = $ScaleRateItem;
        return json_encode($json);
    }

    public function update(Request $request, $id){
        $master = $request->master;
        $detail = $request->detail;
        $master = (object) $master;
//        $validate = [];
        $cscalerate = ScaleRate::find($id);
        $cscalerate->ScaleRateName = $master->ScaleRateName;
        $cscalerate->MaxLevel = $master->MaxLevel;
        $cscalerate->UsingEvaluationTask = $master->UsingEvaluationTask;
        $cscalerate->isDefault = $master->isDefault;
        $cscalerate->Comment = $master->Comment;
        $cscalerate->Inactive = $master->Inactive;
        $cscalerate->save();
        ScaleRateItem::where('ScaleRateID', $id)->delete();
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->LevelText)){
                $arrInsert[] = [
                    'ScaleRateID' => $cscalerate->ScaleRateID,
                    'LevelInt' => $item->LevelInt,
                    'LevelChar' => $item->LevelChar,
                    'FromPoint' => $item->FromPoint,
                    'ToPoint' => $item->ToPoint,
                    'FromPoint100' => $item->FromPoint100,
                    'ToPoint100' => $item->ToPoint100,
                    'LevelInt100' => $item->LevelInt100,
                    'LevelText' => $item->LevelText
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('task_scale_rate_item')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }

    public function view($id){
        $ScaleRate = ScaleRate::all();
        $ScaleRateItem = ScaleRateItem::where('ScaleRateID', $id)->get();
        $data = ScaleRate::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['ScaleRate'] = $ScaleRate;
        $json['data']['ScaleRateItem'] = $ScaleRateItem;
        return json_encode($json);
    }

    public function delete($id){
        ScaleRateItem::where('ScaleRateID', $id)->delete();
        $data = ScaleRate::find($id);
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
        ScaleRateItem::whereIn('ScaleRateID', $IDs)->delete();
        ScaleRate::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
