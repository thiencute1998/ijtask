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

class SBP3432016TTBTCMB104Controller extends Controller {
    public function SBP3432016TTBTC_MB104(Request $request){

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
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3432016ttbtc_bs104')->get();

        $insertArr = [];
        foreach ($templateTable as $template) {
            $insertArr[] = [
                'STT' => $template->STT,
                'ItemID' => $template->ItemID,
                'ParentID' => $template->ParentID,
                'Level' => $template->Level,
                'ItemName' => $template->ItemName   ,
                'FontWeight' => $template->FontWeight,
                'Italic' => $template->Italic,
                'Center' => $template->Center,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
                'I5' => $template->I5,
                'I6' => $template->I6,
            ];
        }
        DB::table('table_tmp')->insert($insertArr);
        $this->I1234_I($whereCondition, $year, $yearPre);
        $this->I1234_II($whereCondition, $year, $yearPre);
        $this->I1234_III_IV_V_VI($whereCondition, $year, $yearPre);
        // sum up
        $this->sumUpTableTmp(1, 4);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Ước thực hiện năm ' . $yearPre. ' (năm hiện hành)';
            $data[0]->HeaderName2 = 'Dự toán năm ' . $year;

        }
        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            if($data[$key]->I1 !=0 ){
                $data[$key]->I5 = round((int)($data[$key]->I3) / (int)($data[$key]->I1), 2);
            }
            if($data[$key]->I2 !=0 ){
                $data[$key]->I6 = round((int)($data[$key]->I4) / (int)($data[$key]->I2), 2);
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
                "ReportName" => "DỰ TOÁN THU NGÂN SÁCH XÃ NĂM " . $year,
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
    public function I1234_I($whereCondition, $year, $yearPre){
        $sqlUpdate ="UPDATE table_tmp AS TT ,(
    SELECT ItemID,
           MAX(CASE WHEN ( B.AccountNo = '0711' AND B.BudgetLevel IN ('1','2','3','4') AND YEAR(B.PeriodFromDate)='$yearPre' ) THEN I END) AS I1, /*Đk chung Năm N-1, PS Nợ TK 0711 , Cấp NS=1,2,3,4 */
           MAX(CASE WHEN ( B.AccountNo = '0711' AND B.BudgetLevel = '4' AND YEAR(B.PeriodFromDate)='$yearPre' ) THEN I END) AS I2, /*Đk chung Cấp NS=4, Năm N-1, PS Nợ TK 0711 */
           MAX(CASE WHEN ( B.AccountNo = '0311' AND B.BudgetLevel IN ('1','2','3','4') AND YEAR(B.PeriodFromDate)='$year' ) THEN I END) AS I3, /* Năm N, PS Nợ TK 0311, Cấp NS=1,2,3,4 */
           MAX(CASE WHEN ( B.AccountNo = '0311' AND B.BudgetLevel = '4' AND YEAR(B.PeriodFromDate)='$year' ) THEN I END) AS I4 /* Đk chung Cấp NS=4, Năm N, PS Nợ TK 0311 */
    FROM ( SELECT SUM(A.FCDebitAmount) AS I, A.AccountNo, A.BudgetLevel, A.PeriodFromDate,
             (CASE
                  WHEN ( RC.CateNo = '202110301' AND RC.CateValue= '1' ) THEN 'A#01#01'
                  WHEN ( RC.CateNo = '202110301' AND RC.CateValue= '2' ) THEN 'A#01#02'
                  WHEN ( RC.CateNo = '202110301' AND RC.CateValue= '3' ) THEN 'A#01#03'
                  WHEN ( RC.CateNo = '202110301' AND RC.CateValue= '4') THEN 'A#01#04'
                  WHEN ( RC.CateNo = '202110301' AND RC.CateValue= '5') THEN 'A#01#05'
                  WHEN ( RC.CateNo = '202110301' AND RC.CateValue= '6') THEN 'A#01#06'
                  WHEN ( RC.CateNo = '202110301' AND RC.CateValue= '7') THEN 'A#01#07'
                  WHEN ( RC.CateNo = '202110301' AND RC.CateValue= '99') THEN 'A#01#08'
                 END ) as ItemID
          FROM  act_gl_books AS A
                    INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                    INNER JOIN revenue_regu AS RR ON RR.RevenueID = RC.RevenueID
          WHERE RC.CateNo = '202110301'
                AND RC.CateValue IN ('1', '2', '3','4','5','6','7','99')
                AND RR.ReguRate= '100'
                AND RR.EffectiveDate <= A.PostDate <= RR.ExpirationDate
                AND RR.RevenueReguActive = 1
                     $whereCondition
          GROUP BY ItemID , RC.CateValue,A.AccountNo, A.PeriodFromDate
        ) AS B GROUP BY ItemID
) AS HH
SET TT.I1 = HH.I1 , TT.I2 = HH.I2, TT.I3 = HH.I3 , TT.I4 = HH.I4
WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function I1234_II($whereCondition, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp as TT,(
            SELECT ItemID,RevenueNo,
                   MAX(CASE WHEN ( B.AccountNo = '0711' AND B.BudgetLevel IN ('1','2','3','4') AND YEAR(B.PeriodFromDate)='$yearPre' ) THEN I END) AS I1, /*Đk chung Năm N-1, PS Nợ TK 0711 , Cấp NS=1,2,3,4 */
                   MAX(CASE WHEN ( B.AccountNo = '0711' AND B.BudgetLevel = '4' AND YEAR(B.PeriodFromDate)='$yearPre' ) THEN I END) AS I2, /*Đk chung Cấp NS=4, Năm N-1, PS Nợ TK 0711 */
                   MAX(CASE WHEN ( B.AccountNo = '0311' AND B.BudgetLevel IN ('1','2','3','4') AND YEAR(B.PeriodFromDate)='$year' ) THEN I END) AS I3, /* Năm N, PS Nợ TK 0311, Cấp NS=1,2,3,4 */
                   MAX(CASE WHEN ( B.AccountNo = '0311' AND B.BudgetLevel = '4' AND YEAR(B.PeriodFromDate)='$year' ) THEN I END) AS I4 /* Đk chung Cấp NS=4, Năm N, PS Nợ TK 0311 */
            FROM(
                SELECT SUM(FCDebitAmount) as I, RC.CateNo as RevenueNo,A.AccountNo,A.BudgetLevel,A.PeriodFromDate,
                    (CASE WHEN RC.CateNo = '20211030201' AND RC.CateValue='1' THEN 'A#02#01#01'
                          WHEN RC.CateNo = '20211030201' AND RC.CateValue='2' THEN 'A#02#01#02'
                          WHEN RC.CateNo = '20211030201' AND RC.CateValue='3' THEN 'A#02#01#03'
                          WHEN RC.CateNo = '20211030201' AND RC.CateValue='4' THEN 'A#02#01#04'
                          WHEN RC.CateNo = '20211030202' AND RC.CateValue='1' THEN 'A#02#02#01'
                          WHEN RC.CateNo = '20211030202' AND RC.CateValue='2' THEN 'A#02#02#02'
                          WHEN RC.CateNo = '20211030202' AND RC.CateValue='3' THEN 'A#02#02#03'
                          WHEN RC.CateNo = '20211030202' AND RC.CateValue='4' THEN 'A#02#02#04'
                          WHEN RC.CateNo = '20211030202' AND RC.CateValue='5' THEN 'A#02#02#05'
                          WHEN RC.CateNo = '20211030202' AND RC.CateValue='6' THEN 'A#02#02#06'
                          WHEN RC.CateNo = '20211030202' AND RC.CateValue='99' THEN 'A#02#02#07'
                    END ) AS ItemID
                FROM act_gl_books AS A
                    INNER JOIN Revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                WHERE ( (RC.CateNo = '20211030201' AND RC.CateValue IN ('1','2','3','4')) OR
                      (RC.CateNo = '20211030202' AND RC.CateValue IN ('1','2','3','4','5','6','99')) )
                            $whereCondition
                GROUP BY RC.CateNo, RC.CateValue,A.AccountNo,A.PeriodFromDate
            ) AS B GROUP BY ItemID, RevenueNo
        ) AS HH
        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function I1234_III_IV_V_VI($whereCondition, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp as TT,(
            SELECT ItemID,RevenueNo,
                   MAX(CASE WHEN ( B.AccountNo = '0711' AND B.BudgetLevel IN ('1','2','3','4') AND YEAR(B.PeriodFromDate)='$yearPre' ) THEN I END) AS I1, /*Đk chung Năm N-1, PS Nợ TK 0711 , Cấp NS=1,2,3,4 */
                   MAX(CASE WHEN ( B.AccountNo = '0711' AND B.BudgetLevel = '4' AND YEAR(B.PeriodFromDate)='$yearPre' ) THEN I END) AS I2, /*Đk chung Cấp NS=4, Năm N-1, PS Nợ TK 0711 */
                   MAX(CASE WHEN ( B.AccountNo = '0311' AND B.BudgetLevel IN ('1','2','3','4') AND YEAR(B.PeriodFromDate)='$year' ) THEN I END) AS I3, /* Năm N, PS Nợ TK 0311, Cấp NS=1,2,3,4 */
                   MAX(CASE WHEN ( B.AccountNo = '0311' AND B.BudgetLevel = '4' AND YEAR(B.PeriodFromDate)='$year' ) THEN I END) AS I4 /* Đk chung Cấp NS=4, Năm N, PS Nợ TK 0311 */
            FROM(
                SELECT SUM(FCDebitAmount) as I, RC.CateNo as RevenueNo,A.AccountNo,A.BudgetLevel,A.PeriodFromDate,
                    (CASE WHEN RC.CateNo = '202110303' THEN 'A#03'
                          WHEN RC.CateNo = '202110304' THEN 'A#03'
                          WHEN RC.CateNo = '202110305' THEN 'A#03'
                          WHEN RC.CateNo = '202110306' AND RC.CateValue='1' THEN 'A#06#01'
                          WHEN RC.CateNo = '202110306' AND RC.CateValue='2' THEN 'A#06#01'
                    END ) AS ItemID
                FROM act_gl_books AS A
                    INNER JOIN Revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                WHERE (RC.CateNo IN('202110303','202110304','202110305') OR
                      (RC.CateNo = '202110306' AND RC.CateValue IN ('1','2')))
                            $whereCondition
                GROUP BY RC.CateNo, RC.CateValue, A.AccountNo,A.PeriodFromDate
            ) AS B GROUP BY ItemID, RevenueNo
        ) AS HH
        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE TT.ItemID = HH.ItemID";
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
                    SET I1 = " . $I1 . ", I2 = " . $I2 . ",I3 = " . $I3 . ", I4 = " . $I4 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
