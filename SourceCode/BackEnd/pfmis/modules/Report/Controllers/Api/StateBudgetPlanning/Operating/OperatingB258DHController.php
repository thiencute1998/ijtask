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

class OperatingB258DHController extends Controller {
    use Common;
    public function Deleterownull($value){
        if(!$value){ $value= 1;}
        $sql = "DELETE FROM `table_tmp` WHERE  $value";
        DB::statement($sql);
    }
    public function SBPB258DH(Request $request)
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
          `I6` double(20,4) DEFAULT 0 NOT NULL,
          `I7` double(20,4) DEFAULT 0 NOT NULL,
          `I8` double(20,4) DEFAULT 0 NOT NULL,
          `I9` double(20,4) DEFAULT 0 NOT NULL,
            `SectorCateValue` varchar(10) DEFAULT '',
            `ProjectNo` varchar(10) DEFAULT '',
          `Detail` tinyint(1) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);


        $this->InsertExpenseI_1($whereCondition, $year);
        $this->InsertExpenseI_2($whereCondition, $year);
        $this->InsertExpenseII_1($whereCondition, $year);
        $this->InsertExpenseII_2($whereCondition, $year);
        $this->InsertExpense1_3_5_7($whereCondition, $year);
        $this->InsertorUpdateExpense2_4_6_8($whereCondition, $year);
        $this->getParentDataTreeCommon([['sector_cate_value','20002'],'project'],'A');


        $this->setLevel();
        // Thiết lập STT
        $this->updateIndexReportB200('A');


        // Thêm dữ liệu mặc định
        $insertArr1 = [
            ['STT'=>'', 'ItemID'=> 'A', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'Tổng số', 'FontWeight'=> '1'],
            ['STT'=>'I', 'ItemID'=> 'A#I', 'ParentID'=> 'A', 'Level'=> 1, 'ItemName'=> 'Chi hoạt động của các cơ quan quản lý nhà nước, đảng, đoàn thể ', 'FontWeight'=> '1'],
            ['STT'=>'II', 'ItemID'=> 'A#II', 'ParentID'=> 'A', 'Level'=> 1, 'ItemName'=> 'Chi giáo dục - đào tạo và dạy nghề', 'FontWeight'=> '1']
        ];
        DB::table('table_tmp')->insert($insertArr1);

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $this->sumUpSBPB258DH(0,6, $maxLevel);

        $data = DB::table("table_tmp")->orderBy('ItemID', 'asc')->get();
        $year2 = $year -2;
        $year1 = $year -1;
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $year2;
            $data[0]->HeaderName2 = 'Dự toán năm ' . $year1;
            $data[0]->HeaderName3 = 'Ước thực hiện năm ' .$year;
            $data[0]->HeaderName4 = 'Dự toán năm ' .$year;
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
            if($data[$key]->I3 !=0){
                $data[$key]->I9 = round((((int)($data[$key]->I9) - (int)($data[$key]->I3))*100)/(int)($data[$key]->I3), 2);
            }
            $data[$key]->I9 = ($data[$key]->I9) ? $data[$key]->I9 : '';

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
                "ReportName" => "DỰ TOÁN CHI BẰNG NGOẠI TỆ NĂM " . $year . " GIAI ĐOẠN " . $year1."-".$year,
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
    public function InsertExpenseI_1($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project,Detail,SectorCateValue, I1, I3, I5, I7)
               SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project , 1, SectorCateValue,
               MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2  THEN I ELSE 0 END) AS I1,
               MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
               MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I5,
               MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I7
        FROM
            (SELECT 'A#I' AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, SC.CateValue as SectorCateValue, A.AccountNo,YEAR(A.PeriodFromDate) AS YEARD, SUM(FCDebitAmount) as I
             FROM act_gl_books as A
                      INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
                      INNER JOIN sector_cate as SC on SC.SectorID = A.SectorID
             WHERE  A.AccountNo in('0461','0441','0721','0411')
               AND LEFT(SC.CateNo, 5) = '20002' AND SC.CateValue='15'
               AND LEFT(PC.CateNo, 3) =  '019' AND PC.CateValue = '2'
                 $whereCondition
             GROUP BY A.AccountNo,A.ProjectNo
            ) as CH GROUP BY Project";

        DB::statement($sql);
    }
    public function InsertExpenseI_2($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project,Detail,SectorCateValue, I2, I4, I6, I8)
  SELECT * FROM
            (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project , 1, SectorCateValue,
                   MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2  THEN I ELSE 0 END) AS AA,
                   MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS BB,
                   MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS CC,
                   MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS DD
            FROM
                (SELECT 'A#I' AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project,SC.CateValue as SectorCateValue, A.AccountNo,YEAR(A.PeriodFromDate) AS YEARD, SUM(LCDebitAmount) as I
                 FROM act_gl_books as A
                          INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
                          INNER JOIN sector_cate as SC on SC.SectorID = A.SectorID
                 WHERE  A.AccountNo in('0461','0441','0721','0411')
               AND LEFT(SC.CateNo, 5) = '20002' AND SC.CateValue='15'
               AND LEFT(PC.CateNo, 3) =  '019' AND PC.CateValue = '2'
                     $whereCondition
                 GROUP BY A.AccountNo,A.ProjectNo
                ) as CH GROUP BY Project)as DT ON DUPLICATE KEY UPDATE I2 = AA, I4 = BB,I6 = CC, I8 = DD";

        DB::statement($sql);
    }
    public function InsertExpenseII_1($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project,Detail,SectorCateValue, I1, I3, I5, I7)
               SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project , 1, SectorCateValue,
               MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2  THEN I ELSE 0 END) AS I1,
               MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
               MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I5,
               MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I7
        FROM
            (SELECT 'A#II' AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, SC.CateValue as SectorCateValue, A.AccountNo,YEAR(A.PeriodFromDate) AS YEARD, SUM(FCDebitAmount) as I
             FROM act_gl_books as A
                      INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
                      INNER JOIN sector_cate as SC on SC.SectorID = A.SectorID
             WHERE  A.AccountNo in('0461','0441','0721','0411')
               AND LEFT(SC.CateNo, 5) = '20002' AND SC.CateValue='3'
               AND LEFT(PC.CateNo, 3) =  '019' AND PC.CateValue = '2'
                 $whereCondition
             GROUP BY A.AccountNo,A.ProjectNo
            ) as CH GROUP BY Project";

        DB::statement($sql);
    }
    public function InsertExpenseII_2($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project,Detail,SectorCateValue, I2, I4, I6, I8)
  SELECT * FROM
            (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project , 1, SectorCateValue,
                   MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2  THEN I ELSE 0 END) AS AA,
                   MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS BB,
                   MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS CC,
                   MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS DD
            FROM
                (SELECT 'A#II' AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project,SC.CateValue as SectorCateValue, A.AccountNo,YEAR(A.PeriodFromDate) AS YEARD, SUM(LCDebitAmount) as I
                 FROM act_gl_books as A
                          INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
                          INNER JOIN sector_cate as SC on SC.SectorID = A.SectorID
                 WHERE  A.AccountNo in('0461','0441','0721','0411')
               AND LEFT(SC.CateNo, 5) = '20002' AND SC.CateValue='3'
               AND LEFT(PC.CateNo, 3) =  '019' AND PC.CateValue ='2'
                     $whereCondition
                 GROUP BY A.AccountNo,A.ProjectNo
                ) as CH GROUP BY Project)as DT ON DUPLICATE KEY UPDATE I2 = AA, I4 = BB,I6 = CC, I8 = DD";

        DB::statement($sql);
    }
    public function InsertExpense1_3_5_7($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project,Detail,SectorCateValue, I1, I3, I5, I7)
               SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project , 1,SectorCateValue,
               MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2  THEN I ELSE 0 END) AS I1,
               MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
               MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I5,
               MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I7
        FROM
            (SELECT CONCAT('A','#',SC.CateValue) AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project, SC.CateValue as SectorCateValue, A.AccountNo,YEAR(A.PeriodFromDate) AS YEARD, SUM(FCDebitAmount) as I
             FROM act_gl_books as A
                      INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
                      INNER JOIN sector_cate as SC on SC.SectorID = A.SectorID
             WHERE  A.AccountNo in('0461','0441','0721','0411')
               AND LEFT(SC.CateNo, 5) = '20002'
               AND LEFT(PC.CateNo, 3) =  '019' AND PC.CateValue = '2'
                 $whereCondition
             GROUP BY A.AccountNo,A.ProjectNo
            ) as CH GROUP BY Project";

        DB::statement($sql);
    }

    public function InsertorUpdateExpense2_4_6_8($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Project,Detail,SectorCateValue, I2, I4, I6, I8)
  SELECT * FROM
            (SELECT CONCAT(ParentID,'#',Project), ParentID, ItemName, Project , 1,SectorCateValue,
                   MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2  THEN I ELSE 0 END) AS AA,
                   MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS BB,
                   MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS CC,
                   MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS DD
            FROM
                (SELECT CONCAT('A','#',SC.CateValue) AS ParentID, A.ProjectName as ItemName, A.ProjectNo as Project,SC.CateValue as SectorCateValue, A.AccountNo,YEAR(A.PeriodFromDate) AS YEARD, SUM(LCDebitAmount) as I
                 FROM act_gl_books as A
                          INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
                          INNER JOIN sector_cate as SC on SC.SectorID = A.SectorID
                 WHERE  A.AccountNo in('0461','0441','0721','0411')
               AND LEFT(SC.CateNo, 5) = '20002'
               AND LEFT(PC.CateNo, 3) =  '019' AND PC.CateValue = '2'
                     $whereCondition
                 GROUP BY A.AccountNo,A.ProjectNo
                ) as CH GROUP BY Project)as DT ON DUPLICATE KEY UPDATE I2 = AA, I4 = BB,I6 = CC, I8 = DD";

        DB::statement($sql);
    }

    // SUM
    public function sumUpSBPB258DH($start, $end, $maxLevel){
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4, SUM(M.I1) AS I5, SUM(M.I2) AS I6, SUM(M.I3) AS I7, SUM(M.I4) AS I8 FROM table_tmp M
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
                        $update = "UPDATE table_tmp
                    SET I1 = I1 + ".$I1.", I2 = I2 + ".$I2.", I3 = I3 + ".$I3.", I4 = I4 + ".$I4.", I5 = I5 + ".$I5.", I6 = I6 + ".$I6.", I7 = I7 + ".$I7.", I8= I8 + ".$I8."
                    WHERE ItemID = '".$value['ParentID']."'
                        AND table_tmp.Level = $j"; //Log::debug($update);
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
