<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Symfony\Component\ErrorHandler\Debug;

class SBP3422016TTBTCB4Controller extends Controller {
    public function sbptt3422016ttbtcmb04(Request $request){
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
//        if (isset($filter['SectorName'])) {
//            $note .= 'Ngành ' . $filter['SectorName'] . ', ';
//        }
//        if (isset($filter['ProvinceName'])) {
//            $note .= 'tỉnh ' . $filter['ProvinceName'] . ', ';
//        }
//        if (isset($filter['DistrictName'])) {
//            $note .= 'huyện ' . $filter['DistrictName'] . ', ';
//        }
//        if (isset($filter['CommuneName'])) {
//            $note .= 'xã ' . $filter['CommuneName'] . ', ';
//        }
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

        // TODO check isFinalTrans: AND A.isFinalTrans = 1

        $yearPre = (int)$year - 1;
        $yearNext = (int)$year + 1;
        $whereCondition = "AND YEAR(A.PostDate) = $year $whereCompany $whereDirection";
        $whereConditionPre = "AND YEAR(A.PostDate) = $yearPre $whereCompany $whereDirection";
        $whereConditionNext = "AND YEAR(A.PostDate) = $yearNext $whereCompany $whereDirection";
        $intranstyle= "AND A.InTransTypeID = 19";
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
          `ConAccountNoI1` varchar(20) DEFAULT '',
          `ConAccountNoI2` varchar(20) DEFAULT '',
          `ConAccountNoI3` varchar(20) DEFAULT '',
          `ConAccountNoI4` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb04')->get();
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
                'ConAccountNoI1' => $template->ConAccountNoI1,
                'ConAccountNoI2' => $template->ConAccountNoI2,
                'ConAccountNoI3' => $template->ConAccountNoI3,
                'ConAccountNoI4' => $template->ConAccountNoI4,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4
            ];
        }

        DB::table('table_tmp')->insert($insertArr);

        // row 1
        $this->insertTableTmpR1($whereConditionPre,'017112','I1',$intranstyle);
//        $this->insertTableTmpR1($whereConditionPre,'015112','I2');
//        $this->insertTableTmpR1($whereConditionPre,'011112','I3');
//        $this->insertTableTmpR1($whereConditionPre,'012112','I4');
//        // row 2
//        $this->insertTableTmpR2($whereConditionPre,'017112','I1',$intranstyle);
//        $this->insertTableTmpR2($whereConditionPre,'015112','I2');
//        $this->insertTableTmpR2($whereConditionPre,'011112','I3');
//        $this->insertTableTmpR2($whereConditionPre,'012112','I4');
//        // row 3
//        $this->insertTableTmpR3($whereConditionPre,'017111','I1',$intranstyle);
//        $this->insertTableTmpR3($whereConditionPre,'015111','I2');
//        $this->insertTableTmpR3($whereConditionPre,'011111','I3');
//        $this->insertTableTmpR3($whereConditionPre,'012111','I4');
//        // row 4
//        $this->insertTableTmpR4($whereConditionPre,'017111','I1',$intranstyle);
//        $this->insertTableTmpR4($whereConditionPre,'015111','I2');
//        $this->insertTableTmpR4($whereConditionPre,'011111','I3');
//        $this->insertTableTmpR4($whereConditionPre,'012111','I4');
//       //row 5
//        $this->insertTableTmpR5($whereConditionPre,'017111','I1',$intranstyle);
//        $this->insertTableTmpR5($whereConditionPre,'015111','I2');
//        $this->insertTableTmpR5($whereConditionPre,'011111','I3');
//        $this->insertTableTmpR5($whereConditionPre,'012111','I4');
        // sum up
//        $this->sumUpTableTmp(1, 4);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $yearPre;
            $data[0]->HeaderName2 = 'Năm ' . $year;
            $data[0]->HeaderName3 = 'Dự toán năm ' .$yearNext;
        }

        $A3C1 = 0;
        $A3C2 = 0;
        $A3C3 = 0;
        $A3C4 = 0;
//        foreach ($data as $key => $value) {
//            if ($value->ItemID === 'A#01') {
//                $A3C1 += $data[$key]->I1;
//                $A3C2 += $data[$key]->I2;
//                $A3C3 += $data[$key]->I3;
//                $A3C4 += $data[$key]->I4;
//            }
//            if ($value->ItemID === 'A#02') {
//                $A3C1 -= $data[$key]->I1;
//                $A3C2 -= $data[$key]->I2;
//                $A3C3 -= $data[$key]->I3;
//                $A3C4 -= $data[$key]->I4;
//            }
//
//            if ($value->ItemID === 'A#03') {
//                $data[$key]->I1 = $A3C1;
//                $data[$key]->I2 = $A3C2;
//                $data[$key]->I3 = $A3C3;
//                $data[$key]->I4 = $A3C4;
//            }
//
//            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
//            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
//            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
//            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
//        }

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
                "ReportName" => "DỰ TOÁN THU, CHI NGÂN SÁCH NHÀ NƯỚC NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    // row 1
    public function insertTableTmpR1($whereCondition,$coa,$i,$intranstyle = ''){
        $sqlUpdate = "UPDATE table_tmp  AS TT, (SELECT SUM(FCCreditAmount) AS I
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = $coa
        AND (LEFT(A.RevenueNo, 4) = '1111' OR LEFT(A.RevenueNo, 6) = '190126') $whereCondition
        ) AS HH SET TT.$i = HH.I WHERE TT.ItemID = 'A#01'";
        DB::statement($sqlUpdate);
    }
    //row 2
    public function insertTableTmpR2($whereCondition,$coa,$i,$intranstyle = ''){
        $sqlUpdate = "UPDATE report_sbp_3422016ttbtc_mb04 AS TT, (SELECT SUM(FCCreditAmount) AS I
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = $coa
        AND (LEFT(A.RevenueNo, 4) = '1112' OR LEFT(A.RevenueNo, 4) = '1113' OR LEFT(A.RevenueNo, 6) = '190127')
        $whereCondition
        ) AS HH SET TT.$i = HH.I WHERE TT.ItemID = 'A#02'";
        DB::statement($sqlUpdate);
    }
    //row 3
    public function insertTableTmpR3($whereCondition,$coa,$i,$intranstyle = ''){
        $sqlUpdate = "UPDATE report_sbp_3422016ttbtc_mb04 AS TT, (SELECT SUM(FCCreditAmount) AS I
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = $coa
        AND A.RevenueNo IN ('111001','111010','111011','111012','111013','111014','190120')
        $whereCondition
        ) AS HH SET TT.$i = HH.I WHERE TT.ItemID = 'A#03'";
        DB::statement($sqlUpdate);
    }
    //row 4
    public function insertTableTmpR4($whereCondition,$coa,$i,$intranstyle=''){
        $sqlUpdate = "UPDATE report_sbp_3422016ttbtc_mb04 AS TT, (SELECT SUM(FCCreditAmount) AS I
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = $coa
        AND A.RevenueNo='110902'
        $whereCondition
        ) AS HH SET TT.$i = HH.I WHERE TT.ItemID = 'A#04'";
        DB::statement($sqlUpdate);
    }
    //row 5
    public function insertTableTmpR5($whereCondition,$coa,$i,$intranstyle=''){
        $sqlUpdate = "UPDATE report_sbp_3422016ttbtc_mb04 AS TT, (SELECT SUM(FCCreditAmount) AS I
        FROM act_gl_books AS A
        WHERE A.ConAccountNo = $coa
        AND A.RevenueNo IN ('111414','111416','111417','111418','111419','111420','111421','190128')
        $whereCondition
        ) AS HH SET TT.$i = HH.I WHERE TT.ItemID = 'A#05'";
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
                    SET I1 = I1 + " . $I1 . ", I2 = I2 + " . $I2 . ", I3 = I3 + " . $I3 . ", I4 = I4 + " . $I4 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }
}
