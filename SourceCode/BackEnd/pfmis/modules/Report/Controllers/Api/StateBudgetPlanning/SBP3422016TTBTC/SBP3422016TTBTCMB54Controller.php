<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use IjCore\IjNumber;
use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\CoaCon;
use Module\Listing\Models\Company;
use Module\Listing\Models\Item;
use Module\Report\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class SBP3422016TTBTCMB54Controller extends Controller {
    public function SBP3422016TTBTC_MB54(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $filter = $request->post('filter');
        $companyID = (isset($filter['CompanyID'])) ? $filter['CompanyID'] : null;
        $conAccountNo = (isset($filter['ConAccountNo'])) ? $filter['ConAccountNo'] : null;
        $monthFilter = (isset($filter['Month'])) ? $filter['Month'] : null;
        $year = (isset($filter['Year'])) ? $filter['Year'] : null;
        if (!$monthFilter || !$year) {
            $json['status'] = 0;
            $json['msg'] = 'Chưa chọn tháng năm';
            return json_encode($json);
        }
        $companyID = $filter['CompanyID'];
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
        $month = $monthFilter;
        $yearPre = (int)$year - 1;
        $yearNext = (int)$year + 1;
        $quarter = $filter['Quarter'];
        $whereCondition = "AND (
            YEAR(A.PostDate) = $year
        ) $whereCompany";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `SbiCategoryNo` varchar(20) DEFAULT '',
          `ConAccountNo` varchar(20) DEFAULT '',
          `ConCoAccountNo` varchar(20) DEFAULT '',
          `RevenueCateNo` varchar(20) DEFAULT '',
          `ExpenseCateNo` varchar (20) DEFAULT '',
          `ExpenseNo` varchar (20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);


        $this->A_I_II($whereCondition, $year, $quarter);
        $this->insertII_A_a($whereCondition,$quarter, $year);
        $this->insertII_A_b($whereCondition,$quarter, $year);
        $this->insertII_B_a($whereCondition,$quarter, $year);
        $this->insertII_B_b($whereCondition,$quarter, $year);
        $this->C_D($whereCondition,$quarter, $year);
        $this->InsertExpenseI_1($whereCondition, $year);
        $this->InsertExpenseI_2($whereCondition, $year);

        $this->getParentDataTreeCommon([['sector_cate_value','20002'],'project'],'A');


        $this->setLevel();
        // Thiết lập STT
        $this->updateIndexReportB200('A');


        // Thêm dữ liệu mặc định
        $insertArr1 = [
            ['STT'=>'I', 'ItemID'=> 'I', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'Thu phí, lệ phí', 'FontWeight'=> '1'],
            ['STT'=>'II', 'ItemID'=> 'II', 'ParentID'=> '', 'Level'=> 0, 'ItemName'=> 'Dự toán chi ngân sách nhà nước', 'FontWeight'=> '1'],
            ['STT'=>'1', 'ItemID'=> 'I#01', 'ParentID'=> 'I', 'Level'=> 1, 'ItemName'=> 'Tổng Thu', 'FontWeight'=> '1'],
            ['STT'=>'2', 'ItemID'=> 'I#01#01', 'ParentID'=> 'I#01', 'Level'=> 2, 'ItemName'=> 'Thu phí được để lại đơn vị', 'FontWeight'=> '1'],
            ['STT'=>'3', 'ItemID'=> 'I#01#02', 'ParentID'=> 'I#01', 'Level'=> 2, 'ItemName'=> 'Số phí, lệ phí nộp NSNN', 'FontWeight'=> '1'],
            ['STT'=>'A', 'ItemID'=> 'II#A', 'ParentID'=> 'II', 'Level'=> 1, 'ItemName'=> 'Chi thường xuyên', 'FontWeight'=> '1'],
            ['STT'=>'B', 'ItemID'=> 'II#B', 'ParentID'=> 'II', 'Level'=> 1, 'ItemName'=> 'Chi đầu tư phát triển', 'FontWeight'=> '1'],
            ['STT'=>'C', 'ItemID'=> 'II#C', 'ParentID'=> 'II', 'Level'=> 1, 'ItemName'=> 'Chi dự trữ', 'FontWeight'=> '1'],
            ['STT'=>'D', 'ItemID'=> 'II#D', 'ParentID'=> 'II', 'Level'=> 1, 'ItemName'=> 'Chi cải cách lương', 'FontWeight'=> '1'],
            ['STT'=>'E', 'ItemID'=> 'II#E', 'ParentID'=> 'II', 'Level'=> 1, 'ItemName'=> 'Chi CTMTQG, CTMT', 'FontWeight'=> '1'],
            ['STT'=>'1', 'ItemID'=> 'II#A#01', 'ParentID'=> 'II#A', 'Level'=> 2, 'ItemName'=> 'Chi quản lý hành chính:', 'FontWeight'=> '1'],
            ['STT'=>'2', 'ItemID'=> 'II#A#02', 'ParentID'=> 'II#A', 'Level'=> 2, 'ItemName'=> 'Chi quốc phòng', 'FontWeight'=> '1'],
            ['STT'=>'3', 'ItemID'=> 'II#A#03', 'ParentID'=> 'II#A', 'Level'=> 2, 'ItemName'=> 'Chi an ninh và trật tự, an toàn xã hội', 'FontWeight'=> '1'],
            ['STT'=>'4', 'ItemID'=> 'II#A#04', 'ParentID'=> 'II#A', 'Level'=> 2, 'ItemName'=> 'Chi giáo dục - đào tạo và dạy nghề', 'FontWeight'=> '1'],
            ['STT'=>'5', 'ItemID'=> 'II#A#05', 'ParentID'=> 'II#A', 'Level'=> 2, 'ItemName'=> 'Chi khoa học và công nghệ', 'FontWeight'=> '1'],
            ['STT'=>'6', 'ItemID'=> 'II#A#06', 'ParentID'=> 'II#A', 'Level'=> 2, 'ItemName'=> 'Chi y tế, dân số và gia đình', 'FontWeight'=> '1'],
            ['STT'=>'7', 'ItemID'=> 'II#A#07', 'ParentID'=> 'II#A', 'Level'=> 2, 'ItemName'=> 'Chi văn hóa thông tin', 'FontWeight'=> '1'],
            ['STT'=>'8', 'ItemID'=> 'II#A#08', 'ParentID'=> 'II#A', 'Level'=> 2, 'ItemName'=> 'Chi phát thanh, truyền hình, thông tấn', 'FontWeight'=> '1'],
            ['STT'=>'9', 'ItemID'=> 'II#A#09', 'ParentID'=> 'II#A', 'Level'=> 2, 'ItemName'=> 'Chi thể dục thể thao', 'FontWeight'=> '1'],
            ['STT'=>'10', 'ItemID'=> 'II#A#10', 'ParentID'=> 'II#A', 'Level'=> 2, 'ItemName'=> 'Chi bảo vệ môi trường', 'FontWeight'=> '1'],
            ['STT'=>'11', 'ItemID'=> 'II#A#11', 'ParentID'=> 'II#A', 'Level'=> 2, 'ItemName'=> 'Chi bảo đảm xã hội', 'FontWeight'=> '1'],
            ['STT'=>'12', 'ItemID'=> 'II#A#12', 'ParentID'=> 'II#A', 'Level'=> 2, 'ItemName'=> 'Chi TX khác...', 'FontWeight'=> '1'],
            ['STT'=>'1', 'ItemID'=> 'II#B#01', 'ParentID'=> 'II#B', 'Level'=> 2, 'ItemName'=> 'Chi các cơ quan quản lý nhà nước, đảng, đoàn thể', 'FontWeight'=> '1'],
            ['STT'=>'2', 'ItemID'=> 'II#B#02', 'ParentID'=> 'II#B', 'Level'=> 2, 'ItemName'=> 'Chi quốc phòng', 'FontWeight'=> '1'],
            ['STT'=>'3', 'ItemID'=> 'II#B#03', 'ParentID'=> 'II#B', 'Level'=> 2, 'ItemName'=> 'Chi an ninh và trật tự, an toàn xã hội', 'FontWeight'=> '1'],
            ['STT'=>'4', 'ItemID'=> 'II#B#04', 'ParentID'=> 'II#B', 'Level'=> 2, 'ItemName'=> 'Chi giáo dục - đào tạo và dạy nghề', 'FontWeight'=> '1'],
            ['STT'=>'5', 'ItemID'=> 'II#B#05', 'ParentID'=> 'II#B', 'Level'=> 2, 'ItemName'=> 'Chi khoa học và công nghệ', 'FontWeight'=> '1'],
            ['STT'=>'6', 'ItemID'=> 'II#B#06', 'ParentID'=> 'II#B', 'Level'=> 2, 'ItemName'=> 'Chi y tế, dân số và gia đình', 'FontWeight'=> '1'],
            ['STT'=>'7', 'ItemID'=> 'II#B#07', 'ParentID'=> 'II#B', 'Level'=> 2, 'ItemName'=> 'Chi văn hóa thông tin', 'FontWeight'=> '1'],
            ['STT'=>'8', 'ItemID'=> 'II#B#08', 'ParentID'=> 'II#B', 'Level'=> 2, 'ItemName'=> 'Chi phát thanh, truyền hình, thông tấn', 'FontWeight'=> '1'],
            ['STT'=>'9', 'ItemID'=> 'II#B#09', 'ParentID'=> 'II#B', 'Level'=> 2, 'ItemName'=> 'Chi thể dục thể thao', 'FontWeight'=> '1'],
            ['STT'=>'10', 'ItemID'=> 'II#B#10', 'ParentID'=> 'II#B', 'Level'=> 2, 'ItemName'=> 'Chi bảo vệ môi trường', 'FontWeight'=> '1'],
            ['STT'=>'11', 'ItemID'=> 'II#B#11', 'ParentID'=> 'II#B', 'Level'=> 2, 'ItemName'=> 'Chi bảo đảm xã hội', 'FontWeight'=> '1'],
            ['STT'=>'12', 'ItemID'=> 'II#B#12', 'ParentID'=> 'IIB', 'Level'=> 2, 'ItemName'=> 'Chi ĐT khác...', 'FontWeight'=> '1'],
            ['STT'=>'1', 'ItemID'=> 'II#E#01', 'ParentID'=> 'II#E', 'Level'=> 2, 'ItemName'=> 'Chương trình MTQG', 'FontWeight'=> '1'],
            ['STT'=>'2', 'ItemID'=> 'II#E#02', 'ParentID'=> 'II#E', 'Level'=> 2, 'ItemName'=> 'Chương trình mục tiêu', 'FontWeight'=> '1'],
        ];
        DB::table('table_tmp')->insert($insertArr1);

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $this->sumUpSBPMB54(0,6, $maxLevel);

        $data = DB::table("table_tmp")->orderBy('ItemID', 'asc')->get();
        $year2 = $year -2;
        $year1 = $year -1;
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Quý ' . $year2;
            $data[0]->HeaderName2 = 'Lũy kế ' . $year1 . ' Quý';
            $data[0]->HeaderName3 = 'Ước ' .$year.' Quý so với dự toán';
        }
        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            if($data[$key]->I1 !=0){
                $data[$key]->I4 = round(((int)($data[$key]->I3) *100)/(int)($data[$key]->I1), 2);
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
                "ReportName" => "DỰ TOÁN CHI BẰNG NGOẠI TỆ NĂM " . $year . " GIAI ĐOẠN " . $year1."-".$year,
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
    public function createTemporaryBudget(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `BudgetTemporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $insertA = [
            ['ItemID'=> '1','ItemName'=> 'Kinh phí giao tự chủ'],
            ['ItemID'=> '2','ItemName'=> 'Kinh phí không giao tự chủ'],
        ];

        DB::table('BudgetTemporary')->insert($insertA);

    }
    public function A_I_II($whereCondition, $year, $quarter)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ParentID,
                               SUM(CASE WHEN (B.AccountNo IN ('0343','0341') AND YEAR(A.PostDate = $year) ) THEN I END) AS I1,
                               SUM(CASE WHEN (B.AccountNo IN ('0713','0711') AND  MONTH(A.PostDate) >= (($quarter-1)*3+1) AND MONTH(A.PostDate) <= $quarter*3) THEN I END) AS I2,
                               SUM(CASE WHEN (B.AccountNo IN ('0713','0711') AND MONTH(A.PostDate) >= 1 AND  MONTH(A.PostDate) <= $quarter*3) THEN I END) AS I3
                        FROM (
                                 SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo,
                                        (Case when NC.CateValue = '1' THEN 'I#01#01'
                                              when NC.CateValue = '1' THEN 'A#01#02'
                                            END) as ParentID
                                 FROM act_gl_books as A
                                          INNER JOIN revenue_cate AS RC ON RC.RevenueID = A.RevenueID
                                          INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                            INNER JOIN company_cate AS CPC ON CPC.CompanyID = A.CompanyID
                                 WHERE A.AccountNo IN ('0343','0341','0713','0711')
                                   AND RC.CateNo = '2011104' AND NC.CateNo = '2011112'
                                   AND CPC.CateNo = '1000' AND CPC.CateValue = '1'
                                     $whereCondition
                                 GROUP BY  ParentID,  A.AccountNo, NC.CateValue
                             ) AS B Group BY ParentID
                            ) AS HH
                            SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3
                            WHERE  TT.ParentID = HH.ParentID";
        DB::statement($sqlUpdate);
    }
    public function insertII_A_a($whereCondition,$quarter, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,SbiCategory ,Norm, BudgetTemporary,Detail, I1, I2, I3)
SELECT CONCAT(ParentID,'#',Norm), ParentID,SbiCategory, ItemName,Norm, '1', 1,
       SUM(CASE WHEN AccountNo = 0441 AND YEAR(A.PostDate = $year)  THEN I END) AS I1,
       SUM(CASE WHEN AccountNo = 0721 AND MONTH(A.PostDate) >= (($quarter-1)*3+1) AND MONTH(A.PostDate) <= $quarter*3 THEN I END) AS I2,
       SUM(CASE WHEN AccountNo = 0721 AND MONTH(A.PostDate) >= 1 AND  MONTH(A.PostDate) <= $quarter*3 THEN I END) AS I3
FROM
    (SELECT CONCAT('II#A#','SCC.CateValue','#01') AS ParentID, SCC.CateValue as SbiCategory,NC.CateValue as Norm, A.NormName AS ItemName, A.AccountNo,SUM(A.FCDebitAmount) AS I
     FROM act_gl_books as A
              INNER JOIN norm_cate as NC ON A.NormID = NC.NormID
              INNER JOIN sbi_category_cate as SCC ON A.SbiCategoryID = SCC.SbiCategoryID
              INNER JOIN expense_cate as EC ON A.ExpenseID = EC.ExpenseID
              INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
              INNER JOIN company_cate as CPC ON A.CompanyID = CPC.CompanyID
     WHERE  A.AccountNo in('0441','0721')
       AND SCC.CateNo = 001
       AND SCC.CateValue IN (1,2,3,4,5,6,7,8,9,12,13,14)
       AND LEFT(EC.CateNo,5)='20151'
       AND LEFT(CC.CateNo,5) ='20011'
       AND NC.CateNo='2015119'
       AND CPC.CateNo = '1000' AND CPC.CateValue = '1'
         $whereCondition
     GROUP BY A.AccountNo,P.ProjectNo,A.ProgramNo
    ) as CH GROUP BY Norm,SbiCategory";

        DB::statement($sql);
    }
    public function insertII_A_b($whereCondition,$quarter, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,SbiCategory,Norm, BudgetTemporary,Detail, I1, I2, I3)
            SELECT CONCAT(ParentID,'#',Norm), ParentID, ItemName,SbiCategory,Norm, '2', 1,
                   SUM(CASE WHEN AccountNo = 0441 AND YEAR(A.PostDate = $year)  THEN I END) AS I1,
                   SUM(CASE WHEN AccountNo = 0721 AND MONTH(A.PostDate) >= (($quarter-1)*3+1) AND MONTH(A.PostDate) <= $quarter*3 THEN I END) AS I2,
                   SUM(CASE WHEN AccountNo = 0721 AND MONTH(A.PostDate) >= 1 AND  MONTH(A.PostDate) <= $quarter*3 THEN I END) AS I3
            FROM
                (SELECT CONCAT('II#A#','SCC.CateValue','#02') AS ParentID,SCC.CateValue as SbiCategory, NC.CateValue as Norm, A.NormName AS ItemName, A.AccountNo,SUM(A.FCDebitAmount) AS I
                 FROM act_gl_books as A
                          INNER JOIN norm_cate as NC ON A.NormID = NC.NormID
                          INNER JOIN sbi_category_cate as SCC ON A.SbiCategoryID = SCC.SbiCategoryID
                          INNER JOIN expense_cate as EC ON A.ExpenseID = EC.ExpenseID
                          INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
                        INNER JOIN company_cate as CPC ON A.CompanyID = CPC.CompanyID
                 WHERE  A.AccountNo in('0441','0721')
                 AND SCC.CateNo = 001
                 AND SCC.CateValue IN (1,2,3,4,5,6,7,8,9,12,13,14)
                 AND LEFT(EC.CateNo,5)='20151'
                 AND LEFT(CC.CateNo,5) ='20012'
                 AND NC.CateNo='2015119'
                 AND CPC.CateNo = '1000' AND CPC.CateValue = '1'
                     $whereCondition
                 GROUP BY A.AccountNo,P.ProjectNo,A.ProgramNo
                ) as CH GROUP BY Norm,SbiCategory";

        DB::statement($sql);
    }
    public function insertII_B_a($whereCondition,$quarter, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,SbiCategory,Norm, BudgetTemporary,Detail, I1, I2, I3)
            SELECT CONCAT(ParentID,'#',Norm), ParentID, ItemName,SbiCategory,Norm, '1', 1,
                   SUM(CASE WHEN AccountNo = 0441 AND YEAR(A.PostDate = $year)  THEN I END) AS I1,
                   SUM(CASE WHEN AccountNo = 0721 AND MONTH(A.PostDate) >= (($quarter-1)*3+1) AND MONTH(A.PostDate) <= $quarter*3 THEN I END) AS I2,
                   SUM(CASE WHEN AccountNo = 0721 AND MONTH(A.PostDate) >= 1 AND  MONTH(A.PostDate) <= $quarter*3 THEN I END) AS I3
            FROM
                (SELECT CONCAT('II#B#','SCC.CateValue','#01') AS ParentID,SCC.CateValue as SbiCategory, NC.CateValue as Norm, A.NormName AS ItemName, A.AccountNo,SUM(A.FCDebitAmount) AS I
                 FROM act_gl_books as A
                          INNER JOIN norm_cate as NC ON A.NormID = NC.NormID
                          INNER JOIN sbi_category_cate as SCC ON A.SbiCategoryID = SCC.SbiCategoryID
                          INNER JOIN expense_cate as EC ON A.ExpenseID = EC.ExpenseID
                          INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
                          INNER JOIN company_cate as CPC ON A.CompanyID = CPC.CompanyID
                 WHERE  A.AccountNo in('0441','0721')
                 AND SCC.CateNo = 001
                 AND SCC.CateValue IN (1,2,3,4,5,6,7,8,9,12,13,14)
                 AND LEFT(EC.CateNo,8)='2015201'
                 AND LEFT(CC.CateNo,5) ='20011'
                 AND NC.CateNo='201511112'
                 AND CPC.CateNo = '1000' AND CPC.CateValue = '1'
                     $whereCondition
                 GROUP BY A.AccountNo,P.ProjectNo,A.ProgramNo
                ) as CH GROUP BY Norm,SbiCategory";

        DB::statement($sql);
    }
    public function insertII_B_b($whereCondition,$quarter, $year){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName,SbiCategory,Norm, BudgetTemporary,Detail, I1, I2, I3)
            SELECT CONCAT(ParentID,'#',Norm), ParentID, ItemName,SbiCategory,Norm, '2', 1,
                   SUM(CASE WHEN AccountNo = 0441 AND YEAR(A.PostDate = $year)  THEN I END) AS I1,
                   SUM(CASE WHEN AccountNo = 0721 AND MONTH(A.PostDate) >= (($quarter-1)*3+1) AND MONTH(A.PostDate) <= $quarter*3 THEN I END) AS I2,
                   SUM(CASE WHEN AccountNo = 0721 AND MONTH(A.PostDate) >= 1 AND  MONTH(A.PostDate) <= $quarter*3 THEN I END) AS I3
            FROM
                (SELECT CONCAT('II#B#','SCC.CateValue','#02') AS ParentID,SCC.CateValue as SbiCategory, NC.CateValue as Norm, A.NormName AS ItemName, A.AccountNo,SUM(A.FCDebitAmount) AS I
                 FROM act_gl_books as A
                          INNER JOIN norm_cate as NC ON A.NormID = NC.NormID
                          INNER JOIN sbi_category_cate as SCC ON A.SbiCategoryID = SCC.SbiCategoryID
                          INNER JOIN expense_cate as EC ON A.ExpenseID = EC.ExpenseID
                          INNER JOIN capital_cate as CC ON A.CapitalID = CC.CapitalID
                        INNER JOIN company_cate as CPC ON A.CompanyID = CPC.CompanyID
                 WHERE  A.AccountNo in('0441','0721')
                 AND SCC.CateNo = 001
                 AND SCC.CateValue IN (1,2,3,4,5,6,7,8,9,12,13,14)
                 AND LEFT(EC.CateNo,8)='2015201'
                 AND LEFT(CC.CateNo,5) ='20012'
                 AND NC.CateNo='201511112'
                 AND CPC.CateNo = '1000' AND CPC.CateValue = '1'
                     $whereCondition
                 GROUP BY A.AccountNo,P.ProjectNo,A.ProgramNo
                ) as CH GROUP BY Norm,SbiCategory";

        DB::statement($sql);
    }
    public function C_D($whereCondition, $year, $quarter)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT,(SELECT ItemID,
                               SUM(CASE WHEN (B.AccountNo = 0441 AND YEAR(A.PostDate = $year) ) THEN I END) AS I1,
                               SUM(CASE WHEN (B.AccountNo = 0721 AND  MONTH(A.PostDate) >= (($quarter-1)*3+1) AND MONTH(A.PostDate) <= $quarter*3) THEN I END) AS I2,
                               SUM(CASE WHEN (B.AccountNo = 0721 AND MONTH(A.PostDate) >= 1 AND  MONTH(A.PostDate) <= $quarter*3) THEN I END) AS I3
                        FROM (
                                 SELECT SUM(A.FCDebitAmount) AS I,A.AccountNo,
                                        (Case when (EC.CateNo = 20155 AND EC.CateValue = 1 AND NC.CateNo = 201511113 AND NC.CateValue = 1)THEN 'II#C'
                                              when (EC.CateNo = 20355 AND EC.CateValue = 1 AND NC.CateNo = 200194 )THEN 'II#D'
                                            END) as ItemID
                                 FROM act_gl_books as A
                                          INNER JOIN expense_cate AS EC ON EC.ExpenseID = A.ExpenseID
                                          INNER JOIN capital_cate AS CC ON CC.CapitalID = A.CapitalID
                                          INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                 WHERE A.AccountNo IN ('0441','0721')
                                   AND (EC.CateNo = '20155' OR EC.CateNo= '20355') AND EC.CateValue = 1
                                   AND ((NC.CateNo = '201511113'AND NC.CateValue = 1 ) OR NC.CateNo = '200194')
                                     $whereCondition
                                 GROUP BY  ItemID,  A.AccountNo, NC.CateValue
                             ) AS B Group BY ItemID
                    ) AS HH
                    SET  TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3
                    WHERE  TT.ItemID = HH.ItemID";
        DB::statement($sqlUpdate);
    }

    // SUM
    public function sumUpSBPMB54($start, $end, $maxLevel){
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4, SUM(M.I1) AS I5, SUM(M.I2) AS I6, SUM(M.I3) AS I7, SUM(M.I4) AS I8 FROM table_tmp M
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
                        $update = "UPDATE table_tmp
                    SET I1 = I1 + ".$I1.", I2 = I2 + ".$I2.", I3 = I3 + ".$I3.", I4 = I4 + ".$I4.", I5 = I5 + ".$I5.", I6 = I6 + ".$I6.", I7 = I7 + ".$I7.", I8= I8 + ".$I8."
                    WHERE ItemID = '".$value['ParentID']."'
                        AND table_tmp.Level = $j"; //Log::debug($update);
                        DB::statement($update);
                    }
                }
            }
        }
    }



public function setDate($month){
        switch ($month) {
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
                return 31;
            case 4:
            case 6:
            case 9:
            case 11:
                return 30;
            case 2:
                return 28;

        }
    }



}
