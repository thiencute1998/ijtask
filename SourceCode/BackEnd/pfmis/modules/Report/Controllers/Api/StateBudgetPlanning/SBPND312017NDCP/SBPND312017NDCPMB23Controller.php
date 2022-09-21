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

class SBPND312017NDCPMB23Controller extends Controller {
    public function SBPND312017NDCP_MB23(Request $request){

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
          `STT` varchar(20) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
            `Project` varchar(50) DEFAULT '',
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
        $templateTable = DB::table('report_sbp_312017ndcp_bm17')->get();

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
                'Project' => $template->Project,
                'Detail' => $template->Detail,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->A_C($whereCondition, $year);
        $this->B_I_1a($whereCondition, $year);
        $this->B_I_1b($whereCondition, $year);
        $this->B_I_2_3($whereCondition, $year);
        $this->B_II1($whereCondition, $year);
        $this->B_II2($whereCondition, $year);
        $this->B_III_IV_V_VI($whereCondition, $year);
        // sum up
        $this->sumUpTableTmp(1, 5);

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
    public function A_C($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,ExpenseCateNo,
                               MAX(CASE WHEN  AccountNo = '0341'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo , EC.CateNo as ExpenseCateNo,YEAR(A.PostDate)AS YearD,
                            (CASE WHEN EC.CateNo ='201610101' THEN 'A#01'
                                  WHEN LEFT(EC.CateNo,5)='20161' THEN 'A#03' END) AS ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN  ('0341','0721')
                                AND (EC.CateNo='201610101' OR LEFT(EC.CateNo,5)='20161')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD,ExpenseCateNo
                             ) AS  B GROUP BY  ItemID,ExpenseCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function B_I_1a($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT SectorCateNo,ItemID,
                               MAX(CASE WHEN  AccountNo = '0341'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,SC.CateNo as SectorCateNo,YEAR(A.PostDate)AS YearD,
                                    (CASE WHEN LEFT(SC.CateNo,7)='2000303' THEN 'A#02#01#01#01'
                                          WHEN  LEFT(SC.CateNo,7)='2000305' THEN 'A#02#01#01#05'
                                          WHEN SC.CateNo='2000305' THEN 'A#02#01#01#06'
                                          WHEN LEFT(SC.CateNo,7)='2000308' THEN 'A#02#01#01#07'
                                          WHEN LEFT(SC.CateNo,7)='2000307' THEN 'A#02#01#01#10' END) AS ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                                       INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                              WHERE A.AccountNo IN  ('0341','0721')
                                AND LEFT(EC.CateNo,7) ='2015201'
                                AND (CC.CateNo = '20012102' OR CC.CateNo IN('20012201','20012202','20012203','20012204') AND CC.CateValue ='1')
                                AND (SC.CateNo ='2000305' OR LEFT(SC.CateNo,7) IN ('2000303','2000305','2000308','2000307'))
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, SectorCateNo
                             ) AS  B Group by SectorCateNo,ItemID
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }

    public function B_I_1b($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT SectorCateNo,ItemID,
                               MAX(CASE WHEN  AccountNo = '0341'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,SC.CateNo as SectorCateNo,YEAR(A.PostDate)AS YearD,
                                    (CASE WHEN SC.CateNo='2000304' THEN 'A#02#01#01#02'
                                          WHEN SC.CateNo='2000301' THEN 'A#02#01#01#03'
                                          WHEN SC.CateNo='2000302' THEN 'A#02#01#01#04'
                                          WHEN SC.CateNo='2000309' THEN 'A#02#01#01#08'
                                          WHEN SC.CateNo='2000311' THEN 'A#02#01#01#09'
                                          WHEN SC.CateNo='2000312' THEN 'A#02#01#01#11'
                                          WHEN SC.CateNo='2000313' THEN 'A#02#01#01#12'
                                          WHEN SC.CateNo='2000314' THEN 'A#02#01#01#13'
                                 END) AS ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                                       INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                              WHERE A.AccountNo IN  ('0341','0721')
                                AND LEFT(EC.CateNo,7) ='2015201'
                                AND (CC.CateNo = '20012102' OR CC.CateNo IN('20012201','20012202','20012203','20012204') AND CC.CateValue ='1')
                                AND SC.CateNo in ('2000304','2000301','2000302','2000309','2000311','2000312','2000313','2000314') AND SC.CateValue ='1'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, SectorCateNo
                             ) AS  B Group by SectorCateNo,ItemID
        ) AS HH
        SET TT.I1 = HH.I1, TT.I2 = HH.I2
        WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }


    public function B_I_2_3($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,ExpenseCateNo,
                               MAX(CASE WHEN  AccountNo = '0341'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo , EC.CateNo as ExpenseCateNo,YEAR(A.PostDate)AS YearD,
                                     (CASE WHEN EC.CateNo ='2015202' THEN 'A#02#02'
                                           WHEN EC.CateNo ='2015203' THEN 'A#02#03' END) AS ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN  ('0341','0721')
                                AND EC.CateNo IN ('2015202','2015203')
                                AND (CC.CateNo ='20012102' OR (CC.CateNo IN ('20012201','20012202','20012203','20012204') AND CC.CateValue ='1'))
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD,ExpenseCateNo
                             ) AS  B GROUP BY  ItemID,ExpenseCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function B_II1($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT SectorCateNo,ItemID,
                               MAX(CASE WHEN  AccountNo = '0341'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,SC.CateNo as SectorCateNo,YEAR(A.PostDate)AS YearD,
                                     (CASE WHEN LEFT(SC.CateNo,7)='2000303' THEN 'A#02#02#01#01'
                                           WHEN  LEFT(SC.CateNo,7)='2000305' THEN 'A#02#02#01#05'
                                           WHEN SC.CateNo='2000305' THEN 'A#02#02#01#06'
                                           WHEN LEFT(SC.CateNo,7)='2000308' THEN 'A#02#02#01#07'
                                           WHEN LEFT(SC.CateNo,7)='2000307' THEN 'A#02#02#01#10' END) AS ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                                       INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                              WHERE A.AccountNo IN  ('0341','0721')
                                AND LEFT(EC.CateNo,5) ='20151'
                                AND (CC.CateNo = '20012101' OR LEFT(CC.CateNo,5) ='20011')
                                AND (SC.CateNo ='2000305' OR LEFT(SC.CateNo,7) IN ('2000303','2000305','2000308','2000307'))
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, SectorCateNo
                             ) AS  B Group by SectorCateNo,ItemID
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function B_II2($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT SectorCateNo,ItemID,
                               MAX(CASE WHEN  AccountNo = '0341'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,SC.CateNo as SectorCateNo,YEAR(A.PostDate)AS YearD,
                                     (CASE WHEN SC.CateNo='2000304' THEN 'A#02#02#02'
                                           WHEN SC.CateNo='2000301' THEN 'A#02#02#03'
                                           WHEN SC.CateNo='2000302' THEN 'A#02#02#04'
                                           WHEN SC.CateNo='2000309' THEN 'A#02#02#08'
                                           WHEN SC.CateNo='2000311' THEN 'A#02#02#09'
                                           WHEN SC.CateNo='2000312' THEN 'A#02#02#11'
                                           WHEN SC.CateNo='2000313' THEN 'A#02#02#12'
                                           WHEN SC.CateNo='2000314' THEN 'A#02#02#13'
                                         END) AS ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                                       INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                              WHERE A.AccountNo IN  ('0341','0721')
                                AND LEFT(EC.CateNo,5) ='20151'
                                AND (CC.CateNo = '20012101' OR LEFT(CC.CateNo,5) ='20011')
                                AND SC.CateNo in ('2000304','2000301','2000302','2000309','2000311','2000312','2000313','2000314') AND SC.CateValue ='1'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, SectorCateNo
                             ) AS  B Group by SectorCateNo,ItemID
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function B_III_IV_V_VI($whereCondition, $year){
    $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ExpenseCateNo,ItemID,
                               MAX(CASE WHEN  AccountNo = '0341'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,EC.CateNo as ExpenseCateNo,YEAR(A.PostDate)AS YearD,
                                     (CASE WHEN EC.CateNo='20153' THEN 'A#02#03'
                                           WHEN EC.CateNo='20154' THEN 'A#02#04'
                                           WHEN EC.CateNo='20159' THEN 'A#02#05'
                                           WHEN EC.CateNo='20164' THEN 'A#02#06'
                                         END) AS ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN  ('0341','0721')
                                AND EC.CateNo IN ('20153','20154','20159','20164') AND EC.CateValue ='1'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, ExpenseCateNo
                             ) AS  B Group by ExpenseCateNo,ItemID
                            ) AS HH
                            SET TT.I1 = HH.I1, TT.I2 = HH.I2
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
