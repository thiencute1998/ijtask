<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\CustomerSalesTransCateList;
use Module\Listing\Models\CustomerSalesTransCateValue;

class CustomerSalesTransCateListController extends Controller{

    public function list(Request $request){
        $data = CustomerSalesTransCateList::search($request);
        $json['status'] = 1;
        $json['data'] = $data;
        $json['msg'] = 'Thành công!';
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('customer_sales_trans_cate_list', ['select' => ['CateName'], 'conditions' => []]), 'loaigdkd.xlsx');
    }

    public function create(){
        $customerSalesTransCateList = CustomerSalesTransCateList::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['customerSalesTransCateList'] = $customerSalesTransCateList;
        return json_encode($json);
    }
    public function store(Request $request){

        $master = (object)$request->master;
        $detail = $request->detail;
        $ccatelist = new CustomerSalesTransCateList([
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
            DB::table('customer_sales_trans_cate_value')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ccatelist->CateID;
        return json_encode($json);
    }

    public function edit($id){
        $customerSalesTransCateList = CustomerSalesTransCateList::all();
        $customerSalesTransCateValue = CustomerSalesTransCateValue::where('CateID', $id)->get();
        $data = CustomerSalesTransCateList::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['customerSalesTransCateList'] = $customerSalesTransCateList;
        $json['data']['customerSalesTransCateValue'] = $customerSalesTransCateValue;
        return json_encode($json);
    }

    public function update(Request $request, $id){
        $master = $request->master;
        $detail = $request->detail;
        $master = (object) $master;
        $validate = [];
        $ccatelist = CustomerSalesTransCateList::find($id);
        $ccatelist->CateName = $master->CateName;
        $ccatelist->ParentID = $master->ParentID;
        $ccatelist->ParentName = $master->ParentName;
        $ccatelist->Inactive = $master->Inactive;
        $ccatelist->save();
        CustomerSalesTransCateValue::where('CateID', $id)->delete();
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
            DB::table('customer_sales_trans_cate_value')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }

    public function view($id){
        $customerSalesTransCateList = CustomerSalesTransCateList::all();
        $customerSalesTransCateValue = CustomerSalesTransCateValue::where('CateID', $id)->get();
        $data = CustomerSalesTransCateList::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['customerSalesTransCateList'] = $customerSalesTransCateList;
        $json['data']['customerSalesTransCateValue'] = $customerSalesTransCateValue;
        return json_encode($json);
    }
    public function delete($id){
        CustomerSalesTransCateValue::where('CateID', $id)->delete();
        $data = CustomerSalesTransCateList::find($id);
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
        CustomerSalesTransCateValue::whereIn('CateID', $IDs)->delete();
        CustomerSalesTransCateList::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
