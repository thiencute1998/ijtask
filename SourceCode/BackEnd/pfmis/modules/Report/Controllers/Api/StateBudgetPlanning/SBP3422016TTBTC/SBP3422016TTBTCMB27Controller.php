<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB27Controller extends Controller {
    public function SBP3422016TTBTC_MB27(Request $request){
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
        ) $whereCompany  AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18 AND LEFT(EC.CateNo,5) = '20152' ";


        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
          `ItemName` varchar(100) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `TranTypeID` int(2) DEFAULT 1,
          `Show` int(1) DEFAULT 1,
          `AccountNo` varchar(15) DEFAULT '',
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `CapitalNo` varchar(100) DEFAULT '',
          `CapitalCateNo` varchar(100) DEFAULT '',
          `CapitalValue` varchar(100) DEFAULT '',
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
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $this->MB27InsertTemplate();
        $this->MB27InsertTemplateII($whereCondition);
        $this->MB27InsertTemplateIII($whereCondition);
        $this->updateI($whereCondition,$preYear);
        $this->updateINS($whereCondition,$year,$nextYear);
        $this->updateI1($whereCondition,$preYear);
        $this->updateI1NS($whereCondition,$year,$nextYear);
        $this->updateII($whereCondition,$preYear);
        $this->updateIINS($whereCondition,$year,$nextYear);
        $this->updateIII($whereCondition,$preYear);
        $this->updateIIINS($whereCondition,$year,$nextYear);

        // sum up
        $this->sumUpTableTmp(1, 6);


        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID')->get();

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Kế hoạch trung hạn 5 năm ' . $year ;
            $data[0]->HeaderName2 = 'Thực hiện năm ' . $preYear ;
            $data[0]->HeaderName3 = 'Năm ' . $year ;
            $data[0]->HeaderName4 = 'Kế hoạch được giao năm hiện hành ';
            $data[0]->HeaderName5 = 'Số vốn kéo dài các năm trước sang năm hiện hành ' ;
            $data[0]->HeaderName6 = 'Giải ngân từ 1/1/năm hiện hành đến 30/6/năm hiện hành ' ;
            $data[0]->HeaderName7 = 'Ước thực hiện năm hiện hành ' ;
            $data[0]->HeaderName8 = 'Dự kiến kế hoạch năm ' .$nextYear ;
        }

        foreach ($data as $key => $value) {
            $data[$key]->I1 = (int)($data[$key]->I2)+(int)($data[$key]->I3);
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = (int)($data[$key]->I5)+(int)($data[$key]->I6);
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
            $data[$key]->I7 = (int)($data[$key]->I8)+(int)($data[$key]->I9);
            $data[$key]->I7 = ($data[$key]->I7) ? $data[$key]->I7 : '';
            $data[$key]->I8 = ($data[$key]->I8) ? $data[$key]->I8 : '';
            $data[$key]->I9 = ($data[$key]->I9) ? $data[$key]->I9 : '';
            $data[$key]->I10 = (int)($data[$key]->I11)+(int)($data[$key]->I12);
            $data[$key]->I10 = ($data[$key]->I10) ? $data[$key]->I10 : '';
            $data[$key]->I11 = ($data[$key]->I11) ? $data[$key]->I11 : '';
            $data[$key]->I12 = ($data[$key]->I12) ? $data[$key]->I12 : '';
            $data[$key]->I13 = (int)($data[$key]->I14)+(int)($data[$key]->I15);
            $data[$key]->I13 = ($data[$key]->I13) ? $data[$key]->I13 : '';
            $data[$key]->I14 = ($data[$key]->I14) ? $data[$key]->I14 : '';
            $data[$key]->I15 = ($data[$key]->I15) ? $data[$key]->I15 : '';
            $data[$key]->I16 = (int)($data[$key]->I17)+(int)($data[$key]->I18);
            $data[$key]->I16 = ($data[$key]->I16) ? $data[$key]->I16 : '';
            $data[$key]->I17 = ($data[$key]->I17) ? $data[$key]->I17 : '';
            $data[$key]->I18 = ($data[$key]->I18) ? $data[$key]->I18 : '';
            $data[$key]->I19 = (int)($data[$key]->I20)+(int)($data[$key]->I21);
            $data[$key]->I19 = ($data[$key]->I19) ? $data[$key]->I19 : '';
            $data[$key]->I20 = ($data[$key]->I20) ? $data[$key]->I20 : '';
            $data[$key]->I21 = ($data[$key]->I21) ? $data[$key]->I21 : '';
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
//                "TransNo" => "00001",
                "ReportName" => "TỔNG HỢP DỰ TOÁN CHI ĐẦU TƯ PHÁT TRIỂN NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => ($company) ? $company->CompanyName : 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    // Tĩnh
    public function MB27InsertTemplate(){
        $arrInsert = [
            [
                'STT' => '',
                'Show' => 1,
                'ItemID' => 'A#',
                'ParentID' => '',
                'ItemName' => 'Tổng số',
                'AccountNo' => '',
                'Level' => '1',
                'FontWeight' => '1',
                'Italic' => '',
                'Center' => '1',
                'CapitalNo'=>'',
                'CapitalCateNo'=>'',
                'CapitalValue'=>'',
                'I1'=>'',
                'I2'=>'',
                'I3'=>'',
                'I4'=>'',
                'I5'=>'',
                'I6'=>'',
                'I7'=>'',
                'I8'=>'',
                'I9'=>'',
                'I10'=>'',
                'I11'=>'',
                'I12'=>'',
                'I13'=>'',
                'I14'=>'',
                'I15'=>'',
                'I16'=>'',
                'I17'=>'',
                'I18'=>'',
                'I19'=>'',
                'I20'=>'',
                'I21'=>'',

            ],
            [
                'STT' => 'I',
                'Show' => 1,
                'ItemID' => 'A#01',
                'ParentID' => 'A#',
                'ItemName' => 'Vốn NSNN',
                'AccountNo' => '',
                'Level' => '2',
                'FontWeight' => '1',
                'Italic' => '',
                'Center' => '',
                'CapitalNo'=>'2021',
                'CapitalCateNo'=>'',
                'CapitalValue'=>'',
                'I1'=>'',
                'I2'=>'',
                'I3'=>'',
                'I4'=>'',
                'I5'=>'',
                'I6'=>'',
                'I7'=>'',
                'I8'=>'',
                'I9'=>'',
                'I10'=>'',
                'I11'=>'',
                'I12'=>'',
                'I13'=>'',
                'I14'=>'',
                'I15'=>'',
                'I16'=>'',
                'I17'=>'',
                'I18'=>'',
                'I19'=>'',
                'I20'=>'',
                'I21'=>'',

            ],
            [
                'STT' => '',
                'Show' => 1,
                'ItemID' => 'A#01#01',
                'ParentID' => '',
                'ItemName' => 'Trong đó:',
                'AccountNo' => '',
                'Level' => '4',
                'FontWeight' => '',
                'Italic' => '1',
                'Center' => '',
                'CapitalNo'=>'',
                'CapitalCateNo'=>'',
                'CapitalValue'=>'',
                'I1'=>'',
                'I2'=>'',
                'I3'=>'',
                'I4'=>'',
                'I5'=>'',
                'I6'=>'',
                'I7'=>'',
                'I8'=>'',
                'I9'=>'',
                'I10'=>'',
                'I11'=>'',
                'I12'=>'',
                'I13'=>'',
                'I14'=>'',
                'I15'=>'',
                'I16'=>'',
                'I17'=>'',
                'I18'=>'',
                'I19'=>'',
                'I20'=>'',
                'I21'=>'',

            ],
            [
                'STT' => '',
                'Show' => 1,
                'ItemID' => 'A#01#20211',
                'ParentID' => 'A#01',
                'ItemName' => 'Vốn trái phiếu Chính phủ',
                'AccountNo' => '',
                'Level' => '3',
                'FontWeight' => '',
                'Italic' => '',
                'Center' => '',
                'CapitalNo'=>'20211',
                'CapitalCateNo'=>'',
                'CapitalValue'=>'1',
                'I1'=>'',
                'I2'=>'',
                'I3'=>'',
                'I4'=>'',
                'I5'=>'',
                'I6'=>'',
                'I7'=>'',
                'I8'=>'',
                'I9'=>'',
                'I10'=>'',
                'I11'=>'',
                'I12'=>'',
                'I13'=>'',
                'I14'=>'',
                'I15'=>'',
                'I16'=>'',
                'I17'=>'',
                'I18'=>'',
                'I19'=>'',
                'I20'=>'',
                'I21'=>'',

            ],
            [
                'STT' => 'II',
                'Show' => 1,
                'ItemID' => 'A#02',
                'ParentID' => 'A#',
                'ItemName' => 'Vốn đầu tư từ nguồn thu để lại nhưng chưa đưa vào NSNN',
                'AccountNo' => '',
                'Level' => '2',
                'FontWeight' => '1',
                'Italic' => '',
                'Center' => '',
                'CapitalNo'=>'2022',
                'CapitalCateNo'=>'',
                'CapitalValue'=>'1',
                'I1'=>'',
                'I2'=>'',
                'I3'=>'',
                'I4'=>'',
                'I5'=>'',
                'I6'=>'',
                'I7'=>'',
                'I8'=>'',
                'I9'=>'',
                'I10'=>'',
                'I11'=>'',
                'I12'=>'',
                'I13'=>'',
                'I14'=>'',
                'I15'=>'',
                'I16'=>'',
                'I17'=>'',
                'I18'=>'',
                'I19'=>'',
                'I20'=>'',
                'I21'=>'',

            ],
            [
                'STT' => 'III',
                'Show' => 1,
                'ItemID' => 'A#03',
                'ParentID' => 'A#',
                'ItemName' => 'Các nguồn vốn khác',
                'AccountNo' => '',
                'Level' => '2',
                'FontWeight' => '1',
                'Italic' => '',
                'Center' => '',
                'CapitalNo'=>'2029',
                'CapitalCateNo'=>'',
                'CapitalValue'=>'1',
                'I1'=>'',
                'I2'=>'',
                'I3'=>'',
                'I4'=>'',
                'I5'=>'',
                'I6'=>'',
                'I7'=>'',
                'I8'=>'',
                'I9'=>'',
                'I10'=>'',
                'I11'=>'',
                'I12'=>'',
                'I13'=>'',
                'I14'=>'',
                'I15'=>'',
                'I16'=>'',
                'I17'=>'',
                'I18'=>'',
                'I19'=>'',
                'I20'=>'',
                'I21'=>'',

            ],

        ];
        DB::table('table_tmp')->insert($arrInsert);
    }
    // Động

    public function MB27InsertTemplateII($whereCondition)
    {
        $sql = "SELECT A.PeriodToDate, A.PeriodFromDate, A.AccountNo, A.ExpenseNo ExpenseNo, A.ExpenseID ExpenseID,
                       A.CapitalNo , A.CapitalName ItemName, A.CapitalID CapitalID, EC.ExpenseID, EC.CateNo ExpenseCateNo, CC.CateNo CapitalCateNo,
                        CC.CateValue CapitalValue
                    FROM act_gl_books A
                          INNER JOIN expense_cate EC ON EC.ExpenseID= A.ExpenseID
                          INNER JOIN capital_cate CC ON CC.CapitalID= A.CapitalID
                          INNER JOIN capital CL ON CL.CapitalID= CC.CapitalID
                          WHERE LEFT(CC.CateNo,4) = '2022' AND CC.CateValue = '1'
                            $whereCondition GROUP BY CapitalCateNo, ExpenseCateNo, ItemName ";

        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert1 = [];
        $i = 1;
        if ($result) {
            foreach ($result as $template) {
                $STT = $i++;
                $ItemID = 'A#02#' . $template->CapitalID ;
                $ParentID = 'A#02';
                $arrInsert1[] = [
                    'STT' => $STT,
                    'Show'=>'',
                    'ItemID' => $ItemID,
                    'ParentID' => $ParentID,
                    'ItemName' => $template->ItemName,
                    'AccountNo' => $template->AccountNo,
                    'Level'=>'3',
                    'FontWeight'=> '',
                    'Italic' =>'',
                    'Center'=>'',
                    'CapitalNo'=>'2022',
                    'CapitalCateNo'=>'',
                    'CapitalValue'=>'1',
                    'I1'=>'',
                    'I2'=>'',
                    'I3'=>'',
                    'I4'=>'',
                    'I5'=>'',
                    'I6'=>'',
                    'I7'=>'',
                    'I8'=>'',
                    'I9'=>'',
                    'I10'=>'',
                    'I11'=>'',
                    'I12'=>'',
                    'I13'=>'',
                    'I14'=>'',
                    'I15'=>'',
                    'I16'=>'',
                    'I17'=>'',
                    'I18'=>'',
                    'I19'=>'',
                    'I20'=>'',
                    'I21'=>'',
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

    public function MB27InsertTemplateIII($whereCondition)
    {
        $sql = "SELECT A.PeriodToDate, A.PeriodFromDate, A.AccountNo, A.ExpenseNo ExpenseNo, A.ExpenseID ExpenseID,
                       A.CapitalNo, A.CapitalName ItemName, A.CapitalID CapitalID, EC.ExpenseID, EC.CateNo ExpenseCateNo, CC.CateNo CapitalCateNo,
                        CC.CateValue CapitalValue
                    FROM act_gl_books A
                          INNER JOIN expense_cate EC ON EC.ExpenseID= A.ExpenseID
                          INNER JOIN capital_cate CC ON CC.CapitalID= A.CapitalID
                          INNER JOIN capital CL ON CL.CapitalID= CC.CapitalID
                          WHERE  LEFT(CC.CateNo,4) = '2029' AND CC.CateValue = '1' $whereCondition  GROUP BY CapitalCateNo, ExpenseCateNo, ItemName ";

        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert2 = [];
        $i = 1;
        if ($result) {
            foreach ($result as $template) {
                $STT = $i++;
                $ItemID = 'A#03#' . $template->CapitalID ;
                $ParentID = 'A#03';
                $arrInsert2[] = [
                    'STT' => $STT,
                    'Show'=>'',
                    'ItemID' => $ItemID,
                    'ParentID' => $ParentID,
                    'ItemName' => $template->ItemName,
                    'AccountNo' => $template->AccountNo,
                    'Level'=>'3',
                    'FontWeight'=> '',
                    'Italic' =>'',
                    'Center'=>'',
                    'CapitalNo'=>'2029',
                    'CapitalCateNo'=>'',
                    'CapitalValue'=>'1',
                    'I1'=>'',
                    'I2'=>'',
                    'I3'=>'',
                    'I4'=>'',
                    'I5'=>'',
                    'I6'=>'',
                    'I7'=>'',
                    'I8'=>'',
                    'I9'=>'',
                    'I10'=>'',
                    'I11'=>'',
                    'I12'=>'',
                    'I13'=>'',
                    'I14'=>'',
                    'I15'=>'',
                    'I16'=>'',
                    'I17'=>'',
                    'I18'=>'',
                    'I19'=>'',
                    'I20'=>'',
                    'I21'=>'',
                ];
            }
            DB::table('table_tmp')->insert($arrInsert2);
        }
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }
    public function updateI($whereCondition,$preYear){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT CapitalCateNo,CapitalName,
                                MAX(CASE WHEN (AccountNo = '0461' OR AccountNo = '0462') AND YearD = $preYear AND CapitalInOut = '1' THEN I END) AS I5,
                                MAX(CASE WHEN (AccountNo = '0461' OR AccountNo = '0462') AND YearD = $preYear AND CapitalInOut = '2' THEN I END) AS I6
                         FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
                               A.ExpenseNo ExpenseNo, A.ExpenseID ExpenseID, A.CapitalName, CC.CateNo CapitalCateNo, CL.CapitalInOut CapitalInOut
                               FROM act_gl_books A
                                   INNER JOIN expense_cate EC ON EC.ExpenseID= A.ExpenseID
                                   INNER JOIN capital_cate CC ON CC.CapitalID= A.CapitalID
                                   INNER JOIN capital CL ON CL.CapitalID= CC.CapitalID
                               WHERE CC.CateNo = '2021'
                                 AND A.AccountNo IN ('0461', '0462') AND  CL.CapitalInOut IN ('1','2') $whereCondition
                               GROUP BY YearD, CapitalCateNo,CapitalInOut) AS B
                                GROUP BY CapitalCateNo,ExpenseNo ) AS HH
                                SET TT.I5= HH.I5,  TT.I6= HH.I6
                                WHERE TT.ParentID = 'A#'  ";
        DB::statement($sqlUpdate);
    }
    public function updateINS($whereCondition,$year,$nextYear){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT CapitalName,CapitalCateNo,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '2' AND CapitalInOut = '1' THEN I END) AS I8,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '2' AND CapitalInOut = '2' THEN I END) AS I9,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '1' AND CapitalInOut = '1' THEN I END) AS I11,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '1' AND CapitalInOut = '2' THEN I END) AS I12,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '1' THEN D END) AS I14,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '2' THEN D END) AS I15,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '1' THEN I END) AS I17,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '2' THEN I END) AS I18,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $nextYear AND YearP = '2' AND CapitalInOut = '1' THEN I END) AS I20,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $nextYear AND YearP = '2' AND CapitalInOut = '2' THEN I END) AS I21
                         FROM (SELECT SUM(FCDebitAmount) AS I, SUM(FCCreditAmount) AS D, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
                                      A.ExpenseNo ExpenseNo, A.ExpenseID ExpenseID,
                                      A.CapitalNo CapitalNo, A.CapitalName,
                                      CL.CapitalInOut CapitalInOut, A.FiscalPeriod YearP, CC.CateNo CapitalCateNo
                               FROM act_gl_books A
                                        INNER JOIN expense_cate EC ON EC.ExpenseID= A.ExpenseID
                                        INNER JOIN capital_cate CC ON CC.CapitalID= A.CapitalID
                                        INNER JOIN capital CL ON CL.CapitalID= CC.CapitalID
                               WHERE CC.CateNo = '2021'
                                   AND A.AccountNo IN ('0441', '0442','0721','0722') AND  CL.CapitalInOut IN ('1','2') AND A.FiscalPeriod IN ('1','2','3')
                                $whereCondition
                               GROUP BY YearD, A.AccountNo, CapitalInOut) AS B
                            GROUP BY CapitalNo,ExpenseNo,AccountNo ,CapitalCateNo ) AS HH
                                SET TT.I8= HH.I8,  TT.I9= HH.I9, TT.I11= HH.I11, TT.I12= HH.I12, TT.I14= HH.I14, TT.I15= HH.I15, TT.I17= HH.I17, TT.I18= HH.I18,TT.I20= HH.I20, TT.I21= HH.I21
                                WHERE TT.ItemID = 'A#01#20211'  AND TT.CapitalCateNo= TT.CapitalCateNo ";
        DB::statement($sqlUpdate);
    }
    public function updateI1($whereCondition,$preYear){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT CapitalCateNo,CapitalName,
                                MAX(CASE WHEN (AccountNo = '0461' OR AccountNo = '0462') AND YearD = $preYear AND CapitalInOut = '1' THEN I END) AS I5,
                                MAX(CASE WHEN (AccountNo = '0461' OR AccountNo = '0462') AND YearD = $preYear AND CapitalInOut = '2' THEN I END) AS I6
                         FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
                               A.ExpenseNo ExpenseNo, A.ExpenseID ExpenseID, A.CapitalName, CC.CateNo CapitalCateNo, CL.CapitalInOut CapitalInOut
                               FROM act_gl_books A
                                   INNER JOIN expense_cate EC ON EC.ExpenseID= A.ExpenseID
                                   INNER JOIN capital_cate CC ON CC.CapitalID= A.CapitalID
                                   INNER JOIN capital CL ON CL.CapitalID= CC.CapitalID
                               WHERE CC.CateNo = '20211' AND CC.CateValue = '1'
                                 AND A.AccountNo IN ('0461', '0462') AND  CL.CapitalInOut IN ('1','2') $whereCondition
                               GROUP BY YearD, CapitalCateNo,CapitalInOut) AS B GROUP BY CapitalCateNo,ExpenseNo ) AS HH
                                SET TT.I5= HH.I5,  TT.I6= HH.I6
                                WHERE TT.ParentID = 'A#01'  AND TT.CapitalCateNo= TT.CapitalCateNo ";
        DB::statement($sqlUpdate);
    }
    public function updateI1NS($whereCondition,$year,$nextYear){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT CapitalCateNo,CapitalName,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '2' AND CapitalInOut = '1' THEN I END) AS I8,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '2' AND CapitalInOut = '2' THEN I END) AS I9,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '1' AND CapitalInOut = '1' THEN I END) AS I11,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '1' AND CapitalInOut = '2' THEN I END) AS I12,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '1' THEN D END) AS I14,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '2' THEN D END) AS I15,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '1' THEN I END) AS I17,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '2' THEN I END) AS I18,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $nextYear AND YearP = '2' AND CapitalInOut = '1' THEN I END) AS I20,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $nextYear AND YearP = '2' AND CapitalInOut = '2' THEN I END) AS I21
                         FROM (SELECT SUM(FCDebitAmount) AS I, SUM(FCCreditAmount) AS D, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
                                      A.ExpenseNo ExpenseNo,
                                      A.CapitalNo CapitalNo, A.CapitalName,
                                      CL.CapitalInOut CapitalInOut, A.FiscalPeriod YearP, CC.CateNo CapitalCateNo
                               FROM act_gl_books A
                                        INNER JOIN expense_cate EC ON EC.ExpenseID= A.ExpenseID
                                        INNER JOIN capital_cate CC ON CC.CapitalID= A.CapitalID
                                        INNER JOIN capital CL ON CL.CapitalID= CC.CapitalID
                               WHERE CC.CateNo = '20211' AND CC.CateValue = '1'
                                   AND A.AccountNo IN ('0441', '0442','0721','0722') AND  CL.CapitalInOut IN ('1','2') AND A.FiscalPeriod IN ('1','2','3')
                                $whereCondition
                               GROUP BY YearD, CapitalInOut) AS B
                            GROUP BY CapitalNo,ExpenseNo,AccountNo  ) AS HH
                                SET TT.I8= HH.I8,  TT.I9= HH.I9, TT.I11= HH.I11, TT.I12= HH.I12, TT.I14= HH.I14, TT.I15= HH.I15, TT.I17= HH.I17, TT.I18= HH.I18,TT.I20= HH.I20, TT.I21= HH.I21
                                WHERE TT.ParentID = 'A#01'  AND TT.CapitalCateNo= TT.CapitalCateNo ";
        DB::statement($sqlUpdate);
    }
    public function updateII($whereCondition,$preYear){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT CapitalCateNo,CapitalName,
                                MAX(CASE WHEN (AccountNo = '0461' OR AccountNo = '0462') AND YearD = $preYear AND CapitalInOut = '1' THEN I END) AS I5,
                                MAX(CASE WHEN (AccountNo = '0461' OR AccountNo = '0462') AND YearD = $preYear AND CapitalInOut = '2' THEN I END) AS I6
                         FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
                               A.ExpenseNo ExpenseNo, A.ExpenseID ExpenseID, A.CapitalName, CC.CateNo CapitalCateNo, CL.CapitalInOut CapitalInOut
                               FROM act_gl_books A
                                   INNER JOIN expense_cate EC ON EC.ExpenseID= A.ExpenseID
                                   INNER JOIN capital_cate CC ON CC.CapitalID= A.CapitalID
                                   INNER JOIN capital CL ON CL.CapitalID= CC.CapitalID
                               WHERE CC.CateNo = '2022' AND CC.CateValue = '1'
                                 AND A.AccountNo IN ('0461', '0462') AND  CL.CapitalInOut IN ('1','2') $whereCondition
                               GROUP BY YearD, CapitalCateNo,CapitalInOut) AS B
                                GROUP BY CapitalCateNo,ExpenseNo ) AS HH
                                SET TT.I5= HH.I5,  TT.I6= HH.I6
                                WHERE TT.ParentID = 'A#02'  AND TT.CapitalCateNo= TT.CapitalCateNo ";
        DB::statement($sqlUpdate);
    }
    public function updateIINS($whereCondition,$year,$nextYear){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT CapitalCateNo,CapitalName,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '2' AND CapitalInOut = '1' THEN I END) AS I8,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '2' AND CapitalInOut = '2' THEN I END) AS I9,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '1' AND CapitalInOut = '1' THEN I END) AS I11,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '1' AND CapitalInOut = '2' THEN I END) AS I12,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '1' THEN D END) AS I14,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2')  AND CapitalInOut = '2' THEN D END) AS I15,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2')  AND CapitalInOut = '1' THEN I END) AS I17,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2')  AND CapitalInOut = '2' THEN I END) AS I18,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $nextYear AND YearP = '2' AND CapitalInOut = '1' THEN I END) AS I20,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $nextYear AND YearP = '2' AND CapitalInOut = '2' THEN I END) AS I21
                         FROM (SELECT SUM(FCDebitAmount) AS I, SUM(FCCreditAmount) AS D,A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
                                      A.ExpenseNo ExpenseNo, A.CapitalNo CapitalNo, A.CapitalName,
                                      CL.CapitalInOut CapitalInOut, A.FiscalPeriod YearP,CC.CateNo CapitalCateNo
                               FROM act_gl_books A
                                   INNER JOIN expense_cate EC ON EC.ExpenseID= A.ExpenseID
                                   INNER JOIN capital_cate CC ON CC.CapitalID= A.CapitalID
                                   INNER JOIN capital CL ON CL.CapitalID= CC.CapitalID
                               WHERE CC.CateNo = '2022' AND CC.CateValue = '1'
                                 AND A.AccountNo IN ('0441', '0442','0721','0722') AND  CL.CapitalInOut IN ('1','2') AND A.FiscalPeriod IN ('1','2','3') $whereCondition
                               GROUP BY  YearD, CapitalInOut) AS B GROUP BY CapitalNo,ExpenseNo,AccountNo ) AS HH
                                SET TT.I8= HH.I8,  TT.I9= HH.I9, TT.I11= HH.I11, TT.I12= HH.I12, TT.I14= HH.I14, TT.I15= HH.I15, TT.I17= HH.I17, TT.I18= HH.I18,TT.I20= HH.I20, TT.I21= HH.I21
                                WHERE TT.ParentID = 'A#02'  AND TT.CapitalCateNo= TT.CapitalCateNo  ";
        DB::statement($sqlUpdate);
    }
    public function updateIII($whereCondition,$preYear){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT CapitalCateNo,CapitalName,
                                MAX(CASE WHEN (AccountNo = '0461' OR AccountNo = '0462') AND YearD = $preYear AND CapitalInOut = '1' THEN I END) AS I5,
                                MAX(CASE WHEN (AccountNo = '0461' OR AccountNo = '0462') AND YearD = $preYear AND CapitalInOut = '2' THEN I END) AS I6
                         FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
                                      A.ExpenseNo ExpenseNo, A.ExpenseID ExpenseID,
                                      A.CapitalNo CapitalNo, A.CapitalName ,
                                    CL.CapitalInOut CapitalInOut,CC.CateNo CapitalCateNo
                               FROM act_gl_books A
                                   INNER JOIN expense_cate EC ON EC.ExpenseID= A.ExpenseID
                                   INNER JOIN capital_cate CC ON CC.CapitalID= A.CapitalID
                                   INNER JOIN capital CL ON CL.CapitalID= CC.CapitalID
                               WHERE  CC.CateNo = '2029' AND CC.CateValue = '1'
                                 AND A.AccountNo IN ('0461', '0462') AND  CL.CapitalInOut IN ('1','2') $whereCondition
                               GROUP BY YearD, CapitalInOut) AS B GROUP BY CapitalNo,ExpenseNo ) AS HH
                                SET TT.I5= HH.I5,  TT.I6= HH.I6
                                WHERE TT.ParentID = 'A#03'  AND TT.CapitalCateNo= TT.CapitalCateNo ";
        DB::statement($sqlUpdate);
    }
    public function updateIIINS($whereCondition,$year,$nextYear){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT CapitalCateNo,CapitalName,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '2' AND CapitalInOut = '1' THEN I END) AS I8,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')  AND YearD = $year AND YearP = '2' AND CapitalInOut = '2' THEN I END) AS I9,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')   AND YearD = $year AND YearP = '1' AND CapitalInOut = '1' THEN I END) AS I11,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')   AND YearD = $year AND YearP = '1' AND CapitalInOut = '2' THEN I END) AS I12,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '1' THEN D END) AS I14,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '2' THEN D END) AS I15,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '1' THEN I END) AS I17,
                                MAX(CASE WHEN (AccountNo = '0721' OR AccountNo = '0722')  AND YearD = $year AND (YearP = '1' OR YearP = '2') AND CapitalInOut = '2' THEN I END) AS I18,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')   AND YearD = $nextYear AND YearP = '2' AND CapitalInOut = '1' THEN I END) AS I20,
                                MAX(CASE WHEN (AccountNo = '0441' OR AccountNo = '0442')   AND YearD = $nextYear AND YearP = '2' AND CapitalInOut = '2' THEN I END) AS I21
                         FROM (SELECT SUM(FCDebitAmount) AS I,SUM(FCCreditAmount) AS D, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
                                      A.ExpenseNo ExpenseNo, A.ExpenseID ExpenseID,
                                      A.CapitalNo CapitalNo, A.CapitalName,
                                      CL.CapitalInOut CapitalInOut, A.FiscalPeriod YearP,CC.CateNo CapitalCateNo
                               FROM act_gl_books A
                                   INNER JOIN expense_cate EC ON EC.ExpenseID= A.ExpenseID
                                   INNER JOIN capital_cate CC ON CC.CapitalID= A.CapitalID
                                   INNER JOIN capital CL ON CL.CapitalID= CC.CapitalID
                               WHERE CC.CateNo = '2029' AND CC.CateValue = '1'
                                 AND A.AccountNo IN ('0441', '0442','0721','0722') AND  CL.CapitalInOut IN ('1','2') AND A.FiscalPeriod IN ('1','2','3') $whereCondition
                               GROUP BY YearD, CapitalInOut) AS B GROUP BY CapitalNo,ExpenseNo,AccountNo ) AS HH
                                SET TT.I8= HH.I8,  TT.I9= HH.I9, TT.I11= HH.I11, TT.I12= HH.I12, TT.I14= HH.I14, TT.I15= HH.I15, TT.I17= HH.I17, TT.I18= HH.I18, TT.I20= HH.I20, TT.I21= HH.I21
                                WHERE TT.ParentID = 'A#03'  AND TT.CapitalCateNo= TT.CapitalCateNo ";
        DB::statement($sqlUpdate);
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1,SUM(M.I2) AS I2,SUM(M.I3) AS I3, SUM(M.I4) AS I4,SUM(M.I5) AS I5,SUM(M.I6) AS I6,
              SUM(M.I7) AS I7,SUM(M.I8) AS I8,SUM(M.I9) AS I9, SUM(M.I10) AS I10,SUM(M.I11) AS I11,SUM(M.I12) AS I12,SUM(M.I13) AS I13, SUM(M.I14) AS I14,
              SUM(M.I15) AS I15,SUM(M.I16) AS I16, SUM(M.I17) AS I17,SUM(M.I18) AS I18,SUM(M.I19) AS I19, SUM(M.I20) AS I20,SUM(M.I21) AS I21
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
                        $I5 = ($value['I5']) ? $value['I5'] : 0;
                        $I6 = ($value['I6']) ? $value['I6'] : 0;
                        $I7 = ($value['I7']) ? $value['I7'] : 0;
                        $I8 = ($value['I8']) ? $value['I8'] : 0;
                        $I9 = ($value['I9']) ? $value['I9'] : 0;
                        $I10 = ($value['I10']) ? $value['I10'] : 0;
                        $I11 = ($value['I11']) ? $value['I11'] : 0;
                        $I12 = ($value['I12']) ? $value['I12'] : 0;
                        $I13 = ($value['I13']) ? $value['I13'] : 0;
                        $I14 = ($value['I14']) ? $value['I14'] : 0;
                        $I15 = ($value['I15']) ? $value['I15'] : 0;
                        $I16 = ($value['I16']) ? $value['I16'] : 0;
                        $I17 = ($value['I17']) ? $value['I17'] : 0;
                        $I18 = ($value['I18']) ? $value['I18'] : 0;
                        $I19 = ($value['I19']) ? $value['I19'] : 0;
                        $I20 = ($value['I20']) ? $value['I20'] : 0;
                        $I21 = ($value['I21']) ? $value['I21'] : 0;

                        $update = "UPDATE table_tmp
                    SET I1 = I1 + ".$I1.",I2 = I2 + ".$I2.", I3 = I3 + ".$I3.",I4 = I4 + ".$I4.",I5 = I5 + ".$I5.",
                     I6 = I6 + ".$I6.",I7 = I7 + ".$I7.",I8 = I8 + ".$I8.", I9 = I9 + ".$I9.",I10 = I10 + ".$I10.",
                     I11 = I11 + ".$I11.",I12 = I12 + ".$I12.", I13 = I13 + ".$I13.",I14 = I14 + ".$I14.",I15 = I15 + ".$I15.",
                     I16 = I16 + ".$I16.",I17 = I17 + ".$I17.",I18 = I18 + ".$I18.", I19 = I19 + ".$I19.",I20 = I20 + ".$I20.",
                     I21 = I21 + ".$I21."
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
