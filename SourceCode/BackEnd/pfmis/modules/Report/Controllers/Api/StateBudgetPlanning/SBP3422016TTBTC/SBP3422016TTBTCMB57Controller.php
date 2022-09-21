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

class SBP3422016TTBTCMB57Controller extends Controller {
    public function SBP3422016TTBTC_MB57(Request $request){
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
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb57')->get();

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
        $this->A_I_II_VI_VII_VIII($whereCondition, $month, $year);
        $this->A_III($whereCondition, $month, $year);
        $this->B($whereCondition, $month, $year);
        $this->I4_I6();
        $this->sumUpTableTmp(1, 3);


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
                "ReportName" => "ƯỚC THỰC HIỆN CHI NSĐP THÁNG ". $month. " NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'BỘ TÀI CHÍNH',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    public function A_I_II_VI_VII_VIII($whereCondition, $month, $year)
    {
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
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                               MAX(CASE WHEN (B.AccountNo = '0441' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
                               MAX(CASE WHEN (B.AccountNo = '0721' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
                               MAX(CASE WHEN (B.AccountNo = '0721' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
                               MAX(CASE WHEN (B.AccountNo = '0721' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
                        FROM (
                                 SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo,A.PeriodFromDate,
                                        (Case when LEFT (EC.CateNo,8) ='2015201' THEN 'A#01#01'
                                              when EC.CateNo ='2015202' THEN 'A#01#02'
                                              when EC.CateNo ='2015203' THEN 'A#01#03'
                                              when EC.CateNo ='20153' THEN 'A#02'
                                              when EC.CateNo ='20154' THEN 'A#04'
                                              when EC.CateNo ='20159' THEN 'A#05'
                                              when E.ExpenseNo  ='6999999' THEN 'A#06'
                                          END) as ItemID
                                 FROM act_gl_books as A
                                          INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                          INNER JOIN expense AS E ON E.ExpenseID = EC.ExpenseID
                                 WHERE A.AccountNo IN ('0441','0721')
                                   AND (LEFT (EC.CateNo,8) ='2015201'
                                    OR EC.CateNo IN ('2015202','2015203')
                                    OR (EC.CateNo = '20153' AND EC.CateValue IN ('1','2'))
                                    OR (EC.CateNo IN ('20154','20159') AND EC.CateValue ='1')
                                    OR E.ExpenseNo = '6999999'
                                       )
                                     $whereCondition
                                 GROUP BY  ItemID,  A.AccountNo
                             ) AS B Group BY ItemID
                        ) AS HH
                        SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function A_III($whereCondition, $month, $year)
    {
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
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                               MAX(CASE WHEN (B.AccountNo = '0441' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
                               MAX(CASE WHEN (B.AccountNo = '0721' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
                               MAX(CASE WHEN (B.AccountNo = '0721' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
                               MAX(CASE WHEN (B.AccountNo = '0721' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
                        FROM (
                                 SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo,A.PeriodFromDate,
                                        (Case when SCC.CateValue ='1' THEN 'A#03#01'
                                              when SCC.CateValue ='2' THEN 'A#03#02'
                                              when SCC.CateValue ='3' THEN 'A#03#03'
                                              when SCC.CateValue ='4' THEN 'A#03#04'
                                              when SCC.CateValue ='5' THEN 'A#03#05'
                                              when SCC.CateValue ='6' THEN 'A#03#06'
                                              when SCC.CateValue ='7' THEN 'A#03#07'
                                              when SCC.CateValue ='8' THEN 'A#03#08'
                                              when SCC.CateValue ='9' THEN 'A#03#09'
                                              when SCC.CateValue ='11' THEN 'A#03#10'
                                              when SCC.CateValue ='12' THEN 'A#03#11'
                                              when SCC.CateValue ='13' THEN 'A#03#12'
                                              when SCC.CateValue ='14' THEN 'A#03#13'  END) as ItemID
                                 FROM act_gl_books as A
                                          INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                          INNER JOIN sbi_category_cate AS SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                                 WHERE A.AccountNo IN ('0441','0721')
                                   AND LEFT(EC.CateNo,5) = '20151' AND SCC.CateNo ='001'
                                   AND SCC.CateValue IN ('1','2','3','4','5','6','7','8','9','11','12','13','14')
                                     $whereCondition
                                 GROUP BY  ItemID,  A.AccountNo
                             ) AS B Group BY ItemID
                        ) AS HH
                        SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function B($whereCondition, $month, $year)
    {
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
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                               MAX(CASE WHEN (B.AccountNo = '0441' AND  YEAR(B.PeriodFromDate) = '$year' ) THEN I END) AS I1,
                               MAX(CASE WHEN (B.AccountNo = '0721' AND  ('$fromDate' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I2,
                               MAX(CASE WHEN (B.AccountNo = '0721' AND  ('$fistDateYear' <= B.PeriodFromDate <= '$toDate') ) THEN I END) AS I3,
                               MAX(CASE WHEN (B.AccountNo = '0721' AND  ('$fromDateN1' <=  B.PeriodFromDate <= '$toDateN1') ) THEN I END) AS I5
                        FROM (
                                 SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo,A.PeriodFromDate,
                                        (Case when PC.CateValue ='2' THEN 'B#01'
                                              when PC.CateValue ='9' THEN 'B#02'
                                              when PC.CateValue ='8' THEN 'B#03'  END) as ItemID
                                 FROM act_gl_books as A
                                          INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                          INNER JOIN project_cate AS PC ON PC.ProjectID = A.ProjectID
                                          INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                                 WHERE A.AccountNo IN ('0441','0721')
                                   AND EC.CateNo ='201610101' AND EC.CateValue ='2'
                                   AND ((CC.CateNo = '20012102' AND CC.CateValue = '9')
                                       OR (CC.CateNo IN ('20012201','20012201','20012203') AND CC.CateValue ='2')
                                       )
                                   AND PC.CateNo ='006' AND PC.CateValue IN ('2','8','9')
                                     $whereCondition
                                 GROUP BY  ItemID,  A.AccountNo
                             ) AS B Group BY ItemID
                        ) AS HH
                        SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function I4_I6(){
        $createTmpTemporary = "CREATE TEMPORARY TABLE tmp (SELECT * FROM table_tmp)";
        DB::statement($createTmpTemporary);
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT (I3/I1*100) as value4, (I3/I5*100) as value6, ItemID FROM tmp) as HH
                        set TT.I4 = HH.value4, TT.I6 = HH.value6
                        where TT.ItemID = HH.ItemID
                        ";
        DB::statement($sqlUpdate);
    }
    //     sum up
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
