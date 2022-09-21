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

class SBPND312017NDCPMB13Controller extends Controller {
    use Common;
    public function SBPND312017NDCP_MB13(Request $request){

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
          `SbiCategoryNo` varchar(20) DEFAULT '',
          `ConAccountNo` varchar(20) DEFAULT '',
          `ConCoAccountNo` varchar(20) DEFAULT '',
          `SbiCategoryValue` varchar(20) DEFAULT '',
          `ExpenseCateValue` varchar (20) DEFAULT '',
          `SbiCategoryCateValue` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          `I5` double(20,4) DEFAULT NULL,
          `I6` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
            $templateTable = DB::table('report_sbp_312017ndcp_bm13')->get();

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
                'SbiCategoryCateValue' => $template->SbiCategoryCateValue,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
                'I5' => $template->I5,
                'I6' => $template->I6,
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->updateRevenueI1_2_3_4($whereCondition,$year);
        $this->updateRevenue_8I1_2_3_4($whereCondition,$year);

        $this->insertCTST($whereCondition, $year);
        $this->getParentDataTreeForB200('program','revenue_cate_list','revenue');

        // sum up
        $this->sumUpTableTmp(1, 5);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Dự toán năm ' . $year;
            $data[0]->HeaderName2 = 'Ước thực hiện năm ' .$year;
            $data[0]->HeaderName3 = 'So sánh ' ;
        }
        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            if($data[$key]->I1 !=0){
                $data[$key]->I5 = round(((int)($data[$key]->I3) / (int)($data[$key]->I1))*100, 2);
            }$data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';

            if($data[$key]->I2 !=0){
                $data[$key]->I6 = round(((int)($data[$key]->I4) / (int)($data[$key]->I2))*100, 2);
            }$data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
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
                "ReportName" => "ĐÁNH GIÁ THỰC HIỆN THU NGÂN SÁCH NHÀ NƯỚC THEO LĨNH VỰC NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    public function updateRevenueI1_2_3_4($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, RevenueCateValue,
              MAX(CASE WHEN AccountNo = '0341' AND BudgetLevel IN ('1','2','3','4') AND YearD = $year  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0341' AND BudgetLevel IN ('2','3','4') AND YearD = $year THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0711' AND BudgetLevel IN ('1','2','3','4') AND  YearD = $year THEN I END) AS I3,
              MAX(CASE WHEN AccountNo = '0711' AND BudgetLevel IN ('2','3','4') AND YearD = $year THEN I END) AS I4  FROM
            (SELECT  (Case when RC.CateNo = '2011102'  AND  RC.CateValue ='1' THEN 'A#05'
                           when RC.CateNo = '2011103' AND  RC.CateValue ='1' THEN 'A#06#01'
                           when RC.CateNo = '2011103' AND  RC.CateValue ='2'  THEN 'A#06#02'
                           when RC.CateNo = '2011104' AND RC.CateValue ='3' THEN  'A#07'
                           when RC.CateNo = '2011104' AND  RC.CateValue IN('1','2') THEN 'A#08'
                           when RC.CateNo = '201110501' AND  RC.CateValue ='1' THEN 'A#09'
                           when RC.CateNo= '201110502' AND  RC.CateValue ='1' THEN 'A#10'
                            when RC.CateNo= '201110503' AND  RC.CateValue ='1' THEN 'A#11'
                            when RC.CateNo= '201110504' AND  RC.CateValue IN('1','2') THEN 'A#12'
                            when RC.CateNo= '201110507' AND  RC.CateValue ='1' THEN 'A#13'
                            when RC.CateNo= '201110801' AND  RC.CateValue IN('1','2')  THEN 'A#15'
                            when RC.CateNo= '2011107'  THEN 'A#16'
                            when RC.CateNo= '2011111' AND  RC.CateValue ='1' THEN 'A#17'
                            when RC.CateNo IN ('1101620201','1101030301') THEN 'A#18'
                            when RC.CateNo IN ('1101030401','1101030101') THEN 'A#19'
                            when RC.CateNo= '201110902' AND  RC.CateValue ='2' THEN 'A#20'

                            when RC.CateNo= '20112' THEN 'B'
                            when RC.CateNo= '20113' THEN 'C'
                            when RC.CateNo= '20113' AND  RC.CateValue ='5' THEN 'C#01'
                            when RC.CateNo= '20113' AND  RC.CateValue ='1' THEN 'C#02'
                            when RC.CateNo= '20113' AND  RC.CateValue in ('2','6') THEN 'C#03'
                            when RC.CateNo= '20113' AND  RC.CateValue ='3' THEN 'C#04'
                            when RC.CateNo= '20113' AND  RC.CateValue ='4' THEN 'C#05'
                            when RC.CateNo= '20113' AND  RC.CateValue ='99' THEN 'C#06'
                            when RC.CateNo= '20114'THEN 'D'
                             END) as ItemID,
             RC.CateValue as RevenueCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo,A.BudgetLevel as BudgetLevel
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
            WHERE (RC.CateNo IN ('202110201','202110202','201210101','201210102','20124','20123','20122') OR RC.CateValue IN ('1','2') )
            AND A.AccountNo IN('0341','0711')
            $whereCondition
            GROUP BY  RC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY RevenueCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I4 = HH.I4,TT.I3 = HH.I3
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function updateRevenue_8I1_2_3_4($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, RevenueCateValue,
              MAX(CASE WHEN AccountNo = '0341' AND YearD = $year  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0341' AND YearD = $year THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0711' AND YearD = $year THEN I END) AS I3,
              MAX(CASE WHEN AccountNo = '0711' AND YearD = $year THEN I END) AS I4  FROM
            (SELECT  (Case when A.BudgetLevel= '1' THEN 'A#08#01'
                           when A.BudgetLevel= '2' THEN 'A#08#02'
                           when A.BudgetLevel='3' THEN 'A#08#03'
                           when A.BudgetLevel='4' THEN 'A#08#04'
                             END) as ItemID,
             RC.CateValue as RevenueCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo,A.BudgetLevel as BudgetLevel
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
            WHERE RC.CateNo ='2011104' and RC.CateValue IN ('1','2')
            AND A.AccountNo IN('0341','0711')
            $whereCondition
            GROUP BY  RC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY RevenueCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }

    public function insertCTST($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, SbiCategoryCateValue,
              MAX(CASE WHEN AccountNo = '0341' AND BudgetLevel IN ('1','2','3','4') AND YearD = $year  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0341' AND BudgetLevel IN ('2','3','4') AND YearD = $year THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0711' AND BudgetLevel IN ('1','2','3','4') AND  YearD = $year THEN I END) AS I3,
              MAX(CASE WHEN AccountNo = '0711' AND BudgetLevel IN ('2','3','4') AND YearD = $year THEN I END) AS I4  FROM
            (SELECT  (Case when A.BudgetLevel= '1' THEN 'A#08#01'
                           when A.BudgetLevel= '2' THEN 'A#08#02'
                           when A.BudgetLevel='3' THEN 'A#08#03'
                           when A.BudgetLevel='4' THEN 'A#08#04'
                             END) as ItemID,
             RC.CateValue as RevenueCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo,A.BudgetLevel as BudgetLevel,SBC.CateValue as SbiCategoryCateValue
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
            INNER JOIN sbi_category_cate as SBC on SBC.SbiCategoryID = A.SbiCategoryID
            WHERE left(RC.CateNo,7) ='2011101' and SBC.CateNo='002'
            AND A.AccountNo IN('0341','0711')
            $whereCondition
            GROUP BY  SBC.CateValue, YearD, A.AccountNo,ItemID) as CH GROUP BY SbiCategoryCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
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
