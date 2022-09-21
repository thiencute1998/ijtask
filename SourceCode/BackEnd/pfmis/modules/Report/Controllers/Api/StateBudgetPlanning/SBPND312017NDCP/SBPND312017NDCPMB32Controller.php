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

class SBPND312017NDCPMB32Controller extends Controller {
    use Common;
    public function SBPND312017NDCP_MB32(Request $request){

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
        if(in_array($userCompany->SumCompanyType, [2,3])){
            if($userCompany->SumCompanyType == 1){
                // Tổng hợp đơn vị con
                $whereCommonCondition = " AND C.CompanyNo = '" . $userCompany->CompanyNo ."'";
            }
            else{
                if($userCompany->SumCompanyType == 2){
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
          `ParentID1` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
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
          `I8` double(20,4) DEFAULT NULL,
          `I9` double(20,4) DEFAULT NULL,
          `I10` double(20,4) DEFAULT NULL,
          `I11` double(20,4) DEFAULT NULL,
          `I12` double(20,4) DEFAULT NULL,
          `I13` double(20,4) DEFAULT NULL,
          `I14` double(20,4) DEFAULT NULL,
          `I15` double(20,4) DEFAULT NULL,
          `I16` double(20,4) DEFAULT NULL,
          `I17` double(20,4) DEFAULT NULL,
          `I18` double(20,4) DEFAULT NULL,
          `I19` double(20,4) DEFAULT NULL,
          `I20` double(20,4) DEFAULT NULL,
          `I21` double(20,4) DEFAULT NULL,
          `I22` double(20,4) DEFAULT NULL,
          `District` varchar(100) DEFAULT '',
          `Commune` varchar(100) DEFAULT '',
          `Path` varchar(255) DEFAULT '',
          `Detail` tinyint(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (`ItemID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);





        // sum up
        $this->sumUpTableTmp(1, 5);

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
            $data[$key]->I12 = ($data[$key]->I12) ? $data[$key]->I12 : '';
            $data[$key]->I13 = ($data[$key]->I13) ? $data[$key]->I13 : '';
            $data[$key]->I14 = ($data[$key]->I14) ? $data[$key]->I14 : '';
            $data[$key]->I15 = ($data[$key]->I15) ? $data[$key]->I15 : '';
            $data[$key]->I16 = ($data[$key]->I16) ? $data[$key]->I16 : '';
            $data[$key]->I17 = ($data[$key]->I17) ? $data[$key]->I17 : '';
            $data[$key]->I18 = ($data[$key]->I18) ? $data[$key]->I18 : '';
            $data[$key]->I19 = ($data[$key]->I19) ? $data[$key]->I19 : '';
            $data[$key]->I20 = ($data[$key]->I20) ? $data[$key]->I20 : '';
            $data[$key]->I21 = ($data[$key]->I21) ? $data[$key]->I21 : '';
            $data[$key]->I22 = ($data[$key]->I22) ? $data[$key]->I22 : '';
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
                "ReportName" => "DỰ TOÁN THU NGÂN SÁCH NHÀ NƯỚC TRÊN ĐỊA BÀN TỪNG HUYỆN (XÃ) THEO LĨNH VỰC NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    public function removeLevelI(){
        return "REPLACE(R.Path, CONCAT(SUBSTRING_INDEX(R.Path, '#', 1), '#'), '')";
    }

    public function insertC3ToC10(){
        $sql = "INSERT INTO table_tmp (ItemID, ParentID, ParentID1, ItemName, Commune, Path, Detail, I3, I4, I5, I6, I7, I8, I9, I10)
        SELECT CONCAT(ParentID, '#', RevenueNo) as ItemID, ParentID, ParentID1, RevenueName, Path, 1 as Detail,
        SUM(CASE WHEN THEN I ELSE 0 END) as I3,
        SUM(CASE WHEN THEN I ELSE 0 END) as I4,
        SUM(CASE WHEN THEN I ELSE 0 END) as I5,
        SUM(CASE WHEN THEN I ELSE 0 END) as I6,
        SUM(CASE WHEN THEN I ELSE 0 END) as I7,
        SUM(CASE WHEN THEN I ELSE 0 END) as I8,
        SUM(CASE WHEN THEN I ELSE 0 END) as I9,
        SUM(CASE WHEN THEN I ELSE 0 END) as I10 FROM
        (SELECT CONCAT('A#', C.DistrictNo, '#', C.CommuneNo, '#', " . $this->removeLevelI() . ") as ParentID,
        1 as ParentID1, A.RevenueName, " . $this->removeLevelI() . " as Path, SUM(LCDebitAmount) as I,
        SSCV.CateValue as SbrSectorCateValue
        FROM act_gl_books as A
        INNER JOIN company as C on A.InputCompanyID = C.CompanyID
        INNER JOIN sbr_sector_cate_value as SSCV on SSCV.SbrSectorID = A.SbrSectorID
        INNER JOIN revenue_cate as RC on A.RevenueID = RC.RevenueID
        INNER JOIN capital_cate as CC on A.CapitalID = CC.CapitalID
        WHERE
        )";

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

    public function getParentDataTreeForMB31($table, $id = null){
        $uniqueItems = [];
        for($i = count($table) - 2; $i >=0 ; $i--){
            $insert = [];
            $insertArr = [];
            $groupBy = '';
            $where = ' AND 1 = 1';
            if($i <= count($table) - 2 && $i >= count($table) - 3){
                for($j = 0; $j <= $i; $j++) {
                    if(is_array($table[$j])){
                        $tmp = $table[$j][0];
                        $argValue = $table[$j][1];
                    }
                    else{
                        $tmp = $table[$j];
                    }
                    $join = ucfirst($tmp);
                    if($j == count($table) - 2){
                        continue;
                    }
                    if(strpos($tmp,'_') !== false){
                        $join = join(array_map('ucfirst',explode('_',$tmp)));
                    }
                    if(is_array($table[$j])){
                        if(strpos($tmp,'_cate_value') === false){
                            $where .= " AND $join = '" . $argValue . "'";
                        }
                    }
                    $where .= " AND $join != '' ";
                    $groupBy .= $join . ',';
                }
                if(substr($groupBy,'-1') == ','){
                    $groupBy = rtrim($groupBy,',');
                }
                $like = '';
                if($id !== null){
                    $like = "ItemID LIKE '" . $id . "%' AND";
                }
                if($i == count($table) - 2){
                    if($groupBy != ''){
                        $sql = "SELECT * FROM table_tmp WHERE $like Detail = 1 $where GROUP BY " . $groupBy . ",Path";
                    }
                    else{
                        $sql = "SELECT * FROM table_tmp WHERE $like Detail = 1 $where GROUP BY Path";
                    }
                    $listDetail = DB::select($sql);
//                    $listGroupParent = explode(',',$groupBy);
                    foreach($listDetail as $item){
                        $item = (array)$item;
                        $getParentID = explode('#',$item['Path']);
                        array_shift($getParentID);
                        if(!empty($getParentID)){
                            $listParent = DB::table($table[$i])
                                ->where('Inactive','!=',1)
                                ->whereIn('CateNo',$getParentID)
                                ->orderBy('Level','DESC')
                                ->get();
                            $itemID = $item['ItemID'];
                            foreach($listParent as $key=> $data){
                                $insert = [];
//                                foreach($listGroupParent as $value){
//                                    $insert[] = [
//                                        $value=> $item[$value],
//                                    ];
//                                }
                                $detailCateID = explode('#',$itemID);
                                array_pop($detailCateID);
                                $itemID = join('#',$detailCateID);
                                array_pop($detailCateID);
                                $parentID = join('#',$detailCateID);
                                $insertArr[] = [
                                    'ItemID'=> $itemID,
                                    'ParentID'=> $parentID,
                                    'ItemName'=> $data->CateName,
                                    'Detail'=> 0
                                ];
//                                $insertArr[] = call_user_func_array('array_merge', $insert);
                            }
                        }
                    }
                }
                else{
                    $sql = "SELECT * FROM table_tmp WHERE $like Detail = 1 $where GROUP BY $groupBy";
                    $listDetail = DB::select($sql);
                    if(is_array($table[$i])){
                        $tmp = $table[$i][0];
                        $argCateNo = $table[$i][1];
                    }
                    else{
                        $tmp = $table[$i];
                        $argCateNo = '';
                    }
                    $argValue = join(array_map('ucfirst',explode('_',$tmp)));
                    foreach($listDetail as $item){
                        $item = (array)$item;
                        $itemID = str_replace('#'.$item['Path']. '#' .$item[ucfirst(end($table))], '', $item['ItemID']);
                        $detailItemID = explode('#',$itemID);
                        array_pop($detailItemID);
                        $parentID = join('#',$detailItemID);
                        $itemName = $this->getItemNameReport($tmp, $argCateNo, $item[$argValue]);
                        $insertArr[] = [
                            'ItemID'=> $itemID,
                            'ParentID'=> $parentID,
                            'ItemName'=> $itemName,
                            'Detail'=> 0
                        ];
                        $uniqueItems = $insertArr;
                    }
                }
                DB::table('table_tmp')->insertOrIgnore($insertArr);
            }
            else{
                if($uniqueItems){
                    $arrItemID = [];
                    foreach($uniqueItems as $item){
                        if(!in_array($item['ParentID'], $arrItemID)){
                            $arrItemID[] = $item['ParentID'];
                            $lastIndex = strrpos($item['ParentID'], '#');
                            $parentID = substr($item['ParentID'],0,$lastIndex);
                            $argValue = substr($item['ParentID'], $lastIndex + 1, strlen($item['ParentID']));
                            if(is_array($table[$i])){
                                $tmp = $table[$i][0];
                                $argCateNo = $table[$i][1];
                            }
                            else{
                                $tmp = $table[$i];
                                $argCateNo = '';
                            }
                            $itemName = $this->getItemNameReport($tmp, $argCateNo, $argValue);
                            $insertArr[] = [
                                'ItemID' => $item['ParentID'],
                                'ParentID'=> $parentID,
                                'ItemName'=> $itemName,
                            ];
                        }
                    }
                    DB::table('table_tmp')->insertOrIgnore($insertArr);
                    $uniqueItems = $insertArr;
                }
            }
        }

    }

}
