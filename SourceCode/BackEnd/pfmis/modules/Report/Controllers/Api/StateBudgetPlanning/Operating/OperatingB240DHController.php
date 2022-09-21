<?php
namespace Module\Report\Controllers\StateBudgetPlanning\Operating;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Module\Report\Traits\Common;
use function Psy\debug;

class OperatingB240DHController extends Controller {
    use Common;
    public function BCDHB02DHCH(){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }

    public function STDS02DHCH()
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }
    public function STDTHDTTNSNN02()
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }
    public function STDDTGTGT03()
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }
    //B240-DT-DH
    public function TotalSBPB240DH($listChildrenCompany, $company, $year){
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

        $insertArr = [];
        $result2 = DB::table('act_gl_books as A')
            ->select('ExpenseNo','RC.CateNo as ExpenseCateNo','RCL.Level as LevelExpenseCate',
                'company.*','ExpenseName')
            ->join('expense_cate as RC','A.ExpenseID','=','RC.ExpenseID')
            ->join('expense_cate_list as RCL','RC.CateID','=','RCL.CateID')
            ->join('company','A.InputCompanyID','=','company.CompanyID')
            ->whereIn('A.AccountNo',['0461','0441','0721','0411'])
            ->whereIn('A.InputCompanyID',$arrCompanyID)
            ->whereBetween(DB::raw('YEAR(A.PeriodFromDate)'), [$year - 2, $year])
            ->groupBy('ExpenseNo','RC.CateNo','company.CompanyID')
            ->get();
        if ($result2) {
            foreach ($result2 as $template) {
                $ItemID = 'B#' . $template->ExpenseCateNo . '#' . $template->ExpenseNo;
                $ItemNo = $template->CompanyNo . '.' . 'B#' . $template->ExpenseCateNo . '#' . $template->ExpenseNo;
                $ParentID = 'B#' . $template->ExpenseCateNo;
                $Level = $template->LevelExpenseCate + 1;
                if(substr($template->CompanyNo, 0 ,3* $company->Level) === substr($company->CompanyNo, 0 ,3* $company->Level) && $company->IsFinancialCompany == 1){
                    $norder = 1;
                }
                else{
                    $norder = 2;
                }
                $insertArr[] = $this->formatInsertArr('', $ItemID, $ItemNo, $ParentID, $Level, $template->ExpenseName, 0, 0, 0, 0, null, null, $template->ExpenseCateNo,$template->ExpenseNo, $template, $norder);
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
                            if(substr($value->CompanyNo, 0 ,3 * $company->Level) === substr($company->CompanyNo, 0 ,3 * $company->Level) && $company->IsFinancialCompany == 1){
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
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }

    public function SBPB240DH(Request $request)
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $Year = date("Y");
        $filter = $request->post('filter');
        $year = (isset($filter['Year'])) ? $filter['Year'] : $Year;
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

        $companyName = $company->CompanyName;
        if(!isset($filter['LevelCompany'])){
            $levelCompany = null;
        }
        else{
            $levelCompany = $filter['LevelCompany'];
        }

        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year - 2 AND $year AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18";
        $whereConditionCompany = " AND ((A.AccountNo IN ('0361','0461') AND YEAR(A.PeriodFromDate) = $year - 2)
        OR (A.AccountNo IN ('0341','0441','0711','0721') AND YEAR(A.PeriodFromDate) = $year - 1)
        OR (A.AccountNo IN ('0311','0411') AND YEAR(A.PeriodFromDate) = $year))";
        // Lọc đơn vị phát sinh chứng từ

        $arr[] = array_column($this->selectSBPB240DHI($whereConditionCompany), 'PathCompanyID');
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

        // TODO check isFinalTrans: AND A.isFinalTrans = 1

        $yearPre = (int)$year - 2;
        $yearNext = (int)$year - 1;

        $whereConditionPre = "AND YEAR(A.PeriodFromDate) = $yearPre AND YEAR(A.PeriodToDate) = $yearPre AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18 ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
            `TableID` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `STT` varchar(50) DEFAULT '',
            `ItemID` varchar(100) DEFAULT '',
            `ItemNo` varchar(255) DEFAULT '',
            `ParentID` varchar(100) DEFAULT '',
            `Level` int(2) DEFAULT 1,
            `ItemName` varchar(255) DEFAULT NULL,
            `FontWeight` tinyint(1) DEFAULT 0,
            `Italic` tinyint(1) DEFAULT 0,
            `Center` tinyint(1) DEFAULT 0,
            `Year` varchar(5) DEFAULT '',
            `AccountNo` varchar(20) DEFAULT '',
            `RevenueID` varchar(15) DEFAULT '',
            `RevenueNo` varchar(20) DEFAULT '',
            `RevenueCateNo` varchar(20) DEFAULT '',
            `RevenueCateValue` varchar(20) DEFAULT '',
            `CateID` varchar(15) DEFAULT '',
            `CateParentID` varchar(15) DEFAULT '',
            `CateLevel` tinyint(2) NOT NULL,
            `CapitalCateNo` varchar(20) DEFAULT '',
            `CapitalCateValue` varchar(20) DEFAULT '',
            `ExpenseID` varchar(15) DEFAULT '',
            `ExpenseNo` varchar(20) DEFAULT '',
            `ExpenseCateNo` varchar(20) DEFAULT '',
            `ExpenseCateValue` varchar(20) DEFAULT '',
            `I1` double(20,4) DEFAULT 0 NOT NULL,
            `I2` double(20,4) DEFAULT 0 NOT NULL,
            `I3` double(20,4) DEFAULT 0 NOT NULL,
            `I4` double(20,4) DEFAULT 0 NOT NULL,
            `Hidden` tinyint(1) NOT NULL,
            `CompanyID` int(11) DEFAULT NULL,
            `ParentCompanyID` int(11) DEFAULT NULL,
            `LevelCompany` int(11) DEFAULT NULL,
            `Detail` int(11) DEFAULT NULL,
            `Norder` int(11) DEFAULT NULL,
          INDEX itemid(TableID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        //Tạo bảng b240_dt_dh
        $this->SBPB240DHTemp();
        //A -> Các chứng từ phát sinh
        $this->TotalSBPB240DH($listChildrenCompany, $company,$year);
        // Leve 1
        $this->SBPB240DHC1($listChildrenCompany, $company);
        // Cell 1 -> B
        $this->SBPB240DHI_B($whereCondition,$year);

        // -> A
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $maxLevelCompany = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.LevelCompany) AS maxLevel'))->first();
        $createTableTmp1 = "CREATE TEMPORARY TABLE t (SELECT * FROM table_tmp)";
        DB::statement($createTableTmp1);
        $this->sumUpSBPB240DH(0,6, $maxLevel);

        //Xóa dòng trống
        $checknull1="(LEFT(ItemID, 2)='A#' OR LEFT(ItemID, 2)='B#') AND I1=0 AND I2=0 AND I3=0 AND I4=0 AND Level!=0";
        $this->Deleterownull($checknull1);
        //Đánh chỉ mục bảng tạm
        $indexcompany = DB::table('table_tmp')->groupBy('CompanyID')->get();
        foreach ($indexcompany as $key => $item){
            $this->updateIndexListing('table_tmp', $item->CompanyID);
        }

        //
        $type = $filter['Type'];
        if(!is_null($levelCompany)){
            $data = DB::table("table_tmp")
                ->when($type == 1, function($q){
                    return $q->where(DB::raw('LEFT(ItemID,2)'),'!=','B#');
                })
                ->orderBy('Norder','ASC')->orderBy('ItemNo', 'ASC')->where("Hidden", 1)->where('LevelCompany','<=',$levelCompany)->get();
        }
        else{
            $data = DB::table("table_tmp")
                ->when($type == 2, function($q){
                    return $q->where(DB::raw('LEFT(ItemID,2)'),'!=','A#');
                })
                ->orderBy('Norder','ASC')->orderBy('ItemNo', 'ASC')->where("Hidden", 1)->where('CompanyID','=',$company->CompanyID)->get();
        }
        $year2 = $year -2;
        $year1 = $year -1;
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $year2;
            $data[0]->HeaderName2 = 'Năm ' . $year1;
            $data[0]->HeaderName3 = 'Dự toán năm ' .$year;
        }
        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            if($data[$key]->I2 !=0){
                $data[$key]->I5 = round(((int)($data[$key]->I4) / (int)($data[$key]->I2))*100, 2);
                $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            }
        }

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $dateTime = "Vĩnh Phú, ngày $date tháng $month năm $yearC";
        if ($company) {
            $dateTime = $company->Address . ", ngày $date tháng $month năm $yearC";
        }
        $response = [
            'master' => [
                "Year" => "",
                "CreateName" => "Trần Thế Thái",
                "TransNo" => "00001",
                "ReportName" => "TỔNG HỢP DỰ TOÁN CHI NSNN NĂM " . $year,
                "DateTime" => $dateTime,
//                "ParentCompanyName" => 'UBND tỉnh Vĩnh Phú',
                "CompanyName" => $companyName,
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    //Tạo bảng b240_dt_dh
    public function SBPB240DHTemp(){
        $DataTable = DB::table('b240_dt_dh')->get();
        if(!count($DataTable)) {
            //Loại khoản chi
            $sql = "SELECT * FROM expense_cate_list ORDER by CateNo ASC";
            //Log::debug($sql);
            $result = DB::select($sql);
            $result = (array)$result;
            $insertArr = [];
            $i = 1;
            if ($result) {
                foreach ($result as $template) {
                    if ($template->Level == 1) {
                        $FontWeight = 1;
                    }
                    $insertArr[] = [
                        'ItemNo' => $template->CateNo,
                        'ItemName' => $template->CateName,
                        'ParentNo' => $template->ParentNo,
                        'Level' => $template->Level,
                        'Operator' => '',
                        'Formula' => '',
                        'Printed' => '',
                        'FontStyle' => '',
                        'FontWeight' => isset($FontWeight) ? $FontWeight : '',
                        'ExpenseCateListID' => $template->CateID,
                        'ItemType' => '2',
                        'Hidden' => '1',
                    ];
                }
                DB::table('b240_dt_dh')->insert($insertArr);
            }
            //Đánh chỉ mục
            $this->updateIndexType('b240_dt_dh');

        }
    }
    public function SBPB240DHC1($listChildrenCompany, $company){
        $sql = "SELECT * FROM b240_dt_dh WHERE 1";
        //Log::debug($sql);
        $result = DB::select($sql);
        $result = (array)$result; $insertArr = [];
        if ($result) {
            foreach ($listChildrenCompany as $item){
                if(substr($item->CompanyNo, 0 ,3* $company->Level) === substr($company->CompanyNo, 0 ,3* $company->Level) && $company->IsFinancialCompany == 1)
                    $norder = 1;
                else
                    $norder = 2;
                foreach ($result as $key=> $template) {
                    $ItemID =  '';
                    $ParentID = '';
                    if($template->RevenueCateListID) {
                        $ItemID = 'A#'. $template->ItemNo;
                        $ParentID = 'A#'. $template->ParentNo;
                    }
                    if($template->ExpenseCateListID) {
                        $ItemID = 'B#'. $template->ItemNo;
                        $ParentID = 'B#'. $template->ParentNo;
                    }
                    if($key == 0){
                        $insertArr[] = $this->formatInsertArr('', '', $item->CompanyNo, null, null, $item->CompanyName, 1, 0, 0, 1, null,null, null, null, $item, $norder);
                        $insertArr[] = $this->formatInsertArr('B', 'B#', $item->CompanyNo . '.B#', null, 0, 'TỔNG CHI', 1, 0, 0, 1, null, null, null, null, $item, $norder);
                    }
                    $insertArr[] = $this->formatInsertArr($template->Index, $ItemID, $item->CompanyNo. '.' . $ItemID, $ParentID, $template->Level, $template->ItemName, 0, 0, 0, 1, $template->ItemNo, null, $template->ItemNo, null, $item, $norder);
                }
            }

            DB::table('table_tmp')->insert($insertArr);
        }
    }
    public function Deleterownull($value){
        if(!$value){ $value= 1;}
        $sql = "DELETE FROM `table_tmp` WHERE  $value";
        DB::statement($sql);
    }
    public function updateIndexListing($table, $CompanyID){
        $insertArr = [];
        $maxLevel = DB::table($table)->select(DB::raw('Max(Level) as maxLevel'))->where('CompanyID','=',$CompanyID)->first();
        for($i = 1; $i<=$maxLevel->maxLevel-1 ; $i++){
            $insertArr1 = [];
            $test = DB::table($table)->orderBy('ItemID','ASC')->where('Level','=',$i)->where('CompanyID','=',$CompanyID)->get();
            if($i == 1){
                foreach ($test as $key => $item){
                    $insertArr1[] = [
                        'ItemID' => $item->ItemID,
                        'ParentID'=> $item->ParentID,
                        'Level'=> $item->Level,
                        'STT'=> $key + 1
                    ];
                    DB::table($table)->where('ItemID','=',$item->ItemID)->update(['STT'=>$key + 1]);
                }
            }
            else{
                foreach($insertArr[$i-2] as $value){
                    $x = 1;
                    foreach ($test as $key => $item){
                        if($value['ItemID'] == $item->ParentID){
                            $insertArr1[] = [
                                'ItemID' => $item->ItemID,
                                'ParentID'=> $item->ParentID,
                                'Level'=>$item->Level,
                                'STT'=> $value['STT']. '.'. $x,
                            ];
                            DB::table($table)->where('ItemID','=',$item->ItemID)->update(['STT'=>$value['STT']. '.'. $x]);
                            $x++;
                        }
                    }
                }
            }
            $insertArr[] = $insertArr1;
        }
    }
    // -> B
    public function SBPB240DHI_B($whereCondition,$year){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT ExpenseNo, CompanyID,
		MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4
		FROM (SELECT SUM(A.FCDebitAmount) AS I, A.InputCompanyID AS CompanyID, A.AccountNo, A.ExpenseNo, YEAR(A.PeriodFromDate) AS YEARD
        FROM act_gl_books AS A
        WHERE A.AccountNo IN ('0361','0341','0711','0311')
        $whereCondition
        GROUP BY A.AccountNo, A.ExpenseNo, A.InputCompanyID, YEAR(A.PeriodFromDate)
        ) as B GROUP BY ExpenseNo, CompanyID) AS HH SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE LEFT(TT.ItemID,2) = 'A#' AND TT.Hidden = 0
        AND TT.ExpenseNo = HH.ExpenseNo AND TT.CompanyID = HH.CompanyID";
        DB::statement($sqlUpdate);
    }
    public function sumUpSBPB240DH($start, $end, $maxLevel){
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID,SUM(M.I1) AS I1, SUM(M.I2) AS I2,SUM(M.I3) AS I3, SUM(M.I4) AS I4, SUM(M.I5) AS I5, SUM(M.I6) AS I6 FROM table_tmp M
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
                        $update = "UPDATE table_tmp
                    SET I1 = I1 + ".$I1.",I2 = I2 + ".$I2.", I3 = I3 + ".$I3.",I4 = I4 + ".$I4.", I5 = I5 + ".$I5.", I6 = I6 + ".$I6."
                    WHERE ItemID = '".$value['ParentID']."'
                        AND table_tmp.Level = $j"; //Log::debug($update);
                        DB::statement($update);
                    }
                }
            }
        }
    }

    public function selectSBPB240DHI($whereConditionCompany){
        $sql = "SELECT PathCompanyID
        FROM act_gl_books AS A
        WHERE 1 = 1
        $whereConditionCompany
        GROUP BY PathCompanyID";
        return DB::select($sql);
    }

}
