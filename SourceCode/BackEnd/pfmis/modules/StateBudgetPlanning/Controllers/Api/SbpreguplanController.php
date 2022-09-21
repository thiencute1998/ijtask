<?php
namespace Module\StateBudgetPlanning\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use IjCore\IjDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaType;
use Module\Listing\Models\NormTable;
use Module\Listing\Models\NormTableItem;
use Module\Listing\Models\SysAutoNumber;
use Module\Sbpmakeplan\Models\Actautoact;
use Module\Sbpgiveplan\Models\Sbpgiveplan;
use Module\Sbpmakeplan\Models\SbpmakeplanTrans;
use Module\Sbpmakeplan\Models\SbpmakeplanBook;
use Module\Sbpreviewplan\Models\Sbpreviewplan;
use Module\StateBudgetPlanning\Models\Sysperiod;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysStatusItem;
use Module\SysAdmin\Models\SysSetup;
use function Psy\debug;

class SbpreguplanController extends Controller{

    public function list(Request $request){
        $data = Sbpgiveplan::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('act_gvouc_trans', ['select' => ['TransNo', 'TransDate'], 'conditions' => []]), 'Sbpgiveplan.xlsx');
    }

    public function create(){
        if(old('TransNo')){
            $auto = old('TransNo');
        }else{
            $auto = AutoNo::genNewNumberNoTable('act_gvouc_trans', 'TransID', 'TransNo');
        }
        $ArrCoatype = CoaType::where('Inactive', '=', 0)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        $json['data']['ArrCoatype'] = $ArrCoatype;
        return json_encode($json);
    }
    public function createTrans(Request $request){
        $master = (object)$request->master;
        $detail = $request->detail;
        $CoaTypeName = "";
        if($master->CoaTypeID){
            $CoaType = CoaType::find($master->CoaTypeID);
            if(isset($CoaType->CoaTypeName)){
                $CoaTypeName = $CoaType->CoaTypeName;
            }
        }
        $StatusDescription = "";
        if ($master->StatusID && $master->StatusValue) {
            $SysStatusItem = SysStatusItem::where([
                'StatusID' => $master->StatusID,
                'StatusValue' => $master->StatusValue,
            ])->first();
            $StatusDescription = $SysStatusItem->StatusDescription;
        }
        if($master->CreateTrans ==2){
            $AutoactID = 34;
            $TransTypeID = 3;
        }elseif ($master->CreateTrans ==3){
            $AutoactID = 35;
            $TransTypeID = 4;
        }else{ $AutoactID = 33; $TransTypeID = 2;}
        //Kiểm tra đã chọn khoản thu chưa +
        if($master->RevenueNo){
        $itemp = new Sbpgiveplan([
            'TransDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->TransDate))),
            'PostDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PostDate))),
            'eTransDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->eTransDate))),
            'TransNo' => $master->TransNo,
            'TransTypeID' => $TransTypeID,
            'eTransNo' => $master->eTransNo,
            'TransTypeName' => $master->TransTypeName,
            'CoaTypeID' => $master->CoaTypeID,
            'CoaTypeNo' => $master->CoaTypeNo,
            'CoaTypeName' => $CoaTypeName,
            'StatusID' => $master->StatusID,
            'StatusValue' => $master->StatusValue,
            'StatusDescription' => $StatusDescription,
            'PeriodID' => $master->PeriodID,
            'PeriodType' => $master->PeriodType,
            'PeriodName' => $master->PeriodName,
            'PeriodFromDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PeriodFromDate))),
            'PeriodToDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PeriodToDate))),
            'LCTotalAmount' => $master->TotalAllocAmount,
            'Comment' => $master->Comment,
            'InputCompanyID' => Auth::user()->CompanyID,
            'InputCompanyNo' => Auth::user()->CompanyNo,
            'InputCompanyName' => Auth::user()->CompanyName,
        ]);
        $itemp->save();

        $arrInsert = [];
        $TransID = $itemp->TransID;
        foreach ($detail as $item) {
            $item = (object)$item;
            if (!empty($item->ReguRate)) {
                $arrInsert[] = [
                    'TransID' => $TransID,
                    'AutoactID' => $AutoactID,
                    'AccountID' => isset($item->AccountID) ? $item->AccountID : '',
                    'AccountNo' => isset($item->AccountNo) ? $item->AccountNo : '',
                    'CoAccountID' => isset($item->CoAccountID) ? $item->CoAccountID : '',
                    'CoAccountNo' => isset($item->CoAccountNo) ? $item->CoAccountNo : '',
                    'CcyID' => isset($master->CcyID) ? $master->CcyID : '',
                    'CcyNo' => isset($master->CcyNo) ? $master->CcyNo : '',
                    'CcyName' => isset($master->CcyName) ? $master->CcyName : '',
                    'ExchangeRate' => isset($master->ExchangeRate) ? $master->ExchangeRate : '1',
                    'FCPlanAmount' => isset($item->FCPlanAmount) ? $item->FCPlanAmount : '0',
                    'LCPlanAmount' => isset($item->LCPlanAmount) ? $item->LCPlanAmount : '0',
                    'FCDebitAmount' => isset($item->FCAllocAmount) ? $item->FCAllocAmount : '0',
                    'LCDebitAmount' => isset($item->LCAllocAmount) ? $item->LCAllocAmount : '0',
                    'FCAmount' => isset($item->FCAllocAmount) ? $item->FCAllocAmount : '0',
                    'LCAmount' => isset($item->LCAllocAmount) ? $item->LCAllocAmount : '0',
                    'RevenueID' => isset($master->RevenueID) ? $master->RevenueID : '',
                    'RevenueNo' => isset($master->RevenueNo) ? $master->RevenueNo : '',
                    'RevenueName' => isset($master->RevenueName) ? $master->RevenueName : '',
                    'NormTableItemID' => isset($item->NormTableItemID) ? $item->NormTableItemID : '',
                    'NormTableItemNo' => isset($item->NormTableItemNo) ? $item->NormTableItemNo : '',
                    'NormTableItemName' => isset($item->Description) ? $item->Description : '',
                    'FCNormAmount' => isset($item->FCNormAmount) ? $item->FCNormAmount : '0',
                    'LCNormAmount' => isset($item->LCNormAmount) ? $item->LCNormAmount : '0',
                    'NormID' => isset($item->NormID) ? $item->NormID : '',
                    'NormNo' => isset($item->NormNo) ? $item->NormNo : '',
                    'NormName' => isset($item->NormName) ? $item->NormName : '',
                    'TransTypeID' => $TransTypeID,
                    'BudgetLevel' => isset($item->BudgetLevel) ? $item->BudgetLevel : '',
                    'Quantity' => '1',
                    'FiscalPeriod' => '2',
                    'ReceiveBy' => '1',
                    'BudgetAllocTypeID' => '1',
                    'UomID' => '7',
                    'UomNo' => '401',
                    'UomName' => 'Đồng',
                ];
            }
        }

        if (!empty($arrInsert)) {
            DB::table('act_gvouc_trans_item')->insert($arrInsert);
        }
        //Ghi xổ
        $arrInsert1 = [];
        foreach ($detail as $item) {
            $item = (object)$item;
            if (!empty($item->ReguRate)) {
                if($item->Detail==1){
                $arrInsert1[] = [
                    'PostType' => '1',
                    'CoaTypeID' => $master->CoaTypeID,
                    'CoaTypeNo' => $master->CoaTypeNo,
                    'CoaTypeName' => $master->CoaTypeName,
                    'TransDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->TransDate))),
                    'PostDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PostDate))),
                    'eTransDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->eTransDate))),
                    'TransNo' => $master->TransNo,
                    'eTransNo' => $master->eTransNo,
                    'TransTypeName' => $master->TransTypeName,
                    'PeriodID' => $master->PeriodID,
                    'PeriodType' => $master->PeriodType,
                    'PeriodName' => $master->PeriodName,
                    'PeriodFromDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PeriodFromDate))),
                    'PeriodToDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PeriodToDate))),
                    'InputCompanyID' => Auth::user()->CompanyID,
                    'InputCompanyNo' => Auth::user()->CompanyNo,
                    'InputCompanyName' => Auth::user()->CompanyName,

                    'TransID' => $TransID,
                    'AccountID' => isset($item->AccountID) ? $item->AccountID : '',
                    'AccountNo' => isset($item->AccountNo) ? $item->AccountNo : '',
                    'CoAccountID' => isset($item->CoAccountID) ? $item->CoAccountID : '',
                    'CoAccountNo' => isset($item->CoAccountNo) ? $item->CoAccountNo : '',
                    'CcyID' => isset($master->CcyID) ? $master->CcyID : '',
                    'CcyNo' => isset($master->CcyNo) ? $master->CcyNo : '',
                    'CcyName' => isset($master->CcyName) ? $master->CcyName : '',
                    'ExchangeRate' => isset($master->ExchangeRate) ? $master->ExchangeRate : '1',
                    'FCPlanAmount' => isset($item->FCPlanAmount) ? $item->FCPlanAmount : '0',
                    'LCPlanAmount' => isset($item->LCPlanAmount) ? $item->LCPlanAmount : '0',
                    'FCDebitAmount' => isset($item->FCAllocAmount) ? $item->FCAllocAmount : '0',
                    'LCDebitAmount' => isset($item->LCAllocAmount) ? $item->LCAllocAmount : '0',
                    'FCAmount' => isset($item->FCAllocAmount) ? $item->FCAllocAmount : '0',
                    'LCAmount' => isset($item->LCAllocAmount) ? $item->LCAllocAmount : '0',
                    'RevenueID' => isset($master->RevenueID) ? $master->RevenueID : '',
                    'RevenueNo' => isset($master->RevenueNo) ? $master->RevenueNo : '',
                    'RevenueName' => isset($master->RevenueName) ? $master->RevenueName : '',
                    'NormTableItemID' => isset($item->NormTableItemID) ? $item->NormTableItemID : '',
                    'NormTableItemNo' => isset($item->NormTableItemNo) ? $item->NormTableItemNo : '',
                    'NormTableItemName' => isset($item->Description) ? $item->Description : '',
                    'FCNormAmount' => isset($item->FCNormAmount) ? $item->FCNormAmount : '0',
                    'LCNormAmount' => isset($item->LCNormAmount) ? $item->LCNormAmount : '0',
                    'NormID' => isset($item->NormID) ? $item->NormID : '',
                    'NormNo' => isset($item->NormNo) ? $item->NormNo : '',
                    'NormName' => isset($item->NormName) ? $item->NormName : '',
                    'TransTypeID' => $master->TransTypeID,
                    'BudgetLevel' => isset($item->BudgetLevel) ? $item->BudgetLevel : '',
                    'Quantity' => '1',
                    'FiscalPeriod' => '2',
                    'ReceiveBy' => '1',
                    'BudgetAllocTypeID' => '1',
                    'UomID' => '7',
                    'UomNo' => '401',
                    'UomName' => 'Đồng',
                ];
                }
            }
        }
        if (!empty($arrInsert1)) {
            DB::table('act_gl_books')->insert($arrInsert1);
            Sbpgiveplan::where(['TransID' => $TransID])->update(['Posted' => 1,'PostedDate' => $PostedDate, 'PostedUserID' => Auth::user()->UserID]);
        }

            $json['status'] = 1;
            $json['TransID'] = $TransID;
            $json['CreateTrans'] = $master->CreateTrans;
            $json['msg'] = 'Thành công!';
            $json['data'] = $itemp->TransID;
        }else{
            $json['status'] = 1;
            $json['checkRevenue'] = 1;
            $json['data'] = '';
        }//Check ! khoản thu

        return json_encode($json);
    }
    public function getlistregu(Request $request){
        $RevenueID = $request->post('RevenueID');
        $RevenueNo = $request->post('RevenueNo');
        $PeriodFromDate = date('Y-m-d', strtotime(str_replace('/', '-', $request->post('PeriodFromDate'))));
        //$RevenueRegu = NormTableItem::where(['RevenueNo' => $RevenueNo])->get();
        $RevenueRegu = DB::table('revenue_regu')->where('RevenueNo', '=', $RevenueNo)->where('EffectiveDate', '=', $PeriodFromDate)->get();;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['RevenueRegu'] = $RevenueRegu;
        return json_encode($json);
    }

    //Ghi sổ
    public function actbook(Request $request){
        $TransID = $request->post('TransID');
        $PostType = $request->post('PostType');
        $CoaTypeID = $request->post('CoaTypeID');
        $CoaTypeNo = $request->post('CoaTypeNo');
        $CoaTypeName = $request->post('CoaTypeName');
        $Posted = $request->post('Posted');
        $TransDate = $request->post('TransDate');
        $PostDate = $request->post('PostDate');
        $eTransDate = $request->post('eTransDate');
        $TransNo = $request->post('TransNo');
        $eTransNo = $request->post('eTransNo');
        $TransTypeID = $request->post('TransTypeID');
        $TransTypeName = $request->post('TransTypeName');
        $FCTotalAmount = $request->post('FCTotalAmount');
        $LCTotalAmount = $request->post('LCTotalAmount');
        $FCTotalTaxAmount = $request->post('FCTotalTaxAmount');
        $LCTotalTaxAmount = $request->post('LCTotalTaxAmount');
        $CompanyID = $request->post('CompanyID');
        $CompanyNo = $request->post('CompanyNo');
        $CompanyName = $request->post('CompanyName');
        $InputCompanyID = $request->post('InputCompanyID');
        $PeriodID = $request->post('PeriodID');
        $PeriodType = $request->post('PeriodType');
        $PeriodName = $request->post('PeriodName');
        $PeriodFromDate = $request->post('PeriodFromDate');
        $PeriodToDate = $request->post('PeriodToDate');
        $detail = $request->detail;
        $arrInsert = [];
        if($Posted==1){
            SbpmakeplanBook::where('TransID', $TransID)->delete();
            Sbpgiveplan::where(['TransID' => $TransID])->update(['Posted' => 0]);
        }else {
            $PostedDate = date('Y-m-d H:i', time());
            Sbpgiveplan::where(['TransID' => $TransID])->update(['Posted' => 1,'PostedDate' => $PostedDate, 'PostedUserID' => Auth::user()->UserID]);
            foreach ($detail as $item) {
                $item = (object)$item;
                if (!empty($PostType)) {
                    $arrInsert[] = [
                        'TransID' => $TransID,
                        'PostType' => $PostType,
                        'CoaTypeID' => $CoaTypeID,
                        'CoaTypeNo' => $CoaTypeNo,
                        'CoaTypeName' => $CoaTypeName,
                        'TransDate' => $TransDate,
                        'PostDate' => $PostDate,
                        'eTransDate' => $eTransDate,
                        'TransNo' => $TransNo,
                        'eTransNo' => $eTransNo,
                        'TransTypeID' => $TransTypeID,
                        'TransTypeName' => $TransTypeName,
                        'FCTotalAmount' => $FCTotalAmount,
                        'LCTotalAmount' => $LCTotalAmount,
                        'FCTotalTaxAmount' => $FCTotalTaxAmount,
                        'LCTotalTaxAmount' => $LCTotalTaxAmount,
                        'CompanyID' => $CompanyID,
                        'CompanyNo' => $CompanyNo,
                        'CompanyName' => $CompanyName,
                        'PeriodID' => $PeriodID,
                        'PeriodType' => $PeriodType,
                        'PeriodName' => $PeriodName,
                        'PeriodFromDate' => $PeriodFromDate,
                        'PeriodToDate' => $PeriodToDate,
                        'InputCompanyID' => $InputCompanyID,
                        'InputCompanyNo' => Auth::user()->CompanyNo,
                        'InputCompanyName' => Auth::user()->CompanyName,
                        'ConAccountID' => isset($item->ConAccountID) ? $item->ConAccountID : '',
                        'ConAccountNo' => isset($item->ConAccountNo) ? $item->ConAccountNo : '',
                        'ConCoAccountID' => isset($item->ConCoAccountID) ? $item->ConCoAccountID : '',
                        'ConCoAccountNo' => isset($item->ConCoAccountNo) ? $item->ConCoAccountNo : '',
                        'SnaAccountID' => isset($item->SnaAccountID) ? $item->SnaAccountID : '',
                        'SnaAccountNo' => isset($item->SnaAccountNo) ? $item->SnaAccountNo : '',
                        'SnaCoAccountID' => isset($item->SnaCoAccountID) ? $item->SnaCoAccountID : '',
                        'SnaCoAccountNo' => isset($item->SnaCoAccountNo) ? $item->SnaCoAccountNo : '',
                        'TabAccountID' => isset($item->TabAccountID) ? $item->TabAccountID : '',
                        'TabAccountNo' => isset($item->TabAccountNo) ? $item->TabAccountNo : '',
                        'TabCoAccountID' => isset($item->TabCoAccountID) ? $item->TabCoAccountID : '',
                        'TabCoAccountNo' => isset($item->TabCoAccountNo) ? $item->TabCoAccountNo : '',
                        'AnuAccountID' => isset($item->AnuAccountID) ? $item->AnuAccountID : '',
                        'AnuAccountNo' => isset($item->AnuAccountNo) ? $item->AnuAccountNo : '',
                        'AnuCoAccountID' => isset($item->AnuCoAccountID) ? $item->AnuCoAccountID : '',
                        'AnuCoAccountNo' => isset($item->AnuCoAccountNo) ? $item->AnuCoAccountNo : '',
                        'PmuAccountID' => isset($item->PmuAccountID) ? $item->PmuAccountID : '',
                        'PmuAccountNo' => isset($item->PmuAccountNo) ? $item->PmuAccountNo : '',
                        'PmuCoAccountID' => isset($item->PmuCoAccountID) ? $item->PmuCoAccountID : '',
                        'PmuCoAccountNo' => isset($item->PmuCoAccountNo) ? $item->PmuCoAccountNo : '',
                        'ScbAccountID' => isset($item->ScbAccountID) ? $item->ScbAccountID : '',
                        'ScbAccountNo' => isset($item->ScbAccountNo) ? $item->ScbAccountNo : '',
                        'ScbCoAccountID' => isset($item->ScbCoAccountID) ? $item->ScbCoAccountID : '',
                        'ScbCoAccountNo' => isset($item->ScbCoAccountNo) ? $item->ScbCoAccountNo : '',
                        'AccountID' => isset($item->AccountID) ? $item->AccountID : '',
                        'AccountNo' => isset($item->AccountNo) ? $item->AccountNo : '',
                        'EasCoAccountID' => isset($item->EasCoAccountID) ? $item->EasCoAccountID : '',
                        'EasCoAccountNo' => isset($item->EasCoAccountNo) ? $item->EasCoAccountNo : '',
                        'Description' => isset($item->Description) ? $item->Description : '',
                        'CcyID' => isset($item->CcyID) ? $item->CcyID : '',
                        'CcyNo' => isset($item->CcyNo) ? $item->CcyNo : '',
                        'CcyName' => isset($item->CcyName) ? $item->CcyName : '',
                        'ExchangeRate' => isset($item->ExchangeRate) ? $item->ExchangeRate : '1',
                        'FCDebitAmount' => isset($item->FCDebitAmount) ? $item->FCDebitAmount : '0',
                        'LCDebitAmount' => isset($item->LCDebitAmount) ? $item->LCDebitAmount : '0',
                        'FCCreditAmount' => isset($item->FCCreditAmount) ? $item->FCCreditAmount : '0',
                        'LCCreditAmount' => isset($item->LCCreditAmount) ? $item->LCCreditAmount : '0',
                        'FCAmount' => isset($item->FCAmount) ? $item->FCAmount : '0',
                        'LCAmount' => isset($item->LCAmount) ? $item->LCAmount : '0',
                        'FCTaxAmount' => isset($item->FCTaxAmount) ? $item->FCTaxAmount : '0',
                        'LCTaxAmount' => isset($item->LCTaxAmount) ? $item->LCTaxAmount : '0',
                        'InTransTypeID' => isset($item->InTransTypeID) ? $item->InTransTypeID : '',
                        'InTransTypeName' => isset($item->InTransTypeName) ? $item->InTransTypeName : '',
                        'InvoiceSerialNo' => isset($item->InvoiceSerialNo) ? $item->InvoiceSerialNo : '',
                        'InvoiceFormNo' => isset($item->InvoiceFormNo) ? $item->InvoiceFormNo : '',
                        'InvoiceSecurityCode' => isset($item->InvoiceSecurityCode) ? $item->InvoiceSecurityCode : '',
                        'InvoiceLookupAddress' => isset($item->InvoiceLookupAddress) ? $item->InvoiceLookupAddress : '',
                        'Quantity' => isset($item->Quantity) ? $item->Quantity : '',
                        'FCUnitPrice' => isset($item->FCUnitPrice) ? $item->FCUnitPrice : '',
                        'LCUnitPrice' => isset($item->LCUnitPrice) ? $item->LCUnitPrice : '',
                        'EmployeeID' => isset($item->EmployeeID) ? $item->EmployeeID : '',
                        'EmployeeNo' => isset($item->EmployeeNo) ? $item->EmployeeNo : '',
                        'EmployeeName' => isset($item->EmployeeName) ? $item->EmployeeName : '',
                        'CustomerID' => isset($item->CustomerID) ? $item->CustomerID : '',
                        'CustomerNo' => isset($item->CustomerNo) ? $item->CustomerNo : '',
                        'CustomerName' => isset($item->CustomerName) ? $item->CustomerName : '',
                        'VendorID' => isset($item->VendorID) ? $item->VendorID : '',
                        'VendorNo' => isset($item->VendorNo) ? $item->VendorNo : '',
                        'VendorName' => isset($item->VendorName) ? $item->VendorName : '',
                        'PartnerID' => isset($item->PartnerID) ? $item->PartnerID : '',
                        'PartnerNo' => isset($item->PartnerNo) ? $item->PartnerNo : '',
                        'FullName' => isset($item->FullName) ? $item->FullName : '',
                        'RevenueID' => isset($item->RevenueID) ? $item->RevenueID : '',
                        'RevenueNo' => isset($item->RevenueNo) ? $item->RevenueNo : '',
                        'RevenueName' => isset($item->RevenueName) ? $item->RevenueName : '',
                        'ExpenseID' => isset($item->ExpenseID) ? $item->ExpenseID : '',
                        'ExpenseNo' => isset($item->ExpenseNo) ? $item->ExpenseNo : '',
                        'ExpenseName' => isset($item->ExpenseName) ? $item->ExpenseName : '',
                        'FundID' => isset($item->FundID) ? $item->FundID : '',
                        'FundNo' => isset($item->FundNo) ? $item->FundNo : '',
                        'FundName' => isset($item->FundName) ? $item->FundName : '',
                        'CapitalID' => isset($item->CapitalID) ? $item->CapitalID : '',
                        'CapitalNo' => isset($item->CapitalNo) ? $item->CapitalNo : '',
                        'CapitalName' => isset($item->CapitalName) ? $item->CapitalName : '',
                        'FiscalPeriod' => isset($item->FiscalPeriod) ? $item->FiscalPeriod : '',
                        'FiscalYear' => isset($item->FiscalYear) ? $item->FiscalYear : '',
                        'SbiChapterID' => isset($item->SbiChapterID) ? $item->SbiChapterID : '',
                        'SbiChapterNo' => isset($item->SbiChapterNo) ? $item->SbiChapterNo : '',
                        'SbiChapterName' => isset($item->SbiChapterName) ? $item->SbiChapterName : '',
                        'SbiCategoryID' => isset($item->SbiCategoryID) ? $item->SbiCategoryID : '',
                        'SbiCategoryNo' => isset($item->SbiCategoryNo) ? $item->SbiCategoryNo : '',
                        'SbiCategoryName' => isset($item->SbiCategoryName) ? $item->SbiCategoryName : '',
                        'SbiItemID' => isset($item->SbiItemID) ? $item->SbiItemID : '',
                        'SbiItemNo' => isset($item->SbiItemNo) ? $item->SbiItemNo : '',
                        'SbiItemName' => isset($item->SbiItemName) ? $item->SbiItemName : '',
                        'ProjectID' => isset($item->ProjectID) ? $item->ProjectID : '',
                        'ProjectNo' => isset($item->ProjectNo) ? $item->ProjectNo : '',
                        //'TabmisNo' => isset($item->TabmisNo) ? $item->TabmisNo : '',
                        'ProjectName' => isset($item->ProjectName) ? $item->ProjectName : '',
                        'ContractID' => isset($item->ContractID) ? $item->ContractID : '',
                        'ContractNo' => isset($item->ContractNo) ? $item->ContractNo : '',
                        'ContractName' => isset($item->ContractName) ? $item->ContractName : '',
                        'FixedAssetID' => isset($item->FixedAssetID) ? $item->FixedAssetID : '',
                        'FixedAssetNo' => isset($item->FixedAssetNo) ? $item->FixedAssetNo : '',
                        'FixedAssetName' => isset($item->FixedAssetName) ? $item->FixedAssetName : '',
                        'ToolID' => isset($item->ToolID) ? $item->ToolID : '',
                        'ToolNo' => isset($item->ToolNo) ? $item->ToolNo : '',
                        'ToolName' => isset($item->ToolName) ? $item->ToolName : '',
                        'InvestAssetID' => isset($item->InvestAssetID) ? $item->InvestAssetID : '',
                        'InvestAssetNo' => isset($item->InvestAssetNo) ? $item->InvestAssetNo : '',
                        'InvestAssetName' => isset($item->InvestAssetName) ? $item->InvestAssetName : '',
                        'ItemID' => isset($item->ItemID) ? $item->ItemID : '',
                        'ItemNo' => isset($item->ItemNo) ? $item->ItemNo : '',
                        'ItemName' => isset($item->ItemName) ? $item->ItemName : '',
                        'BudgetLevel' => isset($item->BudgetLevel) ? $item->BudgetLevel : '',
                        'ProgramType' => isset($item->ProgramType) ? $item->ProgramType : '',
                        'ProgramID' => isset($item->ProgramID) ? $item->ProgramID : '',
                        'ProgramNo' => isset($item->ProgramNo) ? $item->ProgramNo : '',
                        'ProgramName' => isset($item->ProgramName) ? $item->ProgramName : '',
                        'SectorID' => isset($item->SectorID) ? $item->SectorID : '',
                        'SectorNo' => isset($item->SectorNo) ? $item->SectorNo : '',
                        'SectorName' => isset($item->SectorName) ? $item->SectorName : '',
                        'BudgetAllocTypeID' => isset($item->BudgetAllocTypeID) ? $item->BudgetAllocTypeID : '',
                        'ReceiveBy' => isset($item->ReceiveBy) ? $item->ReceiveBy : '',
                    ];
                }
            }

        }
        if (!empty($arrInsert)) {
            DB::table('act_gl_books')->insert($arrInsert);
            $json['Posted'] = 1;
        }else{
            $json['Posted'] = 0;
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    //Định mức doanh thu
    public function getNormTableItem(Request $request){
        $NormTableID = $request->post('NormTableID');
        $NormTableItem = NormTableItem::where(['NormTableID' => $NormTableID, 'Detail' => 1])->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['NormTableItem'] = $NormTableItem;
        return json_encode($json);
    }
    //Lấy dữ liệu từ Giao dự toán
    public function getList(Request $request){
        $data = null;
        $query = Sbpreviewplan::query();
        $where = [];
        $query->where('TransTypeID', '=', $request->TransTypeID);
        if($request->post('search')){
            $query->where(function ($query) use ($request) {
                $query->where('Comment', 'like', '%'.$request->search.'%')->orWhere('TransNo', 'like', '%'.$request->search.'%')->orWhere('TransDate', 'like', '%'.$request->search.'%')->orWhere('LCTotalAmount', 'like', '%'.$request->search.'%');
            });
        }
        if($request->post('RequestRepID')){
            $query->where('RequestRepID', '=', $request->RequestRepID);
        }else{
            $query->where('RequestRepID', '!=', 1);
        }
        if($request->post('CompanyID')){
            $query->where('InputCompanyID', 'like', $request->CompanyID);
        }
        $query->orderBy('act_gvouc_trans.TransDate', 'desc');
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $data = $query->paginate($per_page);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function loadTemp($id)
    {
        $data = Sbpreviewplan::find($id);
        $SbpmakeplanTrans = SbpmakeplanTrans::with('actautoact')->where('TransID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['SbpmakeplanTrans'] = $SbpmakeplanTrans;
        return json_encode($json);
    }

}
