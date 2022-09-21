<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBPND312017NDCP;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class SBPND312017NDCPMB35Controller extends Controller {
    public function SBPND312017NDCP_MB35(Request $request){

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

        $whereCompany = '';
        if ($companyID) {
            $whereCompany .= " AND A.CompanyID = " . $companyID . ' ';
        }
        $whereDirection = '';
        if ($directionID) {
            $whereDirection = 'AND A.DirectionID = ' . $directionID . ' ';
        }

        $userCompany = DB::table('company')->where('CompanyID','=', Auth::user()->CompanyID)->first();
        $whereCommonCondition = '';
        if(in_array($userCompany->SumCompanyType, [2,3])){
            if($userCompany->SumCompanyType == 2){
                $whereCommonCondition .= " AND C.ManagementLevel = " . $userCompany->ManagementLevel;
            }
            else{
                // Tổng hợp theo ngành
                $whereCommonCondition .= " C.SectorNo = '" . $userCompany->SectorNo . "' AND C.ManagementLevel = " . $userCompany->ManagementLevel;
            }
            if($userCompany->ManagementLevel == 2){
                $whereCommonCondition .= " AND C.ProvinceID = $userCompany->ProvinceID";
            }
            else if($userCompany->ManagementLevel == 3){
                $whereCommonCondition .= " AND C.DistrictID = $userCompany->DistrictID";
            }
            else if($userCompany->ManagementLevel == 4){
                $whereCommonCondition .= " AND C.CommuneID = $userCompany->CommuneID";
            }

        }
        else{
            $whereCommonCondition .= " AND C.CompanyID = '" . $userCompany->CompanyID . "'";
        }
        $whereCommonCondition .= " AND A.BudgetLevel <= $userCompany->ManagementLevel AND A.BudgetLevel >= 2";

        $yearNext = (int)$year + 1;

        $whereCondition = " AND YEAR(A.PeriodFromDate) = $yearNext AND A.InTransTypeID NOT IN (16, 17, 18) $whereCommonCondition";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `SbiCategoryNo` varchar(20) DEFAULT '',
          `ConAccountNo` varchar(20) DEFAULT '',
          `ConCoAccountNo` varchar(20) DEFAULT '',
          `SbiCategoryValue` varchar(20) DEFAULT '',
          `ExpenseCateValue` varchar (20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
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
          `Detail` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $this->setDefaultData();

        $this->insertRIC2($whereCondition);
        $this->insertRIC3($whereCondition);
        $this->insertRIC9($whereCondition);
        $this->insertRIC10($whereCondition);
        $this->updateRII($whereCondition);
        $this->updateRIII($whereCondition);
        $this->updateRIV($whereCondition);
        $this->updateRV($whereCondition);
        $this->updateRVI($whereCondition);
        $this->updateRVII($whereCondition);

        $this->sumColumn();

        // sum up
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumRow($maxLevel, 0);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
            $data[$key]->I7 = ($data[$key]->I7) ? $data[$key]->I7 : '';
            $data[$key]->I8 = ($data[$key]->I8) ? $data[$key]->I8 : '';
            $data[$key]->I9 = ($data[$key]->I9) ? $data[$key]->I9 : '';
            $data[$key]->I10 = ($data[$key]->I10) ? $data[$key]->I10 : '';
            $data[$key]->I11 = ($data[$key]->I11) ? $data[$key]->I11 : '';
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
                "ReportName" => "DỰ TOÁN CHI NGÂN SÁCH CẤP TỈNH (HUYỆN, XÃ) CHO TỪNG CƠ QUAN, TỔ CHỨC THEO LĨNH VỰC NĂM " . $yearNext,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function setDefaultData(){
        $insert = [
            ['STT'=> '', 'ItemID'=> 'A', 'ParentID'=> '', 'ItemName'=> 'Tổng số', 'Level' => 0,'FontWeight'=> 1],
            ['STT'=> 'I', 'ItemID'=> 'A#01', 'ParentID'=> 'A', 'ItemName'=> 'CÁC CƠ QUAN, TỔ CHỨC', 'Level' => 1, 'FontWeight'=> 1],
            ['STT'=> 'II', 'ItemID'=> 'A#02', 'ParentID'=> 'A', 'ItemName'=> 'CHI TRẢ NỢ LÃI CÁC KHOẢN DO CHÍNH QUYỀN ĐỊA PHƯƠNG VAY', 'Level' => 1, 'FontWeight'=> 1],
            ['STT'=> 'III', 'ItemID'=> 'A#03', 'ParentID'=> 'A', 'ItemName'=> 'CHI BỔ SUNG QUỸ DỰ TRỮ TÀI CHÍNH', 'Level' => 1, 'FontWeight'=> 1],
            ['STT'=> 'IV', 'ItemID'=> 'A#04', 'ParentID'=> 'A', 'ItemName'=> 'CHI DỰ PHÒNG NGÂN SÁCH', 'Level' => 1, 'FontWeight'=> 1],
            ['STT'=> 'V', 'ItemID'=> 'A#05', 'ParentID'=> 'A', 'ItemName'=> 'CHI TẠO NGUỒN, ĐIỀU CHỈNH TIỀN LƯƠNG', 'Level' => 1, 'FontWeight'=> 1],
            ['STT'=> 'VI', 'ItemID'=> 'A#06', 'ParentID'=> 'A', 'ItemName'=> 'CHI BỔ SUNG CÓ MỤC TIÊU CHO NGÂN SÁCH CẤP DƯỚI', 'Level' => 1, 'FontWeight'=> 1],
            ['STT'=> 'VII', 'ItemID'=> 'A#07', 'ParentID'=> 'A', 'ItemName'=> 'CHI CHUYỂN NGUỒN SANG NGÂN SÁCH NĂM SAU', 'Level' => 1, 'FontWeight'=> 1],
        ];

        DB::table('table_tmp')->insert($insert);
    }

    public function insertRIC2($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Level, Detail, I2)
        SELECT CONCAT('A#01#', C.CompanyNo) as ItemID, 'A#01' as ParentID, C.CompanyName as ItemName, 2 as Level, 1 as Detail,
        SUM(LCDebitAmount) as I2 FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        WHERE A.AccountNo = '0411'
        AND LEFT(EC.CateNo, 5) = '20152'
        AND (CC.CateNo = '20012102' OR (CC.CateNo IN ('20012201', '20012202', '20012203', '20012204') AND CC.CateValue = '1'))
        AND A.ProjectNo != ''
        AND A.ProgramNo = ''
        $whereCondition
        GROUP BY C.CompanyNo
        ";

        DB::statement($sql);
    }

    public function insertRIC3($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Level, Detail, I3)
        SELECT * FROM
        (SELECT CONCAT('A#01#', C.CompanyNo) as ItemID, 'A#01' as ParentID, C.CompanyName as ItemName, 2 as Level, 1 as Detail,
        SUM(LCDebitAmount) as A1 FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        WHERE A.AccountNo = '0411'
        AND LEFT(EC.CateNo, 5) = '20151'
        AND (LEFT(CC.CateNo, 5) = '20011' OR LEFT(CC.CateNo, 6) = '200121')
        AND A.ProjectNo != ''
        AND A.ProgramNo = ''
        $whereCondition
        GROUP BY C.CompanyNo) as DT ON DUPLICATE KEY UPDATE I3 = A1
        ";

        DB::statement($sql);
    }

    public function insertRIC9($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Level, Detail, I9)
        SELECT * FROM
        (SELECT CONCAT('A#01#', C.CompanyNo) as ItemID, 'A#01' as ParentID, C.CompanyName as ItemName, 2 as Level, 1 as Detail,
        SUM(LCDebitAmount) as A1 FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        INNER JOIN program as P on A.ProgramID = P.ProgramID
        WHERE A.AccountNo = '0411'
        AND LEFT(EC.CateNo, 5) = '20152'
        AND (CC.CateNo = '20012102' OR (CC.CateNo IN ('20012201', '20012202', '20012203', '20012204') AND CC.CateValue = '1'))
        AND P.ProgramType = 1
        $whereCondition
        GROUP BY C.CompanyNo) as DT ON DUPLICATE KEY UPDATE I9 = A1
        ";

        DB::statement($sql);
    }

    public function insertRIC10($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Level, Detail, I10)
        SELECT * FROM
        (SELECT CONCAT('A#01#', C.CompanyNo) as ItemID, 'A#01' as ParentID, C.CompanyName as ItemName, 2 as Level, 1 as Detail,
        SUM(LCDebitAmount) as A1 FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        INNER JOIN program as P on A.ProgramID = P.ProgramID
        WHERE A.AccountNo = '0411'
        AND LEFT(EC.CateNo, 5) = '20151'
        AND (LEFT(CC.CateNo, 5) = '20011' OR LEFT(CC.CateNo, 6) = '200121')
        AND P.ProgramType = 1
        $whereCondition
        GROUP BY C.CompanyNo) as DT ON DUPLICATE KEY UPDATE I10 = A1
        ";

        DB::statement($sql);
    }

    public function updateRII($whereCondition){
        $sql = "UPDATE table_tmp as TT ,
        (SELECT SUM(LCDebitAmount) as I4 FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
        INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
        WHERE A.AccountNo = '0411'
        AND EC.CateNo = '20153' AND EC.CateValue = '1'
        AND CC.CateNo = '200193'
        $whereCondition
        ) as HH SET TT.I4 = HH.I4
        WHERE TT.ItemID = 'A#02'";

        DB::statement($sql);
    }

    public function updateRIII($whereCondition){
        $sql = "UPDATE table_tmp as TT ,
        (SELECT SUM(LCDebitAmount) as I5 FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
        WHERE A.AccountNo = '0411'
        AND EC.CateNo = '20154' AND EC.CateValue = '1'
        $whereCondition
        ) as HH SET TT.I5 = HH.I5
        WHERE TT.ItemID = 'A#03'";

        DB::statement($sql);
    }

    public function updateRIV($whereCondition){
        $sql = "UPDATE table_tmp as TT ,
        (SELECT SUM(LCDebitAmount) as I6 FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
        WHERE A.AccountNo = '0411'
        AND EC.CateNo = '20159' AND EC.CateValue = '1'
        $whereCondition
        ) as HH SET TT.I6 = HH.I6
        WHERE TT.ItemID = 'A#04'";

        DB::statement($sql);
    }

    public function updateRV($whereCondition){
        $sql = "UPDATE table_tmp as TT ,
        (SELECT SUM(LCDebitAmount) as I7 FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
        INNER JOIN capital_cate as CC on CC.CapitalID = A.CapitalID
        WHERE A.AccountNo = '0411'
        AND EC.CateNo = '20164' AND EC.CateValue = '1'
        AND CC.CateNo = '200194'
        $whereCondition
        ) as HH SET TT.I7 = HH.I7
        WHERE TT.ItemID = 'A#05'";

        DB::statement($sql);
    }

    public function updateRVI($whereCondition){
        $sql = "UPDATE table_tmp as TT ,
        (SELECT SUM(LCDebitAmount) as I1 FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
        WHERE EC.CateNo = '201610101'
        $whereCondition
        ) as HH SET TT.I1 = HH.I1
        WHERE TT.ItemID = 'A#06'";

        DB::statement($sql);
    }

    public function updateRVII($whereCondition){
        $sql = "UPDATE table_tmp as TT ,
        (SELECT SUM(LCDebitAmount) as I11 FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN expense_cate as EC on EC.ExpenseID = A.ExpenseID
        WHERE LEFT(EC.CateNo, 5) = '20161'
        $whereCondition
        ) as HH SET TT.I11 = HH.I11
        WHERE TT.ItemID = 'A#07'";

        DB::statement($sql);
    }

    // sum column
    public function sumColumn(){
        $listItems = DB::table('table_tmp')->where('ItemID','!=', 'A#06')->get();
        foreach($listItems as $item){
            if($item->ItemID == 'A#02'){
                $I1 = $item->I4;
            }
            else if($item->ItemID == 'A#03'){
                $I1 = $item->I5;
            }
            else if($item->ItemID == 'A#04'){
                $I1 = $item->I6;
            }
            else if($item->ItemID == 'A#05'){
                $I1 = $item->I7;
            }
            else if($item->ItemID == 'A#07'){
                $I1 = $item->I11;
            }
            else{
                $I1 = $item->I2 + $item->I3 + $item->I9 + $item->I10;
            }

            DB::table('table_tmp')->where('ItemID',$item->ItemID)->update([
                'I1'=> $I1
            ]);
        }
    }

    // sum up
    public function sumRow($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
            SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8,
            SUM(M.I9) AS I9, SUM(M.I10) AS I10, SUM(M.I11) AS I11
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
                    $I5 = ($value['I5']) ? $value['I5'] : 0;
                    $I6 = ($value['I6']) ? $value['I6'] : 0;
                    $I7 = ($value['I7']) ? $value['I7'] : 0;
                    $I8 = ($value['I8']) ? $value['I8'] : 0;
                    $I9 = ($value['I9']) ? $value['I9'] : 0;
                    $I10 = ($value['I10']) ? $value['I10'] : 0;
                    $I11 = ($value['I11']) ? $value['I11'] : 0;
                    $update = "UPDATE table_tmp
                SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ",
                I9 = " . $I9 . ", I10 = " . $I10 . ", I11 = " . $I11 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }
    }

}
