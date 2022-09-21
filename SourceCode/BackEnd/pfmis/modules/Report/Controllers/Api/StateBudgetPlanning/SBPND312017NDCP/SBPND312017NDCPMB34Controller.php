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

class SBPND312017NDCPMB34Controller extends Controller {
    public function SBPND312017NDCP_MB34(Request $request){

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
          `SbiCategoryNo` varchar(20) DEFAULT '',
          `ConAccountNo` varchar(20) DEFAULT '',
          `ConCoAccountNo` varchar(20) DEFAULT '',
          `SbiCategoryValue` varchar(20) DEFAULT '',
          `ExpenseCateValue` varchar (20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_312017ndcp_bm34')->get();

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
                'I1' => $template->I1
            ];
        }
        DB::table('table_tmp')->insert($insertArr);
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
        $this->updateProvinceI_A_C($whereCondition, $yearNext, $BudgetLevelA);
        $this->updateProvinceI_B_I_1($whereCondition, $yearNext, $BudgetLevelA);
        $this->updateProvinceI_B_I_23($whereCondition, $yearNext, $BudgetLevelA);
        $this->updateProvinceI_B_II($whereCondition, $yearNext, $BudgetLevelA);
        $this->updateProvinceI_B_III_IV_V_VI($whereCondition, $yearNext, $BudgetLevelA);

        $this->updateDistrictI_A_C($whereCondition, $yearNext, $BudgetLevelB);
        $this->updateDistrictI_B_I_1($whereCondition, $yearNext, $BudgetLevelB);
        $this->updateDistrictI_B_I_23($whereCondition, $yearNext, $BudgetLevelB);
        $this->updateDistrictI_B_II($whereCondition, $yearNext, $BudgetLevelB);
        $this->updateDistrictI_B_III_IV_V_VI($whereCondition, $yearNext, $BudgetLevelB);

        $this->sumUpTableTmp(1, 5);



        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
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
                "ReportName" => "DỰ TOÁN CHI NGÂN SÁCH CẤP TỈNH (HUYỆN, XÃ) THEO LĨNH VỰC NĂM " . $yearNext,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function updateProvinceI_A_C($whereCondition, $yearNext, $BudgetLevelA){
        $sqlUpdate = "UPDATE table_tmp AS TT, (
                                SELECT SUM(A.FCDebitAmount) AS I,
                                        (CASE
                                             WHEN ( LEFT(EC.CateNo,8) = '201610101'  ) THEN 'A#01'
                                             WHEN ( LEFT(EC.CateNo,5) ='20161') THEN 'A#03'
                                            END ) as ItemID
                                 FROM  act_gl_books AS A
                                        INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                        INNER JOIN company as C ON C.CompanyID= A.CompanyID
                                 WHERE ( LEFT(EC.CateNo,8) = '201610101' OR LEFT(EC.CateNo,5) ='20161')
                                       AND AccountNo= '0411'
                                       AND A.BudgetLevel = '$BudgetLevelA'
                                       AND YEAR(A.PeriodFromDate)='$yearNext'
                                             $whereCondition
                                 GROUP BY ItemID,EC.CateNo
                            ) AS HH
                        SET TT.I1 = HH.I
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateProvinceI_B_I_1($whereCondition, $yearNext, $BudgetLevelA){
        $sqlUpdate = "UPDATE table_tmp AS TT, (
                        SELECT SUM(A.FCDebitAmount) AS I,
                                (CASE
                                     WHEN ( left(SC.CateNo,7) = '2000303'  ) THEN 'A#02#01#01#01'
                                     WHEN ( SC.CateNo = '2000304' AND SC.CateValue='1'  ) THEN 'A#02#01#01#02'
                                     WHEN ( SC.CateNo = '2000301' AND SC.CateValue='1'  ) THEN 'A#02#01#01#03'
                                     WHEN ( SC.CateNo = '2000302' AND SC.CateValue='1'  ) THEN 'A#02#01#01#04'
                                     WHEN ( left(SC.CateNo,7) = '2000305' ) THEN 'A#02#01#01#05'
                                     WHEN ( left(SC.CateNo,7) = '2000306' ) THEN 'A#02#01#01#06'
                                     WHEN ( left(SC.CateNo,7) = '2000308' ) THEN 'A#02#01#01#07'
                                     WHEN ( SC.CateNo = '2000309' AND SC.CateValue='1'  ) THEN 'A#02#01#01#08'
                                     WHEN ( SC.CateNo = '2000311' AND SC.CateValue='1'  ) THEN 'A#02#01#01#09'
                                     WHEN ( left(SC.CateNo,7) = '2000307' ) THEN 'A#02#01#01#10'
                                     WHEN ( SC.CateNo = '2000312' AND SC.CateValue='1'  ) THEN 'A#02#01#01#11'
                                     WHEN ( SC.CateNo = '2000313' AND SC.CateValue='1'  ) THEN 'A#02#01#01#12'
                                     WHEN ( SC.CateNo = '2000314' AND SC.CateValue='1'  ) THEN 'A#02#01#01#13'
                                END ) as ItemID
                         FROM  act_gl_books AS A
                                INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                                INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                         WHERE ( LEFT(CC.CateNo,8)='20012102' OR
                                 ( CC.CateNo IN ('20012201','20012202','20012203','20012204') AND CC.CateValue = '1')
                                )
                               AND A.BudgetLevel = '$BudgetLevelA'
                               AND AccountNo= '0411'
                               AND LEFT(EC.CateNo,7) = '2015201'
                               AND YEAR(A.PeriodFromDate)='$yearNext'
                                $whereCondition
                         GROUP BY ItemID , CC.cateNo
                ) AS HH
SET TT.I1 = HH.I
WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateProvinceI_B_I_23($whereCondition, $yearNext, $BudgetLevelA){
        $sqlUpdate = "UPDATE table_tmp AS TT, (
                        SELECT SUM(A.FCDebitAmount) AS I,EC.CateNo,
                                (CASE
                                     WHEN ( LEFT(EC.CateNo,7)='2015202'  ) THEN 'A#02#01#02'
                                     WHEN ( LEFT(EC.CateNo,7)='2015203'  ) THEN 'A#02#01#03'
                                END ) as ItemID
                         FROM  act_gl_books AS A
                                INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                         WHERE ( LEFT(CC.CateNo,8)='20012102' OR
                                 ( CC.CateNo IN ('20012201','20012202','20012203','20012204') AND CC.CateValue = '1')
                                )
                               AND A.BudgetLevel = '$BudgetLevelA'
                               AND AccountNo= '0411'
                               AND LEFT(EC.CateNo,7) IN('2015202','2015203')
                               AND YEAR(A.PeriodFromDate)='$yearNext'
                                $whereCondition
                         GROUP BY ItemID , CC.cateNo, EC.CateNo
                ) AS HH
SET TT.I1 = HH.I
WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateProvinceI_B_II($whereCondition, $yearNext, $BudgetLevelA){
        $sqlUpdate = "UPDATE table_tmp AS TT, (
                        SELECT SUM(A.FCDebitAmount) AS I,SC.CateNo,
                                (CASE
                                     WHEN ( left(SC.CateNo,7) = '2000303'  ) THEN 'A#02#02#01'
                                     WHEN ( SC.CateNo = '2000304' AND SC.CateValue='1'  ) THEN 'A#02#02#02'
                                     WHEN ( SC.CateNo = '2000301' AND SC.CateValue='1'  ) THEN 'A#02#02#03'
                                     WHEN ( SC.CateNo = '2000302' AND SC.CateValue='1'  ) THEN 'A#02#02#04'
                                     WHEN ( left(SC.CateNo,7) = '2000305' ) THEN 'A#02#02#05'
                                     WHEN ( left(SC.CateNo,7) = '2000306' ) THEN 'A#02#02#06'
                                     WHEN ( left(SC.CateNo,7) = '2000308' ) THEN 'A#02#02#07'
                                     WHEN ( SC.CateNo = '2000309' AND SC.CateValue='1'  ) THEN 'A#02#02#08'
                                     WHEN ( SC.CateNo = '2000311' AND SC.CateValue='1'  ) THEN 'A#02#02#09'
                                     WHEN ( left(SC.CateNo,7) = '2000307' ) THEN 'A#02#02#10'
                                     WHEN ( SC.CateNo = '2000312' AND SC.CateValue='1'  ) THEN 'A#02#02#11'
                                     WHEN ( SC.CateNo = '2000313' AND SC.CateValue='1'  ) THEN 'A#02#02#12'
                                     WHEN ( SC.CateNo = '2000314' AND SC.CateValue='1'  ) THEN 'A#02#02#13'
                                END ) as ItemID
                         FROM  act_gl_books AS A
                                INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                                INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                         WHERE ( LEFT(CC.CateNo,8)='20012101' OR
                                 LEFT(CC.CateNo,5)='20011' )
                               AND A.BudgetLevel = '$BudgetLevelA'
                               AND AccountNo= '0411'
                               AND LEFT(EC.CateNo,5) = '20151'
                               AND YEAR(A.PeriodFromDate)='$yearNext'
                                $whereCondition
                         GROUP BY ItemID , CC.CateNo, SC.CateNo
                ) AS HH
SET TT.I1 = HH.I
WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateProvinceI_B_III_IV_V_VI($whereCondition, $yearNext, $BudgetLevelA){
        $sqlUpdate = "UPDATE table_tmp AS TT, (
                                SELECT SUM(A.FCDebitAmount) AS I,EC.CateNo,
                                        (CASE
                                             WHEN ( EC.CateNo ='20153' AND EC.CateValue ='1'  ) THEN 'A#02#03'
                                             WHEN ( EC.CateNo ='20154' AND EC.CateValue ='1'  ) THEN 'A#02#04'
                                             WHEN ( EC.CateNo ='20159' AND EC.CateValue ='1'  ) THEN 'A#02#05'
                                             WHEN ( EC.CateNo ='20164' AND EC.CateValue ='1'  ) THEN 'A#02#06'
                                            END ) as ItemID
                                 FROM  act_gl_books AS A
                                        INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                 WHERE EC.CateNo IN ('20153','20154','20159','20164') AND EC.CateValue ='1'
                                       AND A.BudgetLevel = '$BudgetLevelA'
                                       AND AccountNo= '0411'
                                       AND YEAR(A.PeriodFromDate)='$yearNext'
                                        $whereCondition
                                 GROUP BY ItemID, EC.CateNo
                            ) AS HH
                        SET TT.I1 = HH.I
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }


    public function updateDistrictI_A_C($whereCondition, $yearNext, $BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT, (
                                SELECT SUM(A.FCDebitAmount) AS I,
                                        (CASE
                                             WHEN ( LEFT(EC.CateNo,8) = '201610101'  ) THEN 'A#01'
                                             WHEN ( LEFT(EC.CateNo,5) ='20161') THEN 'A#03'
                                            END ) as ItemID
                                 FROM  act_gl_books AS A
                                        INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                        INNER JOIN company as C ON C.CompanyID= A.CompanyID
                                 WHERE ( LEFT(EC.CateNo,8) = '201610101' OR LEFT(EC.CateNo,5) ='20161')
                                       AND AccountNo= '0411'
                                       AND A.BudgetLevel = '$BudgetLevelB'
                                       AND YEAR(A.PeriodFromDate)='$yearNext'
                                             $whereCondition
                                 GROUP BY ItemID,EC.CateNo
                            ) AS HH
                        SET TT.I1 = HH.I
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateDistrictI_B_I_1($whereCondition, $yearNext, $BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT, (
                        SELECT SUM(A.FCDebitAmount) AS I,
                                (CASE
                                     WHEN ( left(SC.CateNo,7) = '2000303'  ) THEN 'A#02#01#01#01'
                                     WHEN ( SC.CateNo = '2000304' AND SC.CateValue='1'  ) THEN 'A#02#01#01#02'
                                     WHEN ( SC.CateNo = '2000301' AND SC.CateValue='1'  ) THEN 'A#02#01#01#03'
                                     WHEN ( SC.CateNo = '2000302' AND SC.CateValue='1'  ) THEN 'A#02#01#01#04'
                                     WHEN ( left(SC.CateNo,7) = '2000305' ) THEN 'A#02#01#01#05'
                                     WHEN ( left(SC.CateNo,7) = '2000306' ) THEN 'A#02#01#01#06'
                                     WHEN ( left(SC.CateNo,7) = '2000308' ) THEN 'A#02#01#01#07'
                                     WHEN ( SC.CateNo = '2000309' AND SC.CateValue='1'  ) THEN 'A#02#01#01#08'
                                     WHEN ( SC.CateNo = '2000311' AND SC.CateValue='1'  ) THEN 'A#02#01#01#09'
                                     WHEN ( left(SC.CateNo,7) = '2000307' ) THEN 'A#02#01#01#10'
                                     WHEN ( SC.CateNo = '2000312' AND SC.CateValue='1'  ) THEN 'A#02#01#01#11'
                                     WHEN ( SC.CateNo = '2000313' AND SC.CateValue='1'  ) THEN 'A#02#01#01#12'
                                     WHEN ( SC.CateNo = '2000314' AND SC.CateValue='1'  ) THEN 'A#02#01#01#13'
                                END ) as ItemID
                         FROM  act_gl_books AS A
                                INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                                INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                         WHERE ( LEFT(CC.CateNo,8)='20012102' OR
                                 ( CC.CateNo IN ('20012201','20012202','20012203','20012204') AND CC.CateValue = '1')
                                )
                               AND A.BudgetLevel = '$BudgetLevelB'
                               AND AccountNo= '0411'
                               AND LEFT(EC.CateNo,7) = '2015201'
                               AND YEAR(A.PeriodFromDate)='$yearNext'
                                $whereCondition
                         GROUP BY ItemID , CC.cateNo
                ) AS HH
SET TT.I1 = HH.I
WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateDistrictI_B_I_23($whereCondition, $yearNext, $BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT, (
                        SELECT SUM(A.FCDebitAmount) AS I,EC.CateNo,
                                (CASE
                                     WHEN ( LEFT(EC.CateNo,7)='2015202'  ) THEN 'A#02#01#02'
                                     WHEN ( LEFT(EC.CateNo,7)='2015203'  ) THEN 'A#02#01#03'
                                END ) as ItemID
                         FROM  act_gl_books AS A
                                INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                         WHERE ( LEFT(CC.CateNo,8)='20012102' OR
                                 ( CC.CateNo IN ('20012201','20012202','20012203','20012204') AND CC.CateValue = '1')
                                )
                               AND A.BudgetLevel = '$BudgetLevelB'
                               AND AccountNo= '0411'
                               AND LEFT(EC.CateNo,7) IN('2015202','2015203')
                               AND YEAR(A.PeriodFromDate)='$yearNext'
                                $whereCondition
                         GROUP BY ItemID , CC.cateNo, EC.CateNo
                ) AS HH
SET TT.I1 = HH.I
WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateDistrictI_B_II($whereCondition, $yearNext, $BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT, (
                        SELECT SUM(A.FCDebitAmount) AS I,SC.CateNo,
                                (CASE
                                     WHEN ( left(SC.CateNo,7) = '2000303'  ) THEN 'A#02#02#01'
                                     WHEN ( SC.CateNo = '2000304' AND SC.CateValue='1'  ) THEN 'A#02#02#02'
                                     WHEN ( SC.CateNo = '2000301' AND SC.CateValue='1'  ) THEN 'A#02#02#03'
                                     WHEN ( SC.CateNo = '2000302' AND SC.CateValue='1'  ) THEN 'A#02#02#04'
                                     WHEN ( left(SC.CateNo,7) = '2000305' ) THEN 'A#02#02#05'
                                     WHEN ( left(SC.CateNo,7) = '2000306' ) THEN 'A#02#02#06'
                                     WHEN ( left(SC.CateNo,7) = '2000308' ) THEN 'A#02#02#07'
                                     WHEN ( SC.CateNo = '2000309' AND SC.CateValue='1'  ) THEN 'A#02#02#08'
                                     WHEN ( SC.CateNo = '2000311' AND SC.CateValue='1'  ) THEN 'A#02#02#09'
                                     WHEN ( left(SC.CateNo,7) = '2000307' ) THEN 'A#02#02#10'
                                     WHEN ( SC.CateNo = '2000312' AND SC.CateValue='1'  ) THEN 'A#02#02#11'
                                     WHEN ( SC.CateNo = '2000313' AND SC.CateValue='1'  ) THEN 'A#02#02#12'
                                     WHEN ( SC.CateNo = '2000314' AND SC.CateValue='1'  ) THEN 'A#02#02#13'
                                END ) as ItemID
                         FROM  act_gl_books AS A
                                INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                                INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                         WHERE ( LEFT(CC.CateNo,8)='20012101' OR
                                 LEFT(CC.CateNo,5)='20011' )
                               AND A.BudgetLevel = '$BudgetLevelB'
                               AND AccountNo= '0411'
                               AND LEFT(EC.CateNo,5) = '20151'
                               AND YEAR(A.PeriodFromDate)='$yearNext'
                                $whereCondition
                         GROUP BY ItemID , CC.CateNo, SC.CateNo
                ) AS HH
SET TT.I1 = HH.I
WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateDistrictI_B_III_IV_V_VI($whereCondition, $yearNext, $BudgetLevelB){
        $sqlUpdate = "UPDATE table_tmp AS TT, (
                                SELECT SUM(A.FCDebitAmount) AS I,EC.CateNo,
                                        (CASE
                                             WHEN ( EC.CateNo ='20153' AND EC.CateValue ='1'  ) THEN 'A#02#03'
                                             WHEN ( EC.CateNo ='20154' AND EC.CateValue ='1'  ) THEN 'A#02#04'
                                             WHEN ( EC.CateNo ='20159' AND EC.CateValue ='1'  ) THEN 'A#02#05'
                                             WHEN ( EC.CateNo ='20164' AND EC.CateValue ='1'  ) THEN 'A#02#06'
                                            END ) as ItemID
                                 FROM  act_gl_books AS A
                                        INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                 WHERE EC.CateNo IN ('20153','20154','20159','20164') AND EC.CateValue ='1'
                                       AND A.BudgetLevel = '$BudgetLevelB'
                                       AND AccountNo= '0411'
                                       AND YEAR(A.PeriodFromDate)='$yearNext'
                                        $whereCondition
                                 GROUP BY ItemID, EC.CateNo
                            ) AS HH
                        SET TT.I1 = HH.I
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1
                FROM table_tmp M
                WHERE M.Level = " . $i . " GROUP BY ParentID";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
                        $I1 = ($value['I1']) ? $value['I1'] : 0;
                        $update = "UPDATE table_tmp
                    SET I1 = " . $I1 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
