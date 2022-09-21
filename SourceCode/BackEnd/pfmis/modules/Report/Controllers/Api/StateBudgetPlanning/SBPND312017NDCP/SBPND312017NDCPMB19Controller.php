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

class SBPND312017NDCPMB19Controller extends Controller {
    public function SBPND312017NDCP_MB19(Request $request){

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

        $userCompany = DB::table('company')->where('CompanyID','=', Auth::user()->CompanyID)->first();
        $whereCommonCondition = ' AND A.InTransTypeID NOT IN (16, 17 ,18) ';
        if($userCompany->ManagementLevel == 2 || $userCompany->ManagementLevel == 3){
            if($userCompany->SumDataType == 1){
                // Tổng hợp đơn vị con
                $whereCommonCondition = " AND C.CompanyNo = '" . $userCompany->CompanyNo ."'";
            }
            else{
                if($userCompany->SumDataType == 2){
                    $whereCommonCondition .= " AND C.ManagementLevel = " . ($userCompany->ManagementLevel + 1);
                }
                else{
                    // Tổng hợp theo ngành
                    $whereCommonCondition .= " C.SectorNo = '" . $userCompany->SectorNo . "' AND C.ManagementLevel = " . $userCompany->ManagementLevel;
                }
            }
            if($userCompany->ManagementLevel == 2){
                $whereCommonCondition .= " AND C.ProvinceID = $userCompany->ProvinceID";
            }
            else{
                $whereCommonCondition .= " AND C.DistrictID = $userCompany->DistrictID";
            }
        }
        $BudgetLevelA='';
        $BudgetLevelB='';

        $yearNext = (int)$year + 1;

        $whereCondition = " AND YEAR(A.PeriodFromDate) = $yearNext $whereCommonCondition ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(20) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
            `Detail` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_312017ndcp_bm19')->get();
        if(in_array($userCompany->SumDataType, [2,3])){
            if($userCompany->ManagementLevel == 2){
                $BudgetLevelA ='2';
                $BudgetLevelB ='3';
            }
            else{
                $BudgetLevelA ='3';
                $BudgetLevelB ='4';
            }
        }
        $this->A_I_1($whereCondition, $year,$BudgetLevelA);
        $this->A_I_2345($whereCondition, $year,$BudgetLevelA);
        $this->A_II_1($whereCondition, $year,$BudgetLevelA);
        $this->A_II_23($whereCondition, $year,$BudgetLevelA);
//        $this->III();
        $this->B_I_1($whereCondition, $year,$BudgetLevelB);
        $this->B_I_2345($whereCondition, $year,$BudgetLevelB);
        $this->B_II_1($whereCondition, $year,$BudgetLevelB);
        $this->B_II_23($whereCondition, $year,$BudgetLevelB);
        // sum up
        $this->sumUpTableTmp(1, 5);

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
                'Detail' => $template->Detail,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
            ];
        }

        DB::table('table_tmp')->insert($insertArr);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Dự toán năm ' . $year;
            $data[0]->HeaderName2 = 'Ước thực hiện năm ' . $year;

        }
        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = (int)($data[$key]->I2) - (int)($data[$key]->I1);
            if($data[$key]->I1 !=0){
                $data[$key]->I4 = round(((int)($data[$key]->I2) *100)/(int)($data[$key]->I1), 2);
            }
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
                "ReportName" => "DỰ TOÁN CHI NGÂN SÁCH CẤP TỈNH (HUYỆN, XÃ) CHO TỪNG CƠ QUAN, TỔ CHỨC THEO LĨNH VỰC NĂM " . $yearNext,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    public function A_I_1($whereCondition, $year,$BudgetLevelA){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            SUM(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                            SUM(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN company AS C ON C.CompanyID = A.CompanyID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0341','0711')
                                AND LEFT(CC.CateNo,4)='2001'
                                AND (LEFT(RC.CateNo,5) ='20111' OR RC.CateNo IN ('20112','20113','20114','20115','20116'))
                                AND A.BudgetLevel = '$BudgetLevelA'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2
                    WHERE TT.ItemID = 'A#01#01'";
        DB::statement($sqlUpdate);
    }
    public function A_I_2345($whereCondition, $year,$BudgetLevelA){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID, RevenueCateNo,
                            SUM(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                            SUM(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD, RC.CateNo as RevenueCateNo,
                                 (CASE when (RC.CateNo= '201210101'  AND RC.CateValue ='1') THEN 'A#01#02#01'
                                         when (RC.CateNo= '201210102'  AND RC.CateValue IN ('1','2')) THEN 'A#01#02#02'
                                         when (RC.CateNo= '20124'  AND RC.CateValue ='1') THEN 'A#01#03'
                                         when (RC.CateNo= '20123'  AND RC.CateValue ='1') THEN 'A#01#04'
                                         when (RC.CateNo= '20122'  AND RC.CateValue ='1') THEN 'A#01#05'END) AS ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN company AS C ON C.CompanyID = A.CompanyID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0341','0711') AND LEFT(CC.CateNo,4)='2001'
                                AND ((RC.CateNo = '201210102' AND RC.CateValue IN ('1','2'))
                               AND A.BudgetLevel = '$BudgetLevelA'
                                    OR (RC.CateNo IN('201210101','20124','20123','20122') AND RC.CateValue ='1'))
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD,RevenueCateNo
                             ) AS  B GROUP BY ItemID, RevenueCateNo
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function A_II_1($whereCondition, $year,$BudgetLevelA){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            SUM(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                            SUM(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN company AS C ON C.CompanyID = A.CompanyID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0441','0721')
                              AND A.BudgetLevel = '$BudgetLevelA'
                                AND LEFT(CC.CateNo,4)='2001'
                                AND (LEFT(EC.CateNo,5) IN('20151','20152') OR EC.CateNo ='20153')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE TT.ItemID = 'A#01#01'";
        DB::statement($sqlUpdate);
    }
    public function A_II_23($whereCondition,$year,$BudgetLevelA){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID, ExpenseCateNo,
                               SUM(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                               SUM(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD, EC.CateNo as ExpenseCateNo,
                                     (CASE when (EC.CateNo= '201610101 '  AND EC.CateValue ='1') THEN 'A#02#02#01'
                                           when (EC.CateNo= '201610101'  AND EC.CateValue = '2') THEN 'A#02#02#02'
                                           when (EC.CateNo= '2016103'  AND EC.CateValue ='1') THEN 'A#02#03' END) AS ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN company AS C ON C.CompanyID = A.CompanyID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0441','0721') AND LEFT(CC.CateNo,4)='2001'
                                    AND A.BudgetLevel = '$BudgetLevelA'
                                AND ((EC.CateNo = '201610101' AND EC.CateValue ='2')
                                  OR (EC.CateNo IN('201610101','2016103') AND EC.CateValue ='1'))
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD,ExpenseCateNo
                             ) AS  B GROUP BY ItemID, ExpenseCateNo
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }

    public function B_I_1($whereCondition, $year,$BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            SUM(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                            SUM(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN company AS C ON C.CompanyID = A.CompanyID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0341','0711')
                                AND LEFT(CC.CateNo,4)='2001'
                                AND (LEFT(RC.CateNo,5) ='20111' OR RC.CateNo IN ('20112','20113','20114','20115','20116'))
                                AND A.BudgetLevel = '$BudgetLevelB'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2
                    WHERE TT.ItemID = 'B#01#01'";
        DB::statement($sqlUpdate);
    }
    public function B_I_2345($whereCondition, $year,$BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID, RevenueCateNo,
                            SUM(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                            SUM(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD, RC.CateNo as RevenueCateNo,
                                 (CASE when (RC.CateNo= '201210101'  AND RC.CateValue ='1') THEN 'B#01#02#01'
                                         when (RC.CateNo= '201210102'  AND RC.CateValue IN ('1','2')) THEN 'B#01#02#02'
                                         when (RC.CateNo= '20124'  AND RC.CateValue ='1') THEN 'B#01#03'
                                         when (RC.CateNo= '20123'  AND RC.CateValue ='1') THEN 'B#01#04'
                                         when (RC.CateNo= '20122'  AND RC.CateValue ='1') THEN 'B#01#05'END) AS ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN company AS C ON C.CompanyID = A.CompanyID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0341','0711') AND LEFT(CC.CateNo,4)='2001'
                                AND ((RC.CateNo = '201210102' AND RC.CateValue IN ('1','2'))
                               AND A.BudgetLevel = '$BudgetLevelB'
                                    OR (RC.CateNo IN('201210101','20124','20123','20122') AND RC.CateValue ='1'))
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD,RevenueCateNo
                             ) AS  B GROUP BY ItemID, RevenueCateNo
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function B_II_1($whereCondition, $year,$BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            SUM(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                            SUM(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN company AS C ON C.CompanyID = A.CompanyID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0441','0721')
                              AND A.BudgetLevel = '$BudgetLevelB'
                                AND LEFT(CC.CateNo,4)='2001'
                                AND (LEFT(EC.CateNo,5) IN('20151','20152') OR EC.CateNo ='20153')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE TT.ItemID = 'B#01#01'";
        DB::statement($sqlUpdate);
    }
    public function B_II_23($whereCondition,$year,$BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID, ExpenseCateNo,
                               SUM(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                               SUM(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD, EC.CateNo as ExpenseCateNo,
                                     (CASE when (EC.CateNo= '201610101 '  AND EC.CateValue ='1') THEN 'B#02#02#01'
                                           when (EC.CateNo= '201610101'  AND EC.CateValue = '2') THEN 'B#02#02#02'
                                           when (EC.CateNo= '2016103'  AND EC.CateValue ='1') THEN 'B#02#03' END) AS ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN company AS C ON C.CompanyID = A.CompanyID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0441','0721') AND LEFT(CC.CateNo,4)='2001'
                                    AND A.BudgetLevel = '$BudgetLevelB'
                                AND ((EC.CateNo = '201610101' AND EC.CateValue ='2')
                                  OR (EC.CateNo IN('201610101','2016103') AND EC.CateValue ='1'))
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD,ExpenseCateNo
                             ) AS  B GROUP BY ItemID, ExpenseCateNo
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
//    public function III(){
//        $itemA = DB::table('table_tmp')->where('ItemID', '=', 'A#02')->first();
//        $itemB = DB::table('table_tmp')->where('ItemID', '=', 'A#01')->first();
//        DB::table('table_tmp')->where('ItemID','=','A#03')
//            ->update([
//                'I1'=> $itemA->I1-$itemB->I1,
//                'I2'=> $itemA->I2-$itemB->I2
//            ]);
//    }
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
