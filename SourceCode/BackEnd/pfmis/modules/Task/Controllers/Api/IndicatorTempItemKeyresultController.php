<?php
namespace Module\Task\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Uom;
use Module\Task\Models\Indicator;
use Module\Task\Models\IndicatorTempItemKeyresult;

class IndicatorTempItemKeyresultController extends Controller{

    public function list(Request $request){
        $data = IndicatorTempItem::search($request);
        $json['status'] = 1;
        $json['data'] = $data;
        $json['msg'] = 'Thành công!';
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('task_indicator_temp_item_keyresult', ['select' => ['KeyresultID', 'KeyresultName'], 'conditions' => []]), 'mauketquathenchot.xlsx');
    }

    public function create(){
        $IndicatorTempItemKeyresult = IndicatorTempItemKeyresult::all();
        $Uom = Uom::all();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['Uom'] = $Uom;
        $json['data']['auto'] = $auto;
        $json['data']['IndicatorTempItemKeyresult'] = $IndicatorTempItemKeyresult;
        return json_encode($json);
    }
    public function store(Request $request){

        $IndicatorTempItemKeyresult = new IndicatorTempItemKeyresult([
            'TemplateID' => $request->TemplateID,
            'IndicatorNo' => $request->IndicatorNo,
            'IndicatorName' => $request->IndicatorName,
            'ParentID' => $request->ParentID,
            'Level' => empty($request->ParentID)? 1 : $validator->ParentLevel + 1,
            'Description' => $request->Description,
            'UomID' => $request->UomID,
            'GradingType' => $request->GradingType,
            'FrequencyType' => $request->FrequencyType,
            'Rate' => $request->Rate

        ]);
        $IndicatorTempItemKeyresult->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $IndicatorTempItemKeyresult;
        return json_encode($json);
    }


    public function edit($id){
        $IndicatorTempItemKeyresult = IndicatorTempItemKeyresult::all();
        $IndicatorTempItemKeyresult = IndicatorTempItemKeyresult::where('KeyresultID', $id)->get();
        $data = IndicatorTempItemKeyresult::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['IndicatorTempItemKeyresult'] = $IndicatorTempItemKeyresult;

        return json_encode($json);
    }


    public function update(Request $request, $id){
        $master = $request->master;
        $detail = $request->detail;
        $master = (object) $master;
        $validate = [];
        $ccatelist = IndicatorTempItemKeyresult::find($id);
        $ccatelist->IndicatorName = $master->IndicatorName;
        $ccatelist->ParentID = $master->ParentID;
        $ccatelist->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }

    public function view($id){
        $IndicatorTempItemKeyresult = IndicatorTempItemKeyresult::all();
        $IndicatorTempItemKeyresult = IndicatorTempItemKeyresult::where('KeyresultID', $id)->get();
        $data = IndicatorTempItemKeyresult::find($id);
        $UomName = '';
        if($data->UomID){
            $parent = Uom::find($data->UomID);
            if(isset($parent->UomName)){
                $UomName = $parent->UomName;
            }
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['IndicatorTempItemKeyresult'] = $IndicatorTempItemKeyresult;
        $json['data']['UomName'] = $UomName;
        return json_encode($json);
    }
    public function delete($id){
        IndicatorTempItemKeyresult::where('KeyresultID', $id)->delete();
        $data = IndicatorTempItemKeyresult::find($id);
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
        IndicatorTempItemKeyresult::whereIn('KeyresultID', $IDs)->delete();
        IndicatorTempItemKeyresult::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
