<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB13_7Controller extends Controller {
    use Common;

    public function SBP3422016TTBTC_MB13_7(Request $request){
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

        $whereCommonCondition = "AND A.InTransTypeID NOT IN (16,17,18) AND SC.CateNo = '2000311' AND SC.CateValue = 11 ";
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
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          `I5` double(20,4) DEFAULT NULL,
          `I6` double(20,4) DEFAULT NULL,
          `I7` double(20,4) DEFAULT NULL,
          `I8` double(20,4) DEFAULT NULL,
            `Program` varchar(50) DEFAULT '',
            `Project` varchar(50) DEFAULT '',
            `PerformDate` varchar(255) DEFAULT '',
            `InvestNo` varchar(255) DEFAULT '',
            `Detail` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        // I, II

        $this->insertProjectR1C3($whereDebt, $year);
        $this->insertOrUpdateProjectR1C4_5_6_8($whereCondition, $year);
        $this->insertOrUpdateProjectR1C7($whereCondition1, $year);

//        III
        $this->insertProjectR3C3($whereDebt, $year);
        $this->insertOrUpdateProjectR3C4_5_6_8($whereCondition, $year);
        $this->insertOrUpdateProjectR3C7($whereCondition1, $year);

        $this->setLevel();

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
            $data[0]->HeaderName3 = 'Lũy kế số bố trí đến hết năm ' .$year;
            $data[0]->HeaderName4 = 'Dự toán năm ' .$yearNext;

        }

        foreach ($data as $key => $value) {
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
            $data[$key]->I7 = ($data[$key]->I7) ? $data[$key]->I7 : '';
            $data[$key]->I8 = ($data[$key]->I8) ? $data[$key]->I8 : '';
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
        return "  P.InvestdocNo as InvestNo,
        CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as PerformDate";
    }

    public function selectPerformDate($year){
        return " CASE WHEN P.StartedDate <  $year THEN '1'
                      WHEN P.StartedDate > '" . $year . "-01-01' THEN '2' END ";
    }

    public function insertProjectR1C3($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,  InvestNo, PerformDate,
            Project, Detail, I3)
            SELECT CONCAT('A#',PC.CateValue, '#1#', A.ProjectNo) as ItemID,
            CONCAT('A#',PC.CateValue, '#1#' ) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo as Project, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo = '091'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo = '016'
            $whereDebt
            GROUP BY A.ProjectNo,PC.CateValue
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR1C4_5_6_8($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,InvestNo,PerformDate,
            Project, Detail, I4,I5,I6,I8)
            SELECT * FROM
            (SELECT CONCAT('A#',ProjectCateValue, '#', ProjectNo) as ItemID,ParentID,ItemName, InvestNo,PerformDate,ProjectNo, 1 as Detail,
            MAX(CASE WHEN AccountNo IN ('0461','0462') AND YearD = $year - 1 THEN I ELSE 0 END) as A4,
            MAX(CASE WHEN AccountNo IN ('0441','0442') AND YearD = $year THEN I ELSE 0 END) as A5,
            MAX(CASE WHEN AccountNo IN ('0721','0722') AND YearD = $year  THEN I ELSE 0 END) as A6,
            MAX(CASE WHEN AccountNo IN ('0411','0412') AND YearD = $year + 1 THEN I ELSE 0 END) as A8 FROM
            (SELECT CONCAT('A#', PC.CateValue ) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo,
            PC.CateNo as ProjectCateNo, A.AccountNo, YEAR(A.PeriodFromDate) as YearD ,SUM(LCDebitAmount) as I, PC.CateValue as ProjectCateValue
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) IN('2001','2002')
            AND A.AccountNo IN ('0461','0462','0441','0442','0721', '0722','0411','0412')
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo =' 016' AND PC.CateValue IN ('1','2')
            $whereCondition
            GROUP BY  PC.CateNo, A.ProjectNo, YearD, A.AccountNo) as CH GROUP BY ProjectCateNo, ProjectNo)  as DT ON DUPLICATE KEY UPDATE I4 = A4,I5 = A5,I6 = A6,I8 = A8
            ";

        DB::statement($sql);
    }

    public function insertOrUpdateProjectR1C7($whereCondition1, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestNo,PerformDate,
            Project, Detail, I7)
            SELECT * FROM
          (SELECT CONCAT('A#',ProjectCateValue, '#', ProjectNo) as ItemID,ParentID,ItemName, InvestNo,PerformDate,ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) as I
            FROM  (SELECT CONCAT('A#', PC.CateValue ) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo,
            PC.CateNo as ProjectCateNo, A.AccountNo, YEAR(A.PeriodFromDate) as YearD ,SUM(LCDebitAmount) as I, PC.CateValue as ProjectCateValue
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) IN('2001','2002')
            AND A.AccountNo IN ('0441','0442')
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo = '016' AND PC.CateValue IN ('1','2')
            $whereCondition1
            GROUP BY  PC.CateNo, A.ProjectNo, YearD, A.AccountNo) as CH GROUP BY ProjectCateNo, ProjectNo) as DT ON DUPLICATE KEY UPDATE I7 = I
            ";

        DB::statement($sql);
    }

    public function insertProjectR3C3($whereDebt, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I3)
            SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#' , A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1)) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo = '091'
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo = '016' AND PC.CateValue IN ('1','2')
            $whereDebt
            GROUP BY PC.CateNo, A.ProjectNo
            ";

        DB::statement($sql);
    }
    public function insertOrUpdateProjectR3C4_5_6_8($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I3)
            SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#' , A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1)) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            MAX(CASE WHEN AccountNo IN ('0461','0462') AND YearD = $year - 1 THEN I ELSE 0 END) as A4,
            MAX(CASE WHEN AccountNo IN ('0441','0442') AND YearD = $year THEN I ELSE 0 END) as A5,
            MAX(CASE WHEN AccountNo IN ('0721','0722') AND YearD = $year  THEN I ELSE 0 END) as A6,
            MAX(CASE WHEN AccountNo IN ('0411','0412') AND YearD = $year + 1 THEN I ELSE 0 END) as A8
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) IN('2001','2002')
            AND A.AccountNo IN ('0461','0462','0441','0442','0721', '0722','0411','0412')
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo =' 016' AND PC.CateValue IN ('1','2')
            $whereCondition
            GROUP BY PC.CateNo, A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I4 = A4,I5 = A5,I6 = A6,I8 = A8
            ";

        DB::statement($sql);
    }
    public function insertOrUpdateProjectR3C7($whereCondition1, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, InvestorName, PerformDate, Invest,
            Project, Detail, I3)
            SELECT CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1), '#' , A.ProjectNo) as ItemID,
            CONCAT('A#', SUBSTRING_INDEX(PC.CateNo, '.', -1)) as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", A.ProjectNo, 1 as Detail,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN sector_cate as SC on A.SectorID = SC.SectorID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE LEFT(CC.CateNo,4) IN('2001','2002')
            AND A.AccountNo IN ('0441','0442')
            AND (P.StartedDate < '" . $year . "-01-01' OR P.StartedDate > '" . $year . "-01-01')
            AND PC.CateNo = '016' AND PC.CateValue IN ('1','2')
            $whereCondition1
            GROUP BY A.ProjectNo) as DT ON DUPLICATE KEY UPDATE I7 = I
            ";

        DB::statement($sql);
    }


    public function insertDataDefault(){
        $insert = [
            ['STT'=>'I', 'ItemID'=> 'A#01', 'ParentID'=> '', 'Level'=> 1, 'ItemName'=> 'Kinh phí thực hiện nhiệm vụ, dự án về bảo vệ môi trường', 'FontWeight'=> '1'],
            ['STT'=>'1', 'ItemID'=> 'A#01#1', 'ParentID'=> 'A#01', 'Level'=> 2, 'ItemName'=> 'Nhiệm vụ chuyển tiếp', 'FontWeight'=> '0'],
            ['STT'=>'2', 'ItemID'=> 'A#01#2', 'ParentID'=> 'A#01', 'Level'=> 2, 'ItemName'=> 'Dự án mở mới', 'FontWeight'=> '0'],

            ['STT'=>'II', 'ItemID'=> 'A#02', 'ParentID'=> '', 'Level'=> 1, 'ItemName'=> 'Kinh phí hỗ trợ xử lý cơ sở gây ô nhiễm theo quyết định của cấp có thẩm quyền', 'FontWeight'=> '1'],
            ['STT'=>'1', 'ItemID'=> 'A#02#1', 'ParentID'=> 'A#02', 'Level'=> 2, 'ItemName'=> 'Dự án chuyển tiếp', 'FontWeight'=> '0'],
            ['STT'=>'2', 'ItemID'=> 'A#02#2', 'ParentID'=> 'A#02', 'Level'=> 2, 'ItemName'=> 'Dự án mở mới ', 'FontWeight'=> '0'],

            ['STT'=>'III', 'ItemID'=> 'A#03', 'ParentID'=> '', 'Level'=> 1, 'ItemName'=> 'Các hoạt động môi trường khác ', 'FontWeight'=> '1'],
        ];

        DB::table("table_tmp")->insert($insert);
    }
    // sum row
    public function sumUpTableTmp($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID,SUM(M.I3) AS I3, SUM(M.I4) AS I4,
           SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8
            FROM table_tmp M
            WHERE M.Level = " . $i . " GROUP BY ParentID";
            $result = DB::select($sql);
            $result = (array)$result;
            if ($result) {
                foreach ($result as $value) {
                    $value = (array)$value;
                    $I3 = ($value['I3']) ? $value['I3'] : 0;
                    $I4 = ($value['I4']) ? $value['I4'] : 0;
                    $I5 = ($value['I5']) ? $value['I5'] : 0;
                    $I6 = ($value['I6']) ? $value['I6'] : 0;
                    $I7 = ($value['I7']) ? $value['I7'] : 0;
                    $I8 = ($value['I8']) ? $value['I8'] : 0;
                    $update = "UPDATE table_tmp
                SET  I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . "

                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }

    }

    public function setIndexMB13_7($maxLevel, $id = null, $index, $stt = ''){
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
