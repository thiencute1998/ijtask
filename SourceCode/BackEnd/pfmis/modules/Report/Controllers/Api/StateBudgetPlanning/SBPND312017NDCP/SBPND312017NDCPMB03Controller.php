<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBPND312017NDCP;

use IjCore\IjNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Module\Report\Traits\Common;
use PHPUnit\Framework\Constraint\Count;

class SBPND312017NDCPMB03Controller extends Controller {
    use Common;
    public function SBPND312017NDCP_MB03(Request $request){

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
            $provinceNo = $filter['ProvinceNo'];
        }
        else{
            $provinceNo = Company::where('CompanyID', '=', Auth::user()->CompanyID)->first()->ProvinceNo;
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

        $yearPre = (int)$year - 1;
        $yearNext = (int)$year + 1;

        $whereCondition = " AND A.PeriodType = 9 AND CP.ProvinceNo = '$provinceNo' AND A.InTransTypeID NOT IN (16, 17, 18) ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(20) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
            `ParentID1` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT '',
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          `I5` double(20,4) DEFAULT NULL,
          `I6` double(20,4) DEFAULT NULL,
          `I7` double(20,4) DEFAULT NULL,
            `Program` varchar(50) DEFAULT '',
            `Detail` int(11) DEFAULT NULL,

          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
            $templateTable = DB::table('report_sbp_312017ndcp_bm03')->get();

        $insertArr = [];
        foreach ($templateTable as $template) {
            $insertArr[] = [
                'STT' => $template->STT,
                'ItemID' => $template->ItemID,
                'ParentID' => $template->ParentID,
                'ParentID1' => $template->ParentID1,
                'Level' => $template->Level,
                'ItemName' => $template->ItemName,
                'FontWeight' => $template->FontWeight,
                'Italic' => $template->Italic,
                'Center' => $template->Center,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
                'I5' => $template->I5,
                'I6' => $template->I6,
                'I7' => $template->I7,
                'Program'=> null,
                'Detail'=> 0,
            ];
        }

        DB::table('table_tmp')->insert($insertArr);


        // caculator here
        $this->updateRI($whereCondition, '');
        $this->updateRI_I1_2($whereCondition, '');
        $this->updateRI1_2_3_4($whereCondition, '');
        $this->updateRIII1_2($whereCondition, '');

        $countRecords = DB::table('table_tmp')->count();

        $this->insertRII($whereCondition, $year);
        if(DB::table('table_tmp')->count() > $countRecords){
            $this->getParentDataTreeCommonDetail(['program','']);
        }
        $this->insertDetail();

        $this->setLevel();
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumCapital($maxLevel, 1);

        $this->sumUpColumn();

        // sum up
        $this->sumUpTableTmp($maxLevel);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('ItemID','ASC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
            $data[$key]->I7 = ($data[$key]->I7) ? $data[$key]->I7 : '';
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

    public function setCaseRII_I1_2(){
        return "CASE WHEN C.CapitalInOut = 1 THEN '01'
                     WHEN C.CapitalInOut = 2 THEN '02' END";
    }

    public function updateRI($whereCondition, $year){
        $sql = "UPDATE table_tmp as TT, (SELECT
            SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 2 THEN I ELSE 0 END) as I2,
            SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 3 THEN I ELSE 0 END) as I3,
            SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 2 THEN I ELSE 0 END) as I5,
            SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 3 THEN I ELSE 0 END) as I6
            FROM (
            SELECT A.BudgetLevel, A.AccountNo, SUM(LCDebitAmount) as I
            FROM act_gl_books as A
            INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN company as CP on A.InputCompanyID = CP.CompanyID
            WHERE A.AccountNo IN ('0441', '0442', '0411', '0412')
            AND LEFT(EC.CateNo, 5) = '20152'
            AND A.BudgetLevel IN (2, 3)
            AND C.BudgetStateType = 1
            AND A.ProjectID IS NOT NULL
            AND A.ProgramID IS NULL
            $whereCondition
            GROUP BY A.AccountNo, A.BudgetLevel
        ) as CH) as HH SET TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5, TT.I6 = HH.I6
        WHERE TT.ItemID = 'A#03'";

        DB::statement($sql);
    }

    public function updateRI_I1_2($whereCondition, $year){
        $sql = "UPDATE table_tmp as TT, (SELECT ItemID,
            SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 2 THEN I ELSE 0 END) as I2,
            SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 3 THEN I ELSE 0 END) as I3,
            SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 2 THEN I ELSE 0 END) as I5,
            SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 3 THEN I ELSE 0 END) as I6
            FROM (
            SELECT CONCAT('A#03#'," . $this->setCaseRII_I1_2() . ") as ItemID,
            A.BudgetLevel, A.AccountNo, SUM(LCDebitAmount) as I
            FROM act_gl_books as A
            INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN company as CP on A.InputCompanyID = CP.CompanyID
            WHERE A.AccountNo IN ('0441', '0442', '0411', '0412')
            AND LEFT(EC.CateNo, 5) = '20152'
            AND A.BudgetLevel IN (2, 3)
            AND C.BudgetStateType = 1
            AND C.CapitalInOut IN (1, 2)
            AND A.ProjectID IS NOT NULL
            AND A.ProgramID IS NULL
            $whereCondition
            GROUP BY A.AccountNo, A.BudgetLevel, C.CapitalInOut
        ) as CH GROUP BY ItemID) as HH SET TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5, TT.I6 = HH.I6
        WHERE TT.ItemID = HH.ItemID";

        DB::statement($sql);
    }

    public function setCaseRI1_2_3_4(){
        return "CASE WHEN CC.CateValue = '6' THEN '04'
                     WHEN CC.CateValue = '7' THEN '05'
                     WHEN CC.CateValue = '17' THEN '06'
                     WHEN CC.CateValue = '14' THEN '07' END";
    }

    public function updateRI1_2_3_4($whereCondition, $year){
        $sql = "UPDATE table_tmp as TT, (SELECT ItemID,
            SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 2 THEN I ELSE 0 END) as I2,
            SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 3 THEN I ELSE 0 END) as I3,
            SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 2 THEN I ELSE 0 END) as I5,
            SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 3 THEN I ELSE 0 END) as I6
            FROM (
            SELECT CONCAT('A#03#'," . $this->setCaseRI1_2_3_4() . ") as ItemID,
            A.BudgetLevel, A.AccountNo, SUM(LCDebitAmount) as I
            FROM act_gl_books as A
            INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN company as CP on A.InputCompanyID = CP.CompanyID
            WHERE A.AccountNo IN ('0441', '0442', '0411', '0412')
            AND LEFT(EC.CateNo, 5) = '20152'
            AND A.BudgetLevel IN (2, 3)
            AND C.BudgetStateType = 1
            AND CC.CateNo = '20012102' AND CC.CateValue IN ('6', '7', '17', '14')
            AND A.ProjectID IS NOT NULL
            AND A.ProgramID IS NULL
            $whereCondition
            GROUP BY A.AccountNo, A.BudgetLevel, CC.CateValue
        ) as CH GROUP BY ItemID) as HH SET TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5, TT.I6 = HH.I6
        WHERE TT.ItemID = HH.ItemID ";

        DB::statement($sql);
    }

    public function updateRIII1_2($whereCondition, $year){
        $sql = "UPDATE table_tmp as TT, (SELECT ItemID,
            SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 2 THEN I ELSE 0 END) as I2,
            SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel IN (3, 4) THEN I ELSE 0 END) as I3,
            SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 2 THEN I ELSE 0 END) as I5,
            SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel IN (3, 4) THEN I ELSE 0 END) as I6
            FROM (
            SELECT CONCAT('A#05#'," . $this->setCaseRII_I1_2() . ") as ItemID,
            A.BudgetLevel, A.AccountNo, SUM(LCDebitAmount) as I
            FROM act_gl_books as A
            INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
            INNER JOIN capital as C on A.CapitalID = C.CapitalID
            INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
            INNER JOIN company as CP on A.InputCompanyID = CP.CompanyID
            WHERE A.AccountNo IN ('0441', '0442', '0411', '0412')
            AND LEFT(EC.CateNo, 5) = '20152'
            AND A.BudgetLevel IN (2, 3, 4)
            AND CC.CateNo = '200195' AND CC.CateValue = '1'
            AND C.CapitalInOut IN (1, 2)
            AND A.ProjectID IS NOT NULL
            AND A.ProgramID IS NULL
            $whereCondition
            GROUP BY A.AccountNo, A.BudgetLevel, C.CapitalInOut
        ) as CH GROUP BY ItemID) as HH SET TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I5 = HH.I5, TT.I6 = HH.I6
        WHERE TT.ItemID = HH.ItemID ";

        DB::statement($sql);
    }

    public function setCaseNoRII(){
        return "CASE WHEN PC.CateValue = '1' THEN '03'
                    WHEN PC.CateValue = '2' THEN '04' END";
    }

    public function setCaseNameRII(){
        return "CASE WHEN C.CapitalInOut = 1 THEN 'Vốn trong nước'
                    WHEN C.CapitalInOut = 2 THEN 'Vốn nước ngoài' END";
    }

    public function insertRII($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ParentID1, ItemName, Program, Detail, I2, I3, I5, I6)
        SELECT CONCAT(ParentID, '#', CapitalInOut) as ItemID, ParentID, ParentID1, ItemName, ProgramNo, 1 as Detail,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel = 2 THEN I ELSE 0 END) as A1,
        SUM(CASE WHEN AccountNo IN ('0441', '0442') AND BudgetLevel IN (3, 4) THEN I ELSE 0 END) as A2,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel = 2 THEN I ELSE 0 END) as A3,
        SUM(CASE WHEN AccountNo IN ('0411', '0412') AND BudgetLevel IN (3, 4) THEN I ELSE 0 END) as A4
        FROM
        (SELECT CONCAT('A#04#', " . $this->setCaseNoRII() . ", '#', A.ProgramNo) as ParentID,
        CONCAT('A#04#', " . $this->setCaseNoRII() . ", '#0', C.CapitalInOut) as ParentID1,
         " . $this->setCaseNameRII() . " as ItemName,
        A.AccountNo, A.BudgetLevel, C.CapitalInOut, A.ProgramNo, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN capital as C on A.CapitalID = C.CapitalID
        INNER JOIN company as CP on A.CompanyID = CP.CompanyID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN program_cate as PC on A.ProgramID = PC.ProgramID
        WHERE A.AccountNo IN ('0441', '0442', '0411', '0412')
        AND LEFT(EC.CateNo, 5) = '20152'
        AND A.BudgetLevel IN (2, 3, 4)
        AND C.BudgetStateType = 1
        AND C.CapitalInOut IN (1, 2)
        AND PC.CateNo = '001' AND PC.CateValue IN ('1', '2')
        $whereCondition
        GROUP BY A.AccountNo, A.BudgetLevel,  C.CapitalInOut, A.ProgramNo) as CH GROUP BY ProgramNo, CapitalInOut
        ";

        DB::statement($sql);
    }

    public function insertDetail(){
        $sql = "SELECT * FROM table_tmp WHERE Program IS NOT NULL AND Detail != 1 GROUP BY Program";
        $result = DB::select($sql);
        if($result){
            $insertArr = [];
            foreach($result as $item){
                $insertArr[] = [
                    'ItemID'=> $item->ItemID . '#00',
                    'ParentID'=> '',
                    'ItemName'=> 'Trong đó',
                    'Italic'=> 1
                ];
            }
            DB::table('table_tmp')->insert($insertArr);
        }
    }


    public function sumCapital($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $sql = "SELECT ItemID, ParentID1, SUM(M.I2) AS I2, SUM(M.I3) AS I3,
           SUM(M.I5) AS I5, SUM(M.I6) AS I6
            FROM table_tmp M
            WHERE M.Level = " . $i . " GROUP BY ParentID1";
            $result = DB::select($sql);
            $result = (array)$result;
            if ($result) {
                foreach ($result as $value) {
                    $value = (array)$value;
                    $I2 = ($value['I2']) ? $value['I2'] : 0;
                    $I3 = ($value['I3']) ? $value['I3'] : 0;
                    $I5 = ($value['I5']) ? $value['I5'] : 0;
                    $I6 = ($value['I6']) ? $value['I6'] : 0;
                    $update = "UPDATE table_tmp
                SET I2 = " . $I2 . ", I3 = " . $I3 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . "
                WHERE ItemID = '" . $value['ParentID1'] . "'
                ";
                    DB::statement($update);
                }
            }
        }
    }

    public function sumUpColumn(){
        $listItems = DB::table('table_tmp')->get();
        foreach($listItems as $item){
            $I1 = $item->I2 + $item->I3;
            $I4 = $item->I5 + $item->I6;
            DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update([
                'I1'=> $I1,  'I4'=> $I4
            ]);
        }
    }

    // sum up
    public function sumUpTableTmp($maxLevel, $start = 0)
    {
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
           SUM(M.I5) AS I5, SUM(M.I6) AS I6
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
                    $update = "UPDATE table_tmp
                SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 = " . $I5 . ", I6 = " . $I6 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }
    }



}
