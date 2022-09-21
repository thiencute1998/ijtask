<?php

namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Accounting\Models\Accounting;
use Module\Accounting\Models\AccountingBook;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;

class SBP3422016TTBTCMB07Controller extends Controller
{
    use Common;

    public function formatArr($cateID, $parentCateID, $itemID, $itemName, $parentID, $level, $type, $index){

    }

    public function SBP3422016TTBTC_MB07(Request $request)
    {
        //
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : date('Y');
        $fromDate = (isset($filter['Year'])) ? $filter['Year'].'-01-01' : $year.'-01-01';
        $toDate = (isset($filter['Year'])) ? $filter['Year'].'-12-31' : $year.'-12-31';
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

        if (isset($filter['CompanyID'])) {
            $note .= 'đơn vị ' . $filter['CompanyName'];
            $company = Company::where('CompanyID',$filter['CompanyID'])->first();
        }
        else{
            $company = Company::where('CompanyID',Auth::user()->CompanyID)->first();
        }

        $companyName = $company->CompanyName;

        $whereCondition = " AND (YEAR(A.PeriodFromDate) BETWEEN $year - 1 AND $year + 1) AND A.InTransTypeID NOT IN (16, 17, 18) ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT '',
          `RevenueNo` varchar(255) DEFAULT '',
          `SbiCategoryCateValue` varchar(255) DEFAULT '',
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `Norder` int(11) DEFAULT NULL,
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
            `Revenue` varchar(20) DEFAULT '',
          `Detail` int(11) DEFAULT NULL,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        // caculator here
        $this->insertRI($whereCondition, $year);

        $countRecords = DB::table('table_tmp')->count();
        $this->insertRII_2($whereCondition, $year);
        if(DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeCommon([['sbi_category_cate_value','001'],'revenue']);
        }

        $this->insertRII_3($whereCondition, $year);

        $this->insertRII_1();

        $this->setLevel();

        // set STT

        $this->setLevelMB07();

        $this->setDataDefault();

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumUpColumn($maxLevel, 1);
        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID','ASC')->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . ($year - 1). '(năm trước)';
            $data[0]->HeaderName2 = 'Năm ' .$year. '(năm hiện hành)';
            $data[0]->HeaderName3 = 'Dự toán năm ' .($year + 1). '(năm kế hoạch)';
        }
        $dateTime = "Vĩnh Phú, ngày $date tháng $month năm $yearC";
        if ($company) {
            $dateTime = $company->Address . ", ngày $date tháng $month năm $yearC";
        }

        $response = [
            'master' => [
                "Year" => "",
                "CreateName" => "Nguyễn Hoàng Anh",
                "TransNo" => "00001",
                "ReportName" => "DỰ TOÁN THU, CHI, NỘP NGÂN SÁCH NHÀ NƯỚC TỪ CÁC KHOẢN PHÍ VÀ LỆ PHÍ NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => $companyName,
                "Note" => $note
            ],
            'detail' => $data,
            'total' => 1,
            'per_page' => 1,
            'current_page' => 1,
        ];

        $json['data'] = $response;
        return json_encode($json);
    }



    public function sumA3(){
        $sql = "SELECT SUM(I1) as I1, SUM(I2) as I2, SUM(I3) as I3, SUM(I4) as I4 FROM table_tmp
                WHERE (ItemID = 'A#01' OR ItemID = 'A#02#03')";
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
                    WHERE ItemID = 'A#03'";
                DB::statement($update);
            }
        }
    }

    public function sumUpColumn($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
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
            if($i == 2){
                $this->sumA3();
            }
        }

    }

    public function insertRI($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Revenue, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', RevenueNo) as ItemID, ParentID, ItemName, RevenueNo, 1 as Detail,
        MAX(CASE WHEN AccountNo = '0361' AND YearD = $year - 1 THEN I ELSE 0 END) as I1,
        MAX(CASE WHEN AccountNo = '0341' AND YearD = $year THEN I ELSE 0 END) as I2,
        MAX(CASE WHEN AccountNo = '0711' AND YearD = $year THEN I ELSE 0 END) as I3,
        MAX(CASE WHEN AccountNo = '0311' AND YearD = $year + 1 THEN I ELSE 0 END) as I4 FROM
        (SELECT 'A#01' as ParentID, RevenueName as ItemName, A.RevenueNo,
        A.AccountNo, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
        WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
        AND RC.CateNo = '2011104' AND RC.CateValue = '2'
        AND NC.CateNo = '2011111' AND NC.CateValue = '1'
        $whereCondition
        GROUP BY A.AccountNo, Year(A.PeriodFromDate), A.RevenueNo
        ) as CH GROUP BY RevenueNo";

        DB::statement($sql);
    }

    public function insertRII_2($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, SbiCategoryCateValue, Revenue, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', RevenueNo) as ItemID, ParentID, ItemName, SbiCategoryCateValue, RevenueNo, 1 as Detail,
        MAX(CASE WHEN AccountNo = '0462' AND YearD = $year - 1 THEN I ELSE 0 END) as I1,
        MAX(CASE WHEN AccountNo = '0442' AND YearD = $year THEN I ELSE 0 END) as I2,
        MAX(CASE WHEN AccountNo = '0722' AND YearD = $year THEN I ELSE 0 END) as I3,
        MAX(CASE WHEN AccountNo = '0412' AND YearD = $year + 1 THEN I ELSE 0 END) as I4 FROM
        (SELECT CONCAT('A#02#02#', SCC.CateValue) as ParentID, RevenueName as ItemName, SCC.CateValue as SbiCategoryCateValue, A.RevenueNo,
        A.AccountNo, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
        INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
        INNER JOIN sbi_category_cate as SCC on A.SbiCategoryID = SCC.SbiCategoryID
        WHERE A.AccountNo IN ('0462', '0442', '0722', '0412')
        AND RC.CateNo = '2011104' AND RC.CateValue = '1'
        AND CC.CateNo = '200211'
        AND LEFT(NC.CateNo, 7) = '2015112'
        AND SCC.CateNo = '001'
        $whereCondition
        GROUP BY A.AccountNo, Year(A.PeriodFromDate), SCC.CateValue, A.RevenueNo
        ) as CH GROUP BY SbiCategoryCateValue, RevenueNo";

        DB::statement($sql);
    }

    public function insertRII_3($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Revenue, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', RevenueNo) as ItemID, ParentID, ItemName, RevenueNo, 1 as Detail,
        MAX(CASE WHEN AccountNo = '0361' AND YearD = $year - 1 THEN I ELSE 0 END) as I1,
        MAX(CASE WHEN AccountNo = '0341' AND YearD = $year THEN I ELSE 0 END) as I2,
        MAX(CASE WHEN AccountNo = '0711' AND YearD = $year THEN I ELSE 0 END) as I3,
        MAX(CASE WHEN AccountNo = '0311' AND YearD = $year + 1 THEN I ELSE 0 END) as I4 FROM
        (SELECT 'A#02#03' as ParentID, RevenueName as ItemName, A.RevenueNo,
        A.AccountNo, YEAR(A.PeriodFromDate) as YearD, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
        WHERE A.AccountNo IN ('0361', '0341', '0711', '0311')
        AND RC.CateNo = '2011104' AND RC.CateValue = '1'
        AND ((NC.CateNo = '2011111' AND NC.CateValue = '1') OR (NC.CateNo = '2011112' AND NC.CateValue = '2'))
        $whereCondition
        GROUP BY A.AccountNo, Year(A.PeriodFromDate), A.RevenueNo
        ) as CH GROUP BY RevenueNo";

        DB::statement($sql);
    }

    public function insertRII_1(){
        $sql = "SELECT Revenue, ItemName, SUM(I1) as I1, SUM(I2) as I2, SUM(I3) as I3, SUM(I4) as I4
        FROM table_tmp WHERE Detail = 1 GROUP BY Revenue";
        $result = DB::select($sql);
        if($result){
            $insertArr = [];
            foreach($result as $item){
                $insertArr[] = [
                    'ItemID'=> 'A#02#01#' . $item->Revenue,
                    'ParentID'=> 'A#02#01',
                    'ItemName'=> $item->ItemName,
                    'Revenue'=> $item->Revenue
                ];
            }
            DB::table('table_tmp')->insert($insertArr);
        }
    }


    public function setLevelMB07(){
        $sql = "SELECT * FROM table_tmp WHERE Level = 3 AND LEFT(ItemID, 7) = 'A#02#02' ORDER BY ItemID ASC";
        $result = DB::select($sql);
        if($result){
            foreach($result as $key=> $item){
                DB::table('table_tmp')->where('ItemID',$item->ItemID)->update(['STT'=> '2.'. ($key + 1)]);
            }
        }
    }

    public function setDataDefault(){
        $insert = [
            ['STT'=> 'I', 'ItemID'=> 'A#01', 'ParentID'=> '', 'ItemName'=> 'Số thu lệ phí', 'Level'=> 1, 'FontWeight'=> 1],
            ['STT'=> 'II', 'ItemID'=> 'A#02', 'ParentID'=> '', 'ItemName'=> 'Số thu phí', 'Level'=> 1, 'FontWeight'=> 1],
            ['STT'=> '1', 'ItemID'=> 'A#02#01', 'ParentID'=> 'A#02', 'ItemName'=> 'Tổng số thu phí', 'Level'=> 1, 'FontWeight'=> 2],
            ['STT'=> '2', 'ItemID'=> 'A#02#02', 'ParentID'=> 'A#02', 'ItemName'=> 'Chi từ nguồn thu phí được để lại', 'Level'=> 2, 'FontWeight'=> 1],
            ['STT'=> '3', 'ItemID'=> 'A#02#03', 'ParentID'=> 'A#02', 'ItemName'=> 'Số phí nộp ngân sách nhà nước', 'Level'=> 2, 'FontWeight'=> 1],
            ['STT'=> 'III', 'ItemID'=> 'A#03', 'ParentID'=> '', 'ItemName'=> 'Tổng số thu lệ phí, phí nộp ngân sách nhà nước', 'Level'=> 1, 'FontWeight'=> 1],
        ];
        DB::table('table_tmp')->insert($insert);
    }

}
