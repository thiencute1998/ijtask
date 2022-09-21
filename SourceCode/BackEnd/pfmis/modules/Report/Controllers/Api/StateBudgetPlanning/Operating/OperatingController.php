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

class OperatingController extends Controller {
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
    //B200-DT-DH - TỔNG HỢP CÂN ĐỐI DỰ TOÁN NSNN NĂM YYYY/GIAI ĐOẠN XXXX-YYYY
    public function TotalSBPB200DH($listChildrenCompany, $company, $year){
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
        $result1 = DB::table('act_gl_books as A')
            ->select('RevenueNo','RC.CateNo as RevenueCateNo','RCL.Level as LevelRevenueCate',
                'company.*','RevenueName')
            ->join('revenue_cate as RC','A.RevenueID','=','RC.RevenueID')
            ->join('revenue_cate_list as RCL','RC.CateID','=','RCL.CateID')
            ->join('company','A.InputCompanyID','=','company.CompanyID')
            ->whereIn('A.AccountNo',['0361','0341','0711','0311'])
            ->whereIn('A.InputCompanyID',$arrCompanyID)
            ->whereBetween(DB::raw('YEAR(A.PeriodFromDate)'), [$year - 2, $year])
            ->groupBy('RevenueNo','RC.CateNo','company.CompanyID')
            ->get();
        if ($result1) {
            foreach ($result1 as $template) {
                $ItemID = 'A#' . $template->RevenueCateNo . '#' . $template->RevenueNo;
                $ItemNo = $template->CompanyNo . '.' . 'A#' . $template->RevenueCateNo . '#' . $template->RevenueNo;
                $ParentID = 'A#' . $template->RevenueCateNo;
                $Level = $template->LevelRevenueCate + 1;
                $insertArr[] = $this->formatInsertArr('', $ItemID, $ItemNo, $ParentID, $Level, $template->RevenueName, 0, 0, 0, 0, $template->RevenueCateNo,$template->RevenueNo, null, null , $template);
            }
        }

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
                $insertArr[] = $this->formatInsertArr('', $ItemID, $ItemNo, $ParentID, $Level, $template->ExpenseName, 0, 0, 0, 0, null, null, $template->ExpenseCateNo,$template->ExpenseNo, $template);
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

    public function SBPB200DH(Request $request)
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

        $arr[] = array_column($this->selectSBPB200DHI($whereConditionCompany), 'PathCompanyID');
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
          INDEX itemid(TableID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        //Tạo bảng b200_dt_dh
        $this->SBPB200DHTemp();
        //A -> Các chứng từ phát sinh
        $this->TotalSBPB200DH($listChildrenCompany, $company,$year);
        // Leve 1
        $this->SBPB200DHC1($listChildrenCompany, $company);
        //$maxlevelc1 = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.CateLevel) AS maxLevel'))->where("Level", '=',4)->first();
        //$maxlevelc1 = $maxlevelc1->maxLevel;
        //$this->SBPB200DHC2($maxlevelc1);
        // Cell 1 -> A
        $this->SBPB200DHI($whereCondition,$year);
        // Cell 1 -> B
        $this->SBPB200DHI_B($whereCondition,$year);
        // ->D
        $this->SBPB200DHI_D1($whereConditionPre, $yearPre, $yearNext, $year);
        $this->SBPB200DHI_D2($whereConditionPre, $yearPre, $yearNext, $year);
        // ->E
        $this->SBPB200DHI_E1($whereConditionPre, $yearPre, $yearNext, $year);
        $this->SBPB200DHI_E2($whereConditionPre, $yearPre, $yearNext, $year);

        // -> A
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $maxLevelCompany = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.LevelCompany) AS maxLevel'))->first();
        $createTableTmp1 = "CREATE TEMPORARY TABLE t (SELECT * FROM table_tmp)";
        DB::statement($createTableTmp1);
        $this->sumUpSBPB200DH(0, $maxLevel);
        if(count($listChildrenCompany) > 1){
            $this->sumParent($maxLevelCompany,$company);
            if($company->IsFinancialCompany){
                $this->sumFinalcialCompany($company);
            }
        }
        //
        $ArrIA= DB::table('table_tmp')->where('ItemID','=','A#')->first();
        $ArrIB= DB::table('table_tmp')->where('ItemID','=','B#')->first();
        $IA1 = $ArrIA->I1; $IA2 = $ArrIA->I2; $IA3 = $ArrIA->I3; $IA4 = $ArrIA->I4;
        $IB1 = $ArrIB->I1; $IB2 = $ArrIB->I2; $IB3 = $ArrIB->I3; $IB4 = $ArrIB->I4;
        //Update To C
        $IC1 =  (double)$IA1 - (double)$IB1; $IC2 =  (double)$IA2 - (double)$IB2; $IC3 =  (double)$IA3 - (double)$IB3; $IC4 =  (double)$IA4 - (double)$IB4;
        $update = "UPDATE table_tmp
                    SET I1 = '".$IC1."', I2 = '".$IC2."', I3 = '".$IC3."', I4 = '".$IC4."'
                    WHERE ItemID = 'C#' ";//Log::debug($update);
        DB::statement($update);
        //Xóa dòng trống
        $checknull1="(LEFT(ItemID, 2)='A#' OR LEFT(ItemID, 2)='B#') AND I1=0 AND I2=0 AND I3=0 AND I4=0 AND Level!=0";
        $this->Deleterownull($checknull1);
        //Đánh chỉ mục bảng tạm
        $indexcompany = DB::table('table_tmp')->groupBy('CompanyID')->get();
        foreach ($indexcompany as $key => $item){
            $this->updateIndexListing('table_tmp', $item->CompanyID);
        }

        //
        $type = isset($filter['Type']) ? $filter['Type'] : null;
        $data = DB::table("table_tmp")
            ->when($type == 1, function($q){
                return $q->where(DB::raw('LEFT(ItemID,2)'),'!=','B#');
            })
            ->when($type == 2, function($q){
                return $q->where(DB::raw('LEFT(ItemID,2)'),'!=','A#');
            })
            ->orderBy('ItemNo', 'ASC')->where("Hidden", 1)
            ->where('ItemID','!=','')
            ->where('CompanyID','=',$company->CompanyID)->get();

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
                "ReportName" => "TỔNG HỢP CÂN ĐỐI DỰ TOÁN NSNN NĂM " . $year,
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
    //Tạo bảng b200_dt_dh
    public function SBPB200DHTemp(){
        $DataTable = DB::table('b200_dt_dh')->get();
        if(!count($DataTable)) {
            //Loại khoản thu
            $sql = "SELECT * FROM revenue_cate_list ORDER by CateNo ASC";
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
                        'RevenueCateListID' => $template->CateID,
                        'ItemType' => '1',
                        'Hidden' => '1',
                    ];
                }
                DB::table('b200_dt_dh')->insert($insertArr);
            }
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
                DB::table('b200_dt_dh')->insert($insertArr);
            }
            //Đánh chỉ mục
            $this->updateIndexType('b200_dt_dh');

        }
    }
    public function SBPB200DHC1($listChildrenCompany, $company){
        $sql = "SELECT * FROM b200_dt_dh WHERE 1";
        //Log::debug($sql);
        $result = DB::select($sql);
        $result = (array)$result; $insertArr = [];
        if ($result) {
            foreach ($listChildrenCompany as $item){
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
                        $insertArr[] = $this->formatInsertArr('', '', $item->CompanyNo, null, null, $item->CompanyName, 1, 0, 0, 1, null,null, null, null, $item);
                        $insertArr[] = $this->formatInsertArr('A', 'A#', $item->CompanyNo . '.A#', null, 0, 'TỔNG THU', 1, 0, 0, 1, null, null, null, null, $item);
                        $insertArr[] = $this->formatInsertArr('B', 'B#', $item->CompanyNo . '.B#', null, 0, 'TỔNG CHI', 1, 0, 0, 1, null, null, null, null, $item);
                        $insertArr[] = $this->formatInsertArr('C', 'C#', $item->CompanyNo . '.C#', null, 0, 'BỘI CHI/ BỘI THU', 1, 0, 0, 1, null, null, null, null, $item);
                        $insertArr[] = $this->formatInsertArr('D', 'D#', $item->CompanyNo . '.D#', null, 0, 'VAY', 1, 0, 0, 1, null, null, null, null, $item);
                        $insertArr[] = $this->formatInsertArr('E', 'E#', $item->CompanyNo . '.E#', null, 0, 'TRẢ NỢ', 1, 0, 0, 1, null, null, null, null, $item);
                        $insertArr[] = $this->formatInsertArr('1', 'D#1', $item->CompanyNo . '.D#1', 'D#', 1, 'Vay bù đắp bội chi', 0, 0, 0, 1, null, null, null, null, $item);
                        $insertArr[] = $this->formatInsertArr('2', 'D#2', $item->CompanyNo . '.D#2', 'D#', 1, 'Vay trả nợ gốc', 0, 0, 0, 1, null, null, null, null, $item);
                        $insertArr[] = $this->formatInsertArr('1', 'E#1', $item->CompanyNo . '.E#1', 'E#', 1, 'Trả nợ gốc từ nguồn vay', 0, 0, 0, 1, null, null, null, null, $item);
                        $insertArr[] = $this->formatInsertArr('2', 'E#2', $item->CompanyNo . '.E#2', 'E#', 1, 'Trả nợ từ nguồn khác (bội thu, tăng thu, tiết kiệm chi, kết dư NS,...)', 0, 0, 0, 1, null, null, null, null, $item);
                    }
                    $insertArr[] = $this->formatInsertArr($template->Index, $ItemID, $item->CompanyNo. '.' . $ItemID, $ParentID, $template->Level, $template->ItemName, 0, 0, 0, 1, $template->ItemNo, null, $template->ItemNo, null, $item);
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

    public function SBPB200DHI($whereCondition,$year){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT RevenueNo, CompanyID,
		MAX(CASE WHEN AccountNo = '0361' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0711' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0311' AND YEARD = $year THEN I ELSE 0 END) AS I4
		FROM (SELECT SUM(A.LCDebitAmount) AS I, A.InputCompanyID AS CompanyID, A.AccountNo, A.RevenueNo, YEAR(A.PeriodFromDate) AS YEARD
        FROM act_gl_books AS A
        WHERE A.AccountNo IN ('0361','0341','0711','0311')
        $whereCondition
        GROUP BY A.AccountNo, A.RevenueNo, A.InputCompanyID, YEAR(A.PeriodFromDate)
        ) as B GROUP BY RevenueNo, CompanyID) AS HH SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE LEFT(TT.ItemID,2) = 'A#' AND TT.Hidden = 0
        AND TT.RevenueNo = HH.RevenueNo AND TT.CompanyID = HH.CompanyID";
        DB::statement($sqlUpdate);
    }
    // -> B
    public function SBPB200DHI_B($whereCondition,$year){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT ExpenseNo, CompanyID,
		MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4
		FROM (SELECT SUM(A.LCDebitAmount) AS I, A.InputCompanyID AS CompanyID, A.AccountNo, A.ExpenseNo, YEAR(A.PeriodFromDate) AS YEARD
        FROM act_gl_books AS A
        WHERE A.AccountNo IN ('0461','0441','0721','0411')
        $whereCondition
        GROUP BY A.AccountNo, A.ExpenseNo, A.InputCompanyID, YEAR(A.PeriodFromDate)
        ) as B GROUP BY ExpenseNo, CompanyID) AS HH SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
        WHERE LEFT(TT.ItemID,2) = 'B#' AND TT.Hidden = 0
        AND TT.ExpenseNo = HH.ExpenseNo AND TT.CompanyID = HH.CompanyID";
        DB::statement($sqlUpdate);
    }
    //D, E
    public function SBPB200DHI_D1($whereCondition, $preYear, $nextYear, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT
            MAX(CASE WHEN AccountNo = '0361' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN AccountNo = '0341' AND B.YearD = $nextYear THEN I END) AS I2,
            MAX(CASE WHEN AccountNo = '0711' AND B.YearD = $nextYear THEN I END) AS I3,
            MAX(CASE WHEN AccountNo = '0311' AND B.YearD = $year THEN I END) AS I4
            FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo, RC.CateValue AS CateValue, YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN capital_cate AS RC ON RC.CapitalID = A.CapitalID
            INNER JOIN capital_cate_list AS SC ON SC.CateID = RC.CateID
            WHERE SC.CateNo = '1500' AND RC.CateValue = 1
            $whereCondition
            GROUP BY A.AccountNo) AS B) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = 'D#1'";
        DB::statement($sqlUpdate);
    }
    public function SBPB200DHI_D2($whereCondition, $preYear, $nextYear, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT
            MAX(CASE WHEN AccountNo = '0361' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN AccountNo = '0341' AND B.YearD = $nextYear THEN I END) AS I2,
            MAX(CASE WHEN AccountNo = '0711' AND B.YearD = $nextYear THEN I END) AS I3,
            MAX(CASE WHEN AccountNo = '0311' AND B.YearD = $year THEN I END) AS I4
            FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo, RC.CateValue AS CateValue, YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN capital_cate AS RC ON RC.CapitalID = A.CapitalID
            INNER JOIN capital_cate_list AS SC ON SC.CateID = RC.CateID
            WHERE SC.CateNo = '1500' AND RC.CateValue = 2
            $whereCondition
            GROUP BY A.AccountNo) AS B) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = 'D#2'";
        DB::statement($sqlUpdate);
    }
    public function SBPB200DHI_E1($whereCondition, $preYear, $nextYear, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT
            MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $nextYear THEN I END) AS I2,
            MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $nextYear THEN I END) AS I3,
            MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $year THEN I END) AS I4
            FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo, RC.CateValue AS CateValue, YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN capital_cate AS RC ON RC.CapitalID = A.CapitalID
            INNER JOIN capital_cate_list AS SC ON SC.CateID = RC.CateID
            WHERE SC.CateNo = '1400' AND RC.CateValue = 2
            $whereCondition
            GROUP BY A.AccountNo) AS B) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = 'E#1'";
        DB::statement($sqlUpdate);
    }
    public function SBPB200DHI_E2($whereCondition, $preYear, $nextYear, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT
            MAX(CASE WHEN AccountNo = '0461' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN AccountNo = '0441' AND B.YearD = $nextYear THEN I END) AS I2,
            MAX(CASE WHEN AccountNo = '0721' AND B.YearD = $nextYear THEN I END) AS I3,
            MAX(CASE WHEN AccountNo = '0411' AND B.YearD = $year THEN I END) AS I4
            FROM (SELECT SUM(LCDebitAmount) AS I, A.AccountNo, RC.CateValue AS CateValue, YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN capital_cate AS RC ON RC.CapitalID = A.CapitalID
            INNER JOIN capital_cate_list AS SC ON SC.CateID = RC.CateID
            WHERE SC.CateNo = '1400' AND RC.CateValue = 1
            $whereCondition
            GROUP BY A.AccountNo) AS B) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = 'E#2'";
        DB::statement($sqlUpdate);
    }
    //
    public function sumUpSBPB200DH($start, $maxLevel){
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
        }
    }
    //CÂN ĐỐI DỰ TOÁN NSNN CHI TIẾT THEO KỲ NGÂN SÁCH

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

    public function formatInsertArr($stt, $itemID, $itemNo, $parentID, $level, $itemName, $fontWeight, $italic, $center,
                                    $hidden, $revenueCateNo, $revenueNo,$expenseCateNo, $expenseNo, $company){
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
            'Hidden'=> $hidden,
            'RevenueCateNo'=> $revenueCateNo,
            'RevenueNo' => $revenueNo,
            'ExpenseCateNo'=> $expenseCateNo,
            'ExpenseNo'=> $expenseNo,
            'I1' => 0,
            'I2' => 0,
            'I3' => 0,
            'I4' => 0,
            'CompanyID' => $company->CompanyID,
            'ParentCompanyID'=> $company->ParentID,
            'LevelCompany'=> $company->Level,
            'Detail'=> $company->Detail,

        ];
    }

    public function selectSBPB200DHI($whereConditionCompany){
        $sql = "SELECT PathCompanyID
        FROM act_gl_books AS A
        WHERE 1 = 1
        $whereConditionCompany
        GROUP BY PathCompanyID";
        return DB::select($sql);
    }

}
