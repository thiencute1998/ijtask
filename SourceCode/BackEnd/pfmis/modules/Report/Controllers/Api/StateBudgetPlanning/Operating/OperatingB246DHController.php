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

class OperatingB246DHController extends Controller {
    use Common;
    public function Deleterownull($value){
        if(!$value){ $value= 1;}
        $sql = "DELETE FROM `table_tmp` WHERE  $value";
        DB::statement($sql);
    }
    public function SBPB246DH(Request $request)
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
        $whereDebt = " AND A.PeriodFromDate <= '". $currentDate ."' AND A.InTransTypeID NOT IN (16,17,18) AND PC.CateNo = '006' AND PC.CateValue = 1 ";
        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year - 1 AND $year AND A.InTransTypeID NOT IN (16,17,18)  AND PC.CateNo = '006' AND PC.CateValue = 1 ";

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
        $this->insertR1C8_9_10_11($whereDebt);
        $this->insertOrUpdateR1C12($whereDebt);
        $this->insertOrUpdateR1C15_16_17_18_22_23_24_25($whereCondition, $year);
        $this->insertOrUpdateR1C19_26($whereCondition, $year);

        $countRecords = DB::table('table_tmp')->count();

        // Các dự án hoàn thành đã phê duyệt quyết toán
        $this->insertR2_1_1C8_9_10_11($whereDebt);
        $this->insertOrUpdateR2_1_1C12($whereDebt);
        $this->insertOrUpdateR2_1_1C15_16_17_18_22_23_24_25($whereCondition, $year);
        $this->insertOrUpdateR2_1_1C19_26($whereCondition, $year);

        // Các dự án hoàn thành chưa phê duyệt quyết toán
        $this->insertR2_1_2C8_9_10_11($whereDebt, $year);
        $this->insertOrUpdateR2_1_2C12($whereDebt, $year);
        $this->insertR2_1_2C15_16_17_18_22_23_24_25($whereCondition, $year);
        $this->insertOrUpdateR2_1_2C19_26($whereCondition, $year);
        if(DB::table('table_tmp')->count() > $countRecords){
            $this->getParentDataTreeCommon(['sector',['ProjectStatusTemporary',1],'ProjectCompleteTemporary','project'],'I#A');
            $this->getParentDataTreeCommon(['sector',['ProjectStatusTemporary',1],'ProjectCompleteTemporary','project'],'I#B');
            $countRecords = DB::table('table_tmp')->count();
        }

        // Các dự án chuyển tiếp
        $this->insertR2_2C8_9_10_11($whereDebt, $year);
        $this->insertOrUpdateR2_2C12($whereDebt, $year);
        $this->insertOrUpdateR2_2C15_16_17_18_22_23_24_25($whereCondition, $year);
        $this->insertOrUpdateR2_2C19_26($whereCondition, $year);
        if(DB::table('table_tmp')->count() != $countRecords){
            $this->getParentDataTreeCommon(['sector',['ProjectStatusTemporary',2],'project'],'I#A');
            $this->getParentDataTreeCommon(['sector',['ProjectStatusTemporary',2],'project'],'I#B');
            $countRecords = DB::table('table_tmp')->count();
        }

        // Các dự án khởi công mới
        $this->insertR2_3C8_9_10_11($whereDebt, $year);
        $this->insertOrUpdateR2_3C12($whereDebt, $year);
        $this->insertOrUpdateR2_3C15_16_17_18_22_23_24_25($whereCondition, $year);
        $this->insertOrUpdateR2_3C19_26($whereCondition, $year);
        if(DB::table('table_tmp')->count() != $countRecords){
            $this->getParentDataTreeCommon(['sector',['ProjectStatusTemporary',3],'project'], 'I#A');
            $this->getParentDataTreeCommon(['sector',['ProjectStatusTemporary',3],'project'], 'I#B');
        }

        $this->setLevel();


        // Thiết lập STT
        $this->setIndexSBP246DH('I#A');
        $this->setIndexSBP246DH('I#B');
        // Sum Row
        $this->sumUpRow();

        // Thêm dữ liệu mặc định
        $insertArr1 = [
            ['STT'=>'', 'ItemID'=> 'I', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG SỐ', 'FontWeight'=> '1'],
            ['STT'=>'A', 'ItemID'=> 'I#A', 'ParentID'=> 'I', 'Level'=> 1, 'ItemName'=> 'Cấp tỉnh quản lý', 'FontWeight'=> '1'],
            ['STT'=>'I', 'ItemID'=> 'I#A#01', 'ParentID'=> 'I#A', 'Level'=> 2, 'ItemName'=> 'Vốn chuẩn bị đầu tư', 'FontWeight'=> '1'],
            ['STT'=>'II', 'ItemID'=> 'I#A#02', 'ParentID'=> 'I#A', 'Level'=> 2, 'ItemName'=> 'Vốn thực hiện đầu tư', 'FontWeight'=> '1'],
            ['STT'=>'B', 'ItemID'=> 'I#B', 'ParentID'=> 'I', 'Level'=> 1, 'ItemName'=> 'Cấp huyện quản lý', 'FontWeight'=> '1'],
            ['STT'=>'I', 'ItemID'=> 'I#B#01', 'ParentID'=> 'I#B', 'Level'=> 2, 'ItemName'=> 'Vốn chuẩn bị đầu tư', 'FontWeight'=> '1'],
            ['STT'=>'II', 'ItemID'=> 'I#B#02', 'ParentID'=> 'I#B', 'Level'=> 2, 'ItemName'=> 'Vốn thực hiện đầu tư', 'FontWeight'=> '1']
        ];
        DB::table('table_tmp')->insert($insertArr1);

        // Sum Column
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $this->sumUpSBPB246DH($maxLevel->maxLevel);

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

    public function insertR1C8_9_10_11($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo, Detail, I3, I4, I5, I6)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo, 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS I3,
        SUM(CASE WHEN BudgetLevel = 2 THEN I ELSE 0 END) AS I4,
        SUM(CASE WHEN BudgetLevel = 3 THEN I ELSE 0 END) AS I5,
        SUM(CASE WHEN BudgetLevel = 4 THEN I ELSE 0 END) AS I6
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'01') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, SUM(LCDebitAmount - LCCreditAmount) as I, A.BudgetLevel
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo = '091'
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND P.ManagementLevel IN (2,3)
        AND PC.CateNo = '009' AND PC.CateValue = 1
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project
        ";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C12($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo, Detail, I7)
        SELECT * FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END, '01#', A.ProjectNo) AS ItemID,
        CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'01') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, 1, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo = '091'
        AND C.BudgetStateType = 2
        AND P.ManagementLevel IN (2,3)
        AND PC.CateNo = '009' AND PC.CateValue = 1
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I7 = I
        ";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C15_16_17_18_22_23_24_25($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo, Detail, I10, I11, I12, I13, I17, I18, I19, I20)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo, 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel = 2 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 3 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel = 4 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS DD,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS EE,
        SUM(CASE WHEN BudgetLevel = 2 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS FF,
        SUM(CASE WHEN BudgetLevel = 3 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS GG,
        SUM(CASE WHEN BudgetLevel = 4 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS II
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'01') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, SUM(LCDebitAmount) as I, A.BudgetLevel, A.AccountNo, YEAR(A.PeriodFromDate) as YearD
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo IN ('008','009','012','013','112','0411')
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND P.ManagementLevel IN (2,3)
        AND PC.CateNo = '009' AND PC.CateValue = 1
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE
        I10 = AA, I11 = BB, I12 = CC, I13 = DD, I17 = EE, I18 = FF, I19 = GG, I20 = II
        ";

        DB::statement($sql);
    }

    public function insertOrUpdateR1C19_26($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo, Detail, I14, I21)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo, 1,
        SUM(CASE WHEN AccountNo IN ('014','018') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0412' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'01') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, SUM(LCDebitAmount) as I, A.AccountNo, YEAR(A.PeriodFromDate) as YearD
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo IN ('014','018','0412')
        AND C.BudgetStateType = 2
        AND P.ManagementLevel IN (2,3)
        AND PC.CateNo = '009' AND PC.CateValue = 1
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I14 = AA, I21 = BB";

        DB::statement($sql);
    }

    public function insertR2_1_1C8_9_10_11($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I3, I4, I5, I6)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '1' as ProjectCompleteTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS I3,
        SUM(CASE WHEN BudgetLevel = 2 THEN I ELSE 0 END) AS I4,
        SUM(CASE WHEN BudgetLevel = 3 THEN I ELSE 0 END) AS I5,
        SUM(CASE WHEN BudgetLevel = 4 THEN I ELSE 0 END) AS I6
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#1', '#1') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.BudgetLevel, A.SectorNo as Sector, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo = '091'
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND P.SettlementDate <= A.TransDate
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_1C12($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I7)
        SELECT * FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END, '01#', A.ProjectNo) AS ItemID,
        CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'01') AS ParentID,
        A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.SectorNo as Sector, '1' as ProjectStatusTemporary,
        '1' as ProjectCompleteTemporary, 1, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo = '091'
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND P.SettlementDate <= A.TransDate
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I7 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_1C15_16_17_18_22_23_24_25($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I10, I11, I12, I13, I17, I18, I19, I20)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '1' as ProjectCompleteTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel = 2 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 3 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel = 4 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS DD,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS EE,
        SUM(CASE WHEN BudgetLevel = 2 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS FF,
        SUM(CASE WHEN BudgetLevel = 3 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS GG,
        SUM(CASE WHEN BudgetLevel = 4 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS II
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#1', '#1') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.BudgetLevel, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo IN ('008','009','012','013','112','0411')
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND P.SettlementDate <= A.TransDate
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE
        I10 = AA, I11 = BB, I12 = CC, I13 = DD, I17 = EE, I18 = FF, I19 = GG, I20 = II
        ";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_1C19_26($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I14, I21)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '1' as ProjectCompleteTemporary, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('014','018') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0412' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#1', '#1') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo IN ('014','018','0412')
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND P.SettlementDate <= A.TransDate
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I14 = AA, I21 = BB";

        DB::statement($sql);
    }

    public function insertR2_1_2C8_9_10_11($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I3, I4, I5, I6)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '2' as ProjectCompleteTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS I3,
        SUM(CASE WHEN BudgetLevel = 2 THEN I ELSE 0 END) AS I4,
        SUM(CASE WHEN BudgetLevel = 3 THEN I ELSE 0 END) AS I5,
        SUM(CASE WHEN BudgetLevel = 4 THEN I ELSE 0 END) AS I6
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#1', '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.BudgetLevel, A.SectorNo as Sector, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo = '091'
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND YEAR(P.FinishedDate) <= $year
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_2C12($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I7)
        SELECT * FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#1', '#2#', A.ProjectNo) AS ItemID,
        CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#1', '#2') AS ParentID,
        A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.SectorNo as Sector, '1' as ProjectStatusTemporary,
        '2' as ProjectCompleteTemporary, 1, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo = '091'
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND YEAR(P.FinishedDate) <= $year
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I7 = I";

        DB::statement($sql);
    }

    public function insertR2_1_2C15_16_17_18_22_23_24_25($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I10, I11, I12, I13, I17, I18, I19, I20)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '2' as ProjectCompleteTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel = 2 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 3 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel = 4 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS DD,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS EE,
        SUM(CASE WHEN BudgetLevel = 2 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS FF,
        SUM(CASE WHEN BudgetLevel = 3 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS GG,
        SUM(CASE WHEN BudgetLevel = 4 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS II
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#1', '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.BudgetLevel, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo IN ('008','009','012','013','112','0411')
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND YEAR(P.FinishedDate) <= $year
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE
        I10 = AA, I11 = BB, I12 = CC, I13 = DD, I17 = EE, I18 = FF, I19 = GG, I20 = II
        ";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_1_2C19_26($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, ProjectCompleteTemporary, Detail, I14, I21)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, '1' as ProjectStatusTemporary, '2' as ProjectCompleteTemporary, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('014','018') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0412' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#1', '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo IN ('014','018','0412')
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND YEAR(P.FinishedDate) <= $year
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I14 = AA, I21 = BB";

        DB::statement($sql);
    }

    public function insertR2_2C8_9_10_11($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I3, I4, I5, I6)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, '2' as ProjectStatusTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS I3,
        SUM(CASE WHEN BudgetLevel = 2 THEN I ELSE 0 END) AS I4,
        SUM(CASE WHEN BudgetLevel = 3 THEN I ELSE 0 END) AS I5,
        SUM(CASE WHEN BudgetLevel = 4 THEN I ELSE 0 END) AS I6
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.BudgetLevel, A.SectorNo as Sector, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo = '091'
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND YEAR(P.StartedDate) < $year AND YEAR(P.FinishedDate) > $year
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_2C12($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I7)
        SELECT * FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#1', '#2#', A.ProjectNo) AS ItemID,
        CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#1', '#2') AS ParentID,
        A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.SectorNo as Sector,
        '2' as ProjectStatusTemporary, 1, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo = '091'
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND YEAR(P.StartedDate) < $year AND YEAR(P.FinishedDate) > $year
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I7 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_2C15_16_17_18_22_23_24_25($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I10, I11, I12, I13, I17, I18, I19, I20)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, '2' as ProjectStatusTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel = 2 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 3 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel = 4 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS DD,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS EE,
        SUM(CASE WHEN BudgetLevel = 2 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS FF,
        SUM(CASE WHEN BudgetLevel = 3 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS GG,
        SUM(CASE WHEN BudgetLevel = 4 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS II
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.BudgetLevel, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo IN ('008','009','012','013','112','0411')
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND YEAR(P.StartedDate) < $year AND YEAR(P.FinishedDate) > $year
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE
        I10 = AA, I11 = BB, I12 = CC, I13 = DD, I17 = EE, I18 = FF, I19 = GG, I20 = II
        ";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_2C19_26($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I14, I21)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, '2' as ProjectStatusTemporary, 1,
        SUM(CASE WHEN AccountNo IN ('014','018') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0412' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#2') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo IN ('014','018','0412')
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND YEAR(P.StartedDate) < $year AND YEAR(P.FinishedDate) > $year
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I14 = AA, I21 = BB";

        DB::statement($sql);
    }

    public function insertR2_3C8_9_10_11($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I3, I4, I5, I6)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, '3' as ProjectStatusTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 THEN I ELSE 0 END) AS I3,
        SUM(CASE WHEN BudgetLevel = 2 THEN I ELSE 0 END) AS I4,
        SUM(CASE WHEN BudgetLevel = 3 THEN I ELSE 0 END) AS I5,
        SUM(CASE WHEN BudgetLevel = 4 THEN I ELSE 0 END) AS I6
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#3') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.BudgetLevel, A.SectorNo as Sector, SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo = '091'
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND YEAR(P.ExpectedStartDate) = $year
        $whereDebt
        GROUP BY P.ProjectNo, A.BudgetLevel
        ) as CH GROUP BY Project";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_3C12($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I7)
        SELECT * FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#3#', A.ProjectNo) AS ItemID,
        CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#3') AS ParentID,
        A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.SectorNo as Sector, '3' as ProjectStatusTemporary, 1,
        SUM(LCDebitAmount - LCCreditAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo = '091'
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND YEAR(P.ExpectedStartDate) = $year
        $whereDebt
        GROUP BY P.ProjectNo
        ) as DT ON DUPLICATE KEY UPDATE I7 = I";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_3C15_16_17_18_22_23_24_25($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I10, I11, I12, I13, I17, I18, I19, I20)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, '3' as ProjectStatusTemporary, 1,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN BudgetLevel = 2 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS BB,
        SUM(CASE WHEN BudgetLevel = 3 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS CC,
        SUM(CASE WHEN BudgetLevel = 4 AND AccountNo IN ('008','009','012','013','112') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS DD,
        SUM(CASE WHEN BudgetLevel = 1 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS EE,
        SUM(CASE WHEN BudgetLevel = 2 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS FF,
        SUM(CASE WHEN BudgetLevel = 3 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS GG,
        SUM(CASE WHEN BudgetLevel = 4 AND AccountNo = '0411' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS II
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#3') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.BudgetLevel, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo IN ('008','009','012','013','112','0411')
        AND C.BudgetStateType = 1
        AND A.BudgetLevel IN (1,2,3,4)
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND YEAR(P.ExpectedStartDate) = $year
        $whereCondition
        GROUP BY P.ProjectNo, A.BudgetLevel, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE
        I10 = AA, I11 = BB, I12 = CC, I13 = DD, I17 = EE, I18 = FF, I19 = GG, I20 = II
        ";

        DB::statement($sql);
    }

    public function insertOrUpdateR2_3C19_26($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, ProjectStatusTemporary, Detail, I14, I21)
        SELECT * FROM
        (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project, BuildAddress, CapableDesign, StartFinishDate, InvestdocNo,
        Sector, '3', 1,
        SUM(CASE WHEN AccountNo IN ('014','018') AND YearD = '" . ($year - 1) . "' THEN I ELSE 0 END) AS AA,
        SUM(CASE WHEN AccountNo = '0412' AND YearD = '" . $year . "' THEN I ELSE 0 END) AS BB
        FROM
        (SELECT CONCAT('I#', CASE P.ManagementLevel
        WHEN 2 THEN 'A#'
        WHEN 3 THEN 'B#' END,'02#', A.SectorNo, '#3') AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, BuildAddress, CapableDesign,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
        InvestdocNo, A.AccountNo, A.SectorNo as Sector, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo IN ('014','018','0412')
        AND C.BudgetStateType = 2
        AND PC.CateNo = '009' AND PC.CateValue = 2
        AND SC.CateNo = '20002' AND SC.CateValue = 1
        AND P.ManagementLevel IN (2,3)
        AND YEAR(P.ExpectedStartDate) = $year
        $whereCondition
        GROUP BY P.ProjectNo, A.AccountNo, YearD
        ) as CH GROUP BY Project) as DT ON DUPLICATE KEY UPDATE I14 = AA, I21 = BB";

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
            $I2 = $item->I3 + $item->I4 + $item->I5 + $item->I6;
            $I9 = $item->I10 + $item->I11 + $item->I12 + $item->I13;
            $I16 = $item->I17 + $item->I18 + $item->I19 + $item->I20;
            $I1 = $I2 + $item->I7;
            $I8 = $I9 + $item->I14;
            $I15 = $I16 + $item->I21;
            DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update([
                'I2'=> $I2, 'I9'=> $I9, 'I16'=> $I16,
                'I1'=> $I1, 'I8'=> $I8, 'I15'=> $I15,
            ]);
        }

    }

    public function sumUpSBPB246DH($maxLevel, $start = 0){
        for ($i = $maxLevel; $i >= $start; $i--) {
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
                SET I1 = $I1, I2 = $I2 , I3 = $I3, I4 = $I4,
                    I5 = $I5, I6 = $I6 , I7 = $I7, I8 = $I8,
                    I9 = $I9, I10 = $I10 , I11 = $I11, I12 = $I12,
                    I13 = $I13, I14 = $I14 , I15 = $I15, I16 = $I16,
                    I17 = $I17, I18 = $I18 , I19 = $I19, I20 = $I20,
                    I21 = $I21, I22 = $I22
                WHERE ItemID = '".$value['ParentID']."'
                    AND table_tmp.Level = $j";
                    DB::statement($update);
                }
            }
        }

    }

    public function setIndexSBP246DH($parentID){
        $insertArr = [];
        $maxLevel = 5;
//        $maxLevel = DB::table('table_tmp')->select(DB::raw('Max(Level) as maxLevel'))->first()->maxLevel;
        for($i = 3; $i <= $maxLevel; $i++){
            $insertArr1 = [];
            $sql = "SELECT * FROM table_tmp WHERE Level = $i AND LEFT(ItemID,3) = '" . $parentID . "' AND Detail != 1 ORDER BY ItemID ASC";
            $result = DB::select($sql);
            if($i == 3){
                $index = 1;
                foreach($result as $item){
                    $stt = 'II.' . $index;
                    $index++;
                    $insertArr1[] = [
                        'ItemID'=> $item->ItemID,
                        'ParentID'=> $item->ParentID
                    ];

                    DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=> $stt]);
                }
            }
            else{
                if($result){
                    foreach($insertArr[$i-4] as $value){
                        $index = 1;
                        foreach($result as $key=> $item){
                            if($value['ItemID'] == $item->ParentID){
                                $insertArr1[] = [
                                    'ItemID'=> $item->ItemID,
                                    'ParentID'=> $item->ParentID
                                ];
                                if($i == 4){
                                    $stt = $index;
                                }
                                if($i == 5){
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
        }
    }


}
