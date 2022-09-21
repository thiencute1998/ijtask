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

class SBPND312017NDCPMB15Controller extends Controller {
    public function SBPND312017NDCP_MB15(Request $request){

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
        ) $whereCompany AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(20) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `SbiCategoryNo` varchar(20) DEFAULT '',
          `ConAccountNo` varchar(20) DEFAULT '',
          `ConCoAccountNo` varchar(20) DEFAULT '',
          `SbiCategoryValue` varchar(20) DEFAULT '',
          `ExpenseCateValue` varchar (20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          `I5` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_312017ndcp_bm15')->get();

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
                'SbiCategoryNo' => $template->SbiCategoryNo,
                'ConAccountNo' => $template->ConAccountNo,
                'ConCoAccountNo' => $template->ConCoAccountNo,
                'SbiCategoryValue' => $template->SbiCategoryValue,
                'ExpenseCateValue' => $template->ExpenseCateValue,
                'NormCateValue' => $template->NormCateValue,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
                'I5' => $template->I5,
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->AI($whereCondition, $yearNext, $year);
        $this->A_II_III_IV_V($whereCondition, $yearNext, $year);
        $this->B_I_III($whereCondition, $yearNext, $year);
        $this->B_II($whereCondition, $yearNext, $year);
        $this->C();
        $this->D($whereCondition, $yearNext, $year);
        $this->E($whereCondition, $yearNext, $year);
        // sum up
        $this->sumUpTableTmp(1, 5);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = (int)($data[$key]->I3) - (int)($data[$key]->I2);
            if($data[$key]->I2 !=0){
                $data[$key]->I5 = round(((int)($data[$key]->I3) *100)/(int)($data[$key]->I2), 2);
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
                "ReportName" => "CÂN ĐỐI NGÂN SÁCH ĐỊA PHƯƠNG NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    public function AI($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,RevenueCateNo,
                               MAX(CASE WHEN  AccountNo = '0341'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,RC.CateNo as RevenueCateNo,YEAR(A.PostDate)AS YearD,
                                     (Case WHEN  RC.CateNo = '202110201' THEN 'A#01#01'
                                           WHEN  RC.CateNo = '202110202' THEN 'A#01#02'  END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              WHERE A.AccountNo IN ('0341', '0711', '0311')
                                AND RC.CateNo IN ('202110201','202110202')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, ItemID,RevenueCateNo
                             ) AS  B GROUP BY  ItemID,RevenueCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3
                        WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function A_II_III_IV_V($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,RevenueCateNo,
                               MAX(CASE WHEN  AccountNo = '0341'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,RC.CateNo as RevenueCateNo,YEAR(A.PostDate)AS YearD,
                                     (Case WHEN  RC.CateNo = '201210101' THEN 'A#02#01'
                                           WHEN  RC.CateNo = '201210102' THEN 'A#02#02'
                                           WHEN  RC.CateNo = '20124' THEN 'A#03'
                                           WHEN  RC.CateNo = '20123' THEN 'A#04'
                                           WHEN  RC.CateNo = '20122' THEN 'A#05'  END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN revenue AS R ON R.RevenueID = R.RevenueID
                              WHERE A.AccountNo IN ('0341', '0711', '0311')
                                AND (RC.CateNo IN ('20124','20123','201210101','20122') AND RC.CateValue ='1'
                                OR (RC.CateNo ='201210102' AND RC.CateValue IN ('1','2')))
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, ItemID,RevenueCateNo
                             ) AS  B GROUP BY  ItemID,RevenueCateNo
            ) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3
            WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function B_I_III($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,ExpenseCateNo,
                               MAX(CASE WHEN  AccountNo = '0441'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,EC.CateNo as ExpenseCateNo, YEAR(A.PostDate)AS YearD,
                                     (Case WHEN  EC.CateNo = '20152' THEN 'B#01#01'
                                           WHEN  EC.CateNo = '20151' THEN 'B#01#02'
                                           WHEN  EC.CateNo = '20153' THEN 'B#01#03'
                                           WHEN  EC.CateNo = '20154' THEN 'B#01#04'
                                           WHEN  EC.CateNo = '20159' THEN 'B#01#05'
                                           WHEN  EC.CateNo = '20164' THEN 'B#01#06'
                                           WHEN  EC.CateNo = '20161' THEN 'B#03'END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0441', '0721', '0411')
                                 AND((EC.CateNo = '20152' AND CC.CateNo = '20012102')
                                  OR (EC.CateNo = '20151' AND CC.CateNo IN ('20011101','20012101'))
                                  OR (EC.CateNo = '20153' AND CC.CateNo = '200193')
                                  OR EC.CateNo = '20154'  OR EC.CateNo = '20159' OR EC.CateNo = '20161'
                                  OR (EC.CateNo = '20164' AND CC.CateNo = '200194')
                                  )
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, ItemID, ExpenseCateNo
                             ) AS  B GROUP BY  ItemID, ExpenseCateNo
                            ) AS HH
                            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3
                            WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function B_II($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,ProjectCateValue,
                               MAX(CASE WHEN  AccountNo = '0441'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,PC.CateValue as ProjectCateValue, YEAR(A.PostDate)AS YearD,
                                     (Case WHEN  PC.CateValue = '1' THEN 'B#02#01'
                                           WHEN  PC.CateValue = '2' THEN 'B#02#02'
                                         END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN program_cate as PC ON PC.ProgramID = A.ProgramID
                              WHERE A.AccountNo IN ('0441', '0721', '0411')
                                AND PC.CateNo ='001' AND PC.CateValue IN ('1','2')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, ItemID, ProjectCateValue
                             ) AS  B GROUP BY  ItemID, ProjectCateValue
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function C(){
        $itemA = DB::table('table_tmp')->where('ItemID', '=', 'A')->first();
        $itemB = DB::table('table_tmp')->where('ItemID', '=', 'B')->first();
        DB::table('table_tmp')->where('ItemID','=','C')
            ->update([
                'I1'=> $itemA->I1-$itemB->I1,
                'I2'=> $itemA->I1-$itemB->I1,
                'I3'=> $itemA->I1-$itemB->I1,
            ]);
    }
    public function D($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID, CapitalCateValue,
                               MAX(CASE WHEN  AccountNo = '0441'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,CC.CateValue as CapitalCateValue, YEAR(A.PostDate)AS YearD,
                                     (Case WHEN  CC.CateValue = '1' THEN 'D#01'
                                           WHEN  CC.CateValue IN ('2','3','4','5') THEN 'D#02' END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0441', '0721', '0411')
                                AND EC.CateNo = '2016202' AND CC.CateNo ='200193'
                                AND CC.CateValue IN ('1','2','3','4','5')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, ItemID,CapitalCateValue
                             ) AS  B GROUP BY  ItemID,CapitalCateValue
                            ) AS HH
                            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3
                            WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function E($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,CapitalCateValue,
                               MAX(CASE WHEN  AccountNo = '0341'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,CC.CateValue as CapitalCateValue,YEAR(A.PostDate)AS YearD,
                                     (Case WHEN  CC.CateValue = '1' THEN 'E#01'
                                           WHEN  CC.CateValue = '2' THEN 'E#02' END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0341', '0711', '0311')
                                AND RC.CateNo = '2012501' AND CC.CateNo ='200192'
                                AND CC.CateValue IN ('1','2')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, ItemID,CapitalCateValue
                             ) AS  B GROUP BY  ItemID, CapitalCateValue
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3
                        WHERE TT.ItemID = HH.ItemID";
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
