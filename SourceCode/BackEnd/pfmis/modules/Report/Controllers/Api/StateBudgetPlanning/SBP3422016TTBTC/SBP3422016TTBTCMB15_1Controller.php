<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB15_1Controller extends Controller {
    public function SBP3422016TTBTC_MB15_1(Request $request){
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
        $preYear = (int)$year - 1;
        $nextYear = (int)$year + 1;
        $whereCondition = "AND (
        ( YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $preYear AND YEAR(A.PeriodToDate) = $preYear)
        OR (YEAR(A.PeriodFromDate) = $nextYear AND YEAR(A.PeriodToDate) = $nextYear)
        ) $whereCompany  AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `TranTypeID` int(2) DEFAULT 1,
          `Show` int(1) DEFAULT 1,
          `ItemNo` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT NULL,
          `AccountNo` varchar(15) DEFAULT '',
          `CoAccountNo` varchar(15) DEFAULT '',
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `CompanyID` varchar(11) DEFAULT '',
          `CompanyName` varchar(100) DEFAULT '',
          `CompanyNo` varchar(100) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          `I5` double(20,4) DEFAULT NULL,
          `I6` double(20,4) DEFAULT NULL,
          `I7` double(20,4) DEFAULT NULL,
          `I8` double(20,4) DEFAULT NULL,
          `I9` double(20,4) DEFAULT NULL,
          `I10` double(20,4) DEFAULT NULL,
          `I11` double(20,4) DEFAULT NULL,
          `I12` double(20,4) DEFAULT NULL,
          `I13` double(20,4) DEFAULT NULL,
          `I14` double(20,4) DEFAULT NULL,
          `I15` double(20,4) DEFAULT NULL,
          `I16` double(20,4) DEFAULT NULL,
          `I17` double(20,4) DEFAULT NULL,
          `I18` double(20,4) DEFAULT NULL,
          `I19` double(20,4) DEFAULT NULL,
          `I20` double(20,4) DEFAULT NULL,
          `I21` double(20,4) DEFAULT NULL,
          `I22` double(20,4) DEFAULT NULL,

          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $this->MB15_1InsertTemplate();
        $this->MB15_1InsertTemplateLV($whereCondition);
        $this->updateI1_2_7_12_13_18($whereCondition,$preYear, $year, $nextYear);
        $this->updateI4_5_6_9_10_11_15_16_17_20_21_22($whereCondition,$preYear, $year, $nextYear);
        // sum up
        $this->sumUpTableTmp(1, 3);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID')->get();

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Kế hoạch năm ' . $year ;
            $data[0]->HeaderName2 = 'Ước thực hiện năm ' . $year ;
            $data[0]->HeaderName3 = 'Tồn kho đến 31/12 năm ' . $year ;
            $data[0]->HeaderName4 = 'Kế hoạch năm ' .$nextYear ;
        }

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = (int)($data[$key]->I4) + (int)($data[$key]->I5) + (int)($data[$key]->I6);
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
            $data[$key]->I7 = ($data[$key]->I7) ? $data[$key]->I7 : '';
            $data[$key]->I8 = (int)($data[$key]->I9) + (int)($data[$key]->I10) + (int)($data[$key]->I11);
            $data[$key]->I9 = ($data[$key]->I9) ? $data[$key]->I9 : '';
            $data[$key]->I10 = ($data[$key]->I10) ? $data[$key]->I10 : '';
            $data[$key]->I11 = ($data[$key]->I11) ? $data[$key]->I11 : '';
            $data[$key]->I12 = ($data[$key]->I12) ? $data[$key]->I12 : '';
            $data[$key]->I13 = ($data[$key]->I13) ? $data[$key]->I13 : '';
            $data[$key]->I14 = (int)($data[$key]->I15) + (int)($data[$key]->I16) + (int)($data[$key]->I17);
            $data[$key]->I15 = ($data[$key]->I15) ? $data[$key]->I15 : '';
            $data[$key]->I16 = ($data[$key]->I16) ? $data[$key]->I16 : '';
            $data[$key]->I17 = ($data[$key]->I17) ? $data[$key]->I17 : '';
            $data[$key]->I18 = ($data[$key]->I18) ? $data[$key]->I18 : '';
            $data[$key]->I19 = (int)($data[$key]->I20) + (int)($data[$key]->I21) + (int)($data[$key]->I22);
            $data[$key]->I20 = ($data[$key]->I20) ? $data[$key]->I20 : '';
            $data[$key]->I21 = ($data[$key]->I21) ? $data[$key]->I21 : '';
            $data[$key]->I22 = ($data[$key]->I22) ? $data[$key]->I22 : '';

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
//                "TransNo" => "00001",
                "ReportName" => "CƠ SỞ TÍNH CHI MUA BỔ SUNG HÀNG DỰ TRỮ QUỐC GIA NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => ($company) ? $company->CompanyName : 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    // Level 1
    public function MB15_1InsertTemplate(){
        $arrInsert = [
            [
                'STT' => '',
                'Show' => 1,
                'CompanyID' => 'A#',
                'ParentID' => '',
                'Level' => '1',
                'FontWeight' => '1',
                'Italic' => '',
                'Center' => '1',
                'ItemNo'=>'',
                'ItemName' => 'Tổng số',
                'AccountNo'=>'',
                'CoAccountNo'=>'',
                'CompanyNo'=>'',
                'CompanyName'=>'',
                'I1'=>'',
                'I2'=>'',
                'I3'=>'',
                'I4'=>'',
                'I5'=>'',
                'I6'=>'',
                'I7'=>'',
                'I8'=>'',
                'I9'=>'',
                'I10'=>'',
                'I11'=>'',
                'I12'=>'',
                'I13'=>'',
                'I14'=>'',
                'I15'=>'',
                'I16'=>'',
                'I17'=>'',
                'I18'=>'',
                'I19'=>'',
                'I20'=>'',
                'I21'=>'',
                'I22'=>''
            ],

        ];
        DB::table('table_tmp')->insert($arrInsert);
    }
    public function MB15_1InsertTemplateLV($whereCondition)
    {
        $sql = "SELECT A.PeriodToDate, A.PeriodFromDate, A.FiscalYear, A.AccountNo AccountNo, A.CoAccountNo CoAccountNo,A.CompanyID CompanyID, A.CompanyNo CompanyNo, A.CompanyName CompanyName,
                   SC.CateNo SectorCateNo, CCL.CompanyID CompanyID
                    FROM act_gl_books A
                        INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                        INNER JOIN company CCL ON CCL.CompanyID = A.CompanyID
                        INNER JOIN norm_cate NC ON NC.NormID = A.NormID
                        WHERE SC.CateNo = '003' AND SC.CateValue='12'
                        $whereCondition
                        GROUP BY  CompanyName";

        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert1 = [];
        $i = 1;
        if ($result) {
            foreach ($result as $template) {
                $STT = $i++;
                $CompanyID = 'A#' . $template->CompanyID ;
                $ParentID = 'A#';
                $arrInsert1[] = [
                    'STT' => $STT,
                    'Show'=>'',
                    'CompanyID' => $CompanyID,
                    'ParentID' => $ParentID,
                    'Level'=>'2',
                    'FontWeight'=> '',
                    'Italic' =>'',
                    'Center'=>'',
                    'CompanyNo' => $template->CompanyNo,
                    'AccountNo' => $template->AccountNo,
                    'CoAccountNo' => $template->CoAccountNo,
                    'ItemName'=>$template->CompanyName,
                    'I1'=>'',
                    'I2'=>'',
                    'I3'=>'',
                    'I4'=>'',
                    'I5'=>'',
                    'I6'=>'',
                    'I7'=>'',
                    'I8'=>'',
                    'I9'=>'',
                    'I10'=>'',
                    'I11'=>'',
                    'I12'=>'',
                    'I13'=>'',
                    'I14'=>'',
                    'I15'=>'',
                    'I16'=>'',
                    'I17'=>'',
                    'I18'=>'',
                    'I19'=>'',
                    'I20'=>'',
                    'I21'=>'',
                    'I22'=>''
                ];
            }
            DB::table('table_tmp')->insert($arrInsert1);
        }
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }
        public function updateI1_2_7_12_13_18($whereCondition,$preYear, $year, $nextYear){
        $sqlUpdate="UPDATE table_tmp AS TT, (SELECT CompanyNo,
               MAX(CASE WHEN AccountNo = '0461' AND NormCateValue = '1'AND YearD = $preYear  THEN I END) AS I1,
               MAX(CASE WHEN AccountNo = '0461' AND NormCateValue = '2'AND YearD = $preYear  THEN I END) AS I2,
               MAX(CASE WHEN AccountNo = '0441' AND NormCateValue = '1'AND YearD = $year  THEN I END) AS I7,
               MAX(CASE WHEN AccountNo = '0721' AND NormCateValue = '1'AND YearD = $year  THEN I END) AS I12,
               MAX(CASE WHEN AccountNo = '0721' AND NormCateValue = '2'AND YearD = $year  THEN I END) AS I13,
               MAX(CASE WHEN AccountNo = '0411' AND NormCateValue = '1'AND YearD = $nextYear  THEN I END) AS I18
        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , NC.CateValue AS NormCateValue, YEAR(A.PeriodFromDate) AS YearD,
                     A.CompanyName as CompanyName, A.CompanyNo CompanyNo, CCL.CompanyID CompanyID
              FROM act_gl_books AS A
                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                       INNER JOIN company AS CCL ON CCL.CompanyID = A.CompanyID
                       INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
              WHERE
                      A.AccountNo IN ('0461', '0441', '0721','0411')
                AND  SC.CateNo = '003' AND SC.CateValue='12'
                AND  NC.CateValue IN('1','2')
                AND  NC.CateNo ='20214'
             $whereCondition
              GROUP BY A.AccountNo, YearD, CompanyName, CompanyNo, CompanyID, NormCateValue
             ) AS B GROUP BY CompanyName
                                ) AS HH
                            SET TT.I1= HH.I1, TT.I2= HH.I2, TT.I7= HH.I7, TT.I12= HH.I12,TT.I13= HH.I13, TT.I18= HH.I18
                            WHERE TT.ParentID = 'A#' AND  TT.CompanyNo = HH.CompanyNo ";
            DB::statement($sqlUpdate);
        }
    public function updateI4_5_6_9_10_11_15_16_17_20_21_22($whereCondition,$preYear, $year, $nextYear){
        $sqlUpdate="UPDATE table_tmp AS TT, (SELECT CompanyNo,
                         MAX(CASE WHEN AccountNo = '0461' AND NormCateValue = '1' AND ExpenseCateValue = '1' AND YearD = $preYear  THEN I END) AS I4,
                         MAX(CASE WHEN AccountNo = '0461' AND NormCateValue = '2' AND ExpenseCateValue = '2' AND YearD = $preYear  THEN I END) AS I5,
                         MAX(CASE WHEN AccountNo = '0461' AND NormCateValue = '3' AND ExpenseCateValue = '3' AND YearD = $preYear  THEN I END) AS I6,
                         MAX(CASE WHEN AccountNo = '0441' AND NormCateValue = '1' AND ExpenseCateValue = '1' AND YearD = $year  THEN I END) AS I9,
                         MAX(CASE WHEN AccountNo = '0441' AND NormCateValue = '2' AND ExpenseCateValue = '2' AND YearD = $year  THEN I END) AS I10,
                         MAX(CASE WHEN AccountNo = '0441' AND NormCateValue = '3' AND ExpenseCateValue = '3' AND YearD = $year  THEN I END) AS I11,
                         MAX(CASE WHEN AccountNo = '0721' AND NormCateValue = '1' AND ExpenseCateValue = '1' AND YearD = $year  THEN I END) AS I15,
                         MAX(CASE WHEN AccountNo = '0721' AND NormCateValue = '2' AND ExpenseCateValue = '2' AND YearD = $year  THEN I END) AS I16,
                         MAX(CASE WHEN AccountNo = '0721' AND NormCateValue = '3' AND ExpenseCateValue = '3' AND YearD = $year  THEN I END) AS I17,
                         MAX(CASE WHEN AccountNo = '0411' AND NormCateValue = '1' AND ExpenseCateValue = '1' AND YearD = $nextYear  THEN I END) AS I20,
                         MAX(CASE WHEN AccountNo = '0411' AND NormCateValue = '2' AND ExpenseCateValue = '2' AND YearD = $nextYear  THEN I END) AS I21,
                         MAX(CASE WHEN AccountNo = '0411' AND NormCateValue = '3' AND ExpenseCateValue = '3' AND YearD = $nextYear  THEN I END) AS I22
                         FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo ,NC.CateValue AS NormCateValue, EC.CateValue AS ExpenseCateValue ,YEAR(A.PeriodFromDate) AS YearD,
                        A.CompanyName as CompanyName, A.CompanyNo CompanyNo, CCL.CompanyID CompanyID
                               FROM act_gl_books AS A
                                        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                        INNER JOIN company AS CCL ON CCL.CompanyID = A.CompanyID
                                        INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                                        INNER JOIN expense_cate EC ON EC.ExpenseID = A.ExpenseID
                               WHERE
                                  A.AccountNo IN ('0461', '0441', '0721','0411')
                                AND  SC.CateNo = '003' AND SC.CateValue='12'
                                AND  EC.CateNo = '20201' AND EC.CateValue IN('1','2','3')
                                AND  NC.CateNo ='2041111'AND NC.CateValue IN('1','2','3')
                                 $whereCondition
                        GROUP BY A.AccountNo, YearD, CompanyName, CompanyNo, CompanyID,NormCateValue,ExpenseCateValue
                            ) AS B GROUP BY CompanyName
                        ) AS HH
                    SET TT.I4= HH.I4,TT.I5= HH.I5,TT.I6= HH.I6,TT.I9= HH.I9,TT.I10= HH.I10,TT.I11= HH.I11,TT.I15= HH.I15,TT.I16= HH.I16,TT.I17= HH.I17,TT.I20= HH.I20,TT.I21= HH.I21,TT.I22= HH.I22
                    WHERE TT.ParentID = 'A#' AND  TT.CompanyNo = HH.CompanyNo ";
        DB::statement($sqlUpdate);
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I3) AS I3,SUM(M.I8) AS I8,SUM(M.I14) AS I14, SUM(M.I19) AS I19
                FROM table_tmp M
                WHERE M.Level = " . $i . " GROUP BY ParentID";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
                        $I3 = ($value['I3']) ? $value['I3'] : 0;
                        $I8 = ($value['I8']) ? $value['I8'] : 0;
                        $I14 = ($value['I14']) ? $value['14'] : 0;
                        $I19 = ($value['I19']) ? $value['I19'] : 0;
                        $update = "UPDATE table_tmp
                    SET I3 = I3 + ".$I3.",I8 = I8 + ".$I8.", I14 = I14 + ".$I14.", I19 = I19 + ".$I19."
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
