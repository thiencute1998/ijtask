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

class OperatingB241DHController extends Controller {
    use Common;
    public function Deleterownull($value){
        if(!$value){ $value= 1;}
        $sql = "DELETE FROM `table_tmp` WHERE  $value";
        DB::statement($sql);
    }
    public function SBPB241DH(Request $request)
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

        $whereCondition = '';
        $whereCondition .= "AND YEAR(A.PeriodFromDate) BETWEEN $year - 2 AND $year AND A.InTransTypeID NOT IN (16,17,18) ";
        $whereConditionPre = "AND YEAR(A.PeriodFromDate) = $yearPre AND YEAR(A.PeriodToDate) = $yearPre $whereCompany AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18 ";
        $whereConditionNext = "AND YEAR(A.PeriodFromDate) = $yearNext AND YEAR(A.PeriodToDate) = $yearNext $whereCompany $whereDirection AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18 ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(50) DEFAULT '',
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
            `Path` varchar(255) NOT NULL,
            `BudgetLevelTemporary` varchar(10) NOT NULL,
          `Detail` tinyint(1) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        // Tạo bảng tạm để tạo cây
        $this->createTemporaryTableBudgetLevel();
        $insertA = [
            ['ItemID'=> '1','ItemName'=> 'Ngân sách Trung Ương'],
            ['ItemID'=> '2','ItemName'=> 'Ngân sách Tỉnh'],
            ['ItemID'=> '3','ItemName'=> 'Ngân sách Huyện'],
            ['ItemID'=> '4','ItemName'=> 'Ngân sách Xã']
        ];
        DB::table('BudgetLevelTemporary')->insert($insertA);

        // Chi
        $this->insertExpenseI($whereCondition, $year);
        $this->insertExpenseII($whereCondition, $year);
        $this->insertExpenseIII($whereCondition, $year);
        $this->insertExpenseIV($whereCondition, $year);
        $this->getParentDataTreeForB200(['BudgetLevelTemporary','expense_cate_list','expense'],'B');
        $this->setLevel();

        // Thiết lập STT
        $this->updateIndexReportB200();

        // Thêm dữ liệu mặc định
        $insertArr1 = [
            ['STT'=>'', 'ItemID'=> 'B', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG CHI', 'FontWeight'=> '1','Center'=>'1'],
        ];
        DB::table('table_tmp')->insert($insertArr1);

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $this->sumUpSBPB241DH(0,6, $maxLevel);
        //update Bội Thu/Bội Chi
        $this->updateBudgetLevel();
        //Xóa dòng trống
        //$checknull1="(LEFT(ItemID, 2)='A#' OR LEFT(ItemID, 2)='B#') AND I1=0 AND I2=0 AND I3=0 AND I4=0";
        //$this->Deleterownull($checknull1);
        //
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
                "ReportName" => "DỰ TOÁN THU CHI NSNN NĂM " . $year . " THEO CẤP NGÂN SÁCH",
                "DateTime" => $dateTime,
                "ParentCompanyName" => 'UBND TỈNH VĨNH PHÚ',
                "CompanyName" => $companyName,
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    // Bảng tạm để tạo cây

    public function createTemporaryTableBudgetLevel(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `BudgetLevelTemporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
    }
    public function insertExpenseI($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Expense, Path, BudgetLevelTemporary, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', Expense), ParentID, ItemName, Expense, Path, BudgetLevelTemporary, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('B#',A.BudgetLevel,'#',ECL.Path) AS ParentID, A.ExpenseName as ItemName, A.ExpenseNo as Expense, ECL.Path,
        A.AccountNo, A.BudgetLevel as BudgetLevelTemporary, YEAR(A.PeriodFromDate) AS YEARD, SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN expense_cate_list as ECL on EC.CateID = ECL.CateID
		WHERE A.AccountNo IN ('0461','0441','0721','0411')
        AND A.BudgetLevel = 1
        AND EC.CateNo BETWEEN '20051' AND '20069'
        $whereCondition
        GROUP BY A.AccountNo, A.ExpenseNo, YEAR(A.PeriodFromDate)) as CH GROUP BY Expense";

        DB::statement($sql);
    }

    public function insertExpenseII($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Expense, Path, BudgetLevelTemporary, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', Expense), ParentID, ItemName, Expense, Path, BudgetLevelTemporary, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('B#',A.BudgetLevel,'#',ECL.Path) AS ParentID, A.ExpenseName as ItemName, A.ExpenseNo as Expense, ECL.Path,
        A.AccountNo, A.BudgetLevel as BudgetLevelTemporary, YEAR(A.PeriodFromDate) AS YEARD, SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN expense_cate_list as ECL on EC.CateID = ECL.CateID
		WHERE A.AccountNo IN ('0461','0441','0721','0411')
        AND A.BudgetLevel = 2
        AND EC.CateNo BETWEEN '20051' AND '20069'
        $whereCondition
        GROUP BY A.AccountNo, A.ExpenseNo, YEAR(A.PeriodFromDate)) as CH GROUP BY Expense";

        DB::statement($sql);
    }

    public function insertExpenseIII($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Expense, Path, BudgetLevelTemporary, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', Expense), ParentID, ItemName, Expense, Path, BudgetLevelTemporary, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('B#',A.BudgetLevel,'#',ECL.Path) AS ParentID, A.ExpenseName as ItemName, A.ExpenseNo as Expense, ECL.Path,
        A.AccountNo, A.BudgetLevel as BudgetLevelTemporary, YEAR(A.PeriodFromDate) AS YEARD, SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN expense_cate_list as ECL on EC.CateID = ECL.CateID
		WHERE A.AccountNo IN ('0461','0441','0721','0411')
        AND A.BudgetLevel = 3
        AND EC.CateNo BETWEEN '20051' AND '20069'
        $whereCondition
        GROUP BY A.AccountNo, A.ExpenseNo, YEAR(A.PeriodFromDate)) as CH GROUP BY Expense";

        DB::statement($sql);
    }

    public function insertExpenseIV($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Expense, Path, BudgetLevelTemporary, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', Expense), ParentID, ItemName, Expense, Path, BudgetLevelTemporary, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('B#',A.BudgetLevel,'#',ECL.Path) AS ParentID, A.ExpenseName as ItemName, A.ExpenseNo as Expense, ECL.Path,
        A.AccountNo, A.BudgetLevel as BudgetLevelTemporary, YEAR(A.PeriodFromDate) AS YEARD, SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN expense_cate_list as ECL on EC.CateID = ECL.CateID
		WHERE A.AccountNo IN ('0461','0441','0721','0411')
        AND A.BudgetLevel = 4
        AND EC.CateNo BETWEEN '20051' AND '20069'
        $whereCondition
        GROUP BY A.AccountNo, A.ExpenseNo, YEAR(A.PeriodFromDate)) as CH GROUP BY Expense";

        DB::statement($sql);
    }

    // SUM
    public function sumUpSBPB241DH($start, $end, $maxLevel){
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

    public function updateBudgetLevel(){
        $result = DB::table('table_tmp')->where('Detail','=',0)->where('Level','=',1)->get();
        $insertArr = [];
        if($result){
            foreach($result as $item){
                $itemID = explode('#',$item->ItemID);
                $subItemID = sprintf("%02d",($itemID[1]));
                $insertArr[] = [
                    'ItemID'=> $itemID[0] . '#' . $subItemID,
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

}
