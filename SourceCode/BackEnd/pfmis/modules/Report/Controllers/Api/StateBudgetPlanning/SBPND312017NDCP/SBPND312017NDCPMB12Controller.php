<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBPND312017NDCP;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class SBPND312017NDCPMB12Controller extends Controller {
    public function SBPND312017NDCP_MB12(Request $request){

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
        $next2Year = (int)$year + 2;
        $whereCondition = "AND (
        ( YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $preYear AND YEAR(A.PeriodToDate) = $preYear)
        OR (YEAR(A.PeriodFromDate) = $nextYear AND YEAR(A.PeriodToDate) = $nextYear)
        ) $whereCompany  AND A.InTransTypeID NOT IN (16,17,18) ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(20) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `ProgramCateValue` varchar(20) DEFAULT '',
          `ConAccountNo` varchar(20) DEFAULT '',
          `ConCoAccountNo` varchar(20) DEFAULT '',
          `RevenueCateValue` varchar(20) DEFAULT '',
          `ExpenseCateValue` varchar (20) DEFAULT '',
          `CapitalCateValue` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
            $templateTable = DB::table('report_sbp_312017ndcp_bm12')->get();

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
                'ProgramCateValue' => $template->ProgramCateValue,
                'ConAccountNo' => $template->ConAccountNo,
                'ConCoAccountNo' => $template->ConCoAccountNo,
                'RevenueCateValue' => $template->RevenueCateValue,
                'ExpenseCateValue' => $template->ExpenseCateValue,
                'CapitalCateValue' => $template->CapitalCateValue,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->updateRevenueI1_2($whereCondition,$year);
        $this->updateExpenseI1_2($whereCondition,$year);
        $this->updateBCapitalExpenseI1_2($whereCondition,$year);
        $this->updateDCapitalExpenseI1_2($whereCondition,$year);
        $this->updateCapitalRevenueI1_2($whereCondition,$year);
        $this->updateProgramRevenueI1_2($whereCondition,$year);

        // sum up
        $this->sumUpTableTmp(1, 5);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Dự toán năm ' . $preYear;
            $data[0]->HeaderName2 = 'Ước thực hiện năm ' .$year;
        }
        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = (int)($data[$key]->I2) - (int)($data[$key]->I1);
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            if($data[$key]->I1 !=0){
                $data[$key]->I4 = round(((int)($data[$key]->I2) / (int)($data[$key]->I1))*100, 2);
            }

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
                "ReportName" => "ĐÁNH GIÁ CÂN ĐỐI NGÂN SÁCH ĐỊA PHƯƠNG NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    public function updateRevenueI1_2($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, RevenueCateValue,
              MAX(CASE WHEN AccountNo = '0341' AND YearD = $year - 1  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0711' AND YearD = $year   THEN I END) AS I2  FROM
            (SELECT  (Case when RC.CateNo = '202110201' THEN 'A#01#01'
                           when RC.CateNo = '202110202' THEN 'A#01#02'
                           when RC.CateNo = '201210101' AND  RC.CateValue ='1'  THEN 'A#02#01'
                           when RC.CateNo = '201210102' AND RC.CateValue IN('1','2') THEN  'A#02#02'
                           when RC.CateNo = '20124' AND  RC.CateValue ='1' THEN 'A#03'
                           when RC.CateNo = '20123' AND  RC.CateValue ='1' THEN 'A#04'
                           when RC.CateNo= '20122'  AND  RC.CateValue ='1' THEN 'A#05'
                             END) as ItemID,
             RC.CateValue as RevenueCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
            WHERE (RC.CateNo IN ('202110201','202110202','201210101','201210102','20124','20123','20122') OR RC.CateValue IN ('1','2') )
            AND A.AccountNo IN('0341','0711')
            $whereCondition
            GROUP BY  RC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY RevenueCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateExpenseI1_2($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, ExpenseCateValue,
              MAX(CASE WHEN AccountNo = '0461' AND YearD = $year - 1  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0721' AND YearD = $year   THEN I END) AS I2  FROM
            (SELECT  (Case when LEFT(EC.CateNo,5) = '20152' THEN 'B#01#01'
                           when LEFT(EC.CateNo,5)= '20151' THEN 'B#01#02'
                           when EC.CateNo = '20153' AND EC.CateValue IN('1','2')   THEN 'B#01#03'
                           when EC.CateNo = '20154' AND EC.CateValue ='1' THEN  'B#01#04'
                           when EC.CateNo = '20159' AND  EC.CateValue ='1' THEN 'B#01#05'
                           when LEFT(EC.CateNo,5) THEN 'B#03'
                             END) as ItemID,
             EC.CateValue as ExpenseCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            WHERE (EC.CateNo IN ('20153','20154','20159') OR EC.CateValue IN ('1','2') or LEFT(EC.CateNo,5) in('20152','20151'))
            AND A.AccountNo IN('0461','0721')
            $whereCondition
            GROUP BY  EC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY ExpenseCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateBCapitalExpenseI1_2($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, CapitalCateValue,
              MAX(CASE WHEN AccountNo = '0461' AND YearD = $year - 1  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0721' AND YearD = $year   THEN I END) AS I2  FROM
            (SELECT  (Case when EC.CateNo = '20164' and EC.CateValue = '1'  and CC.CateNo='200194'  THEN 'B#01#06'
                             END) as ItemID,
             CC.CateValue as CapitalCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
             WHERE EC.CateNo ='20164' AND EC.CateValue ='1' AND  CC.CateNo='200194'
            AND A.AccountNo IN('0461','0721')
            $whereCondition
            GROUP BY  CC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY CapitalCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateDCapitalExpenseI1_2($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, CapitalCateValue,
              MAX(CASE WHEN  YearD = $year - 1  THEN I END) AS I1,
              MAX(CASE WHEN YearD = $year   THEN I END) AS I2  FROM
            (SELECT  (Case when  CC.CateValue = '1' THEN 'D#01'
                         when  CC.CateValue IN('2','3','4','5') THEN 'D#02'
                             END) as ItemID,
             CC.CateValue as CapitalCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
             WHERE EC.CateNo ='2016202'  AND  CC.CateNo='200193' AND CC.CateValue in ('1','2','3','4','5')
            AND A.AccountNo = '0461'
            $whereCondition
            GROUP BY  CC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY CapitalCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateCapitalRevenueI1_2($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, CapitalCateValue,
              MAX(CASE WHEN AccountNo = '0361' AND YearD = $year - 1  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0711' AND YearD = $year   THEN I END) AS I2  FROM
            (SELECT  (Case when CC.CateValue ='1' THEN  'E#01'
                           when CC.CateValue = '2' THEN 'E#02'
                             END) as ItemID,
             CC.CateValue as CapitalCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            WHERE RC.CateNo='2000301' AND RC.CateValue in('1','2') AND CC.CateNo = '200192' and CC.CateValue in('1','2')
            AND A.AccountNo IN('0361','0711')
            $whereCondition
            GROUP BY  CC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY CapitalCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateProgramRevenueI1_2($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, ProgramCateValue,
              MAX(CASE WHEN AccountNo = '0461' AND YearD = $year - 1  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0721' AND YearD = $year   THEN I END) AS I2  FROM
            (SELECT  (Case when PC.CateValue ='1' THEN  'B#02#01'
                           when PC.CateValue = '2' THEN 'B#02#02'
                             END) as ItemID,
             PC.CateValue as ProgramCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo
            FROM act_gl_books AS A
            INNER JOIN program_cate as PC on PC.ProgramID = A.ProgramID
            WHERE PC.CateNo='001' AND PC.CateValue in('1','2')
            AND A.AccountNo IN('0461','0721')
            $whereCondition
            GROUP BY  PC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY ProgramCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE  TT.ItemID = HH.ItemID";
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
