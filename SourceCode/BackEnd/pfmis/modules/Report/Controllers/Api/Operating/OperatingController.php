<?php
namespace Module\Report\Controllers\Operating;

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
    public function TotalOPTB200DTDH($whereCondition){
        $sql = "SELECT A.PeriodToDate, A.PeriodFromDate, A.InTransTypeID, A.FiscalYear, A.ConAccountNo ConAccountNo, A.RevenueID RevenueID, A.RevenueNo RevenueNo, A.RevenueName RevenueName, RNC.RevenueID RevenueID, RNC.CateNo RevenueCateNo, RNC.CateValue CateValue, RNCL.CateName CateName, RNCL.Level Level  FROM act_gl_books A
                        INNER JOIN revenue_cate RNC ON RNC.RevenueID = A.RevenueID
                        INNER JOIN revenue_cate_list RNCL ON RNCL.CateID = RNC.CateID WHERE 1=1 $whereCondition  GROUP BY ConAccountNo, RevenueCateNo, RevenueNo
                        ";
        //Log::debug($sql);
        $result = DB::select($sql);
        $result = (array)$result; $insertArr = []; $i=1;
        if ($result) {
            foreach ($result as $template) {
                $STT = $i++;
                $ItemID = 'A#'.$template->ConAccountNo.'#'.$template->RevenueCateNo.'#'.$template->RevenueNo;
                $ParentID = 'A#'.$template->RevenueCateNo;
                $Level = $template->Level +1;
                $insertArr[] = [
                    //'STT' => $STT,
                    'ItemID' => $ItemID,
                    'ParentID' => $ParentID,
                    'Level' => $Level,
                    'ItemName' => $template->CateName,
                    'Year' => $template->FiscalYear,
                    'ConAccountNo' => $template->ConAccountNo,
                    'RevenueCateNo' => $template->RevenueCateNo,
                    'RevenueID' => $template->RevenueID,
                    'RevenueNo' => $template->RevenueNo,
                    'Detail' => '0',
                ];
                }
            DB::table('table_tmp')->insert($insertArr);
            }
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }
    public function TotalOPTB200DTDH_B($whereCondition){
        $sql = "SELECT A.PeriodToDate, A.PeriodFromDate, A.InTransTypeID, A.FiscalYear, A.ConAccountNo ConAccountNo, A.ExpenseID ExpenseID, A.ExpenseNo ExpenseNo, A.ExpenseName ExpenseName, RNC.ExpenseID ExpenseID, RNC.CateNo ExpenseCateNo, RNC.CateValue CateValue, RNCL.CateName CateName, RNCL.Level Level  FROM act_gl_books A
                        INNER JOIN expense_cate RNC ON RNC.ExpenseID = A.ExpenseID
                        INNER JOIN expense_cate_list RNCL ON RNCL.CateID = RNC.CateID WHERE 1=1 $whereCondition  GROUP BY ConAccountNo, ExpenseCateNo, ExpenseNo
                        ";
        //Log::debug($sql);
        $result = DB::select($sql);
        $result = (array)$result; $insertArr = []; $i=1;
        if ($result) {
            foreach ($result as $template) {
                $STT = $i++;
                $ItemID = 'B#'.$template->ConAccountNo.'#'.$template->ExpenseCateNo.'#'.$template->ExpenseNo;
                $ParentID = 'B#'.$template->ExpenseCateNo;
                $Level = $template->Level +1;
                $insertArr[] = [
                    //'STT' => $STT,
                    'ItemID' => $ItemID,
                    'ParentID' => $ParentID,
                    'Level' => $Level,
                    'ItemName' => $template->CateName,
                    'Year' => $template->FiscalYear,
                    'ConAccountNo' => $template->ConAccountNo,
                    'ExpenseCateNo' => $template->ExpenseCateNo,
                    'ExpenseID' => $template->ExpenseID,
                    'ExpenseNo' => $template->ExpenseNo,
                    'Detail' => '0',
                ];
            }
            DB::table('table_tmp')->insert($insertArr);
        }
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }
    public function OPTB200DTDH(Request $request)
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

        $whereCompany = '';
        if ($companyID) {
            $whereCompany .= " AND A.CompanyID = " . $companyID . ' ';
        }
        $whereDirection = '';
        if ($directionID) {
            $whereDirection = 'AND A.DirectionID = ' . $directionID . ' ';
        }

        // TODO check isFinalTrans: AND A.isFinalTrans = 1

        $yearPre = (int)$year - 2;
        $yearNext = (int)$year - 1;

        $whereCondition = "AND YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year $whereCompany ";
        $whereConditionPre = "AND YEAR(A.PeriodFromDate) = $yearPre AND YEAR(A.PeriodToDate) = $yearPre $whereCompany AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18 ";
        $whereConditionNext = "AND YEAR(A.PeriodFromDate) = $yearNext AND YEAR(A.PeriodToDate) = $yearNext $whereCompany $whereDirection AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18 ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `Year` varchar(5) DEFAULT '',
          `ConAccountNo` varchar(20) DEFAULT '',
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
          `Detail` tinyint(1) NOT NULL,
          INDEX itemid(TableID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        //Cấp 1
        $insertArr1 = [
            ['STT'=>'A', 'ItemID'=> 'A#', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG THU', 'FontWeight'=> '1', 'Detail'=> '1'],
            ['STT'=>'B', 'ItemID'=> 'B#', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG CHI', 'FontWeight'=> '1', 'Detail'=> '1'],
            ['STT'=>'C', 'ItemID'=> 'C#', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'BỘI CHI/ BỘI THU', 'FontWeight'=> '1', 'Detail'=> '1'],
            ['STT'=>'D', 'ItemID'=> 'D#', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'VAY', 'FontWeight'=> '1', 'Detail'=> '1'],
            ['STT'=>'E', 'ItemID'=> 'E#', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TRẢ NỢ', 'FontWeight'=> '1', 'Detail'=> '1'],
            ['STT'=>'1', 'ItemID'=> 'D#1', 'ParentID'=> 'D#', 'Level'=> 1, 'ItemName'=> 'Vay bù đắp bội chi', 'FontWeight'=> '0', 'Detail'=> '1'],
            ['STT'=>'2', 'ItemID'=> 'D#2', 'ParentID'=> 'D#', 'Level'=> 1, 'ItemName'=> 'Vay trả nợ gốc', 'FontWeight'=> '0', 'Detail'=> '1'],
            ['STT'=>'1', 'ItemID'=> 'E#1', 'ParentID'=> 'E#', 'Level'=> 1, 'ItemName'=> 'Trả nợ gốc từ nguồn vay', 'FontWeight'=> '0', 'Detail'=> '1'],
            ['STT'=>'2', 'ItemID'=> 'E#2', 'ParentID'=> 'E#', 'Level'=> 1, 'ItemName'=> 'Trả nợ từ nguồn khác (bội thu, tăng thu, tiết kiệm chi, kết dư NS,...)', 'FontWeight'=> '0', 'Detail'=> '1'],
        ];
        //Model::insert($data); // Eloquent approach

        DB::table('table_tmp')->insert($insertArr1); // Query Builder approach
        //Tạo bảng b200_dt_dh
        $this->OPTB200DTDHTemp();
        //A -> Các chứng từ phát sinh
        $this->TotalOPTB200DTDH($whereCondition);
        //B -> Các chứng từ phát sinh
        $this->TotalOPTB200DTDH_B($whereCondition);
        // Leve 1
        $this->OPTB200DTDHC1($whereConditionPre);
        //$maxlevelc1 = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.CateLevel) AS maxLevel'))->where("Level", '=',4)->first();
        //$maxlevelc1 = $maxlevelc1->maxLevel;
        //$this->OPTB200DTDHC2($maxlevelc1);
        // Cell 1 -> A
        $this->OPTB200DTDHI1($whereConditionPre);
        $this->OPTB200DTDHI2($whereConditionPre);
        $this->OPTB200DTDHI3($whereConditionPre);
        $this->OPTB200DTDHI4($whereConditionPre);
        // Cell 1 -> B
        $this->OPTB200DTDHI1_B($whereConditionPre);
        $this->OPTB200DTDHI2_B($whereConditionPre);
        $this->OPTB200DTDHI3_B($whereConditionPre);
        $this->OPTB200DTDHI4_B($whereConditionPre);
        // ->D
        $this->OPTB200DTDHI_D1($whereConditionPre, $yearPre, $yearNext, $year);
        $this->OPTB200DTDHI_D2($whereConditionPre, $yearPre, $yearNext, $year);
        // ->E
        $this->OPTB200DTDHI_E1($whereConditionPre, $yearPre, $yearNext, $year);
        $this->OPTB200DTDHI_E2($whereConditionPre, $yearPre, $yearNext, $year);
        //
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $this->sumUpOPTB200DTDH(0,6, $maxLevel);
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
        //
        $data = DB::table("table_tmp")->where("Detail", 1)->orderBy('ItemID', 'asc')->get();
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
            }
        }

        if (isset($filter['CompanyID'])) {
            $note .= 'đơn vị ' . $filter['CompanyName'];
            $company = Company::where('CompanyID',$filter['CompanyID'])->get();
        }
        else{
            $company = Company::where('CompanyID',Auth::user()->CompanyID)->get();
        }

        $companyName = $company[0]->CompanyName;
        $company = Company::find($companyID);
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
                "ParentCompanyName" => 'UBND tỉnh Vĩnh Phú',
                "CompanyName" => $companyName,
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }
    //Tạo bảng b200_dt_dh
    public function OPTB200DTDHTemp(){
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
                        'Detail' => '1',
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
                        'Detail' => '1',
                    ];
                }
                DB::table('b200_dt_dh')->insert($insertArr);
            }
            //Đánh chỉ mục
            $this->updateIndexType('b200_dt_dh');

        }
    }
    //
    public function OPTB200DTDHC1($whereConditionPre){
//        $sql = "SELECT A.ItemID, A.ParentID, A.CateID, A.ConAccountNo ConAccountNo, A.RevenueID RevenueID, A.RevenueNo RevenueNo, A.ExpenseNo ExpenseNo, A.RevenueCateNo RevenueCateNo,  B.ItemName ItemName, B.RevenueCateListID RevenueCateListID, B.ExpenseCateListID ExpenseCateListID, B.ItemNo ItemNo, B.ParentNo ParentNo, B.Level FROM table_tmp A
//                        INNER JOIN b200_dt_dh B ON B.ItemNo = A.RevenueNo OR B.ItemNo = A.ExpenseNo OR B.ParentNo = A.RevenueNo OR B.ParentNo = A.ExpenseNo
//                        WHERE A.Detail=0 GROUP BY RevenueNo, ExpenseNo";
        $sql = "SELECT * FROM b200_dt_dh WHERE 1";
        //Log::debug($sql);
        $result = DB::select($sql);
        $result = (array)$result; $insertArr = []; $i=1;
        if ($result) {
            foreach ($result as $template) {
                $STT = $i++;
                if($template->RevenueCateListID) {
                    $ItemID = 'A#'. $template->ItemNo;
                    $ParentID = 'A#'. $template->ParentNo;
                }
                if($template->ExpenseCateListID) {
                    $ItemID = 'B#'. $template->ItemNo;
                    $ParentID = 'B#'. $template->ParentNo;
                }
                $insertArr[] = [
                    'STT' => $template->Index,
                    'ItemID' => $ItemID,
                    'ParentID' => $ParentID,
                    'Level' => $template->Level,
                    'ItemName' => $template->ItemName,
                    'Detail' => '1',
                ];
            }
            DB::table('table_tmp')->insert($insertArr);
        }
    }

    // -> A
    public function OPTB200DTDHI1($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(A.FCDebitAmount) AS I1, A.ConAccountNo, A.RevenueNo
        FROM act_gl_books AS A
        WHERE LEFT(A.ConAccountNo, 4) = '0171'
        GROUP BY A.ConAccountNo, A.RevenueNo
        ) AS HH SET TT.I1 = HH.I1 WHERE left(TT.ItemID,2) = 'A#' AND TT.Detail =0 AND TT.ConAccountNo = HH.ConAccountNo  AND TT.RevenueNo = HH.RevenueNo";
        DB::statement($sqlUpdate);
    }
    //
    public function OPTB200DTDHI2($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(A.FCDebitAmount) AS I2, A.ConAccountNo, A.RevenueNo
        FROM act_gl_books AS A
        WHERE LEFT(A.ConAccountNo, 4) = '0151'
        GROUP BY A.ConAccountNo, A.RevenueNo
        ) AS HH SET TT.I2 = HH.I2 WHERE left(TT.ItemID,2) = 'A#' AND TT.Detail =0 AND TT.ConAccountNo = HH.ConAccountNo  AND TT.RevenueNo = HH.RevenueNo";
        DB::statement($sqlUpdate);
    }
    //
    public function OPTB200DTDHI3($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(A.FCDebitAmount) AS I3, A.ConAccountNo, A.RevenueNo
        FROM act_gl_books AS A
        WHERE LEFT(A.ConAccountNo, 4) = '0111'
        GROUP BY A.ConAccountNo, A.RevenueNo
        ) AS HH SET TT.I3 = HH.I3 WHERE left(TT.ItemID,2) = 'A#' AND TT.Detail =0 AND TT.ConAccountNo = HH.ConAccountNo  AND TT.RevenueNo = HH.RevenueNo";
        DB::statement($sqlUpdate);
    }
    //
    public function OPTB200DTDHI4($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(A.FCDebitAmount) AS I4, A.ConAccountNo, A.RevenueNo
        FROM act_gl_books AS A
        WHERE LEFT(A.ConAccountNo, 4) = '0121'
        GROUP BY A.ConAccountNo, A.RevenueNo
        ) AS HH SET TT.I4 = HH.I4 WHERE left(TT.ItemID,2) = 'A#' AND TT.Detail =0 AND TT.ConAccountNo = HH.ConAccountNo  AND TT.RevenueNo = HH.RevenueNo";
        DB::statement($sqlUpdate);
    }
    // -> B
    public function OPTB200DTDHI1_B($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(A.FCDebitAmount) AS I1, A.ConAccountNo, A.ExpenseNo
        FROM act_gl_books AS A
        WHERE LEFT(A.ConAccountNo, 4) = '0271'
        GROUP BY A.ConAccountNo, A.ExpenseNo
        ) AS HH SET TT.I1 = HH.I1 WHERE left(TT.ItemID,2) = 'B#' AND TT.Detail =0 AND TT.ConAccountNo = HH.ConAccountNo  AND TT.ExpenseNo = HH.ExpenseNo";
        DB::statement($sqlUpdate);
    }
    //
    public function OPTB200DTDHI2_B($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(A.FCDebitAmount) AS I2, A.ConAccountNo, A.ExpenseNo
        FROM act_gl_books AS A
        WHERE LEFT(A.ConAccountNo, 4) = '0251'
        GROUP BY A.ConAccountNo, A.ExpenseNo
        ) AS HH SET TT.I2 = HH.I2 WHERE left(TT.ItemID,2) = 'B#' AND TT.Detail =0 AND TT.ConAccountNo = HH.ConAccountNo  AND TT.ExpenseNo = HH.ExpenseNo";
        DB::statement($sqlUpdate);
    }
    //
    public function OPTB200DTDHI3_B($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(A.FCDebitAmount) AS I3, A.ConAccountNo, A.ExpenseNo
        FROM act_gl_books AS A
        WHERE LEFT(A.ConAccountNo, 4) = '0211'
        GROUP BY A.ConAccountNo, A.ExpenseNo
        ) AS HH SET TT.I3 = HH.I3 WHERE left(TT.ItemID,2) = 'B#' AND TT.Detail =0 AND TT.ConAccountNo = HH.ConAccountNo  AND TT.ExpenseNo = HH.ExpenseNo";
        DB::statement($sqlUpdate);
    }
    //
    public function OPTB200DTDHI4_B($whereCondition){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT SUM(A.FCDebitAmount) AS I4, A.ConAccountNo, A.ExpenseNo
        FROM act_gl_books AS A
        WHERE LEFT(A.ConAccountNo, 4) = '0221'
        GROUP BY A.ConAccountNo, A.ExpenseNo
        ) AS HH SET TT.I4 = HH.I4 WHERE left(TT.ItemID,2) = 'B#' AND TT.Detail =0 AND TT.ConAccountNo = HH.ConAccountNo  AND TT.ExpenseNo = HH.ExpenseNo";
        DB::statement($sqlUpdate);
    }
    //D, E
    public function OPTB200DTDHI_D1($whereCondition, $preYear, $nextYear, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT
            MAX(CASE WHEN ConAccountNo = '0171' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN ConAccountNo = '0151' AND B.YearD = $nextYear THEN I END) AS I2,
            MAX(CASE WHEN ConAccountNo = '0111' AND B.YearD = $nextYear THEN I END) AS I3,
            MAX(CASE WHEN ConAccountNo = '0121' AND B.YearD = $year THEN I END) AS I4
            FROM (SELECT SUM(FCDebitAmount) AS I, A.ConAccountNo, RC.CateValue AS CateValue, YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN capital_cate AS RC ON RC.CapitalID = A.CapitalID
            INNER JOIN capital_cate_list AS SC ON SC.CateID = RC.CateID
            WHERE SC.CateNo = '1500' AND RC.CateValue = 1
            $whereCondition
            GROUP BY A.ConAccountNo) AS B) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = 'D#1'";
        DB::statement($sqlUpdate);
    }
    public function OPTB200DTDHI_D2($whereCondition, $preYear, $nextYear, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT
            MAX(CASE WHEN ConAccountNo = '0171' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN ConAccountNo = '0151' AND B.YearD = $nextYear THEN I END) AS I2,
            MAX(CASE WHEN ConAccountNo = '0111' AND B.YearD = $nextYear THEN I END) AS I3,
            MAX(CASE WHEN ConAccountNo = '0121' AND B.YearD = $year THEN I END) AS I4
            FROM (SELECT SUM(FCDebitAmount) AS I, A.ConAccountNo, RC.CateValue AS CateValue, YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN capital_cate AS RC ON RC.CapitalID = A.CapitalID
            INNER JOIN capital_cate_list AS SC ON SC.CateID = RC.CateID
            WHERE SC.CateNo = '1500' AND RC.CateValue = 2
            $whereCondition
            GROUP BY A.ConAccountNo) AS B) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = 'D#2'";
        DB::statement($sqlUpdate);
    }
    public function OPTB200DTDHI_E1($whereCondition, $preYear, $nextYear, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT
            MAX(CASE WHEN ConAccountNo = '0271' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN ConAccountNo = '0251' AND B.YearD = $nextYear THEN I END) AS I2,
            MAX(CASE WHEN ConAccountNo = '0211' AND B.YearD = $nextYear THEN I END) AS I3,
            MAX(CASE WHEN ConAccountNo = '0221' AND B.YearD = $year THEN I END) AS I4
            FROM (SELECT SUM(FCDebitAmount) AS I, A.ConAccountNo, RC.CateValue AS CateValue, YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN capital_cate AS RC ON RC.CapitalID = A.CapitalID
            INNER JOIN capital_cate_list AS SC ON SC.CateID = RC.CateID
            WHERE SC.CateNo = '1400' AND RC.CateValue = 2
            $whereCondition
            GROUP BY A.ConAccountNo) AS B) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = 'E#1'";
        DB::statement($sqlUpdate);
    }
    public function OPTB200DTDHI_E2($whereCondition, $preYear, $nextYear, $year){
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT
            MAX(CASE WHEN ConAccountNo = '0271' AND B.YearD = $preYear THEN I END) AS I1,
            MAX(CASE WHEN ConAccountNo = '0251' AND B.YearD = $nextYear THEN I END) AS I2,
            MAX(CASE WHEN ConAccountNo = '0211' AND B.YearD = $nextYear THEN I END) AS I3,
            MAX(CASE WHEN ConAccountNo = '0221' AND B.YearD = $year THEN I END) AS I4
            FROM (SELECT SUM(FCDebitAmount) AS I, A.ConAccountNo, RC.CateValue AS CateValue, YEAR(A.PeriodFromDate)AS YearD
            FROM act_gl_books AS A
            INNER JOIN capital_cate AS RC ON RC.CapitalID = A.CapitalID
            INNER JOIN capital_cate_list AS SC ON SC.CateID = RC.CateID
            WHERE SC.CateNo = '1400' AND RC.CateValue = 1
            $whereCondition
            GROUP BY A.ConAccountNo) AS B) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = 'E#2'";
        DB::statement($sqlUpdate);
    }
    //
    public function sumUpOPTB200DTDH($start, $end, $maxLevel){
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4 FROM table_tmp M
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
                    SET I1 = I1 + ".$I1.", I2 = I2 + ".$I2.", I3 = I3 + ".$I3.", I4 = I4 + ".$I4."
                    WHERE ItemID = '".$value['ParentID']."'
                        AND table_tmp.Level = $j"; //Log::debug($update);
                        DB::statement($update);
                    }
                }
            }
        }
    }
    //CÂN ĐỐI DỰ TOÁN NSNN CHI TIẾT THEO KỲ NGÂN SÁCH

}
