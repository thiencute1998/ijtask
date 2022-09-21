<?php

namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB02Controller extends Controller
{
    public function SBP3422016TTBTC_MB02(Request $request)
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : 2021;
        Log::debug($year);
        $companyID = (isset($filter['CompanyID'])) ? $filter['CompanyID'] : null;
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
            $company = Company::where('CompanyID',$filter['CompanyID'])->get();
        }
        else{
            $company = Company::where('CompanyID',Auth::user()->CompanyID)->get();
        }

        $companyName = Company::find(Auth::user()->CompanyID)->CompanyName;

        $listCompany = [];
        $allCompanyChild = self::populateChildrenCompanys($company);
        foreach ($allCompanyChild as $companyChild) {
            $listCompany[] = $companyChild;
        }
        $company = $company->merge($listCompany);

        $nextYear = (int)$year + 1;
        $whereCondition = "AND (YEAR(A.PostDate) = $year" . " OR YEAR(A.PostDate) = $nextYear)";
        $whereConditionNext = "AND YEAR(A.PostDate) = $nextYear";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `RevenueCateValue1` varchar(20) DEFAULT '',
          `RevenueNo2` varchar(20) DEFAULT '',
          `RevenueCateValue3` varchar(20) DEFAULT '',
          `RevenueCateNo4` varchar(20) DEFAULT '',
          `RevenueCateValue4` varchar(20) DEFAULT '',
          `CompanyID` int(11) DEFAULT NULL,
          `ParentCompanyID` int(11) DEFAULT NULL,
          `LevelCompany` int(11) DEFAULT NULL,
          `Detail` int(11) DEFAULT NULL,
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
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb02')->get();
        $insertArr = [];
        foreach ($company as $item){
            foreach ($templateTable as $key=> $template) {
                if($key == 0){
                    $insertArr[] = [
                        'STT' => $template->STT,
                        'ItemID' => $template->ItemID,
                        'ParentID' => $template->ParentID,
                        'Level' => $template->Level,
                        'ItemName' => $item->CompanyName,
                        'FontWeight' => $template->FontWeight,
                        'Italic' => $template->Italic,
                        'Center' => $template->Center,
                        'RevenueCateValue1' => $template->RevenueCateValue1,
                        'RevenueNo2' => $template->RevenueNo2,
                        'RevenueCateValue3' => $template->RevenueCateValue3,
                        'RevenueCateNo4' => $template->RevenueCateNo4,
                        'RevenueCateValue4' => $template->RevenueCateValue4,
                        'CompanyID' => $item->CompanyID,
                        'ParentCompanyID'=> $item->ParentID,
                        'LevelCompany'=> $item->Level,
                        'Detail'=> $item->Detail,
                        'I1' => $template->I1,
                        'I2' => $template->I2,
                        'I3' => $template->I3,
                        'I4' => $template->I4,
                        'I5' => $template->I5,
                        'I6' => $template->I6,
                        'I7' => $template->I7,
                        'I8' => $template->I8,
                        'I9' => $template->I9,
                        'I10' => $template->I10,
                        'I11' => $template->I11,
                        'I12' => $template->I12,
                        'I13' => $template->I13,
                        'I14' => $template->I14,
                        'I15' => $template->I15,
                        'I16' => $template->I16,
                        'I17' => $template->I17,
                        'I18' => $template->I18,
                        'I19' => $template->I19,
                        'I20' => $template->I20,
                        'I21' => $template->I21,
                    ];
                }
                else{
                    $insertArr[] = [
                        'STT' => $template->STT,
                        'ItemID' => $template->ItemID,
                        'ParentID' => $template->ParentID,
                        'Level' => $template->Level,
                        'ItemName' => $template->ItemName,
                        'FontWeight' => $template->FontWeight,
                        'Italic' => $template->Italic,
                        'Center' => $template->Center,
                        'RevenueCateValue1' => $template->RevenueCateValue1,
                        'RevenueNo2' => $template->RevenueNo2,
                        'RevenueCateValue3' => $template->RevenueCateValue3,
                        'RevenueCateNo4' => $template->RevenueCateNo4,
                        'RevenueCateValue4' => $template->RevenueCateValue4,
                        'CompanyID' => $item->CompanyID,
                        'ParentCompanyID'=> $item->ParentID,
                        'LevelCompany'=> $item->Level,
                        'Detail'=> $item->Detail,
                        'I1' => $template->I1,
                        'I2' => $template->I2,
                        'I3' => $template->I3,
                        'I4' => $template->I4,
                        'I5' => $template->I5,
                        'I6' => $template->I6,
                        'I7' => $template->I7,
                        'I8' => $template->I8,
                        'I9' => $template->I9,
                        'I10' => $template->I10,
                        'I11' => $template->I11,
                        'I12' => $template->I12,
                        'I13' => $template->I13,
                        'I14' => $template->I14,
                        'I15' => $template->I15,
                        'I16' => $template->I16,
                        'I17' => $template->I17,
                        'I18' => $template->I18,
                        'I19' => $template->I19,
                        'I20' => $template->I20,
                        'I21' => $template->I21,
                    ];
                }

            }
        }

        DB::table('table_tmp')->insert($insertArr);

        $this->insertDetail();
        $this->insertI2I9I16($whereCondition,$year,$nextYear);
        $this->insertI3I4I10I11I17I18($whereCondition,$year,$nextYear);
        $this->insertI5I12I19($whereCondition,$year,$nextYear);
        $this->insertI6I13I20($whereCondition,$year,$nextYear);
        $this->insertI7I14I21($whereCondition,$year,$nextYear);
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $this->sumUpColumn(1,21, $maxLevel);
        $this->sumUpRow();

        $this->deleteDetail($maxLevel);
        if(count($company) > 1){
            $this->sumParent();
        }

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->paginate(30);
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Dự toán năm ' . $year. '(năm hiện hành)';
            $data[0]->HeaderName2 = 'Ước thực hiện năm ' . $year. '(năm hiện hành)';
            $data[0]->HeaderName3 = 'Dự toán năm ' .$nextYear. '(năm kế hoạch)';
        }
        $company = Company::find($companyID);
        $dateTime = "Vĩnh Phú, ngày $date tháng $month năm $yearC";
        if ($company) {
            $dateTime = $company->Address . ", ngày $date tháng $month năm $yearC";
        }

        $response = [
            'master' => [
                "Year" => "",
                "CreateName" => "Nguyễn Hoàng Anh",
                "TransNo" => "00001",
                "ReportName" => "TỔNG HỢP DỰ TOÁN THU NGÂN SÁCH NHÀ NƯỚC THEO SẮC THUẾ NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => $companyName,
                "Note" => $note
            ],
            'detail' => $data->items(),
            'total' => $data->total(),
            'per_page' => $data->perPage(),
            'current_page' => $data->currentPage(),
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function insertDetail(){
        // insert detail

        $arrInsert = [];
        $parents = DB::table('table_tmp')->where('RevenueCateValue1','like','%,%')->get();
        foreach ($parents as $parent){
            $listRevenueCateValue = explode(',',$parent->RevenueCateValue1);
            foreach ($listRevenueCateValue as $catevalue){
                $arrInsert[] = [
                    'Level' => $parent->Level + 1,
                    'ParentID'=> $parent->ItemID,
                    'RevenueCateValue1'=> $catevalue,
                    'CompanyID' => $parent->CompanyID,
                    'ParentCompanyID'=> $parent->ParentCompanyID,
                    'LevelCompany'=> $parent->LevelCompany,
                    'Detail'=> $parent->Detail,
                ];
            }

        }

        $arrInsert1 = [];
        $parent4s = DB::table('table_tmp')->where('RevenueCateValue4','like','%,%')->get();
        foreach ($parent4s as $parent4){
            $listRevenueCateValue4 = explode(',',$parent4->RevenueCateValue4);
            foreach ($listRevenueCateValue4 as $catevalue4){
                $arrInsert1[] = [
                    'Level' => $parent4->Level + 1,
                    'ParentID'=> $parent4->ItemID,
                    'RevenueCateNo4'=> $parent4->RevenueCateNo4,
                    'RevenueCateValue4'=> $catevalue4,
                    'CompanyID' => $parent4->CompanyID,
                    'ParentCompanyID'=> $parent4->ParentCompanyID,
                    'LevelCompany'=> $parent4->LevelCompany,
                    'Detail'=> $parent4->Detail,
                ];
            }

        }

        DB::table('table_tmp')->insert($arrInsert);
        DB::table('table_tmp')->insert($arrInsert1);
    }

    public static function populateChildrenCompanys($company){
        $listCompanyChild = [];
        $companyIDArr = [];
        foreach ($company as $company) {
            $companyIDArr[] = $company->CompanyID;
        }
        if (count($companyIDArr)) {
            $query = Company::query();
            $query->whereIn('ParentID', $companyIDArr);
            $companyChild = $query->get();
            if (count($companyChild)) {
                self::populateChildrenCompanys($companyChild);
                foreach ($companyChild as $companyChild) {
                    $listCompanyChild[] = $companyChild;
                }
            }
        }

        return $listCompanyChild;

    }

    public function insertI2I9I16($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID, AccountNo, YearPostDate, RevenueCateValue,
			MAX(CASE WHEN AccountNo = '0151' AND YearPostDate = $year THEN I END) AS I2,
			MAX(CASE WHEN AccountNo = '0111' AND YearPostDate = $year THEN I END) as I9,
			MAX(CASE WHEN AccountNo = '0121' AND YearPostDate = $nextYear THEN I END) as I16
			FROM (SELECT A.CompanyID as CompanyID, A.AccountNo as AccountNo, R.CateValue as RevenueCateValue, YEAR(A.PostDate) as YearPostDate, SUM(LCDebitAmount) AS I
        FROM act_gl_books AS A
				INNER JOIN revenue_cate as R on A.RevenueID = R.RevenueID
				INNER JOIN sbi_chapter_cate as S on A.SbiChapterID = S.SbiChapterID
				WHERE A.AccountNo IN ('0151','0111','0121')
				AND R.CateNo = '1101' AND S.CateNo = '002'
				AND S.CateValue IN ('1','2')
				AND A.InTransTypeID not in (16,17,18)
				$whereCondition
				GROUP BY A.CompanyID, A.AccountNo, R.CateNo, R.CateValue, S.CateNo, S.CateValue, YearPostDate
			) as CH GROUP BY CompanyID, RevenueCateValue) as HH set TT.I2 = HH.I2, TT.I9 = HH.I9, TT.I16 = HH.I16
				WHERE TT.RevenueCateValue1 = HH.RevenueCateValue
				AND TT.CompanyID = HH.CompanyID
				AND TT.Detail = 1";

        DB::statement($sql);
    }

    public function insertI3I4I10I11I17I18($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID, AccountNo, YearPostDate, SbiChapterCateNo, RevenueCateValue,
			MAX(CASE WHEN AccountNo = '0151' AND SbiChapterCateNo = '003' AND YearPostDate = $year THEN I END) AS I3,
			MAX(CASE WHEN AccountNo = '0151' AND SbiChapterCateNo = '004' AND YearPostDate = $year THEN I END) as I4,
			MAX(CASE WHEN AccountNo = '0111' AND SbiChapterCateNo = '003' AND YearPostDate = $year THEN I END) as I10,
			MAX(CASE WHEN AccountNo = '0111' AND SbiChapterCateNo = '004' AND YearPostDate = $year THEN I END) as I11,
			MAX(CASE WHEN AccountNo = '0121' AND SbiChapterCateNo = '003' AND YearPostDate = $nextYear THEN I END) as I17,
			MAX(CASE WHEN AccountNo = '0121' AND SbiChapterCateNo = '004' AND YearPostDate = $nextYear THEN I END) as I18
			FROM (SELECT A.CompanyID as CompanyID, A.AccountNo as AccountNo, R.CateValue as RevenueCateValue, S.CateNo as SbiChapterCateNo, YEAR(A.PostDate) as YearPostDate, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as R on A.RevenueID = R.RevenueID
            INNER JOIN sbi_chapter_cate as S on A.SbiChapterID = S.SbiChapterID
            WHERE A.AccountNo IN ('0151','0111','0121')
            AND S.CateNo IN ('003','004')
            AND A.InTransTypeID not in (16,17,18)
            $whereCondition
            GROUP BY A.CompanyID, A.AccountNo, R.CateNo, R.CateValue, S.CateNo, YearPostDate
			) as CH GROUP BY RevenueCateValue, CompanyID)  as HH set
			TT.I3 = HH.I3, TT.I4 = HH.I4, TT.I10 = HH.I10, TT.I11 = HH.I11, TT.I17 = HH.I17, TT.I18 = HH.I18
			WHERE TT.RevenueCateValue1 = HH.RevenueCateValue
			AND TT.CompanyID = HH.CompanyID
			AND TT.Detail = 1";
        DB::statement($sql);
    }

    public function insertI5I12I19($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID, AccountNo, YearPostDate, RevenueNo,
			MAX(CASE WHEN AccountNo = '0151' AND YearPostDate = $year THEN I END) AS I5,
			MAX(CASE WHEN AccountNo = '0111' AND YearPostDate = $year THEN I END) as I12,
			MAX(CASE WHEN AccountNo = '0121' AND YearPostDate = $nextYear THEN I END) as I19
			FROM (SELECT A.CompanyID as CompanyID, A.AccountNo as AccountNo, A.RevenueNo as RevenueNo, YEAR(A.PostDate) as YearPostDate, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            WHERE A.AccountNo IN ('0151','0111','0121')
            AND A.RevenueNo IN ('140302','140301', '140303')
            AND A.InTransTypeID not in (16,17,18)
            $whereCondition
            GROUP BY A.CompanyID, A.AccountNo, A.RevenueNo, YEAR(A.PostDate)
			) as CH GROUP BY CompanyID, RevenueNo) as HH
			set TT.I5 = HH.I5, TT.I12 = HH.I12, TT.I19 = HH.I19
	        WHERE TT.RevenueNo2 = HH.RevenueNo
	        AND TT.CompanyID = HH.CompanyID
	        AND TT.Detail = 1";
        DB::statement($sql);
    }

    public function insertI6I13I20($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID, AccountNo, YearPostDate, RevenueCateValue,
			MAX(CASE WHEN AccountNo = '0151' AND YearPostDate = $year THEN I END) AS I6,
			MAX(CASE WHEN AccountNo = '0111' AND YearPostDate = $year THEN I END) as I13,
			MAX(CASE WHEN AccountNo = '0121' AND YearPostDate = $nextYear THEN I END) as I20
			FROM (SELECT A.CompanyID as CompanyID, A.AccountNo as AccountNo, R.CateValue as RevenueCateValue, YEAR(A.PostDate) as YearPostDate, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as R on A.RevenueID = R.RevenueID
            WHERE A.AccountNo IN ('0151','0111','0121')
            AND R.CateNo = '1104' AND R.CateValue IN ('1','2','3')
            AND A.InTransTypeID not in (16,17,18)
            $whereCondition
            GROUP BY A.CompanyID, A.AccountNo, R.CateNo, R.CateValue, YEAR(A.PostDate)
			) as CH GROUP BY CompanyID, RevenueCateValue) as HH
			set TT.I6 = HH.I6, TT.I13 = HH.I13, TT.I20 = HH.I20
	        WHERE TT.RevenueCateValue3 = HH.RevenueCateValue
            AND TT.CompanyID = HH.CompanyID
            AND TT.Detail = 1";
        DB::statement($sql);
    }

    public function insertI7I14I21($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID, AccountNo, YearPostDate, RevenueCateNo, RevenueCateValue,
        MAX(CASE WHEN AccountNo = '0151' AND YearPostDate = $year THEN I END) AS I7,
        MAX(CASE WHEN AccountNo = '0111' AND YearPostDate = $year THEN I END) as I14,
        MAX(CASE WHEN AccountNo = '0121' AND YearPostDate = $nextYear THEN I END) as I21
        FROM (SELECT A.CompanyID as CompanyID, A.AccountNo as AccountNo, R.CateNo as RevenueCateNo, R.CateValue as RevenueCateValue, YEAR(A.PostDate) as YearPostDate, SUM(LCDebitAmount) AS I
        FROM act_gl_books AS A
        INNER JOIN revenue_cate as R on A.RevenueID = R.RevenueID
        WHERE A.AccountNo IN ('0151','0111','0121')
        AND A.InTransTypeID not in (16,17,18)
        $whereCondition
        GROUP BY A.CompanyID, A.AccountNo, R.CateNo, R.CateValue, YEAR(A.PostDate)
        ) as CH GROUP BY CompanyID, RevenueCateNo, RevenueCateValue)  as HH
        set TT.I7 = HH.I7, TT.I14 = HH.I14, TT.I21 = HH.I21
	    WHERE TT.RevenueCateNo4 = HH.RevenueCateNo
        AND TT.RevenueCateValue4 = HH.RevenueCateValue
	    AND TT.CompanyID = HH.CompanyID
	    AND TT.Detail = 1";
        DB::statement($sql);
    }

    public function sumUpRow(){
        $data = DB::table('table_tmp')->get();
        foreach ($data as $item){
            $I1 = $item->I2 + $item->I3 + $item->I4 + $item->I5 + $item->I6 + $item->I7;
            $I8 = $item->I9 + $item->I10 + $item->I11 + $item->I12 + $item->I13 + $item->I14;
            $I15 = $item->I16 + $item->I17 + $item->I18 + $item->I19 + $item->I20 + $item->I21;
            DB::table('table_tmp')->where('TableID',$item->TableID)->update(['I1'=>$I1, 'I8'=> $I8, 'I15'=> $I15]);
        }
    }

    public function sumUpColumn($start, $end, $maxLevel){
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT CompanyID,ItemID, ParentID, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
       SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7,
       SUM(M.I9) AS I9, SUM(M.I10) AS I10, SUM(M.I11) AS I11, SUM(M.I12) AS I12,
       SUM(M.I13) AS I13, SUM(M.I14) AS I14, SUM(M.I16) AS I16,
       SUM(M.I17) AS I17, SUM(M.I18) AS I18, SUM(M.I19) AS I19, SUM(M.I20) AS I20,
       SUM(M.I21) AS I21 FROM table_tmp M
                WHERE M.Level = " . $i . " GROUP BY CompanyID, ParentID";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
                        $I2 = ($value['I2']) ? $value['I2'] : 0;
                        $I3 = ($value['I3']) ? $value['I3'] : 0;
                        $I4 = ($value['I4']) ? $value['I4'] : 0;
                        $I5 = ($value['I5']) ? $value['I5'] : 0;
                        $I6 = ($value['I6']) ? $value['I6'] : 0;
                        $I7 = ($value['I7']) ? $value['I7'] : 0;
                        $I9 = ($value['I9']) ? $value['I9'] : 0;
                        $I10 = ($value['I10']) ? $value['I10'] : 0;
                        $I11 = ($value['I11']) ? $value['I11'] : 0;
                        $I12 = ($value['I12']) ? $value['I12'] : 0;
                        $I13 = ($value['I13']) ? $value['I13'] : 0;
                        $I14 = ($value['I14']) ? $value['I14'] : 0;
                        $I16 = ($value['I16']) ? $value['I16'] : 0;
                        $I17 = ($value['I17']) ? $value['I17'] : 0;
                        $I18 = ($value['I18']) ? $value['I18'] : 0;
                        $I19 = ($value['I19']) ? $value['I19'] : 0;
                        $I20 = ($value['I20']) ? $value['I20'] : 0;
                        $I21 = ($value['I21']) ? $value['I21'] : 0;
                        $update = "UPDATE table_tmp
                    SET I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                    I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ",
                    I9 = " . $I9 . ", I10 = " . $I10 . ", I11 = " . $I11 . ",
                    I12 = " . $I12 . ", I13 = " . $I13 . ", I14 = " . $I14 . ",
                    I16 = " . $I16 . ", I17 = " . $I17 . ", I18 = " . $I18 . ",
                    I19 = " . $I19 . ", I20 = " . $I20 . ", I21 = " . $I21 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j . " AND CompanyID = " .$value['CompanyID'];
                        DB::statement($update);
                    }
                }
            }
        }
    }

    public function deleteDetail($level){
        $maxLevel = $level->maxLevel;
        $sql = 'DELETE FROM table_tmp WHERE Level ='. $maxLevel;
        DB::statement($sql);
    }

    public function sumParent(){
        $maxLevelNotDetail = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.LevelCompany) AS maxLevel'))->first();
        for ($i = $maxLevelNotDetail->maxLevel; $i > 1; $i--){
            $j = $i - 1;
            $sql = "SELECT ParentCompanyID ,ItemID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
       SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8,
       SUM(M.I9) AS I9, SUM(M.I10) AS I10, SUM(M.I11) AS I11, SUM(M.I12) AS I12,
       SUM(M.I13) AS I13, SUM(M.I14) AS I14, SUM(M.I15) AS I15, SUM(M.I16) AS I16,
       SUM(M.I17) AS I17, SUM(M.I18) AS I18, SUM(M.I19) AS I19, SUM(M.I20) AS I20,
       SUM(M.I21) AS I21 FROM table_tmp M
                WHERE M.LevelCompany = " . $i . " GROUP BY ParentCompanyID, ItemID";
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
                    SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                    I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ",
                    I9 = " . $I9 . ", I10 = " . $I10 . ", I11 = " . $I11 . ",
                    I12 = " . $I12 . ", I13 = " . $I13 . ", I14 = " . $I14 . ", I15 = " . $I15 . ",
                    I16 = " . $I16 . ", I17 = " . $I17 . ", I18 = " . $I18 . ",
                    I19 = " . $I19 . ", I20 = " . $I20 . ", I21 = " . $I21 . "
                    WHERE ItemID = '" . $value['ItemID'] . "'
                    AND table_tmp.LevelCompany = " . $j . " AND CompanyID = " .$value['ParentCompanyID'];
                    DB::statement($update);
                }
            }
        }
    }

}
