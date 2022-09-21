<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3432016TTBTC;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class SBP3432016TTBTCMB105Controller extends Controller {
    public function SBP3432016TTBTC_MB105(Request $request){

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
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(100) NOT NULL DEFAULT '',
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
          `I7` double(20,4) DEFAULT NULL,
          `I8` double(20,4) DEFAULT NULL,
          `I9` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3432016ttbtc_bs105')->get();

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
                'I6' => $template->I6,
                'I7' => $template->I7,
                'I8' => $template->I8,
                'I9' => $template->I9,
            ];
        }
        DB::table('table_tmp')->insert($insertArr);
        $this->I2356_1_12($whereCondition, $year, $yearNext);
        // sum up
        $this->sumUpTableTmp(1, 2);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Dự toán năm ' . $year. ' (năm hiện hành)';
            $data[0]->HeaderName2 = 'Dự toán năm ' . $yearNext;
        }
        foreach ($data as $key => $value) {
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            if($data[$key]->I2 !=0 || $data[$key]->I3 !=0)  {
                $data[$key]->I1 = round((int)($data[$key]->I2) + (int)($data[$key]->I3), 2);
            }
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
            if($data[$key]->I5 !=0 || $data[$key]->I6 !=0) {
                $data[$key]->I4 = round((int)($data[$key]->I5) + (int)($data[$key]->I6), 2);
            }
            if($data[$key]->I1 !=0 ){
                $data[$key]->I7 = round(((int)($data[$key]->I4) / (int)($data[$key]->I1)*100), 2);
            }
            if($data[$key]->I2 !=0 ){
                $data[$key]->I8 = round(((int)($data[$key]->I5) / (int)($data[$key]->I2)*100), 2);
            }
            if($data[$key]->I3 !=0 ){
                $data[$key]->I9 = round(((int)($data[$key]->I6) / (int)($data[$key]->I3)*100), 2);
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
                "ReportName" => "DỰ TOÁN CHI NGÂN SÁCH XÃ NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND XÃ..',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    // ===================================== A =====================================
    public function I2356_1_12($whereCondition, $year, $yearNext){
        $sqlUpdate = "UPDATE table_tmp AS TT, (
    SELECT ItemID,SBICateNo,
           MAX(CASE WHEN (  B.AccountNo = '0441' AND LEFT (B.CateNo,5) ='20152' AND LEFT(CapitalNo,8)= '20012102' AND YEAR(B.PeriodFromDate)='$year' ) THEN I END) AS I2,
           MAX(CASE WHEN (  B.AccountNo = '0441' AND LEFT (B.CateNo,5) ='20151' AND LEFT(CapitalNo,6) IN ('200111', '200121') AND YEAR(B.PeriodFromDate)='$year' ) THEN I END) AS I3,
           MAX(CASE WHEN (  B.AccountNo = '0411' AND LEFT (B.CateNo,5) ='20152' AND LEFT(CapitalNo,8)= '20012102' AND YEAR(B.PeriodFromDate)='$yearNext' ) THEN I END) AS I5,
           MAX(CASE WHEN (  B.AccountNo = '0411' AND LEFT (B.CateNo,5) ='20151' AND LEFT(CapitalNo,6) IN ('200111', '200121') AND YEAR(B.PeriodFromDate)='$yearNext' ) THEN I END) AS I6
    FROM (SELECT SUM(A.FCDebitAmount) AS I, EC.CateNo, CC.CateNo AS CapitalNo, A.PeriodFromDate,A.AccountNo,SC.CateNo AS SBICateNo,
                 (CASE
                      WHEN SC.CateNo = '00303'  THEN 'A#02'
                      WHEN SC.CateNo = '00304'  THEN 'A#03'
                      WHEN SC.CateNo = '00305'  THEN 'A#04'
                      WHEN SC.CateNo = '00306'  THEN 'A#05'
                      WHEN SC.CateNo = '00307'  THEN 'A#06'
                      WHEN SC.CateNo = '00308'  THEN 'A#07'
                      WHEN SC.CateNo = '00309'  THEN 'A#08'
                      WHEN SC.CateNo = '00311'  THEN 'A#09'
                      WHEN SC.CateNo = '00312'  THEN 'A#10'
                      WHEN SC.CateNo = '00313'  THEN 'A#11'
                      WHEN SC.CateNo = '00314'  THEN 'A#12'
                      WHEN SC.CateNo = '00315'  THEN 'A#13'
                     END ) as ItemID
          FROM  act_gl_books AS A
                    INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                    INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                    INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
          WHERE SC.CateNo IN ('00303', '00304','00305','00306','00307','00308','00309','00311','00312','00313','00314','00315')
            AND A.BudgetLevel = '4'
            $whereCondition
          GROUP BY ItemID , SC.CateNo, A.PeriodFromDate, CapitalNo
         ) AS B GROUP BY ItemID, SBICateNo
) AS HH
SET TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5, TT.I6 = HH.I6
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I5) AS I5, SUM(M.I6) AS I6
                FROM table_tmp M
                WHERE M.Level = " . $i . " GROUP BY ParentID";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
                        $I2 = ($value['I2']) ? $value['I2'] : 0;
                        $I3 = ($value['I3']) ? $value['I3'] : 0;
                        $I5 = ($value['I5']) ? $value['I5'] : 0;
                        $I6 = ($value['I6']) ? $value['I6'] : 0;
                        $update = "UPDATE table_tmp
                    SET I2 = " . $I2 . ", I3 = " . $I3 . ",I5 = " . $I5 . ", I6 = " . $I6 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
