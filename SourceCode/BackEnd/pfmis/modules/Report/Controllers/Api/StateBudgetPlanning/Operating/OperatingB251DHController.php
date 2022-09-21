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

class OperatingB251DHController extends Controller {
    use Common;
    public function Deleterownull($value){
        if(!$value){ $value= 1;}
        $sql = "DELETE FROM `table_tmp` WHERE  $value";
        DB::statement($sql);
    }
    public function SBPB251DH(Request $request)
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
            $company = Company::where('CompanyID', $filter['CompanyID'])->first();

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
          `AreaTemporary` varchar (100) DEFAULT '',
          `ParentCompany` varchar (255) DEFAULT '',
          `Company` varchar (255) DEFAULT '',
          `Detail` tinyint(1) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        // Tạo bảng tạm để tạo cây
        $this->AreaTemporary();
        $insertA = [
            ['ItemID'=> '01','ItemName'=> 'Đơn vị cấp tỉnh'],
            ['ItemID'=> '02','ItemName'=> 'Đơn vị cấp Huyện']
        ];
        DB::table('AreaTemporary')->insert($insertA);

        $this->insertRIProject($whereCondition, $year, $provinceNo);
        $this->getParentDataTreeCommon(['company','project'],'A#01');
        $this->getParentCompany();

        // Chi
        $this->insertExpenseI($whereCondition, $year);
        $this->getParentDataTreeCommon(['District','Company','Project'],'A#02');
        $this->setLevel();
        // Thiết lập STT

        // Thêm dữ liệu mặc định
        $insertArr1 = [
            ['STT'=>'A', 'ItemID'=> 'A', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'TỔNG CHI', 'FontWeight'=> '1'],
            ['STT'=>'I','ItemID'=> 'B#01','ParentID'=>'B','Level'=> 1, 'ItemName'=> 'Đơn vị cấp tỉnh','FontWeight'=> '0'],
            ['STT'=>'II','ItemID'=> 'B#02','ParentID'=>'B','Level'=> 1,'ItemName'=> 'Đơn vị cấp huyện','FontWeight'=> '0'],
        ];
        DB::table('table_tmp')->insert($insertArr1);

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $this->sumUpSBPB251DH(0,6, $maxLevel);

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

    public function insertRIProject($whereCondition, $year, $provinceNo){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, ParentCompany, Company, Project,Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID,'#',Project) as ItemID, ParentID, ItemName, ParentNo, Company, Project, 1,
        SUM(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4
        FROM
        (SELECT CONCAT('A#','01#',C.PathNo) AS ParentID, A.ProjectName as ItemName,
        C.CompanyNo as Company, A.ProjectNo as Project, C.ParentNo, A.AccountNo,YEAR(A.PeriodFromDate) AS YEARD,
        SUM(A.LCDebitAmount) AS I
        FROM act_gl_books as A
		INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        INNER JOIN company as C on C.CompanyID = A.InputCompanyID
        INNER JOIN company_cate as CC on CC.CompanyID = A.InputCompanyID
        WHERE A.AccountNo in('0461','0441','0721','0411')
        AND CC.CateNo = '1200' AND CC.CateValue = '2'
        AND EC.CateNo = '2005102' AND EC.CateValue IN ('9','11','13')
        AND PC.CateNo = '019' AND PC.CateValue = 1
        AND C.Level = 2
        AND C.ProvinceNo = '" . $provinceNo . "'
        $whereCondition
        GROUP BY A.AccountNo, A.ProjectNo, YEAR(A.PeriodFromDate), A.InputCompanyID
        ) as CH GROUP BY Project, Company";

        DB::statement($sql);
    }

    // Bảng tạm để tạo cây

    public function AreaTemporary(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `AreaTemporary` (
         `STT` varchar(20) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
    }
    public function insertExpenseI($whereCondition, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, AreaTemporary, District, Company, Project,Detail, I1, I2, I3, I4)
        SELECT CONCAT(ParentID,'#',Project) as ItemID, ParentID, ItemName, AreaTemporary, District, Company, Project, 1,
        SUM(CASE WHEN AccountNo = '0461' AND YEARD = $year - 2 THEN I ELSE 0 END) AS I1,
		MAX(CASE WHEN AccountNo = '0441' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I2,
		MAX(CASE WHEN AccountNo = '0721' AND YEARD = $year - 1 THEN I ELSE 0 END) AS I3,
		MAX(CASE WHEN AccountNo = '0411' AND YEARD = $year THEN I ELSE 0 END) AS I4
        FROM
        (SELECT CONCAT('A#','02#', C.DistrictNo,'#',C.CompanyNo) AS ParentID, A.ProjectName as ItemName,'02' as AreaTemporary,
        C.DistrictNo as District, C.CompanyNo as Company, A.ProjectNo as Project, A.AccountNo,YEAR(A.PeriodFromDate) AS YEARD,
        SUM(A.LCDebitAmount) AS I
        FROM act_gl_books as A
		INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN project_cate as PC on PC.ProjectID = A.ProjectID
        INNER JOIN project_cate_list as PCL on PC.CateID = PCL.CateID
        INNER JOIN company as C on C.CompanyID = A.InputCompanyID
        INNER JOIN company_cate as CC on CC.CompanyID = A.InputCompanyID
        WHERE  A.AccountNo IN ('0461','0441','0721','0411')
        AND CC.CateNo = '1200' AND CC.CateValue='3'
        AND EC.CateNo = '2005102' AND EC.CateValue IN ('9','11','13')
        AND PC.CateNo = '019' AND PC.CateValue = 1
        $whereCondition
        GROUP BY A.AccountNo, A.ProjectNo, YEAR(A.PeriodFromDate), C.DistrictNo, A.InputCompanyID
        ) as CH GROUP BY Project, Company, District";

        DB::statement($sql);
    }


    // SUM
    public function sumUpSBPB251DH($start, $end, $maxLevel){
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

    public function getParentCompany(){
        $sql = "SELECT ParentCompany FROM table_tmp WHERE Detail = 1 AND ParentCompany != '' GROUP BY ParentCompany";
        $result = DB::select($sql);
        if($result){
            $insertArr = [];
            $listCompanyID = array_column($result, 'ParentCompany');
            $companies = DB::table('company')->whereIn('CompanyNo',$listCompanyID)->get();
            foreach($companies as $item){
                $insertArr[] = [
                    'ItemID'=> 'A#01#' . $item->CompanyNo,
                    'ParentID'=> 'A#01',
                    'ItemName'=> $item->CompanyName,
                ];
            }

            DB::table('table_tmp')->insertOrIgnore($insertArr);
        }


    }

}
