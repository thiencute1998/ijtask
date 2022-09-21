<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB13_12Controller extends Controller {
    use Common;

    public function SBP3422016TTBTC_MB13_12(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : date("Y");
        $yearNext = $year + 1;
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

        $yearPre = (int)$year - 1;
        $yearNext = (int)$year + 1;

        $whereCommonCondition = "AND A.InTransTypeID NOT IN (16,17,18) ";
        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year - 1 AND $year +1  $whereCommonCondition";

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
            `NormCateValue` varchar(255) DEFAULT '',
            `NormNo` varchar(255) DEFAULT '',
            `Detail` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);


        // Thêm dữ liệu mặc định
        $this->insertDataDefault();

        $this->insertR1C1_2_4_5_7_8_9($whereCondition, $year);
        $this->updateR2_3C1_2_4_5_7_8_9($whereCondition, $year);
        $this->updateBC1_2_4_5_7_8_9($whereCondition, $year);

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumUpTableTmp($maxLevel, 1);


        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID','ASC')->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $yearPre;
            $data[0]->HeaderName2 = 'Năm ' . $year;
            $data[0]->HeaderName3 = 'Lũy kế số bố trí đến hết năm ' .$year;
            $data[0]->HeaderName4 = 'Dự toán năm ' .$yearNext;
        }
        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = (int)($data[$key]->I1) * (int)($data[$key]->I2);
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 = (int)($data[$key]->I4) * (int)($data[$key]->I5);
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
            $data[$key]->I7 = ($data[$key]->I7) ? $data[$key]->I7 : '';
            $data[$key]->I8 = ($data[$key]->I8) ? $data[$key]->I8 : '';
            $data[$key]->I9 = ($data[$key]->I9) ? $data[$key]->I9 : '';
            $data[$key]->I10 = (int)($data[$key]->I10) * (int)($data[$key]->I10);
            $data[$key]->I10 = ($data[$key]->I10) ? $data[$key]->I10 : '';

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

    public function insertR1C1_2_4_5_7_8_9($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, NormCateValue,NormNo, Detail, I1, I2,I4,I5,I7,I8,I9)
            SELECT  CONCAT(ParentID,'#',NormNo) as ItemID,ParentID, ItemName, NormCateValue, NormNo, 1 as Detail,
              MAX(CASE WHEN AccountNo = '0461' AND YearD = $year - 1  THEN Q END) AS I1,
              MAX(CASE WHEN AccountNo = '0461' AND YearD = $year - 1  THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN Q END) AS I4,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I5 ,
              MAX(CASE WHEN AccountNo = '0721' AND YearD = $year   THEN I END) AS I7,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1  THEN Q END) AS I8,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1  THEN I END) AS I9 FROM
            (SELECT
            A.NormName as ItemName, NC.CateValue as NormCateValue, A.NormNo as NormNo,
            SUM(A.FCUnitPrice) as I, SUM(A.Quantity) as Q, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo,
                            (Case  when  NC.CateValue ='1' THEN 'A#01#1'
                                                     when  NC.CateValue ='2' THEN 'A#01#2'
                                                     when  NC.CateValue ='3' THEN 'A#01#3' END) as ParentID
            FROM act_gl_books AS A
            INNER JOIN norm as N on A.NormID = N.NormID
            INNER JOIN norm_cate as NC on A.NormID = A.NormID
            WHERE NC.CateNo = '2015117111111' AND NC.CateValue IN ('1','2','3')
            AND A.AccountNo IN('0461','0441','0721','0411')
            $whereCondition
            GROUP BY  NC.CateValue, A.NormNo, YearD, A.AccountNo,ItemID) as CH GROUP BY NormCateValue,NormNo
                        ";
        DB::statement($sql);
    }
    public function updateR2_3C1_2_4_5_7_8_9($whereCondition, $year){
        $sql = " UPDATE table_tmp AS TT,
            (SELECT   ItemID,ItemName, NormCateValue, NormNo, 1 as Detail,
              MAX(CASE WHEN AccountNo = '0461' AND YearD = $year - 1  THEN Q END) AS I1,
              MAX(CASE WHEN AccountNo = '0461' AND YearD = $year - 1  THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN Q END) AS I4,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I5 ,
              MAX(CASE WHEN AccountNo = '0721' AND YearD = $year   THEN I END) AS I7,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1  THEN Q END) AS I8,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1  THEN I END) AS I9 FROM
            (SELECT  (Case  when  NC.CateValue ='4' THEN 'A#02'
                            when  NC.CateValue ='5' THEN 'A#03' END) as ItemID,
            A.NormName as ItemName, NC.CateValue as NormCateValue, A.NormNo as NormNo,
            SUM(A.FCUnitPrice) as I, SUM(A.Quantity) as Q, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo

            FROM act_gl_books AS A
            INNER JOIN norm as N on A.NormID = N.NormID
            INNER JOIN norm_cate as NC on A.NormID = A.NormID
            WHERE NC.CateNo = '2015117111111' AND NC.CateValue IN ('4','5')
            AND A.AccountNo IN('0461','0441','0721','0411')
            $whereCondition
            GROUP BY  NC.CateValue, A.NormNo, YearD, A.AccountNo,ItemID) as CH GROUP BY NormCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2,  TT.I4 = HH.I4,TT.I5 = HH.I5, TT.I7 = HH.I7, TT.I8 = HH.I8, TT.I9 = HH.I9
                        WHERE  TT.ItemID = HH.ItemID
                        ";
        DB::statement($sql);
    }
    public function updateBC1_2_4_5_7_8_9($whereCondition, $year){
        $sql = " UPDATE table_tmp AS TT,
            (SELECT   ItemID,ItemName, NormCateValue, NormNo, 1 as Detail,
              MAX(CASE WHEN AccountNo = '0461' AND YearD = $year - 1  THEN Q END) AS I1,
              MAX(CASE WHEN AccountNo = '0461' AND YearD = $year - 1  THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN Q END) AS I4,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I5 ,
              MAX(CASE WHEN AccountNo = '0721' AND YearD = $year   THEN I END) AS I7,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1  THEN Q END) AS I8,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1  THEN I END) AS I9 FROM
            (SELECT  'A#05' as ItemID,
            A.NormName as ItemName, NC.CateValue as NormCateValue, A.NormNo as NormNo,
            SUM(A.FCUnitPrice) as I, SUM(A.Quantity) as Q, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo

            FROM act_gl_books AS A
            INNER JOIN norm as N on A.NormID = N.NormID
            INNER JOIN norm_cate as NC on A.NormID = A.NormID
            WHERE NC.CateNo = '2015117111112' AND NC.CateValue ='1'
            AND A.AccountNo IN('0461','0441','0721','0411')
            $whereCondition
            GROUP BY  NC.CateValue, A.NormNo, YearD, A.AccountNo,ItemID) as CH GROUP BY NormCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2,  TT.I4 = HH.I4,TT.I5 = HH.I5, TT.I7 = HH.I7, TT.I8 = HH.I8, TT.I9 = HH.I9
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sql);
    }

    public function insertDataDefault(){
        $insert = [
            ['STT'=>'I', 'ItemID'=> 'A#01', 'ParentID'=> 'A#04', 'Level'=> 1, 'ItemName'=> 'Trợ cấp và nuôi dưỡng hàng tháng tại cộng đồng', 'FontWeight'=> '1','Detail'=>''],
            ['STT'=>'1', 'ItemID'=> 'A#01#1', 'ParentID'=> 'A#01', 'Level'=> 2, 'ItemName'=> 'Trợ cấp xã hội ', 'FontWeight'=> '0','Detail'=>''],
            ['STT'=>'2', 'ItemID'=> 'A#01#2', 'ParentID'=> 'A#01', 'Level'=> 2, 'ItemName'=> 'Hỗ trợ chi phí chăm sóc', 'FontWeight'=> '0','Detail'=>''],
            ['STT'=>'3', 'ItemID'=> 'A#01#3', 'ParentID'=> 'A#01', 'Level'=> 2, 'ItemName'=> 'Nuôi dưỡng trong cơ sở BTXH', 'FontWeight'=> '0','Detail'=>''],

            ['STT'=>'II', 'ItemID'=> 'A#02', 'ParentID'=> 'A#04', 'Level'=> 1, 'ItemName'=> 'Kinh phí mua thẻ BHYT', 'FontWeight'=> '1','Detail'=>'1'],
            ['STT'=>'III', 'ItemID'=> 'A#03', 'ParentID'=> 'A#04', 'Level'=> 1, 'ItemName'=> 'Mai táng phí', 'FontWeight'=> '1','Detail'=>'1'],
            ['STT'=>'A', 'ItemID'=> 'A#04', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG TRỢ CẤP THƯỜNG XUYÊN', 'FontWeight'=> '1','Detail'=>''],
            ['STT'=>'B', 'ItemID'=> 'A#05', 'ParentID'=> '', 'Level'=> 1, 'ItemName'=> 'TRỢ CẤP ĐỘT XUẤT', 'FontWeight'=> '1','Detail'=>''],

        ];

        DB::table("table_tmp")->insert($insert);
    }
    // sum row
    public function sumUpTableTmp($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID,SUM(M.I1) AS I1, SUM(M.I2) AS I2,SUM(M.I3) AS I3, SUM(M.I4) AS I4,
           SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8,SUM(M.I9) AS I9,
             SUM(M.I10) AS I10
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
                    $I6 = ($value['I6']) ? $value['I6'] : 0;
                    $I7 = ($value['I7']) ? $value['I7'] : 0;
                    $I8 = ($value['I8']) ? $value['I8'] : 0;
                    $I9 = ($value['I9']) ? $value['I9'] : 0;
                    $I10 = ($value['I10']) ? $value['I10'] : 0;
                    $update = "UPDATE table_tmp
                SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ",I9 = " . $I9 . ", I10 = " . $I10 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }
    }
}
