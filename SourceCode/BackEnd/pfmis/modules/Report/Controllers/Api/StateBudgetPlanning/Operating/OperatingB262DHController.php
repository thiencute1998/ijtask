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

class OperatingB262DHController extends Controller
{
    use Common;

    public function BCDHB02DHCH()
    {
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


    public function SBPB262DH(Request $request)
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

        if (isset($filter['CompanyID'])) {
            $note .= 'đơn vị ' . $filter['CompanyName'];
            $company = Company::where('CompanyID', $filter['CompanyID'])->first();
            $subCompanyNo = substr($filter['CompanyNo'], 0, 3);
            $whereCompany = " AND LEFT(C.CompanyNo, 3) = '" . $subCompanyNo . "' ";

        } else {
            $company = Company::where('CompanyID', Auth::user()->CompanyID)->first();
        }

        if (isset($filter['ProvinceNo'])) {
            $provinceNo = $filter['ProvinceNo'];
        } else {
            $provinceNo = $company->ProvinceNo;
        }

        $whereDirection = '';
        if ($directionID) {
            $whereDirection = 'AND A.DirectionID = ' . $directionID . ' ';
        }

        // TODO check isFinalTrans: AND A.isFinalTrans = 1

        $whereCondition = "AND YEAR(A.PeriodFromDate) BETWEEN ($year - 2) AND $year AND A.InTransTypeID NOT IN (16,17,18) $whereCompany";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
            `STT` varchar(50) DEFAULT '',
            `ItemID` varchar(100) NOT NULL PRIMARY KEY,
            `ParentID` varchar(100) DEFAULT '',
            `Level` int(2) DEFAULT 1,
            `ItemName` varchar(255) DEFAULT NULL,
            `FontWeight` tinyint(1) DEFAULT 0,
            `Italic` tinyint(1) DEFAULT 0,
            `Center` tinyint(1) DEFAULT 0,
            `Item` varchar(20) DEFAULT '',
            `Company` varchar(20) DEFAULT '',
            `Path` varchar(255) DEFAULT '',
            `District` varchar(20) DEFAULT '',
            `I1` double(20,4) DEFAULT 0 NOT NULL,
            `I2` double(20,4) DEFAULT 0 NOT NULL,
            `I3` double(20,4) DEFAULT 0 NOT NULL,
            `I4` double(20,4) DEFAULT 0 NOT NULL,
            `I5` double(20,4) DEFAULT 0 NOT NULL,
            `I6` double(20,4) DEFAULT 0 NOT NULL,
            `I7` double(20,4) DEFAULT 0 NOT NULL,
            `I8` double(20,4) DEFAULT 0 NOT NULL,
            `I9` double(20,4) DEFAULT 0 NOT NULL,
            `I10` double(20,4) DEFAULT 0 NOT NULL,
            `I11` double(20,4) DEFAULT 0 NOT NULL,
            `I12` double(20,4) DEFAULT 0 NOT NULL,
            `I13` double(20,4) DEFAULT 0 NOT NULL,
            `I14` double(20,4) DEFAULT 0 NOT NULL,
            `I15` double(20,4) DEFAULT 0 NOT NULL,
            `I16` double(20,4) DEFAULT 0 NOT NULL,
            `I17` double(20,4) DEFAULT 0 NOT NULL,
            `I18` double(20,4) DEFAULT 0 NOT NULL,
            `I19` double(20,4) DEFAULT 0 NOT NULL,
            `I20` double(20,4) DEFAULT 0 NOT NULL,
            `I21` double(20,4) DEFAULT 0 NOT NULL,
            `I22` double(20,4) DEFAULT 0 NOT NULL,
            `Detail` tinyint(1) NOT NULL
        )   ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        // Thu
        $countRecords = DB::table('table_tmp')->count();

        $this->insertCompanyII($whereCondition, $year, $provinceNo);
        $this->insertOrUpdateCompanyII($whereCondition, $year, $provinceNo);
        if (DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeListing(['company'], 'A#01');
            $countRecords = DB::table('table_tmp')->count();
        }

        $this->insertDistrictII($whereCondition, $year, $provinceNo);
        $this->insertOrUpdateDistrictII($whereCondition, $year, $provinceNo);
        if (DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeCommon(['district', 'company'], 'A#02');
        }
        // Chi

        $this->setLevel();

        $this->sumUpRow();

        // Thiết lập STT
        $maxLevel = DB::table('table_tmp')->select(DB::raw('Max(Level) as maxLevel'))->first();
        $this->setIndexReportB262($maxLevel, 'A#01');
        $this->setIndexReportB262($maxLevel, 'A#02');

        //Cấp 1
        $insertArr1 = [
            ['STT' => '', 'ItemID' => 'A', 'ParentID' => '', 'Level' => 0, 'ItemName' => 'Tổng số', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'A', 'ItemID' => 'A#01', 'ParentID' => 'A', 'Level' => 1, 'ItemName' => 'Cấp tỉnh', 'FontWeight' => '1', 'Detail' => '0'],
            ['STT' => 'B', 'ItemID' => 'A#02', 'ParentID' => 'A', 'Level' => 1, 'ItemName' => 'Cấp huyện', 'FontWeight' => '1', 'Detail' => '0'],
        ];

        DB::table('table_tmp')->insert($insertArr1); // Query Builder approach

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumUpSBPB262DH(0, $maxLevel);

        // insert detail

        $data = DB::table("table_tmp")->orderBy('ItemID', 'asc')->get();
        $yearPre = (int)$year - 2;
        $yearNext = (int)$year - 1;

        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Thực hiện năm ' . $yearPre;
            $data[0]->HeaderName2 = 'Năm ' . $yearNext;
            $data[0]->HeaderName3 = 'Dự toán năm ' . $year;

        }


        $companyName = '';
        if ($company) {
            $companyName = $company->CompanyName;
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
                "ReportName" => "DỰ TOÁN CHI TIỀN LƯƠNG " . $year,
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


    public function insertCompanyII($whereCondition, $year, $provinceNo)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Company, Path, Detail, I1, I2, I7, I12, I13, I18)
        SELECT CONCAT(ParentID, '#', CompanyNo) as ItemID, ParentID, ItemName, CompanyNo, Path, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 AND NormCateValue = '3' THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 AND NormCateValue = '4' THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 AND NormCateValue = '3' THEN I ELSE 0 END) AS I7,
        MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 AND NormCateValue = '3' THEN I ELSE 0 END) AS I12,
        MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 AND NormCateValue = '4' THEN I ELSE 0 END) AS I13,
		MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year AND NormCateValue = '3' THEN I ELSE 0 END) AS I18 FROM
		(SELECT CONCAT('A#01#', SUBSTRING_INDEX(C.PathNo, '#', 1)) AS ParentID, C.CompanyName as ItemName,
        A.AccountNo, C.CompanyNo, C.PathNo as Path, NC.CateValue as NormCateValue, YEAR(A.PeriodFromDate) AS YEARD,
        SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
		INNER JOIN Company as C on A.InputCompanyID = C.CompanyID
		WHERE A.AccountNo IN ('0461','0441','0721','0341')
		AND NC.CateNo = '20214' AND NC.CateValue IN ('3','4')
		AND C.ProvinceNo = '" . $provinceNo . "'
		AND C.DistrictID IS NULL AND C.CommuneID IS NULL
        $whereCondition
        GROUP BY A.AccountNo, A.InputCompanyID, NC.CateValue, YEAR(A.PeriodFromDate)) as CH GROUP BY CompanyNo";

        DB::statement($sql);
    }

    public function insertOrUpdateCompanyII($whereCondition, $year, $provinceNo)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Company, Path, Detail, I4, I5, I6, I9, I10, I11, I15, I16, I17, I20, I21, I22)
        SELECT * FROM
        (SELECT CONCAT(ParentID, '#', CompanyNo) as ItemID, ParentID, ItemName, CompanyNo, Path, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 AND NormCateValue = '1' AND ExpenseCateValue = '1' THEN I ELSE 0 END) AS A1,
		MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 AND NormCateValue = '2' AND ExpenseCateValue = '2' THEN I ELSE 0 END) AS B1,
		MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 AND NormCateValue = '3' AND ExpenseCateValue = '3' THEN I ELSE 0 END) AS C1,
        MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 AND NormCateValue = '1' AND ExpenseCateValue = '1' THEN I ELSE 0 END) AS A2,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 AND NormCateValue = '2' AND ExpenseCateValue = '2' THEN I ELSE 0 END) AS B2,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 AND NormCateValue = '3' AND ExpenseCateValue = '3' THEN I ELSE 0 END) AS C2,
        MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 AND NormCateValue = '1' AND ExpenseCateValue = '1' THEN I ELSE 0 END) AS A3,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 AND NormCateValue = '2' AND ExpenseCateValue = '2' THEN I ELSE 0 END) AS B3,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 AND NormCateValue = '3' AND ExpenseCateValue = '3' THEN I ELSE 0 END) AS C3,
        MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year AND NormCateValue = '1' AND ExpenseCateValue = '1' THEN I ELSE 0 END) AS A4,
		MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year AND NormCateValue = '2' AND ExpenseCateValue = '2' THEN I ELSE 0 END) AS B4,
		MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year AND NormCateValue = '3' AND ExpenseCateValue = '3' THEN I ELSE 0 END) AS C4 FROM
		(SELECT CONCAT('A#01#', SUBSTRING_INDEX(C.PathNo, '#', 1)) AS ParentID, C.CompanyName as ItemName,
        A.AccountNo, C.CompanyNo, C.PathNo as Path, NC.CateValue as NormCateValue, EC.CateValue as ExpenseCateValue, YEAR(A.PeriodFromDate) AS YEARD,
        SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
		INNER JOIN Company as C on A.InputCompanyID = C.CompanyID
		WHERE A.AccountNo IN ('0461','0441','0721','0341')
		AND NC.CateNo = '2041111' AND NC.CateValue IN ('1','2','3')
		AND EC.CateNo = '20201' AND EC.CateValue IN ('1','2','3')
		AND C.ProvinceNo = '" . $provinceNo . "'
		AND C.DistrictID IS NULL AND C.CommuneID IS NULL
        $whereCondition
        GROUP BY A.AccountNo, A.InputCompanyID, NC.CateValue, EC.CateValue, YEAR(A.PeriodFromDate)) as CH GROUP BY CompanyNo)
        as DT ON DUPLICATE KEY UPDATE
        I4 = A1, I5 = B1, I6 = C1, I9 = A2, I10 = B2, I11 = C2, I15 = A3, I16 = B3, I17 = C3, I20 = A4, I21 = B4, I22 = C4";

        DB::statement($sql);
    }

    public function insertDistrictII($whereCondition, $year, $provinceNo)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, District, Company, Detail, I1, I2, I7, I12, I13, I18)
        SELECT CONCAT(ParentID, '#', CompanyNo) as ItemID, ParentID, ItemName, DistrictNo, CompanyNo, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 AND NormCateValue = '3' THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 AND NormCateValue = '4' THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 AND NormCateValue = '3' THEN I ELSE 0 END) AS I7,
        MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 AND NormCateValue = '3' THEN I ELSE 0 END) AS I12,
        MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 AND NormCateValue = '4' THEN I ELSE 0 END) AS I13,
		MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year AND NormCateValue = '3' THEN I ELSE 0 END) AS I18 FROM
		(SELECT CONCAT('A#02#',C.DistrictNo) AS ParentID, C.CompanyName as ItemName,
        A.AccountNo, C.DistrictNo, C.CompanyNo, NC.CateValue as NormCateValue, YEAR(A.PeriodFromDate) AS YEARD,
        SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
		INNER JOIN Company as C on A.InputCompanyID = C.CompanyID
		WHERE A.AccountNo IN ('0461','0441','0721','0341')
		AND NC.CateNo = '20214' AND NC.CateValue IN ('3','4')
		AND C.ProvinceNo = '" . $provinceNo . "'
		AND C.DistrictID IS NOT NULl
        $whereCondition
        GROUP BY A.AccountNo, A.InputCompanyID, NC.CateValue, YEAR(A.PeriodFromDate)) as CH GROUP BY CompanyNo";

        DB::statement($sql);

    }

    public function insertOrUpdateDistrictII($whereCondition, $year, $provinceNo)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, District, Company, Detail, I4, I5, I6, I9, I10, I11, I15, I16, I17, I20, I21, I22)
        SELECT * FROM
        (SELECT CONCAT(ParentID, '#', CompanyNo) as ItemID, ParentID, ItemName, DistrictNo, CompanyNo, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 AND NormCateValue = '1' AND ExpenseCateValue = '1' THEN I ELSE 0 END) AS A1,
		MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 AND NormCateValue = '2' AND ExpenseCateValue = '2' THEN I ELSE 0 END) AS B1,
		MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 AND NormCateValue = '3' AND ExpenseCateValue = '3' THEN I ELSE 0 END) AS C1,
        MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 AND NormCateValue = '1' AND ExpenseCateValue = '1' THEN I ELSE 0 END) AS A2,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 AND NormCateValue = '2' AND ExpenseCateValue = '2' THEN I ELSE 0 END) AS B2,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 AND NormCateValue = '3' AND ExpenseCateValue = '3' THEN I ELSE 0 END) AS C2,
        MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 AND NormCateValue = '1' AND ExpenseCateValue = '1' THEN I ELSE 0 END) AS A3,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 AND NormCateValue = '2' AND ExpenseCateValue = '2' THEN I ELSE 0 END) AS B3,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 AND NormCateValue = '3' AND ExpenseCateValue = '3' THEN I ELSE 0 END) AS C3,
        MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year AND NormCateValue = '1' AND ExpenseCateValue = '1' THEN I ELSE 0 END) AS A4,
		MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year AND NormCateValue = '2' AND ExpenseCateValue = '2' THEN I ELSE 0 END) AS B4,
		MAX(CASE WHEN AccountNo = '0341' AND YEARD = $year AND NormCateValue = '3' AND ExpenseCateValue = '3' THEN I ELSE 0 END) AS C4 FROM
		(SELECT CONCAT('A#02#', C.DistrictNo) AS ParentID, C.CompanyName as ItemName,
        A.AccountNo, C.DistrictNo, C.CompanyNo, NC.CateValue as NormCateValue, EC.CateValue as ExpenseCateValue, YEAR(A.PeriodFromDate) AS YEARD,
        SUM(A.FCDebitAmount) AS I FROM act_gl_books as A
        INNER JOIN norm_cate as NC on A.NormID = NC.NormID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
		INNER JOIN Company as C on A.InputCompanyID = C.CompanyID
		WHERE A.AccountNo IN ('0461','0441','0721','0341')
		AND NC.CateNo = '2041111' AND NC.CateValue IN ('1','2','3')
		AND EC.CateNo = '20201' AND EC.CateValue IN ('1','2','3')
		AND C.ProvinceNo = '" . $provinceNo . "'
		AND C.DistrictID IS NOT NULl
        $whereCondition
        GROUP BY A.AccountNo, A.InputCompanyID, NC.CateValue, EC.CateValue, YEAR(A.PeriodFromDate)) as CH GROUP BY CompanyNo)
        as DT ON DUPLICATE KEY UPDATE
        I4 = A1, I5 = B1, I6 = C1, I9 = A2, I10 = B2, I11 = C2, I15 = A3, I16 = B3, I17 = C3, I20 = A4, I21 = B4, I22 = C4";

        DB::statement($sql);
    }

    // SUM
    public function sumUpSBPB262DH($start, $maxLevel)
    {
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID,
            SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
           SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8,
           SUM(M.I9) AS I9, SUM(M.I10) AS I10, SUM(M.I11) AS I11, SUM(M.I12) AS I12,
           SUM(M.I13) AS I13, SUM(M.I14) AS I14, SUM(M.I15) AS I15, SUM(M.I16) AS I16,
           SUM(M.I17) AS I17, SUM(M.I18) AS I18, SUM(M.I19) AS I19, SUM(M.I20) AS I20,
           SUM(M.I21) AS I21, SUM(M.I22) AS I22
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
                    $I12 = ($value['I12']) ? $value['I12'] : 0;
                    $I13 = ($value['I13']) ? $value['I13'] : 0;
                    $I14 = ($value['I14']) ? $value['I14'] : 0;
                    $I15 = ($value['I15']) ? $value['I15'] : 0;
                    $I16 = ($value['I16']) ? $value['I16'] : 0;
                    $I17 = ($value['I17']) ? $value['I17'] : 0;
                    $I18 = ($value['I18']) ? $value['I18'] : 0;
                    $I19 = ($value['I19']) ? $value['I19'] : 0;
                    $I20 = ($value['I20']) ? $value['I20'] : 0;
                    $I21 = ($value['I21']) ? $value['I21'] : 0;
                    $I22 = ($value['I22']) ? $value['I22'] : 0;
                    $update = "UPDATE table_tmp
                SET I1 =  " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ",
                I5 =  " . $I5 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . ",
                I9 =  " . $I9 . ", I10 = " . $I10 . ", I11 = " . $I11 . ", I12 = " . $I12 . ",
                I13 =  " . $I13 . ", I14 = " . $I14 . ", I15 = " . $I15 . ", I16 = " . $I16 . ",
                I17 =  " . $I17 . ", I18 = " . $I18 . ", I19 = " . $I19 . ", I20 = " . $I20 . ",
                I21 =  " . $I21 . ", I22 = " . $I22 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = $j"; //Log::debug($update);
                    DB::statement($update);
                }
            }
        }
    }

    public function sumUpRow()
    {
        $listItems = DB::table('table_tmp')->where('Detail', '=', 1)->get();
        foreach ($listItems as $item) {
            $I3 = $item->I4 + $item->I5 + $item->I6;
            $I8 = $item->I9 + $item->I10 + $item->I11;
            $I14 = $item->I15 + $item->I16 + $item->I17;
            $I19 = $item->I20 + $item->I21 + $item->I22;
            DB::table('table_tmp')->where('ItemID', '=', $item->ItemID)->update([
                'I3' => $I3, 'I8' => $I8,
                'I14' => $I14, 'I19' => $I19
            ]);
        }

    }

    public function setIndexReportB262($maxLevel, $id = null)
    {
        $insertArr = [];
        for ($i = 2; $i <= $maxLevel->maxLevel; $i++) {
            $insertArr1 = [];
            $where = '';
            if ($id !== null) {
                $where = "AND LEFT(ItemID, LENGTH('" . $id . "')) = '" . $id . "'";
            }
            $sql = "SELECT * FROM table_tmp WHERE Level = $i $where ORDER BY ItemID ASC ";
            $result = DB::select($sql);
            if ($i == 2) {
                foreach ($result as $key => $item) {
                    $insertArr1[] = [
                        'ItemID' => $item->ItemID,
                        'ParentID' => $item->ParentID,
                        'Level' => $item->Level,
                        'STT' => $key + 1
                    ];
                    DB::table('table_tmp')->where('ItemID', '=', $item->ItemID)->update(['STT' => $key + 1]);
                }
            } else {
                foreach ($insertArr[$i - 3] as $value) {
                    $x = 1;
                    foreach ($result as $key => $item) {
                        if ($value['ItemID'] == $item->ParentID) {
                            $insertArr1[] = [
                                'ItemID' => $item->ItemID,
                                'ParentID' => $item->ParentID,
                                'Level' => $item->Level,
                                'STT' => $value['STT'] . '.' . $x,
                            ];
                            DB::table('table_tmp')->where('ItemID', '=', $item->ItemID)->update(['STT' => $value['STT'] . '.' . $x]);
                            $x++;
                        }
                    }
                }
            }
            $insertArr[] = $insertArr1;
        }
    }


}

