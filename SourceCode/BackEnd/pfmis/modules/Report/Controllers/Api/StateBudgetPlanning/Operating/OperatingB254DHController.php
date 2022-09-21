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

class OperatingB254DHController extends Controller {
    use Common;

    public function SBPB254DH(Request $request)
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

        $whereCompany = '';
        if ($companyID) {
            $whereCompany .= " AND A.CompanyID = " . $companyID . ' ';
        }
        $whereDirection = '';
        if ($directionID) {
            $whereDirection = 'AND A.DirectionID = ' . $directionID . ' ';
        }

        // TODO check
        $yearPre = (int)$year - 1;
        $yearNext = (int)$year + 1;

        $whereCommonCondition = "AND A.InTransTypeID NOT IN (16,17,18) ";
        $currentDate = date('Y-m-d');
        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year - 2 AND $year  $whereCommonCondition";
        $whereDebt = " AND A.PeriodFromDate <= '". $currentDate ."' $whereCommonCondition ";
        $wherePeriodDate = " $whereCommonCondition";
        $whereYear = " AND YEAR(A.PeriodFromDate) = $year $whereCommonCondition ";
        $wherePeriodDate = " AND A.PeriodFromDate BETWEEN '" . $year . "-01-01' AND '" . $year . "-06-30' ";
        $whereNextYear = " AND YEAR(A.PeriodFromDate) = $year + 1 $whereCommonCondition ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(20) DEFAULT '',
          `ItemID` varchar(100) NOT NULL PRIMARY KEY,
          `ParentID` varchar(100) DEFAULT '',
          `ParentID2` varchar(100) DEFAULT '',
            `Level` int(2) DEFAULT 1,
           `Level2` int(2) DEFAULT 1,
            `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `Year` varchar(5) DEFAULT '',
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
          `I29` double(20,4) DEFAULT 0 NOT NULL,
          `I30` double(20,4) DEFAULT 0 NOT NULL,
          `I31` double(20,4) DEFAULT 0 NOT NULL,
          `I32` double(20,4) DEFAULT 0 NOT NULL,
          `I33` double(20,4) DEFAULT 0 NOT NULL,
            `Project` varchar(20) DEFAULT '',
            `Program` varchar(20) DEFAULT '',
            `StartFinishDate` varchar(50) DEFAULT '',
            `SponsorName` varchar(255) DEFAULT '',
            `Detail` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        //


        // Chương trình chương trình- dự án
        $this->insertI_CT_C7C10($whereCondition, $year);
        $this->insertOrUpdateI_CT_C8C11($whereCondition, $year);

        $this->insertOrUpdateI_CT_C14C17($whereCondition, $year);
        $this->insertOrUpdateI_CT_C15C18($whereCondition, $year);

        $this->insertOrUpdateI_CT_C21C24C28C31($whereCondition, $year);
        $this->insertOrUpdateI_CT_C22C25C29C32($whereCondition, $year);
        $this->getParentDataTreeCommon(['program',''], 'B#03#01');

        $this->insertI_DA_C7C10($whereCondition, $year);
        $this->insertOrUpdateI_DA_C8C11($whereCondition, $year);

        $this->insertOrUpdateI_DA_C14C17($whereCondition, $year);
        $this->insertOrUpdateI_DA_C15C18($whereCondition, $year);

        $this->insertOrUpdateI_DA_C21C24C28C31($whereCondition, $year);
        $this->insertOrUpdateI_DA_C22C25C29C32($whereCondition, $year);
        $this->getParentDataTreeCommon(['program', 'project','']);

        // Dự án
        $this->insertII_C7C10($whereDebt);
        $this->insertOrUpdateII_C8C11($whereDebt);

        $this->insertOrUpdateII_C14C17($whereCondition, $year);
        $this->insertOrUpdateII_C15C18($whereCondition, $year);

        $this->insertOrUpdateII_C21C24C28C31($whereCondition, $year);
        $this->insertOrUpdateII_C22C25C29C32($whereCondition, $year);
        $this->getParentDataTreeCommon(['project',''], 'B#03#02');

        // phi dự án
        $this->insertIII_C7C10($whereCondition, $year);
        $this->insertOrUpdateIII_C8C11($whereCondition, $year);

        $this->insertOrUpdateIII_C14C17($whereCondition, $year);
        $this->insertOrUpdateIII_C15C18($whereCondition, $year);

        $this->insertOrUpdateIII_C21C24C28C31($whereCondition, $year);
        $this->insertOrUpdateIII_C22C25C29C32($whereCondition, $year);
        $this->getParentDataTreeCommon(['project',''], 'B#03#03');

        $this->setLevel();

//        thiết lập số thứ tự
//        $this->setIndexReportB254('B');

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
      $this->sumUpSBPB254DH(0,9, $maxLevel);

        $insertArr1 = [
            ['STT'=>'B', 'ItemID'=> 'B', 'ParentID'=> '', 'ParentID2'=> '', 'Level'=> 1,  'Level2'=> '', 'ItemName'=> 'TỔNG CHI', 'FontWeight'=> '1', 'Detail' => '0'],
            ['STT'=>'', 'ItemID'=> 'B#01', 'ParentID'=> 'B','ParentID2'=> '', 'Level'=> 2,  'Level2'=> '', 'ItemName'=> 'Vốn viện trợ', 'FontWeight'=> '0', 'Detail' => '0'],
            ['STT'=>'', 'ItemID'=> 'B#02', 'ParentID'=> 'B','ParentID2'=> '',  'Level'=> 2,  'Level2'=> '', 'ItemName'=> 'Vốn đối ứng', 'FontWeight'=> '0', 'Detail' => '0'],
            ['STT'=>'I','ItemID'=> 'B#03#01','ParentID'=>'','ParentID2'=> '', 'Level'=> 3,  'Level2'=> '','ItemName'=> 'Chương trình','FontWeight'=> '0', 'Detail' => '0'],
            ['STT'=>'I.1','ItemID'=> 'B#03#01#01','ParentID'=>'B#03#01','ParentID2'=> '', 'Level'=> 1, 'Level2'=> '','ItemName'=> 'Vốn viện trợ','FontWeight'=> '0', 'Detail' => '0'],
            ['STT'=>'I.2','ItemID'=> 'B#03#01#02','ParentID'=>'B#03#01','ParentID2'=> '', 'Level'=> 1, 'Level2'=> '','ItemName'=> 'Vốn đối ứng','FontWeight'=> '0', 'Detail' => '0'],
            ['STT'=>'II','ItemID'=> 'B#03#02','ParentID'=>'','ParentID2'=> '', 'Level'=> 3,  'Level2'=> '','ItemName'=> 'Dự án','FontWeight'=> '0', 'Detail' => '0'],
            ['STT'=>'II.1', 'ItemID'=> 'B#03#02#01', 'ParentID'=> 'B#03#02','ParentID2'=> '', 'Level'=> 4,  'Level2'=> '', 'ItemName'=> 'Vốn viện trợ', 'FontWeight'=> '0', 'Detail' => '0'],
            ['STT'=>'II.2', 'ItemID'=> 'B#03#02#02', 'ParentID'=> 'B#03#02','ParentID2'=> '',  'Level'=> 4,  'Level2'=> '', 'ItemName'=> 'Vốn đối ứng', 'FontWeight'=> '0', 'Detail' => '0'],
            ['STT'=>'III','ItemID'=> 'B#03#03','ParentID'=>'','ParentID2'=> '', 'Level'=> 3,  'Level2'=> '','ItemName'=> 'Phi dự án','FontWeight'=> '0', 'Detail' => '0'],
            ['STT'=>'III.1', 'ItemID'=> 'B#03#03#01', 'ParentID'=> 'B#03#03','ParentID2'=> '', 'Level'=> 4,  'Level2'=> '', 'ItemName'=> 'Vốn viện trợ', 'FontWeight'=> '0', 'Detail' => '0'],
            ['STT'=>'III.2', 'ItemID'=> 'B#03#03#02', 'ParentID'=> 'B#03#03','ParentID2'=> '',  'Level'=> 4,  'Level2'=> '', 'ItemName'=> 'Vốn đối ứng', 'FontWeight'=> '0', 'Detail' => '0'],
        ];
        DB::table('table_tmp')->insert($insertArr1);

        $data = DB::table("table_tmp")->orderBy('ItemID', 'asc')->get();
        $year2 = $year -2;
        $year1 = $year -1;

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Tổng số vốn viện trợ ký kết hoặc cam kết và vốn đối ứng cam kết';
            $data[0]->HeaderName2 = 'Lũy kế thực hiện đến năm ' . $year2;
            $data[0]->HeaderName3 = 'Đánh giá thực hiện năm ' .$year1;
            $data[0]->HeaderName4 = 'Dự toán năm ' .$year;

        }
        foreach ($data as $key => $value) {
            $data[$key]->I7 = ($data[$key]->I7) ? $data[$key]->I7 : '';
            $data[$key]->I8 = ($data[$key]->I8) ? $data[$key]->I8 : '';
            $data[$key]->I10 = ($data[$key]->I10) ? $data[$key]->I10 : '';
            $data[$key]->I11 = ($data[$key]->I11) ? $data[$key]->I11 : '';

            $data[$key]->I9 = (int)($data[$key]->I10) + (int)($data[$key]->I11);
            $data[$key]->I9 = ($data[$key]->I9) ? $data[$key]->I9 : '';
            $data[$key]->I6 = (int)($data[$key]->I7) + (int)($data[$key]->I8);
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
            $data[$key]->I5 = (int)($data[$key]->I6) + (int)($data[$key]->I9);
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';

            $data[$key]->I14 = ($data[$key]->I14) ? $data[$key]->I14 : '';
            $data[$key]->I15 = ($data[$key]->I15) ? $data[$key]->I15 : '';
            $data[$key]->I17 = ($data[$key]->I17) ? $data[$key]->I17 : '';
            $data[$key]->I18 = ($data[$key]->I18) ? $data[$key]->I18 : '';

            $data[$key]->I16 = (int)($data[$key]->I17) + (int)($data[$key]->I18);
            $data[$key]->I16 = ($data[$key]->I16) ? $data[$key]->I16 : '';
            $data[$key]->I13 = (int)($data[$key]->I14) + (int)($data[$key]->I15);
            $data[$key]->I13 = ($data[$key]->I13) ? $data[$key]->I13 : '';
            $data[$key]->I12 = (int)($data[$key]->I13) + (int)($data[$key]->I16);
            $data[$key]->I12 = ($data[$key]->I12) ? $data[$key]->I12 : '';

            $data[$key]->I21 = ($data[$key]->I21) ? $data[$key]->I21 : '';
            $data[$key]->I22 = ($data[$key]->I22) ? $data[$key]->I22 : '';
            $data[$key]->I24 = ($data[$key]->I24) ? $data[$key]->I24 : '';
            $data[$key]->I25 = ($data[$key]->I25) ? $data[$key]->I25 : '';

            $data[$key]->I23 = (int)($data[$key]->I24) + (int)($data[$key]->I25);
            $data[$key]->I23 = ($data[$key]->I23) ? $data[$key]->I23 : '';
            $data[$key]->I20 = (int)($data[$key]->I21) + (int)($data[$key]->I22);
            $data[$key]->I20 = ($data[$key]->I20) ? $data[$key]->I20 : '';
            $data[$key]->I19 = (int)($data[$key]->I20) + (int)($data[$key]->I21);
            $data[$key]->I19 = ($data[$key]->I19) ? $data[$key]->I19 : '';

            $data[$key]->I28 = ($data[$key]->I28) ? $data[$key]->I28 : '';
            $data[$key]->I29 = ($data[$key]->I29) ? $data[$key]->I29 : '';
            $data[$key]->I31 = ($data[$key]->I31) ? $data[$key]->I31 : '';
            $data[$key]->I32 = ($data[$key]->I32) ? $data[$key]->I32 : '';

            $data[$key]->I30 = (int)($data[$key]->I31) + (int)($data[$key]->I32);
            $data[$key]->I30 = ($data[$key]->I30) ? $data[$key]->I30 : '';
            $data[$key]->I27 = (int)($data[$key]->I28) + (int)($data[$key]->I29);
            $data[$key]->I27 = ($data[$key]->I27) ? $data[$key]->I27 : '';
            $data[$key]->I26 = (int)($data[$key]->I27) + (int)($data[$key]->I30);
            $data[$key]->I26 = ($data[$key]->I26) ? $data[$key]->I26 : '';

        }

        if (isset($filter['CompanyID'])) {
            $note .= 'đơn vị ' . $filter['CompanyName'];
            $company = Company::where('CompanyID',$filter['CompanyID'])->get();
        }
        else{
            $company = Company::where('CompanyID',Auth::user()->CompanyID)->get();
        }

        $companyName = $company[0]->CompanyName;
        $company = Company::find($companyID);
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
                "CreateName" => "Trần Thế Thái",
                "TransNo" => "00001",
                "ReportName" => "CÂN ĐỐI DỰ TOÁN CHI TỪ NGUỒN VIỆN TRỢ KHÔNG HOÀN LẠI NĂM " . $year,
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
    // Chương trình - 1 chương trình
    public function insertI_CT_C7C10($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName ,Program, Detail, I7, I10)
            SELECT CONCAT('B#03#01#02#', Program, '#01') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Program, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052' THEN I ELSE 0 END) as I7,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  THEN I ELSE 0 END) as I10 FROM
            (SELECT CONCAT('B#03#01#02#',  A.ProgramNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn viện trợ' as ItemName, A.ProgramNo as Program, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN program_cate as PMC on A.ProgramID = PMC.ProgramID
            INNER JOIN program as PM on A.ProgramID = PM.ProgramID
             INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012204' AND CC.CateValue = '1'
            AND A.AccountNo = '091'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 1  $whereDebt
            GROUP BY EC.CateNo,A.ProgramNo) as CH Group  by ExpenseCateNo";

        DB::statement($sql);
    }

    public function insertOrUpdateI_CT_C8C11($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Program, Detail, I8, I11)
             SELECT * FROM
            (SELECT CONCAT('B#03#01#02#', Program, '#02') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Program, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052' THEN I ELSE 0 END) as A8,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  THEN I ELSE 0 END) as A11 FROM
            (SELECT CONCAT('B#03#01#02#', A.ProgramNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn đối ứng' as ItemName, A.ProgramNo as Program, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN program_cate as PMC on A.ProgramID = PMC.ProgramID
            INNER JOIN program as PM on A.ProgramID = PM.ProgramID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = '1' and C.BudgetStateType = '1'
            AND A.AccountNo = '091'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 1  $whereDebt
            GROUP BY EC.CateNo,A.ProgramNo) as CH Group  by ExpenseCateNo) AS DT ON DUPLICATE KEY UPDATE I8 = A8, I11 = A11";

        DB::statement($sql);
    }

    public function insertOrUpdateI_CT_C14C17($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Program, Detail, I14, I17)
            SELECT * FROM
            (SELECT CONCAT('B#03#01#02#', Program, '#01') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Program, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052'  AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A14,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A17 FROM
            (SELECT CONCAT('B#03#01#02#', A.ProgramNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn viện trợ' as ItemName, A.ProgramNo as Program, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(LCDebitAmount - LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN program_cate as PMC on A.ProgramID = PMC.ProgramID
            INNER JOIN program as PM on A.ProgramID = PM.ProgramID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012204' AND CC.CateValue = '1'
            AND A.AccountNo = '0461'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
             and PC.CateNo = '018' AND PC.CateValue = 1 $whereCondition
            GROUP BY EC.CateNo,A.ProgramNo) as CH Group  by ExpenseCateNo) as  DT ON DUPLICATE KEY UPDATE I14 = A14, I17 = A17 ";

        DB::statement($sql);
    }
    public function insertOrUpdateI_CT_C15C18($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Program, Detail, I15, I18)
             SELECT * FROM
            (SELECT CONCAT('B#03#01#02#', Program, '#02') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Program, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052' AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A15,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A18 FROM
            (SELECT CONCAT('B#03#01#02#', A.ProgramNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn đối ứng' as ItemName, A.ProgramNo as Program, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(LCDebitAmount - LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN program_cate as PMC on A.ProgramID = PMC.ProgramID
            INNER JOIN program as PM on A.ProgramID = PM.ProgramID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = '1' and C.BudgetStateType = '1'
            AND A.AccountNo = '0461'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 1  $whereCondition
            GROUP BY EC.CateNo,A.ProgramNo) as CH Group  by ExpenseCateNo) AS DT ON DUPLICATE KEY UPDATE I15 = A15, I18 = A18";

        DB::statement($sql);
    }

    public function insertOrUpdateI_CT_C21C24C28C31($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Program, Detail, I21, I24, I28, I31)
            SELECT * FROM
            (SELECT CONCAT('B#03#01#02#', Program, '#01') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Program, 1,
            SUM(CASE WHEN AccountNo = '0721' and left(ExpenseCateNo,'5') = '20052'  AND YearD = $year - 1  THEN I ELSE 0 END) as A21,
            SUM(CASE WHEN AccountNo = '0721' and left(ExpenseCateNo,'5') = '20051'  AND YearD = $year - 1  THEN I ELSE 0 END) as A24,
            SUM(CASE WHEN AccountNo = '0411' and left(ExpenseCateNo,'5') = '20052' AND YearD = $year   THEN I ELSE 0 END) as A28,
            SUM(CASE WHEN AccountNo = '0411' and left(ExpenseCateNo,'5') = '20051' and YearD = $year   THEN I ELSE 0 END) as A31 FROM
            (SELECT CONCAT('B#03#01#02#', A.ProgramNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn viện trợ' as ItemName, A.ProgramNo as Program, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            INNER JOIN program_cate as PMC on A.ProgramID = PMC.ProgramID
            INNER JOIN program as PM on A.ProgramID = PM.ProgramID
            WHERE CC.CateNo = '20012204' AND CC.CateValue = '1'
            AND A.AccountNo in ('0721','0411')
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 1  $whereCondition
            GROUP BY EC.CateNo,A.ProgramNo) as CH Group  by ExpenseCateNo) as  DT ON DUPLICATE KEY UPDATE I21 = A21, I24 = A24, I28 = A28, I31 = A31 ";

        DB::statement($sql);
    }
    public function insertOrUpdateI_CT_C22C25C29C32($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Program, Detail, I22, I25, I29, I32)
             SELECT * FROM
            (SELECT CONCAT('B#03#01#02#', Program, '#02') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Program, 1,
             SUM(CASE WHEN AccountNo = '0721' and left(ExpenseCateNo,'5') = '20052'  AND YearD = $year - 1  THEN I ELSE 0 END) as A22,
            SUM(CASE WHEN  AccountNo = '0721' and left(ExpenseCateNo,'5') = '20051'  AND YearD = $year - 1  THEN I ELSE 0 END) as A25,
            SUM(CASE WHEN  AccountNo = '0411' and left(ExpenseCateNo,'5') = '20052'  AND YearD = $year   THEN I ELSE 0 END) as A29,
            SUM(CASE WHEN  AccountNo = '0411' and left(ExpenseCateNo,'5') = '20051'  AND YearD = $year   THEN I ELSE 0 END) as A32 FROM
            (SELECT CONCAT('B#03#01#02#', A.ProgramNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn đối ứng' as ItemName, A.ProgramNo as Program, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            INNER JOIN program_cate as PMC on A.ProgramID = PMC.ProgramID
            INNER JOIN program as PM on A.ProgramID = PM.ProgramID
            WHERE C.CapitalInOut = '1' and C.BudgetStateType = '1'
            AND A.AccountNo = '0461'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 1  $whereCondition
            GROUP BY EC.CateNo,A.ProgramNo) as CH Group  by ExpenseCateNo) AS DT ON DUPLICATE KEY UPDATE I22 = A22, I25 = A25, I29 = A29, I32=A32";

        DB::statement($sql);
    }
    // chương trình - 1,1 dự án
    public function insertI_DA_C7C10($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName ,Program, Project, Detail, I7, I10)
            SELECT CONCAT('B#03#01#02#',Program,'#', Project, '#01') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName,Program, Project, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052' THEN I ELSE 0 END) as I7,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  THEN I ELSE 0 END) as I10 FROM
            (SELECT CONCAT('B#03#02#02#',A.ProgramNo,'#',  A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn viện trợ' as ItemName,A.ProgramNo as Program, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012204' AND CC.CateValue = '1'
            AND A.AccountNo = '091'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 1  $whereDebt
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo";

        DB::statement($sql);
    }

    public function insertOrUpdateI_DA_C8C11($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I8, I11)
             SELECT * FROM
            (SELECT CONCAT('B#03#01#02#',Program,'#', '#02') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052' THEN I ELSE 0 END) as A8,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  THEN I ELSE 0 END) as A11 FROM
            (SELECT CONCAT('B#03#02#02#',A.ProgramNo,'#',  A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn đối ứng' as ItemName,A.ProgramNo as Program, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = '1' and C.BudgetStateType = '1'
            AND A.AccountNo = '091'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 1  $whereDebt
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) AS DT ON DUPLICATE KEY UPDATE I8 = A8, I11 = A11";

        DB::statement($sql);
    }

    public function insertOrUpdateI_DA_C14C17($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I14, I17)
            SELECT * FROM
            (SELECT CONCAT('B#03#01#02#',Program,'#', Project, '#01') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052'  AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A14,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A17 FROM
            (SELECT CONCAT('B#03#01#02#',A.ProgramNo,'#', A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn viện trợ' as ItemName,A.ProgramNo as Program, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(LCDebitAmount - LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012204' AND CC.CateValue = '1'
            AND A.AccountNo = '0461'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
             and PC.CateNo = '018' AND PC.CateValue = 1 $whereCondition
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) as  DT ON DUPLICATE KEY UPDATE I14 = A14, I17 = A17 ";

        DB::statement($sql);
    }
    public function insertOrUpdateI_DA_C15C18($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I15, I18)
             SELECT * FROM
            (SELECT CONCAT('B#03#01#02#',Program,'#', Project, '#02') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052' AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A15,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A18 FROM
            (SELECT CONCAT('B#03#02#02#',A.ProgramNo,'#',  A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn đối ứng' as ItemName, A.ProgramNo as Program,A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(LCDebitAmount - LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = '1' and C.BudgetStateType = '1'
            AND A.AccountNo = '0461'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 1  $whereCondition
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) AS DT ON DUPLICATE KEY UPDATE I15 = A15, I18 = A18";

        DB::statement($sql);
    }

    public function insertOrUpdateI_DA_C21C24C28C31($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I21, I24, I28, I31)
            SELECT * FROM
            (SELECT CONCAT('B#03#01#02#',Program,'#', Project, '#01') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN AccountNo = '0721' and left(ExpenseCateNo,'5') = '20052'  AND YearD = $year - 1  THEN I ELSE 0 END) as A21,
            SUM(CASE WHEN AccountNo = '0721' and left(ExpenseCateNo,'5') = '20051'  AND YearD = $year - 1  THEN I ELSE 0 END) as A24,
            SUM(CASE WHEN AccountNo = '0411' and left(ExpenseCateNo,'5') = '20052' AND YearD = $year   THEN I ELSE 0 END) as A28,
            SUM(CASE WHEN AccountNo = '0411' and left(ExpenseCateNo,'5') = '20051' and YearD = $year   THEN I ELSE 0 END) as A31 FROM
            (SELECT CONCAT('B#03#02#02#',A.ProgramNo,'#',  A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn viện trợ' as ItemName, A.ProgramNo as Program,A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
           INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012204' AND CC.CateValue = '1'
            AND A.AccountNo in ('0721','0411')
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 1  $whereCondition
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) as  DT ON DUPLICATE KEY UPDATE I21 = A21, I24 = A24, I28 = A28, I31 = A31 ";

        DB::statement($sql);
    }
    public function insertOrUpdateI_DA_C22C25C29C32($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I22, I25, I29, I32)
             SELECT * FROM
            (SELECT CONCAT('B#03#01#02#',Program,'#', Project, '#02') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
             SUM(CASE WHEN AccountNo = '0721' and left(ExpenseCateNo,'5') = '20052'  AND YearD = $year - 1  THEN I ELSE 0 END) as A22,
            SUM(CASE WHEN  AccountNo = '0721' and left(ExpenseCateNo,'5') = '20051'  AND YearD = $year - 1  THEN I ELSE 0 END) as A25,
            SUM(CASE WHEN  AccountNo = '0411' and left(ExpenseCateNo,'5') = '20052'  AND YearD = $year   THEN I ELSE 0 END) as A29,
            SUM(CASE WHEN  AccountNo = '0411' and left(ExpenseCateNo,'5') = '20051'  AND YearD = $year   THEN I ELSE 0 END) as A32 FROM
            (SELECT CONCAT('B#03#02#02#',A.ProgramNo,'#',  A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn đối ứng' as ItemName, A.ProgramNo as Program,A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = '1' and C.BudgetStateType = '1'
            AND A.AccountNo = '0461'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 1  $whereCondition
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) AS DT ON DUPLICATE KEY UPDATE I22 = A22, I25 = A25, I29 = A29, I32=A32";

        DB::statement($sql);
    }
// Dự án-> đây a ạ

    public function insertII_C7C10($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I7, I10)
            SELECT CONCAT('B#03#02#02#', Project, '#01') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052' THEN I ELSE 0 END) as I7,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  THEN I ELSE 0 END) as I10 FROM
            (SELECT CONCAT('B#03#02#02#',  A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn viện trợ' as ItemName, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012204' AND CC.CateValue = '1'
            AND A.AccountNo = '091'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 2  $whereDebt
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo";

        DB::statement($sql);
    }

    public function insertOrUpdateII_C8C11($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I8, I11)
             SELECT * FROM
            (SELECT CONCAT('B#03#02#02#', Project, '#02') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052' THEN I ELSE 0 END) as A8,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  THEN I ELSE 0 END) as A11 FROM
            (SELECT CONCAT('B#03#02#02#', A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn đối ứng' as ItemName, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = '1' and C.BudgetStateType = '1'
            AND A.AccountNo = '091'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 2  $whereDebt
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) AS DT ON DUPLICATE KEY UPDATE I8 = A8, I11 = A11";

        DB::statement($sql);
    }

    public function insertOrUpdateII_C14C17($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I14, I17)
            SELECT * FROM
            (SELECT CONCAT('B#03#02#02#', Project, '#01') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052'  AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A14,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A17 FROM
            (SELECT CONCAT('B#03#02#02#', A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn viện trợ' as ItemName, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(LCDebitAmount - LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012204' AND CC.CateValue = '1'
            AND A.AccountNo = '0461'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
             and PC.CateNo = '018' AND PC.CateValue = 2 $whereCondition
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) as  DT ON DUPLICATE KEY UPDATE I14 = A14, I17 = A17 ";

        DB::statement($sql);
    }
    public function insertOrUpdateII_C15C18($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I15, I18)
             SELECT * FROM
            (SELECT CONCAT('B#03#02#02#', Project, '#02') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052' AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A15,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A18 FROM
            (SELECT CONCAT('B#03#02#02#', A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn đối ứng' as ItemName, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(LCDebitAmount - LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = '1' and C.BudgetStateType = '1'
            AND A.AccountNo = '0461'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 2  $whereCondition
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) AS DT ON DUPLICATE KEY UPDATE I15 = A15, I18 = A18";

        DB::statement($sql);
    }

    public function insertOrUpdateII_C21C24C28C31($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I21, I24, I28, I31)
            SELECT * FROM
            (SELECT CONCAT('B#03#02#02#', Project, '#01') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN AccountNo = '0721' and left(ExpenseCateNo,'5') = '20052'  AND YearD = $year - 1  THEN I ELSE 0 END) as A21,
            SUM(CASE WHEN AccountNo = '0721' and left(ExpenseCateNo,'5') = '20051'  AND YearD = $year - 1  THEN I ELSE 0 END) as A24,
            SUM(CASE WHEN AccountNo = '0411' and left(ExpenseCateNo,'5') = '20052' AND YearD = $year   THEN I ELSE 0 END) as A28,
            SUM(CASE WHEN AccountNo = '0411' and left(ExpenseCateNo,'5') = '20051' and YearD = $year   THEN I ELSE 0 END) as A31 FROM
            (SELECT CONCAT('B#03#02#02#', A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn viện trợ' as ItemName, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
           INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012204' AND CC.CateValue = '1'
            AND A.AccountNo in ('0721','0411')
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 2  $whereCondition
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) as  DT ON DUPLICATE KEY UPDATE I21 = A21, I24 = A24, I28 = A28, I31 = A31 ";

        DB::statement($sql);
    }
    public function insertOrUpdateII_C22C25C29C32($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I22, I25, I29, I32)
             SELECT * FROM
            (SELECT CONCAT('B#03#02#02#', Project, '#02') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
             SUM(CASE WHEN AccountNo = '0721' and left(ExpenseCateNo,'5') = '20052'  AND YearD = $year - 1  THEN I ELSE 0 END) as A22,
            SUM(CASE WHEN  AccountNo = '0721' and left(ExpenseCateNo,'5') = '20051'  AND YearD = $year - 1  THEN I ELSE 0 END) as A25,
            SUM(CASE WHEN  AccountNo = '0411' and left(ExpenseCateNo,'5') = '20052'  AND YearD = $year   THEN I ELSE 0 END) as A29,
            SUM(CASE WHEN  AccountNo = '0411' and left(ExpenseCateNo,'5') = '20051'  AND YearD = $year   THEN I ELSE 0 END) as A32 FROM
            (SELECT CONCAT('B#03#02#02#', A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn đối ứng' as ItemName, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = '1' and C.BudgetStateType = '1'
            AND A.AccountNo = '0461'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 2  $whereCondition
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) AS DT ON DUPLICATE KEY UPDATE I22 = A22, I25 = A25, I29 = A29, I32=A32";

        DB::statement($sql);
    }

    /// PHI DỰ ÁN
    public function insertIII_C7C10($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I7, I10)
            SELECT CONCAT('B#03#03#02#', Project, '#01') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052' THEN I ELSE 0 END) as I7,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  THEN I ELSE 0 END) as I10 FROM
            (SELECT CONCAT('B#03#03#02#',A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn viện trợ' as ItemName, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012204' AND CC.CateValue = '1'
            AND A.AccountNo = '091'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 3  $whereDebt
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo";

        DB::statement($sql);
    }

    public function insertOrUpdateIII_C8C11($whereDebt){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I8, I11)
             SELECT * FROM
            (SELECT CONCAT('B#03#03#02#', Project, '#02') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052' THEN I ELSE 0 END) as A8,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  THEN I ELSE 0 END) as A11 FROM
            (SELECT CONCAT('B#03#03#02#', A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn đối ứng' as ItemName, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
           INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = '1' and C.BudgetStateType = '1'
            AND A.AccountNo = '091'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
            and PC.CateNo = '018' AND PC.CateValue = 3              $whereDebt
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) AS DT ON DUPLICATE KEY UPDATE I8 = A8, I11 = A11";

        DB::statement($sql);
    }

    public function insertOrUpdateIII_C14C17($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I14, I17)
            SELECT * FROM
            (SELECT CONCAT('B#03#03#02#', Project, '#01') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052'  AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A14,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A17 FROM
            (SELECT CONCAT('B#03#03#02#', A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn viện trợ' as ItemName, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(LCDebitAmount - LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012204' AND CC.CateValue = '1'
            AND A.AccountNo = '0461'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
             and PC.CateNo = '018' AND PC.CateValue = 3 $whereCondition
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) as  DT ON DUPLICATE KEY UPDATE I14 = A14, I17 = A17 ";

        DB::statement($sql);
    }
    public function insertOrUpdateIII_C15C18($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I15, I18)
             SELECT * FROM
            (SELECT CONCAT('B#03#03#02#', Project, '#02') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20052' AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A15,
            SUM(CASE WHEN left(ExpenseCateNo,'5') = '20051'  AND YearD <= ($year - 2)  THEN I ELSE 0 END) as A18 FROM
            (SELECT CONCAT('B#03#03#02#', A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn đối ứng' as ItemName, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(LCDebitAmount - LCCreditAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = '1' and C.BudgetStateType = '1'
            AND A.AccountNo = '0461'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
              and PC.CateNo = '018' AND PC.CateValue = 3 $whereCondition
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) AS DT ON DUPLICATE KEY UPDATE I15 = A15, I18 = A18";

        DB::statement($sql);
    }

    public function insertOrUpdateIII_C21C24C28C31($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I21, I24, I28, I31)
            SELECT * FROM
            (SELECT CONCAT('B#03#03#02#', Project, '#01') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
            SUM(CASE WHEN AccountNo = '0721' and left(ExpenseCateNo,'5') = '20052' AND YearD = $year - 1  THEN I ELSE 0 END) as A21,
            SUM(CASE WHEN AccountNo = '0721' and left(ExpenseCateNo,'5') = '20051' AND YearD = $year - 1  THEN I ELSE 0 END) as A24,
            SUM(CASE WHEN AccountNo = '0411' and left(ExpenseCateNo,'5') = '20052' AND YearD = $year   THEN I ELSE 0 END) as A28,
            SUM(CASE WHEN AccountNo = '0411' and left(ExpenseCateNo,'5') = '20051' and YearD = $year   THEN I ELSE 0 END) as A31 FROM
            (SELECT CONCAT('B#03#03#02#', A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn viện trợ' as ItemName, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE CC.CateNo = '20012204' AND CC.CateValue = '1'
            AND A.AccountNo in ('0721','0411')
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
              and PC.CateNo = '018' AND PC.CateValue = 3 $whereCondition
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) as  DT ON DUPLICATE KEY UPDATE I21 = A21, I24 = A24, I28 = A28, I31 = A31 ";

        DB::statement($sql);
    }
    public function insertOrUpdateIII_C22C25C29C32($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, StartFinishDate, SponsorName , Project, Detail, I22, I25, I29, I32)
             SELECT * FROM
            (SELECT CONCAT('B#03#03#02#', Project, '#02') as ItemID, ParentID, ItemName,StartFinishDate, SponsorName, Project, 1,
             SUM(CASE WHEN AccountNo = '0721' and left(ExpenseCateNo,'5') = '20052'  AND YearD = $year - 1  THEN I ELSE 0 END) as A22,
            SUM(CASE WHEN  AccountNo = '0721' and left(ExpenseCateNo,'5') = '20051'  AND YearD = $year - 1  THEN I ELSE 0 END) as A25,
            SUM(CASE WHEN  AccountNo = '0411' and left(ExpenseCateNo,'5') = '20052'  AND YearD = $year   THEN I ELSE 0 END) as A29,
            SUM(CASE WHEN  AccountNo = '0411' and left(ExpenseCateNo,'5') = '20051'  AND YearD = $year   THEN I ELSE 0 END) as A32 FROM
            (SELECT CONCAT('B#03#03#02#', A.ProjectNo) as ParentID, CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,
           'Vốn đối ứng' as ItemName, A.ProjectNo as Project, A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SponsorName, EC.CateNo as ExpenseCateNo, SUM(A.FCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN project as P on A.ProjectID = P.ProjectID
            WHERE C.CapitalInOut = '1' and C.BudgetStateType = '1'
            AND A.AccountNo = '0461'
           AND left(EC.CateNo,5) in('20052','20051')  AND EC.CateValue IS NOT NULl
              and PC.CateNo = '018' AND PC.CateValue = 3 $whereCondition
            GROUP BY EC.CateNo,A.ProjectNo) as CH Group  by ExpenseCateNo) AS DT ON DUPLICATE KEY UPDATE I22 = A22, I25 = A25, I29 = A29, I32=A32";

        DB::statement($sql);
    }

    // sumup
    public function sumUpSBPB254DH($start, $end, $maxLevel){
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID, SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8,
            SUM(M.I9) AS I9, SUM(M.I10) AS I10, SUM(M.I11) AS I11, SUM(M.I12) AS I12,
            SUM(M.I13) AS I13, SUM(M.I14) AS I14, SUM(M.I15) AS I15, SUM(M.I16) AS I16,
            SUM(M.I17) AS I17, SUM(M.I18) AS I18, SUM(M.I19) AS I19, SUM(M.I20) AS I20,
            SUM(M.I21) AS I21, SUM(M.I22) AS I22, SUM(M.I23) AS I23, SUM(M.I24) AS I24, SUM(M.I25) AS I25, SUM(M.I26) AS I26,
            SUM(M.I27) AS I27, SUM(M.I28) AS I28, SUM(M.I29) AS I29, SUM(M.I30) AS I30, SUM(M.I31) AS I31, SUM(M.I32) AS I32
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
                        $update = "UPDATE table_tmp
                    SET I5 = I5 + ".$I5.", I6 = I6 + ".$I6.", I7 = I7 + ".$I7.", I8 = I8 + ".$I8.",I9 = I9 + ".$I9.",
                    I10 = I10 + ".$I10.", I11 = I11 + ".$I11.", I12 = I12 + ".$I12.", I13 = I13 + ".$I13.", I14 = I14 + ".$I14.",I15 = I15 + ".$I15.", I16 = I16 + ".$I16.", I17 = I17 + ".$I17.", I18 = I18 + ".$I18.",I19 = I19 + ".$I19.",
                     I20 = I20 + ".$I20.", I21 = I21 + ".$I21.", I22 = I22 + ".$I22.", I23 = I23 + ".$I23.", I24 = I24 + ".$I24.",I25 = I25 + ".$I25.", I26 = I26 + ".$I26.", I27 = I27 + ".$I27.", I28 = I28 + ".$I28.",I29 = I29 + ".$I29.",
                     I30 = I30 + ".$I30.", I31 = I31 + ".$I31.", I32 = I32 + ".$I32."
                    WHERE ItemID = '".$value['ParentID']."'
                        AND table_tmp.Level = $j"; //Log::debug($update);
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
