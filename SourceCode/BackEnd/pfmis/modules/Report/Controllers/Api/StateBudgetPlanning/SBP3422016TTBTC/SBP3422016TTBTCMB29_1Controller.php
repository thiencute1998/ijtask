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

class SBP3422016TTBTCMB29_1Controller extends Controller {
    public function SBP3422016TTBTC_MB29_1(Request $request){

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
        (YEAR(A.PostDate) = $year AND YEAR(A.PostDate) = $year)
        OR (YEAR(A.PostDate) = $yearPre AND YEAR(A.PostDate) = $yearPre)
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
          `AccountNoI1` varchar(20) DEFAULT '',
          `AccountNoI2` varchar(20) DEFAULT '',
          `AccountNoI3` varchar(20) DEFAULT '',
          `AccountNoI4` varchar(20) DEFAULT '',
          `NormCateNo` varchar (20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
          `RevenueCateNo` varchar(20) DEFAULT '',
          `RevenueCateValue` varchar(20) DEFAULT '',
          `CapitalCateNo` varchar(20) DEFAULT '',
          `ExpenseCateNo` varchar(20) DEFAULT '',
          `CapitalNo` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb2901')->get();

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
                'AccountNoI1' => $template->AccountNoI1,
                'AccountNoI2' => $template->AccountNoI2,
                'AccountNoI3' => $template->AccountNoI3,
                'AccountNoI4' => $template->AccountNoI4,
                'NormCateValue' => $template->NormCateValue,
                'NormCateNo' => $template->NormCateValue,
                'RevenueCateNo' => $template->RevenueCateNo,
                'RevenueCateValue' => $template->RevenueCateValue,
                'CapitalCateNo' => $template->CapitalCateNo,
                'ExpenseCateNo' => $template->ExpenseCateNo,
                'CapitalNo' => $template->ExpenseCateNo,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->A_BI($whereCondition, $yearNext, $year, $yearPre);
        $this->A32($whereCondition, $yearNext, $year, $yearPre);
        $this->BII1_III_IV_V($whereCondition, $yearNext, $year, $yearPre);
        $this->CI_2_3_4_III($whereCondition, $yearNext, $year, $yearPre);
        $this->CII1_3($whereCondition, $yearNext, $year, $yearPre);
        $this->CII_2($whereCondition, $yearNext, $year, $yearPre);
        $this->BII2($whereCondition, $yearNext, $year, $yearPre);
        $this->CI_1($whereCondition, $yearNext, $year, $yearPre);
        $this->CI_5($whereCondition, $yearNext, $year, $yearPre);

       //  sum up
        $this->sumUpTableTmp(1, 5);
        $item31 = DB::table('table_tmp')->where('ItemID', '=', 'A#03#01')->first();
        $item32 = DB::table('table_tmp')->where('ItemID', '=', 'A#03#02')->first();
        DB::table('table_tmp')->where('ItemID','=','A#03')
            ->update(
                [   'I1'=>$item31->I1- $item32->I1,
                    'I2'=>$item31->I2- $item32->I2,
                    'I3'=>$item31->I3- $item32->I3,
                    'I4'=>$item31->I4- $item32->I4,
                ]
            );

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->whereNotIn('ItemID',['A#03#01','A#03#02'])->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $yearPre;
            $data[0]->HeaderName2 = 'Năm ' . $year;
            $data[0]->HeaderName3 = 'Dự toán năm ' .$yearNext;
        }

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
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
                "ReportName" => "CÂN ĐỐI NGÂN SÁCH ĐỊA PHƯƠNG NĂM " . $year,
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
    public function A_BI($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE report_sbp_3422016ttbtc_mb2901 AS TT,(SELECT ItemID, RevenueCateNo,
                                                    MAX(CASE WHEN  AccountNo = '0361'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                                    MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                                                    MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                                                    MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                                             FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,RC.CateNo as RevenueCateNo,YEAR(A.PostDate)AS YearD,
                                                          (Case WHEN  LEFT(RC.CateNo,5)='20111' THEN  'A#01'
                                                                WHEN  LEFT(RC.CateNo,5)='20112' THEN  'A#02'
                                                                WHEN  LEFT(RC.CateNo,5)='20113' THEN  'A#03#01'
                                                                WHEN  LEFT(RC.CateNo,5)='20114' THEN  'A#04'
                                                                WHEN  RC.CateNo='202110201' THEN  'B#01#01'
                                                                WHEN  RC.CateNo='202110202' THEN  'B#01#02'
                                                              END ) as ItemID
                                                   FROM act_gl_books as A
                                                            INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                                   WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
                                                     AND (LEFT(RC.CateNo,5)='20111'
													 OR LEFT(RC.CateNo,5)='20112'
													 OR LEFT(RC.CateNo,5)='20113'
													 OR LEFT(RC.CateNo,5)='20114'
                                                      OR RC.CateNo = '202110201'
								                  OR RC.CateNo = '202110202')
                                                       $whereCondition
                                                   GROUP BY A.AccountNo, YearD, ItemID, RevenueCateNo
                                                  ) AS  B GROUP BY ItemID, RevenueCateNo
                                        ) AS HH
                                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function A32($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            MAX(CASE WHEN  AccountNo = '0461'  AND B.Yeard = $yearPre THEN I END) AS I1,
                            MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                            MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                            MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense AS E ON E.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND E.ExpenseNo IN ('51060101','510699','51060201')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                ) AS HH
                SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                WHERE TT.ItemID = 'A#03#02'";
        DB::statement($sqlUpdate);
    }


    public function BII2($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            MAX(CASE WHEN  AccountNo = '0361' AND B.Yeard = $yearPre THEN I END) AS I1,
                            MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                            MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                            MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
                                AND RC.CateValue IN ('1','2') AND
                                      RC.CateNo = '201210102'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                    WHERE TT.ItemID = 'B#02#02'";
        DB::statement($sqlUpdate);
    }
    public function BII1_III_IV_V($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,RevenueCateNo,
                               MAX(CASE WHEN  AccountNo = '0361' AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,RC.CateNo as RevenueCateNo, YEAR(A.PostDate)AS YearD,
                                     (Case when RC.CateNo = '201210101' THEN 'B#02#01'
                                           when RC.CateNo = '20124' THEN 'B#03'
                                           when RC.CateNo = '20123' THEN 'B#04'
                                           when RC.CateNo = '20122' THEN 'B#05' END) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                              WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
                                AND RC.CateValue = '1'
                                AND RC.CateNo IN ('201210101','20124','20123','20122')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, ItemID, RevenueCateNo
                             ) AS  B GROUP BY ItemID, RevenueCateNo
                            ) AS HH
                            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                            WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function CI_1($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                               MAX(CASE WHEN  AccountNo = '0461' AND B.Yeard = 2020 THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = 2021 THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = 2021 THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = 2022 THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND (LEFT(EC.CateNo,5) IN ('20152','20153') OR EC.CateNo='2016202')
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                    WHERE  TT.ItemID = 'C#01#01'";
        DB::statement($sqlUpdate);
    }
    public function CI_2_3_4_III($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,ExpenseCateNo,
                               MAX(CASE WHEN  AccountNo = '0461' AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,EC.CateNo as ExpenseCateNo, YEAR(A.PostDate)AS YearD,
                                         (Case  when LEFT(EC.CateNo,5) = '20151' THEN 'C#01#02'
                                           when EC.CateNo = '20154' THEN 'C#01#03'
                                           when EC.CateNo = '20159' THEN 'C#01#04'
                                           when EC.CateNo = '2016103' THEN 'C#03' END) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND ( LEFT(EC.CateNo,5) = '20151' OR (EC.CateNo IN ('20154','20159','2016103') AND EC.CateValue ='1'))
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, ItemID, ExpenseCateNo
                             ) AS  B GROUP BY ItemID, ExpenseCateNo
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4, TT.ItemID = HH.ItemID
                    WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function CI_5($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            MAX(CASE WHEN  AccountNo = '0461' AND B.Yeard = 2020 THEN I END) AS I1,
                            MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = 2021 THEN I END) AS I2,
                            MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = 2021 THEN I END) AS I3,
                            MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = 2022 THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND CC.CateNo = '200194'
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                    WHERE  TT.ItemID = 'C#01#05'";
        DB::statement($sqlUpdate);
    }
    public function CII1_3($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,ProgramCateValue,
                               MAX(CASE WHEN  AccountNo = '0461' AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,PC.CateValue as ProgramCateValue, YEAR(A.PostDate)AS YearD,
                                     (Case when PC.CateValue = '5' THEN 'C#02#01'
                                           when PC.CateValue = '2' THEN 'C#02#03' END) as ItemID
                              FROM act_gl_books as A
                                       INNER JOIN project_cate AS PC ON PC.ProjectID = A.ProjectID
                                       INNER JOIN program_cate AS PGC ON PGC.ProgramID = A.ProgramID
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND EC.CateNo = '201610101' AND EC.CateValue ='2'
                                AND PC.CateNo ='006'
                                AND ((PGC.CateNo = '001' AND PGC.CateValue ='2' AND PC.CateValue ='5')
                                    OR (PGC.CateNo = '001' AND PGC.CateValue ='1' AND PC.CateValue ='2'))
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, ItemID, ProgramCateValue
                             ) AS  B GROUP BY ItemID, ProgramCateValue
                            ) AS HH
                            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                            WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function CII_2($whereCondition, $yearNext, $year, $yearPre){
    $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                               MAX(CASE WHEN  AccountNo = '0461' AND B.Yeard = $yearPre THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN project_cate AS PC ON PC.ProjectID = A.ProjectID
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND EC.CateNo = '201610101' AND EC.CateValue ='2'
                                AND PC.CateNo ='006'
                                AND PC.CateValue ='7'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                            ) AS HH
                            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                            WHERE  TT.ItemID = 'C#02#02'";
    DB::statement($sqlUpdate);
}





    public function makeTree(){
        $listChildNull = DB::table('table_tmp')->wherNull('I1')->wherNull('I2')->wherNull('I3')->wherNull('I4')->get();
       if( count($listChildNull) > 0 ){
           foreach ($listChildNull as $childNull){
               $listChild = DB::table('table_tmp')-> where('ParentID', $childNull->ParentID)
                                                            ->whereNotNull('I1')->orWhereNotNull('I2')->orWhereNotNull('I3')->orWhereNotNull('I4')->get()->toArray();
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
