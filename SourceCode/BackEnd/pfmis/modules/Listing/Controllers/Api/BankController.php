<?php
namespace Module\Listing\Controllers;

use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Bank;
use Module\Listing\Models\BankLink;


class BankController extends Controller{

    public function list(Request $request){
        $data = Bank::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function store(Request $request){
        $master = (object) $request->master;
        $detail = $request->detail;
        $validator = Validator::make($request->master, [
            'BankName' => 'required',
            'BankAddress'=> 'required',
        ],
            [
                'BankName.required' => 'Tên ngân hàng không được để trống',
                'BankAddress.required' => 'Địa chỉ không được để trống',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $bank = new Bank([
            'BankNo' => $master->BankNo,
            'BankName' => $master->BankName,
            'BankAddress' => $master->BankAddress,
            'Note' => $master->Note,
            'BankType' => $master->selectedType,
            'Inactive' => $master->Inactive
        ]);
        $bank->save();
        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
                $linkNo = $item->LinkNo;
                $linkName = $item->LinkName;
                $linkTable = $item->LinkTable;

                $arrInsert[] = [
                    'BankID' => $bank->BankID,
                    'LinkNo' => $item->LinkNo,
                    'LinkName' => $item->LinkName,
                    'LinkTable' => $item->LinkTable
                ];
        }

        if(!empty($arrInsert)){
            DB::table('bank_link')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $bank->BankID;
        return json_encode($json);
    }

    public function view($id){
        $bank = Bank::all();
        $bankLink = BankLink::where('BankID', $id)->get();
        $data = Bank::find($id);

        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['bank'] = $bank;
        $json['data']['BankLink'] = $bankLink;

        return json_encode($json);
    }

    public function create(){
        if(old('BankNo')){
            $auto = old('BankNo');
        }else{
            $auto = AutoNo::genNewNumberNo('bank');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }

    public function delete($id){
        BankLink::where('BankID', $id)->delete();
        $data = Bank::find($id);
        if($data){
            $data->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = $id;

            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = $id;

            return json_encode($json);
        }
    }
    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        BankLink::whereIn('BankID', $IDs)->delete();
        Bank::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function edit($id){
        $bank = Bank::all();
        $bankLink = BankLink::where('BankID', $id)->get();
        $data = Bank::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['BankLink'] = $bankLink;
        $json['data']['bank'] = $bank;

        return json_encode($json);
    }

    public function update(Request $request, $id){
        $master = $request->master;
        $detail = $request->detail;
        $validate = [];
        $master = (object) $master;
        $bank = Bank::find($id);

        $bank->BankNo = $master->BankNo;
        $bank->BankName = $master->BankName;
        $bank->BankType = $master->selectedType;
        $bank->BankAddress = $master->BankAddress;
        $bank->Note = $master->Note;

        $bank->save();

        BankLink::where('BankID', $id)->delete();
        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;

                $arrInsert[] = [
                    'BankID' => $bank->BankID,
                    'LinkNo' => $item->LinkNo,
                    'LinkName' => $item->LinkName,
                    'LinkTable' => $item->LinkTable,
                ];
            }


        if(!empty($arrInsert)){
            DB::table('bank_link')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;

        return json_encode($json);
    }
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = Bank::search($request);
//        $Uom = Uom::select('UomID', 'UomName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }

}
