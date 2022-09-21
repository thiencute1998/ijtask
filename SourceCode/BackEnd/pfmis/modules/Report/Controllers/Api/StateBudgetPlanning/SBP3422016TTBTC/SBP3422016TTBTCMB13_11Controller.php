<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB13_11Controller extends Controller {
    public function SBP3422016TTBTC_MB13_11(Request $request){
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
        $whereCondition = "AND (
        ( YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $preYear AND YEAR(A.PeriodToDate) = $preYear)
        OR (YEAR(A.PeriodFromDate) = $nextYear AND YEAR(A.PeriodToDate) = $nextYear)
        ) $whereCompany  AND A.InTransTypeID NOT IN (16,17,18)";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `TranTypeID` int(2) DEFAULT 1,
          `Show` int(1) DEFAULT 1,
          `ItemNo` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT NULL,
          `AccountNo` varchar(15) DEFAULT '',
          `CoAccountNo` varchar(15) DEFAULT '',
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `CompanyID` varchar(11) DEFAULT '',
          `CompanyName` varchar(100) DEFAULT '',
          `CompanyNo` varchar(100) DEFAULT '',
          `Norm` varchar(255) DEFAULT NULL,
          `NormNo` varchar(100) DEFAULT '',
          `Detail` tinyint(1) NOT NULL DEFAULT 0,
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

          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $this->updateI1_2_3($whereCondition,$preYear, $year, $nextYear);
//        $this->updateI6_7_8_9($whereCondition,$preYear, $year, $nextYear);
        // sum up
        $this->sumUpTableTmp(1, 3);
        // Thêm dữ liệu mặc định
        $insertArr1 = [
            ['STT'=>'', 'ItemID'=> 'A', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG SỐ', 'FontWeight'=> '1'],
            ['STT'=>'I', 'ItemID'=> 'A#01', 'ParentID'=> 'A', 'Level'=> 1, 'ItemName'=> 'TRỢ CẤP MỘT LẦN (chi tiết các đối tượng theo quy định của pháp luật)', 'FontWeight'=> '1'],
            ['STT'=>'II', 'ItemID'=> 'A#02', 'ParentID'=> 'A', 'Level'=> 1, 'ItemName'=> 'TRỢ CẤP, PHỤ CẤP HÀNG THÁNG (chi tiết các đối tượng theo quy định của pháp luật)', 'FontWeight'=> '1'],
            ['STT'=>'III', 'ItemID'=> 'A#03', 'ParentID'=> 'A', 'Level'=> 1, 'ItemName'=> 'BẢO HIỂM Y TẾ (chi tiết các đối tượng theo quy định của pháp luật)', 'FontWeight'=> '1'],
            ['STT'=>'IV', 'ItemID'=> 'A#04', 'ParentID'=> 'A', 'Level'=> 1, 'ItemName'=> 'CHẾ ĐỘ ƯU ĐÃI KHÁC (chi tiết các đối tượng theo quy định của pháp luật)', 'FontWeight'=> '1'],
            ['STT'=>'V', 'ItemID'=> 'A#05', 'ParentID'=> 'A', 'Level'=> 1, 'ItemName'=> 'CHI PHÍ QUẢN LÝ (chi tiết các nội dung theo quy định của pháp luật)', 'FontWeight'=> '1'],
            ['STT'=>'VI', 'ItemID'=> 'A#06', 'ParentID'=> 'A', 'Level'=> 1, 'ItemName'=> 'CHI CHO CÁC CÔNG VIỆC (chi tiết các nội dung theo quy định của pháp luật)', 'FontWeight'=> '1'],
        ];
        DB::table('table_tmp')->insert($insertArr1);

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumUpTableTmp($maxLevel);
        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID')->get();

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $preYear ;
            $data[0]->HeaderName2 = 'Ước thực hiện năm ' . $year ;
            $data[0]->HeaderName3 = 'Dự toán năm ' . $nextYear ;
            $data[0]->HeaderName4 = 'Dự toán năm '.$nextYear. ' so với ước thực hiện năm ' . $year ;
            $data[0]->HeaderName5 = 'Thực hiện năm ' . $preYear ;
            $data[0]->HeaderName6 = 'Năm ' . $year ;
            $data[0]->HeaderName7 = 'Dự toán năm ' . $nextYear ;
            $data[0]->HeaderName8 = 'Dự toán năm '.$nextYear. ' so với ước thực hiện năm ' . $year ;
        }

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = (int)($data[$key]->I3) - (int)($data[$key]->I2);
            if($data[$key]->I2 !=0){
                $data[$key]->I5 = round(((int)($data[$key]->I3) *100)/(int)($data[$key]->I2), 2);
            }
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6: '';
            $data[$key]->I7 = ($data[$key]->I7) ? $data[$key]->I7: '';
            $data[$key]->I8 = ($data[$key]->I8) ? $data[$key]->I8 : '';
            $data[$key]->I9 = ($data[$key]->I9) ? $data[$key]->I9 : '';
            $data[$key]->I10 = (int)($data[$key]->I9) - (int)($data[$key]->I8);
            if($data[$key]->I8 !=0){
                $data[$key]->I11 = round(((int)($data[$key]->I9) *100)/(int)($data[$key]->I8), 2);
            }
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
//                "TransNo" => "00001",
                "ReportName" => "CƠ SỞ TÍNH CHI MUA BỔ SUNG HÀNG DỰ TRỮ QUỐC GIA NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => ($company) ? $company->CompanyName : 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function updateI1_2_3($whereCondition,$preYear, $year, $nextYear){
        $sqlUpdate="INSERT INTO table_tmp ( ItemID, ParentID, ItemName, Norm,Detail, I1,I2,I3,I6,I7,I8,I9)
                    SELECT CONCAT(ParentID,'#',Norm), ParentID, ItemName, Norm , 1,
                           MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I1 END) AS I1,
                           MAX(CASE WHEN AccountNo = '0721' AND YearD = $year  THEN I1 END) AS I2,
                           MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I1 END) AS I3,
                           MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I2 END) AS I6,
                           MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I2 END) AS I7,
                           MAX(CASE WHEN AccountNo = '0721' AND YearD = $year  THEN I2 END) AS I8,
                           MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I2 END) AS I9
                    FROM (SELECT SUM(Quantity) AS I1,SUM(FCDebitAmount) AS I2,A.NormName as ItemName, A.NormNo as Norm, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
                                 (Case when (NC.CateNo = '201511811' AND NC.CateValue = '1') THEN 'A#01'
                                       when (NC.CateNo = '201511811' AND NC.CateValue = '2') THEN 'A#02'
                                       when (NC.CateNo = '201511811' AND NC.CateValue = '3') THEN 'A#03'
                                       when (NC.CateNo = '201511811' AND NC.CateValue = '99') THEN 'A#04'
                                       when (NC.CateNo = '201511812' AND NC.CateValue = '1') THEN 'A#05'
                                       when (NC.CateNo = '201511813' AND NC.CateValue = '1') THEN 'A#06' END) AS ParentID
                          FROM act_gl_books AS A
                                   INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                          WHERE
                                      A.AccountNo IN ('0461', '0721','0411')
                                  AND  (NC.CateNo = '201511811' AND NC.CateValue IN (1,2,3,99))
                             OR   (NC.CateNo IN ('201511812','201511813') AND NC.CateValue =1)
                              $whereCondition
                          GROUP BY A.AccountNo, YearD, ParentID, A.NormNo
                         ) AS B GROUP BY Norm, ParentID";
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1,SUM(M.I2) AS I2,SUM(M.I3) AS I3, SUM(M.I6) AS I6
                                                , SUM(M.I7) AS I7,SUM(M.I8) AS I8,SUM(M.I9) AS I9
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
                        $I6 = ($value['I6']) ? $value['I6'] : 0;
                        $I7 = ($value['I7']) ? $value['I7'] : 0;
                        $I8 = ($value['I8']) ? $value['I8'] : 0;
                        $I9 = ($value['I9']) ? $value['9'] : 0;
                        $update = "UPDATE table_tmp
                    SET I1 = I1 + ".$I1.",I2 = I2+ ".$I2.", I3 = I3 + ".$I3.", I6 = I16 + ".$I6.",I7 = I7+ ".$I7.", I8 = I8 + ".$I8.", I9 = I9 + ".$I9."
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
