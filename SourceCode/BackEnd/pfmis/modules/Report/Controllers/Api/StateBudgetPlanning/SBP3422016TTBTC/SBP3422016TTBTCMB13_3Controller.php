<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB13_3Controller extends Controller {
    use Common;

    public function SBP3422016TTBTC_MB13_3(Request $request){
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

        $whereCommonCondition = "AND A.InTransTypeID NOT IN (16,17,18) AND SC.CateNo = '2000304' AND SC.CateValue = 1 ";
        $currentDate = date('Y-m-d');
        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year - 1 AND $year  $whereCommonCondition";
        $whereDebt = "AND A.PeriodFromDate <= '". $currentDate ."' $whereCommonCondition ";
        $whereCondition1 = "AND A.PeriodFromDate <= '" . $year . "-12-31' $whereCommonCondition";

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
            `Program` varchar(50) DEFAULT '',
            `Project` varchar(50) DEFAULT '',
            `BuildAddress` varchar(255) DEFAULT '',
            `InvestorName` varchar(255) DEFAULT '',
            `PerformDate` varchar(255) DEFAULT '',
            `Invest` varchar(255) DEFAULT '',
            `Detail` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        //calculator here


        // I, II

        $countRecords = DB::table('table_tmp')->count();

        $this->insertProgramR1_2C5($whereDebt, $year);
        $this->insertOrUpdateProgramR1_2C6($whereDebt, $year);
        $this->insertOrUpdateProgramR1_2C8_9_15($whereCondition, $year);
        $this->insertOrUpdateProgramR1_2C10_16($whereCondition,$year);
        $this->insertOrUpdateProgramR1_2C12($whereCondition1, $year);
        $this->insertOrUpdateProgramR1_2C13($whereCondition1, $year);

        if(DB::table('table_tmp')->count() > $countRecords){
            $this->getParentDataTreeCommon(['program','project']);
        }

        $this->insertProjectR1_2C5($whereDebt, $year);
        $this->insertOrUpdateProjectR1_2C6($whereDebt, $year);
        $this->insertOrUpdateProjectR1_2C8_9_15($whereCondition, $year);
        $this->insertOrUpdateProjectR1_2C10_16($whereCondition,$year);
        $this->insertOrUpdateProjectR1_2C12($whereCondition1, $year);
        $this->insertOrUpdateProjectR1_2C13($whereCondition1, $year);

        // III

        $this->insertProjectR3C5($whereDebt, $year);
        $this->insertOrUpdateProjectR3C6($whereDebt, $year);
        $this->insertOrUpdateProjectR3C8_9_15($whereCondition, $year);
        $this->insertOrUpdateProjectR3C10_16($whereCondition,$year);
        $this->insertOrUpdateProjectR3C12($whereCondition1, $year);
        $this->insertOrUpdateProjectR3C13($whereCondition1, $year);

        // IV, V

        $this->insertProjectR4_5C5($whereDebt, $year);
        $this->insertOrUpdateProjectR4_5C6($whereDebt, $year);
        $this->insertOrUpdateProjectR4_5C8_9_15($whereCondition, $year);
        $this->insertOrUpdateProjectR4_5C10_16($whereCondition,$year);
        $this->insertOrUpdateProjectR4_5C12($whereCondition1, $year);
        $this->insertOrUpdateProjectR4_5C13($whereCondition1, $year);

        $this->setLevel();

        //set STT
        $maxLevel = DB::table('table_tmp')->select(DB::raw('Max(Level) as maxLevel'))->first();
        $this->setIndexMB13_3($maxLevel, 'A#01#1#1', 4,'1.');
        $this->setIndexMB13_3($maxLevel, 'A#01#1#2', 4,'2.');
        $this->setIndexMB13_3($maxLevel, 'A#01#2#1', 4, '1.');
        $this->setIndexMB13_3($maxLevel, 'A#01#2#2', 4, '2.');
        $this->setIndexMB13_3($maxLevel, 'A#02#1#1',4,'1.');
        $this->setIndexMB13_3($maxLevel, 'A#02#1#2',4,'2.');
        $this->setIndexMB13_3($maxLevel, 'A#02#2#1',4,'1.');
        $this->setIndexMB13_3($maxLevel, 'A#02#2#2',4,'2.');
        $this->setIndexMB13_3($maxLevel, 'A#02#2',4, '2.');
        $this->setIndexMB13_3($maxLevel, 'A#03#1', 3);
        $this->setIndexMB13_3($maxLevel, 'A#03#2',3);
        $this->setIndexMB13_3($maxLevel, 'A#04',2);
        $this->setIndexMB13_3($maxLevel, 'A#05',2);
        // sum Row
        $this->sumUpRow();

        // Thêm dữ liệu mặc định
        $this->insertDataDefault();

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumUpTableTmp($maxLevel, 1);


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


    public function selectCommon(){
        return " P.InvestorName, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as PerformDate,
         CONCAT(P.InvestDecisionOrganName, ': ', P.InvestdocNo, '(', DATE_FORMAT(P.InvestdocDate, '%d/%m/%Y') ,')') as Invest ";
    }

    public function selectPerformDate($year){
        return " CASE WHEN P.StartedDate < '" . $year . "-01-01' THEN '1'
                      WHEN P.StartedDate > '" . $year . "-01-01' THEN '2' END ";
    }

    public function insertProgramR1_2C5($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Program, Project, Detail, I2)
            SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#1#', A.ProgramNo , '#' , A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#1#', A.ProgramNo) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProgramNo, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) = '2001'
            AND A.AccountNo = '091'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.01', '007.02') AND PC.CateValue = '1'
            AND A.ProgramNo IS NOT NULL
            $whereDebt
            GROUP BY PC.CateNo, A.ProgramNo, A.ProjectNo
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProgramR1_2C6($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Program, Project, Detail, I3)
            SELECT * FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#1#', A.ProgramNo , '#' , A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#1#', A.ProgramNo) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProgramNo, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2002' OR LEFT(CC.CateNo,4) = '2003')
            AND A.AccountNo = '091'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.01', '007.02') AND PC.CateValue = '1'
            AND A.ProgramNo IS NOT NULL
            $whereDebt
            GROUP BY PC.CateNo, A.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I3 = I
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProgramR1_2C8_9_15($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Program, Project, Detail, I5, I6, I12)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            ProgramNo, ProjectNo, 1 as Detail ,
            MAX(CASE WHEN AccountNo = '0441' AND YearD = $year THEN I ELSE 0 END) as A1,
            MAX(CASE WHEN AccountNo = '0721' AND YearD = $year THEN I ELSE 0 END) as A2,
            MAX(CASE WHEN AccountNo = '0441' AND YearD = $year + 1 THEN I ELSE 0 END) as A3 FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#1#', A.ProgramNo) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProgramNo, A.ProjectNo,
            PC.CateNo as ProjectCateNo, A.AccountNo, YEAR(A.PeriodFromDate) as YearD ,SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) = '2001'
            AND A.AccountNo IN ('0441','0721')
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.01', '007.02') AND PC.CateValue = '1'
            AND A.ProgramNo IS NOT NULL
            $whereCondition
            GROUP BY PC.CateNo, A.ProgramNo, A.ProjectNo, YearD, A.AccountNo) as CH GROUP BY ProjectCateNo, ProgramNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I5 = A1, I6 = A2, I12 = A3
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProgramR1_2C10_16($whereCondition,$year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Program, Project, Detail, I7, I13)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            ProgramNo, ProjectNo, 1 as Detail ,
            MAX(CASE WHEN YearD = $year THEN I ELSE 0 END) as A1,
            MAX(CASE WHEN YearD = $year + 1 THEN I ELSE 0 END) as A2 FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#1#', A.ProgramNo) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProgramNo, A.ProjectNo,
            PC.CateNo as ProjectCateNo, YEAR(A.PeriodFromDate) as YearD ,SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2002' OR LEFT(CC.CateNo,4) = '2003')
            AND A.AccountNo = '0442'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.01', '007.02') AND PC.CateValue = '1'
            AND A.ProgramNo IS NOT NULL
            $whereCondition
            GROUP BY PC.CateNo, A.ProgramNo, A.ProjectNo, YearD) as CH GROUP BY ProjectCateNo, ProgramNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I7 = A1, I13 = A2
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProgramR1_2C12($whereCondition1, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Program, Project, Detail, I9)
            SELECT * FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#1#', A.ProgramNo , '#' , A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#1#', A.ProgramNo) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProgramNo, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) = '2001'
            AND A.AccountNo = '0441'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.01', '007.02') AND PC.CateValue = '1'
            AND A.ProgramNo IS NOT NULL
            $whereCondition1
            GROUP BY PC.CateNo, A.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I9 = I
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProgramR1_2C13($whereCondition1, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Program, Project, Detail, I10)
            SELECT * FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#1#', A.ProgramNo , '#' , A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#1#', A.ProgramNo) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProgramNo, A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2002' OR LEFT(CC.CateNo,4) = '2003')
            AND A.AccountNo = '0442'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.01', '007.02') AND PC.CateValue = '1'
            AND A.ProgramNo IS NOT NULL
            $whereCondition1
            GROUP BY PC.CateNo, A.ProgramNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I10 = I
            ";

        DB::statement($sql);
    }


    // Dự án I, II
    public function insertProjectR1_2C5($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I2)
            SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#2#' , A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#2') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) = '2001'
            AND A.AccountNo = '091'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.01', '007.02') AND PC.CateValue = '2'
            $whereDebt
            GROUP BY PC.CateNo, A.ProjectNo
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR1_2C6($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I3)
            SELECT * FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#2#', A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#2') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2002' OR LEFT(CC.CateNo,4) = '2003')
            AND A.AccountNo = '091'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.01', '007.02') AND PC.CateValue = '2'
            $whereDebt
            GROUP BY PC.CateNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I3 = I
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR1_2C8_9_15($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I5, I6, I12)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            ProjectNo, 1 as Detail ,
            MAX(CASE WHEN AccountNo = '0441' AND YearD = $year THEN I ELSE 0 END) as A1,
            MAX(CASE WHEN AccountNo = '0721' AND YearD = $year THEN I ELSE 0 END) as A2,
            MAX(CASE WHEN AccountNo = '0441' AND YearD = $year + 1 THEN I ELSE 0 END) as A3 FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#2') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo,
            PC.CateNo as ProjectCateNo, A.AccountNo, YEAR(A.PeriodFromDate) as YearD ,SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) = '2001'
            AND A.AccountNo IN ('0441','0721')
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.01', '007.02') AND PC.CateValue = '2'
            $whereCondition
            GROUP BY PC.CateNo, A.ProjectNo, YearD, A.AccountNo) as CH GROUP BY ProjectCateNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I5 = A1, I6 = A2, I12 = A3
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR1_2C10_16($whereCondition,$year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I7, I13)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            ProjectNo, 1 as Detail ,
            MAX(CASE WHEN YearD = $year THEN I ELSE 0 END) as A1,
            MAX(CASE WHEN YearD = $year + 1 THEN I ELSE 0 END) as A2 FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#2') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo,
            PC.CateNo as ProjectCateNo, YEAR(A.PeriodFromDate) as YearD ,SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2002' OR LEFT(CC.CateNo,4) = '2003')
            AND A.AccountNo = '0442'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.01', '007.02') AND PC.CateValue = '2'
            $whereCondition
            GROUP BY PC.CateNo, A.ProjectNo, YearD) as CH GROUP BY ProjectCateNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I7 = A1, I13 = A2
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR1_2C12($whereCondition1, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I9)
            SELECT * FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#2#', A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#2') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) = '2001'
            AND A.AccountNo = '0441'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.01', '007.02') AND PC.CateValue = '2'
            $whereCondition1
            GROUP BY PC.CateNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I9 = I
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR1_2C13($whereCondition1, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I10)
            SELECT * FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#2#', A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#2') as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2002' OR LEFT(CC.CateNo,4) = '2003')
            AND A.AccountNo = '0442'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.01', '007.02') AND PC.CateValue = '2'
            $whereCondition1
            GROUP BY PC.CateNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I10 = I
            ";

        DB::statement($sql);
    }

    public function insertProjectR3C5($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I2)
            SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#' , A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " ) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) = '2001'
            AND A.AccountNo = '091'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo = '007.03'
            $whereDebt
            GROUP BY A.ProjectNo
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR3C6($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I3)
            SELECT * FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " ) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2002' OR LEFT(CC.CateNo,4) = '2003')
            AND A.AccountNo = '091'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo = '007.03'
            $whereDebt
            GROUP BY A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I3 = I
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR3C8_9_15($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I5, I6, I12)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            ProjectNo, 1 as Detail ,
            MAX(CASE WHEN AccountNo = '0441' AND YearD = $year THEN I ELSE 0 END) as A1,
            MAX(CASE WHEN AccountNo = '0721' AND YearD = $year THEN I ELSE 0 END) as A2,
            MAX(CASE WHEN AccountNo = '0441' AND YearD = $year + 1 THEN I ELSE 0 END) as A3 FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . ") as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo,
            A.AccountNo, YEAR(A.PeriodFromDate) as YearD ,SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) = '2001'
            AND A.AccountNo IN ('0441','0721')
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo = '007.03'
            $whereCondition
            GROUP BY A.ProjectNo, YearD, A.AccountNo) as CH GROUP BY ProjectNo) as DT ON DUPLICATE KEY UPDATE I5 = A1, I6 = A2, I12 = A3
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR3C10_16($whereCondition,$year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I7, I13)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            ProjectNo, 1 as Detail ,
            MAX(CASE WHEN YearD = $year THEN I ELSE 0 END) as A1,
            MAX(CASE WHEN YearD = $year + 1 THEN I ELSE 0 END) as A2 FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . ") as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo,
            YEAR(A.PeriodFromDate) as YearD ,SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2002' OR LEFT(CC.CateNo,4) = '2003')
            AND A.AccountNo = '0442'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo = '007.03'
            $whereCondition
            GROUP BY A.ProjectNo, YearD) as CH GROUP BY ProjectNo) as DT ON DUPLICATE KEY UPDATE I7 = A1, I13 = A2
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR3C12($whereCondition1, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I9)
            SELECT * FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " ) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) = '2001'
            AND A.AccountNo = '0441'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo = '007.03'
            $whereCondition1
            GROUP BY A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I9 = I
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR3C13($whereCondition1, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I10)
            SELECT * FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " , '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', " . $this->selectPerformDate($year) . " ) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2002' OR LEFT(CC.CateNo,4) = '2003')
            AND A.AccountNo = '0442'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo = '007.03'
            $whereCondition1
            GROUP BY A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I10 = I
            ";

        DB::statement($sql);
    }

    public function insertProjectR4_5C5($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I2)
            SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#' , A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1)) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) = '2001'
            AND A.AccountNo = '091'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.04','007.05')
            $whereDebt
            GROUP BY PC.CateNo, A.ProjectNo
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR4_5C6($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I3)
            SELECT * FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1)) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2002' OR LEFT(CC.CateNo,4) = '2003')
            AND A.AccountNo = '091'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.04','007.05')
            $whereDebt
            GROUP BY PC.CateNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I3 = I
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR4_5C8_9_15($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I5, I6, I12)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            ProjectNo, 1 as Detail ,
            MAX(CASE WHEN AccountNo = '0441' AND YearD = $year THEN I ELSE 0 END) as A1,
            MAX(CASE WHEN AccountNo = '0721' AND YearD = $year THEN I ELSE 0 END) as A2,
            MAX(CASE WHEN AccountNo = '0441' AND YearD = $year + 1 THEN I ELSE 0 END) as A3 FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1)) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo,
            PC.CateNo as ProjectCateNo, A.AccountNo, YEAR(A.PeriodFromDate) as YearD ,SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) = '2001'
            AND A.AccountNo IN ('0441','0721')
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.04','007.05')
            $whereCondition
            GROUP BY PC.CateNo, A.ProjectNo, YearD, A.AccountNo) as CH GROUP BY ProjectCateNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I5 = A1, I6 = A2, I12 = A3
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR4_5C10_16($whereCondition,$year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I7, I13)
            SELECT * FROM
            (SELECT CONCAT(ParentID, '#', ProjectNo) as ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            ProjectNo, 1 as Detail ,
            MAX(CASE WHEN YearD = $year THEN I ELSE 0 END) as A1,
            MAX(CASE WHEN YearD = $year + 1 THEN I ELSE 0 END) as A2 FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1)) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, PC.CateNo as ProjectCateNo,
            YEAR(A.PeriodFromDate) as YearD ,SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2002' OR LEFT(CC.CateNo,4) = '2003')
            AND A.AccountNo = '0442'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.04','007.05')
            $whereCondition
            GROUP BY PC.CateNo, A.ProjectNo, YearD) as CH GROUP BY ProjectCateNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I7 = A1, I13 = A2
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR4_5C12($whereCondition1, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I9)
            SELECT * FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1)) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) = '2001'
            AND A.AccountNo = '0441'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.04','007.05')
            $whereCondition1
            GROUP BY PC.CateNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I9 = I
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR4_5C13($whereCondition1, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I10)
            SELECT * FROM
            (SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#', A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1)) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE (LEFT(CC.CateNo,4) = '2002' OR LEFT(CC.CateNo,4) = '2003')
            AND A.AccountNo = '0442'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo IN ('007.04','007.05')
            $whereCondition1
            GROUP BY PC.CateNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I10 = I
            ";

        DB::statement($sql);
    }

    public function insertDataDefault(){
        $insert = [
            ['STT'=>'I', 'ItemID'=> 'A#01', 'ParentID'=> '', 'Level'=> 1, 'ItemName'=> 'Nhiệm vụ cấp quốc gia', 'FontWeight'=> '1'],
            ['STT'=>'I.1', 'ItemID'=> 'A#01#1', 'ParentID'=> 'A#01', 'Level'=> 2, 'ItemName'=> 'Nhiệm vụ chuyển tiếp', 'FontWeight'=> '1'],
            ['STT'=>'1', 'ItemID'=> 'A#01#1#1', 'ParentID'=> 'A#01#1', 'Level'=> 3, 'ItemName'=> 'Chương trình', 'FontWeight'=> '1'],
            ['STT'=>'2', 'ItemID'=> 'A#01#1#2', 'ParentID'=> 'A#01#1', 'Level'=> 3, 'ItemName'=> 'Các đề án/dự án/đề tài cấp quốc gia', 'FontWeight'=> '1'],
            ['STT'=>'I.2', 'ItemID'=> 'A#01#2', 'ParentID'=> 'A#01', 'Level'=> 2, 'ItemName'=> 'Nhiệm vụ mở mới', 'FontWeight'=> '1'],
            ['STT'=>'1', 'ItemID'=> 'A#01#2#1', 'ParentID'=> 'A#01#2', 'Level'=> 3, 'ItemName'=> 'Chương trình', 'FontWeight'=> '1'],
            ['STT'=>'2', 'ItemID'=> 'A#01#2#2', 'ParentID'=> 'A#01#2', 'Level'=> 3, 'ItemName'=> 'Các đề án/dự án/đề tài cấp quốc gia', 'FontWeight'=> '1'],

            ['STT'=>'II', 'ItemID'=> 'A#02', 'ParentID'=> '', 'Level'=> 1, 'ItemName'=> 'Nhiệm vụ cấp Bộ/Tỉnh', 'FontWeight'=> '1'],
            ['STT'=>'II.1', 'ItemID'=> 'A#02#1', 'ParentID'=> 'A#02', 'Level'=> 2, 'ItemName'=> 'Nhiệm vụ chuyển tiếp', 'FontWeight'=> '1'],
            ['STT'=>'1', 'ItemID'=> 'A#02#1#1', 'ParentID'=> 'A#02#1', 'Level'=> 3, 'ItemName'=> 'Chương trình', 'FontWeight'=> '1'],
            ['STT'=>'2', 'ItemID'=> 'A#02#1#2', 'ParentID'=> 'A#02#1', 'Level'=> 3, 'ItemName'=> 'Các đề án/dự án/đề tài cấp quốc gia', 'FontWeight'=> '1'],
            ['STT'=>'II.2', 'ItemID'=> 'A#02#2', 'ParentID'=> 'A#02', 'Level'=> 2, 'ItemName'=> 'Nhiệm vụ mở mới', 'FontWeight'=> '1'],
            ['STT'=>'1', 'ItemID'=> 'A#02#2#1', 'ParentID'=> 'A#02#2', 'Level'=> 3, 'ItemName'=> 'Chương trình', 'FontWeight'=> '1'],
            ['STT'=>'2', 'ItemID'=> 'A#02#2#2', 'ParentID'=> 'A#02#2', 'Level'=> 3, 'ItemName'=> 'Các đề án/dự án/đề tài cấp quốc gia', 'FontWeight'=> '1'],

            ['STT'=>'III', 'ItemID'=> 'A#03', 'ParentID'=> '', 'Level'=> 1, 'ItemName'=> 'Nhiệm vụ cấp cơ sở', 'FontWeight'=> '1'],
            ['STT'=>'III.1', 'ItemID'=> 'A#03#1', 'ParentID'=> 'A#03', 'Level'=> 2, 'ItemName'=> 'Nhiệm vụ chuyển tiếp', 'FontWeight'=> '1'],
            ['STT'=>'III.2', 'ItemID'=> 'A#03#2', 'ParentID'=> 'A#03', 'Level'=> 2, 'ItemName'=> 'Nhiệm vụ mở mới', 'FontWeight'=> '1'],
            ['STT'=>'IV', 'ItemID'=> 'A#04', 'ParentID'=> '', 'Level'=> 1, 'ItemName'=> 'Các hoạt động dịch vụ công/Nhiệm vụ thường xuyên theo chức năng/Hoạt động thường xuyên theo cơ chế khoán', 'FontWeight'=> '1'],
            ['STT'=>'V', 'ItemID'=> 'A#05', 'ParentID'=> '', 'Level'=> 1, 'ItemName'=> 'Các nhiệm vụ khác được cơ quan có thẩm quyền giao', 'FontWeight'=> '1'],

        ];

        DB::table("table_tmp")->insert($insert);
    }





    // sum row
    public function sumUpRow(){
        $listItems = DB::table('table_tmp')->where('Detail','=',1)->get();
        foreach($listItems as $item){
            $I1 = $item->I2 + $item->I3;
            $I4 = $item->I6 + $item->I7;
            $I8 = $item->I9 + $item->I10;
            $I11 = $item->I12 + $item->I13;
            DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update([
                'I1'=> $I1,  'I4'=> $I4,
                'I8'=> $I8,  'I11'=> $I11,
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
           SUM(M.I13) AS I13
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
                    $update = "UPDATE table_tmp
                SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ",
                I9 = " . $I9 . ", I10 = " . $I10 . ", I11 = " . $I11 . ", I12 = " . $I12 . ",
                I13 = " . $I13 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }

    }

    public function setIndexMB13_3($maxLevel, $id = null, $index, $stt = ''){
        $insertArr = [];
        for ($i = 2; $i <= $maxLevel->maxLevel; $i++) {
            $insertArr1 = [];
            $where = '';
            if ($id !== null) {
                $where = "AND LEFT(ItemID, LENGTH('" . $id . "')) = '" . $id . "'";
            }
            $sql = "SELECT * FROM table_tmp WHERE Level = $i $where ORDER BY ItemID ASC ";
            $result = DB::select($sql);
            if($result){
                if ($i == $index) {
                    foreach ($result as $key => $item) {
                        $insertArr1[] = [
                            'ItemID' => $item->ItemID,
                            'ParentID' => $item->ParentID,
                            'Level' => $item->Level,
                            'STT' => $stt . ($key + 1)
                        ];
                        DB::table('table_tmp')->where('ItemID', '=', $item->ItemID)->update(['STT' => $stt . ($key + 1)]);
                    }
                } else {
                    foreach ($insertArr[$i - $index - 1] as $value) {
                        $x = 1;
                        foreach ($result as $key => $item) {
                            if ($value['ItemID'] == $item->ParentID) {
                                $insertArr1[] = [
                                    'ItemID' => $item->ItemID,
                                    'ParentID' => $item->ParentID,
                                    'Level' => $item->Level,
                                    'STT' => $value['STT'] . '.' . $x,
                                ];
                                DB::table('table_tmp')->where('ItemID', '=', $item->ItemID)->update(['STT' => $value['STT'] . '.' . $x]);
                                $x++;
                            }
                        }
                    }
                }
                $insertArr[] = $insertArr1;
            }
        }
    }


}
