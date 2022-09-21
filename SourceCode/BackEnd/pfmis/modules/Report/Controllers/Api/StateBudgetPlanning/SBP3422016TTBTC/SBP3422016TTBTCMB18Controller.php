<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB18Controller extends Controller {
    use Common;

    public function SBP3422016TTBTC_MB18(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : date("Y");
        $fromDate = (isset($filter['Year'])) ? $filter['Year'].'-01-01' : $year.'-01-01';
        $toDate = (isset($filter['Year'])) ? $filter['Year'].'-12-31' : $year.'-12-31';
        $lastYear = date('Y-m-d', strtotime('-1 year', strtotime($toDate)) );
        $nextYear = date('Y-m-d', strtotime('+1 year', strtotime($toDate)) );
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

        $whereCommonCondition = "";

        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year AND $year + 1 $whereCommonCondition";
        $whereDebt = " AND YEAR(A.PeriodFromDate) <= $year - 1 $whereCommonCondition ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(255) NOT NULL PRIMARY KEY,
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
          `I7` double(20,4) DEFAULT NULL,
          `I8` double(20,4) DEFAULT NULL,
          `I9` double(20,4) DEFAULT NULL,
          `I10` double(20,4) DEFAULT NULL,
          `I11` double(20,4) DEFAULT NULL,
          `I12` double(20,4) DEFAULT NULL,
          `I13` double(20,4) DEFAULT NULL,
            `Company` varchar(20) DEFAULT '',
            `Detail` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        //calculator here

        $this->insertR1C1($whereDebt, $year);
        $this->insertOrUpdateR2_4_8_10($whereCondition, $year);
        $this->insertOrUpdateR3_5_9_11($whereCondition, $year);
        $this->setLevel();


        // sum Row and set STT
        $this->sumUpRow();

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID','ASC')->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'DƯ NGUỒN ĐẾN 31/12/' . ($year - 1);
            $data[0]->HeaderName2 = 'ƯỚC THỰC HIỆN NĂM ' . $year;
            $data[0]->HeaderName3 = 'DƯ NGUỒN ĐẾN 31/12/' . $year;
            $data[0]->HeaderName4 = 'KẾ HOẠCH NĂM ' . ($year + 1);
            $data[0]->HeaderName5 = 'DƯ NGUỒN ĐẾN 31/12/' . ($year + 1);
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
                "ReportName" => "KẾ HOẠCH TÀI CHÍNH CỦA CÁC QUỸ TÀI CHÍNH NHÀ NƯỚC NGOÀI NGÂN SÁCH NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function insertR1C1($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Company, Detail, I1)
            SELECT CONCAT('A', '#', C.CompanyNo) as ItemID, 'A' as ParentID, C.CompanyName as ItemName, C.CompanyNo, 1 as Detail,
            SUM(LCCreditAmount - LCCreditAmount)
            FROM act_gl_books AS A
            INNER JOIN company as C on A.InputCompanyID = C.CompanyID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            WHERE A.AccountNo IN ('0441','0442')
            AND LEFT(CC.CateNo, 3) = '203'
            $whereDebt
            GROUP BY C.CompanyNo
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_4_8_10($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Company, Detail, I2, I4, I8, I10)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', CompanyNo) as ItemID, ParentID, ItemName,
            CompanyNo, 1 as Detail,
            MAX(CASE WHEN AccountNo = '0712' AND YearD = $year THEN I ELSE 0 END) as A1,
            MAX(CASE WHEN AccountNo = '0722' AND YearD = $year THEN I ELSE 0 END) as A2,
            MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1 THEN I ELSE 0 END) as A3,
            MAX(CASE WHEN AccountNo = '0412' AND YearD = $year + 1 THEN I ELSE 0 END) as A4 FROM
            (SELECT 'A' as ParentID, C.CompanyName as ItemName, C.CompanyNo, A.AccountNo,
            SUM(LCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD
            FROM act_gl_books AS A
            INNER JOIN company as C on A.InputCompanyID = C.CompanyID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            WHERE A.AccountNo IN ('0712', '0722', '0411', '0412')
            AND LEFT(CC.CateNo, 3) = '203'
            $whereCondition
            GROUP BY A.AccountNo, C.CompanyNo, YEAR(A.PeriodFromDate)) as CH GROUP BY CompanyNo)
            as DT ON DUPLICATE KEY UPDATE I2 = A1, I4 = A2, I8 = A3, I10 = A4";

        DB::statement($sql);
    }

    public function insertOrUpdateR3_5_9_11($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Company, Detail, I3, I5, I9, I11)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', CompanyNo) as ItemID, ParentID, ItemName,
            CompanyNo, 1 as Detail,
            MAX(CASE WHEN AccountNo = '0712' AND YearD = $year THEN I ELSE 0 END) as A1,
            MAX(CASE WHEN AccountNo = '0722' AND YearD = $year THEN I ELSE 0 END) as A2,
            MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1 THEN I ELSE 0 END) as A3,
            MAX(CASE WHEN AccountNo = '0412' AND YearD = $year + 1 THEN I ELSE 0 END) as A4 FROM
            (SELECT 'A' as ParentID, C.CompanyName as ItemName, C.CompanyNo, A.AccountNo,
            SUM(LCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD
            FROM act_gl_books AS A
            INNER JOIN company as C on A.InputCompanyID = C.CompanyID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            WHERE A.AccountNo IN ('0712', '0722', '0411', '0412')
            AND CC.CateNo = '20311'
            AND A.ExpenseID = '5115'
            $whereCondition
            GROUP BY A.AccountNo, C.CompanyNo, YEAR(A.PeriodFromDate)) as CH GROUP BY CompanyNo)
            as DT ON DUPLICATE KEY UPDATE I3 = A1, I5 = A2, I9 = A3, I11 = A4";

        DB::statement($sql);
    }


    // sum row
    public function sumUpRow(){
        $listItems = DB::table('table_tmp')->where('Detail','=',1)->orderBy('ItemID','ASC')
            ->where('I7','!=', 0)->where('I13','!=',0)->get();
        foreach($listItems as $key =>$item){
            $I6 = $item->I12 - $item->I14;
            $I7 = $item->I1 + $item->I5 + $I6;
            $I12 = $item->I18 - $item->I10;
            $I13 = $I7 + $item->I11 + $I12;
            DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update([
                'I6'=> $I6, 'I7'=> $I7,  'I12'=> $I12, 'I13'=> $I13, 'STT'=> ($key + 1)
            ]);
        }
    }


}
