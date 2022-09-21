<?php

namespace Module\Task\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use IjCore\IjDate;
use IjSmartBooks\IjCore\IjFunction\Date;
use IjSmartBooks\IjCore\IjFunction\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\Uom;
use Module\SysAdmin\Models\SysSetup;
use Module\Task\Models\BinaryData;
use Module\Task\Models\Evaluation1job;
use Module\Task\Models\EvaluationTrans;
use Module\Task\Models\EvaluationTransItem;
use Module\Task\Models\EvaluationTransItemFeedback;
use Module\Task\Models\EvaluationTransItemKeyresult;
use Module\Task\Models\EvaluationTransPer;
use Module\Task\Models\EvaluationTransSubItem;
use Module\Task\Models\Indicator;
use Module\Task\Models\IndicatorTable;
use Module\Task\Models\IndicatorTableEmployeeMap;
use Module\Task\Models\IndicatorTableItem;
use Module\Task\Models\IndicatorTableItemFeedback;
use Module\Task\Models\IndicatorTableItemKeyresult;
use Module\Task\Models\IndicatorTableMap;
use Module\Task\Models\ScaleRate;
use Module\Task\Models\ScaleRateItem;
use Module\Task\Models\Task;

class EvaluationTransController extends Controller
{

    public function list(Request $request)
    {
        $data = EvaluationTrans::search($request);
        $json['status'] = 1;
        $json['data'] = $data;
        $json['msg'] = 'Thành công!';
        return json_encode($json);
    }

    public function export(Request $request)
    {
        return Excel::download(new ModelExport('task_evaluation_trans', ['select' => ['TransID', 'TransName'], 'conditions' => []]), 'phieudgcv.xlsx');
    }

    public function create(Request $request)
    {
        $GradingMethod = [
            1 => 'Đơn vị đo',
            2 => 'Bảng điểm',
            3 => 'Nhị phân',
            4 => 'Tỷ lệ %'
        ];

        if(old('IndicatorNo')){
            $auto = old('IndicatorNo');
        }else{
            $auto = AutoNo::genNewNumberNoTable('task_evaluation_trans', 'TransID', 'TransNo');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['GradingMethod'] = $GradingMethod;
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public static function getIndicatorTable($PeriodType, $Fromdate, $ToDate, $EmployeeID){
        $IndicatorTable = IndicatorTable::where('PeriodType', '=', $PeriodType)
                ->where('FromDate', '=', $Fromdate)
                ->where('EmployeeID', '=', $EmployeeID)
                ->where('ToDate', '=', $ToDate)
                ->first();
        return $IndicatorTable;
    }
    public function store(Request $request)
    {
        $master = (object)$request->master;
        $detail = $request->detail;
        $SubDetail = $request->SubDetail;
        $EvaluationTransItemKeyresult = $request->EvaluationTransItemKeyresult;
        $EvaluationTransItemFeedback = $request->EvaluationTransItemFeedback;

        $EmployeeAuth = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        if(isset($master->TransID)){
            EvaluationTrans::where(['TransID' => $master->TransID])->delete();
            EvaluationTransItem::where(['TransID' => $master->TransID])->delete();
            EvaluationTransSubItem::where(['TransID' => $master->TransID])->delete();
            EvaluationTransItemFeedback::where(['TransID' => $master->TransID])->delete();
            EvaluationTransItemKeyresult::where(['TransID' => $master->TransID])->delete();
        }

        $evaluationTrans = new EvaluationTrans([
            'TransName' => $master->TransName,
            'TransNo' => (!empty($master->TransNo)) ? $master->TransNo : '',
            'TransType' => $master->TransType,
            'Inactive' => ($master->Inactive) ? $master->Inactive : 0,
            'PeriodType' => $master->PeriodType,
            'FromDate' => IjDate::ConvertClientDateToServerDate($master->FromDate),
            'ToDate' => IjDate::ConvertClientDateToServerDate($master->ToDate),
            'EvaluatorID' => $EmployeeAuth->EmployeeID,
            'EvaluatorNo' => $EmployeeAuth->EmployeeNo,
            'EvaluatorName' => $EmployeeAuth->EmployeeName,
        ]);
        if($master->TransType == 1){
            $evaluationTrans->CompanyID = $master->CompanyID;
            $evaluationTrans->CompanyNo = $master->CompanyNo;
            $evaluationTrans->CompanyName = $master->CompanyName;
        }elseif($master->TransType == 2){
            $evaluationTrans->EmployeeID = $master->EmployeeID;
            $evaluationTrans->EmployeeNo = $master->EmployeeNo;
            $evaluationTrans->EmployeeName = $master->EmployeeName;
        }
        $evaluationTrans->save();
        $id = $evaluationTrans->TransID;
        $arrInsert = []; $totalActualRate = 0;

        foreach ($detail as $item) {
            $item = (object)$item;
            if ($item->IndicatorID) {
                $totalActualRate += (float)$item->ActualRate;
                $arrInsert[] = [
                    'TransID' => $id,
                    'Description' => $item->Description,
                    'UomID' => $item->UomID,
                    'UomName' => $item->UomName,
                    'GradingType' => $item->GradingType,
                    'GradingMethod' => $item->GradingMethod,
                    'EvaluationMethod' => $item->EvaluationMethod,
                    'ScaleRateID' => $item->ScaleRateID,
                    'ObjectiveRate' => $item->ObjectiveRate,
                    'ObjectiveIndex' => $item->ObjectiveIndex,
                    'ActualIndex' => $item->ActualIndex,
                    'ActualRate' => $item->ActualRate,
                    'IndicatorID' => $item->IndicatorID,
                    'IndicatorNo' => $item->IndicatorNo,
                    'IndicatorName' => $item->IndicatorName,
                    'LineIDTemp' => $item->LineIDTemp
                ];
            }
        }

        if (!empty($arrInsert)) {
            DB::table('task_evaluation_trans_item')->insert($arrInsert);
        }


        $EvaluationTransItem = EvaluationTransItem::select(['TransItemID', 'LineIDTemp', 'EvaluationMethod', 'IndicatorID'])->where('TransID', $id)->get();

        $arrInsertKeyresult = [];
        $arrInsertFeedback = [];
        $arrInsertSubDetail = [];
        foreach ($EvaluationTransItem as $item) {

            foreach ($SubDetail as $keyChild => $itemChild) {
                if($item->IndicatorID .'_'. $itemChild['EvaluatorID'] == $keyChild){
                    $arrInsertSubDetail[] = [
                        'TransID' => $id,
                        'TransItemID' => $item->TransItemID,
                        'ActualIndex' => $itemChild['ActualIndex'],
                        'EvaluatorID' => $itemChild['EvaluatorID'],
                        'EvaluatorName' => $itemChild['EvaluatorName'],
                    ];
                }
            }

            if (isset($EvaluationTransItemKeyresult[$item->LineIDTemp]) && !empty($EvaluationTransItemKeyresult[$item->LineIDTemp])) {

//                $json['status'] = 1;
//                $json['msg'] = 'Thành công!';
//                $json['data'] = $itemChild['KeyresultName'];
//                $json['data'] = $itemChild['Description'];
//                $json['data'] = $itemChild['UomID'];
//                $json['data'] = $itemChild['KeyresultType'];
//                $json['data'] = $itemChild['Rate'];
//                $json['data'] = $itemChild['ObjectiveIndex'];
//                $json['data'] = $itemChild['ActualIndex'];
//                $json['data'] = $itemChild['ActualRate'];
//                $json['data'] = $itemChild['IndicatorID'];
//                $json['data'] = $itemChild['IndicatorNo'];
//                $json['data'] = $itemChild['IndicatorName'];
//                $json['data'] = $itemChild['GradingMethod'];
//                $json['data'] = $itemChild['ScaleRateID'];
//                $json['data'] = $itemChild['BinaryDataID'];
//                return json_encode($json);
                $LineIDTemp = $item->LineIDTemp;
                foreach ($EvaluationTransItemKeyresult[$LineIDTemp] as $itemChild) {
                    $arrInsertKeyresult[] = [
                        'TransID' => $id,
                        'TransItemID' => $item->TransItemID,
                        'KeyresultName' => $itemChild['KeyresultName'],
                        'UomID' => $itemChild['UomID'],
                        'KeyresultType' => $itemChild['KeyresultType'],
                        'Rate' => $itemChild['Rate'],
                        'ObjectiveIndex' => $itemChild['ObjectiveIndex'],
                        'ActualIndex' => $itemChild['ActualIndex'],
                        'ActualRate' => $itemChild['ActualRate'],
                        'IndicatorID' => $itemChild['IndicatorID'],
                        'IndicatorNo' => $itemChild['IndicatorNo'],
                        'IndicatorName' => $itemChild['IndicatorName'],
                        'GradingMethod' => $itemChild['GradingMethod'],
                        'ScaleRateID' => $itemChild['ScaleRateID'],
                        'BinaryDataID' => $itemChild['BinaryDataID']
                    ];
                }
            }



            if (isset($EvaluationTransItemFeedback[$item->LineIDTemp]) && !empty($EvaluationTransItemFeedback[$item->LineIDTemp])) {
                $LineIDTemp = $item->LineIDTemp;
                foreach ($EvaluationTransItemFeedback[$LineIDTemp] as $itemChild) {
                    $arrInsertFeedback[] = [
                        'TransID' => $id,
                        'TransItemID' => $item->TransItemID,
                        'FeedbackName' => $itemChild['FeedbackName'],
                        'FeedbackValue' => $itemChild['FeedbackValue'],
                        'FeedbackContent' => $itemChild['FeedbackContent'],
                        'Description' => $itemChild['Description'],
                        'IsBinaryData' => ($itemChild['IsBinaryData']) ? 1 : 0,
                        'BinaryDataID' => $itemChild['BinaryDataID'],
                        'FeedbackDate' => $itemChild['FeedbackDate'],
                        'UserID' => $itemChild['UserID'],
                        'UserName' => $itemChild['UserName'],
                        'IndicatorID' => $itemChild['IndicatorID'],
                        'IndicatorNo' => $itemChild['IndicatorNo'],
                        'IndicatorName' => $itemChild['IndicatorName'],
                        'ScaleRateID' => $itemChild['ScaleRateID'],
                        'GradingMethod' => $itemChild['GradingMethod'],
                    ];
                }
            }
        }
        if (!empty($arrInsertKeyresult)) {
            DB::table('task_evaluation_trans_item_keyresult')->insert($arrInsertKeyresult);
        }
        if (!empty($arrInsertFeedback)) {
            DB::table('task_evaluation_trans_item_feedback')->insert($arrInsertFeedback);
        }
        if (!empty($arrInsertSubDetail)) {
            DB::table('task_evaluation_trans_subitem')->insert($arrInsertSubDetail);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $evaluationTrans->TransID;
        return json_encode($json);
    }


    public function edit($id)
    {
        $EvaluationTransItem = EvaluationTransItem::where('TransID', $id)->orderBy('TransItemID', 'asc')->get();
        $data = EvaluationTrans::find($id);

        $EvaluationTransItemKeyResult = EvaluationTransItemKeyresult::where(['TransID' => $id])->orderBy('KeyresultID', 'asc')->get();
        $EvaluationTransItemFeedback = EvaluationTransItemFeedback::where(['TransID' => $id])->orderBy('FeedbackID', 'asc')->get();


        $GradingMethod = [
            1 => 'Đơn vị đo',
            2 => 'Bảng điểm',
            3 => 'Nhị phân',
            4 => 'Tỷ lệ %'
        ];
        $TaskEvaluationEmployee = DB::select(DB::raw('SELECT C.IndicatorID, C.ActualIndex ActualIndexC, S.EvaluatorID, S.EvaluatorName, S.ActualIndex
            FROM task_evaluation_trans P
            LEFT JOIN task_evaluation_trans_item C
            ON P.TransID = C.TransID
            LEFT JOIN task_evaluation_trans_subitem S
            ON C.TransItemID = S.TransItemID
            WHERE P.TransID = '.$id.'
            ORDER BY C.IndicatorID
        '));
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['Uom'] = Uom::all();
        $json['data']['TaskEvaluationEmployee'] = $TaskEvaluationEmployee;
        $json['data']['EvaluationTransItemFeedback'] = $EvaluationTransItemFeedback;
        $json['data']['EvaluationTransItemKeyresult'] = $EvaluationTransItemKeyResult;
        $json['data']['EvaluationTransItem'] = $EvaluationTransItem;
        $json['data']['GradingMethod'] = $GradingMethod;
        return json_encode($json);
    }


    public function update(Request $request, $id)
    {
        $master = $request->master;
        $detail = $request->detail;
        $master = (object)$master;
        $EvaluationTransItemKeyresult = $request->EvaluationTransItemKeyresult;
        $EvaluationTransItemFeedback = $request->EvaluationTransItemFeedback;
        $validate = [];

        $EmployeeAuth = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        $ctrans = EvaluationTrans::find($id);
        $ctrans->TransNo = $master->TransNo;
        $ctrans->TransName = $master->TransName;
        $ctrans->TransType = $master->TransType;
        $ctrans->Inactive = $master->Inactive;
        $ctrans->PeriodType = $master->PeriodType;
        $ctrans->FromDate = IjDate::ConvertClientDateToServerDate($master->FromDate);
        $ctrans->ToDate = IjDate::ConvertClientDateToServerDate($master->ToDate);
        $ctrans->EvaluatorID = $EmployeeAuth->EmployeeID;
        $ctrans->EvaluatorName = $EmployeeAuth->EmployeeName;
        $ctrans->EvaluatorNo = $EmployeeAuth->EmployeeNo;

        if($ctrans->TransType == 1){
            $ctrans->CompanyID = $master->CompanyID;
            $ctrans->CompanyNo = $master->CompanyNo;
            $ctrans->CompanyName = $master->CompanyName;
        }elseif($ctrans->TransType == 2){
            $ctrans->EmployeeID = $master->EmployeeID;
            $ctrans->EmployeeNo = $master->EmployeeNo;
            $ctrans->EmployeeName = $master->EmployeeName;
        }
        $ctrans->save();
        EvaluationTransItem::where('TransID', $id)->delete();
        $arrInsert = [];
        $totalActualRate = 0;
        foreach ($detail as $item) {
            $item = (object)$item;
            if (!empty($item->IndicatorID)) {
                $totalActualRate += (float)$item->ActualRate;
                $arrInsert[] = [
                    'TransID' => $ctrans->TransID,
                    'Description' => $item->Description,
                    'UomID' => $item->UomID,
                    'UomName' => $item->UomName,
                    'GradingType' => $item->GradingType,
                    'GradingMethod' => $item->GradingMethod,
                    'EvaluationMethod' => $item->EvaluationMethod,
                    'ScaleRateID' => $item->ScaleRateID,
                    'ObjectiveRate' => $item->ObjectiveRate,
                    'ObjectiveIndex' => $item->ObjectiveIndex,
                    'ActualIndex' => $item->ActualIndex,
                    'ActualRate' => $item->ActualRate,
                    'IndicatorID' => $item->IndicatorID,
                    'IndicatorNo' => $item->IndicatorNo,
                    'IndicatorName' => $item->IndicatorName,
                    'LineIDTemp' => $item->LineIDTemp
                ];
            }
        }
        //Update master
        //EvaluationLevel
        if($totalActualRate){
            $ScaleRateQuery = ScaleRate::query();
            $ScaleRateQuery->select('task_scale_rate.ScaleRateID', 'task_scale_rate.ScaleRateName', 'task_scale_rate_item.levelchar', 'task_scale_rate_item.leveltext')
                ->join('task_scale_rate_item', 'task_scale_rate.ScaleRateID','=', 'task_scale_rate_item.ScaleRateID')
                ->where('task_scale_rate_item.Frompoint100', '<=', $totalActualRate)
                ->where('task_scale_rate_item.ToPoint100', '>=',$totalActualRate)
                ->limit(1);
            $ScaleRate = $ScaleRateQuery->first();
            $EvaluationLevel = $ScaleRate->levelchar . '-' . $ScaleRate->leveltext;
            $ctrans->TotalActualRate = $totalActualRate;
            $ctrans->EvaluationLevel = $EvaluationLevel;
            $ctrans->save();
        }
        if (!empty($arrInsert)) {
            DB::table('task_evaluation_trans_item')->insert($arrInsert);
        }


        $EvaluationTransItem = EvaluationTransItem::select(['TransItemID', 'LineIDTemp'])->where('TransID', $id)->get();
        $arrInsertKeyresult = [];
        $arrInsertFeedback = [];
        foreach ($EvaluationTransItem as $item) {
            if (count($EvaluationTransItemKeyresult) && isset($EvaluationTransItemKeyresult[$item->LineIDTemp]) && !empty($EvaluationTransItemKeyresult[$item->LineIDTemp])) {
                $LineIDTemp = $item->LineIDTemp;
                foreach ($EvaluationTransItemKeyresult[$LineIDTemp] as $itemChild) {
                    $arrInsertKeyresult[] = [
                        'TransID' => $id,
                        'TransItemID' => $item->TransItemID,
                        'KeyresultName' => $itemChild['KeyresultName'],
                        'Description' => $itemChild['Description'],
                        'UomID' => $itemChild['UomID'],
                        'KeyresultType' => $itemChild['KeyresultType'],
                        'Rate' => $itemChild['Rate'],
                        'ObjectiveIndex' => $itemChild['ObjectiveIndex'],
                        'ActualIndex' => $itemChild['ActualIndex'],
                        'ActualRate' => $itemChild['ActualRate'],
                        'IndicatorID' => $itemChild['IndicatorID'],
                        'IndicatorNo' => $itemChild['IndicatorNo'],
                        'IndicatorName' => $itemChild['IndicatorName'],
                        'GradingMethod' => $itemChild['GradingMethod'],
                        'ScaleRateID' => $itemChild['ScaleRateID'],
                        'EvaluationMethod' => $item['EvaluationMethod'],
                        'BinaryDataID' => $itemChild['BinaryDataID']
                    ];
                }
            }
            if (count($EvaluationTransItemFeedback) && isset($EvaluationTransItemFeedback[$item->LineIDTemp]) && !empty($EvaluationTransItemFeedback[$item->LineIDTemp])) {
                $LineIDTemp = $item->LineIDTemp;
                foreach ($EvaluationTransItemFeedback[$LineIDTemp] as $itemChild) {
                    $arrInsertFeedback[] = [
                        'TransID' => $id,
                        'TransItemID' => $item->TransItemID,
                        'FeedbackName' => $itemChild['FeedbackName'],
                        'FeedbackValue' => $itemChild['FeedbackValue'],
                        'FeedbackContent' => $itemChild['FeedbackContent'],
                        'Description' => $itemChild['Description'],
                        'BinaryDataID' => $itemChild['BinaryDataID'],
                        'IsBinaryData' => ($itemChild['IsBinaryData']) ? 1 : 0,
                        'FeedbackDate' => IjDate::ConvertClientDateToServerDate($itemChild['FeedbackDate']),
                        'UserID' => $itemChild['UserID'],
                        'UserName' => $itemChild['UserName'],
                        'IndicatorID' => $itemChild['IndicatorID'],
                        'IndicatorNo' => $itemChild['IndicatorNo'],
                        'IndicatorName' => $itemChild['IndicatorName'],
                        'ScaleRateID' => $itemChild['ScaleRateID'],
                        'GradingMethod' => $itemChild['GradingMethod'],
                        'EvaluationMethod' => $item['EvaluationMethod']
                    ];
                }
            }
        }
        EvaluationTransItemKeyresult::where('TransID', $id)->delete();
        if (!empty($arrInsertKeyresult)) {
            DB::table('task_evaluation_trans_item_keyresult')->insert($arrInsertKeyresult);
        }
        EvaluationTransItemFeedback::where('TransID', $id)->delete();
        if (!empty($arrInsertFeedback)) {
            DB::table('task_evaluation_trans_item_feedback')->insert($arrInsertFeedback);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }

    public function view($id)
    {
        $EvaluationTransItem = EvaluationTransItem::where('TransID', $id)->orderBy('TransItemID', 'asc')->get();
        $data = EvaluationTrans::find($id);

        $EvaluationTransItemKeyResult = EvaluationTransItemKeyresult::where(['TransID' => $id])->orderBy('KeyresultID', 'asc')->get();
        $EvaluationTransItemFeedback = EvaluationTransItemFeedback::where(['TransID' => $id])->orderBy('FeedbackID', 'asc')->get();


        $GradingMethod = [
            1 => 'Đơn vị đo',
            2 => 'Bảng điểm',
            3 => 'Nhị phân',
            4 => 'Tỷ lệ %'
        ];
        $TaskEvaluationEmployee = DB::select(DB::raw('SELECT C.IndicatorID, C.ActualIndex ActualIndexC, S.EvaluatorID, S.EvaluatorName, S.ActualIndex
            FROM task_evaluation_trans P
            LEFT JOIN task_evaluation_trans_item C
            ON P.TransID = C.TransID
            LEFT JOIN task_evaluation_trans_subitem S
            ON C.TransItemID = S.TransItemID
            WHERE P.TransID = '.$id.'
            ORDER BY C.IndicatorID
        '));
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['Uom'] = Uom::all();
        $json['data']['TaskEvaluationEmployee'] = $TaskEvaluationEmployee;
        $json['data']['EvaluationTransItemFeedback'] = $EvaluationTransItemFeedback;
        $json['data']['EvaluationTransItemKeyresult'] = $EvaluationTransItemKeyResult;
        $json['data']['EvaluationTransItem'] = $EvaluationTransItem;
        $json['data']['GradingMethod'] = $GradingMethod;
        return json_encode($json);
    }

    public function delete($id)
    {
        $data = EvaluationTrans::find($id);
        if($data->EvaluationMethod == 2){
            EvaluationTransItemKeyresult::where('TransID', $id)->delete();
        }elseif($data->EvaluationMethod == 6){
            EvaluationTransItemFeedback::where('TransID', $id)->delete();
        }
        EvaluationTransItem::where('TransID', $id)->delete();
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
        EvaluationTransItem::whereIn('TransID', $IDs)->delete();
        EvaluationTransItemFeedback::whereIn('TransID', $IDs)->delete();
        EvaluationTransItemKeyresult::whereIn('TransID', $IDs)->delete();
        EvaluationTrans::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getTemp(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];


        $indicatorType = $request->post('IndicatorType');
        $periodType = $request->post('PeriodType');
        $fromDate = ($request->post('FromDate')) ? IjDate::StringToDate($request->post('FromDate')) : '';
        $toDate = ($request->post('ToDate')) ? IjDate::StringToDate($request->post('ToDate')) : '';

        if ($indicatorType && $periodType && $fromDate && $toDate) {
            $indicatorTableQuery = IndicatorTable::query();
            $indicatorTableQuery->where('IndicatorType', '=', $indicatorType);
            if ($indicatorType == 1) {
                $indicatorTableQuery->where('CompanyID', '=', $request->post('CompanyID'));
            }
            if ($indicatorType == 2) {
                $indicatorTableQuery->where('EmployeeID', '=', $request->post('EmployeeID'));
            }

            $indicatorTableQuery->where('PeriodType', '=', $periodType);
            $indicatorTableQuery->where('FromDate', '=', $fromDate);
            $indicatorTableQuery->where('ToDate', '=', $toDate);
            $indicatorTable = $indicatorTableQuery->first();

            if ($indicatorTable) {
                $json['status'] = 1;
                $json['msg'] = 'Thành công';
                $json['data'] = $indicatorTable;
                $json['toDate'] = $toDate;
                $json['fromDate'] = $fromDate;
            }
        }

        return json_encode($json);
    }

    public function loadTemp(Request $request)
    {
        $TableID = $request->post('TableID');
        $TransType = $request->post('TransType');
        $CompanyID = $request->post('CompanyID');
        $EmployeeID = $request->post('EmployeeID');
        $PeriodType = $request->post('PeriodType');
        $FromDate = $request->post('FromDate');
        $ToDate = $request->post('ToDate');

        $IndicatorTableItem = IndicatorTableItem::where('TableID', $TableID)->orderBy('TableItemID', 'asc')->get();
        $data = IndicatorTable::find($TableID);
        $TaskEvaluationEmployee = DB::select(DB::raw('SELECT C.IndicatorID, C.ActualIndex ActualIndexC, S.EvaluatorID, S.EvaluatorName, S.ActualIndex
            FROM task_evaluation_trans P
            LEFT JOIN task_evaluation_trans_item C
            ON P.TransID = C.TransID
            LEFT JOIN task_evaluation_trans_subitem S
            ON C.TransItemID = S.TransItemID
            WHERE P.EmployeeID = '.$EmployeeID.' AND P.PeriodType = '.$PeriodType.'
            AND P.FromDate = "'.$FromDate.'" AND P.ToDate = "'.$ToDate.'"
            ORDER BY C.IndicatorID
        '));
        $json['data']['TaskEvaluationEmployee'] = $TaskEvaluationEmployee;
        $IndicatorTableItemKeyResult = IndicatorTableItemKeyresult::where(['TableID' => $TableID])->orderBy('KeyresultID', 'asc')->get();
        $json['data']['IndicatorTableItemKeyResult'] = $IndicatorTableItemKeyResult;
        $IndicatorTableItemFeedback = IndicatorTableItemFeedback::where(['TableID' => $TableID])->orderBy('FeedbackID', 'asc')->get();
        $json['data']['IndicatorTableItemFeedback'] = $IndicatorTableItemFeedback;


        $indicatorIDs = [];
        foreach ($IndicatorTableItem as $item) {
            $indicatorIDs[] = $item->IndicatorID;
        }


        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['IndicatorTableItem'] = $IndicatorTableItem;
        return json_encode($json);
    }

    public function loadIndicator(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => [],
        ];
        $TableID = $request->post('TableID');
        $IndicatorID = $request->post('IndicatorID');
        $TransType = $request->post('TransType');
        $EmployeeID = $request->post('EmployeeID');
        $CompanyID = $request->post('CompanyID');
        $PeriodType = $request->post('PeriodType');
        $FromDate = $request->post('FromDate');
        $ToDate = $request->post('ToDate');

        $data = Indicator::find($IndicatorID);
        $json['data']['data'] = $data;

        if ($TableID) {
            $IndicatorTableItem = IndicatorTableItem::where([
                'TableID' => $TableID,
                'IndicatorID' => $IndicatorID
            ])->first();
            $json['data']['IndicatorTableItem'] = $IndicatorTableItem;

            $IndicatorTableItemKeyResult = IndicatorTableItemKeyresult::where([
                'TableID' => $TableID,
                'IndicatorID' => $IndicatorID
            ])->first();
            $json['data']['IndicatorTableItemKeyResult'] = $IndicatorTableItemKeyResult;

            $IndicatorTableItemFeedback = IndicatorTableItemFeedback::where([
                'TableID' => $TableID,
                'IndicatorID' => $IndicatorID
            ])->first();
            $json['data']['IndicatorTableItemFeedback'] = $IndicatorTableItemFeedback;
        }

        // ActualIndex
        $evaluation1JobQuery = Evaluation1job::query();
        $evaluation1JobQuery->select('task_evaluation_1job_trans.*', 'task.TaskName', 'task.TaskNo', 'task.UomID', 'uom.UomName',
            'task.EstimatedQuantity', 'task.TotalActualQuantity', 'task.TotalActualConvertQuantity');
        $evaluation1JobQuery->join('task', 'task_evaluation_1job_trans.TaskID', '=', 'task.TaskID')
            ->leftJoin('uom', 'task.UomID', '=', 'uom.UomID')
            ->where('task_evaluation_1job_trans.EmployeeID', '=', $EmployeeID)
            ->where('task_evaluation_1job_trans.TransDate', '<=', IjDate::ConvertClientDateToServerDate($ToDate))
            ->where('task_evaluation_1job_trans.TransDate', '>=', IjDate::ConvertClientDateToServerDate($FromDate))
            ->where('task_evaluation_1job_trans.IndicatorID', $IndicatorID);
        $evaluation1Job = $evaluation1JobQuery->get();
        $json['data']['Evaluation1Job'] = $evaluation1Job;

        return json_encode($json);
    }

    public function getTransPer(Request $request){
        $TransID = $request->post('TransID');
        $EvaluationTransPer = EvaluationTransPer::where(['TransID' => $TransID])->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $EvaluationTransPer;
        return json_encode($json);
    }

    public function getList(Request $request){
        $data = null;
        $query = IndicatorTable::query();
        $where = [];
        if($request->post('search')){
            $query->where('TableName', 'like', '%'.$request->search.'%');
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

    public function getEvaluationTrans(Request $request){
        $EmployeeID = $request->post('EmployeeID');
        $FromDate = $request->post('FromDate');
        $ToDate = $request->post('ToDate');
        $FrequencyType = $request->post('FrequencyType');
        $AllowWhenNotEvaluation = $request->post('AllowWhenNotEvaluation');
        $TaskEvaluation1Job = null;
        $TransDateArr = explode('/', $TransDate);

        $FromDate = IjDate::ConvertClientDateToServerDate($FromDate);
        $ToDate = IjDate::ConvertClientDateToServerDate($ToDate);
        if($TaskID && $EmployeeID && $TransDate){
            $TransDate = IjDate::ConvertClientDateToServerDate($TransDate);
            $TaskEvaluation1Job = DB::select(DB::raw('SELECT P.TransID, P.TransDate, P.TaskID,
            P.EmployeeID, P.EmployeeName, P.LevelInt LevelIntP, P.LevelResult LevelResultP,
            C.*, true showChild, true showChildTransDate
            FROM task_evaluation_1job_trans P
            LEFT JOIN task_evaluation_1job_item C
            ON P.TransID = C.TransID
            WHERE P.TaskID = '.$TaskID.' AND P.EmployeeID = '.$EmployeeID.' AND P.TransDate = "'.$TransDate.'" AND P.FrequencyType = '.$FrequencyType.'
            ORDER BY P.EmployeeID, P.TransDate, C.IndicatorID'));
        }


        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['TaskEvaluation1Job'] = $TaskEvaluation1Job;
        return json_encode($json);
    }

    public function calEvaluationFromChildPeriod($EmployeeID, $FrequencyType, $Fromdate, $Todate){
        if($FrequencyType > 6){
            return $TaskID .'==='. $EmployeeID .'==='. $FrequencyType .'==='. $Fromdate .'==='. $Todate;
        }
        $TaskEvaluation1Job = DB::select(DB::raw('SELECT C.IndicatorID, C.IndicatorName, P.EmployeeID, P.EmployeeName,
                if(C.IndicatorCalMethod = 1, AVG(C.LevelInt100), if(C.IndicatorCalMethod = 2, AVG(C.LevelInt100), "null")) LevelInt100Temp,
                C.EvaluatorID, C.EvaluatorName, true showChild, true showChildTransDate, C.ScaleRateID
                FROM task_evaluation_1job_trans P
                LEFT JOIN task_evaluation_1job_item C
                ON P.TransID = C.TransID
                WHERE P.EmployeeID = '.$EmployeeID.'
                AND P.TransDate >= "'.$Fromdate.'"  AND P.TransDate <= "'.$Todate.'"
                AND P.FrequencyType = '.$FrequencyType.' AND C.EvaluatorID = '.Auth::user()->EmployeeID.'
                GROUP BY C.IndicatorID, P.EmployeeID, C.EvaluatorID
                ORDER BY P.EmployeeID, P.TransDate, C.IndicatorID'));
        $TaskEvaluation1JobTemp = DB::select(DB::raw('
                SELECT  C.IndicatorID, P.EmployeeID, C.LevelInt100, C.EvaluatorID, P.TransDate
                FROM task_evaluation_1job_trans P
                LEFT JOIN task_evaluation_1job_item C
                ON P.TransID = C.TransID
                WHERE P.EmployeeID = '.$EmployeeID.'
                AND P.TransDate >= "'.$Fromdate.'"  AND P.TransDate <= "'.$Todate.'"
                AND P.FrequencyType = '.$FrequencyType.' AND C.IndicatorCalMethod = 3
                AND C.EvaluatorID = '.Auth::user()->EmployeeID.'
                GROUP BY C.IndicatorID, P.EmployeeID, C.EvaluatorID'));
        $TaskEvaluation1JobTempArr = [];
        foreach ($TaskEvaluation1JobTemp as $key=>$value){
            if(isset($TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID])){
                if($TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['TransDate'] < $value->TransDate){
                    $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['TransDate'] = $value->TransDate;
                    $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['LevelInt100'] = $value->LevelInt100;
                }
            }else{
                $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['TransDate'] = $value->TransDate;
                $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['LevelInt100'] = $value->LevelInt100;
            }
        }

        foreach ($TaskEvaluation1Job as $key=>$value){
            if($value->LevelInt100Temp == "null"){
                $TaskEvaluation1Job[$key]->LevelInt100 = $TaskEvaluation1JobTempArr[$value->IndicatorID.'_'.$value->EmployeeID.'_'.$value->EvaluatorID]['LevelInt100'];
            }else{
                $TaskEvaluation1Job[$key]->LevelInt100 = $TaskEvaluation1Job[$key]->LevelInt100Temp;
            }
        }
        if($TaskEvaluation1Job){
            return $TaskEvaluation1Job;
        }else{
            if($FrequencyType == 4){
                $FrequencyType = 6;
            }else{
                $FrequencyType++;
            }
            return $this->calFromChildPeriod($TaskID, $EmployeeID, $FrequencyType, $Fromdate, $Todate);
        }
    }



    public function getTask(Request $request){
        $EmployeeID = $request->post('EmployeeID');
        $FromDate = $request->post('FromDate');
        $ToDate = $request->post('ToDate');
        $FrequencyType = $request->post('FrequencyType');
        $IndicatorID = $request->post('IndicatorID');

        $FromDate = IjDate::ConvertClientDateToServerDate($FromDate);
        $ToDate = IjDate::ConvertClientDateToServerDate($ToDate);
        $data = null;
        if($FromDate && $ToDate && $EmployeeID && $IndicatorID && $FrequencyType){
            $data = DB::select(DB::raw("SELECT T.TaskName, T.TaskNo, T.UomID, T.TaskID, T.EstimatedQuantity
            FROM task_evaluation_1job_trans ET
            LEFT JOIN task_evaluation_1job_trans_item EI
            ON ET.TransID = EI.TransID
            LEFT JOIN task T
            ON ET.TaskID = T.TaskID
            WHERE ET.TransDate >= '".$FromDate."' AND ET.TransDate <= '".$ToDate."'/*  AND ET.FrequencyType = '".$FrequencyType."'*/
            AND ET.EmployeeID = '".$EmployeeID."' AND EI.IndicatorID = '".$IndicatorID."'"));
        }
        $Uom = Uom::all();
        $json['Uom'] = $Uom;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function calEvaluation(Request $request){
        $EmployeeID = $request->post('EmployeeID');
        $FromDate = $request->post('FromDate');
        $ToDate = $request->post('ToDate');
        $PeriodType = $request->post('PeriodType');
        $IndicatorID = $request->post('IndicatorID');
        $FromDate = IjDate::ConvertClientDateToServerDate($FromDate);
        $ToDate = IjDate::ConvertClientDateToServerDate($ToDate);
        $data = 1;
        if($FromDate && $ToDate && $PeriodType && $IndicatorID && $EmployeeID){
            $data = $this->calEvaluationFromChild($EmployeeID, $PeriodType, $FromDate, $ToDate, $IndicatorID, $PeriodType);
            if(!$data){
                $TotalEstimatedQuantityExcute = DB::select(DB::raw('
                    SELECT sum(ActualQuantity) ActualQuantityP FROM task_execution_trans WHERE TransDate >= "'.$Fromdate.' 00:00:00" AND TransDate <= "'.$Todate.' 23:59:59"
                    AND EmployeeID = "'.$EmployeeID.'"
                '));

                if($TotalEstimatedQuantityExcute[0]->ActualQuantityP){
                    $json['TotalEstimatedQuantity'] = $TotalEstimatedQuantityExcute[0]->ActualQuantityP;
                }else{
                    $json['TotalEstimatedQuantity'] = 0;
                }
            }

            $TotalPlanEstimatedQuantity = DB::select(DB::raw('SELECT SUM(EstimatedQuantity) Quantity FROM task_plan_trans_subitem
                    WHERE PeriodValue >= "'.$Fromdate.'" AND PeriodValue <= "'.$Todate.'"
                    AND EmployeeID = "'.$EmployeeID.'"
                '));
            if($TotalPlanEstimatedQuantity[0]->Quantity){
                $json['TotalPlanEstimatedQuantity'] = $TotalPlanEstimatedQuantity[0]->Quantity;
            }else{
                $json['TotalPlanEstimatedQuantity'] = 0;
            }
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function calEvaluationFromChild($EmployeeID, $PeriodType, $Fromdate, $Todate, $IndicatorID, $PeriodType1Job){
        if($PeriodType > 6){
            return $this->calEvaluationFrom1Job($EmployeeID, $PeriodType1Job, $Fromdate, $Todate, $IndicatorID);
        }
        $Evaluation = DB::select(DB::raw("SELECT C.IndicatorID, sum(C.ActualIndex) ActualIndex FROM task_evaluation_trans P
            LEFT JOIN task_evaluation_trans_item C
            ON P.TransID = C.TransID
            WHERE P.PeriodType = '".$PeriodType."' AND P.ToDate >= '".$Fromdate."' AND P.ToDate <= '".$Todate."'
            AND P.EmployeeID = '".$EmployeeID."' AND C.IndicatorID = '".$IndicatorID."'
            GROUP BY C.IndicatorID"));

        if($Evaluation){
            return $Evaluation;
        }else{
            if($PeriodType == 4){
                $PeriodType = 6;
            }else{
                $PeriodType++;
            }
            return $this->calEvaluationFromChild($EmployeeID, $PeriodType, $Fromdate, $Todate, $IndicatorID, $PeriodType1Job);
        }
    }

    public function calEvaluationFrom1Job($EmployeeID, $PeriodType, $Fromdate, $Todate, $IndicatorID){
        if($PeriodType > 6){
            return null;
        }
        $Evaluation = DB::select(DB::raw("SELECT C.IndicatorID, sum(C.LevelInt100) ActualIndex FROM task_evaluation_1job_trans P
            LEFT JOIN task_evaluation_1job_trans_item C
            ON P.TransID = C.TransID
            WHERE P.FrequencyType = '".$PeriodType."' AND P.TransDate >= '".$Fromdate."' AND P.TransDate <= '".$Todate."'
            AND P.EmployeeID = '".$EmployeeID."' AND C.IndicatorID = '".$IndicatorID."'
            GROUP BY C.IndicatorID"));

        if($Evaluation){
            return $Evaluation;
        }else{
            if($PeriodType == 4){
                $PeriodType = 6;
            }else{
                $PeriodType++;
            }
            return $this->calEvaluationFrom1Job($EmployeeID, $PeriodType, $Fromdate, $Todate, $IndicatorID);
        }
    }
}
