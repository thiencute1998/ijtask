<?php

namespace Module\Task\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Uom;
use Module\SysAdmin\Models\SysSetup;
use Module\Task\Models\BinaryData;
use Module\Task\Models\IndicatorTemp;
use Module\Task\Models\IndicatorTempItem;
use Module\Task\Models\IndicatorTempItemFeedback;
use Module\Task\Models\IndicatorTempItemKeyresult;

class IndicatorTempController extends Controller
{

    public function list(Request $request)
    {
        $data = IndicatorTemp::search($request);
        $json['status'] = 1;
        $json['data'] = $data;
        $json['msg'] = 'Thành công!';
        return json_encode($json);
    }

    public function export(Request $request)
    {
        return Excel::download(new ModelExport('task_indicator_temp', ['select' => ['TemplateID', 'TemplateName'], 'conditions' => []]), 'maubangchitieu.xlsx');
    }

    public function create()
    {
        $indicatorTemp = IndicatorTemp::all();
        $Uom = Uom::all();
        $BinaryData = BinaryData::all();
        if (old('TemplateNo')) {
            $auto = old('TemplateNo');
        } else {
            $auto = AutoNo::genNewNumberNoTable('task_indicator_temp', 'TemplateID', 'TemplateNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['Uom'] = $Uom;
        $json['data']['BinaryData'] = $BinaryData;
        $json['data']['auto'] = $auto;
        $json['data']['IndicatorTemp'] = $indicatorTemp;
        return json_encode($json);
    }

    public function store(Request $request)
    {
        $master = (object)$request->master;
        $detail = $request->detail;
        $IndicatorTempItemKeyresult = $request->IndicatorTempItemKeyresult;
        $IndicatorTempItemFeedback = $request->IndicatorTempItemFeedback;

        $itemp = new IndicatorTemp([
            'TemplateNo' => $master->TemplateNo,
            'TemplateName' => $master->TemplateName,
            'IndicatorType' => $master->IndicatorType,
            //'EvaluationMethod' => $master->EvaluationMethod,
            'Inactive' => $master->Inactive
        ]);
        $itemp->save();

        $arrInsert = [];
        $id = $itemp->TemplateID;
        foreach ($detail as $item) {
            $item = (object)$item;
            if (!empty($item->IndicatorName)) {
                $arrInsert[] = [
                    'TemplateID' => $id,
                    'IndicatorID' => $item->IndicatorID,
                    'IndicatorNo' => $item->IndicatorNo,
                    'IndicatorName' => $item->IndicatorName,
                    'Description' => $item->Description,
                    'UomID' => $item->UomID,
                    'UomName' => $item->UomName,
                    'GradingType' => $item->GradingType,
                    'FrequencyType' => $item->FrequencyType,
                    'GradingMethod' => $item->GradingMethod,
                    'EvaluationMethod' => $item->EvaluationMethod,
                    'Rate' => $item->Rate,
                    'LineIDTemp' => $item->LineIDTemp
                ];
            }
        }

        if (!empty($arrInsert)) {
            DB::table('task_indicator_temp_item')->insert($arrInsert);
        }

        $indicatorTempItem = IndicatorTempItem::select(['TransItemID', 'LineIDTemp', 'EvaluationMethod'])->where('TemplateID', $id)->get();
        //Add to Keyresult
        $arrInsertKeyresult = [];
        //Add to Feedback
        $arrInsertFeedback = [];

        foreach ($indicatorTempItem as $item) {
            if (isset($IndicatorTempItemKeyresult[$item->LineIDTemp]) && !empty($IndicatorTempItemKeyresult[$item->LineIDTemp])) {
                $LineIDTemp = $item->LineIDTemp;
                foreach ($IndicatorTempItemKeyresult[$LineIDTemp] as $itemChild) {
                    $arrInsertKeyresult[] = [
                        'TemplateID' => $id,
                        'TransItemID' => $item->TransItemID,
                        'KeyresultName' => $itemChild['KeyresultName'],
                        //'Description' => $itemChild['Description'],
                        'UomID' => $itemChild['UomID'],
                        'ScaleRateID' => $itemChild['ScaleRateID'],
                        'BinaryDataID' => $itemChild['BinaryDataID'],
                        'KeyresultType' => $itemChild['KeyresultType'],
                        'Rate' => $itemChild['Rate'],
                        'IndicatorID' => $itemChild['IndicatorID'],
                        'IndicatorNo' => $itemChild['IndicatorNo'],
                        'IndicatorName' => $itemChild['IndicatorName'],
                        'GradingMethod' => $itemChild['GradingMethod'],
                        'EvaluationMethod' => $item->EvaluationMethod,
                    ];
                }
            }
            if (isset($IndicatorTempItemFeedback[$item->LineIDTemp]) && !empty($IndicatorTempItemFeedback[$item->LineIDTemp])) {
                $LineIDTemp = $item->LineIDTemp;
                foreach ($IndicatorTempItemFeedback[$LineIDTemp] as $itemChild) {

                    $arrInsertFeedback[] = [
                        'TemplateID' => $id,
                        'TransItemID' => $item->TransItemID,
                        'FeedbackName' => $itemChild['FeedbackName'],
                        //'Description' => $itemChild['Description'],
                        'BinaryDataID' => $itemChild['BinaryDataID'],
                        'IsBinaryData' => $itemChild['IsBinaryData'],
                        'IndicatorID' => $itemChild['IndicatorID'],
                        'IndicatorNo' => $itemChild['IndicatorNo'],
                        'IndicatorName' => $itemChild['IndicatorName'],
                        'ScaleRateID' => $itemChild['ScaleRateID'],
                        'GradingMethod' => $itemChild['GradingMethod'],
                        'EvaluationMethod' => $item->EvaluationMethod,
                    ];
                }
            }

        }
        if (!empty($arrInsertKeyresult)) {
            DB::table('task_indicator_temp_item_keyresult')->insert($arrInsertKeyresult);
        }

        if (!empty($arrInsertFeedback)) {
            DB::table('task_indicator_temp_item_feedback')->insert($arrInsertFeedback);
        }

        //$json['IndicatorTempItemFeedback'] = $IndicatorTempItemFeedback; bug
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $itemp->TemplateID;
        return json_encode($json);
    }


    public function edit($id)
    {
        $Uom = Uom::all();
        $BinaryData = BinaryData::all();
        $indicatorTemp = IndicatorTemp::all();
        $indicatorTempItem = IndicatorTempItem::select(DB::raw('TransItemID, TemplateID, IndicatorID, IndicatorNo,
            IndicatorName, Description, ParentID, Level, Detail, UomID, UomName, EvaluationMethod, GradingType, GradingMethod,
            FrequencyType, Rate, Prefix, Suffix, NumberValue, TransItemID as LineIDTemp
            '))->where('TemplateID', $id)->orderBy('TransItemID', 'asc')->get();
        $data = IndicatorTemp::find($id);

        $IndicatorTempItemKeyResult = IndicatorTempItemKeyresult::where(['TemplateID' => $id])->orderBy('KeyresultID', 'asc')->get();
        $json['data']['IndicatorTempItemKeyResult'] = $IndicatorTempItemKeyResult;

        $IndicatorTempItemFeedback = IndicatorTempItemFeedback::where(['TemplateID' => $id])->orderBy('FeedbackID', 'asc')->get();
        $json['data']['IndicatorTempItemFeedback'] = $IndicatorTempItemFeedback;

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['IndicatorTemp'] = $indicatorTemp;
        $json['data']['IndicatorTempItem'] = $indicatorTempItem;
        $json['data']['Uom'] = $Uom;
        $json['data']['BinaryData'] = $BinaryData;
        return json_encode($json);
    }


    public function update(Request $request, $id)
    {
        $master = $request->master;
        $detail = $request->detail;
        $master = (object)$master;
        $IndicatorTempItemKeyresult = $request->IndicatorTempItemKeyresult;
        $IndicatorTempItemFeedback = $request->IndicatorTempItemFeedback;
        $validate = [];
        $ctemp = IndicatorTemp::find($id);
        $ctemp->TemplateNo = $master->TemplateNo;
        $ctemp->TemplateName = $master->TemplateName;
        $ctemp->IndicatorType = $master->IndicatorType;
        //$ctemp->EvaluationMethod = $master->EvaluationMethod;
        $ctemp->Inactive = $master->Inactive;
        $ctemp->save();
        IndicatorTempItem::where('TemplateID', $id)->delete();
        $arrInsert = [];
        foreach ($detail as $item) {
            $item = (object)$item;
            if (!empty($item->IndicatorName)) {
                $arrInsert[] = [
                    'TemplateID' => $ctemp->TemplateID,
                    'IndicatorID' => $item->IndicatorID,
                    'IndicatorNo' => $item->IndicatorNo,
                    'IndicatorName' => $item->IndicatorName,
                    'Description' => $item->Description,
                    'UomID' => $item->UomID,
                    'UomName' => $item->UomName,
                    'GradingType' => $item->GradingType,
                    'FrequencyType' => $item->FrequencyType,
                    'GradingMethod' => $item->GradingMethod,
                    'EvaluationMethod' => $item->EvaluationMethod,
                    'Rate' => $item->Rate,
                    'LineIDTemp' => $item->LineIDTemp
                ];
            }
        }

        if (!empty($arrInsert)) {
            DB::table('task_indicator_temp_item')->insert($arrInsert);
        }

        $indicatorTempItem = IndicatorTempItem::select(['TransItemID', 'LineIDTemp'])->where('TemplateID', $id)->get();
        //Add Keyresult
        IndicatorTempItemKeyresult::where('TemplateID', $id)->delete();
        $arrInsertKeyresult = [];
        //Add Feedback
        IndicatorTempItemFeedback::where('TemplateID', $id)->delete();
        $arrInsertFeedback = [];

        foreach ($indicatorTempItem as $item) {
                if (isset($IndicatorTempItemKeyresult[$item->LineIDTemp]) && !empty($IndicatorTempItemKeyresult[$item->LineIDTemp])) {
                $LineIDTemp = $item->LineIDTemp;
                foreach ($IndicatorTempItemKeyresult[$LineIDTemp] as $itemChild) {
                    $arrInsertKeyresult[] = [
                        'TemplateID' => $id,
                        'TransItemID' => $item->TransItemID,
                        'ScaleRateID' => $itemChild['ScaleRateID'],
                        'BinaryDataID' => $itemChild['BinaryDataID'],
                        'KeyresultName' => $itemChild['KeyresultName'],
                        //'Description' => $itemChild['Description'],
                        'UomID' => $itemChild['UomID'],
                        'KeyresultType' => $itemChild['KeyresultType'],
                        'Rate' => $itemChild['Rate'],
                        'IndicatorID' => $itemChild['IndicatorID'],
                        'IndicatorNo' => $itemChild['IndicatorNo'],
                        'IndicatorName' => $itemChild['IndicatorName'],
                        'GradingMethod' => $itemChild['GradingMethod'],
                        'EvaluationMethod' => $item->EvaluationMethod,
                    ];
                }
            }
            if (isset($IndicatorTempItemFeedback[$item->LineIDTemp]) && !empty($IndicatorTempItemFeedback[$item->LineIDTemp])) {
                $LineIDTemp = $item->LineIDTemp;
                foreach ($IndicatorTempItemFeedback[$LineIDTemp] as $itemChild) {
                    $arrInsertFeedback[] = [
                        'TemplateID' => $id,
                        'TransItemID' => $item->TransItemID,
                        'BinaryDataID' => $itemChild['BinaryDataID'],
                        'FeedbackName' => $itemChild['FeedbackName'],
                        //'Description' => $itemChild['Description'],
                        'IsBinaryData' => $itemChild['IsBinaryData'],
                        'IndicatorID' => $itemChild['IndicatorID'],
                        'IndicatorNo' => $itemChild['IndicatorNo'],
                        'IndicatorName' => $itemChild['IndicatorName'],
                        'ScaleRateID' => $itemChild['ScaleRateID'],
                        'GradingMethod' => $itemChild['GradingMethod'],
                        'EvaluationMethod' => $item->EvaluationMethod,
                    ];
                }
            }
        }
        if (!empty($arrInsertKeyresult)) {
            DB::table('task_indicator_temp_item_keyresult')->insert($arrInsertKeyresult);
        }
        //return('okkkkkkkkkk');
        if (!empty($arrInsertFeedback)) {
            DB::table('task_indicator_temp_item_feedback')->insert($arrInsertFeedback);
        }
        //return('okkkkkkkk');
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }

    public function view($id)
    {
        $indicatorTemp = IndicatorTemp::all();
        $indicatorTempItem = IndicatorTempItem::where('TemplateID', $id)->get();
        $data = IndicatorTemp::find($id);
        $Uom = Uom::all();
        $BinaryData = BinaryData::all();
        $GradingType = [
            '1' => 'Điểm thường',
            '2' => 'Điểm thưởng',
            '3' => 'Điểm phạt'
        ];
        $FrequencyType = [
            '1' => 'Năm',
            '2' => '6 tháng',
            '3' => 'Quý',
            '4' => 'Tháng',
            '5' => 'Tuần',
            '6' => 'Ngày',
            '7' => 'Vụ việc'
        ];

        $IndicatorTempItemKeyResult = IndicatorTempItemKeyresult::where(['TemplateID' => $id])->get();
        $json['data']['IndicatorTempItemKeyResult'] = $IndicatorTempItemKeyResult;

        $IndicatorTempItemFeedback = IndicatorTempItemFeedback::where(['TemplateID' => $id])->get();
        $json['data']['IndicatorTempItemFeedback'] = $IndicatorTempItemFeedback;

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['IndicatorTemp'] = $indicatorTemp;
        $json['data']['IndicatorTempItem'] = $indicatorTempItem;
        $json['data']['Uom'] = $Uom;
        $json['data']['BinaryData'] = $BinaryData;
        $json['data']['GradingTypeName'] = $GradingType;
        $json['data']['FrequencyTypeName'] = $FrequencyType;
        return json_encode($json);
    }

    public function delete($id)
    {
        IndicatorTempItem::where('TemplateID', $id)->delete();
        IndicatorTempItemKeyresult::where('TemplateID', $id)->delete();
        IndicatorTempItemFeedback::where('TemplateID', $id)->delete();
        $data = IndicatorTemp::find($id);
        if ($data) {
            $data->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        } else {
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function deleteArray(Request $request)
    {
        $IDs = $request->post('array_id');
        IndicatorTempItem::whereIn('TemplateID', $IDs)->delete();
        IndicatorTempItemKeyresult::whereIn('TemplateID', $IDs)->delete();
        IndicatorTempItemFeedback::whereIn('TemplateID', $IDs)->delete();
        IndicatorTemp::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getList(Request $request){
        $data = null;
        $query = IndicatorTemp::query();
        $where = [];
        if($request->post('search')){
            $query->where('TemplateName', 'like', '%'.$request->search.'%');
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $data = $query->paginate($per_page);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
}
