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

class SBPND312017NDCPMB33Controller extends Controller {
    public function SBPND312017NDCP_MB33(Request $request){

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
            if($userCompany->SumCompanyType == 1){
                // Tổng hợp đơn vị con
                $whereCommonCondition = " AND C.CompanyNo = '" . $userCompany->CompanyNo ."'";
            }
            else{
                if($userCompany->SumCompanyType == 2){
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
        $yearPre = (int)$year - 1;
        $yearNext = (int)$year + 1;

        $whereCondition = "AND (
        (YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $yearPre AND YEAR(A.PeriodToDate) = $yearPre)
        OR (YEAR(A.PeriodFromDate) = $yearNext AND YEAR(A.PeriodToDate) = $yearNext)
        ) $whereCompany";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `Project` varchar(50) DEFAULT '',
          `SbiCategoryNo` varchar(20) DEFAULT '',
          `ConAccountNo` varchar(20) DEFAULT '',
          `ConCoAccountNo` varchar(20) DEFAULT '',
          `SbiCategoryValue` varchar(20) DEFAULT '',
          `ExpenseCateValue` varchar (20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `Detail` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_312017ndcp_bm33')->get();
        if(in_array($userCompany->SumCompanyType, [2,3])){
            if($userCompany->ManagementLevel == 2){
                $BudgetLevelA ='2';
                $BudgetLevelB ='3';
            }
            else{
                $BudgetLevelA ='3';
                $BudgetLevelB ='4';
            }
        }
        $this->A_I_1($whereCondition, $yearNext, $BudgetLevelA, $BudgetLevelB);
        $this->A_I_2_3($whereCondition, $yearNext, $BudgetLevelA, $BudgetLevelB);
        $this->A_II($whereCondition, $yearNext, $BudgetLevelA, $BudgetLevelB);
        $this->A_III_IV_V_VI($whereCondition,$yearNext, $BudgetLevelA, $BudgetLevelB);
        $this->C($whereCondition,$yearNext, $BudgetLevelA, $BudgetLevelB);
        $this->insertB_I($whereCondition, $year,$yearNext, $BudgetLevelA);
        $this->insertB_II($whereCondition, $year,$yearNext, $BudgetLevelA);
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
                'Project' => $template->Project,
                'Detail' => $template->Detail,
                'SbiCategoryNo' => $template->SbiCategoryNo,
                'ConAccountNo' => $template->ConAccountNo,
                'ConCoAccountNo' => $template->ConCoAccountNo,
                'SbiCategoryValue' => $template->SbiCategoryValue,
                'ExpenseCateValue' => $template->ExpenseCateValue,
                'NormCateValue' => $template->NormCateValue,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            if($BudgetLevelA ='2'){
                $data[0]->HeaderName1 = 'Ngân sách cấp Tỉnh ' ;
                $data[0]->HeaderName2 = 'Ngân sách cấp Huyện ' ;
            }else if($BudgetLevelA ='3' ){
                $data[0]->HeaderName1 = 'Ngân sách cấp Huyện ';
                $data[0]->HeaderName2 = 'Ngân sách cấp Xã ' ;
            }

        }

        foreach ($data as $key => $value) {
            if($data[$key]->I2 !=0 || $data[$key]->I3 !=0){
                $data[$key]->I1 = round((int)($data[$key]->I2) + (int)($data[$key]->I3), 2);
            }
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
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
                "ReportName" => "DỰ TOÁN CHI NGÂN SÁCH ĐỊA PHƯƠNG, CHI NGÂN SÁCH CẤP TỈNH  VÀ CHI NGÂN SÁCH HUYỆN THEO CƠ CẤU CHI NĂM " . $yearNext,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    public function A_I_1($whereCondition, $yearNext, $BudgetLevelA, $BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                            SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelA' AND B.YearD = '$yearNext' THEN I END) AS I2,
                            SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelB' AND B.YearD = '$yearNext' THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD,A.BudgetLevel,
                                     (Case WHEN LEFT(SC.CateNo,7) ='2000303' THEN  'A#01#01#01#02'
                                           WHEN SC.CateNo ='2000304' and CC.CateValue = 1 THEN  'A#01#01#01#03'
                                           WHEN CC.CateNo= '20012102' AND CC.CateValue = '6' THEN  'A#01#01#01#05'
                                           WHEN CC.CateNo= '20012102' AND CC.CateValue = '7' THEN  'A#01#01#01#06'
                                         END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID /*loại nguồn vốn */
                                       INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID /*loại lĩnh vực */
                              WHERE LEFT(EC.CateNo,7)='2015201'
                                AND ( ( ( LEFT(CC.CateNo,8) ='20012102' OR (CC.CateNo IN ('20012201','20012202','20012203','20012204') AND CC.CateValue ='1') )
                                          AND ( LEFT(SC.CateNo,7) ='2000303' OR (SC.CateNo ='2000304' and CC.CateValue = 1 ) ) )
                                        OR ( CC.CateNo= '20012102' AND CC.CateValue IN('6','7') ) )
                                  $whereCondition
                              GROUP BY A.AccountNo, CC.CateNo, SC.CateNo
                             ) AS  B group by ItemID
) AS HH
SET TT.I2 = HH.I2, TT.I3 = HH.I3
WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function A_I_2_3($whereCondition, $yearNext, $BudgetLevelA, $BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,CapitalNo,
                               SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelA' AND B.YearD = '$yearNext' THEN I END) AS I2,
                               SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelB' AND B.YearD = '$yearNext' THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD,A.BudgetLevel ,CC.CateNo as CapitalNo,
                                     (Case WHEN EC.CateNo ='2015202' THEN  'A#01#01#02'
                                           WHEN EC.CateNo ='2015203' THEN  'A#01#01#03' END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE ( LEFT(CC.CateNo,8)='20012102'
                                  OR (CC.CateNo in ('20012201','20012201','20012203','20012204') AND CC.CateValue ='1')
                                  )
                                AND ((EC.CateNo = '2015202' AND EC.CateNo IN ('1','2')) OR (EC.CateNo='2015203' AND EC.CateNo ='99'))
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, EC.CateNo, CC.CateNo
                             ) AS  B GROUP BY ItemID
) AS HH
SET TT.I2 = HH.I2, TT.I3 = HH.I3
WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function A_II($whereCondition, $yearNext, $BudgetLevelA, $BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,SectorCateNo, CapitalNo,
                               SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelA' AND B.YearD = '$yearNext' THEN I END) AS I2,
                               SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelB' AND B.YearD = '$yearNext' THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD,SC.CateNo AS SectorCateNo ,CC.CateNo as CapitalNo,A.BudgetLevel,
                                 (Case WHEN LEFT(SC.CateNo,7) ='2000303' THEN  'A#01#02#02'
                                       WHEN SC.CateNo ='2000304' THEN  'A#01#02#03' END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE LEFT(EC.CateNo,5)='20151'
                                AND ( LEFT(CC.CateNo,5)='20011' OR LEFT(CC.CateNo,6)='200121')
                                AND ( LEFT (SC.CateNo,7) = '2000303' OR ( SC.CateNo ='2000304' AND SC.CateValue = 1 ) )
                                  $whereCondition
                              GROUP BY  A.AccountNo, CC.CateNo, SectorCateNo, CapitalNo
                             ) AS  B GROUP BY ItemID
) AS HH
SET TT.I2 = HH.I2, TT.I3 = HH.I3
WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function A_III_IV_V_VI($whereCondition, $yearNext, $BudgetLevelA, $BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,ExpenseCateNo,
                               SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelA' AND B.YearD = '$yearNext' THEN I END) AS I2,
                               SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelB' AND B.YearD = '$yearNext' THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,EC.CateNo as ExpenseCateNo  ,YEAR(A.PostDate)AS YearD,A.BudgetLevel,
                                     (Case WHEN EC.CateNo ='20153' THEN  'A#01#03'
                                           WHEN EC.CateNo ='20154' THEN  'A#01#04'
                                           WHEN EC.CateNo ='20159' THEN  'A#01#05'
                                           WHEN EC.CateNo ='20164' THEN  'A#01#06'  END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE EC.CateNo IN ('20153', '20154','20159','20164') AND EC.CateValue ='1'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, EC.CateNo
                             ) AS  B GROUP BY ItemID, ExpenseCateNo
) AS HH
SET TT.I2 = HH.I2, TT.I3 = HH.I3
WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function C($whereCondition, $yearNext, $BudgetLevelA, $BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                               SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelA' AND B.YearD = '$yearNext' THEN I END) AS I2,
                               SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelB' AND B.YearD = '$yearNext' THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,YEAR(A.PostDate)AS YearD,A.BudgetLevel
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE LEFT(EC.CateNo,5)='20161'
                                    AND LEFT(CC.CateNo,4)='2001'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD,A.BudgetLevel
                             ) AS  B
) AS HH
SET TT.I2 = HH.I2, TT.I3 = HH.I3
WHERE TT.ItemID = 'A#03'";
        DB::statement($sqlUpdate);
    }
    public function insertB_I($whereCondition, $yearNext, $BudgetLevelA, $BudgetLevelB){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, Detail, I2, I3)
                    SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName,Project, '1',
                           SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelA' AND B.YearD = '$yearNext' THEN I END) AS I2,
                           SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelB' AND B.YearD = '$yearNext' THEN I END) AS I3
                    FROM
                        (SELECT 'A#02#01' AS ParentID ,A.ProgramName AS ItemName,A.ProjectNo as Project, A.AccountNo,SUM(A.FCDebitAmount) AS I,YEAR(A.PeriodFromDate) AS YearD, A.BudgetLevel
                         FROM act_gl_books as A
                                  INNER JOIN program_cate as PC ON PC.ProgramID = A.ProgramID
                                  INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                         WHERE LEFT(EC.CateNo,5) IN ('20151','20152')
                               AND PC.CateNo ='001' AND PC.CateValue ='1'
                                 $whereCondition
                         GROUP BY A.AccountNo, Project, EC.CateNo
                    ) as B GROUP BY Project";
        DB::statement($sql);
    }
    public function insertB_II($whereCondition, $yearNext, $BudgetLevelA, $BudgetLevelB){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, Detail, I2, I3)
                    SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName,Project, '1',
                           SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelA' AND B.YearD = '$yearNext' THEN I END) AS I2,
                           SUM(CASE WHEN  AccountNo = '0411' AND B.BudgetLevel = '$BudgetLevelB' AND B.YearD = '$yearNext' THEN I END) AS I3
                    FROM
                        (SELECT 'A#02#02' AS ParentID ,A.ProgramName AS ItemName,A.ProjectNo as Project, A.AccountNo,SUM(A.FCDebitAmount) AS I,YEAR(A.PeriodFromDate) AS YearD, A.BudgetLevel
                         FROM act_gl_books as A
                                  INNER JOIN program_cate as PC ON PC.ProgramID = A.ProgramID
                                  INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                         WHERE LEFT(EC.CateNo,5) IN ('20151','20152')
                               AND PC.CateNo ='001' AND PC.CateValue ='2'
                                 $whereCondition
                         GROUP BY A.AccountNo, Project, EC.CateNo
                    ) as B GROUP BY Project";
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I2) AS I2, SUM(M.I3) AS I3
                FROM table_tmp M
                WHERE M.Level = " . $i . " GROUP BY ParentID";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
                        $I2 = ($value['I2']) ? $value['I2'] : 0;
                        $I3 = ($value['I3']) ? $value['I3'] : 0;
                        $update = "UPDATE table_tmp
                    SET  I2 = " . $I2 . ", I3 = " . $I3 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
