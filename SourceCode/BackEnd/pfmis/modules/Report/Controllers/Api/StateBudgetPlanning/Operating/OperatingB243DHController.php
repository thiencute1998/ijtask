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

class OperatingB243DHController extends Controller {
    use Common;
    public function Deleterownull($value){
        if(!$value){ $value= 1;}
        $sql = "DELETE FROM `table_tmp` WHERE  $value";
        DB::statement($sql);
    }
    //B243-DT-DH -
    public function SBPB243DH(Request $request)
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

        $whereCondition = '';
        $whereCondition .= "AND YEAR(A.PeriodFromDate) BETWEEN $year - 2 AND $year AND A.InTransTypeID NOT IN (16,17,18) ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(10) DEFAULT '',
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
            `SectorCateValue` varchar(10) NOT NULL,
          `Detail` tinyint(1) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        //Cấp 1
//        $this->insertRevenueI($whereCondition, $year);
//        $this->getParentDataTreeForB200(['revenue_cate_list','revenue'],'A#1');
        // Chi
        $this->insertExpense($whereCondition, $year);
        $this->getParentDataTreeForB200([['Sector_cate_value','201'],'expense_cate_list','expense']);
        $this->setLevel();
        $this->updateIndexReportB200('B');
        $insertArr1 = [
            ['STT'=>'', 'ItemID'=> '', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG CHI', 'FontWeight'=> '1'],
        ];
        //Model::insert($data); // Eloquent approach
        DB::table('table_tmp')->insert($insertArr1); // Query Builder approach
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();

        $this->sumUpSBPB243DH(0,6, $maxLevel);
        $this->updateExpenseLevel();
        //Xóa dòng trống
        //$checknull1="(LEFT(ItemID, 2)='A#' OR LEFT(ItemID, 2)='B#') AND I1=0 AND I2=0 AND I3=0 AND I4=0";
        //$this->Deleterownull($checknull1);
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
                "ReportName" => "CÂN ĐỐI DỰ TOÁN NSNN NĂM " . $year. " CHI TIẾT THEO LĨNH VỰC",
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

    public function sumUpSBPB243DH($start, $end, $maxLevel){
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


    public function insertExpense($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Expense, Path, SectorCateValue, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', Expense), ParentID, ItemName, Expense, Path, SectorCateValue, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4 FROM
		(SELECT CONCAT('A#',SC.CateValue,'#',ECL.Path) AS ParentID, A.ExpenseName as ItemName, A.ExpenseNo as Expense, ECL.Path,
        A.AccountNo, SC.CateValue as SectorCateValue, YEAR(A.PeriodFromDate) AS YEARD, SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN expense_cate_list as ECL on EC.CateID = ECL.CateID
		INNER JOIN sector_cate as SC on SC.SectorID= A.SectorID
		WHERE ( EC.CateNo BETWEEN '20051' AND '20071') AND(LEFT(A.AccountNo, 4) = '0461' OR LEFT(A.AccountNo, 4) = '0441' OR LEFT(A.AccountNo, 4) = '0721' OR LEFT(A.AccountNo, 4) = '0411')
        AND SC.CateNo = '201'
		  $whereCondition
        GROUP BY A.AccountNo, A.ExpenseNo, YEAR(A.PeriodFromDate), SC.CateValue) as CH GROUP BY Expense,SectorCateValue";

        DB::statement($sql);
    }
    public function updateExpenseLevel(){
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
