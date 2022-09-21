<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBPND312017NDCP;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class SBPND312017NDCPMB10Controller extends Controller {
    public function SBPND312017NDCP_MB10(Request $request){

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

        $preYear = (int)$year - 1;
        $nextYear = (int)$year + 1;
        $next2Year = (int)$year + 2;
        $whereCondition = "AND (
        ( YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $preYear AND YEAR(A.PeriodToDate) = $preYear)
        OR (YEAR(A.PeriodFromDate) = $nextYear AND YEAR(A.PeriodToDate) = $nextYear)
        ) $whereCompany  AND A.InTransTypeID NOT IN (16,17,18) ";

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
          `SbiCategoryValue` varchar(20) DEFAULT '',
          `ExpenseCateValue` varchar (20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          `I5` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
            $templateTable = DB::table('report_sbp_312017ndcp_bm10')->get();

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
                'SbiCategoryNo' => $template->SbiCategoryNo,
                'ConAccountNo' => $template->ConAccountNo,
                'ConCoAccountNo' => $template->ConCoAccountNo,
                'SbiCategoryValue' => $template->SbiCategoryValue,
                'ExpenseCateValue' => $template->ExpenseCateValue,
                'NormCateValue' => $template->NormCateValue,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
                'I5' => $template->I5,
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->updateA_I_II_I1_2_4_5($whereCondition,$year);
        $this->updateI1_2_4_5($whereCondition,$year);

        // sum up
        $this->sumUpTableTmp(1, 5);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Dự toán năm ' . $preYear;
            $data[0]->HeaderName2 = 'Dự toán ngân sách năm ' .$year;
            $data[0]->HeaderName3 = 'So sánh ' ;
            $data[0]->HeaderName4 = 'Dự kiến ngân sách năm ' .$nextYear;
            $data[0]->HeaderName5 = 'Dự kiến ngân sách năm ' .$next2Year;

        }
        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            if($data[$key]->I1 !=0){
                $data[$key]->I3 = round(((int)($data[$key]->I2) / (int)($data[$key]->I1))*100, 2);
            }
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
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
                "ReportName" => "DỰ KIẾN CHI NGÂN SÁCH CẤP TỈNH THEO CƠ CẤU CHI GIAI ĐOẠN 03 NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function updateA_I_II_I1_2_4_5($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, ExpenseCateValue,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year - 1  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year   THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1 THEN I END) AS I4,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 2 THEN I END) AS I5 FROM
            (SELECT  (Case when EC.CateNo = '201610101' AND  EC.CateValue ='1' THEN 'A#01'
                           when EC.CateNo = '201610101' AND  EC.CateValue ='2'  THEN 'A#02'
                           when  LEFT(EC.CateNo,7) = '2015201' AND CC.CateNo ='20012102'  THEN 'B#01#01'
                           when LEFT(EC.CateNo,6) = '201520' AND CC.CateNo ='20012102' AND CC.CateValue IN ('1','2') THEN 'B#01#02'
                           when LEFT(EC.CateNo,5) = '20151'  AND (LEFT(CC.CateNo,8) IN('20011101' ,'20012101')) THEN 'B#02'
                           when EC.CateNo = '20164' AND  EC.CateValue ='1' AND CC.CateNo ='200194' THEN 'B#03'
             END) as ItemID,
             EC.CateValue as ExpenseCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            WHERE  A.BudgetLevel =2
            and ((EC.CateNo IN ('201610101','20164') OR (LEFT(EC.CateNo,5) ='20151' ) OR LEFT(EC.CateNo,6) = '201520' OR LEFT(EC.CateNo,7) = '2015201') or EC.CateValue IN ('1','2')
            OR CC.CateNo IN('20012102','200194') OR LEFT(CC.CateNo,8) IN('20011101' ,'20012101') OR CC.CateValue IN ('1','2') )
            AND A.AccountNo IN('0441','0411')
            $whereCondition
            GROUP BY   YearD, A.AccountNo,ItemID) as CH GROUP BY AccountNo) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I4 = HH.I4,TT.I5 = HH.I5
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }

    public function updateI1_2_4_5($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID, ExpenseCateValue,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year - 1  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year   THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1 THEN I END) AS I4,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 2 THEN I END) AS I5 FROM
            (SELECT  (Case when LEFT(EC.CateNo,7) = '2015201' AND CC.CateNo ='20012102' AND PC.CateValue='1' THEN 'B#01#01#01#01'
                           when LEFT(EC.CateNo,7) = '2015201' AND CC.CateNo ='20012102' AND PC.CateValue='2'  THEN 'B#01#01#01#02'
                           when LEFT(EC.CateNo,5) = '20151'  AND (LEFT(CC.CateNo,8) IN('20011101' ,'20012101')) AND PC.CateValue='1'  THEN 'B#02#01#01'
                           when LEFT(EC.CateNo,5) = '20151' AND (LEFT(CC.CateNo,8) IN('20011101' ,'20012101')) AND PC.CateValue='2'  THEN 'B#02#01#02'
             END) as ItemID,
             EC.CateValue as ExpenseCateValue,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN program_cate as PC on PC.ProgramID = A.ProgramID
            WHERE  A.BudgetLevel =2
            and PC.CateNo = '001' and PC.CateValue in ('1','2') and (LEFT(EC.CateNo,7) or LEFT(EC.CateNo,5) = '20151')
            and LEFT(CC.CateNo,8) IN('20011101' ,'20012101')
            AND A.AccountNo IN('0441','0411')
            $whereCondition
            GROUP BY   YearD, A.AccountNo,ItemID) as CH GROUP BY AccountNo) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I4 = HH.I4,TT.I5 = HH.I5
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,SUM(M.I5) AS I5
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
                        $I5 = ($value['I5']) ? $value['I5'] : 0;

                        $update = "UPDATE table_tmp
                    SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",I5 = I5 + ".$I5."
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
