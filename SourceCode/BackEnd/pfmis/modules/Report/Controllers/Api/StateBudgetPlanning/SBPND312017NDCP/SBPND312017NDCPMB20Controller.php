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

class SBPND312017NDCPMB20Controller extends Controller {
    use Common;
    public function SBPND312017NDCP_MB20(Request $request){

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
        $whereCommonCondition = ' AND A.InTransTypeID NOT IN (16, 17 ,18) ';
        if($userCompany->ManagementLevel == 2 || $userCompany->ManagementLevel == 3){
            if($userCompany->SumDataType == 1){
                // Tổng hợp đơn vị con
                $whereCommonCondition = " AND C.CompanyNo = '" . $userCompany->CompanyNo ."'";
            }
            else{
                if($userCompany->SumDataType == 2){
                    $whereCommonCondition .= " AND C.ManagementLevel = " . ($userCompany->ManagementLevel + 1);
                }
                else{
                    // Tổng hợp theo ngành
                    $whereCommonCondition .= " C.SectorNo = '" . $userCompany->SectorNo . "' AND C.ManagementLevel = " . $userCompany->ManagementLevel;
                }
            }
            if($userCompany->ManagementLevel == 2){
                $whereCommonCondition .= " AND C.ProvinceID = $userCompany->ProvinceID";
            }
            else{
                $whereCommonCondition .= " AND C.DistrictID = $userCompany->DistrictID";
            }
        }


        $yearNext = (int)$year + 1;

        $whereCondition = " AND YEAR(A.PeriodFromDate) = $yearNext $whereCommonCondition ";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
             `District` varchar (255) DEFAULT '',
                `Commune` varchar (255) DEFAULT '',
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
          `I12` double(20,4) DEFAULT NULL,
          `Detail` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $countRecords = DB::table('table_tmp')->count();
        if(in_array($userCompany->SumDataType, [2,3])){
            if($userCompany->ManagementLevel == 2){
                $this->insertDistrict($whereCondition);
                $this->insertCommune1($whereCondition);
                if (DB::table('table_tmp')->count() > $countRecords) {
                    $this->getParentDataTreeCommon(['district', 'commune'], 'A');
                }
            }
            else{
                $this->insertCommune2($whereCondition);
            }
            // sum up
            $this->sumUpTableTmp(1, 5);
        }



        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->get();
        if (isset($data[0])) {
            $data[0]->HeaderName1 = 'Ước thực hiện năm ' . $yearNext;
            $data[0]->HeaderName2 = 'Dự toán năm ' . $yearNext;
        }
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
            $data[$key]->I12 = ($data[$key]->I12) ? $data[$key]->I12 : '';
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
                "ReportName" => "DỰ TOÁN THU NGÂN SÁCH NHÀ NƯỚC TRÊN ĐỊA BÀN TỪNG HUYỆN (XÃ) NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function insertDistrict($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, District, I2, I3, I4, I6, I7, I8)
        SELECT ItemID, ParentID, ItemName, DistrictNo,
               SUM(CASE WHEN AccountNo = '0341' AND RevenueCateNo = '20111' THEN I ELSE 0 END) as I2,
               SUM(CASE WHEN AccountNo = '0341' AND RevenueCateNo = '20112' THEN I ELSE 0 END) as I3,
               SUM(CASE WHEN AccountNo = '0341' AND RevenueCateNo = '20113' THEN I ELSE 0 END) as I4,
               SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20111' THEN I ELSE 0 END) as I6,
               SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20112' THEN I ELSE 0 END) as I7,
               SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20113' THEN I ELSE 0 END) as I8
        FROM
            (SELECT CONCAT('A#', C.DistrictNo) as ItemID, 'A#' as ParentID, C.DistrictName as ItemName, C.DistrictNo,
                    A.AccountNo, LEFT(RC.CateNo, 5) as RevenueCateNo, SUM(LCDebitAmount) as I
             FROM act_gl_books as A
                      INNER JOIN company as C on A.InputCompanyID = C.CompanyID
                      INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
                      INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
             WHERE A.AccountNo IN ('0341', '0711')
               AND (LEFT(RC.CateNo, 5) = '20111' OR LEFT(RC.CateNo, 5) = '20112' OR LEFT(RC.CateNo, 5) = '20113')
               AND LEFT(CC.CateNo, 4) = '2001'
               AND C.DistrictID IS NOT NULl AND C.CommuneID IS NULL
                 $whereCondition
             GROUP BY C.DistrictNo, A.AccountNo, LEFT(RC.CateNo, 5)
            ) as CH GROUP BY DistrictNo";

        DB::statement($sql);
    }
    public function insertCommune1($whereCondition)
    {
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, District,Commune, I2, I3, I4, I6, I7, I8)
        SELECT ItemID, ParentID, ItemName, DistrictNo, CommuneNo,
        SUM(CASE WHEN AccountNo = '0341' AND RevenueCateNo = '20111' THEN I ELSE 0 END) as I2,
        SUM(CASE WHEN AccountNo = '0341' AND RevenueCateNo = '20112' THEN I ELSE 0 END) as I3,
        SUM(CASE WHEN AccountNo = '0341' AND RevenueCateNo = '20113' THEN I ELSE 0 END) as I4,
        SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20111' THEN I ELSE 0 END) as I6,
        SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20112' THEN I ELSE 0 END) as I7,
        SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20113' THEN I ELSE 0 END) as I8
FROM	(SELECT CONCAT('A#',C.DistrictNo,'#', C.CommuneNo) as ItemID, 'A#' as ParentID, C.CommuneName as ItemName,C.DistrictNo, C.CommuneNo,
        A.AccountNo,LEFT(RC.CateNo, 5) as RevenueCateNo, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
		WHERE A.AccountNo IN ('0341', '0711')
               AND (LEFT(RC.CateNo, 5) = '20111' OR LEFT(RC.CateNo, 5) = '20112' OR LEFT(RC.CateNo, 5) = '20113')
               AND LEFT(CC.CateNo, 4) = '2001'
		AND C.DistrictID IS NOT NULl AND C.CommuneID IS NOT NULL
        $whereCondition
        GROUP BY C.DistrictNo, C.CommuneNo , A.AccountNo, LEFT(RC.CateNo, 5)
            ) as CH GROUP BY CommuneNo";

        DB::statement($sql);

    }
    public function insertCommune2($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Commune, I2, I3, I4, I6, I7, I8)
        SELECT ItemID, ParentID, ItemName, CommuneNo,
        SUM(CASE WHEN AccountNo = '0341' AND RevenueCateNo = '20111' THEN I ELSE 0 END) as I2,
        SUM(CASE WHEN AccountNo = '0341' AND RevenueCateNo = '20112' THEN I ELSE 0 END) as I3,
        SUM(CASE WHEN AccountNo = '0341' AND RevenueCateNo = '20113' THEN I ELSE 0 END) as I4,
        SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20111' THEN I ELSE 0 END) as I6,
        SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20112' THEN I ELSE 0 END) as I7,
        SUM(CASE WHEN AccountNo = '0711' AND RevenueCateNo = '20113' THEN I ELSE 0 END) as I8
        FROM
        (SELECT CONCAT('A#', C.CommuneNo) as ItemID, 'A#' as ParentID, C.CommuneName as ItemName, C.CommuneNo,
        A.AccountNo, LEFT(RC.CateNo, 5) as RevenueCateNo, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        WHERE A.AccountNo IN ('0341', '0711')
        AND (LEFT(RC.CateNo, 5) = '20111' OR LEFT(RC.CateNo, 5) = '20112' OR LEFT(RC.CateNo, 5) = '20113')
        AND LEFT(CC.CateNo, 4) = '2001'
        $whereCondition
        GROUP BY C.CommuneNo, A.AccountNo, LEFT(RC.CateNo, 5)
        ) as CH GROUP BY CommuneNo";

        DB::statement($sql);
    }




    // sum up
    public function sumUpTableTmp($start, $end = null){
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
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
                        $update = "UPDATE table_tmp
                    SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . "
                    WHERE ItemID = '" . $value['ParentID'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }

}
