<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB25Controller extends Controller {
    use Common;

    public function SBP3422016TTBTC_MB25(Request $request){
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

        $whereCommonCondition = "AND A.InTransTypeID NOT IN (16,17,18) AND A.BudgetAllocTypeID = '1' AND PC.CateNo = '021' AND PC.CateValue IN ('1','2')";
        $currentDate = date('Y-m-d');
        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year - 1 AND $year $whereCommonCondition";
        $whereDebt = " AND A.PeriodFromDate <= '". $currentDate ."' $whereCommonCondition ";
        $wherePeriodDate = " AND A.PeriodType = 9 AND YEAR(A.PeriodFromDate) >= $year AND YEAR(A.PeriodToDate) <= ($year + 5) $whereCommonCondition";
        $whereYear = " AND YEAR(A.PeriodFromDate) = $year $whereCommonCondition ";
        $wherePeriodDate1 = " AND A.PeriodFromDate BETWEEN '" . $year . "-01-01' AND '" . $year . "-06-30' ";
        $whereNextYear = " AND YEAR(A.PeriodFromDate) = $year + 1 $whereCommonCondition ";



        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(50) DEFAULT '',
          `ItemID` varchar(255) DEFAULT '',
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
          `I41` double(20,4) DEFAULT NULL,
          `I42` double(20,4) DEFAULT NULL,
          `I43` double(20,4) DEFAULT NULL,
          `I44` double(20,4) DEFAULT NULL,
            `program` varchar(50) DEFAULT '',
            `Project` varchar(50) DEFAULT '',
            `BuildAddress` varchar(255) DEFAULT '',
            `CapableDesign` varchar(255) DEFAULT '',
            `PerformDate` varchar(255) DEFAULT '',
            `InvestdocNo` varchar(255) DEFAULT '',
            `SponsorName` varchar(255) DEFAULT '',
            `PacttdocDate` varchar(255) DEFAULT '',
            `SectorCateList` varchar(50) DEFAULT '',
            `ProjectCateTemporary` varchar(50) DEFAULT '',
            `ProjectPhaseTemporary` varchar(20) DEFAULT '',
            `ProjectGroupTemporary` varchar(20) DEFAULT '',
            `Detail` int(11) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $this->createTemporaryProjectCate();
        $this->createTemporaryProjectPhase($year);
        $this->createTemporaryProjectGroup();

        $countRecords = DB::table('table_tmp')->count();

        $whereR2 = " AND ((YEAR(P.ExpectedFinishDate) = $year AND YEAR(P.ExpectedHandoverDate) = $year) OR YEAR(P.ExpectedFinishDate) >= $year + 1 OR YEAR(P.ExpectedStartDate) = $year + 1)";
        $this->insertC7_8($whereDebt, $whereR2, $year);
        $this->insertOrUpdateC9($whereDebt, $whereR2, $year);
        $this->insertOrUpdateC12_13($wherePeriodDate, $whereR2, $year);
        $this->insertOrUpdateC14($wherePeriodDate, $whereR2, $year);
        $this->insertOrUpdateC17_18_22_23_27_28_37_38_42_43($whereCondition, $whereR2, $year);
        $this->insertOrUpdateC19_24_29_39_44($whereCondition, $whereR2, $year);

        $this->insertOrUpdateC32_33($wherePeriodDate1, $whereR2, $year);
        $this->insertOrUpdateC34($wherePeriodDate1, $whereR2, $year);

        if(DB::table('table_tmp')->count() > $countRecords){
            $this->getParentDataTreeCommon([['ProjectCateTemporary','1'],'program', 'ProjectPhaseTemporary', 'ProjectGroupTemporary','project'],'A#1');
            $this->getParentDataTreeCommon([['ProjectCateTemporary','2'],'program', 'ProjectPhaseTemporary', 'ProjectGroupTemporary','project'],'A#2');
            $this->getParentDataTreeCommon([['ProjectCateTemporary','3'],'program', 'ProjectPhaseTemporary', 'ProjectGroupTemporary','project'],'A#3');
            $this->getParentDataTreeCommon([['ProjectCateTemporary','4'],'program', 'ProjectPhaseTemporary', 'ProjectGroupTemporary','project'],'A#4');}

        $this->setLevel();

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumUpTableTmp($maxLevel);

        // sum Row
//        $this->sumUpRow();
        $insertArr1 = [
            ['STT' => 'A', 'ItemID' => 'A', 'ParentID' => '', 'Level' => 0, 'ItemName' => 'TỔNG SỐ', 'FontWeight' => '1', 'Detail' => '0'],
//            ['STT' => 'I', 'ItemID' => 'A#01', 'ParentID' => 'A', 'Level' => 1, 'ItemName' => 'Chương trình mục tiêu', 'FontWeight' => '1', 'Detail' => '0'],
//            ['STT' => 'II', 'ItemID' => 'A#02', 'ParentID' => 'A', 'Level' => 1, 'ItemName' => 'Dự án', 'FontWeight' => '1', 'Detail' => '0'],
//            ['STT' => 'III', 'ItemID' => 'A#03', 'ParentID' => 'A', 'Level' => 1, 'ItemName' => 'Chương trình', 'FontWeight' => '1', 'Detail' => '0'],
//            ['STT' => 'IV', 'ItemID' => 'A#04', 'ParentID' => 'A', 'Level' => 1, 'ItemName' => 'Các chương trình, dự án khác giải ngân theo cơ chế tài chính trong nước', 'FontWeight' => '1', 'Detail' => '0'],

        ];

        DB::table('table_tmp')->insert($insertArr1);

        // sum up
//        $this->sumUpTableTmp(1, 4);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID','ASC')->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $yearPre;
            $data[0]->HeaderName2 = 'Kế hoạch năm ' . $year;
            $data[0]->HeaderName3 = 'Số vốn kéo dài các năm trước sang năm ' .$year;
            $data[0]->HeaderName4 = 'Giải ngân từ 1/1/năm ' . $year;
            $data[0]->HeaderName5 = 'Ước thực hiện năm ' . $year;
            $data[0]->HeaderName6 = 'Dự kiến kế hoạch đầu tư phát triển nguồn NSNN năm ' .$yearNext;
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
                "ReportName" => "DỰ TOÁN CHI ĐẦU TƯ TỪ NGUỒN VỐN ODA VÀ VỐN VAY ƯU ĐÃI THEO PHƯƠNG THỨC CẤP PHÁT (GIẢI NGÂN THEO CƠ CHẾ TÀI CHÍNH TRONG NƯỚC) NĂM " . $year,
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
            ['ItemID'=> '1','ItemName'=> 'Chương trình mục tiêu'],
            ['ItemID'=> '2','ItemName'=> 'Dự án'],
            ['ItemID'=> '3','ItemName'=> 'Chương trình'],
            ['ItemID'=> '4','ItemName'=> 'Các chương trình, dự án khác giải ngân theo cơ chế tài chính trong nước'],
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
            ['ItemID'=> 'A','ItemName'=> 'Dự án nhóm A'],
            ['ItemID'=> 'B','ItemName'=> 'Dự án nhóm B'],
            ['ItemID'=> 'C','ItemName'=> 'Dự án nhóm C'],
        ];

        DB::table('ProjectGroupTemporary')->insert($insertA);
    }


    // Chuẩn bị dự án
    public function setCaseProjectPhase($year){
        return " CASE
            WHEN YEAR(ExpectedFinishDate) = $year AND YEAR(ExpectedHandoverDate) = $year THEN '01'
            WHEN YEAR(ExpectedFinishDate) = $year + 1 THEN '02'
            WHEN YEAR(ExpectedFinishDate) > $year + 1 THEN '03'
            WHEN YEAR(ExpectedStartDate) = $year + 1 THEN '04' END";
    }

    public function selectCommon(){
        return " P.BuildAddress, P.CapableDesign, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as PerformDate, P.InvestdocNo ";
    }

    public function insertC7_8($whereDebt, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I7, I8)
           SELECT  CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            MAX(CASE WHEN  BudgetLevel = 1 THEN I ELSE 0 END) as I7,
            MAX(CASE WHEN  BudgetLevel IN ('2','3','4') THEN I ELSE 0 END) as I8 FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PGC.CateValue as ProjectCateTemporary, PG.ProgramNo,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo as ProjectNo,
            A.BudgetLevel,PG.ProgramNo as program, SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE  A.AccountNo = '091'  AND A.BudgetLevel IN ('1','2','3','4') AND  C.CapitalInOut = 1
            AND PC.CateNo = '015'
            $whereDebt
            $whereR2
            GROUP BY A.BudgetLevel,PG.ProgramNo, A.ProjectNo,PC.CateValue) as CH GROUP BY program, ProjectNo";
            DB::statement($sql);

    }

    public function insertOrUpdateC9($whereDebt, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I9 )
           SELECT * FROM
            (SELECT  CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
              SUM(I) as A9  FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PGC.CateValue as ProjectCateTemporary, PG.ProgramNo,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo as ProjectNo,
            A.BudgetLevel,PG.ProgramNo as program, SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE  A.AccountNo = '091' AND CC.CateNo IN ('20012201','20012202')  AND  C.CapitalInOut = 2
            AND PC.CateNo = '015' AND PC.CateValue IN ('1','2','3','4')
             $whereDebt
            $whereR2
            GROUP BY A.BudgetLevel,PG.ProgramNo, A.ProjectNo,PC.CateValue) as CH GROUP BY program, ProjectNo)
              as DT ON DUPLICATE KEY UPDATE I9 = A9";
        DB::statement($sql);
    }
    public function insertOrUpdateC12_13($wherePeriodDate, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I12, I13 )
           SELECT * FROM
            (SELECT  CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            MAX(CASE WHEN  BudgetLevel = 1 THEN I ELSE 0 END) as A12,
            MAX(CASE WHEN  BudgetLevel IN ('2','3','4') THEN I ELSE 0 END) as A13 FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PGC.CateValue as ProjectCateTemporary, PG.ProgramNo,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo as ProjectNo,
            A.BudgetLevel,PG.ProgramNo as program, SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE  A.AccountNo IN ('0441','0442') AND  A.BudgetLevel IN ('1','2','3','4') AND  C.CapitalInOut = 1
            AND PC.CateNo = '015' AND PC.CateValue IN ('1','2','3','4')
             $wherePeriodDate
            $whereR2
            GROUP BY A.BudgetLevel,PG.ProgramNo, A.ProjectNo,PC.CateValue) as CH GROUP BY program, ProjectNo)
              as DT ON DUPLICATE KEY UPDATE I12 = A12, I13=A13 ";
        DB::statement($sql);
    }
    public function insertOrUpdateC14($wherePeriodDate, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I14 )
           SELECT * FROM
            (SELECT  CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
              SUM(I) as A14  FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PGC.CateValue as ProjectCateTemporary, PG.ProgramNo,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo as ProjectNo,
            A.BudgetLevel,PG.ProgramNo as program, SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE  A.AccountNo = '091' AND CC.CateNo IN ('20012201','20012202')  AND  C.CapitalInOut = 2
            AND PC.CateNo = '015' AND PC.CateValue IN ('1','2','3','4')
             $wherePeriodDate
            $whereR2
            GROUP BY A.BudgetLevel,PG.ProgramNo, A.ProjectNo,PC.CateValue) as CH GROUP BY program, ProjectNo)
              as DT ON DUPLICATE KEY UPDATE I14 = A14";
        DB::statement($sql);
    }

    public function insertOrUpdateC17_18_22_23_27_28_37_38_42_43($whereCondition, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I17, I18, I22,I23, I27, I28,  I37, I38, I42,I43 )
           SELECT * FROM
            (SELECT  CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            MAX(CASE WHEN  AccountNo IN ('0461','0462') AND YearD = $year - 1 AND BudgetLevel = 1 THEN I ELSE 0 END) as A17,
            MAX(CASE WHEN  AccountNo IN ('0461','0462') AND YearD = $year - 1 AND  BudgetLevel IN ('2','3','4') THEN I ELSE 0 END) as A18,
            MAX(CASE WHEN  AccountNo IN ('0441','0442') AND YearD = $year AND FiscalPeriod = 2 AND BudgetLevel = 1 THEN I ELSE 0 END) as A22,
            MAX(CASE WHEN  AccountNo IN ('0441','0442') AND YearD = $year AND FiscalPeriod = 2 AND BudgetLevel IN ('2','3','4') THEN I ELSE 0 END) as A23,
            MAX(CASE WHEN  AccountNo IN ('0441','0442') AND YearD = $year AND FiscalPeriod = 1 AND BudgetLevel = 1 THEN I ELSE 0 END) as A27,
            MAX(CASE WHEN  AccountNo IN ('0441','0442') AND YearD = $year AND  FiscalPeriod = 1 AND BudgetLevel IN ('2','3','4') THEN I ELSE 0 END) as A28,
            MAX(CASE WHEN  AccountNo = '0721' AND YearD = $year AND FiscalPeriod in('1','2') AND BudgetLevel = 1 THEN I ELSE 0 END) as A37,
            MAX(CASE WHEN  AccountNo = '0721' AND YearD = $year AND FiscalPeriod in('1','2') AND BudgetLevel IN ('2','3','4') THEN I ELSE 0 END) as A38,
            MAX(CASE WHEN  AccountNo IN ('0411','0412') AND YearD = $year + 1 AND BudgetLevel = 1 THEN I ELSE 0 END) as A42,
            MAX(CASE WHEN  AccountNo IN ('0411','0412') AND YearD = $year + 1 AND BudgetLevel IN ('2','3','4') THEN I ELSE 0 END) as A43 FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PGC.CateValue as ProjectCateTemporary, PG.ProgramNo,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo as ProjectNo,
            A.BudgetLevel, A.AccountNo , A.FiscalPeriod, PG.ProgramNo as program,  SUM(LCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE  A.AccountNo IN ('0461','0462','0441','0442','0721','0411','0412') AND  A.BudgetLevel IN ('1','2','3','4')
            AND A.FiscalPeriod IN('1','2') AND  C.CapitalInOut = 1
            AND PC.CateNo = '015' AND PC.CateValue IN ('1','2','3','4')
             $whereCondition
            $whereR2
            GROUP BY A.BudgetLevel,PG.ProgramNo, A.ProjectNo,PC.CateValue) as CH GROUP BY program, ProjectNo)
            as DT ON DUPLICATE KEY UPDATE I17=A17, I18=A18, I22=A22, I23=A23, I27=A27, I28=A28,  I37=A37, I38=A38, I42=A42, I43=A43 ";
        DB::statement($sql);
    }
    public function insertOrUpdateC19_24_29_39_44($whereCondition, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I19, I24, I29, I39,I44)
           SELECT * FROM
            (SELECT  CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            MAX(CASE WHEN  AccountNo IN ('0461','0462') AND YearD = $year - 1  THEN I ELSE 0 END) as A19,
            MAX(CASE WHEN  AccountNo IN ('0441','0442') AND YearD = $year AND FiscalPeriod = 2  THEN I ELSE 0 END) as A24,
            MAX(CASE WHEN  AccountNo IN ('0441','0442') AND YearD = $year AND FiscalPeriod = 1 THEN I ELSE 0 END) as A29,
            MAX(CASE WHEN  AccountNo = '0721' AND YearD = $year AND FiscalPeriod in('1','2') THEN I ELSE 0 END) as A39,
            MAX(CASE WHEN  AccountNo IN ('0411','0412') AND YearD = $year + 1 THEN I ELSE 0 END) as A44 FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PGC.CateValue as ProjectCateTemporary, PG.ProgramNo,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo as ProjectNo,
            A.BudgetLevel,PG.ProgramNo as program, SUM(LCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD, A.AccountNo , A.FiscalPeriod
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE   A.AccountNo IN ('0721','0722') AND CC.CateNo IN ('20012201','20012202')  AND  C.CapitalInOut = 2
            AND PC.CateNo = '015' AND PC.CateValue IN ('1','2','3','4')
             $whereCondition
            $whereR2
            GROUP BY A.BudgetLevel,PG.ProgramNo, A.ProjectNo,PC.CateValue) as CH GROUP BY program, ProjectNo)
              as DT ON DUPLICATE KEY UPDATE I19 = A19, I24=A24 , I29=A29, I39=A39,I44=A44";
        DB::statement($sql);
    }
    public function insertOrUpdateC32_33($wherePeriodDate1, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I32, I33 )
           SELECT * FROM
            (SELECT  CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            MAX(CASE WHEN  BudgetLevel = 1 THEN I ELSE 0 END) as A32,
            MAX(CASE WHEN  BudgetLevel IN ('2','3','4') THEN I ELSE 0 END) as A33 FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PGC.CateValue as ProjectCateTemporary, PG.ProgramNo,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo as ProjectNo,
            A.BudgetLevel,PG.ProgramNo as program, SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE  A.AccountNo IN ('0721','0722') AND  A.BudgetLevel IN ('1','2','3','4') AND  C.CapitalInOut = 1
            AND PC.CateNo = '015' AND PC.CateValue IN ('1','2','3','4') AND FiscalPeriod in('1','2')
             $wherePeriodDate1
            $whereR2
            GROUP BY A.BudgetLevel,PG.ProgramNo, A.ProjectNo,PC.CateValue) as CH GROUP BY program, ProjectNo)
              as DT ON DUPLICATE KEY UPDATE I32 = A32, I33=A33 ";
        DB::statement($sql);
    }
    public function insertOrUpdateC34($wherePeriodDate1, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I34 )
           SELECT * FROM
            (SELECT  CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            ProjectCateTemporary, program, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
              SUM(I) as A34  FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PGC.CateValue as ProjectCateTemporary, PG.ProgramNo,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo as ProjectNo,
            A.BudgetLevel,PG.ProgramNo as program, SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE   A.AccountNo IN ('0721','0722') AND CC.CateNo IN ('20012201','20012202')  AND  C.CapitalInOut = 2
            AND PC.CateNo = '015' AND PC.CateValue IN ('1','2','3','4')
             $wherePeriodDate1
            $whereR2
            GROUP BY A.BudgetLevel,PG.ProgramNo, A.ProjectNo,PC.CateValue) as CH GROUP BY program, ProjectNo)
              as DT ON DUPLICATE KEY UPDATE I34 = A34";
        DB::statement($sql);
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
                $sql = "SELECT ItemID, ParentID,  SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8,SUM(M.I9) AS I9,
             SUM(M.I10) AS I10, SUM(M.I11) AS I11, SUM(M.I12) AS I12,SUM(M.I13) AS I13, SUM(M.I14) AS I14, SUM(M.I15) AS I15, SUM(M.I16) AS I16,SUM(M.I17) AS I17, SUM(M.I18) AS I18, SUM(M.I19) AS I19, SUM(M.I20) AS I20,
           SUM(M.I21) AS I21, SUM(M.I22) AS I22, SUM(M.I23) AS I23, SUM(M.I24) AS I24,SUM(M.I25) AS I25, SUM(M.I26) AS I26, SUM(M.I27) AS I27, SUM(M.I28) AS I28,
           SUM(M.I29) AS I29, SUM(M.I30) AS I30, SUM(M.I31) AS I31, SUM(M.I32) AS I32,SUM(M.I33) AS I33, SUM(M.I34) AS I34, SUM(M.I35) AS I35, SUM(M.I36) AS I36,
           SUM(M.I37) AS I37, SUM(M.I38) AS I38, SUM(M.I39) AS I39, SUM(M.I40) AS I40,SUM(M.I41) AS I41, SUM(M.I42) AS I42, SUM(M.I43) AS I43, SUM(M.I44) AS I44
            FROM table_tmp M
            WHERE M.Level = " . $i . " GROUP BY ParentID";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
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
                        $I21 = ($value['I21']) ? $value['I21'] : 0;
                        $I22 = ($value['I22']) ? $value['I22'] : 0;
                        $I23 = ($value['I23']) ? $value['I23'] : 0;
                        $I24 = ($value['I24']) ? $value['I24'] : 0;
                        $I25 = ($value['I25']) ? $value['I25'] : 0;
                        $I26 = ($value['I26']) ? $value['I26'] : 0;
                        $I27 = ($value['I27']) ? $value['I27'] : 0;
                        $I28 = ($value['I28']) ? $value['I28'] : 0;
                        $I29 = ($value['I29']) ? $value['I29'] : 0;
                        $I30 = ($value['I30']) ? $value['I30'] : 0;
                        $I31 = ($value['I31']) ? $value['I31'] : 0;
                        $I32 = ($value['I32']) ? $value['I32'] : 0;
                        $I33 = ($value['I33']) ? $value['I33'] : 0;
                        $I34 = ($value['I34']) ? $value['I34'] : 0;
                        $I35 = ($value['I35']) ? $value['I35'] : 0;
                        $I36 = ($value['I36']) ? $value['I36'] : 0;
                        $I37 = ($value['I37']) ? $value['I37'] : 0;
                        $I38 = ($value['I38']) ? $value['I38'] : 0;
                        $I39 = ($value['I39']) ? $value['I39'] : 0;
                        $I40 = ($value['I40']) ? $value['I40'] : 0;
                        $I41 = ($value['I41']) ? $value['I41'] : 0;
                        $I42 = ($value['I42']) ? $value['I42'] : 0;
                        $I43 = ($value['I43']) ? $value['I43'] : 0;
                        $I44 = ($value['I44']) ? $value['I44'] : 0;

                        $update = "UPDATE table_tmp
                    SET  I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ", I9 = " . $I9 . ", I10 = " . $I10 . ",
                    I11 = " . $I11 . ", I12 = " . $I12 . ", I13 = " . $I13 . ", I14 = " . $I14 . ", I15 = " . $I15 . ", I16 = " . $I16 . ",I17 = " . $I17 . ", I18 = " . $I18 . ", I19 = " . $I19 . ", I20 = " . $I20 . ",
                    I21 = " . $I21 . ", I22 = " . $I22 . ", I23 = " . $I23 . ", I24 = " . $I24 . ",I25 = " . $I25 . ", I26 = " . $I26 . ", I27 = " . $I27 . ", I28 = " . $I28 . ",
                    I29 = " . $I29 . ", I30 = " . $I30 . ",I31 = " . $I31 . ", I32 = " . $I32 . ", I33 = " . $I33 . ", I34 = " . $I34 . ",I35 = " . $I35 . ", I36 = " . $I36 . ", I37 = " . $I37 . ", I38 = " . $I38 . ",
                    I39 = " . $I39 . ", I40 = " . $I40 . ",I41 = " . $I41 . ", I42 = " . $I42 . ", I43 = " . $I43 . ", I44 = " . $I44 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
