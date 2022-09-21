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

class OperatingB248DHController extends Controller
{
    use Common;

    public function BCDHB02DHCH()
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }

    public function STDS02DHCH()
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }

    public function STDTHDTTNSNN02()
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }

    public function STDDTGTGT03()
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }


    public function SBPB248DH(Request $request)
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

        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN ($year - 2) AND $year AND A.InTransTypeID NOT IN (16,17,18) ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
            `STT` varchar(50) DEFAULT '',
            `ItemID` varchar(100) NOT NULL PRIMARY KEY,
            `ParentID` varchar(100) DEFAULT '',
            `ParentID1` varchar(100) DEFAULT '',
            `Level` int(2) DEFAULT 1,
            `ItemName` varchar(255) DEFAULT NULL,
            `FontWeight` tinyint(1) DEFAULT 0,
            `Italic` tinyint(1) DEFAULT 0,
            `Center` tinyint(1) DEFAULT 0,
            `Item` varchar(20) DEFAULT '',
            `Program` varchar(50) DEFAULT '',
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
            `I10` double(20,4) DEFAULT 0 NOT NULL,
            `I11` double(20,4) DEFAULT 0 NOT NULL,
            `I12` double(20,4) DEFAULT 0 NOT NULL,
            `I13` double(20,4) DEFAULT 0 NOT NULL,
            `Detail` tinyint(1) NOT NULL
        )   ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        // Thu
        $countRecords = DB::table('table_tmp')->count();

        $this->insertCommon($whereCondition, $year);
        if (DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeCommon(['program','project','']);
        }

        $this->setLevel();

        // Thiết lập STT
        $maxLevel = DB::table('table_tmp')->select(DB::raw('Max(Level) as maxLevel'))->first()->maxLevel;
        $this->setIndexReportB248(3, 'A#2');
        $this->setIndexReportB248(3, 'A#5');

        //sum row
        $this->sumUpRow();

        //Cấp 1
        $insertA = [
            ['STT' => 'A', 'ItemID' => 'A', 'ParentID' => '', 'Level' => 0, 'ItemName' => 'TỔNG THU', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'I', 'ItemID' => 'A#2', 'ParentID' => 'A', 'Level' => 1, 'ItemName' => 'Chương trình mục tiêu quốc gia', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'II', 'ItemID' => 'A#5', 'ParentID' => 'A', 'Level' => 1, 'ItemName' => 'Chương trình mục tiêu', 'FontWeight' => '1', 'Detail' => '0'],
        ];

        DB::table('table_tmp')->insert($insertA); // Query Builder approach

        $this->sumUpSBPB248DH(0, $maxLevel);

        $this->insertDetailB248();

        $insertA = [
            ['STT' => '', 'ItemID' => 'A#2#00#01', 'ParentID' => 'A#2', 'Level' => 2, 'ItemName' => 'Chi đầu tư phát triển', 'Detail' => '0'],
            ['STT' => '', 'ItemID' => 'A#2#00#02', 'ParentID' => 'A#2', 'Level' => 2, 'ItemName' => 'Chi thường xuyên', 'Detail' => '0'],
            ['STT' => '', 'ItemID' => 'A#5#00#01', 'ParentID' => 'A#5', 'Level' => 2, 'ItemName' => 'Chi đầu tư phát triển', 'Detail' => '0'],
            ['STT' => '', 'ItemID' => 'A#5#00#02', 'ParentID' => 'A#5', 'Level' => 2, 'ItemName' => 'Chi thường xuyên', 'Detail' => '0'],
        ];
        DB::table('table_tmp')->insert($insertA);

        $this->sumUpSBPB248DHDetail(2, $maxLevel);

        // insert detail

        $data = DB::table("table_tmp")->orderBy('ItemID', 'asc')->get();

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . ($year - 2);
            $data[0]->HeaderName2 = 'Dự toán năm ' . ($year - 1);
            $data[0]->HeaderName3 = 'Ước thực hiện năm ' . ($year - 1);
            $data[0]->HeaderName4 = 'Dự toán năm ' . $year;
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
        }


        $company = Company::find($companyID);
        $companyName = '';
        if ($company) {
            $companyName = $company->CompanyName;
        }
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
                "ReportName" => "DỰ TOÁN CHI CÁC CHƯƠNG TRÌNH MỤC TIÊU QUỐC GIA, CHƯƠNG TRÌNH MỤC TIÊU NĂM $year/GIAI ĐOẠN XXXX-
                $year ",
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

    public function insertCommon($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ParentID1, ItemName, Program, Project, Detail, I2, I3, I5, I6, I8, I9, I11, I12)
        SELECT CONCAT(ParentID, '#', CASE WHEN ExpenseCateNo = '20052' THEN '01'
                                            WHEN ExpenseCateNo = '20051' THEN '02' END) as ItemID, ParentID, ParentID1, ItemName, ProgramNo, ProjectNo, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('0461','0462') AND YearD = $year - 2 AND CapitalInOut = 1 THEN I ELSE 0 END) as I2,
        SUM(CASE WHEN AccountNo IN ('0461','0462') AND YearD = $year - 2 AND CapitalInOut = 2 THEN I ELSE 0 END) as I3,
        SUM(CASE WHEN AccountNo IN ('0441','0441') AND YearD = $year - 1 AND CapitalInOut = 1 THEN I ELSE 0 END) as I5,
        SUM(CASE WHEN AccountNo IN ('0441','0441') AND YearD = $year - 1 AND CapitalInOut = 2 THEN I ELSE 0 END) as I6,
        SUM(CASE WHEN AccountNo IN ('0721','0722') AND YearD = $year - 1 AND CapitalInOut = 1 THEN I ELSE 0 END) as I8,
        SUM(CASE WHEN AccountNo IN ('0721','0722') AND YearD = $year - 1 AND CapitalInOut = 2 THEN I ELSE 0 END) as I9,
        SUM(CASE WHEN AccountNo IN ('0411','0412') AND YearD = $year AND CapitalInOut = 1 THEN I ELSE 0 END) as I11,
        SUM(CASE WHEN AccountNo IN ('0411','0412') AND YearD = $year AND CapitalInOut = 2 THEN I ELSE 0 END) as I12 FROM
        (SELECT CONCAT('A#', PC.CateValue, '#', A.ProgramNo, '#', A.ProjectNo) as ParentID,
        CONCAT('A#',PC.CateValue, '#', A.ProgramNo, '#00#', CASE WHEN LEFT(EC.CateNo, 5) = '20052' THEN '01'
                                                                 WHEN LEFT(EC.CateNo, 5) = '20051' THEN '02' END) as ParentID1,
        CASE WHEN LEFT(EC.CateNo, 5) = '20052' THEN 'Chi đầu tư phát triển'
             WHEN LEFT(EC.CateNo, 5) = '20051' THEN 'Chi thường xuyên' END as ItemName,
        PC.CateValue as ProjectCateValue, A.ProgramNo, A.ProjectNo, LEFT(EC.CateNo, 5) as ExpenseCateNo,
        A.AccountNo, CapitalInOut, SUM(LCDebitAmount) as I, YEAR(A.PeriodFromDate) as YearD
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        WHERE A.AccountNo IN ('0461','0462','0441','0441','0721','0722','0411','0412')
        AND C.CapitalInOut IN (1,2)
        AND PC.CateNo = '006' AND PC.CateValue IN (2, 5)
        AND (LEFT(EC.CateNo, 5) = '20052' OR LEFT(EC.CateNo, 5) = '20051')
        $whereCondition
        GROUP BY PC.CateValue, C.CapitalInOut, A.AccountNo, YearD, LEFT(EC.CateNo ,5), A.ProgramNo, A.ProjectNo) as CH GROUP BY ProjectCateValue, ExpenseCateNo, ProgramNo, ProjectNo
        ";

        DB::statement($sql);
    }

    public function sumUpRow(){
        $listItems = DB::table('table_tmp')->where('Detail','=',1)->get();
        foreach($listItems as $item){
            $I1 = $item->I2 + $item->I3;
            $I4 = $item->I5 + $item->I6;
            $I7 = $item->I8 + $item->I9;
            $I10 = $item->I11 + $item->I12;
            DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update([
                'I1'=> $I1, 'I4'=> $I4, 'I7'=> $I7, 'I10'=> $I10
            ]);
        }

    }

    // SUM
    public function sumUpSBPB248DH($start, $maxLevel)
    {
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
           SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8,
           SUM(M.I9) AS I9, SUM(M.I10) AS I10, SUM(M.I11) AS I11, SUM(M.I12) AS I12
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
                    $update = "UPDATE table_tmp
                SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ",
                I9 = " . $I9 . ", I10 = " . $I10 . ", I11 = " . $I11 . ", I12 = " . $I12 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = $j"; //Log::debug($update);
                    DB::statement($update);
                }
            }
        }
    }

    public function setIndexReportB248($maxLevel, $id = null)
    {
        $insertArr = [];
        for ($i = 2; $i <= $maxLevel; $i++) {
            $insertArr1 = [];
            $where = '';
            if ($id !== null) {
                $where = "AND LEFT(ItemID, LENGTH('" . $id . "')) = '" . $id . "'";
            }
            $sql = "SELECT * FROM table_tmp WHERE Level = $i $where ORDER BY ItemID ASC ";
            $result = DB::select($sql);
            if ($i == 2) {
                foreach ($result as $key => $item) {
                    $insertArr1[] = [
                        'ItemID' => $item->ItemID,
                        'ParentID' => $item->ParentID,
                        'Level' => $item->Level,
                        'STT' => $key + 1
                    ];
                    DB::table('table_tmp')->where('ItemID', '=', $item->ItemID)->update(['STT' => $key + 1]);
                }
            } else {
                foreach ($insertArr[$i - 3] as $value) {
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

    public function insertDetailB248(){
        $listItem = DB::table('table_tmp')->where('Level','2')->where('Detail',0)->get();
        if($listItem){
            $insertArr = [];
            foreach($listItem as $item){
                $insertArr[] = [
                    'ItemID'=> $item->ItemID . '#00#01',
                    'ParentID'=> $item->ItemID,
                    'ParentID1'=> substr($item->ItemID, 0 ,4) . '00#01',
                    'ItemName'=> 'Chi đầu tư phát triển',
                    'Level'=> 3,
                    'Detail'=> 0,
                ];
                $insertArr[] = [
                    'ItemID'=> $item->ItemID . '#00#02',
                    'ParentID'=> $item->ItemID,
                    'ParentID1'=> substr($item->ItemID, 0 ,4) . '00#02',
                    'ItemName'=> 'Chi thường xuyên',
                    'Level'=> 3,
                    'Detail'=> 0,
                ];

            }
            DB::table('table_tmp')->insert($insertArr);
        }
    }

    public function sumUpSBPB248DHDetail($start, $maxLevel)
    {
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID1, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
           SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8,
           SUM(M.I9) AS I9, SUM(M.I10) AS I10, SUM(M.I11) AS I11, SUM(M.I12) AS I12
            FROM table_tmp M
            WHERE M.Level = " . $i . " AND ParentID1 != '' GROUP BY ParentID1";
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
                    $update = "UPDATE table_tmp
                SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ",
                I9 = " . $I9 . ", I10 = " . $I10 . ", I11 = " . $I11 . ", I12 = " . $I12 . "
                WHERE ItemID = '" . $value['ParentID1'] . "'
                    AND table_tmp.Level = $j"; //Log::debug($update);
                    DB::statement($update);
                }
            }
        }
    }

}

