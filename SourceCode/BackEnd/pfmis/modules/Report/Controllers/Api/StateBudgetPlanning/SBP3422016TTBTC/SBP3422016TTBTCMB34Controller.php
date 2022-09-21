<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB34Controller extends Controller {
    use Common;

    public function SBP3422016TTBTC_MB34(Request $request){
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

        $whereCommonCondition = "AND PC.CateNo = '021' AND PC.CateValue IN ('1','2') ";
        $currentDate = date('Y-m-d');
        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year - 1 AND $year $whereCommonCondition";
        $whereDebt = " AND A.PeriodFromDate <= '". $currentDate ."' $whereCommonCondition ";
        $wherePeriodDate = " AND A.PeriodType = 9 AND YEAR(A.PeriodFromDate) >= $year AND YEAR(A.PeriodToDate) <= ($year + 5) $whereCommonCondition";
        $wherePeriodDate1 = " AND A.PeriodFromDate = '" . $year . "-01-01' AND A.PeriodToDate = '" . $year . "-06-30' ";

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
            `Project` varchar(50) DEFAULT '',
            `BuildAddress` varchar(255) DEFAULT '',
            `CapableDesign` varchar(255) DEFAULT '',
            `PerformDate` varchar(255) DEFAULT '',
            `Investdoc` varchar(255) DEFAULT '',
            `SponsorName` varchar(255) DEFAULT '',
            `PacttdocDate` varchar(255) DEFAULT '',
            `SectorCateList` varchar(50) DEFAULT '',
            `ProjectPhaseTemporary` varchar(20) DEFAULT '',
            `ProjectGroupTemporary` varchar(20) DEFAULT '',
            `Detail` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        //calculator here

        $this->createTemporaryProjectPhase($year);
        $this->createTemporaryProjectGroup();

        $countRecords = DB::table('table_tmp')->count();

        $this->insertR2C9_10($whereDebt, $year);
        $this->insertOrUpdateR2C11_12_13($whereDebt, $year);
        $this->insertOrUpdateR2R15_20($wherePeriodDate, $year);
        $this->insertOrUpdateR2C16_17_18_19($wherePeriodDate, $year);
        $this->insertOrUpdateR2C22_25_27_30_37_40_42_45($whereCondition, $year);
        $this->insertOrUpdateR2C23_24_28_29_38_39_43_44($whereCondition, $year);
        $this->insertOrUpdateR2C32_35($wherePeriodDate1, $year);
        $this->insertOrUpdateR2C33_34($wherePeriodDate1, $year);

        if(DB::table('table_tmp')->count() > $countRecords){
            $this->getParentDataTreeCommon(['sector_cate_list', 'ProjectPhaseTemporary', 'ProjectGroupTemporary','project']);
        }

        $this->setLevel();

        //set STT
        $maxLevel = DB::table('table_tmp')->select(DB::raw('Max(Level) as maxLevel'))->first()->maxLevel;
        $this->setIndexMB34($maxLevel);

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
            $data[0]->HeaderName3 = 'Dự toán năm ' .$yearNext;
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
            $data[$key]->I14 = ($data[$key]->I14) ? $data[$key]->I14 : '';
            $data[$key]->I15 = ($data[$key]->I15) ? $data[$key]->I15 : '';
            $data[$key]->I16 = ($data[$key]->I16) ? $data[$key]->I16 : '';
            $data[$key]->I17 = ($data[$key]->I17) ? $data[$key]->I17 : '';
            $data[$key]->I18 = ($data[$key]->I18) ? $data[$key]->I18 : '';
            $data[$key]->I19 = ($data[$key]->I19) ? $data[$key]->I19 : '';
            $data[$key]->I20 = ($data[$key]->I20) ? $data[$key]->I20 : '';

            $data[$key]->I21 = ($data[$key]->I21) ? $data[$key]->I21 : '';
            $data[$key]->I22 = ($data[$key]->I22) ? $data[$key]->I22 : '';
            $data[$key]->I23 = ($data[$key]->I23) ? $data[$key]->I23 : '';
            $data[$key]->I24 = ($data[$key]->I24) ? $data[$key]->I24 : '';
            $data[$key]->I25 = ($data[$key]->I25) ? $data[$key]->I25 : '';
            $data[$key]->I26 = ($data[$key]->I26) ? $data[$key]->I26 : '';
            $data[$key]->I27 = ($data[$key]->I27) ? $data[$key]->I27 : '';
            $data[$key]->I28 = ($data[$key]->I28) ? $data[$key]->I28 : '';
            $data[$key]->I29 = ($data[$key]->I29) ? $data[$key]->I29 : '';
            $data[$key]->I30 = ($data[$key]->I30) ? $data[$key]->I30 : '';
            $data[$key]->I31 = ($data[$key]->I31) ? $data[$key]->I31 : '';
            $data[$key]->I32 = ($data[$key]->I32) ? $data[$key]->I32 : '';
            $data[$key]->I33 = ($data[$key]->I33) ? $data[$key]->I33 : '';
            $data[$key]->I34 = ($data[$key]->I34) ? $data[$key]->I34 : '';
            $data[$key]->I35 = ($data[$key]->I35) ? $data[$key]->I35 : '';
            $data[$key]->I36 = ($data[$key]->I36) ? $data[$key]->I36 : '';
            $data[$key]->I37 = ($data[$key]->I37) ? $data[$key]->I37 : '';
            $data[$key]->I38 = ($data[$key]->I38) ? $data[$key]->I38 : '';
            $data[$key]->I39 = ($data[$key]->I39) ? $data[$key]->I39 : '';
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
        return " P.BuildAddress, P.CapableDesign, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as PerformDate, P.InvestdocNo, P.SponsorName, DATE_FORMAT(P.PacttdocDate, '%d/%m/%Y') as PacttdocDate ";
    }

    public function setCaseProjectPhase($year){
        return " CASE
            WHEN YEAR(ExpectedFinishDate) = $year AND YEAR(ExpectedHandoverDate) = $year THEN '01'
            WHEN YEAR(ExpectedFinishDate) = $year + 1 THEN '02'
            WHEN YEAR(ExpectedFinishDate) > $year + 1 THEN '03'
            WHEN YEAR(ExpectedStartDate) = $year + 1 THEN '04' END";
    }

    public function insertR2C9_10($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, Investdoc, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I2, I3)
            SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            SUM(I) as I2,
            SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) as I3 FROM
            (SELECT CONCAT('A#', SC.CateNo, '#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo as SectorCateList,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo,
            A.BudgetLevel, SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo = '091'
            AND C.CapitalInOut = 1
            AND CC.CateNo = '2012' AND CC.CateValue IN ('1','2')
            $whereDebt
            GROUP BY A.BudgetLevel, SC.CateNo, A.ProjectNo) as CH GROUP BY SectorCateList, ProjectNo
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C11_12_13($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, Investdoc, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I4, I5, I6)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            SUM(II) as A1,
            SUM(I) as A2,
            SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) as A3 FROM
            (SELECT CONCAT('A#', SC.CateNo, '#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo as SectorCateList,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo,
            A.BudgetLevel, SUM(LCDebitAmount - LCCreditAmount) as I, SUM(FCDebitAmount - FCCreditAmount) as II
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo = '091'
            AND C.CapitalInOut = 2
            AND (CC.CateNo = '20012201' OR CC.CateNo = '20012202')
            AND A.CcyNo != '033'
            $whereDebt
            GROUP BY A.BudgetLevel, SC.CateNo, A.ProjectNo) as CH GROUP BY SectorCateList, ProjectNo)
            as DT ON DUPLICATE KEY UPDATE I4 = A1, I5 = A2, I6 = A3";

        DB::statement($sql);
    }

    public function insertOrUpdateR2R15_20($wherePeriodDate, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, Investdoc, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I8, I13)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN CapitalInOut = 1 AND CapitalCateNo = '2012' THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN CapitalInOut = 2 AND CapitalCateNo IN ('20012201', '20012202') THEN I ELSE 0 END) as A2 FROM
            (SELECT CONCAT('A#', SC.CateNo, '#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo as SectorCateList,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo,
            C.CapitalInOut, CC.CateNo as CapitalCateNo, SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut IN (1,2)
            AND A.AccountNo IN ('0441', '0442')
            AND ((CC.CateNo = '2012' AND CC.CateValue IN ('1','2')) OR CC.CateNo = '20012201' OR CC.CateNo = '20012202')
            $wherePeriodDate
            GROUP BY C.CapitalInOut, CC.CateNo, SC.CateNo, A.ProjectNo) as CH GROUP BY SectorCateList, ProjectNo)
            as DT ON DUPLICATE KEY UPDATE I8 = A1, I13 = A2";

        DB::statement($sql);

    }

    public function insertOrUpdateR2C16_17_18_19($wherePeriodDate, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, Investdoc, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I9, I10, I11, I12)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN CapitalCateNo = '2012' AND BudgetLevel = 1 THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN CapitalCateNo = '2012' AND BudgetLevel = 1 AND InTransTypeID = 6 THEN I ELSE 0 END) as A2,
            SUM(CASE WHEN CapitalCateNo IN ('2012', '20012102') THEN I ELSE 0 END) as A3,
            SUM(CASE WHEN CapitalCateNo IN ('2012', '20012102') AND InTransTypeID = 6 THEN I ELSE 0 END) as A4 FROM
            (SELECT CONCAT('A#', SC.CateNo, '#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo as SectorCateList,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo,
            CC.CateNo as CapitalCateNo, A.BudgetLevel, A.InTransTypeID, SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = 1
            AND A.AccountNo IN ('0441', '0442')
            AND ((CC.CateNo = '2012' AND CC.CateValue IN ('1','2')) OR (CC.CateNo = '20012102' AND CC.CateValue = '3'))
            $wherePeriodDate
            GROUP BY CC.CateNo, A.BudgetLevel, A.InTransTypeID, SC.CateNo, A.ProjectNo) as CH GROUP BY SectorCateList, ProjectNo)
            as DT ON DUPLICATE KEY UPDATE I9 = A1, I10 = A2, I11 = A3, I12 = A4";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C22_25_27_30_37_40_42_45($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, Investdoc, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I15, I18, I20, I23, I30, I33, I35, I38)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN AccountNo IN ('0461', '0462') AND CapitalCateNo = '2012' AND CapitalInOut = 1 AND YearD = $year - 1 THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN AccountNo IN ('0461', '0462') AND CapitalCateNo IN ('20012201','20012202') AND CapitalInOut = 2 AND YearD = $year - 1 THEN I ELSE 0 END) as A2,
            SUM(CASE WHEN AccountNo IN ('0441', '0442') AND CapitalCateNo = '2012' AND CapitalInOut = 1 AND FiscalPeriod = 2 AND YearD = $year THEN I ELSE 0 END) as A3,
            SUM(CASE WHEN AccountNo IN ('0441', '0442') AND CapitalCateNo IN ('20012201','20012202') AND FiscalPeriod = 2 AND CapitalInOut = 2 AND YearD = $year THEN I ELSE 0 END) as A4,
            SUM(CASE WHEN AccountNo IN ('0721', '0722') AND CapitalCateNo = '2012' AND CapitalInOut = 1 AND FiscalPeriod IN (1,2) AND YearD = $year THEN I ELSE 0 END) as A5,
            SUM(CASE WHEN AccountNo IN ('0721', '0722') AND CapitalCateNo IN ('20012201','20012202') AND CapitalInOut = 2 AND FiscalPeriod IN (1,2) AND YearD = $year THEN I ELSE 0 END) as A6,
            SUM(CASE WHEN AccountNo IN ('0411', '0412') AND CapitalCateNo = '2012' AND CapitalInOut = 1 AND YearD = $year + 1 THEN I ELSE 0 END) as A7,
            SUM(CASE WHEN AccountNo IN ('0411', '0412') AND CapitalCateNo IN ('20012201','20012202') AND CapitalInOut = 2 AND YearD = $year + 1 THEN I ELSE 0 END) as A8 FROM
            (SELECT CONCAT('A#', SC.CateNo, '#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo as SectorCateList,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo,
            A.AccountNo, A.FiscalPeriod, CC.CateNo as CapitalCateNo, C.CapitalInOut, SUM(LCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut IN (1,2)
            AND A.AccountNo IN ('0461', '0462','0441', '0442', '0721', '0722', '0411', '0412')
            AND ((CC.CateNo = '2012' AND CC.CateValue IN ('1','2')) OR CC.CateNo = '20012201' OR CC.CateNo = '20012202')
            $whereCondition
            GROUP BY A.AccountNo, A.FiscalPeriod, CC.CateNo, C.CapitalInOut, SC.CateNo, A.ProjectNo, YearD) as CH GROUP BY SectorCateList, ProjectNo)
            as DT ON DUPLICATE KEY UPDATE I15 = A1, I18 = A2, I20 = A3, I23 = A4, I30 = A5, I33 = A6, I35 = A7, I38 = A8";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C23_24_28_29_38_39_43_44($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, Investdoc, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I16, I17, I21, I22, I31, I32, I36, I37)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN AccountNo IN ('0461', '0462') AND CapitalCateNo = '2012' AND BudgetLevel = 1 AND YearD = $year - 1 THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN AccountNo IN ('0461', '0462') AND CapitalCateNo IN ('2012','20012102') AND YearD = $year - 1 THEN I ELSE 0 END) as A2,
            SUM(CASE WHEN AccountNo IN ('0441', '0442') AND CapitalCateNo = '2012' AND BudgetLevel = 1 AND FiscalPeriod = 2 AND YearD = $year THEN I ELSE 0 END) as A3,
            SUM(CASE WHEN AccountNo IN ('0441', '0442') AND CapitalCateNo IN ('2012','20012102') AND FiscalPeriod = 2 AND YearD = $year THEN I ELSE 0 END) as A4,
            SUM(CASE WHEN AccountNo IN ('0721', '0722') AND CapitalCateNo = '2012' AND BudgetLevel = 1 AND FiscalPeriod IN (1,2) AND YearD = $year THEN I ELSE 0 END) as A5,
            SUM(CASE WHEN AccountNo IN ('0721', '0722') AND CapitalCateNo IN ('2012','20012102') AND FiscalPeriod IN (1,2) AND YearD = $year THEN I ELSE 0 END) as A6,
            SUM(CASE WHEN AccountNo IN ('0411', '0412') AND CapitalCateNo = '2012' AND BudgetLevel = 1 AND YearD = $year + 1 THEN I ELSE 0 END) as A7,
            SUM(CASE WHEN AccountNo IN ('0411', '0412') AND CapitalCateNo IN ('2012','20012102') AND YearD = $year + 1 THEN I ELSE 0 END) as A8 FROM
            (SELECT CONCAT('A#', SC.CateNo, '#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo as SectorCateList,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo,
            A.AccountNo, A.BudgetLevel, A.FiscalPeriod, CC.CateNo as CapitalCateNo, SUM(LCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = 1
            AND A.AccountNo IN ('0461', '0462','0441', '0442', '0721', '0722', '0411', '0412')
            AND ((CC.CateNo = '2012' AND CC.CateValue IN ('1','2')) OR (CC.CateNo = '20012102' AND CC.CateValue = '3'))
            $whereCondition
            GROUP BY A.AccountNo, A.BudgetLevel, A.FiscalPeriod, CC.CateNo, SC.CateNo, A.ProjectNo, YearD) as CH GROUP BY SectorCateList, ProjectNo)
            as DT ON DUPLICATE KEY UPDATE I16 = A1, I17 = A2, I21 = A3, I22 = A4, I31 = A5, I32 = A6, I36 = A7, I37 = A8";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C32_35($wherePeriodDate1, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, Investdoc, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I25, I28)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN CapitalInOut = 1 AND CapitalCateNo = '2012' THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN CapitalInOut = 2 AND CapitalCateNo IN ('20012201', '20012202') THEN I ELSE 0 END) as A2 FROM
            (SELECT CONCAT('A#', SC.CateNo, '#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo as SectorCateList,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo,
            C.CapitalInOut, CC.CateNo as CapitalCateNo, SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut IN (1,2)
            AND A.FiscalPeriod IN (1,2)
            AND A.AccountNo IN ('0721', '0722')
            AND ((CC.CateNo = '2012' AND CC.CateValue IN ('1','2')) OR CC.CateNo = '20012201' OR CC.CateNo = '20012202')
            $wherePeriodDate1
            GROUP BY C.CapitalInOut, CC.CateNo, SC.CateNo, A.ProjectNo) as CH GROUP BY SectorCateList, ProjectNo)
            as DT ON DUPLICATE KEY UPDATE I25 = A1, I28 = A2";

        DB::statement($sql);
    }

    public function insertOrUpdateR2C33_34($wherePeriodDate1, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, Investdoc, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I26, I27)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName , BuildAddress, CapableDesign, PerformDate, InvestdocNo, SponsorName, PacttdocDate,
            SectorCateList, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN CapitalCateNo = '2012' AND BudgetLevel = 1 THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN CapitalCateNo IN ('2012', '20012102') THEN I ELSE 0 END) as A2 FROM
            (SELECT CONCAT('A#', SC.CateNo, '#', " . $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", SC.CateNo as SectorCateList,
            " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo,
            CC.CateNo as CapitalCateNo, A.BudgetLevel ,SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = 1
            AND A.FiscalPeriod IN (1,2)
            AND A.AccountNo IN ('0721', '0722')
            AND ((CC.CateNo = '2012' AND CC.CateValue IN ('1','2')) OR (CC.CateNo = '20012102' AND CC.CateValue = '3'))
            $wherePeriodDate1
            GROUP BY CC.CateNo, A.BudgetLevel, SC.CateNo, A.ProjectNo) as CH GROUP BY SectorCateList, ProjectNo)
            as DT ON DUPLICATE KEY UPDATE I26 = A1, I27 = A2";

        DB::statement($sql);
    }



    // sum row
    public function sumUpRow(){
        $listItems = DB::table('table_tmp')->where('Detail','=',1)->get();
        foreach($listItems as $item){
            $I1 = $item->I2 + $item->I5;
            $I7 = $item->I8 + $item->I13;
            $I14 = $item->I15 + $item->I18;
            $I19 = $item->I20 + $item->I23;
            $I24 = $item->I25 + $item->I28;
            $I29 = $item->I30 + $item->I33;
            $I34 = $item->I35 + $item->I38;
            DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update([
                'I1'=> $I1,  'I7'=> $I7, 'I14'=> $I14, 'I19'=> $I19,
                'I24'=> $I24, 'I29'=> $I29, 'I34'=> $I34
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
           SUM(M.I17) AS I17, SUM(M.I18) AS I18, SUM(M.I19) AS I19, SUM(M.I20) AS I20,
            SUM(M.I21) AS I21, SUM(M.I22) AS I22, SUM(M.I23) AS I23, SUM(M.I24) AS I24,
           SUM(M.I25) AS I25, SUM(M.I26) AS I26, SUM(M.I27) AS I27, SUM(M.I28) AS I28,
           SUM(M.I29) AS I29, SUM(M.I30) AS I30, SUM(M.I31) AS I31, SUM(M.I32) AS I32,
           SUM(M.I33) AS I33, SUM(M.I34) AS I34, SUM(M.I35) AS I35, SUM(M.I36) AS I36,
           SUM(M.I37) AS I37, SUM(M.I38) AS I38, SUM(M.I39) AS I39
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
                    $update = "UPDATE table_tmp
                SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ",
                I9 = " . $I9 . ", I10 = " . $I10 . ", I11 = " . $I11 . ", I12 = " . $I12 . ",
                I13 = " . $I13 . ", I14 = " . $I14 . ", I15 = " . $I15 . ", I16 = " . $I16 . ",
                I17 = " . $I17 . ", I18 = " . $I18 . ", I19 = " . $I19 . ", I20 = " . $I20 . ",
                I21 = " . $I21 . ", I22 = " . $I22 . ", I23 = " . $I23 . ", I24 = " . $I24 . ",
                I25 = " . $I25 . ", I26 = " . $I26 . ", I27 = " . $I27 . ", I28 = " . $I28 . ",
                I29 = " . $I29 . ", I30 = " . $I30 . ", I31 = " . $I31 . ", I32 = " . $I32 . ",
                I33 = " . $I33 . ", I34 = " . $I34 . ", I35 = " . $I35 . ", I36 = " . $I36 . ",
                I37 = " . $I37 . ", I38 = " . $I38 . ", I39 = " . $I39 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }

    }

    public function setIndexMB34($maxLevel){
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
            else if($i == 3){
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
