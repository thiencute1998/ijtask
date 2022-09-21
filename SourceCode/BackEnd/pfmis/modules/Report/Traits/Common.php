<?php
namespace Module\Report\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

Trait Common {

    public function updateIndexListing($table){
        $insertArr = [];
        $maxLevel = DB::table($table)->select(DB::raw('Max(Level) as maxLevel'))->first();
        for($i = 1; $i<=$maxLevel->maxLevel ; $i++){
            $insertArr1 = [];
            $test = DB::table($table)->orderBy('CateNo','ASC')->where('Level','=',$i)->get();
            if($i == 1){
                foreach ($test as $key => $item){
                    $insertArr1[] = [
                        'CateID' => $item->CateID,
                        'ParentID'=> $item->ParentID,
                        'Level'=> $item->Level,
                        'Index'=> $key + 1
                    ];
                    DB::table($table)->where('CateID','=',$item->CateID)->update(['Index'=>$key + 1]);
                }
            }
            else{
                foreach($insertArr[$i-2] as $value){
                    $x = 1;
                    foreach ($test as $key => $item){
                        if($value['CateID'] == $item->ParentID){
                            $insertArr1[] = [
                                'CateID' => $item->CateID,
                                'ParentID'=> $item->ParentID,
                                'Level'=>$item->Level,
                                'Index'=> $value['Index']. '.'. $x,
                            ];
                            DB::table($table)->where('CateID','=',$item->CateID)->update(['Index'=>$value['Index']. '.'. $x]);
                            $x++;
                        }
                    }
                }
            }
            $insertArr[] = $insertArr1;
        }
    }

    public function updateIndexType($table){
        $insertArr = [];
        $maxLevelType = DB::table($table)->select(DB::raw('Max(ItemType) as maxLevelType'))->first();
        for($a = 1; $a <= $maxLevelType->maxLevelType ; $a++){
            $maxLevel = DB::table($table)->select(DB::raw('Max(Level) as maxLevel'))->where('ItemType','=',$a)->first();
            for($i = 1; $i<=$maxLevel->maxLevel ; $i++){
                $insertArr1 = [];
                $test = DB::table($table)->orderBy('ItemNo','ASC')->where('Level','=',$i)->where('ItemType','=',$a)->get();
                if($i == 1){
                    foreach ($test as $key => $item){
                        $insertArr1[] = [
                            'ItemNo' => $item->ItemNo,
                            'ParentNo'=> $item->ParentNo,
                            'ItemType'=> $item->ItemType,
                            'Level'=> $item->Level,
                            'Index'=> $key + 1
                        ];
                        DB::table($table)->where('ItemNo','=',$item->ItemNo)->where('ItemType','=',$item->ItemType)->update(['Index'=>$key + 1]);
                    }
                }
                else{
                    foreach($insertArr[$a][$i-2] as $value){
                        $x = 1;
                        foreach ($test as $key => $item){
                            if($value['ItemNo'] == $item->ParentNo){
                                $insertArr1[] = [
                                    'ItemNo' => $item->ItemNo,
                                    'ParentNo'=> $item->ParentNo,
                                    'ItemType'=> $item->ItemType,
                                    'Level'=>$item->Level,
                                    'Index'=> $value['Index']. '.'. $x,
                                ];
                                DB::table($table)->where('ItemNo','=',$item->ItemNo)->where('ItemType','=',$item->ItemType)->update(['Index'=> $value['Index']. '.'. $x]);
                                $x++;
                            }
                        }
                    }
                }
                $insertArr[$a][] = $insertArr1;
            }
        }
    }

    public function updateIndexReport($id = null){
        $insertArr = [];
        $maxLevel = DB::table('table_tmp')->select(DB::raw('Max(Level) as maxLevel'))->first();
        for($i = 1; $i<=$maxLevel->maxLevel ; $i++){
            $insertArr1 = [];
            $where = '';
            if($id !== null){
                $where = "AND LEFT(ItemID, LENGTH('" . $id ."')) = '" . $id . "'";
            }
            $sql = "SELECT * FROM table_tmp WHERE Level = $i $where ORDER BY ItemID ASC";
            $result = DB::select($sql);
            if($i == 1){
                foreach ($result as $key => $item){
                    $insertArr1[] = [
                        'ItemID' => $item->ItemID,
                        'ParentID'=> $item->ParentID,
                        'Level'=> $item->Level,
                        'STT'=> $key + 1
                    ];
                    DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=>$key + 1]);
                }
            }
            else{
                foreach($insertArr[$i-2] as $value){
                    $x = 1;
                    foreach ($result as $key => $item){
                        if($value['ItemID'] == $item->ParentID){
                            $insertArr1[] = [
                                'ItemID' => $item->ItemID,
                                'ParentID'=> $item->ParentID,
                                'Level'=>$item->Level,
                                'STT'=> $value['STT']. '.'. $x,
                            ];
                            DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=>$value['STT']. '.'. $x]);
                            $x++;
                        }
                    }
                }
            }
            $insertArr[] = $insertArr1;
        }
    }

    public function updateIndexReportB200($id = null){
        $insertArr = [];
        $maxLevel = DB::table('table_tmp')->select(DB::raw('Max(Level) as maxLevel'))->first();
        for($i = 1; $i<=$maxLevel->maxLevel ; $i++){
            $insertArr1 = [];
            $where = '';
            if($id !== null){
                $where = "AND LEFT(ItemID, LENGTH('" . $id ."')) = '" . $id . "'";
            }
            $sql = "SELECT * FROM table_tmp WHERE Level = $i $where ORDER BY ItemID ASC";
            $result = DB::select($sql);
            if($i == 1){
                foreach ($result as $key => $item){
                    DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=>$this->numberToRomanRepresentation($key + 1)]);
                }
            }
            else if($i == 2){
               $sqlParent = "SELECT * FROM table_tmp WHERE Level = " . ($i - 1) . " $where ORDER BY ItemID ASC";
               $resultParent = DB::select($sqlParent);
               foreach($resultParent as $value){
                   $index = 1;
                   foreach($result as $key=> $item){
                       if($value->ItemID == $item->ParentID){
                           $insertArr1[] = [
                               'ItemID' => $item->ItemID,
                               'ParentID'=> $item->ParentID,
                               'Level'=> $item->Level,
                               'STT'=> $index
                           ];
                           DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=>$index]);
                           $index++;
                       }
                   }
               }
            }
            else{
                foreach($insertArr[$i-2] as $value){
                    $x = 1;
                    foreach ($result as $key => $item){
                        if($value['ItemID'] == $item->ParentID){
                            $insertArr1[] = [
                                'ItemID' => $item->ItemID,
                                'ParentID'=> $item->ParentID,
                                'Level'=>$item->Level,
                                'STT'=> $value['STT']. '.'. $x,
                            ];
                            DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['STT'=>$value['STT']. '.'. $x]);
                            $x++;
                        }
                    }
                }
            }
            $insertArr[] = $insertArr1;
        }
    }

    public function getDebitAmount($ldFromDate, $ldToDate, $tnDateType = 1, $tnAmountType = 1, $tcCoaTypeNo = '01', $tcColumn = 1
        , $tcSelect1, $tcFrom1, $tcWhere1, $tcGroup1, $tcSet, $tcWhere2, $tcSelect2 = '', $tcSelectMax = '', $tcGroup2 = '')
    {

        $lcSQLCommand = "SELECT $tcSelect1, IF($tnAmountType = 1, SUM(LCDebitAmount), SUM(FCDebitAmount)) AS I
            FROM $tcFrom1 WHERE $tcWhere1
            AND PostType = 1
            AND ";
        if($tcColumn == 1){
            $ldYear = "IF($tnDateType = 1, PostDate BETWEEN '" .$ldFromDate. "' AND '" . $ldToDate . "', TransDate BETWEEN '" .$ldFromDate. "' AND '" . $ldToDate . "')";
            $sql = "UPDATE table_tmp as TT, ($lcSQLCommand $ldYear GROUP BY $tcGroup1
                ) as HH SET $tcSet WHERE $tcWhere2";
        }
        else{
            $ldYear = "(IF($tnDateType = 1,
            (YEAR(PostDate) BETWEEN YEAR('" . $ldToDate ."') - 1 AND YEAR('" . $ldToDate ."') + 1),
            (YEAR(TransDate) BETWEEN YEAR('" . $ldToDate ."') - 1 AND YEAR('" . $ldToDate ."') + 1)
            ))";
            $sql = "UPDATE table_tmp as TT, (SELECT $tcSelect2, $tcSelectMax FROM
                ($lcSQLCommand  $ldYear AND CoaTypeNo = $tcCoaTypeNo GROUP BY $tcGroup1
                ) as CH GROUP BY $tcGroup2) as HH SET $tcSet WHERE $tcWhere2";
        }
        DB::statement($sql);
    }

    public function getCreditAmount($ldFromDate, $ldToDate, $tnDateType = 1, $tnAmountType = 1, $tcCoaTypeNo = '01', $tcColumn = 1
        , $tcSelect1, $tcFrom1, $tcWhere1, $tcGroup1, $tcSet, $tcWhere2, $tcSelect2 = '', $tcSelectMax = '', $tcGroup2 = '')
    {

        $lcSQLCommand = "SELECT $tcSelect1, IF($tnAmountType = 1, SUM(LCDebitAmount), SUM(FCDebitAmount)) AS I
            FROM $tcFrom1 WHERE $tcWhere1
            AND PostType = 2
            AND ";
        if($tcColumn == 1){
            $ldYear = "IF($tnDateType = 1, PostDate BETWEEN '" .$ldFromDate. "' AND '" . $ldToDate . "', TransDate BETWEEN '" .$ldFromDate. "' AND '" . $ldToDate . "')";
            $sql = "UPDATE table_tmp as TT, ($lcSQLCommand $ldYear GROUP BY $tcGroup1
                ) as HH SET $tcSet WHERE $tcWhere2";
        }
        else{
            $ldYear = "(IF($tnDateType = 1,
            (YEAR(PostDate) BETWEEN YEAR('" . $ldToDate ."') - 1 AND YEAR('" . $ldToDate ."') + 1),
            (YEAR(TransDate) BETWEEN YEAR('" . $ldToDate ."') - 1 AND YEAR('" . $ldToDate ."') + 1)
            ))";
            $sql = "UPDATE table_tmp as TT, (SELECT $tcSelect2, $tcSelectMax FROM
                ($lcSQLCommand  $ldYear AND CoaTypeNo = $tcCoaTypeNo GROUP BY $tcGroup1
                ) as CH GROUP BY $tcGroup2) as HH SET $tcSet WHERE $tcWhere2";
        }
        DB::statement($sql);
    }

    /**
     * @param int $number
     * @return string
     */
    function numberToRomanRepresentation($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }


    // tree data
    public function createParentDataTree($arr){
        for($i = count($arr) - 2; $i >= 0; $i--){
            $insertArr = [];
            $groupBy = '';
            for($j = 0; $j <= $i; $j++){
                $join = ucfirst($arr[$j]);
                if(strpos($arr[$j],'_') !== false){
                    $join = join(array_map('ucfirst',explode('_',$arr[$j])));
                }
                if($j < $i){
                    $join .= ',';
                }
                $groupBy .= $join;
            }
            $listParent = explode(',',$groupBy);
            $sql = "SELECT * FROM table_tmp GROUP BY $groupBy";
            $list = DB::select($sql);
            foreach($list as $item){
                $item = (array)$item;
                $result = $this->getParentDataTree($item['ParentID']);
                $insert = [];
                foreach ($listParent as $value){
                    $insert[] = [
                        $value=> $item[$value],
                        $value.'Name'=> $item[$value.'Name']
                    ];
                }
                $insert[] = [
                    'ItemID'=> $result['ItemID'],
                    'ItemName'=> $item[end($listParent).'Name'],
                    'ParentID'=> $result['ParentID'],

                ];
                $insertArr[] = call_user_func_array('array_merge', $insert);
            }
            DB::table('table_tmp')->insert($insertArr);
        }
    }

    public function getParentDataTree($itemID){
        $lastIndex = strrpos($itemID, '#');
        $parentID = substr($itemID,0,$lastIndex);
        $itemNo = substr($itemID,$lastIndex + 1, strlen($itemID));
        return [
            'ItemID'=> $itemID,
            'ParentID'=> $parentID,
            'ItemNo'=>$itemNo
        ];
    }

    public function setLevel($level = 0){
        $listItems = DB::table('table_tmp')->get();
        foreach($listItems as $item){
            $countLevel = substr_count($item->ItemID,'#');
            DB::table('table_tmp')->where('ItemID','=',$item->ItemID)->update(['Level'=> $countLevel + $level]);
        }
    }

    // tree data B200

    public function getParentDataTreeForB200($table, $id = null){
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

    // tree data common

    public function getParentDataTreeCommon($table, $id = null){
        $uniqueItems = [];
        $arrID = [];
        for($i = count($table) - 2; $i >=0 ; $i--){
            $insertArr = [];
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
                                    'Detail'=> 0
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
                                'Detail'=> 0
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
                            if (strpos($tmp, 'Temporary') !== false) {
                                $listTemporary = DB::table($tmp)->get();
                                foreach($listTemporary as $data){
                                    $insertArr[] = [
                                        'ItemID'=> $parentID . '#' . $data->ItemID,
                                        'ParentID'=> $parentID,
                                        'ItemName'=> $data->ItemName,
                                        'Detail'=> 0
                                    ];
                                }
                            }
                            else{
                                $itemName = $this->getItemNameReport($tmp, $argCateNo, $argValue);
                                $insertArr[] = [
                                    'ItemID' => $item['ParentID'],
                                    'ParentID'=> $parentID,
                                    'ItemName'=> $itemName,
                                    'Detail'=> 0
                                ];
                            }
                        }
                    }
                    DB::table('table_tmp')->insertOrIgnore($insertArr);
                    $uniqueItems = $insertArr;
                }

            }

        }
    }

    public function getParentDataTreeCommonDetail($table, $id = null){
        $uniqueItems = [];
        $arrID = [];
        $listParent = [];
        for($i = count($table) - 2; $i >=0 ; $i--){
            $insertArr = [];
            $insert = [];
            if($i == count($table) - 2){
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
                $listParent = explode(',',$groupBy);
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
                        if($listParent != ''){
                            foreach($listParent as $value){
                                $insert[] = [
                                    $value=> $item[$value],
                                ];
                            }
                        }
                        $itemID = $item['ParentID'];
                        $lastIndex = strrpos($itemID, '#');
                        $parentID = substr($itemID,0,$lastIndex);
                        if (strpos($tmp, 'Temporary') !== false) {
                            $listTemporary = DB::table($tmp)->get();
                            foreach($listTemporary as $data){
                                $insert[] = [
                                    'ItemID'=> $parentID . '#' . $data->ItemID,
                                    'ParentID'=> $parentID,
                                    'ItemName'=> $data->ItemName,
                                    'Detail'=> 0
                                ];
                                $insertArr[] = call_user_func_array('array_merge', $insert);
                            }
                        }
                        else{
                            $itemName = $this->getItemNameReport($tmp, $argCateNo, $item[$argValue]);
                            $insert[] = [
                                'ItemID'=> $itemID,
                                'ParentID'=> $parentID,
                                'ItemName'=> $itemName,
                                'Detail'=> 0
                            ];
                            $insertArr[] = call_user_func_array('array_merge', $insert);
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
                    array_pop($listParent);
                    foreach($uniqueItems as $item){
                        if(!in_array($item['ParentID'], $arrItemID)){
                            if($listParent != ''){
                                foreach($listParent as $value){
                                    $insert[] = [
                                        $value=> $item[$value],
                                    ];
                                }
                            }

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
                            if (strpos($tmp, 'Temporary') !== false) {
                                $listTemporary = DB::table($tmp)->get();
                                foreach($listTemporary as $data){
                                    $insert[] = [
                                        'ItemID'=> $parentID . '#' . $data->ItemID,
                                        'ParentID'=> $parentID,
                                        'ItemName'=> $data->ItemName,
                                        'Detail'=> 0
                                    ];
                                    $insertArr[] = call_user_func_array('array_merge', $insert);
                                }
                            }
                            else{
                                $itemName = $this->getItemNameReport($tmp, $argCateNo, $argValue);
                                $insert[] = [
                                    'ItemID' => $item['ParentID'],
                                    'ParentID'=> $parentID,
                                    'ItemName'=> $itemName,
                                    'Detail'=> 0
                                ];
                                $insertArr[] = call_user_func_array('array_merge', $insert);
                            }
                        }
                    }
                    DB::table('table_tmp')->insertOrIgnore($insertArr);
                    $uniqueItems = $insertArr;
                }

            }

        }
    }

    public function getParentDataTreeListing($table, $id = null)
    {
        $uniqueItems = [];
        for ($i = count($table) - 1; $i >= 0; $i--) {
            $insert = [];
            $insertArr = [];
            $groupBy = '';
            $where = ' AND 1 = 1';
            if ($i <= count($table) - 1 && $i >= count($table) - 2) {
                for ($j = 0; $j <= $i; $j++) {
                    if (is_array($table[$j])) {
                        $tmp = $table[$j][0];
                        $argValue = $table[$j][1];
                    } else {
                        $tmp = $table[$j];
                    }
                    $join = ucfirst($tmp);
                    if ($j == count($table) - 2) {
                        continue;
                    }
                    if (strpos($tmp, '_') !== false) {
                        $join = join(array_map('ucfirst', explode('_', $tmp)));
                    }
                    if (is_array($table[$j])) {
                        if (strpos($tmp, '_cate_value') === false) {
                            $where .= " AND $join = '" . $argValue . "'";
                        }
                    }
                    $where .= " AND $join != '' ";
                    $groupBy .= $join . ',';
                }
                if (substr($groupBy, '-1') == ',') {
                    $groupBy = rtrim($groupBy, ',');
                }
                $like = '';
                if ($id !== null) {
                    $like = "ItemID LIKE '" . $id . "%' AND";
                }
                if ($i == count($table) - 1) {
                    if ($groupBy != '') {
                        $sql = "SELECT * FROM table_tmp WHERE $like Detail = 1 $where GROUP BY " . $groupBy . ",Path";
                    } else {
                        $sql = "SELECT * FROM table_tmp WHERE $like Detail = 1 $where GROUP BY Path";
                    }
                    $listDetail = DB::select($sql);
//                    $listGroupParent = explode(',',$groupBy);
                    foreach ($listDetail as $item) {
                        $item = (array)$item;
                        $lastIndex = strrpos($item['Path'], '#');
                        $parentID = substr($item['Path'], 0, $lastIndex);
                        $getParentID = explode('#', $parentID);
                        if (!empty($getParentID)) {
                            $listParent = DB::table($table[$i])
                                ->where('Inactive', '!=', 1)
                                ->whereIn($table[$i] . 'No', $getParentID)
                                ->orderBy('Level', 'DESC')
                                ->get();
                            $itemID = $item['ItemID'];
                            foreach ($listParent as $key => $data) {
                                $data = (array)$data;
                                $insert = [];
//                                foreach($listGroupParent as $value){
//                                    $insert[] = [
//                                        $value=> $item[$value],
//                                    ];
//                                }
                                $detailCateID = explode('#', $itemID);
                                array_pop($detailCateID);
                                $itemID = join('#', $detailCateID);
                                array_pop($detailCateID);
                                $parentID = join('#', $detailCateID);
                                $insertArr[] = [
                                    'ItemID' => $itemID,
                                    'ParentID' => $parentID,
                                    'ItemName' => $data[ucfirst($table[$i]) . 'Name'],
                                    'Detail' => 0
                                ];
//                                $insertArr[] = call_user_func_array('array_merge', $insert);
                            }
                        }
                    }
                } else {
                    $sql = "SELECT * FROM table_tmp WHERE $like Detail = 1 $where GROUP BY $groupBy";
                    $listDetail = DB::select($sql);
                    if (is_array($table[$i])) {
                        $tmp = $table[$i][0];
                        $argCateNo = $table[$i][1];
                    } else {
                        $tmp = $table[$i];
                        $argCateNo = '';
                    }
                    $argValue = join(array_map('ucfirst', explode('_', $tmp)));
                    foreach ($listDetail as $item) {
                        $item = (array)$item;
                        $itemID = str_replace('#' . $item['Path'] . '#' . $item[ucfirst(end($table))], '', $item['ItemID']);
                        $detailItemID = explode('#', $itemID);
                        array_pop($detailItemID);
                        $parentID = join('#', $detailItemID);
                        $itemName = $this->getItemNameReport($tmp, $argCateNo, $item[$argValue]);
                        $insertArr[] = [
                            'ItemID' => $itemID,
                            'ParentID' => $parentID,
                            'ItemName' => $itemName,
                            'Detail' => 0
                        ];
                        $uniqueItems = $insertArr;
                    }
                }
                DB::table('table_tmp')->insertOrIgnore($insertArr);
            } else {
                if ($uniqueItems) {
                    $arrItemID = [];
                    foreach ($uniqueItems as $item) {
                        if (!in_array($item['ParentID'], $arrItemID)) {
                            $arrItemID[] = $item['ParentID'];
                            $lastIndex = strrpos($item['ParentID'], '#');
                            $parentID = substr($item['ParentID'], 0, $lastIndex);
                            $argValue = substr($item['ParentID'], $lastIndex + 1, strlen($item['ParentID']));
                            if (is_array($table[$i])) {
                                $tmp = $table[$i][0];
                                $argCateNo = $table[$i][1];
                            } else {
                                $tmp = $table[$i];
                                $argCateNo = '';
                            }
                            $itemName = $this->getItemNameReport($tmp, $argCateNo, $argValue);
                            $insertArr[] = [
                                'ItemID' => $item['ParentID'],
                                'ParentID' => $parentID,
                                'ItemName' => $itemName,
                            ];
                        }
                    }
                    DB::table('table_tmp')->insertOrIgnore($insertArr);
                    $uniqueItems = $insertArr;
                }
            }
        }
    }

    public function getItemNameReport($tmp, $argCateNo, $argValue)
    {
        if (strpos($tmp, 'Temporary') !== false) {
            $itemName = DB::table($tmp)->where('ItemID', '=', $argValue)->first()->ItemName;
        } else {
            if (strpos($tmp, '_cate_list') !== false) {
                $itemName = DB::table($tmp)->where('CateNo', '=', $argValue)->first()->CateName;
            } else if (strpos($tmp, '_cate_value') !== false) {
                $itemName = DB::table($tmp)->where('CateNo','=',$argCateNo)->where('CateValue','=',$argValue)->first()->Description;
            }
            else{
                $itemSub = DB::table($tmp)->where(ucfirst($tmp).'No','=',$argValue)->first();
                $itemName = '';
                if($itemSub){
                    $itemSub = (array)$itemSub;
                    $itemName = $itemSub[ucfirst($tmp).'Name'];
                }
            }
        }
        return $itemName;
    }

    public function getParentCompanyFromPath($levelCompany){
        $listPath = DB::table('table_tmp')->where('Detail',1)->where('Path','like','%#%')->get();
        if($listPath){
            $insertArr = [];
            foreach($listPath as $item){
                $listParent = explode('#', $item->Path);
                $listItemID = explode('#', $item->ItemID);
                $listItemParentID = explode('#', $item->ParentID);
                array_pop($listParent);
                $listParent = array_reverse($listParent);
                if(!empty($listParent)){
                    foreach($listParent as $value){
                        array_pop($listItemID);
                        array_pop($listItemParentID);
                        $itemID = join("#", $listItemID);
                        $parentID = join("#", $listItemParentID);
                        $itemName = DB::table('company')->where('Inactive','!=', 1)->where('CompanyNo','=', $value)->first()->CompanyName;
                        $insertArr[] = [
                            'ItemID'=> $itemID,
                            'ParentID'=> $parentID,
                            'ItemName'=> $itemName
                        ];
                    }
                }
            }
            DB::table('table_tmp')->insertOrIgnore($insertArr);
        }
    }

    public function getParentDataTreeCommonNorder($table, $id = null){
        $uniqueItems = [];
        $arrID = [];
        for($i = count($table) - 2; $i >=0 ; $i--){
            $insertArr = [];
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
                                    'Norder'=> $item['Norder']
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
                                'Norder'=> $item['Norder']
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
                            if (strpos($tmp, 'Temporary') !== false) {
                                $listTemporary = DB::table($tmp)->get();
                                foreach($listTemporary as $data){
                                    $insertArr[] = [
                                        'ItemID'=> $parentID . '#' . $data->ItemID,
                                        'ParentID'=> $parentID,
                                        'ItemName'=> $data->ItemName,
                                        'Detail'=> 0,
                                        'Norder'=> $item['Norder']
                                    ];
                                }
                            }
                            else{
                                $itemName = $this->getItemNameReport($tmp, $argCateNo, $argValue);
                                $insertArr[] = [
                                    'ItemID' => $item['ParentID'],
                                    'ParentID'=> $parentID,
                                    'ItemName'=> $itemName,
                                    'Detail'=> 0,
                                    'Norder'=> $item['Norder']
                                ];
                            }
                        }
                    }
                    DB::table('table_tmp')->insertOrIgnore($insertArr);
                    $uniqueItems = $insertArr;
                }

            }

        }
    }


}
