<?php

namespace Module\Task\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Uom;
use Module\Task\Models\BinaryData;
use Module\Task\Models\IndicatorTable;
use Module\Task\Models\IndicatorTableItem;
use Module\Task\Models\IndicatorTableItemFeedback;
use Module\Task\Models\IndicatorTableItemKeyresult;
use Module\Task\Models\IndicatorTablePer;
use Module\Task\Models\IndicatorTemp;
use Module\Task\Models\IndicatorTempItem;
use Module\Task\Models\IndicatorTempItemFeedback;
use Module\Task\Models\IndicatorTempItemKeyresult;

class IndicatorTableController extends Controller
{

    public function list(Request $request)
    {
        $data = IndicatorTable::search($request);
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
        $Uom = Uom::all();
        $auto = AutoNo::genNewNumberNoTable('task_indicator_table', 'TableID', 'TableNo');
        $BinaryData = BinaryData::all();
        $json['data']['BinaryData'] = $BinaryData;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['Uom'] = $Uom;
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }

    public function store(Request $request)
    {
        $master = (object)$request->master;
        $detail = $request->detail;
        $IndicatorTableItemKeyresult = $request->IndicatorTableItemKeyresult;
        $IndicatorTableItemFeedback = $request->IndicatorTableItemFeedback;

        $itable = new IndicatorTable([
            'TableNo' => $master->TableNo,
            'TableName' => $master->TableName,
            'IndicatorType' => $master->IndicatorType,
            'Inactive' => $master->Inactive,
            'PeriodType' => $master->PeriodType,
            'FromDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->FromDate))),
            'ToDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->ToDate)))
        ]);
        if($itable->IndicatorType == 1){
            $itable->CompanyID = $master->CompanyID;
            $itable->CompanyNo = $master->CompanyNo;
            $itable->CompanyName = $master->CompanyName;
        }elseif($itable->IndicatorType == 2){
            $itable->EmployeeID = $master->EmployeeID;
            $itable->EmployeeNo = $master->EmployeeNo;
            $itable->EmployeeName = $master->EmployeeName;
        }
        $itable->save();

        $id = $itable->TableID;
        $arrInsert = [];
        foreach ($detail as $item) {
            $item = (object)$item;
            if (!empty($item->IndicatorID)) {
                $arrInsert[] = [
                    'TableID' => $id,
                    'TableNo' => $itable->TableNo,
                    'TableName' => $itable->TableName,
                    'Description' => $item->Description,
                    'IndicatorID' => $item->IndicatorID,
                    'IndicatorNo' => $item->IndicatorNo,
                    'IndicatorName' => $item->IndicatorName,
                    'EvaluationMethod' => $item->EvaluationMethod,
                    'UomID' => $item->UomID,
                    'UomName' => $item->UomName,
                    'GradingType' => $item->GradingType,
                    'ObjectiveRate' => $item->ObjectiveRate,
                    'ObjectiveIndex' => $item->ObjectiveIndex,
                    'FrequencyType' => $item->FrequencyType,
                    'LineIDTemp' => $item->LineIDTemp
                ];
            }
        }

        if (!empty($arrInsert)) {
            DB::table('task_indicator_table_item')->insert($arrInsert);
        }
        $indicatorTableItem = IndicatorTableItem::select(['TableItemID', 'LineIDTemp', 'EvaluationMethod'])->where('TableID', $id)->get();

        //Add to Keyresult
        $arrInsertKeyresult = [];
        //Add to Feedback
        $arrInsertFeedback = [];

        foreach ($indicatorTableItem as $item) {
            if (isset($IndicatorTableItemKeyresult[$item->LineIDTemp]) && !empty($IndicatorTableItemKeyresult[$item->LineIDTemp])) {
                $LineIDTemp = $item->LineIDTemp;
                foreach ($IndicatorTableItemKeyresult[$LineIDTemp] as $itemChild) {
                    $arrInsertKeyresult[] = [
                        'TableID' => $id,
                        'TableItemID' => $item->TableItemID,
                        'KeyresultName' => $itemChild['KeyresultName'],
                        'Description' => $itemChild['Description'],
                        'UomID' => $itemChild['UomID'],
                        'KeyresultType' => $itemChild['KeyresultType'],
                        'Rate' => $itemChild['Rate'],
                        'IndicatorID' => $itemChild['IndicatorID'],
                        'IndicatorNo' => $itemChild['IndicatorNo'],
                        'IndicatorName' => $itemChild['IndicatorName'],
                        'EvaluationMethod' => $item->EvaluationMethod,
                    ];
                }
            }
            if (isset($IndicatorTableItemFeedback[$item->LineIDTemp]) && !empty($IndicatorTableItemFeedback[$item->LineIDTemp])) {
                $LineIDTemp = $item->LineIDTemp;
                foreach ($IndicatorTableItemFeedback[$LineIDTemp] as $itemChild) {

                    $arrInsertFeedback[] = [
                        'TableID' => $id,
                        'TableItemID' => $item->TableItemID,
                        'FeedbackName' => $itemChild['FeedbackName'],
                        'Description' => $itemChild['Description'],
                        'IsBinaryData' => $itemChild['IsBinaryData'],
                        'BinaryDataID' => $itemChild['BinaryDataID'],
                        'IndicatorID' => $itemChild['IndicatorID'],
                        'IndicatorNo' => $itemChild['IndicatorNo'],
                        'IndicatorName' => $itemChild['IndicatorName'],
                        'EvaluationMethod' => $item->EvaluationMethod,
                    ];
                }
            }
        }

        if (!empty($arrInsertKeyresult)) {
            DB::table('task_indicator_table_item_keyresult')->insert($arrInsertKeyresult);
        }
        if (!empty($arrInsertFeedback)) {
            DB::table('task_indicator_table_item_feedback')->insert($arrInsertFeedback);
        }

        $tablePer = new IndicatorTablePer(
            [
                'TableID' => $itable->TableID,
                'EmployeeID' => Auth::user()->EmployeeID,
                'EmployeeName' => Auth::user()->FullName,
                'EvaluatorGroup' => 1,
                'Access' => 1,
                'AccessField' => 'all',
                'Edit' => 1,
                'EditField' => 'all',
                'Delete' => 1
            ]
        );
        $tablePer->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $itable->TableID;
        return json_encode($json);
    }


    public function edit($id)
    {
        $TablePer = IndicatorTablePer::where(['TableID' => $id, 'EmployeeID' => Auth::user()->EmployeeID])->first();
        if(!$TablePer){
            $json['status'] = 2;
            $json['msg'] = 'Bạn không có quyền truy cập công việc này!';
            $json['data'] = '';
            return json_encode($json);
        }else{
            $Uom = Uom::all();
            $indicatorTableItem = IndicatorTableItem::select(DB::raw('TableItemID, TableID, TableNo, EvaluationMethod, IndicatorID, IndicatorNo, IndicatorName,
            TableName, Description, UomID, UomName, GradingType, FrequencyType, ObjectiveRate, ObjectiveIndex, TableItemID as LineIDTemp
            '))->where('TableID', $id)->orderBy('TableItemID', 'asc')->get();
            $data = IndicatorTable::find($id);
            $IndicatorTableItemKeyResult = IndicatorTableItemKeyresult::where(['TableID' => $id])->orderBy('KeyresultID', 'asc')->get();
            $json['data']['IndicatorTableItemKeyResult'] = $IndicatorTableItemKeyResult;

            $IndicatorTableItemFeedback = IndicatorTableItemFeedback::where(['TableID' => $id])->orderBy('FeedbackID', 'asc')->get();
            $json['data']['IndicatorTableItemFeedback'] = $IndicatorTableItemFeedback;
            $BinaryData = BinaryData::all();
            $json['data']['BinaryData'] = $BinaryData;
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;
            $json['data']['IndicatorTableItem'] = $indicatorTableItem;
            $json['data']['Uom'] = $Uom;
            $json['data']['TablePer'] = $TablePer;
            return json_encode($json);
        }
    }


    public function update(Request $request, $id)
    {
        $master = $request->master;
        $detail = $request->detail;
        $master = (object)$master;
        $IndicatorTableItemKeyresult = $request->IndicatorTableItemKeyresult;
        $IndicatorTableItemFeedback = $request->IndicatorTableItemFeedback;
        $validate = [];
        $ctable = IndicatorTable::find($id);
        $ctable->TableNo = $master->TableNo;
        $ctable->TableName = $master->TableName;
        $ctable->IndicatorType = $master->IndicatorType;
        $ctable->Inactive = $master->Inactive;
        $ctable->PeriodType = $master->PeriodType;
        $ctable->FromDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->FromDate)));
        $ctable->ToDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->ToDate)));
        if($ctable->IndicatorType == 1){
            $ctable->CompanyID = $master->CompanyID;
            $ctable->CompanyNo = $master->CompanyNo;
            $ctable->CompanyName = $master->CompanyName;
        }elseif($ctable->IndicatorType == 2){
            $ctable->EmployeeID = $master->EmployeeID;
            $ctable->EmployeeNo = $master->EmployeeNo;
            $ctable->EmployeeName = $master->EmployeeName;
        }
        $ctable->save();
        IndicatorTableItem::where('TableID', $id)->delete();
        $arrInsert = [];
        foreach ($detail as $item) {
            $item = (object)$item;
            if (!empty($item->IndicatorID)) {
                $arrInsert[] = [
                    'TableID' => $ctable->TableID,
                    'TableNo' => $ctable->TableNo,
                    'TableName' => $ctable->TableName,
                    'Description' => $item->Description,
                    'EvaluationMethod' => $item->EvaluationMethod,
                    'IndicatorID' => $item->IndicatorID,
                    'IndicatorNo' => $item->IndicatorNo,
                    'IndicatorName' => $item->IndicatorName,
                    'UomID' => $item->UomID,
                    'UomName' => $item->UomName,
                    'GradingType' => $item->GradingType,
                    'FrequencyType' => $item->FrequencyType,
                    'ObjectiveRate' => $item->ObjectiveRate,
                    'ObjectiveIndex' => $item->ObjectiveIndex,
                    'LineIDTemp' => $item->LineIDTemp
                ];
            }
        }

        if (!empty($arrInsert)) {
            DB::table('task_indicator_table_item')->insert($arrInsert);
        }
        //Add Keyresult

        $indicatorTableItem = IndicatorTableItem::select(['TableItemID', 'LineIDTemp'])->where('TableID', $id)->get();
        $arrInsertKeyresult = [];
        $arrInsertFeedback = [];
        foreach ($indicatorTableItem as $item) {
            if (isset($IndicatorTableItemKeyresult[$item->LineIDTemp]) && !empty($IndicatorTableItemKeyresult[$item->LineIDTemp])) {
                $LineIDTemp = $item->LineIDTemp;
                foreach ($IndicatorTableItemKeyresult[$LineIDTemp] as $itemChild) {
                    $arrInsertKeyresult[] = [
                        'TableID' => $id,
                        'TableItemID' => $item->TableItemID,
                        'KeyresultName' => $itemChild['KeyresultName'],
                        'Description' => $itemChild['Description'],
                        'UomID' => $itemChild['UomID'],
                        'KeyresultType' => $itemChild['KeyresultType'],
                        'Rate' => $itemChild['Rate'],
                        'EvaluationMethod' => $item->EvaluationMethod,
                        'IndicatorID' => $itemChild['IndicatorID'],
                        'IndicatorNo' => $itemChild['IndicatorNo'],
                        'IndicatorName' => $itemChild['IndicatorName'],
                    ];
                }
            }
            if (isset($IndicatorTableItemFeedback[$item->LineIDTemp]) && !empty($IndicatorTableItemFeedback[$item->LineIDTemp])) {
                $LineIDTemp = $item->LineIDTemp;
                foreach ($IndicatorTableItemFeedback[$LineIDTemp] as $itemChild) {
                    $arrInsertFeedback[] = [
                        'TableID' => $id,
                        'TableItemID' => $item->TableItemID,
                        'FeedbackName' => $itemChild['FeedbackName'],
                        'Description' => $itemChild['Description'],
                        'BinaryDataID' => $itemChild['BinaryDataID'],
                        'IsBinaryData' => $itemChild['IsBinaryData'],
                        'EvaluationMethod' => $item->EvaluationMethod,
                        'IndicatorID' => $itemChild['IndicatorID'],
                        'IndicatorNo' => $itemChild['IndicatorNo'],
                        'IndicatorName' => $itemChild['IndicatorName'],
                    ];
                }
            }
        }
        IndicatorTableItemKeyresult::where('TableID', $id)->delete();
        if (!empty($arrInsertKeyresult)) {
            DB::table('task_indicator_table_item_keyresult')->insert($arrInsertKeyresult);
        }
        IndicatorTableItemFeedback::where('TableID', $id)->delete();
        if (!empty($arrInsertFeedback)) {
            DB::table('task_indicator_table_item_feedback')->insert($arrInsertFeedback);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }

    public function view($id)
    {
        $TablePer = IndicatorTablePer::where(['TableID' => $id, 'EmployeeID' => Auth::user()->EmployeeID])->first();
        if(!$TablePer){
            $json['status'] = 2;
            $json['msg'] = 'Bạn không có quyền truy cập công việc này!';
            $json['data'] = '';
            return json_encode($json);
        }else {
            $indicatorTableItem = IndicatorTableItem::select(DB::raw('TableItemID, TableID, TableNo, EvaluationMethod, IndicatorID, IndicatorNo, IndicatorName,
                TableName, Description, UomID, UomName, GradingType, FrequencyType, ObjectiveRate, ObjectiveIndex, TableItemID as LineIDTemp
                '))->where('TableID', $id)->orderBy('TableItemID', 'asc')->get();
            $data = IndicatorTable::find($id);
            $Uom = Uom::all();
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
            $IndicatorTableItemKeyResult = IndicatorTableItemKeyresult::where(['TableID' => $id])->orderBy('TableItemID', 'asc')->get();
            $json['data']['IndicatorTableItemKeyResult'] = $IndicatorTableItemKeyResult;
            $IndicatorTableItemFeedback = IndicatorTableItemFeedback::where(['TableID' => $id])->orderBy('TableItemID', 'asc')->get();
            $json['data']['IndicatorTableItemFeedback'] = $IndicatorTableItemFeedback;
            $BinaryData = BinaryData::all();
            $json['data']['BinaryData'] = $BinaryData;
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;
            $json['data']['IndicatorTableItem'] = $indicatorTableItem;
            $json['data']['Uom'] = $Uom;
            $json['data']['GradingTypeName'] = $GradingType;
            $json['data']['FrequencyTypeName'] = $FrequencyType;
            $json['data']['TablePer'] = $TablePer;
            return json_encode($json);
        }
    }

    public function delete($id)
    {
        $data = IndicatorTable::find($id);
        IndicatorTableItemKeyresult::where('TableID', $id)->delete();
        IndicatorTableItemFeedback::where('TableID', $id)->delete();
        IndicatorTableItem::where('TableID', $id)->delete();
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
        IndicatorTableItem::whereIn('TableID', $IDs)->delete();
        IndicatorTableItemFeedback::whereIn('TableID', $IDs)->delete();
        IndicatorTableItemKeyresult::whereIn('TableID', $IDs)->delete();
        IndicatorTable::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }



    public function loadTemp($id)
    {
        $indicatorTempItem = IndicatorTempItem::select(DB::raw('TransItemID, EvaluationMethod, TemplateID, IndicatorID, IndicatorNo,
            IndicatorName, Description, ParentID, Level, Detail, UomID, UomName, GradingType, FrequencyType, Rate, Prefix, Suffix, NumberValue, TransItemID as LineIDTemp
            '))->where('TemplateID', $id)->orderBy('TransItemID', 'asc')->get();
        $data = IndicatorTemp::find($id);
        $IndicatorTempItemKeyResult = IndicatorTempItemKeyresult::where(['TemplateID' => $id])->orderBy('KeyresultID', 'asc')->get();
        $json['data']['IndicatorTempItemKeyResult'] = $IndicatorTempItemKeyResult;
        $IndicatorTempItemFeedback = IndicatorTempItemFeedback::where(['TemplateID' => $id])->orderBy('FeedbackID', 'asc')->get();
        $json['data']['IndicatorTempItemFeedback'] = $IndicatorTempItemFeedback;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['IndicatorTempItem'] = $indicatorTempItem;
        return json_encode($json);
    }

    public function getTablePer(Request $request){
        $TableID = $request->post('TableID');
        $IndicatorTablePer = IndicatorTablePer::where(['TableID' => $TableID])->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $IndicatorTablePer;
        return json_encode($json);
    }

    public function updateTablePer(Request $request){
        $TableID = $request->post('TableID');
        $Detail = $request->post('Detail');
        $arrInsert = [];
        foreach ($Detail as $item) {
            $arrInsert[] = [
                'TableID' => $TableID,
                'EmployeeID' => $item['EmployeeID'],
                'EmployeeName' => $item['EmployeeName'],
                'EvaluatorGroup' => $item['EvaluatorGroup'],
                'Access' => $item['Access'],
                'AccessField' => $item['AccessField'],
                'Edit' => $item['Edit'],
                'EditField' => $item['EditField'],
                'Delete' => $item['Delete']
            ];
        }
        IndicatorTablePer::where('TableID', $TableID)->delete();
        if (!empty($arrInsert)) {
            DB::table('task_indicator_table_per')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = 1;
        return json_encode($json);
    }
}
