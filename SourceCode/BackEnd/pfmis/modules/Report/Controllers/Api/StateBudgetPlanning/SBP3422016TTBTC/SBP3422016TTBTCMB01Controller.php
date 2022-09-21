<?php

namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Accounting\Models\AccountingBook;
use Module\Report\Controllers\Controller;
use function foo\func;

class SBP3422016TTBTCMB01Controller extends Controller
{
    public function SBP3422016TTBTC_MB01(Request $request)
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => []
        ];

        $filter = $request->post('filter');
        Log::debug('aa');
        Log::debug($filter);
        $year = (isset($filter['Year'])) ? $filter['Year'] : 2020;
        $companyID = (isset($filter['CompanyID'])) ? $filter['CompanyID'] : null;
        $preYear = $year - 1;
        $nextYear = $year + 1;

        $whereCompany = '';
        if ($companyID) {
            $whereCompany .= " AND A.CompanyID = " . $companyID . ' ';
        }

        $wherePreCondition = "Year = $preYear";
        $whereCondition = "Year = $year";
        $whereNextCondition = "Year = $nextYear";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(200) DEFAULT '',
          `ParentID` varchar(200) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `Show` tinyint(1) DEFAULT 1,
          `BudgetLevel` tinyint(1) DEFAULT NULL,
          `AccountNoI1` varchar(30) DEFAULT NULL,
          `AccountNoI2` varchar(30) DEFAULT NULL,
          `AccountNoI3` varchar(30) DEFAULT NULL,
          `AccountNoI4` varchar(30) DEFAULT NULL,
          `RevenueCateNo` varchar(30) DEFAULT NULL,
          `RevenueCateValue` varchar(30) DEFAULT NULL,
          `SbiCategoryCateNo` varchar(30) DEFAULT NULL,
          `SbiCategoryCateValue` varchar(30) DEFAULT NULL,
          `RevenueNo` varchar(30) DEFAULT NULL,
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb01')->get();
        $insertArr = [];
        foreach ($templateTable as $template) {
            $insertArr[] = [
                'STT' => $template->STT,
                'ItemID' => $template->ItemID,
                'ParentID' => $template->ParentID,
                'Level' => $template->Level,
                'ItemName' => $template->ItemName,
                'FontWeight' => $template->FontWeight,
                'Italic' => $template->Italic,
                'BudgetLevel' => $template->BudgetLevel,
                'AccountNoI1' => $template->AccountNoI1,
                'AccountNoI2' => $template->AccountNoI2,
                'AccountNoI3' => $template->AccountNoI3,
                'AccountNoI4' => $template->AccountNoI4,
                'RevenueCateNo' => $template->RevenueCateNo,
                'RevenueCateValue' => $template->RevenueCateValue,
                'SbiCategoryCateNo' => $template->SbiCategoryCateNo,
                'SbiCategoryCateValue' => $template->SbiCategoryCateValue,
                'RevenueNo' => $template->RevenueNo,
                'Show' => $template->Show,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4
            ];
        }
        DB::table('table_tmp')->insert($insertArr);
        $this->insertDetail();

        $createTableData = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_data_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(200) DEFAULT '',
          `ParentID` varchar(200) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `Year` double(20,4) DEFAULT NULL,
          `AccountNo` double(20,4) DEFAULT NULL,
          `RevenueCateNo` varchar(20) DEFAULT '',
          `RevenueCateValue` varchar(20) DEFAULT '',
          `SbiCategoryCateNo` varchar(20) DEFAULT '',
          `SbiCategoryCateValue` varchar(20) DEFAULT '',
          `BudgetLevel` int(1) DEFAULT 0,
          `RevenueNo` varchar(30) DEFAULT NULL,
          `FCDebitAmount` double(20,4) DEFAULT NULL,
          `LCDebitAmount` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTableData);

        $sql = "SELECT A.*, YEAR(A.PeriodFromDate) AS 'Year', RC.CateNo AS RevenueCateNo, RC.CateValue AS RevenueCateValue, SC.CateNo AS SbiCategoryCateNo, SC.CateValue AS SbiCategoryCateValue
            FROM act_gl_books AS A
            INNER JOIN revenue_cate AS RC ON A.RevenueID = RC.RevenueID
            INNER JOIN sbi_chapter_cate AS SC ON A.SbiChapterID = SC.SbiChapterID
            WHERE YEAR(A.PeriodFromDate) IN ($preYear, $year, $nextYear)
            AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18
            AND (
            A.AccountNo IN ('0171', '0151', '0111', '0121')
            AND RC.CateNo IN ('1101', '1200')
            AND SC.CateNo IN ('002', '003', '004')
            ) $whereCompany
            UNION ALL
            SELECT A.*, YEAR(A.PeriodFromDate) AS 'Year', RC.CateNo AS RevenueCateNo, RC.CateValue AS RevenueCateValue,
            '' AS SbiCategoryCateNo, '' AS SbiCategoryCateValue
            FROM act_gl_books AS A
            INNER JOIN revenue_cate AS RC ON A.RevenueID = RC.RevenueID
            WHERE YEAR(A.PeriodFromDate) IN ($preYear, $year, $nextYear)
            AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18
            AND(
            A.AccountNo IN ('0171', '0151', '0111', '0121')
            AND RC.CateNo IN ('1102', '1200', '1104', '1105', '1106', '1107', '110301', '110302', '110304', '110305', '110306', '110307', '110308','110801')
            ) $whereCompany";
        $glBooksQuery = DB::select($sql);
        $glBooks = (array)$glBooksQuery;

        $itemsArr = [];
        if ($glBooks) {
            foreach ($glBooks as $glBook) {
                $glBook = (array)$glBook;

                $tmpArr = [];

                $tmpArr['Year'] = $glBook['Year'];
                $tmpArr['AccountNo'] = $glBook['AccountNo'];
                $tmpArr['RevenueCateNo'] = $glBook['RevenueCateNo'];
                $tmpArr['RevenueCateValue'] = $glBook['RevenueCateValue'];
                $tmpArr['SbiCategoryCateNo'] = $glBook['SbiCategoryCateNo'];
                $tmpArr['SbiCategoryCateValue'] = $glBook['SbiCategoryCateValue'];
                $tmpArr['BudgetLevel'] = $glBook['BudgetLevel'];
                $tmpArr['RevenueNo'] = $glBook['RevenueNo'];
                $tmpArr['FCDebitAmount'] = $glBook['FCDebitAmount'];
                $tmpArr['LCDebitAmount'] = $glBook['LCDebitAmount'];

                $itemsArr[] = $tmpArr;
            }

            if (count($itemsArr)) {
                DB::table('table_data_tmp')->insert($itemsArr);
            }

            // update to table_tmp
            $this->updateCT1('I1', $wherePreCondition);
            $this->updateCT2('I1', $wherePreCondition);
            $this->updateCT3('I1', $wherePreCondition);
            $this->updateCT4('I1', $wherePreCondition);
            $this->updateCT5('I1', $wherePreCondition);
            $this->updateCT6('I1', $wherePreCondition);

            $this->updateCT1('I2', $whereCondition);
            $this->updateCT2('I2', $whereCondition);
            $this->updateCT3('I2', $whereCondition);
            $this->updateCT4('I2', $whereCondition);
            $this->updateCT5('I2', $whereCondition);
            $this->updateCT6('I2', $whereCondition);

            $this->updateCT1('I3', $whereCondition);
            $this->updateCT2('I3', $whereCondition);
            $this->updateCT3('I3', $whereCondition);
            $this->updateCT4('I3', $whereCondition);
            $this->updateCT5('I3', $whereCondition);
            $this->updateCT6('I3', $whereCondition);

            $this->updateCT1('I4', $whereNextCondition);
            $this->updateCT2('I4', $whereNextCondition);
            $this->updateCT3('I4', $whereNextCondition);
            $this->updateCT4('I4', $whereNextCondition);
            $this->updateCT5('I4', $whereNextCondition);
            $this->updateCT6('I4', $whereNextCondition);

            // sum up
            $this->sumUpTableTmp(1, 4);
        }

        $data = DB::table('table_tmp')->where('Show', '=', 1)->get();
        foreach ($data as $key => $item) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';

            if ($key === 0) {
                $data[0]->ColumnHead1 = 'Thực hiện năm ' . $preYear;
                $data[0]->ColumnHead2 = 'Năm hiện hành ' . $year;
                $data[0]->ColumnHead3 = 'Dự toán năm ' . $nextYear;
            }
        }

        $response = [
            'master' => [
                "Year" => "",
                "CreateName" => "Trần Thế Thái",
                "TransNo" => "00001",
                "ReportName" => "TỔNG HỢP DỰ TOÁN THU NGÂN SÁCH NHÀ NƯỚC " . $year,
                "DateTime" => '',
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => ''
            ],
            'detail' => $data
        ];
        $json['data'] = $response;
        $json['status'] = 1;
        $json['msg'] = '';

        return json_encode($json);
    }

    // AccountNo + RevenueCateNo + RevenueCateValue + SbiCategoryCateNo + SbiCategoryCateValue
    public function updateCT1($I, $whereCondition){
        $sql = "UPDATE table_tmp as TT, (SELECT TD.AccountNo, TD.RevenueCateNo, TD.RevenueCateValue,
            TD.SbiCategoryCateNo, TD.SbiCategoryCateValue, SUM(FCDebitAmount) AS I
            FROM table_data_tmp AS TD
            WHERE $whereCondition
            GROUP BY TD.AccountNo, TD.RevenueCateNo, TD.RevenueCateValue, TD.SbiCategoryCateNo, TD.SbiCategoryCateValue
        )  as HH set TT.{$I} = HH.I
            WHERE TT.AccountNo{$I} = HH.AccountNo
            AND TT.RevenueCateNo = HH.RevenueCateNo
            AND TT.RevenueCateValue = HH.RevenueCateValue
            AND TT.SbiCategoryCateNo = HH.SbiCategoryCateNo
            AND TT.SbiCategoryCateValue = HH.SbiCategoryCateValue";

        DB::statement($sql);
    }

    // AccountNo + RevenueCateNo + RevenueCateValue + SbiCategoryCateNo
    public function updateCT2($I, $whereCondition){
        $sql = "UPDATE table_tmp as TT, (SELECT TD.AccountNo, TD.RevenueCateNo, TD.RevenueCateValue,
            TD.SbiCategoryCateNo, SUM(FCDebitAmount) AS I
            FROM table_data_tmp AS TD
            WHERE $whereCondition
            GROUP BY TD.AccountNo, TD.RevenueCateNo, TD.RevenueCateValue, TD.SbiCategoryCateNo
        )  as HH set TT.$I = HH.I
            WHERE TT.AccountNo{$I} = HH.AccountNo
            AND TT.RevenueCateNo = HH.RevenueCateNo
            AND TT.RevenueCateValue = HH.RevenueCateValue
            AND TT.SbiCategoryCateNo = HH.SbiCategoryCateNo";

        DB::statement($sql);
    }

    // AccountNo + RevenueCateNo + RevenueCateValue
    public function updateCT3($I, $whereCondition){
        $sql = "UPDATE table_tmp as TT, (SELECT TD.AccountNo, TD.RevenueCateNo,
            TD.RevenueCateValue, SUM(FCDebitAmount) AS I
            FROM table_data_tmp AS TD
            WHERE $whereCondition
            GROUP BY TD.AccountNo, TD.RevenueCateNo, TD.RevenueCateValue
        )  as HH set TT.$I = HH.I
            WHERE TT.AccountNo{$I} = HH.AccountNo
            AND TT.RevenueCateNo = HH.RevenueCateNo
            AND TT.RevenueCateValue = HH.RevenueCateValue";

        DB::statement($sql);
    }

    // AccountNo + RevenueNo + SbiCategoryCateNo + SbiCategoryCateValue
    public function updateCT4($I, $whereCondition){
        $sql = "UPDATE table_tmp as TT, (SELECT TD.AccountNo, TD.RevenueNo,
            TD.SbiCategoryCateNo, TD.SbiCategoryCateValue, SUM(FCDebitAmount) AS I
            FROM table_data_tmp AS TD
            WHERE $whereCondition
            GROUP BY TD.AccountNo, TD.RevenueNo, TD.SbiCategoryCateNo, TD.SbiCategoryCateValue
        )  as HH set TT.$I = HH.I
            WHERE TT.AccountNo{$I} = HH.AccountNo
            AND TT.RevenueNo = HH.RevenueNo
            AND TT.SbiCategoryCateNo = HH.SbiCategoryCateNo
            AND TT.SbiCategoryCateValue = HH.SbiCategoryCateValue";

        DB::statement($sql);
    }

    // AccountNo + RevenueNo + SbiCategoryCateNo
    public function updateCT5($I, $whereCondition){
        $sql = "UPDATE table_tmp as TT, (SELECT TD.AccountNo, TD.RevenueNo,
            TD.SbiCategoryCateNo, SUM(FCDebitAmount) AS I
            FROM table_data_tmp AS TD
            WHERE $whereCondition
            GROUP BY TD.AccountNo, TD.RevenueNo, TD.SbiCategoryCateNo
        )  as HH set TT.$I = HH.I
            WHERE TT.AccountNo{$I} = HH.AccountNo
            AND TT.RevenueNo = HH.RevenueNo
            AND TT.SbiCategoryCateNo = HH.SbiCategoryCateNo";

        DB::statement($sql);
    }

    // AccountNo + BudgetLevel + RevenueCateNo, RevenueCateValue
    public function updateCT6($I, $whereCondition){
        $sql = "UPDATE table_tmp as TT, (SELECT TD.AccountNo, TD.BudgetLevel, TD.RevenueCateNo,
            TD.RevenueCateValue, SUM(FCDebitAmount) AS I
            FROM table_data_tmp AS TD
            WHERE $whereCondition
            GROUP BY TD.AccountNo, TD.RevenueNo, TD.SbiCategoryCateNo
        )  as HH set TT.$I = HH.I
            WHERE TT.AccountNo{$I} = HH.AccountNo
            AND TT.BudgetLevel = HH.BudgetLevel
            AND TT.RevenueCateValue = HH.RevenueCateValue
            AND TT.RevenueCateNo = HH.RevenueCateNo";

        DB::statement($sql);
    }

    // insert item detail
    public function insertDetail(){
        $arrInsert = [
            // 1.1.1
            [
                'Show' => 0,
                'ParentID' => 'I#01#01#01#01',
                'Level' => '6',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171',
                'AccountNoI2' => '0151',
                'AccountNoI3' => '0111',
                'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '002',
                'SbiCategoryCateValue' => '1',
                'RevenueNo' => '11010902'
            ],
            [
                'Show' => 0,
                'ParentID' => 'I#01#01#01#01',
                'Level' => '6',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171',
                'AccountNoI2' => '0151',
                'AccountNoI3' => '0111',
                'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '002',
                'SbiCategoryCateValue' => '1',
                'RevenueNo' => '11017923'
            ],
//             1.1.3
            [
                'Show' => 0, 'ParentID' => 'I#01#01#03', 'Level' => '6',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '201110103',
                'RevenueCateValue' => '2',
                'SbiCategoryCateNo' => '002',
                'SbiCategoryCateValue' => '1',
                'RevenueNo' => null
            ],
            // 1.1.4
            [
                'Show' => 0, 'ParentID' => 'I#01#01#04#01', 'Level' => '6',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '002',
                'SbiCategoryCateValue' => '1',
                'RevenueNo' => '11010701'
            ],
            [
                'Show' => 0, 'ParentID' => 'I#01#01#04#01', 'Level' => '6',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '002',
                'SbiCategoryCateValue' => '1',
                'RevenueNo' => '11010709'
            ],
            [
                'Show' => 0, 'ParentID' => 'I#01#01#04#01', 'Level' => '6',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '002',
                'SbiCategoryCateValue' => '1',
                'RevenueNo' => '11010711'
            ],
            [
                'Show' => 0, 'ParentID' => 'I#01#01#04#01', 'Level' => '6',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '002',
                'SbiCategoryCateValue' => '1',
                'RevenueNo' => '11017918'
            ],
            [
                'Show' => 0, 'ParentID' => 'I#01#01#04#01', 'Level' => '6',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '002',
                'SbiCategoryCateValue' => '1',
                'RevenueNo' => '11017919'
            ],

//             1.2.3
            [
                'Show' => 0, 'ParentID' => 'I#01#02#03', 'Level' => '6',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '201110103',
                'RevenueCateValue' => '2',
                'SbiCategoryCateNo' => '002',
                'SbiCategoryCateValue' => '2',
                'RevenueNo' => null
            ],
            // 2.1
            [
                'Show' => 0, 'ParentID' => 'I#02#01#01', 'Level' => '5',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '003',
                'SbiCategoryCateValue' => null,
                'RevenueNo' => '11010902'
            ],
            [
                'Show' => 0, 'ParentID' => 'I#02#01#01', 'Level' => '5',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '003',
                'SbiCategoryCateValue' => null,
                'RevenueNo' => '11017923'
            ],
            // 2.2
            [
                'Show' => 0, 'ParentID' => 'I#02#02#01', 'Level' => '5',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '003',
                'SbiCategoryCateValue' => null,
                'RevenueNo' => '110204'
            ],
            [
                'Show' => 0, 'ParentID' => 'I#02#02#01', 'Level' => '5',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '003',
                'SbiCategoryCateValue' => null,
                'RevenueNo' => '1901102'
            ],
            // 2.4
            [
                'Show' => 0, 'ParentID' => 'I#02#04', 'Level' => '5',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '201110103',
                'RevenueCateValue' => '2',
                'SbiCategoryCateNo' => '003',
                'SbiCategoryCateValue' => null,
                'RevenueNo' => null
            ],
            // 2.5
            [
                'Show' => 0, 'ParentID' => 'I#02#05#01', 'Level' => '5',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '003',
                'SbiCategoryCateValue' => null,
                'RevenueNo' => '11010701'
            ],
            [
                'Show' => 0, 'ParentID' => 'I#02#05#01', 'Level' => '5',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '003',
                'SbiCategoryCateValue' => null,
                'RevenueNo' => '11010709'
            ],
            [
                'Show' => 0, 'ParentID' => 'I#02#05#01', 'Level' => '5',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '003',
                'SbiCategoryCateValue' => null,
                'RevenueNo' => '11010711'
            ],
            [
                'Show' => 0, 'ParentID' => 'I#02#05#01', 'Level' => '5',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '003',
                'SbiCategoryCateValue' => null,
                'RevenueNo' => '11017918'
            ],
            [
                'Show' => 0, 'ParentID' => 'I#02#05#01', 'Level' => '5',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => '003',
                'SbiCategoryCateValue' => null,
                'RevenueNo' => '11017919'
            ],
            // 3.3
            [
                'Show' => 0, 'ParentID' => 'I#03#03', 'Level' => '4',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '201110103',
                'RevenueCateValue' => '2',
                'SbiCategoryCateNo' => '004',
                'SbiCategoryCateValue' => null,
                'RevenueNo' => null
            ],
            // 7.1
            [
                'Show' => 0, 'ParentID' => 'I#07#01', 'Level' => '4',
                'BudgetLevel' => '1',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011104',
                'RevenueCateValue' => '1',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#07#01', 'Level' => '4',
                'BudgetLevel' => '1',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011104',
                'RevenueCateValue' => '2',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            // 7.2
            [
                'Show' => 0, 'ParentID' => 'I#07#02', 'Level' => '4',
                'BudgetLevel' => '2',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011104',
                'RevenueCateValue' => '1',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#07#02', 'Level' => '4',
                'BudgetLevel' => '2',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011104',
                'RevenueCateValue' => '2',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            // 7.3
            [
                'Show' => 0, 'ParentID' => 'I#07#03', 'Level' => '4',
                'BudgetLevel' => '3',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011104',
                'RevenueCateValue' => '1',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#07#03', 'Level' => '4',
                'BudgetLevel' => '3',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011104',
                'RevenueCateValue' => '2',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            // 7.4
            [
                'Show' => 0, 'ParentID' => 'I#07#04', 'Level' => '4',
                'BudgetLevel' => '4',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011104',
                'RevenueCateValue' => '1',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#07#04', 'Level' => '4',
                'BudgetLevel' => '4',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011104',
                'RevenueCateValue' => '2',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],

            // 7.4.1
            [
                'Show' => 0, 'ParentID' => 'I#07#04#01', 'Level' => '5',
                'BudgetLevel' => '4',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => '11013202'
            ],
            [
                'Show' => 0, 'ParentID' => 'I#07#04#01', 'Level' => '5',
                'BudgetLevel' => '4',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => null,
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => '11013203'
            ],
            // 9
            [
                'Show' => 0, 'ParentID' => 'I#09', 'Level' => '3',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011106',
                'RevenueCateValue' => '1',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#09', 'Level' => '3',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011106',
                'RevenueCateValue' => '2',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#09', 'Level' => '3',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011106',
                'RevenueCateValue' => '3',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#09', 'Level' => '3',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011106',
                'RevenueCateValue' => '4',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#09', 'Level' => '3',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011106',
                'RevenueCateValue' => '99',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            // 10
            [
                'Show' => 0, 'ParentID' => 'I#10', 'Level' => '3',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011107',
                'RevenueCateValue' => '1',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#10', 'Level' => '3',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011107',
                'RevenueCateValue' => '2',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#10', 'Level' => '3',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011107',
                'RevenueCateValue' => '3',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#10', 'Level' => '3',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011107',
                'RevenueCateValue' => '4',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#10', 'Level' => '3',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011107',
                'RevenueCateValue' => '99',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            // 10.1
            [
                'Show' => 0, 'ParentID' => 'I#10#01', 'Level' => '4',
                'BudgetLevel' => '1',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011107',
                'RevenueCateValue' => '1',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#10#01', 'Level' => '4',
                'BudgetLevel' => '1',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011107',
                'RevenueCateValue' => '2',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#10#01', 'Level' => '4',
                'BudgetLevel' => '1',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011107',
                'RevenueCateValue' => '3',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#10#01', 'Level' => '4',
                'BudgetLevel' => '1',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011107',
                'RevenueCateValue' => '4',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#10#01', 'Level' => '4',
                'BudgetLevel' => '1',
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '2011107',
                'RevenueCateValue' => '99',
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],

            // 11.1
            [
                'Show' => 0, 'ParentID' => 'I#11#01', 'Level' => '4',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '110401',
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#11#01', 'Level' => '4',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '190113',
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            // 11.2
            [
                'Show' => 0, 'ParentID' => 'I#11#02', 'Level' => '4',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '110402',
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => ''
            ],
            [
                'Show' => 0, 'ParentID' => 'I#11#02', 'Level' => '4',
                'BudgetLevel' => null,
                'AccountNoI1' => '0171', 'AccountNoI2' => '0151', 'AccountNoI3' => '0111', 'AccountNoI4' => '0121',
                'RevenueCateNo' => '190114',
                'RevenueCateValue' => null,
                'SbiCategoryCateNo' => null,
                'SbiCategoryCateValue' => null,
                'RevenueNo' => null
            ]
        ];
        DB::table('table_tmp')->insert($arrInsert);
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
                FROM table_tmp AS M
                WHERE M.Level = $i
                GROUP BY ParentID";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
                        $I1 = ($value['I1']) ? $value['I1'] : 0;
                        $I2 = ($value['I2']) ? $value['I2'] : 0;
                        $I3 = ($value['I3']) ? $value['I3'] : 0;
                        $I4 = ($value['I4']) ? $value['I4'] : 0;
                        $update = "UPDATE table_tmp
                    SET I1 = I1 + " . $I1 . ", I2 = I2 + " . $I2 . ", I3 = I3 + " . $I3 . ", I4 = I4 + " . $I4 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }
}
