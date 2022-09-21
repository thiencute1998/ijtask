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

class SBP3422016TTBTCMB06Controller extends Controller
{

    public function SBP3422016TTBTC_MB06(Request $request)
    {
        //
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : 2021;
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
            $company = Company::where('CompanyID',$filter['CompanyID'])->first();
        }
        else{
            $company = Company::where('CompanyID',Auth::user()->CompanyID)->first();
        }

        $companyName = $company->CompanyName;
        if(!isset($filter['LevelCompany']) || $filter['LevelCompany'] === null){
            $levelCompany = $company->Level;
        }
        else{
            $levelCompany = $filter['LevelCompany'];
        }
        $leftNumber = 3 * $company->Level;
        $subCompanyNo = substr($company->CompanyNo, 0,3 * $company->Level);
        $sql = "SELECT * FROM Company WHERE LEFT(CompanyNo,$leftNumber) = $subCompanyNo";
        $listChildrenCompany = DB::select($sql);
        if($company->IsFinancialCompany){
            $sameLevelCompany = Company::where('IsFinancialCompany','=',0)->where('Level','=',1)->where(DB::raw('LENGTH(CompanyNo)'),'=',15)->get();
            if($listChildrenCompany){
                foreach ($sameLevelCompany as $item){
                    $subItemNo = substr($item->CompanyNo, 0,3);
                    $sql1 = "SELECT * FROM Company WHERE LEFT(CompanyNo,3) = $subItemNo";
                    $arr = DB::select($sql1);
                    $listChildrenCompany = array_merge($listChildrenCompany,$arr);
                }
            }
        }

        $uniqueSbiItems = [];
        $tmpUids = [];
        foreach ($listChildrenCompany as $insert) {
            if (!in_array($insert->CompanyID, $tmpUids)) {
                $tmpUids[] = $insert->CompanyID;
                $uniqueSbiItems[] = $insert;
            }
        }

        $listChildrenCompany = $uniqueSbiItems;
        $nextYear = (int)$year + 1;
        $whereCondition = "AND (YEAR(A.PostDate) = $year" . " OR YEAR(A.PostDate) = $nextYear)";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
            `TableID` int(11) NOT NULL AUTO_INCREMENT,
            `STT` varchar(10) DEFAULT '',
            `ItemID` varchar(100) DEFAULT '',
            INDEX(ItemID),
            `ItemNo` varchar(100) DEFAULT '',
            `ParentID` varchar(100) DEFAULT '',
            INDEX(ParentID),
            `Level` int(2) DEFAULT 1,
            INDEX(Level),
            `ItemName` varchar(255) DEFAULT NULL,
            `FontWeight` tinyint(1) DEFAULT 0,
            `Italic` tinyint(1) DEFAULT 0,
            `Center` tinyint(1) DEFAULT 0,
            `NormCateValue` varchar(20) DEFAULT '',
            `SbiCategoryCateValue` varchar(20) DEFAULT '',
            `CompanyID` int(11) DEFAULT NULL,
            `ParentCompanyID` int(11) DEFAULT NULL,
            `LevelCompany` int(11) DEFAULT NULL,
            `I1` double(20,4) DEFAULT NULL,
            `I2` double(20,4) DEFAULT NULL,
            `Detail` int(11) DEFAULT NULL,
            `ProjectNo` varchar(50) DEFAULT '',
            `ProgramCateValue` int(11) DEFAULT NULL,
            `Norder` int(11) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb06')->get();
        $insertArr = [];
        foreach ($listChildrenCompany as $index=> $item){
            foreach ($templateTable as $key=> $template) {
                if(substr($item->CompanyNo, 0 ,3) === substr($company->CompanyNo, 0 ,3) && $company->IsFinancialCompany == 1){
                    if($key == 0){
                        $insertArr[] = $this->formatInsertDetail1($item,$template->STT,$template->ItemID, $item->CompanyNo,$template->ParentID,$template->Level,$item->CompanyName, $template->FontWeight, $template->Italic, $template->Center, $template->NormCateValue, $template->SbiCategoryCateValue, 1);
                    }
                    else{
                        $insertArr[] = $this->formatInsertDetail1($item,$template->STT,$template->ItemID, $item->CompanyNo . '.' . $template->ItemID,$template->ParentID,$template->Level,$template->ItemName, $template->FontWeight, $template->Italic, $template->Center, $template->NormCateValue, $template->SbiCategoryCateValue, 1);
                    }
                }
                else{
                    if($key == 0){
                        $insertArr[] = $this->formatInsertDetail1($item,$template->STT,$template->ItemID, $item->CompanyNo,$template->ParentID,$template->Level,$item->CompanyName, $template->FontWeight, $template->Italic, $template->Center, $template->NormCateValue, $template->SbiCategoryCateValue);
                    }
                    else{
                        $insertArr[] = $this->formatInsertDetail1($item,$template->STT,$template->ItemID, $item->CompanyNo . '.' . $template->ItemID,$template->ParentID,$template->Level,$template->ItemName, $template->FontWeight, $template->Italic, $template->Center, $template->NormCateValue, $template->SbiCategoryCateValue);
                    }
                }
            }
        }

        $chunks = array_chunk($insertArr, 1000);
        foreach ($chunks as $chunk){
            DB::table('table_tmp')->insert($chunk);
        }

        $this->insertDetailByCompany($listChildrenCompany,$company);

        $this->insertA2Detail($whereCondition,$year,$nextYear);
        $this->insertB1_1Detail($whereCondition,$year,$nextYear);
        $this->insertB3Detail($whereCondition,$year,$nextYear);
        $this->insertA3($whereCondition,$year,$nextYear);
        $this->insertB1_2and3($whereCondition,$year,$nextYear);
        $this->insertB2($whereCondition,$year,$nextYear);
        $this->insertB4_1Detail($whereCondition,$year,$nextYear);
        $this->insertB4_2Detail($whereCondition,$year,$nextYear);

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $maxLevelCompany = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.LevelCompany) AS maxLevel'))->first();
        $this->sumUpColumn(1,2, $maxLevel);
        if(count($listChildrenCompany) > 1){
            $this->sumParent($maxLevelCompany,$company);
            if($company->IsFinancialCompany){
                $this->sumFinalcialCompany($company);
            }
        }

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('Norder','ASC')->orderBy('ItemNo','ASC')->where('LevelCompany','<=',$levelCompany)->where(function($query){
            $query->where('I1','!=',0)
                ->orWhere('I2','!=',0)
                ->orWhere('ItemID','=','X');
        })->get();

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Ước thực hiện năm ' . $year. '(năm hiện hành)';
            $data[0]->HeaderName2 = 'Dự toán năm ' .$nextYear. '(năm kế hoạch)';
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
                "ReportName" => "TỔNG HỢP DỰ TOÁN THU NGÂN SÁCH NHÀ NƯỚC THEO SẮC THUẾ NĂM " . $year,
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

    public function insertDetailByCompany($listChildrenCompany,$company){
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
            $arrCompanyID[] = $item->CompanyID;
        }
        if(!empty($arrCompanyID)){
            $project1 = DB::table('act_gl_books')->select('company.*','ProjectNo','ProjectName')
                ->join('program_cate','act_gl_books.ProgramID','=','program_cate.ProgramID')
                ->join('company','act_gl_books.InputCompanyID','=','company.CompanyID')
                ->whereIn('InputCompanyID',$arrCompanyID)->where('program_cate.CateNo','=','001')
                ->where('program_cate.CateValue','=','1')
                ->groupBy('act_gl_books.ProjectID')->get();

            $project2 = DB::table('act_gl_books')->select('company.*','ProjectNo','ProjectName')
                ->join('program_cate','act_gl_books.ProgramID','=','program_cate.ProgramID')
                ->join('company','act_gl_books.InputCompanyID','=','company.CompanyID')
                ->whereIn('InputCompanyID',$arrCompanyID)->where('program_cate.CateNo','=','001')
                ->where('program_cate.CateValue','=','2')
                ->groupBy('act_gl_books.ProjectID')->get();
            if($project1){
                foreach ($project1 as $key1 => $item){
                    $itemID = 'B#04#01.' . $item->ProjectNo;
                    $itemNo = $item->CompanyNo . '.B#04#01#'. sprintf("%02d",($key1 + 1)) . $item->ProjectNo;
                    $itemName = $item->ProjectName;
                    if(substr($item->CompanyNo, 0 ,3) === substr($company->CompanyNo, 0 ,3) && $company->IsFinancialCompany == 1){
                        $insertArr[] = $this->formatInsertDetail2($item,'',$itemID, $itemNo,'B#04#01',4,$itemName, $item->ProjectNo, null, 1);
                        $insertArr[] = $this->formatInsertDetail2($item,'-',$itemID.'#01',$itemNo . '#01',  $itemID,5,'Chi đầu tư phát triển', $item->ProjectNo, 1,1);
                        $insertArr[] = $this->formatInsertDetail2($item,'-',$itemID.'#02',$itemNo . '#02', $itemID, 5,'Chi thường xuyên', $item->ProjectNo, 1, 1);
                    }
                    else{
                        $insertArr[] = $this->formatInsertDetail2($item,'',$itemID, $itemNo,'B#04#01',4,$itemName, $item->ProjectNo);
                        $insertArr[] = $this->formatInsertDetail2($item,'-',$itemID.'#01',$itemNo . '#01',  $itemID,5,'Chi đầu tư phát triển', $item->ProjectNo, 1);
                        $insertArr[] = $this->formatInsertDetail2($item,'-',$itemID.'#02',$itemNo . '#02', $itemID, 5,'Chi thường xuyên', $item->ProjectNo, 1);
                    }
                }
            }
            if($project2){
                foreach ($project2 as $key2 => $item){
                    $itemID = 'B#04#02.' . $item->ProjectNo;
                    $itemNo = $item->CompanyNo . '.B#04#02#'. sprintf("%02d",($key2 + 1)) . $item->ProjectNo;
                    $itemName = $item->ProjectName;
                    if(substr($item->CompanyNo, 0 ,3) === substr($company->CompanyNo, 0 ,3) && $company->IsFinancialCompany == 1) {
                        $insertArr[] = $this->formatInsertDetail2($item,'',$itemID, $itemNo,'B#04#02',4,$itemName, $item->ProjectNo, null , 1);
                        $insertArr[] = $this->formatInsertDetail2($item,'-',$itemID.'#01',$itemNo . '#01',$itemID,5,'Chi đầu tư phát triển', $item->ProjectNo, 2, 1);
                        $insertArr[] = $this->formatInsertDetail2($item,'-',$itemID.'#02', $itemNo . '#02',$itemID,5,'Chi thường xuyên', $item->ProjectNo, 2,1);
                    }
                    else{
                        $insertArr[] = $this->formatInsertDetail2($item,'',$itemID, $itemNo,'B#04#02',4,$itemName, $item->ProjectNo);
                        $insertArr[] = $this->formatInsertDetail2($item,'-',$itemID.'#01',$itemNo . '#01',$itemID,5,'Chi đầu tư phát triển', $item->ProjectNo, 2);
                        $insertArr[] = $this->formatInsertDetail2($item,'-',$itemID.'#02', $itemNo . '#02',$itemID,5,'Chi thường xuyên', $item->ProjectNo, 2);
                    }
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
                            if(substr($value->CompanyNo, 0 ,3) === substr($company->CompanyNo, 0 ,3) && $company->IsFinancialCompany == 1){
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

    public function formatInsertDetail1($value, $stt, $itemID, $itemNo, $parentID, $level, $itemName, $fontweight = 0, $italic = 0, $center = 0, $normCateValue = null, $sbiCategory = null, $norder = 2){
        return [
            'STT'=> $stt,
            'ItemID'=> $itemID,
            'ItemNo'=> $itemNo,
            'ParentID'=> $parentID,
            'Level'=> $level,
            'ItemName'=> $itemName,
            'FontWeight' => $fontweight,
            'Italic' => $italic,
            'Center' => $center,
            'NormCateValue' => $normCateValue,
            'SbiCategoryCateValue'=> $sbiCategory,
            'CompanyID' => $value->CompanyID,
            'ParentCompanyID'=> $value->ParentID,
            'LevelCompany'=> $value->Level,
            'I1' => null,
            'I2' => null,
            'Detail'=> $value->Detail,
            'ProjectNo'=> null,
            'ProgramCateValue'=> null,
            'Norder'=> $norder,
        ];
    }

    public function formatInsertDetail2($value, $stt, $itemID, $itemNo, $parentID, $level, $itemName, $projectNo = null, $programType = null, $norder = 2){
        return [
            'STT'=> $stt,
            'ItemID'=> $itemID,
            'ItemNo'=> $itemNo,
            'ParentID'=> $parentID,
            'Level'=> $level,
            'ItemName'=> $itemName,
            'FontWeight' => 0,
            'Italic' => 0,
            'Center' => 0,
            'NormCateValue' => null,
            'SbiCategoryCateValue'=> null,
            'CompanyID' => $value->CompanyID,
            'ParentCompanyID'=> $value->ParentID,
            'LevelCompany'=> $value->Level,
            'I1' => null,
            'I2' => null,
            'Detail'=> $value->Detail,
            'ProjectNo'=> $projectNo,
            'ProgramCateValue'=> $programType,
            'Norder'=> $norder,
        ];
    }

    public function insertA2Detail($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID, SbiCategoryCateValue,
			MAX(CASE WHEN AccountNo = '0112' AND YearPostDate = $year THEN I END) AS I1,
			MAX(CASE WHEN AccountNo = '0122' AND YearPostDate = $nextYear THEN I END) as I2 FROM (SELECT A.InputCompanyID as CompanyID, A.AccountNo as AccountNo, S.CateValue as SbiCategoryCateValue, YEAR(A.PostDate) as YearPostDate, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as R on A.RevenueID = R.RevenueID
            INNER JOIN sbi_category_cate as S on A.SbiCategoryID = S.SbiCategoryID
            INNER JOIN norm_cate as N on A.NormID = N.NormID
            WHERE A.InTransTypeID not in (16,17,18)
            AND A.AccountNo IN('0112', '0122')
            AND R.CateNo = '2200' AND S.CateNo = '001'
            AND N.CateNo = '1102' AND N.CateValue = '1'
            $whereCondition
            GROUP BY A.InputCompanyID, A.AccountNo, R.CateNo, S.CateNo, S.CateValue, N.CateNo, N.CateValue, YEAR(A.PostDate)
			) as CH GROUP BY CompanyID, SbiCategoryCateValue) as HH set TT.I1 = HH.I1, TT.I2 = HH.I2
				WHERE TT.SbiCategoryCateValue = HH.SbiCategoryCateValue
				AND TT.ParentID = 'A#02'
				AND TT.CompanyID = HH.CompanyID
                AND TT.Detail = 1";
        DB::statement($sql);
    }

    public function insertB1_1Detail($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID, SbiCategoryCateValue,
			MAX(CASE WHEN AccountNo = '0211' AND YearPostDate = $year THEN I END) AS I1,
			MAX(CASE WHEN AccountNo = '0221' AND YearPostDate = $nextYear THEN I END) as I2 FROM (SELECT A.InputCompanyID AS CompanyID, A.AccountNo as AccountNo, S.CateValue as SbiCategoryCateValue, YEAR(A.PostDate) as YearPostDate, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as E on A.ExpenseID = E.ExpenseID
            INNER JOIN sbi_category_cate as S on A.SbiCategoryID = S.SbiCategoryID
            INNER JOIN program_cate as P on A.ProgramID = P.ProgramID
            INNER JOIN norm_cate as N on A.NormID = N.NormID
            WHERE A.AccountNo IN ('0211','0221') AND E.CateNo = '1201'
            AND S.CateNo = '001' AND P.CateNo != '001'
            AND N.CateNo = '1203' AND N.CateValue = '1'
            AND A.InTransTypeID not in (16,17,18)
            $whereCondition
            GROUP BY A.InputCompanyID, A.AccountNo, E.CateNo, S.CateNo, S.CateValue, P.CateNo, N.CateNo, N.CateValue, YEAR(A.PostDate)
			) AS CH GROUP BY CompanyID, SbiCategoryCateValue) AS HH SET TT.I1 = HH.I1, TT.I2 = HH.I2
            WHERE TT.SbiCategoryCateValue = HH.SbiCategoryCateValue
            AND TT.ParentID = 'B#01#01'
            AND TT.CompanyID = HH.CompanyID
            AND TT.Detail = 1";
        DB::statement($sql);
    }

    public function insertB3Detail($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID, SbiCategoryCateValue,
			MAX(CASE WHEN AccountNo = '0211' AND YearPostDate = $year THEN I END) AS I1,
			MAX(CASE WHEN AccountNo = '0221' AND YearPostDate = $nextYear THEN I END) as I2 FROM (SELECT A.InputCompanyID AS CompanyID, A.AccountNo AS AccountNo, S.CateValue as SbiCategoryCateValue, YEAR(A.PostDate) as YearPostDate, SUM(LCDebitAmount) AS I
        FROM act_gl_books AS A
				INNER JOIN expense_cate as E on A.ExpenseID = E.ExpenseID
				INNER JOIN sbi_category_cate as S on A.SbiCategoryID = S.SbiCategoryID
				INNER JOIN program_cate as P on A.ProgramID = P.ProgramID
				INNER JOIN norm_cate as N on A.NormID = N.NormID
				WHERE A.AccountNo IN ('0211','0221')
				AND LEFT(E.CateNo,4) = '1100'
				AND S.CateNo = '001'
				AND P.CateNo != '001'
				AND N.CateNo = '1201' AND (N.CateValue != '9' OR N.CateValue != '10')
				AND A.InTransTypeID not in (16,17,18)
                $whereCondition
				GROUP BY A.InputCompanyID, A.AccountNo, E.CateNo, S.CateNo, S.CateValue, P.CateNo, N.CateNo, N.CateValue, YEAR(A.PostDate)
			) as CH GROUP BY CompanyID, SbiCategoryCateValue) as HH set TT.I1 = HH.I1, TT.I2 = HH.I2
				WHERE  TT.SbiCategoryCateValue = HH.SbiCategoryCateValue
				AND TT.ParentID = 'B#03'
				AND TT.CompanyID = HH.CompanyID
				AND TT.Detail = 1";
        DB::statement($sql);
    }

    public function insertA3($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID,
			MAX(CASE WHEN AccountNo = '0111' AND YearPostDate = $year THEN I END) AS I1,
			MAX(CASE WHEN AccountNo = '0121' AND YearPostDate = $nextYear THEN I END) as I2 FROM (SELECT A.InputCompanyID as CompanyID, A.AccountNo as AccountNo, YEAR(A.PostDate) as YearPostDate, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN revenue_cate as R on A.RevenueID = R.RevenueID
            INNER JOIN norm_cate as N on A.NormID = N.NormID
            WHERE A.AccountNo IN ('0111','0121')
            AND R.CateNo = '2200'
            AND ((N.CateNo = '1102' AND N.CateValue = '2') OR N.CateNo = '1101')
            AND A.InTransTypeID not in (16,17,18)
            $whereCondition
            GROUP BY A.InputCompanyID, A.AccountNo, R.CateNo, N.CateNo, N.CateValue, YEAR(A.PostDate)
			) as CH GROUP BY CompanyID) as HH set TT.I1 = HH.I1, TT.I2 = HH.I2
            WHERE TT.ItemID = 'A#03'
            AND TT.CompanyID = HH.CompanyID
            AND TT.Detail = 1";
        DB::statement($sql);
    }

    public function insertB1_2and3($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID, NormCateValue,
			MAX(CASE WHEN AccountNo = '0211' AND YearPostDate = $year THEN I END) AS I1,
			MAX(CASE WHEN AccountNo = '0221' AND YearPostDate = $nextYear THEN I END) as I2 FROM (SELECT A.InputCompanyID as CompanyID, A.AccountNo as AccountNo, N.CateValue as NormCateValue, YEAR(A.PostDate) as YearPostDate, SUM(LCDebitAmount) AS I
        FROM act_gl_books AS A
				INNER JOIN expense_cate as E on A.ExpenseID = E.ExpenseID
				INNER JOIN program_cate as P on A.ProgramID = P.ProgramID
				INNER JOIN norm_cate as N on A.NormID = N.NormID
				WHERE A.AccountNo IN ('0211','0221') AND E.CateNo = '1202'
				AND N.CateNo = '1203' AND N.CateValue IN ('2','3')
				AND A.InTransTypeID not in (16,17,18)
                $whereCondition
				GROUP BY A.InputCompanyID, A.AccountNo, E.CateNo, N.CateNo, N.CateValue, YEAR(A.PostDate)
			) as CH GROUP BY CompanyID, NormCateValue) as HH set TT.I1 = HH.I1, TT.I2 = HH.I2
				WHERE TT.NormCateValue = HH.NormCateValue
				AND TT.ParentID = 'B#01'
				AND TT.ItemID != 'B#01#01'
				AND TT.CompanyID = HH.CompanyID
				AND TT.Detail = 1";
        DB::statement($sql);
    }

    public function insertB2($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID,
			MAX(CASE WHEN AccountNo = '0211' AND YearPostDate = $year THEN I END) AS I1,
			MAX(CASE WHEN AccountNo = '0221' AND YearPostDate = $nextYear THEN I END) as I2 FROM (SELECT A.InputCompanyID as CompanyID, A.AccountNo as AccountNo, YEAR(A.PostDate) as YearPostDate, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as E on A.ExpenseID = E.ExpenseID
            INNER JOIN program_cate as P on A.ProgramID = P.ProgramID
            INNER JOIN norm_cate as N on A.NormID = N.NormID
            WHERE A.AccountNo IN ('0211','0221') AND E.CateNo = '1500'
            AND N.CateNo = '2400'
            AND A.InTransTypeID not in (16,17,18)
            $whereCondition
            GROUP BY A.InputCompanyID, A.AccountNo, E.CateNo, N.CateNo, YEAR(A.PostDate)
			) as CH GROUP BY CompanyID) as HH set TT.I1 = HH.I1, TT.I2 = HH.I2
            WHERE TT.ItemID != 'B#02'
            AND TT.CompanyID = HH.CompanyID
            AND TT.Detail = 1";
        DB::statement($sql);
    }

    public function insertB4_1Detail($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID, ProjectNo, ProgramCateValue,
			MAX(CASE WHEN AccountNo = '0211' AND YearPostDate = $year THEN I END) AS I1,
			MAX(CASE WHEN AccountNo = '0221' AND YearPostDate = $nextYear THEN I END) as I2 FROM (SELECT A.InputCompanyID as CompanyID, A.ProjectNo as ProjectNo, A.AccountNo, P.CateValue as ProgramCateValue, YEAR(A.PostDate) as YearPostDate, SUM(LCDebitAmount) AS I
        FROM act_gl_books AS A
				INNER JOIN expense_cate as E on A.ExpenseID = E.ExpenseID
				INNER JOIN program_cate as P on A.ProgramID = P.ProgramID
				INNER JOIN norm_cate as N on A.NormID = N.NormID
				WHERE A.ProjectNo != ''
				AND A.AccountNo IN ('0211','0221') AND E.CateNo = '1201'
				AND P.CateNo = '001' AND P.CateValue IN ('1','2')
				AND N.CateNo = '1203' AND N.CateValue = '1'
				AND A.InTransTypeID not in (16,17,18)
                $whereCondition
				GROUP BY A.InputCompanyID, A.AccountNo, E.CateNo, P.CateNo, P.CateValue, N.CateNo, N.CateValue, A.ProjectNo, YEAR(A.PostDate)
			) as CH GROUP BY CompanyID, ProjectNo, ProgramCateValue) as HH set TT.I1 = HH.I1, TT.I2 = HH.I2
				WHERE (TT.ItemID = CONCAT('B#04#01.',HH.ProjectNo,'#01') OR TT.ItemID = CONCAT('B#04#02.',HH.ProjectNo,'#01'))
				AND TT.CompanyID = HH.CompanyID
				AND TT.ProjectNo = HH.ProjectNo
				AND TT.ProgramCateValue = HH.ProgramCateValue
				AND TT.Detail = 1";
        DB::statement($sql);
    }

    public function insertB4_2Detail($whereCondition, $year, $nextYear){
        $sql = "UPDATE table_tmp as TT, (SELECT CompanyID, ProjectNo, ProgramCateValue,
			MAX(CASE WHEN AccountNo = '0211' AND YearPostDate = $year THEN I END) AS I1,
			MAX(CASE WHEN AccountNo = '0221' AND YearPostDate = $nextYear THEN I END) as I2 FROM (SELECT A.InputCompanyID as CompanyID, A.ProjectNo as ProjectNo, A.AccountNo as AccountNo, P.CateValue as ProgramCateValue, YEAR(A.PostDate) as YearPostDate, N.CateNo as NormNo, SUM(LCDebitAmount) AS I
            FROM act_gl_books AS A
            INNER JOIN expense_cate as E on A.ExpenseID = E.ExpenseID
            INNER JOIN program_cate as P on A.ProgramID = P.ProgramID
            INNER JOIN norm_cate as N on A.NormID = N.NormID
            WHERE A.ProjectNo != ''
            AND A.AccountNo IN ('0211','0221') AND LEFT(E.CateNo,4) = '1100'
            AND P.CateNo = '001' AND P.CateValue IN ('1','2')
            AND N.CateNo = '1201' AND N.CateValue IN ('9','10')
            AND A.InTransTypeID not in (16,17,18)
            $whereCondition
            GROUP BY A.InputCompanyID, A.AccountNo, E.CateNo, P.CateNo, P.CateValue, N.CateNo, N.CateValue, A.ProjectNo, YEAR(A.PostDate)
			) as CH GROUP BY CompanyID, ProjectNo, ProgramCateValue) as HH set TT.I1 = HH.I1, TT.I2 = HH.I2
            WHERE (TT.ItemID = CONCAT('B#04#01.',HH.ProjectNo,'#02') OR TT.ItemID = CONCAT('B#04#02.',HH.ProjectNo,'#02'))
            AND TT.CompanyID = HH.CompanyID
            AND TT.ProjectNo = HH.ProjectNo
            AND TT.ProgramCateValue = HH.ProgramCateValue
            AND TT.Detail = 1";
        DB::statement($sql);
    }

    public function sumA1(){
        $sql = "SELECT SUM(I1) as I1, SUM(I2) as I2, CompanyID FROM table_tmp
                WHERE (ItemID = 'A#02' OR ItemID = 'A#03')
                AND Detail = 1
				GROUP BY CompanyID ";
        $result = DB::select($sql);
        $result = (array)$result;
        if ($result) {
            foreach ($result as $value) {
                $value = (array)$value;
                $I1 = ($value['I1']) ? $value['I1'] : 0;
                $I2 = ($value['I2']) ? $value['I2'] : 0;
                $update = "UPDATE table_tmp
                    SET I1 = " . $I1 . ", I2 = " . $I2 . "
                    WHERE ItemID = 'A#01' AND Detail = 1 AND CompanyID = " .$value['CompanyID'];
                DB::statement($update);
            }
        }
    }

    public function sumUpColumn($start, $end, $maxLevel){
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT CompanyID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2 FROM table_tmp M
                WHERE M.Level = " . $i . " AND Detail = 1 GROUP BY CompanyID, ParentID";
                $result = DB::select($sql);
                $result = (array)$result;
                if ($result) {
                    foreach ($result as $value) {
                        $value = (array)$value;
                        $I1 = ($value['I1']) ? $value['I1'] : 0;
                        $I2 = ($value['I2']) ? $value['I2'] : 0;
                        $update = "UPDATE table_tmp
                    SET I1 = " . $I1 . ", I2 = " . $I2 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND Detail = 1
                    AND table_tmp.Level = " . $j . " AND CompanyID = " .$value['CompanyID'];
                        DB::statement($update);
                    }
                }
                if($i == 3){
                    $this->sumA1();
                }
            }
        }
    }

    public function sumParent($maxLevelCompany,$company){
        for ($i = $maxLevelCompany->maxLevel; $i > $company->Level; $i--){
            $j = $i - 1;
            $sql = "SELECT ParentCompanyID ,ItemID, SUM(M.I1) AS I1, SUM(M.I2) AS I2
                FROM table_tmp M
                WHERE M.LevelCompany = " . $i . " GROUP BY ParentCompanyID, ItemID";
            $result = DB::select($sql);
            $result = (array)$result;
            if ($result) {
                foreach ($result as $value) {
                    $value = (array)$value;
                    $I1 = ($value['I1']) ? $value['I1'] : 0;
                    $I2 = ($value['I2']) ? $value['I2'] : 0;
                    $update = "UPDATE table_tmp
                    SET I1 = " . $I1 . ", I2 = " . $I2 . "
                    WHERE ItemID = '" . $value['ItemID'] . "'
                    AND ItemID != 'X'
                    AND table_tmp.LevelCompany = " . $j . " AND CompanyID = " .$value['ParentCompanyID'];
                    DB::statement($update);
                }
            }
        }
    }

    public function sumFinalcialCompany($company){
        $sql = "SELECT ItemID, SUM(I1) as I1, SUM(I2) as I2 FROM table_tmp WHERE CompanyID != $company->CompanyID AND LevelCompany = 1 GROUP BY ItemID";
        $result = DB::select($sql);
        $result = (array)$result;
        if ($result) {
            foreach ($result as $value) {
                $value = (array)$value;
                $I1 = ($value['I1']) ? $value['I1'] : 0;
                $I2 = ($value['I2']) ? $value['I2'] : 0;
                $update = "UPDATE table_tmp
                    SET I1 = I1 + " . $I1 . ", I2 = I2 + " . $I2 . "
                    WHERE ItemID = '" . $value['ItemID'] . "'
                    AND ItemID != 'X'
                    AND table_tmp.LevelCompany = 1 AND CompanyID = " .$company->CompanyID;
                DB::statement($update);
            }
        }
    }


}
