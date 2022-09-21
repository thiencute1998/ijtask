<?php
namespace Module\Accounting\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use IjCore\IjDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Accounting\Models\AccountingAutoact;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaType;
use Module\Listing\Models\SysAutoNumber;
use Module\Accounting\Models\Accounting;
use Module\Accounting\Models\AccountingTrans;
use Module\Accounting\Models\AccountingBook;
use Module\Listing\Models\Uom;
use Module\Sbpmakeplan\Models\Actautoact;
use Module\Sbpmakeplan\Models\SbpmakeplanBook;
use Module\StateBudgetPlanning\Models\Sysperiod;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysStatusItem;

class AccountingController extends Controller{

    public function list(Request $request){
        $data = Accounting::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('act_gvouc_trans', ['select' => ['TransNo', 'TransDate'], 'conditions' => []]), 'Accounting.xlsx');
    }

    public function create(){
        if(old('TransNo')){
            $auto = old('TransNo');
        }else{
            $auto = AutoNo::genNewNumberNoTable('act_gvouc_trans', 'TransID', 'TransNo');
        }
        $Status = SysStatus::where('Inactive', '!=', 1)->get();
        $StatusItem = SysStatusItem::all();
        $ArrCoatype = CoaType::where('Inactive', '=', 0)->get();
        $Sysperiod = Sysperiod::all();
        $ArrAutoact = AccountingAutoact::where('Inactive', '=', 0)->orderBy('AutoactType', 'asc')->orderBy('Norder', 'asc')->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        $json['data']['Status'] = $Status;
        $json['data']['StatusItem'] = $StatusItem;
        $json['data']['ArrCoatype'] = $ArrCoatype;
        $json['data']['Sysperiod'] = $Sysperiod;
        $json['data']['ArrAutoact'] = $ArrAutoact;
        return json_encode($json);
    }
    public function store(Request $request){
        $master = (object)$request->master;
        $detail = $request->detail;
        //$detail = $request->post(detail);
        $validator = Validator::make($request->master, [
            'TransNo' => 'required|unique:act_gvouc_trans'
        ],
            [
                'TransNo.required' => 'Số CTG không được để trống',
                'TransNo.unique' => 'Số CTG đã tồn tại',
            ]);
        $autoModel = SysAutoNumber::where('NumberKey', 'act_gvouc_trans')->first();
        AutoNo::storeValidateIDNo($validator, $master, 'act_gvouc_trans','TransID', 'TransNo', $autoModel, 'chứng từ chung');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
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
            ])->first(); //Log::debug($SysStatusItem);
            $StatusDescription = $SysStatusItem->StatusDescription;
        }
        //Log::debug(Auth::user());
        $itemp = new Accounting([
            'TransDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->TransDate))),
            'PostDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PostDate))),
            'eTransDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->eTransDate))),
            'TransNo' => $master->TransNo,
            'eTransNo' => $master->eTransNo,
            'TransTypeID' => $master->TransTypeID,
            'TransTypeName' => $master->TransTypeName,
            'CoaTypeID' => $master->CoaTypeID,
            'CoaTypeNo' => $master->CoaTypeNo,
            'CoaTypeName' => $CoaTypeName,
            'PeriodID' => $master->PeriodID,
            'PeriodType' => $master->PeriodType,
            'PeriodName' => $master->PeriodName,
            'PeriodFromDate' =>  date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PeriodFromDate))),
            'PeriodToDate' =>  date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PeriodToDate))),
            'Comment' => $master->Comment,
            'FCTotalAmount' => $master->FCTotalAmount,
            'LCTotalAmount' => $master->LCTotalAmount,
            'FCTotalTaxAmount' => $master->FCTotalTaxAmount,
            'LCTotalTaxAmount' => $master->LCTotalTaxAmount,
            'CompanyID' => $master->CompanyID,
            'CompanyNo' => $master->CompanyNo,
            'CompanyMOFNo' => $master->CompanyMOFNo,
            'CompanyLocationNo' => $master->CompanyLocationNo,
            'CompanyName' => $master->CompanyName,
            'CompanyAddress' => $master->CompanyAddress,
            'CompanyBankAccount' => $master->CompanyBankAccount,
            'CompanyBankName' => $master->CompanyBankName,
            'CompanyContactName' => $master->CompanyContactName,
            'CompanyContactPosition' => $master->CompanyContactPosition,
            'EmployeeID' => $master->EmployeeID,
            'EmployeeNo' => $master->EmployeeNo,
            'EmployeeName' => $master->EmployeeName,
            'EmployeeCitizenIdNo' => $master->EmployeeCitizenIdNo,
            'EmployeeCitizenIdDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->EmployeeCitizenIdDate))),
            'EmployeeCitizenIdAt' => $master->EmployeeCitizenIdAt,
            'EmployeeDeparttmentName' => $master->EmployeeDeparttmentName,
            'EmployeePosition' => $master->EmployeePosition,
            'EmployeeBankAccount' => $master->EmployeeBankAccount,
            'EmployeeBankName' => $master->EmployeeBankName,
            'VendorID' => $master->VendorID,
            'VendorNo' => $master->VendorNo,
            'VendorName' => $master->VendorName,
            'VendorTaxcode' => $master->VendorTaxcode,
            'VendorLocationNo' => $master->VendorLocationNo,
            'VendorAddress' => $master->VendorAddress,
            'VendorBankAccount' => $master->VendorBankAccount,
            'VendorBankName' => $master->VendorBankName,
            'VendorContactName' => $master->VendorContactName,
            'VendorContactPosition' => $master->VendorContactPosition,
            'CustomerID' => $master->CustomerID,
            'CustomerNo' => $master->CustomerNo,
            'CustomerName' => $master->CustomerName,
            'CustomerTaxcode' => $master->CustomerTaxcode,
            'CustomerLocationNo' => $master->CustomerLocationNo,
            'CustomerAddress' => $master->CustomerAddress,
            'CustomerBankAccount' => $master->CustomerBankAccount,
            'CustomerBankName' => $master->CustomerBankName,
            'CustomerContactName' => $master->CustomerContactName,
            'CustomerContactPosition' => $master->CustomerContactPosition,
            'PartnerID' => $master->PartnerID,
            'PartnerName' => $master->PartnerName,
            'PartnerTaxcode' => $master->PartnerTaxcode,
            'PartnerLocationNo' => $master->PartnerLocationNo,
            'PartnerAddress' => $master->PartnerAddress,
            'PartnerBankAccount' => $master->PartnerBankAccount,
            'PartnerBankName' => $master->PartnerBankName,
            'PartnerContactName' => $master->PartnerContactName,
            'PartnerContactPosition' => $master->PartnerContactPosition,
            'PartnerCitizenIdNo' => $master->PartnerCitizenIdNo,
            'PartnerCitizenIdDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PartnerCitizenIdDate))),
            'PartnerCitizenIdAt' => $master->PartnerCitizenIdAt,
            'Locked' => $master->Locked,
            'LockedDate' => $master->LockedDate,
            'LockedUserID' => $master->LockedUserID,
            'StatusID' => $master->StatusID,
            'StatusValue' => $master->StatusValue,
            'StatusDescription' => $StatusDescription,
            'ArrCoaChecked'=> json_encode($master->ArrCoaChecked),
            'isAdjustTrans' => $master->isAdjustTrans,
            'InputCompanyID' => Auth::user()->CompanyID,
            'InputCompanyNo' => Auth::user()->CompanyNo,
            'InputCompanyName' => Auth::user()->CompanyName,
        ]);
        $itemp->save();

        $arrInsert = [];
        $TransID = $itemp->TransID;
        foreach ($detail as $item) {
            $item = (object)$item;
            if (!empty($item->FiscalYear)) {
                $arrInsert[] = [
                    'TransID' => $TransID,
                    'AutoactID' => isset($item->AutoactID) ? $item->AutoactID : '',
                    'AccountID' => isset($item->AccountID) ? $item->AccountID : '',
                    'AccountNo' => isset($item->AccountNo) ? $item->AccountNo : '',
                    'AccountName' => isset($item->AccountName) ? $item->AccountName : '',
                    'CoAccountID' => isset($item->CoAccountID) ? $item->CoAccountID : '',
                    'CoAccountNo' => isset($item->CoAccountNo) ? $item->CoAccountNo : '',
                    'CoAccountName' => isset($item->CoAccountName) ? $item->CoAccountName : '',
                    'Description' => isset($item->Description) ? $item->Description : '',
                    'CcyID' => isset($item->CcyID) ? $item->CcyID : '',
                    'CcyNo' => isset($item->CcyNo) ? $item->CcyNo : '',
                    'CcyName' => isset($item->CcyName) ? $item->CcyName : '',
                    'ExchangeRate' => isset($item->ExchangeRate) ? $item->ExchangeRate : '1',
                    'FCDebitAmount' => isset($item->FCDebitAmount) ? $item->FCDebitAmount : '0',
                    'LCDebitAmount' => isset($item->LCDebitAmount) ? $item->LCDebitAmount : '0',
                    'FCCreditAmount' => isset($item->FCCreditAmount) ? $item->FCCreditAmount : '0',
                    'LCCreditAmount' => isset($item->LCCreditAmount) ? $item->LCCreditAmount : '0',
                    'FCAmount' => isset($item->FCDebitAmount) ? $item->FCDebitAmount : '0',
                    'LCAmount' => isset($item->LCDebitAmount) ? $item->LCDebitAmount : '0',
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
                    'UomID' => isset($item->UomID) ? $item->UomID : '',
                    'UomNo' => isset($item->UomNo) ? $item->UomNo : '',
                    'UomName' => isset($item->UomName) ? $item->UomName : '',
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
                    'SbrSectorID' => isset($item->SbrSectorID) ? $item->SbrSectorID : '',
                    'SbrSectorNo' => isset($item->SbrSectorNo) ? $item->SbrSectorNo : '',
                    'SbrSectorName' => isset($item->SbrSectorName) ? $item->SbrSectorName : '',
                    'NormTableID' => $master->NormTableID,
                    'NormTableItemID' => isset($item->NormTableItemID) ? $item->NormTableItemID : '',
                    'NormTableItemNo' => isset($item->NormTableItemNo) ? $item->NormTableItemNo : '',
                    'NormTableItemName' => isset($item->Description) ? $item->Description : '',
                    'FCNormAmount' => isset($item->FCNormAmount) ? $item->FCNormAmount : '0',
                    'LCNormAmount' => isset($item->LCNormAmount) ? $item->LCNormAmount : '0',
                    'NormID' => isset($item->NormID) ? $item->NormID : '',
                    'NormNo' => isset($item->NormNo) ? $item->NormNo : '',
                    'NormName' => isset($item->NormName) ? $item->NormName : '',
                    'NormAllotID' => isset($item->NormAllotID) ? $item->NormAllotID : '',
                    'NormAllotNo' => isset($item->NormAllotNo) ? $item->NormAllotNo : '',
                    'NormAllotName' => isset($item->NormAllotName) ? $item->NormAllotName : '',
                    'NormAllotILevelID' => isset($item->NormAllotILevelID) ? $item->NormAllotILevelID : '',
                    'NormAllotLevelNo' => isset($item->NormAllotLevelNo) ? $item->NormAllotLevelNo : '',
                    'NormAllotLevelName' => isset($item->NormAllotLevelName) ? $item->NormAllotLevelName : '',
                    'BudgetAllocTypeID' => isset($item->BudgetAllocTypeID) ? $item->BudgetAllocTypeID : '',
                    'ReceiveBy' => isset($item->ReceiveBy) ? $item->ReceiveBy : '',
                    'Detail' => $item->Detail,
                    'Level' => $item->Level,
                    'ParentID' => (!empty($item->ParentID)) ? $item->ParentID : null,
                    'LineIDTmp' => $item->LineID,
                ];
            }
        }

        if (!empty($arrInsert)) {
            DB::table('act_gvouc_trans_item')->insert($arrInsert);

            $updateSql = "UPDATE act_gvouc_trans_item AS A, (SELECT LineID, LineIDTmp FROM act_gvouc_trans_item WHERE TransID = $TransID) AS B
                SET A.ParentID = B.LineID
                WHERE A.TransID = $TransID AND A.ParentID = B.LineIDTmp";
            DB::update(DB::raw($updateSql));

            $updateSqlLineID = "UPDATE act_gvouc_trans_item AS A
                SET A.LineIDTmp = A.LineID
                WHERE A.TransID = $TransID";
            DB::update(DB::raw($updateSqlLineID));
        }
        //Tách TK, TKĐƯ nếu nhập cùng dòng
        $AccountingTrans = AccountingTrans::where('AccountNo', '!=', 0)->where('CoAccountNo', '!=', 0)->where('isCCAccount', '=', 0)->where('TransID', '=', $TransID)->get();
        //Log::debug(count($AccountingTrans));
        $arrInsert1 = []; $arrInsert2 = [];
        if($AccountingTrans){
            foreach ($AccountingTrans as $item) {
                $LineID = $item->LineID;
                //$AccountNo = $item->AccountNo;
                //$CoAccountNo = $item->CoAccountNo;
                $FCDebitAmount = $item->FCDebitAmount;
                $FCCreditAmount = $item->FCCreditAmount;
                if($FCDebitAmount){
                    $arrInsert1[] = [
                        'TransID' => $TransID,
                        'AccountNo' => $item->CoAccountNo,
                        'AccountName' => isset($item->CoAccountName) ? $item->CoAccountName : '',
                        'CoAccountNo' => $item->AccountNo,
                        'CoAccountName' => isset($item->AccountName) ? $item->AccountName : '',
                        'FCCreditAmount' => $item->FCDebitAmount,
                        'AutoactID' => $item->AutoactID,
                        'Description' => $item->Description,
                        'CcyID' => $item->CcyID,
                        'CcyNo' => $item->CcyNo,
                        'CcyName' => $item->CcyName,
                        'ExchangeRate' => $item->ExchangeRate,
                        'FCAmount' => $item->FCAmount,
                        'LCAmount' => $item->LCAmount,
                        'InTransTypeID' => $item->InTransTypeID,
                        'InTransTypeName' => $item->InTransTypeName,
                        'InvoiceSerialNo' => $item->InvoiceSerialNo,
                        'InvoiceFormNo' => $item->InvoiceFormNo,
                        'InvoiceSecurityCode' => $item->InvoiceSecurityCode,
                        'InvoiceLookupAddress' => $item->InvoiceLookupAddress,
                        'Quantity' => $item->Quantity,
                        'FCUnitPrice' => $item->FCUnitPrice,
                        'LCUnitPrice' => $item->LCUnitPrice,
                        'EmployeeID' => $item->EmployeeID,
                        'EmployeeNo' => $item->EmployeeNo,
                        'EmployeeName' => $item->EmployeeName,
                        'CustomerID' => $item->CustomerID,
                        'CustomerNo' => $item->CustomerNo,
                        'CustomerName' => $item->CustomerName,
                        'VendorID' => $item->VendorID,
                        'VendorNo' => $item->VendorNo,
                        'VendorName' => $item->VendorName,
                        'PartnerID' => $item->PartnerID,
                        'PartnerNo' => $item->PartnerNo,
                        'FullName' => $item->FullName,
                        'RevenueID' => $item->RevenueID,
                        'RevenueNo' => $item->RevenueNo,
                        'RevenueName' => $item->RevenueName,
                        'ExpenseID' => $item->ExpenseID,
                        'ExpenseNo' => $item->ExpenseNo,
                        'ExpenseName' => $item->ExpenseName,
                        'FundID' => $item->FundID,
                        'FundNo' => $item->FundNo,
                        'FundName' => $item->FundName,
                        'CapitalID' => $item->CapitalID,
                        'CapitalNo' => $item->CapitalNo,
                        'CapitalName' => $item->CapitalName,
                        'FiscalPeriod' => $item->FiscalPeriod,
                        'FiscalYear' => $item->FiscalYear,
                        'SbiChapterID' => $item->SbiChapterID,
                        'SbiChapterNo' => $item->SbiChapterNo,
                        'SbiChapterName' => $item->SbiChapterName,
                        'SbiCategoryID' => $item->SbiCategoryID,
                        'SbiCategoryNo' => $item->SbiCategoryNo,
                        'SbiCategoryName' => $item->SbiCategoryName,
                        'SbiItemID' => $item->SbiItemID,
                        'SbiItemNo' => $item->SbiItemNo,
                        'SbiItemName' => $item->SbiItemName,
                        'ProjectID' => $item->ProjectID,
                        'ProjectNo' => $item->ProjectNo,
                        'TabmisNo' => $item->TabmisNo,
                        'ProjectName' => $item->ProjectName,
                        'ContractID' => $item->ContractID,
                        'ContractNo' => $item->ContractNo,
                        'ContractName' => $item->ContractName,
                        'FixedAssetID' => $item->FixedAssetID,
                        'FixedAssetNo' => $item->FixedAssetNo,
                        'FixedAssetName' => $item->FixedAssetName,
                        'UomID' => isset($item->UomID) ? $item->UomID : '',
                        'UomNo' => isset($item->UomNo) ? $item->UomNo : '',
                        'UomName' => isset($item->UomName) ? $item->UomName : '',
                        'ToolID' => $item->ToolID,
                        'ToolNo' => $item->ToolNo,
                        'ToolName' => $item->ToolName,
                        'InvestAssetID' => $item->InvestAssetID,
                        'InvestAssetNo' => $item->InvestAssetNo,
                        'InvestAssetName' => $item->InvestAssetName,
                        'ItemID' => $item->ItemID,
                        'ItemNo' => $item->ItemNo,
                        'ItemName' => $item->ItemName,
                        'BudgetLevel' => $item->BudgetLevel,
                        'ProgramType' => $item->ProgramType,
                        'ProgramID' => $item->ProgramID,
                        'ProgramNo' => $item->ProgramNo,
                        'ProgramName' => $item->ProgramName,
                        'SectorID' => $item->SectorID,
                        'SectorNo' => $item->SectorNo,
                        'SectorName' => $item->SectorName,
                        'SbrSectorID' => isset($item->SbrSectorID) ? $item->SbrSectorID : '',
                        'SbrSectorNo' => isset($item->SbrSectorNo) ? $item->SbrSectorNo : '',
                        'SbrSectorName' => isset($item->SbrSectorName) ? $item->SbrSectorName : '',
                        'NormTableID' => $master->NormTableID,
                        'NormTableItemID' => isset($item->NormTableItemID) ? $item->NormTableItemID : '',
                        'NormTableItemNo' => isset($item->NormTableItemNo) ? $item->NormTableItemNo : '',
                        'NormTableItemName' => isset($item->Description) ? $item->Description : '',
                        'FCNormAmount' => isset($item->FCNormAmount) ? $item->FCNormAmount : '0',
                        'LCNormAmount' => isset($item->LCNormAmount) ? $item->LCNormAmount : '0',
                        'NormID' => isset($item->NormID) ? $item->NormID : '',
                        'NormNo' => isset($item->NormNo) ? $item->NormNo : '',
                        'NormName' => isset($item->NormName) ? $item->NormName : '',
                        'NormAllotID' => isset($item->NormAllotID) ? $item->NormAllotID : '',
                        'NormAllotNo' => isset($item->NormAllotNo) ? $item->NormAllotNo : '',
                        'NormAllotName' => isset($item->NormAllotName) ? $item->NormAllotName : '',
                        'NormAllotILevelID' => isset($item->NormAllotILevelID) ? $item->NormAllotILevelID : '',
                        'NormAllotLevelNo' => isset($item->NormAllotLevelNo) ? $item->NormAllotLevelNo : '',
                        'NormAllotLevelName' => isset($item->NormAllotLevelName) ? $item->NormAllotLevelName : '',
                        'BudgetAllocTypeID' => $item->BudgetAllocTypeID,
                        'ReceiveBy' => $item->ReceiveBy,
                        'LineIDTmp' => $item->LineID,
                        'isCCAccount' => 1,
                    ];
                    //DB::table('act_gvouc_trans_item')->where('LineID', $LineID)->update(['CoAccountNo' => '']);
                    DB::table('act_gvouc_trans_item')->where('LineID', $LineID)->update(['isCCAccount' => 1]);
                }
                if($FCCreditAmount){
                    $arrInsert2[] = [
                        'TransID' => $TransID,
                        'AccountNo' => $item->CoAccountNo,
                        'AccountName' => isset($item->CoAccountName) ? $item->CoAccountName : '',
                        'CoAccountNo' => $item->AccountNo,
                        'CoAccountName' => isset($item->AccountName) ? $item->AccountName : '',
                        'FCDebitAmount' => $item->FCCreditAmount,
                        'AutoactID' => $item->AutoactID,
                        'Description' => $item->Description,
                        'CcyID' => $item->CcyID,
                        'CcyNo' => $item->CcyNo,
                        'CcyName' => $item->CcyName,
                        'ExchangeRate' => $item->ExchangeRate,
                        'FCAmount' => $item->FCAmount,
                        'LCAmount' => $item->LCAmount,
                        'InTransTypeID' => $item->InTransTypeID,
                        'InTransTypeName' => $item->InTransTypeName,
                        'InvoiceSerialNo' => $item->InvoiceSerialNo,
                        'InvoiceFormNo' => $item->InvoiceFormNo,
                        'InvoiceSecurityCode' => $item->InvoiceSecurityCode,
                        'InvoiceLookupAddress' => $item->InvoiceLookupAddress,
                        'Quantity' => $item->Quantity,
                        'FCUnitPrice' => $item->FCUnitPrice,
                        'LCUnitPrice' => $item->LCUnitPrice,
                        'EmployeeID' => $item->EmployeeID,
                        'EmployeeNo' => $item->EmployeeNo,
                        'EmployeeName' => $item->EmployeeName,
                        'CustomerID' => $item->CustomerID,
                        'CustomerNo' => $item->CustomerNo,
                        'CustomerName' => $item->CustomerName,
                        'VendorID' => $item->VendorID,
                        'VendorNo' => $item->VendorNo,
                        'VendorName' => $item->VendorName,
                        'PartnerID' => $item->PartnerID,
                        'PartnerNo' => $item->PartnerNo,
                        'FullName' => $item->FullName,
                        'RevenueID' => $item->RevenueID,
                        'RevenueNo' => $item->RevenueNo,
                        'RevenueName' => $item->RevenueName,
                        'ExpenseID' => $item->ExpenseID,
                        'ExpenseNo' => $item->ExpenseNo,
                        'ExpenseName' => $item->ExpenseName,
                        'FundID' => $item->FundID,
                        'FundNo' => $item->FundNo,
                        'FundName' => $item->FundName,
                        'CapitalID' => $item->CapitalID,
                        'CapitalNo' => $item->CapitalNo,
                        'CapitalName' => $item->CapitalName,
                        'FiscalPeriod' => $item->FiscalPeriod,
                        'FiscalYear' => $item->FiscalYear,
                        'SbiChapterID' => $item->SbiChapterID,
                        'SbiChapterNo' => $item->SbiChapterNo,
                        'SbiChapterName' => $item->SbiChapterName,
                        'SbiCategoryID' => $item->SbiCategoryID,
                        'SbiCategoryNo' => $item->SbiCategoryNo,
                        'SbiCategoryName' => $item->SbiCategoryName,
                        'SbiItemID' => $item->SbiItemID,
                        'SbiItemNo' => $item->SbiItemNo,
                        'SbiItemName' => $item->SbiItemName,
                        'ProjectID' => $item->ProjectID,
                        'ProjectNo' => $item->ProjectNo,
                        'TabmisNo' => $item->TabmisNo,
                        'ProjectName' => $item->ProjectName,
                        'ContractID' => $item->ContractID,
                        'ContractNo' => $item->ContractNo,
                        'ContractName' => $item->ContractName,
                        'FixedAssetID' => $item->FixedAssetID,
                        'FixedAssetNo' => $item->FixedAssetNo,
                        'FixedAssetName' => $item->FixedAssetName,
                        'UomID' => isset($item->UomID) ? $item->UomID : '',
                        'UomNo' => isset($item->UomNo) ? $item->UomNo : '',
                        'UomName' => isset($item->UomName) ? $item->UomName : '',
                        'ToolID' => $item->ToolID,
                        'ToolNo' => $item->ToolNo,
                        'ToolName' => $item->ToolName,
                        'InvestAssetID' => $item->InvestAssetID,
                        'InvestAssetNo' => $item->InvestAssetNo,
                        'InvestAssetName' => $item->InvestAssetName,
                        'ItemID' => $item->ItemID,
                        'ItemNo' => $item->ItemNo,
                        'ItemName' => $item->ItemName,
                        'BudgetLevel' => $item->BudgetLevel,
                        'ProgramType' => $item->ProgramType,
                        'ProgramID' => $item->ProgramID,
                        'ProgramNo' => $item->ProgramNo,
                        'ProgramName' => $item->ProgramName,
                        'SectorID' => $item->SectorID,
                        'SectorNo' => $item->SectorNo,
                        'SectorName' => $item->SectorName,
                        'SbrSectorID' => isset($item->SbrSectorID) ? $item->SbrSectorID : '',
                        'SbrSectorNo' => isset($item->SbrSectorNo) ? $item->SbrSectorNo : '',
                        'SbrSectorName' => isset($item->SbrSectorName) ? $item->SbrSectorName : '',
                        'NormTableID' => $item->NormTableID,
                        'NormTableItemID' => $item->NormTableItemID,
                        'NormTableItemNo' => $item->NormTableItemNo,
                        'NormTableItemName' => $item->NormTableItemName,
                        'NormID' => $item->NormID,
                        'NormNo' => $item->NormNo,
                        'NormName' => $item->NormName,
                        'BudgetAllocTypeID' => $item->BudgetAllocTypeID,
                        'ReceiveBy' => $item->ReceiveBy,
                        'LineIDTmp' => $item->LineID,
                        'isCCAccount' => 1,
                    ];
                    //DB::table('act_gvouc_trans_item')->where('LineID', $LineID)->update(['AccountNo' => '']);
                    DB::table('act_gvouc_trans_item')->where('LineID', $LineID)->update(['isCCAccount' => 1]);
                }

                //DB::table('act_gvouc_trans_item')->where('LineID', $LineID)->delete();
            }
            if(!empty($arrInsert1)){
                DB::table('act_gvouc_trans_item')->insert($arrInsert1);
            }

            if(!empty($arrInsert2)){
                DB::table('act_gvouc_trans_item')->insert($arrInsert2);
            }

        }
        ///
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $itemp->TransID;

        return json_encode($json);
    }

    public function view($id){
        $data = Accounting::find($id);
        $AccountingTrans = AccountingTrans::where(['TransID' => $id])->get();
        $Actautoact = Actautoact::all();
        $Uom = Uom::find($data->UomID); $UomName = isset($Uom->UomName)? $Uom->UomName : '';
        $data->TransNo = $data->TransNo;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['AccountingTrans'] = $AccountingTrans;
        $json['data']['Actautoact'] = $Actautoact;
        $json['data']['UomName'] = $UomName;
        return json_encode($json);
    }

    public function edit($id){
        $data = Accounting::find($id);
        $AccountingTrans = AccountingTrans::with('actautoact')->where('TransID',$id)->get();
        $Status = SysStatus::where('Inactive', '!=', 1)->get();
        $StatusItem = SysStatusItem::all();
        $ArrCoatype = CoaType::where('Inactive', '=', 0)->get();
        $Sysperiod = Sysperiod::all();
        $ArrAutoact = AccountingAutoact::where('Inactive', '=', 0)->orderBy('AutoactType', 'asc')->orderBy('Norder', 'asc')->get();
        if(old('TransNo')){
            $data->TransNo = old('TransNo');
        }else{
            $data->TransNo = $data->Prefix.$data->TransNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['AccountingTrans'] = $AccountingTrans;
        $json['data']['Status'] = $Status;
        $json['data']['StatusItem'] = $StatusItem;
        $json['data']['ArrCoatype'] = $ArrCoatype;
        $json['data']['Sysperiod'] = $Sysperiod;
        $json['data']['ArrAutoact'] = $ArrAutoact;
        return json_encode($json);
    }
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $date = date('d');
        $month = date('m');
        $year = date('Y');

        $data = Accounting::search($request);
        $response = [
            'master' => [
                "CreateName" => Auth::user()->FullName,
                "TransNo" => "00001",
                "Company" => "UBND tỉnh Vĩnh Phú",
                "DateTime" => "Vĩnh Phú, ngày $date tháng $month năm $year"
            ],
            'detail' => $data
        ];
        $json['data'] = $response;
        return json_encode($json);
    }
    public function getDetailReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $id = $request->id;
        $master = Accounting::find($id);
        $detail = AccountingTrans::where('TransID', '=', $id)->get();

        $date = date('d');
        $month = date('m');
        $year = date('Y');

        $response = [
            'master' => [
                "Company" => "UBND tỉnh Vĩnh Phú",
                "TransNo" => $master->TransNo,
                "TransDate" => IjDate::DateToString($master->TransDate, 'd/m/Y'),
                "PostDate" => IjDate::DateToString($master->PostDate, 'd/m/Y'),
                "eTransNo" => $master->eTransNo,
                "eTransDate" => IjDate::DateToString($master->eTransDate, 'd/m/Y'),
                "CoaTypeName" => $master->CoaTypeName,
                "AccoutingStatus" => $master->StatusDescription,
                "Comment" => $master->Comment,
                "LCTotalDebitAmount" => $master->LCTotalDebitAmount,
                "CreateName" => Auth::user()->FullName,
                "AccountName" => "Trần Phương Hoa",
                "HeadName" => "Khương Công Trung",
                "DateTime" => "Vĩnh Phú, ngày $date tháng $month năm $year",
                "CurrencyName" => "đ"
            ],
            'detail' => $detail
        ];
        $json['data'] = $response;
        return json_encode($json);
    }

    public function update(Request $request, $id){
        $validate = [];
        $master = (object)$request->master;
        $detail = $request->detail;
        $Accounting = Accounting::find($id);
        if(!$Accounting){
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($Accounting->Prefix.$Accounting->TransNo.$Accounting->Suffix != $master->TransNo){
            $validator = Validator::make($request->all(), [
                'TransNo' => 'required|unique:act_gvouc_trans'
            ],
                [
                    'TransNo.required' => 'Số CTG không được để trống',
                    'TransNo.unique' => 'Số CTG đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'act_gvouc_trans')->first();
            AutoNo::UpdateValidateIDNO($validator, $request, $Accounting,'act_gvouc_trans', $autoModel, 'chứng từ chung','TransNo', 'TransID');

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $Accounting->TransNo = $request->TransNo;
        }
        $Accounting->TransDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->TransDate)));
        $Accounting->PostDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PostDate)));
        $Accounting->eTransDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->eTransDate)));
        $Accounting->TransNo = $master->TransNo;
        $Accounting->eTransNo = $master->eTransNo;
        $Accounting->TransTypeID = $master->TransTypeID;
        $Accounting->TransTypeName = $master->TransTypeName;
        $Accounting->CoaTypeID = $master->CoaTypeID;
        $Accounting->CoaTypeNo = $master->CoaTypeNo;
        $Accounting->CoaTypeName = $master->CoaTypeName;
        $Accounting->PeriodID = $master->PeriodID;
        $Accounting->PeriodType = $master->PeriodType;
        $Accounting->PeriodName = $master->PeriodName;
        $Accounting->PeriodFromDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PeriodFromDate)));
        $Accounting->PeriodToDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PeriodToDate)));
        $Accounting->Comment = $master->Comment;
        $Accounting->FCTotalAmount = $master->FCTotalAmount;
        $Accounting->LCTotalAmount = $master->LCTotalAmount;
        $Accounting->FCTotalTaxAmount = $master->FCTotalTaxAmount;
        $Accounting->LCTotalTaxAmount = $master->LCTotalTaxAmount;
        $Accounting->CompanyID = $master->CompanyID;
        $Accounting->CompanyNo = $master->CompanyNo;
        $Accounting->CompanyMOFNo = $master->CompanyMOFNo;
        $Accounting->CompanyLocationNo = $master->CompanyLocationNo;
        $Accounting->CompanyName = $master->CompanyName;
        $Accounting->CompanyAddress = $master->CompanyAddress;
        $Accounting->CompanyBankAccount = $master->CompanyBankAccount;
        $Accounting->CompanyBankName = $master->CompanyBankName;
        $Accounting->CompanyContactName = $master->CompanyContactName;
        $Accounting->CompanyContactPosition = $master->CompanyContactPosition;
        $Accounting->EmployeeID = $master->EmployeeID;
        $Accounting->EmployeeNo = $master->EmployeeNo;
        $Accounting->EmployeeName = $master->EmployeeName;
        $Accounting->EmployeeCitizenIdNo = $master->EmployeeCitizenIdNo;
        $Accounting->EmployeeCitizenIdDate = $master->EmployeeCitizenIdDate;
        $Accounting->EmployeeCitizenIdAt = $master->EmployeeCitizenIdAt;
        $Accounting->EmployeeDeparttmentName = $master->EmployeeDeparttmentName;
        $Accounting->EmployeePosition = $master->EmployeePosition;
        $Accounting->EmployeeBankAccount = $master->EmployeeBankAccount;
        $Accounting->EmployeeBankName = $master->EmployeeBankName;
        $Accounting->VendorID = $master->VendorID;
        $Accounting->VendorNo = $master->VendorNo;
        $Accounting->VendorName = $master->VendorName;
        $Accounting->VendorTaxcode = $master->VendorTaxcode;
        $Accounting->VendorLocationNo = $master->VendorLocationNo;
        $Accounting->VendorAddress = $master->VendorAddress;
        $Accounting->VendorBankAccount = $master->VendorBankAccount;
        $Accounting->VendorBankName = $master->VendorBankName;
        $Accounting->VendorContactName = $master->VendorContactName;
        $Accounting->VendorContactPosition = $master->VendorContactPosition;
        $Accounting->CustomerID = $master->CustomerID;
        $Accounting->CustomerNo = $master->CustomerNo;
        $Accounting->CustomerName = $master->CustomerName;
        $Accounting->CustomerTaxcode = $master->CustomerTaxcode;
        $Accounting->CustomerLocationNo = $master->CustomerLocationNo;
        $Accounting->CustomerAddress = $master->CustomerAddress;
        $Accounting->CustomerBankAccount = $master->CustomerBankAccount;
        $Accounting->CustomerBankName = $master->CustomerBankName;
        $Accounting->CustomerContactName = $master->CustomerContactName;
        $Accounting->CustomerContactPosition = $master->CustomerContactPosition;
        $Accounting->PartnerID = $master->PartnerID;
        $Accounting->PartnerName = $master->PartnerName;
        $Accounting->PartnerTaxcode = $master->PartnerTaxcode;
        $Accounting->PartnerLocationNo = $master->PartnerLocationNo;
        $Accounting->PartnerAddress = $master->PartnerAddress;
        $Accounting->PartnerBankAccount = $master->PartnerBankAccount;
        $Accounting->PartnerBankName = $master->PartnerBankName;
        $Accounting->PartnerContactName = $master->PartnerContactName;
        $Accounting->PartnerContactPosition = $master->PartnerContactPosition;
        $Accounting->PartnerCitizenIdNo = $master->PartnerCitizenIdNo;
        $Accounting->PartnerCitizenIdDate = $master->PartnerCitizenIdDate;
        $Accounting->PartnerCitizenIdAt = $master->PartnerCitizenIdAt;
        $Accounting->Locked = $master->Locked;
        $Accounting->LockedDate = $master->LockedDate;
        $Accounting->LockedUserID = $master->LockedUserID;
        $Accounting->StatusID = $master->StatusID;
        $Accounting->StatusValue = $master->StatusValue;
        $Accounting->StatusDescription = $master->StatusDescription;
        $Accounting->ArrCoaChecked = json_encode($master->ArrCoaChecked);
        $Accounting->isAdjustTrans = $master->isAdjustTrans;
        $Accounting->save();
        //Xóa Trans Item
        AccountingTrans::where('TransID', $id)->delete();
        $arrInsert = [];
        $TransID = $Accounting->TransID;
        foreach ($detail as $item) {
            $item = (object)$item;
            if (!empty($item->FiscalYear)) {
                $arrInsert[] = [
                    'TransID' => $TransID,
                    'AutoactID' => isset($item->AutoactID) ? $item->AutoactID : '',
                    'AccountID' => isset($item->AccountID) ? $item->AccountID : '',
                    'AccountNo' => isset($item->AccountNo) ? $item->AccountNo : '',
                    'AccountName' => isset($item->AccountName) ? $item->AccountName : '',
                    'CoAccountID' => isset($item->CoAccountID) ? $item->CoAccountID : '',
                    'CoAccountNo' => isset($item->CoAccountNo) ? $item->CoAccountNo : '',
                    'CoAccountName' => isset($item->CoAccountName) ? $item->CoAccountName : '',
                    'Description' => isset($item->Description) ? $item->Description : '',
                    'CcyID' => isset($item->CcyID) ? $item->CcyID : '',
                    'CcyNo' => isset($item->CcyNo) ? $item->CcyNo : '',
                    'CcyName' => isset($item->CcyName) ? $item->CcyName : '',
                    'ExchangeRate' => isset($item->ExchangeRate) ? $item->ExchangeRate : '1',
                    'FCDebitAmount' => isset($item->FCDebitAmount) ? $item->FCDebitAmount : '0',
                    'LCDebitAmount' => isset($item->LCDebitAmount) ? $item->LCDebitAmount : '0',
                    'FCCreditAmount' => isset($item->FCCreditAmount) ? $item->FCCreditAmount : '0',
                    'LCCreditAmount' => isset($item->LCCreditAmount) ? $item->LCCreditAmount : '0',
                    'FCAmount' => isset($item->FCDebitAmount) ? $item->FCDebitAmount : '0',
                    'LCAmount' => isset($item->LCDebitAmount) ? $item->LCDebitAmount : '0',
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
                    'UomID' => isset($item->UomID) ? $item->UomID : '',
                    'UomNo' => isset($item->UomNo) ? $item->UomNo : '',
                    'UomName' => isset($item->UomName) ? $item->UomName : '',
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
                    'SbrSectorID' => isset($item->SbrSectorID) ? $item->SbrSectorID : '',
                    'SbrSectorNo' => isset($item->SbrSectorNo) ? $item->SbrSectorNo : '',
                    'SbrSectorName' => isset($item->SbrSectorName) ? $item->SbrSectorName : '',
                    'NormTableID' => $master->NormTableID,
                    'NormTableItemID' => isset($item->NormTableItemID) ? $item->NormTableItemID : '',
                    'NormTableItemNo' => isset($item->NormTableItemNo) ? $item->NormTableItemNo : '',
                    'NormTableItemName' => isset($item->Description) ? $item->Description : '',
                    'FCNormAmount' => isset($item->FCNormAmount) ? $item->FCNormAmount : '0',
                    'LCNormAmount' => isset($item->LCNormAmount) ? $item->LCNormAmount : '0',
                    'NormID' => isset($item->NormID) ? $item->NormID : '',
                    'NormNo' => isset($item->NormNo) ? $item->NormNo : '',
                    'NormName' => isset($item->NormName) ? $item->NormName : '',
                    'NormAllotID' => isset($item->NormAllotID) ? $item->NormAllotID : '',
                    'NormAllotNo' => isset($item->NormAllotNo) ? $item->NormAllotNo : '',
                    'NormAllotName' => isset($item->NormAllotName) ? $item->NormAllotName : '',
                    'NormAllotILevelID' => isset($item->NormAllotILevelID) ? $item->NormAllotILevelID : '',
                    'NormAllotLevelNo' => isset($item->NormAllotLevelNo) ? $item->NormAllotLevelNo : '',
                    'NormAllotLevelName' => isset($item->NormAllotLevelName) ? $item->NormAllotLevelName : '',
                    'BudgetAllocTypeID' => isset($item->BudgetAllocTypeID) ? $item->BudgetAllocTypeID : '',
                    'ReceiveBy' => isset($item->ReceiveBy) ? $item->ReceiveBy : '',
                    'Detail' => $item->Detail,
                    'Level' => $item->Level,
                    'ParentID' => (!empty($item->ParentID)) ? $item->ParentID : null,
                    'LineIDTmp' => $item->LineID,
                    'isCCAccount' => isset($item->isCCAccount) ? $item->isCCAccount : '',
                ];
            }
        }

        if (!empty($arrInsert)) {
            DB::table('act_gvouc_trans_item')->insert($arrInsert);

            $updateSql = "UPDATE act_gvouc_trans_item AS A, (SELECT LineID, LineIDTmp FROM act_gvouc_trans_item WHERE TransID = $TransID) AS B
                SET A.ParentID = B.LineID
                WHERE A.TransID = $TransID AND A.ParentID = B.LineIDTmp";
            DB::update(DB::raw($updateSql));

            $updateSqlLineID = "UPDATE act_gvouc_trans_item AS A
                SET A.LineIDTmp = A.LineID
                WHERE A.TransID = $TransID";
            DB::update(DB::raw($updateSqlLineID));
        }

        //Tách TK, TKĐƯ nếu nhập cùng dòng
        $AccountingTrans = AccountingTrans::where('AccountNo', '!=', 0)->where('CoAccountNo', '!=', 0)->where('isCCAccount', '=', 0)->where('TransID', '=', $id)->get();
        //Log::debug(count($AccountingTrans));
        $arrInsert1 = []; $arrInsert2 = [];
        if($AccountingTrans) {
            foreach ($AccountingTrans as $item) {
                $LineID = $item->LineID;
                //$AccountNo = $item->AccountNo;
                //$CoAccountNo = $item->CoAccountNo;
                $FCDebitAmount = $item->FCDebitAmount;
                $FCCreditAmount = $item->FCCreditAmount;
                if ($FCDebitAmount) {
                    $arrInsert1[] = [
                        'TransID' => $TransID,
                        'AccountNo' => $item->CoAccountNo,
                        'AccountName' => isset($item->CoAccountName) ? $item->CoAccountName : '',
                        'CoAccountNo' => $item->AccountNo,
                        'CoAccountName' => isset($item->AccountName) ? $item->AccountName : '',
                        'FCCreditAmount' => $item->FCDebitAmount,
                        'AutoactID' => $item->AutoactID,
                        'Description' => $item->Description,
                        'CcyID' => $item->CcyID,
                        'CcyNo' => $item->CcyNo,
                        'CcyName' => $item->CcyName,
                        'ExchangeRate' => $item->ExchangeRate,
                        'FCAmount' => $item->FCAmount,
                        'LCAmount' => $item->LCAmount,
                        'InTransTypeID' => $item->InTransTypeID,
                        'InTransTypeName' => $item->InTransTypeName,
                        'InvoiceSerialNo' => $item->InvoiceSerialNo,
                        'InvoiceFormNo' => $item->InvoiceFormNo,
                        'InvoiceSecurityCode' => $item->InvoiceSecurityCode,
                        'InvoiceLookupAddress' => $item->InvoiceLookupAddress,
                        'Quantity' => $item->Quantity,
                        'FCUnitPrice' => $item->FCUnitPrice,
                        'LCUnitPrice' => $item->LCUnitPrice,
                        'EmployeeID' => $item->EmployeeID,
                        'EmployeeNo' => $item->EmployeeNo,
                        'EmployeeName' => $item->EmployeeName,
                        'CustomerID' => $item->CustomerID,
                        'CustomerNo' => $item->CustomerNo,
                        'CustomerName' => $item->CustomerName,
                        'VendorID' => $item->VendorID,
                        'VendorNo' => $item->VendorNo,
                        'VendorName' => $item->VendorName,
                        'PartnerID' => $item->PartnerID,
                        'PartnerNo' => $item->PartnerNo,
                        'FullName' => $item->FullName,
                        'RevenueID' => $item->RevenueID,
                        'RevenueNo' => $item->RevenueNo,
                        'RevenueName' => $item->RevenueName,
                        'ExpenseID' => $item->ExpenseID,
                        'ExpenseNo' => $item->ExpenseNo,
                        'ExpenseName' => $item->ExpenseName,
                        'FundID' => $item->FundID,
                        'FundNo' => $item->FundNo,
                        'FundName' => $item->FundName,
                        'CapitalID' => $item->CapitalID,
                        'CapitalNo' => $item->CapitalNo,
                        'CapitalName' => $item->CapitalName,
                        'FiscalPeriod' => $item->FiscalPeriod,
                        'FiscalYear' => $item->FiscalYear,
                        'SbiChapterID' => $item->SbiChapterID,
                        'SbiChapterNo' => $item->SbiChapterNo,
                        'SbiChapterName' => $item->SbiChapterName,
                        'SbiCategoryID' => $item->SbiCategoryID,
                        'SbiCategoryNo' => $item->SbiCategoryNo,
                        'SbiCategoryName' => $item->SbiCategoryName,
                        'SbiItemID' => $item->SbiItemID,
                        'SbiItemNo' => $item->SbiItemNo,
                        'SbiItemName' => $item->SbiItemName,
                        'ProjectID' => $item->ProjectID,
                        'ProjectNo' => $item->ProjectNo,
                        'TabmisNo' => $item->TabmisNo,
                        'ProjectName' => $item->ProjectName,
                        'ContractID' => $item->ContractID,
                        'ContractNo' => $item->ContractNo,
                        'ContractName' => $item->ContractName,
                        'FixedAssetID' => $item->FixedAssetID,
                        'FixedAssetNo' => $item->FixedAssetNo,
                        'FixedAssetName' => $item->FixedAssetName,
                        'UomID' => isset($item->UomID) ? $item->UomID : '',
                        'UomNo' => isset($item->UomNo) ? $item->UomNo : '',
                        'UomName' => isset($item->UomName) ? $item->UomName : '',
                        'ToolID' => $item->ToolID,
                        'ToolNo' => $item->ToolNo,
                        'ToolName' => $item->ToolName,
                        'InvestAssetID' => $item->InvestAssetID,
                        'InvestAssetNo' => $item->InvestAssetNo,
                        'InvestAssetName' => $item->InvestAssetName,
                        'ItemID' => $item->ItemID,
                        'ItemNo' => $item->ItemNo,
                        'ItemName' => $item->ItemName,
                        'BudgetLevel' => $item->BudgetLevel,
                        'ProgramType' => $item->ProgramType,
                        'ProgramID' => $item->ProgramID,
                        'ProgramNo' => $item->ProgramNo,
                        'ProgramName' => $item->ProgramName,
                        'SectorID' => $item->SectorID,
                        'SectorNo' => $item->SectorNo,
                        'SectorName' => $item->SectorName,
                        'SbrSectorID' => isset($item->SbrSectorID) ? $item->SbrSectorID : '',
                        'SbrSectorNo' => isset($item->SbrSectorNo) ? $item->SbrSectorNo : '',
                        'SbrSectorName' => isset($item->SbrSectorName) ? $item->SbrSectorName : '',
                        'NormTableID' => $item->NormTableID,
                        'NormTableItemID' => isset($item->NormTableItemID) ? $item->NormTableItemID : '',
                        'NormTableItemNo' => isset($item->NormTableItemNo) ? $item->NormTableItemNo : '',
                        'NormTableItemName' => isset($item->Description) ? $item->Description : '',
                        'FCNormAmount' => isset($item->FCNormAmount) ? $item->FCNormAmount : '0',
                        'LCNormAmount' => isset($item->LCNormAmount) ? $item->LCNormAmount : '0',
                        'NormID' => isset($item->NormID) ? $item->NormID : '',
                        'NormNo' => isset($item->NormNo) ? $item->NormNo : '',
                        'NormName' => isset($item->NormName) ? $item->NormName : '',
                        'NormAllotID' => isset($item->NormAllotID) ? $item->NormAllotID : '',
                        'NormAllotNo' => isset($item->NormAllotNo) ? $item->NormAllotNo : '',
                        'NormAllotName' => isset($item->NormAllotName) ? $item->NormAllotName : '',
                        'NormAllotILevelID' => isset($item->NormAllotILevelID) ? $item->NormAllotILevelID : '',
                        'NormAllotLevelNo' => isset($item->NormAllotLevelNo) ? $item->NormAllotLevelNo : '',
                        'NormAllotLevelName' => isset($item->NormAllotLevelName) ? $item->NormAllotLevelName : '',
                        'BudgetAllocTypeID' => $item->BudgetAllocTypeID,
                        'ReceiveBy' => $item->ReceiveBy,
                        'LineIDTmp' => $item->LineID,
                        'isCCAccount' => 1,
                    ];
                    //DB::table('act_gvouc_trans_item')->where('LineID', $LineID)->update(['CoAccountNo' => '']);
                    DB::table('act_gvouc_trans_item')->where('LineID', $LineID)->update(['isCCAccount' => 1]);
                }
                if ($FCCreditAmount) {
                    $arrInsert2[] = [
                        'TransID' => $TransID,
                        'AccountNo' => $item->CoAccountNo,
                        'AccountName' => isset($item->CoAccountName) ? $item->CoAccountName : '',
                        'CoAccountNo' => $item->AccountNo,
                        'CoAccountName' => isset($item->AccountName) ? $item->AccountName : '',
                        'FCDebitAmount' => $item->FCCreditAmount,
                        'AutoactID' => $item->AutoactID,
                        'Description' => $item->Description,
                        'CcyID' => $item->CcyID,
                        'CcyNo' => $item->CcyNo,
                        'CcyName' => $item->CcyName,
                        'ExchangeRate' => $item->ExchangeRate,
                        'FCAmount' => $item->FCAmount,
                        'LCAmount' => $item->LCAmount,
                        'InTransTypeID' => $item->InTransTypeID,
                        'InTransTypeName' => $item->InTransTypeName,
                        'InvoiceSerialNo' => $item->InvoiceSerialNo,
                        'InvoiceFormNo' => $item->InvoiceFormNo,
                        'InvoiceSecurityCode' => $item->InvoiceSecurityCode,
                        'InvoiceLookupAddress' => $item->InvoiceLookupAddress,
                        'Quantity' => $item->Quantity,
                        'FCUnitPrice' => $item->FCUnitPrice,
                        'LCUnitPrice' => $item->LCUnitPrice,
                        'EmployeeID' => $item->EmployeeID,
                        'EmployeeNo' => $item->EmployeeNo,
                        'EmployeeName' => $item->EmployeeName,
                        'CustomerID' => $item->CustomerID,
                        'CustomerNo' => $item->CustomerNo,
                        'CustomerName' => $item->CustomerName,
                        'VendorID' => $item->VendorID,
                        'VendorNo' => $item->VendorNo,
                        'VendorName' => $item->VendorName,
                        'PartnerID' => $item->PartnerID,
                        'PartnerNo' => $item->PartnerNo,
                        'FullName' => $item->FullName,
                        'RevenueID' => $item->RevenueID,
                        'RevenueNo' => $item->RevenueNo,
                        'RevenueName' => $item->RevenueName,
                        'ExpenseID' => $item->ExpenseID,
                        'ExpenseNo' => $item->ExpenseNo,
                        'ExpenseName' => $item->ExpenseName,
                        'FundID' => $item->FundID,
                        'FundNo' => $item->FundNo,
                        'FundName' => $item->FundName,
                        'CapitalID' => $item->CapitalID,
                        'CapitalNo' => $item->CapitalNo,
                        'CapitalName' => $item->CapitalName,
                        'FiscalPeriod' => $item->FiscalPeriod,
                        'FiscalYear' => $item->FiscalYear,
                        'SbiChapterID' => $item->SbiChapterID,
                        'SbiChapterNo' => $item->SbiChapterNo,
                        'SbiChapterName' => $item->SbiChapterName,
                        'SbiCategoryID' => $item->SbiCategoryID,
                        'SbiCategoryNo' => $item->SbiCategoryNo,
                        'SbiCategoryName' => $item->SbiCategoryName,
                        'SbiItemID' => $item->SbiItemID,
                        'SbiItemNo' => $item->SbiItemNo,
                        'SbiItemName' => $item->SbiItemName,
                        'ProjectID' => $item->ProjectID,
                        'ProjectNo' => $item->ProjectNo,
                        'TabmisNo' => $item->TabmisNo,
                        'ProjectName' => $item->ProjectName,
                        'ContractID' => $item->ContractID,
                        'ContractNo' => $item->ContractNo,
                        'ContractName' => $item->ContractName,
                        'FixedAssetID' => $item->FixedAssetID,
                        'FixedAssetNo' => $item->FixedAssetNo,
                        'FixedAssetName' => $item->FixedAssetName,
                        'UomID' => isset($item->UomID) ? $item->UomID : '',
                        'UomNo' => isset($item->UomNo) ? $item->UomNo : '',
                        'UomName' => isset($item->UomName) ? $item->UomName : '',
                        'ToolID' => $item->ToolID,
                        'ToolNo' => $item->ToolNo,
                        'ToolName' => $item->ToolName,
                        'InvestAssetID' => $item->InvestAssetID,
                        'InvestAssetNo' => $item->InvestAssetNo,
                        'InvestAssetName' => $item->InvestAssetName,
                        'ItemID' => $item->ItemID,
                        'ItemNo' => $item->ItemNo,
                        'ItemName' => $item->ItemName,
                        'BudgetLevel' => $item->BudgetLevel,
                        'ProgramType' => $item->ProgramType,
                        'ProgramID' => $item->ProgramID,
                        'ProgramNo' => $item->ProgramNo,
                        'ProgramName' => $item->ProgramName,
                        'SectorID' => $item->SectorID,
                        'SectorNo' => $item->SectorNo,
                        'SectorName' => $item->SectorName,
                        'SbrSectorID' => isset($item->SbrSectorID) ? $item->SbrSectorID : '',
                        'SbrSectorNo' => isset($item->SbrSectorNo) ? $item->SbrSectorNo : '',
                        'SbrSectorName' => isset($item->SbrSectorName) ? $item->SbrSectorName : '',
                        'NormTableID' => $item->NormTableID,
                        'NormTableItemID' => isset($item->NormTableItemID) ? $item->NormTableItemID : '',
                        'NormTableItemNo' => isset($item->NormTableItemNo) ? $item->NormTableItemNo : '',
                        'NormTableItemName' => isset($item->Description) ? $item->Description : '',
                        'FCNormAmount' => isset($item->FCNormAmount) ? $item->FCNormAmount : '0',
                        'LCNormAmount' => isset($item->LCNormAmount) ? $item->LCNormAmount : '0',
                        'NormID' => isset($item->NormID) ? $item->NormID : '',
                        'NormNo' => isset($item->NormNo) ? $item->NormNo : '',
                        'NormName' => isset($item->NormName) ? $item->NormName : '',
                        'NormAllotID' => isset($item->NormAllotID) ? $item->NormAllotID : '',
                        'NormAllotNo' => isset($item->NormAllotNo) ? $item->NormAllotNo : '',
                        'NormAllotName' => isset($item->NormAllotName) ? $item->NormAllotName : '',
                        'NormAllotILevelID' => isset($item->NormAllotILevelID) ? $item->NormAllotILevelID : '',
                        'NormAllotLevelNo' => isset($item->NormAllotLevelNo) ? $item->NormAllotLevelNo : '',
                        'NormAllotLevelName' => isset($item->NormAllotLevelName) ? $item->NormAllotLevelName : '',
                        'BudgetAllocTypeID' => $item->BudgetAllocTypeID,
                        'ReceiveBy' => $item->ReceiveBy,
                        'LineIDTmp' => $item->LineID,
                        'isCCAccount' => 1,
                    ];
                    //DB::table('act_gvouc_trans_item')->where('LineID', $LineID)->update(['AccountNo' => '']);
                    DB::table('act_gvouc_trans_item')->where('LineID', $LineID)->update(['isCCAccount' => 1]);
                }

                //DB::table('act_gvouc_trans_item')->where('LineID', $LineID)->delete();
            }
            if (!empty($arrInsert1)) {
                DB::table('act_gvouc_trans_item')->insert($arrInsert1);
            }

            if (!empty($arrInsert2)) {
                DB::table('act_gvouc_trans_item')->insert($arrInsert2);
            }
        }
        ///
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = null;

        return json_encode($json);
    }

    public function delete($id){
        SbpmakeplanBook::where('TransID', $id)->delete();
        AccountingTrans::where('TransID', $id)->delete();
        $data = Accounting::find($id);
        if($data){
            $data->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        SbpmakeplanBook::whereIn('TransID', $IDs)->delete();
        AccountingTrans::whereIn('TransID', $IDs)->delete();
        Accounting::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
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
        $isAdjustTrans = $request->post('isAdjustTrans');
        $detail = $request->detail;
        $arrInsert = [];
        if($Posted==1){
            AccountingBook::where('TransID', $TransID)->delete();
            Accounting::where(['TransID' => $TransID])->update(['Posted' => 0]);
        }else {
            $PostedDate = date('Y-m-d H:i', time());
            Accounting::where(['TransID' => $TransID])->update(['Posted' => 1,'PostedDate' => $PostedDate, 'PostedUserID' => Auth::user()->UserID]);
            foreach ($detail as $item) {
                $item = (object)$item;
                if (!empty($PostType) && $item->Detail==1) {
                    if($item->CoAccountNo){
                        $PostType = 2;
                        $item->FCAmount = - $item->FCCreditAmount;
                        $item->LCAmount = - $item->LCCreditAmount;
                    }
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
                        'isAdjustTrans' => $isAdjustTrans,
                        'AccountID' => isset($item->AccountID) ? $item->AccountID : '',
                        'AccountNo' => isset($item->AccountNo) ? $item->AccountNo : '',
                        'CoAccountID' => isset($item->CoAccountID) ? $item->CoAccountID : '',
                        'CoAccountNo' => isset($item->CoAccountNo) ? $item->CoAccountNo : '',
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
                        'UomID' => isset($item->UomID) ? $item->UomID : '',
                        'UomNo' => isset($item->UomNo) ? $item->UomNo : '',
                        'UomName' => isset($item->UomName) ? $item->UomName : '',
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
                        'SbrSectorID' => isset($item->SbrSectorID) ? $item->SbrSectorID : '',
                        'SbrSectorNo' => isset($item->SbrSectorNo) ? $item->SbrSectorNo : '',
                        'SbrSectorName' => isset($item->SbrSectorName) ? $item->SbrSectorName : '',
                        'NormTableID' => $item->NormTableID,
                        'NormTableItemID' => isset($item->NormTableItemID) ? $item->NormTableItemID : '',
                        'NormTableItemNo' => isset($item->NormTableItemNo) ? $item->NormTableItemNo : '',
                        'NormTableItemName' => isset($item->Description) ? $item->Description : '',
                        'FCNormAmount' => isset($item->FCNormAmount) ? $item->FCNormAmount : '0',
                        'LCNormAmount' => isset($item->LCNormAmount) ? $item->LCNormAmount : '0',
                        'NormID' => isset($item->NormID) ? $item->NormID : '',
                        'NormNo' => isset($item->NormNo) ? $item->NormNo : '',
                        'NormName' => isset($item->NormName) ? $item->NormName : '',
                        'NormAllotID' => isset($item->NormAllotID) ? $item->NormAllotID : '',
                        'NormAllotNo' => isset($item->NormAllotNo) ? $item->NormAllotNo : '',
                        'NormAllotName' => isset($item->NormAllotName) ? $item->NormAllotName : '',
                        'NormAllotILevelID' => isset($item->NormAllotILevelID) ? $item->NormAllotILevelID : '',
                        'NormAllotLevelNo' => isset($item->NormAllotLevelNo) ? $item->NormAllotLevelNo : '',
                        'NormAllotLevelName' => isset($item->NormAllotLevelName) ? $item->NormAllotLevelName : '',
                        'BudgetAllocTypeID' => isset($item->BudgetAllocTypeID) ? $item->BudgetAllocTypeID : '',
                        'ReceiveBy' => isset($item->ReceiveBy) ? $item->ReceiveBy : '',
                        'InputCompanyID' => $InputCompanyID,
                        'InputCompanyNo' => Auth::user()->CompanyNo,
                        'InputCompanyName' => Auth::user()->CompanyName,
                        'PathCompanyNo' => Auth::user()->PathCompanyNo,
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

}
