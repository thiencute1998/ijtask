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

class SBPND312017NDCPMB04Controller extends Controller {
    use Common;
    public function SBPND312017NDCP_MB04(Request $request){

        $userCompany = DB::table('company')->where('CompanyID','=', Auth::user()->CompanyID)->first();
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
        if(isset($filter['PeriodYear'])){
            $periodYear = $filter['PeriodYear'];
            $periodDate = explode('-', $filter['PeriodYear']);
            $fromDate = $periodDate[0];
            $toDate = $periodDate[1];
        }
        else{
            $periodYear = '2021-2025';
            $fromDate = 2021;
            $toDate = 2025;
        }
        $whereCompany = '';
        if ($companyID) {
            $whereCompany .= " AND A.CompanyID = " . $companyID . ' ';
        }
        $whereDirection = '';
        if ($directionID) {
            $whereDirection = 'AND A.DirectionID = ' . $directionID . ' ';
        }

        $typeUserCompany = DB::table('company_cate')->where('CompanyID', Auth::user()->CompanyID)
            ->where('CateNo', '1000')->whereNotNull('CateValue')
            ->orderBy('CateValue','ASC')->first();
        $whereCommonCondition = ' AND A.InTransTypeID NOT IN (16, 17 ,18) ';
        $whereCondition = " AND A.PeriodType = 9 AND A.PeriodName = '" . $periodYear . "' $whereCommonCondition";

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `STT` varchar(20) DEFAULT '',
          `ItemID` varchar(100) NOT NULL PRIMARY KEY,
          `ParentID` varchar(100) DEFAULT '',
          `ParentID1` varchar(100) DEFAULT '',
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
            `SectorCateValue` varchar(20) DEFAULT '',
            `T1Temporary` varchar(20) DEFAULT '',
            `T2Temporary` varchar(20) DEFAULT '',
            `District` varchar(30) DEFAULT '',
            `Commune` varchar(30) DEFAULT '',
            `Company` varchar(30) DEFAULT '',
            `Project` varchar(30) DEFAULT '',
            `BuildAddress` varchar(255) DEFAULT '',
            `CapableDesign` varchar(255) DEFAULT '',
            `PerformDate` varchar(255) DEFAULT '',
            `Investdoc` varchar(255) DEFAULT '',
            `Path` varchar(255) DEFAULT '',
            `ParentCompanyNo` varchar(20) DEFAULT '',
            `Norder` int(10) DEFAULT 0,
            `Detail` tinyint(1) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);

        $this->createTemporaryT1();
        $this->createTemporaryT2();

        // caculator here
        $insertArr = [
            'ItemID'=> "A", 'ItemName'=> 'Tổng số', 'Level'=> 0, 'FontWeight'=> 1
        ];
        DB::table('table_tmp')->insert($insertArr);
        $this->insertDataMB04($userCompany, $typeUserCompany, $whereCondition, $provinceNo, $districtNo, $fromDate, $toDate);
        $this->setLevel();
        $this->sortSector();


        // sum up
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first()->maxLevel;
        $this->sumRow1($maxLevel);
        $this->sumRow($maxLevel);

        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        $data = DB::table("table_tmp")->orderBy('Norder', 'ASC')->orderBy('ItemID', 'ASC')->get();

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';
            $data[$key]->I5 = ($data[$key]->I5) ? $data[$key]->I5 : '';
            $data[$key]->I6 = ($data[$key]->I6) ? $data[$key]->I6 : '';
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
                "ReportName" => "TỔNG HỢP DỰ KIẾN KẾ HOẠCH ĐẦU TƯ CÔNG TRUNG HẠN VỐN NSNN CỦA CÁC CƠ QUAN, ĐƠN VỊ VÀ ĐỊA PHƯƠNG GIAI ĐOẠN 05 NĂM " . $periodYear,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function createTemporaryT1(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `T1Temporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $insertA = [
            ['ItemID'=> '1','ItemName'=> 'Chuẩn bị đầu tư'],
            ['ItemID'=> '2','ItemName'=> 'Thực hiện dự án'],
        ];
        DB::table('T1Temporary')->insert($insertA);
    }

    public function createTemporaryT2(){
        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `T2Temporary` (
          `ItemID` varchar(100) DEFAULT '',
          `ItemName` varchar(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $insertA = [
            ['ItemID'=> '01','ItemName'=> 'Dự án chuyển tiếp từ giai đoạn 5 năm … sang giai đoạn 5 năm …'],
            ['ItemID'=> '02','ItemName'=> 'Dự án khởi công mới trong giai đoạn 5 năm …'],
        ];
        DB::table('T2Temporary')->insert($insertA);
    }

    public function getDetailParentCompany($levelCompany){
        $insertArr = [];
        $listItemDetail = DB::table('table_tmp')->where('Detail', 1)->where('Company','!=', '')->get();
        if($listItemDetail){
            foreach($listItemDetail as $item){
                $listParent = explode('#', $item->Path);
                array_pop($listParent);
                if(!empty($listParent)){
                    for($i = count($listParent) - 1; $i >= ($levelCompany - 1) ; $i--){
                        $parentID1 = '';
                        $itemID = str_replace($item->Company, $listParent[$i], $item->ItemID);
                        $parentID = str_replace($item->Company, $listParent[$i], $item->ParentID);
                        if($i > ($levelCompany - 1) && $item->ParentID1 != ''){
                            // Có ParentID1
                            $parentID1 = str_replace($item->Company, $listParent[$i - 1], $item->ItemID);
                            $parentCompanyNo = $listParent[$i-1];
                        }
                        else{
                            $parentCompanyNo = null;
                        }
                        $insertArr [] = [
                            'ItemID'=>  $itemID,
                            'ParentID'=> $parentID,
                            'ParentID1'=> $parentID1,
                            'ItemName'=> $item->ItemName,
                            'SectorCateValue'=> $item->SectorCateValue,
                            'District'=> $item->District,
                            'Company'=> $listParent[$i],
                            'T1Temporary'=> $item->T1Temporary,
                            'T2Temporary'=> $item->T2Temporary,
                            'Project'=> $item->Project,
                            'BuildAddress'=> $item->BuildAddress,
                            'CapableDesign'=> $item->CapableDesign,
                            'PerformDate'=> $item->PerformDate,
                            'Investdoc'=> $item->Investdoc,
                            'ParentCompanyNo'=> $parentCompanyNo,
                            'Norder'=> $item->Norder,
                            'Detail'=> 1,
                        ];
                    }
                }
            }
            DB::table('table_tmp')->insertOrIgnore($insertArr);
        }
    }

    public function insertDataMB04($userCompany, $typeUserCompany, $whereCondition, $provinceNo, $districtNo, $fromDate, $toDate){
        if($typeUserCompany){
            if($typeUserCompany->CateValue == '1'){
                $whereCondition .= " AND C.Level >= " . $userCompany->Level;
                // Đơn vị dự toán cấp 1
                if($userCompany->SumCompanyType == '2'){
                    // Tổng hợp dữ liệu theo cấp chính quyền
                    if($userCompany->ManagementLevel == '2'){
                        // Tổng hợp theo cấp tỉnh (Riêng báo cáo này tổng hợp cả đơn vị của Huyện)
                        // Lấy ds đơn vị cấp tỉnh
                        $whereConditionArea = $whereCondition . " AND C.ProvinceNo = '$provinceNo' AND C.ManagementLevel >= 3";
                        $this->insertAreaR1C4_5_8_9($whereConditionArea, 'District');
                        $whereCondition .= " AND C.ManagementLevel = 2 AND C.ProvinceNo = '$provinceNo'";
                    }
                    else if($userCompany->ManagementLevel == '3'){
                        $whereConditionArea = $whereCondition . " AND C.DistrictNo = '$districtNo' AND C.ManagementLevel = 4";
                        $this->insertAreaR1C4_5_8_9($whereConditionArea, 'Commune');
                        $whereCondition .= " AND C.ManagementLevel = 3 AND C.DistrictNo = '$districtNo'";
                    }
                    else{
                        // Tổng hợp xã
                        $whereCondition .= " AND C.ManagementLevel = 4";
                    }
                }
                else if($userCompany->SumCompanyType == '3'){
                    // Tổng hợp dữ liệu theo ngành, Lấy danh sách đơn vị thuộc ngành đó + cùng cấp chính quyền
                    $whereCondition .= " AND C.ManagementLevel = " . $userCompany->ManagementLevel . " AND C.SectorID = " . $userCompany->SectorID;
                    if($userCompany->ManagementLevel == '2'){
                        $whereCondition .= " AND C.ProvinceNo = '$provinceNo'";
                    }
                    else if($userCompany->ManagementLevel == '3'){
                        $whereCondition .= " AND C.DistrictNo = '$districtNo'";
                    }
                }
            }
            else{
                if($typeUserCompany->CateValue == '2'){
                    $whereCondition .= " AND C.ManagementLevel = " . $userCompany->ManagementLevel . " AND C.AutOrgNo = '" . $userCompany->AutOrgNo . "'";
                    // Đơn vị dự toán cấp 2 (Đơn vị trung gian) -> Tổng hợp cùng đơn vị chủ quản
                }
                else{
                    // Lấy các đơn vị con của nó
                    $whereCondition .= " AND LEFT(C.CompanyNo, " . 3* ($userCompany->Level) . ") = LEFT('" .  $userCompany->CompanyNo. "', " . 3* ($userCompany->Level) . ")";
                }
                if($userCompany->ManagementLevel == '2'){
                    $whereCondition .= " AND C.ProvinceNo = '$provinceNo'";
                }
                else if($userCompany->ManagementLevel == '3'){
                    $whereCondition .= " AND C.DistrictNo = '$districtNo'";
                }

            }
            $this->insertR2C4_5_8_9($whereCondition);
//            $this->getDetailParentCompany($userCompany->Level);
//            $this->getParentDataTreeCommonMB04([['sector_cate_value', '200030301'], 'company', 'T1Temporary', 'project']);
//            $this->getParentDataTreeCommonMB04([['sector_cate_value', '200030301'], 'company', 'T1Temporary', 'T2Temporary', 'project']);
        }
    }

    public function getParentPath(){
        return "REPLACE(C.Path, CONCAT('#', SUBSTRING_INDEX(C.Path, '#', -1)), '')";
    }

    public function insertR2C4_5_8_9($whereCondition){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, Company, Path, Norder, Detail, I4, I5, I8, I9)
        SELECT CONCAT(ParentID, '#', Path) as ItemID, ParentID, ItemName, CompanyNo,
        Path, 1 as Norder, 1 as Detail,
        SUM(CASE WHEN CapitalInOut = 1 AND ProgramCateValue = '1' THEN I ELSE 0 END) as I4,
        SUM(CASE WHEN CapitalInOut = 1 AND ProgramCateValue = '2' THEN I ELSE 0 END) as I5,
        SUM(CASE WHEN CapitalInOut = 2 AND ProgramCateValue = '1' THEN I ELSE 0 END) as I8,
        SUM(CASE WHEN CapitalInOut = 2 AND ProgramCateValue = '2' THEN I ELSE 0 END) as I9
        FROM(
        SELECT CONCAT('A', IF(C.Path LIKE '%#%', CONCAT('#', " . $this->getParentPath() . "), '')) as ParentID, A.CompanyName as ItemName,
        CA.CapitalInOut, PC.CateValue as ProgramCateValue, C.CompanyNo, SUM(LCDebitAmount) as I, C.Path
        FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        INNER JOIN program_cate as PC on A.ProgramID = PC.ProgramID
        WHERE CA.CapitalInOut IN (1, 2) AND CA.BudgetStateType = 1
        AND PC.CateNo = '001' AND PC.CateValue IN ('1', '2')
        $whereCondition
        GROUP BY CA.CapitalInOut, PC.CateValue, C.CompanyNo
        ) as CH GROUP BY CompanyNo
        ";

        DB::statement($sql);
    }

    public function insertAreaR1C4_5_8_9($whereCondition, $field){
        $fieldNo = $field . "No";
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ItemName, $field, Norder, Detail, I4, I5, I8, I9)
        SELECT CONCAT(ParentID, '#', $fieldNo) as ItemID, ParentID, ItemName, $fieldNo, 2 as Norder, 1 as Detail,
        SUM(CASE WHEN CapitalInOut = 1 AND ProgramCateValue = '1' THEN I ELSE 0 END) as I4,
        SUM(CASE WHEN CapitalInOut = 1 AND ProgramCateValue = '2' THEN I ELSE 0 END) as I5,
        SUM(CASE WHEN CapitalInOut = 2 AND ProgramCateValue = '1' THEN I ELSE 0 END) as I8,
        SUM(CASE WHEN CapitalInOut = 2 AND ProgramCateValue = '2' THEN I ELSE 0 END) as I9 FROM(
        SELECT 'A' as ParentID, A.CompanyName as ItemName, $fieldNo, C.CompanyNo,
        CA.CapitalInOut, PC.CateValue as ProgramCateValue, SUM(LCDebitAmount) as I
        FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN capital as CA on A.CapitalID = CA.CapitalID
        INNER JOIN program_cate as PC on A.ProgramID = PC.ProgramID
        WHERE CA.CapitalInOut IN (1, 2) AND CA.BudgetStateType = 1
        AND PC.CateNo = '001' AND PC.CateValue IN ('1', '2')
        $whereCondition
        GROUP BY CA.CapitalInOut, PC.CateValue, C.CompanyNo
        ) as CH GROUP BY CompanyNo
        ";

        DB::statement($sql);
    }

    // sum Row ParentID

    public function sumRow1($maxLevel, $start = 0){
        for ($i = $maxLevel; $i > $start; $i--) {
            $sql = "SELECT ItemID, ParentID1, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4,
            SUM(M.I5) AS I5, SUM(M.I6) AS I6
            FROM table_tmp M
            WHERE ParentID1 != '' AND M.Level = " . $i . " GROUP BY ParentID1";
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
                SET I1 = I1 + " . $I1 . ", I2 = I2 + " . $I2 . ", I3 = I3 + " . $I3 . ", I4 = I4 + " . $I4 . ",
                I5 = I5 + " . $I5 . ", I6 = I6 + " . $I6 . "
                WHERE ItemID = '" . $value['ParentID1'] . "'
                AND table_tmp.Level = " . $i;
                    DB::statement($update);
                }
            }
        }

    }

    // sum Row ParentID
    public function sumRow($maxLevel, $start = 0){
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
                SET I1 = I1 + " . $I1 . ", I2 = I2 + " . $I2 . ", I3 = I3 + " . $I3 . ", I4 = I4 + " . $I4 . ",
                I5 = I5 + " . $I5 . ", I6 = I6 + " . $I6 . "
                WHERE ItemID = '" . $value['ParentID'] . "'
                AND table_tmp.Level = " . $j;
                    DB::statement($update);
                }
            }
        }

    }

    public function sortSector(){
        DB::table('table_tmp')->where('Level', 1)->update(['Norder'=> 1]);
    }

    public function getParentDataTreeCommonMB04($table, $id = null){
        $uniqueItems = [];
        $arrID = [];
        for($i = count($table) - 2; $i >=0 ; $i--){
            $insertArr = [];
            $insertNoParent = [];
            if($i == count($table) - 2){
                $insert = [];
                $groupBy = '';
                $where = ' 1 = 1';
                for($j = 0; $j <= $i; $j++) {
                    if(is_array($table[$j])){
                        $tmp = $table[$j][0];
                        $argValue = $table[$j][1];
                    }
                    else{
                        $tmp = $table[$j];
                    }
                    $join = ucfirst($tmp);
                    if(strpos($tmp,'_') !== false){
                        $join = join(array_map('ucfirst',explode('_',$tmp)));
                    }
                    if(is_array($table[$j])){
                        if(strpos($tmp,'_cate_value') === false){
                            $where .= " AND $join = '" . $argValue . "'";
                        }
                    }
                    $where .= " AND $join != '' ";
                    if($j < $i){
                        $join .= ',';
                    }
                    $groupBy .= $join;
                }
                $like = '';
                if($id !== null){
                    $like = "ItemID LIKE '" . $id . "%' AND";
                }
                if($groupBy != ''){
                    $sql = "SELECT * FROM table_tmp where $like $where GROUP BY $groupBy";
                }
                else{
                    $sql = "SELECT * FROM table_tmp where $like $where";
                }
                $listDetail = DB::select($sql);
//                $listParent = explode(',',$groupBy);
                if(is_array($table[$i])){
                    $tmp = $table[$i][0];
                    $argCateNo = $table[$i][1];
                }
                else{
                    $tmp = $table[$i];
                    $argCateNo = '';
                }
                $argValue = join(array_map('ucfirst',explode('_',$tmp)));
                if($listDetail){
                    foreach ($listDetail as $item){
                        $item = (array)$item;
//                        if($listParent != ''){
//                            foreach($listParent as $value){
//                                $insert[] = [
//                                    $value=> $item[$value],
//                                ];
//                            }
//                        }
                        $itemID = $item['ParentID'];
                        $lastIndex = strrpos($itemID, '#');
                        $parentID = substr($itemID,0,$lastIndex);
                        if (strpos($tmp, 'Temporary') !== false) {
                            $listTemporary = DB::table($tmp)->get();
                            foreach($listTemporary as $data){
                                $insertArr[] = [
                                    'ItemID'=> $parentID . '#' . $data->ItemID,
                                    'ParentID'=> $parentID,
                                    'ItemName'=> $data->ItemName,
                                    'Detail'=> 0,
                                    'Norder'=> $item['Norder'],
                                    'Company'=> $item['Company'],
                                    'ParentCompanyNo'=> $item['ParentCompanyNo'],

                                ];
//                                $insertArr[] = call_user_func_array('array_merge', $insert);
                            }
                        }
                        else{
                            $itemName = $this->getItemNameReport($tmp, $argCateNo, $item[$argValue]);
                            $insertArr[] = [
                                'ItemID'=> $itemID,
                                'ParentID'=> $parentID,
                                'ItemName'=> $itemName,
                                'Detail'=> 0,
                                'Norder'=> $item['Norder'],
                                'Company'=> $item['Company'],
                                'ParentCompanyNo'=> $item['ParentCompanyNo'],
                            ];
//                            $insertArr[] = call_user_func_array('array_merge', $insert);
                        }
                    }
                    foreach($insertArr as $insert){
                        if(!in_array($insert['ItemID'], $arrID)){
                            $arrID[] = $insert['ItemID'];
                            $uniqueItems[] = $insert;
                        }
                    }
                    DB::table('table_tmp')->insertOrIgnore($uniqueItems);
                }
            }
            else{
                if($uniqueItems){
                    $arrItemID = [];
                    foreach($uniqueItems as $item){
                        if(!in_array($item['ParentID'], $arrItemID)){
                            $arrItemID[] = $item['ParentID'];
                            $lastIndex = strrpos($item['ParentID'], '#');
                            if($table[$i] == 'company' && $item['ParentCompanyNo'] != null){
                                $parentID = str_replace($item['Company'], $item['ParentCompanyNo'], $item['ParentID']);
                            }
                            else{
                                $parentID = substr($item['ParentID'],0,$lastIndex);
                            }
                            $argValue = substr($item['ParentID'], $lastIndex + 1, strlen($item['ParentID']));
                            if(is_array($table[$i])){
                                $tmp = $table[$i][0];
                                $argCateNo = $table[$i][1];
                            }
                            else{
                                $tmp = $table[$i];
                                $argCateNo = '';
                            }
                            if (strpos($tmp, 'Temporary') !== false) {
                                $listTemporary = DB::table($tmp)->get();
                                foreach($listTemporary as $data){
                                    $insertArr[] = [
                                        'ItemID'=> $parentID . '#' . $data->ItemID,
                                        'ParentID'=> $parentID,
                                        'ItemName'=> $data->ItemName,
                                        'Detail'=> 0,
                                        'Norder'=> $item['Norder'],
                                        'Company'=> $item['Company'],
                                        'ParentCompanyNo'=> $item['ParentCompanyNo'],
                                    ];
                                }
                            }
                            else{
                                $itemName = $this->getItemNameReport($tmp, $argCateNo, $argValue);
                                if($table[$i] == 'company' && $item['ParentCompanyNo'] != null){
                                    $insertNoParent[] =
                                        [
                                            'ItemID' => $item['ParentID'],
                                            'ParentID'=> $parentID,
                                            'ItemName'=> $itemName,
                                            'Detail'=> 0,
                                            'Norder'=> $item['Norder'],
                                            'Company'=> $item['Company'],
                                            'ParentCompanyNo'=> $item['ParentCompanyNo'],
                                        ];
                                }
                                else{
                                    $insertArr[] = [
                                        'ItemID' => $item['ParentID'],
                                        'ParentID'=> $parentID,
                                        'ItemName'=> $itemName,
                                        'Detail'=> 0,
                                        'Norder'=> $item['Norder'],
                                        'Company'=> $item['Company'],
                                        'ParentCompanyNo'=> $item['ParentCompanyNo'],
                                    ];
                                }
                            }
                        }
                    }
                    DB::table('table_tmp')->insertOrIgnore($insertArr);
                    DB::table('table_tmp')->insertOrIgnore($insertNoParent);
                    $uniqueItems = $insertArr;
                }

            }

        }
    }

}
