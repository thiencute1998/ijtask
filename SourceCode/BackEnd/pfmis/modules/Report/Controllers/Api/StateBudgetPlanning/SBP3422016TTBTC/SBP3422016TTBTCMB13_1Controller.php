<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Listing\Models\Program;
use Module\Listing\Models\Project;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB13_1Controller extends Controller {
    public function SBP3422016TTBTC_MB13_1(Request $request){
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
        $this->MB13_1InsertTemplate();
        $this->UpdateStudent($whereCondition,$preYear, $year, $nextYear, '#I#01#02');
        $this->UpdateStudent($whereCondition,$preYear, $year, $nextYear, '#I#02#02');
        $this->UpdateSchool($whereCondition,$preYear, $year, $nextYear);
        $this->UpdateSchool2($whereCondition,$preYear, $year, $nextYear);
        $this->MB13_1InsertTemplateCT($whereCondition);
        $this->MB13_2InsertTemplateNSNN($whereCondition,$preYear, $year, $nextYear);
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
    public function MB13_1InsertTemplate(){
        $arrInsert = [
            [
                'STT' => 'I',
                'Show' => 1,
                'ItemID' => '#I',
                'ParentID' => '',
                'UomName'=>'',
                'ItemName' => 'Các cấp học và trình độ đào tạo theo quy định của Luật Giáo dục, Luật Giáo dục nghề nghiệp',
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
                'ItemName' => 'Đào tạo và bồi dưỡng cán bộ công chức Nhà nước',
                'Level' => 2,
                'FontWeight' => 1,
                'Italic' => '',
                'Center' => ''
            ],

        ];

        $arrInsert[] = [
            'STT' => '1',
            'Show' => 1,
            'ItemID' => '#I'.'#01',
            'ParentID' => '#I',
            'UomName'=>'',
            'ItemName' => 'Giáo dục phổ thông (chi tiết: tiểu học, trung học cơ sở, trung học phổ thông)',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2',
            'Show' => 1,
            'ItemID' => '#I'.'#02',
            'ParentID' => '#I',
            'UomName'=>'',
            'ItemName' => 'Giáo dục nghề nghiệp (chi tiết sơ cấp, trung cấp, cao đẳng,...)',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '3',
            'Show' => 1,
            'ItemID' => '#I'.'#03',
            'ParentID' => '#I',
            'UomName'=>'',
            'ItemName' => 'Giáo dục đại học và sau đại học',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1',
            'Show' => 1,
            'ItemID' => '#II'.'#01',
            'ParentID' => '#II',
            'UomName'=>'Triệu đồng',
            'ItemName' => 'Chi tiêu đào tạo, bồi dưỡng ở trong nước',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2',
            'Show' => 1,
            'ItemID' => '#II'.'#02',
            'ParentID' => '#II',
            'UomName'=>'Triệu đồng',
            'ItemName' => 'Chi tiêu đào tạo, bồi dưỡng ở nước ngoài',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1.1',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#01',
            'ParentID' => '#I'.'#01',
            'UomName'=>'',
            'ItemName' =>'Số trường',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1.2',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#02',
            'ParentID' => '#I'.'#01',
            'UomName'=>'',
            'ItemName' =>'Số học sinh',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1.3',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#03',
            'ParentID' => '#I'.'#01',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Tổng kinh phí NSNN cấp',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#01',
            'ParentID' => '#I'.'#02',
            'UomName'=>'',
            'ItemName' =>'Số trường',
            'Level' => 4,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2.3',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#03',
            'ParentID' => '#I'.'#02',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Tổng kinh phí NSNN cấp',
            'Level' => 4,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1.1',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#01',
            'ParentID' => '#I'.'#03',
            'UomName'=>'',
            'ItemName' =>'Số trường',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1.2',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#02',
            'ParentID' => '#I'.'#03',
            'UomName'=>'',
            'ItemName' =>'Số học sinh',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];

        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#01'.'#01',
            'ParentID' => '#I'.'#01'.'#01',
            'UomName'=>'Trường',
            'ItemName' =>'Trường tiểu học',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#01'.'#02',
            'ParentID' => '#I'.'#01'.'#01',
            'UomName'=>'Trường',
            'ItemName' =>'Trường trung học cơ sở',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'c',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#01'.'#03',
            'ParentID' => '#I'.'#01'.'#01',
            'UomName'=>'Trường',
            'ItemName' =>'Trường trung học phổ thông',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#02'.'#01',
            'ParentID' => '#I'.'#01'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số học sinh ra trường',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#02'.'#02',
            'ParentID' => '#I'.'#01'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số học sinh tuyển mới',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'c',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#02'.'#03',
            'ParentID' => '#I'.'#01'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số học sinh có mặt tại thời điểm 31/5',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'd',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#02'.'#04',
            'ParentID' => '#I'.'#01'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số học sinh bình quân (1)',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#02'.'#04'.'#01',
            'ParentID' => '#I'.'#01'.'#02'.'#04',
            'UomName'=>'Người',
            'ItemName' =>'Trong đó - Số học sinh được miễn học phí',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#02'.'#04'.'#02',
            'ParentID' => '#I'.'#01'.'#02'.'#04',
            'UomName'=>'Người',
            'ItemName' =>'- Số học sinh được giảm học phí',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#02'.'#04'.'#03',
            'ParentID' => '#I'.'#01'.'#02'.'#04',
            'UomName'=>'Người',
            'ItemName' =>'- Số học sinh được hỗ trợ chi phí học tập',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#03'.'#01',
            'ParentID' => '#I'.'#01'.'#03',
            'UomName'=>'',
            'ItemName' =>'Kinh phí giao tự chủ ',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.1',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#03'.'#01'.'#01',
            'ParentID' => '#I'.'#01'.'#03'.'#01',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Chi lương, phụ cấp, các khoản trích nộp theo lương ',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#03'.'#01',
            'ParentID' => '#I'.'#01'.'#03',
            'UomName'=>'',
            'ItemName' =>'Kinh phí không giao tự chủ ',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.1',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#03'.'#02'.'#01',
            'ParentID' => '#I'.'#01'.'#03'.'#02',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Chi nhiệm vụ, chính sách đặc thù do Nhà nước giao',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.2',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#03'.'#02'.'#02',
            'ParentID' => '#I'.'#01'.'#03'.'#02',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Chi mua sắm TSCĐ',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.3',
            'Show' => 1,
            'ItemID' => '#I'.'#01'.'#03'.'#02'.'#03',
            'ParentID' => '#I'.'#01'.'#03'.'#02',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Chi sửa chữa lớn-chống xuống cấp tài sản',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#01'.'#01',
            'ParentID' => '#I'.'#02'.'#01',
            'UomName'=>'Trường',
            'ItemName' =>'Sơ cấp',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#01'.'#02',
            'ParentID' => '#I'.'#02'.'#01',
            'UomName'=>'Trường',
            'ItemName' =>'Trung cấp',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'c',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#01'.'#03',
            'ParentID' => '#I'.'#02'.'#01',
            'UomName'=>'Trường',
            'ItemName' =>'Cao đẳng',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#03'.'#01',
            'ParentID' => '#I'.'#02'.'#03',
            'UomName'=>'',
            'ItemName' =>'Kinh phí giao tự chủ',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#03'.'#02',
            'ParentID' => '#I'.'#02'.'#03',
            'UomName'=>'',
            'ItemName' =>'Kinh phí không giao tự chủ',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.1',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#03'.'#01'.'#01',
            'ParentID' => '#I'.'#02'.'#03'.'#01',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'-Chi lương, phụ cấp, các khoản trích nộp theo lương',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.2',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#03'.'#01'.'#02 ',
            'ParentID' => '#I'.'#02'.'#03'.'#01',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'-Chi hoạt động chuyên môn, quản lý',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.1',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#03'.'#02'.'#01',
            'ParentID' => '#I'.'#02'.'#03'.'#02',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Chi nhiệm vụ, chính sách đặc thù do Nhà nước giao',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.2',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#03'.'#02'.'#02',
            'ParentID' => '#I'.'#02'.'#03'.'#02',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Chi mua sắm TSCĐ',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.3',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#03'.'#02'.'#03',
            'ParentID' => '#I'.'#02'.'#03'.'#03',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Chi sửa chữa lớn-chống xuống cấp tài sản',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#01'.'#01',
            'ParentID' => '#I'.'#03'.'#01',
            'UomName'=>'Trường',
            'ItemName' =>'- Trường tiểu học',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#01'.'#02',
            'ParentID' => '#I'.'#03'.'#01',
            'UomName'=>'Trường',
            'ItemName' =>'- Trường trung học cơ sở',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'c',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#01'.'#03',
            'ParentID' => '#I'.'#03'.'#01',
            'UomName'=>'Trường',
            'ItemName' =>'-Trường trung học phổ thông',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#02'.'#01',
            'ParentID' => '#I'.'#03'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số sinh viên ra trường',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#02'.'#02',
            'ParentID' => '#I'.'#03'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số sinh viên tuyển mới',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'c',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#02'.'#03',
            'ParentID' => '#I'.'#03'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số sinh viên có mặt tại thời điểm 31/5',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'd',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#02'.'#04',
            'ParentID' => '#I'.'#03'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số sinh viên bình quân (1)',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#02'.'#04'.'#01',
            'ParentID' => '#I'.'#03'.'#02'.'#04',
            'UomName'=>'Người',
            'ItemName' =>'- Số sinh viên được miễn học phí',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#02'.'#04'.'#02',
            'ParentID' => '#I'.'#03'.'#02'.'#04',
            'UomName'=>'Người',
            'ItemName' =>'- Số sinh viên được giảm học phí',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#02'.'#04'.'#02',
            'ParentID' => '#I'.'#03'.'#02'.'#04',
            'UomName'=>'Người',
            'ItemName' =>'- Số học sinh được hỗ trợ chi phí học tập',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1.3',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#03',
            'ParentID' => '#I'.'#03',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Tổng kinh phí NSNN cấp',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#03'.'#01',
            'ParentID' => '#I'.'#03'.'#03',
            'UomName'=>'',
            'ItemName' =>'Kinh phí giao tự chủ ',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.1',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#03'.'#01'.'#01',
            'ParentID' => '#I'.'#03'.'#03'.'#01',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'-Chi lương, phụ cấp, các khoản trích nộp theo lương',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a.2',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#03'.'#01'.'#02',
            'ParentID' => '#I'.'#03'.'#03'.'#01',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'-Chi hoạt động chuyên môn, quản lý',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#03'.'#02',
            'ParentID' => '#I'.'#03'.'#03',
            'UomName'=>'',
            'ItemName' =>'Kinh phí không giao tự chủ ',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.1',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#03'.'#02'.'#01',
            'ParentID' => '#I'.'#03'.'#03'.'#02',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Chi nhiệm vụ, chính sách đặc thù do Nhà nước giao',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.2',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#03'.'#02'.'#02',
            'ParentID' => '#I'.'#03'.'#03'.'#02',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Chi mua sắm TSCĐ',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b.3',
            'Show' => 1,
            'ItemID' => '#I'.'#03'.'#03'.'#02'.'#03',
            'ParentID' => '#I'.'#03'.'#03'.'#02',
            'UomName'=>'Triệu đồng',
            'ItemName' =>'Chi sửa chữa lớn-chống xuống cấp tài sản',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];;
        $arrInsert[] = [
            'STT' => '2.2',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#02',
            'ParentID' => '#I'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số sinh viên',
            'Level' => 4,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#02'.'#01',
            'ParentID' => '#I'.'#02'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số sinh viên ra trường',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#02'.'#02',
            'ParentID' => '#I'.'#02'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số sinh viên tuyển mới',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'c',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#02'.'#03',
            'ParentID' => '#I'.'#02'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số sinh viên có mặt tại thời điểm 31/5',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'd',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#02'.'#04',
            'ParentID' => '#I'.'#02'.'#02',
            'UomName'=>'Người',
            'ItemName' =>'Số sinh viên bình quân (1)',
            'Level' => 5,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#02'.'#04'.'#01',
            'ParentID' => '#I'.'#02'.'#02'.'#04',
            'UomName'=>'Người',
            'ItemName' =>'Trong đó - Số sinh viên được miễn học phí',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#02'.'#04'.'#02',
            'ParentID' => '#I'.'#02'.'#02'.'#04',
            'UomName'=>'Người',
            'ItemName' =>'- Số học sinh được giảm học phí',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '',
            'Show' => 1,
            'ItemID' => '#I'.'#02'.'#02'.'#04'.'#03',
            'ParentID' => '#I'.'#02'.'#02'.'#04',
            'UomName'=>'Người',
            'ItemName' =>'- Số học sinh được hỗ trợ chi phí học tập',
            'Level' => 6,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];



        DB::table('table_tmp')->insert($arrInsert);
    }

    public function MB13_1InsertTemplateCT($whereCondition)
    {
        $sql = "SELECT A.PeriodToDate, A.PeriodFromDate, A.FiscalYear, A.AccountNo AccountNo, A.CoAccountNo CoAccountNo, NM.UomName UomName,A.NormID NormID, A.NormNo NormNo, A.NormName NormName,
       NM.NormID NormID
                    FROM act_gl_books A
                        INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                        INNER JOIN norm NM ON NM.NormID = A.NormID
                        INNER JOIN norm_cate NC ON NC.NormID = A.NormID
                        WHERE SC.CateNo = '003' AND SC.CateValue='3'
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
                    'Level'=>'',
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
    //Cập nhật I1_1.1
    public function UpdateSchool($whereCondition, $preYear,$year, $nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
       MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
       MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I2,
       MAX(CASE WHEN AccountNo = '0721' AND YearD = $year  THEN I END) AS I3,
       MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I4
FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
      (Case when NC.CateValue='1' THEN '#I#01#01#01'
            When NC.CateValue='2' THEN '#I#01#01#02'
            When NC.CateValue='3' THEN '#I#01#01#03' END) as ItemID
      FROM act_gl_books AS A
               INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
               INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
               INNER JOIN company_cate AS CC ON CC.CompanyID = A.CompanyID
      WHERE   SC.CateNo = '003' AND SC.CateValue='3'
        AND  A.AccountNo IN ('0461', '0441', '0721','0411')
        AND  NC.CateNo = '202111113'
        AND  CC.CateNo= '2110' AND CC.CateValue = '1'
          $whereCondition
      GROUP BY A.AccountNo, YearD
     ) AS B
    ) AS HH
     SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
     WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    //Cập nhật 2.1
    public function UpdateSchool2($whereCondition, $preYear,$year, $nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
       MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
       MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I2,
       MAX(CASE WHEN AccountNo = '0721' AND YearD = $year  THEN I END) AS I3,
       MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I4
FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
      (Case when NC.CateValue='1' THEN '#I#02#01#01'
            When NC.CateValue='2' THEN '#I#02#01#02'
            When NC.CateValue='3' THEN '#I#02#01#03' END) as ItemID
      FROM act_gl_books AS A
               INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
               INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
               INNER JOIN company_cate AS CC ON CC.CompanyID = A.CompanyID
      WHERE   SC.CateNo = '003' AND SC.CateValue='3'
        AND  A.AccountNo IN ('0461', '0441', '0721','0411')
        AND  NC.CateNo = '202111115'
        AND  CC.CateNo= '2110' AND CC.CateValue = '2'
          $whereCondition
      GROUP BY A.AccountNo, YearD
     ) AS B
    ) AS HH
     SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
     WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    //Cập nhật II
    public function UpdateII($whereCondition, $preYear,$year, $nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
       MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
       MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I2,
       MAX(CASE WHEN AccountNo = '0721' AND YearD = $year  THEN I END) AS I3,
       MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I4
FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
      (Case when NC.CateNo='5121211121511' THEN '#II#01'
            When NC.CateNo='5121211121512' THEN '#II#02'  END) as ItemID
      FROM act_gl_books AS A
               INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
               INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
      WHERE   SC.CateNo = '003' AND SC.CateValue='3'
        AND  A.AccountNo IN ('0461', '0441', '0721','0411')
        AND  NC.CateNo IN ('5121211121511','5121211121512')
          $whereCondition
      GROUP BY A.AccountNo, YearD
     ) AS B
    ) AS HH
     SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
     WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    //Cập nhật 1.2&2.2
    public function UpdateStudent($whereCondition, $preYear,$year, $nextYear, $parentID1)
    {
        $sql = "SELECT  NormCateValue,
                            MAX(CASE WHEN AccountNo = '0461'  AND YearD = $preYear THEN I END) AS I1,
                            MAX(CASE WHEN AccountNo = '0441'  AND YearD = $year THEN I END) AS I2,
                            MAX(CASE WHEN AccountNo = '0721'  AND YearD = $year THEN I END) AS I3,
                            MAX(CASE WHEN AccountNo = '0411'  AND YearD = $nextYear THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo, NC.CateValue as NormCateValue
                        FROM act_gl_books AS A
                                       INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN norm AS NM ON NM.NormID = A.NormID
                                    Where NC.CateNo ='202111311'
                                    AND SC.CateNo = '003' AND SC.CateValue='3'
                        $whereCondition
                              GROUP BY YearD, A.AccountNo,  NormCateValue ) AS B GROUP BY NormCateValue ";
        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert1 = [];
        $i = 1;
        if ($result) {
            foreach ($result as $template) {
                if($template->NormCateValue == 1 || $template->NormCateValue == 2 || $template->NormCateValue == 3 || $template->NormCateValue == 4 ){
                    $parentID = $parentID1;
                    $level = 4;
                } else {
                    $parentID = '';
                    $level = '';
                }
                $STT = $i++;
                $ItemID = $parentID1.$template->NormCateValue;
                $arrInsert1[] = [
                    'STT' => $STT,
                    'Show' => '',
                    'ItemID' => $ItemID,
                    'ParentID' => $parentID,
                    'ItemName' => $template->ItemName,
                    'Level' => $level,
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

    public function MB13_2InsertTemplateNSNN($whereCondition, $preYear,$year, $nextYear)
    {
        $arrInsert = [];
        $sqlSelect = "SELECT  NormCateValue, NormNo,
                            MAX(CASE WHEN AccountNo = '0461'  AND YearD = $preYear THEN I END) AS I1,
                            MAX(CASE WHEN AccountNo = '0441'  AND YearD = $year THEN I END) AS I2,
                            MAX(CASE WHEN AccountNo = '0721'  AND YearD = $year THEN I END) AS I3,
                            MAX(CASE WHEN AccountNo = '0411'  AND YearD = $nextYear THEN I END) AS I4
                        FROM (SELECT SUM(FCDebitAmount) AS I, YEAR(A.PeriodFromDate) AS YearD,A.AccountNo, NC.CateValue as NormCateValue, NM.NormNo as NormNo
                              FROM act_gl_books AS A
                                       INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
                                       INNER JOIN company_cate AS CC ON CC.CompanyID = A.CompanyID
                                       INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                       INNER JOIN norm AS NM ON NM.NormID = A.NormID
                              WHERE SC.CateNo = '003' AND SC.CateValue='3'
                                AND CC.CateNo ='2110' AND CC.CateValue='1'
                                AND NC.CateNo ='20112111113'
                        $whereCondition
                              GROUP BY YearD, A.AccountNo, NormCateValue,  NormNo) AS B GROUP BY NormCateValue,  NormNo";
        $results = DB::select($sqlSelect);
        $results = (array)$results;
        foreach ($results as $value) {
            $value = (array)$value;
            if ($value->NormCateValue == 1 ) {
                $ParentID = '#I'.'#01'.'#03'.'#01';
                $Level = 6;
                $ItemID = $ParentID . '#01';
            }
            if ($value->NormCateValue == 2) {
                $ParentID = '#I'.'#01'.'#03'.'#02';
                $Level = 6;
                $ItemID = $ParentID . '#01';
            }
            $arrInsert[] = [
                'STT' => '',
                'Show' => 1,
                'ItemID' => $ItemID,
                'ParentID' => $ParentID,
                'ItemName' => $value->NormName,
                'UomName'=>'Triệu đồng',
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
