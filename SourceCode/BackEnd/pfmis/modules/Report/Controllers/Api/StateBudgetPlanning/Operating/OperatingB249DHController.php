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

class OperatingB249DHController extends Controller {
    use Common;
    public function Deleterownull($value){
        if(!$value){ $value= 1;}
        $sql = "DELETE FROM `table_tmp` WHERE  $value";
        DB::statement($sql);
    }
    public function SBPB249DH(Request $request)
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
          `Year` varchar(5) DEFAULT 2021,
          `Project` varchar(50) DEFAULT '' ,
          `I1` double(20,4) DEFAULT 0 NOT NULL,
          `I2` double(20,4) DEFAULT 0 NOT NULL,
          `I3` double(20,4) DEFAULT 0 NOT NULL,
          `I4` double(20,4) DEFAULT 0 NOT NULL,
          `I5` double(20,4) DEFAULT 0 NOT NULL,
          `District` varchar (255) DEFAULT '',
            `Commune` varchar (255) DEFAULT '',
            `Company` varchar (255) DEFAULT '',
            `Path` varchar(255) DEFAULT '',
          `Detail` tinyint(1) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        // Tạo bảng tạm để tạo cây
        $countRecords = DB::table('table_tmp')->count();
        // Tỉnh
        $this->insertProvince($whereCondition, $year,$provinceNo);
        if (DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeListing(['company'], 'A#01');
            $countRecords = DB::table('table_tmp')->count();
        }
        // Huyện
        $this->insertDistrict($whereCondition, $year,$provinceNo);
        $this->insertCommune($whereCondition, $year,$provinceNo);

        if (DB::table('table_tmp')->count() > $countRecords) {
            $this->getParentDataTreeCommon(['district', 'company'], 'A#02');
        }

        $this->setLevel();
        // Thiết lập STT

        // Thêm dữ liệu mặc định
        $insertArr1 = [
            ['STT'=>'', 'ItemID'=> 'A', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG CHI', 'FontWeight'=> '1'],
            ['STT'=>'I','ItemID'=> 'A#01','ParentID'=>'A','Level'=> 1, 'ItemName'=> 'Đơn vị cấp tỉnh','FontWeight'=> '0'],
            ['STT'=>'II','ItemID'=> 'A#02','ParentID'=>'A','Level'=> 1,'ItemName'=> 'Đơn vị cấp huyện','FontWeight'=> '0'],
        ];
        DB::table('table_tmp')->insert($insertArr1);

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $this->sumUpSBPB249DH(0,6, $maxLevel);

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
                $data[$key]->I5 = round((((int)($data[$key]->I4) - (int)($data[$key]->I2))*100)/(int)($data[$key]->I2), 2);
            }
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';

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
                "ReportName" => "CÂN ĐỐI DỰ TOÁN VIỆN TRỢ KHÔNG HOÀN LẠI: NĂM " . $year,
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

    // Bảng tạm để tạo cây


    public function insertProvince($whereCondition, $year, $provinceNo)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Company, Path, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', CompanyNo) as ItemID, ParentID, ItemName, CompanyNo, Path, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2  THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1  THEN I ELSE 0 END) AS I2,
        MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1  THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year  THEN I ELSE 0 END) AS I4
        FROM (SELECT CONCAT('A#01#', SUBSTRING_INDEX(C.PathNo, '#', 1)) AS ParentID, C.CompanyName as ItemName,
        A.AccountNo, C.CompanyNo, C.PathNo as Path, YEAR(A.PeriodFromDate) AS YEARD,  SUM(A.FCDebitAmount) AS I
        FROM act_gl_books as A
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
		INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN company_cate as CC on C.CompanyID = CC.CompanyID
		WHERE A.AccountNo IN ('0461','0441','0721','0411')
		AND EC.CateNo = '2005100'
		AND CC.CateNo = '1200' AND CC.CateValue= '2'
		AND C.ProvinceNo = '" . $provinceNo . "'
		AND C.DistrictID IS NULL AND C.CommuneID IS NULL
        $whereCondition
        GROUP BY A.AccountNo, A.InputCompanyID, YEAR(A.PeriodFromDate)) as CH GROUP BY CompanyNo";

        DB::statement($sql);
    }
    public function insertDistrict($whereCondition, $year, $provinceNo)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, District, Company, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', CompanyNo) as ItemID, ParentID, ItemName, DistrictNo, CompanyNo, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2  THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1  THEN I ELSE 0 END) AS I2,
        MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1  THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4
FROM	(SELECT CONCAT('A#02#',C.DistrictNo) AS ParentID, C.CompanyName as ItemName,
        A.AccountNo, C.DistrictNo, C.CompanyNo, YEAR(A.PeriodFromDate) AS YEARD, SUM(A.FCDebitAmount) AS I
        FROM act_gl_books as A
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
		INNER JOIN Company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN company_cate as CC on C.CompanyID = CC.CompanyID
		WHERE A.AccountNo IN ('0461','0441','0721','0411')
		AND EC.CateNo = '2005100'
		AND CC.CateNo = '1200' AND CC.CateValue= '2'
		AND C.ProvinceNo = '" . $provinceNo . "'
		AND C.DistrictID IS NOT NULl AND C.CommuneID IS NULL
        $whereCondition
        GROUP BY A.AccountNo, A.InputCompanyID, YEAR(A.PeriodFromDate)) as CH GROUP BY CompanyNo";

        DB::statement($sql);

    }
    public function insertCommune($whereCondition, $year, $provinceNo)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, District,Commune, Company, Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID, '#', CompanyNo) as ItemID, ParentID, ItemName, DistrictNo, CommuneNo, CompanyNo, 1,
        MAX(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2  THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1  THEN I ELSE 0 END) AS I2,
        MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1  THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4
FROM	(SELECT CONCAT('A#02#',C.DistrictNo,'#',C.CommuneNo) AS ParentID, C.CompanyName as ItemName,
        A.AccountNo, C.DistrictNo, C.CommuneNo, C.CompanyNo,  YEAR(A.PeriodFromDate) AS YEARD, SUM(A.FCDebitAmount) AS I
        FROM act_gl_books as A
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
		INNER JOIN Company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN company_cate as CC on C.CompanyID = CC.CompanyID
		WHERE A.AccountNo IN ('0461','0441','0721','0411')
		AND EC.CateNo = '2005100'
		AND CC.CateNo = '1200' AND CC.CateValue= '2'
		AND C.ProvinceNo = '" . $provinceNo . "'
		AND C.DistrictID IS NOT NULl AND C.CommuneID IS NOT NULL
        $whereCondition
        GROUP BY A.AccountNo, A.InputCompanyID, YEAR(A.PeriodFromDate)) as CH GROUP BY CompanyNo";

        DB::statement($sql);

    }


    // SUM
    public function sumUpSBPB249DH($start, $end, $maxLevel){
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

}
