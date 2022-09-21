<?php
namespace Module\Report\Controllers\StateBudgetPlanning\Operating;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class OperatingB226DHController extends Controller {
    public function SBPB226DH(Request $request){

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

        $yearPre = (int)$year - 2;
        $yearNext = (int)$year -1;


        $whereCondition = '';
        $whereCondition .= "AND YEAR(A.PeriodFromDate) BETWEEN $year - 2 AND $year AND A.InTransTypeID NOT IN (16,17,18) ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(50) DEFAULT '',
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
           `RevenueCateValue` varchar(25) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('b226_dhdt')->get();

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
                'RevenueCateValue' => $template->RevenueCateValue,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
                'I5' => $template->I5
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->I_1_2_3($whereCondition, $year);
        $this->I_4___11($whereCondition, $year);
        // sum up
//        $this->sumUpTableTmp(1, 5);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $yearPre;
            $data[0]->HeaderName2 = 'Năm ' . $yearNext;
            $data[0]->HeaderName3 = 'Dự toán năm ' .$year;
        }

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            IF (($data[$key]->I2) !=0) {
                $data[$key]->I5 = round((((int)($data[$key]->I4) - (int)($data[$key]->I2)) * 100) / (int)($data[$key]->I2),2);
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
    public function I_1_2_3($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT RevenueCateValue,ItemID,
                               MAX(CASE WHEN  AccountNo = '0361' AND B.Yeard = $year -2 THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year -1 THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year -1 THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $year THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,RC.CateValue as RevenueCateValue, YEAR(A.PostDate)AS YearD,
                                    (CASE When (RC.CateNo='2040101' AND RC.CateValue ='1') THEN 'A#01#01#01'
                                          When (RC.CateNo='2040101' AND RC.CateValue ='2') THEN 'A#01#01#02'
                                          When (RC.CateNo='2040102' AND RC.CateValue ='1') THEN 'A#01#02#01'
                                          When (RC.CateNo='2040102' AND RC.CateValue ='2') THEN 'A#01#02#02'
                                          When (RC.CateNo='2040102' AND RC.CateValue ='3') THEN 'A#01#02#03'
                                          When (RC.CateNo='2040103' AND RC.CateValue ='1') THEN 'A#01#03#01'
                                          When (RC.CateNo='2040103' AND RC.CateValue ='2') THEN 'A#01#03#02'
                                          When (RC.CateNo='2040103' AND RC.CateValue ='3') THEN 'A#01#03#03' END
                                       ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
                                AND (RC.CateNo ='2040101' AND RC.CateVaLue IN ('1','2')
                                  OR RC.CateNo ='2040102' AND RC.CateVaLue IN ('1','2','3')
                                  OR RC.CateNo ='2040103' AND RC.CateVaLue IN ('1','2','3')
                                    )
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, RevenueCateValue, ItemID
                             ) AS  B GROUP BY RevenueCateValue, ItemID
                ) AS HH
                SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function I_4___11($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT RevenueCateValue,ItemID,
                               MAX(CASE WHEN  AccountNo = '0361' AND B.Yeard = $year -2 THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year -1 THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year -1 THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $year THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,RC.CateValue as RevenueCateValue, YEAR(A.PostDate)AS YearD,
                                    (CASE When RC.CateNo='2040104' THEN 'A#01#04'
                                          When RC.CateNo='2040105' THEN 'A#01#05'
                                          When RC.CateNo='2040106' THEN 'A#01#06'
                                          When RC.CateNo='2040107' THEN 'A#01#07'
                                          When RC.CateNo='2040108' THEN 'A#01#08'
                                          When RC.CateNo='2040109' THEN 'A#01#09'
                                          When RC.CateNo='2040111' THEN 'A#01#10'
                                          When RC.CateNo='2040112' THEN 'A#01#11'
                                          When RC.CateNo='2040201' THEN 'A#02#01'  END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
                                AND RC.CateNo IN ('2040104','2040105','2040106','2040107','2040108',
                                                  '2040109','2040111','2040112','2040201')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, RevenueCateValue, ItemID
                             ) AS  B GROUP BY RevenueCateValue, ItemID
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function II_III_IV_156($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT RevenueCateValue,ItemID,
                               MAX(CASE WHEN  AccountNo = '0361' AND B.Yeard = $year -2 THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year -1 THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year -1 THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $year THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,RC.CateValue as RevenueCateValue, YEAR(A.PostDate)AS YearD,
                                     (CASE When (RC.CateNo='2040202' AND RC.CateValue ='1') THEN 'A#02#02#01'
                                           When (RC.CateNo='2040202' AND RC.CateValue ='2') THEN 'A#02#02#02'
                                           When (RC.CateNo='20403' AND RC.CateValue ='1') THEN 'A#03#01'
                                           When (RC.CateNo='20403' AND RC.CateValue ='2') THEN 'A#03#02'
                                           When (RC.CateNo='20403' AND RC.CateValue ='3') THEN 'A#03#03'
                                           When (RC.CateNo='2040401' AND RC.CateValue ='1') THEN 'A#04#01#01'
                                           When (RC.CateNo='2040401' AND RC.CateValue ='2') THEN 'A#04#01#02'
                                           When (RC.CateNo='2040401' AND RC.CateValue ='3') THEN 'A#04#01#03'
                                           When (RC.CateNo='2040405' AND RC.CateValue ='1') THEN 'A#04#05#01'
                                           When (RC.CateNo='2040405' AND RC.CateValue ='2') THEN 'A#04#05#02'
                                           When (RC.CateNo='2040405' AND RC.CateValue ='3') THEN 'A#04#05#03'
                                           When (RC.CateNo='2040406' AND RC.CateValue ='1') THEN 'A#04#06#01'
                                           When (RC.CateNo='2040406' AND RC.CateValue ='2') THEN 'A#04#06#02'
                                           When (RC.CateNo='2040406' AND RC.CateValue ='3') THEN 'A#04#06#03'
                                           When (RC.CateNo='2040406' AND RC.CateValue ='4') THEN 'A#04#06#04'
                                           When (RC.CateNo='2040406' AND RC.CateValue ='5') THEN 'A#04#06#05'
                                           When (RC.CateNo='2040406' AND RC.CateValue ='6') THEN 'A#04#06#06'END
                                         ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
                                AND (RC.CateNo ='2040202' AND RC.CateVaLue IN ('1','2')
                                  OR RC.CateNo ='20403' AND RC.CateVaLue IN ('1','2','3')
                                  OR RC.CateNo ='2040401' AND RC.CateVaLue IN ('1','2','3')
                                  OR RC.CateNo ='2040405' AND RC.CateVaLue IN ('1','2','3')
                                  OR RC.CateNo ='2040406' AND RC.CateVaLue IN ('1','2','3','4','5','6')
                                  )
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, RevenueCateValue, ItemID
                                     ) AS  B GROUP BY RevenueCateValue, ItemID
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                    WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function IV234789_V($whereCondition, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT RevenueCateValue,ItemID,
                               MAX(CASE WHEN  AccountNo = '0361' AND B.Yeard = $year -2 THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year -1 THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year -1 THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $year THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,RC.CateValue as RevenueCateValue, YEAR(A.PostDate)AS YearD,
                                     (CASE When RC.CateNo='2040402' THEN 'A#04#02'
                                           When RC.CateNo='2040403' THEN 'A#04#03'
                                           When RC.CateNo='2040404' THEN 'A#04#04'
                                           When RC.CateNo='2040407' THEN 'A#04#07'
                                           When RC.CateNo='2040408' THEN 'A#04#08'
                                           When RC.CateNo='2040409' THEN 'A#04#09'
                                           When RC.CateNo='20405' THEN 'A#05' END ) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
                                AND RC.CateNo IN ('2040402','2040403','2040404','2040407','2040408',
                                                  '2040409','20405')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, RevenueCateValue, ItemID
                             ) AS  B GROUP BY RevenueCateValue, ItemID
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
