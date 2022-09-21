<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Listing\Models\Program;
use Module\Listing\Models\Project;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB13_4Controller extends Controller {
    public function SBP3422016TTBTC_MB13_4(Request $request){
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
        $preYear = (int)$year - 1;
        $nextYear = (int)$year + 1;

        $whereCondition = "AND (
        ( YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $preYear AND YEAR(A.PeriodToDate) = $preYear)
        OR (YEAR(A.PeriodFromDate) = $nextYear AND YEAR(A.PeriodToDate) = $nextYear)
        ) $whereCompany  AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `UomName` varchar(100) DEFAULT '',
          `Show` int(1) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $this->MB13_4InsertTemplate();
        $this->MB13_4InsertTemplateCoSo($whereCondition,$preYear , $year, $nextYear);
        $this->UpdateI3_II3_III2_IV2($whereCondition,$preYear, $year, $nextYear);
        $this->UpdateI2_II2($whereCondition,$preYear, $year, $nextYear);
        $this->MB13_4InsertTemplateCT($whereCondition);
        $this->MB13_4InsertTemplateTongsokinhphi($whereCondition,$preYear, $year, $nextYear);
        $this->MB13_4InsertTemplateNhiemVu($whereCondition,$preYear, $year, $nextYear);
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
    public function MB13_4InsertTemplate(){
        $arrInsert = [
            [
                'STT' => 'I',
                'Show' => 1,
                'ItemID' => '#I',
                'ParentID' => '',
                'UomName'=>'',
                'ItemName' => 'Quỹ lương',
                'Level' => 2,
                'FontWeight' => '1',
                'Italic' => '',
                'Center' => ''
            ],
            [
                'STT' => 'II',
                'Show' => 1,
                'ItemID' => '#II',
                'UomName'=>'',
                'ParentID' => '',
                'ItemName' => 'Chi nghiệp vụ chuyên môn thường xuyên',
                'Level' => 2,
                'FontWeight' => 1,
                'Italic' => '',
                'Center' => ''
            ],
            [
                'STT' => 'III',
                'Show' => 1,
                'ItemID' => '#III',
                'UomName'=>'',
                'ParentID' => '',
                'ItemName' => 'Các nhiệm vụ không thường xuyên (1)',
                'Level' => 2,
                'FontWeight' => 1,
                'Italic' => '',
                'Center' => ''
            ],

        ];
        $arrInsert[] = [
            'STT' => '1',
            'Show' => 1,
            'ItemID' => '#I'. '#01',
            'ParentID' => '#I',
            'UomName'=>'',
            'ItemName' => 'Số người làm việc được cấp có thẩm quyền giao',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2',
            'Show' => 1,
            'ItemID' => '#I'. '#02',
            'ParentID' => '#I',
            'UomName'=>'',
            'ItemName' => 'Số người làm việc thực tế',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '3',
            'Show' => 1,
            'ItemID' => '#I'. '#03',
            'ParentID' => '#I',
            'UomName'=>'',
            'ItemName' => 'Quỹ lương, phụ cấp và các khoản đóng góp theo lương tính theo số người làm việc thực tế',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1',
            'Show' => 1,
            'ItemID' => '#II'. '#01',
            'ParentID' => '#II',
            'UomName'=>'',
            'ItemName' => 'Kinh phí thực hiện chế độ chính sách trong lĩnh vực văn hóa thông tin (chi tiết theo từng nội dung)',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1',
            'Show' => 1,
            'ItemID' => '#II'. '#02',
            'ParentID' => '#II',
            'UomName'=>'',
            'ItemName' => 'Các khoản chi thường xuyên khác (1)',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];

        DB::table('table_tmp')->insert($arrInsert);
    }

    public function MB13_4InsertTemplateND($whereCondition)
    {
        $sql = "SELECT A.PeriodToDate, A.PeriodFromDate, A.FiscalYear, A.AccountNo AccountNo, A.CoAccountNo CoAccountNo, NM.UomName UomName,A.NormID NormID, A.NormNo NormNo, A.NormName NormName,
       NM.NormID NormID
                    FROM act_gl_books A
                        INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                        INNER JOIN norm NM ON NM.NormID = A.NormID
                        INNER JOIN norm_cate NC ON NC.NormID = A.NormID
                        WHERE SC.CateNo = '003' AND SC.CateValue='6'
                        $whereCondition
                        GROUP BY  NormName";

        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert1 = [];
        $i = 1;
        if ($result) {
            foreach ($result as $template) {
                $STT = $i++;
                $ParentID = '';
                $arrInsert1[] = [
                    'STT' => $STT,
                    'Show'=>'',
                    'ParentID' => $ParentID,
                    'Level'=>'2',
                    'UomName'=>$template->UomName,
                    'FontWeight'=> '',
                    'Italic' =>'',
                    'Center'=>'',
                    'AccountNo' => $template->AccountNo,
                    'CoAccountNo' => $template->CoAccountNo,
                    'ItemName'=>$template->ItemName,
                    'I1'=>'',
                    'I2'=>'',
                    'I3'=>'',
                    'I4'=>''
                ];
            }
            DB::table('table_tmp')->insert($arrInsert1);
        }
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }
    //Cập nhật A mục I1,I2
    public function UpdateI1_2($whereCondition, $preYear,$year, $nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
       MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
       MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I2,
       MAX(CASE WHEN AccountNo = '0711' AND YearD = $year  THEN I END) AS I3,
       MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I4
FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
    (Case (when  NC.CateValue IN('1','3','5','6')) THEN '#I#01'
          (when  NC.CateValue IN('2','4','5','6')) THEN '#I#02'  END) as ItemID
      FROM act_gl_books AS A
               INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
               INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
      WHERE  A.AccountNo IN ('0461', '0441', '0711','0411')
        AND  SC.CateNo = '003' AND SC.CateValue='6'
        AND  NC.CateNo = '20215' AND NC.CateValue IN ('1','2','3','4','5','6')
          $whereCondition
      GROUP BY A.AccountNo, YearD
     ) AS B
    ) AS HH
     SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
     WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    //Cập nhật A mục I3
    public function UpdateI3($whereCondition, $preYear,$year, $nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT  FCAmount,
       MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
       MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I2,
       MAX(CASE WHEN AccountNo = '0711' AND YearD = $year  THEN I END) AS I3,
       MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I4
FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD, NT.FCAmount AS FCAmount
      FROM act_gl_books AS A
               INNER JOIN norm AS NM ON NM.NormID = A.CompanyID
               INNER JOIN norm_table_item AS NT ON NT.NormID = A.CompanyID
               INNER JOIN expense_cate AS NC ON NC.NormID = A.CompanyID
               INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
      WHERE  A.AccountNo IN ('0461', '0441', '0711','0411')
        AND  NM.NormNo = '2041111'
        AND  SC.CateNo = '003' AND SC.CateValue='6'
        AND  EC.CateNo ='20201' AND (EC.CateNo='20202' AND EC.CateValue = '1')
          $whereCondition
      GROUP BY A.AccountNo, YearD
     ) AS B
    ) AS HH
     SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
     WHERE TT.ItemID ='#I#03'";
        DB::statement($sqlUpdate);
    }


    public function MB11_2InsertTemplateCTMT_DN(){
        $results = DB::table('act_gl_books')->join('norm', 'act_gl_books.normID', '=', 'norm.normID')
            ->groupBy('act_gl_books.NormNo')->get();
        $arrInsert = [];
        $i = 1;
        $j = 1;
        $k = 1;
        foreach ($results as $value) {
            if ($value->NormNo == 56212121212) {
                $ParentID = '#II#01';
                $arrInsert[] = [
                    'STT' => $i,
                    'Show' => 1,
                    'ItemID' => $ParentID . '#' . $value->NormNo,
                    'ParentID' => $ParentID,
                    'ParentID2' => '',
                    'ItemName' => $value->NormName,
                    'Level' => 3,
                    'Level2' => '',
                    'FontWeight' => '',
                    'Italic' => '',
                    'Center' => ''
                ];
            }
            if ($value->NormNo == 56212121299) {
                $ParentID = '#II#02';
                $arrInsert[] = [
                    'STT' => $j,
                    'Show' => 1,
                    'ItemID' => $ParentID . '#' . $value->NormNo,
                    'ParentID' => $ParentID,
                    'ParentID2' => '',
                    'ItemName' => $value->NormName,
                    'Level' => 3,
                    'Level2' => '',
                    'FontWeight' => '',
                    'Italic' => '',
                    'Center' => ''
                ];
            }
            if ($value->NormNo == 56213) {
                $ParentID = '#III';
                $arrInsert[] = [
                    'STT' => $k,
                    'Show' => 1,
                    'ItemID' => $ParentID . '#' . $value->NormNo,
                    'ParentID' => $ParentID,
                    'ParentID2' => '',
                    'ItemName' => $value->NormName,
                    'Level' => 3,
                    'Level2' => '',
                    'FontWeight' => '',
                    'Italic' => '',
                    'Center' => ''
                ];
            }


                $i++;
                $j++;
                $k++;
            }

        if (count($arrInsert)) {
            DB::table('table_tmp')->insert($arrInsert);
        }
    }
    //Cập nhật Số cơ sở II_1
    public function MB11_2InsertTemplateII1($whereCondition, $preYear,$year, $nextYear)
    {
        $sql = "SELECT   NormNo,
                               MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear THEN I END) AS I1,
                               MAX(CASE WHEN AccountNo = '0441' AND YearD = $year THEN I END) AS I2,
                               MAX(CASE WHEN AccountNo = '0711' AND YearD = $year THEN I END) AS I3,
                               MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear THEN I END) AS I4,
                                FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo, A.NormNo AS NormNo
                     FROM act_gl_books AS A
                            INNER JOIN norm AS NM ON NM.NormID = A.NormID
                      WHERE A.AccountNo IN ('0461','0441','0711','0411')
                        AND NM.NormNo = '56212121212'
                        $whereCondition
                      GROUP BY YearD, A.AccountNo, A.NormNo ) AS B";
        $result = DB::table('talbe_tmp')->join('act_gl_books', 'table_tmp.NormID', '=', 'act_gl_books.NormID')->get();
        $result = (array)$result;
        $arrInsert = [];
        $i = 1;
        if ($result) {
            foreach ($result as $value) {
                $STT = $i++;
                $ParentID = '#II#01';
                    $arrInsert[] = [
                        'STT' => $STT,
                        'Show' => 1,
                        'ItemID' => $ParentID .'#'.$value['NormNo']  ,
                        'ParentID' => $ParentID,
                        'ParentID2' =>'',
                        'ItemName' => $value['NormName'],
                        'Level' => 3,
                        'Level2' => '',
                        'FontWeight' => '',
                        'Italic' => '',
                        'Center' => '',
                        'I1' => $value['I1'],
                        'I2' => $value['I2'],
                        'I3' => $value['I3'],
                        'I4' => $value['I4'],

                    ];
                }
            }

        if(count($arrInsert)){
            DB::table('table_tmp')->insert($arrInsert);
        }
    }
    public function MB11_2InsertTemplateSL($whereCondition, $preYear,$year, $nextYear)
    {
        $sql = "SELECT   Quantity,NormNo,
                               MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear THEN I END) AS I1,
                               MAX(CASE WHEN AccountNo = '0441' AND YearD = $year THEN I END) AS I2,
                               MAX(CASE WHEN AccountNo = '0711' AND YearD = $year THEN I END) AS I3,
                               MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear THEN I END) AS I4,
                                FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo, NT.Quantity AS Quantity, A.NormNo NormNo
                     FROM act_gl_books AS A
                            INNER JOIN norm_table_item AS NT ON NT.NormID = A.NormID
                             INNER JOIN norm AS NM ON NM.NormID = A.NormID
                      WHERE A.AccountNo IN ('0461','0441','0711','0411')
                        AND NM.NormNo = '56212121212'
                        $whereCondition
                      GROUP BY YearD, A.AccountNo,A.NormNo  ) AS B";
        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert = [];
        $i = 1;
        if ($result) {
            foreach ($result as $value) {
                $STT = $i++;
                $arrInsert[] = [
                    'STT' => $STT,
                    'Show' => 1,
                    'ItemID' => '#II'.'#01'.'#'.$value['NormNo'].'#01' ,
                    'ParentID' =>'',
                    'ParentID2' =>'',
                    'ItemName' => 'Số lượng',
                    'Level' => 5,
                    'Level2' => '',
                    'FontWeight' => '',
                    'Italic' => '',
                    'Center' => '',
                    'I1' => $value['I1'],
                    'I2' => $value['I2'],
                    'I3' => $value['I3'],
                    'I4' => $value['I4'],

                ];
            }
        }

        if(count($arrInsert)){
            DB::table('table_tmp')->insert($arrInsert);
        }
    }
    public function MB11_2InsertTemplateDM($whereCondition, $preYear,$year, $nextYear)
    {
        $sql = "SELECT   FCUnitPrice,NormNo,
                               MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear THEN I END) AS I1,
                               MAX(CASE WHEN AccountNo = '0441' AND YearD = $year THEN I END) AS I2,
                               MAX(CASE WHEN AccountNo = '0711' AND YearD = $year THEN I END) AS I3,
                               MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear THEN I END) AS I4,
                                FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo, NT.FCUnitPrice AS FCUnitPrice, A.NormNo NormNo
                     FROM act_gl_books AS A
                            INNER JOIN norm_table_item AS NT ON NT.NormID = A.NormID
                             INNER JOIN norm AS NM ON NM.NormID = A.NormID
                      WHERE A.AccountNo IN ('0461','0441','0711','0411')
                        AND NM.NormNo = '56212121212'
                        $whereCondition
                      GROUP BY YearD, A.AccountNo,A.NormNo  ) AS B";
        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert = [];
        $i = 1;
        if ($result) {
            foreach ($result as $value) {
                $STT = $i++;
                $arrInsert[] = [
                    'STT' => $STT,
                    'Show' => 1,
                    'ItemID' => '#II'.'#01'.'#'.$value['NormNo']. '#02' ,
                    'ParentID' => '',
                    'ParentID2' =>'',
                    'ItemName' => 'Định mức',
                    'Level' => 5,
                    'Level2' => '',
                    'FontWeight' => '',
                    'Italic' => '',
                    'Center' => '',
                    'I1' => $value['I1'],
                    'I2' => $value['I2'],
                    'I3' => $value['I3'],
                    'I4' => $value['I4'],

                ];
            }
        }

        if(count($arrInsert)){
            DB::table('table_tmp')->insert($arrInsert);
        }
    }
    public function MB11_2InsertTemplateKP($whereCondition, $preYear,$year, $nextYear)
    {
        $sql = "SELECT   FCAmount,NormNo,
                               MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear THEN I END) AS I1,
                               MAX(CASE WHEN AccountNo = '0441' AND YearD = $year THEN I END) AS I2,
                               MAX(CASE WHEN AccountNo = '0711' AND YearD = $year THEN I END) AS I3,
                               MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear THEN I END) AS I4,
                                FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo, NT.FCAmount AS FCAmount, A.NormNo NormNo
                     FROM act_gl_books AS A
                            INNER JOIN norm_table_item AS NT ON NT.NormID = A.NormID
                             INNER JOIN norm AS NM ON NM.NormID = A.NormID
                      WHERE A.AccountNo IN ('0461','0441','0711','0411')
                        AND NM.NormNo = '56212121212'
                        $whereCondition
                      GROUP BY YearD, A.AccountNo,A.NormNo  ) AS B";
        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert = [];
        $i = 1;
        if ($result) {
            foreach ($result as $value) {
                $STT = $i++;
                $arrInsert[] = [
                    'STT' => $STT,
                    'Show' => 1,
                    'ItemID' => '#II'.'#01'.'#'.$value['NormNo']. '#03' ,
                    'ParentID' => '#II'.'#01'.'#'.$value['NormNo'],
                    'ParentID2' =>'#II'.'#01',
                    'ItemName' => 'Kinh phí thực hiện',
                    'Level' => '',
                    'Level2' => '5',
                    'FontWeight' => '',
                    'Italic' => '',
                    'Center' => '',
                    'I1' => $value['I1'],
                    'I2' => $value['I2'],
                    'I3' => $value['I3'],
                    'I4' => $value['I4'],

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
        $maxLevel2 = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level2) AS maxLevel2'))->first();
        if ($maxLevel2) {
            $end = $maxLevel2->maxLevel2;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID2, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
                FROM table_tmp M
                WHERE M.Level2 = " . $i . " GROUP BY ParentID2";
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
                    WHERE ItemID = '" . $value['ParentID2'] . "'
                    AND table_tmp.Level2 = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }



}
