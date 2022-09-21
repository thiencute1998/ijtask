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
use IjSmartBooks\IjCore\IjFunction\Date;


class SBP3422016TTBTCMB56Controller extends Controller {
    public function SBP3422016TTBTC_MB56(Request $request){
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
        (YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $yearPre AND YEAR(A.PeriodToDate) = $yearPre)
        OR (YEAR(A.PeriodFromDate) = $yearNext AND YEAR(A.PeriodToDate) = $yearNext)
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
          `SbiCategoryNo` varchar(20) DEFAULT '',
          `ConAccountNo` varchar(20) DEFAULT '',
          `ConCoAccountNo` varchar(20) DEFAULT '',
          `RevenueCateNo` varchar(20) DEFAULT '',
          `ExpenseCateNo` varchar (20) DEFAULT '',
          `ExpenseNo` varchar (20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          `I5` double(20,4) DEFAULT NULL,
          `I6` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb56')->get();

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
                'SbiCategoryNo' => $template->SbiCategoryNo,
                'ConAccountNo' => $template->ConAccountNo,
                'ConCoAccountNo' => $template->ConCoAccountNo,
                'RevenueCateNo' => $template->RevenueCateNo,
                'ExpenseCateNo' => $template->ExpenseCateNo,
                'ExpenseNo' => $template->ExpenseNo,
                'NormCateValue' => $template->NormCateValue,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
                'I5' => $template->I5,
                'I6' => $template->I6
            ];
        }
        $month = $monthFilter;
        DB::table('table_tmp')->insert($insertArr);
//        $this->I1235_A_I12345689101112($whereCondition, $month, $year);
        $this->I1235_A_I123($whereCondition, $month, $year);
        $this->I1234_A_I6_1($whereCondition, $month, $year);
        $this->I1235_A_I45689101112($whereCondition, $month, $year);
        $this->I1235_A_I7($whereCondition, $month, $year);
        $this->I1235_A_III_2($whereCondition, $month, $year);
        $this->I1235_A_III_1($whereCondition, $month, $year);
        $this->I1235_A_II($whereCondition, $month, $year);
        $this->I1235_A_IV($whereCondition, $month, $year);
        $this->I1235_B($whereCondition, $month, $year);
//        $this->I4_I6();
        $this->sumUpTableTmp(1, 4);


        $date = date('d');
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
            if($data[$key]->I1 !=0 ){
                $data[$key]->I4 = round(((int)($data[$key]->I3) / (int)($data[$key]->I1))*100, 2);
            }
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            if($data[$key]->I3 !=0 ){
                $data[$key]->I6 = round(((int)($data[$key]->I3) / (int)($data[$key]->I5))*100, 2);
            }
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
                "ReportName" => "ƯỚC THỰC HIỆN THU NSNN THÁNG ". $month. " NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND TỈNH VĨNH PHÚ',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    // ===================================== A =====================================
//    public function I1235_A_I12345689101112($whereCondition, $month, $year){
//        $preYear = $year - 1;
//        $fromDateArr = $year . '/' . $month . '/01';
//        $fromDate = Date::StringToDate($fromDateArr);
//        $toDateArr = $year . '/' . $month . '/'.$this->setDate($month, $year );
//        $toDate = Date::StringToDate($toDateArr);
//        $fistDateYearArr = $year . '/01/01';
//        $fistDateYear = Date::StringToDate($fistDateYearArr);
//        $fromDateArrN1 = $preYear . '/01/01';
//        $fromDateN1 = Date::StringToDate($fromDateArrN1);
//        $toDateArrN1 = $preYear . '/' . $month . '/'.$this->setDate($month, $year );
//        $toDateN1 = Date::StringToDate($toDateArrN1);
//        $sqlUpdate = "UPDATE table_tmp as TT, (SELECT ItemID,
//                                  MAX(CASE WHEN (B.AccountNo = '0341' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
//                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
//                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
//                                  MAX(CASE WHEN (B.AccountNo = '0361' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
//                         FROM (SELECT SUM(A.FCDebitAmount) AS I, A.AccountNo,A.PeriodFromDate,
//                                      (Case  when ( LEFT(RC.CateNo,7) = '2011101' AND SC.CateNo = '002' AND SC.CateValue IN (1,2)) THEN 'A#01#01'
//                                             when ( LEFT(RC.CateNo,7) = '2011101' AND SC.CateNo = '003') THEN 'A#01#02'
//                                             when ( LEFT(RC.CateNo,7) = '2011101' AND SC.CateNo = '004' ) THEN 'A#01#03'
//                                             when ( RC.CateNo = '2011102' AND RC.CateValue = '1') THEN 'A#01#04'
//                                             when ( RC.CateNo = '2011103' AND RC.CateValue IN ('1','2')) THEN 'A#01#05'
//                                             when  LEFT(RC.CateNo,7) = '2011104' THEN 'A#01#06'
//                                             when ( RC.CateNo = '2011104' AND RC.CateValue = '3') THEN 'A#01#06#01'
//                                             when  LEFT(RC.CateNo,7) = '2011106' THEN 'A#01#08'
//                                             when ( RC.CateNo = '201110801' AND RC.CateValue = '1') THEN 'A#01#09'
//                                             when  LEFT(RC.CateNo,7) = '2011107' THEN 'A#01#10'
//                                             when ( RC.CateNo = '2011111' AND RC.CateValue = '1') THEN 'A#01#11'
//                                             when ( ( RC.CateNo = '201110901' AND RC.CateValue IN ('1','2')) OR (RC.CateNo = '201110902' AND RC.CateValue = '1') )  THEN 'A#01#12'
//                                            END)
//                                          as ItemID
//                               FROM  act_gl_books AS A
//                                         INNER JOIN revenue_cate AS  RC ON RC.RevenueID = A.RevenueID
//                                         INNER JOIN  sbi_chapter_cate AS SC ON SC.SbiChapterID = A.SbiChapterID
//                               WHERE A.AccountNo IN ('0341', '0711', '0361')
//                                   AND ( (LEFT(RC.CateNo, 7) = '2011101' AND(SC.CateNo IN ( '003', '004')) OR (LEFT(RC.CateNo, 7) = '2011101' AND (SC.CateNo = '002' AND SC.CateValue IN (1,2))))OR
//                                         ( RC.CateNo = '2011102' AND RC.CateValue = '1') OR
//                                         ( RC.CateNo = '2011103' AND RC.CateValue IN ('1','2')) OR
//                                         LEFT(RC.CateNo,7) = '2011104' OR
//                                         ( RC.CateNo = '2011104' AND RC.CateValue = '3') OR
//                                         LEFT(RC.CateNo,7) = '2011106' OR
//                                         ( RC.CateNo = '201110801' AND RC.CateValue = '1') OR
//                                         LEFT(RC.CateNo,7) = '2011107' OR
//                                         ( RC.CateNo = '2011111' AND RC.CateValue = '1') OR
//                                         ( ( RC.CateNo = '201110901' AND RC.CateValue IN ('1','2')) OR (RC.CateNo = '201110902' AND RC.CateValue = '1')))
//                                   $whereCondition
//                                    GROUP BY A.AccountNo, ItemID , SC.CateNo
//                                ) AS B GROUP BY  ItemID
//                            ) AS HH
//                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
//                        WHERE  TT.ItemID = HH.ItemID";
//        DB::statement($sqlUpdate);
//    }
    public function I1235_A_I123($whereCondition, $month, $year){
        $preYear = $year - 1;
        $fromDateArr = $year . '/' . $month . '/01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '/' . $month . '/'.$this->setDate($month, $year );
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '/01/01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '/01/01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '/' . $month . '/'.$this->setDate($month, $year );
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp as TT, (SELECT ItemID,
                                  MAX(CASE WHEN (B.AccountNo = '0341' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
                                  MAX(CASE WHEN (B.AccountNo = '0361' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
                         FROM (SELECT SUM(A.FCDebitAmount) AS I, A.AccountNo, A.PeriodFromDate,
                                      (CASE WHEN (LEFT (RC.CateNo,7) ='2011101' AND SC.CateNo = '002' AND SC.CateValue IN (1,2) )THEN 'A#01#01'
                                            WHEN (LEFT (RC.CateNo,7) ='2011101' AND SC.CateNo = '003') THEN 'A#01#02'
                                            WHEN (LEFT (RC.CateNo,7) ='2011101' AND SC.CateNo = '004') THEN 'A#01#03'
                                          END ) as ItemID
                               FROM  act_gl_books AS A
                                         INNER JOIN revenue_cate AS  RC ON RC.RevenueID = A.RevenueID
                                         INNER JOIN  sbi_chapter_cate AS SC ON SC.SbiChapterID = A.SbiChapterID
                               WHERE  LEFT(RC.CateNo, 7) = '2011101'
                                 AND A.AccountNo IN ('0341', '0711', '0361')
                                 AND (SC.CateNo IN ( '003', '004') OR (SC.CateNo = '002' AND SC.CateValue IN (1,2)))
                                    $whereCondition
                               GROUP BY A.AccountNo, ItemID , SC.CateNo
                              ) AS B GROUP BY ItemID
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function I1234_A_I6_1($whereCondition, $month, $year){
        $preYear = $year - 1;
        $fromDateArr = $year . '/' . $month . '/01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '/' . $month . '/'.$this->setDate($month, $year );
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '/01/01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '/01/01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '/' . $month . '/'.$this->setDate($month, $year );
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp as TT, (SELECT
                                  MAX(CASE WHEN (B.AccountNo = '0341' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
                                  MAX(CASE WHEN (B.AccountNo = '0361' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
                         FROM (
                                  SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo,A.PeriodFromDate
                                  FROM  act_gl_books AS A
                                            INNER JOIN revenue_cate AS  RC ON RC.RevenueID = A.RevenueID
                                  WHERE  RC.CateNo = '2011104' AND RC.CateValue='3'
                                          AND A.AccountNo IN ('0341', '0711', '0361')
                                        $whereCondition
                                  GROUP BY  A.AccountNo
                                ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
                        WHERE  TT.ItemID = 'A#01#06#01'";
        DB::statement($sqlUpdate);
    }
    public function I1235_A_I45689101112($whereCondition, $month, $year){
        $preYear = $year - 1;
        $fromDateArr = $year . '/' . $month . '/01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '/' . $month . '/'.$this->setDate($month, $year );
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '/01/01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '/01/01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '/' . $month . '/'.$this->setDate($month, $year );
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp as TT, (SELECT ItemID,
                                  MAX(CASE WHEN (B.AccountNo = '0341' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
                                  MAX(CASE WHEN (B.AccountNo = '0361' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
                         FROM (
                                  SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo,A.PeriodFromDate,
                                         (Case when (RC.CateNo ='2011102' AND RC.CateValue='1') THEN 'A#01#04'
                                               when (RC.CateNo ='2011103' AND ( RC.CateValue='1' OR RC.CateValue='2')) THEN 'A#01#05'
                                               when LEFT (RC.CateNo,7) ='2011104' THEN 'A#01#06'
                                               when LEFT (RC.CateNo,7) ='2011106' THEN 'A#01#08'
                                               when (RC.CateNo ='201110801' AND RC.CateValue='1') THEN 'A#01#09'
                                               when LEFT (RC.CateNo,7) ='2011107' THEN 'A#01#10'
                                               when (RC.CateNo ='2011111' AND RC.CateValue='1' )THEN 'A#01#11'
                                               when ((RC.CateNo ='201110901' AND ( RC.CateValue='1' OR RC.CateValue='2')) OR (RC.CateNo ='201110902' AND RC.CateValue='1')) THEN 'A#01#12'
                                             END) as ItemID
                                  FROM  act_gl_books AS A
                                            INNER JOIN revenue_cate AS  RC ON RC.RevenueID = A.RevenueID
                                  WHERE ( (RC.CateNo ='2011102' AND RC.CateValue='1')  OR
                                          (RC.CateNo ='2011103' AND ( RC.CateValue='1' OR RC.CateValue='2')) OR
                                          (LEFT (RC.CateNo,7) ='2011104') OR
                                          (LEFT (RC.CateNo,7) ='2011106') OR
                                          (RC.CateNo ='201110801' AND RC.CateValue='1') OR
                                          (LEFT (RC.CateNo,7) ='2011107') OR
                                          (LEFT (RC.CateNo,7) ='2011111') OR
                                          ((RC.CateNo ='201110901' AND ( RC.CateValue='1' OR RC.CateValue='2')) OR (RC.CateNo ='201110902' AND RC.CateValue='1')))
                                    AND A.AccountNo IN ('0341', '0711', '0361')
                                    $whereCondition
                                  GROUP BY ItemID, A.AccountNo, RC.CateNo
                              ) AS B GROUP BY ItemID
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function I1235_A_I7($whereCondition, $month, $year){
        $preYear = $year - 1;
        $fromDateArr = $year . '/' . $month . '/01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '/' . $month . '/'.$this->setDate($month, $year );
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '/01/01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '/01/01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '/' . $month . '/'.$this->setDate($month, $year );
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp as TT, (SELECT ItemID,
                                  MAX(CASE WHEN (B.AccountNo = '0341' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
                                  MAX(CASE WHEN (B.AccountNo = '0361' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
                         FROM (
                                  SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo, A.PeriodFromDate,
                                         (Case  when ( RC.CateNo = '201110501' AND RC.CateValue = '1') THEN 'A#01#07#01'
                                                when ( RC.CateNo = '201110502' AND RC.CateValue = '1') THEN 'A#01#07#02'
                                                when ( RC.CateNo = '201110503' AND RC.CateValue =  '1' ) THEN 'A#01#07#03'
                                                when ( RC.CateNo = '201110504' AND RC.CateValue IN ('1','2')) THEN 'A#01#07#04'
                                                ELSE 'A#01#07#05' END)
                                                as ItemID
                                  FROM  act_gl_books AS A
                                            INNER JOIN revenue_cate AS  RC ON RC.RevenueID = A.RevenueID
                                  WHERE
                                        (( RC.CateNo = '201110501' AND RC.CateValue = '1') OR
                                         ( RC.CateNo = '201110502' AND RC.CateValue = '1') OR
                                         ( RC.CateNo = '201110503' AND RC.CateValue = '1' ) OR
                                         ( RC.CateNo = '201110504' AND RC.CateValue IN ('1','2')) OR
                                         ( RC.CateNo = '201110507' AND RC.CateValue = '1'))
                                         AND A.AccountNo IN ('0341', '0711', '0361')
                                            $whereCondition
                                         GROUP BY  A.AccountNo, ItemID, RC.CateNo
                              ) AS B GROUP BY ItemID
                            ) AS HH
                            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
                            WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function I1235_A_II($whereCondition, $month, $year){
        $preYear = $year - 1;
        $fromDateArr = $year . '/' . $month . '/01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '/' . $month . '/'.$this->setDate($month, $year );
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '/01/01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '/01/01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '/' . $month . '/'.$this->setDate($month, $year );
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp as TT, (SELECT
                                MAX(CASE WHEN (B.AccountNo = '0341' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
                                  MAX(CASE WHEN (B.AccountNo = '0361' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
                         FROM (
                                  SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo,A.PeriodFromDate
                                  FROM  act_gl_books AS A
                                            INNER JOIN revenue_cate AS  RC ON RC.RevenueID = A.RevenueID
                                  WHERE  LEFT(RC.CateNo, 6) = '20112'
                                         AND RC.CateValue = '1'
                                         AND A.AccountNo IN ('0341', '0721', '0461')
                                         $whereCondition
                                  GROUP BY  A.AccountNo
                              ) AS B
                            ) AS HH
                         SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
                         WHERE  TT.ItemID = 'A#02'";
        DB::statement($sqlUpdate);
    }
    public function I1235_A_III_1($whereCondition, $month, $year){
        $preYear = $year - 1;
        $fromDateArr = $year . '/' . $month . '/01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '/' . $month . '/'.$this->setDate($month, $year );
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '/01/01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '/01/01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '/' . $month . '/'.$this->setDate($month, $year );
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp as TT, (SELECT ItemID,
                            MAX(CASE WHEN (B.AccountNo = '0341' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
                            MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
                            MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
                            MAX(CASE WHEN (B.AccountNo = '0361' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
                         FROM (
                                  SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo,A.PeriodFromDate,
                                         (Case  when ( RC.CateNo = '20113' AND RC.CateValue = '5') THEN 'A#03#01#01'
                                                when ( RC.CateNo = '20113' AND RC.CateValue = '1') THEN 'A#03#01#02'
                                                when ( RC.CateNo = '20113' AND RC.CateValue IN ('2','6') ) THEN 'A#03#01#03'
                                                when ( RC.CateNo = '20113' AND RC.CateValue = '3') THEN 'A#03#01#04'
                                                when ( RC.CateNo = '20113' AND RC.CateValue = '4') THEN 'A#03#01#05'
                                                ELSE 'A#03#01#06' END)
                                                as ItemID
                                  FROM  act_gl_books AS A
                                            INNER JOIN revenue_cate AS  RC ON RC.RevenueID = A.RevenueID
                                  WHERE
                                        (( RC.CateNo = '20113' AND RC.CateValue = '5') OR
                                         ( RC.CateNo = '20113' AND RC.CateValue = '1') OR
                                         ( RC.CateNo = '20113' AND RC.CateValue IN ('2','6') ) OR
                                         ( RC.CateNo = '20113' AND RC.CateValue = '3') OR
                                         ( RC.CateNo = '20113' AND RC.CateValue = '4') OR
                                         ( RC.CateNo = '20113' AND RC.CateValue = '99'))
                                         AND A.AccountNo IN ('0341', '0711', '0361')
                                            $whereCondition
                                         GROUP BY  A.AccountNo, ItemID, RC.CateNo
                              ) AS B GROUP BY ItemID
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function I1235_A_III_2($whereCondition, $month, $year){
        $preYear = $year - 1;
        $fromDateArr = $year . '/' . $month . '/01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '/' . $month . '/'.$this->setDate($month, $year );
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '/01/01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '/01/01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '/' . $month . '/'.$this->setDate($month, $year );
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp as TT, (SELECT
                            MAX(CASE WHEN (B.AccountNo = '0441' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
                            MAX(CASE WHEN (B.AccountNo = '0721' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
                            MAX(CASE WHEN (B.AccountNo = '0721' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
                            MAX(CASE WHEN (B.AccountNo = '0461' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
                         FROM (
                                  SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo,A.PeriodFromDate
                                  FROM  act_gl_books AS A
                                            INNER JOIN expense_cate AS EC ON EC.ExpenseID = EC.ExpenseID
                                  WHERE  LEFT(EC.CateNo, 4) = '5106'
                                    AND A.AccountNo IN ('0441', '0721', '0461')
                                    $whereCondition
                                  GROUP BY A.AccountNo
                              ) AS B
                            ) AS HH
                         SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
                         WHERE  TT.ItemID = 'A#03#02'";
        DB::statement($sqlUpdate);
    }
    public function I1235_A_IV($whereCondition, $month, $year){
        $preYear = $year - 1;
        $fromDateArr = $year . '/' . $month . '/01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '/' . $month . '/'.$this->setDate($month, $year );
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '/01/01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '/01/01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '/' . $month . '/'.$this->setDate($month, $year );
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp as TT, (SELECT
                            MAX(CASE WHEN (B.AccountNo = '0341' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
                            MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
                            MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
                            MAX(CASE WHEN (B.AccountNo = '0361' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
                      FROM (
                                  SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo,A.PeriodFromDate
                                  FROM  act_gl_books AS A
                                            INNER JOIN revenue_cate AS  RC ON RC.RevenueID = A.RevenueID
                                  WHERE  LEFT(RC.CateNo, 5) = '20114'
                                    AND A.AccountNo IN ('0341', '0711', '0361')
                                    $whereCondition
                                  GROUP BY A.AccountNo
                              ) AS B
                            ) AS HH
                         SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
                         WHERE  TT.ItemID = 'A#04'";
        DB::statement($sqlUpdate);
    }
    public function I1235_B($whereCondition, $month, $year){
        $preYear = $year - 1;
        $fromDateArr = $year . '/' . $month . '/01';
        $fromDate = Date::StringToDate($fromDateArr);
        $toDateArr = $year . '/' . $month . '/'.$this->setDate($month, $year );
        $toDate = Date::StringToDate($toDateArr);
        $fistDateYearArr = $year . '/01/01';
        $fistDateYear = Date::StringToDate($fistDateYearArr);
        $fromDateArrN1 = $preYear . '/01/01';
        $fromDateN1 = Date::StringToDate($fromDateArrN1);
        $toDateArrN1 = $preYear . '/' . $month . '/'.$this->setDate($month, $year );
        $toDateN1 = Date::StringToDate($toDateArrN1);
        $sqlUpdate = "UPDATE table_tmp as TT, (SELECT ItemID,
                                  MAX(CASE WHEN (B.AccountNo = '0341' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
                                  MAX(CASE WHEN (B.AccountNo = '0711' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
                                  MAX(CASE WHEN (B.AccountNo = '0361' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
                         FROM (
                                  SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo,A.PeriodFromDate,
                                         (Case  when LEFT(RC.CateNo,9) = '202110202' THEN 'B#01'
                                                when LEFT(RC.CateNo,9) = '202110201' THEN 'B#02'
                                             END)
                                         AS ItemID
                                  FROM  act_gl_books AS A
                                            INNER JOIN revenue_cate AS  RC ON RC.RevenueID = A.RevenueID
                                  WHERE  (LEFT(RC.CateNo, 9) = '202110202' OR
                                          LEFT(RC.CateNo, 9) = '202110201')
                                          AND A.AccountNo IN ('0341', '0711', '0361')
                                        $whereCondition
                                  GROUP BY ItemID, A.AccountNo
                                    ) AS B
                            GROUP BY ItemID
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }


//    public function I4_I6(){
//        $createTmpTemporary = "CREATE TEMPORARY TABLE tmp (SELECT * FROM table_tmp)";
//        DB::statement($createTmpTemporary);
//        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT (I3/I1*100) as value4, (I3/I5*100) as value6, ItemID FROM tmp) as HH
//                        set TT.I4 = HH.value4, TT.I6 = HH.value6
//                        where TT.ItemID = HH.ItemID
//                        ";
//        DB::statement($sqlUpdate);
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
    public function isCheck($year) {
        return (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0);
    }
    public function setDate($month, $year){
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
                if($this->isCheck($year)){
                    return 29;
                }
                else
                    return 28;
        }
    }


}
