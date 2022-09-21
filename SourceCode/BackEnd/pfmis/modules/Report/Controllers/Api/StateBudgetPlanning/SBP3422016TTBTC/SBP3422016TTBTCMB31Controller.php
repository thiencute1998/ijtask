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

class SBP3422016TTBTCMB31Controller extends Controller {
    public function SBP3422016TTBTC_MB31(Request $request){

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


        $yearNext = (int)$year + 1;

        $whereCondition = "AND (
        (YEAR(A.PostDate) = $year AND YEAR(A.PostDate) = $year)
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
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          `I5` double(20,4) DEFAULT NULL,
          `I6` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb31')->get();

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
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
                'I5' => $template->I5,
                'I6' => $template->I6
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->AI1($whereCondition, $year);
        $this->AI2($whereCondition, $year);
        $this->AI3($whereCondition, $year);
        $this->AI4($whereCondition, $year);
        $this->AI5_6_7_8_9_10($whereCondition, $year);
        $this->AI11_12_13_14_15($whereCondition, $year);
        $this->AI16_17_18_19_20_21($whereCondition, $year);
        $this->AII_III($whereCondition, $year);

        // sum up
        $this->sumUpTableTmp(1, 5);
        $item1 = DB::table('table_tmp')->where('ItemID', '=', 'A#01')->first();
        $item2 = DB::table('table_tmp')->where('ItemID', '=', 'A#02')->first();
        DB::table('table_tmp')->where('ItemID','=','A')
            ->update(
                [   'I1'=>$item1->I1- $item2->I1,
                    'I2'=>$item1->I2- $item2->I2,
                    'I3'=>$item1->I3- $item2->I3,
                ]
            );
        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->whereNotIn('ItemID',['B#01','B#02'])->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Năm ' . $year;
            $data[0]->HeaderName2 = 'Dự toán năm ' .$yearNext;
        }

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
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
    public function  AI1($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                                MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                                MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard=  $year -1 THEN I END) AS I5
                             FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,YEAR(A.PeriodFromDate) AS YearD,
                                   Case when (RC.CateNo='2011101' AND RC.CateValue='1') THEN 'A#01#01#01'
                                          when RC.CateNo IN('1101090201','1101792301') THEN 'A#01#01#01#01'
                                          when (RC.CateNo='201110102' AND RC.CateValue='1') THEN 'A#01#01#02'
                                          when (RC.CateNo='201110103' AND RC.CateValue IN('1','2')) THEN 'A#01#01#03'
                                          when (RC.CateNo='201110103' AND RC.CateValue ='1') THEN 'A#01#01#03#01'
                                          when RC.CateNo='201110104'  THEN 'A#01#01#04'
                                          when RC.CateNo IN('1101070101','1101070901','1101071101','1101791801','1101791901')  THEN 'A#01#01#04#01' END as ItemID
                            FROM act_gl_books AS A
                                     INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
                                     INNER JOIN sbi_category_cate as SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                            WHERE ((RC.CateNo='2011101' AND RC.CateValue='1')
                                OR RC.CateNo IN('1101090201','1101792301')
                                OR (RC.CateNo='201110102' AND RC.CateValue='1')
                                OR (RC.CateNo='201110103' AND RC.CateValue IN('1','2'))
                                OR (RC.CateNo='201110103' AND RC.CateValue ='1')
                                OR  RC.CateNo='201110104'
                                OR RC.CateNo IN('1101070101','1101070901','1101071101','110179 1801','1101791901'))
                            AND A.AccountNo IN('0341','0711','0311') AND SCC.CateNo='002' AND SCC.CateValue='1'
                            $whereCondition
                          Group BY ItemID, A.AccountNo, YearD
                        ) AS B Group BY ItemID
                        ) AS HH
                       SET TT.I1 = HH.I1,TT.I3 = HH.I3,TT.I5 = HH.I5
                            WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function  AI2($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                                MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                                MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $year -1 THEN I END) AS I5
                             FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,YEAR(A.PeriodFromDate) AS YearD,
                                   Case  when (RC.CateNo='2011101' AND RC.CateValue='1') THEN 'A#01#02#01'
                                          when( RC.CateNo='201110102' AND RC.CateValue='1') THEN 'A#01#02#02'
                                          when (RC.CateNo='201110103' AND RC.CateValue IN('1','2')) THEN 'A#01#02#03'
                                          when (RC.CateNo='201110103' AND RC.CateValue ='1') THEN 'A#01#02#03#01'
                                          when RC.CateNo='201110104'  THEN 'A#01#02#04' END as ItemID
                            FROM act_gl_books AS A
                                     INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
                                     INNER JOIN sbi_category_cate as SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                            WHERE (( RC.CateNo='2011101' AND RC.CateValue='1')
                                    OR (RC.CateNo='201110102' AND RC.CateValue='1')
                                    OR (RC.CateNo='201110103' AND RC.CateValue IN('1','2'))
                                    OR (RC.CateNo='201110103' AND RC.CateValue ='1')
                                    OR RC.CateNo='201110104')
                            AND A.AccountNo IN('0341','0711','0311') AND SCC.CateNo='002' AND SCC.CateValue='2'
                            $whereCondition
                        Group BY ItemID, A.AccountNo, YearD
                        ) AS B Group BY ItemID
                        ) AS HH
                       SET TT.I1 = HH.I1,TT.I3 = HH.I3,TT.I5 = HH.I5
                            WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function  AI3($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                                MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                                MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $year -1 THEN I END) AS I5
                             FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,YEAR(A.PeriodFromDate) AS YearD,
                                   (Case  when (RC.CateNo='2011101' AND RC.CateValue='1') THEN 'A#01#03#01'
                                          when RC.CateNo IN ('1101090201','1101792301') THEN 'A#01#03#01#01'
                                          when (RC.CateNo='201110102' AND RC.CateValue='1') THEN 'A#01#03#02'
                                          when RC.CateNo IN ('11020401','190110201') THEN 'A#01#03#02#01'
                                          when RC.CateNo='201110105'  THEN 'A#01#03#03'
                                          when (RC.CateNo='201110103' AND RC.CateValue IN('1','2')) THEN 'A#01#03#04'
                                          when (RC.CateNo='201110103' AND RC.CateValue='1') THEN 'A#01#03#04#01'
                                          when RC.CateNo='201110104'  THEN 'A#01#03#05'
                                          when RC.CateNo IN('1101070101','1101070901','1101071101','1101791801','1101791901')  THEN 'A#01#03#05#01'
                                          when RC.CateNo ='201110106'  THEN 'A#01#03#06'
                                          when RC.CateNo ='1101610301'  THEN 'A#01#03#06#01'
                                       END) as ItemID
                            FROM act_gl_books AS A
                                    INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
                                     INNER JOIN sbi_category_cate as SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                            WHERE ((RC.CateNo='2011101' AND RC.CateValue='1')
                                   OR (RC.CateNo IN ('1101090201','1101792301'))
                                   OR (RC.CateNo='201110102' AND RC.CateValue='1')
                                   OR (RC.CateNo IN ('11020401','190110201'))
                                   OR RC.CateNo='201110105'
                                   OR (RC.CateNo='201110103' AND RC.CateValue IN('1','2'))
                                   OR (RC.CateNo='201110103' AND RC.CateValue='1')
                                   OR RC.CateNo='201110104'
                                   OR RC.CateNo IN('1101070101','1101070901','1101071101','1101791801','1101791901')
                                   OR RC.CateNo ='201110106'
                                   OR RC.CateNo ='1101610301')
                            AND A.AccountNo IN('0341','0711','0311') AND SCC.CateNo='002'
                            $whereCondition
                        Group BY ItemID, A.AccountNo, YearD
                        ) AS B Group BY ItemID
                        ) AS HH
                       SET TT.I1 = HH.I1,TT.I3 = HH.I3,TT.I5 = HH.I5
                            WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function  AI4($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                                MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                                MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $year -1 THEN I END) AS I5
                             FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,YEAR(A.PeriodFromDate) AS YearD,
                                   (Case  when (RC.CateNo='2011101' AND RC.CateValue='1') THEN 'A#01#04#01'
                                          when (RC.CateNo='201110102' AND RC.CateValue='1') THEN 'A#01#04#02'
                                          when( RC.CateNo='201110103' AND RC.CateValue IN('1','2')) THEN 'A#01#04#03'
                                          when (RC.CateNo='201110103' AND RC.CateValue='1') THEN 'A#01#04#03#01'
                                          when RC.CateNo='201110104'  THEN 'A#01#04#04'
                                       END) as ItemID
                            FROM act_gl_books AS A
                                    INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
                                     INNER JOIN sbi_category_cate as SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                            WHERE ((RC.CateNo='2011101' AND RC.CateValue='1')
                                   OR (RC.CateNo='201110102' AND RC.CateValue='1')
                                   OR (RC.CateNo='201110103' AND RC.CateValue IN('1','2'))
                                   OR (RC.CateNo='201110103' AND RC.CateValue='1')
                                   OR RC.CateNo='201110104')
                            AND A.AccountNo IN('0341','0711','0311') AND SCC.CateNo='002'
                            $whereCondition
                        Group BY ItemID, A.AccountNo, YearD
                        ) AS B Group BY ItemID
                        ) AS HH
                       SET TT.I1 = HH.I1,TT.I3 = HH.I3,TT.I5 = HH.I5
                            WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function  AI5_6_7_8_9_10($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                                MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                                MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $year -1 THEN I END) AS I5
                             FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,YEAR(A.PeriodFromDate) AS YearD,
                                   (Case  when (RC.CateNo='2011104' AND RC.CateValue='3') THEN 'A#01#05'
                                          when RC.CateNo='201110501' THEN 'A#01#06'
                                          when RC.CateNo='201110502' THEN 'A#01#07'
                                          when(RC.CateNo='2011102' AND RC.CateValue='1') THEN 'A#01#08'
                                          when (RC.CateNo='2011103' AND RC.CateValue IN('1','2'))  THEN 'A#01#09'
                                          when (RC.CateNo='2011103' AND RC.CateValue ='1')  THEN 'A#01#09#01'
                                          when (RC.CateNo='2011103' AND RC.CateValue ='2')  THEN 'A#01#09#02'
                                          when (RC.CateNo='2011104' AND RC.CateValue IN ('1','2') AND A.BudgetLevel=1) THEN 'A#01#10#01'
                                          when (RC.CateNo='2011104' AND RC.CateValue IN ('1','2') AND A.BudgetLevel IN(2,3,4)) THEN 'A#01#10#02'
                                          when (RC.CateNo IN('11013202','11013203') AND A.BudgetLevel =4) THEN 'A#01#10#03'
                                       END) as ItemID
                            FROM act_gl_books AS A
                                    INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
                                     INNER JOIN sbi_category_cate as SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                            WHERE ((RC.CateNo='2011104' AND RC.CateValue='3')
                                    OR (RC.CateNo='201110501')
                                    OR (RC.CateNo='201110502')
                                    OR (RC.CateNo='2011102' AND RC.CateValue='1')
                                    OR (RC.CateNo='2011103' AND RC.CateValue IN('1','2'))
                                    OR (RC.CateNo='2011103' AND RC.CateValue ='1')
                                    OR (RC.CateNo='2011103' AND RC.CateValue ='2')
                                    OR (RC.CateNo='2011104' AND RC.CateValue IN ('1','2') AND A.BudgetLevel=1)
                                    OR (RC.CateNo='2011104' AND RC.CateValue IN ('1','2') AND A.BudgetLevel IN(2,3,4))
                                    OR (RC.CateNo IN('11013202','11013203') AND A.BudgetLevel =4))
                            AND A.AccountNo IN('0341','0711','0311')
                            $whereCondition
                       Group BY ItemID, A.AccountNo, YearD
                        ) AS B Group BY ItemID
                        ) AS HH
                       SET TT.I1 = HH.I1,TT.I3 = HH.I3,TT.I5 = HH.I5
                            WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function  AI11_12_13_14_15($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                                MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                                MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $year -1 THEN I END) AS I5
                             FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,YEAR(A.PeriodFromDate) AS YearD,
                                   (Case  when (RC.CateNo='201110504' AND RC.CateValue IN('1','2')) THEN 'A#01#11'
                                          when (RC.CateNo='201110504' AND RC.CateValue ='1') THEN 'A#01#11#01'
                                          when (RC.CateNo='201110504' AND RC.CateValue ='1') THEN 'A#01#11#01'
                                          when (RC.CateNo='201110503') THEN 'A#01#12'
                                          when (RC.CateNo='201110505' AND RC.CateValue IN('1','2')) THEN 'A#01#13'
                                          when (RC.CateNo='201110505' AND RC.CateValue ='1') THEN 'A#01#13#01'
                                          when (RC.CateNo='201110505' AND RC.CateValue ='2') THEN 'A#01#13#02'
                                          when (RC.CateNo='201110506' AND RC.CateValue IN('1','2')) THEN 'A#01#14'
                                          when (RC.CateNo='201110506' AND RC.CateValue ='1') THEN 'A#01#14#01'
                                          when (RC.CateNo='201110506' AND RC.CateValue ='2') THEN 'A#01#14#02'
                                          when (RC.CateNo='201110508' AND RC.CateValue IN('1','2')) THEN 'A#01#15'
                                          when (RC.CateNo='201110508' AND RC.CateValue ='1') THEN 'A#01#15#01'
                                          when (RC.CateNo='201110508' AND RC.CateValue ='2') THEN 'A#01#15#02'
                                       END) as ItemID
                            FROM act_gl_books AS A
                                    INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
                                     INNER JOIN sbi_category_cate as SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                            WHERE ((RC.CateNo='201110504' AND RC.CateValue IN('1','2'))
                                        OR (RC.CateNo='201110504' AND RC.CateValue ='1')
                                        OR (RC.CateNo='201110504' AND RC.CateValue ='1')
                                        OR (RC.CateNo='201110503')
                                        OR (RC.CateNo='201110505' AND RC.CateValue IN('1','2'))
                                        OR (RC.CateNo='201110505' AND RC.CateValue ='1')
                                        OR (RC.CateNo='201110505' AND RC.CateValue ='2')
                                        OR (RC.CateNo='201110506' AND RC.CateValue IN('1','2'))
                                        OR (RC.CateNo='201110506' AND RC.CateValue ='1')
                                        OR (RC.CateNo='201110506' AND RC.CateValue ='2')
                                        OR (RC.CateNo='201110508' AND RC.CateValue IN('1','2'))
                                        OR (RC.CateNo='201110508' AND RC.CateValue ='1')
                                        OR (RC.CateNo='201110508' AND RC.CateValue ='2') )
                            AND A.AccountNo IN('0341','0711','0311')
                            $whereCondition
                        Group BY ItemID, A.AccountNo, YearD
                        ) AS B Group BY ItemID
                        ) AS HH
                       SET TT.I1 = HH.I1,TT.I3 = HH.I3,TT.I5 = HH.I5
                            WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function  AI16_17_18_19_20_21($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                                MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                                MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $year -1 THEN I END) AS I5
                             FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,YEAR(A.PeriodFromDate) AS YearD,
                                   (Case  when (RC.CateNo='201110507') THEN 'A#01#16'
                                          when (RC.CateNo='2011107' AND RC.CateValue IN('1','2','3','4','99')) THEN 'A#01#17'
                                          when (RC.CateNo='201110801' AND RC.CateValue IN('1','2')) THEN 'A#01#18'
                                          when (RC.CateNo='201110801' AND RC.CateValue ='1') THEN 'A#01#18#01'
                                          when (RC.CateNo='201110801' AND RC.CateValue ='2') THEN 'A#01#18#02'
                                          when RC.CateNo='2011108' THEN 'A#01#19'
                                          when (RC.CateNo='201110901' AND RC.CateValue='2') THEN 'A#01#20'
                                          when (RC.CateNo='2011106' AND RC.CateValue IN('1','2','3','4','99')) THEN 'A#01#21'
                                       END) as ItemID
                            FROM act_gl_books AS A
                                    INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
                                     INNER JOIN sbi_category_cate as SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                            WHERE    ((RC.CateNo='201110507')
                                      OR (RC.CateNo='2011107' AND RC.CateValue IN('1','2','3','4','99'))
                                      OR (RC.CateNo='201110801' AND RC.CateValue IN('1','2'))
                                      OR (RC.CateNo='201110801' AND RC.CateValue ='1')
                                      OR (RC.CateNo='201110801' AND RC.CateValue ='2')
                                      OR RC.CateNo='2011108'
                                      OR (RC.CateNo='201110901' AND RC.CateValue='2')
                                      OR (RC.CateNo='2011106' AND RC.CateValue IN('1','2','3','4','99')))
                            AND A.AccountNo IN('0341','0711','0311')
                            $whereCondition
 Group BY ItemID, A.AccountNo, YearD
                        ) AS B Group BY ItemID
                        ) AS HH
                       SET TT.I1 = HH.I1,TT.I3 = HH.I3,TT.I5 = HH.I5
                            WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function  AII_III($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                                MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                                MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $year -1 THEN I END) AS I5
                             FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,YEAR(A.PeriodFromDate) AS YearD,
                                   (Case  when RC.CateNo='20112' THEN 'A#02'
                                          when (RC.CateNo='20113' AND RC.CateValue ='1') THEN 'A#03#01'
                                          when (RC.CateNo='20113' AND RC.CateValue ='2') THEN 'A#03#02'
                                          when (RC.CateNo='20113' AND RC.CateValue ='3') THEN 'A#03#03'
                                          when (RC.CateNo='20113' AND RC.CateValue ='4') THEN 'A#03#04'
                                          when (RC.CateNo='20113' AND RC.CateValue ='5') THEN 'A#03#05'
                                       END) as ItemID
                            FROM act_gl_books AS A
                                    INNER JOIN revenue_cate as RC on RC.RevenueID = A.RevenueID
                                     INNER JOIN sbi_category_cate as SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                          WHERE  (RC.CateNo='20112'
                                  OR (RC.CateNo='20113' AND RC.CateValue ='1')
                                  OR (RC.CateNo='20113' AND RC.CateValue ='2')
                                  OR (RC.CateNo='20113' AND RC.CateValue ='3')
                                  OR (RC.CateNo='20113' AND RC.CateValue ='4')
                                  OR (RC.CateNo='20113' AND RC.CateValue ='5') )
                            AND A.AccountNo IN('0341','0711','0311')
                            $whereCondition
                         Group BY ItemID, A.AccountNo, YearD
                        ) AS B Group BY ItemID
                        ) AS HH
                       SET TT.I1 = HH.I1,TT.I3 = HH.I3,TT.I5 = HH.I5
                            WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function makeTree(){
        $listChildNull = DB::table('table_tmp')->wherNull('I1')->wherNull('I2')->wherNull('I3')->get();
        if( count($listChildNull) > 0 ){
            foreach ($listChildNull as $childNull){
                $listChild = DB::table('table_tmp')-> where('ParentID', $childNull->ParentID)
                    ->whereNotNull('I1')->orWhereNotNull('I2')->orWhereNotNull('I3')->get()->toArray();
                $parent =  DB::table('table_tmp')->find($childNull->ParentID);
                $i = 1;
                foreach ($listChild as $child){
                    if($parent->treeType == 'Roman'){
                        if($parent->Index =1 ){
                            $stt = IjNumber::ConvertNumberToRoman($i);

                        } else {
                            $stt = $parent->STT.IjNumber::ConvertNumberToRoman($i);
                        }

                    }
                    if($parent->treeType == 'Alpha'){
                        if($parent->Index =1 ){
                            $stt = IjNumber::ConvertNumberToAlpha($i);

                        } else {
                            $stt = $parent->STT.IjNumber::ConvertNumberToAlpha($i);
                        }
                    }
                    if($parent->treeType == 'AlphaUpcase'){
                        if($parent->Index =1 ){
                            $stt = IjNumber::ConvertNumberToAlphaUpCase($i);

                        } else {
                            $stt = $parent->STT.'.'.IjNumber::ConvertNumberToAlphaUpCase($i);
                        }
                    }
                    if(!$parent->treType){
                        if ($parent->index = 1){
                            $stt = $i;
                        } else {
                            $stt = $parent->STT.'.'.$i;
                        }
                    }
                    DB::table('table_tmp')->where('TableID', $child->TableID)
                        ->update(['STT', $stt]);
                }
            }
        }

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
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3
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
                        $update = "UPDATE table_tmp
                    SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }



}
