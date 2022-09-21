<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB26Controller extends Controller {
    use Common;

    private $AccountNo = "('091','0441','0442','0461','0462','0721','0722','0411','0412')";
    private $AccountParentNo = ['008','009','004','006','012','013','014'];
    public function SBP3422016TTBTC_MB26(Request $request){
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

        $whereCommonCondition = "AND A.InTransTypeID NOT IN (16,17,18) AND A.BudgetAllocTypeID != '3' AND C.CapitalInOut = 1 ";
        $currentDate = date('Y-m-d');
        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year - 1 AND $year $whereCommonCondition";
        $whereDebt = " AND A.PeriodFromDate <= '". $currentDate ."' $whereCommonCondition ";
        $wherePeriodDate = " AND A.PeriodType = 9 AND A.PeriodFromDate >= $year AND A.PeriodToDate <= $year $whereCommonCondition";
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

            `Project` varchar(50) DEFAULT '',
            `ProgramNo` varchar(255) DEFAULT '',
            `BuildAddress` varchar(255) DEFAULT '',
            `CapableDesign` varchar(255) DEFAULT '',
            `PerformDate` varchar(255) DEFAULT '',
            `InvestdocNo` varchar(255) DEFAULT '',
            `program` varchar(50) DEFAULT '',
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

        $this->insertR1C5($whereDebt);
        $this->insertOrUpdateR1C6($whereDebt);
        $this->insertOrUpdateR1C7($wherePeriodDate);
        $this->insertOrUpdateR1C9($wherePeriodDate);
        $this->insertOrUpdateR1C10($wherePeriodDate);
        $this->insertOrUpdateR1C11_13_15_19_23($whereCondition, $year);
        $this->insertOrUpdateR1C12_14_16_20($whereCondition, $year);
        $this->insertOrUpdateR1C17($wherePeriodDate1);
        $this->insertOrUpdateR1C18($wherePeriodDate1);
        $this->insertOrUpdateR1C21($wherePeriodDate);
        $this->insertOrUpdateR1C22($wherePeriodDate);
        $this->insertOrUpdateR1C24($whereNextYear);
        $this->insertOrUpdateR1C25($whereNextYear);
        if(DB::table('table_tmp')->count() > $countRecords){
            $this->getParentDataTreeCommon(['program',['ProjectCateTemporary','1'],'project'],'A#1');
            $this->getParentDataTreeCommon(['program',['ProjectCateTemporary','1'],'project'],'A#2');
            $countRecords = DB::table('table_tmp')->count();
        }


        $whereR2 = " AND ((YEAR(P.ExpectedFinishDate) = $year AND YEAR(P.ExpectedHandoverDate) = $year) OR YEAR(P.ExpectedFinishDate) >= $year + 1 OR YEAR(P.ExpectedStartDate) = $year + 1) AND PC.CateNo = '009' AND PC.CateValue = '2' ";
        $this->insertR2C5($whereDebt,$whereR2,$year);
        $this->insertOrUpdateR2C6($whereDebt,$whereR2,$year);
        $this->insertOrUpdateR2C7($wherePeriodDate,$whereR2,$year);
        $this->insertOrUpdateR2C9($wherePeriodDate,$whereR2,$year);
        $this->insertOrUpdateR2C10($wherePeriodDate,$whereR2,$year);
        $this->insertOrUpdateR2C11_13_15_19_23($whereCondition,$whereR2,$year);
        $this->insertOrUpdateR2C12_14_16_20($whereCondition,$whereR2,$year);
        $this->insertOrUpdateR2C17($wherePeriodDate1,$whereR2,$year);
        $this->insertOrUpdateR2C18($wherePeriodDate1,$whereR2,$year);
        $this->insertOrUpdateR2C21($wherePeriodDate,$whereR2,$year);
        $this->insertOrUpdateR2C22($wherePeriodDate,$whereR2,$year);
        $this->insertOrUpdateR2C24($whereNextYear,$whereR2,$year);
        $this->insertOrUpdateR2C25($whereNextYear,$whereR2,$year);
        if(DB::table('table_tmp')->count() > $countRecords){
            $this->getParentDataTreeCommon(['program',['ProjectCateTemporary','2'], 'ProjectPhaseTemporary', 'ProjectGroupTemporary','project'],'A#1');
            $this->getParentDataTreeCommon(['program',['ProjectCateTemporary','2'], 'ProjectPhaseTemporary', 'ProjectGroupTemporary','project'],'A#2');
        }

        $this->setLevel();

        //set STT


        // sum Row
        $this->sumUpRow();


        // Thêm dữ liệu mặc định
        $insertArr1 = [
            ['STT'=>'', 'ItemID'=> 'A', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG SỐ', 'FontWeight'=> '1'],
            ['STT'=>'A', 'ItemID'=> 'A#1', 'ParentID'=> 'A', 'Level'=> 1, 'ItemName'=> 'CÁC CHƯƠNG TRÌNH MỤC TIÊU QUỐC GIA:', 'FontWeight'=> '1'],
            ['STT'=>'B', 'ItemID'=> 'A#2', 'ParentID'=> 'A', 'Level'=> 1, 'ItemName'=> 'CÁC CHƯƠNG TRÌNH MỤC TIÊU:', 'FontWeight'=> '1'],
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
            ['ItemID'=> 'QTQG','ItemName'=> 'QTQG'],
            ['ItemID'=> 'A','ItemName'=> 'A'],
            ['ItemID'=> 'B','ItemName'=> 'B'],
            ['ItemID'=> 'C','ItemName'=> 'C'],
        ];

        DB::table('ProjectGroupTemporary')->insert($insertA);
    }


    // Chuẩn bị dự án

    public function selectCommon(){
        return " P.BuildAddress, P.CapableDesign, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as PerformDate, P.InvestdocNo ";
    }

    public function insertR1C5($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
          program, ProjectCateTemporary, Project, Detail, I1)
            SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo, 1 as ProjectCateTemporary,A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE  A.AccountNo = '091' AND PGC.CateNo ='001'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            $whereDebt
            GROUP BY PG.ProgramNo, A.ProjectNo
            ";

        DB::statement($sql);
    }
    public function insertOrUpdateR1C6($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary, Project, Detail, I2)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo,1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012102' AND CC.CateValue IN ('9','4')
            AND A.AccountNo = '091' AND PGC.CateNo ='001'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            $whereDebt
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I2 = I
            ";

        DB::statement($sql);
    }
    public function insertOrUpdateR1C7($wherePeriodDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
             program,ProjectCateTemporary, Project, Detail, I3)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo,1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.InTransTypeID = 6
            AND A.AccountNo = '0441'
              $wherePeriodDate
            AND PGC.CateNo ='001'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I3 = I
            ";

        DB::statement($sql);
    }
    public function insertOrUpdateR1C9($wherePeriodDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
             program,ProjectCateTemporary, Project, Detail, I4)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo,1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.InTransTypeID = 6
            AND A.AccountNo = '0441'
              $wherePeriodDate
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I4 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR1C10($wherePeriodDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
             program,ProjectCateTemporary, Project, Detail, I5)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo,1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.isDebtTrans = 1
            AND A.AccountNo = '0441'
              $wherePeriodDate
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I5 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR1C11_13_15_19_23($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
                  program, ProjectCateTemporary, Project, Detail, I7, I9, I11, I15, I19)
    SELECT * FROM
    (SELECT CONCAT(ParentID, '#', ProjectNo), ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
           program, 1 as ProjectCateTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN AccountNo = '0461' AND YearD = $year - 1 THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN AccountNo = '0441' AND YearD = $year AND FiscalPeriod = 2 THEN I ELSE 0 END) as A2,
            SUM(CASE WHEN AccountNo = '0441' AND YearD = $year AND FiscalPeriod = 1 THEN I ELSE 0 END) as A3,
            SUM(CASE WHEN AccountNo = '0721' AND YearD = $year AND FiscalPeriod IN ('1','2') THEN I ELSE 0 END) as A4,
            SUM(CASE WHEN AccountNo = '0411' AND YearD = $year + 1 THEN I ELSE 0 END) as A5
    FROM
         (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID, A.FiscalPeriod as FiscalPeriod,
                 A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, PG.ProgramNo as program,
                 A.AccountNo, Year(PeriodFromDate) as YearD, SUM(LCDebitAmount) AS I
          FROM act_gl_books AS A
                   INNER JOIN program as PG on A.ProgramID = PG.ProgramID
                   INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
                   INNER JOIN capital as C on A.CapitalID = C.CapitalID
                   INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
                   INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
                   INNER JOIN Project as P on A.ProjectID = P.ProjectID
          WHERE A.AccountNo IN ('0461','0441','0721','0411')
            AND PGC.CateNo ='001'
              $whereCondition
            AND PC.CateNo = '009' AND PC.CateValue = '1'
          GROUP BY  A.ProjectNo, A.AccountNo, YearD, FiscalPeriod, PG.ProgramNo
         ) as CH GROUP BY program, ProjectNo) as DT ON DUPLICATE KEY UPDATE I7 = A1, I9 = A2, I11 = A3, I15 = A4, I19 = A5";

        DB::statement($sql);
    }
    public function insertOrUpdateR1C12_14_16_20($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
                  program, ProjectCateTemporary, Project, Detail, I8, I10, I12,I16)
    SELECT * FROM
    (SELECT CONCAT(ParentID, '#', ProjectNo), ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
           program,1 as ProjectCateTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN AccountNo = '0461' AND YearD = $year - 1 THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN AccountNo = '0441' AND YearD = $year AND FiscalPeriod = 2 THEN I ELSE 0 END) as A2,
            SUM(CASE WHEN AccountNo = '0441' AND YearD = $year AND FiscalPeriod = 1 THEN I ELSE 0 END) as A3,
            SUM(CASE WHEN AccountNo = '0721' AND YearD = $year AND FiscalPeriod IN ('1','2') THEN I ELSE 0 END) as A4
    FROM
         (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID, A.FiscalPeriod as FiscalPeriod,
                 A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, PG.ProgramNo as program,
                 A.AccountNo, Year(PeriodFromDate) as YearD, SUM(LCDebitAmount) AS I
          FROM act_gl_books AS A
                   INNER JOIN program as PG on A.ProgramID = PG.ProgramID
                   INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
                   INNER JOIN capital as C on A.CapitalID = C.CapitalID
                   INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
                   INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
                   INNER JOIN Project as P on A.ProjectID = P.ProjectID
          WHERE A.AccountNo IN ('0461','0441','0721')
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
              $whereCondition
            AND PC.CateNo = '009' AND PC.CateValue = '1'
          GROUP BY  A.ProjectNo, A.AccountNo, YearD, FiscalPeriod, PG.ProgramNo
         ) as CH GROUP BY program, ProjectNo) as DT ON DUPLICATE KEY UPDATE I8 = A1, I10 = A2, I12 = A3, I16 = A4";

        DB::statement($sql);
    }
    public function insertOrUpdateR1C17($wherePeriodDate1){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary, Project, Detail, I13)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PG.ProgramNo, 1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.FiscalPeriod IN ('1','2')
            AND A.AccountNo = '0721'
              $wherePeriodDate1
            AND PGC.CateNo ='001'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I13 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR1C18($wherePeriodDate1){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary, Project, Detail, I14)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo,1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.FiscalPeriod IN ('1','2')
            AND A.AccountNo = '0721'
              $wherePeriodDate1
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I14 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR1C21($wherePeriodDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary, Project, Detail, I17)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo,1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.FiscalPeriod = 2
            AND A.AccountNo = '0251'
              $wherePeriodDate
            AND PGC.CateNo ='001'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I17 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR1C22($wherePeriodDate){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary, Project, Detail, I18)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo,1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.FiscalPeriod = 2
            AND A.AccountNo = '0251'
              $wherePeriodDate
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I18 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR1C24($whereNextYear){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary, Project, Detail, I21)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo, 1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.InTransTypeID = 6
            AND A.AccountNo = '0411'
              $whereNextYear
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I21 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR1C25($whereNextYear){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary, Project, Detail, I22)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#1') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo,1 as ProjectCateTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.isDebtTrans = 1
            AND A.AccountNo = '0411'
              $whereNextYear
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I22 = I";
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
    public function insertR2C5($whereDebt, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I1)
            SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#'," .  $this->setCaseProjectPhase($year) . ", '#', P.Group, '#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PG.ProgramNo, 2 as ProjectCateTemporary," . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE  A.AccountNo = '091' AND PGC.CateNo ='001'
            $whereDebt
            $whereR2
            GROUP BY PG.ProgramNo, A.ProjectNo
            ";

        DB::statement($sql);
    }
    public function insertOrUpdateR2C6($whereDebt, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I2)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#'," .  $this->setCaseProjectPhase($year) . ", '#', P.Group, '#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PG.ProgramNo, 2 as ProjectCateTemporary," . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012102' AND CC.CateValue IN ('9','4')
            AND A.AccountNo = '091' AND PGC.CateNo ='001'
            $whereDebt
            $whereR2
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I2 = I
            ";

        DB::statement($sql);
    }
    public function insertOrUpdateR2C7($wherePeriodDate,$whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I3)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#'," .  $this->setCaseProjectPhase($year) . ", '#', P.Group, '#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo,2 as ProjectCateTemporary," . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.InTransTypeID = 6
            AND A.AccountNo = '0441'
              $wherePeriodDate
              $whereR2
            AND PGC.CateNo ='001'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I3 = I
            ";

        DB::statement($sql);
    }
    public function insertOrUpdateR2C9($wherePeriodDate,$whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I4)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#'," .  $this->setCaseProjectPhase($year) . ", '#', P.Group, '#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PG.ProgramNo, 2 as ProjectCateTemporary," . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary,
            A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.InTransTypeID = 6
            AND A.AccountNo = '0441'
              $wherePeriodDate
              $whereR2
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I4 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR2C10($wherePeriodDate,$whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I5)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#'," .  $this->setCaseProjectPhase($year) . ", '#', P.Group, '#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PG.ProgramNo, 2 as ProjectCateTemporary," . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary,
            A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.isDebtTrans = 1
            AND A.AccountNo = '0441'
              $wherePeriodDate
              $whereR2
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
            AND PC.CateNo = '009' AND PC.CateValue = '1'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I5 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR2C11_13_15_19_23($whereCondition,$whereR2,$year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
         program,ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I7, I9, I11, I15, I19)
    SELECT * FROM
    (SELECT CONCAT(ParentID, '#', ProjectNo), ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, 2 as ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN AccountNo = '0461' AND YearD = $year - 1 THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN AccountNo = '0441' AND YearD = $year AND FiscalPeriod = 2 THEN I ELSE 0 END) as A2,
            SUM(CASE WHEN AccountNo = '0441' AND YearD = $year AND FiscalPeriod = 1 THEN I ELSE 0 END) as A3,
            SUM(CASE WHEN AccountNo = '0721' AND YearD = $year AND FiscalPeriod IN ('1','2') THEN I ELSE 0 END) as A4,
            SUM(CASE WHEN AccountNo = '0411' AND YearD = $year + 1 THEN I ELSE 0 END) as A5
    FROM
         (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID, A.FiscalPeriod as FiscalPeriod,
                 A.ProjectName as ItemName, " . $this->selectCommon() . ", " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo, PG.ProgramNo as program,
                 A.AccountNo, Year(PeriodFromDate) as YearD, SUM(LCDebitAmount) AS I
          FROM act_gl_books AS A
                   INNER JOIN program as PG on A.ProgramID = PG.ProgramID
                   INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
                   INNER JOIN capital as C on A.CapitalID = C.CapitalID
                   INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
                   INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
                   INNER JOIN Project as P on A.ProjectID = P.ProjectID
          WHERE A.AccountNo IN ('0461','0441','0721','0411')
            AND PGC.CateNo ='001'
              $whereCondition
              $whereR2
          GROUP BY  A.ProjectNo, A.AccountNo, YearD, FiscalPeriod, PG.ProgramNo
         ) as CH GROUP BY program, ProjectNo) as DT ON DUPLICATE KEY UPDATE I7 = A1, I9 = A2, I11 = A3, I15 = A4, I19 = A5";

        DB::statement($sql);
    }
    public function insertOrUpdateR2C12_14_16_20($whereCondition,$whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
         program,ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I8, I10, I12,I16)
    SELECT * FROM
    (SELECT CONCAT(ParentID, '#', ProjectNo), ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, 2 as ProjectCateTemporary, ProjectPhaseTemporary, ProjectGroupTemporary, ProjectNo, 1 as Detail,
            SUM(CASE WHEN AccountNo = '0461' AND YearD = $year - 1 THEN I ELSE 0 END) as A1,
            SUM(CASE WHEN AccountNo = '0441' AND YearD = $year AND FiscalPeriod = 2 THEN I ELSE 0 END) as A2,
            SUM(CASE WHEN AccountNo = '0441' AND YearD = $year AND FiscalPeriod = 1 THEN I ELSE 0 END) as A3,
            SUM(CASE WHEN AccountNo = '0721' AND YearD = $year AND FiscalPeriod IN ('1','2') THEN I ELSE 0 END) as A4
    FROM
         (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID, A.FiscalPeriod as FiscalPeriod,
                 A.ProjectName as ItemName, " . $this->selectCommon() . ", 2 as ProjectCateTemporary," . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary, A.ProjectNo, PG.ProgramNo as program,
                 A.AccountNo, Year(PeriodFromDate) as YearD, SUM(LCDebitAmount) AS I
          FROM act_gl_books AS A
                   INNER JOIN program as PG on A.ProgramID = PG.ProgramID
                   INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
                   INNER JOIN capital as C on A.CapitalID = C.CapitalID
                   INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
                   INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
                   INNER JOIN Project as P on A.ProjectID = P.ProjectID
          WHERE A.AccountNo IN ('0461','0441','0721')
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
              $whereCondition
              $whereR2
          GROUP BY  A.ProjectNo, A.AccountNo, YearD, FiscalPeriod, PG.ProgramNo
         ) as CH GROUP BY program, ProjectNo) as DT ON DUPLICATE KEY UPDATE I8 = A1, I10 = A2, I12 = A3, I16 = A4";

        DB::statement($sql);
    }
    public function insertOrUpdateR2C17($wherePeriodDate1,$whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I13)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#'," .  $this->setCaseProjectPhase($year) . ", '#', P.Group, '#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo,2 as ProjectCateTemporary," . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary,
            A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.FiscalPeriod IN ('1','2')
            AND A.AccountNo = '0721'
              $wherePeriodDate1
              $whereR2
            AND PGC.CateNo ='001'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I13 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR2C18($wherePeriodDate1,$whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I14)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#'," .  $this->setCaseProjectPhase($year) . ", '#', P.Group, '#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", PG.ProgramNo,2 as ProjectCateTemporary," . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary,
            A.ProjectNo, 1 as Detail, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.FiscalPeriod IN ('1','2')
            AND A.AccountNo = '0721'
              $wherePeriodDate1
              $whereR2
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I14 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR2C21($wherePeriodDate,$whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I17)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#'," .  $this->setCaseProjectPhase($year) . ", '#', P.Group, '#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PG.ProgramNo, 2 as ProjectCateTemporary," . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary,
            A.ProjectNo, 1 as Detail,
            SUM(LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.FiscalPeriod = 2
            AND A.AccountNo = '0251'
              $wherePeriodDate
              $whereR2
            AND PGC.CateNo ='001'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I17 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR2C22($wherePeriodDate,$whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I18)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#'," .  $this->setCaseProjectPhase($year) . ", '#', P.Group, '#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PG.ProgramNo, 2 as ProjectCateTemporary," . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary,
            A.ProjectNo, 1 as Detail,
            SUM(LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.FiscalPeriod = 2
            AND A.AccountNo = '0251'
              $wherePeriodDate
              $whereR2
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I18 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR2C24($whereNextYear,$whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I21)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#'," .  $this->setCaseProjectPhase($year) . ", '#', P.Group, '#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PG.ProgramNo, 2 as ProjectCateTemporary," . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary,
            A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.InTransTypeID = 6
            AND A.AccountNo = '0411'
              $whereNextYear
              $whereR2
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I21 = I";

        DB::statement($sql);
    }
    public function insertOrUpdateR2C25($whereNextYear,$whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, BuildAddress, CapableDesign, PerformDate, InvestdocNo,
            program, ProjectCateTemporary,ProjectPhaseTemporary, ProjectGroupTemporary, Project, Detail, I22)
            SELECT * FROM
            (SELECT CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#'," .  $this->setCaseProjectPhase($year) . ", '#', P.Group, '#',A.ProjectNo) as ItemID, CONCAT('A#',PGC.CateValue,'#', PG.ProgramNo, '#2#' ," .  $this->setCaseProjectPhase($year) . ", '#', P.Group) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ",PG.ProgramNo, 2 as ProjectCateTemporary," . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, P.Group as ProjectGroupTemporary,
            A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN program as PG on A.ProgramID = PG.ProgramID
            INNER JOIN program_cate as PGC on A.ProgramID = PGC.ProgramID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.isDebtTrans = 1
            AND A.AccountNo = '0411'
              $whereNextYear
              $whereR2
            AND CC.CateNo='20012102' AND CC.CateValue IN ('5','11') AND PGC.CateNo ='001'
            GROUP BY PG.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I22 = I";
        DB::statement($sql);
    }

    // sum row
    public function sumUpRow(){
        $listItems = DB::table('table_tmp')->where('Detail','=',1)->get();
        foreach($listItems as $item){
            $I4 = $item->I5 + $item->I6;
            $I20 = $item->I21 + $item->I22;
            DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update([
                'I4'=> $I4,  'I18'=> $I20,
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
           SUM(M.I21) AS I21, SUM(M.I22) AS I22
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
                    $update = "UPDATE table_tmp
                SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ",
                I9 = " . $I9 . ", I10 = " . $I10 . ", I11 = " . $I11 . ", I12 = " . $I12 . ",
                I13 = " . $I13 . ", I14 = " . $I14 . ", I15 = " . $I15 . ", I16 = " . $I16 . ",
                I17 = " . $I17 . ", I18 = " . $I18 . ", I19 = " . $I19 . ", I20 = " . $I20 . ", I21 = " . $I21 . ", I22 = " . $I22 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }

    }

    public function setIndexMB26($maxLevel){
        $insertArr = [];
        for($i = 1; $i <= $maxLevel ; $i++){
            $insertArr1 = [];
            $sql = "SELECT * FROM table_tmp WHERE Level = " . $i . " ORDER BY ItemID ASC";
            $result = DB::select($sql);
            foreach($result as $key=> $item){
                if($i == 1){
                    $insertArr1[] = [
                        'ItemID' => $item->ItemID,
                        'ParentID'=> $item->ParentID,
                        'Level'=> $item->Level,
                        'STT'=> $this->numberToRomanRepresentation($key + 1)
                    ];
                    DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=>$this->numberToRomanRepresentation($key + 1)]);
                }
                if($i == 2){
                    foreach($insertArr[$i-2] as $value){
                        $index = 1;
                        foreach ($result as $key => $item){
                            if($value['ItemID'] == $item->ParentID){
                                $insertArr1[] = [
                                    'ItemID' => $item->ItemID,
                                    'ParentID'=> $item->ParentID,
                                    'Level'=>$item->Level,
                                    'STT'=> $value['STT']. '.'. $index,
                                ];
                                DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=>$value['STT']. '.'. $index]);
                                $index++;
                            }
                        }
                    }
                }
            }
        }
    }


}
