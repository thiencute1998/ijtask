<?php
namespace Module\Customer\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Customer\Models\CustomerSalestransCate;
use Module\Listing\Models\AutoNo;
use Module\Customer\Models\CustomerSalesTrans;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysStatusItem;

class SalesTransController extends Controller{

    public function list(Request $request){
        $data = CustomerSalesTrans::search($request);
        $CustomerSalesTransCate = CustomerSalesTransCate::all();
        $Sl_Status = SysStatus::where('Inactive', '!=', 1)->get();
        $Sl_StatusItem = SysStatusItem::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['Sl_Status'] = $Sl_Status;
        $json['data']['Sl_StatusItem'] = $Sl_StatusItem;
        $json['data']['CustomerSalesTransCate'] = $CustomerSalesTransCate;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('customer_sales_trans', ['select' => ['TransComment'], 'conditions' => []]), 'giaodichbanhang.xlsx');
    }
    public function create(){
        $CustomerSalesTransCate = CustomerSalesTransCate::where('CustomerID', $id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';

        $json['data']['CustomerSalesTransCate'] = $CustomerSalesTransCate;

        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'TransDate' => 'required'
        ],
        [
            'TransDate.required' => 'Ngày giao dịch không được để trống',
        ]);

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new CustomerSalesTrans([
            'TransDate' => $request->TransDate,
            'TransComment' => $request->TransComment,
            'EmployeeID' => $request->EmployeeID,
            'EmployeeName' => $request->EmployeeName,
            'CustomerName' => $request->CustomerName,
            'ContactName' => $request->ContactName,
            'CustomerInfo' => $request->CustomerInfo,
            'Time' => $request->Time,
            'FileID' => $request->FileID,
            'FileName' => $request->FileName,
            'ItemID' => $request->ItemID,
            'ItemName' => $request->ItemName,
            'ExchangeRate' => $request->ExchangeRate,
            'FCAmount' => $request->FCAmount,
            'LCAmount' => $request->LCAmount,
            'ExpectedEndDate' => $request->ExpectedEndDate,
            'PercentSuccess' => $request->PercentSuccess,
            'StatusID' => $request->StatusID,
            'StatusDescription' => $request->StatusDescription,
            'CreatedDate' => $request->CreatedDate,
        ]);
        $data->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->OpportunityID;

        return json_encode($json);
    }

    public function view($id){
        $data = CustomerSalesTrans::find($id);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function edit($id){
        $data = CustomerSalesTrans::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function update(Request $request, $id){
        $data = CustomerSalesTrans::find($id);

        $data->TransDate = $request->TransDate;
        $data->TransComment = $request->TransComment;
        $data->EmployeeID = $request->EmployeeID;
        $data->EmployeeName = $request->EmployeeID;
        $data->CustomerName = $request->CustomerName;
        $data->ContactName = $request->ContactName;
        $data->CustomerInfo = $request->CustomerInfo;
        $data->Time = $request->Time;
        $data->FileID = $request->FileID;
        $data->FileName = $request->FileName;
        $data->ItemID = $request->ItemID;
        $data->ItemName = $request->ItemName;
        $data->ExchangeRate = $request->ExchangeRate;
        $data->FCAmount = $request->FCAmount;
        $data->LCAmount = $request->LCAmount;
        $data->ExpectedEndDate = $request->ExpectedEndDate;
        $data->PercentSuccess = $request->PercentSuccess;
        $data->StatusID = $request->StatusID;
        $data->StatusDescription = $request->StatusDescription;
        $data->CreatedDate = $request->CreatedDate;

        $data->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }

    public function delete($id){
        $data = CustomerSalesTrans::find($id);
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        CustomerSalesTrans::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
