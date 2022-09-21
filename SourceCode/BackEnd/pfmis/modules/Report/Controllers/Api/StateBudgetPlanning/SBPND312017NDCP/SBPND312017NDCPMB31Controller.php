<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBPND312017NDCP;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Module\Report\Traits\Common;
use PHPUnit\Framework\Constraint\Count;

class SBPND312017NDCPMB31Controller extends Controller {
    use Common;
    public function SBPND312017NDCP_MB31(Request $request){

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

        $userCompany = DB::table('company')->where('CompanyID','=', Auth::user()->CompanyID)->first();
        $whereCommonCondition = ' AND A.InTransTypeID NOT IN (16, 17 ,18) ';
        if(in_array($userCompany->SumCompanyType, [2,3])){
            if($userCompany->SumCompanyType == 1){
                // Tổng hợp đơn vị con
                $whereCommonCondition = " AND C.CompanyNo = '" . $userCompany->CompanyNo ."'";
            }
            else{
                if($userCompany->SumCompanyType == 2){
                    $whereCommonCondition .= " AND C.ManagementLevel = " . ($userCompany->ManagementLevel + 1);
                }
                else{
                    // Tổng hợp theo ngành
                    $whereCommonCondition .= " C.SectorNo = '" . $userCompany->SectorNo . "' AND C.ManagementLevel = " . $userCompany->ManagementLevel;
                }
            }
            if($userCompany->ManagementLevel == 2){
                $whereCommonCondition .= " AND C.ProvinceID = $userCompany->ProvinceID";
            }
            else{
                $whereCommonCondition .= " AND C.DistrictID = $userCompany->DistrictID";
            }
        }


        $yearNext = (int)$year + 1;

        $whereCondition = " AND YEAR(A.PeriodFromDate) = $yearNext $whereCommonCondition ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
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
          `District` varchar(50) DEFAULT NULL,
          `Commune` varchar(50) DEFAULT NULL,
          `Detail` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        if(in_array($userCompany->SumCompanyType, [2,3])){
            if($userCompany->ManagementLevel == 2){
                $this->insertDistrict($whereCondition);
            }
            else{
                $this->insertCommune($whereCondition);
            }
            $this->setLevel();

            // data default
            $this->setDefaultData();

            // sum column
            $this->sumColumn();

            // sum row
            $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
            $this->sumRow($maxLevel, 0);
        }

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID','ASC')->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Ước thực hiện năm ' . $yearNext;
            $data[0]->HeaderName2 = 'Dự toán năm ' . $yearNext;
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
        }

        $company = Company::find($companyID);
        $dateTime = "Vĩnh Phú, ngày $date tháng $month năm $yearC";
        if ($company) {
            $dateTime = $company->Address . ", ngày $date tháng $month năm $yearC";
        }

        $subReportName = '';
        if($userCompany->ManagementLevel == 2){
            $subReportName = "Huyện";
        }
        else if($userCompany->ManagementLevel == 3){
            $subReportName = "XÃ";
        }

        $response = [
            'master' => [
                "Year" => "",
                "CreateName" => "Trần Thế Thái",
                "TransNo" => "00001",
                "ReportName" => "DỰ TOÁN THU NGÂN SÁCH NHÀ NƯỚC TRÊN ĐỊA BÀN TỪNG $subReportName NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function insertDistrict($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, District, Detail, I2, I3, I4, I6, I7, I8)
        SELECT ItemID, ParentID, ItemName, DistrictID, 1 as Detail,
        SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20111' THEN I ELSE 0 END) as I2,
        SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20112' THEN I ELSE 0 END) as I3,
        SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20113' THEN I ELSE 0 END) as I4,
        SUM(CASE WHEN AccountNo = '0311' AND RevenueCateNo = '20111' THEN I ELSE 0 END) as I6,
        SUM(CASE WHEN AccountNo = '0311' AND RevenueCateNo = '20112' THEN I ELSE 0 END) as I7,
        SUM(CASE WHEN AccountNo = '0311' AND RevenueCateNo = '20113' THEN I ELSE 0 END) as I8
        FROM
        (SELECT CONCAT('A#', C.DistrictID) as ItemID, 'A' as ParentID, C.DistrictName as ItemName, C.DistrictID,
        A.AccountNo, LEFT(RC.CateNo, 5) as RevenueCateNo, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        WHERE A.AccountNo IN ('0711', '0311')
        AND (LEFT(RC.CateNo, 5) = '20111' OR LEFT(RC.CateNo, 5) = '20112' OR LEFT(RC.CateNo, 5) = '20113')
        AND LEFT(CC.CateNo, 4) = '2001'
        $whereCondition
        GROUP BY C.DistrictID, A.AccountNo, LEFT(RC.CateNo, 5)
        ) as CH GROUP BY DistrictID";

        DB::statement($sql);
    }

    public function insertCommune($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Commune, Detail, I2, I3, I4, I6, I7, I8)
        SELECT ItemID, ParentID, ItemName, CommuneID, 1 as Detail,
        SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20111' THEN I ELSE 0 END) as I2,
        SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20112' THEN I ELSE 0 END) as I3,
        SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20113' THEN I ELSE 0 END) as I4,
        SUM(CASE WHEN AccountNo = '0311' AND RevenueCateNo = '20111' THEN I ELSE 0 END) as I6,
        SUM(CASE WHEN AccountNo = '0311' AND RevenueCateNo = '20112' THEN I ELSE 0 END) as I7,
        SUM(CASE WHEN AccountNo = '0311' AND RevenueCateNo = '20113' THEN I ELSE 0 END) as I8
        FROM
        (SELECT CONCAT('A#', C.CommuneID) as ItemID, 'A' as ParentID, C.CommuneName as ItemName, C.CommuneID,
        A.AccountNo, LEFT(RC.CateNo, 5) as RevenueCateNo, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        WHERE A.AccountNo IN ('0711', '0311')
        AND (LEFT(RC.CateNo, 5) = '20111' OR LEFT(RC.CateNo, 5) = '20112' OR LEFT(RC.CateNo, 5) = '20113')
        AND LEFT(CC.CateNo, 4) = '2001'
        $whereCondition
        GROUP BY C.CommuneID, A.AccountNo, LEFT(RC.CateNo, 5)
        ) as CH GROUP BY CommuneID";

        DB::statement($sql);
    }

    // sum column
    public function sumColumn(){
        $listItems = DB::table('table_tmp')->get();
        foreach($listItems as $item){
            $I1 = $item->I2 + $item->I3 + $item->I4;
            $I5 = $item->I6 + $item->I7 + $item->I8;
            $I9 = $I1 == 0 ? $I5 . "%" : ($I5 / $I1) * 100 . "%";
            $I10 = $item->I2 == 0 ? $item->I6 . "%" : ($item->I6 / $item->I2) * 100 . "%";
            $I11 = $item->I3 == 0 ? $item->I7 . "%" : ($item->I7 / $item->I3) * 100 . "%";
            $I12 = $item->I4 == 0 ? $item->I8 . "%" : ($item->I8 / $item->I4) * 100 . "%";

            DB::table('table_tmp')->where('ItemID',$item->ItemID)->update([
                'I1'=> $I1, 'I5'=> $I5, 'I9'=> $I9,
                'I10'=> $I10, 'I11'=> $I11, 'I12'=> $I12
            ]);
        }
    }

    public function setDefaultData(){
        $insert = [
          'ItemID'=> 'A', 'ParentID'=> '', 'ItemName'=> 'Tổng số', 'Level'=> 0, 'FontWeight'=> 1
        ];
        DB::table('table_tmp')->insert($insert);
    }

    // sum up
    public function sumRow($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
            SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8,
            SUM(M.I9) AS I9, SUM(M.I10) AS I10, SUM(M.I11) AS I11, SUM(M.I12) AS I12
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
                    $I11 = ($value['I11']) ? $value['I11'] : 0;
                    $I12 = ($value['I12']) ? $value['I12'] : 0;
                    $update = "UPDATE table_tmp
                SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ",
                I9 = " . $I9 . ", I10 = " . $I10 . ", I11 = " . $I11 . ", I12 = " . $I12 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }

    }

}
