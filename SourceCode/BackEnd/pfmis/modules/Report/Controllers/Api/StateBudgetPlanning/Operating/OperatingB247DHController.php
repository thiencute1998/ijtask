<?php
namespace Module\Report\Controllers\StateBudgetPlanning\Operating;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;
use function Psy\debug;

class OperatingB247DHController extends Controller {
    use Common;
    public function Deleterownull($value){
        if(!$value){ $value= 1;}
        $sql = "DELETE FROM `table_tmp` WHERE  $value";
        DB::statement($sql);
    }
    public function SBPB247DH(Request $request)
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $Year = date("Y");
        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : $Year;
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

        $whereDirection = '';
        if ($directionID) {
            $whereDirection = 'AND A.DirectionID = ' . $directionID . ' ';
        }

        // TODO check isFinalTrans: AND A.isFinalTrans = 1

        $currentDate = date('Y-m-d');
        $whereDebt = " AND A.PeriodFromDate <= '". $currentDate ."' AND A.InTransTypeID NOT IN (16,17,18) ";
        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year - 1 AND $year AND A.InTransTypeID NOT IN (16,17,18) ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(50) DEFAULT '',
          `ItemID` varchar(100) NOT NULL PRIMARY KEY,
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `Year` varchar(5) DEFAULT '',
          `Project` varchar(50) DEFAULT '',
          `BuildAddress` varchar(255) DEFAULT '',
          `CapableDesign` varchar(255) DEFAULT '',
          `StartFinishDate` varchar(50) DEFAULT '',
          `SponsorName` varchar(50) DEFAULT '',
          `PacttdocDate` varchar(20) DEFAULT '',
          `InvestdocNo` varchar(50) DEFAULT '',
          `I1` double(20,4) DEFAULT 0 NOT NULL,
          `I2` double(20,4) DEFAULT 0 NOT NULL,
          `I3` double(20,4) DEFAULT 0 NOT NULL,
          `I4` double(20,4) DEFAULT 0 NOT NULL,
          `I5` double(20,4) DEFAULT 0 NOT NULL,
          `I6` double(20,4) DEFAULT 0 NOT NULL,
          `I7` double(20,4) DEFAULT 0 NOT NULL,
          `I8` double(20,4) DEFAULT 0 NOT NULL,
          `I9` double(20,4) DEFAULT 0 NOT NULL,
          `I10` double(20,4) DEFAULT 0 NOT NULL,
          `I11` double(20,4) DEFAULT 0 NOT NULL,
          `I12` double(20,4) DEFAULT 0 NOT NULL,
          `I13` double(20,4) DEFAULT 0 NOT NULL,
          `I14` double(20,4) DEFAULT 0 NOT NULL,
          `I15` double(20,4) DEFAULT 0 NOT NULL,
          `I16` double(20,4) DEFAULT 0 NOT NULL,
          `I17` double(20,4) DEFAULT 0 NOT NULL,
          `I18` double(20,4) DEFAULT 0 NOT NULL,
          `I19` double(20,4) DEFAULT 0 NOT NULL,
          `I20` double(20,4) DEFAULT 0 NOT NULL,
          `I21` double(20,4) DEFAULT 0 NOT NULL,
          `I22` double(20,4) DEFAULT 0 NOT NULL,
          `I23` double(20,4) DEFAULT 0 NOT NULL,
          `I24` double(20,4) DEFAULT 0 NOT NULL,
          `I25` double(20,4) DEFAULT 0 NOT NULL,
          `I26` double(20,4) DEFAULT 0 NOT NULL,
          `I27` double(20,4) DEFAULT 0 NOT NULL,
          `I28` double(20,4) DEFAULT 0 NOT NULL,
          `Sector` varchar(10) DEFAULT '',
          `ProjectStatusTemporary` varchar(10) DEFAULT '',
            `ProjectCompleteTemporary` varchar(10) DEFAULT '',
          `Detail` tinyint(1) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        // create temporary project status
        $this->createTemporaryProjectStatus();

        // create temporary project complete
        $this->createTemporaryProjectComplete();

        // tree data

        // Các dự án chuẩn bị đầu tư
        //Dư nợ
        $this->insertR1C10_11($whereDebt);
        $this->insertOrUpdateR1C12($whereDebt);
        $this->insertOrUpdateR1C13($whereDebt);
        $this->insertOrUpdateR1C15_16($whereDebt);
        $this->insertOrUpdateR1C19_20_28_29($whereCondition, $year);
        $this->insertOrUpdateR1C21_30($whereCondition, $year);
        $this->insertOrUpdateR1C22_31($whereCondition, $year);
        $this->insertOrUpdateR1C24_25_33_34($whereCondition, $year);

        $countRecords = DB::table('table_tmp')->count();

        // Các dự án hoàn thành đã phê duyệt quyết toán
        $this->insertR2_1_1C10_11($whereDebt);
        $this->insertOrUpdateR2_1_1C12($whereDebt);
        $this->insertOrUpdateR2_1_1C13($whereDebt);
        $this->insertOrUpdateR2_1_1C15_16($whereDebt);
        $this->insertOrUpdateR2_1_1C19_20_28_29($whereCondition, $year);
        $this->insertOrUpdateR2_1_1C21_30($whereCondition, $year);
        $this->insertOrUpdateR2_1_1C22_31($whereCondition, $year);
        $this->insertOrUpdateR2_1_1C24_25_33_34($whereCondition, $year);

        // Các dự án hoàn thành chưa phê duyệt quyết toán
        $this->insertR2_1_2C10_11($whereDebt, $year);
        $this->insertOrUpdateR2_1_2C12($whereDebt, $year);
        $this->insertOrUpdateR2_1_2C13($whereDebt, $year);
        $this->insertOrUpdateR2_1_2C15_16($whereDebt, $year);
        $this->insertR2_1_2C19_20_28_29($whereCondition, $year);
        $this->insertOrUpdateR2_1_2C21_30($whereCondition, $year);
        $this->insertOrUpdateR2_1_2C22_31($whereCondition, $year);
        $this->insertOrUpdateR2_1_2C24_25_33_34($whereCondition, $year);
        if(DB::table('table_tmp')->count() > $countRecords){
            $this->getParentDataTreeCommon(['sector',['ProjectStatusTemporary',1],'ProjectCompleteTemporary','project']);
            $countRecords = DB::table('table_tmp')->count();
        }

        // Các dự án chuyển tiếp
        $this->insertR2_2C10_11($whereDebt, $year);
        $this->insertOrUpdateR2_2C12($whereDebt, $year);
        $this->insertOrUpdateR2_2C13($whereDebt, $year);
        $this->insertOrUpdateR2_2C15_16($whereDebt, $year);
        $this->insertOrUpdateR2_2C19_20_28_29($whereCondition, $year);
        $this->insertOrUpdateR2_2C21_30($whereCondition, $year);
        $this->insertOrUpdateR2_2C22_31($whereCondition, $year);
        $this->insertOrUpdateR2_2C24_25_33_34($whereCondition, $year);
        if(DB::table('table_tmp')->count() != $countRecords){
            $this->getParentDataTreeCommon(['sector',['ProjectStatusTemporary',2],'project']);
            $countRecords = DB::table('table_tmp')->count();
        }

        // Các dự án khởi công mới
        $this->insertR2_3C10_11($whereDebt, $year);
        $this->insertOrUpdateR2_3C12($whereDebt, $year);
        $this->insertOrUpdateR2_3C13($whereDebt, $year);
        $this->insertOrUpdateR2_3C15_16($whereDebt, $year);
        $this->insertOrUpdateR2_3C19_20_28_29($whereCondition, $year);
        $this->insertOrUpdateR2_3C21_30($whereCondition, $year);
        $this->insertOrUpdateR2_3C22_31($whereCondition, $year);
        $this->insertOrUpdateR2_3C24_25_33_34($whereCondition, $year);
        if(DB::table('table_tmp')->count() != $countRecords){
            $this->getParentDataTreeCommon(['sector',['ProjectStatusTemporary',3],'project']);
        }

        $this->setLevel();


        // Thiết lập STT
        $this->setIndexSBP247DH();

        // Sum Row
        $this->sumUpRow();

        // Thêm dữ liệu mặc định
        $insertArr1 = [
            ['STT'=>'', 'ItemID'=> 'A', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG SỐ', 'FontWeight'=> '1'],
            ['STT'=>'I', 'ItemID'=> 'A#01', 'ParentID'=> 'A', 'Level'=> 1, 'ItemName'=> 'Vốn chuẩn bị đầu tư', 'FontWeight'=> '1'],
            ['STT'=>'II', 'ItemID'=> 'A#02', 'ParentID'=> 'A', 'Level'=> 1, 'ItemName'=> 'Vốn thực hiện đầu tư', 'FontWeight'=> '1']
        ];
        DB::table('table_tmp')->insert($insertArr1);

        // Sum Column
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $this->sumUpSBPB247DH($maxLevel->maxLevel);

        //Xóa dòng trống
        //$checknull1="(LEFT(ItemID, 2)='A#' OR LEFT(ItemID, 2)='B#') AND I1=0 AND I2=0 AND I3=0 AND I4=0";
        //$this->Deleterownull($checknull1);
        //

        $data = DB::table("table_tmp")->orderBy('ItemID', 'asc')->get();

        $this->formatReport($data);

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Lũy kế vốn đã bố trí từ khởi công đến hết năm ' . ($year - 1);
            $data[0]->HeaderName2 = 'Kế hoạch vốn năm ' . $year;
        }

        if (isset($filter['CompanyID'])) {
            $note .= 'đơn vị ' . $filter['CompanyName'];
            $company = Company::where('CompanyID',$filter['CompanyID'])->first();
        }
        else{
            $company = Company::where('CompanyID',Auth::user()->CompanyID)->first();
        }

        $companyName = $company->CompanyName;
        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $dateTime = "Vĩnh Phú, ngày $date tháng $month năm $yearC";
        if ($company) {
            $dateTime = $company->Address . ", ngày $date tháng $month năm $yearC";
        }
        $response = [
            'master' => [
                "Year" => "",
                "CreateName" => Auth::user()->FullName,
                "TransNo" => "00001",
                "ReportName" => "DỰ TOÁN CHI ĐẦU TƯ TỪ NGUỒN VỐN ODA VÀ VỐN VAY ƯU ĐÃI NĂM " . $year,
                "DateTime" => $dateTime,
                "ParentCompanyName" => 'UBND tỉnh Vĩnh Phú',
                "CompanyName" => $companyName,
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function insertR1C10_11($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, Detail, I3, I4)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS I3,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) THEN I ELSE 0 END) AS I4
        FROM
        (SELECT CONCAT('A#','01') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, SUM(LCDebitAmount - LCCreditAmount) as I, A.BudgetLevel
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo = '091'
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 1
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project
        ";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C12($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, Detail, I5)
        SELECT * FROM
        (SELECT CONCAT('A#','01#', A.ProjectNo) AS ItemID, CONCAT('A#','01') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, 1, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo = '091'
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 1
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I5 = I
        ";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C13($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, Detail, I6)
        SELECT * FROM
        (SELECT CONCAT('A#','01#', A.ProjectNo) AS ItemID, CONCAT('A#','01') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, 1, SUM(FCDebitAmount - FCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo = '091'
        AND A.CcyID != 34
        AND PC.CateNo = '009' AND PC.CateValue = 1
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I6 = I
        ";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C15_16($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, Detail, I8, I9)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','01') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, SUM(LCDebitAmount - LCCreditAmount) as I, A.BudgetLevel
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo = '091'
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 1
        AND A.CcyID != 34
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I8 = AA, I9 = BB
        ";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C19_20_28_29($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, Detail, I12, I13, I21, I22)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('008','009','012','013') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo IN ('008','009','012','013') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS DD
        FROM
        (SELECT CONCAT('A#','01') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, SUM(LCDebitAmount) as I, A.BudgetLevel, A.AccountNo, YEAR(A.PeriodFromDate) as YearD
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo IN ('008','009','012','013','0411')
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 1
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I12 = AA, I13 = BB, I21 = CC, I22 = DD
        ";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C21_30($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, Detail, I14, I23)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, 1,
        SUM(CASE WHEN AccountNo IN ('014','018') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0412' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','01') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, SUM(LCDebitAmount) as I, A.AccountNo, YEAR(A.PeriodFromDate) as YearD
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo IN ('014','018','0412')
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 1
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I14 = AA, I23 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C22_31($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, Detail, I15, I24)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, 1,
        SUM(CASE WHEN AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','01') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, SUM(FCDebitAmount) as I, A.AccountNo, YEAR(A.PeriodFromDate) as YearD
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo IN ('006','112','0411')
        AND PC.CateNo = '009' AND PC.CateValue = 1
        AND A.CcyID != 34
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I15 = AA, I24 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C24_25_33_34($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, Detail, I17, I18, I26, I27)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo, 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0412' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo = '0412' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS DD
        FROM
        (SELECT CONCAT('A#','01') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, SUM(LCDebitAmount) as I, A.AccountNo, A.BudgetLevel, YEAR(A.PeriodFromDate) as YearD
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo IN ('006','112','0411')
        AND PC.CateNo = '009' AND PC.CateValue = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND A.CcyID != 34
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, A.BudgetLevel, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I17 = AA, I18 = BB, I26 = CC, I27 = DD";

        DB::statement($sql);
    }

    public function insertR2_1_1C10_11($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I3, I4)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '1', '1', 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS I3,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) THEN I ELSE 0 END) AS I4
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#1') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.SectorNo as Sector, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo = '091'
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.SettlementDate <= A.TransDate
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_1C12($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I5)
        SELECT * FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#1#', A.ProjectNo) AS ItemID ,CONCAT('A#','02#', A.SectorNo, '#1', '#1') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.SectorNo as Sector, '1' as ProjectStatusTemporary,
        '1' as ProjectCompleteTemporary, 1, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo = '091'
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.SettlementDate <= A.TransDate
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I5 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_1C13($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I6)
        SELECT * FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#1#', A.ProjectNo) AS ItemID ,CONCAT('A#','02#', A.SectorNo, '#1', '#1') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.SectorNo as Sector, '1' as ProjectStatusTemporary,
        '1' as ProjectCompleteTemporary, 1, SUM(FCDebitAmount - FCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo = '091'
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND P.SettlementDate <= A.TransDate
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I6 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_1C15_16($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I8, I9)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '1' as ProjectCompleteTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#1') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.SectorNo as Sector, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo = '091'
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND P.SettlementDate <= A.TransDate
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I8 = AA, I9 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_1C19_20_28_29($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I12, I13, I21, I22)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '1' as ProjectCompleteTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('008','009','012','013') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo IN ('008','009','012','013') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS DD
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#1') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo IN ('008','009','012','013','0411')
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.SettlementDate <= A.TransDate
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I12 = AA, I13 = BB, I21 = CC, I22 = DD";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_1C21_30($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I14, I23)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '1' as ProjectCompleteTemporary, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('014','018') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0412' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#1') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo IN ('014','018','0412')
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.SettlementDate <= A.TransDate
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I14 = AA, I23 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_1C22_31($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I15, I24)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '1' as ProjectCompleteTemporary, 1,
        SUM(CASE WHEN AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#1') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(FCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo IN ('006','112','0411')
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND P.SettlementDate <= A.TransDate
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project)  as DT ON DUPLICATE KEY UPDATE I15 = AA, I24 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_1C24_25_33_34($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I17, I18, I26, I27)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '1' as ProjectCompleteTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS DD
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#1') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo IN ('006','112','0411')
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND P.SettlementDate <= A.TransDate
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I17 = AA, I18 = BB, I26 = CC, I27 = DD";

        DB::statement($sql);
    }

    public function insertR2_1_2C10_11($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I3, I4)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '1', '2', 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS I3,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) THEN I ELSE 0 END) AS I4
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.SectorNo as Sector, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo = '091'
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND YEAR(P.FinishedDate) <= $year
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_2C12($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I5)
        SELECT * FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#2#', A.ProjectNo) AS ItemID, CONCAT('A#','02#', A.SectorNo, '#1', '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.SectorNo as Sector, '1' as ProjectStatusTemporary,
        '2' as ProjectCompleteTemporary, 1, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo = '091'
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND YEAR(P.FinishedDate) <= $year
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I5 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_2C13($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I6)
        SELECT * FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#2#', A.ProjectNo) AS ItemID, CONCAT('A#','02#', A.SectorNo, '#1', '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.SectorNo as Sector, '1' as ProjectStatusTemporary,
        '2' as ProjectCompleteTemporary, 1, SUM(FCDebitAmount - FCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo = '091'
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND YEAR(P.FinishedDate) <= $year
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I6 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_2C15_16($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I8, I9)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '2' as ProjectCompleteTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.SectorNo as Sector, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo = '091'
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND YEAR(P.FinishedDate) <= $year
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I8 = AA, I9 = BB";

        DB::statement($sql);
    }

    public function insertR2_1_2C19_20_28_29($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I12, I13, I21, I22)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '2' as ProjectCompleteTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('008','009','012','013') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo IN ('008','009','012','013') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS DD
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo IN ('008','009','012','013','0411')
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND YEAR(P.FinishedDate) <= $year
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I12 = AA, I13 = BB, I21 = CC, I22 = DD";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_2C21_30($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I14, I23)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '2' as ProjectCompleteTemporary, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('014','018') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0412' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo IN ('014','018','0412')
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND YEAR(P.FinishedDate) <= $year
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I14 = AA, I23 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_2C22_31($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I15, I24)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '2' as ProjectCompleteTemporary, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(FCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo IN ('006','112','0411')
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND YEAR(P.FinishedDate) <= $year
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I15 = AA, I24 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_2C24_25_33_34($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I17, I18, I26, I27)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '2' as ProjectCompleteTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS DD
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#1', '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo IN ('006','112','0411')
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND YEAR(P.FinishedDate) <= $year
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I17 = AA, I18 = BB, I26 = CC, I27 = DD";

        DB::statement($sql);
    }

    public function insertR2_2C10_11($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I3, I4)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '2', 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS I3,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) THEN I ELSE 0 END) AS I4
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.SectorNo as Sector, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo = '091'
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND YEAR(P.StartedDate) < $year AND YEAR(P.FinishedDate) > $year
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_2C12($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I5)
        SELECT * FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#2#', A.ProjectNo) AS ItemID, CONCAT('A#','02#', A.SectorNo, '#2') AS ParentID,
        A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.SectorNo as Sector,
        '2' as ProjectStatusTemporary, 1, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo = '091'
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND YEAR(P.StartedDate) < $year AND YEAR(P.FinishedDate) > $year
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I5 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_2C13($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I6)
        SELECT * FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#2#', A.ProjectNo) AS ItemID, CONCAT('A#','02#', A.SectorNo, '#2') AS ParentID,
        A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.SectorNo as Sector,
        '2' as ProjectStatusTemporary, 1, SUM(FCDebitAmount - FCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo = '091'
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND YEAR(P.StartedDate) < $year AND YEAR(P.FinishedDate) > $year
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I6 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_2C15_16($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I8, I9)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '2' as ProjectStatusTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.SectorNo as Sector, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo = '091'
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND YEAR(P.StartedDate) < $year AND YEAR(P.FinishedDate) > $year
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I8 = AA, I9 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_2C19_20_28_29($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I12, I13, I21, I22)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '2', 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('008','009','012','013') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo IN ('008','009','012','013') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS DD
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo IN ('008','009','012','013','0411')
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND YEAR(P.StartedDate) < $year AND YEAR(P.FinishedDate) > $year
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I12 = AA, I13 = BB, I21 = CC, I22 = DD";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_2C21_30($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I14, I23)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '2', 1,
        SUM(CASE WHEN AccountNo IN ('014','018') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0412' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo IN ('014','018','0412')
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND YEAR(P.StartedDate) < $year AND YEAR(P.FinishedDate) > $year
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I14 = AA, I23 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_2C22_31($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I15, I24)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '2', 1,
        SUM(CASE WHEN AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(FCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo IN ('006','112','0411')
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND YEAR(P.StartedDate) < $year AND YEAR(P.FinishedDate) > $year
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I15 = AA, I24 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_2C24_25_33_34($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I17, I18, I26, I27)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '2', 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS DD
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo IN ('006','112','0411')
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND YEAR(P.StartedDate) < $year AND YEAR(P.FinishedDate) > $year
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I17 = AA, I18 = BB, I26 = CC, I27 = DD";

        DB::statement($sql);
    }

    public function insertR2_3C10_11($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I3, I4)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '3', 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS I3,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) THEN I ELSE 0 END) AS I4
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#3') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.SectorNo as Sector, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo = '091'
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND YEAR(P.ExpectedStartDate) = $year
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_3C12($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I5)
        SELECT * FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#3#', A.ProjectNo) AS ItemID, CONCAT('A#','02#', A.SectorNo, '#3') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.SectorNo as Sector, '3' as ProjectStatusTemporary, 1,
        SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo = '091'
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND YEAR(P.ExpectedStartDate) = $year
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I5 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_3C13($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I6)
        SELECT * FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#3#', A.ProjectNo) AS ItemID, CONCAT('A#','02#', A.SectorNo, '#3') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.SectorNo as Sector, '3' as ProjectStatusTemporary, 1,
        SUM(FCDebitAmount - FCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo = '091'
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND YEAR(P.ExpectedStartDate) = $year
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I6 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_3C15_16($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I8, I9)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '3' as ProjectStatusTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#3') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.SectorNo as Sector, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo = '091'
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND YEAR(P.ExpectedStartDate) = $year
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I8 = AA, I9 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_3C19_20_28_29($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I12, I13, I21, I22)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '3', 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('008','009','012','013') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo IN ('008','009','012','013') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS DD
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#3') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo IN ('008','009','012','013','0411')
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND YEAR(P.ExpectedStartDate) = $year
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I12 = AA, I13 = BB, I21 = CC, I22 = DD";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_3C21_30($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I14, I23)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '3', 1,
        SUM(CASE WHEN AccountNo IN ('014','018') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0412' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#3') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 1
        AND A.AccountNo IN ('014','018','0412')
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND YEAR(P.ExpectedStartDate) = $year
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I14 = AA, I23 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_3C22_31($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I15, I24)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '3', 1,
        SUM(CASE WHEN AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#3') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(FCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo IN ('006','112','0411')
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND YEAR(P.ExpectedStartDate) = $year
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I15 = AA, I24 = BB";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_3C24_25_33_34($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I17, I18, I26, I27)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, SponsorName, PacttdocDate, InvestdocNo,
        Sector, '3', 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo IN ('006','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel IN (2,3,4) AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS DD
        FROM
        (SELECT CONCAT('A#','02#', A.SectorNo, '#3') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        SponsorName, PacttdocDate, InvestdocNo, A.BudgetLevel, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE C.CapitalInOut = 2
        AND A.AccountNo IN ('006','112','0411')
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND A.CcyID != 34
        AND YEAR(P.ExpectedStartDate) = $year
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I17 = AA, I18 = BB, I26 = CC, I27 = DD";

        DB::statement($sql);
    }

    public function createTemporaryProjectStatus(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `ProjectStatusTemporary` (
          `ItemID` varchar(10) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $insert = [
            ['ItemID' => '1','ItemName'=> 'Các dự án hoàn thành'],
            ['ItemID' => '2','ItemName'=> 'Các dự án chuyển tiếp'],
            ['ItemID' => '3','ItemName'=> 'Các dự án khởi công mới']
        ];
        DB::table('ProjectStatusTemporary')->insert($insert);
    }

    public function createTemporaryProjectComplete(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `ProjectCompleteTemporary` (
          `ItemID` varchar(10) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $insert = [
            ['ItemID' => '1','ItemName'=> 'Các dự án hoàn thành đã phê duyệt quyết toán'],
            ['ItemID' => '2','ItemName'=> 'Các dự án hoàn thành chưa phê duyệt quyết toán']
        ];
        DB::table('ProjectCompleteTemporary')->insert($insert);
    }

    public function sumUpRow(){
        $listItems = DB::table('table_tmp')->where('Detail','=',1)->get();
        foreach($listItems as $item){
            $I2 = $item->I3 + $item->I4 + $item->I5;
            $I11 = $item->I12 + $item->I13 + $item->I14;
            $I20 = $item->I21 + $item->I22 + $item->I23;
            $I7 = $item->I8 + $item->I9;
            $I16 = $item->I17 + $item->I18;
            $I25 = $item->I26 + $item->I27;
            $I1 = $I2 + $I7;
            $I10 = $I11 + $I16;
            $I19 = $I20 + $I25;
            DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update([
                'I2'=> $I2, 'I11'=> $I11, 'I20'=> $I20,
                'I7'=> $I7, 'I16'=> $I16, 'I25'=> $I25,
                'I1'=> $I1, 'I10'=> $I10, 'I19'=> $I19,
            ]);
        }

    }

    public function sumUpSBPB247DH($maxLevel, $start = 0){
        for ($i = $maxLevel; $i >= $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
            SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8,
            SUM(M.I9) AS I9, SUM(M.I10) AS I10, SUM(M.I11) AS I11, SUM(M.I12) AS I12,
            SUM(M.I13) AS I13, SUM(M.I14) AS I14, SUM(M.I15) AS I15, SUM(M.I16) AS I16,
            SUM(M.I17) AS I17, SUM(M.I18) AS I18, SUM(M.I19) AS I19, SUM(M.I20) AS I20,
            SUM(M.I21) AS I21, SUM(M.I22) AS I22, SUM(M.I23) AS I23, SUM(M.I24) AS I24,
            SUM(M.I25) AS I25, SUM(M.I26) AS I26, SUM(M.I27) AS I27, SUM(M.I28) AS I28
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
                    $update = "UPDATE table_tmp
                SET I1 = $I1, I2 = $I2 , I3 = $I3, I4 = $I4,
                    I5 = $I5, I6 = $I6 , I7 = $I7, I8 = $I8,
                    I9 = $I9, I10 = $I10 , I11 = $I11, I12 = $I12,
                    I13 = $I13, I14 = $I14 , I15 = $I15, I16 = $I16,
                    I17 = $I17, I18 = $I18 , I19 = $I19, I20 = $I20,
                    I21 = $I21, I22 = $I22 , I23 = $I23, I24 = $I24,
                    I25 = $I25, I26 = $I26 , I27 = $I27, I28 = $I28
                WHERE ItemID = '".$value['ParentID']."'
                    AND table_tmp.Level = $j";
                    DB::statement($update);
                }
            }
        }

    }

    public function setIndexSBP247DH(){
        $insertArr = [];
        $maxLevel = 4;
//        $maxLevel = DB::table('table_tmp')->select(DB::raw('Max(Level) as maxLevel'))->first()->maxLevel;
        for($i = 2; $i <= $maxLevel; $i++){
            $insertArr1 = [];
            $sql = "SELECT * FROM table_tmp WHERE Level = $i ORDER BY ItemID ASC";
            $result = DB::select($sql);
            if($i == 2){
                $index1 = 1;
                $index2 = 1;
                foreach($result as $item){
                    if(substr($item->ItemID, 0, 4) == 'A#01'){
                        $stt = $index1;
                        $index1++;
                    }
                    else{
                        $stt = 'II.' . $index2;
                        $index2++;
                        $insertArr1[] = [
                            'ItemID'=> $item->ItemID,
                            'ParentID'=> $item->ParentID
                        ];
                    }
                    DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=> $stt]);
                }
            }
            else{
                if($result){
                    foreach($insertArr[$i-3] as $value){
                        $index = 1;
                        foreach($result as $key=> $item){
                            if($value['ItemID'] == $item->ParentID){
                                $insertArr1[] = [
                                    'ItemID'=> $item->ItemID,
                                    'ParentID'=> $item->ParentID
                                ];
                                if($i == 3){
                                    $stt = $index;
                                }
                                if($i == 4){
                                    $stt = '1.' . $index;
                                }
                                DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=>$stt]);
                                $index++;
                            }

                        }
                    }
                }
            }
            $insertArr[] = $insertArr1;
        }
    }

    public function formatReport($data){
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
        }
    }


}
