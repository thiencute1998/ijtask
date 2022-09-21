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

class SBP3422016TTBTCMB35Controller extends Controller {
    public function SBP3422016TTBTC_MB35(Request $request){
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
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
            `SCCCateNo` varchar(25) DEFAULT NULL,
           `ExpenseCateNo` varchar(25) DEFAULT NULL,
           `ProgramCateValue` varchar(25) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb35')->get();

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
                'ProgramCateValue' => $template->ProgramCateValue,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
                'SCCCateNo' => $template->SCCCateNo,
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->A($whereCondition, $yearNext, $year, $yearPre);
        // sum up
//        $this->sumUpTableTmp(1, 5);

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
    public function A($whereCondition, $yearNext, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT RevenueCateNo,NormCateNo,SCCCateNo,
                               MAX(CASE WHEN  AccountNo = '0342' AND B.Yeard = $year THEN I END) AS I2,
                               MAX(CASE WHEN  AccountNo = '0712' AND B.Yeard = $year THEN I END) AS I3,
                               MAX(CASE WHEN  AccountNo = '0312' AND B.Yeard = $yearNext THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo,NC.CateNo as NormCateNo,RC.CateNo as RevenueCateNo,SCC.CateNo AS SCCCateNo, YEAR(A.PostDate)AS YearD
                              FROM act_gl_books as A
                                   INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                   INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                   INNER JOIN sbi_category_cate AS SCC ON SCC.SbiCategoryID = A.SbiCategoryID
                              WHERE A.AccountNo IN ('0342', '0712', '0312') AND RC.CateNo IN ('2013101','2013199') AND NC.CateNo='2011114' AND NC.CateValue IN ('1','2')
                                AND SCC.CateNo ='002' AND SCC.CateValue IN ('1','2','3','4','5','6','7','8','9','10','11')
                               $whereCondition
                              GROUP BY A.AccountNo, YearD, NormCateNo,RevenueCateNo,SCCCateNo
                             ) AS  B GROUP BY NormCateNo,RevenueCateNo,SCCCateNo
                        ) AS HH
                        SET TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                        WHERE TT.NormCateNo=HH.NormCateNo AND TT.RevenueCateNo=HH.RevenueCateNo AND TT.SCCCateNo=HH.SCCCateNo ";
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
                FROM table_tmp M
                WHERE M.Level = " . $i . " GROUP BY ParentID";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
                        $I2 = ($value['I2']) ? $value['I2'] : 0;
                        $I3 = ($value['I3']) ? $value['I3'] : 0;
                        $I4 = ($value['I4']) ? $value['I4'] : 0;
                        $update = "UPDATE table_tmp
                    SET I1 =  I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
