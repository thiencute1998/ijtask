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

class SBPND312017NDCPMB38Controller extends Controller {
    use Common;
    public function SBPND312017NDCP_MB38(Request $request){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $userCompany = DB::table('company')->where('CompanyID', Auth::user()->CompanyID)->first();

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
            $provinceNo = $userCompany->ProvinceNo;
        }
        if (isset($filter['DistrictName'])) {
            $note .= 'huyện ' . $filter['DistrictName'] . ', ';
            $districtNo = $filter['DistrictNo'];
        }
        else{
            $districtNo = $userCompany->DistrictNo;
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

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `I1` double(20,4) DEFAULT 0,
          `I2` double(20,4) DEFAULT 0,
          `I3` double(20,4) DEFAULT 0,
          `I4` double(20,4) DEFAULT 0,
          `I5` double(20,4) DEFAULT 0,
          `I6` double(20,4) DEFAULT 0,
          `I7` double(20,4) DEFAULT 0,
          `I8` double(20,4) DEFAULT 0,
          `I9` double(20,4) DEFAULT 0,
          `I10` double(20,4) DEFAULT 0,
          `I11` double(20,4) DEFAULT 0,
          `I12` double(20,4) DEFAULT 0,
          `I13` double(20,4) DEFAULT 0,
          `I14` double(20,4) DEFAULT 0,
          `I15` double(20,4) DEFAULT 0,
          `I16` double(20,4) DEFAULT 0,
          `I17` double(20,4) DEFAULT 0,
          `Path` varchar(255) DEFAULT NULL,
          `Norder` int(10) DEFAULT 0,
          `Detail` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $nextYear = (int)$year + 1;
        $whereCondition = " AND YEAR(A.PeriodFromDate) = $nextYear AND A.AccountNo = '0411' AND A.InTransTypeID NOT IN (16, 17 ,18)";
        $typeUserCompany = DB::table('company_cate')->where('CompanyID', Auth::user()->CompanyID)
            ->where('CateNo', '1000')->whereNotNull('CateValue')
            ->orderBy('CateValue','ASC')->first();

        $insertArr = [
          'ItemID'=> 'A', 'ParentID'=> '', 'ItemName'=> 'Tổng số', 'FontWeight'=> 1
        ];
        DB::table('table_tmp')->insert($insertArr);

        $this->insertDataMB38($userCompany, $typeUserCompany, $whereCondition, $provinceNo, $districtNo);

        $this->setLevel();

        // sum up
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumRow($maxLevel, 0);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Chương trình mục tiêu quốc gia ' .$year;
            $data[0]->HeaderName2 = 'Chương trình mục tiêu '.$year;
        }
        foreach ($data as $key => $value) {
            $data[$key]->I5 = round( (int)($data[$key]->I6) + (int)($data[$key]->I7),2);
            $data[$key]->I8 = round( (int)($data[$key]->I9) + (int)($data[$key]->I10),2);
            $data[$key]->I12 = round( (int)($data[$key]->I13) + (int)($data[$key]->I14),2);
            $data[$key]->I15 = round( (int)($data[$key]->I16) + (int)($data[$key]->I17),2);
            $data[$key]->I11 = round( (int)($data[$key]->I12) + (int)($data[$key]->I15),2);
            $data[$key]->I2 = round( (int)($data[$key]->I5) + (int)($data[$key]->I12),2);
            $data[$key]->I3 = round( (int)($data[$key]->I8) + (int)($data[$key]->I15),2);
            $data[$key]->I4 = round( (int)($data[$key]->I5) + (int)($data[$key]->I8),2);
            $data[$key]->I1 = round( (int)($data[$key]->I2) + (int)($data[$key]->I3),2);
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
            $data[$key]->I7 = ($data[$key]->I7) ? $data[$key]->I7 : '';
            $data[$key]->I9 = ($data[$key]->I9) ? $data[$key]->I9 : '';
            $data[$key]->I10 = ($data[$key]->I10) ? $data[$key]->I10 : '';
            $data[$key]->I13 = ($data[$key]->I13) ? $data[$key]->I13 : '';
            $data[$key]->I14 = ($data[$key]->I14) ? $data[$key]->I14 : '';
            $data[$key]->I16 = ($data[$key]->I16) ? $data[$key]->I16 : '';
            $data[$key]->I17 = ($data[$key]->I17) ? $data[$key]->I17 : '';
        }

        $company = Company::find($companyID);
        $dateTime = "Vĩnh Phú, ngày $date tháng $month năm $yearC";
        if ($company) {
            $dateTime = $company->Address . ", ngày $date tháng $month năm $yearC";
        }
        $subReportName = "";
        if($userCompany->ManagementLevel == 2){
            $subReportName = "TỈNH (HUYỆN)";
        }
        else if($userCompany->ManagementLevel == 3){
            $subReportName = " HUYỆN (XÃ)";
        }

        $response = [
            'master' => [
                "Year" => "",
                "CreateName" => "Trần Thế Thái",
                "TransNo" => "00001",
                "ReportName" => "DỰ TOÁN CHI CHƯƠNG TRÌNH MỤC TIÊU QUỐC GIA NGÂN SÁCH CẤP $subReportName NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }


    // sum up
    public function sumRow($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4, SUM(M.I5) AS I5,
            SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8, SUM(M.I9) AS I9, SUM(M.I10) AS I10,
            SUM(M.I11) AS I11, SUM(M.I12) AS I12, SUM(M.I13) AS I13, SUM(M.I14) AS I14, SUM(M.I15) AS I15, SUM(M.I16) AS I16, SUM(M.I17) AS I17
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
                    $update = "UPDATE table_tmp
                SET I1 = I1 + " . $I1 . ", I2 = I2 + " . $I2 . ", I3 = I3 + " . $I3 . ", I4 = I4 + " . $I4 . ", I5 = I5 + " . $I5 . ",
                I6 = I6 + " . $I6 . ", I7 = I7 + " . $I7 . ", I8 = I8 + " . $I8 . ", I9 = I9 + " . $I9 . ", I10 = I10 + " . $I10 . ",
                I11 = I11 + " . $I11 . ", I12 = I12 + " . $I12 . ", I13 = I13 + " . $I13 . ", I14 = I14 + " . $I14 . ", I15 = I15 + " . $I15 . ", I16 = I16 + " . $I16 . ", I17 = I17 + " . $I17 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }

    }

    public function getParentPath(){
        return "REPLACE(C.Path, CONCAT('#', SUBSTRING_INDEX(C.Path, '#', -1)), '')";
    }

    public function insertCompany($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Path, Norder, Detail, I6, I7, I9, I10, I13, I14, I16, I17)
        SELECT CONCAT('A#01#', Path) as ItemID, ParentID, ItemName, Path, 1 as Norder, 1 as Detail,
        SUM(CASE WHEN ProgramCateValue = '1' AND ExpenseCateList = '20152' AND CapitalInOut = 1 THEN I ELSE 0 END) as I6,
        SUM(CASE WHEN ProgramCateValue = '1' AND ExpenseCateList = '20152' AND CapitalInOut = 2 THEN I ELSE 0 END) as I7,
        SUM(CASE WHEN ProgramCateValue = '1' AND ExpenseCateList = '20151' AND CapitalInOut = 1 THEN I ELSE 0 END) as I9,
        SUM(CASE WHEN ProgramCateValue = '1' AND ExpenseCateList = '20151' AND CapitalInOut = 2 THEN I ELSE 0 END) as I10,
        SUM(CASE WHEN ProgramCateValue = '2' AND ExpenseCateList = '20152' AND CapitalInOut = 1 THEN I ELSE 0 END) as I13,
        SUM(CASE WHEN ProgramCateValue = '2' AND ExpenseCateList = '20152' AND CapitalInOut = 2 THEN I ELSE 0 END) as I14,
        SUM(CASE WHEN ProgramCateValue = '2' AND ExpenseCateList = '20151' AND CapitalInOut = 1 THEN I ELSE 0 END) as I16,
        SUM(CASE WHEN ProgramCateValue = '2' AND ExpenseCateList = '20151' AND CapitalInOut = 2 THEN I ELSE 0 END) as I17 FROM
        (SELECT CONCAT('A#01', IF(C.Path LIKE '%#%', CONCAT('#', " . $this->getParentPath() . "), '')) as ParentID, C.CompanyName as ItemName, SUM(LCDebitAmount) as I, C.Path,
        PC.CateValue as ProgramCateValue, LEFT(EC.CateNo, 5) as ExpenseCateList , CapitalInOut FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN program_cate as PC on A.ProgramID = PC.ProgramID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN capital as CP on A.CapitalID = CP.CapitalID
        WHERE PC.CateNo = '001' AND PC.CateValue IN ('1', '2')
        AND (LEFT(EC.CateNo, 5) = '20152' OR LEFT(EC.CateNo, 5) = '20151')
        AND CapitalInOut IN (1, 2)
        $whereCondition
        GROUP BY C.Path, PC.CateValue, LEFT(EC.CateNo, 5), CapitalInOut) as CH GROUP BY Path
        ";

        DB::statement($sql);
    }

    public function insertDistrict($whereCondition, $provinceNo){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Norder, Detail, I6, I7, I9, I10, I13, I14, I16, I17)
        SELECT CONCAT(ParentID, '#', DistrictNo) as ItemID, ParentID, ItemName, 2 as Norder, 1 as Detail,
        SUM(CASE WHEN ProgramCateValue = '1' AND ExpenseCateList = '20152' AND CapitalInOut = 1 THEN I ELSE 0 END) as I6,
        SUM(CASE WHEN ProgramCateValue = '1' AND ExpenseCateList = '20152' AND CapitalInOut = 2 THEN I ELSE 0 END) as I7,
        SUM(CASE WHEN ProgramCateValue = '1' AND ExpenseCateList = '20151' AND CapitalInOut = 1 THEN I ELSE 0 END) as I9,
        SUM(CASE WHEN ProgramCateValue = '1' AND ExpenseCateList = '20151' AND CapitalInOut = 2 THEN I ELSE 0 END) as I10,
        SUM(CASE WHEN ProgramCateValue = '2' AND ExpenseCateList = '20152' AND CapitalInOut = 1 THEN I ELSE 0 END) as I13,
        SUM(CASE WHEN ProgramCateValue = '2' AND ExpenseCateList = '20152' AND CapitalInOut = 2 THEN I ELSE 0 END) as I14,
        SUM(CASE WHEN ProgramCateValue = '2' AND ExpenseCateList = '20151' AND CapitalInOut = 1 THEN I ELSE 0 END) as I16,
        SUM(CASE WHEN ProgramCateValue = '2' AND ExpenseCateList = '20151' AND CapitalInOut = 2 THEN I ELSE 0 END) as I17 FROM
        (SELECT 'A#02' as ParentID, C.DistrictName as ItemName, SUM(LCDebitAmount) as I, C.DistrictNo,
        PC.CateValue as ProgramCateValue, LEFT(EC.CateNo, 5) as ExpenseCateList , CapitalInOut FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN program_cate as PC on A.ProgramID = PC.ProgramID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN capital as CP on A.CapitalID = CP.CapitalID
        WHERE PC.CateNo = '001' AND PC.CateValue IN ('1', '2')
        AND (LEFT(EC.CateNo, 5) = '20152' OR LEFT(EC.CateNo, 5) = '20151')
        AND CapitalInOut IN (1, 2)
        AND C.ProvinceNo = '$provinceNo'
        AND C.ManagementLevel = 3
        AND A.BudgetLevel = 3
        $whereCondition
        GROUP BY C.DistrictNo, PC.CateValue, LEFT(EC.CateNo, 5), CapitalInOut) as CH GROUP BY DistrictNo
        ";

        DB::statement($sql);
    }

    public function insertCommune($whereCondition, $districtNo){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Norder, Detail, I6, I7, I9, I10, I13, I14, I16, I17)
        SELECT CONCAT(ParentID, '#', CommuneNo) as ItemID, ParentID, ItemName, 2 as Norder, 1 as Detail,
        SUM(CASE WHEN ProgramCateValue = '1' AND ExpenseCateList = '20152' AND CapitalInOut = 1 THEN I ELSE 0 END) as I6,
        SUM(CASE WHEN ProgramCateValue = '1' AND ExpenseCateList = '20152' AND CapitalInOut = 2 THEN I ELSE 0 END) as I7,
        SUM(CASE WHEN ProgramCateValue = '1' AND ExpenseCateList = '20151' AND CapitalInOut = 1 THEN I ELSE 0 END) as I9,
        SUM(CASE WHEN ProgramCateValue = '1' AND ExpenseCateList = '20151' AND CapitalInOut = 2 THEN I ELSE 0 END) as I10,
        SUM(CASE WHEN ProgramCateValue = '2' AND ExpenseCateList = '20152' AND CapitalInOut = 1 THEN I ELSE 0 END) as I13,
        SUM(CASE WHEN ProgramCateValue = '2' AND ExpenseCateList = '20152' AND CapitalInOut = 2 THEN I ELSE 0 END) as I14,
        SUM(CASE WHEN ProgramCateValue = '2' AND ExpenseCateList = '20151' AND CapitalInOut = 1 THEN I ELSE 0 END) as I16,
        SUM(CASE WHEN ProgramCateValue = '2' AND ExpenseCateList = '20151' AND CapitalInOut = 2 THEN I ELSE 0 END) as I17 FROM
        (SELECT 'A#02' as ParentID, C.CommuneName as ItemName, SUM(LCDebitAmount) as I, C.CommuneNo,
        PC.CateValue as ProgramCateValue, LEFT(EC.CateNo, 5) as ExpenseCateList , CapitalInOut FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN program_cate as PC on A.ProgramID = PC.ProgramID
        INNER JOIN expense_cate as EC on A.ExpenseID = EC.ExpenseID
        INNER JOIN capital as CP on A.CapitalID = CP.CapitalID
        WHERE PC.CateNo = '001' AND PC.CateValue IN ('1', '2')
        AND (LEFT(EC.CateNo, 5) = '20152' OR LEFT(EC.CateNo, 5) = '20151')
        AND CapitalInOut IN (1, 2)
        AND C.DistrictNo = '$districtNo'
        AND C.ManagementLevel = 4
        AND A.BudgetLevel = 4
        $whereCondition
        GROUP BY C.CommuneNo, PC.CateValue, LEFT(EC.CateNo, 5), CapitalInOut) as CH GROUP BY CommuneNo
        ";

        DB::statement($sql);
    }

    public function insertDataMB38($userCompany, $typeUserCompany, $whereCondition, $provinceNo, $districtNo){
        if($typeUserCompany){
            if($typeUserCompany->CateValue == '1'){
                $whereCondition .= " AND C.Level >= " . $userCompany->Level;
                // Đơn vị dự toán cấp 1
                if($userCompany->SumCompanyType == '2'){
                    // Tổng hợp dữ liệu theo cấp chính quyền
                    if($userCompany->ManagementLevel == '2'){
                        // Tổng hợp theo cấp tỉnh (Riêng báo cáo này tổng hợp cả đơn vị của Huyện)
                            $insertArr = [
                                ['STT'=> 'I', 'ItemID' => 'A#01', 'ParentID'=> 'A', 'ItemName'=> 'Ngân sách cấp tỉnh', 'Norder'=> 1, 'FontWeight'=> 1],
                                ['STT'=> 'II', 'ItemID' => 'A#02', 'ParentID'=> 'A', 'ItemName'=> 'Ngân sách cấp huyện', 'Norder'=> 1, 'FontWeight'=> 1],
                            ];
                            DB::table('table_tmp')->insert($insertArr);
                            // Lấy ds đơn vị cấp tỉnh
                            $whereCondition1 = $whereCondition . " AND C.ProvinceNo = '$provinceNo' AND C.ManagementLevel = 2 AND A.BudgetLevel = 2 ";
                            $this->insertCompany($whereCondition1);
                            $this->getParentCompanyFromPath();

                            // lấy các huyện
                            $this->insertDistrict($whereCondition, $provinceNo);

                    }
                    else if($userCompany->ManagementLevel == '3'){
                        // Tổng hợp theo cấp huyện
                        $insertArr = [
                            ['STT'=> 'I', 'ItemID' => 'A#01', 'ParentID'=> 'A', 'ItemName'=> 'Ngân sách cấp huyện', 'Norder'=> 1, 'FontWeight'=> 1],
                            ['STT'=> 'II', 'ItemID' => 'A#02', 'ParentID'=> 'A', 'ItemName'=> 'Ngân sách cấp xã', 'Norder'=> 1, 'FontWeight'=> 1],
                        ];
                        DB::table('table_tmp')->insert($insertArr);
                            // Lấy ds đơn vị cấp huyện
                        $whereCondition1 = $whereCondition . " AND C.DistrictNo = '$districtNo' AND C.ManagementLevel = 3 AND A.BudgetLevel = 3 ";
                        $this->insertCompany($whereCondition1);
                        $this->getParentCompanyFromPath();

                            // Lấy các Xã
                        $this->insertCommune($whereCondition, $districtNo);
                    }
                    else{
                        $whereCondition .= " AND C.CommuneNo = " . $userCompany->CommuneNo . " AND C.ManagementLevel = 4 AND A.BudgetLevel = 4 ";
                        $this->insertCompany($whereCondition);
                        $this->getParentCompanyFromPath();
                    }

                }
                else if($userCompany->SumCompanyType == '3'){
                    // Tổng hợp dữ liệu theo ngành, Lấy danh sách đơn vị thuộc ngành đó + cùng cấp chính quyền
                    $whereCondition .= " AND C.SectorID = " . $userCompany->SectorID;
                    if($userCompany->ManagementLevel == '2'){
                        $whereCondition .= " AND C.ProvinceNo = '$provinceNo' AND C.ManagementLevel = 2 AND A.BudgetLevel = 2";
                    }
                    else if($userCompany->ManagementLevel == '3'){
                        $whereCondition .= " AND C.DistrictNo = '$districtNo' AND C.ManagementLevel = 3 AND A.BudgetLevel = 3 ";
                    }
                    $this->insertCompany($whereCondition);
                    $this->getParentCompanyFromPath();

                }
            }
            else{
                if($typeUserCompany->CateValue == '2'){
                    $whereCondition .= " AND C.AutOrgNo = '" . $userCompany->AutOrgNo . "'";
                    // Đơn vị dự toán cấp 2 (Đơn vị trung gian) -> Tổng hợp cùng đơn vị chủ quản
                }
                else{
                    // Lấy các đơn vị con của nó
                    $whereCondition .= "AND LEFT(C.CompanyNo, " . 3* ($userCompany->Level) . ") = LEFT('" .  $userCompany->CompanyNo. "', " . 3* ($userCompany->Level) . ")";
                }
                if($userCompany->ManagementLevel == '2'){
                    $whereCondition .= " AND C.ProvinceNo = '$provinceNo' AND C.ManagementLevel = 2 AND A.BudgetLevel = 2";
                }
                else if($userCompany->ManagementLevel == '3'){
                    $whereCondition .= " AND C.DistrictNo = '$districtNo' AND C.ManagementLevel = 3 AND A.BudgetLevel = 3 ";
                }
                $this->insertCompany($whereCondition);
                $this->getParentCompanyFromPath();
            }

        }
    }

}
