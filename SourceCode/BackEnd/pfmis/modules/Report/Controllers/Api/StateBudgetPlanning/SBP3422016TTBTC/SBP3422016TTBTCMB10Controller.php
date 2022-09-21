<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use IjCore\IjNumber;
use IjSmartBooks\IjCore\IjFunction\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Task\Models\Task;
use function foo\func;

class SBP3422016TTBTCMB10Controller extends Controller {
    public function SBP3422016TTBTC_MB10(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : 2021;
        $companyID = (isset($filter['CompanyID'])) ? $filter['CompanyID'] : null;
        $directionID = (isset($filter['DirectionID'])) ? $filter['DirectionID'] : null;
        $note = 'Ghi chú: ';
        if (isset($filter['SectorName'])) {
            $note .= 'Ngành ' . $filter['SectorName'] . ', ';
        }
        if (isset($filter['ProvinceName'])) {
            $note .= 'tỉnh ' . $filter['ProvinceName'] . ', ';
        }
        if (isset($filter['DistrictName'])) {
            $note .= 'huyện ' . $filter['DistrictName'] . ', ';
        }
        if (isset($filter['CommuneName'])) {
            $note .= 'xã ' . $filter['CommuneName'] . ', ';
        }
        if (isset($filter['CompanyName'])) {
            $note .= 'đơn vị ' . $filter['CompanyName'];
        }

        $whereCompany = '';
        if ($companyID) {
            $whereCompany .= " AND A.CompanyID = " . $companyID . ' ';
        }
        $whereDirection = '';
        if ($directionID) {
            $whereDirection = 'AND A.DirectionID = ' . $directionID . ' ';
        }

        $yearPre = (int)$year - 1;
        $yearNext = (int)$year + 1;

        $whereCondition = "AND (
        (YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $yearPre AND YEAR(A.PeriodToDate) = $yearPre)
        OR (YEAR(A.PeriodFromDate) = $yearNext AND YEAR(A.PeriodToDate) = $yearNext)
        ) $whereCompany";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `Show` int(1) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `ConAccountNoI1` varchar(20) DEFAULT '',
          `ConAccountNoI2` varchar(20) DEFAULT '',
          `ConAccountNoI3` varchar(20) DEFAULT '',
          `ConAccountNoI4` varchar(20) DEFAULT '',
          `SbiCategoryCateNo` varchar(20) DEFAULT '',
          `sbiCategoryCateValue` varchar(20) DEFAULT '',
          `I11` double(20,4) DEFAULT NULL,
          `I12` double(20,4) DEFAULT NULL,
          `I21` double(20,4) DEFAULT NULL,
          `I22` double(20,4) DEFAULT NULL,
          `I31` double(20,4) DEFAULT NULL,
          `I32` double(20,4) DEFAULT NULL,
          `I41` double(20,4) DEFAULT NULL,
          `I42` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $this->insertTemplate();
        $this->insertTableTmp($whereCondition, $yearPre, $year, $yearNext);

        // sum up
        $this->sumUpTableTmp(1, 3);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID')->get();
        Log::debug($data->toArray());

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $yearPre . '(năm trước)';
            $data[0]->HeaderName2 = 'Dự toán năm ' . $year . '(năm hiện hành)';
            $data[0]->HeaderName3 = 'Ước thực hiện năm ' . $year . '(năm hiện hành)';
            $data[0]->HeaderName4 = 'Dự toán năm ' .$yearNext . '(năm kế hoạch)';
        }

        foreach ($data as $key => $value) {
            $data[$key]->I11 = ($data[$key]->I11) ? $data[$key]->I11 : '';
            $data[$key]->I12 = ($data[$key]->I12) ? $data[$key]->I12 : '';
            $data[$key]->I21 = ($data[$key]->I21) ? $data[$key]->I21 : '';
            $data[$key]->I22 = ($data[$key]->I22) ? $data[$key]->I22 : '';
            $data[$key]->I31 = ($data[$key]->I31) ? $data[$key]->I31 : '';
            $data[$key]->I32 = ($data[$key]->I32) ? $data[$key]->I32 : '';
            $data[$key]->I41 = ($data[$key]->I41) ? $data[$key]->I41 : '';
            $data[$key]->I42 = ($data[$key]->I42) ? $data[$key]->I42 : '';
        }

        $company = Company::find($companyID);
        $dateTime = "Vĩnh Phú, ngày $date tháng $month năm $yearC";
        if ($company) {
            $dateTime = $company->Address . ", ngày $date tháng $month năm $yearC";
        }

        $response = [
            'master' => [
                "Year" => "",
                "CreateName" => "Trần Thế Thái",
                "TransNo" => "00001",
                "ReportName" => "DỰ TOÁN CHI BẰNG NGOẠI TỆ NĂM " . $yearNext,
                "DateTime" => $dateTime,
                "CompanyName" => ($company) ? $company->CompanyName : 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function insertTemplate(){
        $sbiCategoryCateValue = DB::table('sbi_category_cate_value')->where('CateNo', '001')->get();
        $arrInsert = [];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 0,
            'ItemID' => '#',
            'ParentID' => '',
            'ItemName' => 'Tổng số',
            'Level' => '1',
            'FontWeight' => '',
            'Italic' => '',
            'Center' => '',
            'ConAccountNoI1' => '',
            'ConAccountNoI2' => '',
            'ConAccountNoI3' => '',
            'ConAccountNoI4' => '',
            'SbiCategoryCateNo' => '',
            'SbiCategoryCateValue' => '',
        ];
        foreach ($sbiCategoryCateValue as $value) {
            if ($value->CateValue === '12') {
                $CateValue = '1';
            } elseif ($value->CateValue === '3') {
                $CateValue = '2';
            } elseif ($value->CateValue === '1') {
                $CateValue = '12';
            } elseif ($value->CateValue === '2') {
                $CateValue = '3';
            } else {
                $CateValue = $value->CateValue;
            }

            $ItemID = $CateValue;
            if (strlen($ItemID) == 1) {
                $ItemID = '0' . $ItemID;
            }
            $CateValue = (int)$CateValue;

            $romanNumber = IjNumber::ConvertNumberToRoman($CateValue);
            $arrInsert[] = [
                'STT' => $romanNumber,
                'Show' => 0,
                'ItemID' => '#' . $ItemID,
                'ParentID' => '#',
                'ItemName' => $value->Description,
                'Level' => '2',
                'FontWeight' => '1',
                'Italic' => '',
                'Center' => '',
                'ConAccountNoI1' => '',
                'ConAccountNoI2' => '',
                'ConAccountNoI3' => '',
                'ConAccountNoI4' => '',
                'SbiCategoryCateNo' => '',
                'SbiCategoryCateValue' => '',
            ];
        }
        DB::table('table_tmp')->insert($arrInsert);
    }

    public function insertTableTmp($whereCondition, $preYear, $year, $nextYear){
        $sqlSelect = "SELECT NormName, SbiCategoryCateValue, NormID,
            MAX(CASE WHEN ConAccountNo = '0271' AND B.YearD = $preYear THEN C1 END) AS I11,
            MAX(CASE WHEN ConAccountNo = '0271' AND B.YearD = $preYear THEN C2 END) AS I12,
            MAX(CASE WHEN ConAccountNo = '0251' AND B.YearD = $year THEN C1 END) AS I21,
            MAX(CASE WHEN ConAccountNo = '0251' AND B.YearD = $year THEN C2 END) AS I22,
            MAX(CASE WHEN ConAccountNo = '0211' AND B.YearD = $nextYear THEN C1 END) AS I31,
            MAX(CASE WHEN ConAccountNo = '0211' AND B.YearD = $nextYear THEN C2 END) AS I32,
            MAX(CASE WHEN ConAccountNo = '0221' AND B.YearD = $nextYear THEN C1 END) AS I41,
            MAX(CASE WHEN ConAccountNo = '0221' AND B.YearD = $nextYear THEN C2 END) AS I42
            FROM (SELECT SUM(FCDebitAmount) AS C1, SUM(LCDebitAmount) AS C2, A.ConAccountNo, SC.CateValue AS SbiCategoryCateValue, YEAR(A.PeriodFromDate)AS YearD, A.NormName, A.NormID
            FROM act_gl_books AS A
            INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
            INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
            WHERE A.CcyName = 'USD'
            AND NC.CateNo = '3200'
            AND SC.CateNo = '001'
            AND A.ConAccountNo IN ('0271', '0251', '0211', '0221')
            $whereCondition
            GROUP BY YearD, A.ConAccountNo, SbiCategoryCateValue, A.NormID) AS B";
        $result = DB::select($sqlSelect);
        $result = (array)$result;
        if ($result) {
            $arrInsert = [];
            $arrCount = [];
            foreach ($result as $value) {
                $value = (array)$value;
                if ($value['SbiCategoryCateValue'] && $value['NormID']) {
                    if ($value['SbiCategoryCateValue'] === '12') {
                        $CateValue = '1';
                    } elseif ($value['SbiCategoryCateValue'] === '3') {
                        $CateValue = '2';
                    } elseif ($value['SbiCategoryCateValue'] === '1') {
                        $CateValue = '12';
                    } elseif ($value['SbiCategoryCateValue'] === '2') {
                        $CateValue = '3';
                    } else {
                        $CateValue = $value['SbiCategoryCateValue'];
                    }
                    $ItemID = $CateValue;
                    if (strlen($ItemID) == 1) {
                        $ItemID = '0' . $ItemID;
                    }
                    $CateValue = (int)$CateValue;
                    $romanNumber = IjNumber::ConvertNumberToRoman($CateValue);
                    if (!isset($arrCount[$romanNumber])) {
                        $arrCount[$romanNumber] = 1;
                    }else{
                        $arrCount[$romanNumber] += 1;
                    }
                    $arrInsert[] = [
                        'STT' => $arrCount[$romanNumber],
                        'Show' => 1,
                        'ItemID' => '#' . $ItemID . '#' . $value['NormID'],
                        'ParentID' => '#' . $romanNumber,
                        'ItemName' => $value['NormName'],
                        'Level' => '3',
                        'FontWeight' => '',
                        'Italic' => '',
                        'Center' => '',
                        'ConAccountNoI1' => '0271',
                        'ConAccountNoI2' => '0251',
                        'ConAccountNoI3' => '0211',
                        'ConAccountNoI4' => '0221',
                        'I11' => $value['I11'],
                        'I12' => $value['I12'],
                        'I21' => $value['I21'],
                        'I22' => $value['I22'],
                        'I31' => $value['I31'],
                        'I32' => $value['I32'],
                        'I41' => $value['I41'],
                        'I42' => $value['I42'],
                        'SbiCategoryCateNo' => '',
                        'SbiCategoryCateValue' => $value['SbiCategoryCateValue']
                    ];
                }
            }
        }
        if (count($arrInsert)) {
            DB::table('table_tmp')->insert($arrInsert);
        }
    }

    // sum up
    public function sumUpTableTmp($start, $end = null){
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID, SUM(M.I11) AS I11, SUM(M.I12) AS I12, SUM(M.I21) AS I21, SUM(M.I22) AS I22, SUM(M.I31) AS I31, SUM(M.I32) AS I32, SUM(M.I41) AS I41, SUM(M.I42) AS I42
                FROM table_tmp M
                WHERE M.Level = " . $i . " GROUP BY ParentID";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
                        $I11 = ($value['I11']) ? $value['I11'] : 0;
                        $I12 = ($value['I12']) ? $value['I12'] : 0;
                        $I21 = ($value['I21']) ? $value['I21'] : 0;
                        $I22 = ($value['I22']) ? $value['I22'] : 0;
                        $I31 = ($value['I31']) ? $value['I31'] : 0;
                        $I32 = ($value['I32']) ? $value['I32'] : 0;
                        $I41 = ($value['I41']) ? $value['I41'] : 0;
                        $I42 = ($value['I42']) ? $value['I42'] : 0;
                        $update = "UPDATE table_tmp
                    SET I11 = " . $I11 . ", I12 = " . $I12 . ", I21 = " . $I21 . ", I22 = " . $I22 . ", I31 = " . $I31 . ", I32 = " . $I32 . ", I41 = " . $I41 . ", I42 = " . $I42 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }
}
