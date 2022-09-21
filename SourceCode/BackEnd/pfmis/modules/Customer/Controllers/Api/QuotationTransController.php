<?php
namespace Module\Customer\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Customer\Models\CustomerQuotationTrans;
use Module\Listing\Models\SysAutoNumber;

class OpportunityController extends Controller{

    public function list(Request $request){
        $data = CustomerQuotationTrans::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('customer_quotation_trans', ['select' => ['OpportunityName'], 'conditions' => []]), 'cohoi.xlsx');
    }
    public function create(){
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'OpportunityName' => 'required'
        ],
        [
            'OpportunityName.required' => 'Tên cơ hội không được để trống',
        ]);

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new CustomerQuotationTrans([
            //'TransDate' => $request->TransDate,
            'TransDate' => date('Y-m-d', strtotime(str_replace('/', '-', $request->TransDate))),
            'TransNo' => $request->TransNo,
            'TransComment' => $request->TransComment,
            'CompanyID' => $request->CompanyID,
            'CompanyName' => $request->CompanyName,
            'OTAmount' => $request->OTAmount,
            'CompanyName' => $request->CompanyName,
            'OTAmount' => $request->OTAmount,
            'CompanyName' => $request->CompanyName,
            'OTAmount' => $request->OTAmount,
            'CompanyName' => $request->CompanyName,
            'OTAmount' => $request->OTAmount,
            'CompanyName' => $request->CompanyName,
            'OTAmount' => $request->OTAmount,
            'CompanyName' => $request->CompanyName,
            'OTAmount' => $request->OTAmount,
            'Inactive' => $request->Inactive,
        ]);
        $data->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->OpportunityID;

        return json_encode($json);
    }

    public function view($id){
        $data = CustomerQuotationTrans::find($id);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function edit($id){
        $data = CustomerQuotationTrans::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function update(Request $request, $id){
        $data = CustomerQuotationTrans::find($id);

        $data->OpportunityName = $request->OpportunityName;
        $data->CustomerID = $request->CustomerID;
        $data->CustomerName = $request->CustomerName;
        $data->EmployeeID = $request->EmployeeID;
        $data->EmployeeName = $request->EmployeeName;
        $data->OpportunityDate = $request->OpportunityDate;
        $data->ExpectedDate = $request->ExpectedDate;
        $data->OTAmount = $request->OTAmount;
        $data->Inactive = $request->Inactive;
        $data->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }

    public function delete($id){
        $data = CustomerQuotationTrans::find($id);
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
        CustomerQuotationTrans::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
