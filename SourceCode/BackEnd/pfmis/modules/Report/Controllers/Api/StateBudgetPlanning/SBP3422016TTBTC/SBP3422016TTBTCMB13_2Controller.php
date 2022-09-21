<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Listing\Models\Program;
use Module\Listing\Models\Project;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB13_2Controller extends Controller {
    public function SBP3422016TTBTC_MB13_2(Request $request){
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
        $this->MB13_2InsertTemplate();
        $this->MB13_2InsertTemplateCoSo($whereCondition,$preYear , $year, $nextYear);
        $this->UpdateI3_II3_III2_IV2($whereCondition,$preYear, $year, $nextYear);
        $this->UpdateI2_II2($whereCondition,$preYear, $year, $nextYear);
        $this->MB13_2InsertTemplateCT($whereCondition);
        $this->MB13_2InsertTemplateKinhPhi($whereCondition,$preYear, $year, $nextYear);
        $this->MB13_2InsertTemplateNhiemVu($whereCondition,$preYear, $year, $nextYear);
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
    public function MB13_2InsertTemplate(){
        $arrInsert = [
            [
                'STT' => 'I',
                'Show' => 1,
                'ItemID' => '#I',
                'ParentID' => '',
                'UomName'=>'',
                'ItemName' => 'Cơ sở khám,chữa bệnh',
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
                'ItemName' => 'Cơ sở điều dưỡng và phục hồi chức năng',
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
                'ItemName' => 'Cơ sở y tế dự phòng',
                'Level' => 2,
                'FontWeight' => 1,
                'Italic' => '',
                'Center' => ''
            ],
            [
                'STT' => 'IV',
                'Show' => 1,
                'ItemID' => '#IV',
                'UomName'=>'',
                'ParentID' => '',
                'ItemName' => 'Đơn vị sự nghiệp y tế, chuyên ngành đặc thù',
                'Level' => 2,
                'FontWeight' => 1,
                'Italic' => '',
                'Center' => ''
            ],
            [
                'STT' => 'V',
                'Show' => 1,
                'ItemID' => '#V',
                'ParentID' => '',
                'UomName'=>'',
                'ItemName' => 'Tổng kinh phí NSNN cấp',
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
            'UomName'=>'Cơ sở',
            'ItemName' => 'Số Cơ sở',
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
            'UomName'=>'Giường',
            'ItemName' => 'Số giường bệnh',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '3',
            'Show' => 1,
            'ItemID' => '#I'. '#03',
            'UomName'=>'Người',
            'ParentID' =>  '#I' ,
            'ItemName' => 'Số người lao động được cấp có thẩm quyền phê duyệt',
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
            'UomName'=>'Cơ sở',
            'ItemName' => 'Số Cơ sở',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2',
            'Show' => 1,
            'ItemID' => '#II'. '#02',
            'UomName'=>'Giường',
            'ParentID' => '#II',
            'ItemName' => 'Số giường bệnh',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '3',
            'Show' => 1,
            'ItemID' => '#II'. '#03',
            'UomName'=>'Người',
            'ParentID' =>  '#II' ,
            'ItemName' => 'Số người lao động được cấp có thẩm quyền phê duyệt',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1',
            'Show' => 1,
            'ItemID' => '#III'. '#01',
            'ParentID' => '#III',
            'UomName'=>'Cơ sở',
            'ItemName' => 'Số Cơ sở',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2',
            'Show' => 1,
            'ItemID' => '#III'. '#02',
            'UomName'=>'Người',
            'ParentID' =>  '#III' ,
            'ItemName' => 'Số người lao động được cấp có thẩm quyền phê duyệt',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1',
            'Show' => 1,
            'ItemID' => '#IV'. '#01',
            'ParentID' => '#IV',
            'UomName'=>'Cơ sở',
            'ItemName' => 'Số Cơ sở',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2',
            'Show' => 1,
            'ItemID' => '#IV'. '#02',
            'UomName'=>'Người',
            'ParentID' =>  '#IV' ,
            'ItemName' => 'Số người lao động được cấp có thẩm quyền phê duyệt',
            'Level' => 3,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '1',
            'Show' => 1,
            'ItemID' => '#V'. '#01',
            'ParentID' => '#V',
            'UomName'=>'',
            'ItemName' => 'Nhiệm vụ khám chữa bệnh',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '2',
            'Show' => 1,
            'ItemID' => '#V'. '#02',
            'ParentID' => '#V',
            'UomName'=>'',
            'ItemName' => 'Nhiệm vụ phòng bệnh',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => '3',
            'Show' => 1,
            'ItemID' => '#V'. '#03',
            'ParentID' => '#V',
            'UomName'=>'',
            'ItemName' => 'Nhiệm vụ',
            'Level' => 3,
            'FontWeight' => '1',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#V'.'#01'.'#01',
            'ParentID' => '#V'.'#01',
            'UomName'=>'Triệu đồng',
            'ItemName' => 'Kinh phí giao tự chủ',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#V'.'#01'.'#02',
            'ParentID' => '#V'.'#01',
            'UomName'=>'Triệu đồng',
            'ItemName' => 'Kinh phí không giao tự chủ',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'a',
            'Show' => 1,
            'ItemID' => '#V'.'#02'.'#01',
            'ParentID' => '#V'.'#02',
            'UomName'=>'Triệu đồng',
            'ItemName' => 'Kinh phí giao tự chủ',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];
        $arrInsert[] = [
            'STT' => 'b',
            'Show' => 1,
            'ItemID' => '#V'.'#02'.'#02',
            'ParentID' => '#V'.'#02',
            'UomName'=>'Triệu đồng',
            'ItemName' => 'Kinh phí không giao tự chủ',
            'Level' => 4,
            'FontWeight' => '',
            'Italic' => '',
            'Center' => ''
        ];


        DB::table('table_tmp')->insert($arrInsert);
    }

    public function MB13_2InsertTemplateCT($whereCondition)
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
    //Cập nhật số giường bệnh I & II
    public function UpdateI2_II2($whereCondition, $preYear,$year, $nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
       MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
       MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I2,
       MAX(CASE WHEN AccountNo = '0721' AND YearD = $year  THEN I END) AS I3,
       MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I4
FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
      (Case when CC.CateValue='1' THEN '#I#02'
            When CC.CateValue='2' THEN '#II#02' END) as ItemID
      FROM act_gl_books AS A
               INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
               INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
               INNER JOIN company_cate AS CC ON CC.CompanyID = A.CompanyID
      WHERE   SC.CateNo = '003' AND SC.CateValue='3'
        AND  A.AccountNo IN ('0461', '0441', '0721','0411')
        AND  NC.CateNo = '201151212'
        AND  CC.CateNo= '2200'
          $whereCondition
      GROUP BY A.AccountNo, YearD
     ) AS B
    ) AS HH
     SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
     WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    //Cập nhật Số người lao động được cấp có thẩm quyền phê duyệt I, II, III, IV
    public function UpdateI3_II3_III2_IV2($whereCondition, $preYear,$year, $nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
       MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
       MAX(CASE WHEN AccountNo = '0441' AND YearD = $year  THEN I END) AS I2,
       MAX(CASE WHEN AccountNo = '0721' AND YearD = $year  THEN I END) AS I3,
       MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear  THEN I END) AS I4
FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
    (Case when CC.CateValue='1' THEN '#I#03'
          When CC.CateValue='2' THEN '#II#03'
          when CC.CateValue='3' THEN '#III#02'
          When CC.CateValue='4' THEN '#IV#02' END) as ItemID
      FROM act_gl_books AS A
               INNER JOIN sector_cate SC ON SC.SectorID = A.SectorID
               INNER JOIN norm_cate AS NC ON NC.NormID = A.CompanyID
               INNER JOIN company_cate AS CC ON CC.CompanyID = A.CompanyID
      WHERE  SC.CateNo = '003' AND SC.CateValue='3'
        AND  A.AccountNo IN ('0461', '0441', '0721','0411')
        AND  NC.CateNo = '201151213' AND NC.CateValue='1'
        AND  CC.CateNo= '2200'
          $whereCondition
      GROUP BY A.AccountNo, YearD
     ) AS B
    ) AS HH
     SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
     WHERE TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }
    //Cập nhật Số cơ sở I, II, III, IV
    public function MB13_2InsertTemplateCoSo($whereCondition, $preYear,$year, $nextYear)
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
    //Cập nhật  V_1_2
    public function MB13_2InsertTemplateKinhPhi($whereCondition, $preYear,$year, $nextYear)
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
    public function MB13_2InsertTemplateNhiemVu($whereCondition, $preYear,$year, $nextYear)
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
