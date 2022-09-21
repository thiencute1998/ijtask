<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB08Controller extends Controller {
    use Common;
    private $tnDateType = 1;
    private $tnAmountType = 1;
    private $tcCoaTypeNo = '01';
    public function SBP3422016TTBTC_MB08(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : 2021;
        $fromDate = (isset($filter['Year'])) ? $filter['Year'].'-01-01' : $year.'-01-01';
        $toDate = (isset($filter['Year'])) ? $filter['Year'].'-12-31' : $year.'-12-31';
        $filterDate = date('Y-m-d', strtotime('+1 year', strtotime($toDate)) );
        $currentDate = date('Y-m-d');
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
          `ItemID` varchar(100) NOT NULL PRIMARY KEY,
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
          `I31` double(20,4) DEFAULT NULL,
          `I32` double(20,4) DEFAULT NULL,
          `I33` double(20,4) DEFAULT NULL,
          `I34` double(20,4) DEFAULT NULL,
          `I35` double(20,4) DEFAULT NULL,
          `I36` double(20,4) DEFAULT NULL,
          `I37` double(20,4) DEFAULT NULL,
          `I38` double(20,4) DEFAULT NULL,
          `I39` double(20,4) DEFAULT NULL,
          `I40` double(20,4) DEFAULT NULL,
            `SbiCategoryCate` varchar(20) DEFAULT '',
            `SponsorName` varchar(255) DEFAULT '',
            `PerformDate` varchar(50) DEFAULT '',
            `Project` varchar(50) DEFAULT '',
            `Program` varchar(50) DEFAULT '',
            `SbiCategoryCateName` varchar(255) DEFAULT '',
            `ListType` varchar(10) DEFAULT '',
            `ListTypeName` varchar(255) DEFAULT '',
            `Detail` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $this->insertR1C1C3C5C7($currentDate);
        $this->insertR2C1C3C5C7($currentDate);
        $this->insertOrUpdateR1C2C4C6C8($currentDate);
        $this->insertOrUpdateR2C2C4C6C8($currentDate);

//        $this->createTemporaryListType();
        //tree data
        $this->createParentDataTree(['sbi_category_cate','list_type',['program','project']]);
        $this->setLevel();
        $this->setDefaultData();
        $this->updateIndexReport('table_tmp');
        // sum up
        $this->sumUpTableTmp(1, 4);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID')->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $yearPre;
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

    // sum up
    public function sumUpTableTmp($start, $end = null){
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i >= $start; $i--) {
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

    public function insertR1C1C3C5C7($currentDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, SbiCategoryCate, Program, SbiCategoryCateName, ListType, ListTypeName, Detail, I1, I3, I5, I7)
                SELECT ItemID, ParentID, ItemName,SbiCategoryCate, Program, SbiCategoryCateName, '01', 'Chương trình', 1,
                MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20152' THEN I ELSE 0 END) as I1,
	            MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20151' THEN I ELSE 0 END) as I3,
                MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20152' THEN I ELSE 0 END) as I5,
	            MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20151' THEN I ELSE 0 END) as I7 FROM
                (SELECT CONCAT('A#',SC.CateValue,'#01','#',A.ProgramNo) as ItemID, CONCAT('A#',SC.CateValue,'#01') as ParentID,
                A.ProgramName as ItemName, SC.CateValue as SbiCategoryCate, A.ProgramNo as Program, SC.Description as SbiCategoryCateName, A.BudgetAllocTypeID, EC.CateNo as ExpenseCateNo,1
                , IF($this->tnAmountType = 1, SUM(LCDebitAmount), SUM(FCDebitAmount)) AS I
                FROM act_gl_books AS A
                INNER JOIN sbi_category_cate as SC on A.SbiCategoryID = SC.SbiCategoryID
                INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
                INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
                WHERE A.InTransTypeID NOT IN (16,17,18)
                AND SC.CateNo = '001' AND SC.CateValue IS NOT NULL
                AND A.AccountNo = '091'
                AND BudgetAllocTypeID IN (1,3)
                AND (LEFT(EC.CateNo,5) = '20151' OR LEFT(EC.CateNo,5) = '20152')
                AND (CC.CateNo IN ('20012201','20011202','20011203')
                OR (CC.CateNo = '20011202' AND CC.CateValue = '1')
                OR (CC.CateNo = '20011203' AND CC.CateValue = '1')
                OR (CC.CateNo = '20011204' AND CC.CateValue = '1'))
                AND A.ProgramNo != ''
                AND PostType = 1
                AND (IF($this->tnDateType = 1,
                (PostDate <= '". $currentDate ."'),
                (TransDate <= '". $currentDate ."')
                )) AND CoaTypeNo = '01' GROUP BY SC.CateValue, A.ProgramNo, A.BudgetAllocTypeID, EC.CateNo
                ) as CH GROUP BY SbiCategoryCate, Program";
        DB::statement($sql);

    }

    public function insertR2C1C3C5C7($currentDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, PerformDate, SbiCategoryCate, Project, SbiCategoryCateName, ListType, ListTypeName, Detail, I1, I3, I5, I7)
                SELECT ItemID, ParentID, ItemName, PerformDate, SbiCategoryCate, Project, SbiCategoryCateName, '02', 'Dự án', 1,
                MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20152' THEN I ELSE 0 END) as I1,
	            MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20151' THEN I ELSE 0 END) as I3,
                MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20152' THEN I ELSE 0 END) as I5,
	            MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20151' THEN I ELSE 0 END) as I7 FROM
                (SELECT CONCAT('A#',SC.CateValue,'#02','#',A.ProjectNo) as ItemID, CONCAT('A#',SC.CateValue,'#02') as ParentID,
                A.ProjectName as ItemName, CONCAT(P.StartedDate, ' - ', P.FinishedDate) as PerformDate, SC.CateValue as SbiCategoryCate, A.ProjectNo as Project, SC.Description as SbiCategoryCateName, A.BudgetAllocTypeID, EC.CateNo as ExpenseCateNo,1
                , IF($this->tnAmountType = 1, SUM(LCDebitAmount), SUM(FCDebitAmount)) AS I
                FROM act_gl_books AS A
                INNER JOIN sbi_category_cate as SC on A.SbiCategoryID = SC.SbiCategoryID
                INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
                INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
                INNER JOIN project as P on A.ProjectID = P.ProjectID
                WHERE A.InTransTypeID NOT IN (16,17,18)
                AND SC.CateNo = '001' AND SC.CateValue IS NOT NULL
                AND A.AccountNo = '091'
                AND BudgetAllocTypeID IN (1,3)
                AND (LEFT(EC.CateNo,5) = '20151' OR LEFT(EC.CateNo,5) = '20152')
                AND (CC.CateNo IN ('20012201','20011202','20011203')
                OR (CC.CateNo = '20011202' AND CC.CateValue = '1')
                OR (CC.CateNo = '20011203' AND CC.CateValue = '1')
                OR (CC.CateNo = '20011204' AND CC.CateValue = '1'))
                AND A.ProjectNo != ''
                AND PostType = 1
                AND (IF($this->tnDateType = 1,
                (PostDate <= '". $currentDate ."'),
                (TransDate <= '". $currentDate ."')
                )) AND CoaTypeNo = '01' GROUP BY SC.CateValue, A.ProjectNo, A.BudgetAllocTypeID, EC.CateNo
                ) as CH GROUP BY SbiCategoryCate, Project";
        DB::statement($sql);
    }

    public function insertOrUpdateR1C2C4C6C8($currentDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, SbiCategoryCate, Program, SbiCategoryCateName, ListType, ListTypeName, Detail, I2, I4, I6, I8)
                SELECT * FROM
                (SELECT ItemID, ParentID, ItemName, SbiCategoryCate, Program, SbiCategoryCateName, '01', 'Chương trình', 1,
                MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20152' AND CapitalCateNo = '20012102' THEN I ELSE 0 END) as AA,
	            MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20151' AND CapitalCateNo = '20012101' THEN I ELSE 0 END) as BB,
                MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20152' AND CapitalCateNo = '20012102' THEN I ELSE 0 END) as CC,
	            MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20151' AND CapitalCateNo = '20012101' THEN I ELSE 0 END) as DD FROM
                (SELECT CONCAT('A#',SC.CateValue,'#01','#',A.ProgramNo) as ItemID, CONCAT('A#',SC.CateValue,'#01') as ParentID,
                A.ProgramName as ItemName, SC.CateValue as SbiCategoryCate, A.ProgramNo as Program, SC.Description as SbiCategoryCateName, A.BudgetAllocTypeID, EC.CateNo as ExpenseCateNo,1,
                CC.CateNo as CapitalCateNo, IF($this->tnAmountType = 1, SUM(LCDebitAmount), SUM(FCDebitAmount)) AS I
                FROM act_gl_books AS A
                INNER JOIN sbi_category_cate as SC on A.SbiCategoryID = SC.SbiCategoryID
                INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
                INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
                INNER JOIN program as P on A.ProgramID = P.ProgramID
                WHERE A.InTransTypeID NOT IN (16,17,18)
                AND SC.CateNo = '001' AND SC.CateValue IS NOT NULL
                AND A.AccountNo = '091'
                AND BudgetAllocTypeID IN (1,3)
                AND (LEFT(EC.CateNo,5) = '20151' OR LEFT(EC.CateNo,5) = '20152')
                AND CC.CateNo IN ('20012102','20012101')
                AND A.ProgramNo != ''
                AND PostType = 1
                AND (IF($this->tnDateType = 1,
                (PostDate <= '". $currentDate ."'),
                (TransDate <= '". $currentDate ."')
                )) AND CoaTypeNo = '01' GROUP BY SC.CateValue, A.ProgramNo, A.BudgetAllocTypeID, EC.CateNo, CC.CateNo
                ) as CH GROUP BY SbiCategoryCate, Program) as DT ON DUPLICATE KEY UPDATE I2 = AA, I4 = BB, I6 = CC, I8 = DD";

            DB::statement($sql);
    }

    public function insertOrUpdateR2C2C4C6C8($currentDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, PerformDate, SbiCategoryCate, Project, SbiCategoryCateName, ListType, ListTypeName, Detail, I2, I4, I6, I8)
                SELECT * FROM
                (SELECT ItemID, ParentID, ItemName, PerformDate, SbiCategoryCate, Project, SbiCategoryCateName, '02', 'Dự án', 1,
                MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20152' AND CapitalCateNo = '20012102' THEN I ELSE 0 END) as AA,
	            MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20151' AND CapitalCateNo = '20012101' THEN I ELSE 0 END) as BB,
                MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20152' AND CapitalCateNo = '20012102' THEN I ELSE 0 END) as CC,
	            MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20151' AND CapitalCateNo = '20012101' THEN I ELSE 0 END) as DD FROM
                (SELECT CONCAT('A#',SC.CateValue,'#02','#',A.ProjectNo) as ItemID, CONCAT('A#',SC.CateValue,'#02') as ParentID,
                A.ProjectName as ItemName, CONCAT(P.StartedDate, ' - ', P.FinishedDate) as PerformDate, SC.CateValue as SbiCategoryCate, A.ProjectNo as Project, SC.Description as SbiCategoryCateName, A.BudgetAllocTypeID, EC.CateNo as ExpenseCateNo,1,
                CC.CateNo as CapitalCateNo, IF($this->tnAmountType = 1, SUM(LCDebitAmount), SUM(FCDebitAmount)) AS I
                FROM act_gl_books AS A
                INNER JOIN sbi_category_cate as SC on A.SbiCategoryID = SC.SbiCategoryID
                INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
                INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
                INNER JOIN project as P on A.ProjectID = P.ProjectID
                WHERE A.InTransTypeID NOT IN (16,17,18)
                AND SC.CateNo = '001' AND SC.CateValue IS NOT NULL
                AND A.AccountNo = '091'
                AND BudgetAllocTypeID IN (1,3)
                AND (LEFT(EC.CateNo,5) = '20151' OR LEFT(EC.CateNo,5) = '20152')
                AND CC.CateNo IN ('20012102','20012101')
                AND A.ProjectNo != ''
                AND PostType = 1
                AND (IF($this->tnDateType = 1,
                (PostDate <= '". $currentDate ."'),
                (TransDate <= '". $currentDate ."')
                )) AND CoaTypeNo = '01' GROUP BY SC.CateValue, A.ProjectNo, A.BudgetAllocTypeID, EC.CateNo, CC.CateNo
                ) as CH GROUP BY SbiCategoryCate, Project) as DT ON DUPLICATE KEY UPDATE I2 = AA, I4 = BB, I6 = CC, I8 = DD";

        DB::statement($sql);
    }

    public function setDefaultData(){
        DB::table('table_tmp')->insert([
            'ItemID'=> 'A',
            'ParentID'=> null,
            'ItemName'=> 'Tổng số',
            'Level'=> 0
        ]);
    }

    public function createTemporaryListType(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `list_type` (
            `STT` varchar(10) DEFAULT '',
            `ItemID` varchar(10) DEFAULT '',
            `ParentID` varchar(100) DEFAULT '',
            `Level` int(2) DEFAULT 1,
            `ItemName` varchar(255) DEFAULT NULL,
            `FontWeight` tinyint(1) DEFAULT 0,
            `Italic` tinyint(1) DEFAULT 0,
            `Center` tinyint(1) DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

        $insertArr[] = [
            'ItemID'=> '01',
            'ItemName'=> 'Chương trình'
        ];
        $insertArr[] = [
            'ItemID'=> '02',
            'ItemName'=> 'Dự án'
        ];
        DB::statement($createTable);
        DB::table('list_type')->insert($insertArr);

    }



}
