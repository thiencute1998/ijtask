<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB15_2Controller extends Controller {
    public function SBP3422016TTBTC_MB15_2(Request $request){
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
        $nextYear = (int)$year + 1;
        $whereCondition = "AND (
        ( YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
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
          `I23` double(20,4) DEFAULT NULL,
          `I24` double(20,4) DEFAULT NULL,
          `I25` double(20,4) DEFAULT NULL,
          `I26` double(20,4) DEFAULT NULL,
          `I27` double(20,4) DEFAULT NULL,
          `I28` double(20,4) DEFAULT NULL,
          `I29` double(20,4) DEFAULT NULL,
          `I30` double(20,4) DEFAULT NULL,
          `I31` double(20,4) DEFAULT NULL,
          `I32` double(20,4) DEFAULT NULL,
          `I33` double(20,4) DEFAULT NULL,
          `I34` double(20,4) DEFAULT NULL,
          `I35` double(20,4) DEFAULT NULL,

          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $this->MB15_2InsertTemplate();
        $this->MB15_2InsertTemplateLV($whereCondition);
        $this->updateI4_5_6_7_17_18_19_20_28_29_30_31($whereCondition, $year, $nextYear);
        $this->updateI8_9_10_11_21_22_23_24_32_33_34_35($whereCondition, $year, $nextYear);
        $this->updateI12_13_14_25($whereCondition, $year, $nextYear);
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
            $data[$key]->I2 = (int)($data[$key]->I3) + (int)($data[$key]->I7);
            $data[$key]->I3 = (int)($data[$key]->I4) + (int)($data[$key]->I5) + (int)($data[$key]->I6);
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
            $data[$key]->I7 = ($data[$key]->I7) ? $data[$key]->I7 : '';
            $data[$key]->I8 = ($data[$key]->I8) ? $data[$key]->I8 : '';
            $data[$key]->I9 = ($data[$key]->I9) ? $data[$key]->I9 : '';
            $data[$key]->I10 = ($data[$key]->I10) ? $data[$key]->I10 : '';
            $data[$key]->I11 = ($data[$key]->I11) ? $data[$key]->I11 : '';
            $data[$key]->I12 = ($data[$key]->I12) ? $data[$key]->I12 : '';
            $data[$key]->I13 = ($data[$key]->I13) ? $data[$key]->I13 : '';
            $data[$key]->I14 = ($data[$key]->I14) ? $data[$key]->I14 : '';
            $data[$key]->I15 = (int)($data[$key]->I16) + (int)($data[$key]->I20);
            $data[$key]->I16 = (int)($data[$key]->I17) + (int)($data[$key]->I18) + (int)($data[$key]->I19);
            $data[$key]->I17 = ($data[$key]->I17) ? $data[$key]->I17 : '';
            $data[$key]->I18 = ($data[$key]->I18) ? $data[$key]->I18 : '';
            $data[$key]->I19 = ($data[$key]->I19) ? $data[$key]->I19 : '';
            $data[$key]->I20 = ($data[$key]->I20) ? $data[$key]->I20 : '';
            $data[$key]->I21 = ($data[$key]->I21) ? $data[$key]->I21 : '';
            $data[$key]->I22 = ($data[$key]->I22) ? $data[$key]->I22 : '';
            $data[$key]->I23 = ($data[$key]->I23) ? $data[$key]->I23 : '';
            $data[$key]->I24 = ($data[$key]->I24) ? $data[$key]->I24 : '';
            $data[$key]->I25 = ($data[$key]->I25) ? $data[$key]->I25 : '';
            $data[$key]->I26 = (int)($data[$key]->I27) + (int)($data[$key]->I31);
            $data[$key]->I27 = (int)($data[$key]->I28) + (int)($data[$key]->I29) + (int)($data[$key]->I30);
            $data[$key]->I28 = ($data[$key]->I28) ? $data[$key]->I28 : '';
            $data[$key]->I29 = ($data[$key]->I29) ? $data[$key]->I29 : '';
            $data[$key]->I30 = ($data[$key]->I30) ? $data[$key]->I30 : '';
            $data[$key]->I31 = ($data[$key]->I31) ? $data[$key]->I31 : '';
            $data[$key]->I32 = ($data[$key]->I32) ? $data[$key]->I32 : '';
            $data[$key]->I33 = ($data[$key]->I33) ? $data[$key]->I33 : '';
            $data[$key]->I34 = ($data[$key]->I34) ? $data[$key]->I34 : '';
            $data[$key]->I35 = ($data[$key]->I35) ? $data[$key]->I35 : '';
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
    public function MB15_2InsertTemplate(){
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
                'I22'=>'',
                'I23'=>'',
                'I24'=>'',
                'I25'=>'',
                'I26'=>'',
                'I27'=>'',
                'I28'=>'',
                'I29'=>'',
                'I30'=>'',
                'I31'=>'',
                'I32'=>'',
                'I33'=>'',
                'I34'=>'',
                'I35'=>'',
            ],

        ];
        DB::table('table_tmp')->insert($arrInsert);
    }
    public function MB15_2InsertTemplateLV($whereCondition)
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
                    'I22'=>'',
                    'I23'=>'',
                    'I24'=>'',
                    'I25'=>'',
                    'I26'=>'',
                    'I27'=>'',
                    'I28'=>'',
                    'I29'=>'',
                    'I30'=>'',
                    'I31'=>'',
                    'I32'=>'',
                    'I33'=>'',
                    'I34'=>'',
                    'I35'=>'',
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
    public function updateI4_5_6_7_17_18_19_20_28_29_30_31($whereCondition, $year, $nextYear){
        $sqlUpdate="UPDATE table_tmp AS TT, (SELECT CompanyNo,
               MAX(CASE WHEN AccountNo = '0441' AND NormCateValue = '1'AND ExpenseCateValue = '1' AND EmployeeCateValue='1' AND YearD = $year  THEN I END) AS I4,
               MAX(CASE WHEN AccountNo = '0441' AND NormCateValue = '2'AND ExpenseCateValue = '2' AND EmployeeCateValue='1' AND YearD = $year  THEN I END) AS I5,
               MAX(CASE WHEN AccountNo = '0441' AND NormCateValue = '3'AND ExpenseCateValue = '3' AND EmployeeCateValue='1' AND YearD = $year  THEN I END) AS I6,
               MAX(CASE WHEN AccountNo = '0441' AND ExpenseCateValue = '1' AND EmployeeCateValue IN ('2','3','4') AND  YearD = $year  THEN I END) AS I7,
               MAX(CASE WHEN AccountNo = '0721' AND NormCateValue = '1'AND ExpenseCateValue = '1' AND EmployeeCateValue='1' AND  YearD = $year  THEN I END) AS I17,
               MAX(CASE WHEN AccountNo = '0721' AND NormCateValue = '2'AND ExpenseCateValue = '2' AND EmployeeCateValue='1' AND  YearD = $year  THEN I END) AS I18,
               MAX(CASE WHEN AccountNo = '0721' AND NormCateValue = '3'AND ExpenseCateValue = '3' AND EmployeeCateValue='1' AND  YearD = $year  THEN I END) AS I19,
               MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateValue = '1' AND EmployeeCateValue IN('2','3','4') AND  YearD = $year  THEN I END) AS I20,
               MAX(CASE WHEN AccountNo = '0411' AND NormCateValue = '1'AND ExpenseCateValue = '1' AND EmployeeCateValue='1' AND  YearD = $nextYear  THEN I END) AS I28,
               MAX(CASE WHEN AccountNo = '0411' AND NormCateValue = '2'AND ExpenseCateValue = '2' AND EmployeeCateValue='1' AND  YearD = $nextYear  THEN I END) AS I29,
               MAX(CASE WHEN AccountNo = '0411' AND NormCateValue = '3'AND ExpenseCateValue = '3' AND EmployeeCateValue='1' AND  YearD = $nextYear  THEN I END) AS I30,
               MAX(CASE WHEN AccountNo = '0411' AND ExpenseCateValue = '1' AND EmployeeCateValue IN ('2','3','4') AND  YearD = $nextYear  THEN I END) AS I31
        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , NC.CateValue AS NormCateValue,EC.CateValue AS ExpenseCateValue,EPC.CateValue AS EmployeeCateValue, YEAR(A.PeriodFromDate) AS YearD,
                     A.CompanyName as CompanyName, A.CompanyNo CompanyNo, CCL.CompanyID CompanyID
              FROM act_gl_books AS A
                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                       INNER JOIN company AS CCL ON CCL.CompanyID = A.CompanyID
                       INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                       INNER JOIN expense_cate EC ON EC.ExpenseID = A.ExpenseID
                       INNER JOIN employee_cate EPC ON EPC.EmployeeID = A.EmployeeID
              WHERE
                      A.AccountNo IN ( '0441', '0721','0411')
                AND  SC.CateNo = '003' AND SC.CateValue='3'
                AND  NC.CateNo ='2041111'
                AND  (EC.CateNo = '20201' AND EC.CateValue IN ('1','2','3') OR (EC.CateNo='2002' AND EC.CateValue='1'))
                AND  EPC.CateNo='001' AND EPC.CateValue IN('1','2','3','4')
             $whereCondition
              GROUP BY A.AccountNo, YearD, CompanyName, CompanyNo, CompanyID, NormCateValue,EmployeeCateValue,ExpenseCateValue
             ) AS B GROUP BY CompanyName
                                ) AS HH
                            SET TT.I4= HH.I4, TT.I5= HH.I5, TT.I6= HH.I6, TT.I7= HH.I7,TT.I17= HH.I17, TT.I18= HH.I18,TT.I19= HH.I19,TT.I20= HH.I20,TT.I28= HH.I28,TT.I29= HH.I29,TT.I30= HH.I30,TT.I31= HH.I31
                            WHERE TT.ParentID = 'A#' AND  TT.CompanyNo = HH.CompanyNo ";
        DB::statement($sqlUpdate);
    }
    public function updateI8_9_10_11_21_22_23_24_32_33_34_35($whereCondition, $year, $nextYear){
        $sqlUpdate="UPDATE table_tmp AS TT, (SELECT CompanyNo,
               MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I8,
               MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I9,
               MAX(CASE WHEN AccountNo = '0441' AND NormCateValue = '1'AND YearD = $year  THEN I END) AS I10,
               MAX(CASE WHEN AccountNo = '0441' AND NormCateValue = '1'AND YearD = $year  THEN I END) AS I11,
               MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I21,
               MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I22,
               MAX(CASE WHEN AccountNo = '0441' AND NormCateValue = '1'AND YearD = $year  THEN I END) AS I23,
               MAX(CASE WHEN AccountNo = '0441' AND NormCateValue = '1'AND YearD = $year  THEN I END) AS I24,
               MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I32,
               MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I33,
               MAX(CASE WHEN AccountNo = '0411' AND NormCateValue = '1' AND  YearD = $nextYear  THEN I END) AS I34,
               MAX(CASE WHEN AccountNo = '0411' AND NormCateValue = '1' AND  YearD = $nextYear  THEN I END) AS I35
        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , NC.CateValue AS NormCateValue,CT.CateValue AS CapitalCateValue, YEAR(A.PeriodFromDate) AS YearD,
                     A.CompanyName as CompanyName, A.CompanyNo CompanyNo, CCL.CompanyID CompanyID
              FROM act_gl_books AS A
                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                       INNER JOIN company AS CCL ON CCL.CompanyID = A.CompanyID
                       INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                       INNER JOIN capital_cate AS CT ON CT.CapitalID = A.CapitalID
              WHERE
                      A.AccountNo IN ( '0441', '0721','0411')
                AND  SC.CateNo = '003' AND SC.CateValue='3'
                AND  NC.CateNo IN ('2011113','2011114','2011112','2011199')
                AND  CT.CateNo IN('2001','200212','200211','200213','200214','200215','200216','200219')
             $whereCondition
              GROUP BY A.AccountNo, YearD, CompanyName, CompanyNo, CompanyID, NormCateValue,CapitalCateValue
             ) AS B GROUP BY CompanyName
                                ) AS HH
                            SET TT.I8= HH.I8, TT.I9= HH.I9, TT.I10= HH.I10, TT.I11= HH.I11,TT.I21= HH.I21, TT.I22= HH.I22,TT.I23= HH.I23,TT.I24= HH.I24,TT.I32= HH.I32,TT.I33= HH.I33,TT.I34= HH.I34,TT.I35= HH.I35
                            WHERE TT.ParentID = 'A#' AND  TT.CompanyNo = HH.CompanyNo";
        DB::statement($sqlUpdate);
    }
    public function updateI12_13_14_25($whereCondition, $year, $nextYear){
        $sqlUpdate="UPDATE table_tmp AS TT, (SELECT CompanyNo,
               MAX(CASE WHEN AccountNo = '0721' AND NormCateValue IN ('1','3','5','6') AND YearD =$year THEN I END) AS I12,
               MAX(CASE WHEN AccountNo = '0721' AND NormCateValue IN ('2','4','5','6') AND YearD = $year  THEN I END) AS I13,
               MAX(CASE WHEN AccountNo = '0721' AND NormCateValue = '2'AND YearD = $year  THEN I END) AS I14,
               MAX(CASE WHEN AccountNo = '0411' AND NormCateValue IN ('1','3','5','6') AND YearD = $nextYear  THEN I END) AS I25
        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , NC.CateValue AS NormCateValue, YEAR(A.PeriodFromDate) AS YearD,
                     A.CompanyName as CompanyName, A.CompanyNo CompanyNo, CCL.CompanyID CompanyID
              FROM act_gl_books AS A
                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                       INNER JOIN company AS CCL ON CCL.CompanyID = A.CompanyID
                       INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
              WHERE
                      A.AccountNo IN ('0721','0411')
                AND  SC.CateNo = '003' AND SC.CateValue='12'
                AND  NC.CateValue IN('1','2','3','4','5','6')
                AND  (NC.CateNo ='20215' AND NC.CateValue IN ('1','3','5','6') OR (NC.CateNo = '20214' AND NC.CateValue IN ('1','2','3','4','5','6')))
             $whereCondition
              GROUP BY A.AccountNo, YearD, CompanyName, CompanyNo, CompanyID, NormCateValue
             ) AS B GROUP BY CompanyName
                                ) AS HH
                            SET TT.I12= HH.I12, TT.I13= HH.I13, TT.I14= HH.I14, TT.I25= HH.I25
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
