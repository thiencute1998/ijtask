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

class SBP3422016TTBTCMB32Controller extends Controller {
    public function SBP3422016TTBTC_MB32(Request $request){

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
          `AccountNoI1` varchar(20) DEFAULT '',
          `AccountNoI2` varchar(20) DEFAULT '',
          `AccountNoI3` varchar(20) DEFAULT '',
          `SbiCategoryCateValue` varchar(20) DEFAULT '',
          `RevenueCateNo` varchar(20) DEFAULT '',
          `RevenueCateValue` varchar(20) DEFAULT '',
          `ProjectCateValue` varchar(20) DEFAULT '',
          `CapitalCateNo` varchar(20) DEFAULT '',
          `ExpenseCateNo` varchar(20) DEFAULT '',
          `CapitalCateValue` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb32')->get();

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
                'RevenueCateValue' => $template->RevenueCateValue,
                'ProjectCateValue' => $template->ProjectCateValue,
                'RevenueCateNo' => $template->RevenueCateNo,
                'CapitalCateValue' => $template->CapitalCateValue,
                'SbiCategoryCateValue' => $template->SbiCategoryCateValue,
                'CapitalCateNo' => $template->CapitalCateNo,
                'ExpenseCateNo' => $template->ExpenseCateNo,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->AI1_1($whereCondition, $yearNext, $year);
        $this->AI6($whereCondition, $yearNext, $year);
        $this->AI1_2($whereCondition, $yearNext, $year);
        $this->AI1_2_1($whereCondition, $yearNext, $year);
        $this->AI1_2_2($whereCondition, $yearNext, $year);
        $this->AI2($whereCondition, $yearNext, $year);
        $this->AI3($whereCondition, $yearNext, $year);
        $this->AI4_5($whereCondition, $yearNext, $year);
        $this->AII($whereCondition, $yearNext, $year);
        $this->B1($whereCondition, $yearNext, $year);
        $this->B2($whereCondition, $yearNext, $year);
        $this->C($whereCondition, $yearNext, $year);
        // sum up
        $this->sumUpTableTmp(1, 5);
        $item1 = DB::table('table_tmp')->where('ItemID', '=', 'B#01')->first();
        $item2 = DB::table('table_tmp')->where('ItemID', '=', 'B#02')->first();
        DB::table('table_tmp')->where('ItemID','=','B')
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
    public function AI1_1($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                            MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                            MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0441','0721','0311')
                                AND A.CapitalNo = '121212'
                                AND EC.CateNo = '2015202'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2,TT.I3 = HH.I3
                        WHERE TT.ItemID = 'A#01#01#01'";
        DB::statement($sqlUpdate);
    }
    public function AI6($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                            MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                            MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0441','0721','0311')
                                AND CC.CateNo = '200194'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2,TT.I3 = HH.I3
                        WHERE TT.ItemID = 'A#01#06'";
        DB::statement($sqlUpdate);
    }

    public function AI1_2($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                             MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                             MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                             MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0721','0311','0441')
                                AND EC.CateNo IN ('2015202','2015203')
                                    $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3
                        WHERE TT.ItemID = 'A#01#01#02'";
        DB::statement($sqlUpdate);
    }
    public function AI1_2_1($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT CapitalCateValue,
                            MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                            MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                            MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, CC.CateValue AS CapitalCateValue ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE A.AccountNo IN ('0721','0311','0441')
                                AND EC.CateNo IN ('2015202','2015203')
                                AND CC.CateNo = '20012102'
                               $whereCondition
                              GROUP BY A.AccountNo, YearD, CapitalCateValue
                             ) AS  B GROUP BY  CapitalCateValue
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3
                        WHERE TT.CapitalCateValue = HH.CapitalCateValue AND TT.ParentID = 'A#01#01#02#01'";
        DB::statement($sqlUpdate);
    }
    public function AI1_2_2($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT SbiCategoryCateValue,
                            MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                            MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                            MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, SC.CateValue AS SbiCategoryCateValue ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                                       INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0721','0311','0441')
                                AND EC.CateNo IN ('2015202','2015203')
                                AND CC.CateNo = '20012102'
                                AND SC.CateNo = '001'
                              $whereCondition
                              GROUP BY A.AccountNo, YearD, SbiCategoryCateValue
                             ) AS  B GROUP BY  SbiCategoryCateValue
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3
                    WHERE TT.SbiCategoryCateValue = HH.SbiCategoryCateValue AND TT.ParentID = 'A#01#01#02#02'";
        DB::statement($sqlUpdate);
    }
    public function AI2($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT SbiCategoryCateValue,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, SC.CateValue AS SbiCategoryCateValue ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                                       INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0721','0311','0441')
                                AND CC.CateNo IN ('200111','200121')
                                AND SC.CateNo = '001'
                                $whereCondition
                              GROUP BY A.AccountNo, YearD, SbiCategoryCateValue
                             ) AS  B GROUP BY  SbiCategoryCateValue
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3
                        WHERE TT.SbiCategoryCateValue = HH.SbiCategoryCateValue AND TT.ParentID = 'A#01#02'";
                                DB::statement($sqlUpdate);
    }
    public function AI3($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0721','0311','0441')
                                AND EC.CateNo = '20153'
                                AND EC.CateValue IN ('1','2')
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2,TT.I3 = HH.I3
                        WHERE TT.ItemID = 'A#01#03'";
        DB::statement($sqlUpdate);
    }
    public function AI4_5($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ExpenseCateNo,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, EC.CateNo AS ExpenseCateNo  ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                              WHERE A.AccountNo IN ('0721','0311','0441')
                                AND EC.CateValue = '1'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, ExpenseCateNo
                             ) AS  B group by ExpenseCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2,TT.I3 = HH.I3
                        WHERE TT.ExpenseCateNo = HH.ExpenseCateNo AND TT.ParentID = 'A#01'";
        DB::statement($sqlUpdate);
    }
    public function AII($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ProjectCateValue,
                               MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, PJ.CateValue AS ProjectCateValue ,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                                       INNER JOIN project_cate AS PJ ON PJ.ProjectID = A.ProjectID
                               WHERE A.AccountNo IN ('0411','0721','0441')
                                AND ( (CC.CateNo = '20012102' AND CC.CateValue = '9')
                                OR (CC.CateNo IN ('20012201','20012202','20012203') AND CC.CateValue = '2') )
                                AND PJ.CateNo = '006'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD, ProjectCateValue
                             ) AS  B group by ProjectCateValue
                    ) AS HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2 ,TT.I3 = HH.I3
                    WHERE TT.ProjectCateValue = HH.ProjectCateValue AND TT.ParentID = 'A'";
        DB::statement($sqlUpdate);
    }
    public function B1($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I1,
                            MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I2,
                            MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                              WHERE A.AccountNo IN ('0311','0711','0311')
                                AND A.RevenueID is not NULL
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                            ) AS HH
                            SET TT.I1 = HH.I1, TT.I2 = HH.I2,TT.I3 = HH.I3
                            WHERE  TT.ItemID = 'B#01'";
        DB::statement($sqlUpdate);
    }
    public function B2($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                            MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                            MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                              WHERE A.AccountNo IN ('0441','0721','0411')
                                AND A.ExpenseID is not NULL
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                            ) AS HH
                            SET TT.I1 = HH.I1, TT.I2 = HH.I2,TT.I3 = HH.I3
                            WHERE  TT.ItemID = 'B#02'";
        DB::statement($sqlUpdate);
    }

    public function C($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I1,
                            MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I2,
                            MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I3
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN expense_cate AS EC ON EC.ExpenseID=A.ExpenseID
                              WHERE A.AccountNo IN ('0721','0411','0441')
                                AND EC.CateNo = '2016103'
                                AND EC.CateValue = '1'
                                  $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                            ) AS HH
                            SET TT.I1 = HH.I1, TT.I2 = HH.I2,TT.I3 = HH.I3
                            WHERE  TT.ItemID = 'C'";
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
