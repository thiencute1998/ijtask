<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\CustomerQuotationTransCateList;
use Module\Listing\Models\CustomerQuotationTransCateValue;

class CustomerQuotationTransCateListController extends Controller{

    public function list(Request $request){
        $data = CustomerQuotationTransCateList::search($request);
        $json['status'] = 1;
        $json['data'] = $data;
        $json['msg'] = 'Thành công!';
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('customer_quotation_trans_cate_list', ['select' => ['CateName'], 'conditions' => []]), 'loaigdbg.xlsx');
    }

    public function create(){
        $customerQuotationTransCateList = CustomerQuotationTransCateList::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['customerQuotationTransCateList'] = $customerQuotationTransCateList;
        return json_encode($json);
    }
    public function store(Request $request){
        $master = (object)$request->master;
        $detail = $request->detail;
        $ccatelist = new CustomerQuotationTransCateList([
            'CateName' => $master->CateName,
            'ParentID' => $master->ParentID,
            'ParentName' => $master->ParentName,
            'Inactive' => $master->Inactive
        ]);
        $ccatelist->save();

        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->CateValue)){
                $ConvertedValue = '';
                switch ($item->DataType){
                    case 1:
                        $ConvertedValue = $item->ConvertedValue;
                        break;
                    case 2:
                        $ConvertedValue = $item->ConvertedValue;
                        break;
                    case 3:
                        $ConvertedValue = date('Y-m-d', strtotime(str_replace('/', '-', $item->ConvertedValue)));
                        break;
                    case 4:
                        $ConvertedValue = date('Y-m-d H:i', strtotime(str_replace('/', '-', $item->ConvertedValue)));
                        break;
                    case 5:
                        $ConvertedValue = $item->ConvertedValue;
                        break;
                    case 6:
                        $ConvertedValue = $item->ConvertedValue;
                        break;
                    default:
                        break;
                }
                $arrInsert[] = [
                    'CateID' => $ccatelist->CateID,
                    'CateValue' => $item->CateValue,
                    'DataType' => $item->DataType,
                    'ConvertedValue' => $ConvertedValue
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('customer_quotation_trans_cate_value')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ccatelist->CateID;
        return json_encode($json);
    }

    public function edit($id){
        $customerQuotationTransCateList = CustomerQuotationTransCateList::all();
        $customerQuotationTransCateValue = CustomerQuotationTransCateValue::where('CateID', $id)->get();
        $data = CustomerQuotationTransCateList::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['customerQuotationTransCateList'] = $customerQuotationTransCateList;
        $json['data']['customerQuotationTransCateValue'] = $customerQuotationTransCateValue;
        return json_encode($json);
    }

    public function update(Request $request, $id){
        $master = $request->master;
        $detail = $request->detail;
        $master = (object) $master;
        $validate = [];
        $ccatelist = CustomerQuotationTransCateList::find($id);
        $ccatelist->CateName = $master->CateName;
        $ccatelist->ParentID = $master->ParentID;
        $ccatelist->ParentName = $master->ParentName;
        $ccatelist->Inactive = $master->Inactive;
        $ccatelist->save();
        CustomerQuotationTransCateValue::where('CateID', $id)->delete();
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->CateValue)){
                $ConvertedValue = '';
                switch ($item->DataType){
                    case 1:
                        $ConvertedValue = $item->ConvertedValue;
                        break;
                    case 2:
                        $ConvertedValue = $item->ConvertedValue;
                        break;
                    case 3:
                        $ConvertedValue = date('Y-m-d', strtotime(str_replace('/', '-', $item->ConvertedValue)));
                        break;
                    case 4:
                        $ConvertedValue = date('Y-m-d H:i', strtotime(str_replace('/', '-', $item->ConvertedValue)));
                        break;
                    case 5:
                        $ConvertedValue = $item->ConvertedValue;
                        break;
                    case 6:
                        $ConvertedValue = $item->ConvertedValue;
                        break;
                    default:
                        break;
                }
                $arrInsert[] = [
                    'CateID' => $ccatelist->CateID,
                    'CateValue' => $item->CateValue,
                    'DataType' => $item->DataType,
                    'ConvertedValue' => $ConvertedValue
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('customer_quotation_trans_cate_value')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }

    public function view($id){
        $customerQuotationTransCateList = CustomerQuotationTransCateList::all();
        $customerQuotationTransCateValue = CustomerQuotationTransCateValue::where('CateID', $id)->get();
        $data = CustomerQuotationTransCateList::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['customerQuotationTransCateList'] = $customerQuotationTransCateList;
        $json['data']['customerQuotationTransCateValue'] = $customerQuotationTransCateValue;
        return json_encode($json);
    }
    public function delete($id){
        CustomerQuotationTransCateValue::where('CateID', $id)->delete();
        $data = CustomerQuotationTransCateList::find($id);
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
        CustomerQuotationTransCateValue::whereIn('CateID', $IDs)->delete();
        CustomerQuotationTransCateList::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
