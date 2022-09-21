<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class SBP3422016TTBTCMB29_2Controller extends Controller {
    public function SBP3422016TTBTC_MB29_2(Request $request){

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
        (YEAR(A.PostDate) = $year AND YEAR(A.PostDate) = $year)
        OR (YEAR(A.PostDate) = $yearPre AND YEAR(A.PostDate) = $yearPre)
        OR (YEAR(A.PostDate) = $yearNext AND YEAR(A.PostDate) = $yearNext)
        ) $whereCompany";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `AccountNoI1` varchar(20) DEFAULT '',
          `AccountNoI2` varchar(20) DEFAULT '',
          `AccountNoI3` varchar(20) DEFAULT '',
          `AccountNoI4` varchar(20) DEFAULT '',
          `NormCateNo` varchar (20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
          `RevenueCateNo` varchar(20) DEFAULT '',
          `RevenueCateValue` varchar(20) DEFAULT '',
          `CapitalCateNo` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
           `ExpenseCateNo` varchar(25) DEFAULT NULL,
           `ProgramCateValue` varchar(25) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb2902')->get();

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
                'Center' => $template->Center,
                'AccountNoI1' => $template->AccountNoI1,
                'AccountNoI2' => $template->AccountNoI2,
                'AccountNoI3' => $template->AccountNoI3,
                'AccountNoI4' => $template->AccountNoI4,
                'NormCateValue' => $template->NormCateValue,
                'NormCateNo' => $template->NormCateValue,
                'RevenueCateNo' => $template->RevenueCateNo,
                'RevenueCateValue' => $template->RevenueCateValue,
                'CapitalCateNo' => $template->CapitalCateNo,
                'ExpenseCateNo' => $template->ExpenseCateNo,
                'ProgramCateValue' => $template->ProgramCateValue,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->A($whereCondition, $yearNext, $year, $yearPre);
        $this->A32($whereCondition, $yearNext, $year, $yearPre);
        $this->BII1_BIII_BIV_BV($whereCondition, $yearNext, $year, $yearPre);
        $this->BII2($whereCondition, $yearNext, $year, $yearPre);
        $this->CI1($whereCondition, $yearNext, $year, $yearPre);
        $this->CI23($whereCondition, $yearNext, $year, $yearPre);
        $this->CI45($whereCondition, $yearNext, $year, $yearPre);
        $this->CI6($whereCondition, $yearNext, $year, $yearPre);
        $this->CII($whereCondition, $yearNext, $year, $yearPre);
        $this->CIII($whereCondition, $yearNext, $year, $yearPre);
        $this->E($whereCondition, $yearNext, $year, $yearPre);
        $this->F($whereCondition, $yearNext, $year, $yearPre);
        // sum up
//        $this->sumUpTableTmp(1, 5);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $yearPre;
            $data[0]->HeaderName2 = 'Năm ' . $year;
            $data[0]->HeaderName3 = 'Dự toán năm ' .$yearNext;
        }

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
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
                "ReportName" => "DỰ TOÁN THU, CHI NGÂN SÁCH NHÀ NƯỚC NĂM " . $yearNext,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    // ===================================== A =====================================
    public function A($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT RevenueCateNo,
                               MAX(CASE WHEN  AccountNo = '0361'  AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,RC.CateNo as RevenueCateNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
                               $whereCondition

                              GROUP BY A.AccountNo, YearD, RevenueCateNo
                             ) AS  B GROUP BY RevenueCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID IN ('A','A#03','B#01') AND LEFT(HH.RevenueCateNo,5) = TT.RevenueCateNo";
        DB::statement($sqlUpdate);
    }
    public function A32($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                               MAX(CASE WHEN  AccountNo = '0361'  AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,A.ExpenseNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                              WHERE A.AccountNo IN ('0361', '0341', '0711', '0311') AND A.ExpenseNo IN ('51060101','510699','51060201')
                               $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID ='A#03#02'";
        DB::statement($sqlUpdate);
    }
    public function BII1_BIII_BIV_BV($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT RevenueCateNo,
                               MAX(CASE WHEN  AccountNo = '0361'  AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,RC.CateNo as RevenueCateNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
                               AND RC.CateValue='1'
                               $whereCondition
                              GROUP BY A.AccountNo, YearD, RevenueCateNo
                             ) AS  B GROUP BY RevenueCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.RevenueCateNo = HH.RevenueCateNo AND TT.ParentID IN ('B#02','B')";
        DB::statement($sqlUpdate);
    }
    public function BII2($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT RevenueCateNo,
                               MAX(CASE WHEN  AccountNo = '0361'  AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,RC.CateNo as RevenueCateNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
                               AND RC.CateValue IN ('1','2')
                               $whereCondition
                              GROUP BY A.AccountNo, YearD, RevenueCateNo
                             ) AS  B GROUP BY RevenueCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.RevenueCateNo = HH.RevenueCateNo AND TT.ParentID = 'B#02'";
        DB::statement($sqlUpdate);
    }
    public function CI1($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ExpenseCateNo,
                               MAX(CASE WHEN  AccountNo = '0461'  AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,EC.CateNo as ExpenseCateNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                               AND EC.CateNo IN('20152','2016202')
                               $whereCondition
                              GROUP BY A.AccountNo, YearD, ExpenseCateNo
                             ) AS  B GROUP BY ExpenseCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID = 'C#01#01'";
        DB::statement($sqlUpdate);
    }
    public function CI23($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ExpenseCateNo,
                               MAX(CASE WHEN  AccountNo = '0461'  AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,EC.CateNo as ExpenseCateNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                               $whereCondition
                              GROUP BY A.AccountNo, YearD, ExpenseCateNo
                             ) AS  B GROUP BY ExpenseCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ParentID = 'C#01' AND LEFT(HH.ExpenseCateNo,5) = TT.ExpenseCateNo";
        DB::statement($sqlUpdate);
    }
    public function CI45($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ExpenseCateNo,
                               MAX(CASE WHEN  AccountNo = '0461'  AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,EC.CateNo as ExpenseCateNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                               AND EC.CateValue='1'
                               $whereCondition
                              GROUP BY A.AccountNo, YearD, ExpenseCateNo
                             ) AS  B GROUP BY ExpenseCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ExpenseCateNo = HH.ExpenseCateNo AND TT.ParentID = 'C#01'";
        DB::statement($sqlUpdate);
    }
    public function CI6($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                               MAX(CASE WHEN  AccountNo = '0461'  AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND A.CapitalNo IN ('194101','194201','194501')
                               $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID = 'C#01#06'";
        DB::statement($sqlUpdate);
    }
    public function CII($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ProgramCateValue,
                               MAX(CASE WHEN  AccountNo = '0461'  AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,EC.CateNo as ExpenseCateNo,PC.CateValue as ProgramCateValue, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN program_cate AS PC ON PC.ProgramID = A.ProgramID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND EC.CateNo ='201610101' AND EC.CateVaLue='2' AND PC.CateValue IN ('1','2','5')
                               $whereCondition
                              GROUP BY A.AccountNo, YearD, ExpenseCateNo,ProgramCateValue
                             ) AS  B GROUP BY ProgramCateValue
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ProgramCateValue = HH.ProgramCateValue AND TT.ParentID = 'C'";
        DB::statement($sqlUpdate);
    }
    public function CIII($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                               MAX(CASE WHEN  AccountNo = '0461'  AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,EC.CateNo as ExpenseCateNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND EC.CateNo ='2016103' AND EC.CateVaLue='1'
                               $whereCondition
                              GROUP BY A.AccountNo, YearD, ExpenseCateNo
                             ) AS  B GROUP BY ExpenseCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE  TT.ItemID = 'C#03'";
        DB::statement($sqlUpdate);
    }
    public function E($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT RevenueCateNo,
                               MAX(CASE WHEN  AccountNo = '0361'  AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,RC.CateNo as RevenueCateNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND RC.CateNo ='2000301' AND RC.CateVaLue IN ('1','2') AND A.CapitalNo='192101'
                               $whereCondition
                              GROUP BY A.AccountNo, YearD, RevenueCateNo
                             ) AS  B GROUP BY RevenueCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE  TT.ItemID = 'E'";
        DB::statement($sqlUpdate);
    }
    public function F($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                       MAX(CASE WHEN  AccountNo = '0361'            AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,EC.CateNo as ExpenseCateNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND EC.CateNo ='2016202' AND A.CapitalNo='193201'
                               $whereCondition
                              GROUP BY A.AccountNo, YearD, ExpenseCateNo
                             ) AS  B GROUP BY ExpenseCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE  TT.ItemID = 'F'";
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
                FROM table_tmp M
                WHERE M.Level = " . $i . " GROUP BY ParentID";
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
                    SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
