<?php

namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB03Controller extends Controller
{
    public function SBP3422016TTBTC_MB03(Request $request)
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => []
        ];

        $filter = $request->post('filter');
        $companyID = (isset($filter['CompanyID'])) ? $filter['CompanyID'] : null;

        if (!isset($filter['Year']) || !$filter['Year'] || !$companyID) {
            $json['status'] = 0;
            $json['msg'] = 'Chưa chọn kỳ , đơn vị ';
            return json_encode($json);
        }

        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : 2020;


        $companyID = (isset($filter['CompanyID'])) ? $filter['CompanyID'] : null;
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

        if (isset($filter['CompanyID'])) {
            $note .= 'đơn vị ' . $filter['CompanyName'];
            $company = Company::where('CompanyID',$filter['CompanyID'])->get();
        }
        else{
            $company = Company::where('CompanyID',Auth::user()->CompanyID)->get();
        }

        $whereCompany = '';
        if ($companyID) {
            $whereCompany .= " AND A.CompanyID = " . $companyID . ' ';
        }

        $companyName = $company[0]->CompanyName;

        $listCompany = [];
        $allCompanyChild = self::populateChildrenCompanys($company);
        foreach ($allCompanyChild as $companyChild) {
            $listCompany[] = $companyChild;
        }
        $company = $company->merge($listCompany);

        $preYear = $year - 1;
        $nextYear = $year + 1;
//        $wherePreCondition = "AND Year(A.PeriodFromDate) = $preYear ";
//        $whereCondition = "AND Year(A.PeriodFromDate)  = $year ";
//        $whereNextCondition = "AND YEAR(A.PeriodFromDate) = $nextYear ";

        $whereCondition = "AND (
        (YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $preYear AND YEAR(A.PeriodToDate) = $preYear)
        OR (YEAR(A.PeriodFromDate) = $nextYear AND YEAR(A.PeriodToDate) = $nextYear)
        )$whereCompany";


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
          `ExpenseNo` varchar(20) DEFAULT '',
          `CompanyID` int(11) DEFAULT NULL,
          `ParentCompanyID` int(11) DEFAULT NULL,
          `LevelCompany` int(11) DEFAULT NULL,
          `AccountNoI1` varchar(20) DEFAULT '',
          `AccountNoI2` varchar(20) DEFAULT '',
          `AccountNoI3` varchar(20) DEFAULT '',
          `AccountNoI4` varchar(20) DEFAULT '',
          `FCDebitAmount` double(20,4) DEFAULT NULL,
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb03')->get();
        $insertArr = [];
        foreach ($company as $item){
            foreach ($templateTable as $key=> $template) {
                if($key == 0) {
                    $insertArr[] = [
                        'STT' => $template->STT,
                        'ItemID' => $template->ItemID,
                        'ParentID' => $template->ParentID,
                        'Level' => $template->Level,
                        'ItemName' => $item->CompanyName,
                        'CompanyID' => $item->CompanyID,
                        'ParentCompanyID'=> $item->ParentID,
                        'LevelCompany'=> $item->Level,
                        'FontWeight' => $template->FontWeight,
                        'Italic' => $template->Italic,
                        'Center' => $template->Center,
                        'ExpenseNo' => $template->ExpenseNo,
                        'AccountNoI1' => $template->AccountNoI1,
                        'AccountNoI2' => $template->AccountNoI2,
                        'AccountNoI3' => $template->AccountNoI3,
                        'AccountNoI4' => $template->AccountNoI4,
                        'I1' => $template->I1,
                        'I2' => $template->I2,
                        'I3' => $template->I3,
                        'I4' => $template->I4,
                    ];
                }
                else{

                        $insertArr[] = [
                            'STT' => $template->STT,
                            'ItemID' => $template->ItemID,
                            'ParentID' => $template->ParentID,
                            'Level' => $template->Level,
                            'ItemName' => $template->ItemName,
                            'CompanyID' => $item->CompanyID,
                            'ParentCompanyID'=> $item->ParentID,
                            'LevelCompany'=> $item->Level,
                            'FontWeight' => $template->FontWeight,
                            'Italic' => $template->Italic,
                            'Center' => $template->Center,
                            'ExpenseNo' => $template->ExpenseNo,
                            'AccountNoI1' => $template->AccountNoI1,
                            'AccountNoI2' => $template->AccountNoI2,
                            'AccountNoI3' => $template->AccountNoI3,
                            'AccountNoI4' => $template->AccountNoI4,
                            'I1' => $template->I1,
                            'I2' => $template->I2,
                            'I3' => $template->I3,
                            'I4' => $template->I4,

                        ];

                }
            }

        }
        DB::table('table_tmp')->insert($insertArr);

        $this->TableTmpC1($whereCondition, $preYear, $year, $nextYear);
        $this->TableTmpC2($whereCondition, $preYear, $year, $nextYear);
        $this->TableTmpC3($whereCondition, $preYear, $year, $nextYear);
        $this->TableTmpC4($whereCondition, $preYear, $year, $nextYear);
        $this->TableTmpC5($whereCondition, $preYear, $year, $nextYear);
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();

        $this->sumUpTableTmp(1,4);


        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();

        $company = Company::find($companyID);
        $dateTime = "Vĩnh Phú, ngày $date tháng $month năm $yearC";
        if ($company) {
            $dateTime = $company->Address . ", ngày $date tháng $month năm $yearC";
        }
        foreach ($data as $key => $item) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';

            if ($key === 0) {
                $data[0]->ColumnHead1 = 'Thực hiện năm ' . $preYear;
                $data[0]->ColumnHead2 = 'Năm hiện hành ' . $year;
                $data[0]->ColumnHead3 = 'Dự toán năm ' . $nextYear;
            }
            $response = [
                'master' => [
                    "Year" => "",
                    "CreateName" => "Trần Mai Phương",
                    "TransNo" => "00001",
                    "ReportName" => "DỰ KIẾN SỐ THUẾ GIÁ TRỊ GIA TĂNG PHẢI HOÀN NĂM " . $year,
                    "DateTime" => $dateTime,
                    "CompanyName" => $companyName,
                    "Note" => $note
                ],
                'detail' => $data

            ];

            $json['data'] = $response;
            return json_encode($json);
        }
    }
    public static function populateChildrenCompanys($company){
        $listCompanyChild = [];
        $companyIDArr = [];
        foreach ($company as $company) {
            $companyIDArr[] = $company->CompanyID;
        }
        if (count($companyIDArr)) {
            $query = Company::query();
            $query->whereIn('ParentID', $companyIDArr);
            $companyChild = $query->get();
            if (count($companyChild)) {
                self::populateChildrenCompanys($companyChild);
                foreach ($companyChild as $companyChild) {
                    $listCompanyChild[] = $companyChild;
                }
            }
        }

        return $listCompanyChild;

        }

        public function TableTmpC1($whereCondition, $preYear, $year, $nextYear){
             $sqlUpdate= "UPDATE table_tmp AS TT,
         (SELECT  CompanyID,
            MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $year THEN I END) AS I2,
            MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $year THEN I END) AS I3,
            MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $nextYear THEN I END) AS I4
            FROM (SELECT A.CompanyID,SUM(FCDebitAmount) AS I, A.AccountNo, YEAR(A.PeriodFromDate)AS YearD
        FROM act_gl_books AS A
        WHERE  A.ExpenseNo = '51060101'
            AND A.AccountNo IN ('0461', '0441', '0721', '0411')
        $whereCondition
        GROUP BY YearD, A.AccountNo) AS B) AS HH
        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE TT.ItemID = 'A#01'and TT.CompanyID = HH.CompanyID";
            DB::statement($sqlUpdate);
        }
    public function TableTmpC2($whereCondition, $preYear, $year, $nextYear){
        $sqlUpdate= "UPDATE table_tmp AS TT,
           (SELECT CompanyID,
            MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $year THEN I END) AS I2,
            MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $year THEN I END) AS I3,
            MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $nextYear THEN I END) AS I4
            FROM (SELECT A.CompanyID,SUM(FCDebitAmount) AS I, A.AccountNo, YEAR(A.PeriodFromDate)AS YearD
        FROM act_gl_books AS A
        WHERE (A.ExpenseNo = '51060102' or A.ExpenseNo = '51060103')
            AND A.AccountNo IN ('0461', '0441', '0721', '0411')
        $whereCondition
        GROUP BY YearD, A.AccountNo) AS B) AS HH
        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE TT.ItemID = 'A#02'and TT.CompanyID = HH.CompanyID";
        DB::statement($sqlUpdate);
    }
    public function TableTmpC3($whereCondition, $preYear, $year, $nextYear){
        $sqlUpdate= "UPDATE table_tmp AS TT,( SELECT  CompanyID,
            MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $year THEN I END) AS I2,
            MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $year THEN I END) AS I3,
            MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $nextYear THEN I END) AS I4
            FROM (SELECT A.CompanyID,SUM(FCDebitAmount) AS I, A.AccountNo, YEAR(A.PeriodFromDate)AS YearD
        FROM act_gl_books AS A
        WHERE  A.ExpenseNo = '51060102'
            AND A.AccountNo IN ('0461', '0441', '0721', '0411')
        $whereCondition
        GROUP BY YearD, A.AccountNo) AS B) AS HH
        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE TT.ItemID = 'A#02#01' AND TT.CompanyID = HH.CompanyID";
        DB::statement($sqlUpdate);
    }
    public function TableTmpC4($whereCondition, $preYear, $year, $nextYear){
        $sqlUpdate= "UPDATE table_tmp AS TT, (SELECT CompanyID,
            MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $year THEN I END) AS I2,
            MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $year THEN I END) AS I3,
            MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $nextYear THEN I END) AS I4
            FROM (SELECT A.CompanyID,SUM(FCDebitAmount) AS I, A.AccountNo, YEAR(A.PeriodFromDate)AS YearD
        FROM act_gl_books AS A
        WHERE A.ExpenseNo = '51060103'
            AND A.AccountNo IN ('0461', '0441', '0721', '0411')
        $whereCondition
        GROUP BY YearD, A.AccountNo) AS B) AS HH
        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE TT.ItemID = 'A#02#02'AND TT.CompanyID = HH.CompanyID";
        DB::statement($sqlUpdate);
    }
    public function TableTmpC5($whereCondition, $preYear, $year, $nextYear){
        $sqlUpdate= "UPDATE table_tmp AS TT, (  SELECT  CompanyID,
            MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $year THEN I END) AS I2,
            MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $year THEN I END) AS I3,
            MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $nextYear THEN I END) AS I4
            FROM (SELECT A.CompanyID,SUM(FCDebitAmount) AS I, A.AccountNo, YEAR(A.PeriodFromDate)AS YearD
        FROM act_gl_books AS A
        WHERE A.ExpenseNo = '51060199'
            AND A.AccountNo IN ('0461', '0441', '0721', '0411')
        $whereCondition
        GROUP BY YearD, A.AccountNo) AS B) AS HH
        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE TT.ItemID = 'A#03'AND TT.CompanyID = HH.CompanyID";
        DB::statement($sqlUpdate);
    }

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
