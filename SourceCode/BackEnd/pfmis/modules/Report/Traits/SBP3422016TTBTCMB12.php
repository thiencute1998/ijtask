<?php
namespace Module\Report\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;

Trait SBP3422016TTBTCMB12
{
    public function SBP3422016TTBTC_MB12($request, $companyCateValue){
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
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `ConAccountNoI1` varchar(20) DEFAULT '',
          `ConAccountNoI2` varchar(20) DEFAULT '',
          `ConAccountNoI3` varchar(20) DEFAULT '',
          `ConAccountNoI4` varchar(20) DEFAULT '',
          `NormCateNo` varchar (20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
          `RevenueCateNo` varchar(20) DEFAULT '',
          `RevenueCateValue` varchar(20) DEFAULT '',
          `CapitalCateNo` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
         `GroupItems` varchar(25) DEFAULT '',
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb1202')->get();

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
                'ConAccountNoI1' => $template->ConAccountNoI1,
                'ConAccountNoI2' => $template->ConAccountNoI2,
                'ConAccountNoI3' => $template->ConAccountNoI3,
                'ConAccountNoI4' => $template->ConAccountNoI4,
                'NormCateValue' => $template->NormCateValue,
                'NormCateNo' => $template->NormCateValue,
                'RevenueCateNo' => $template->RevenueCateNo,
                'RevenueCateValue' => $template->RevenueCateValue,
                'CapitalCateNo' => $template->CapitalCateNo,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
                'GroupItems' => $template->GroupItems,

            ];
        }
        $this->I1_I31($whereCondition, $yearNext, $year, $yearPre,$companyCateValue);
        $this->I2($whereCondition, $yearNext, $year, $yearPre,$companyCateValue);
        $this->I1Note($whereCondition, $yearNext, $year, $yearPre,$companyCateValue);
        $this->I32($whereCondition, $yearNext, $year, $yearPre, $companyCateValue);
        $this->I4($whereCondition, $yearNext, $year, $yearPre,$companyCateValue);
        $this->II1_II2($whereCondition, $yearNext, $year, $yearPre, $companyCateValue);
        $this->II31($whereCondition, $yearNext, $year, $yearPre, $companyCateValue);
        $this->II32($whereCondition, $yearNext, $year, $yearPre, $companyCateValue);
        $this->II4($whereCondition, $yearNext, $year, $yearPre, $companyCateValue);
        // sum up
        $this->sumUpTableTmp(1, 5);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
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
    public function I1_I31($whereCondition, $yearNext, $year, $yearPre, $companyCateValue){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT NormCateValue, ParentID,
                            MAX(CASE WHEN  (AccountNo = '0362' OR AccountNo = '0361')  AND B.Yeard = $yearPre THEN I END) AS I1,
                            MAX(CASE WHEN  (AccountNo = '0342' OR AccountNo = '0341') AND B.Yeard = $year THEN I END) AS I2,
                            MAX(CASE WHEN  (AccountNo = '0712' OR AccountNo = '0711') AND B.Yeard = $year THEN I END) AS I3,
                            MAX(CASE WHEN  (AccountNo = '0312' OR AccountNo = '0311') AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, NC.CateValue AS NormCateValue, YEAR(A.PeriodFromDate)AS YearD,
                                     (Case WHEN (RC.CateNo = '2013101' OR RC.CateNo = '2013199' OR RC.CateNo = '2013102') AND NC.CateNo ='1104' THEN '01#01'
                                         WHEN RC.CateNo ='201' AND NC.CateNo ='110301' THEN '01#03#01' END ) AS ParentID
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                              WHERE A.AccountNo IN ('362', '0342', '0712', '0312', '0361', '0341', '0711', '0311')
                                AND (CC.CateNo = '1700' AND  CC.CateValue = $companyCateValue)
                                $whereCondition
                              GROUP BY A.AccountNo, YearD, NormCateValue, PartnerID
                             ) AS  B GROUP BY NormCateValue, ParentID
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE   TT.ParentID = HH.ParentID AND TT.NormCateValue = HH.NormCateValue";
        DB::statement($sqlUpdate);
    }

    public function I1Note($whereCondition, $yearNext, $year, $yearPre, $companyCateValue){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                            MAX(CASE WHEN  AccountNo = '0362'  AND B.Yeard = $yearPre THEN I END) AS I1,
                            MAX(CASE WHEN  AccountNo = '0342' AND B.Yeard = $year THEN I END) AS I2,
                            MAX(CASE WHEN  AccountNo = '0712' AND B.Yeard = $year THEN I END) AS I3,
                            MAX(CASE WHEN  AccountNo = '0312' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                              WHERE A.AccountNo IN ('362', '0342', '0712', '0312')
                                AND NC.CateNo = '1104' AND NC.CateNo = 1
                                AND RC.CateNo = '31010102'
                                AND (CC.CateNo = '1700' AND  CC.CateValue = $companyCateValue)
                              GROUP BY A.AccountNo, YearD
                              $whereCondition
                             ) AS  B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE   TT.ItemID = '01#01#01#01'";
        DB::statement($sqlUpdate);
    }
    public function I2($whereCondition, $yearNext, $year, $yearPre, $companyCateValue){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0362'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0342' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0712' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0312' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo ,YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                              WHERE A.AccountNo IN ('0362', '0342', '0712', '0312')
                                AND NC.CateNo = '1102' AND  NC.CateValue = 1
                                AND RC.CateNo = '2011104' AND  RC.CateValue = 1
                                AND (CC.CateNo = '1700' AND  CC.CateValue = $companyCateValue)
                                $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE   TT.ItemID = '01#02'";
        DB::statement($sqlUpdate);
    }
    public function I32($whereCondition, $yearNext, $year, $yearPre, $companyCateValue){
        $sql = "UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0361'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                              WHERE A.AccountNo IN ('361', '0341', '0711', '0311')
                                AND NC.CateNo = '110303'
                                AND RC.CateNo = '201'
                                AND (CC.CateNo = '1700' AND  CC.CateValue = $companyCateValue)
                                $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE    TT.ItemID = '01#03#02'";
        DB::statement($sql);
    }
    public function I4($whereCondition, $yearNext, $year, $yearPre, $companyCateValue){
        $sql = "UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0362'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0342' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0712' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0312' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                              WHERE A.AccountNo IN ('0362', '0342', '0712', '0312')
                                AND NC.CateNo = '1105'
                                AND RC.CateNo = '202'
                                AND (CC.CateNo = '1700' AND  CC.CateValue = $companyCateValue)
                                $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE    TT.ItemID = '01#04'";
        DB::statement($sql);
    }
    public function II1_II2($whereCondition, $yearNext, $year, $yearPre, $companyCateValue){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT NormCateValue, ParentID,
                                MAX(CASE WHEN  AccountNo = '0462'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0442' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0722' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0412' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, NC.CateValue AS NormCateValue,  YEAR(A.PeriodFromDate)AS YearD,
                                     (Case WHEN NC.CateNo = '1202' AND CptC.CateNo = '200211' THEN  '02#01'
                                         WHEN NC.CateNo = '1204' AND CptC.CateNo = '200212' THEN  '02#02' END ) AS ParentID
                              FROM act_gl_books as A
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN capital_cate AS CptC ON CptC.CapitalID = A.CapitalID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                              WHERE A.AccountNo IN ('0462', '0442', '0722', '0412')
                                AND (NC.CateNo = '1204' OR NC.CateNo = '1202')
                                AND (CptC.CateNo = '200211' OR CptC.CateNo = '200212')
                                AND (CC.CateNo = '1700' AND  CC.CateValue = $companyCateValue)
                                $whereCondition
                              GROUP BY A.AccountNo, YearD, NormCateValue, ParentID
                             ) AS B GROUP BY   NormCateValue, ParentID
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE    TT.NormCateValue = HH.NormCateValue AND TT.ParentID = HH.ParentID";
        DB::statement($sqlUpdate);
    }
    public function II31($whereCondition, $yearNext, $year, $yearPre, $companyCateValue){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0461'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,   YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN capital_cate AS CptC ON CptC.CapitalID = A.CapitalID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND CptC.CateNo = '200111' OR CptC.CateNo = '200121'
                                AND (CC.CateNo = '1700' AND  CC.CateValue = $companyCateValue)
                                $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID = '02#03#01'";
        DB::statement($sqlUpdate);
    }
    public  function II32($whereCondition, $yearNext, $year, $yearPre, $companyCateValue){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0461'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,   YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN capital_cate AS CptC ON CptC.CapitalID = A.CapitalID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND CptC.CateNo = '200112' OR CptC.CateNo = '20012202' OR CptC.CateNo = '20012203' OR CptC.CateNo = '20012204'
                                AND NC.CateNo = '110303'
                                AND (CC.CateNo = '1700' AND  CC.CateValue = $companyCateValue)
                                $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID = '02#03#02'";
        DB::statement($sqlUpdate);
    }
    public  function II4($whereCondition, $yearNext, $year, $yearPre, $companyCateValue){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0462'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0442' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0722' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0412' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,   YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                              WHERE A.AccountNo IN ('0462', '0442', '0722', '0412')
                                AND NC.CateNo = '1205'
                                AND (CC.CateNo = '1700' AND  CC.CateValue = $companyCateValue)
                                $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID = '02#04'";
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
