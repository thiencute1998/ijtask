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

class OperatingB204DHController extends Controller {
    use Common;
    public function Deleterownull($value){
        if(!$value){ $value= 1;}
        $sql = "DELETE FROM `table_tmp` WHERE  $value";
        DB::statement($sql);
    }
    public function SBPB204DH(Request $request)
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


        if (isset($filter['CompanyID'])) {
            $note .= 'đơn vị ' . $filter['CompanyName'];
            $company = Company::where('CompanyID',$filter['CompanyID'])->first();
        }
        else{
            $company = Company::where('CompanyID',Auth::user()->CompanyID)->first();
        }

        $whereDirection = '';
        if ($directionID) {
            $whereDirection = 'AND A.DirectionID = ' . $directionID . ' ';
        }

        // TODO check isFinalTrans: AND A.isFinalTrans = 1

        if(isset($filter['ProvinceNo'])){
            $provinceNo = $filter['ProvinceNo'];
        }
        else{

            $provinceNo = $company->ProvinceNo;
        }
        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN $year - 2 AND $year AND A.InTransTypeID NOT IN (16,17,18) ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(20) DEFAULT '',
          `ItemID` varchar(100) NOT NULL PRIMARY KEY,
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `Year` varchar(5) DEFAULT '',
          `Revenue` varchar(50) DEFAULT '',
          `Expense` varchar(15) DEFAULT '',
          `I1` double(20,4) DEFAULT 0 NOT NULL,
          `I2` double(20,4) DEFAULT 0 NOT NULL,
          `I3` double(20,4) DEFAULT 0 NOT NULL,
          `I4` double(20,4) DEFAULT 0 NOT NULL,
            `Path` varchar(255) DEFAULT '',
            `District` varchar(20) DEFAULT '',
            `LocationTemporary` varchar(20) DEFAULT '',
          `Detail` tinyint(1) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $this->createTemporaryLocation();
        $insertA = ['ItemID'=> '01','ItemName'=> 'Địa bàn khối tỉnh'];
        DB::table('LocationTemporary')->insert($insertA);

        // Thu
        $this->insertRevenue($whereCondition, $year, $provinceNo);
        $this->getParentDataTreeForB200(['district','revenue_cate_list','revenue'],'A');
        $this->insertRevenueLocation($whereCondition, $year, $provinceNo);
        $this->getParentDataTreeForB200([['LocationTemporary', '01'],'revenue_cate_list','revenue'],'A');

        // Chi
        $whereExpense = "";
        for($i = 20051; $i<= 20069; $i++){
            $whereExpense .= "LEFT(EC.CateNo, 5) = '". $i ."'";
            if($i != 20069){
                $whereExpense .= " OR ";
            }
        }
        $whereConditionExpense = " AND ($whereExpense" . ")";

        $this->insertExpense($whereCondition, $whereConditionExpense, $year, $provinceNo);
        $this->getParentDataTreeForB200(['district','expense_cate_list','expense'],'B');
        $this->insertExpenseLocation($whereCondition, $whereConditionExpense, $year, $provinceNo);
        $this->getParentDataTreeForB200([['LocationTemporary', '01'],'expense_cate_list','expense'],'B');

        $this->setLevel();

        // Thiết lập STT
        $this->updateIndexReport('A');
        $this->updateIndexReport('B');
        $insertArr1 = [
            ['STT'=>'A', 'ItemID'=> 'A', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG THU', 'FontWeight'=> '1'],
            ['STT'=>'B', 'ItemID'=> 'B', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG CHI', 'FontWeight'=> '1'],
            ['STT'=>'C', 'ItemID'=> 'C', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'BỘI CHI/ BỘI THU', 'FontWeight'=> '1'],
        ];
        DB::table('table_tmp')->insert($insertArr1); // Query Builder approach

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $this->sumUpSBPB204DH(0,6, $maxLevel);
        //insert Bội Thu/ Bội Chi

        $this->insertC();

        // Thiết lập STT Item C
        $this->updateIndexReport('C');

        //insert Level 1
        $this->insertLocation();

        $ArrIA= DB::table('table_tmp')->where('ItemID','=','A')->first();
        $ArrIB= DB::table('table_tmp')->where('ItemID','=','B')->first();
        $IA1 = $ArrIA->I1; $IA2 = $ArrIA->I2; $IA3 = $ArrIA->I3; $IA4 = $ArrIA->I4;
        $IB1 = $ArrIB->I1; $IB2 = $ArrIB->I2; $IB3 = $ArrIB->I3; $IB4 = $ArrIB->I4;
        //Update To C
        $IC1 =  (double)$IA1 - (double)$IB1; $IC2 =  (double)$IA2 - (double)$IB2; $IC3 =  (double)$IA3 - (double)$IB3; $IC4 =  (double)$IA4 - (double)$IB4;
        $update = "UPDATE table_tmp
                    SET I1 = '".$IC1."', I2 = '".$IC2."', I3 = '".$IC3."', I4 = '".$IC4."'
                    WHERE ItemID = 'C#' ";//Log::debug($update);
        DB::statement($update);
        //
        $data = DB::table("table_tmp")->orderBy('ItemID', 'asc')->get();
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

        $companyName = $company->CompanyName;
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
                "ReportName" => "CÂN ĐỐI DỰ TOÁN NSNN NĂM " . $year. " CHI TIẾT THEO ĐỊA BÀN",
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

    // SUM
    public function sumUpSBPB204DH($start, $end, $maxLevel){
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

    public function insertRevenueLocation($whereCondition, $year, $provinceNo){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Revenue, Path, LocationTemporary, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', Revenue), ParentID, ItemName, Revenue, Path, '01', 1,
        MAX(CASE WHEN AccountNo = '0361' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0711' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0311' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('A#01','#',RCL.Path) AS ParentID, A.RevenueName as ItemName, A.RevenueNo as Revenue, RCL.Path,
        A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
        INNER JOIN revenue_cate_list as RCL on RC.CateID = RCL.CateID
		INNER JOIN company as C on A.InputCompanyID = C.CompanyID
		INNER JOIN company_cate as CC on C.CompanyID = CC.CompanyID
		WHERE A.AccountNo IN ('0361','0341','0711','0311')
        AND C.ProvinceNo = '" .$provinceNo ."'
        AND CC.CateNo = '1200' AND CC.CateValue = 2
        AND A.BudgetLevel IN (2,3,4)
        AND LEFT(RC.CateNo, 5) = '20011'
        $whereCondition
        GROUP BY A.AccountNo, A.RevenueNo, YEAR(A.PeriodFromDate)) as CH GROUP BY Revenue";

        DB::statement($sql);
    }

    public function insertRevenue($whereCondition, $year, $provinceNo){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Revenue, Path, District, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', Revenue), ParentID, ItemName, Revenue, Path, District, 1,
        MAX(CASE WHEN AccountNo = '0361' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0711' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0311' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('A#',C.DistrictNo,'#',RCL.Path) AS ParentID, A.RevenueName as ItemName, A.RevenueNo as Revenue, RCL.Path,
        A.AccountNo, C.DistrictNo as District, YEAR(A.PeriodFromDate) AS YEARD, SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
        INNER JOIN revenue_cate_list as RCL on RC.CateID = RCL.CateID
		INNER JOIN company as C on A.InputCompanyID = C.CompanyID
		INNER JOIN company_cate as CC on C.CompanyID = CC.CompanyID
		WHERE A.AccountNo IN ('0361','0341','0711','0311')
        AND C.ProvinceNo = '" .$provinceNo ."'
        AND CC.CateNo = '1200' AND CC.CateValue = 3
        AND A.BudgetLevel IN (2,3,4)
        AND LEFT(RC.CateNo, 5) = '20011'
        $whereCondition
        GROUP BY A.AccountNo, A.RevenueNo, YEAR(A.PeriodFromDate), C.DistrictNo) as CH GROUP BY Revenue, District";

        DB::statement($sql);
    }

    public function insertExpenseLocation($whereCondition, $whereConditionExpense, $year, $provinceNo){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Expense, Path, LocationTemporary, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', Expense), ParentID, ItemName, Expense, Path, '01', 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('B#01','#',ECL.Path) AS ParentID, A.ExpenseName as ItemName, A.ExpenseNo as Expense, ECL.Path,
        A.AccountNo, YEAR(A.PeriodFromDate) AS YEARD, SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN expense_cate_list as ECL on EC.CateID = ECL.CateID
		INNER JOIN company as C on A.InputCompanyID = C.CompanyID
		INNER JOIN company_cate as CC on C.CompanyID = CC.CompanyID
		WHERE A.AccountNo IN ('0461','0441','0721','0411')
        AND C.ProvinceNo = '" .$provinceNo ."'
        AND CC.CateNo = '1200' AND CC.CateValue = 2
        AND A.BudgetLevel IN (1,2,3,4)
        $whereConditionExpense
        $whereCondition
        GROUP BY A.AccountNo, A.ExpenseNo, YEAR(A.PeriodFromDate)) as CH GROUP BY Expense";

        DB::statement($sql);
    }

    public function insertExpense($whereCondition, $whereConditionExpense, $year, $provinceNo){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Expense, Path, District, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', Expense), ParentID, ItemName, Expense, Path, District, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('B#',C.DistrictNo,'#',ECL.Path) AS ParentID, A.ExpenseName as ItemName, A.ExpenseNo as Expense, ECL.Path,
        A.AccountNo, C.DistrictNo as District, YEAR(A.PeriodFromDate) AS YEARD, SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN expense_cate_list as ECL on EC.CateID = ECL.CateID
		INNER JOIN company as C on A.InputCompanyID = C.CompanyID
		INNER JOIN company_cate as CC on C.CompanyID = CC.CompanyID
		WHERE A.AccountNo IN ('0461','0441','0721','0411')
        AND C.ProvinceNo = '" .$provinceNo ."'
        AND CC.CateNo = '1200' AND CC.CateValue = 3
        AND A.BudgetLevel IN (1,2,3,4)
        $whereConditionExpense
        $whereCondition
        GROUP BY A.AccountNo, A.ExpenseNo, YEAR(A.PeriodFromDate), C.DistrictNo) as CH GROUP BY Expense, District";

        DB::statement($sql);
    }

    public function createTemporaryLocation(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `LocationTemporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
    }

    public function insertLocation(){
        $result = DB::table('table_tmp')->orderBy('ItemID','ASC')->where('Detail','=',0)->where('Level','=',1)->where(DB::raw('LEFT(ItemID,1)'),'!=','C')->get();
        $insertArr = [];
        if($result){
            foreach($result as $key=> $item){
                $subItemID = explode('#',$item->ItemID);
                $insertArr[] = [
                    'ItemID'=> $subItemID[0] . '#' . sprintf("%04d",($key+1)) ,
                    'ItemName'=> $item->ItemName,
                    'I1'=> $item->I1,
                    'I2'=> $item->I2,
                    'I3'=> $item->I3,
                    'I4'=> $item->I4
                ];
            }
            if(!empty($insertArr)){
                DB::table('table_tmp')->insert($insertArr);
            }
        }
    }

    public function insertC(){
        $insertArr = [];
        $sql = "SELECT SUBSTR(ItemID,3) as SubItemID, ItemName, Level FROM table_tmp where Level = 1 GROUP BY SUBSTR(ItemID,3)";
        $result = DB::select($sql);
        if($result){
            $sumI1 = 0;
            $sumI2 = 0;
            $sumI3 = 0;
            $sumI4 = 0;
            foreach($result as $item){
                $sql1 = "SELECT * FROM table_tmp WHERE Level = 1 AND SUBSTR(ItemID, 3) = '" . $item->SubItemID . "' ORDER BY ItemID ASC";
                $result1 = DB::select($sql1);
                $result1 = (array)$result1;
                if(count($result1) == 2){
                    $I1 = $result1[0]->I1 - $result1[1]->I1;
                    $I2 = $result1[0]->I2 - $result1[1]->I2;
                    $I3 = $result1[0]->I3 - $result1[1]->I3;
                    $I4 = $result1[0]->I4 - $result1[1]->I4;
                }
                else{
                    if($result1[0]->ItemID[0] == 'A'){
                        $I1 = $result1[0]->I1;
                        $I2 = $result1[0]->I2;
                        $I3 = $result1[0]->I3;
                        $I4 = $result1[0]->I4;
                    }
                    else{
                        $I1 = - $result1[0]->I1;
                        $I2 = - $result1[0]->I2;
                        $I3 = - $result1[0]->I3;
                        $I4 = - $result1[0]->I4;
                    }
                }
                $sumI1 += $I1;
                $sumI2 += $I2;
                $sumI3 += $I3;
                $sumI4 += $I4;
                $insertArr[] = [
                    'ItemID'=> 'C#'. $item->SubItemID,
                    'ParentID'=> 'C',
                    'ItemName'=> $item->ItemName,
                    'Level'=> 1,
                    'I1'=> $I1,
                    'I2'=> $I2,
                    'I3'=> $I3,
                    'I4'=> $I4
                ];
            }
            if(!empty($insertArr)){
                DB::table('table_tmp')->insert($insertArr);
            }
            DB::table('table_tmp')->where('ItemID','=','C')->update(['I1'=> $sumI1,'I2'=> $sumI2, 'I3'=> $sumI3, 'I4'=> $sumI4]);
        }
    }


}
