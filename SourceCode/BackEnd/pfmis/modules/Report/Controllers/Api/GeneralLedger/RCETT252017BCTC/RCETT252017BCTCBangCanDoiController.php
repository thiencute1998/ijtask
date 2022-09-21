<?php
namespace Module\Report\Controllers\GeneralLedger\RCETT252017BCTC;

use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Accounting\Models\AccountingBook;
use Module\Listing\Models\CoaCon;
use Module\Listing\Models\Company;
use Module\Listing\Models\Employee;
use Module\Report\Controllers\Controller;

class RCETT252017BCTCBangCanDoiController extends Controller {
    public function RCETT252017BCTCBangCanDoi(Request $request)
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $companyID = (isset($filter['CompanyID'])) ? $filter['CompanyID'] : null;
        if (!isset($filter['DateRange']) || !$filter['DateRange'] || !$companyID ) {
            $json['status'] = 0;
            $json['msg'] = 'Chưa chọn kỳ và đơn vị ';
            return json_encode($json);
        }
        $companyID = $filter['CompanyID'];
        $companyName = $filter['CompanyName'];

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `AccountNo` varchar(11) DEFAULT '',
          `AccountName` varchar (200) DEFAULT '',
          `ParentNo` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `BalanceType` int(2) DEFAULT 1,
          `Detail` int(2) DEFAULT 1,
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
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $maxLevel = DB::table('coa_con')->max('Level');
        $books = DB::table('act_gl_books')->select('AccountNo')
                                                ->where('AccountNo','!=','')
                                                ->where('CompanyID', $companyID)
                                                ->where('isAdjustTrans', 0)
                                                ->groupBy('AccountNo')->get()->toArray();
        $arrAccount = [];
        $data = [];
        $listCoaCon = [];
        for($i = 1; $i<= $maxLevel ; $i++){
            foreach($books as $book){
                $arrAccount[$i][] = substr($book->AccountNo,0,$i + 2);
            }
            $data[$i] = DB::table('coa_con')->whereIn(DB::raw('AccountNo'), array_unique($arrAccount[$i]))->where(DB::raw('Level'), $i)->groupBy('AccountNo')->get();
            if($data[$i]){
                foreach ($data[$i] as $template) {
                    $listCoaCon[] = [
                        'AccountNo' => $template->AccountNo,
                        'Level'=> $template->Level,
                        'ParentNo'=> $template->ParentNo,
                        'AccountName'=> $template->AccountName,
                        'Detail'=> $template->Detail,
                        'BalanceType'=> $template->BalanceType,
                    ];
                }
            }
        }
        $fromDate = Date::StringToDate($filter['DateRange']['fromDate']);
        $fromDate1 =  date("d-m-Y", strtotime($fromDate));
        $toDate = Date::StringToDate($filter['DateRange']['toDate']);
        $toDate1 =  date("d-m-Y", strtotime($toDate));
        $arr = explode ( '/' , $filter['DateRange']['toDate'] );
        $date1 = $arr[2].'/01/01';
        $date1= Date::StringToDate($date1);
        $date = date('d');
        $month = date('m');
        $year = date('Y');
        DB::table('table_tmp')->insert($listCoaCon);

        foreach ($books as $coaCon){
            $conAccountNo = $coaCon->AccountNo;

            // Số dư đầu kỳ
            $coaCon = CoaCon::where('AccountNo', $conAccountNo)->first();
            $sumSql = AccountingBook::where('AccountNo',$conAccountNo)
                ->where('CompanyID', $companyID)
                ->where('isAdjustTrans', 0)
                ->where(DB::raw('DATE_FORMAT(act_gl_books.PostDate, "%Y-%m-%d")'), '<', $fromDate);
            $sumFCDebitAmount = $sumSql->sum('FCDebitAmount');
            $sumFCCreditAmount = $sumSql->sum('FCCreditAmount');
            $sumOpeningBalance = $sumFCDebitAmount - $sumFCCreditAmount;
            $this->insert($coaCon, $sumOpeningBalance, 'I1', 'I2' );
            // Điều chỉnh số dư đầu năm
            $VouchersAdjustedSql = AccountingBook::where('AccountNo',$conAccountNo)
                ->where('CompanyID', $companyID)
                ->where('isAdjustTrans',1)
                ->where(DB::raw('DATE_FORMAT(act_gl_books.PostDate, "%Y-%m-%d")'), '<', $date1);
            $VouchersAdjusted = $VouchersAdjustedSql->sum('FCDebitAmount') - $VouchersAdjustedSql->sum('FCCreditAmount');
            $this->insert($coaCon, $VouchersAdjusted, 'I3', 'I4');
            // Số phát sinh trong tháng
            $AccruedBalanceSql = AccountingBook::where('AccountNo',$conAccountNo)
                ->where('CompanyID', $companyID)
                ->where('isAdjustTrans', 0)
                ->where(DB::raw('DATE_FORMAT(act_gl_books.PostDate, "%Y-%m-%d")'), '>=', $fromDate)
                ->where(DB::raw('DATE_FORMAT(act_gl_books.PostDate, "%Y-%m-%d")'), '<=', $toDate);
            $sumAccruedBalance = $AccruedBalanceSql->sum('FCDebitAmount') - $AccruedBalanceSql->sum('FCCreditAmount');
            $this->insert($coaCon, $sumAccruedBalance, 'I5', 'I6');
            // Số dư cuối kỳ
            $ClosingBalanceSql = AccountingBook::where('AccountNo',$conAccountNo)
                                    ->where('CompanyID', $companyID);
            $sumClosingBalance = $ClosingBalanceSql->sum('FCDebitAmount') - $ClosingBalanceSql->sum('FCCreditAmount');
            $this->insert($coaCon, $sumClosingBalance, 'I7', 'I8');
        }
        $this->sumUpTableTmp(1, 4);
        $data = [];
        $glbooks =DB::table('table_tmp')->get();
        foreach ($glbooks as $glbook){
            if($glbook->I1 == 0) $glbook->I1 = '';
            if($glbook->I2 == 0) $glbook->I2 = '';
            if($glbook->I3 == 0) $glbook->I3 = '';
            if($glbook->I4 == 0) $glbook->I4 = '';
            if($glbook->I5 == 0) $glbook->I5 = '';
            if($glbook->I6 == 0) $glbook->I6 = '';
            if($glbook->I7 == 0) $glbook->I7 = '';
            if($glbook->I8 == 0) $glbook->I8 = '';
            $str = "";
            if($glbook->Level > 1) {
                for ( $i = 1; $i < $glbook->Level; $i++ ){
                    $str = $str . "-";
                }
            }
            $str .= ' ';
            if($glbook->Level ==1){
                $glbook->FontWeight = 1;
            }

            $arrGlBook = [
                "TableID" =>  $glbook->TableID,
                "AccountNo" => $glbook->AccountNo,
                "ParentNo" => $glbook->ParentNo,
                "Level" => $glbook->Level,
                "AccountName" =>$glbook->AccountName,
                "FontWeight" => $glbook->FontWeight,
                "Italic" => $glbook->Italic,
                "Center" => $glbook->Center,
                "I1" => $glbook->I1,
                "I2" => $glbook->I2,
                "I3" => $glbook->I3,
                "I4" => $glbook->I4,
                "I5" => $glbook->I5,
                "I6" => $glbook->I6,
                "I7" => $glbook->I7,
                "I8" => $glbook->I8,
                "Link" => '#/report/RCE_TT-25-2017-BCTC_Socai?'.'fromDate='.$fromDate1.'&toDate='.$toDate1.'&companyID='.$companyID.'&conAccountNo='.$glbook->AccountNo
            ];
            $data[] = $arrGlBook;
        }
        $response = [
            'master' => [
                "DateRange" => 'Từ ngày: '.$filter['DateRange']['fromDate'] . ' đến ngày: '. $filter['DateRange']['toDate'],
                "CreateName" => "Trần Thế Thái",
                "TransNo" => "00001",
                "ReportName" => "DỰ TOÁN THU, CHI NGÂN SÁCH NHÀ NƯỚC NĂM ",
                "DateTime" =>  " Ngày $date tháng $month năm $year",
                "CompanyName" =>  $filter['CompanyName'],
                "MasterAccount" => "Nguyen Van An",
                "Note" => '',

            ],
            'detail' => $data
        ];
        $json['data'] = $response;
        return json_encode($json);
    }
    public  function  insert( $coaCon, $sumBalance, $I1, $I2){
        $i1 = '';
        $i2 = '';
        if($coaCon->BalanceType == 1){
            $i1 = $sumBalance;
            $i2 = '';
        }
        if($coaCon->BalanceType == 2){
            $i1 = '';
            $i2 = -$sumBalance;
        }
        if($coaCon->BalanceType == 3){
            if($sumBalance > 0){
                $i1 = $sumBalance;
                $i2 = '';
            } elseif ($sumBalance < 0 ){
                $i1 = '';
                $i2 = -$sumBalance;
            } else {
                $i1 = '';
                $i2 = '';
            }
        }
        DB::table('table_tmp')->where('AccountNo', $coaCon->AccountNo)
                                        ->update([$I1 => $i1, $I2 => $i2 ]);
    }
    public function sumUpTableTmp($start, $end = null){
        $maxLevel = DB::table('table_tmp')->select(DB::raw('MAX(table_tmp.Level) AS maxLevel'))->first();
        if ($maxLevel) {
            $end = $maxLevel->maxLevel;
        }
        if ($end) {
            for ($i = $end; $i > $start; $i--) {
                $j = $i - 1;
                $sql = "SELECT AccountNo, ParentNo, SUM(M.I1) AS I1, SUM(M.I2) AS I2, SUM(M.I3) AS I3, SUM(M.I4) AS I4, SUM(M.I5) AS I5, SUM(M.I6) AS I6, SUM(M.I7) AS I7, SUM(M.I8) AS I8
                FROM table_tmp M
                WHERE M.Level = " . $i . " GROUP BY ParentNo";
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
                    SET I1 = " . $I1 . ", I2 = " . $I2 . ", I3 = " . $I3 . ", I4 = " . $I4 . ", I5 = " . $I5 . ", I6 = " . $I6 . ", I6 = " . $I6 . ", I7 = " . $I7 . ", I8 = " . $I8 . "
                    WHERE AccountNo = '" . $value['ParentNo'] . "'
                    AND table_tmp.Level = " . $j;
                        DB::statement($update);
                    }
                }
            }
        }
    }
}
