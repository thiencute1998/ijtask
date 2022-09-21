<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use IjCore\IjDate;
use IjCore\IjFile;
use IjCore\IjString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Customer\Models\CustomerPer;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Company;
use Module\Listing\Models\CustomerCateValue;
use Module\Listing\Models\CustomerVideo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\EmployeeManager;
use Module\Listing\Models\SysAutoNumber;
use Module\Customer\Models\Customer;
use Module\Listing\Models\CustomerCate;
use Module\Listing\Models\CustomerCateList;
use Module\Customer\Models\CustomerContact;
use Module\Customer\Models\CustomerLink;
use Module\Customer\Models\CustomerSalesTrans;
use Module\Customer\Models\CustomerSalesTransCate;
use Module\Customer\Models\CustomerContractTrans;
use Module\Customer\Models\CustomerContractTransCate;
use Module\Customer\Models\CustomerQuotationTrans;
use Module\Customer\Models\CustomerQuotationtransCate;
use Module\Customer\Models\CustomerQuotationTransItem;
use Module\Customer\Models\CustomerSupportTrans;
use Module\Customer\Models\CustomerSupportTransCate;
use Module\Listing\Models\Uom;
use Module\SysAdmin\Models\SysStatusItem;
use Module\SysAdmin\Models\SysTable;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Task\Models\FileModel;
use Module\Customer\Models\CustomerFile;

class CustomerListController extends Controller{

    public function list(Request $request){
        $data = Customer::search($request);
        $json['status'] = 1;
        $json['data'] = $data;
        $json['msg'] = 'Thành công!';
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('company', ['select' => ['CustomerNo', 'CustomerName'], 'conditions' => []]), 'khachhang.xlsx');
    }

    public function create(){
        if(old('CustomerNo')){
            $auto = old('CustomerNo');
        }else{
            $auto = AutoNo::genNewNumberNo('customer');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $detail = $request->detail;
        $auth = Auth::user();
        $validator = Validator::make($request->all(), [
            'CustomerNo' => 'required|unique:customer',
            'Email'=>'required|email',
            'OfficePhone' => 'required',
        ],
            [
                'CustomerNo.required' => 'Mã khách hàng không được để trống',
                'CustomerNo.unique' => 'Mã khách hàng đã tồn tại',
                'Email.required'=>  'Email không được để trống',
                'Email.email' =>    'Email không hợp lệ',
                'OfficePhone.required'=>  'Số điện thoại không được để trống',
            ]);

        $autoModel = SysAutoNumber::where('NumberKey', 'customer')->first();
        AutoNo::storeValidate($validator, $request, 'customer', $autoModel, 'khách hàng');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new Customer([
            'CustomerNo' => $request->CustomerNo,
            'CustomerName' => $request->CustomerName,
            'Address' => $request->Address,
            'BillTo' => $request->BillTo,
            'ShipTo' => $request->ShipTo,
            'TaxCode' => $request->TaxCode,
            'BankAccount' => $request->BankAccount,
            'BankName' => $request->BankName,
            'OfficePhone' => $request->OfficePhone,
            'Fax' => $request->Fax,
            'Email' => $request->Email,
            'Website' => $request->Website,
            'ProvinceID' => $request->ProvinceID,
            'ProvinceName' => $request->ProvinceName,
            'DistrictID' => $request->DistrictID,
            'DistrictName' => $request->DistrictName,
            'CommuneID' => $request->CommuneID,
            'CommuneName' => $request->CommuneName,
            'Note' => $request->Note,
            'IsVendor' => $request->IsVendor,
            'Prefix' => $autoModel->Prefix,
            'Suffix' => $autoModel->Suffix,
            'Inactive' => $request->Inactive,
//            'AuthorizedPerson' => $auth->UserID
        ]);
        $data->save();

        //Thêm vào loại khách hàng
        $arrInsert = [];
        $id = $data->CustomerID;
        foreach ($detail as $item) {
            $item = (object)$item;
            if (!empty($item->CateID)) {
                $arrInsert[] = [
                    'CustomerID' => $id,
                    'CateID' => $item->CateID,
                    'CateValue' => $item->CateValue,
//                    'Description' => $item->Description,
                ];
            }
        }
        if (!empty($arrInsert)) {
            DB::table('customer_cate')->insert($arrInsert);
        }

        // Nguời tạo
        $CustomerPer = new CustomerPer([
            'CustomerID' => $id,
            'EmployeeID' => $auth->EmployeeID,
            'EmployeeName' => $auth->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,

        ]);
        $CustomerPer->save();

        // Người quản lý trực tiếp ~ người giao việc
        $employeeManager = EmployeeManager::where([
            'EmployeeID' => $auth->EmployeeID,
            'IsDirectManager' => 1
        ])->first();
        if ($employeeManager && ($employeeManager->ManagerID !== $auth->EmployeeID)) {
            $CustomerPer = new CustomerPer([
                'CustomerID' => $id,
                'EmployeeID' => $employeeManager->EmployeeID,
                'EmployeeName' => $employeeManager->ManagerName,
                'Access' => 1,
                'AccessField' => 'all',
                'Edit' => 1,
                'EditField' => 'all',
                'Delete' => 1,
                'TypePer' => 2
            ]);
            $CustomerPer->save();
        }


        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');
            $customerPerCopy = CustomerPer::where('CustomerID', '=', $CopyID)
                ->where('EmployeeID', '!=', Auth::user()->EmployeeID)
                ->get();
            $customerPerArrInsert = [];
            foreach ($customerPerCopy as $perCopy) {
                if ($employeeManager && ($employeeManager->EmployeeID == $perCopy->EmployeeID)) {
                    continue;
                }
                $customerPerArrInsert[] = [
                    'CustomerID' => $id,
                    'EmployeeID' => $perCopy->EmployeeID,
                    'EmployeeName' => $perCopy->EmployeeName,
                    'Access' => $perCopy->Access,
                    'AccessField' => $perCopy->AccessField,
                    'Edit' => $perCopy->Edit,
                    'EditField' => $perCopy->EditField,
                    'Delete' => $perCopy->Delete,
                    'TypePer' => $perCopy->TypePer
                ];
            }
            if (count($customerPerArrInsert)) {
                DB::table('customer_per')->insert($customerPerArrInsert);
            }
        }


        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->CustomerID;

        return json_encode($json);
    }

    public function view($id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];
        $data = Customer::find($id);

//        $CustomerContact = CustomerContact::where(['CustomerID' => $id])->get();
        $CustomerLink = CustomerLink::where(['CustomerID' => $id])->get();
//        $CustomerSalesTrans = CustomerSalesTrans::where(['CustomerID' => $id])->get();
        $CustomerCate = CustomerCate::where('CustomerID', $id)->get();
//        $CustomerSalesTransCate = CustomerSalesTransCate::where('CustomerID', $id)->get();
//        $CustomerQuotationTrans = CustomerQuotationTrans::where(['CustomerID' => $id])->get();
//        $CustomerQuotationTransCate = CustomerQuotationTransCate::where('CustomerID', $id)->get();
//        $CustomerContractTrans = CustomerContractTrans::where(['CustomerID' => $id])->get();
//        $CustomerContractTransCate = CustomerContractTransCate::where('CustomerID', $id)->get();
//        $CustomerSupportTrans = CustomerSupportTrans::where(['CustomerID' => $id])->get();
//        $CustomerSupportTransCate = CustomerSupportTransCate::where('CustomerID', $id)->get();
        $data->CustomerNo = $data->Prefix.$data->CustomerNo.$data->Suffix;
        $dataCustomerCateList= CustomerCateList::all();
        //$StatusOption= SysStatus::all();
        $Status = SysStatus::where('Inactive', '!=', 1)->get();
        $StatusItem = SysStatusItem::all();
        $arrCustomerCateList = [];
        foreach ($dataCustomerCateList as $item){
            $arrCustomerCateList[$item->CateID] = $item->CateName;
        }

        $CustomerFile = CustomerFile::where('CustomerID', '=', $id)->get();
        $CustomerVideo = CustomerVideo::where('CustomerID', '=', $id)->get();

        $CustomerPer = CustomerPer::getCustomerPer($id);
        $Employee = Employee::all();
        $Company = Company::all();
        $Group = SysUserGroup::all();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        $json['data']['arrCustomerCateList'] = $arrCustomerCateList;
        $json['data']['CustomerCate'] = $CustomerCate;
//        $json['data']['CustomerContact'] = $CustomerContact;
        $json['data']['CustomerLink'] = $CustomerLink;
//        $json['data']['CustomerSalesTrans'] = $CustomerSalesTrans;
        //$json['data']['StatusOption'] = $StatusOption;
        $json['data']['Status'] = $Status;
        $json['data']['StatusItem'] = $StatusItem;
        $json['CustomerFile'] = $CustomerFile;
        $json['CustomerVideo'] = $CustomerVideo;
//        $json['data']['CustomerSalesTransCate'] = $CustomerSalesTransCate;
//        $json['data']['CustomerQuotationTrans'] = $CustomerQuotationTrans;
//        $json['data']['CustomerQuotationTransCate'] = $CustomerQuotationTransCate;
//        $json['data']['CustomerContractTrans'] = $CustomerContractTrans;
//        $json['data']['CustomerContractTransCate'] = $CustomerContractTransCate;
//        $json['data']['CustomerSupportTrans'] = $CustomerSupportTrans;
//        $json['data']['CustomerSupportTransCate'] = $CustomerSupportTransCate;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['CustomerPer'] = $CustomerPer;
        return json_encode($json);
    }

    public function edit($id){
        $data = Customer::find($id);
        $SysStatus = SysStatus::all();
        $CustomerCate = CustomerCate::where('CustomerID', $id)->get();
//        $CustomerSalesTransCate = CustomerSalesTransCate::where(['CustomerID' => $id])->orderBy('LineID', 'asc')->get();
        if(old('CustomerNo')){
            $data->CustomerNo = old('CustomerNo');
        }else{
            $data->CustomerNo = $data->Prefix.$data->CustomerNo.$data->Suffix;
        }
        $dataCustomerCateList= CustomerCateList::all();
        $arrCustomerCateList = [];
        foreach ($dataCustomerCateList as $item){
            $arrCustomerCateList[$item->CateID] = $item->CateName;
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        $json['data']['CustomerCate'] = $CustomerCate;
        $json['data']['arrCustomerCateList'] = $arrCustomerCateList;
//        $json['data']['CustomerSalesTransCate'] = $CustomerSalesTransCate;
        $json['data']['SysStatus'] = $SysStatus;
        return json_encode($json);
    }

    public function update(Request $request, $id){
        $validate = [];
        $detail = $request->detail;
        $data = Customer::find($id);
        if($data->Prefix.$data->CustomerNo.$data->Suffix != $request->CustomerNo){
            $validator = Validator::make($request->all(), [
                'CustomerNo' => 'required|unique:customer'
            ],
                [
                    'CustomerNo.required' => 'Mã khách hàng không được để trống',
                    'CustomerNo.unique' => 'Mã khách hàng đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'customer')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data,'customer', $autoModel, 'khách hàng');
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            else{
                $data->CustomerNo = $request->CustomerNo;
            }

        }
        //email
        if($data->Prefix.$data->Email.$data->Suffix != $request->Email){
            $validator = Validator::make($request->all(), [
                'Email'=>'required|email',
            ],
                [
                    'Email.required'=>  'Email không được để trống',
                    'Email.email' =>    'Email không hợp lệ',
                ]);

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            else{
                $data->Email = $request->Email;
            }

        }
        //phone
        if($data->Prefix.$data->OfficePhone.$data->Suffix != $request->OfficePhone){
            $validator = Validator::make($request->all(), [
                'OfficePhone' => 'required',
            ],
                [
                    'OfficePhone.required'=>  'Số điện thoại không được để trống',
                ]);

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            else{
                $data->OfficePhone = $request->OfficePhone;
            }

        }
        $data->CustomerName = $request->CustomerName;
        $data->Address = $request->Address;
        $data->BillTo = $request->BillTo;
        $data->ShipTo = $request->ShipTo;
        $data->TaxCode = $request->TaxCode;
        $data->BankAccount = $request->BankAccount;
        $data->BankName = $request->BankName;
        $data->Fax = $request->Fax;
        $data->Website = $request->Website;
        $data->ProvinceID = $request->ProvinceID;
        $data->ProvinceName = $request->ProvinceName;
        $data->DistrictID = $request->DistrictID;
        $data->DistrictName = $request->DistrictName;
        $data->CommuneID = $request->CommuneID;
        $data->CommuneName = $request->CommuneName;
        $data->Note = $request->Note;
        $data->IsVendor = $request->IsVendor;
        $data->Inactive = $request->Inactive;
        $data->save();

        CustomerCate::where('CustomerID', $id)->delete();
        //Thêm vào loại khách hàng
        $arrInsert = [];
        $id = $data->CustomerID;

        foreach ($detail as $item) {
            $item = (object)$item;
            if (!empty($item->CateValue)) {
                $arrInsert[] = [
                    'CustomerID' => $id,
                    'CateID' => $item->CateID,
                    'CateValue' => $item->CateValue,
//                    'Description' => $item->Description,
                ];
            }
        }

        if (!empty($arrInsert)) {
            DB::table('customer_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = null;

        return json_encode($json);
    }

    public function delete($id){
        $data = Customer::find($id);
        CustomerCate::where('CustomerID', $id)->delete();
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
        Customer::destroy($IDs);
        CustomerCate::whereIn('CustomerID', $IDs)->delete();
        CustomerCate::whereIn('CustomerID', $IDs)->delete();
        CustomerCate::whereIn('CustomerID', $IDs)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    //Contact
    public function updateContact(Request $request, $id){
        $ContactArray = $request->post('CustomerContact');
        $arrContact = [];
        foreach ($ContactArray as $item){
            if($item['CustomerName']){
                $arrContact[] = [
                    'CustomerID' => $id,
                    'CustomerName' => $item['CustomerName'],
                    'ContactName' => $item['ContactName'],
                    'PositionName' => $item['PositionName'],
                    'DepartmentName' => $item['DepartmentName'],
                    'OfficePhone' => $item['OfficePhone'],
                    'HandPhone' => $item['HandPhone'],
                    'Email' => $item['Email'],
                    'FacebookID' => $item['FacebookID'],
                    'TiwtterID' => $item['TiwtterID'],
                    'SkypeID' => $item['SkypeID'],
                    'ZaloID' => $item['ZaloID'],
                ];
            }
        }
        CustomerContact::where(['CustomerID' => $id])->delete();
        if(!empty($arrContact)){
            DB::table('customer_contact')->insert($arrContact);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    //Get list Table
    public function getAllTable(){
        $table = SysTable::where(['isTaskLink' => 1])->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $table;
        return json_encode($json);
    }

    //Get list Uom
    public function getAllUom(){
        $data = Uom::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    //Link
    public function updateLink(Request $request, $id){
        $LinkArray = $request->post('CustomerLink');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkName']){
                $arrLink[] = [
                    'CustomerID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        CustomerLink::where(['CustomerID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('customer_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    //Sales Trans
    public function updatesales_old(Request $request, $id){
//        $SalesArray = $request->post('CustomerSalesTrans');
//        $CustomerSalesTransCate = $request->post('CustomerSalesTransCate');
//        $arrSales = [];
//        foreach ($SalesArray as $item){
//            if($item['CustomerName']){
//                $CustomerInfo = 'Tên khách hàng: '.$item['CustomerName'].', '.'Người liên hệ: '.$item['ContactName'].', '.'Bộ phận: '.$item['DepartmentName'].', '.'Chức vụ: '.$item['PositionName'].', '.'Điện thoại: '.$item['OfficePhone'];
//                //return($CustomerInfo);
//                $arrSales[] = [
//                    'CustomerID' => $id,
//                    'TransDate' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $item['TransDate']))),
//                    'TransComment' => $item['TransComment'],
//                    'EmployeeID' => $item['EmployeeID'],
//                    'EmployeeName' => $item['EmployeeName'],
//                    'CustomerName' => $item['CustomerName'],
//                    'ContactName' => $item['ContactName'],
//                    'CustomerInfo' => $CustomerInfo,
//                    'Time' => $item['Time'],
//                    'FileID' => $item['FileID'],
//                    'FileName' => $item['FileName'],
//                    'ItemID' => $item['ItemID'],
//                    'ItemName' => $item['ItemName'],
//                    'CcyID' => $item['CcyID'],
//                    'CcyNo' => $item['CcyNo'],
//                    'ExchangeRate' => $item['ExchangeRate'],
//                    'FCAmount' => $item['FCAmount'],
//                    'LCAmount' => $item['LCAmount'],
//                    'ExpectedEndDate' => date('Y-m-d', strtotime(str_replace('/', '-', $item['ExpectedEndDate']))),
//                    'PercentSuccess' => $item['PercentSuccess'],
//                    'StatusID' => $item['StatusID'],
//                    'StatusDescription' => $item['StatusDescription'],
//                    'CreatedDate' => $item['CreatedDate'],
//                ];
//            }
//        }
//        CustomerSalesTrans::where(['CustomerID' => $id])->delete();
//        if(!empty($arrSales)){
//            DB::table('customer_sales_trans')->insert($arrSales);
//        }
//
//        //CustomerSalesTransCate
//        $CSTCate = CustomerSalesTransCate::select(['TransID', 'CustomerID'])->where('TransID', $id)->get();
//        CustomerSalesTransCate::where('TransID', $id)->delete();
//        $arrInsertCSTCate = [];
//        foreach ($CSTCate as $item) {
//            if (isset($CustomerSalesTransCate[$item->TransID]) && !empty($CustomerSalesTransCate[$item->TransID])) {
//                $TransID = $item->TransID;
//                foreach ($CustomerSalesTransCate[$TransID] as $itemChild) {
//                    $arrInsertCSTCate[] = [
//                        'TransID' => $id,
//                        'CateID' => $itemChild['CateID'],
//                        'CateValue' => $itemChild['CateValue'],
//                    ];
//                }
//            }
//        }
//        if (!empty($arrInsertCSTCate)) {
//            DB::table('customer_sales_trans_cate')->insert($arrInsertCSTCate);
//        }
//
//        $json['status'] = 1;
//        $json['msg'] = 'Thành công!';
//        $json['data'] = [];
//        return json_encode($json);
    }
    public function updateSales(Request $request, $id){
        $CustomerSalesTrans = $request->post('CustomerSalesTrans'); //return($CustomerSalesTrans['ContactID']);
        $CustomerSalesTransCate = $request->post('CustomerSalesTransCate');
        $CustomerSalesTrans['TransDate'] = IjDate::ConvertClientDateToServerDate($CustomerSalesTrans['TransDate']);
        $CustomerSalesTrans['ExpectedEndDate'] = IjDate::ConvertClientDateToServerDate($CustomerSalesTrans['ExpectedEndDate']);
        $SalesTrans = CustomerSalesTrans::where(['CustomerID' => $id, 'TransDate' => $CustomerSalesTrans['TransDate']])->first();
        if(!$SalesTrans){
            if(!isset($CustomerSalesTrans['ContactName'])){$CustomerSalesTrans['ContactName'] = '';}
            if(!isset($CustomerSalesTrans['PositionName'])){$CustomerSalesTrans['PositionName'] = '';}
            if(!isset($CustomerSalesTrans['OfficePhone'])){$CustomerSalesTrans['OfficePhone'] = '';}
            $CustomerSalesTrans['CustomerInfo'] = 'Tên khách hàng: '.$CustomerSalesTrans['CustomerName'].', '.'Người liên hệ: '.$CustomerSalesTrans['ContactName'].', '.'Chức vụ: '.$CustomerSalesTrans['PositionName'].', '.'Điện thoại: '.$CustomerSalesTrans['OfficePhone'];
            $SalesTrans = new CustomerSalesTrans([
                'CustomerID' => $id,
                'TransDate' => (isset($CustomerSalesTrans['TransDate'])) ? $CustomerSalesTrans['TransDate'] : null,
                'TransComment' => (isset($CustomerSalesTrans['TransComment'])) ? $CustomerSalesTrans['TransComment'] : null,
                'EmployeeID' => (isset($CustomerSalesTrans['EmployeeID'])) ? $CustomerSalesTrans['EmployeeID'] : null,
                'EmployeeName' => (isset($CustomerSalesTrans['EmployeeName'])) ? $CustomerSalesTrans['EmployeeName'] : null,
                'CustomerName' => (isset($CustomerSalesTrans['CustomerName'])) ? $CustomerSalesTrans['CustomerName'] : null,
                'ContactID' => (isset($CustomerSalesTrans['ContactID'])) ? $CustomerSalesTrans['ContactID'] : null,
                'ContactName' => (isset($CustomerSalesTrans['ContactName'])) ? $CustomerSalesTrans['ContactName'] : null,
                'CustomerInfo' => (isset($CustomerSalesTrans['CustomerInfo'])) ? $CustomerSalesTrans['CustomerInfo'] : null,
                'Time' => (isset($CustomerSalesTrans['Time'])) ? $CustomerSalesTrans['Time'] : null,
                'FileID' => (isset($CustomerSalesTrans['FileID'])) ? $CustomerSalesTrans['FileID'] : null,
                'FileName' => (isset($CustomerSalesTrans['FileName'])) ? $CustomerSalesTrans['FileName'] : null,
                'ItemID' => (isset($CustomerSalesTrans['ItemID'])) ? $CustomerSalesTrans['ItemID'] : null,
                'ItemName' => (isset($CustomerSalesTrans['ItemName'])) ? $CustomerSalesTrans['ItemName'] : null,
                'ExchangeRate' => (isset($CustomerSalesTrans['ExchangeRate'])) ? $CustomerSalesTrans['ExchangeRate'] : null,
                'FCAmount' => (isset($CustomerSalesTrans['FCAmount'])) ? $CustomerSalesTrans['FCAmount'] : null,
                'LCAmount' => (isset($CustomerSalesTrans['LCAmount'])) ? $CustomerSalesTrans['LCAmount'] : null,
                'ExpectedEndDate' => (isset($CustomerSalesTrans['ExpectedEndDate'])) ? $CustomerSalesTrans['ExpectedEndDate'] : null,
                'PercentSuccess' => (isset($CustomerSalesTrans['PercentSuccess'])) ? $CustomerSalesTrans['PercentSuccess'] : null,
                'StatusID' => (isset($CustomerSalesTrans['StatusID'])) ? $CustomerSalesTrans['StatusID'] : null,
                'StatusValue' => (isset($CustomerSalesTrans['StatusValue'])) ? $CustomerSalesTrans['StatusValue'] : null,
                'StatusName' => (isset($CustomerSalesTrans['StatusName'])) ? $CustomerSalesTrans['StatusName'] : null,
                'StatusDescription' => (isset($CustomerSalesTrans['StatusDescription'])) ? $CustomerSalesTrans['StatusDescription'] : null,
                'CreatedDate' => (isset($CustomerSalesTrans['CreatedDate'])) ? $CustomerSalesTrans['CreatedDate'] : null,
                'CcyID' => (isset($CustomerSalesTrans['CcyID'])) ? $CustomerSalesTrans['CcyID'] : null,
                'CcyName' => (isset($CustomerSalesTrans['CcyName'])) ? $CustomerSalesTrans['CcyName'] : null,
            ]);
        }else{
            $SalesTrans->TransDate = (isset($CustomerSalesTrans['TransDate'])) ? $CustomerSalesTrans['TransDate'] : null;
            $SalesTrans->TransComment = (isset($CustomerSalesTrans['TransComment'])) ? $CustomerSalesTrans['TransComment'] : null;
            $SalesTrans->EmployeeID = (isset($CustomerSalesTrans['EmployeeID'])) ? $CustomerSalesTrans['EmployeeID'] : null;
            $SalesTrans->EmployeeName = (isset($CustomerSalesTrans['EmployeeName'])) ? $CustomerSalesTrans['EmployeeName'] : null;
            $SalesTrans->CustomerName = (isset($CustomerSalesTrans['CustomerName'])) ? $CustomerSalesTrans['CustomerName'] : null;
            $SalesTrans->ContactID =  (isset($CustomerSalesTrans['ContactID'])) ? $CustomerSalesTrans['ContactID'] : null;
            $SalesTrans->ContactName = (isset($CustomerSalesTrans['ContactName'])) ? $CustomerSalesTrans['ContactName'] : null;
            //$SalesTrans->CustomerInfo = (isset($CustomerSalesTrans['CustomerInfo'])) ? $CustomerSalesTrans['CustomerInfo'] : null;
            $SalesTrans->Time = (isset($CustomerSalesTrans['Time'])) ? $CustomerSalesTrans['Time'] : null;
            $SalesTrans->FileID = (isset($CustomerSalesTrans['FileID'])) ? $CustomerSalesTrans['FileID'] : null;
            $SalesTrans->FileName = (isset($CustomerSalesTrans['FileName'])) ? $CustomerSalesTrans['FileName'] : null;
            $SalesTrans->ItemID = (isset($CustomerSalesTrans['ItemID'])) ? $CustomerSalesTrans['ItemID'] : null;
            $SalesTrans->ItemName = (isset($CustomerSalesTrans['ItemName'])) ? $CustomerSalesTrans['ItemName'] : null;
            $SalesTrans->ExchangeRate = (isset($CustomerSalesTrans['ExchangeRate'])) ? $CustomerSalesTrans['ExchangeRate'] : null;
            $SalesTrans->FCAmount = (isset($CustomerSalesTrans['FCAmount'])) ? $CustomerSalesTrans['FCAmount'] : null;
            $SalesTrans->LCAmount = (isset($CustomerSalesTrans['LCAmount'])) ? $CustomerSalesTrans['LCAmount'] : null;
            $SalesTrans->ExpectedEndDate = (isset($CustomerSalesTrans['ExpectedEndDate'])) ? $CustomerSalesTrans['ExpectedEndDate'] : null;
            $SalesTrans->PercentSuccess = (isset($CustomerSalesTrans['PercentSuccess'])) ? $CustomerSalesTrans['PercentSuccess'] : null;
            $SalesTrans->StatusID = (isset($CustomerSalesTrans['StatusID'])) ? $CustomerSalesTrans['StatusID'] : null;
            $SalesTrans->StatusValue = (isset($CustomerSalesTrans['StatusValue'])) ? $CustomerSalesTrans['StatusValue'] : null;
            $SalesTrans->StatusName = (isset($CustomerSalesTrans['StatusName'])) ? $CustomerSalesTrans['StatusName'] : null;
            $SalesTrans->StatusDescription = (isset($CustomerSalesTrans['StatusDescription'])) ? $CustomerSalesTrans['StatusDescription'] : null;
            $SalesTrans->CreatedDate = (isset($CustomerSalesTrans['CreatedDate'])) ? $CustomerSalesTrans['CreatedDate'] : null;
            $SalesTrans->CcyID = (isset($CustomerSalesTrans['CcyID'])) ? $CustomerSalesTrans['CcyID'] : null;
            $SalesTrans->CcyName = (isset($CustomerSalesTrans['CcyName'])) ? $CustomerSalesTrans['CcyName'] : null;
        }
        $SalesTrans->save();

        //CustomerSalesTransCate
        $TransID = $SalesTrans->TransID;
        CustomerSalesTransCate::where('TransID', $TransID)->delete();
        $arrInsertCSTCate = [];
        if($CustomerSalesTransCate){
            foreach ($CustomerSalesTransCate as $item) {
                $item = (object)$item;
                if (!empty($item->CateID)) {
                    $arrInsertCSTCate[] = [
                        'CustomerID' => $id,
                        'TransID' => $TransID,
                        'CateID' => $item->CateID,
                        'CateName' => $item->CateName,
                        'CateValue' => $item->CateValue,
                    ];
                }
            }
            if (!empty($arrInsertCSTCate)) {
                DB::table('customer_sales_trans_cate')->insert($arrInsertCSTCate);
            }
        }
        //Update to Customer File
        if($TransID){
            $userId = Auth::user()->UserID;
            CustomerFile::where('TransID', '=', $userId)->where('TransTable', '=', 'customer_sales_trans')->update(array('TransID' => $TransID));
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        //$json['data'] = [];
        $json['data']['CustomerSalesTrans'] = $SalesTrans;
        return json_encode($json);
    }
    //Delete Customer SalesTrans
    public function deleteSalesTrans(Request $request){
        $param = $request->post('TransID');
        $data = CustomerSalesTrans::find($param);
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['param'] = $param;
        return json_encode($json);
    }
    //Contract Trans
    public function updateContract(Request $request, $id){
        $CustomerContractTrans = $request->post('CustomerContractTrans'); //return($CustomerContractTrans['ContactID']);
        $CustomerContractTransCate = $request->post('CustomerContractTransCate');
        $CustomerContractTrans['TransDate'] = IjDate::ConvertClientDateToServerDate($CustomerContractTrans['TransDate']);
        //$CustomerContractTrans['ExpectedEndDate'] = IjDate::ConvertClientDateToServerDate($CustomerContractTrans['ExpectedEndDate']);
        $CustomerContractTrans['InvoiceDate'] = IjDate::ConvertClientDateToServerDate($CustomerContractTrans['InvoiceDate']);
        $ContractTrans = CustomerContractTrans::where(['CustomerID' => $id, 'TransDate' => $CustomerContractTrans['TransDate']])->first();
        if(!$ContractTrans){
            if(!isset($CustomerContractTrans['ContactName'])){$CustomerContractTrans['ContactName'] = '';}
            if(!isset($CustomerContractTrans['PositionName'])){$CustomerContractTrans['PositionName'] = '';}
            if(!isset($CustomerContractTrans['OfficePhone'])){$CustomerContractTrans['OfficePhone'] = '';}
            $CustomerContractTrans['CustomerInfo'] = 'Tên khách hàng: '.$CustomerContractTrans['CustomerName'].', '.'Người liên hệ: '.$CustomerContractTrans['ContactName'].', '.'Chức vụ: '.$CustomerContractTrans['PositionName'].', '.'Điện thoại: '.$CustomerContractTrans['OfficePhone'];
            if(!isset($CustomerContractTrans['ContractName'])){$CustomerContractTrans['ContractName'] = '';}
            if(!isset($CustomerContractTrans['ContractNo'])){$CustomerContractTrans['ContractNo'] = '';}
            if(!isset($CustomerContractTrans['ContractDate'])){$CustomerContractTrans['ContractDate'] = '';}
            if(!isset($CustomerContractTrans['ContractAmount'])){$CustomerContractTrans['ContractAmount'] = '';
            }else{ $CustomerContractTrans['ContractAmount'] = number_format($CustomerContractTrans['ContractAmount'], 2, ',', '.');}
            $CustomerContractTrans['ContractInfo'] = 'Tên hợp đồng: ' . $CustomerContractTrans['ContractName'] . ', ' . 'Số hợp đồng: ' . $CustomerContractTrans['ContractNo'] . ', ' . 'Ngày hợp đồng : ' . $CustomerContractTrans['ContractDate'] . ', ' . 'Giá trị HĐ: ' . $CustomerContractTrans['ContractAmount'];
            $ContractTrans = new CustomerContractTrans([
                'CustomerID' => $id,
                'TransDate' => (isset($CustomerContractTrans['TransDate'])) ? $CustomerContractTrans['TransDate'] : null,
                'TransComment' => (isset($CustomerContractTrans['TransComment'])) ? $CustomerContractTrans['TransComment'] : null,
                'InvoiceDate' => (isset($CustomerContractTrans['InvoiceDate'])) ? $CustomerContractTrans['InvoiceDate'] : null,
                'InvoiceNo' => (isset($CustomerContractTrans['InvoiceNo'])) ? $CustomerContractTrans['InvoiceNo'] : null,
                'CustomerName' => (isset($CustomerContractTrans['CustomerName'])) ? $CustomerContractTrans['CustomerName'] : null,
                'ContractID' => (isset($CustomerContractTrans['ContractID'])) ? $CustomerContractTrans['ContractID'] : null,
                'ContractName' => (isset($CustomerContractTrans['ContractName'])) ? $CustomerContractTrans['ContractName'] : null,
                'CustomerInfo' => (isset($CustomerContractTrans['CustomerInfo'])) ? $CustomerContractTrans['CustomerInfo'] : null,
                'ContractInfo' => (isset($CustomerContractTrans['ContractInfo'])) ? $CustomerContractTrans['ContractInfo'] : null,
                'ContactID' => (isset($CustomerContractTrans['ContactID'])) ? $CustomerContractTrans['ContactID'] : null,
                'ContactName' => (isset($CustomerContractTrans['ContactName'])) ? $CustomerContractTrans['ContactName'] : null,
                'Time' => (isset($CustomerContractTrans['Time'])) ? $CustomerContractTrans['Time'] : null,
                'FileID' => (isset($CustomerContractTrans['FileID'])) ? $CustomerContractTrans['FileID'] : null,
                'FileName' => (isset($CustomerContractTrans['FileName'])) ? $CustomerContractTrans['FileName'] : null,
                'ItemID' => (isset($CustomerContractTrans['ItemID'])) ? $CustomerContractTrans['ItemID'] : null,
                'ItemName' => (isset($CustomerContractTrans['ItemName'])) ? $CustomerContractTrans['ItemName'] : null,
                'ExchangeRate' => (isset($CustomerContractTrans['ExchangeRate'])) ? $CustomerContractTrans['ExchangeRate'] : null,
                'FCAmount' => (isset($CustomerContractTrans['FCAmount'])) ? $CustomerContractTrans['FCAmount'] : null,
                'LCAmount' => (isset($CustomerContractTrans['LCAmount'])) ? $CustomerContractTrans['LCAmount'] : null,
                //'ExpectedEndDate' => (isset($CustomerContractTrans['ExpectedEndDate'])) ? $CustomerContractTrans['ExpectedEndDate'] : null,
                'PercentSuccess' => (isset($CustomerContractTrans['PercentSuccess'])) ? $CustomerContractTrans['PercentSuccess'] : null,
                'CcyID' => (isset($CustomerContractTrans['CcyID'])) ? $CustomerContractTrans['CcyID'] : null,
                'CcyName' => (isset($CustomerContractTrans['CcyName'])) ? $CustomerContractTrans['CcyName'] : null,
                'StatusID' => (isset($CustomerContractTrans['StatusID'])) ? $CustomerContractTrans['StatusID'] : null,
                'StatusValue' => (isset($CustomerContractTrans['StatusValue'])) ? $CustomerContractTrans['StatusValue'] : null,
                'StatusName' => (isset($CustomerContractTrans['StatusName'])) ? $CustomerContractTrans['StatusName'] : null,
                'StatusDescription' => (isset($CustomerContractTrans['StatusDescription'])) ? $CustomerContractTrans['StatusDescription'] : null,
                'CreatedDate' => (isset($CustomerContractTrans['CreatedDate'])) ? $CustomerContractTrans['CreatedDate'] : null,
            ]);
        }else{
            $ContractTrans->TransDate = (isset($CustomerContractTrans['TransDate'])) ? $CustomerContractTrans['TransDate'] : null;
            $ContractTrans->TransComment = (isset($CustomerContractTrans['TransComment'])) ? $CustomerContractTrans['TransComment'] : null;
            $ContractTrans->InvoiceDate = (isset($CustomerContractTrans['InvoiceDate'])) ? $CustomerContractTrans['InvoiceDate'] : null;
            $ContractTrans->InvoiceNo = (isset($CustomerContractTrans['InvoiceNo'])) ? $CustomerContractTrans['InvoiceNo'] : null;
            $ContractTrans->CustomerName = (isset($CustomerContractTrans['CustomerName'])) ? $CustomerContractTrans['CustomerName'] : null;
            $ContractTrans->ContractID =  (isset($CustomerContractTrans['ContractID'])) ? $CustomerContractTrans['ContractID'] : null;
            $ContractTrans->ContractName = (isset($CustomerContractTrans['ContractName'])) ? $CustomerContractTrans['ContractName'] : null;
            $ContractTrans->ContactID =  (isset($CustomerContractTrans['ContactID'])) ? $CustomerContractTrans['ContactID'] : null;
            $ContractTrans->ContactName = (isset($CustomerContractTrans['ContactName'])) ? $CustomerContractTrans['ContactName'] : null;
            //$ContractTrans->CustomerInfo = (isset($CustomerContractTrans['CustomerInfo'])) ? $CustomerContractTrans['CustomerInfo'] : null;
            $ContractTrans->Time = (isset($CustomerContractTrans['Time'])) ? $CustomerContractTrans['Time'] : null;
            $ContractTrans->FileID = (isset($CustomerContractTrans['FileID'])) ? $CustomerContractTrans['FileID'] : null;
            $ContractTrans->FileName = (isset($CustomerContractTrans['FileName'])) ? $CustomerContractTrans['FileName'] : null;
            $ContractTrans->ItemID = (isset($CustomerContractTrans['ItemID'])) ? $CustomerContractTrans['ItemID'] : null;
            $ContractTrans->ItemName = (isset($CustomerContractTrans['ItemName'])) ? $CustomerContractTrans['ItemName'] : null;
            $ContractTrans->ExchangeRate = (isset($CustomerContractTrans['ExchangeRate'])) ? $CustomerContractTrans['ExchangeRate'] : null;
            $ContractTrans->FCAmount = (isset($CustomerContractTrans['FCAmount'])) ? $CustomerContractTrans['FCAmount'] : null;
            $ContractTrans->LCAmount = (isset($CustomerContractTrans['LCAmount'])) ? $CustomerContractTrans['LCAmount'] : null;
            //$ContractTrans->ExpectedEndDate = (isset($CustomerContractTrans['ExpectedEndDate'])) ? $CustomerContractTrans['ExpectedEndDate'] : null;
            $ContractTrans->PercentSuccess = (isset($CustomerContractTrans['PercentSuccess'])) ? $CustomerContractTrans['PercentSuccess'] : null;
            $ContractTrans->CcyID = (isset($CustomerContractTrans['CcyID'])) ? $CustomerContractTrans['CcyID'] : null;
            $ContractTrans->CcyName = (isset($CustomerContractTrans['CcyName'])) ? $CustomerContractTrans['CcyName'] : null;
            $ContractTrans->StatusID = (isset($CustomerContractTrans['StatusID'])) ? $CustomerContractTrans['StatusID'] : null;
            $ContractTrans->StatusValue = (isset($CustomerContractTrans['StatusValue'])) ? $CustomerContractTrans['StatusValue'] : null;
            $ContractTrans->StatusName = (isset($CustomerContractTrans['StatusName'])) ? $CustomerContractTrans['StatusName'] : null;
            $ContractTrans->StatusDescription = (isset($CustomerContractTrans['StatusDescription'])) ? $CustomerContractTrans['StatusDescription'] : null;
            $ContractTrans->CreatedDate = (isset($CustomerContractTrans['CreatedDate'])) ? $CustomerContractTrans['CreatedDate'] : null;
        }
        $ContractTrans->save();

        //CustomerContractTransCate
        $TransID = $ContractTrans->TransID;
        CustomerContractTransCate::where('TransID', $TransID)->delete();
        $arrInsertCSTCate = [];
        if($CustomerContractTransCate){
            foreach ($CustomerContractTransCate as $item) {
                $item = (object)$item;
                if (!empty($item->CateID)) {
                    $arrInsertCSTCate[] = [
                        'CustomerID' => $id,
                        'TransID' => $TransID,
                        'CateID' => $item->CateID,
                        'CateName' => $item->CateName,
                        'CateValue' => $item->CateValue,
                    ];
                }
            }
            if (!empty($arrInsertCSTCate)) {
                DB::table('customer_contract_trans_cate')->insert($arrInsertCSTCate);
            }
        }
        //Update to Customer File
        if($TransID){
            $userId = Auth::user()->UserID;
            CustomerFile::where('TransID', '=', $userId)->where('TransTable', '=', 'customer_contract_trans')->update(array('TransID' => $TransID));
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        //$json['data'] = [];
        $json['data']['CustomerContractTrans'] = $ContractTrans;
        return json_encode($json);
    }
    //Delete Customer ContractTrans
    public function deleteContractTrans(Request $request){
        $param = $request->post('TransID');
        $data = CustomerContractTrans::find($param);
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['param'] = $param;
        return json_encode($json);
    }
    public function getAllQuotation(){
        $Uom = Uom::all();
        if(old('CustomerNo')){
            $auto = old('CustomerNo');
        }else{
            $auto = AutoNo::genNewNumberNo('customer_quotation_trans');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        $json['data']['Uom'] = $Uom;
        return json_encode($json);
    }
    public function updateQuotation(Request $request, $id, $CustomerQuotationTransItem){
        $CustomerQuotationTrans = $request->post('CustomerQuotationTrans');
        $CustomerQuotationTransCate = $request->post('CustomerQuotationTransCate');
        $CustomerQuotationTrans['TransDate'] = IjDate::ConvertClientDateToServerDate($CustomerQuotationTrans['TransDate']);
        $QuotationTrans = CustomerQuotationTrans::where(['CustomerID' => $id, 'TransDate' => $CustomerQuotationTrans['TransDate']])->first();

        //$auth = Auth::user(); Lấy ra EmployeeID
        if(!$QuotationTrans){
            $QuotationTrans = new CustomerQuotationTrans([
                'CustomerID' => $id,
                'TransDate' => (isset($CustomerQuotationTrans['TransDate'])) ? $CustomerQuotationTrans['TransDate'] : null,
                'TransNo' => (isset($CustomerQuotationTrans['TransNo'])) ? $CustomerQuotationTrans['TransNo'] : null,
                'TransComment' => (isset($CustomerQuotationTrans['TransComment'])) ? $CustomerQuotationTrans['TransComment'] : null,
                'CompanyID' => (isset($CustomerQuotationTrans['CompanyID'])) ? $CustomerQuotationTrans['CompanyID'] : null,
                'CompanyName' => (isset($CustomerQuotationTrans['CompanyName'])) ? $CustomerQuotationTrans['CompanyName'] : null,
                'CompanyAddress' => (isset($CustomerQuotationTrans['Address'])) ? $CustomerQuotationTrans['Address'] : null,
                'CompanyTaxCode' => (isset($CustomerQuotationTrans['TaxCode'])) ? $CustomerQuotationTrans['TaxCode'] : null,
                'CompanyTel' => (isset($CustomerQuotationTrans['Tel'])) ? $CustomerQuotationTrans['Tel'] : null,
                'CompanyFax' => (isset($CustomerQuotationTrans['Fax'])) ? $CustomerQuotationTrans['Fax'] : null,
                'CustomerName' => (isset($CustomerQuotationTrans['CustomerName'])) ? $CustomerQuotationTrans['CustomerName'] : null,
                'CustomerAddress' => (isset($CustomerQuotationTrans['Address'])) ? $CustomerQuotationTrans['Address'] : null,
                'CustomerTaxCode' => (isset($CustomerQuotationTrans['TaxCode'])) ? $CustomerQuotationTrans['TaxCode'] : null,
                'CustomerEmail' => (isset($CustomerQuotationTrans['Email'])) ? $CustomerQuotationTrans['Email'] : null,
                'CustomerTel' => (isset($CustomerQuotationTrans['OfficePhone'])) ? $CustomerQuotationTrans['OfficePhone'] : null,
                'CcyID' => (isset($CustomerQuotationTrans['CcyID'])) ? $CustomerQuotationTrans['CcyID'] : null,
                'ExchangeRate' => (isset($CustomerQuotationTrans['ExchangeRate'])) ? $CustomerQuotationTrans['ExchangeRate'] : null,
            ]);
        }else{
            $QuotationTrans->TransDate = (isset($CustomerQuotationTrans['TransDate'])) ? $CustomerQuotationTrans['TransDate'] : null;
            $QuotationTrans->TransNo = (isset($CustomerQuotationTrans['TransNo'])) ? $CustomerQuotationTrans['TransNo'] : null;
            $QuotationTrans->TransComment = (isset($CustomerQuotationTrans['TransComment'])) ? $CustomerQuotationTrans['TransComment'] : null;
            $QuotationTrans->CompanyID = (isset($CustomerQuotationTrans['CompanyID'])) ? $CustomerQuotationTrans['CompanyID'] : null;
            $QuotationTrans->CompanyName = (isset($CustomerQuotationTrans['CompanyName'])) ? $CustomerQuotationTrans['CompanyName'] : null;
            $QuotationTrans->CompanyAddress = (isset($CustomerQuotationTrans['Address'])) ? $CustomerQuotationTrans['Address'] : null;
            $QuotationTrans->CompanyTaxCode = (isset($CustomerQuotationTrans['TaxCode'])) ? $CustomerQuotationTrans['TaxCode'] : null;
            $QuotationTrans->CompanyTel = (isset($CustomerQuotationTrans['Tel'])) ? $CustomerQuotationTrans['Tel'] : null;
            $QuotationTrans->CompanyFax = (isset($CustomerQuotationTrans['Fax'])) ? $CustomerQuotationTrans['Fax'] : null;
            $QuotationTrans->CustomerName = (isset($CustomerQuotationTrans['CustomerName'])) ? $CustomerQuotationTrans['CustomerName'] : null;
            $QuotationTrans->CustomerAddress = (isset($CustomerQuotationTrans['Address'])) ? $CustomerQuotationTrans['Address'] : null;
            $QuotationTrans->CustomerTaxCode = (isset($CustomerQuotationTrans['TaxCode'])) ? $CustomerQuotationTrans['TaxCode'] : null;
            $QuotationTrans->CustomerEmail = (isset($CustomerQuotationTrans['Email'])) ? $CustomerQuotationTrans['Email'] : null;
            $QuotationTrans->CustomerTel = (isset($CustomerQuotationTrans['Tel'])) ? $CustomerQuotationTrans['Tel'] : null;
            $QuotationTrans->CcyID = (isset($CustomerQuotationTrans['CcyID'])) ? $CustomerQuotationTrans['CcyID'] : null;
            $QuotationTrans->ExchangeRate = (isset($CustomerQuotationTrans['ExchangeRate'])) ? $CustomerQuotationTrans['ExchangeRate'] : null;
        }
        $QuotationTrans->save();

        //customer_quotation_trans_item
        $TransID = $QuotationTrans->TransID;
        CustomerQuotationTransItem::where('TransID', $TransID)->delete();
        $arrInsertQuotationTransItem = [];
        foreach ($CustomerQuotationTransItem as $item) {
            $item = (object)$item;
            if (!empty($item->LineID)) {
                $arrInsertQuotationTransItem[] = [
                    'CustomerID' => $id,
                    'TransID' => $TransID,
                    'ItemID' => $item->ItemID,
                    'ItemName' => $item->ItemName,
                    'Description' => $item->Description,
                    'UomID' => $item->UomID,
                    'Quantity' => $item->Quantity,
                    'FCUnitPrice' => $item->FCUnitPrice,
                    'LCUnitPrice' => $item->LCUnitPrice,
                    'FCAmount' => $item->FCAmount,
                    'LCAmount' => $item->LCAmount,
                    'TaxRate' => $item->TaxRate,
                    'FCTaxAmount' => $item->FCTaxAmount,
                    'LCTaxAmount' => $item->LCTaxAmount,
                    'PromotionPercent' => $item->PromotionPercent,
                    'FCPromotionAmount' => $item->FCPromotionAmount,
                    'LCPromotionAmount' => $item->LCPromotionAmount,
                    'DiscountPercent' => $item->DiscountPercent,
                    'FCDiscountAmount' => $item->FCDiscountAmount,
                    'LCDiscountAmount' => $item->LCDiscountAmount,
                ];
            }
        }
        if (!empty($arrInsertQuotationTransItem)) {
            DB::table('customer_quotation_trans_item')->insert($arrInsertQuotationTransItem);
        }
        //CustomerQuotationTransCate
//        $TransID = $QuotationTrans->TransID;
//        CustomerQuotationTransCate::where('TransID', $TransID)->delete();
//        $arrInsertCSTCate = [];
//        foreach ($CustomerQuotationTransCate as $item) {
//            $item = (object)$item;
//            if (!empty($item->CateID)) {
//                $arrInsertCSTCate[] = [
//                    'CustomerID' => $id,
//                    'TransID' => $TransID,
//                    'CateID' => $item->CateID,
//                    'CateName' => $item->CateName,
//                    'CateValue' => $item->CateValue,
//                ];
//            }
//        }
//        if (!empty($arrInsertCSTCate)) {
//            DB::table('customer_quotation_trans_cate')->insert($arrInsertCSTCate);
//        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    //Delete Customer QuotationTrans
    public function deleteQuotationTrans(Request $request){
        $param = $request->post('TransID');
        $data = CustomerQuotationTrans::find($param);
        if($data){
            $data->delete();
        }
        CustomerQuotationTransItem::whereIn('TransID', $param)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['param'] = $param;
        return json_encode($json);
    }

    public function updateSupport(Request $request, $id){
        $CustomerSupportTrans = $request->post('CustomerSupportTrans'); //return($CustomerSupportTrans['ContactID']);
        $CustomerSupportTransCate = $request->post('CustomerSupportTransCate');
        $CustomerSupportTrans['TransDate'] = IjDate::ConvertClientDateToServerDate($CustomerSupportTrans['TransDate']);
        $SupportTrans = CustomerSupportTrans::where(['CustomerID' => $id, 'TransDate' => $CustomerSupportTrans['TransDate']])->first();
        if(!$SupportTrans){
            if(!isset($CustomerSupportTrans['ContactName'])){$CustomerSupportTrans['ContactName'] = '';}
            if(!isset($CustomerSupportTrans['PositionName'])){$CustomerSupportTrans['PositionName'] = '';}
            if(!isset($CustomerSupportTrans['OfficePhone'])){$CustomerSupportTrans['OfficePhone'] = '';}
            $CustomerSupportTrans['CustomerInfo'] = 'Tên khách hàng: '.$CustomerSupportTrans['CustomerName'].', '.'Người liên hệ: '.$CustomerSupportTrans['ContactName'].', '.'Chức vụ: '.$CustomerSupportTrans['PositionName'].', '.'Điện thoại: '.$CustomerSupportTrans['OfficePhone'];
            $SupportTrans = new CustomerSupportTrans([
                'CustomerID' => $id,
                'TransDate' => (isset($CustomerSupportTrans['TransDate'])) ? $CustomerSupportTrans['TransDate'] : null,
                'TransComment' => (isset($CustomerSupportTrans['TransComment'])) ? $CustomerSupportTrans['TransComment'] : null,
                'EmployeeID' => (isset($CustomerSupportTrans['EmployeeID'])) ? $CustomerSupportTrans['EmployeeID'] : null,
                'EmployeeName' => (isset($CustomerSupportTrans['EmployeeName'])) ? $CustomerSupportTrans['EmployeeName'] : null,
                'CustomerName' => (isset($CustomerSupportTrans['CustomerName'])) ? $CustomerSupportTrans['CustomerName'] : null,
                'ContactID' => (isset($CustomerSupportTrans['ContactID'])) ? $CustomerSupportTrans['ContactID'] : null,
                'ContactName' => (isset($CustomerSupportTrans['ContactName'])) ? $CustomerSupportTrans['ContactName'] : null,
                'CustomerInfo' => (isset($CustomerSupportTrans['CustomerInfo'])) ? $CustomerSupportTrans['CustomerInfo'] : null,
                'Time' => (isset($CustomerSupportTrans['Time'])) ? $CustomerSupportTrans['Time'] : null,
                'FileID' => (isset($CustomerSupportTrans['FileID'])) ? $CustomerSupportTrans['FileID'] : null,
                'FileName' => (isset($CustomerSupportTrans['FileName'])) ? $CustomerSupportTrans['FileName'] : null,
                'ItemID' => (isset($CustomerSupportTrans['ItemID'])) ? $CustomerSupportTrans['ItemID'] : null,
                'ItemName' => (isset($CustomerSupportTrans['ItemName'])) ? $CustomerSupportTrans['ItemName'] : null,
                'StatusID' => (isset($CustomerSupportTrans['StatusID'])) ? $CustomerSupportTrans['StatusID'] : null,
                'StatusValue' => (isset($CustomerSupportTrans['StatusValue'])) ? $CustomerSupportTrans['StatusValue'] : null,
                'StatusName' => (isset($CustomerSupportTrans['StatusName'])) ? $CustomerSupportTrans['StatusName'] : null,
                'StatusDescription' => (isset($CustomerSupportTrans['StatusDescription'])) ? $CustomerSupportTrans['StatusDescription'] : null,
                'CreatedDate' => (isset($CustomerSupportTrans['CreatedDate'])) ? $CustomerSupportTrans['CreatedDate'] : null,
            ]);
        }else{
            $SupportTrans->TransDate = (isset($CustomerSupportTrans['TransDate'])) ? $CustomerSupportTrans['TransDate'] : null;
            $SupportTrans->TransComment = (isset($CustomerSupportTrans['TransComment'])) ? $CustomerSupportTrans['TransComment'] : null;
            $SupportTrans->EmployeeID = (isset($CustomerSupportTrans['EmployeeID'])) ? $CustomerSupportTrans['EmployeeID'] : null;
            $SupportTrans->EmployeeName = (isset($CustomerSupportTrans['EmployeeName'])) ? $CustomerSupportTrans['EmployeeName'] : null;
            $SupportTrans->CustomerName = (isset($CustomerSupportTrans['CustomerName'])) ? $CustomerSupportTrans['CustomerName'] : null;
            $SupportTrans->ContactID =  (isset($CustomerSupportTrans['ContactID'])) ? $CustomerSupportTrans['ContactID'] : null;
            $SupportTrans->ContactName = (isset($CustomerSupportTrans['ContactName'])) ? $CustomerSupportTrans['ContactName'] : null;
            $SupportTrans->Time = (isset($CustomerSupportTrans['Time'])) ? $CustomerSupportTrans['Time'] : null;
            $SupportTrans->FileID = (isset($CustomerSupportTrans['FileID'])) ? $CustomerSupportTrans['FileID'] : null;
            $SupportTrans->FileName = (isset($CustomerSupportTrans['FileName'])) ? $CustomerSupportTrans['FileName'] : null;
            $SupportTrans->ItemID = (isset($CustomerSupportTrans['ItemID'])) ? $CustomerSupportTrans['ItemID'] : null;
            $SupportTrans->ItemName = (isset($CustomerSupportTrans['ItemName'])) ? $CustomerSupportTrans['ItemName'] : null;
            $SupportTrans->StatusID = (isset($CustomerSupportTrans['StatusID'])) ? $CustomerSupportTrans['StatusID'] : null;
            $SupportTrans->StatusValue = (isset($CustomerSupportTrans['StatusValue'])) ? $CustomerSupportTrans['StatusValue'] : null;
            $SupportTrans->StatusName = (isset($CustomerSupportTrans['StatusName'])) ? $CustomerSupportTrans['StatusName'] : null;
            $SupportTrans->StatusDescription = (isset($CustomerSupportTrans['StatusDescription'])) ? $CustomerSupportTrans['StatusDescription'] : null;
            $SupportTrans->CreatedDate = (isset($CustomerSupportTrans['CreatedDate'])) ? $CustomerSupportTrans['CreatedDate'] : null;
        }
        $SupportTrans->save();

        //CustomerSupportTransCate
        $TransID = $SupportTrans->TransID;
        CustomerSupportTransCate::where('TransID', $TransID)->delete();
        $arrInsertCSTCate = [];
        if($CustomerSupportTransCate){
            foreach ($CustomerSupportTransCate as $item) {
                $item = (object)$item;
                if (!empty($item->CateID)) {
                    $arrInsertCSTCate[] = [
                        'CustomerID' => $id,
                        'TransID' => $TransID,
                        'CateID' => $item->CateID,
                        'CateName' => $item->CateName,
                        'CateValue' => $item->CateValue,
                    ];
                }
            }
            if (!empty($arrInsertCSTCate)) {
                DB::table('customer_support_trans_cate')->insert($arrInsertCSTCate);
            }
        }
        //Update to Customer File
        if($TransID){
            $userId = Auth::user()->UserID;
            CustomerFile::where('TransID', '=', $userId)->where('TransTable', '=', 'customer_support_trans')->update(array('TransID' => $TransID));
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        //$json['data'] = [];
        $json['data']['CustomerSupportTrans'] = $SupportTrans;
        return json_encode($json);
    }
    //Delete Customer SupportTrans
    public function deleteSupportTrans(Request $request){
        $param = $request->post('TransID');
        $data = CustomerSupportTrans::find($param);
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['param'] = $param;
        return json_encode($json);
    }

    //Upload File
//    public function uploadFile(Request $request, $id){
//        if($request->LineID){
//            $TaskFile = CustomerFile::find($request->LineID);
//        }else{
//            $TaskFile = new CustomerFile();
//        }
//        $TaskFile->FileName = $request->FileName;
//        $TaskFile->FileNameRoot = $request->FileName;
//        $TaskFile->Description = $request->Description;
//        $TaskFile->FileType = $request->FileType;
//        $TaskFile->FileSize = $request->FileSize;
//        $TaskFile->TaskID = $id;
//        $TaskFile->CustomerNo = $request->CustomerNo;
//        $TaskFile->CustomerName = $request->CustomerName;
//        $TaskFile->CustomerID = $request->CustomerID;
//        $TaskFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
//        $userId = Auth::user()->UserID;
//        $TaskFile->UserModified = Auth::user()->username;
//        $fileattach = $request->FileUpload;
//        $name = '';
//        $file = '';
//        $linkFileAttach = '';
//        $is_img = false;
//        $ext = '';
//        $size = '';
//        $DateModified = '';
//        $FileID = '';
//        if ($fileattach && $request->changeFile == 1) {
//            $ext = strtolower($fileattach->getClientOriginalExtension());
//            $size = $fileattach->getSize();
//            $name = $fileattach->getClientOriginalName();
//            if ($size <= 400000000) {
//                $arr_ext_img = ["gif", "jpeg", "jpg", "png", "ico", "psd", "ai"];
//                $arr_ext_doc = ["pptx", "ppt", "pps", "xls", "xlsx", "csv", "doc", "docx", "pdf", "txt", "zip", "rar", "cif"];
//                $is_doc = in_array($ext, $arr_ext_doc);
//                $is_img = in_array($ext, $arr_ext_img);
//                if ($is_doc || $is_img) {
//                    $nameFile = Auth::user()->username . '_' . (date('YmdHis') . gettimeofday()['usec']) . "." . $ext;
//                    $linkFileAttach = '/files-attach/' . $nameFile;
//                    $fileModel = new FileModel(
//                        [
//                            'FileName' => $name,
//                            'FileType' => $ext,
//                            'FileSize' => $size,
//                            'DateModified' => date('Y-m-d h:i:s'),
//                            'UserModified' => $userId,
//                            'Link' => $linkFileAttach,
//                        ]
//                    );
//
//                    $fileModel->save();
//                    $fileattach->move('files-attach', $nameFile);
//
//                    $TaskFile->Link = $linkFileAttach;
//                    $TaskFile->DateModifiedRoot = $fileModel->DateModified;
//                    $TaskFile->FileID = $fileModel->FileID;
//
//                }
//            }
//        }
//
//        $TaskFile->save();
//
//        $json['status'] = 1;
//        $json['msg'] = 'Thành công!';
//        $json['data'] = $TaskFile;
//        return json_encode($json);
//    }
    //Upload File
    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $CustomerFile = CustomerFile::find($request->LineID);
        }else{
            $CustomerFile = new CustomerFile();
        }
        $CustomerFile->FileName = $request->FileName;
        $CustomerFile->FileNameRoot = $request->FileName;
        $CustomerFile->Description = $request->Description;
        $CustomerFile->FileType = $request->FileType;
        $CustomerFile->FileSize = $request->FileSize;
        $CustomerFile->TransID = $request->TransID;
        $CustomerFile->TransTable = $request->TransTable;
        $CustomerFile->CustomerID = $id;
        $CustomerFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $CustomerFile->UserModified = Auth::user()->username;
        $fileattach = $request->FileUpload;

        if ($fileattach && $request->changeFile == 1) {
            $ext = strtolower($fileattach->getClientOriginalExtension());
            $size = $fileattach->getSize();
            $name = $fileattach->getClientOriginalName();
            if ($size <= 400000000) {
                $arr_ext_img = ['gif', 'jpeg', 'jpg', 'png', 'ico', 'psd', 'ai'];
                $arr_ext_doc = ['pptx', 'ppt', 'pps', 'xls', 'xlsx', 'csv', 'doc', 'docx', 'pdf', 'txt', 'zip', 'rar', 'cif'];
                $arr_ext_other = ['aif', 'cda', 'mid', 'midi', 'mp3', 'mpa', 'ogg', 'wav', 'wma', 'wpl', '7z', 'arj', 'deb', 'pkg', 'rar', 'rpm', 'tar', '.gz', 'zip', 'z',
                    'bin', 'dmg', 'iso', 'toast', 'vcd', 'csv', 'dat', 'db', 'dbf', 'log', 'mdb', 'sav', 'sql', 'tar', 'xml', 'email', 'eml', 'emlx', 'msg', 'oft', 'ost',
                    'pst', 'vcf', 'fnt', 'fon', 'otf', 'ttf', 'ai', 'bmp', 'gif', 'ico', 'ps', 'psd', 'svg', 'tif', 'tiff', '3g2', '3gp', 'avi', 'flv', 'h264', 'm4v', 'mkv', 'mov',
                    'mp4', 'mpg', 'mpeg', 'rm', 'swf', 'vob', 'wmv', 'm4a'
                ];
                $is_doc = in_array($ext, $arr_ext_doc);
                $is_img = in_array($ext, $arr_ext_img);
                $is_other = in_array($ext, $arr_ext_other);
                if ($is_doc || $is_img || $is_other) {
                    $nameFile = Auth::user()->username . '_' . (date('YmdHis') . gettimeofday()['usec']) . "." . $ext;
                    $linkFileAttach = '/files-attach/' . $nameFile;
                    $fileModel = new FileModel(
                        [
                            'FileName' => $name,
                            'FileType' => $ext,
                            'FileSize' => $size,
                            'DateModified' => date('Y-m-d h:i:s'),
                            'UserModified' => $userId,
                            'Link' => $linkFileAttach,
                        ]
                    );

                    $fileModel->save();
                    $fileattach->move('files-attach', $nameFile);

                    $CustomerFile->Link = $linkFileAttach;
                    $CustomerFile->DateModifiedRoot = $fileModel->DateModified;
                    $CustomerFile->FileID = $fileModel->FileID;

                } else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            } else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $CustomerFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $CustomerFile;
        return json_encode($json);
    }

    //Select File TransID
    public function selectFileTransID(Request $request, $id){
        $data = CustomerFile::where(['TransID' => $id])->orderBy('LineID', 'asc')->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    //Delete File
    public function deleteFile(Request $request, $id){
        $File = CustomerFile::where(['LineID' => $id])->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }
    //Download File
    public function downloadFile(Request $request, $id){
        $file = FileModel::find($id);
        return response()->download(public_path($file->Link));
    }
    //Download File
    public function downloadAllFile(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        if (!$id) {
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại tài liệu';
            return json_encode($json);
        }
        $files = CustomerFile::where('CustomerID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $customer = Customer::find($id);
            $zipName = '';
            if ($customer) {
                $zipName = IjString::CreateSlug($customer->CustomerName, '-');
            }
            $zipLink = IjFile::CreateZipArchive($arrLink, true, $zipName);
            if ($zipLink) {
                $json['status'] = 1;
                $json['msg'] = 'Thành công';
                $json['data'] = $zipLink;
                return json_encode($json);
//                return response()->download(public_path($zipLink));
            }
        }

        $json['msg'] = 'Không tồn tại file zip';
        return json_encode($json);
    }

    //Upload Video
    public function uploadVideo(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $CustomerVideo = CustomerVideo::find($request->LineID);
        }else{
            $CustomerVideo = new CustomerVideo();
        }
        $CustomerVideo->VideoName = $request->VideoName;
        $CustomerVideo->Description = $request->Description;
        $CustomerVideo->VideoType = $request->VideoType;
        $CustomerVideo->VideoSize = $request->VideoSize;
        $CustomerVideo->CustomerID = $id;
        $CustomerVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $CustomerVideo->UserModified = Auth::user()->username;
        $fileattach = $request->VideoUpload;
        if ($fileattach && $request->changeVideo == 1) {
            $ext = strtolower($fileattach->getClientOriginalExtension());
            $size = $fileattach->getSize();
            $name = $fileattach->getClientOriginalName();
            if ($size <= 400000000) {
                $arr_ext_video = ['mpg', 'mpeg', 'avi', 'wmv', 'mov', 'rm', 'ram', 'swf', 'flv', 'ogg', 'webm', '3g2', '3gp', 'avi', 'h264', 'm4v', 'mkv', 'vob',
                    'mp4', 'aif', 'cda', 'mid', 'midi', 'mp3', 'mpa', 'ogg', 'wav', 'wma', 'wpl', 'm4a'];
                $is_video = in_array($ext, $arr_ext_video);
                if ($is_video) {
                    $nameVideo = Auth::user()->username . '_' . (date('YmdHis') . gettimeofday()['usec']) . "." . $ext;
                    $linkFileAttach = '/files-attach/' . $nameVideo;
                    $fileModel = new FileModel(
                        [
                            'FileName' => $name,
                            'FileType' => $ext,
                            'FileSize' => $size,
                            'DateModified' => date('Y-m-d h:i:s'),
                            'UserModified' => $userId,
                            'Link' => $linkFileAttach,
                        ]
                    );

                    $fileModel->save();
                    $fileattach->move('files-attach', $nameVideo);

                    $CustomerVideo->Link = $linkFileAttach;
                    $CustomerVideo->DateModifiedRoot = $fileModel->DateModified;
                    $CustomerVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $CustomerVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $CustomerVideo;
        return json_encode($json);
    }


    //Delete File
    public function deleteVideo(Request $request, $id){
        $tVideo = CustomerVideo::where(['LineID' => $id])->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }
    //Download File
    public function downloadVideo(Request $request, $id){
        $file = FileModel::find($id);
        return response()->download(public_path($file->Link));
    }

    public function updatePer(Request $request, $id){
        $CustomerID = $request->CustomerID;
        $CustomerPer = $request->CustomerPer;

        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CustomerPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'CustomerID' => $CustomerID,
                        'EmployeeID' => $value['EmployeeID'],
                        'Access' => $value['Access'],
                        'AccessField' => $value['AccessField'],
                        'Edit' => $value['Edit'],
                        'EditField' => $value['EditField'],
                        'Delete' => $value['Delete'],
                        'TypePer' => 2
                    ];
                    $ArrEmployee[$value['EmployeeID']] = $value['EmployeeID'];
                }
            }
        }
        CustomerPer::where(['CustomerID' => $CustomerID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('customer_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $CustomerID;
        return json_encode($json);
    }

    public function getCustomerCate(){
        $CateList = CustomerCateList::where('Inactive', '!=', 1)->get();
        $CateValue = CustomerCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CateList'] = $CateList;
        $json['data']['CateValue'] = $CateValue;
        return json_encode($json);
    }

    public function exportExcel(Request $request){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $data = Customer::search($request);
        $Contact = CustomerContact::all();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['Contact'] = $Contact;
        return json_encode($json);

    }
}
