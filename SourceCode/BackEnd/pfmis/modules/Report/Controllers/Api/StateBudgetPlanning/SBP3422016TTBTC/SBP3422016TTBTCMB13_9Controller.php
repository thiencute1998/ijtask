<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB13_9Controller extends Controller {
    use Common;

    public function SBP3422016TTBTC_MB13_9(Request $request){
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

        $yearPre = (int)$year - 1;
        $yearNext = (int)$year + 1;

        $whereCommonCondition = "AND A.InTransTypeID NOT IN (16,17,18) AND A.BudgetAllocTypeID != '3' AND C.CapitalInOut = 1 ";
        $currentDate = date('Y-m-d');
        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year - 1 AND $year $whereCommonCondition";
        $whereDebt = " AND A.PeriodFromDate <= '". $currentDate ."' $whereCommonCondition ";
        $wherePeriodDate = " AND A.PeriodType = 9 AND YEAR(A.PeriodFromDate) >= $year AND YEAR(A.PeriodToDate) <= ($year + 5) $whereCommonCondition";
        $whereYear = " AND YEAR(A.PeriodFromDate) = $year $whereCommonCondition ";
        $wherePeriodDate1 = " AND A.PeriodFromDate BETWEEN '" . $year . "-01-01' AND '" . $year . "-06-30' ";
        $whereNextYear = " AND YEAR(A.PeriodFromDate) = $year + 1 $whereCommonCondition ";

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
            `Project` varchar(50) DEFAULT '',
            `BuildAddress` varchar(255) DEFAULT '',
            `CapableDesign` varchar(255) DEFAULT '',
            `PerformDate` varchar(255) DEFAULT '',
            `InvestdocNo` varchar(255) DEFAULT '',
            `SectorCateList` varchar(50) DEFAULT '',
            `ProjectCateTemporary` varchar(50) DEFAULT '',
            `ProjectPhaseTemporary` varchar(20) DEFAULT '',
            `ProjectGroupTemporary` varchar(20) DEFAULT '',
            `Detail` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        //calculator here

        $this->createTemporaryProjectCate();
        $this->createTemporaryProjectPhase($year);
        $this->createTemporaryProjectGroup();

        $countRecords = DB::table('table_tmp')->count();

        $this->insertR1C6($whereDebt);
        $this->insertOrUpdateR1C7($whereDebt);
        if(DB::table('table_tmp')->count() > $countRecords){
            $this->getParentDataTreeCommon(['sector_cate_list',['ProjectCateTemporary','1'],'project']);
            $countRecords = DB::table('table_tmp')->count();
        }

        $whereR2 = " AND ((YEAR(P.ExpectedFinishDate) = $year AND YEAR(P.ExpectedHandoverDate) = $year) OR YEAR(P.ExpectedFinishDate) >= $year + 1 OR YEAR(P.ExpectedStartDate) = $year + 1) AND PC.CateNo = '009' AND PC.CateValue = '2' ";
        $this->insertR2C6($whereDebt, $whereR2 , $year);
        $this->insertOrUpdateR2C7($whereDebt, $whereR2 , $year);
        if(DB::table('table_tmp')->count() > $countRecords){
            $this->getParentDataTreeCommon(['sector_cate_list',['ProjectCateTemporary','2'], 'ProjectPhaseTemporary', 'ProjectGroupTemporary','project']);
        }

        $this->setLevel();

        //set STT
        $maxLevel = DB::table('table_tmp')->select(DB::raw('Max(Level) as maxLevel'))->first()->maxLevel;
        $this->setIndexMB23($maxLevel);

        // sum Row
        $this->sumUpRow();


        // Thêm dữ liệu mặc định
        $insertArr1 = [
            ['STT'=>'', 'ItemID'=> 'A', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG SỐ', 'FontWeight'=> '1'],
        ];
        DB::table('table_tmp')->insert($insertArr1);

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumUpTableTmp($maxLevel);


        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID','ASC')->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $yearPre;
            $data[0]->HeaderName2 = 'Năm ' . $year;
            $data[0]->HeaderName3 = 'Lũy kế vốn đã bố trí đến hết năm ' . $year;
            $data[0]->HeaderName4 = 'Dự kiến kế hoạch đầu tư vốn NSNN năm ' .$yearNext;
        }

        foreach ($data as $key => $value) {

            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
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
                "ReportName" => "DỰ TOÁN CHI ĐẦU TƯ NGUỒN NSNN (VỐN TRONG NƯỚC) (*) NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function createTemporaryProjectCate(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `ProjectCateTemporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $insertA = [
            ['ItemID'=> '1','ItemName'=> 'Chuẩn bị đầu tư'],
            ['ItemID'=> '2','ItemName'=> 'Thực hiện dự án'],
        ];

        DB::table('ProjectCateTemporary')->insert($insertA);

    }

    public function createTemporaryProjectPhase($year){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `ProjectPhaseTemporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $insertA = [
            ['ItemID'=> '01','ItemName'=> 'Các dự án hoàn thành, bàn giao, đưa vào sử dụng trước ngày 31/12/' . $year],
            ['ItemID'=> '02','ItemName'=> 'Các dự án dự kiến hoàn thành năm '. ($year + 1)],
            ['ItemID'=> '03','ItemName'=> 'Các dự án chuyển tiếp hoàn thành sau năm ' . ($year + 1)],
            ['ItemID'=> '04','ItemName'=> 'Các dự án khởi công mới năm '. ($year + 1)],
        ];

        DB::table('ProjectPhaseTemporary')->insert($insertA);
    }

    public function createTemporaryProjectGroup(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `ProjectGroupTemporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $insertA = [
            ['ItemID'=> 'QTQG','ItemName'=> 'Dự án quan trọng quốc gia'],
            ['ItemID'=> 'A','ItemName'=> 'Dự án nhóm A'],
            ['ItemID'=> 'B','ItemName'=> 'Dự án nhóm B'],
            ['ItemID'=> 'C','ItemName'=> 'Dự án nhóm C'],
        ];

        DB::table('ProjectGroupTemporary')->insert($insertA);
    }


    // Chuẩn bị dự án

    public function selectCommon(){
        return " P.BuildAddress, P.CapableDesign, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as PerformDate, P.InvestdocNo ";
    }

    public function insertR1C6($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, Project, Detail, I1)
            SELECT CONCAT('A#', SC.CateNo, '#', '1', '#',A.ProjectNo) as ItemID, CONCAT('A#', SC.CateNo, '#', '1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 1 as ProjectCateTemporary,A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2001' OR LEFT(CC.CateNo,4) = '2002')
            AND A.AccountNo = '091'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            $whereDebt
            GROUP BY SC.CateNo, A.ProjectNo
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C7($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, Project, Detail, I2)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#1#', A.ProjectNo) as ItemID, CONCAT('A#', SC.CateNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2001')
            AND A.AccountNo = '091'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            $whereDebt
            GROUP BY SC.CateNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I2 = I
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C9($wherePeriodDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, Project, Detail, I4)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#1#', A.ProjectNo) as ItemID, CONCAT('A#', SC.CateNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.InTransTypeID = 6
            AND A.AccountNo IN ('0441','0442')
            $wherePeriodDate
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY SC.CateNo, A.ProjectNo
            )  as DT ON DUPLICATE KEY UPDATE I4 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C10($wherePeriodDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, Project, Detail, I4)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#1#', A.ProjectNo) as ItemID, CONCAT('A#', SC.CateNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.isDebtTrans = 1
            AND A.AccountNo IN ('0441','0442')
            $wherePeriodDate
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY SC.CateNo, A.ProjectNo
            ) as DT ON DUPLICATE KEY UPDATE I5 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C11_13_19($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList , ProjectCateTemporary, Project, Detail, I6, I8, I14)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo), ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, 1 as ProjectCateTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN AccountNo IN ('0461','0462') AND YearD = $year - 1 THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN AccountNo IN ('0441','0442') AND YearD = $year THEN I ELSE 0 END) as A2,
            SUM(CASE WHEN AccountNo IN ('0721','0722') AND YearD = $year THEN I ELSE 0 END) as A3 FROM
            (SELECT CONCAT('A#', SC.CateNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo as SectorCateList, A.ProjectNo,
            A.AccountNo, Year(PeriodFromDate) as YearD, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo IN ('0461','0462','0441','0442','0721','0722')
            AND (LEFT(CC.CateNo,4) = '2001' OR LEFT(CC.CateNo,4) = '2002')
            $whereCondition
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY SC.CateNo, A.ProjectNo, A.AccountNo, YearD
            ) as CH GROUP BY SectorCateList, ProjectNo) as DT ON DUPLICATE KEY UPDATE I6 = A1, I8 = A2, I14 = A3";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C12_14_20($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, Project, Detail, I7, I9, I15)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo), ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, 1 as ProjectCateTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN AccountNo = '0461' AND YearD = $year - 1 THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN AccountNo = '0441' AND YearD = $year THEN I ELSE 0 END) as A2,
            SUM(CASE WHEN AccountNo = '0721' AND YearD = $year THEN I ELSE 0 END) as A3 FROM
            (SELECT CONCAT('A#', SC.CateNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo as SectorCateList, A.ProjectNo,
            A.AccountNo, Year(PeriodFromDate) as YearD, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo IN ('0461','0441','0721')
            AND LEFT(CC.CateNo,4) = '2001'
            $whereCondition
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY SC.CateNo, A.ProjectNo, A.AccountNo, YearD
            ) as CH GROUP BY SectorCateList, ProjectNo) as DT ON DUPLICATE KEY UPDATE I7 = A1, I9 = A2, I15 = A3";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C15($whereYear){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, Project, Detail, I10)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#1#', A.ProjectNo) as ItemID, CONCAT('A#', SC.CateNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo IN ('0441','0442')
            AND (LEFT(CC.CateNo,4) = '2001' OR LEFT(CC.CateNo,4) = '2002')
            AND A.FiscalPeriod = 1
            $whereYear
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY SC.CateNo, A.ProjectNo
            ) as DT ON DUPLICATE KEY UPDATE I10 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C16($whereYear){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, Project, Detail, I11)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#1#', A.ProjectNo) as ItemID, CONCAT('A#', SC.CateNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo = '0441'
            AND LEFT(CC.CateNo,4) = '2001'
            AND A.FiscalPeriod = 1
            $whereYear
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY SC.CateNo, A.ProjectNo
            ) as DT ON DUPLICATE KEY UPDATE I11 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C17($wherePeriodDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, Project, Detail, I12)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#1#', A.ProjectNo) as ItemID, CONCAT('A#', SC.CateNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo IN ('008','009','004','006','012','013','014')
            AND (LEFT(CC.CateNo,4) = '2001' OR LEFT(CC.CateNo,4) = '2002')
            $wherePeriodDate
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY SC.CateNo, A.ProjectNo
            ) as DT ON DUPLICATE KEY UPDATE I12 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C18($wherePeriodDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, Project, Detail, I13)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#1#', A.ProjectNo) as ItemID, CONCAT('A#', SC.CateNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo IN ('008','009','004','006','012','013','014')
            AND LEFT(CC.CateNo,4) = '2001'
            $wherePeriodDate
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY SC.CateNo, A.ProjectNo
            ) as DT ON DUPLICATE KEY UPDATE I13 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C24($whereNextYear){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, Project, Detail, I19)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#1#', A.ProjectNo) as ItemID, CONCAT('A#', SC.CateNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.InTransTypeID = 6
            AND A.AccountNo IN ('0411','0412')
            $whereNextYear
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY SC.CateNo, A.ProjectNo
            )  as DT ON DUPLICATE KEY UPDATE I19 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C25($whereNextYear){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, Project, Detail, I20)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#1#', A.ProjectNo) as ItemID, CONCAT('A#', SC.CateNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.isDebtTrans = 1
            AND A.AccountNo IN ('0411','0412')
            $whereNextYear
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY SC.CateNo, A.ProjectNo
            ) as DT ON DUPLICATE KEY UPDATE I20 = I";

        DB::statement($sql);
    }


    // Thực hiện dự án

    public function setCaseProjectPhase($year){
        return " CASE
            WHEN YEAR(ExpectedFinishDate) = $year AND YEAR(ExpectedHandoverDate) = $year THEN '01'
            WHEN YEAR(ExpectedFinishDate) = $year + 1 THEN '02'
            WHEN YEAR(ExpectedFinishDate) > $year + 1 THEN '03'
            WHEN YEAR(ExpectedStartDate) = $year + 1 THEN '04' END";
    }

    public function insertR2C6($whereDebt, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I1)
            SELECT CONCAT('A#', SC.CateNo, '#2#'," .  $this->setCaseProjectPhase($year) . ", '#',
            P.Group, '#', A.ProjectNo) as ItemID, CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 2 as ProjectCateTemporary, " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary ,
            P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail, SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2001' OR LEFT(CC.CateNo,4) = '2002')
            AND A.AccountNo = '091'
            $whereDebt
            $whereR2
            GROUP BY SC.CateNo, A.ProjectNo
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C7($whereDebt, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I2)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#2#', " .  $this->setCaseProjectPhase($year) . ", '#',
            P.Group, '#' , A.ProjectNo) as ItemID,
            CONCAT('A#', SC.CateNo, '#2#', " .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 2 as ProjectCateTemporary, " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary ,
            P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail, SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2001')
            AND A.AccountNo = '091'
            $whereDebt
            $whereR2
            GROUP BY SC.CateNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I2 = I
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C9($wherePeriodDate, $whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I4)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#',
            P.Group, '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SC.CateNo, '#2#', " .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 2 as ProjectCateTemporary, " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary,
            P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE A.InTransTypeID = 6
            AND A.AccountNo IN ('0441','0442')
            $wherePeriodDate
            $whereR2
            GROUP BY SC.CateNo, A.ProjectNo
            )  as DT ON DUPLICATE KEY UPDATE I4 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C10($wherePeriodDate, $whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I4)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#',
            P.Group, '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 2 as ProjectCateTemporary, " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary,
            P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE A.isDebtTrans = 1
            AND A.AccountNo IN ('0441','0442')
            $wherePeriodDate
            $whereR2
            GROUP BY SC.CateNo, A.ProjectNo
            ) as DT ON DUPLICATE KEY UPDATE I5 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C11_13_19($whereCondition, $whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList , ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I6, I8, I14)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo), ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, 2 as ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN AccountNo IN ('0461','0462') AND YearD = $year - 1 THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN AccountNo IN ('0441','0442') AND YearD = $year THEN I ELSE 0 END) as A2,
            SUM(CASE WHEN AccountNo IN ('0721','0722') AND YearD = $year THEN I ELSE 0 END) as A3 FROM
            (SELECT CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo as SectorCateList, " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary,
            P.Group as ProjectGroupTemporary, A.ProjectNo,
            A.AccountNo, Year(PeriodFromDate) as YearD, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo IN ('0461','0462','0441','0442','0721','0722')
            AND (LEFT(CC.CateNo,4) = '2001' OR LEFT(CC.CateNo,4) = '2002')
            $whereCondition
            $whereR2
            GROUP BY SC.CateNo, A.ProjectNo, A.AccountNo, YearD
            ) as CH GROUP BY SectorCateList, ProjectNo) as DT ON DUPLICATE KEY UPDATE I6 = A1, I8 = A2, I14 = A3";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C12_14_20($whereCondition, $whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I7, I9, I15)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo), ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, 2 as ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN AccountNo = '0461' AND YearD = $year - 1 THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN AccountNo = '0441' AND YearD = $year THEN I ELSE 0 END) as A2,
            SUM(CASE WHEN AccountNo = '0721' AND YearD = $year THEN I ELSE 0 END) as A3 FROM
            (SELECT CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo as SectorCateList, " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary,
            P.Group as ProjectGroupTemporary, A.ProjectNo,
            A.AccountNo, Year(PeriodFromDate) as YearD, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo IN ('0461','0441','0721')
            AND LEFT(CC.CateNo,4) = '2001'
            $whereCondition
            $whereR2
            GROUP BY SC.CateNo, A.ProjectNo, A.AccountNo, YearD
            ) as CH GROUP BY SectorCateList, ProjectNo) as DT ON DUPLICATE KEY UPDATE I7 = A1, I9 = A2, I15 = A3";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C15($whereYear, $whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I10)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#',
            P.Group, '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 2 as ProjectCateTemporary, " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary,
            P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo IN ('0441','0442')
            AND (LEFT(CC.CateNo,4) = '2001' OR LEFT(CC.CateNo,4) = '2002')
            AND A.FiscalPeriod = 1
            $whereYear
            $whereR2
            GROUP BY SC.CateNo, A.ProjectNo
            ) as DT ON DUPLICATE KEY UPDATE I10 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C16($whereYear, $whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I11)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#',
            P.Group, '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 2 as ProjectCateTemporary, " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary,
            P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo = '0441'
            AND LEFT(CC.CateNo,4) = '2001'
            AND A.FiscalPeriod = 1
            $whereYear
            $whereR2
            GROUP BY SC.CateNo, A.ProjectNo
            ) as DT ON DUPLICATE KEY UPDATE I11 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C17($wherePeriodDate, $whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I12)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#',
            P.Group, '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 2 as ProjectCateTemporary, " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary,
            P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo IN ('008','009','004','006','012','013','014')
            AND (LEFT(CC.CateNo,4) = '2001' OR LEFT(CC.CateNo,4) = '2002')
            $wherePeriodDate
            $whereR2
            GROUP BY SC.CateNo, A.ProjectNo
            ) as DT ON DUPLICATE KEY UPDATE I12 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C18($wherePeriodDate, $whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I13)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#',
            P.Group, '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 2 as ProjectCateTemporary, " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary,
            P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo IN ('008','009','004','006','012','013','014')
            AND LEFT(CC.CateNo,4) = '2001'
            $wherePeriodDate
            $whereR2
            GROUP BY SC.CateNo, A.ProjectNo
            ) as DT ON DUPLICATE KEY UPDATE I13 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C24($whereNextYear, $whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I19)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#',
            P.Group, '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 2 as ProjectCateTemporary, " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary,
            P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE A.InTransTypeID = 6
            AND A.AccountNo IN ('0411','0412')
            $whereNextYear
            $whereR2
            GROUP BY SC.CateNo, A.ProjectNo
            )  as DT ON DUPLICATE KEY UPDATE I19 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C25($whereNextYear, $whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            SectorCateList, ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I20)
            SELECT * FROM
            (SELECT CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#',
            P.Group, '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SC.CateNo, '#2#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo, 2 as ProjectCateTemporary, " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary,
            P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE A.isDebtTrans = 1
            AND A.AccountNo IN ('0411','0412')
            $whereNextYear
            $whereR2
            GROUP BY SC.CateNo, A.ProjectNo
            ) as DT ON DUPLICATE KEY UPDATE I20 = I";

        DB::statement($sql);
    }

    // sum row
    public function sumUpRow(){
        $listItems = DB::table('table_tmp')->where('Detail','=',1)->get();
        foreach($listItems as $item){
            $I3 = $item->I4 + $item->I5;
            $I18 = $item->I19 + $item->I20;
            DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update([
                'I3'=> $I3,  'I18'=> $I18,
            ]);
        }
    }

    // sum row
    public function sumUpTableTmp($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
           SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8,
           SUM(M.I9) AS I9, SUM(M.I10) AS I10, SUM(M.I11) AS I11, SUM(M.I12) AS I12,
           SUM(M.I13) AS I13, SUM(M.I14) AS I14, SUM(M.I15) AS I15, SUM(M.I16) AS I16,
           SUM(M.I17) AS I17, SUM(M.I18) AS I18, SUM(M.I19) AS I19, SUM(M.I20) AS I20
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
                    $I17 = ($value['I17']) ? $value['I17'] : 0;
                    $I18 = ($value['I18']) ? $value['I18'] : 0;
                    $I19 = ($value['I19']) ? $value['I19'] : 0;
                    $I20 = ($value['I20']) ? $value['I20'] : 0;
                    $update = "UPDATE table_tmp
                SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ",
                I9 = " . $I9 . ", I10 = " . $I10 . ", I11 = " . $I11 . ", I12 = " . $I12 . ",
                I13 = " . $I13 . ", I14 = " . $I14 . ", I15 = " . $I15 . ", I16 = " . $I16 . ",
                I17 = " . $I17 . ", I18 = " . $I18 . ", I19 = " . $I19 . ", I20 = " . $I20 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }

    }

    public function setIndexMB23($maxLevel){
        $insertArr = [];
        for($i = 1; $i <= $maxLevel ; $i++){
            $parentID = [];
            if($i == 1){
                $sql = "SELECT * FROM table_tmp WHERE Level = " . $i . " ORDER BY ItemID ASC";
                $result = DB::select($sql);
                foreach($result as $key=> $item) {
                    array_push($parentID, $item->ItemID);
                    DB::table('table_tmp')->where('ItemID', '=', $item->ItemID)->update(['STT' => $this->numberToRomanRepresentation($key + 1)]);
                }
            }
            else if($i == 2){
                if(!empty($listParentID)){
                    foreach($listParentID as $value){
                        $stt = DB::table('table_tmp')->where('ItemID',$value)->first()->STT;
                        $sql = "SELECT * FROM table_tmp WHERE Level = " . $i . " AND ParentID = '" . $value . "' ORDER BY ItemID ASC";
                        $result = DB::select($sql);
                        foreach ($result as $key => $item){
                            if($value == $item->ParentID){
                                array_push($parentID, $item->ItemID);
                                DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=>$stt. '.'. ($key + 1)]);
                            }
                        }
                    }
                }
            }
            else if($i == 3){
                if(!empty($listParentID)){
                    foreach($listParentID as $value){
                        $sql1 = "SELECT * FROM table_tmp WHERE Level = " . $i . " AND Detail = 1 AND ParentID = '" . $value . "' ORDER BY ItemID ASC";
                        $result1 = DB::select($sql1);
                        if($result1){
                            foreach ($result1 as $key => $item){
                                if($value == $item->ParentID){
                                    DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=> ($key + 1)]);
                                }
                            }
                        }
                        else{
                            $sql2 = "SELECT * FROM table_tmp WHERE Level = " . $i . " AND Detail = 0 AND ParentID = '" . $value . "' ORDER BY ItemID ASC";
                            $result2 = DB::select($sql2);
                            foreach ($result2 as $key => $item){
                                if($value == $item->ParentID){
                                    array_push($parentID, $item->ItemID);
                                    DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=> -($key + 1)]);
                                }
                            }
                        }
                    }
                }
            }
            else if($i == 4){
                if(!empty($listParentID)){
                    foreach($listParentID as $value){
                        $sql = "SELECT * FROM table_tmp WHERE Level = " . $i . " AND ParentID = '" . $value . "' ORDER BY ItemID ASC";
                        $result = DB::select($sql);
                        foreach ($result as $key => $item){
                            if($value == $item->ParentID){
                                array_push($parentID, $item->ItemID);
                                DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=> base_convert($key+ 10, 10, 36)]);
                            }
                        }
                    }
                }
            }
            else{
                if(!empty($listParentID)){
                    foreach($listParentID as $value){
                        $sql = "SELECT * FROM table_tmp WHERE Level = " . $i . " AND ParentID = '" . $value . "' ORDER BY ItemID ASC";
                        $result = DB::select($sql);
                        foreach ($result as $key => $item){
                            if($value == $item->ParentID){
                                array_push($parentID, $item->ItemID);
                                DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=> ($key + 1)]);
                            }
                        }
                    }
                }
            }
            $listParentID = array_unique($parentID);
        }

    }


}
