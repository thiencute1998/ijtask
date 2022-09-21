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

class SBPND312017NDCPMB37Controller extends Controller {
    use Common;
    public function SBPND312017NDCP_MB37(Request $request){

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
        $whereCommonCondition = '';
        if(in_array($userCompany->SumCompanyType, [2,3])){
            if($userCompany->SumCompanyType == 2){
                $whereCommonCondition .= " AND C.ManagementLevel = " . $userCompany->ManagementLevel;
            }
            else{
                // Tổng hợp theo ngành
                $whereCommonCondition .= " C.SectorNo = '" . $userCompany->SectorNo . "' AND C.ManagementLevel = " . $userCompany->ManagementLevel;
            }
            if($userCompany->ManagementLevel == 2){
                $whereCommonCondition .= " AND C.ProvinceID = $userCompany->ProvinceID";
            }
            else if($userCompany->ManagementLevel == 3){
                $whereCommonCondition .= " AND C.DistrictID = $userCompany->DistrictID";
            }
            else if($userCompany->ManagementLevel == 4){
                $whereCommonCondition .= " AND C.CommuneID = $userCompany->CommuneID";
            }

        }
        else{
            $whereCommonCondition .= " AND C.CompanyID = '" . $userCompany->CompanyID . "'";
        }
        $whereCommonCondition .= " AND A.BudgetLevel = $userCompany->ManagementLevel";


        $yearPre = (int)$year - 1;
        $yearNext = (int)$year + 1;

        $whereCondition = " AND YEAR(A.PeriodFromDate) = $yearNext AND A.AccountNo = '0411' AND LEFT(EC.CateNo, 5) = '20151'
        AND (CC.CateNo = '20012102' OR (CC.CateNo IN ('20012201', '20012202', '20012203', '20012204') AND CC.CateValue = '1'))
        AND A.BudgetLevel IN (2, 3, 4) AND A.InTransTypeID NOT IN (16, 17, 18) $whereCommonCondition";

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
          `I13` double(20,4) DEFAULT NULL,
          `I14` double(20,4) DEFAULT NULL,
          `I15` double(20,4) DEFAULT NULL,
          `I16` double(20,4) DEFAULT NULL,
          `Detail` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $this->insertC2_6_8_11($whereCondition);
        $this->insertC3_4_5_9_10_14_15_16($whereCondition);
        $this->insertC7($whereCondition);
        $this->insertC12_13($whereCondition);



        $this->setLevel();

        // data default
        $this->setDefaultData();

        // sum column
        $this->sumColumn();

        // sum row
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumRow($maxLevel, 0);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();

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
                "ReportName" => "DỰ TOÁN CHI ĐẦU TƯ PHÁT TRIỂN CỦA NGÂN SÁCH CẤP TỈNH (HUYỆN, XÃ) CHO TỪNG CƠ QUAN, TỔ CHỨC THEO LĨNH VỰC NĂM " . $yearNext,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function insertC2_6_8_11($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Detail, I2, I6, I8, I11)
        SELECT ItemID, ParentID, ItemName, 1 as Detail,
        SUM(CASE WHEN SectorCateNo = '2000303' THEN I ELSE 0 END) as I2,
        SUM(CASE WHEN SectorCateNo = '2000305' THEN I ELSE 0 END) as I6,
        SUM(CASE WHEN SectorCateNo = '2000308' THEN I ELSE 0 END) as I8,
        SUM(CASE WHEN SectorCateNo = '2000307' THEN I ELSE 0 END) as I11 FROM
        (
        SELECT CONCAT('A#', C.CompanyNo) as ItemID, ParentID, C.CompanyName as ItemName, SUM(LCDebitAmount) as I,
        C.CompanyNo, LEFT(SC.CateNo, 7) as SectorCateNo FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        WHERE (LEFT(SC.CateNo, 7) = '2000303' OR LEFT(SC.CateNo, 7) = '2000305' OR LEFT(SC.CateNo, 7) = '2000308' OR LEFT(SC.CateNo, 7) = '2000307')
        $whereCondition
        GROUP BY C.CompanyNo, LEFT(SC.CateNo, 7)
        ) as CH GROUP BY CompanyNo";

        DB::statement($sql);
    }

    public function insertC3_4_5_9_10_14_15_16($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Detail, I3, I4, I5, I9, I10, I14, I15, I16)
        SELECT * FROM
        (SELECT ItemID, ParentID, ItemName, 1 as Detail,
        SUM(CASE WHEN SectorCateNo = '2000304' THEN I ELSE 0 END) as A1,
        SUM(CASE WHEN SectorCateNo = '2000301' THEN I ELSE 0 END) as A2,
        SUM(CASE WHEN SectorCateNo = '2000302' THEN I ELSE 0 END) as A3,
        SUM(CASE WHEN SectorCateNo = '2000309' THEN I ELSE 0 END) as A4,
        SUM(CASE WHEN SectorCateNo = '2000311' THEN I ELSE 0 END) as A5,
        SUM(CASE WHEN SectorCateNo = '2000312' THEN I ELSE 0 END) as A6,
        SUM(CASE WHEN SectorCateNo = '2000313' THEN I ELSE 0 END) as A7,
        SUM(CASE WHEN SectorCateNo = '2000314' THEN I ELSE 0 END) as A8 FROM
        (
        SELECT CONCAT('A#', C.CompanyNo) as ItemID, ParentID, C.CompanyName as ItemName, SUM(LCDebitAmount) as I,
        C.CompanyNo, SC.CateNo as SectorCateNo FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        WHERE (SC.CateNo IN ('2000304', '2000301', '2000302', '2000309' , '2000311', '2000312', '2000313', '2000314') AND SC.CateValue = '1')
        $whereCondition
        GROUP BY C.CompanyNo, SC.CateNo
        ) as CH GROUP BY CompanyNo) as DT ON DUPLICATE KEY UPDATE I3 = A1, I4 = A2, I5 = A3, I9 = A4, I10 = A5, I14 = A6, I15 = A7, I16 = A8";

        DB::statement($sql);
    }

    public function insertC7($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Detail, I7)
        SELECT * FROM
        (
        SELECT CONCAT('A#', C.CompanyNo) as ItemID, ParentID, C.CompanyName as ItemName, 1 as Detail, SUM(LCDebitAmount) as A1
        FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        WHERE (SC.CateNo = '2000306' AND SC.CateValue IN ('1', '2'))
        $whereCondition
        GROUP BY C.CompanyNo
        ) as DT ON DUPLICATE KEY UPDATE I7 = A1";

        DB::statement($sql);
    }

    public function insertC12_13($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Detail, I12, I13)
        SELECT * FROM
        (SELECT ItemID, ParentID, ItemName, 1 as Detail,
        SUM(CASE WHEN SectorCateNo = '20003070101' THEN I ELSE 0 END) as A1,
        SUM(CASE WHEN SectorCateNo = '20003070102' THEN I ELSE 0 END) as A2 FROM
        (
        SELECT CONCAT('A#', C.CompanyNo) as ItemID, ParentID, C.CompanyName as ItemName, SUM(LCDebitAmount) as I,
        C.CompanyNo, SC.CateNo as SectorCateNo FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        WHERE (SC.CateNo IN ('20003070101', '20003070102'))
        $whereCondition
        GROUP BY C.CompanyNo, SC.CateNo
        ) as CH GROUP BY CompanyNo) as DT ON DUPLICATE KEY UPDATE I12 = A1, I13 = A2";

        DB::statement($sql);
    }

    public function setDefaultData(){
        $insert = [
            'ItemID'=> 'A', 'ParentID'=> '', 'ItemName'=> 'Tổng số', 'Level'=> 0, 'FontWeight'=> 1
        ];
        DB::table('table_tmp')->insert($insert);
    }

    // sum column
    public function sumColumn(){
        $listItems = DB::table('table_tmp')->get();
        foreach($listItems as $item){
            $I1 = $item->I2 + $item->I3 + $item->I4 + $item->I5 + $item->I6 + $item->I7 + $item->I8 + $item->I9
                + $item->I10 + $item->I11 + $item->I12 + $item->I13 + $item->I14 + $item->I15 + $item->I16;
            DB::table('table_tmp')->where('ItemID',$item->ItemID)->update([
                'I1'=> $I1
            ]);
        }
    }

    // sum up
    public function sumRow($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
            SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8,
            SUM(M.I9) AS I9, SUM(M.I10) AS I10, SUM(M.I11) AS I11, SUM(M.I12) AS I12,
            SUM(M.I13) AS I13, SUM(M.I14) AS I14, SUM(M.I15) AS I15, SUM(M.I16) AS I16
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
                    $I13 = ($value['I13']) ? $value['I13'] : 0;
                    $I14 = ($value['I14']) ? $value['I14'] : 0;
                    $I15 = ($value['I15']) ? $value['I15'] : 0;
                    $I16 = ($value['I16']) ? $value['I16'] : 0;
                    $update = "UPDATE table_tmp
                SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ",
                I9 = " . $I9 . ", I10 = " . $I10 . ", I11 = " . $I11 . ", I12 = " . $I12 . ",
                I13 = " . $I13 . ", I14 = " . $I14 . ", I15 = " . $I15 . ", I16 = " . $I16 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }
    }

}
