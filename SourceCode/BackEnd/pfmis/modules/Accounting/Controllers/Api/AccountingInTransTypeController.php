<?php
namespace Module\Accounting\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use IjCore\IjDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaType;
use Module\Listing\Models\SysAutoNumber;
use Module\Accounting\Models\AccountingInTransType;
use Module\Listing\Models\Uom;
use Module\Sbpmakeplan\Models\Actautoact;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysStatusItem;

class AccountingInTransTypeController extends Controller{

    public function list(Request $request){
        $data = AccountingInTransType::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('act_intranstype', ['select' => ['InTransTypeName', 'TransDate'], 'conditions' => []]), 'AccountingInTransType.xlsx');
    }

    public function create(){
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = '';
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'InTransTypeName' => 'required'
        ],
            [
                'InTransTypeName.required' => 'Tên loại nghiệp vụ kế toán không được để trống',
            ]);

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = AccountingInTransType::create($request->all());
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->TransID;

        return json_encode($json);
    }

    public function view($id){
        $data = AccountingInTransType::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        return json_encode($json);
    }

    public function edit($id){
        $data = AccountingInTransType::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
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

        $data = AccountingInTransType::search($request);
        $response = [
            'master' => [
                "CreateName" => Auth::user()->FullName,
                "InTransTypeName" => "00001",
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
        $master = AccountingInTransType::find($id);

        $date = date('d');
        $month = date('m');
        $year = date('Y');

        $response = [
            'master' => [
                "Company" => "UBND tỉnh Vĩnh Phú",
                "InTransTypeName" => $master->InTransTypeName,
                "TransDate" => IjDate::DateToString($master->TransDate, 'd/m/Y'),
                "PostDate" => IjDate::DateToString($master->PostDate, 'd/m/Y'),
                "eInTransTypeName" => $master->eInTransTypeName,
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
        ];
        $json['data'] = $response;
        return json_encode($json);
    }

    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'InTransTypeName' => 'required'
        ],
            [
                'InTransTypeName.required' => 'Tên loại nghiệp vụ kế toán không được để trống',
            ]);


        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $data = AccountingInTransType::find($id);
        $data->fill($request->all())->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;

        return json_encode($json);
    }

    public function delete($id){

        $data = AccountingInTransType::find($id);
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

        AccountingInTransType::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

}
