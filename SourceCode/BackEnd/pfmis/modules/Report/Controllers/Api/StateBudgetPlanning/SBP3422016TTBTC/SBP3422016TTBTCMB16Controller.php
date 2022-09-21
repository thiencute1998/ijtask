<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB16Controller extends Controller {
    public function SBP3422016TTBTC_MB16(Request $request){
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
          `TranTypeID` int(2) DEFAULT 1,
          `Show` int(1) DEFAULT 1,
          `ItemNo` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT NULL,
          `AccountNo` varchar(15) DEFAULT '',
          `CoAccountNo` varchar(15) DEFAULT '',
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `UomName` varchar(100) DEFAULT '',
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          `I5` double(20,4) DEFAULT NULL,
          `I6` double(20,4) DEFAULT NULL,
          `I61` double(20,4) DEFAULT NULL,
          `I62` double(20,4) DEFAULT NULL,
          `I7` double(20,4) DEFAULT NULL,
          `I8` double(20,4) DEFAULT NULL,
          `I9` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $this->MB16InsertTemplate();
        $this->MB16InsertTemplateMH($whereCondition);
        $this->updateI23459($whereCondition,$year,$nextYear);
        $this->updateI7($whereCondition,$nextYear);
        $this->updateI61($whereCondition,$preYear);
        $this->updateI62($whereCondition,$preYear);
        // sum up
        $this->sumUpTableTmp(1, 3);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID')->get();

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Kế hoạch năm ' . $year ;
            $data[0]->HeaderName2 = 'Ước thực hiện năm ' . $year ;
            $data[0]->HeaderName3 = 'Tồn kho đến 31/12 năm ' . $year ;
            $data[0]->HeaderName4 = 'Kế hoạch năm ' .$nextYear ;
        }

        foreach ($data as $key => $value) {
//            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I7 = ($data[$key]->I7) ? $data[$key]->I7 : '';
            $data[$key]->I9 = ($data[$key]->I9) ? $data[$key]->I9 : '';
            $data[$key]->I6 = ((int)($data[$key]->I61)- (int)($data[$key]->I62))+(int)($data[$key]->I4);
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
            $data[$key]->I8 = (int)($data[$key]->I7) -(int)($data[$key]->I6);
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
//                "TransNo" => "00001",
                "ReportName" => "CƠ SỞ TÍNH CHI MUA BỔ SUNG HÀNG DỰ TRỮ QUỐC GIA NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => ($company) ? $company->CompanyName : 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    // Level 1
    public function MB16InsertTemplate(){
        $arrInsert = [
            [
                'STT' => '',
                'Show' => 1,
                'ItemID' => 'A#',
                'ParentID' => '',
                'ItemName' => 'Tổng số',
                'Level' => '1',
                'FontWeight' => '1',
                'Italic' => '',
                'Center' => '1',
                'ItemNo'=>'',
                'AccountNo'=>'',
                'CoAccountNo'=>'',
                'UomName'=>'',
                'I2'=>'',
                'I3'=>'',
                'I4'=>'',
                'I5'=>'',
                'I6'=>'',
                'I61'=>'',
                'I62'=>'',
                'I7'=>'',
                'I8'=>'',
                'I9'=>''
            ],

        ];
        DB::table('table_tmp')->insert($arrInsert);
    }
    public function MB16InsertTemplateMH($whereCondition)
    {
        $sql = "SELECT A.PeriodToDate, A.PeriodFromDate, A.TransTypeID, A.FiscalYear, A.AccountNo AccountNo, A.CoAccountNo CoAccountNo,A.ItemID ItemID, A.ItemNo ItemNo,ICL.UomName UomName, A.ItemName ItemName,
                   IC.CateNo ItemCateNo, ICL.ItemID ItemID
                    FROM act_gl_books A
                        INNER JOIN item_cate IC ON IC.ItemID = A.ItemID
                        INNER JOIN item ICL ON ICL.ItemID = IC.ItemID
                        WHERE IC.CateNo = '001' $whereCondition  GROUP BY ItemCateNo, ItemNo,ItemName";

        $result = DB::select($sql);
        $result = (array)$result;
        $arrInsert1 = [];
        $i = 1;
        if ($result) {
            foreach ($result as $template) {
                $STT = $i++;
                $ItemID = 'A#' . $template->ItemID ;
                $ParentID = 'A#';
                $arrInsert1[] = [
                    'STT' => $STT,
                    'Show'=>'',
                    'ItemID' => $ItemID,
                    'ParentID' => $ParentID,
                    'ItemName' => $template->ItemName,
                    'Level'=>'2',
                    'FontWeight'=> '',
                    'Italic' =>'',
                     'Center'=>'',
                    'ItemNo' => $template->ItemNo,
                    'AccountNo' => $template->AccountNo,
                    'CoAccountNo' => $template->CoAccountNo,
                    'UomName'=>$template->UomName,
                    'I2'=>'',
                    'I3'=>'',
                    'I4'=>'',
                    'I5'=>'',
                    'I6'=>'',
                    'I61'=>'',
                    'I62'=>'',
                    'I7'=>'',
                    'I8'=>'',
                    'I9'=>''
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
        public function updateI23459($whereCondition,$year,$nextYear){
        $sqlUpdate="UPDATE table_tmp AS TT, (SELECT TranTypeID,ItemName,ItemNo,
                                MAX(CASE WHEN AccountNo = '0441' AND YearD = 2021 AND TranTypeID = '6' THEN Q END) AS I2,
                                MAX(CASE WHEN AccountNo = '0441' AND YearD = 2021 AND TranTypeID = '6' THEN I END) AS I3,
                                MAX(CASE WHEN AccountNo = '0721' AND YearD = 2021 AND TranTypeID = '8' THEN Q END) AS I4,
                                MAX(CASE WHEN AccountNo = '0721' AND YearD = 2021 AND TranTypeID = '8' THEN I END) AS I5,
                                MAX(CASE WHEN AccountNo = '0411' AND YearD = 2022 AND TranTypeID = '2' THEN I END) AS I9
                         FROM (SELECT SUM(FCDebitAmount) AS I,SUM(Quantity) AS Q, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
                               A.TransTypeID as TranTypeID, A.ItemName as ItemName, A.ItemNo ItemNo
                               FROM act_gl_books AS A
                                        INNER JOIN item_cate AS IC ON IC.ItemID = A.ItemID
                               WHERE IC.CateNo = '001'
                                 AND A.AccountNo IN ('0441', '0721', '0411') AND A.TransTypeID IN ('2','6','8')
                                 $whereCondition
                        GROUP BY YearD,A.TransTypeID, ItemNo,A.AccountNo) AS B GROUP BY ItemName,ItemNo ) AS HH
                    SET TT.I3= HH.I3,  TT.I5= HH.I5,  TT.I9= HH.I9,TT.I2= HH.I2,  TT.I4= HH.I4
                    WHERE TT.ParentID = 'A#'  AND  TT.ItemNo = HH.ItemNo ";
            DB::statement($sqlUpdate);
        }
        public function updateI7($whereCondition,$nextYear){
            $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT TranTypeID,AccountNo,ItemName,ItemCateNo,ItemNo,
                                MAX(CASE WHEN AccountNo = '094' AND YearD= $nextYear THEN I END) AS I7
                         FROM (SELECT SUM(Quantity) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
                                      A.TransTypeID as TranTypeID, A.ItemName as ItemName, A.ItemNo ItemNo,A.UomName UomName,
                                      IC.CateNo ItemCateNo
                               FROM act_gl_books AS A
                                        INNER JOIN item_cate AS IC ON IC.ItemID = A.ItemID
                               WHERE  A.AccountNo = '094'
                                    $whereCondition
                               GROUP BY YearD, A.AccountNo,A.TransTypeID,ItemCateNo, ItemNo) AS B GROUP BY ItemName,ItemNo) AS HH
                              SET  TT.I7 = HH.I7
                                WHERE TT.ParentID = 'A#' AND   TT.ItemNo = HH.ItemNo";
            DB::statement($sqlUpdate);
        }
         public function updateI61($whereCondition,$preYear){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT ItemName,ItemNo,
                                MAX(CASE WHEN (AccountNo = '15721' OR  AccountNo = '15722')AND YearD = $preYear THEN I END) AS I61
                         FROM (SELECT SUM(Quantity) AS I, A.AccountNo as AccountNo , YEAR(A.PeriodFromDate) AS YearD,
                                      A.ItemName as ItemName, A.ItemNo ItemNo,
                                      IC.CateNo ItemCateNo
                               FROM act_gl_books AS A
                                        INNER JOIN item_cate AS IC ON IC.ItemID = A.ItemID
                               WHERE  A.AccountNo IN ('15721','15722')
                                   $whereCondition
                               GROUP BY YearD, ItemNo,A.AccountNo) AS B GROUP BY ItemName,ItemNo) AS HH
                              SET TT.I61=HH.I61
                                WHERE TT.ParentID = 'A#' AND   TT.ItemNo = HH.ItemNo";
        DB::statement($sqlUpdate);
    }
         public function updateI62($whereCondition,$preYear){
                      $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT ItemName,ItemNo,
                         MAX(CASE WHEN (CoAccountNo = '15721' OR  CoAccountNo = '15722') AND YearD = $preYear THEN I END) AS I62
                            FROM (SELECT SUM(Quantity) AS I, A.CoAccountNo as CoAccountNo , YEAR(A.PeriodFromDate) AS YearD,
                                     A.ItemName as ItemName, A.ItemNo ItemNo,
                                     IC.CateNo ItemCateNo
                             FROM act_gl_books AS A
                            INNER JOIN item_cate AS IC ON IC.ItemID = A.ItemID
                                 WHERE  A.CoAccountNo IN ('15721','15722')
                                             $whereCondition
                                  GROUP BY YearD, ItemNo,A.AccountNo) AS B GROUP BY ItemName,ItemNo) AS HH
                              SET TT.I62=HH.I62
                                WHERE TT.ParentID = 'A#' AND TT.ItemNo = HH.ItemNo";
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
                $sql = "SELECT ItemID, ParentID, SUM(M.I3) AS I3,SUM(M.I5) AS I5,SUM(M.I9) AS I9
                FROM table_tmp M
                WHERE M.Level = " . $i . " GROUP BY ParentID";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
                        $I3 = ($value['I3']) ? $value['I3'] : 0;
                        $I5 = ($value['I5']) ? $value['I5'] : 0;
                        $I9 = ($value['I9']) ? $value['I9'] : 0;
                        $update = "UPDATE table_tmp
                    SET I3 = I3 + ".$I3.",I5 = I5 + ".$I5.", I9 = I9 + ".$I9."
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
