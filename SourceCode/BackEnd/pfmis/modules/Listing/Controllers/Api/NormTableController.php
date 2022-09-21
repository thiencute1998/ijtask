<?php

namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use IjCore\IjDate;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\NormTable;
use Module\Listing\Models\NormTableItem;
use function GuzzleHttp\Psr7\str;

class NormTableController extends Controller
{
    public function list(Request $request){
        $data = NormTable::search($request);
        $json['status'] = 1;
        $json['data'] = $data;
        $json['msg'] = 'Thành công!';
        return json_encode($json);
    }

    public function export(Request $request){}

    public function create(){
        $auto = AutoNo::genNewNumberNoTable('norm_table', 'NormTableID', 'NormTableNo');
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }

    public function store(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $master = $request->post('master');
        $detail = $request->post('detail');
        $normTable = new NormTable([
            'NormTableNo' => $master['NormTableNo'],
            'NormTableDate' => ($master['NormTableDate']) ? IjDate::ConvertClientDateToServerDate($master['NormTableDate']) : null,
            'NormTableName' => $master['NormTableName'],
            'NormType' => $master['NormType'],
            'EffectiveDate' => ($master['EffectiveDate']) ? IjDate::ConvertClientDateToServerDate($master['EffectiveDate']) : null,
            'PeriodID' => $master['PeriodID'],
            'PeriodName' => $master['PeriodName'],
            'PeriodValue' => $master['PeriodValue'],
            'PeriodValueName' => $master['PeriodValueName'],
            'FromDate' => ($master['FromDate']) ? IjDate::ConvertClientDateToServerDate($master['FromDate']) : null,
            'ToDate' => ($master['ToDate']) ? IjDate::ConvertClientDateToServerDate($master['ToDate']) : null
        ]);

        if ($normTable->save()) {
            $itemInsertArr = [];
            $normTableID = $normTable->NormTableID;
            foreach ($detail as $tableItem) {
                $itemInsertArr[] = [
                    'NormTableItemNo' => $tableItem['NormTableItemNo'],
                    'NormType' => $tableItem['NormType'],
                    'NormTableItemName' => $tableItem['NormTableItemName'],
                    'ParentID' => $tableItem['ParentID'],
                    'Level' => $tableItem['Level'],
                    'Detail' => $tableItem['Detail'],
                    'NOrder' => $tableItem['NOrder'],
                    'NormTableID' => $normTable->NormTableID,
                    'NormTableName' => $normTable->NormTableName,
                    'NormTableNo' => $normTable->NormTableNo,
                    'NormID' => $tableItem['NormID'],
                    'NormNo' => $tableItem['NormNo'],
                    'NormName' => $tableItem['NormName'],
                    'NormAllotLevelID' => $tableItem['NormAllotLevelID'],
                    'NormAllotLevelNo' => $tableItem['NormAllotLevelNo'],
                    'NormAllotLevelName' => $tableItem['NormAllotLevelName'],
                    'UomID' => $tableItem['UomID'],
                    'UomNo' => $tableItem['UomNo'],
                    'UomName' => $tableItem['UomName'],
                    'CcyID' => $tableItem['CcyID'],
                    'CcyNo' => $tableItem['CcyNo'],
                    'CcyName' => $tableItem['CcyName'],
                    'ExchangeRate' => $tableItem['ExchangeRate'],
                    'Formula' => $tableItem['Formula'],
                    'Quantity' => (isset($tableItem['Quantity']) && $tableItem['Quantity']) ? $tableItem['Quantity'] : null,
                    'FCUnitPrice' => $tableItem['FCUnitPrice'],
                    'LCUnitPrice' => $tableItem['LCUnitPrice'],
                    'FCMinUnitPrice' => $tableItem['FCMinUnitPrice'],
                    'FCMaxUnitPrice' => $tableItem['FCMaxUnitPrice'],
                    'LCMinUnitPrice' => $tableItem['LCMinUnitPrice'],
                    'LCMaxUnitPrice' => $tableItem['LCMaxUnitPrice'],
                    'FCBaseUnitPrice' => $tableItem['FCBaseUnitPrice'],
                    'LCBaseUnitPrice' => $tableItem['LCBaseUnitPrice'],
                    'FCAmount' => $tableItem['FCAmount'],
                    'LCAmount' => $tableItem['LCAmount'],
                    'UnitPriceType' => $tableItem['UnitPriceType'],
                    'SaveRate' => $tableItem['SaveRate'],
                    'FCSaveAmount' => $tableItem['FCSaveAmount'],
                    'LCSaveAmount' => $tableItem['LCSaveAmount'],
                    'LineIDTmp' => $tableItem['LineIDTmp'],
                ];
            }

            if (count($itemInsertArr)) {
                DB::table('norm_table_item')->insert($itemInsertArr);

                $updateSql = "UPDATE norm_table_item AS A, (SELECT NormTableItemID, LineIDTmp FROM norm_table_item WHERE NormTableID = $normTableID) AS B
                    SET A.ParentID = B.NormTableItemID
                    WHERE A.NormTableID = $normTableID AND A.ParentID = B.LineIDTmp";
                DB::update(DB::raw($updateSql));

                $updateSqlLineID = "UPDATE norm_table_item AS A
                    SET A.LineIDTmp = A.NormTableItemID
                    WHERE A.NormTableID = $normTableID";
                DB::update(DB::raw($updateSqlLineID));
            }

            $json = [
                'status' => 1,
                'msg' => 'Thành công',
                'data' => $normTableID
            ];
        }
        return json_encode($json);
    }
    public function edit($id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $normTable = NormTable::find($id);
        if ($normTable) {
            $normTableItem = NormTableItem::where('NormTableID', '=', $id)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $normTable;
            $json['data']['NormTableItem'] = $normTableItem;
        }
        return json_encode($json);
    }
    public function update(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $normTable = NormTable::find($id);
        if (!$normTable) return json_encode($json);
        $master = $request->post('master');
        $detail = $request->post('detail');

        $normTable->NormTableNo = $master['NormTableNo'];
        $normTable->NormTableDate = (isset($master['NormTableDate'])) ? IjDate::ConvertClientDateToServerDate($master['NormTableDate']) : null;
        $normTable->NormTableName = $master['NormTableName'];
        $normTable->EffectiveDate = (isset($master['EffectiveDate'])) ? IjDate::ConvertClientDateToServerDate($master['EffectiveDate']) : null;
        $normTable->ExpirationDate = (isset($master['ExpirationDate'])) ? IjDate::ConvertClientDateToServerDate($master['ExpirationDate']) : null;
        $normTable->FromDate = (isset($master['FromDate'])) ? IjDate::ConvertClientDateToServerDate($master['FromDate']) : null;
        $normTable->ToDate = (isset($master['ToDate'])) ? IjDate::ConvertClientDateToServerDate($master['ToDate']) : null;
        $normTable->PeriodID = $master['PeriodID'];
        $normTable->PeriodName = $master['PeriodName'];
        $normTable->PeriodValue = $master['PeriodValue'];
        $normTable->PeriodValueName = $master['PeriodValueName'];

        if ($normTable->save()) {
            DB::beginTransaction();
            try {
                DB::table('norm_table_item')->where('NormTableID', '=', $id)->delete();

                $itemInsertArr = [];
                $normTableID = $normTable->NormTableID;
                foreach ($detail as $tableItem) {
                    $itemInsertArr[] = [
                        'NormTableItemNo' => $tableItem['NormTableItemNo'],
                        'NormType' => (isset($tableItem['NormType'])) ? $tableItem['NormType'] : 1,
                        'NormTableItemName' => (isset($tableItem['NormTableItemName'])) ? $tableItem['NormTableItemName'] : null,
                        'ParentID' => (isset($tableItem['ParentID'])) ? $tableItem['ParentID'] : 0,
                        'Level' => (isset($tableItem['Level'])) ? $tableItem['Level'] : 1,
                        'Detail' => (isset($tableItem['Detail'])) ? $tableItem['Detail'] : 1,
                        'NOrder' => (isset($tableItem['NOrder'])) ? $tableItem['NOrder'] : '',
                        'NormTableID' => (isset($normTable->NormTableID)) ? $normTable->NormTableID : null,
                        'NormTableName' => (isset($normTable->NormTableName)) ? $normTable->NormTableName : null,
                        'NormTableNo' => (isset($normTable->NormTableNo)) ? $normTable->NormTableNo : null,
                        'NormID' => (isset($tableItem['NormID'])) ? $tableItem['NormID'] : null,
                        'NormNo' => (isset($tableItem['NormNo'])) ? $tableItem['NormNo'] : null,
                        'NormName' => (isset($tableItem['NormName'])) ? $tableItem['NormName'] : null,
                        'NormAllotLevelID' => (isset($tableItem['NormAllotLevelID'])) ? $tableItem['NormAllotLevelID'] : null,
                        'NormAllotLevelNo' => (isset($tableItem['NormAllotLevelNo'])) ? $tableItem['NormAllotLevelNo'] : null,
                        'NormAllotLevelName' => (isset($tableItem['NormAllotLevelName'])) ? $tableItem['NormAllotLevelName'] : null,
                        'UomID' => (isset($tableItem['UomID'])) ? $tableItem['UomID'] : null,
                        'UomNo' => (isset($tableItem['UomNo'])) ? $tableItem['UomNo'] : null,
                        'UomName' => (isset($tableItem['UomName'])) ? $tableItem['UomName'] : null,
                        'CcyID' => (isset($tableItem['CcyID'])) ? $tableItem['CcyID'] : null,
                        'CcyNo' => (isset($tableItem['CcyNo'])) ? $tableItem['CcyNo'] : null,
                        'CcyName' => (isset($tableItem['CcyName'])) ? $tableItem['CcyName'] : null,
                        'ExchangeRate' => (isset($tableItem['ExchangeRate'])) ? $tableItem['ExchangeRate'] : null,
                        'Formula' => (isset($tableItem['Formula'])) ? $tableItem['Formula'] : null,
                        'Quantity' => (isset($tableItem['Quantity']) && $tableItem['Quantity']) ? $tableItem['Quantity'] : null,
                        'FCUnitPrice' => (isset($tableItem['FCUnitPrice'])) ? $tableItem['FCUnitPrice'] : null,
                        'LCUnitPrice' => (isset($tableItem['LCUnitPrice'])) ? $tableItem['LCUnitPrice'] : null,
                        'FCMinUnitPrice' => (isset($tableItem['FCMinUnitPrice'])) ? $tableItem['FCMinUnitPrice'] : null,
                        'FCMaxUnitPrice' => (isset($tableItem['FCMaxUnitPrice'])) ? $tableItem['FCMaxUnitPrice'] : null,
                        'LCMinUnitPrice' => (isset($tableItem['LCMinUnitPrice'])) ? $tableItem['LCMinUnitPrice'] : null,
                        'LCMaxUnitPrice' => (isset($tableItem['LCMaxUnitPrice'])) ? $tableItem['LCMaxUnitPrice'] : null,
                        'FCBaseUnitPrice' => (isset($tableItem['FCBaseUnitPrice'])) ? $tableItem['FCBaseUnitPrice'] : null,
                        'LCBaseUnitPrice' => (isset($tableItem['LCBaseUnitPrice'])) ? $tableItem['LCBaseUnitPrice'] : null,
                        'FCAmount' => (isset($tableItem['FCAmount'])) ? $tableItem['FCAmount'] : null,
                        'LCAmount' => (isset($tableItem['LCAmount'])) ? $tableItem['LCAmount'] : null,
                        'UnitPriceType' => (isset($tableItem['UnitPriceType'])) ? $tableItem['UnitPriceType'] : null,
                        'SaveRate' => (isset($tableItem['SaveRate'])) ? $tableItem['SaveRate'] : null,
                        'FCSaveAmount' => (isset($tableItem['FCSaveAmount'])) ? $tableItem['FCSaveAmount'] : null,
                        'LCSaveAmount' => (isset($tableItem['LCSaveAmount'])) ? $tableItem['LCSaveAmount'] : null,
                        'LineIDTmp' => (isset($tableItem['LineIDTmp'])) ? $tableItem['LineIDTmp'] : 0,
                    ];
                }

                if (count($itemInsertArr)) {
                    DB::table('norm_table_item')->insert($itemInsertArr);
                }

                $json = [
                    'status' => 1,
                    'msg' => 'Thành công',
                    'data' => $normTableID
                ];
                DB::commit();

                $updateSql = "UPDATE norm_table_item AS A, (SELECT NormTableItemID, LineIDTmp FROM norm_table_item WHERE NormTableID = $normTableID) AS B
                    SET A.ParentID = B.NormTableItemID
                    WHERE A.NormTableID = $normTableID AND A.ParentID = B.LineIDTmp";
                DB::update(DB::raw($updateSql));

                $updateSqlLineID = "UPDATE norm_table_item AS A
                    SET A.LineIDTmp = A.NormTableItemID
                    WHERE A.NormTableID = $normTableID";
                DB::update(DB::raw($updateSqlLineID));

                $json['status'] = 1;
                $json['msg'] = 'Thành công';
            } catch (Exception $e) {
                DB::rollBack();
                $json['msg'] = $e->getMessage();
                throw new Exception($e->getMessage());
            }

        }
        return json_encode($json);
    }
    public function view($id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $normTable = NormTable::find($id);
        if ($normTable) {
            $normTableItem = NormTableItem::where('NormTableID', '=', $id)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $normTable;
            $json['data']['NormTableItem'] = $normTableItem;
        }
        return json_encode($json);
    }
    public function calculatorTableItem(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $Formula = $request->post('Formula');
        $currentFormula = $request->post('CurrentFormula');
        $Formula = trim($Formula);
        $formulaPieces = preg_split('/(:|-|\*|\+|\(|\)|\/)/', $Formula);
        $formulaArr = [];

        foreach ($formulaPieces as $formulaPiece) {
            if ($formulaPiece && strpos($formulaPiece, '@DMDT') !== false) {
                $formula = trim($formulaPiece);
                if ($currentFormula && array_key_exists($formula, $currentFormula)) {
                    $formulaArr[$formula] = $currentFormula[$formula];
                }else{
                    $formulaArr[$formula] = NormTableItem::calculatorFormula($formula);
                }
            }
        }

        $Formula = str_replace(':', '/', $Formula);
        $expressionFCMinUnitPrice = $Formula;
        $expressionFCMaxUnitPrice = $Formula;
        $expressionLCMinUnitPrice = $Formula;
        $expressionLCMaxUnitPrice = $Formula;

        if (count($formulaArr)) {
            foreach ($formulaArr as $key => $formula) {
                $expressionFCMinUnitPrice = str_replace($key, $formula['FCMinUnitPrice'], $expressionFCMinUnitPrice);
                $expressionFCMaxUnitPrice = str_replace($key, $formula['FCMaxUnitPrice'], $expressionFCMaxUnitPrice);
                $expressionLCMinUnitPrice = str_replace($key, $formula['LCMinUnitPrice'], $expressionLCMinUnitPrice);
                $expressionLCMaxUnitPrice = str_replace($key, $formula['LCMaxUnitPrice'], $expressionLCMaxUnitPrice);
            }

            $result = [
                'FCMinUnitPrice' => 0,
                'FCMaxUnitPrice' => 0,
                'LCMinUnitPrice' => 0,
                'LCMaxUnitPrice' => 0
            ];
            try{
                $result['FCMinUnitPrice'] = eval('return ' . $expressionFCMinUnitPrice . ';');
                $result['FCMaxUnitPrice'] = eval('return ' . $expressionFCMaxUnitPrice . ';');
                $result['LCMinUnitPrice'] = eval('return ' . $expressionLCMinUnitPrice . ';');
                $result['LCMaxUnitPrice'] = eval('return ' . $expressionLCMaxUnitPrice . ';');

                $json['status'] = 1;
                $json['msg'] = 'Thành công';
                $json['data'] = $result;
            }
            catch(Exception $e){
                Log::debug($e);
            }
        }else{
            $result = [
                'FCMinUnitPrice' => 0,
                'FCMaxUnitPrice' => 0,
                'LCMinUnitPrice' => 0,
                'LCMaxUnitPrice' => 0
            ];

            $result['FCMinUnitPrice'] = eval('return ' . $expressionFCMinUnitPrice . ';');
            $result['FCMaxUnitPrice'] = eval('return ' . $expressionFCMaxUnitPrice . ';');
            $result['LCMinUnitPrice'] = eval('return ' . $expressionLCMinUnitPrice . ';');
            $result['LCMaxUnitPrice'] = eval('return ' . $expressionLCMaxUnitPrice . ';');

            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $result;
        }

        return json_encode($json);
    }
    public function reCalculatorTableItem(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $Formulas = $request->post('Formulas');
        $currentFormula = $request->post('CurrentFormula');
        $whereArr = [];
        $formulaArr = [];
        foreach ($Formulas as $item) {
            $Formula = trim($item['Formula']);
            $formulaPieces = preg_split('/(:|-|\*|\+|\(|\)|\/)/', $Formula);

            foreach ($formulaPieces as $formulaPiece) {
                if ($formulaPiece && strpos($formulaPiece, '@DMDT') !== false) {
                    $formula = trim($formulaPiece);

                    if (array_key_exists($formula, $currentFormula)) {
                        $formulaArr[$formula] = $currentFormula[$formula];
                    }else{
                        if (!array_key_exists($formula, $formulaArr)) {
                            $formulaArr[$formula] = [];
                            $formulaPieces = preg_split('/(&|\*|%)/', $formula);
                            if ($formulaPieces[0] === '@DMDT' && $formulaPieces[1] && $formulaPieces[2]) {
                                $whereArr[$formula]['NormTableNo'] = $formulaPieces[1];
                                $whereArr[$formula]['NormTableItemNo'] = $formulaPieces[2];
                            }
                        }
                    }
                }
            }
        }

        if (count($whereArr)) {
            $normTableItemQuery = NormTableItem::query();
            $normTableItemQuery->select('NormTableNo', 'NormTableItemNo', 'FCMinUnitPrice', 'FCMaxUnitPrice', 'LCMinUnitPrice', 'LCMaxUnitPrice');
            foreach ($whereArr as $key => $item) {
                if ($item['NormTableNo'] && $item['NormTableItemNo']) {
                    $normTableItemQuery->orWhere([
                        'NormTableNo' => $item['NormTableNo'],
                        'NormTableItemNo' => $item['NormTableItemNo']
                    ]);
                }
            }
            $normTableItems = $normTableItemQuery->get();
            foreach ($normTableItems as $normTableItem) {
                $formula = '@DMDT&' . $normTableItem->NormTableNo . '&' . $normTableItem->NormTableItemNo;
                $formulaArr[$formula] = [
                    'FCMinUnitPrice' => $normTableItem->FCMinUnitPrice,
                    'FCMaxUnitPrice' => $normTableItem->FCMaxUnitPrice,
                    'LCMinUnitPrice' => $normTableItem->LCMinUnitPrice,
                    'LCMaxUnitPrice' => $normTableItem->LCMaxUnitPrice
                ];
            }
        }

        $calculator = [];
        foreach ($Formulas as $item) {
            $Formula = $item['Formula'];
            $Formula = str_replace(':', '/', $Formula);
            $expressionFCMinUnitPrice = $Formula;
            $expressionFCMaxUnitPrice = $Formula;
            $expressionLCMinUnitPrice = $Formula;
            $expressionLCMaxUnitPrice = $Formula;

            if (count($formulaArr)) {
                foreach ($formulaArr as $key => $formula) {
                    if (strpos($Formula, $key) !== false) {
                        $expressionFCMinUnitPrice = str_replace($key, $formula['FCMinUnitPrice'], $expressionFCMinUnitPrice);
                        $expressionFCMaxUnitPrice = str_replace($key, $formula['FCMaxUnitPrice'], $expressionFCMaxUnitPrice);
                        $expressionLCMinUnitPrice = str_replace($key, $formula['LCMinUnitPrice'], $expressionLCMinUnitPrice);
                        $expressionLCMaxUnitPrice = str_replace($key, $formula['LCMaxUnitPrice'], $expressionLCMaxUnitPrice);
                    }
                }

                $result = [
                    'FCMinUnitPrice' => 0,
                    'FCMaxUnitPrice' => 0,
                    'LCMinUnitPrice' => 0,
                    'LCMaxUnitPrice' => 0
                ];
                try{
                    $result['FCMinUnitPrice'] = eval('return ' . $expressionFCMinUnitPrice . ';');
                    $result['FCMaxUnitPrice'] = eval('return ' . $expressionFCMaxUnitPrice . ';');
                    $result['LCMinUnitPrice'] = eval('return ' . $expressionLCMinUnitPrice . ';');
                    $result['LCMaxUnitPrice'] = eval('return ' . $expressionLCMaxUnitPrice . ';');

                    if ($Formula) {
                        $calculator[$item['NormTableItemID']] = $result;
                    }
                }
                catch(Exception $e){}
            }else{
                $result = [
                    'FCMinUnitPrice' => 0,
                    'FCMaxUnitPrice' => 0,
                    'LCMinUnitPrice' => 0,
                    'LCMaxUnitPrice' => 0
                ];

                $result['FCMinUnitPrice'] = eval('return ' . $expressionFCMinUnitPrice . ';');
                $result['FCMaxUnitPrice'] = eval('return ' . $expressionFCMaxUnitPrice . ';');
                $result['LCMinUnitPrice'] = eval('return ' . $expressionLCMinUnitPrice . ';');
                $result['LCMaxUnitPrice'] = eval('return ' . $expressionLCMaxUnitPrice . ';');

                if ($Formula) {
                    $calculator[$item['NormTableItemID']] = $result;
                }
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $calculator;

        return json_encode($json);
    }
    public function delete($id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        if ($id) {
            DB::beginTransaction();
            try {
                DB::table('norm_table_item')->where('NormTableID', '=', $id)->delete();
                NormTable::destroy($id);
                DB::commit();
                $json['status'] = 1;
                $json['msg'] = 'Thành công';
            } catch (Exception $e) {
                DB::rollBack();
                $json['msg'] = $e->getMessage();
                throw new Exception($e->getMessage());
            }
        }

        return json_encode($json);
    }
    public function deleteArray(Request $request){
        $idArray = $request->post('array_id');
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        if (count($idArray) === 1) {
            $normTableID = array_shift($idArray);
            if ($normTableID) {
                DB::beginTransaction();
                try {
                    DB::table('norm_table_item')->where('NormTableID', '=', $normTableID)->delete();
                    NormTable::destroy($normTableID);
                    DB::commit();
                    $json['status'] = 1;
                    $json['msg'] = 'Thành công';
                } catch (Exception $e) {
                    DB::rollBack();
                    $json['msg'] = $e->getMessage();
                    throw new Exception($e->getMessage());
                }
            }
        } elseif (count($idArray) > 1) {
            DB::beginTransaction();
            try {
                DB::table('norm_table_item')->whereIn('NormTableID', $idArray)->delete();
                NormTable::destroy($idArray);
                DB::commit();
                $json['status'] = 1;
                $json['msg'] = 'Thành công';
            } catch (Exception $e) {
                DB::rollBack();
                $json['msg'] = $e->getMessage();
                throw new Exception($e->getMessage());
            }

        }else{
            $json['msg'] = 'Không có bản ghi nào được chọn';
            return json_encode($json);
        }

        return json_encode($json);
    }
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $id = $request->id;
        $master = NormTable::find($id);
        $detail = NormTableItem::where('NormTableID', '=', $id)->get();
        $detailResponse = [];
        foreach ($detail as $item) {
            $item->NormType = ($item->NormType === 1) ? 'Thu' : 'Chi';
            $detailResponse[] = $item;
        }

        $response = [
            'master' => [
                "NormTableName" => $master->NormTableName,
                "NormTableNo" => $master->NormTableNo,
                "NormTableDate" => IjDate::DateToString($master->NormTableDate, 'd/m/Y'),
                "EffectiveDate" => IjDate::DateToString($master->EffectiveDate, 'd/m/Y'),
                "PeriodName" => IjDate::DateToString($master->PeriodName, 'd/m/Y'),
                "FromDate" => IjDate::DateToString($master->FromDate, 'd/m/Y'),
                "ToDate" => IjDate::DateToString($master->ToDate, 'd/m/Y'),
                "ExpirationDate" => IjDate::DateToString($master->ExpirationDate, 'd/m/Y')
            ],
            'detail' => $detailResponse
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    public function getNormAllotLevel($id){
        $normAllotLevel = DB::table('norm_allot_level')
            ->select('norm_allot_level_item.*', 'norm_allot_level.NormID', 'norm_allot_level.NormNo', 'norm_allot_level.NormName')
            ->join('norm_allot_level_item', 'norm_allot_level.NormAllotLevelID', '=', 'norm_allot_level_item.NormAllotLevelID')
            ->where('norm_allot_level_item.NormAllotLevelID', $id)
            ->orderBy('norm_allot_level_item.EffectiveDate', 'desc')
            ->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $normAllotLevel;
        return json_encode($json);
    }

}
