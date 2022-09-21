<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB36Controller extends Controller {
    use Common;

    public function SBP3422016TTBTC_MB36(Request $request){
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
            `SbiCategoryCateValue` varchar(50) DEFAULT NULL,
            `NormCateNo` varchar(50) DEFAULT NULL,
            `NormCateValue` varchar(20) DEFAULT NULL,
            `ProjectCateNo` varchar(50) DEFAULT NULL,
            `Detail` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        //calculator here

        $this->insertR1C1($whereCondition);

        $this->setDataMB36();

        $this->updateRowI($whereCondition);
        $this->updateRowII_1($whereCondition);
        $this->updateRowII_2($whereCondition);
        $this->updateRowII_3($whereCondition);

        $this->setLevel();

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumUpTableTmp($maxLevel, 1);

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

    public function setCaseRevenue(){
        return "CASE WHEN A.AccountNo = '0343' AND RC.CateValue = '2' AND NC.CateValue = '1' THEN '01#01'
                    WHEN A.AccountNo = '0343' AND RC.CateValue = '1' AND NC.CateValue IN ('1', '2') THEN '01#02'
                    WHEN A.AccountNo = '0341' AND RC.CateValue = '2' AND NC.CateValue = '1' THEN '03#01'
                     WHEN A.AccountNo = '0341' AND RC.CateValue = '1' AND NC.CateValue = '2' THEN '03#02' END";
    }

    public function insertR1C1($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Detail, I1)
            SELECT CONCAT(ParentID, '#', RevenueNo) as ItemID, ParentID, ItemName, 1 as Detail, SUM(I) as I1 FROM
            (SELECT CONCAT('A#01#', " . $this->setCaseRevenue() . ") as ParentID, A.RevenueName as ItemName,
            A.RevenueNo, " . $this->setCaseRevenue() . " as CaseRevenue,
            SUM(LCDebitAmount) as I
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
            INNER JOIN norm_cate as NC on A.NormID = NC.NormID
            WHERE A.AccountNo IN ('0343','0341')
            AND RC.CateNo = '2011104' AND RC.CateValue IN ('1', '2')
            AND NC.CateNo = '2011111' AND NC.CateValue IN ('1', '2')
            $whereCondition
            GROUP BY A.AccountNo, A.RevenueNo, RC.CateValue, NC.CateValue) as CH GROUP BY RevenueNo, CaseRevenue
            ";

        DB::statement($sql);
    }

    public function setDataRaw(){
        $insert = [
            ['STT'=> 'I', 'ItemID'=> 'A#01', 'ParentID'=> '', 'ItemName'=> 'Tổng số thu, chi, nộp ngân sách phí, lệ phí', 'Level'=> 1, 'FontWeight'=> 1],
            ['STT'=> '1', 'ItemID'=> 'A#01#01', 'ParentID'=> 'A#01', 'ItemName'=> 'Số thu phí, lệ phí', 'Level'=> 2, 'FontWeight'=> 1],
            ['STT'=> '1.1', 'ItemID'=> 'A#01#01#01', 'ParentID'=> 'A#01#01', 'ItemName'=> 'Lệ phí', 'Level'=> 3, 'FontWeight'=> 0],
            ['STT'=> '1.2', 'ItemID'=> 'A#01#01#02', 'ParentID'=> 'A#01#01', 'ItemName'=> 'Phí', 'Level'=> 3, 'FontWeight'=> 0],
            ['STT'=> '2', 'ItemID'=> 'A#01#02', 'ParentID'=> 'A#01', 'ItemName'=> 'Chi từ nguồn thu phí được để lại', 'Level'=> 2, 'FontWeight'=> 1],
            ['STT'=> '3', 'ItemID'=> 'A#01#03', 'ParentID'=> 'A#01', 'ItemName'=> 'Số phí, lệ phí nộp NSNN', 'Level'=> 2, 'FontWeight'=> 1],
            ['STT'=> '3.1', 'ItemID'=> 'A#01#03#01', 'ParentID'=> 'A#01#03', 'ItemName'=> 'Lệ phí', 'Level'=> 3, 'FontWeight'=> 0],
            ['STT'=> '3.2', 'ItemID'=> 'A#01#03#02', 'ParentID'=> 'A#01#03', 'ItemName'=> 'Phí', 'Level'=> 3, 'FontWeight'=> 0],
            ['STT'=> 'II', 'ItemID'=> 'A#02', 'ParentID'=> '', 'ItemName'=> 'Dự toán chi ngân sách nhà nước', 'Level'=> 1, 'FontWeight'=> 1],
        ];

        DB::table('table_tmp')->insert($insert);
    }

    public function setDataMB36(){
        // Data Raw
        $this->setDataRaw();

        $listCategoryCate = DB::table('sbi_category_cate_value')
//            ->where('Inactive','!=' , 1)
            ->where('CateNo','005')
            ->get();

        if($listCategoryCate){
            $insertI = [];
            $insertII = [];
            foreach($listCategoryCate as $item){
                if($item->CateValue == '1'){
                    $itemNameI1 = 'Kinh phí thực hiện chế độ tự chủ';
                    $itemNameI2 = 'Kinh phí không thực hiện chế độ tự chủ';

                    $insertII[] = ['STT'=> $item->CateValue . '.1', 'ItemID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#01', 'ParentID'=> 'A#02#' . sprintf("%02d",($item->CateValue)), 'ItemName'=> 'Kinh phí thực hiện chế độ tự chủ', 'Level'=> 4, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> '201511201', 'NormCateValue'=> null, 'ProjectCateNo'=> null];
                    $insertII[] = ['STT'=> $item->CateValue . '.2', 'ItemID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#02', 'ParentID'=> 'A#02#' . sprintf("%02d",($item->CateValue)), 'ItemName'=> 'Kinh phí không thực hiện chế độ tự chủ', 'Level'=> 4, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> '201511202', 'NormCateValue'=> null, 'ProjectCateNo'=> null];
                }
                else{
                    $itemNameI1 = 'Kinh phí nhiệm vụ thường xuyên';
                    $itemNameI2 = 'Kinh phí nhiệm vụ không thường xuyên';
                    if($item->CateValue == '2'){
                        //
                        $insertII[] = ['STT'=> $item->CateValue . '.1', 'ItemID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#01', 'ParentID'=> 'A#02#' . sprintf("%02d",($item->CateValue)), 'ItemName'=> 'Kinh phí thực hiện nhiệm vụ khoa học công nghệ', 'Level'=> 4, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> '20151111112', 'NormCateValue'=> '1', 'ProjectCateNo'=> null];
                        $insertII[] = ['STT'=> '', 'ItemID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#01#01', 'ParentID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#01', 'ItemName'=> '- Nhiệm vụ khoa học công nghệ cấp quốc gia', 'Level'=> 5, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> '20151111112', 'NormCateValue'=> '1', 'ProjectCateNo'=> '00701'];
                        $insertII[] = ['STT'=> '', 'ItemID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#01#02', 'ParentID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#01', 'ItemName'=> '- Nhiệm vụ khoa học công nghệ cấp Bộ', 'Level'=> 5, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> '20151111112', 'NormCateValue'=> '1', 'ProjectCateNo'=> '00702'];
                        $insertII[] = ['STT'=> '', 'ItemID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#01#03', 'ParentID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#01', 'ItemName'=> '- Nhiệm vụ khoa học công nghệ cấp cơ sở', 'Level'=> 5, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> '20151111112', 'NormCateValue'=> '1', 'ProjectCateNo'=> '00703'];
                        $insertII[] = ['STT'=> $item->CateValue . '.2', 'ItemID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#02', 'ParentID'=> 'A#02' . sprintf("%02d",($item->CateValue)), 'ItemName'=> 'Kinh phí nhiệm vụ thường xuyên theo chức năng', 'Level'=> 4, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> '20151111112', 'NormCateValue'=> '2', 'ProjectCateNo'=> '00704'];
                        $insertII[] = ['STT'=> $item->CateValue . '.3', 'ItemID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#03', 'ParentID'=> 'A#02' . sprintf("%02d",($item->CateValue)), 'ItemName'=> 'Kinh phí nhiệm vụ không thường xuyên', 'Level'=> 4, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> '20151111112', 'NormCateValue'=> '3', 'ProjectCateNo'=> '00705'];

                    }
                    else{
                        $insertII[] = ['STT'=> $item->CateValue . '.1', 'ItemID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#01', 'ParentID'=> 'A#02#' . sprintf("%02d",($item->CateValue)), 'ItemName'=> 'Kinh phí nhiệm vụ thường xuyên', 'Level'=> 4, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> '20151111111', 'NormCateValue'=> '1', 'ProjectCateNo'=> null];
                        $insertII[] = ['STT'=> $item->CateValue . '.2', 'ItemID'=> 'A#02#' . sprintf("%02d",($item->CateValue)) . '#02', 'ParentID'=> 'A#02#' . sprintf("%02d",($item->CateValue)), 'ItemName'=> 'Kinh phí nhiệm vụ không thường xuyên', 'Level'=> 4, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> '20151111111', 'NormCateValue'=> '2', 'ProjectCateNo'=> null];
                    }
                }
                $insertI[] = ['STT'=> '2.' . $item->CateValue, 'ItemID'=> 'A#01#02#' . sprintf("%02d",($item->CateValue)), 'ParentID'=> 'A#01#02', 'ItemName'=> $item->Description, 'Level'=> 3, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> null];
                $insertI[] = ['STT'=> 'a', 'ItemID'=> 'A#01#02#' . sprintf("%02d",($item->CateValue)) . '#01', 'ParentID'=> 'A#01#02' . sprintf("%02d",($item->CateValue)), 'ItemName'=> $itemNameI1, 'Level'=> 4, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> '201511201'];
                $insertI[] = ['STT'=> 'b', 'ItemID'=> 'A#01#02#' . sprintf("%02d",($item->CateValue)) . '#02', 'ParentID'=> 'A#01#02' . sprintf("%02d",($item->CateValue)), 'ItemName'=> $itemNameI2, 'Level'=> 4, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> '201511202'];

                $insertII[] = ['STT'=> $item->CateValue, 'ItemID'=> 'A#02#' . sprintf("%02d",($item->CateValue)), 'ParentID'=> 'A#02', 'ItemName'=> $item->Description, 'Level'=> 2, 'FontWeight'=> 1, 'SbiCategoryCateValue'=> $item->CateValue, 'NormCateNo'=> null, 'NormCateValue'=> null, 'ProjectCateNo'=> null];
            }
            DB::table('table_tmp')->insert($insertI);
            DB::table('table_tmp')->insert($insertII);
        }
    }

    public function updateRowI($whereCondition){
        $sql = " UPDATE table_tmp as TT, (
        SELECT SUM(LCDebitAmount) as I, SCC.CateValue as SbiCategoryCateValue, NC.CateNo as NormCateNo
        FROM act_gl_books as A
        INNER JOIN sbi_category_cate as SCC on A.SbiCategoryID = SCC.SbiCategoryID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
        WHERE A.AccountNo = '0442'
        AND SCC.CateNo = '005'
        AND NC.CateNo IN ('201511201', '201511202')
        $whereCondition
        GROUP BY SCC.CateValue, NC.CateNo
        ) as HH SET I1 = I
        WHERE TT.SbiCategoryCateValue = HH.SbiCategoryCateValue
        AND TT.NormCateNo = HH.NormCateNo";

        DB::statement($sql);
    }

    public function updateRowII_1($whereCondition){
        $sql = " UPDATE table_tmp as TT, (
        SELECT SUM(LCDebitAmount) as I, SCC.CateValue as SbiCategoryCateValue, NC.CateNo as NormCateNo
        FROM act_gl_books as A
        INNER JOIN sbi_category_cate as SCC on A.SbiCategoryID = SCC.SbiCategoryID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
        WHERE A.AccountNo = '0341'
        AND SCC.CateNo = '005' AND SCC.CateValue = '1'
        AND NC.CateNo IN ('201511201', '201511202')
        $whereCondition
        GROUP BY NC.CateNo
        ) as HH SET I1 = I
        WHERE TT.SbiCategoryCateValue = HH.SbiCategoryCateValue
        AND TT.NormCateNo = HH.NormCateNo";

        DB::statement($sql);
    }

    public function updateRowII_2($whereCondition){
        $sql = " UPDATE table_tmp as TT, (
        SELECT SUM(LCDebitAmount) as I, SCC.CateValue as SbiCategoryCateValue, NC.CateValue as NormCateValue, PC.CateNo as ProjectCateNo
        FROM act_gl_books as A
        INNER JOIN sbi_category_cate as SCC on A.SbiCategoryID = SCC.SbiCategoryID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
        INNER JOIN project_cate as PC on A.ProjectID = PC.ProjectID
        WHERE A.AccountNo = '0341'
        AND SCC.CateNo = '005' AND SCC.CateValue = '2'
        AND NC.CateNo = '20151111112' AND NC.CateValue IN ('1', '2', '3')
        AND PC.CateNo IN ('00701', '00702', '00703', '00704', '00705')
        $whereCondition
        GROUP BY NC.CateValue, PC.CateNo
        ) as HH SET I1 = I
        WHERE TT.SbiCategoryCateValue = HH.SbiCategoryCateValue
        AND TT.NormCateValue = HH.NormCateValue
        AND TT.ProjectCateNo = HH.ProjectCateNo";

        DB::statement($sql);
    }

    public function updateRowII_3($whereCondition){
        $sql = " UPDATE table_tmp as TT, (
        SELECT SUM(LCDebitAmount) as I, SCC.CateValue as SbiCategoryCateValue, NC.CateValue as NormCateValue
        FROM act_gl_books as A
        INNER JOIN sbi_category_cate as SCC on A.SbiCategoryID = SCC.SbiCategoryID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
        WHERE A.AccountNo = '0341'
        AND SCC.CateNo = '005'
        AND NC.CateNo = '20151111111' AND NC.CateValue IN ('1', '2')
        $whereCondition
        GROUP BY SCC.CateValue, NC.CateValue
        ) as HH SET I1 = I
        WHERE TT.SbiCategoryCateValue = HH.SbiCategoryCateValue
        AND TT.NormCateValue = HH.NormCateValue";

        DB::statement($sql);
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



}
