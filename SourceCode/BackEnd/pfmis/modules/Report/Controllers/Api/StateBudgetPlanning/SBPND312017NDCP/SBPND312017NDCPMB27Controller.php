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

class SBPND312017NDCPMB27Controller extends Controller {
    public function SBPND312017NDCP_MB27(Request $request){

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
          `ItemID` varchar(100) DEFAULT '',
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
          `I6` double(20,4) DEFAULT NULL,
          `I7` double(20,4) DEFAULT NULL,
          `I8` double(20,4) DEFAULT NULL,
          `I9` double(20,4) DEFAULT NULL,
          `I10` double(20,4) DEFAULT NULL,
          `I11` double(20,4) DEFAULT NULL,
          `I12` double(20,4) DEFAULT NULL,
          `I13` double(20,4) DEFAULT NULL,
          `I14` double(20,4) DEFAULT NULL,
          `I15` double(20,4) DEFAULT NULL,
          `I16` double(20,4) DEFAULT NULL,
          `I17` double(20,4) DEFAULT NULL,
          `I18` double(20,4) DEFAULT NULL,
          `I19` double(20,4) DEFAULT NULL,
          `I20` double(20,4) DEFAULT NULL,
          `I21` double(20,4) DEFAULT NULL,
          `I22` double(20,4) DEFAULT NULL,
          `I23` double(20,4) DEFAULT NULL,
          `I24` double(20,4) DEFAULT NULL,
          `I25` double(20,4) DEFAULT NULL,
          `I26` double(20,4) DEFAULT NULL,
          `I27` double(20,4) DEFAULT NULL,
          `I28` double(20,4) DEFAULT NULL,
          `I29` double(20,4) DEFAULT NULL,
          `I30` double(20,4) DEFAULT NULL,
          `Detail` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        // sum up
        $this->sumUpTableTmp(1, 5);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Dự toán chi NSĐP năm ' . $year;
            $data[0]->HeaderName2 = 'Ước thực hiện chi NSĐP năm ' . $year;
        }
        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
            $data[$key]->I7 = ($data[$key]->I7) ? $data[$key]->I7 : '';
            $data[$key]->I8 = ($data[$key]->I8) ? $data[$key]->I8 : '';
            $data[$key]->I9 = ($data[$key]->I9) ? $data[$key]->I9 : '';
            $data[$key]->I10 = ($data[$key]->I10) ? $data[$key]->I10 : '';
            $data[$key]->I11 = ($data[$key]->I11) ? $data[$key]->I11 : '';
            $data[$key]->I12 = ($data[$key]->I12) ? $data[$key]->I12 : '';
            $data[$key]->I13 = ($data[$key]->I13) ? $data[$key]->I13 : '';
            $data[$key]->I14 = ($data[$key]->I14) ? $data[$key]->I14 : '';
            $data[$key]->I15 = ($data[$key]->I15) ? $data[$key]->I15 : '';
            $data[$key]->I16 = ($data[$key]->I16) ? $data[$key]->I16 : '';
            $data[$key]->I17 = ($data[$key]->I17) ? $data[$key]->I17 : '';
            $data[$key]->I18 = ($data[$key]->I18) ? $data[$key]->I18 : '';
            $data[$key]->I19 = ($data[$key]->I19) ? $data[$key]->I19 : '';
            $data[$key]->I20 = ($data[$key]->I20) ? $data[$key]->I20 : '';
            $data[$key]->I21 = ($data[$key]->I21) ? $data[$key]->I21 : '';
            $data[$key]->I22 = ($data[$key]->I22) ? $data[$key]->I22 : '';
            $data[$key]->I23 = ($data[$key]->I23) ? $data[$key]->I23 : '';
            $data[$key]->I24 = ($data[$key]->I24) ? $data[$key]->I24 : '';
            $data[$key]->I25 = ($data[$key]->I25) ? $data[$key]->I25 : '';
            $data[$key]->I26 = ($data[$key]->I26) ? $data[$key]->I26 : '';
            $data[$key]->I27 = ($data[$key]->I27) ? $data[$key]->I27 : '';
            $data[$key]->I28 = ($data[$key]->I28) ? $data[$key]->I28 : '';
            $data[$key]->I29 = ($data[$key]->I29) ? $data[$key]->I29 : '';
            $data[$key]->I30 = ($data[$key]->I30) ? $data[$key]->I30 : '';

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
                "ReportName" => "ĐÁNH GIÁ THỰC HIỆN CHI CÂN ĐỐI NGÂN SÁCH TỪNG HUYỆN (XÃ) NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
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
