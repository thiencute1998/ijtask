<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Listing\Models\Program;
use Module\Listing\Models\Project;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB13_6Controller extends Controller {
    public function SBP3422016TTBTC_MB13_6(Request $request){
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
        $this->MB13_6InsertTemplate();
        $this->MB13_6InsertTemplateCoSo($whereCondition,$preYear , $year, $nextYear);
        $this->UpdateI3_II3_III2_IV2($whereCondition,$preYear, $year, $nextYear);
        $this->UpdateI2_II2($whereCondition,$preYear, $year, $nextYear);
        $this->MB13_6InsertTemplateCT($whereCondition);
        $this->MB13_6InsertTemplateKinhPhi($whereCondition,$preYear, $year, $nextYear);
        $this->MB13_6InsertTemplateNhiemVu($whereCondition,$preYear, $year, $nextYear);
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
    public function MB13_6InsertTemplate(){
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
                'ItemName' => 'Chi nghiệp vụ chuyên môn',
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
            'FontWeight' => '',
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
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '3',
            'Show' => 1,
            'ItemID' => '#I'. '#03',
            'UomName'=>'',
            'ParentID' =>  '#I' ,
            'ItemName' => 'Quỹ lương, phụ cấp và các khoản đóng góp theo lương tính theo số người làm việc thực tế',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1',
            'Show' => 1,
            'ItemID' => '#II'. '#01',
            'ParentID' => '#II',
            'UomName'=>'',
            'ItemName' => 'Kinh phí tập huấn vận động viên',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2',
            'Show' => 1,
            'ItemID' => '#II'. '#02',
            'UomName'=>'',
            'ParentID' => '#II',
            'ItemName' => 'Kinh phí đoàn tập huấn, tham dự các giải thi đấu thể thao ở nước ngoài',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '3',
            'Show' => 1,
            'ItemID' => '#II'. '#03',
            'UomName'=>'',
            'ParentID' =>  '#II' ,
            'ItemName' => 'Kinh phí mua trang thiết bị thường xuyên cho VĐV, HLV',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '4',
            'Show' => 1,
            'ItemID' => '#II'. '#04',
            'ParentID' => '#II',
            'UomName'=>'',
            'ItemName' =>'Các khoản chi thường xuyên khác (1)',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1.1',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#01',
            'UomName'=>'',
            'ParentID' =>  '#II'.'#01',
            'ItemName' => 'Chi tiền ăn',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1.2',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#02',
            'UomName'=>'',
            'ParentID' =>  '#II'.'#01',
            'ItemName' => 'Chi tiền công',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1.3',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#03',
            'UomName'=>'',
            'ParentID' =>  '#II'.'#01',
            'ItemName' => 'Chi tiền thuê chuyên gia',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#01'.'#01',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#01',
            'ItemName' => 'Huấn luyện viên',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#01'.'#02',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#01',
            'ItemName' => 'Vận động viên',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#02'.'#01',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#02',
            'ItemName' => 'Huấn luyện viên',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#02'.'#02',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#02',
            'ItemName' => 'Vận động viên',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#03'.'#01',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#03',
            'ItemName' => 'Số lượng chuyên gia nước ngoài',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#03'.'#02',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#03',
            'ItemName' => 'Định mức chi/chuyên gia',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'c',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#03'.'#03',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#03',
            'ItemName' => 'Kinh phí chi tiền công cho chuyên gia',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2.1.a',
            'Show' => 1,
            'ItemID' => '#II'. '#02'.'#01'.'#01',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#02'.'#01',
            'ItemName' => 'Số lượng người tham gia',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2.1.b',
            'Show' => 1,
            'ItemID' => '#II'. '#02'.'#01'.'#02',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#02'.'#01',
            'ItemName' => 'Kinh phí thực hiện',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#II'. '#03'.'#01'.'#01',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#03'.'#01',
            'ItemName' => 'Huấn luyện viên',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#II'. '#03'.'#01'.'#02',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#03'.'#01',
            'ItemName' => 'Vận động viên',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.1',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#01'.'#01'.'#01',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#01'.'#01',
            'ItemName' => '- Số lượng',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.2',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#01'.'#01'.'#02',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#01'.'#01',
            'ItemName' => '- Định mức chi',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.3',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#01'.'#01'.'#03',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#01'.'#01',
            'ItemName' => '- Kinh phí',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.1',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#01'.'#02'.'#01',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#01'.'#02',
            'ItemName' => '- Số lượng',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.2',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#01'.'#02'.'#02',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#01'.'#02',
            'ItemName' => '- Định mức chi',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.3',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#01'.'#02'.'#03',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#01'.'#02',
            'ItemName' => '- Kinh phí',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];

        $arrInsert[] = [
            'STT' => 'a.1',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#02'.'#01'.'#01',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#02'.'#01',
            'ItemName' => '- Số lượng',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.2',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#02'.'#01'.'#02',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#02'.'#01',
            'ItemName' => '- Định mức chi',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.3',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#02'.'#01'.'#03',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#02'.'#01',
            'ItemName' => '- Kinh phí',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.1',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#02'.'#02'.'#01',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#02'.'#02',
            'ItemName' => '- Số lượng',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.2',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#02'.'#02'.'#02',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#02'.'#02',
            'ItemName' => '- Định mức chi',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.3',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#02'.'#02'.'#03',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#02'.'#02',
            'ItemName' => '- Kinh phí',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.1',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#03'.'#01'.'#01',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#03'.'#01',
            'ItemName' => '- Số lượng',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.2',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#03'.'#01'.'#02',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#03'.'#01',
            'ItemName' => '- Định mức chi',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.3',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#03'.'#01'.'#03',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#03'.'#01',
            'ItemName' => '- Kinh phí',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.1',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#03'.'#02'.'#01',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#03'.'#02',
            'ItemName' => '- Số lượng',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.2',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#03'.'#02'.'#02',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#03'.'#02',
            'ItemName' => '- Định mức chi',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.3',
            'Show' => 1,
            'ItemID' => '#II'. '#01'.'#03'.'#02'.'#03',
            'UomName'=>'',
            'ParentID' =>  '#II'. '#01'.'#03'.'#02',
            'ItemName' => '- Kinh phí',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];


        DB::table('table_tmp')->insert($arrInsert);
    }

    public function MB13_6InsertTemplateND($whereCondition)
    {
        $sql = "SELECT A.PeriodToDate, A.PeriodFromDate, A.FiscalYear, A.AccountNo AccountNo, A.CoAccountNo CoAccountNo, NM.UomName UomName,A.NormID NormID, A.NormNo NormNo, A.NormName NormName,
       NM.NormID NormID
                    FROM act_gl_books A
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
        //Cập nhật  I
        public function UpdateI($whereCondition, $preYear,$year, $nextYear)
        {
            $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
           MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
           MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I2,
           MAX(CASE WHEN AccountNo = '0721' AND YearD = $year  THEN I END) AS I3,
           MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I4
    FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
        (Case (when NC.CateNo = '20215' AND NC.CateValue IN('1','3','5','6')) THEN '#I#01'
              (when NC.CateNo = '20215' AND NC.CateValue IN('2','4','5','6')) THEN '#I#02'
              when NC.CateNo = '2041111' THEN '#I#03' END) as ItemID
          FROM act_gl_books AS A
                   INNER JOIN norm_cate AS NC ON NC.NormID = A.CompanyID
          WHERE  A.AccountNo IN ('0461', '0441', '0721','0411')
            AND  NC.CateNo IN  ('2041111','20215') AND NC.CateValue IN ('1','2','3','4','5','6')
              $whereCondition
          GROUP BY A.AccountNo, YearD
         ) AS B
        ) AS HH
         SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
         WHERE TT.ItemID = HH.ItemID";
            DB::statement($sqlUpdate);
        }
    //Cập nhật a.1 & b.1
    public function UpdateSoLuong($whereCondition, $preYear, $year, $nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT Quantity, ItemID,
       MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
       MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I2,
       MAX(CASE WHEN AccountNo = '0721' AND YearD = $year  THEN I END) AS I3,
       MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I4
FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD, NT.Quantity AS Quantity,
    (CASE WHEN  NM.NormNo='563121212111111' THEN '#II#01#01#01#01'
          WHEN  NM.NormNo='563121212111112' THEN '#II#01#01#02#01'
          WHEN  NM.NormNo='563121212111113' THEN '#II#01#02#01#01'
          WHEN  NM.NormNo='563121212111114' THEN '#II#01#02#02#01'
          WHEN  NM.NormNo='563121212111115' THEN '#II#01#03#01' END ) AS ItemID
      FROM act_gl_books AS A
               INNER JOIN norm AS NM ON NM.NormID = A.NormID
               INNER JOIN norm_table_item AS NT ON NT.NormID = A.NormID
      WHERE  A.AccountNo IN ('0461', '0441', '0721','0411')
          $whereCondition
      GROUP BY A.AccountNo, YearD
     ) AS B
    ) AS HH
     SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
     WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    //Cập nhật a.2 & b.2
    public function UpdateDinhMuc($whereCondition, $preYear, $year, $nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT FCMaxUnitPrice, ItemID,
       MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
       MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I2,
       MAX(CASE WHEN AccountNo = '0721' AND YearD = $year  THEN I END) AS I3,
       MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I4
FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD, NT.FCMaxUnitPrice AS FCMaxUnitPrice,
    (CASE WHEN  NM.NormNo='563121212111111' THEN '#II#01#01#01#02'
          WHEN  NM.NormNo='563121212111112' THEN '#II#01#01#02#02'
          WHEN  NM.NormNo='563121212111113' THEN '#II#01#02#01#02'
          WHEN  NM.NormNo='563121212111114' THEN '#II#01#02#02#02'
          WHEN  NM.NormNo='563121212111115' THEN '#II#01#03#02' END ) AS ItemID
      FROM act_gl_books AS A
               INNER JOIN norm AS NM ON NM.NormID = A.NormID
               INNER JOIN norm_table_item AS NT ON NT.NormID = A.NormID
      WHERE  A.AccountNo IN ('0461', '0441', '0721','0411')
          $whereCondition
      GROUP BY A.AccountNo, YearD
     ) AS B
    ) AS HH
     SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
     WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }

    //Cập nhật Số cơ sở I, II, III, IV
    public function MB13_6InsertTemplateCoSo($whereCondition, $preYear,$year, $nextYear)
    {
        $arrInsert = [];
        $sqlSelect = "SELECT  NormCateValue, NormName, NormNo,UomName,
                            MAX(CASE WHEN AccountNo = '0461'  AND YearD = $preYear THEN I END) AS I1,
                            MAX(CASE WHEN AccountNo = '0441'  AND YearD = $year THEN I END) AS I2,
                            MAX(CASE WHEN AccountNo = '0721'  AND YearD = $year THEN I END) AS I3,
                            MAX(CASE WHEN AccountNo = '0411'  AND YearD = $nextYear THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo,A.UomName as UomName, NC.CateValue as NormCateValue, NM.NormName as NormName, NM.NormNo as NormNo
                              FROM act_gl_books AS A
                                       INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.CompanyID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN norm AS NM ON NM.NormID = A.NormID
                              WHERE SC.CateNo = '003' AND SC.CateValue='3'
                                AND CC.CateNo ='2200'
                                AND NC.CateNo ='201151211'

                        $whereCondition
                              GROUP BY YearD, A.AccountNo, NormCateValue, NormName, NormNo, UomName) AS B GROUP BY NormCateValue, NormName, NormNo, UomName";
        $results = DB::select($sqlSelect);
        $results = (array)$results;
        foreach ($results as $value) {
            $value = (array)$value;
                if ($value->NormCateValue == 1 AND $value->CompanyCateValue == 1) {
                    $ParentID = '#I'.'#01';
                    $Level = 4;
                    $ItemID = $ParentID . '#01';
                }
                if ($value->NormCateValue == 2 AND $value->CompanyCateValue == 2) {
                    $ParentID = '#II'.'#01';
                    $Level = 4;
                    $ItemID = $ParentID . '#01';
                }
                if ($value->NormCateValue == 3 AND $value->CompanyCateValue == 3) {
                    $ParentID = '#III'.'#01';
                    $Level = 4;
                    $ItemID = $ParentID . '#01';
                }
                if ($value->NormCateValue == 4 AND $value->CompanyCateValue == 4) {
                    $ParentID = '#IV'.'#01';
                    $Level = 4;
                    $ItemID = $ParentID . '#01';
                }

                $arrInsert[] = [
                    'STT' => '',
                    'Show' => 1,
                    'ItemID' => $ItemID,
                    'ParentID' => $ParentID,
                    'ItemName' => $value->NormName,
                    'UomName'=>$value->UomName,
                    'Level' =>$Level,
                    'FontWeight' => '',
                    'Italic' => '',
                    'Center' => '',
                    'I1' => $value['I1'],
                    'I2' => $value['I2'],
                    'I3' => $value['I3'],
                    'I4' => $value['I4'],
                ];


            if (count($arrInsert)) {
                DB::table('table_tmp')->insert($arrInsert);
            }

        }
    }
    //Cập nhật Số cơ sở V_1_2
    public function MB13_6InsertTemplateKinhPhi($whereCondition, $preYear,$year, $nextYear)
    {
        $arrInsert = [];
        $sqlSelect = "SELECT  NormCateValue, NormName, NormNo,
                            MAX(CASE WHEN AccountNo = '0461'  AND YearD = $preYear THEN I END) AS I1,
                            MAX(CASE WHEN AccountNo = '0441'  AND YearD = $year THEN I END) AS I2,
                            MAX(CASE WHEN AccountNo = '0721'  AND YearD = $year THEN I END) AS I3,
                            MAX(CASE WHEN AccountNo = '0411'  AND YearD = $nextYear THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo, NC.CateValue as NormCateValue, NM.NormName as NormName, NM.NormNo as NormNo
                              FROM act_gl_books AS A
                                       INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN norm AS NM ON NM.NormID = A.NormID
                                    Where SC.CateNo = '003' AND SC.CateValue='3'
                                      AND NC.CateNo IN ('2031111','2031112')
                                      AND NC.CateValue IN ('1','2')
                        $whereCondition
                              GROUP BY YearD, A.AccountNo,  NormCateValue, NormName, NormNo) AS B GROUP BY NormCateValue, NormName, NormNo";
        $results = DB::select($sqlSelect);
        $results = (array)$results;
        foreach ($results as $value) {
            $value = (array)$value;
            if ($value->NormCateNo == '2031111' and $value->NormCateValue == 1) {
                $ParentID = '#V' . '#01' . '#01';
                $Level = 5;
                $ItemID = $ParentID . '#01';
            }
            if ($value->NormCateNo == '2031111' and $value->NormCateValue == 2) {
                $ParentID = '#V' . '#01' . '#02';
                $Level = 5;
                $ItemID = $ParentID . '#02';
            }
            if ($value->NormCateNo == '2031112' and $value->NormCateValue == 1) {
                $ParentID = '#V' . '#02' . '#01';
                $Level = 5;
                $ItemID = $ParentID . '#01';
            }
            if ($value->NormCateNo == '2031112' and $value->NormCateValue == 2) {
                $ParentID = '#V' . '#02' . '#02';
                $Level = 5;
                $ItemID = $ParentID . '#02';
            }
                $arrInsert[] = [
                    'STT' => '',
                    'Show' => 1,
                    'ItemID' => $ItemID,
                    'ParentID' => $ParentID,
                    'ItemName' => $value->NormName,
                    'UomName' => 'Triệu đồng',
                    'Level' => $Level,
                    'FontWeight' => '',
                    'Italic' => '',
                    'Center' => '',
                    'I1' => $value['I1'],
                    'I2' => $value['I2'],
                    'I3' => $value['I3'],
                    'I4' => $value['I4'],
                ];


                if (count($arrInsert)) {
                    DB::table('table_tmp')->insert($arrInsert);
                }

            }
        }
    //Cập nhật V_3
    public function MB13_6InsertTemplateNhiemVu($whereCondition, $preYear,$year, $nextYear)
    {
        $sql = "SELECT  NormCateValue, NormName, NormNo,
                            MAX(CASE WHEN AccountNo = '0461'  AND YearD = $preYear THEN I END) AS I1,
                            MAX(CASE WHEN AccountNo = '0441'  AND YearD = $year THEN I END) AS I2,
                            MAX(CASE WHEN AccountNo = '0721'  AND YearD = $year THEN I END) AS I3,
                            MAX(CASE WHEN AccountNo = '0411'  AND YearD = $nextYear THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo, NC.CateValue as NormCateValue, NM.NormName as NormName, NM.NormNo as NormNo
                              FROM act_gl_books AS A
                                       INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN norm AS NM ON NM.NormID = A.NormID
                                    Where NC.CateNo ='2031199'
                                    AND SC.CateNo = '003' AND SC.CateValue='3'
                        $whereCondition
                              GROUP BY YearD, A.AccountNo,  NormCateValue, NormName, NormNo) AS B GROUP BY NormCateValue, NormName, NormNo";
        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert1 = [];
        $i = 1;
        if ($result) {
            foreach ($result as $template) {
                $STT = $i++;
                $ItemID = '#V' . $template->ItemID;
                $ParentID = '#V';
                $arrInsert1[] = [
                    'STT' => $STT,
                    'Show' => '',
                    'ItemID' => $ItemID,
                    'ParentID' => $ParentID,
                    'ItemName' => $template->ItemName,
                    'Level' => '4',
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
