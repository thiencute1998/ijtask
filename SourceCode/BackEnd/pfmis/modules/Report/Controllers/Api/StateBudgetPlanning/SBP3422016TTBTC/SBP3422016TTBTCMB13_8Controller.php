<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB13_8Controller extends Controller {
    use Common;

    public function SBP3422016TTBTC_MB13_8(Request $request){
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

        $preYear = (int)$year - 1;
        $nextYear = (int)$year + 1;
        $whereCondition = "AND (
        ( YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $preYear AND YEAR(A.PeriodToDate) = $preYear)
        OR (YEAR(A.PeriodFromDate) = $nextYear AND YEAR(A.PeriodToDate) = $nextYear)
        ) $whereCompany  AND A.InTransTypeID NOT IN (16,17,18) AND A.ProjectID IS NULL";

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
         `NormCateValue` varchar(255) DEFAULT '',
            `NormNo` varchar(255) DEFAULT '',
            `AccountNo` varchar(20) DEFAULT '',
           `SectorCate` varchar(20) DEFAULT '',
           `SectorCateValue` varchar(20) DEFAULT '',
           `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb13_8')->get();

        $insertArr = [];
        foreach ($templateTable as $template) {
            $insertArr[] = [
                'TableID' => $template->TableID,
                'STT' => $template->STT,
                'ItemID' => $template->ItemID,
                'ParentID' => $template->ParentID,
                'Level' => $template->Level,
                'ItemName' => $template->ItemName,
                'FontWeight' => $template->FontWeight,
                'Italic' => $template->Italic,
                'Center' => $template->Center,
                'NormCateValue' => $template->NormCateValue,
                'AccountNo' => $template->AccountNo,
                'NormNo' => $template->NormNo,
                'SectorCate' => $template->SectorCate,
                'SectorCateValue' => $template->SectorCateValue,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $this->updateR1I1_2_3_4($whereCondition,$year);
        $this->updateR2_3_4_5I1_2_3_4($whereCondition,$year);

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
//        $this->sumUpTableTmp($maxLevel, 1);


        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID','ASC')->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $preYear;
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
    public function updateR1I1_2_3_4($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID,ItemName, SectorCateValue, NormNo, 1 as Detail,
              MAX(CASE WHEN AccountNo = '0461' AND YearD = $year - 1  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year   THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0721' AND YearD = $year + 1 THEN I END) AS I3,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1 THEN I END) AS I4  FROM
            (SELECT  (Case when SC.CateNo = '20003070101' AND  SC.CateValue ='1' THEN 'A#01#01#01'
                           when SC.CateNo = '20003070101' AND  SC.CateValue ='2' THEN 'A#01#01#02'
                           when SC.CateNo = '20003070101' AND  SC.CateValue ='3' THEN 'A#01#01#03'
                           when SC.CateNo = '20003070101' AND  SC.CateValue ='4' THEN 'A#01#01#04'
                           when SC.CateNo = '20003070101' AND  SC.CateValue ='5' THEN 'A#01#01#05'
                           when SC.CateNo = '20003070101' AND  SC.CateValue ='6' THEN 'A#01#01#06'
                           when SC.CateNo = '20003070101' AND  SC.CateValue ='7' THEN 'A#01#01#07'
                           when SC.CateNo = '20003070101' AND  SC.CateValue ='8' THEN 'A#01#01#08'

                            when SC.CateNo = '20003070102' AND  SC.CateValue ='1' THEN 'A#01#02#01'
                            when SC.CateNo = '20003070102' AND  SC.CateValue ='2' THEN 'A#01#02#02'
                            when SC.CateNo = '20003070102' AND  SC.CateValue ='3' THEN 'A#01#02#03'
                            when SC.CateNo = '20003070102' AND  SC.CateValue ='4' THEN 'A#01#02#04'
                            when SC.CateNo = '20003070102' AND  SC.CateValue ='5' THEN 'A#01#02#05'

                            when SC.CateNo = '20003070103' AND  SC.CateValue ='1' THEN 'A#01#03'
                            when SC.CateNo = '20003070104' AND  SC.CateValue ='1' THEN 'A#01#04'

                            when SC.CateNo = '20003070199' THEN 'A#01#05'
     END) as ItemID,
            A.NormName as ItemName, NC.CateValue as NormCateValue, A.NormNo as NormNo,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo, SC.CateValue as SectorCateValue
            FROM act_gl_books AS A
            INNER JOIN norm as N on A.NormID = N.NormID
            INNER JOIN norm_cate as NC on NC.NormID = A.NormID
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            WHERE NC.CateNo = '20151111111' AND NC.CateValue IN ('1','2') AND SC.CateNo IN ('20003070101','20003070102', '20003070103','20003070199')
            AND SC.CateValue IN ('1','2','3','4','5','6','7','8')
            AND A.AccountNo IN('0461','0441','0721','0411')
            $whereCondition
            GROUP BY  NC.CateValue, A.NormNo, YearD, A.AccountNo,ItemID) as CH GROUP BY NormCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2,TT.I3 = HH.I3,  TT.I4 = HH.I4
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }

    public function updateR2_3_4_5I1_2_3_4($whereCondition, $year){
        $sqlUpdate=" UPDATE table_tmp AS TT,
            (SELECT   ItemID,ItemName, SectorCateValue, NormNo, 1 as Detail,
              MAX(CASE WHEN AccountNo = '0461' AND YearD = $year - 1  THEN I END) AS I1,
              MAX(CASE WHEN AccountNo = '0441' AND YearD = $year   THEN I END) AS I2 ,
              MAX(CASE WHEN AccountNo = '0721' AND YearD = $year + 1 THEN I END) AS I3,
              MAX(CASE WHEN AccountNo = '0411' AND YearD = $year + 1 THEN I END) AS I4  FROM
            (SELECT  (Case when SC.CateNo = '200030702' AND  SC.CateValue ='1' THEN 'A#02'
                           when SC.CateNo = '20203' AND  SC.CateValue ='1' THEN 'A#03'
                           when SC.CateNo = '200030704' AND  SC.CateValue ='1' THEN 'A#04'
                           when SC.CateNo = '200030799' AND  SC.CateValue ='99' THEN 'A#05'END) as ItemID,
            A.NormName as ItemName, NC.CateValue as NormCateValue, A.NormNo as NormNo,
            SUM(A.FCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo as AccountNo, SC.CateValue as SectorCateValue
            FROM act_gl_books AS A
            INNER JOIN norm as N on A.NormID = N.NormID
            INNER JOIN norm_cate as NC on NC.NormID = A.NormID
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            WHERE NC.CateNo = '20151111111' AND NC.CateValue IN ('1','2') AND SC.CateNo IN ('200030702','20203', '200030704','200030799')
            AND SC.CateValue IN ('1','99')
            AND A.AccountNo IN('0461','0441','0721','0411')
            $whereCondition
            GROUP BY  NC.CateValue, A.NormNo, YearD, A.AccountNo,ItemID) as CH GROUP BY NormCateValue) AS HH
            SET  TT.I1 = HH.I1, TT.I2 = HH.I2,TT.I3 = HH.I3,  TT.I4 = HH.I4
                        WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }

    // sum
    public function sumUpTableTmp($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID,SUM(M.I1) AS I1, SUM(M.I2) AS I2,SUM(M.I3) AS I3, SUM(M.I4) AS I4
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
