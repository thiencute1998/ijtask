<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB05Controller extends Controller {
    public function SBP3422016TTBTC_MB05(Request $request){
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

        $whereDirection = '';
        if ($directionID) {
            $whereDirection = 'AND A.DirectionID = ' . $directionID . ' ';
        }

        if (isset($filter['CompanyID'])) {
            $note .= 'đơn vị ' . $filter['CompanyName'];
            $company = Company::where('CompanyID',$filter['CompanyID'])->first();
        }
        else{
            $company = Company::where('CompanyID',Auth::user()->CompanyID)->first();
        }

        if(!isset($filter['LevelCompany'])){
            $levelCompany = null;
        }
        else{
            $levelCompany = $filter['LevelCompany'];
        }

        $lastYear = (int)$year - 1;
        $nextYear = (int)$year + 1;
        $whereCondition = "AND (YEAR(A.PeriodFromDate) BETWEEN $lastYear AND $nextYear) AND A.InTransTypeID NOT IN (16,17,18)";

        // Lọc đơn vị phát sinh chứng từ

        $arr[] = array_column($this->selectA2($whereCondition), 'PathCompanyID');
        $arr[] = array_column($this->selectA3($whereCondition), 'PathCompanyID');
        $arr[] = array_column($this->selectBI1($whereCondition), 'PathCompanyID');
        $arr[] = array_column($this->selectBI23($whereCondition), 'PathCompanyID');
        $arr[] = array_column($this->selectBII($whereCondition), 'PathCompanyID');
        $arr[] = array_column($this->selectBIII($whereCondition), 'PathCompanyID');
        $arr[] = array_column($this->selectBIV1($whereCondition), 'PathCompanyID');
        $arr[] = array_column($this->selectBIV2($whereCondition), 'PathCompanyID');
        $listPath = array_unique(call_user_func_array('array_merge', $arr));

        $arrID = [];
        if($company->IsFinancialCompany == 0){
            foreach ($listPath as $item){
                if(strpos($item, (string)$company->CompanyID) !== false){
                    $id = explode('_',$item);
                    foreach($id as $i){
                        $arrID[] = $i;
                    }
                }
            }
        }
        else{
            foreach($listPath as $item){
                $id = explode('_',$item);
                foreach($id as $i){
                    $arrID[] = $i;
                }
            }
        }
        $arrID = array_unique($arrID);
        $listChildrenCompany = DB::table('company')->where(function($q) use($arrID,$company){
            $q->whereIn('CompanyID',$arrID)
                ->orWhere('CompanyID','=',$company->CompanyID);
        })
            ->where('Level','>=',$company->Level)
            ->where('Inactive','!=',1)->get()->toArray();

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(255) DEFAULT '',
          `ItemNo` varchar(255) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `SbiCategoryNo` varchar(20) DEFAULT '',
          `SbiCategoryValue` varchar(20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
          `ExpenseCateValue` varchar(20) DEFAULT '',
          `ProgramCateValue` varchar(50) DEFAULT '',
          `ProjectNo` varchar(50) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
            `CompanyID` int(11) DEFAULT NULL,
            `ParentCompanyID` int(11) DEFAULT NULL,
            `LevelCompany` int(11) DEFAULT NULL,
            `Detail` int(11) DEFAULT NULL,
            `Norder` int(11) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb05')->get();
        $insertArr = [];
        foreach ($listChildrenCompany as $index=> $item){
            if(substr($item->CompanyNo, 0 ,3* $company->Level) === substr($company->CompanyNo, 0 ,3* $company->Level) && $company->IsFinancialCompany == 1)
                $norder = 1;
            else
                $norder = 2;
            foreach ($templateTable as $key=> $template) {
                if($key == 0){
                    $insertArr[] = $this->formatInsertArr('','', $item->CompanyNo,'',null,$item->CompanyName,1, 0, 0, null, null, null, null, null, null, $item, $norder);
                }
                $insertArr[] = $this->formatInsertArr($template->STT,$template->ItemID, $item->CompanyNo . '.' .$template->ItemID ,$template->ParentID,$template->Level,$template->ItemName, $template->FontWeight, $template->Italic, $template->Center, $template->SbiCategoryNo, $template->SbiCategoryValue, $template->NormCateValue, $template->ExpenseCateValue, $template->ProgramCateValue, $template->ProjectNo, $item, $norder);
            }
        }

        $chunks = array_chunk($insertArr, count($insertArr)/10);
        foreach ($chunks as $chunk){
            DB::table('table_tmp')->insert($chunk);
        }

        // insert detail

        $this->insertDetail($listChildrenCompany,$company);

        // cell 1

        $this->insertTableTmpA2($whereCondition, $lastYear, $year, $nextYear);
        $this->insertTableTmpA3($whereCondition, $lastYear, $year, $nextYear);
        $this->insertTableTmpBI1($whereCondition, $lastYear, $year, $nextYear);
        $this->insertTableTmpBI23($whereCondition, $lastYear, $year, $nextYear);
        $this->insertTableTmpBII($whereCondition, $lastYear, $year, $nextYear);
        $this->insertTableTmpBIII($whereCondition, $lastYear, $year, $nextYear);
        $this->insertTableTmpBIV1($whereCondition, $lastYear, $year, $nextYear);
        $this->insertTableTmpBIV2($whereCondition, $lastYear, $year, $nextYear);

        // sum up
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $maxLevelCompany = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.LevelCompany) AS maxLevel'))->first();
        $createTableTmp1 = "CREATE TEMPORARY TABLE t (SELECT * FROM table_tmp)";
        DB::statement($createTableTmp1);
        $this->sumUpTableTmp(1, $maxLevel);
        if(count($listChildrenCompany) > 1){
            $this->sumParent($maxLevelCompany,$company);
            if($company->IsFinancialCompany){
                $this->sumFinalcialCompany($company);
            }
        }

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        if(!is_null($levelCompany)){
            $data = DB::table("table_tmp")->orderBy('Norder','ASC')->orderBy('ItemNo','ASC')->where('LevelCompany','<=',$levelCompany)->get();
        }
        else{
            $data = DB::table("table_tmp")->orderBy('Norder','ASC')->orderBy('ItemNo','ASC')->where('CompanyID','=',$company->CompanyID)->get();
        }
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $lastYear;
            $data[0]->HeaderName2 = 'Năm ' . $year;
            $data[0]->HeaderName3 = 'Dự toán năm ' .$nextYear;
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
                "ReportName" => "DỰ TOÁN THU, CHI NGÂN SÁCH NHÀ NƯỚC NĂM " . $nextYear,
                "DateTime" => $dateTime,
             //   "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function formatInsertArr($stt, $itemID, $itemNo, $parentID, $level, $itemName, $fontWeight, $italic, $center,
                                    $sbiCategoryNo, $sbiCategoryValue, $normCateValue, $expenseCateValue,
                                    $programCateValue, $projectNo, $company, $norder = 2){
        return [
            'STT' => $stt,
            'ItemID' => $itemID,
            'ItemNo'=> $itemNo,
            'ParentID' => $parentID,
            'Level' => $level,
            'ItemName' => $itemName,
            'FontWeight' => $fontWeight,
            'Italic' => $italic,
            'Center' => $center,
            'SbiCategoryNo' => $sbiCategoryNo,
            'SbiCategoryValue' => $sbiCategoryValue,
            'NormCateValue' => $normCateValue,
            'ExpenseCateValue' => $expenseCateValue,
            'ProgramCateValue'=> $programCateValue,
            'ProjectNo'=> $projectNo,
            'I1' => 0,
            'I2' => 0,
            'I3' => 0,
            'I4' => 0,
            'CompanyID' => $company->CompanyID,
            'ParentCompanyID'=> $company->ParentID,
            'LevelCompany'=> $company->Level,
            'Detail'=> $company->Detail,
            'Norder'=> $norder,
        ];
    }

    public function insertDetail($listChildrenCompany,$company){
        $insertArr = [];
        $maxLevelDetail = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.LevelCompany) AS maxLevel'))->where('Detail','=',1)->first();
        $listParent = array_filter($listChildrenCompany, function ($var){
            return ($var->Detail == 0);
        });
        if($maxLevelDetail->maxLevel > 1){
            $listChild = array_filter($listChildrenCompany, function ($var){
                return ($var->Detail == 1);
            });
        }
        else{
            $listChild = $listChildrenCompany;
        }

        $arrCompanyID = [];
        foreach ($listChild as $item){
            if($item->Detail == 1){
                $arrCompanyID[] = $item->CompanyID;
            }
        }
        if(!empty($arrCompanyID)){
            $project1 = DB::table('act_gl_books')->select('company.*','ProjectNo','ProjectName')
                ->join('program_cate','act_gl_books.ProgramID','=','program_cate.ProgramID')
                ->join('company','act_gl_books.InputCompanyID','=','company.CompanyID')
                ->whereIn('act_gl_books.AccountNo',['0461','0441','0721','0411'])
                ->whereIn('InputCompanyID',$arrCompanyID)->where('program_cate.CateNo','=','001')
                ->where('program_cate.CateValue','=','1')
                ->groupBy('act_gl_books.ProjectID','company.CompanyID')->get();

            $project2 = DB::table('act_gl_books')->select('company.*','ProjectNo','ProjectName')
                ->join('program_cate','act_gl_books.ProgramID','=','program_cate.ProgramID')
                ->join('company','act_gl_books.InputCompanyID','=','company.CompanyID')
                ->whereIn('act_gl_books.AccountNo',['0461','0441','0721','0411'])
                ->whereIn('InputCompanyID',$arrCompanyID)->where('program_cate.CateNo','=','001')
                ->where('program_cate.CateValue','=','2')
                ->groupBy('act_gl_books.ProjectID','company.CompanyID')->get();
            if($project1){
                foreach ($project1 as $key1 => $item){
                    $itemID = 'B#04#01.' . $item->ProjectNo;
                    $itemNo = $item->CompanyNo . '.B#04#01#'. sprintf("%02d",($key1 + 1)) . $item->ProjectNo;
                    $itemName = $item->ProjectName;
                    if(substr($item->CompanyNo, 0 ,3* $company->Level) === substr($company->CompanyNo, 0 ,3* $company->Level) && $company->IsFinancialCompany == 1){
                        $norder = 1;
                    }
                    else{
                        $norder = 2;
                    }
                    $insertArr[] = $this->formatInsertArr('',$itemID, $itemNo,'B#04#01',4,$itemName, 0, 0, 0, null, null, null, null, 1, $item->ProjectNo, $item, $norder);
                    $insertArr[] = $this->formatInsertArr('-',$itemID.'#01',$itemNo . '#01', $itemID,5,'Chi đầu tư phát triển', 0, 0, 0, null, null, null, null, 1, $item->ProjectNo, $item, $norder);
                    $insertArr[] = $this->formatInsertArr('-',$itemID.'#02',$itemNo . '#02', $itemID, 5,'Chi thường xuyên', 0, 0, 0, null, null, null, null, 1, $item->ProjectNo, $item, $norder);
                }
            }
            if($project2){
                foreach ($project2 as $key2 => $item){
                    $itemID = 'B#04#02.' . $item->ProjectNo;
                    $itemNo = $item->CompanyNo . '.B#04#02#'. sprintf("%02d",($key2 + 1)) . $item->ProjectNo;
                    $itemName = $item->ProjectName;
                    if(substr($item->CompanyNo, 0 ,3* $company->Level) === substr($company->CompanyNo, 0 ,3* $company->Level) && $company->IsFinancialCompany == 1) {
                        $norder = 1;
                    }
                    else{
                        $norder = 2;
                    }
                    $insertArr[] = $this->formatInsertArr('',$itemID, $itemNo,'B#04#02',4,$itemName, 0, 0, 0, null, null, null, null, 2, $item->ProjectNo, $item, $norder);
                    $insertArr[] = $this->formatInsertArr('-',$itemID.'#01',$itemNo . '#01',$itemID,5,'Chi đầu tư phát triển', 0, 0, 0, null, null, null, null, 2, $item->ProjectNo, $item, $norder);
                    $insertArr[] = $this->formatInsertArr('-',$itemID.'#02', $itemNo . '#02',$itemID,5,'Chi thường xuyên', 0, 0, 0, null, null, null, null, 2, $item->ProjectNo, $item, $norder);
                }
            }
        }

        $maxLevelNotDetail = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.LevelCompany) AS maxLevel'))->where('Detail','=',0)->first();
        if($maxLevelNotDetail->maxLevel !== null){
            for($i = $maxLevelNotDetail->maxLevel ; $i>= $company->Level; $i--){
                foreach ($listParent as $key => $value){
                    $test1 = [];
                    if($i == $value->Level){
                        $test = array_filter($insertArr, function ($var) use ($value){
                            return ($var['ParentCompanyID'] == $value->CompanyID);
                        });
                        $test1 = array_map(function($item) use($value, $company){
                            $itemNo = explode('.',$item['ItemNo']);
                            $item['ItemNo'] = $value->CompanyNo. '.' . $itemNo[1];
                            $item['CompanyID'] = $value->CompanyID;
                            $item['LevelCompany'] = $value->Level;
                            $item['ParentCompanyID'] = $value->ParentID;
                            $item['Detail'] = $value->Detail;
                            if(substr($value->CompanyNo, 0 ,3 * $company->Level) === substr($company->CompanyNo, 0 ,3  * $company->Level) && $company->IsFinancialCompany == 1){
                                $item['Norder'] = 1;
                            }
                            else{
                                $item['Norder'] = 2;
                            }
                            return $item;
                        },$test);
                    }
                    $insertArr = array_merge($insertArr, $test1);
                }
            }
        }

        // Loai bo trùng nhau
        $uniqueSbiItems = [];
        $tmpUids = [];
        $listItemIDFinancial = [];
        $listProjectFinancial = [];
        $listProjectNotFinancial = [];
        foreach ($insertArr as $insert) {
            if (!in_array($insert['CompanyID'] . '.' . $insert['ItemID'], $tmpUids)) {
                $tmpUids[] = $insert['CompanyID'] . '.' . $insert['ItemID'];
                $uniqueSbiItems[] = $insert;
                if($insert['CompanyID'] == $company->CompanyID && $company->IsFinancialCompany == 1){
                    $listItemIDFinancial[] = $insert['ItemID'];
                }
                if($insert['CompanyID'] != $company->CompanyID && $insert['LevelCompany'] == 1  && $company->IsFinancialCompany == 1){
                    $listProjectNotFinancial[] = $insert;
                }
            }
        }

        if($company->IsFinancialCompany == 1){
            foreach($listProjectNotFinancial as $item){
                if (!in_array($item['ItemID'], $listItemIDFinancial)) {
                    $itemNo = explode('.',$item['ItemNo']);
                    $item['ItemNo'] = $company->CompanyNo. '.' . $itemNo[1];
                    $item['CompanyID'] = $company->CompanyID;
                    $item['LevelCompany'] = $company->Level;
                    $item['ParentCompanyID'] = $company->ParentID;
                    $item['Norder'] = 1;
                    $listProjectFinancial[] = $item;
                }
            }
            $uniqueSbiItems = array_merge($uniqueSbiItems, $listProjectFinancial);
        }

        DB::table('table_tmp')->insert($uniqueSbiItems);
    }

    // SELECT
    public function selectA2($whereCondition){
        $sql = "SELECT PathCompanyID
            FROM act_gl_books AS A
            INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
            INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
            INNER JOIN capital_cate AS CC ON A.CapitalID = CC.CapitalID
            INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
            WHERE RC.CateNo = '2011104'
            AND RC.CateValue = 1
            AND LEFT(NC.CateNo, 7) = '2015112'
            AND CC.CateNo = '200211'
            AND SC.CateNo = '001'
            AND A.AccountNo IN ('0362', '0342', '0712', '0312')
            $whereCondition
            GROUP BY PathCompanyID";
            return DB::select($sql);
    }
    public function selectA3($whereCondition){
        $sql = "SELECT PathCompanyID
            FROM act_gl_books AS A
            INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
            INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
            WHERE RC.CateNo = '2011104'
            AND RC.CateValue = '1'
            AND LEFT(A.CapitalNo, 3) = '211'
            AND ((NC.CateNo = '2011111' AND NC.CateValue = 1) OR (NC.CateNo = '2011112' AND NC.CateValue = 2))
            AND A.AccountNo IN ('0361', '0341', '0711', '0311')
            $whereCondition
            GROUP BY PathCompanyID";
        return DB::select($sql);
    }
    public function selectBI1($whereCondition){
        $sql = "SELECT PathCompanyID
            FROM act_gl_books AS A
            INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
            INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
            INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
            WHERE LEFT(EC.CateNo, 7) = '2015201'
            AND (NC.CateNo = '201511112' AND NC.CateValue = 1)
            AND A.AccountNo IN ('0461', '0441', '0721', '0411')
            AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
            $whereCondition
            GROUP BY PathCompanyID";
        return DB::select($sql);
    }
    public function selectBI23($whereCondition){
        $sql = "SELECT PathCompanyID
            FROM act_gl_books AS A
            INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
            INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
            WHERE (LEFT(EC.CateNo, 7) = '2015202' OR LEFT(EC.CateNo, 7) = '2015203')
            AND NC.CateNo = '201511112' AND (NC.CateValue = 2 OR NC.CateValue = 3)
            AND A.AccountNo IN ('0461', '0441', '0721', '0411')
            $whereCondition
            GROUP BY PathCompanyID";
        return DB::select($sql);
    }
    public function selectBII($whereCondition){
        $sql = "SELECT PathCompanyID
            FROM act_gl_books AS A
            INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
            INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
            WHERE LEFT(EC.CateNo, 5) = '20155'
            AND NC.CateNo = '201511113' AND NC.CateValue = 1
            AND A.AccountNo IN ('0461', '0441', '0721', '0411')
            $whereCondition
            GROUP BY PathCompanyID";
        return DB::select($sql);
    }
    public function selectBIII($whereCondition){
        $sql = "SELECT PathCompanyID
        FROM act_gl_books AS A
        INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
        INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
        WHERE LEFT(EC.CateNo, 5) = '20151'
        AND LEFT(NC.CateNo, 7) = '2015111'
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND SC.CateNo = '001'
        AND A.AccountNo IN ('0461', '0441', '0721', '0411')
        $whereCondition
        GROUP BY PathCompanyID";
        return DB::select($sql);
    }
    public function selectBIV1($whereCondition){
        $sql = "SELECT PathCompanyID
        FROM act_gl_books AS A
        INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
        INNER JOIN program_cate AS PC ON A.ProgramID = PC.ProgramID
        WHERE LEFT(EC.CateNo, 5) = '20152'
        AND NC.CateNo = '201511112' AND NC.CateValue = '1'
        AND A.AccountNo IN ('0461', '0441', '0721', '0411')
        AND PC.CateNo = '001' AND PC.CateValue IN ('1','2')
        $whereCondition
        GROUP BY PathCompanyID";
        return DB::select($sql);
    }
    public function selectBIV2($whereCondition){
        $sql = "SELECT PathCompanyID
        FROM act_gl_books AS A
        INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
        INNER JOIN program_cate AS PC ON A.ProgramID = PC.ProgramID
        WHERE LEFT(EC.CateNo, 5) = '20151'
        AND LEFT(NC.CateNo, 7) = '2015111'
        AND A.AccountNo IN ('0461', '0441', '0721', '0411')
        AND PC.CateNo = '001' AND PC.CateValue IN ('1','2')
        $whereCondition
        GROUP BY PathCompanyID";
        return DB::select($sql);
    }


    public function insertTableTmpA2($whereCondition, $lastYear, $year, $nextYear){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT CompanyID, SbiCategoryValue,
            MAX(CASE WHEN AccountNo = '0362' AND B.YearD = $lastYear THEN I ELSE 0 END) AS I1,
            MAX(CASE WHEN AccountNo = '0342' AND B.YearD = $year THEN I ELSE 0 END) AS I2,
            MAX(CASE WHEN AccountNo = '0712' AND B.YearD = $year THEN I ELSE 0 END) AS I3,
            MAX(CASE WHEN AccountNo = '0312' AND B.YearD = $nextYear THEN I ELSE 0 END) AS I4
            FROM (SELECT SUM(FCDebitAmount) AS I, A.InputCompanyID AS CompanyID, A.AccountNo, SC.CateValue AS SbiCategoryValue, YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
            INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
            INNER JOIN capital_cate AS CC ON A.CapitalID = CC.CapitalID
            INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
            WHERE RC.CateNo = '2011104'
            AND RC.CateValue = 1
            AND LEFT(NC.CateNo, 7) = '2015112'
            AND CC.CateNo = '200211'
            AND SC.CateNo = '001'
            AND A.AccountNo IN ('0362', '0342', '0712', '0312')
            $whereCondition
            GROUP BY A.InputCompanyID, YearD, A.AccountNo, SbiCategoryValue) AS B
            GROUP BY CompanyID, SbiCategoryValue) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.CompanyID = HH.CompanyID
            AND TT.SbiCategoryValue = HH.SbiCategoryValue
            AND TT.ParentID = 'A#02' AND Detail = 1";

        DB::statement($sqlUpdate);
    }
    public function insertTableTmpA3($whereCondition, $lastYear, $year, $nextYear){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT CompanyID,
            MAX(CASE WHEN AccountNo = '0361' AND B.YearD = $lastYear THEN I ELSE 0 END) AS I1,
            MAX(CASE WHEN AccountNo = '0341' AND B.YearD = $year THEN I ELSE 0 END) AS I2,
            MAX(CASE WHEN AccountNo = '0711' AND B.YearD = $year THEN I ELSE 0 END) AS I3,
            MAX(CASE WHEN AccountNo = '0311' AND B.YearD = $nextYear THEN I ELSE 0 END) AS I4
            FROM (SELECT SUM(FCDebitAmount) AS I, A.InputCompanyID AS CompanyID, A.AccountNo, YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
            INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
            WHERE RC.CateNo = '2011104' AND RC.CateValue = '1'
            AND ((NC.CateNo = '2011111' AND NC.CateValue = 1) OR (NC.CateNo = '2011112' AND NC.CateValue = 2))
            AND A.AccountNo IN ('0361', '0341', '0711', '0311')
            $whereCondition
            GROUP BY YearD, A.AccountNo, A.InputCompanyID) AS B GROUP BY CompanyID) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = 'A#03' AND TT.CompanyID = HH.CompanyID AND Detail = 1";

        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBI1($whereCondition, $lastYear, $year, $nextYear){
        $sqlUpdate = "UPDATE table_tmp AS TT,
            (SELECT SbiCategoryValue, CompanyID,
            MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $lastYear THEN I ELSE 0 END) AS I1,
            MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $year THEN I ELSE 0 END) AS I2,
            MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $year THEN I ELSE 0 END) AS I3,
            MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $nextYear THEN I ELSE 0 END) AS I4
            FROM (SELECT SUM(FCDebitAmount) AS I, A.InputCompanyID AS CompanyID, A.AccountNo, SC.CateValue AS SbiCategoryValue,YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
            INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
            INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
            WHERE LEFT(EC.CateNo, 7) = '2015201'
            AND (NC.CateNo = '201511112' AND NC.CateValue = 1)
            AND A.AccountNo IN ('0461', '0441', '0721', '0411')
            AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
            $whereCondition
            GROUP BY YearD, A.AccountNo, SbiCategoryValue, A.InputCompanyID) AS B
                GROUP BY SbiCategoryValue, CompanyID) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.CompanyID = HH.CompanyID AND TT.SbiCategoryValue = HH.SbiCategoryValue
            AND TT.ParentID = 'B#01#01' AND Detail = 1";

        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBI23($whereCondition, $lastYear, $year, $nextYear){
        $sqlUpdate = "UPDATE table_tmp AS TT,
            (SELECT NormCateValue, ExpenseCateValue, CompanyID,
            MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $lastYear THEN I ELSE 0 END) AS I1,
            MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $year THEN I ELSE 0 END) AS I2,
            MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $year THEN I ELSE 0 END) AS I3,
            MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $nextYear THEN I ELSE 0 END) AS I4
            FROM (SELECT SUM(FCDebitAmount) AS I, A.InputCompanyID AS CompanyID, A.AccountNo, YEAR(A.PeriodFromDate) AS YearD, NC.CateValue AS NormCateValue, EC.CateValue AS ExpenseCateValue
            FROM act_gl_books AS A
            INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
            INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
            WHERE (LEFT(EC.CateNo, 7) = '2015202' OR LEFT(EC.CateNo, 7) = '2015203')
            AND NC.CateNo = '201511112' AND (NC.CateValue = 2 OR NC.CateValue = 3)
            AND A.AccountNo IN ('0461', '0441', '0721', '0411')
            $whereCondition
            GROUP BY YearD, A.AccountNo, NormCateValue, ExpenseCateValue, A.InputCompanyID) AS B
            GROUP BY NormCateValue, CompanyID) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.CompanyID = HH.CompanyID AND TT.NormCateValue = HH.NormCateValue
            AND TT.ExpenseCateValue = HH.ExpenseCateValue AND TT.ParentID = 'B#01' AND Detail = 1";

        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBII($whereCondition, $lastYear, $year, $nextYear){
        $sqlUpdate = "UPDATE table_tmp AS TT,
            (SELECT CompanyID,
            MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $lastYear THEN I ELSE 0 END) AS I1,
            MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $year THEN I ELSE 0 END) AS I2,
            MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $year THEN I ELSE 0 END) AS I3,
            MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $nextYear THEN I ELSE 0 END) AS I4
            FROM (SELECT SUM(FCDebitAmount) AS I, A.InputCompanyID AS CompanyID, A.AccountNo, YEAR(A.PeriodFromDate) AS YearD
            FROM act_gl_books AS A
            INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
            INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
            WHERE LEFT(EC.CateNo, 5) = '20155'
            AND NC.CateNo = '201511113' AND NC.CateValue = 1
            AND A.AccountNo IN ('0461', '0441', '0721', '0411')
            $whereCondition
            GROUP BY YearD, A.AccountNo, A.InputCompanyID) AS B GROUP BY CompanyID) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.CompanyID = HH.CompanyID AND TT.ItemID = 'B#02' AND Detail = 1";

        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIII($whereCondition, $lastYear, $year, $nextYear){
        $sqlUpdate = "UPDATE table_tmp AS TT,
        (SELECT SbiCategoryValue, CompanyID,
        MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $lastYear THEN I ELSE 0 END) AS I1,
        MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $year THEN I ELSE 0 END) AS I2,
        MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $year THEN I ELSE 0 END) AS I3,
        MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $nextYear THEN I ELSE 0 END) AS I4
        FROM (SELECT SUM(FCDebitAmount) AS I, A.InputCompanyID AS CompanyID, A.AccountNo, SC.CateValue AS SbiCategoryValue, YEAR(A.PeriodFromDate)AS YearD
        FROM act_gl_books AS A
        INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
        INNER JOIN sbi_category_cate AS SC ON SC.SbiCategoryID = A.SbiCategoryID
        WHERE LEFT(EC.CateNo, 5) = '20151'
        AND LEFT(NC.CateNo, 7) = '2015111'
        AND (A.ProgramNo IS NULL OR A.ProgramNo = '')
        AND SC.CateNo = '001'
        AND A.AccountNo IN ('0461', '0441', '0721', '0411')
        $whereCondition
        GROUP BY YearD, A.AccountNo, SbiCategoryValue, A.InputCompanyID
        ) AS B
        GROUP BY SbiCategoryValue, CompanyID) AS HH
        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE TT.CompanyID = HH.CompanyID AND TT.SbiCategoryValue = HH.SbiCategoryValue
        AND TT.ParentID = 'B#03' AND Detail = 1";

        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV1($whereCondition, $lastYear, $year, $nextYear){
        $sqlUpdate = "UPDATE table_tmp AS TT,
        (SELECT CompanyID, ProgramCateValue,ProjectNo,
        MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $lastYear THEN I ELSE 0 END) AS I1,
        MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $year THEN I ELSE 0 END) AS I2,
        MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $year THEN I ELSE 0 END) AS I3,
        MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $nextYear THEN I ELSE 0 END) AS I4
        FROM (SELECT SUM(FCDebitAmount) AS I, A.InputCompanyID AS CompanyID, PC.CateValue AS ProgramCateValue, A.ProjectNo, A.AccountNo, YEAR(A.PeriodFromDate) AS YearD
        FROM act_gl_books AS A
        INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
        INNER JOIN program_cate AS PC ON A.ProgramID = PC.ProgramID
        WHERE LEFT(EC.CateNo, 5) = '20152'
        AND NC.CateNo = '201511112' AND NC.CateValue = '1'
        AND A.AccountNo IN ('0461', '0441', '0721', '0411')
        AND PC.CateNo = '001' AND PC.CateValue IN ('1','2')
        $whereCondition
        GROUP BY YearD, A.AccountNo, A.InputCompanyID, PC.CateValue, A.ProjectNo) AS B
        GROUP BY CompanyID, ProgramCateValue,ProjectNo) AS HH
        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE (TT.ItemID = CONCAT('B#04#01.',HH.ProjectNo,'#01') OR TT.ItemID = CONCAT('B#04#02.',HH.ProjectNo,'#01'))
        AND TT.CompanyID = HH.CompanyID AND TT.ProjectNo = HH.ProjectNo
        AND TT.ProgramCateValue = HH.ProgramCateValue AND TT.Detail = 1";

        DB::statement($sqlUpdate);
    }
    public function insertTableTmpBIV2($whereCondition, $lastYear, $year, $nextYear){
        $sqlUpdate = "UPDATE table_tmp AS TT,
        (SELECT CompanyID, ProgramCateValue,ProjectNo,
        MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $lastYear THEN I ELSE 0 END) AS I1,
        MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $year THEN I ELSE 0 END) AS I2,
        MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $year THEN I ELSE 0 END) AS I3,
        MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $nextYear THEN I ELSE 0 END) AS I4
        FROM (SELECT SUM(FCDebitAmount) AS I, A.InputCompanyID AS CompanyID, PC.CateValue AS ProgramCateValue, A.ProjectNo, A.AccountNo, YEAR(A.PeriodFromDate)AS YearD
        FROM act_gl_books AS A
        INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
        INNER JOIN program_cate AS PC ON A.ProgramID = PC.ProgramID
        WHERE LEFT(EC.CateNo, 5) = '20151'
        AND LEFT(NC.CateNo, 7) = '2015111'
        AND A.AccountNo IN ('0461', '0441', '0721', '0411')
        AND PC.CateNo = '001' AND PC.CateValue IN ('1','2')
        $whereCondition
        GROUP BY YearD, A.AccountNo, A.InputCompanyID, PC.CateValue, A.ProjectNo) AS B
        GROUP BY CompanyID, ProgramCateValue,ProjectNo) AS HH
        SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE (TT.ItemID = CONCAT('B#04#01.',HH.ProjectNo,'#02') OR TT.ItemID = CONCAT('B#04#02.',HH.ProjectNo,'#02'))
        AND TT.CompanyID = HH.CompanyID AND TT.ProjectNo = HH.ProjectNo
        AND TT.ProgramCateValue = HH.ProgramCateValue AND TT.Detail = 1";

        DB::statement($sqlUpdate);
    }

    // sum up
    public function sumUpTableTmp($start, $maxLevel){
        for ($i = $maxLevel->maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "UPDATE table_tmp AS TT, (SELECT CompanyID,ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
            FROM t M
            WHERE M.Level = $i GROUP BY CompanyID,ParentID) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = HH.ParentID AND TT.Detail = 1
            AND TT.Level = " . $j . " AND TT.CompanyID = HH.CompanyID" ;
            DB::statement($sql);

            $sql1 = "UPDATE t AS TT, (SELECT CompanyID,ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
            FROM table_tmp M
            WHERE M.Level = $i GROUP BY CompanyID,ParentID) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = HH.ParentID AND TT.Detail = 1
            AND TT.Level = " . $j . " AND TT.CompanyID = HH.CompanyID" ;
            DB::statement($sql1);
            if($i == 3){
                $this->sumA1();
            }
        }
    }

    public function sumParent($maxLevelCompany,$company){
        for ($i = $maxLevelCompany->maxLevel; $i > $company->Level; $i--){
            $j = $i - 1;
            $sql = "UPDATE table_tmp as TT, (SELECT ParentCompanyID ,ItemID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
                FROM t M
                WHERE M.LevelCompany =  $i GROUP BY ParentCompanyID, ItemID) as HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                    WHERE TT.ItemID = HH.ItemID
                    AND TT.LevelCompany = $j AND TT.CompanyID = HH.ParentCompanyID";
            DB::statement($sql);
            $sql1 = "UPDATE t as TT, (SELECT ParentCompanyID ,ItemID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
                FROM table_tmp M
                WHERE M.LevelCompany =  $i GROUP BY ParentCompanyID, ItemID) as HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                    WHERE TT.ItemID = HH.ItemID
                    AND TT.LevelCompany = $j AND TT.CompanyID = HH.ParentCompanyID";
            DB::statement($sql1);
        }
    }

    public function sumFinalcialCompany($company){
        $sql = "UPDATE table_tmp as TT,
        (SELECT ItemID, SUM(I1) as I1, SUM(I2) as I2,SUM(I3) as I3, SUM(I4) as I4
        FROM t WHERE CompanyID != $company->CompanyID AND LevelCompany = $company->Level GROUP BY ItemID) as HH
        SET TT.I1 = TT.I1 + HH.I1, TT.I2 = TT.I2 + HH.I2, TT.I3 = TT.I3 + HH.I3, TT.I4 = TT.I4 + HH.I4
        WHERE TT.ItemID = HH.ItemID
        AND TT.LevelCompany = $company->Level AND TT.CompanyID = " .$company->CompanyID;
        DB::statement($sql);
    }

    public function sumA1(){
        $sql = "UPDATE table_tmp as TT, (SELECT SUM(I1) as I1, SUM(I2) as I2,SUM(I3) as I3, SUM(I4) as I4, CompanyID FROM t
                WHERE (ItemID = 'A#02' OR ItemID = 'A#03')
                AND Detail = 1
				GROUP BY CompanyID) as HH
                SET TT.I1 = HH.I1, TT.I2 = HH.I2,TT.I3 =HH.I3, TT.I4 = HH.I4
                WHERE TT.ItemID = 'A#01' AND TT.Detail = 1 AND TT.CompanyID = HH.CompanyID";
        DB::statement($sql);

        $sql1 = "UPDATE t as TT, (SELECT SUM(I1) as I1, SUM(I2) as I2,SUM(I3) as I3, SUM(I4) as I4, CompanyID FROM table_tmp
                WHERE (ItemID = 'A#02' OR ItemID = 'A#03')
                AND Detail = 1
				GROUP BY CompanyID) as HH
                SET TT.I1 = HH.I1, TT.I2 = HH.I2,TT.I3 =HH.I3, TT.I4 = HH.I4
                WHERE TT.ItemID = 'A#01' AND TT.Detail = 1 AND TT.CompanyID = HH.CompanyID";
        DB::statement($sql1);
    }

}
