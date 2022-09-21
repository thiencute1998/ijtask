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
use Module\Listing\Models\Uom;
use Module\Task\Models\Indicator;
use Module\Task\Models\IndicatorTempItem;

class IndicatorTempItemController extends Controller{

    public function list(Request $request){
        $data = IndicatorTempItem::search($request);
        $json['status'] = 1;
        $json['data'] = $data;
        $json['msg'] = 'Thành công!';
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('task_indicator_temp_item', ['select' => ['TempitemID', 'IndicatorName'], 'conditions' => []]), 'maubangbieu.xlsx');
    }

    public function create(){
        $IndicatorTempItem = IndicatorTempItem::all();
        $Uom = Uom::all();
        if(old('IndicatorNo')){
            $auto = old('IndicatorNo');
        }else{
            $auto = AutoNo::genNewNumberNoTable('task_indicator_temp_item', 'TempitemID', 'IndicatorNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['Uom'] = $Uom;
        $json['data']['auto'] = $auto;
        $json['data']['IndicatorTempItem'] = $IndicatorTempItem;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'IndicatorNo' => 'required|unique:IndicatorTempItem'
        ],
            [
                'IndicatorNo.required' => 'Mã chỉ tiêu không được để trống',
                'IndicatorNo.unique' => 'Mã chỉ tiêu đã tồn tại',
            ]
        );
        $autoModel = SysAutoNumber::where('NumberKey', 'task_indicator_temp_item')->first();
        AutoNo::storeValidateIDNo($validator, $request, 'task_indicator_temp_item','TemplateID', 'IndicatorNo', $autoModel, 'chỉ tiêu');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $IndicatorTempItem = new IndicatorTempItem([
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
        $IndicatorTempItem->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $IndicatorTempItem;
        return json_encode($json);
    }


    public function edit($id){
        $IndicatorTempItem = IndicatorTempItem::all();
        $IndicatorTempItem = IndicatorTempItem::where('TempitemID', $id)->get();
        $data = IndicatorTempItem::find($id);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['IndicatorTempItem'] = $IndicatorTempItem;

        return json_encode($json);
    }


    public function update(Request $request, $id){
        DB::beginTransaction();
        $IndicatorTempItem = IndicatorTempItem::find($id);
        if(!$IndicatorTempItem){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($IndicatorTempItem->Prefix.$IndicatorTempItem->IndicatorNo.$IndicatorTempItem->Suffix != $request->IndicatorNo){
            $validator = Validator::make($request->all(), [
                'IndicatorNo' => 'required|unique:IndicatorTempItem'
            ],
                [
                    'IndicatorNo.required' => 'Mã chỉ tiêu không được để trống',
                    'IndicatorNo.unique' => 'Mã chỉ tiêu đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'task_indicator_temp_item')->first();
            AutoNo::UpdateValidate($validator, $request, $Indicator,'task_indicator_temp_item', $autoModel, 'Chỉ tiêu đánh giá công việc');

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $IndicatorTempItem->IndicatorNo = $request->IndicatorNo;
            $IndicatorTempItem->Level = empty($request->ParentID)? 1 : $validator->ParentLevel + 1;
        }
        $IndicatorParentLevel = 0;
        if($request->ParentID){
            $IndicatorParent = Indicator::find($request->ParentID);
            if($IndicatorParent){
                $IndicatorParentLevel = $IndicatorParent->Level;
            }
        }
        $IndicatorTempItem->TemplateID = $request->TemplateID;
        $IndicatorTempItem->IndicatorName = $request->IndicatorName;
        $IndicatorTempItem->ParentID = $request->ParentID;
        $IndicatorTempItem->Description = $request->Description;
        $IndicatorTempItem->UomName = $request->UomName;
        $IndicatorTempItem->GradingType = $request->GradingType;
        $IndicatorTempItem->FrequencyType = $request->FrequencyType;
        $IndicatorTempItem->Rate = $request->Rate;

        if($IndicatorTempItem->save()){
            DB::commit();
        }else{
            DB::rollBack();
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }

    public function view($id){
        $IndicatorTempItem = IndicatorTempItem::all();
        $IndicatorTempItem = IndicatorTempItem::where('TempitemID', $id)->get();
        $data = IndicatorTempItem::find($id);
        $data->IndicatorNo = $data->Prefix.$data->IndicatorNo.$data->Suffix;
        $ParentName = '';
        if($data->ParentID){
            $parent = Indicator::find($data->ParentID);
            if(isset($parent->IndicatorName)){
                $ParentName = $parent->IndicatorName;
            }
        }
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
        $json['data']['IndicatorTempItem'] = $IndicatorTempItem;
        $json['data']['ParentName'] = $ParentName;
        $json['data']['UomName'] = $UomName;
        return json_encode($json);
    }
    public function delete($id){
        IndicatorTempItem::where('TempitemID', $id)->delete();
        $data = IndicatorTempItem::find($id);
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
        IndicatorTempItem::whereIn('TempitemID', $IDs)->delete();
        IndicatorTempItem::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
