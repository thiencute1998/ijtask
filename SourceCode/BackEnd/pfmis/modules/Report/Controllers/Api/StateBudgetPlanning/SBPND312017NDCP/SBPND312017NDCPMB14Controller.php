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

class SBPND312017NDCPMB14Controller extends Controller {
    public function SBPND312017NDCP_MB14(Request $request){

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
            $templateTable = DB::table('report_sbp_312017ndcp_bm14')->get();

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
        $this->A_I_1a($whereCondition, $year);
        $this->A_I_1b($whereCondition, $year);
        $this->A_I_2_3($whereCondition, $year);
        $this->A_II($whereCondition, $year);
        $this->B();
        $this->A_III_IV_V_VI_C($whereCondition, $year);
        $this->insertB($whereCondition, $year);
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
    public function A_I_1a($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,SbiValue,
                               MAX(CASE WHEN  AccountNo = '0441'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,SCC.CateValue as SbiValue,YEAR(A.PostDate)AS YearD,
                                     (Case WHEN SCC.CateValue ='3' THEN  'A#01#01#01#01'
                                           WHEN SCC.CateValue ='4' THEN  'A#01#01#01#02' END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN sbi_category_cate AS SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0441','0721')
                                AND CC.CateNo = '20012102'
                                AND LEFT(EC.CateNo, 7) = '2015201' AND SCC.CateNo = '001' AND SCC.CateValue IN ('3', '4')

                                  $whereCondition
                              GROUP BY A.AccountNo, YearD,SbiValue
                             ) AS  B GROUP BY ItemID,SbiValue
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function A_I_1b($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID, CapitalCateValue,
                               MAX(CASE WHEN  AccountNo = '0441'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,CC.CateValue as CapitalCateValue,YEAR(A.PostDate)AS YearD,
                                     (Case WHEN CC.CateValue ='6' THEN  'A#01#01#02#01'
                                           WHEN CC.CateValue ='7' THEN  'A#01#01#02#02'END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0441','0721')
                                AND CC.CateNo = '20012102'
                                AND CC.CateValue IN ('6','7') AND LEFT(EC.CateNo, 7) = '2015201'

                                  $whereCondition
                              GROUP BY A.AccountNo, YearD,CapitalCateValue
                             ) AS  B GROUP BY ItemID,CapitalCateValue
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE TT.ItemID = HH.ItemID;";
                                DB::statement($sqlUpdate);
    }
    public function A_I_2_3($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                               MAX(CASE WHEN  AccountNo = '0441'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD,
                                     (Case WHEN EC.CateNo ='2015202' AND EC.CateValue IN('1','2') THEN  'A#01#02'
                                           WHEN EC.CateNo ='2015202' THEN  'A#01#03' END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0441','0721')
                                AND CC.CateNo='20012102' AND EC.CateNo ='2015202'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B GROUP BY ItemID
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function A_II($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                               MAX(CASE WHEN  AccountNo = '0441'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD,
                                     (Case WHEN SCC.CateValue ='3' THEN  'A#02#01#01'
                                           WHEN SCC.CateValue ='4' THEN  'A#02#01#02' END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN sbi_category_cate AS SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0441','0721')
                                AND CC.CateNo IN ('20011101','20012101') AND LEFT(EC.CateNo,5)='20151'
                                AND SCC.CateNo ='001' AND SCC.CateValue IN ('3','4')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B GROUP BY ItemID
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2
                        WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function A_III_IV_V_VI_C($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                               MAX(CASE WHEN  AccountNo = '0441'  AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD,
                                     (Case WHEN EC.CateNo ='20153' THEN  'A#03'
                                           WHEN EC.CateNo ='20154' THEN  'A#04'
                                           WHEN EC.CateNo ='20159' THEN  'A#05'
                                           WHEN EC.CateNo ='20164' THEN  'A#06'
                                           WHEN LEFT(EC.CateNo,5) ='20161' THEN  'C' END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN sbi_category_cate AS SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0441','0721')
                                AND ((EC.CateNo ='20153' AND EC.CateValue IN('1','2') AND CC.CateNo = '200193')
                                OR (EC.CateNo IN ('20154','20154') AND EC.CateValue = '1')
                                OR (EC.CateNo ='20164' AND CC.CateNo ='200194')
                                OR LEFT(EC.CateNo,5) ='20161'
                                    )
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B GROUP BY ItemID
                            ) AS HH
                            SET TT.I1 = HH.I1, TT.I2 = HH.I2
                            WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function B(){
        $itemA = DB::table('table_tmp')->where('ItemID', '=', 'B#01')->first();
        $itemB = DB::table('table_tmp')->where('ItemID', '=', 'B#02')->first();
        DB::table('table_tmp')->where('ItemID','=','B')
            ->update([
                'I1'=> $itemA->I1-$itemB->I1,
                'I2'=> $itemA->I2-$itemB->I2,
            ]);
    }
    public function insertB($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, Detail, I1, I2)
                SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName,Project, '1',
                       MAX(CASE WHEN  AccountNo = '0441'  AND Yeard = $year THEN I END) AS I1,
                       MAX(CASE WHEN  AccountNo = '0721'  AND Yeard = $year THEN I END) AS I2
                FROM
                    (SELECT 'B#01' AS ParentID ,A.ProgramName AS ItemName,A.ProjectNo as Project, A.AccountNo,SUM(A.FCDebitAmount) AS I,YEAR(A.PeriodFromDate) AS YEARD
                     FROM act_gl_books as A
                              INNER JOIN program_cate as PC ON PC.ProgramID = A.ProgramID
                     WHERE  A.AccountNo in('0441','0721')
                       AND PC.CateNo ='001' AND PC.CateValue ='1'
                         $whereCondition
                     GROUP BY A.AccountNo, Project
                    ) as CH GROUP BY Project";
                        DB::statement($sql);
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
