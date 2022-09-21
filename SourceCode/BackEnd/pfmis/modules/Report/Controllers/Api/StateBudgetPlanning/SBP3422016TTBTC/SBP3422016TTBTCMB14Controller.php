<?php
namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB14Controller extends Controller {
    public function SBP3422016TTBTC_MB14(Request $request){
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


        $whereDirection = '';
        if ($directionID) {
            $whereDirection = 'AND A.DirectionID = ' . $directionID . ' ';
        }

        if (isset($filter['CompanyID'])) {
            $note .= 'đơn vị ' . $filter['CompanyName'];
            $company = Company::where('CompanyID',$filter['CompanyID'])->first();
        }
        else{
            $company = Company::where('CompanyID',Auth::user()->CompanyID)->first();
        }

        if(!isset($filter['LevelCompany'])){
            $levelCompany = null;
        }
        else{
            $levelCompany = $filter['LevelCompany'];
        }

        $preYear = (int)$year - 1;
        $nextYear = (int)$year + 1;
        $whereCondition = "AND (
        ( YEAR(A.PeriodFromDate) = $year AND YEAR(A.PeriodToDate) = $year)
        OR (YEAR(A.PeriodFromDate) = $preYear AND YEAR(A.PeriodToDate) = $preYear)
        OR (YEAR(A.PeriodFromDate) = $nextYear AND YEAR(A.PeriodToDate) = $nextYear)
        ) AND A.InTransTypeID != 16 AND A.InTransTypeID != 17 AND A.InTransTypeID != 18 ";
// Lọc đơn vị phát sinh chứng từ

        $arr[] = array_column($this->selectI12($whereCondition), 'PathCompanyID');
        $arr[] = array_column($this->selectII1($whereCondition), 'PathCompanyID');
        $arr[] = array_column($this->selectII23($whereCondition), 'PathCompanyID');
        $arr[] = array_column($this->selectII3CT($whereCondition), 'PathCompanyID');
        $listPath = array_unique(call_user_func_array('array_merge', $arr));

        $arrID = [];
        if($company->IsFinancialCompany == 0){
            foreach ($listPath as $item){
                if(strpos($item, (string)$company->CompanyID) !== false){
                    $id = explode('_',$item);
                    foreach($id as $i){
                        $arrID[] = $i;
                    }
                }
            }
        }
        else{
            foreach($listPath as $item){
                $id = explode('_',$item);
                foreach($id as $i){
                    $arrID[] = $i;
                }
            }
        }
        $arrID = array_unique($arrID);
        $listChildrenCompany = DB::table('company')->where(function($q) use($arrID,$company){
            $q->whereIn('CompanyID',$arrID)
                ->orWhere('CompanyID','=',$company->CompanyID);
        })
            ->where('Level','>=',$company->Level)
            ->where('Inactive','!=',1)->get()->toArray();

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(100) DEFAULT '',
          `ItemNo` varchar(255) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `Show` int(1) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `AccountNo` varchar(15) DEFAULT '',
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `NormCateNo` varchar(100) DEFAULT '',
          `NormCateValue` varchar(100) DEFAULT '',
          `NormNo` varchar(100) DEFAULT '',
          `TranTypeID` int(2) DEFAULT 1,
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
            `CompanyID` int(11) DEFAULT NULL,
            `ParentCompanyID` int(11) DEFAULT NULL,
            `LevelCompany` int(11) DEFAULT NULL,
            `Detail` int(11) DEFAULT NULL,
            `Norder` int(11) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $this->MB14InsertTemplate($listChildrenCompany, $company);
        $this->MB14InsertTemplate3($year,$listChildrenCompany, $company);
        $this->insertI12($whereCondition,$preYear,$year,$nextYear);
        $this->insertII1($whereCondition,$preYear,$year,$nextYear);
        $this->insertII23($whereCondition,$preYear,$year,$nextYear);
        $this->insertII3CT($whereCondition,$preYear,$year,$nextYear);

        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        $maxLevelCompany = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.LevelCompany) AS maxLevel'))->first();
        $createTableTmp1 = "CREATE TEMPORARY TABLE t (SELECT * FROM table_tmp)";
        DB::statement($createTableTmp1);
        $this->sumUpTableTmp(1, $maxLevel);
        if(count($listChildrenCompany) > 1){
            $this->sumParent($maxLevelCompany,$company);
            if($company->IsFinancialCompany){
                $this->sumFinalcialCompany($company);
            }
        }
        $date = date('d');
        $month = date('m');
        $yearC = date('Y');
        if(!is_null($levelCompany)){
            $data = DB::table("table_tmp")->orderBy('Norder','ASC')->orderBy('ItemNo','ASC')->where('LevelCompany','<=',$levelCompany)->get();
        }
        else{
            $data = DB::table("table_tmp")->orderBy('Norder','ASC')->orderBy('ItemNo','ASC')->where('CompanyID','=',$company->CompanyID)->get();
        }

        if (isset($data[0])) {
            $data[0]->ColumnHead1 = 'Thực hiện năm ' . $preYear ;
            $data[0]->ColumnHead2 = 'Năm ' . $year ;
            $data[0]->ColumnHead3 = 'Dự toán năm ' . $nextYear ;
        }

        foreach ($data as $key => $value) {
            $data[$key]->I1 = ($data[$key]->I1) ? $data[$key]->I1 : '';
            $data[$key]->I2 = ($data[$key]->I2) ? $data[$key]->I2 : '';
            $data[$key]->I3 = ($data[$key]->I3) ? $data[$key]->I3 : '';
            $data[$key]->I4 = ($data[$key]->I4) ? $data[$key]->I4 : '';


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
//                "TransNo" => "00001",
                "ReportName" => "CƠ SỞ TÍNH CHI HOẠT ĐỘNG CỦA CÁC CƠ QUAN QUẢN LÝ NHÀ NƯỚC, ĐẢNG, ĐOÀN THỂ NĂM " . $year,
                "DateTime" => $dateTime,
                "CompanyName" => ($company) ? $company->CompanyName : 'UBND tỉnh Vĩnh Phú',
                "Note" => $note
            ],
            'detail' => $data
        ];

        $json['data'] = $response;
        return json_encode($json);
    }

    // Level 1
    public function MB14InsertTemplate($listChildrenCompany, $company){
        $insertArr = [];
        foreach ($listChildrenCompany as $index=> $item) {
            if(substr($item->CompanyNo, 0 ,3* $company->Level) === substr($company->CompanyNo, 0 ,3* $company->Level) && $company->IsFinancialCompany == 1)
                $norder = 1;
            else
                $norder = 2;
            $insertArr[] = $this->formatInsertArr('',1,'', $item->CompanyNo,'',null,$item->CompanyName,1, 0, 0, null, null, null, $item, $norder);
            $insertArr[] = $this->formatInsertArr('I',1,'A#', $item->CompanyNo . '.A#','',1,'Cơ sở tính',1, 0, 0, null, null, null, $item, $norder);
            $insertArr[] = $this->formatInsertArr('1',1,'A#01', $item->CompanyNo . '.A#01','A#',3,'Biên chế được giao',0, 0, 0, '20214', '1', null, $item, $norder);
            $insertArr[] = $this->formatInsertArr('2',1,'A#02', $item->CompanyNo . '.A#02','A#',3,'Biên chế thực tế',0, 0, 0, '20214', '2', null, $item, $norder);
            $insertArr[] = $this->formatInsertArr('II',1,'B#', $item->CompanyNo . '.B#','',1,'Tổng số kinh phí',1, 0, 0, null, null, null, $item, $norder);
            $insertArr[] = $this->formatInsertArr('1',1,'B#01', $item->CompanyNo . '.B#01','B#',3,'Qũy lương, phụ cấp và các khoản đóng góp theo lương',0, 0, 0, '2041211', null, null, $item, $norder);
            $insertArr[] = $this->formatInsertArr('a',1,'B#02', $item->CompanyNo . '.B#02','B#',2,'Tính theo biên chế thực tế',0, 0, 0, '2041211', null, null, $item, $norder);
            $insertArr[] = $this->formatInsertArr('b',1,'B#03', $item->CompanyNo . '.B#03','B#',3,'Tính theo biên chế được cấp có thẩm quyền giao (đối với trường hợp biên chế thực tế thấp hơn biên chế được cấp có thẩm quyền giao)',0, 0, 0, '2041211', null, null, $item, $norder);
            $insertArr[] = $this->formatInsertArr('2',1,'B#04', $item->CompanyNo . '.B#04','B#',2,'Chi thường xuyên theo định mức',0, 0, 0, '2041112', '1', null, $item, $norder);
            $insertArr[] = $this->formatInsertArr('3',1,'B#05', $item->CompanyNo . '.B#05','B#',2,'Chi đặc thù ngoài định mức (1)',0, 0, 0, '2041113', '1', null, $item, $norder);
        }
        DB::table('table_tmp')->insert($insertArr);
    }
    public function MB14InsertTemplate3($year, $listChildrenCompany, $company)
    {
        $maxLevelDetail = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.LevelCompany) AS maxLevel'))->where('Detail','=',1)->first();
        $listParent = array_filter($listChildrenCompany, function ($var){
            return ($var->Detail == 0);
        });
        if($maxLevelDetail->maxLevel > 1){
            $listChild = array_filter($listChildrenCompany, function ($var){
                return ($var->Detail == 1);
            });
        }
        else{
            $listChild = $listChildrenCompany;
        }

        $arrCompanyID = [];
        foreach ($listChild as $item){
            if($item->Detail == 1){
                $arrCompanyID[] = $item->CompanyID;
            }
        }

        $result = DB::table('act_gl_books as A')->select('company.*','A.PeriodToDate','A.PeriodFromDate',
            'A.NormName as ItemName','NC.CateNo as NormCateNo','NC.NormID as NormID','NRM.NormNo','NC.CateValue as NormCateValue')
            ->join('norm_cate as NC','NC.NormID','=','A.NormID')
            ->join('norm as NRM','NRM.NormID','=','A.NormID')
            ->join('sector_cate as SC','SC.SectorID','=','A.SectorID')
            ->join('sector as SCT','SCT.SectorID','=','SC.SectorID')
            ->join('company','company.CompanyID','=','A.InputCompanyID')
            ->where('NC.CateNo','=','2041113')
            ->where('NC.CateValue','=','1')
            ->where(DB::raw('LEFT(NRM.NormNo, 7) = 5111112'))
            ->where('SC.CateNo','=','003')
            ->where('SC.CateValue','=','12')
            ->whereNotIn('A.InTransTypeID',[16,17,18])
            ->whereIn('A.IntransTypeID',[2,4,6])
            ->whereBetween(DB::raw('YEAR(A.PeriodFromDate)'),[$year - 1, $year + 1])
            ->whereIn('A.InputCompanyID',$arrCompanyID)
            ->groupBy('NRM.NormNo','A.InputCompanyID')
            ->get();

        $insertArr = [];

        if ($result) {
            foreach ($result as $template) {
                $STT = '';
                $ItemID = 'B#05' . $template->NormNo ;
                $ParentID = 'B#05';
                $ItemName = '- ' .$template->ItemName;
                if(substr($template->CompanyNo, 0 ,3* $company->Level) === substr($company->CompanyNo, 0 ,3* $company->Level) && $company->IsFinancialCompany == 1){
                    $norder = 1;
                }
                else{
                    $norder = 2;
                }
                $insertArr[] = $this->formatInsertArr($STT,'',$ItemID, $template->CompanyNo . $ItemID,$ParentID,4,$ItemName,0, 0, 0, '2041113', '1', null, $template, $norder);
            }
            $maxLevelNotDetail = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.LevelCompany) AS maxLevel'))->where('Detail','=',0)->first();
            if($maxLevelNotDetail->maxLevel !== null){
                for($i = $maxLevelNotDetail->maxLevel ; $i>= $company->Level; $i--){
                    foreach ($listParent as $key => $value){
                        $test1 = [];
                        if($i == $value->Level){
                            $test = array_filter($insertArr, function ($var) use ($value){
                                return ($var['ParentCompanyID'] == $value->CompanyID);
                            });
                            $test1 = array_map(function($item) use($value, $company){
                                $itemNo = explode('.',$item['ItemNo']);
                                $item['ItemNo'] = $value->CompanyNo. '.' . $itemNo[1];
                                $item['CompanyID'] = $value->CompanyID;
                                $item['LevelCompany'] = $value->Level;
                                $item['ParentCompanyID'] = $value->ParentID;
                                $item['Detail'] = $value->Detail;
                                if(substr($value->CompanyNo, 0 ,3 * $company->Level) === substr($company->CompanyNo, 0 ,3  * $company->Level) && $company->IsFinancialCompany == 1){
                                    $item['Norder'] = 1;
                                }
                                else{
                                    $item['Norder'] = 2;
                                }
                                return $item;
                            },$test);
                        }
                        $insertArr = array_merge($insertArr, $test1);
                    }
                }
            }

            // Loai bo trùng nhau
            $uniqueSbiItems = [];
            $tmpUids = [];
            $listItemIDFinancial = [];
            $listProjectFinancial = [];
            $listProjectNotFinancial = [];
            foreach ($insertArr as $insert) {
                if (!in_array($insert['CompanyID'] . '.' . $insert['ItemID'], $tmpUids)) {
                    $tmpUids[] = $insert['CompanyID'] . '.' . $insert['ItemID'];
                    $uniqueSbiItems[] = $insert;
                    if($insert['CompanyID'] == $company->CompanyID && $company->IsFinancialCompany == 1){
                        $listItemIDFinancial[] = $insert['ItemID'];
                    }
                    if($insert['CompanyID'] != $company->CompanyID && $insert['LevelCompany'] == 1  && $company->IsFinancialCompany == 1){
                        $listProjectNotFinancial[] = $insert;
                    }
                }
            }

            if($company->IsFinancialCompany == 1){
                foreach($listProjectNotFinancial as $item){
                    if (!in_array($item['ItemID'], $listItemIDFinancial)) {
                        $itemNo = explode('.',$item['ItemNo']);
                        $item['ItemNo'] = $company->CompanyNo. '.' . $itemNo[1];
                        $item['CompanyID'] = $company->CompanyID;
                        $item['LevelCompany'] = $company->Level;
                        $item['ParentCompanyID'] = $company->ParentID;
                        $item['Norder'] = 1;
                        $listProjectFinancial[] = $item;
                    }
                }
                $uniqueSbiItems = array_merge($uniqueSbiItems, $listProjectFinancial);
            }

            DB::table('table_tmp')->insert($uniqueSbiItems);
        }
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        return json_encode($json);
    }

    public function formatInsertArr($stt, $show, $itemID, $itemNo, $parentID, $level, $itemName, $fontWeight, $italic, $center,
                                    $normCateNo, $normCateValue, $normNo, $company, $norder = 2){
        return [
            'STT' => $stt,
            'Show'=> $show,
            'ItemID' => $itemID,
            'ItemNo'=> $itemNo,
            'ParentID' => $parentID,
            'Level' => $level,
            'ItemName' => $itemName,
            'FontWeight' => $fontWeight,
            'Italic' => $italic,
            'Center' => $center,
            'NormCateNo' => $normCateNo,
            'NormCateValue' => $normCateValue,
            'NormNo' => $normNo,
            'I1' => 0,
            'I2' => 0,
            'I3' => 0,
            'I4' => 0,
            'CompanyID' => $company->CompanyID,
            'ParentCompanyID'=> $company->ParentID,
            'LevelCompany'=> $company->Level,
            'Detail'=> $company->Detail,
            'Norder'=> $norder,
        ];
    }

    public function insertI12($whereCondition,$preYear,$year,$nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT NormCateValue,TranTypeID,
                                MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear  THEN I END) AS I1,
                                MAX(CASE WHEN AccountNo = '0441' AND YearD = $year AND TranTypeID = '6' THEN I END) AS I2,
                                MAX(CASE WHEN AccountNo = '0721' AND YearD = $year AND TranTypeID = '8' THEN I END) AS I3,
                                MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear AND TranTypeID = '2' THEN I END) AS I4
                              FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo ,
                                       NC.CateValue NormCateValue,YEAR(A.PeriodFromDate) AS YearD,A.TransTypeID as TranTypeID
                               FROM act_gl_books AS A
                                        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                        INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                               WHERE NC.CateNo ='20214' AND (NC.CateValue= '1' OR NC.CateValue= '2')
                                 AND A.AccountNo IN ('0461', '0441', '0721','0411') AND A.TransTypeID IN ('2','6','8')
                                $whereCondition
                                  AND SC.CateNo = '003' AND SC.CateValue = '12'
                               GROUP BY YearD, NormCateValue,A.AccountNo,A.TransTypeID) AS B
                         GROUP BY  NormCateValue ) AS HH
                        SET TT.I1= HH.I1,  TT.I2= HH.I2,  TT.I3= HH.I3,TT.I4= HH.I4
                        WHERE TT.NormCateValue= HH.NormCateValue AND TT.ParentID = 'A#' ";
        DB::statement($sqlUpdate);
    }
    public function insertII1($whereCondition,$preYear,$year,$nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT NormCateNo,TranTypeID,
                                MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear THEN I END) AS I1,
                                MAX(CASE WHEN AccountNo = '0441' AND YearD = $year AND TranTypeID = '6' THEN I END) AS I2,
                                MAX(CASE WHEN AccountNo = '0721' AND YearD = $year AND TranTypeID = '8' THEN I END) AS I3,
                                MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear AND TranTypeID = '2' THEN I END) AS I4
                              FROM ( SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo ,
                                      NC.CateValue NormCateValue,YEAR(A.PeriodFromDate) AS YearD,NC.CateNo NormCateNo,A.TransTypeID as TranTypeID
                               FROM act_gl_books AS A
                                        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                        INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                                    WHERE NC.CateNo = '2041211' AND A.AccountNo IN ('0461', '0441', '0721','0411')
                                     AND SC.CateNo = '003' AND SC.CateValue = '12' AND A.TransTypeID IN ('2','6','8')
                                $whereCondition
                               GROUP BY YearD, NormCateValue,A.AccountNo,A.TransTypeID) AS B
                         GROUP BY NormCateNo ) AS HH
                        SET TT.I1= HH.I1,  TT.I2= HH.I2,  TT.I3= HH.I3,TT.I4= HH.I4
                        WHERE TT.ItemID = 'B#01' ";
        DB::statement($sqlUpdate);
    }

    public function insertII23($whereCondition,$preYear,$year,$nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT  NormCateNo,TranTypeID,
                                MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear THEN I END) AS I1,
                                MAX(CASE WHEN AccountNo = '0441' AND YearD = $year AND TranTypeID = '6' THEN I END) AS I2,
                                MAX(CASE WHEN AccountNo = '0721' AND YearD = $year AND TranTypeID = '8' THEN I END) AS I3,
                                MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear AND TranTypeID = '2' THEN I END) AS I4
                              FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo ,
                                      NC.CateNo NormCateNo,YEAR(A.PeriodFromDate) AS YearD,A.TransTypeID as TranTypeID
                               FROM act_gl_books AS A
                                        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                        INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                                    WHERE (NC.CateNo = '2041112' OR NC.CateNo = '2041113') AND NC.CateValue= '1'
                                     AND A.AccountNo IN ('0461', '0441', '0721','0411')
                                     AND SC.CateNo = '003' AND SC.CateValue = '12' AND A.TransTypeID IN ('2','6','8')
                                $whereCondition
                               GROUP BY YearD,NormCateNo,A.AccountNo,A.TransTypeID) AS B
                         GROUP BY NormCateNo) AS HH
                        SET TT.I1= HH.I1,  TT.I2= HH.I2,  TT.I3= HH.I3,TT.I4= HH.I4
                        WHERE TT.NormCateNo= HH.NormCateNo AND TT.ParentID = 'B#' AND TT.Level = '2'";
        DB::statement($sqlUpdate);
    }
    public function insertII3CT($whereCondition,$preYear,$year,$nextYear)
    {
        $sqlUpdate = "UPDATE table_tmp AS TT, (SELECT NormNo,ItemName,TranTypeID,
                                MAX(CASE WHEN AccountNo = '0461' AND YearD = $preYear THEN I END) AS I1,
                                MAX(CASE WHEN AccountNo = '0441' AND YearD = $year AND TranTypeID = '6' THEN I END) AS I2,
                                MAX(CASE WHEN AccountNo = '0721' AND YearD = $year AND TranTypeID = '8' THEN I END) AS I3,
                                MAX(CASE WHEN AccountNo = '0411' AND YearD = $nextYear AND TranTypeID = '2' THEN I END) AS I4
                              FROM (SELECT SUM(FCDebitAmount) AS I, A.AccountNo as AccountNo ,
                                      YEAR(A.PeriodFromDate) AS YearD,NRM.NormName ItemName,NRM.NormNo,A.TransTypeID as TranTypeID
                               FROM act_gl_books AS A
                                        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                        INNER JOIN norm AS NRM ON NRM.NormID = NC.NormID
                                        INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                                    WHERE NC.CateNo = '2041113' AND NC.CateValue= '1' AND LEFT(NRM.NormNo,7) = '5111112'
                                     AND A.AccountNo IN ('0461', '0441', '0721','0411')
                                     AND SC.CateNo = '003' AND SC.CateValue = '12' AND A.TransTypeID IN ('2','6','8')
                                $whereCondition
                               GROUP BY YearD,NRM.NormID, A.AccountNo,A.TransTypeID) AS B
                         GROUP BY ItemName ) AS HH
                        SET TT.I1= HH.I1,  TT.I2= HH.I2,  TT.I3= HH.I3,TT.I4= HH.I4
                        WHERE  TT.ParentID = 'B#05' AND TT.Level = '4' AND TT.NormNo = HH.NormNo ";
        DB::statement($sqlUpdate);
    }

    public function selectI12($whereCondition){
        $sql = "SELECT PathCompanyID
                               FROM act_gl_books AS A
                                        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                        INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                               WHERE NC.CateNo ='20214' AND (NC.CateValue= '1' OR NC.CateValue= '2')
                                 AND A.AccountNo IN ('0461', '0441', '0721','0411') AND A.InTransTypeID IN (2,4,6)
                                $whereCondition
                                  AND SC.CateNo = '003' AND SC.CateValue = '12'
                               GROUP BY PathCompanyID";
        return DB::select($sql);
    }

    public function selectII1($whereCondition){
        $sql = "SELECT PathCompanyID
                               FROM act_gl_books AS A
                                        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                        INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                                    WHERE NC.CateNo = '2041211' AND A.AccountNo IN ('0461', '0441', '0721','0411')
                                    AND A.InTransTypeID IN (2,4,6) AND SC.CateNo = '003' AND SC.CateValue = '12'
                                $whereCondition
                               GROUP BY PathCompanyID";
        return DB::select($sql);
    }

    public function selectII23($whereCondition){
        $sql = "SELECT PathCompanyID
                               FROM act_gl_books AS A
                                        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                        INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                                    WHERE (NC.CateNo = '2041112' OR NC.CateNo = '2041113') AND NC.CateValue= '1'
                                     AND A.AccountNo IN ('0461', '0441', '0721','0411')  AND A.InTransTypeID IN (2,4,6)
                                     AND SC.CateNo = '003' AND SC.CateValue = '12'
                                $whereCondition
                               GROUP BY PathCompanyID";
        return DB::select($sql);
    }

    public function selectII3CT($whereCondition){
        $sql = "SELECT PathCompanyID
                               FROM act_gl_books AS A
                                        INNER JOIN norm_cate AS NC ON NC.NormID = A.NormID
                                        INNER JOIN norm AS NRM ON NRM.NormID = NC.NormID
                                        INNER JOIN sector_cate AS SC ON SC.SectorID = A.SectorID
                                    WHERE NC.CateNo = '2041113' AND NC.CateValue= '1' AND LEFT(NRM.NormNo,7) = '5111112'
                                     AND A.AccountNo IN ('0461', '0441', '0721','0411')  AND A.InTransTypeID IN (2,4,6)
                                     AND SC.CateNo = '003' AND SC.CateValue = '12'
                                $whereCondition
                               GROUP BY PathCompanyID";
        return DB::select($sql);
    }

    public function sumUpTableTmp($start, $maxLevel){
        for ($i = $maxLevel->maxLevel; $i > $start; $i--) {
            $j = $i - 1;
            $sql = "UPDATE table_tmp AS TT, (SELECT CompanyID,ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
            FROM t M
            WHERE M.Level = $i GROUP BY CompanyID,ParentID) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = HH.ParentID AND TT.Detail = 1
            AND TT.Level = " . $j . " AND TT.CompanyID = HH.CompanyID" ;
            DB::statement($sql);

            $sql1 = "UPDATE t AS TT, (SELECT CompanyID,ItemID, ParentID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
            FROM table_tmp M
            WHERE M.Level = $i GROUP BY CompanyID,ParentID) AS HH
            SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
            WHERE TT.ItemID = HH.ParentID AND TT.Detail = 1
            AND TT.Level = " . $j . " AND TT.CompanyID = HH.CompanyID" ;
            DB::statement($sql1);
        }
    }

    public function sumParent($maxLevelCompany,$company){
        for ($i = $maxLevelCompany->maxLevel; $i > $company->Level; $i--){
            $j = $i - 1;
            $sql = "UPDATE table_tmp as TT, (SELECT ParentCompanyID ,ItemID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
                FROM t M
                WHERE M.LevelCompany =  $i GROUP BY ParentCompanyID, ItemID) as HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                    WHERE TT.ItemID = HH.ItemID
                    AND TT.LevelCompany = $j AND TT.CompanyID = HH.ParentCompanyID";
            DB::statement($sql);
            $sql1 = "UPDATE t as TT, (SELECT ParentCompanyID ,ItemID, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4
                FROM table_tmp M
                WHERE M.LevelCompany =  $i GROUP BY ParentCompanyID, ItemID) as HH
                    SET TT.I1 = HH.I1, TT.I2 = HH.I2, TT.I3 = HH.I3, TT.I4 = HH.I4
                    WHERE TT.ItemID = HH.ItemID
                    AND TT.LevelCompany = $j AND TT.CompanyID = HH.ParentCompanyID";
            DB::statement($sql1);
        }
    }

    public function sumFinalcialCompany($company){
        $sql = "UPDATE table_tmp as TT,
        (SELECT ItemID, SUM(I1) as I1, SUM(I2) as I2,SUM(I3) as I3, SUM(I4) as I4
        FROM t WHERE CompanyID != $company->CompanyID AND LevelCompany = $company->Level GROUP BY ItemID) as HH
        SET TT.I1 = TT.I1 + HH.I1, TT.I2 = TT.I2 + HH.I2, TT.I3 = TT.I3 + HH.I3, TT.I4 = TT.I4 + HH.I4
        WHERE TT.ItemID = HH.ItemID
        AND TT.LevelCompany = $company->Level AND TT.CompanyID = " .$company->CompanyID;
        DB::statement($sql);
    }


}
