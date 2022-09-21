<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Listing\Models\Program;
use Module\Listing\Models\Project;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB11Controller extends Controller {
    public function SBP3422016TTBTC_MB11_1(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : 2021;
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

        $nextYear = (int)$year + 1;

        $whereCondition = "AND (
        (YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $nextYear AND YEAR(A.PeriodToDate) = $nextYear)
        ) $whereCompany";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `ParentID2` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `Show` int(1) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `I11` double(20,4) DEFAULT NULL,
          `I12` double(20,4) DEFAULT NULL,
          `I13` double(20,4) DEFAULT NULL,
          `I21` double(20,4) DEFAULT NULL,
          `I22` double(20,4) DEFAULT NULL,
          `I23` double(20,4) DEFAULT NULL,
          `I31` double(20,4) DEFAULT NULL,
          `I32` double(20,4) DEFAULT NULL,
          `I33` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $this->MB11_1InsertTemplate();
        $this->MB11_1InsertTemplateCTMT();
        $this->MB11_1InsertTemplateDA();
        $this->MB11_1InsertTemplateCapital($whereCondition, $year, $nextYear);

        // sum up
        $this->sumUpTableTmp(1, 5);
        $this->sumUpTableTmp1(1, 5);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID')->get();

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $year . '(năm trước)';
            $data[0]->HeaderName2 = 'Dự toán năm ' . $year . '(năm hiện hành)';
            $data[0]->HeaderName3 = 'Ước thực hiện năm ' . $year . '(năm hiện hành)';
            $data[0]->HeaderName4 = 'Dự toán năm ' .$nextYear . '(năm kế hoạch)';
        }

        foreach ($data as $key => $value) {
            $data[$key]->I11 = ($data[$key]->I11) ? $data[$key]->I11 : '';
            $data[$key]->I12 = ($data[$key]->I12) ? $data[$key]->I12 : '';
            $data[$key]->I13 = ($data[$key]->I13) ? $data[$key]->I13 : '';
            $data[$key]->I21 = ($data[$key]->I21) ? $data[$key]->I21 : '';
            $data[$key]->I22 = ($data[$key]->I22) ? $data[$key]->I22 : '';
            $data[$key]->I23 = ($data[$key]->I23) ? $data[$key]->I23 : '';
            $data[$key]->I31 = ($data[$key]->I31) ? $data[$key]->I31 : '';
            $data[$key]->I32 = ($data[$key]->I32) ? $data[$key]->I32 : '';
            $data[$key]->I33 = ($data[$key]->I33) ? $data[$key]->I33 : '';
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
                "ReportName" => "DỰ TOÁN CHI BẰNG NGOẠI TỆ NĂM " . $nextYear,
                "DateTime" => $dateTime,
                "CompanyName" => ($company) ? $company->CompanyName : 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    // Level 1 -2
    public function MB11_1InsertTemplate(){
        $arrInsert = [
            [
                'STT' => '',
                'Show' => 1,
                'ItemID' => '#',
                'ParentID' => '',
                'ItemName' => 'Tổng số',
                'Level' => '1',
                'FontWeight' => '',
                'Italic' => '',
                'Center' => ''
            ],
            [
                'STT' => 'A',
                'Show' => 1,
                'ItemID' => '#A',
                'ParentID' => '#',
                'ItemName' => 'Chương trình mục tiêu quốc gia',
                'Level' => '2',
                'FontWeight' => '1',
                'Italic' => '',
                'Center' => ''
            ],
            [
                'STT' => 'B',
                'Show' => 1,
                'ItemID' => '#B',
                'ParentID' => '#',
                'ItemName' => 'Chương trình mục tiêu',
                'Level' => '2',
                'FontWeight' => '1',
                'Italic' => '',
                'Center' => ''
            ]
        ];
        DB::table('table_tmp')->insert($arrInsert);
    }


    public function MB11_1InsertTemplateCTMT(){
        $programsQuery = Program::query();
        $programsQuery->join('act_gl_books', 'program.ProgramID', '=', 'act_gl_books.ProgramID');
        $programsQuery->groupBy('program.ProgramID');
        $programs = $programsQuery->get();
        $arrInsert = [];
           $i = 1;
           $j = 1;
        foreach ($programs as $program) {
            $ParentID = '#B';
            if ($program->ProgramType === 1) {
                $ParentID = '#A';
                $stt = $i;
                $i ++;
            } else {
                $stt = $j;
                $j++;
            }
            $arrInsert[] = [
                'STT' => "$stt",
                'Show' => 1,
                'ItemID' => $ParentID . '#' . $program->ProgramNo,
                'ParentID' => $ParentID,
                'ItemName' => $program->ProgramName,
                'Level' => '3',
                'FontWeight' => '',
                'Italic' => '',
                'Center' => ''
            ];
            // Nguồn trong nước và nguồn ngoài nước
            $arrInsert[] = [
                'STT' => '',
                'Show' => 1,
                'ItemID' => $ParentID . '#' . $program->ProgramNo . '#' . '01',
                'ParentID' => $ParentID . '#' . $program->ProgramNo,
                'ItemName' => 'Nguồn trong nước',
                'Level' => '4',
                'FontWeight' => '',
                'Italic' => '',
                'Center' => ''
            ];
            // Nguồn vố ngoài nước
            $arrInsert[] = [
                'STT' => '',
                'Show' => 1,
                'ItemID' => $ParentID . '#' . $program->ProgramNo . '#' . '02',
                'ParentID' => $ParentID . '#' . $program->ProgramNo,
                'ItemName' => 'Nguồn ngoài nước',
                'Level' => '4',
                'FontWeight' => '',
                'Italic' => '',
                'Center' => ''
            ];

        }

        DB::table('table_tmp')->insert($arrInsert);
    }
    public function MB11_1InsertTemplateDA(){
        $projectQuery = Project::query();
        $projectQuery->select('project.*', 'act_gl_books.ProgramID', 'act_gl_books.ProgramName', 'act_gl_books.ProgramNo', 'act_gl_books.ProgramType');
        $projectQuery->join('act_gl_books', 'act_gl_books.ProjectID', '=', 'project.ProjectID');
        $projectQuery->groupBy('act_gl_books.ProgramNo', 'project.ProjectNo');
        $projects = $projectQuery->get();
        $arrInsert = [];

        foreach ($projects as $project) {
            $ParentID = '#B' . '#' . $project->ProgramNo;
            if ($project->ProgramType === 1) {
                $ParentID = '#A';
            }
            $arrInsert[] = [
                'STT' => "",
                'Show' => 1,
                'ItemID' => $ParentID . '#' . $project->ProgramNo . '#' . $project->ProjectNo,
                'ParentID' => '',
                'ItemName' => $project->ProjectName,
                'Level' => '4',
                'FontWeight' => '',
                'Italic' => '',
                'Center' => '',
            ];

        }

        if (count($arrInsert)) {
            DB::table('table_tmp')->insert($arrInsert);
        }
    }

    public function MB11_1InsertTemplateCapital($whereCondition, $year, $nextYear){
        $sqlSelect = "SELECT YearD, AccountNo, CapitalType, ExpenseCateNo, ProgramID, ProjectID, ProgramNo, ProgramType, ProjectNo,
                           MAX(CASE WHEN AccountNo = '0251' AND ExpenseCateNo = '20152' AND YearD = $year THEN I END) AS I12,
                           MAX(CASE WHEN AccountNo = '0251' AND ExpenseCateNo = '20151' AND YearD = $year THEN I END) AS I13,
                           MAX(CASE WHEN AccountNo = '0211' AND ExpenseCateNo = '20152' AND YearD = $year THEN I END) AS I22,
                           MAX(CASE WHEN AccountNo = '0211' AND ExpenseCateNo = '20151' AND YearD = $year THEN I END) AS I23,
                           MAX(CASE WHEN AccountNo = '0221' AND ExpenseCateNo = '20152' AND YearD = $nextYear THEN I END) AS I32,
                           MAX(CASE WHEN AccountNo = '0221' AND ExpenseCateNo = '20151' AND YearD = $nextYear THEN I END) AS I33
                    FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD, A.AccountNo, A.ProgramType, A.ProgramNo, A.ProgramID, A.ProjectID, A.ProjectNo, EC.CateNo AS ExpenseCateNo,
                                 CC.CateNo AS CapitalCateNo, (CASE
                                                                  WHEN CC.CateNo = '200111' OR CC.CateNo = '200121' THEN '01'
                                                                  WHEN CC.CateNo = '200112' OR CC.CateNo = '200112' THEN '02'
                                                                  ELSE '' END) AS CapitalType
                          FROM act_gl_books AS A
                                   INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                   INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                          WHERE EC.CateNo IN ('20152', '20151')
                            AND CC.CateNo IN ('200111', '200121', '200112', '200112')
                            AND A.AccountNo IN ('0251', '0211', '0221')
                            $whereCondition
                          GROUP BY YearD, A.AccountNo, A.ProgramID, A.ProjectID, EC.CateNo, CapitalType) AS B";
        $result = DB::select($sqlSelect);
        $result = (array)$result;
        if ($result) {
            $arrInsert = [];
            foreach ($result as $value) {
                $value = (array)$value;
                $ItemName = '';
                if ($value['CapitalType'] == '01') {
                    $ItemName = 'Nguồn trong nước';
                } elseif ($value['CapitalType'] == '02') {
                    $ItemName = 'Nguồn nước ngoài';
                }
                $ParentID = '#B';
                if ($value['ProgramType'] === 1) {
                    $ParentID = '#A';
                }

                $arrInsert[] = [
                    'STT' => '',
                    'Show' => 1,
                    'ItemID' => $ParentID . '#' . $value['ProgramNo'] . '#' . $value['ProjectNo'] . '#' . $value['CapitalType'],
                    'ParentID' => $ParentID . '#' . $value['ProgramNo'] . '#' . $value['ProjectNo'],
                    'ParentID2' => $ParentID . '#' . $value['ProgramNo'] . '#' . $value['CapitalType'],
                    'ItemName' => $ItemName,
                    'Level' => '5',
                    'FontWeight' => '',
                    'Italic' => '',
                    'Center' => '',
                    'I11' => $value['I12'] +$value['I13'],
                    'I12' => $value['I12'],
                    'I13' => $value['I13'],
                    'I21' => $value['I22'] +$value['I23'],
                    'I22' => $value['I22'],
                    'I23' => $value['I23'],
                    'I31' => $value['I32'] +$value['I33'],
                    'I32' => $value['I32'],
                    'I32' => $value['I33']
                ];
            }

            if (count($arrInsert)) {
                DB::table('table_tmp')->insert($arrInsert);
            }
        }
    }

    public function MB11_1SumUpTableLevel5()
    {

    }

    // sum up
    public function sumUpTableTmp($start, $end = null){
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID, SUM(M.I11) AS I11, SUM(M.I12) AS I12, SUM(M.I13) AS I13, SUM(M.I21) AS I21, SUM(M.I22) AS I22, SUM(M.I23) AS I23, SUM(M.I31) AS I31, SUM(M.I32) AS I32, SUM(M.I33) AS I33
                FROM table_tmp M
                WHERE M.Level = " . $i . " GROUP BY ParentID";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
                        $I11 = ($value['I11']) ? $value['I11'] : 0;
                        $I12 = ($value['I12']) ? $value['I12'] : 0;
                        $I13 = ($value['I13']) ? $value['I13'] : 0;
                        $I21 = ($value['I21']) ? $value['I21'] : 0;
                        $I22 = ($value['I22']) ? $value['I22'] : 0;
                        $I23 = ($value['I23']) ? $value['I23'] : 0;
                        $I31 = ($value['I31']) ? $value['I31'] : 0;
                        $I32 = ($value['I32']) ? $value['I32'] : 0;
                        $I33 = ($value['I33']) ? $value['I33'] : 0;
                        $update = "UPDATE table_tmp
                    SET  I11 = " . $I11 . ", I12 = " . $I12 . ", I13 = " . $I13 . ", I21 = " . $I21 . ", I22 = " . $I22 . ", I23 = " . $I23 . ", I31 = " . $I31 . ", I32 = " . $I32 . ", I33 = " . $I33 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }


    public function sumUpTableTmp1($start, $end = null){
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID2, SUM(M.I11) AS I11, SUM(M.I12) AS I12, SUM(M.I13) AS I13, SUM(M.I21) AS I21, SUM(M.I22) AS I22, SUM(M.I23) AS I23, SUM(M.I31) AS I31, SUM(M.I32) AS I32, SUM(M.I33) AS I33
                FROM table_tmp M
                WHERE M.Level = " . $i . " GROUP BY ParentID2";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
                        $I11 = ($value['I11']) ? $value['I11'] : 0;
                        $I12 = ($value['I12']) ? $value['I12'] : 0;
                        $I13 = ($value['I13']) ? $value['I13'] : 0;
                        $I21 = ($value['I21']) ? $value['I21'] : 0;
                        $I22 = ($value['I22']) ? $value['I22'] : 0;
                        $I23 = ($value['I23']) ? $value['I23'] : 0;
                        $I31 = ($value['I31']) ? $value['I31'] : 0;
                        $I32 = ($value['I32']) ? $value['I32'] : 0;
                        $I33 = ($value['I33']) ? $value['I33'] : 0;
                        $update = "UPDATE table_tmp
                    SET  I11 = " . $I11 . ", I12 = " . $I12 . ", I13 = " . $I13 . ", I21 = " . $I21 . ", I22 = " . $I22 . ", I23 = " . $I23 . ", I31 = " . $I31 . ", I32 = " . $I32 . ", I33 = " . $I33 . "
                    WHERE ItemID = '" . $value['ParentID2'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }
    public  function SortSTT(){
        $result =  DB::table('table_tmp')->where('Level', '=',3)->orderBy('ItemID')->get();
        foreach ($result as $value){
                $childs = DB::table('table_tmp')->where('ParentID', '=', $value->ItemID);
                $i = 1;
                foreach ($childs as $child){
                    $stt = IjNumber::ConvertNumberToAlpha($i);
                    DB::table('table_tmp')->where('ItemID', $child->ItemID)->update(['STT'=>$stt]);
                    $i ++;
                }
        }
    }


}
