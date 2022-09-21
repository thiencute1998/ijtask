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

class RCETT252017BCTCSocaiController extends Controller {
    public function RCETT252017BCTCSocai(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $companyID = (isset($filter['CompanyID'])) ? $filter['CompanyID'] : null;
        $conAccountNo = (isset($filter['AccountNo'])) ? $filter['AccountNo'] : null;
        if (!isset($filter['DateRange']) || !$filter['DateRange'] || !$companyID  || !$conAccountNo) {
            $json['status'] = 0;
            $json['msg'] = 'Chưa chọn kỳ , đơn vị và tài khoản';
            return json_encode($json);
        }
        $companyID = $filter['CompanyID'];
        $conAccountNo = $filter['AccountNo'];



        $date = date('d');
        $month = date('m');
        $year = date('Y');
        $coaCon = CoaCon::where('AccountNo', $conAccountNo)->first();
        $coaConBlanceType = $coaCon->BalanceType;
        $glBooks = [];

        $fromDate = Date::StringToDate($filter['DateRange']['fromDate']);
        $toDate = Date::StringToDate($filter['DateRange']['toDate']);
        $arr = explode ( '/' , $filter['DateRange']['toDate'] );
        $date1 = $arr[2].'/01/01';
        $date1= Date::StringToDate($date1);
        // Số dư đầu năm


        $sumSql = AccountingBook::where('AccountNo',$conAccountNo)
            ->where('CompanyID', $companyID)
            ->where('isAdjustTrans', 0)
            ->where(DB::raw('DATE_FORMAT(act_gl_books.PostDate, "%Y-%m-%d")'), '<', $fromDate);

        $sumFCDebitAmount = $sumSql->sum('FCDebitAmount');
        $sumFCCreditAmount = $sumSql->sum('FCCreditAmount');
        $sumOpeningBalance = $sumFCDebitAmount - $sumFCCreditAmount;

        if($coaConBlanceType == 1){
            $FCD = $sumOpeningBalance;
            $FCC = '';
        }
        if($coaConBlanceType == 2){
            $FCD = '';
            $FCC = -$sumOpeningBalance;
        }
        if($coaConBlanceType == 3){
            if($sumOpeningBalance > 0){
                $FCD = $sumOpeningBalance;
                $FCC = '';
            }
            if($sumOpeningBalance < 0){
                $FCD = '';
                $FCC = -$sumOpeningBalance;
            }
        }
        if($sumOpeningBalance == 0){
            $FCD = '';
            $FCC = '';
        }
        $glBook = [
            "PostDate"=> "",
            "TransNo" => "",
            "TransDate"=> "",
            "Description"=> "Số dư đầu năm",
            "AccountNo"=> "",
            "FCDebitAmount"=> $FCD,
            "FCCreditAmount"=> $FCC
        ];
        $glBooks[]=$glBook;

        // Điều chỉnh số dư đầu năm

        $VouchersAdjustedSql = AccountingBook::where('AccountNo',$conAccountNo)
            ->where('CompanyID', $companyID)
            ->where('isAdjustTrans',1)
            ->where(DB::raw('DATE_FORMAT(act_gl_books.PostDate, "%Y-%m-%d")'), '<', $date1);
        $VouchersAdjusted = $VouchersAdjustedSql->sum('FCDebitAmount') - $VouchersAdjustedSql->sum('FCCreditAmount');
        if($VouchersAdjusted != 0){
            $FCD = $VouchersAdjustedSql->sum('FCDebitAmount');
            $FCC = $VouchersAdjustedSql->sum('FCCreditAmount');
        }
       $glBook = [
           "PostDate"=> "",
           "TransNo" => "",
           "TransDate"=> "",
           "Description"=> "Điều chỉnh số dư đầu năm",
           "AccountNo"=> "",
           "FCDebitAmount"=> $FCD,
           "FCCreditAmount"=> $FCC
       ];
        $glBooks[]=$glBook;



        $arr = [
            "PostDate"=> "",
            "TransNo" => "",
            "TransDate"=> "",
            "Description"=> "Số phát sinh ",
            "AccountNo"=> "",
            "FCDebitAmount"=> "",
            "FCCreditAmount"=> ""
        ];
        $glBooks[] = $arr;

        $glBookListQuery = AccountingBook::query();

        $glBookListQuery->select(DB::raw('DATE_FORMAT(PostDate, "%d/%m/%Y") AS PostDate'), 'TransNo', DB::raw('DATE_FORMAT(TransDate, "%d/%m/%Y") AS TransDate'), 'Description', 'AccountNo', 'FCDebitAmount', 'FCCreditAmount');
        if(isset($filter['DateRange']['fromDate']) && $filter['DateRange']['fromDate'] !== ''){
            $fromDate = Date::StringToDate($filter['DateRange']['fromDate']);
            $glBookListQuery->where(DB::raw('DATE_FORMAT(act_gl_books.PostDate, "%Y-%m-%d")'), '>=', $fromDate);
        }
        if(isset($filter['DateRange']['toDate']) && $filter['DateRange']['toDate'] !== ''){
            $toDate = Date::StringToDate($filter['DateRange']['toDate']);
            $glBookListQuery->where(DB::raw('DATE_FORMAT(act_gl_books.PostDate, "%Y-%m-%d")'), '<=', $toDate);
        }
        if ($companyID) {
            $glBookListQuery->where('act_gl_books.CompanyID', '=', $companyID);
        }
        if ($conAccountNo) {
            $glBookListQuery->where('act_gl_books.AccountNo', '=', $conAccountNo);
        }

        Log::debug($glBookListQuery->toSql());

        $glBookList = $glBookListQuery->get();
        foreach ($glBookList as $glBook){
            if($glBook->FCDebitAmount == 0)  $glBook->FCDebitAmount = '';
            if($glBook->FCCreditAmount == 0) $glBook->FCCreditAmount = '';
            $glBooks[] = $glBook;
        }

        $sumSql = AccountingBook::where('AccountNo',$conAccountNo)
            ->where(DB::raw('DATE_FORMAT(act_gl_books.PostDate, "%Y-%m-%d")'), '>=', $fromDate)
            ->where(DB::raw('DATE_FORMAT(act_gl_books.PostDate, "%Y-%m-%d")'), '<=', $toDate);
        $sumFCDebitAmount = $sumSql->sum('FCDebitAmount');
        $sumFCCreditAmount =$sumSql->sum('FCCreditAmount');
        if($sumFCDebitAmount == 0) $sumFCDebitAmount = '';
        if($sumFCCreditAmount == 0) $sumFCCreditAmount = '';
        $glBook = [
            "PostDate"=> "",
            "TransNo" => "",
            "TransDate"=> "",
            "Description"=> "Cộng phát sinh",
            "AccountNo"=> "",
            "FCDebitAmount"=> $sumFCDebitAmount,
            "FCCreditAmount"=>  $sumFCCreditAmount
        ];
        $glBooks[] = $glBook;
        $sumSql = AccountingBook::where('AccountNo',$conAccountNo)
                ->where(DB::raw('DATE_FORMAT(act_gl_books.PostDate, "%Y-%m-%d")'), '<', $toDate);

        $sumFCDebitAmount = $sumSql->sum('FCDebitAmount');
        $sumFCCreditAmount =$sumSql->sum('FCCreditAmount');
        $sumClosingBalance = $sumFCDebitAmount - $sumFCCreditAmount;

        if($coaConBlanceType == 1){
            $FCD = $sumClosingBalance;
            $FCC = '';
        }
        if($coaConBlanceType == 2){
            $FCD = '';
            $FCC = -$sumClosingBalance;
        }
        if($coaConBlanceType == 3){
            if($sumClosingBalance > 0){
                $FCD = $sumClosingBalance;
                $FCC = '';
            }
            if($sumClosingBalance < 0){
                $FCD = '';
                $FCC = -$sumClosingBalance;
            }
        }
        if($sumClosingBalance == 0){
            $FCD = '';
            $FCC = '';
        }
        $glBook = [
            "PostDate"=> "",
            "TransNo" => "",
            "TransDate"=> "",
            "Description"=> "Số dư cuối kỳ",
            "AccountNo"=> "",
            "FCDebitAmount"=> $FCD,
            "FCCreditAmount"=> $FCC
        ];
        $glBooks[]=$glBook;
        $glBooks1 =[];
        foreach ($glBooks as $gl){
            $arr = [
                "PostDate"=> $gl['PostDate'],
                "TransNo" => $gl['TransNo'],
                "TransDate"=> $gl['TransDate'],
                "Description"=> $gl['Description'],
                "AccountNo"=> $gl['AccountNo'],
                "FCDebitAmount"=> ($gl['FCDebitAmount']) ? $gl['FCDebitAmount'] / 1000000 : '',
                "FCCreditAmount"=> ($gl['FCCreditAmount']) ? $gl['FCCreditAmount']/ 1000000 : ''
            ];
            $glBooks1[] = $arr;
        }

        $response = [
            'master' => [
                "AccountName" => 'Tài khoản: ' . $filter['AccountNo'] . ' - ' . $filter['AccountName'],
                "DateRange" => 'Từ ngày: '.$filter['DateRange']['fromDate'] . ' đến ngày: '. $filter['DateRange']['toDate'],
                "CreateName" => "Trần Thế Thái",
                "TransNo" => "00001",
                "ReportName" => "DỰ TOÁN THU, CHI NGÂN SÁCH NHÀ NƯỚC NĂM ",
                "DateTime" => "Vĩnh Phú, ngày $date tháng $month năm $year",
                "CompanyName" => 'UBND tỉnh Vĩnh Phú',
                "Note" => ''
            ],
            'detail' => $glBooks1
        ];
        Log::debug($response);

        $json['data'] = $response;
        return json_encode($json);
    }

   }
