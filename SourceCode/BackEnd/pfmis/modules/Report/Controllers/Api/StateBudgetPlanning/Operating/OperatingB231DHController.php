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

class OperatingB231DHController extends Controller {
    use Common;
    public function Deleterownull($value){
        if(!$value){ $value= 1;}
        $sql = "DELETE FROM `table_tmp` WHERE  $value";
        DB::statement($sql);
    }
    public function SBPB231DH(Request $request)
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

        // TODO check isFinalTrans: AND A.isFinalTrans = 1

        $yearPre = (int)$year - 2;
        $yearNext = (int)$year - 1;

        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year - 2 AND $year AND A.InTransTypeID NOT IN (16,17,18) ";
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(20) DEFAULT '',
          `ItemID` varchar(100) NOT NULL PRIMARY KEY,
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
         `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `Year` varchar(5) DEFAULT '',
          `Project` varchar(50) DEFAULT '',
          `I1` double(20,4) DEFAULT 0 NOT NULL,
          `I2` double(20,4) DEFAULT 0 NOT NULL,
          `I3` double(20,4) DEFAULT 0 NOT NULL,
          `I4` double(20,4) DEFAULT 0 NOT NULL,
          `I5` double(20,4) DEFAULT 0 NOT NULL,
          `Program` varchar(10) DEFAULT '',
          `StartFinishDate` varchar(50) DEFAULT '',
            `SponsorName` varchar(255) DEFAULT '',
            `PerformDate` varchar(50) DEFAULT '',
            `ProjectTemporary` varchar(10) NOT NULL,
          `Detail` tinyint(1) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        // Tạo bảng tạm để tạo cây
        $this->createTemporaryTableProject();
        $insertA = [
            ['STT'=>'I','ItemID'=> '1','ItemName'=> 'Chương trình']
        ];
        DB::table('ProjectTemporary')->insert($insertA);

        // Thu
        $this->insertRevenueI($whereCondition, $year);
        $this->getParentDataTreeCommon(['program','project'],'A');
        $this->insertRevenueII($whereCondition, $year);
        $this->insertRevenueIII($whereCondition, $year);


        $this->setLevel();
        // Thiết lập STT
        $this->updateIndexReportB200('A');


        // Thêm dữ liệu mặc định
        $insertArr1 = [
            ['STT'=>'A', 'ItemID'=> 'A', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG THU', 'FontWeight'=> '1'],
            ['STT'=>'I','ItemID'=> 'A#01','ParentID'=>'A','Level'=> 1, 'ItemName'=> 'Chương trình', 'FontWeight'=> '0'],
            ['STT'=>'II','ItemID'=> 'A#02','ParentID'=>'A','Level'=> 1, 'ItemName'=> 'Dự án','FontWeight'=> '0'],
            ['STT'=>'III','ItemID'=> 'A#03','ParentID'=>'A','Level'=> 1, 'ItemName'=> 'Phi dự án','FontWeight'=> '0']
        ];
        DB::table('table_tmp')->insert($insertArr1);

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $this->sumUpSBPB231DH(0,6, $maxLevel);

        $data = DB::table("table_tmp")->orderBy('ItemID', 'asc')->get();
        $year2 = $year -2;
        $year1 = $year -1;
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $year2;
            $data[0]->HeaderName2 = 'Năm ' . $year1;
            $data[0]->HeaderName3 = 'Dự toán năm ' .$year;
        }
        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            if($data[$key]->I2 !=0){
                $data[$key]->I5 = round((((int)($data[$key]->I4) - (int)($data[$key]->I2))*100)/(int)($data[$key]->I2), 2);
            }
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';

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
                "ReportName" => "TỔNG HỢP CÂN ĐỐI DỰ TOÁN NSNN NĂM " . $year,
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

    // Bảng tạm để tạo cây

    public function createTemporaryTableProject(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `ProjectTemporary` (
         `STT` varchar(20) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
    }
    public function insertRevenueI($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,Program, Project, StartFinishDate, SponsorName, ProjectTemporary,Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName,Program, Project, StartFinishDate, SponsorName, '1', 1,
        SUM(CASE WHEN AccountNo = '0361' AND YEARD <=($year - 2 ) THEN S ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0711' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0311' AND YEARD = $year THEN I ELSE 0 END) AS I4
        FROM
        (SELECT CONCAT('A#','01#',A.ProgramNo) AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, A.AccountNo,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,YEAR(A.PeriodFromDate) AS YEARD,
        A.ProgramNo as Program, SponsorName,SUM(A.FCDebitAmount) AS I,SUM(LCDebitAmount - LCCreditAmount) as S
        FROM act_gl_books as A
		INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE  A.AccountNo in('0361','0341','0711','0311')
        AND CC.CateNo = '20012204' AND CC.CateValue = 1
        AND PC.CateNo = '018' AND PC.CateValue = 1
        $whereCondition
        GROUP BY A.AccountNo,P.ProjectNo,A.ProgramNo
        ) as CH GROUP BY Project,Program";

        DB::statement($sql);
    }

    public function insertRevenueII($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,Program, StartFinishDate, SponsorName,Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName,Program,  StartFinishDate, SponsorName, 1,
        SUM(CASE WHEN AccountNo = '0361' AND YEARD <=($year - 2 ) THEN S ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0711' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0311' AND YEARD = $year THEN I ELSE 0 END) AS I4
        FROM
        (SELECT CONCAT('A#','02') AS ParentID, A.ProjectName as ItemName,A.ProjectNo as Project, A.AccountNo,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,YEAR(A.PeriodFromDate) AS YEARD,
        SponsorName, A.ProgramNo as Program,SUM(A.FCDebitAmount) AS I,SUM(LCDebitAmount - LCCreditAmount) as S
        FROM act_gl_books as A
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo in('0361','0341','0711','0311')
        AND CC.CateNo = '20012204' AND CC.CateValue = 1
        AND PC.CateNo = '018' AND PC.CateValue = 2
        $whereCondition
        GROUP BY A.AccountNo,P.ProjectNo, YEAR(A.PeriodFromDate)
        ) as CH GROUP BY Project";

        DB::statement($sql);
    }
    public function insertRevenueIII($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Program,StartFinishDate, SponsorName,Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName,Program,  StartFinishDate, SponsorName, 1,
        SUM(CASE WHEN AccountNo = '0361' AND YEARD <=($year - 2 ) THEN S ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0711' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0311' AND YEARD = $year THEN I ELSE 0 END) AS I4
        FROM
        (SELECT CONCAT('A#','03') AS ParentID, A.ProjectName as ItemName,A.ProjectNo as Project, A.AccountNo,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as StartFinishDate,YEAR(A.PeriodFromDate) AS YEARD,
        SponsorName, A.ProgramNo as Program,SUM(A.FCDebitAmount) AS I,SUM(LCDebitAmount - LCCreditAmount) as S
        FROM act_gl_books as A
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        INNER JOIN project as P on A.ProjectID = P.ProjectID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        WHERE A.AccountNo in('0361','0341','0711','0311')
        AND CC.CateNo = '20012204' AND CC.CateValue = 1
        AND PC.CateNo = '018' AND PC.CateValue = 3
        $whereCondition
        GROUP BY A.AccountNo,P.ProjectNo, YEAR(A.PeriodFromDate)
        ) as CH GROUP BY Project";

        DB::statement($sql);
    }

    // SUM
    public function sumUpSBPB231DH($start, $end, $maxLevel){
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4 FROM table_tmp M
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
                    SET I1 = I1 + ".$I1.", I2 = I2 + ".$I2.", I3 = I3 + ".$I3.", I4 = I4 + ".$I4."
                    WHERE ItemID = '".$value['ParentID']."'
                        AND table_tmp.Level = $j"; //Log::debug($update);
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
