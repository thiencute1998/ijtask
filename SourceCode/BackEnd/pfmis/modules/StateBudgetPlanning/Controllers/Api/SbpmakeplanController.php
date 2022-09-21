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
use Module\Accounting\Models\AccountingAutoact;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaType;
use Module\Listing\Models\NormTable;
use Module\Listing\Models\NormTableItem;
use Module\Listing\Models\SysAutoNumber;
use Module\Sbpestimateplan\Models\Sbpestimateplan;
use Module\Sbpmakeplan\Models\Actautoact;
use Module\Sbpmakeplan\Models\Sbpmakeplan;
use Module\Sbpmakeplan\Models\SbpmakeplanTrans;
use Module\Sbpmakeplan\Models\SbpmakeplanBook;
use Module\StateBudgetPlanning\Models\Sysperiod;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysStatusItem;
use Module\SysAdmin\Models\SysSetup;

class SbpmakeplanController extends Controller{

    public function list(Request $request){
        $data = Sbpmakeplan::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('act_gvouc_trans', ['select' => ['TransNo', 'TransDate'], 'conditions' => []]), 'Sbpmakeplan.xlsx');
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
        $NormTable = NormTable::where('Inactive','!=',1)->get();
        $Sysperiod = Sysperiod::all();
        $ArrAutoact = AccountingAutoact::where('Inactive', '=', 0)->orderBy('AutoactType', 'asc')->orderBy('Norder', 'asc')->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        $json['data']['Status'] = $Status;
        $json['data']['StatusItem'] = $StatusItem;
        $json['data']['ArrCoatype'] = $ArrCoatype;
        $json['data']['NormTable'] = $NormTable;
        $json['data']['Sysperiod'] = $Sysperiod;
        $json['data']['ArrAutoact'] = $ArrAutoact;
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
            ])->first();
            $StatusDescription = $SysStatusItem->StatusDescription;
        }

        $itemp = new Sbpmakeplan([
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
            'ParentCompanyID' => $master->ParentCompanyID,
            'ParentCompanyNo' => $master->ParentCompanyNo,
            'ParentCompanyMOFNo' => $master->ParentCompanyMOFNo,
            'ParentCompanyLocationNo' => $master->ParentCompanyLocationNo,
            'ParentCompanyName' => $master->ParentCompanyName,
            'ParentCompanyAddress' => $master->ParentCompanyAddress,
            'ParentCompanyBankAccount' => $master->ParentCompanyBankAccount,
            'ParentCompanyBankName' => $master->ParentCompanyBankName,
            'ParentCompanyContactName' => $master->ParentCompanyContactName,
            'ParentCompanyContactPosition' => $master->ParentCompanyContactPosition,
            'NormTableID' => $master->NormTableID,
            'NormTableNo' => $master->NormTableNo,
            'NormTableName' => $master->NormTableName,
            'DirectionID' => $master->DirectionID,
            'DirectionNo' => $master->DirectionNo,
            'DirectionName' => $master->DirectionName,
            'isFinalTrans' => $master->isFinalTrans,
            'isAdjustTrans' => $master->isAdjustTrans,
            'CompanyContactID' => $master->CompanyContactID,
            'CompanyContactNo' => $master->CompanyContactNo,
            'ParentCompanyContactID' => $master->ParentCompanyContactID,
            'ParentCompanyContactNo' => $master->ParentCompanyContactNo,

            'Locked' => $master->Locked,
            'LockedDate' => $master->LockedDate,
            'LockedUserID' => $master->LockedUserID,
            'StatusID' => $master->StatusID,
            'StatusValue' => $master->StatusValue,
            'StatusDescription' => $StatusDescription,
            'ArrCoaChecked'=> json_encode($master->ArrCoaChecked),
            'isDebtTrans' => $master->isDebtTrans,
            'InputCompanyID' => Auth::user()->CompanyID,
            'InputCompanyNo' => Auth::user()->CompanyNo,
            'InputCompanyName' => Auth::user()->CompanyName,
            'DFID' => $master->DFID,
            'DFKey' => $master->DFKey,
            'WFItemName' => $master->WFItemName,
            'WFItemID' => $master->WFItemID,
            'WFName'=> $master->WFName,
            'WFID' => $master->WFID
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
                    'UomID' => isset($item->UomID) ? $item->UomID : '',
                    'UomNo' => isset($item->UomNo) ? $item->UomNo : '',
                    'UomName' => isset($item->UomName) ? $item->UomName : '',
                    'FCDebitAmount1' => isset($item->FCDebitAmount1) ? $item->FCDebitAmount1 : '0',
                    'LCDebitAmount1' => isset($item->LCDebitAmount1) ? $item->LCDebitAmount1 : '0',
                    'FCDebitAmount2' => isset($item->FCDebitAmount2) ? $item->FCDebitAmount2 : '0',
                    'LCDebitAmount2' => isset($item->LCDebitAmount2) ? $item->LCDebitAmount2 : '0',
                    'FCDebitAmount3' => isset($item->FCDebitAmount3) ? $item->FCDebitAmount3 : '0',
                    'LCDebitAmount3' => isset($item->LCDebitAmount3) ? $item->LCDebitAmount3 : '0',
                    'FCDebitAmount4' => isset($item->FCDebitAmount4) ? $item->FCDebitAmount4 : '0',
                    'LCDebitAmount4' => isset($item->LCDebitAmount4) ? $item->LCDebitAmount4 : '0',
                    'FCDebitAmount5' => isset($item->FCDebitAmount5) ? $item->FCDebitAmount5 : '0',
                    'LCDebitAmount5' => isset($item->LCDebitAmount5) ? $item->LCDebitAmount5 : '0',

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
                    'TransTypeID' => $master->TransTypeID,
                    'FCEstimatedAmount' => isset($item->FCEstimatedAmount) ? $item->FCEstimatedAmount : '0',
                    'LCEstimatedAmount' => isset($item->LCEstimatedAmount) ? $item->LCEstimatedAmount : '0',
                    'BudgetAllocTypeID' => isset($item->BudgetAllocTypeID) ? $item->BudgetAllocTypeID : '',
                    'ReceiveBy' => isset($item->ReceiveBy) ? $item->ReceiveBy : '',
                    'Detail' => $item->Detail,
                    'Level' => $item->Level,
                    'ParentID' => (!empty($item->ParentID)) ? $item->ParentID : null,
                    'LineIDTmp' => $item->LineID
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
        ///
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $itemp->TransID;

        return json_encode($json);
    }

    public function view($id){
        $data = Sbpmakeplan::find($id);
        $SbpmakeplanTrans = SbpmakeplanTrans::where(['TransID' => $id])->get();
        $Actautoact = Actautoact::all();
        $data->TransNo = $data->TransNo;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['SbpmakeplanTrans'] = $SbpmakeplanTrans;
        $json['data']['Actautoact'] = $Actautoact;
        return json_encode($json);
    }

    public function edit($id){
        $data = Sbpmakeplan::find($id);
        $SbpmakeplanTrans = SbpmakeplanTrans::with('actautoact')->where('TransID',$id)->get();
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
        $json['data']['SbpmakeplanTrans'] = $SbpmakeplanTrans;
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

        $data = Sbpmakeplan::search($request);
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
        $master = Sbpmakeplan::find($id);
        $detail = SbpmakeplanTrans::where('TransID', '=', $id)->get();

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
        $Sbpmakeplan = Sbpmakeplan::find($id);
        if(!$Sbpmakeplan){
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($Sbpmakeplan->Prefix.$Sbpmakeplan->TransNo.$Sbpmakeplan->Suffix != $master->TransNo){
            $validator = Validator::make($request->all(), [
                'TransNo' => 'required|unique:act_gvouc_trans'
            ],
                [
                    'TransNo.required' => 'Số CTG không được để trống',
                    'TransNo.unique' => 'Số CTG đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'act_gvouc_trans')->first();
            AutoNo::UpdateValidateIDNO($validator, $request, $Sbpmakeplan,'act_gvouc_trans', $autoModel, 'chứng từ chung','TransNo', 'TransID');

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $Sbpmakeplan->TransNo = $request->TransNo;
        }
        $Sbpmakeplan->TransDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->TransDate)));
        $Sbpmakeplan->PostDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PostDate)));
        $Sbpmakeplan->eTransDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->eTransDate)));
        $Sbpmakeplan->TransNo = $master->TransNo;
        $Sbpmakeplan->eTransNo = $master->eTransNo;
        //$Sbpmakeplan->TransTypeID = $master->TransTypeID;
        $Sbpmakeplan->TransTypeName = $master->TransTypeName;
        $Sbpmakeplan->CoaTypeID = $master->CoaTypeID;
        $Sbpmakeplan->CoaTypeNo = $master->CoaTypeNo;
        $Sbpmakeplan->CoaTypeName = $master->CoaTypeName;
        $Sbpmakeplan->PeriodID = $master->PeriodID;
        $Sbpmakeplan->PeriodType = $master->PeriodType;
        $Sbpmakeplan->PeriodName = $master->PeriodName;
        $Sbpmakeplan->PeriodFromDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PeriodFromDate)));
        $Sbpmakeplan->PeriodToDate = date('Y-m-d H:i', strtotime(str_replace('/', '-', $master->PeriodToDate)));
        $Sbpmakeplan->Comment = $master->Comment;
        $Sbpmakeplan->FCTotalAmount = $master->FCTotalAmount;
        $Sbpmakeplan->LCTotalAmount = $master->LCTotalAmount;
        $Sbpmakeplan->FCTotalTaxAmount = $master->FCTotalTaxAmount;
        $Sbpmakeplan->LCTotalTaxAmount = $master->LCTotalTaxAmount;
        $Sbpmakeplan->CompanyID = $master->CompanyID;
        $Sbpmakeplan->CompanyNo = $master->CompanyNo;
        $Sbpmakeplan->CompanyMOFNo = $master->CompanyMOFNo;
        $Sbpmakeplan->CompanyLocationNo = $master->CompanyLocationNo;
        $Sbpmakeplan->CompanyName = $master->CompanyName;
        $Sbpmakeplan->CompanyAddress = $master->CompanyAddress;
        $Sbpmakeplan->CompanyBankAccount = $master->CompanyBankAccount;
        $Sbpmakeplan->CompanyBankName = $master->CompanyBankName;
        $Sbpmakeplan->CompanyContactName = $master->CompanyContactName;
        $Sbpmakeplan->CompanyContactPosition = $master->CompanyContactPosition;
        $Sbpmakeplan->ParentCompanyID = $master->ParentCompanyID;
        $Sbpmakeplan->ParentCompanyNo = $master->ParentCompanyNo;
        $Sbpmakeplan->ParentCompanyMOFNo = $master->ParentCompanyMOFNo;
        $Sbpmakeplan->ParentCompanyLocationNo = $master->ParentCompanyLocationNo;
        $Sbpmakeplan->ParentCompanyName = $master->ParentCompanyName;
        $Sbpmakeplan->ParentCompanyAddress = $master->ParentCompanyAddress;
        $Sbpmakeplan->ParentCompanyBankAccount = $master->ParentCompanyBankAccount;
        $Sbpmakeplan->ParentCompanyBankName = $master->ParentCompanyBankName;
        $Sbpmakeplan->ParentCompanyContactName = $master->ParentCompanyContactName;
        $Sbpmakeplan->ParentCompanyContactPosition = $master->ParentCompanyContactPosition;
        $Sbpmakeplan->NormTableID = $master->NormTableID;
        $Sbpmakeplan->NormTableNo = $master->NormTableNo;
        $Sbpmakeplan->NormTableName = $master->NormTableName;
        $Sbpmakeplan->DirectionID = $master->DirectionID;
        $Sbpmakeplan->DirectionNo = $master->DirectionNo;
        $Sbpmakeplan->DirectionName = $master->DirectionName;
        $Sbpmakeplan->isFinalTrans = $master->isFinalTrans;
        $Sbpmakeplan->isAdjustTrans = $master->isAdjustTrans;
        $Sbpmakeplan->CompanyContactID = $master->CompanyContactID;
        $Sbpmakeplan->CompanyContactNo = $master->CompanyContactNo;
        $Sbpmakeplan->ParentCompanyContactID = $master->ParentCompanyContactID;
        $Sbpmakeplan->ParentCompanyContactNo = $master->ParentCompanyContactNo;

        $Sbpmakeplan->Locked = $master->Locked;
        $Sbpmakeplan->LockedDate = $master->LockedDate;
        $Sbpmakeplan->LockedUserID = $master->LockedUserID;
        $Sbpmakeplan->StatusID = $master->StatusID;
        $Sbpmakeplan->StatusValue = $master->StatusValue;
        $Sbpmakeplan->StatusDescription = $master->StatusDescription;
        $Sbpmakeplan->ArrCoaChecked = json_encode($master->ArrCoaChecked);
        $Sbpmakeplan->isDebtTrans = $master->isDebtTrans;

        $Sbpmakeplan->save();
        //Xóa Trans Item
        SbpmakeplanTrans::where('TransID', $id)->delete();
        $arrInsert = [];
        $TransID = $Sbpmakeplan->TransID;
        foreach ($detail as $item) {
            $item = (object)$item;
            if (!empty($item->FiscalYear)) {
                $arrInsert[] = [
                    'TransID' => $TransID,
                    'AutoactID' => isset($item->AutoactID) ? $item->AutoactID : '',
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
                    'UomID' => isset($item->UomID) ? $item->UomID : '',
                    'UomNo' => isset($item->UomNo) ? $item->UomNo : '',
                    'UomName' => isset($item->UomName) ? $item->UomName : '',
                    'FCDebitAmount1' => isset($item->FCDebitAmount1) ? $item->FCDebitAmount1 : '0',
                    'LCDebitAmount1' => isset($item->LCDebitAmount1) ? $item->LCDebitAmount1 : '0',
                    'FCDebitAmount2' => isset($item->FCDebitAmount2) ? $item->FCDebitAmount2 : '0',
                    'LCDebitAmount2' => isset($item->LCDebitAmount2) ? $item->LCDebitAmount2 : '0',
                    'FCDebitAmount3' => isset($item->FCDebitAmount3) ? $item->FCDebitAmount3 : '0',
                    'LCDebitAmount3' => isset($item->LCDebitAmount3) ? $item->LCDebitAmount3 : '0',
                    'FCDebitAmount4' => isset($item->FCDebitAmount4) ? $item->FCDebitAmount4 : '0',
                    'LCDebitAmount4' => isset($item->LCDebitAmount4) ? $item->LCDebitAmount4 : '0',
                    'FCDebitAmount5' => isset($item->FCDebitAmount5) ? $item->FCDebitAmount5 : '0',
                    'LCDebitAmount5' => isset($item->LCDebitAmount5) ? $item->LCDebitAmount5 : '0',

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
                    'TransTypeID' => $master->TransTypeID,
                    'FCEstimatedAmount' => isset($item->FCEstimatedAmount) ? $item->FCEstimatedAmount : '0',
                    'LCEstimatedAmount' => isset($item->LCEstimatedAmount) ? $item->LCEstimatedAmount : '0',
                    'BudgetAllocTypeID' => isset($item->BudgetAllocTypeID) ? $item->BudgetAllocTypeID : '',
                    'ReceiveBy' => isset($item->ReceiveBy) ? $item->ReceiveBy : '',
                    'Detail' => $item->Detail,
                    'Level' => $item->Level,
                    'ParentID' => (!empty($item->ParentID)) ? $item->ParentID : null,
                    'LineIDTmp' => $item->LineID
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

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = null;

        return json_encode($json);
    }

    public function delete($id){
        SbpmakeplanBook::where('TransID', $id)->delete();
        SbpmakeplanTrans::where('TransID', $id)->delete();
        $data = Sbpmakeplan::find($id);
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
        SbpmakeplanTrans::whereIn('TransID', $IDs)->delete();
        Sbpmakeplan::destroy($IDs);
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
        $DirectionID = $request->post('DirectionID');
        $DirectionNo = $request->post('DirectionNo');
        $DirectionName = $request->post('DirectionName');
        $isDebtTrans = $request->post('isDebtTrans');
        $detail = $request->detail;
        $arrInsert = [];
        if($Posted==1){
            SbpmakeplanBook::where('TransID', $TransID)->delete();
            Sbpmakeplan::where(['TransID' => $TransID])->update(['Posted' => 0]);
        }else {
            $PostedDate = date('Y-m-d H:i', time());
            Sbpmakeplan::where(['TransID' => $TransID])->update(['Posted' => 1,'PostedDate' => $PostedDate, 'PostedUserID' => Auth::user()->UserID]);
            foreach ($detail as $item) {
                $item = (object)$item;
                if (!empty($PostType) && $item->Detail==1) {
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
                        'DirectionID' => $DirectionID,
                        'DirectionNo' => $DirectionNo,
                        'DirectionName' => $DirectionName,
                        'InputCompanyID' => $InputCompanyID,
                        'InputCompanyNo' => Auth::user()->CompanyNo,
                        'InputCompanyName' => Auth::user()->CompanyName,
                        'isDebtTrans' => $isDebtTrans,
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
                        'FCNormAmount' => isset($item->FCNormAmount) ? $item->FCNormAmount : '',
                        'LCNormAmount' => isset($item->LCNormAmount) ? $item->LCNormAmount : '',
                        'FCEstimatedAmount' => isset($item->FCEstimatedAmount) ? $item->FCEstimatedAmount : '0',
                        'LCEstimatedAmount' => isset($item->LCEstimatedAmount) ? $item->LCEstimatedAmount : '0',
                        'SectorID' => isset($item->SectorID) ? $item->SectorID : '',
                        'SectorNo' => isset($item->SectorNo) ? $item->SectorNo : '',
                        'SectorName' => isset($item->SectorName) ? $item->SectorName : '',
                        'SbrSectorID' => isset($item->SbrSectorID) ? $item->SbrSectorID : '',
                        'SbrSectorNo' => isset($item->SbrSectorNo) ? $item->SbrSectorNo : '',
                        'SbrSectorName' => isset($item->SbrSectorName) ? $item->SbrSectorName : '',
                        'BudgetAllocTypeID' => isset($item->BudgetAllocTypeID) ? $item->BudgetAllocTypeID : '',
                        'ReceiveBy' => isset($item->ReceiveBy) ? $item->ReceiveBy : '',
                        'NormID' => isset($item->NormID) ? $item->NormID : '',
                        'NormNo' => isset($item->NormNo) ? $item->NormNo : '',
                        'NormName' => isset($item->NormName) ? $item->NormName : '',
                        'NormTableItemID' => isset($item->NormTableItemID) ? $item->NormTableItemID : '',
                        'NormTableItemNo' => isset($item->NormTableItemNo) ? $item->NormTableItemNo : '',
                        'NormTableItemName' => isset($item->NormTableItemName) ? $item->NormTableItemName : '',
                        'NormAllotID' => isset($item->NormAllotID) ? $item->NormAllotID : '',
                        'NormAllotNo' => isset($item->NormAllotNo) ? $item->NormAllotNo : '',
                        'NormAllotName' => isset($item->NormAllotName) ? $item->NormAllotName : '',
                        'NormAllotILevelID' => isset($item->NormAllotILevelID) ? $item->NormAllotILevelID : '',
                        'NormAllotLevelNo' => isset($item->NormAllotLevelNo) ? $item->NormAllotLevelNo : '',
                        'NormAllotLevelName' => isset($item->NormAllotLevelName) ? $item->NormAllotLevelName : '',
                        'FCAmount1' => isset($item->FCDebitAmount1) ? $item->FCDebitAmount1 : '0',
                        'LCAmount1' => isset($item->LCDebitAmount1) ? $item->LCDebitAmount1 : '0',
                        'FCAmount2' => isset($item->FCDebitAmount2) ? $item->FCDebitAmount2 : '0',
                        'LCAmount2' => isset($item->LCDebitAmount2) ? $item->LCDebitAmount2 : '0',
                        'FCAmount3' => isset($item->FCDebitAmount3) ? $item->FCDebitAmount3 : '0',
                        'LCAmount3' => isset($item->LCDebitAmount3) ? $item->LCDebitAmount3 : '0',
                        'FCAmount4' => isset($item->FCDebitAmount4) ? $item->FCDebitAmount4 : '0',
                        'LCAmount4' => isset($item->LCDebitAmount4) ? $item->LCDebitAmount4 : '0',
                        'FCAmount5' => isset($item->FCDebitAmount5) ? $item->FCDebitAmount5 : '0',
                        'LCAmount5' => isset($item->LCDebitAmount5) ? $item->LCDebitAmount5 : '0',
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
    //Định mức doanh thu
    public function getNormTableItem(Request $request){
        $NormTableID = $request->post('NormTableID');
        $NormTableItem = NormTableItem::where(['NormTableID' => $NormTableID, 'Detail' => 1])->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['NormTableItem'] = $NormTableItem;
        return json_encode($json);
    }

    //Lấy dữ liệu từ ước thực hiện dự toán
    public function getList(Request $request){
        $data = null;
        $query = Sbpestimateplan::query();
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
        //Update chứng từ đã thêm từ chứng từ lập dự toán
        Sbpestimateplan::where(['TransID' => $id])->update(['RequestRepID' => 1]);
        $data = Sbpestimateplan::find($id);
        $SbpmakeplanTrans = SbpmakeplanTrans::with('actautoact')->where('TransID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['SbpmakeplanTrans'] = $SbpmakeplanTrans;
        return json_encode($json);
    }

}
