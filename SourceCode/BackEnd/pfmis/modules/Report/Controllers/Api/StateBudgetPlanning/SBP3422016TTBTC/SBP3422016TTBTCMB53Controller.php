<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use IjCore\IjNumber;
use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\CoaCon;
use Module\Listing\Models\Company;
use Module\Listing\Models\Item;
use Module\Report\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class SBP3422016TTBTCMB53Controller extends Controller {
    public function SBP3422016TTBTC_MB53(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $filter = $request->post('filter');
        $companyID = (isset($filter['CompanyID'])) ? $filter['CompanyID'] : null;
        $conAccountNo = (isset($filter['ConAccountNo'])) ? $filter['ConAccountNo'] : null;
        $monthFilter = (isset($filter['Month'])) ? $filter['Month'] : null;
        $year = (isset($filter['Year'])) ? $filter['Year'] : null;
        if (!$monthFilter || !$year) {
            $json['status'] = 0;
            $json['msg'] = 'Chưa chọn tháng năm';
            return json_encode($json);
        }
        $companyID = $filter['CompanyID'];
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
        (YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodFromDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $yearPre AND YEAR(A.PeriodFromDate) = $yearPre)
        OR (YEAR(A.PeriodFromDate) = $yearNext AND YEAR(A.PeriodFromDate) = $yearNext)
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
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb53')->get();

        $insertArr = [];
        foreach ($templateTable as $template) {
            $insertArr[] = [
                'TableID' => $template->TableID,
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
        $this->I1_AI12345689101112($whereCondition,$year);
        $this->I1_AI7($whereCondition, $year);
        $this->I1_AII_III($whereCondition, $year);
        $this->I2_AI12345689101112($whereCondition,$monthFilter, $year);
        $this->I2_AI7($whereCondition,$monthFilter, $year);
        $this->I2_AII_III($whereCondition,$monthFilter, $year);
        $this->I35_AI12345689101112($whereCondition,$monthFilter, $year);
        $this->I35_AI7($whereCondition,$monthFilter, $year);
        $this->I35_AII_III($whereCondition,$monthFilter, $year);
        $this->sumUpTableTmp(0);
        $date = date('d');
        $month = $monthFilter;
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Tháng ' . $month;
            $data[0]->HeaderName2 = 'Lũy kế tháng ' . $month;
            $data[0]->HeaderName3 = 'ƯỚC '.$month. ' THÁNG SO (%)';
            $data[0]->HeaderName4 = 'CÙNG KỲ NĂM ' .$yearNext;
        }
        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 =($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 =($data[$key]->I6) ? $data[$key]->I6 : '';
        }

        $company = Company::find($companyID);
        $dateTime = "Vĩnh Phú, ngày $date tháng $month năm $year";
        if ($company) {
            $dateTime = $company->Address . ", ngày $date tháng $month năm $year";
        }

        $response = [
            'master' => [
                "Year" => "",
                "CreateName" => "Trần Thế Thái",
                "TransNo" => "00001",
                "ReportName" => "TÌNH HÌNH CÂN ĐỐI NSNN THÁNG ". $month. " NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'BỘ TÀI CHÍNH',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    public function I1_AI12345689101112($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(
                        SELECT SUM(FCDebitAmount) AS I,
                          Case when (LEFT(RC.CateNo,7)='2011101' AND(SCC.CateNo='002' AND SCC.CateValue IN ('1','2'))) THEN 'A#01#01'
                                when (LEFT(RC.CateNo,7)='2011101' AND SCC.CateNo='003') THEN 'A#01#02'
                                when (LEFT(RC.CateNo,7)='2011101' AND SCC.CateNo='004') THEN 'A#01#03'
                                when (RC.CateNo = '2011102' AND RC.CateValue='1') THEN 'A#04'
                                when (RC.CateNo = '2011103' AND RC.CateValue IN('1','2')) THEN 'A#05'
                                when RC.CateNo = '2011104' THEN 'A#06'
                                when (RC.CateNo = '2011104' AND RC.CateValue='3') THEN 'A#06#01'
                                when RC.CateNo = '2011106' THEN 'A#08'
                                when (RC.CateNo = '201110801' AND RC.CateValue='1') THEN 'A#09'
                                when RC.CateNo = '2011107' THEN 'A#10'
                                when (RC.CateNo = '2011111' AND RC.CateValue='1') THEN 'A#11'
                                when (RC.CateNo = '201110901' AND RC.CateValue IN('1','2')) OR (RC.CateNo='201110902' AND RC.CateValue='1') THEN 'A#12' END as ItemID
                        FROM act_gl_books AS A
                                     INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                    INNER JOIN sbi_chapter_cate AS SCC ON SCC.SbiChapterID = A.SbiChapterID
                        WHERE ((LEFT(RC.CateNo,7)='2011101' AND(SCC.CateNo='002' AND SCC.CateValue IN ('1','2')))
                                    OR (LEFT(RC.CateNo,7)='2011101' AND SCC.CateNo='003')
                                    OR (LEFT(RC.CateNo,7)='2011101' AND SCC.CateNo='004')
                                    OR (RC.CateNo = '2011102' AND RC.CateValue='1')
                                    OR (RC.CateNo = '2011103' AND RC.CateValue IN('1','2'))
                                    OR RC.CateNo = '2011104'
                                    OR (RC.CateNo = '2011104' AND RC.CateValue='3')
                                    OR RC.CateNo = '2011106'
                                    OR (RC.CateNo = '201110801' AND RC.CateValue='1')
                                    OR RC.CateNo = '2011107'
                                    OR (RC.CateNo = '2011111' AND RC.CateValue='1')
                                    OR (RC.CateNo = '201110901' AND RC.CateValue IN('1','2')) OR (RC.CateNo='201110902' AND RC.CateValue='1'))
                          AND A.AccountNo = '0341'
                          AND YEAR(A.PeriodFromDate) = $year
                            $whereCondition
                    ) AS HH
                    SET TT.I2 = HH.I
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function I1_AI7($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(
                        SELECT SUM(FCDebitAmount) AS I,
                          Case when (RC.CateNo='201110501' AND CateValue='1') THEN 'A#01#07#01'
                                when (RC.CateNo='201110502' AND CateValue='1') THEN 'A#01#07#02'
                                when (RC.CateNo='201110503' AND CateValue='1') THEN 'A#01#07#03'
                                when (RC.CateNo='201110504' AND CateValue IN ('1','2')) THEN 'A#01#07#04'
                                when (RC.CateNo='201110507' AND CateValue = '1') THEN 'A#01#07#05' END as ItemID
                        FROM act_gl_books AS A
                                     INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                        WHERE ((RC.CateNo='201110501' AND CateValue='1')
                                    OR (RC.CateNo='201110501' AND CateValue='1')
                                    OR (RC.CateNo='201110503' AND CateValue='1')
                                    OR (RC.CateNo='201110504' AND CateValue IN ('1','2'))
                                    OR (RC.CateNo='201110507' AND CateValue = '1'))
                          AND A.AccountNo = '0341'
                          AND YEAR(A.PeriodFromDate) = $year
                            $whereCondition
                    ) AS HH
                    SET TT.I1 = HH.I
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function I1_AII_III($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(
                        SELECT SUM(FCDebitAmount) AS I,
                             Case when (RC.CateNo='20112' AND CateValue='1') THEN 'A#02'
                                when (RC.CateNo='20113' AND CateValue='5') THEN 'A#03#01#01'
                                when (RC.CateNo='20113' AND CateValue='1') THEN 'A#03#01#02'
                                when (RC.CateNo='20113' AND CateValue IN('2','6')) THEN 'A#03#01#03'
                                when (RC.CateNo='20113' AND CateValue = '3') THEN 'A#03#01#04'
                                when (RC.CateNo='20113' AND CateValue = '4') THEN 'A#03#01#05'
                                when (RC.CateNo='20113' AND CateValue = '99') THEN 'A#03#01#06'
                                when LEFT(RC.CateNo,5)='5106' THEN 'A#03#02' END as ItemID
                        FROM act_gl_books AS A
                                     INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                        WHERE ((RC.CateNo='201110501' AND CateValue='1')
                                    OR (RC.CateNo='201110501' AND CateValue='1')
                                    OR (RC.CateNo='201110503' AND CateValue='1')
                                    OR (RC.CateNo='201110504' AND CateValue IN ('1','2'))
                                    OR (RC.CateNo='201110507' AND CateValue = '1')
                                    OR (LEFT(RC.CateNo,5)='5106'))
                          AND A.AccountNo = '0341'
                          AND YEAR(A.PeriodFromDate) = $year
                            $whereCondition
                    ) AS HH
                    SET TT.I1 = HH.I
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function I2_AI7($whereCondition, $month, $year)
    {
        $preYear = $year - 1;
        $fromDateArr = $year . '-' . $month . '-01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '-' . $month . '-31';
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '-01-01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '-01-01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '-' . $month . '-31';
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                            MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = 'Year2' THEN I END) AS I2
                        FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,
                           Case when ( A.PeriodFromDate >= '$fromDate' AND A.PeriodFromDate <= '$toDate' ) THEN 'Year2' END as YearD,
                           Case when (RC.CateNo='201110501' AND CateValue='1') THEN 'A#01#07#01'
                                when (RC.CateNo='201110502' AND CateValue='1') THEN 'A#01#07#02'
                                when (RC.CateNo='201110503' AND CateValue='1') THEN 'A#01#07#03'
                                when (RC.CateNo='201110504' AND CateValue IN ('1','2')) THEN 'A#01#07#04'
                                when (RC.CateNo='201110507' AND CateValue = '1') THEN 'A#01#07#05' END as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                WHERE A.AccountNo IN ('0711')
                                AND ((RC.CateNo='201110501' AND CateValue='1')
                                    OR (RC.CateNo='201110501' AND CateValue='1')
                                    OR (RC.CateNo='201110503' AND CateValue='1')
                                    OR (RC.CateNo='201110504' AND CateValue IN ('1','2'))
                                    OR (RC.CateNo='201110507' AND CateValue = '1'))
                              $whereCondition
                              GROUP BY RC.Cateno, A.AccountNo, YearD
                             ) AS B Group BY ItemID
                        ) AS HH
                        SET  TT.I2 = HH.I2
                        WHERE  TT.ItemID = HH.ItemID ";
        DB::statement($sqlUpdate);
    }
    public function I2_AI12345689101112($whereCondition, $month, $year)
    {
        $preYear = $year - 1;
        $fromDateArr = $year . '-' . $month . '-01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '-' . $month . '-31';
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '-01-01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '-01-01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '-' . $month . '-31';
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                            MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = 'Year2' THEN I END) AS I2
                        FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,
                           Case when ( A.PeriodFromDate >= '$fromDate' AND A.PeriodFromDate <= '$toDate' ) THEN 'Year2' END as YearD,
                           Case when (LEFT(RC.CateNo,7)='2011101' AND(SCC.CateNo='002' AND SCC.CateValue IN ('1','2'))) THEN 'A#01#01'
                                when (LEFT(RC.CateNo,7)='2011101' AND SCC.CateNo='003') THEN 'A#01#02'
                                when (LEFT(RC.CateNo,7)='2011101' AND SCC.CateNo='004') THEN 'A#01#03'
                                when (RC.CateNo = '2011102' AND RC.CateValue='1') THEN 'A#04'
                                when (RC.CateNo = '2011103' AND RC.CateValue IN('1','2')) THEN 'A#05'
                                when RC.CateNo = '2011104' THEN 'A#06'
                                when (RC.CateNo = '2011104' AND RC.CateValue='3') THEN 'A#06#01'
                                when RC.CateNo = '2011106' THEN 'A#08'
                                when (RC.CateNo = '201110801' AND RC.CateValue='1') THEN 'A#09'
                                when RC.CateNo = '2011107' THEN 'A#10'
                                when (RC.CateNo = '2011111' AND RC.CateValue='1') THEN 'A#11'
                                when (RC.CateNo = '201110901' AND RC.CateValue IN('1','2')) OR (RC.CateNo='201110902' AND RC.CateValue='1') THEN 'A#12' END as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN sbi_chapter_cate AS SCC ON SCC.SbiChapterID = A.SbiChapterID
                                WHERE A.AccountNo IN ('0711')
                                AND((LEFT(RC.CateNo,7)='2011101' AND(SCC.CateNo='002' AND SCC.CateValue IN ('1','2')))
                                    OR (LEFT(RC.CateNo,7)='2011101' AND SCC.CateNo='003')
                                    OR (LEFT(RC.CateNo,7)='2011101' AND SCC.CateNo='004')
                                    OR (RC.CateNo = '2011102' AND RC.CateValue='1')
                                    OR (RC.CateNo = '2011103' AND RC.CateValue IN('1','2'))
                                    OR RC.CateNo = '2011104'
                                    OR (RC.CateNo = '2011104' AND RC.CateValue='3')
                                    OR RC.CateNo = '2011106'
                                    OR (RC.CateNo = '201110801' AND RC.CateValue='1')
                                    OR RC.CateNo = '2011107'
                                    OR (RC.CateNo = '2011111' AND RC.CateValue='1')
                                    OR (RC.CateNo = '201110901' AND RC.CateValue IN('1','2')) OR (RC.CateNo='201110902' AND RC.CateValue='1'))
                              $whereCondition
                              GROUP BY RC.CateNo, A.AccountNo, YearD
                             ) AS B Group BY ItemID
                        ) AS HH
                        SET  TT.I2 = HH.I2
                        WHERE  TT.ItemID = HH.ItemID ";
        DB::statement($sqlUpdate);
    }
    public function I2_AII_III($whereCondition, $month, $year)
    {
        $preYear = $year - 1;
        $fromDateArr = $year . '-' . $month . '-01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '-' . $month . '-31';
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '-01-01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '-01-01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '-' . $month . '-31';
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                            MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = 'Year2' THEN I END) AS I2
                        FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,
                           Case when ( A.PeriodFromDate >= '$fromDate' AND A.PeriodFromDate <= '$toDate' ) THEN 'Year2' END as YearD,
                           Case when (RC.CateNo='20112' AND CateValue='1') THEN 'A#02'
                                when (RC.CateNo='20113' AND CateValue='5') THEN 'A#03#01#01'
                                when (RC.CateNo='20113' AND CateValue='1') THEN 'A#03#01#02'
                                when (RC.CateNo='20113' AND CateValue IN('2','6')) THEN 'A#03#01#03'
                                when (RC.CateNo='20113' AND CateValue = '3') THEN 'A#03#01#04'
                                when (RC.CateNo='20113' AND CateValue = '4') THEN 'A#03#01#05'
                                when (RC.CateNo='20113' AND CateValue = '99') THEN 'A#03#01#06'
                                when LEFT(RC.CateNo,5)='5106' THEN 'A#03#02' END as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                WHERE A.AccountNo IN ('0711')
                                AND ((RC.CateNo='201110501' AND CateValue='1')
                                    OR (RC.CateNo='201110501' AND CateValue='1')
                                    OR (RC.CateNo='201110503' AND CateValue='1')
                                    OR (RC.CateNo='201110504' AND CateValue IN ('1','2'))
                                    OR (RC.CateNo='201110507' AND CateValue = '1')
                                    OR (LEFT(RC.CateNo,5)='5106'))
                              $whereCondition
                              GROUP BY RC.CateNo, A.AccountNo, YearD
                             ) AS B Group BY ItemID
                        ) AS HH
                        SET  TT.I2 = HH.I2
                        WHERE  TT.ItemID = HH.ItemID ";
        DB::statement($sqlUpdate);
    }
    public function I35_AI7($whereCondition, $month, $year)
    {
        $preYear = $year - 1;
        $fromDateArr = $year . '-' . $month . '-01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '-' . $month . '-31';
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '-01-01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '-01-01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '-' . $month . '-31';
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                            MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = 'Year3' THEN I END) AS I3,
                            MAX(CASE WHEN  AccountNo = '0361' AND B.Yeard = 'Year5' THEN I END) AS I5
                        FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,
                            Case when (A.PeriodFromDate >= '$fistDateYear' AND  A.PeriodFromDate <= '$toDate') THEN 'Year3'
                                when (A.PeriodFromDate >= '$fromDateN1' AND  A.PeriodFromDate <= '$toDateN1') THEN 'Year5' END as YearD,
                           Case when (RC.CateNo='201110501' AND CateValue='1') THEN 'A#01#07#01'
                                when (RC.CateNo='201110502' AND CateValue='1') THEN 'A#01#07#02'
                                when (RC.CateNo='201110503' AND CateValue='1') THEN 'A#01#07#03'
                                when (RC.CateNo='201110504' AND CateValue IN ('1','2')) THEN 'A#01#07#04'
                                when (RC.CateNo='201110507' AND CateValue = '1') THEN 'A#01#07#05' END as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                WHERE A.AccountNo IN ('0711')
                                AND ((RC.CateNo='201110501' AND CateValue='1')
                                    OR (RC.CateNo='201110501' AND CateValue='1')
                                    OR (RC.CateNo='201110503' AND CateValue='1')
                                    OR (RC.CateNo='201110504' AND CateValue IN ('1','2'))
                                    OR (RC.CateNo='201110507' AND CateValue = '1'))
                              $whereCondition
                              GROUP BY RC.Cateno, A.AccountNo, YearD
                             ) AS B Group BY ItemID
                        ) AS HH
                        SET  TT.I3 = HH.I3,TT.I5=HH.I5
                        WHERE  TT.ItemID = HH.ItemID ";
        DB::statement($sqlUpdate);
    }
    public function I35_AI12345689101112($whereCondition, $month, $year)
    {
        $preYear = $year - 1;
        $fromDateArr = $year . '-' . $month . '-01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '-' . $month . '-31';
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '-01-01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '-01-01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '-' . $month . '-31';
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                            MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = 'Year3' THEN I END) AS I3,
                            MAX(CASE WHEN  AccountNo = '0361' AND B.Yeard = 'Year5' THEN I END) AS I5
                        FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,
                            Case when (A.PeriodFromDate >= '$fistDateYear' AND  A.PeriodFromDate <= '$toDate') THEN 'Year3'
                                when (A.PeriodFromDate >= '$fromDateN1' AND  A.PeriodFromDate <= '$toDateN1') THEN 'Year5' END as YearD,
                           Case when (LEFT(RC.CateNo,7)='2011101' AND(SCC.CateNo='002' AND SCC.CateValue IN ('1','2'))) THEN 'A#01#01'
                                when (LEFT(RC.CateNo,7)='2011101' AND SCC.CateNo='003') THEN 'A#01#02'
                                when (LEFT(RC.CateNo,7)='2011101' AND SCC.CateNo='004') THEN 'A#01#03'
                                when (RC.CateNo = '2011102' AND RC.CateValue='1') THEN 'A#04'
                                when (RC.CateNo = '2011103' AND RC.CateValue IN('1','2')) THEN 'A#05'
                                when RC.CateNo = '2011104' THEN 'A#06'
                                when (RC.CateNo = '2011104' AND RC.CateValue='3') THEN 'A#06#01'
                                when RC.CateNo = '2011106' THEN 'A#08'
                                when (RC.CateNo = '201110801' AND RC.CateValue='1') THEN 'A#09'
                                when RC.CateNo = '2011107' THEN 'A#10'
                                when (RC.CateNo = '2011111' AND RC.CateValue='1') THEN 'A#11'
                                when (RC.CateNo = '201110901' AND RC.CateValue IN('1','2')) OR (RC.CateNo='201110902' AND RC.CateValue='1') THEN 'A#12' END as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN sbi_chapter_cate AS SCC ON SCC.SbiChapterID = A.SbiChapterID
                                WHERE A.AccountNo IN ('0711')
                                AND (LEFT(RC.CateNo,7)='2011101' AND(SCC.CateNo='002' AND SCC.CateValue IN ('1','2')))
                                    OR (LEFT(RC.CateNo,7)='2011101' AND SCC.CateNo='003')
                                    OR (LEFT(RC.CateNo,7)='2011101' AND SCC.CateNo='004')
                                    OR (RC.CateNo = '2011102' AND RC.CateValue='1')
                                    OR (RC.CateNo = '2011103' AND RC.CateValue IN('1','2'))
                                    OR RC.CateNo = '2011104'
                                    OR (RC.CateNo = '2011104' AND RC.CateValue='3')
                                    OR RC.CateNo = '2011106'
                                    OR (RC.CateNo = '201110801' AND RC.CateValue='1')
                                    OR RC.CateNo = '2011107'
                                    OR (RC.CateNo = '2011111' AND RC.CateValue='1')
                                    OR (RC.CateNo = '201110901' AND RC.CateValue IN('1','2')) OR (RC.CateNo='201110902' AND RC.CateValue='1')
                              $whereCondition
                              GROUP BY RC.Cateno, A.AccountNo, YearD
                             ) AS B Group BY ItemID
                        ) AS HH
                        SET TT.I3 = HH.I3, TT.I5 = HH.I5
                        WHERE  TT.ItemID = HH.ItemID ";
        DB::statement($sqlUpdate);
    }
    public function I35_AII_III($whereCondition, $month, $year)
    {
        $preYear = $year - 1;
        $fromDateArr = $year . '-' . $month . '-01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '-' . $month . '-31';
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '-01-01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '-01-01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '-' . $month . '-31';
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                            MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = 'Year3' THEN I END) AS I3,
                            MAX(CASE WHEN  AccountNo = '0361' AND B.Yeard = 'Year5' THEN I END) AS I5
                        FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo,
                          Case when (A.PeriodFromDate >= '$fistDateYear' AND  A.PeriodFromDate <= '$toDate') THEN 'Year3'
                                when (A.PeriodFromDate >= '$fromDateN1' AND  A.PeriodFromDate <= '$toDateN1') THEN 'Year5' END as YearD,
                           Case when (RC.CateNo='20112' AND CateValue='1') THEN 'A#02'
                                when (RC.CateNo='20113' AND CateValue='5') THEN 'A#03#01#01'
                                when (RC.CateNo='20113' AND CateValue='1') THEN 'A#03#01#02'
                                when (RC.CateNo='20113' AND CateValue IN('2','6')) THEN 'A#03#01#03'
                                when (RC.CateNo='20113' AND CateValue = '3') THEN 'A#03#01#04'
                                when (RC.CateNo='20113' AND CateValue = '4') THEN 'A#03#01#05'
                                when (RC.CateNo='20113' AND CateValue = '99') THEN 'A#03#01#06'
                                when LEFT(RC.CateNo,5)='5106' THEN 'A#03#02' END as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                WHERE A.AccountNo IN ('0711','0721')
                                AND ((RC.CateNo='201110501' AND CateValue='1')
                                    OR (RC.CateNo='201110501' AND CateValue='1')
                                    OR (RC.CateNo='201110503' AND CateValue='1')
                                    OR (RC.CateNo='201110504' AND CateValue IN ('1','2'))
                                    OR (RC.CateNo='201110507' AND CateValue = '1')
                                    OR (LEFT(RC.CateNo,5)='5106'))
                              $whereCondition
                              GROUP BY RC.CateNo, A.AccountNo, YearD
                             ) AS B Group BY ItemID
                        ) AS HH
                        SET  TT.I3 = HH.I3,TT.I5 = HH.I5
                        WHERE  TT.ItemID = HH.ItemID ";
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I5) AS I5
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
                        $I5 = ($value['I5']) ? $value['I5'] : 0;
                        $update = "UPDATE table_tmp
                    SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I5 = " . $I5 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }
    public function setDate($month){
        switch ($month) {
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
                return 31;
            case 4:
            case 6:
            case 9:
            case 11:
                return 30;
            case 2:
                return 28;

        }
    }



}
