<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Listing\Models\Program;
use Module\Listing\Models\Project;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB11_2Controller extends Controller {
    public function SBP3422016TTBTC_MB11_2(Request $request){
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
          `Level2` int(2) DEFAULT 1,
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
        $this->MB11_2InsertTemplate();
        $this->MB11_2InsertTemplateCTMT_DN();
        $this->MB11_2InsertTemplateCapitalAlpha1();
        $this->MB11_2InsertTemplateCapitalAlpha1_1($whereCondition, $year, $nextYear);
        $this->MB11_2InsertTemplateCapitalAlpha1_2($whereCondition, $year, $nextYear);
        $this->MB11_2InsertTemplateCapitalAlpha2($whereCondition, $year, $nextYear);
        $this->MB11_2InsertTemplateCapitalAlpha3($whereCondition, $year, $nextYear);

        $this->sumUpTableTmp(1,5);

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
    public function MB11_2InsertTemplate(){
        $arrInsert = [
            [
                'STT' => '',
                'Show' => 1,
                'ItemID' => '#',
                'ParentID' => '',
                'ParentID2' =>'',
                'ItemName' => 'Tổng số',
                'Level' => 2,
                'Level2'=>'',
                'FontWeight' => '',
                'Italic' => '',
                'Center' => ''
            ],
            [
                'STT' => 'A',
                'Show' => 1,
                'ItemID' => '#A',
                'ParentID' => '#',
                'ParentID2' =>'',
                'ItemName' => 'Chương trình mục tiêu quốc gia',
                'Level' => 2,
                'Level2'=>'',
                'FontWeight' => 1,
                'Italic' => '',
                'Center' => ''
            ],
            [

                'STT' => 'B',
                'Show' => 1,
                'ItemID' => '#B',
                'ParentID' => '#',
                'ParentID2' =>'',
                'ItemName' => 'Chương trình mục tiêu',
                'Level' => 2,
                'Level2'=>'',
                'FontWeight' => '',
                'Italic' => '',
                'Center' => ''
            ],

        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#B'. '#01',
            'ParentID' => '#B',
            'ParentID2' =>'',
            'ItemName' => 'Ngân sách trung ương',
            'Level' => 3,
            'Level2'=>'',
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#B' . '#01'.'#01',
            'ParentID' =>  '#B' . '#01',
            'ParentID2' =>'',
            'ItemName' => 'Nguồn trong nước',
            'Level' => 4,
            'Level2'=>'',
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#B' . '#01'.'#02',
            'ParentID' =>  '#B' . '#01',
            'ParentID2' =>'',
            'ItemName' => 'Nguồn ngoài nước',
            'Level' => 4,
            'Level2'=>'',
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#B' . '#02',
            'ParentID' => '#B',
            'ParentID2' =>'',
            'ItemName' => 'Ngân sách địa phương',
            'Level' => 3,
            'Level2'=>'',
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#B' . '#03',
            'ParentID' => '#B',
            'ParentID2' =>'',
            'ItemName' => 'Nguồn huy động hợp pháp khác',
            'Level' => 3,
            'Level2'=>'',
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        DB::table('table_tmp')->insert($arrInsert);
    }
    public function MB11_2InsertTemplateCTMT_DN(){
        $results = DB::table('act_gl_books')->join('program_cate', 'act_gl_books.ProgramID', '=', 'program_cate.ProgramID')
                                                ->where('program_cate.CateNo', '=', '001')
                                                ->groupBy('act_gl_books.ProgramNo')->get();

        $arrInsert = [];
        $i = 1;
        $j = 1;
        foreach ($results as $value){

            //========================================= A  ==============================================================
           if($value->CateValue == 1){
               $ParentID = '#A';
               $arrInsert[] = [
                   'STT' => $i,
                   'Show' => 1,
                   'ItemID' => $ParentID . '#' . $value->ProgramNo,
                   'ParentID' => $ParentID,
                   'ParentID2' =>'',
                   'ItemName' => $value->ProgramName,
                   'Level' => 3,
                   'Level2'=>'',
                   'FontWeight' => '',
                   'Italic' => '',
                   'Center' => ''
               ];
               // 1.1 - level 4
               $arrInsert[] = [
                   'STT' => '',
                   'Show' => 1,
                   'ItemID' => $ParentID . '#' . $value->ProgramNo.'#01',
                   'ParentID' =>  $ParentID . '#' . $value->ProgramNo,
                   'ParentID2' =>'',
                   'ItemName' => 'Ngân sách trung ương',
                   'Level' => 4,
                   'Level2'=>'',
                   'FontWeight' => '',
                   'Italic' => '',
                   'Center' => ''
               ];
               // 1.1.1 - level 5
               $arrInsert[] = [
                   'STT' => '',
                   'Show' => 1,
                   'ItemID' => $ParentID . '#' . $value->ProgramNo.'#01'.'#01',
                   'ParentID' =>  $ParentID . '#' . $value->ProgramNo.'#01',
                   'ParentID2' =>'',
                   'ItemName' => 'Nguồn trong nước',
                   'Level' => 5,
                   'Level2'=>'',
                   'FontWeight' => '',
                   'Italic' => '',
                   'Center' => ''
               ];
               // 1.1.2 - level 5
               $arrInsert[] = [
                   'STT' => '',
                   'Show' => 1,
                   'ItemID' => $ParentID . '#' . $value->ProgramNo.'#01'.'#02',
                   'ParentID' =>  $ParentID . '#' . $value->ProgramNo.'#01',
                   'ParentID2' =>'',
                   'ItemName' => 'Nguồn ngoài nước(vốn vay và viện trợ)',
                   'Level' => 5,
                   'Level2'=>'',
                   'FontWeight' => '',
                   'Italic' => '',
                   'Center' => ''
               ];
               // 1.2 - level 4
               $arrInsert[] = [
                   'STT' => '',
                   'Show' => 1,
                   'ItemID' => $ParentID . '#' . $value->ProgramNo.'#02',
                   'ParentID' =>  $ParentID . '#' . $value->ProgramNo,
                   'ParentID2' =>'',
                   'ItemName' => 'Ngân sách địa phương',
                   'Level' => 4,
                   'Level2'=>'',
                   'FontWeight' => '',
                   'Italic' => '',
                   'Center' => ''
               ];
               // 1.3 - level 4
               $arrInsert[] = [
                   'STT' => '',
                   'Show' => 1,
                   'ItemID' => $ParentID . '#' . $value->ProgramNo.'#03',
                   'ParentID' =>  $ParentID . '#' . $value->ProgramNo,
                   'ParentID2' => '',
                   'ItemName' => 'Nguồn huy động hợp pháp khác',
                   'Level' => 4,
                   'Level2'=>'',
                   'FontWeight' => '',
                   'Italic' => '',
                   'Center' => ''
               ];


           }

           // ========================================= B ==============================================================

            if($value->CateValue == 2){
                $ParentID = '#B';

                $arrInsert[] = [
                    'STT' => $j,
                    'Show' => 1,
                    'ItemID' => $ParentID . '#' . $value->ProgramNo,
                    'ParentID' => $ParentID,
                    'ParentID2' =>'',
                    'ItemName' => $value->ProgramName,
                    'Level' => 3,
                    'Level2'=>'',
                    'FontWeight' => '',
                    'Italic' => '',
                    'Center' => ''
                ];
            }

            $i++;
            $j++;

            //========================================== Project fix alpha =============================================
            $x = 1;
            $projects = DB::table('act_gl_books')->where('ProgramID', '=', $value->ProgramID)->groupBy('ProjectNo')->get();

            foreach ($projects as $project){
                if($project->ProgramNo && $project->ProjectNo){
                    $sttProject = IjNumber::ConvertNumberToAlpha($x);
                    $arrInsert[] =[
                        'STT'=>$sttProject,
                        'Show' => 1,
                        'ItemID' =>$ParentID.'#'.$project->ProgramNo.'#'.$project->ProjectNo,
                        'ParentID'=>'',
                        'ParentID2'=>'',
                        'Level' => 4,
                        'Level2' => 1,
                        'ItemName'=>$project->ProjectName,
                        'FontWeight' => '',
                        'Italic' => '',
                        'Center' => ''
                    ];
                    $x ++;
                }
            }
        }

        if (count($arrInsert)) {
            DB::table('table_tmp')->insert($arrInsert);
        }
    }

    public function MB11_2InsertTemplateCapitalAlpha1(){
        $arrInsert = [];
        // ===================== a1===========================
        $results = DB::table('act_gl_books')->join('program_cate', 'act_gl_books.ProgramID', '=', 'program_cate.ProgramID')
                                                    ->join('project', 'act_gl_books.ProjectID','=', 'project.ProjectID')
                                                    ->where('program_cate.CateNo', '=', '001')
                                                    ->where('act_gl_books.BudgetLevel', '=', 1)
                                                    ->groupBy('act_gl_books.ProjectNo')->get();
        foreach ($results as $value){
            if($value->CateValue == 1){
                $ParentID = '#A';
                $ProjectItemLevel =  5;
                $ProjectItemParentID =  $ParentID . '#' . $value['ProgramNo'].'#'.'01';
            }
            if ($value->CateValue == 2){
                $ParentID = '#B';
                $ProjectItemLevel = 4;
                $ProjectItemParentID = $ParentID . '#'.'01';
            }
            $arrInsert[] = [
                'STT'=>'',
                'Show' => 1,
                'ItemID' =>$ParentID . '#' . $value->ProgramNo.'#'.$value->ProjectNo.'#'.'01',
                'ParentID'=>$ProjectItemParentID,
                'Level' => $ProjectItemLevel,
                'Level2' => 2,
                'ParentID2'=>$ParentID . '#' . $value->ProgramNo.'#'.$value->ProjectNo,
                'ItemName'=>'Ngân sách trung ương',
                'FontWeight' => '',
                'Italic' => '',
                'Center' => ''
            ];

        }
        if (count($arrInsert)) {
            DB::table('table_tmp')->insert($arrInsert);
        }
    }
    public function MB11_2InsertTemplateCapitalAlpha1_1($whereCondition, $year, $nextYear){
        $arrInsert = [];
        $sqlSelect = "SELECT   ProgramNo,  ProjectNo,ProgramValue,
                               MAX(CASE WHEN AccountNo = '0441' AND ExpenseCateNo = '20152' AND YearD = $year THEN I END) AS I12,
                               MAX(CASE WHEN AccountNo = '0441' AND ExpenseCateNo = '20151' AND YearD = $year THEN I END) AS I13,
                               MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20152' AND YearD = $year THEN I END) AS I22,
                               MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20151' AND YearD = $year THEN I END) AS I23,
                               MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20152' AND YearD = $nextYear THEN I END) AS I32,
                               MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20151' AND YearD = $nextYear THEN I END) AS I33,
                               MAX(CASE WHEN AccountNo = '0411' AND ExpenseCateNo = '20152' AND YearD = $nextYear THEN I END) AS I42,
                               MAX(CASE WHEN AccountNo = '0411' AND ExpenseCateNo = '20151' AND YearD = $nextYear THEN I END) AS I43
                                FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo, A.ProgramNo, A.ProjectNo,PC.CateValue AS ProgramValue ,EC.CateNo AS ExpenseCateNo
                     FROM act_gl_books AS A
                            INNER JOIN  program_cate AS PC ON PC.ProgramID = A.ProgramID
                            INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                            INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                      WHERE EC.CateNo IN ('20152', '20151')
                        AND CC.CateNo IN ('200111', '200121')
                        AND A.AccountNo IN ('0441', '0721','0411')
                        AND  PC.CateNo = '001' AND PC.CateValue IN (1 ,2)
                        AND A.BudgetLevel = 1
                        $whereCondition
                      GROUP BY YearD, A.AccountNo, A.ProgramNo, A.ProjectNo, EC.CateNo) AS B";
        $results = DB::select($sqlSelect);
        $results = (array)$results;
        if(count($results)){
            foreach ($results as $value){
                $value = (array)$value;
                if($value['ProgramNo']){
                    if($value->ProgramValue == 1){
                        $ParentID = '#A';
                        $ProjectItemLevel =  5;
                        $ProjectItemParentID =$ParentID . '#' . $value['ProgramNo'].'#01'.'#01';
                    }
                    if ($value->ProgramValue == 2){
                        $ParentID = '#B';
                        $ProjectItemLevel = 4;
                        $ProjectItemParentID = $ParentID . '#01'.'#01';
                    }
                    $arrInsert[] = [
                        'STT' => '',
                        'Show' => 1,
                        'ItemID' => $ParentID . '#' . $value['ProgramNo'].'#'.$value['ProjectNo'].'#01'.'#01',
                        'ParentID' => $ProjectItemParentID,
                        'ParentID2' =>$ParentID . '#' . $value['ProgramNo'].$value['ProjectNo'].'#01',
                        'ItemName' => 'Nguồn trong nước',
                        'Level' => $ProjectItemLevel,
                        'Level2' => 3,
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
                        'I33' => $value['I33']
                    ];
                }
            }
        }
        if(count($arrInsert)){
            DB::table('table_tmp')->insert($arrInsert);
        }
    }
    public function MB11_2InsertTemplateCapitalAlpha1_2($whereCondition, $year, $nextYear){
        $arrInsert = [];
        $sqlSelect = "SELECT   ProgramNo,  ProjectNo,ProgramValue,
                           MAX(CASE WHEN AccountNo = '0441' AND ExpenseCateNo = '20152' AND YearD = $year THEN I END) AS I12,
                           MAX(CASE WHEN AccountNo = '0441' AND ExpenseCateNo = '20151' AND YearD = $year THEN I END) AS I13,
                           MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20152' AND YearD = $year THEN I END) AS I22,
                           MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20151' AND YearD = $year THEN I END) AS I23,
                           MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20152' AND YearD = $nextYear THEN I END) AS I32,
                           MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20151' AND YearD = $nextYear THEN I END) AS I33,
                           MAX(CASE WHEN AccountNo = '0411' AND ExpenseCateNo = '20152' AND YearD = $nextYear THEN I END) AS I42,
                           MAX(CASE WHEN AccountNo = '0411' AND ExpenseCateNo = '20151' AND YearD = $nextYear THEN I END) AS I43
                    FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo, A.ProgramNo, A.ProjectNo,PC.CateValue AS ProgramValue ,EC.CateNo AS ExpenseCateNo
                                     FROM act_gl_books AS A
                                INNER JOIN  program_cate AS PC ON PC.ProgramID = A.ProgramID
                                INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                      WHERE EC.CateNo IN ('20152', '20151')
                        AND ((CC.CateNo = '20011203' AND CC.CateValue = 1) OR (CC.CateNo = '20011204' AND CC.CateValue = 1) OR (CC.CateNo = '20011205' AND CC.CateValue = 1) OR CC.CateNo IN('20012202','20012203','20012204'))
                        AND A.AccountNo IN ('0441', '0721','0411')
                        AND A.BudgetLevel = 1
                        AND  PC.CateNo = '001'
                        $whereCondition
                      GROUP BY YearD, A.AccountNo, A.ProgramNo, A.ProjectNo, EC.CateNo) AS B";
        $results = DB::select($sqlSelect);
        $results = (array)$results;
        foreach ($results as $value){
            $value = (array)$value;
            if($value['ProgramNo']){
                if($value->ProgramValue == 1){
                    $ParentID = '#A';
                    $ProjectItemLevel =  6;
                    $ProjectItemParentID =$ParentID . '#' . $value['ProgramNo'].'#01'.'#02';
                }
                if ($value->ProgramValue == 2){
                    $ParentID = '#B';
                    $ProjectItemLevel = 5;
                    $ProjectItemParentID = $ParentID . '#01'.'#02';
                }
                $arrInsert[] = [
                    'STT' => '',
                    'Show' => 1,
                    'ItemID' => $ParentID . '#' . $value['ProgramNo'].'#'.$value['ProjectNo'].'#01'.'#02',
                    'ParentID' => $ProjectItemParentID,
                    'ParentID2' =>$ParentID . '#' . $value['ProgramNo'].$value['ProjectNo'].'#01',
                    'ItemName' => 'Nguồn ngoài nước',
                    'Level' => $ProjectItemLevel,
                    'Level2' => 3,
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
                    'I33' => $value['I33']
                ];
            }
        }
        if(count($arrInsert)){
            DB::table('table_tmp')->insert($arrInsert);
        }

    }
    public function MB11_2InsertTemplateCapitalAlpha2($whereCondition, $year, $nextYear){
        $arrInsert = [];
        $sqlSelect = "SELECT   ProgramNo,  ProjectNo,ProgramValue,
                               MAX(CASE WHEN AccountNo = '0441' AND ExpenseCateNo = '20152' AND YearD = $year THEN I END) AS I12,
                               MAX(CASE WHEN AccountNo = '0441' AND ExpenseCateNo = '20151' AND YearD = $year THEN I END) AS I13,
                               MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20152' AND YearD = $year THEN I END) AS I22,
                               MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20151' AND YearD = $year THEN I END) AS I23,
                               MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20152' AND YearD = $nextYear THEN I END) AS I32,
                               MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20151' AND YearD = $nextYear THEN I END) AS I33,
                               MAX(CASE WHEN AccountNo = '0411' AND ExpenseCateNo = '20152' AND YearD = $nextYear THEN I END) AS I42,
                               MAX(CASE WHEN AccountNo = '0412' AND ExpenseCateNo = '20151' AND YearD = $nextYear THEN I END) AS I43
                        FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo, A.ProgramNo, A.ProjectNo,PC.CateValue AS ProgramValue ,EC.CateNo AS ExpenseCateNo
                                         FROM act_gl_books AS A
                                    INNER JOIN  program_cate AS PC ON PC.ProgramID = A.ProgramID
                                    INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                    INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                              WHERE EC.CateNo IN ('20152', '20151')
                                AND CC.CateNo IN ('200111','200121')
                                AND A.AccountNo IN ('0441', '0721', '0411')
                                AND A.BudgetLevel IN ( 2, 3, 4)
                                AND  PC.CateNo = '001'
                                $whereCondition
                              GROUP BY YearD, A.AccountNo, A.ProgramNo, A.ProjectNo, EC.CateNo) AS B";
        $results = DB::select($sqlSelect);
        $results = (array)$results;
        foreach ($results as $value){
            $value = (array)$value;
            if($value['ProgramNo']){
                if($value->ProgramValue == 1){
                    $ParentID = '#A';
                    $ProjectItemLevel =  5;
                    $ProjectItemParentID =$ParentID . '#' . $value['ProgramNo'].'#02';
                }
                if ($value->ProgramValue == 2){
                    $ParentID = '#B';
                    $ProjectItemLevel = 4;
                    $ProjectItemParentID = $ParentID . '#02';
                }
                $arrInsert[] = [
                    'STT' => '',
                    'Show' => 1,
                    'ItemID' => $ParentID . '#' . $value['ProgramNo'].'#'.$value['ProjectNo'].'#02',
                    'ParentID' => $ProjectItemParentID,
                    'ParentID2' =>$ParentID . '#' . $value['ProgramNo'].$value['ProjectNo'],
                    'ItemName' => 'Ngân sách địa phương',
                    'Level' => $ProjectItemLevel,
                    'Level2' => 2,
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
                    'I33' => $value['I33']
                ];
            }
        }
        if(count($arrInsert)){
            DB::table('table_tmp')->insert($arrInsert);
        }
    }
    public function MB11_2InsertTemplateCapitalAlpha3($whereCondition, $year, $nextYear){
            $arrInsert = [];
            $sqlSelect = "SELECT   ProgramNo,  ProjectNo,ProgramValue,
                                   MAX(CASE WHEN AccountNo = '0441' AND ExpenseCateNo = '20152' AND YearD = $year THEN I END) AS I12,
                                   MAX(CASE WHEN AccountNo = '0441' AND ExpenseCateNo = '20151' AND YearD = $year THEN I END) AS I13,
                                   MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20152' AND YearD = $year THEN I END) AS I22,
                                   MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20151' AND YearD = $year THEN I END) AS I23,
                                   MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20152' AND YearD = $nextYear THEN I END) AS I32,
                                   MAX(CASE WHEN AccountNo = '0721' AND ExpenseCateNo = '20151' AND YearD = $nextYear THEN I END) AS I33,
                                   MAX(CASE WHEN AccountNo = '0411' AND ExpenseCateNo = '20152' AND YearD = $nextYear THEN I END) AS I42,
                                   MAX(CASE WHEN AccountNo = '0412' AND ExpenseCateNo = '20151' AND YearD = $nextYear THEN I END) AS I43
                            FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo, A.ProgramNo, A.ProjectNo,PC.CateValue AS ProgramValue ,EC.CateNo AS ExpenseCateNo
                                             FROM act_gl_books AS A
                                        INNER JOIN  program_cate AS PC ON PC.ProgramID = A.ProgramID
                                        INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                        INNER JOIN capital AS C ON C.CapitalID = A.CapitalID
                                  WHERE EC.CateNo IN ('20152', '20151')
                                    AND C.BudgetStateType = 2
                                    AND A.AccountNo IN ('0441', '0721', '0411')
                                    AND A.BudgetLevel IN ( 2, 3, 4)
                                    AND  PC.CateNo = '001'
                                    $whereCondition
                                  GROUP BY YearD, A.AccountNo, A.ProgramNo, A.ProjectNo, EC.CateNo) AS B";
        $results = DB::select($sqlSelect);
        $results = (array)$results;
        foreach ($results as $value){
            $value = (array)$value;
            if($value['ProgramNo']){
                if($value->ProgramValue == 1){
                    $ParentID = '#A';
                    $ProjectItemLevel =  5;
                    $ProjectItemParentID =$ParentID . '#' . $value['ProgramNo'].'#03';
                }
                if ($value->ProgramValue == 2){
                    $ParentID = '#B';
                    $ProjectItemLevel = 4;
                    $ProjectItemParentID = $ParentID . '#03';
                }
                $arrInsert[] = [
                    'STT' => '',
                    'Show' => 1,
                    'ItemID' => $ParentID . '#' . $value['ProgramNo'].'#'.$value['ProjectNo'].'#03',
                    'ParentID' => $ProjectItemParentID,
                    'ParentID2' =>$ParentID . '#' . $value['ProgramNo'].$value['ProjectNo'],
                    'ItemName' => 'Nguồn huy động hợp pháp khác',
                    'Level' => $ProjectItemLevel,
                    'Level2' => 2,
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

        }
        if(count($arrInsert)){
            DB::table('table_tmp')->insert($arrInsert);
        }
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

        $maxLevel2 = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level2) AS maxLevel2'))->first();
        if ($maxLevel2) {
            $end = $maxLevel2->maxLevel2;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID2, SUM(M.I11) AS I11, SUM(M.I12) AS I12, SUM(M.I13) AS I13, SUM(M.I21) AS I21, SUM(M.I22) AS I22, SUM(M.I23) AS I23, SUM(M.I31) AS I31, SUM(M.I32) AS I32, SUM(M.I33) AS I33
                FROM table_tmp M
                WHERE M.Level2 = " . $i . " GROUP BY ParentID2";
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
                    AND table_tmp.Level2 = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }



}
