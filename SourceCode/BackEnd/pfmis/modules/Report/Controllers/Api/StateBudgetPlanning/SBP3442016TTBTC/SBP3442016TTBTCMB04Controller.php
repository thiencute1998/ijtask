<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3442016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3442016TTBTCMB04Controller extends Controller {
    use Common;

    public function SBP3442016TTBTC_MB04(Request $request){
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
        $wherePeriodDate = " AND A.PeriodType = 9 AND YEAR(A.PeriodFromDate) >= $year AND YEAR(A.PeriodToDate) <= ($year + 5) $whereCommonCondition";
        $whereYear = " AND YEAR(A.PeriodFromDate) = $year $whereCommonCondition ";
        $wherePeriodDate1 = " AND A.PeriodFromDate = '" . $year . "-01-01' AND A.PeriodToDate = '" . $year . "-06-30' ";
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
            `Project` varchar(50) DEFAULT '',
            `PerformDate` varchar(255) DEFAULT '',
            `ProjectPhaseTemporary` varchar(20) DEFAULT '',
            `Detail` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $whereR2 = " AND (YEAR(P.StartedDate) < $year OR(YEAR(P.StartedDate) < $year AND YEAR(P.ExpectedFinishDate) = $year) OR YEAR(P.ExpectedStartDate) = $year OR (YEAR(P.ExpectedStartDate) = $year AND YEAR(P.ExpectedFinishDate) = $year) ) ";

        $this->insertProjectC2($whereDebt,$whereR2, $year);
        $this->insertOrUpdateC3($whereDebt,$whereR2, $year);
        $this->insertOrUpdateC7($whereDebt,$whereR2, $year);
        $this->insertOrUpdateC8($whereDebt,$whereR2, $year);
        $this->insertOrUpdateC9($whereDebt,$whereR2, $year);
        $this->setLevel();

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
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
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
        return "CONCAT(DATE_FORMAT(StartedDate, '%d/%m/%Y'), ' - ', DATE_FORMAT(FinishedDate, '%d/%m/%Y')) as PerformDate";
    }

    // Thực hiện dự án

    public function setCaseProjectPhase($year){
        return " CASE
            WHEN YEAR(ExpectedStartDate) < $year THEN '01'
            WHEN YEAR(ExpectedStartDate) < $year AND YEAR(ExpectedFinishDate) =$year  THEN '02'
            WHEN YEAR(ExpectedStartDate) = $year THEN '03'
            WHEN YEAR(ExpectedStartDate) = $year AND YEAR(ExpectedFinishDate) =$year  THEN '04' END";
    }

    public function insertProjectC2($whereDebt,$whereR2, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,PerformDate,ProjectPhaseTemporary,
            Project, Detail, I2)
            SELECT CONCAT('A#'," .  $this->setCaseProjectPhase($year) . ",'#', A.ProjectNo) as ItemID,
            CONCAT('A#'," .  $this->setCaseProjectPhase($year) . ") as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, A.ProjectNo as Project, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo = '091' AND A.BudgetLevel=4
            $whereDebt
            $whereR2
            GROUP BY A.ProjectNo,ProjectPhaseTemporary
            ";

        DB::statement($sql);
    }
    public function insertOrUpdateC3($whereDebt, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,PerformDate,ProjectPhaseTemporary,
            Project, Detail, I2)
            SELECT * FROM
            (SELECT CONCAT('A#'," .  $this->setCaseProjectPhase($year) . ",'#', A.ProjectNo) as ItemID,
            CONCAT('A#'," .  $this->setCaseProjectPhase($year) . ") as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, A.ProjectNo as Project, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
              INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo = '091' AND A.BudgetLevel=4
            AND CC.CateNo='200216'
            $whereDebt
            $whereR2
            GROUP BY A.ProjectNo,ProjectPhaseTemporary )  as DT ON DUPLICATE KEY UPDATE I2 = I
            ";

        DB::statement($sql);
    }
    public function insertOrUpdateC7($whereDebt, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,PerformDate,ProjectPhaseTemporary,
            Project, Detail, I2)
            SELECT * FROM
            (SELECT CONCAT('A#'," .  $this->setCaseProjectPhase($year) . ",'#', A.ProjectNo) as ItemID,
            CONCAT('A#'," .  $this->setCaseProjectPhase($year) . ") as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, A.ProjectNo as Project, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
              INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo = '0411' AND A.BudgetLevel=4
            AND A.isDebtTrans = 1
            $whereDebt
            $whereR2
            GROUP BY A.ProjectNo,ProjectPhaseTemporary )  as DT ON DUPLICATE KEY UPDATE I2 = I
            ";

        DB::statement($sql);
    }
    public function insertOrUpdateC8($whereDebt, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,PerformDate,ProjectPhaseTemporary,
            Project, Detail, I2)
            SELECT * FROM
            (SELECT CONCAT('A#'," .  $this->setCaseProjectPhase($year) . ",'#', A.ProjectNo) as ItemID,
            CONCAT('A#'," .  $this->setCaseProjectPhase($year) . ") as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, A.ProjectNo as Project, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
              INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo = '0411' AND A.BudgetLevel=4
            AND LEFT(CC.CateNo,4)='2001'
            $whereDebt
            $whereR2
            GROUP BY A.ProjectNo,ProjectPhaseTemporary )  as DT ON DUPLICATE KEY UPDATE I2 = I
            ";

        DB::statement($sql);
    }
    public function insertOrUpdateC9($whereDebt, $whereR2 , $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,PerformDate,ProjectPhaseTemporary,
            Project, Detail, I2)
            SELECT * FROM
            (SELECT CONCAT('A#'," .  $this->setCaseProjectPhase($year) . ",'#', A.ProjectNo) as ItemID,
            CONCAT('A#'," .  $this->setCaseProjectPhase($year) . ") as ParentID,
            A.ProjectName as ItemName, " . $this->selectCommon() . ", " . $this->setCaseProjectPhase($year) . " as ProjectPhaseTemporary, A.ProjectNo as Project, 1 as Detail,
            SUM(LCDebitAmount - LCCreditAmount) as I
            FROM act_gl_books AS A
              INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
            INNER JOIN Project as P on A.ProjectID = P.ProjectID
            WHERE A.AccountNo = '0411' AND A.BudgetLevel=4
            AND CC.CateNo='200216'
            $whereDebt
            $whereR2
            GROUP BY A.ProjectNo,ProjectPhaseTemporary )  as DT ON DUPLICATE KEY UPDATE I2 = I
            ";

        DB::statement($sql);
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
