<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB49Controller extends Controller {
    use Common;

    public function SBP3422016TTBTC_MB49(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : date("Y");
        $fromDate = (isset($filter['Year'])) ? $filter['Year'].'-01-01' : $year.'-01-01';
        $toDate = (isset($filter['Year'])) ? $filter['Year'].'-12-31' : $year.'-12-31';
        $lastYear = date('Y-m-d', strtotime('-1 year', strtotime($toDate)) );
        $nextYear = date('Y-m-d', strtotime('+1 year', strtotime($toDate)) );
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

        $whereCondition = "AND YEAR(A.PeriodFromDate) = $year ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
            `STT` varchar(10) DEFAULT '',
            `ItemID` varchar(255) NOT NULL PRIMARY KEY,
            `ParentID` varchar(100) DEFAULT '',
            `Level` int(2) DEFAULT 1,
            `ItemName` varchar(255) DEFAULT NULL,
            `FontWeight` tinyint(1) DEFAULT 0,
            `Italic` tinyint(1) DEFAULT 0,
            `Center` tinyint(1) DEFAULT 0,
            `I1` double(20,4) DEFAULT NULL,
            `Company` varchar(50) DEFAULT NULL,
            `T1Temporary` varchar(50) DEFAULT NULL,
            `T2Temporary` varchar(50) DEFAULT NULL,
            `T3Temporary` varchar(50) DEFAULT NULL,
            `Revenue` varchar(50) DEFAULT NULL,
            `SbiCategoryCateValue` varchar(50) DEFAULT NULL,
            `NormCateNo` varchar(50) DEFAULT NULL,
            `NormCateValue` varchar(20) DEFAULT NULL,
            `Detail` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $this->createTemporaryT1();
        $this->createTemporaryT2();
        $this->createTemporaryT3();
        //calculator here

        $countRecords = DB::table('table_tmp')->count();
        $this->insertRI_3($whereCondition);
        if(DB::table('table_tmp')->count() > $countRecords){
            $this->getParentDataTreeCommonDetail(['company', 'T1Temporary', 'T2Temporary', 'T3Temporary', 'revenue']);
            $countRecords = DB::table('table_tmp')->count();
        }
        // tree

        $this->insertRI_2($whereCondition);
        if(DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeCommonDetail(['company', 'T1Temporary', 'T2Temporary', 'revenue', ['sbi_category_cate_value', '005'], '']);
            $countRecords = DB::table('table_tmp')->count();
        }
            // tree

        $this->insertRII_1_3($whereCondition);
        $this->insertRII_2($whereCondition);
        if(DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeCommonDetail(['company', 'T1Temporary', ['sbi_category_cate_value', '005'], '']);
        }

        $this->setLevel();

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumUpTableTmp($maxLevel, 1);


        // insert RI_1

        $this->insertI_1();

        $this->sumUpTableTmp($maxLevel, 1);

        // SET STT
        $this->setIndexMB49($maxLevel, 2);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID','ASC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
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
                "ReportName" => "DỰ TOÁN THU, CHI NGÂN SÁCH NHÀ NƯỚC NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function createTemporaryT1(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `T1Temporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $insertA = [
            ['ItemID'=> '01','ItemName'=> 'Tổng số thu, chi, nộp ngân sách phí, lệ phí'],
            ['ItemID'=> '02','ItemName'=> 'Dự toán chi ngân sách nhà nước'],
        ];

        DB::table('T1Temporary')->insert($insertA);
    }

    public function createTemporaryT2(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `T2Temporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $insertA = [
            ['ItemID'=> '01','ItemName'=> 'Số thu phí, lệ phí'],
            ['ItemID'=> '02','ItemName'=> 'Chi từ nguồn thu phí được để lại'],
            ['ItemID'=> '03','ItemName'=> 'Số phí, lệ phí nộp NSNN'],
        ];

        DB::table('T2Temporary')->insert($insertA);
    }

    public function createTemporaryT3(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `T3Temporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $insertA = [
            ['ItemID'=> '01','ItemName'=> 'Lệ phí'],
            ['ItemID'=> '02','ItemName'=> 'Phí']
        ];

        DB::table('T3Temporary')->insert($insertA);
    }




    public function setCaseRevenueI_3(){
        return "CASE WHEN NC.CateNo = '2011111' AND NC.CateValue = '1' THEN '01'
                     WHEN NC.CateNo = '2011112' AND NC.CateValue = '2' THEN '02' END";
    }

    public function insertRI_3($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Company, T1Temporary, T2Temporary, T3Temporary, Revenue, Detail, I1)
            SELECT CONCAT(ParentID, '#', RevenueNo) as ItemID, ParentID, ItemName, CompanyNo, '01' as T1Temporary, '02' as T2Temporary, T3Temporary, RevenueNo, 1 as Detail,
            SUM(I) as I1 FROM
            (SELECT CONCAT('A#', C.CompanyNo, '#01#03#', " . $this->setCaseRevenueI_3() . ") as ParentID, A.RevenueName as ItemName,
            A.RevenueNo, C.CompanyNo, " . $this->setCaseRevenueI_3() . " as T3Temporary,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN norm_cate as NC on A.NormID = NC.NormID
            INNER JOIN company as C ON A.InputCompanyID = C.CompanyID
            WHERE A.AccountNo = '0311'
            AND ((NC.CateNo = '2011111' AND NC.CateValue = '1') OR (NC.CateNo = '2011112' AND NC.CateValue = '2'))
            $whereCondition
            GROUP BY A.RevenueNo, C.CompanyNo, NC.CateNo, NC.CateValue) as CH GROUP BY RevenueNo, CompanyNo, T3Temporary
            ";

        DB::statement($sql);
    }

    public function setCaseRevenueNoI_2(){
        return "CASE WHEN NC.CateNo = '201511211' THEN '01'
                     WHEN NC.CateNo = '201511212' THEN '02' END";
    }

    public function setCaseRevenueNameI_2(){
        return "CASE WHEN SCC.CateValue = '1' AND NC.CateNo = '201511211' THEN 'Kinh phí thực hiện chế độ tự chủ'
                     WHEN SCC.CateValue = '1' AND NC.CateNo = '201511212' THEN 'Kinh phí không thực hiện chế độ tự chủ'
                     WHEN SCC.CateValue != '1' AND NC.CateNo = '201511211' THEN 'Kinh phí nhiệm vụ thường xuyên'
                     WHEN SCC.CateValue != '1' AND NC.CateNo = '201511212' THEN 'Kinh phí nhiệm vụ không thường xuyên' END";
    }


    public function insertRI_2($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Company, T1Temporary, T2Temporary, Revenue, SbiCategoryCateValue, Detail, I1)
            (SELECT CONCAT('A#', C.CompanyNo, '#01#02#', A.RevenueNo, '#', SCC.CateValue, '#', " . $this->setCaseRevenueNoI_2() . ") as ItemID,
             CONCAT('A#', C.CompanyNo, '#01#02#', A.RevenueNo, '#', SCC.CateValue) as ParentID,
             " . $this->setCaseRevenueNameI_2() . " as ItemName, C.CompanyNo, '01' as T1Temporary, '02' as T2Temporary,
            A.RevenueNo, SCC.CateValue, 1 as Detail,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN norm_cate as NC on A.NormID = NC.NormID
            INNER JOIN company as C ON A.InputCompanyID = C.CompanyID
            INNER JOIN sbi_category_cate as SCC on A.SbiCategoryID = SCC.SbiCategoryID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            WHERE A.AccountNo = '0412'
            AND A.RevenueID IS NOT NULl
            AND CC.CateNo = '200211'
            AND NC.CateNo IN ('201511211', '201511212')
            $whereCondition
            GROUP BY A.RevenueNo, C.CompanyNo, NC.CateNo, SCC.CateValue)
            ";

        DB::statement($sql);
    }

    public function setCaseNameII_1_3(){
        return " CASE WHEN SCC.CateValue = '1' AND NC.CateValue = '1' THEN 'Kinh phí thực hiện chế độ tự chủ'
                        WHEN SCC.CateValue = '1' AND NC.CateValue = '2' THEN 'Kinh phí không thực hiện chế độ tự chủ'
                        WHEN SCC.CateValue != '1' AND NC.CateValue = '1' THEN 'Kinh phí nhiệm vụ thường xuyên'
                        WHEN SCC.CateValue != '1' AND NC.CateValue = '2' THEN 'Kinh phí nhiệm vụ không thường xuyên' END
        ";
    }

    public function insertRII_1_3($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Company, T1Temporary, SbiCategoryCateValue, Detail, I1)
            (SELECT CONCAT('A#', C.CompanyNo, '#02#', SCC.CateValue, '#', NC.CateValue) as ItemID,
             CONCAT('A#', C.CompanyNo, '#02#', SCC.CateValue) as ParentID,
             " . $this->setCaseNameII_1_3() . " as ItemName, C.CompanyNo, '02' as T1Temporary, SCC.CateValue,
            1 as Detail,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN norm_cate as NC on A.NormID = NC.NormID
            INNER JOIN company as C ON A.InputCompanyID = C.CompanyID
            INNER JOIN sbi_category_cate as SCC on A.SbiCategoryID = SCC.SbiCategoryID
            WHERE A.AccountNo = '0411'
            AND SCC.CateValue != '2'
            AND NC.CateNo = '20151111111' AND NC.CateValue IN ('1', '2')
            $whereCondition
            GROUP BY C.CompanyNo, NC.CateValue, SCC.CateValue)
            ";

        DB::statement($sql);
    }

    public function setCaseNameII_2(){
        return " CASE WHEN NC.CateValue = '1' THEN 'Kinh phí thực hiện nhiệm vụ khoa học công nghệ'
                      WHEN NC.CateValue = '2' THEN 'Kinh phí nhiệm vụ thường xuyên theo chức năng'
                      WHEN NC.CateValue = '3' THEN 'Kinh phí nhiệm vụ không thường xuyên' END
        ";
    }

    public function insertRII_2($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Company, T1Temporary, SbiCategoryCateValue, Detail, I1)
            (SELECT CONCAT('A#', C.CompanyNo, '#02#', SCC.CateValue, '#', NC.CateValue) as ItemID,
             CONCAT('A#', C.CompanyNo, '#02#', SCC.CateValue) as ParentID,
             " . $this->setCaseNameII_2() . " as ItemName, C.CompanyNo, '02' as T1Temporary, SCC.CateValue,
            1 as Detail,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN norm_cate as NC on A.NormID = NC.NormID
            INNER JOIN company as C ON A.InputCompanyID = C.CompanyID
            INNER JOIN sbi_category_cate as SCC on A.SbiCategoryID = SCC.SbiCategoryID
            WHERE A.AccountNo = '0411'
            AND SCC.CateValue = '2'
            AND NC.CateNo = '20151111112' AND NC.CateValue IN ('1', '2', '3')
            $whereCondition
            GROUP BY C.CompanyNo, NC.CateValue)
            ";

        DB::statement($sql);
    }

    public function insertI_1(){
        $insertArr = [];
        $sqlCheckCompany = "SELECT * FROM table_tmp
        WHERE ((T3Temporary = '01' AND Detail = 1) OR (T2Temporary = '02' AND Detail = 0 AND Level = 4 AND Revenue IS NOT NULL) OR (T3Temporary = '02' AND Detail = 1))
        GROUP BY Company";

        $listCheckCompany = DB::select($sqlCheckCompany);
        if($listCheckCompany){
            foreach($listCheckCompany as $item){
                $insertArr[] = [
                    'ItemID'=> 'A#' . $item->Company . '#01#01#01',
                    'ParentID'=> 'A#' . $item->Company . '#01#01',
                    'ItemName'=> 'Lệ phí',
                    'Level'=> 4,
                    'I1'=> 0
                ];
                $insertArr[] = [
                    'ItemID'=> 'A#' . $item->Company . '#01#01#02',
                    'ParentID'=> 'A#' . $item->Company . '#01#01',
                    'ItemName'=> 'Phí',
                    'Level'=> 4,
                    'I1'=> 0
                ];
            }
        }

        $sql1 = "SELECT ItemName, Company, Revenue, I1 FROM table_tmp
        WHERE T3Temporary = '01' AND Detail = 1
        GROUP BY Company, Revenue";
        $listRevenue1 = DB::select($sql1);

        $sql2 = "SELECT ItemName, Company, Revenue, SUM(I1) as I FROM table_tmp
        WHERE ((T2Temporary = '02' AND Detail = 0 AND Level = 4 AND Revenue IS NOT NULL) OR (T3Temporary = '02' AND Detail = 1))
        GROUP BY Company, Revenue";

        $listRevenue2 = DB::select($sql2);

        if($listRevenue1){
            foreach ($listRevenue1 as $item){
                $insertArr[] = [
                    'ItemID'=> "A#" . $item->Company . '#01#01#01#' .$item->Revenue,
                    'ParentID'=> "A#" . $item->Company . "#01#01#01",
                    'ItemName'=> $item->ItemName,
                    'Level'=> 5,
                    'I1'=> $item->I1
                ];
            }
        }

        if($listRevenue2){
            foreach ($listRevenue2 as $item){
                $insertArr[] = [
                    'ItemID'=> "A#" . $item->Company . '#01#01#02#' .$item->Revenue,
                    'ParentID'=> "A#" . $item->Company . "#01#01#02",
                    'ItemName'=> $item->ItemName,
                    'Level'=> 5,
                    'I1'=> $item->I
                ];
            }
        }

        DB::table('table_tmp')->insert($insertArr);

    }



    // sum row
    public function sumUpTableTmp($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1
            FROM table_tmp M
            WHERE M.Level = " . $i . " GROUP BY ParentID";
            $result = DB::select($sql);
            $result = (array)$result;
            if ($result) {
                foreach ($result as $value) {
                    $value = (array)$value;
                    $I1 = ($value['I1']) ? $value['I1'] : 0;
                    $update = "UPDATE table_tmp
                SET I1 = " . $I1 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }

    }

    public function setIndexMB49($maxLevel, $start = 0){
        $insertArr = [];
        for ($i = $start ; $i<= $maxLevel; $i++){
            $insertArr1 = [];
            if($i == 2){
                $sql = " SELECT * FROM table_tmp
                        WHERE Level = 1 AND Company IS NOT NULL
                        GROUP BY Company ORDER BY ItemID ASC";
                $result = DB::select($sql);
                if($result){
                    foreach($result as $key=> $item){
                        $insertArr1[] = [
                            'ItemID' => $item->ItemID . '#01',
                            'ParentID' => $item->ItemID,
                            'Level' => $i,
                            'STT' => $this->numberToRomanRepresentation(1)
                        ];
                        $insertArr1[] = [
                            'ItemID' => $item->ItemID . '#02',
                            'ParentID' => $item->ItemID,
                            'Level' => $i,
                            'STT' => $this->numberToRomanRepresentation(2)
                        ];
                        DB::table('table_tmp')->where('ItemID', '=', $item->ItemID . '#01')->update(['STT' => $this->numberToRomanRepresentation(1)]);
                        DB::table('table_tmp')->where('ItemID', '=', $item->ItemID . '#02')->update(['STT' => $this->numberToRomanRepresentation(2)]);

                    }
                }
            }
            else if($i == 3){
                $sql = " SELECT * FROM table_tmp
                        WHERE Level = 3 AND Company IS NOT NULL
                        AND T1Temporary IS NOT NULL
                        GROUP BY ItemID ORDER BY ItemID ASC";
                $result = DB::select($sql);
                foreach($insertArr[$i-3] as $value){
                    $index = 1;
                    foreach ($result as $item){
                        if ($value['ItemID'] == $item->ParentID) {
                            $insertArr1[] = [
                                'ItemID' => $item->ItemID,
                                'ParentID' => $item->ParentID,
                                'Level' => $item->Level,
                                'STT' => $index,
                            ];
                            DB::table('table_tmp')->where('ItemID', '=', $item->ItemID)->update(['STT' => $index]);
                            $index++;
                        }
                    }
                }
            }
            else{
                $sql = "SELECT * FROM table_tmp WHERE Level = $i GROUP BY ItemID ORDER BY ItemID ASC";
                $result = DB::select($sql);
                foreach($insertArr[$i-3] as $value){
                    $index = 1;
                    foreach ($result as $item){
                        if ($value['ItemID'] == $item->ParentID) {
                            $insertArr1[] = [
                                'ItemID' => $item->ItemID,
                                'ParentID' => $item->ParentID,
                                'Level' => $item->Level,
                                'STT' => $value['STT'] . '.' .$index,
                            ];
                            DB::table('table_tmp')->where('ItemID', '=', $item->ItemID)->update(['STT' => $value['STT'] . '.' .$index]);
                            $index++;
                        }
                    }
                }
            }
            $insertArr[] = $insertArr1;
        }
    }



}
