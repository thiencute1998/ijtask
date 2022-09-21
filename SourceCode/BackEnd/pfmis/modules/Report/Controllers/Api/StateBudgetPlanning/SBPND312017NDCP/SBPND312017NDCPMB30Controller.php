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

class SBPND312017NDCPMB30Controller extends Controller {
    public function SBPND312017NDCP_MB30(Request $request){

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
        $preYear = (int)$year - 1;
        $nextYear = (int)$year + 1;
        $next2Year = (int)$year + 2;
        $whereCondition = "AND (
        ( YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $preYear AND YEAR(A.PeriodToDate) = $preYear)
        OR (YEAR(A.PeriodFromDate) = $nextYear AND YEAR(A.PeriodToDate) = $nextYear)
        ) $whereCompany  AND A.InTransTypeID NOT IN (16,17,18) and AND A.BudgetLevel IN ('2','3','4') $whereCommonCondition";


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
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          `I5` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_312017ndcp_bm30')->get();

        if(in_array($userCompany->SumCompanyType, [2,3])){
            if($userCompany->ManagementLevel == 2){
                $this->updateDistrict_AI_I1_2_3($whereCondition,$year);
                $this->updateDistrict_AII_I1_2_3($whereCondition,$year);
            }
            else{
                $this->updateCommune_AI_I1_2_3($whereCondition,$year);
                $this->updateCommune_AII_I1_2_3($whereCondition,$year);
            }

        }
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
                'I5' => $template->I5
            ];
        }

        DB::table('table_tmp')->insert($insertArr);


        // sum up
        $this->sumUpTableTmp(1, 4);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Dự toán năm ' .$year. ' (hiện hành)';
            $data[0]->HeaderName2 = 'Ước thực hiện năm '.$year.' (hiện hành)';
            $data[0]->HeaderName3 = 'Dự toán năm ' .$nextYear;
        }

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = (int)($data[$key]->I3) - (int)($data[$key]->I2);
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            if($data[$key]->I2 !=0){
                $data[$key]->I5 = round((( (int)($data[$key]->I3) - (int)($data[$key]->I2))/(int)($data[$key]->I2))*100, 2);
            }
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
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
                "ReportName" => "CÂN ĐỐI NGUỒN THU, CHI DỰ TOÁN NGÂN SÁCH CẤP HUYỆN  VÀ NGÂN SÁCH XÃ NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    public function updateDistrict_AI_I1_2_3($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, RevenueCateValue,
              MAX(CASE WHEN AccountNo = '0341' AND YearD = $year  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0711' AND YearD = $year THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0311' AND YearD = $year + 1 THEN I END) AS I3  FROM
            (SELECT  (Case when LEFT(RC.CateNo,5) = '20111' OR RC.CateNo IN ('20112','20113','20114','20115','20116')  THEN 'A#01#01'
                           when RC.CateNo = '201210101' AND  RC.CateValue ='1' THEN 'A#01#02#01'
                           when RC.CateNo = '201210102' AND RC.CateValue IN('1','2')   THEN 'A#01#02#02'
                           when RC.CateNo = '2011104' AND RC.CateValue ='3' THEN  'A#01#03'
                           when RC.CateNo = '2011104' AND  RC.CateValue IN('1','2') THEN 'A#01#04'
                           when RC.CateNo = '201110501' AND  RC.CateValue ='1' THEN 'A#01#05'
                           when RC.CateNo= '201110502' AND  RC.CateValue ='1' THEN 'A#10'
                                             END) as ItemID,
             RC.CateValue as RevenueCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo,A.BudgetLevel as BudgetLevel
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            WHERE left(CC.CateNo,4) = '2001'
            AND A.AccountNo IN('0341','0711','0311') and A.BudgetLevel ='2'
            $whereCondition
            GROUP BY  RC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY RevenueCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I4 = HH.I4,TT.I3 = HH.I3
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateDistrict_AII_I1_2_3($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, RevenueCateValue,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0721' AND YearD = $year THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1 THEN I END) AS I3  FROM
            (SELECT  (Case when LEFT(RC.CateNo,5) = '20111' OR RC.CateNo IN ('20112','20113','20114','20115','20116')  THEN 'A#02#01'
                           when RC.CateNo = '201210101' AND  RC.CateValue ='1' THEN 'A#02#02#01'
                           when RC.CateNo = '201210102' AND RC.CateValue IN('1','2')   THEN 'A#02#02#02'
                           when RC.CateNo = '2011104' AND RC.CateValue ='3' THEN  'A#01#03'
                           when RC.CateNo = '2011104' AND  RC.CateValue IN('1','2') THEN 'A#01#04'
                           when RC.CateNo = '201110501' AND  RC.CateValue ='1' THEN 'A#01#05'
                           when RC.CateNo= '201110502' AND  RC.CateValue ='1' THEN 'A#10'
                                             END) as ItemID,
             RC.CateValue as RevenueCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo,A.BudgetLevel as BudgetLevel
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            WHERE left(CC.CateNo,4) = '2001'
            AND A.AccountNo IN('0441','0721','0411')   and A.BudgetLevel IN('3','4')
            $whereCondition
            GROUP BY  RC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY RevenueCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I4 = HH.I4,TT.I3 = HH.I3
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }


    public function updateCommune_AI_I1_2_3($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, RevenueCateValue,
              MAX(CASE WHEN AccountNo = '0341' AND YearD = $year  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0711' AND YearD = $year THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0311' AND YearD = $year + 1 THEN I END) AS I3  FROM
            (SELECT  (Case when LEFT(RC.CateNo,5) = '20111' OR RC.CateNo IN ('20112','20113','20114','20115','20116')  THEN 'A#01#01'
                           when RC.CateNo = '201210101' AND  RC.CateValue ='1' THEN 'A#01#02#01'
                           when RC.CateNo = '201210102' AND RC.CateValue IN('1','2')   THEN 'A#01#02#02'
                           when RC.CateNo = '2011104' AND RC.CateValue ='3' THEN  'A#01#03'
                           when RC.CateNo = '2011104' AND  RC.CateValue IN('1','2') THEN 'A#01#04'
                           when RC.CateNo = '201110501' AND  RC.CateValue ='1' THEN 'A#01#05'
                           when RC.CateNo= '201110502' AND  RC.CateValue ='1' THEN 'A#10'
                                             END) as ItemID,
             RC.CateValue as RevenueCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo,A.BudgetLevel as BudgetLevel
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            WHERE left(CC.CateNo,4) = '2001'
            AND A.AccountNo IN('0341','0711','0311') AND A.BudgetLevel =('3')
            $whereCondition
            GROUP BY  RC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY RevenueCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I4 = HH.I4,TT.I3 = HH.I3
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateCommune_AII_I1_2_3($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, RevenueCateValue,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0721' AND YearD = $year THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1 THEN I END) AS I3  FROM
            (SELECT  (Case when LEFT(RC.CateNo,5) = '20111' OR RC.CateNo IN ('20112','20113','20114','20115','20116')  THEN 'A#02#01'
                           when RC.CateNo = '201210101' AND  RC.CateValue ='1' THEN 'A#02#02#01'
                           when RC.CateNo = '201210102' AND RC.CateValue IN('1','2')   THEN 'A#02#02#02'
                           when RC.CateNo = '2011104' AND RC.CateValue ='3' THEN  'A#01#03'
                           when RC.CateNo = '2011104' AND  RC.CateValue IN('1','2') THEN 'A#01#04'
                           when RC.CateNo = '201110501' AND  RC.CateValue ='1' THEN 'A#01#05'
                           when RC.CateNo= '201110502' AND  RC.CateValue ='1' THEN 'A#10'
                                             END) as ItemID,
             RC.CateValue as RevenueCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo,A.BudgetLevel as BudgetLevel
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            WHERE left(CC.CateNo,4) = '2001'
            AND A.AccountNo IN('0441','0721','0411')  AND A.BudgetLevel =('4')
            $whereCondition
            GROUP BY  RC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY RevenueCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I4 = HH.I4,TT.I3 = HH.I3
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4, SUM(M.I5) AS I5
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
                        $I5 = ($value['I5']) ? $value['I5'] : 0;
                        $update = "UPDATE table_tmp
                    SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ", I5 = " . $I5 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
