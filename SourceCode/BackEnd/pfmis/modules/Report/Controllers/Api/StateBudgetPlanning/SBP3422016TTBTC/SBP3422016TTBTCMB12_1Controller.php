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

class SBP3422016TTBTCMB12_1Controller extends Controller {
    public function SBP3422016TTBTC_MB12_1(Request $request){

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
          `AccountNoI1` varchar(20) DEFAULT '',
          `AccountNoI2` varchar(20) DEFAULT '',
          `AccountNoI3` varchar(20) DEFAULT '',
          `AccountNoI4` varchar(20) DEFAULT '',
          `NormCateNo` varchar (20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
          `RevenueCateNo` varchar(20) DEFAULT '',
          `RevenueCateValue` varchar(20) DEFAULT '',
          `CapitalCateNo` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb1201')->get();

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
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->AI1($whereCondition, $yearNext, $year, $yearPre);
        $this->AI1Note($whereCondition, $yearNext, $year, $yearPre);
        $this->AI2($whereCondition, $yearNext, $year, $yearPre);
        $this->AI31($whereCondition, $yearNext, $year, $yearPre);
        $this->AI32($whereCondition, $yearNext, $year, $yearPre);
        $this->AI4($whereCondition, $yearNext, $year, $yearPre);
        $this->AII1_AII2($whereCondition, $yearNext, $year, $yearPre);
        $this->AII31($whereCondition, $yearNext, $year, $yearPre);
        $this->AII32($whereCondition, $yearNext, $year, $yearPre);
        $this->AII4($whereCondition, $yearNext, $year, $yearPre);
        $this->BI1($whereCondition, $yearNext, $year, $yearPre);
        $this->BI21($whereCondition, $yearNext, $year, $yearPre);
        $this->BI22($whereCondition, $yearNext, $year, $yearPre);
        $this->BI3($whereCondition, $yearNext, $year, $yearPre);
        $this->BII1($whereCondition, $yearNext, $year, $yearPre);
        $this->BII2($whereCondition, $yearNext, $year, $yearPre);
        $this->BII3($whereCondition, $yearNext, $year, $yearPre);
        $this->C($whereCondition, $yearNext, $year, $yearPre);
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
    // ===================================== A =====================================
    public function AI1($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT RevenueCateValue, NormCateValue,
                                MAX(CASE WHEN  AccountNo = '0362'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0342' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0712' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0312' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, RC.CateNo AS RevenueCateValue, NC.CateValue AS NormCateValue ,YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                       INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0362', '0342', '0712', '0312')
                                AND NC.CateNo = '1104'
                                AND  (CC.CateNo = '1900' AND  CC.CateValue = 5)
                                AND (SC.CateNo = 001 AND SC.CateValue IN ('1','2','3','4','5','6','7','8','9','11'))
                                $whereCondition
                              GROUP BY A.AccountNo, YearD, RevenueCateValue, NormCateValue
                             ) AS  B GROUP BY  RevenueCateValue, NormCateValue
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE  TT.RevenueCateNo = HH.RevenueCateValue AND TT.NormCateValue = HH.NormCateValue AND TT.ParentID = 'A#01#01'";
        DB::statement($sqlUpdate);
    }
    public function AI1Note($whereCondition, $yearNext, $year, $yearPre){
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
                                       INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('362', '0342', '0712', '0312')
                                AND NC.CateNo = '1104' AND NC.CateNo = 1
                                AND RC.CateNo = '31010102'
                                AND  (CC.CateNo = '1900' AND  CC.CateValue = 5)
                                AND (SC.CateNo = 001 AND SC.CateValue IN ('1','2','3','4','5','6','7','8','9','11'))
                              GROUP BY A.AccountNo, YearD
                              $whereCondition
                             ) AS  B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE   TT.ItemID = 'A#01#01#01#01'";
        DB::statement($sqlUpdate);
    }
    public function AI2($whereCondition, $yearNext, $year, $yearPre){
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
                                       INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0362', '0342', '0712', '0312')
                                AND NC.CateNo = '1102' AND  NC.CateValue = 1
                                AND RC.CateNo = '2011104' AND  RC.CateValue = 1
                                AND  (CC.CateNo = '1900' AND  CC.CateValue = 5)
                                AND (SC.CateNo = 001 AND SC.CateValue IN ('1','2','3','4','5','6','7','8','9','11'))
                                $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS  B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE   TT.ItemID = 'A#01#02'";
        DB::statement($sqlUpdate);
    }

    public function AI31($whereCondition, $yearNext, $year, $yearPre){
        $sql = "UPDATE table_tmp AS TT,(SELECT NormCateValue,
                                MAX(CASE WHEN  AccountNo = '0361'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, NC.CateValue AS NormCateValue ,YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                       INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
                                AND NC.CateNo = '110301'
                                AND RC.CateNo = '201'
                                AND  (CC.CateNo = '1900' AND  CC.CateValue = 5)
                                AND (SC.CateNo = 001 AND SC.CateValue IN ('1','2','3','4','5','6','7','8','9','11'))
                                $whereCondition
                              GROUP BY A.AccountNo, YearD, NormCateValue
                             ) AS  B GROUP BY  NormCateValue
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE   TT.NormCateValue = HH.NormCateValue AND TT.ParentID = 'A#01#03#01'";
        DB::statement($sql);
    }

    public function AI32($whereCondition, $yearNext, $year, $yearPre){
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
                                       INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('361', '0341', '0711', '0311')
                                AND NC.CateNo = '110303'
                                AND RC.CateNo = '201'
                                AND  (CC.CateNo = '1900' AND  CC.CateValue = 5)
                                AND (SC.CateNo = 001 AND SC.CateValue IN ('1','2','3','4','5','6','7','8','9','11'))
                                $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE    TT.ItemID = 'A#01#03#02'";
    }
    public function AI4($whereCondition, $yearNext, $year, $yearPre){
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
                                       INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0362', '0342', '0712', '0312')
                                AND NC.CateNo = '1105'
                                AND RC.CateNo = '202'
                                AND  (CC.CateNo = '1900' AND  CC.CateValue = 5)
                                AND (SC.CateNo = 001 AND SC.CateValue IN ('1','2','3','4','5','6','7','8','9','11'))
                                $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE    TT.ItemID = 'A#01#04'";
        DB::statement($sql);
    }
    public function AII1_AII2($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT NormCateValue, ParentID,
                                MAX(CASE WHEN  AccountNo = '0462'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0442' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0722' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0412' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, NC.CateValue AS NormCateValue,  YEAR(A.PeriodFromDate)AS YearD,
                                     (Case WHEN NC.CateNo = '1202' AND CptC.CateNo = '200211' THEN  'A#02#01'
                                         WHEN NC.CateNo = '1204' AND CptC.CateNo = '200212' THEN  'A#02#02' END ) AS ParentID
                              FROM act_gl_books as A
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN capital_cate AS CptC ON CptC.CapitalID = A.CapitalID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                       INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0462', '0442', '0722', '0412')
                                AND (NC.CateNo = '1204' OR NC.CateNo = '1202')
                                AND (CptC.CateNo = '200211' OR CptC.CateNo = '200212')
                                AND  (CC.CateNo = '1900' AND  CC.CateValue = 5)
                                AND (SC.CateNo = 001 AND SC.CateValue IN ('1','2','3','4','5','6','7','8','9','11'))
                                $whereCondition
                              GROUP BY A.AccountNo, YearD, NormCateValue, ParentID
                             ) AS B GROUP BY   NormCateValue, ParentID
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE    TT.NormCateValue = HH.NormCateValue AND TT.ParentID = HH.ParentID";
        DB::statement($sqlUpdate);
    }
    public function AII31($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0461'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,   YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN capital_cate AS CptC ON CptC.CapitalID = A.CapitalID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                       INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND CptC.CateNo = '200111' OR CptC.CateNo = '200121'
                                AND  (CC.CateNo = '1900' AND  CC.CateValue = 5)
                                AND (SC.CateNo = 001 AND SC.CateValue IN ('1','2','3','4','5','6','7','8','9','11'))
                                $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID = 'A#02#03#01'";
        DB::statement($sqlUpdate);
    }
    public  function AII32($whereCondition, $yearNext, $year, $yearPre){
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
                                       INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0461', '0441', '0721', '0411')
                                AND CptC.CateNo = '200112' OR CptC.CateNo = '20012202' OR CptC.CateNo = '20012203' OR CptC.CateNo = '20012204'
                                AND NC.CateNo = '110303'
                                AND  (CC.CateNo = '1900' AND  CC.CateValue = 5)
                                AND (SC.CateNo = 001 AND SC.CateValue IN ('1','2','3','4','5','6','7','8','9','11'))
                                $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID = 'A#02#03#02'";
        DB::statement($sqlUpdate);
    }
    public  function AII4($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0462'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0442' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0722' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0412' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,   YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                       INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0462', '0442', '0722', '0412')
                                AND NC.CateNo = '1205'
                                AND  (CC.CateNo = '1900' AND  CC.CateValue = 5)
                                AND (SC.CateNo = 001 AND SC.CateValue IN ('1','2','3','4','5','6','7','8','9','11'))
                                $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID = 'A#02#04'";
        DB::statement($sqlUpdate);
    }

    // ============================= B =============================

    public function BI1($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate="UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0362'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0342' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0712' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0312' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,  YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                    INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                    INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                    INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                    INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0362', '0342', '0712', '0312')
                                    AND NC.CateNo = '1104' AND NC.CateValue = 3
                                    AND RC.CateNo = '2013102'
                                    AND  (CC.CateNo = '1900' AND  CC.CateValue != 5)
                                    AND (SC.CateNo = 001 AND SC.CateValue = 12 OR SC.CateNo = 13 OR  Sc.CateNo = 14)
                                    $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID = 'B#01#01'
";
        DB::statement($sqlUpdate);
    }
    public function BI21($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT NormCateValue,
                                MAX(CASE WHEN  AccountNo = '0361'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo, NC.CateValue AS NormCateValue,  YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                    INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                    INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                    INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                    INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
                                    AND NC.CateNo = '110301'
                                    AND RC.CateNo = '201'
                                    AND  (CC.CateNo = '1900' AND  CC.CateValue != 5)
                                    AND (SC.CateNo = 001 AND SC.CateValue = 12 OR SC.CateNo = 13 OR  Sc.CateNo = 14)
                                    $whereCondition
                              GROUP BY A.AccountNo, YearD, NormCateValue
                             ) AS B GROUP BY  NormCateValue
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.NormCateValue = HH.NormCateValue AND TT.ParentID = 'A#01#02#01'";
        DB::statement($sqlUpdate);
    }
    public function BI22($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate ="UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0361'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0341' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0711' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0311' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,  YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                    INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                    INNER JOIN  capital_cate AS CptC ON CptC.CapitalID = A.CapitalID
                                    INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                    INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0361', '341', '0711', '311')
                                    AND NC.CateNo = '110303'
                                    AND CptC.CateNo IN ('200112','20012202','20012203','20012204')
                                    AND  (CC.CateNo = '1900' AND  CC.CateValue != 5)
                                    AND (SC.CateNo = 001 AND SC.CateValue = 12 OR SC.CateNo = 13 OR  Sc.CateNo = 14)
                                    $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID = 'B#01#02#02'";
        DB::statement($sqlUpdate);
    }
    public function BI3($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0362'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0342' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0712' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0312' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,  YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                    INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                    INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                    INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                    INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0362', '0342', '0712', '0312')
                                    AND NC.CateNo = '1105'
                                    AND RC.CateNo = '202'
                                    AND  (CC.CateNo = '1900' AND  CC.CateValue != 5)
                                    AND (SC.CateNo = 001 AND SC.CateValue = 12 OR SC.CateNo = 13 OR  Sc.CateNo = 14)
                                    $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID = 'B#01#03'";
        DB::statement($sqlUpdate);
    }
    public function BII1($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0462'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0442' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0722' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0412' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,  YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                    INNER JOIN project_cate AS PC ON PC.ProjectID = A.ProjectID
                                    INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                    INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0462', '0442', '0722', '0312')
                                    AND PC.CateNo = '008' AND  PC.CateValue = 3
                                    AND  (CC.CateNo = '1900' AND  CC.CateValue != 5)
                                    AND (SC.CateNo = 001 AND SC.CateValue = 12 OR SC.CateNo = 13 OR  Sc.CateNo = 14)
                                    $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.ItemID = 'B#02#01'";
        DB::statement($sqlUpdate);
    }
    public function BII2($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT CapitalCateNo,
                                MAX(CASE WHEN  AccountNo = '0461'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,  YEAR(A.PeriodFromDate)AS YearD,
                                    (CASE  WHEN  (CptC.CateNo ='1101' OR CptC.CateNo = '1201') THEN '1101'
                                           WHEN  (CptC.CateNo ='1102' OR CptC.CateNo = '1202') THEN '1102' END ) AS CapitalCateNo
                              FROM act_gl_books as A
                                    INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                    INNER JOIN capital_cate AS CptC ON CptC.CapitalID = A.CapitalID
                                    INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                    INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0461', '0441', '0711', '0411')
                                    AND (NC.CateNo ='1201' OR  NC.CateNo = '1203')
                                    AND CptC.CateNo IN ('1101','1201','1102','1202')
                                    AND  (CC.CateNo = '1900' AND  CC.CateValue != 5)
                                    AND (SC.CateNo = '001' AND SC.CateValue = 12 OR SC.CateNo = 13 OR  Sc.CateNo = 14)
                                    $whereCondition
                              GROUP BY A.AccountNo, YearD, CapitalCateNo
                             ) AS B GROUP BY CapitalCateNo
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE  TT.CapitalCateNo = HH.CapitalCateNo AND TT.ParentID = 'B#02#02'";
        DB::statement($sqlUpdate);
    }
    public function BII3($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0462'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0442' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0722' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0412' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,  YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                    INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                    INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                    INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0462', '0442', '0712', '0412')
                                    AND NC.CateNo = '1205'
                                    AND  (CC.CateNo = '1900' AND  CC.CateValue != 5)
                                    AND (SC.CateNo = 001 AND SC.CateValue = 12 OR SC.CateNo = 13 OR  Sc.CateNo = 14)
                                    $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE  ItemID = 'B#02#03'";
        DB::statement($sqlUpdate);
    }
    public function C($whereCondition, $yearNext, $year, $yearPre){
        $sqlUpdate ="UPDATE table_tmp AS TT,(SELECT
                                MAX(CASE WHEN  AccountNo = '0461'  AND B.Yeard = $yearPre THEN I END) AS I1,
                                MAX(CASE WHEN  AccountNo = '0441' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0721' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0411' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,  YEAR(A.PeriodFromDate)AS YearD
                              FROM act_gl_books as A
                                    INNER JOIN company_cate AS CC ON CC.CompanyID = A.InputCompanyID
                                    INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0461', '0441', '0711', '0411')
                                    AND  CC.CateNo != 1900
                                    AND (SC.CateNo = 001 AND SC.CateValue = 3)
                                    $whereCondition
                              GROUP BY A.AccountNo, YearD
                             ) AS B
                        ) AS HH
                        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE  ItemID = 'C'";
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
