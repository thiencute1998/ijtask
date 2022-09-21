<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Listing\Models\Program;
use Module\Listing\Models\Project;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB13_5Controller extends Controller {
    public function SBP3422016TTBTC_MB13_5(Request $request){
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
          `I5` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $this->MB13_5InsertTemplate();
        $this->UpdateAI($whereCondition,$preYear, $year, $nextYear);
        $this->UpdateAII($whereCondition,$preYear, $year, $nextYear);
        $this->B_I_3($whereCondition,$preYear, $year, $nextYear);
        $this->B_II($whereCondition,$preYear, $year, $nextYear);
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
    public function MB13_5InsertTemplate(){
        $arrInsert = [
            [
                'STT' => 'A',
                'Show' => 1,
                'ItemID' => '#A',
                'ParentID' => '',
                'UomName'=>'',
                'ItemName' => 'Cơ sở tính',
                'Level' => 2,
                'FontWeight' => '1',
                'Italic' => '',
                'Center' => ''
            ],
            [
                'STT' => 'B',
                'Show' => 1,
                'ItemID' => '#I',
                'UomName'=>'',
                'ParentID' => '',
                'ItemName' => 'Tổng số kinh phí',
                'Level' => 2,
                'FontWeight' => 1,
                'Italic' => '',
                'Center' => ''
            ],

        ];
        $arrInsert[] = [
            'STT' => 'I',
            'Show' => 1,
            'ItemID' => '#A'. '#I',
            'ParentID' => '#A',
            'UomName'=>'',
            'ItemName' => 'Đối với khối đơn vị sản xuất',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'II',
            'Show' => 1,
            'ItemID' => '#A'. '#II',
            'ParentID' => '#A',
            'UomName'=>'',
            'ItemName' => 'Đối với khối đơn vị giúp việc (không trực tiếp sản xuất)',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'I',
            'Show' => 1,
            'ItemID' => '#B'. '#I',
            'ParentID' => '#B',
            'UomName'=>'',
            'ItemName' => 'Kinh phí giao tự chủ',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'II',
            'Show' => 1,
            'ItemID' => '#B'. '#II',
            'ParentID' => '#B',
            'UomName'=>'',
            'ItemName' => 'Kinh phí không giao tự chủ (1)',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1',
            'Show' => 1,
            'ItemID' => '#A'. '#I'.'#01',
            'ParentID' => '#A'. '#I',
            'UomName'=>'',
            'ItemName' => 'Số lượng tin bài',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2',
            'Show' => 1,
            'ItemID' => '#A'. '#I'.'#02',
            'ParentID' => '#A'. '#I',
            'UomName'=>'',
            'ItemName' => 'Số giờ phát sóng',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '3',
            'Show' => 1,
            'ItemID' => '#A'. '#I'.'#03',
            'ParentID' => '#A'. '#I',
            'UomName'=>'',
            'ItemName' => 'Số lượng xuất bản',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '4',
            'Show' => 1,
            'ItemID' => '#A'. '#I'.'#04',
            'ParentID' => '#A'. '#I',
            'UomName'=>'',
            'ItemName' => 'Các yếu tố khác (số lượng đơn vị truyền dẫn, số kênh...) (1)',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1',
            'Show' => 1,
            'ItemID' => '#A'. '#II'.'#01',
            'ParentID' => '#A'. '#II',
            'UomName'=>'',
            'ItemName' => 'Định biên về nhân sự',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2',
            'Show' => 1,
            'ItemID' => '#A'. '#II'.'#02',
            'ParentID' => '#A'. '#II',
            'UomName'=>'',
            'ItemName' => 'Hệ số điều chỉnh (nếu có)',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '3',
            'Show' => 1,
            'ItemID' => '#A'. '#II'.'#03',
            'ParentID' => '#A'. '#II',
            'UomName'=>'',
            'ItemName' => 'Các yếu tố khác (1)',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1',
            'Show' => 1,
            'ItemID' => '#B'. '#I'.'#01',
            'ParentID' => '#B'. '#I',
            'UomName'=>'',
            'ItemName' => 'Quỹ lương, phụ cấp và các khoản đóng góp theo lương',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2',
            'Show' => 1,
            'ItemID' => '#B'. '#I'.'#02',
            'ParentID' => '#B'. '#I',
            'UomName'=>'',
            'ItemName' => 'Chi hoạt động chuyên môn, quản lý',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];


        DB::table('table_tmp')->insert($arrInsert);
    }

    public function MB13_5InsertTemplateND($whereCondition)
    {
        $sql = "SELECT A.PeriodToDate, A.PeriodFromDate, A.FiscalYear, A.AccountNo AccountNo, A.CoAccountNo CoAccountNo, NM.UomName UomName,A.NormID NormID, A.NormNo NormNo, A.NormName NormName,
       NM.NormID NormID
                    FROM act_gl_books A
                        INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                        INNER JOIN norm NM ON NM.NormID = A.NormID
                        INNER JOIN norm_cate NC ON NC.NormID = A.NormID
                        $whereCondition
                        GROUP BY  NormName";

        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert1 = [];
        $i = 1;
        if ($result) {
            foreach ($result as $template) {
                $STT = $i++;
                $ParentID = 'A#';
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
                    'I4'=>'',
                    'I5'=>''
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
    //Cập nhật A mục I
    public function UpdateAI($whereCondition, $preYear,$year, $nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
       MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
       MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I2,
       MAX(CASE WHEN AccountNo = '0721' AND YearD = $year  THEN I END) AS I3,
       MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I4
FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
      (Case when NC.CateValue='1' THEN '#A#I#01'
            When NC.CateValue='2' THEN '#A#I#02'
            When NC.CateValue='3' THEN '#A#I#03'
            When NC.CateValue='4' THEN '#A#I#04' END) as ItemID
      FROM act_gl_books AS A
               INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
      WHERE  A.AccountNo IN ('0461', '0441', '0721','0411')
        AND  NC.CateNo = '2021311'
          $whereCondition
      GROUP BY A.AccountNo, YearD
     ) AS B
    ) AS HH
     SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
     WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    //Cập nhật A mục II
    public function UpdateAII($whereCondition, $preYear,$year, $nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
       MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
       MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I2,
       MAX(CASE WHEN AccountNo = '0721' AND YearD = $year  THEN I END) AS I3,
       MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I4
FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
      (Case when NC.CateValue='1' THEN '#A#II#01'
            When NC.CateValue='2' THEN '#A#II#02'
            When NC.CateValue='3' THEN '#A#II#03' END) as ItemID
      FROM act_gl_books AS A
               INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
      WHERE  A.AccountNo IN ('0461', '0441', '0721','0411')
        AND  NC.CateNo = '2021312'
          $whereCondition
      GROUP BY A.AccountNo, YearD
     ) AS B
    ) AS HH
     SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
     WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    public function B_I_3($whereCondition, $preYear,$year, $nextYear)
    {
        $sql = "SELECT  NormCateValue, NormName, NormNo,UomName,
                            MAX(CASE WHEN AccountNo = '0461'  AND YearD = $preYear THEN I END) AS I2,
                            MAX(CASE WHEN AccountNo = '0441'  AND YearD = $year THEN I END) AS I3,
                            MAX(CASE WHEN AccountNo = '0721'  AND YearD = $year THEN I END) AS I4,
                            MAX(CASE WHEN AccountNo = '0411'  AND YearD = $nextYear THEN I END) AS I5
                        FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo,A.UomName,EC.CateValue AS ExpenseCateValue, NC.CateValue as NormCateValue, NM.NormName as NormName, NM.NormNo as NormNo
                              FROM act_gl_books AS A
                                       INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN norm AS NM ON NM.NormID = A.NormID
                                       INNER JOIN expense_cate EC ON EC.ExpenseID = A.ExpenseID
                                    Where NC.CateNo ='20112111119' AND NC.CateValue = '1'
                                    AND LEFT(EC.CateNo,5)='20151'
                        $whereCondition
                              GROUP BY YearD, A.AccountNo,A.UomNo,NormCateValue, NormName, NormNo) AS B GROUP BY NormCateValue, NormName, NormNo";
        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert1 = [];
        $i = 1;
        if ($result) {
            foreach ($result as $template) {
                $STT = $i++;
                $ItemID = '#B' . $template->ItemID;
                $ParentID = '#B';
                $arrInsert1[] = [
                    'STT' => $STT,
                    'Show' => '',
                    'ItemID' => $ItemID,
                    'ParentID' => $ParentID,
                    'ItemName' => $template->ItemName,
                    'Level' => '3',
                    'FontWeight' => '',
                    'Italic' => '',
                    'Center' => '',
                    'ItemNo' => $template->ItemNo,
                    'AccountNo' => $template->AccountNo,
                    'CoAccountNo' => $template->CoAccountNo,
                    'UomName' => $template->UomName,
                    'I1' => '',
                    'I2' => '',
                    'I3' => '',
                    'I4' => '',
                    'I5'=>''
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
    public function B_II($whereCondition, $preYear,$year, $nextYear)
    {
        $sql = "SELECT  NormCateValue, NormName, NormNo,UomName,
                            MAX(CASE WHEN AccountNo = '0461'  AND YearD = $preYear THEN I END) AS I2,
                            MAX(CASE WHEN AccountNo = '0441'  AND YearD = $year THEN I END) AS I3,
                            MAX(CASE WHEN AccountNo = '0721'  AND YearD = $year THEN I END) AS I4,
                            MAX(CASE WHEN AccountNo = '0411'  AND YearD = $nextYear THEN I END) AS I5
                        FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo,A.UomName,EC.CateValue AS ExpenseCateValue, NC.CateValue as NormCateValue, NM.NormName as NormName, NM.NormNo as NormNo
                              FROM act_gl_books AS A
                                       INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN norm AS NM ON NM.NormID = A.NormID
                                       INNER JOIN expense_cate EC ON EC.ExpenseID = A.ExpenseID
                                    Where NC.CateNo ='20112111119' AND NC.CateValue = '2'
                                    AND EC.CateNo = '20152010602'
                        $whereCondition
                              GROUP BY YearD, A.AccountNo,A.UomNo,NormCateValue, NormName, NormNo) AS B GROUP BY NormCateValue, NormName, NormNo";
        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert1 = [];
        $i = 1;
        if ($result) {
            foreach ($result as $template) {
                $STT = $i++;
                $ItemID = '#B' . $template->ItemID;
                $ParentID = '#B';
                $arrInsert1[] = [
                    'STT' => $STT,
                    'Show' => '',
                    'ItemID' => $ItemID,
                    'ParentID' => $ParentID,
                    'ItemName' => $template->ItemName,
                    'Level' => '3',
                    'FontWeight' => '',
                    'Italic' => '',
                    'Center' => '',
                    'ItemNo' => $template->ItemNo,
                    'AccountNo' => $template->AccountNo,
                    'CoAccountNo' => $template->CoAccountNo,
                    'UomName' => $template->UomName,
                    'I1' => '',
                    'I2' => '',
                    'I3' => '',
                    'I4' => '',
                    'I5'=>''
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
    }



}
