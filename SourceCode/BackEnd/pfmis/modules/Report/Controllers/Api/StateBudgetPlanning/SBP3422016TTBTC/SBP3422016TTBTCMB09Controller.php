<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB09Controller extends Controller
{
    use Common;

    private $tnDateType = 1;
    private $tnAmountType = 1;
    private $tcCoaTypeNo = '01';

    public function SBP3422016TTBTC_MB09(Request $request)
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : 2021;
        $fromDate = (isset($filter['Year'])) ? $filter['Year'] . '-01-01' : $year . '-01-01';
        $toDate = (isset($filter['Year'])) ? $filter['Year'] . '-12-31' : $year . '-12-31';
        $filterDate = date('Y-m-d', strtotime('+1 year', strtotime($toDate)));
        $currentDate = date('Y-m-d');
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

        $whereCondition = "AND (
        (YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $yearPre AND YEAR(A.PeriodToDate) = $yearPre)
        OR (YEAR(A.PeriodFromDate) = $yearNext AND YEAR(A.PeriodToDate) = $yearNext)
        ) $whereCompany AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
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
          `I24` double(20,4) DEFAULT NULL,
          `I25` double(20,4) DEFAULT NULL,
          `I26` double(20,4) DEFAULT NULL,
          `I27` double(20,4) DEFAULT NULL,
          `I28` double(20,4) DEFAULT NULL,
          `I29` double(20,4) DEFAULT NULL,
          `I30` double(20,4) DEFAULT NULL,
          `I31` double(20,4) DEFAULT NULL,
          `I32` double(20,4) DEFAULT NULL,
          `I33` double(20,4) DEFAULT NULL,
          `I34` double(20,4) DEFAULT NULL,
          `I35` double(20,4) DEFAULT NULL,
          `I36` double(20,4) DEFAULT NULL,
          `I37` double(20,4) DEFAULT NULL,
          `I38` double(20,4) DEFAULT NULL,
          `I39` double(20,4) DEFAULT NULL,
          `I40` double(20,4) DEFAULT NULL,
          `I41` double(20,4) DEFAULT NULL,
          `I42` double(20,4) DEFAULT NULL,
          `I43` double(20,4) DEFAULT NULL,
          `I44` double(20,4) DEFAULT NULL,
          `I45` double(20,4) DEFAULT NULL,
          `I46` double(20,4) DEFAULT NULL,
          `I47` double(20,4) DEFAULT NULL,
          `I48` double(20,4) DEFAULT NULL,
          `I49` double(20,4) DEFAULT NULL,
          `I50` double(20,4) DEFAULT NULL,
            `SbiCategoryCateValue` varchar(20) DEFAULT '',
            `SponsorName` varchar(255) DEFAULT '',
            `PerformDate` varchar(50) DEFAULT '',
            `ProjectNo` varchar(50) DEFAULT '',
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb09')->get();

        $insertArr = [];
        foreach ($templateTable as $template) {
            $insertArr[] = [
                'STT' => $template->STT,
                'ItemID' => $template->ItemID,
                'ParentID' => $template->ParentID,
                'Level' => $template->Level,
                'ItemName' => $template->ItemName,
                'FontWeight' => $template->FontWeight,
                'Italic' => $template->Italic,
                'Center' => $template->Center,
                'SbiCategoryCateValue' => $template->SbiCategoryCateValue,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
                'I5' => $template->I5,
                'I6' => $template->I6,
                'I7' => $template->I7,
                'I8' => $template->I8,
                'I9' => $template->I9,
                'I10' => $template->I10,
                'I11' => $template->I11,
                'I12' => $template->I12,
                'I13' => $template->I13,
                'I14' => $template->I14,
                'I15' => $template->I15,
                'I16' => $template->I16,
                'I17' => $template->I17,
                'I18' => $template->I18,
                'I19' => $template->I19,
                'I20' => $template->I20,
                'I21' => $template->I21,
                'I22' => $template->I22,
                'I23' => $template->I23,
                'I24' => $template->I24,
                'I25' => $template->I25,
                'I26' => $template->I26,
                'I27' => $template->I27,
                'I28' => $template->I28,
                'I29' => $template->I29,
                'I30' => $template->I30,
                'I31' => $template->I31,
                'I32' => $template->I32,
                'I33' => $template->I33,
                'I34' => $template->I34,
                'I35' => $template->I35,
                'I36' => $template->I36,
                'I37' => $template->I37,
                'I38' => $template->I38,
                'I39' => $template->I39,
                'I40' => $template->I40,
                'I41' => $template->I41,
                'I42' => $template->I42,
                'I43' => $template->I43,
                'I44' => $template->I44,
                'I45' => $template->I45,
                'I46' => $template->I46,
                'I47' => $template->I47,
                'I48' => $template->I48,
                'I49' => $template->I49,
                'I50' => $template->I50,
                'SponsorName' => $template->SponsorName,
                'PerformDate' => $template->PerformDate,
                'ProjectNo' => $template->ProjectNo,
            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        // sum up
        $this->sumUpTableTmp(1, 4);
        $this->insertC3C6C9C12($currentDate);
        $this->insertC4C7C10C13C5C8C11C14($currentDate);
        $this->insertC5C8C11C14($currentDate);



        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Lũy kế thực hiện đến 31/12/ ' . $yearPre;
            $data[0]->HeaderName2 = 'Đánh giá thực hiện năm ' . $year;
            $data[0]->HeaderName3 = 'Dự toán năm ' . $yearNext;
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
                "ReportName" => "TỔNG HỢP DỰ TOÁN THU, CHI TỪ NGUỒN VIỆN TRỢ VÀ VỐN ĐỐI ỨNG NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    // sum up
    public function sumUpTableTmp($start, $end = null)
    {
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
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
                        $update = "UPDATE table_tmp
                    SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

    public function insertC3C6C9C12($currentDate){
        $sql = "UPDATE table_tmp as TT, (SELECT SbiCategoryCateValue, ProjectCateValue,
                                MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20152' THEN I END) AS I3,
                                MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20151' THEN I END) as I6,
                                MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20152' THEN I END) AS I9,
                                MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20151' THEN I END) as I12
                         FROM (SELECT SC.CateValue as SbiCategoryCateValue, A.ProjectNo, A.BudgetAllocTypeID, EC.CateNo as ExpenseCateNo, IF(1 = 1, SUM(LCDebitAmount),
                             SUM(FCDebitAmount)) AS I,  PC.CateValue as ProjectCateValue
                               FROM act_gl_books AS A
                                        INNER JOIN sbi_category_cate as SC on A.SbiCategoryID = SC.SbiCategoryID
                                        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
                                        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
                                        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
                               WHERE A.InTransTypeID not in (16,17,18)
                                 AND SC.CateNo = '001' AND SC.CateValue IS NOT NULL
                                 AND PC.CateNo = '006' AND PC.CateValue IS NOT NULL
                                 AND A.AccountNo = '091'
                                 AND BudgetAllocTypeID IN (1,3)
                                 AND (LEFT(EC.CateNo,5) = '20151' OR LEFT(EC.CateNo,5) = '20152')
                                 AND (CC.CateNo = '20012204'
                                   OR (CC.CateNo = '20011205' AND CC.CateValue = '1'))
                                 AND PostType = 1
                                 AND (IF($this->tnDateType = 1,
                                         (YEAR(PostDate) <= $currentDate),
                                         (YEAR(TransDate) <= $currentDate)
                                   )) AND CoaTypeNo = '01' GROUP BY SC.CateValue, A.ProjectNo, A.BudgetAllocTypeID, EC.CateNo
                              ) as CH GROUP BY SbiCategoryCateValue, ProjectNo) as HH SET TT.I3 = HH.I3, TT.I6 = HH.I6, TT.I9 = HH.I9, TT.I12= HH.I12 WHERE TT.SbiCategoryCateValue = HH.SbiCategoryCateValue
                                                                                                                                                        AND TT.ProjectCateValue = HH.ProjectCateValue
                                                                                                                                                        AND TT.Level = 4";
        DB::statement($sql);
    }
    public function insertC4C7C10C13C5C8C11C14($currentDate){
            $sql = "UPDATE table_tmp as TT, (SELECT SbiCategoryCateValue, ProjectCateValue,
                                MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20152' AND ReceiveBy = 2 THEN I END) AS I4,
                                MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20151' AND ReceiveBy = 2 THEN I END) as I7,
                                MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20152' AND ReceiveBy = 2 THEN I END) AS I10,
                                MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20151' AND ReceiveBy = 2 THEN I END) as I13,
                                MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20152' AND ReceiveBy = 1 THEN I END) AS I5,
                                MAX(CASE WHEN BudgetAllocTypeID = 1 AND LEFT(ExpenseCateNo,5) = '20151' AND ReceiveBy = 1 THEN I END) as I8,
                                MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20152' AND ReceiveBy = 1 THEN I END) AS I11,
                                MAX(CASE WHEN BudgetAllocTypeID = 3 AND LEFT(ExpenseCateNo,5) = '20151' AND ReceiveBy = 1 THEN I END) as I14
                         FROM (SELECT SC.CateValue as SbiCategoryCateValue, A.ProjectNo, A.BudgetAllocTypeID, EC.CateNo as ExpenseCateNo, IF(1 = 1, SUM(LCDebitAmount),
                             SUM(FCDebitAmount)) AS I,  PC.CateValue as ProjectCateValue, A.ReceiveBy
                               FROM act_gl_books AS A
                                        INNER JOIN sbi_category_cate as SC on A.SbiCategoryID = SC.SbiCategoryID
                                        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
                                        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
                                        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
                               WHERE A.InTransTypeID not in (16,17,18)
                                 AND SC.CateNo = '001' AND SC.CateValue IS NOT NULL
                                 AND PC.CateNo = '006' AND PC.CateValue IS NOT NULL
                                 AND A.AccountNo = '091'
                                 AND BudgetAllocTypeID IN (1,3)
                                 AND ReceiveBy IN (1,2)
                                 AND (LEFT(EC.CateNo,5) = '20151' OR LEFT(EC.CateNo,5) = '20152')
                                 AND CC.CateNo IN ('200111','200121')
                                 AND PostType = 1
                                 AND (IF($this->tnDateType = 1,
                                         (YEAR(PostDate) <= $currentDate),
                                         (YEAR(TransDate) <= $currentDate)
                                   )) AND CoaTypeNo = '01' GROUP BY SC.CateValue, A.ProjectNo, A.BudgetAllocTypeID, EC.CateNo
                              ) as CH GROUP BY SbiCategoryCateValue, ProjectNo) as HH SET TT.I4 = HH.I4, TT.I7 = HH.I7, TT.I10 = HH.I10, TT.I13= HH.I13,
                                                                                          TT.I5 = HH.I5, TT.I8 = HH.I8, TT.I11 = HH.I11, TT.I14= HH.I14
                                WHERE TT.SbiCategoryCateValue = HH.SbiCategoryCateValue AND TT.ProjectCateValue = HH.ProjectCateValue
                                  AND TT.Level = 4    ";
            DB::statement($sql);
    }

}
