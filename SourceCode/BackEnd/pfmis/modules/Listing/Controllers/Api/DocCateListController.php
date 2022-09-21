<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\DocCateList;
use Module\Listing\Models\DocCateValue;

class DocCateListController extends Controller{

    public function list(Request $request){
        $data = DocCateList::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('company', ['select' => ['CompanyNo', 'CompanyName'], 'conditions' => []]), 'donvi.xlsx');
    }

    public function create(){
        $docCateList = DocCateList::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['docCateList'] = $docCateList;
        return json_encode($json);
    }
    public function store(Request $request){
        $master = (object) $request->master;
        $detail = $request->detail;
        $dcatelist = new DocCateList([
            'CateName' => $master->CateName,
            'ParentID' => $master->ParentID,
            'Inactive' => $master->Inactive
        ]);
        $dcatelist->save();
        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->Description)){
                $DocCateValue = '';
                switch ($item->DataType){
                    case 1:
                        $DocCateValue = $item->CateValue;
                        break;
                    case 2:
                        $DocCateValue = $item->CateValue;
                        break;
                    case 3:
                        $DocCateValue = date('Y-m-d', strtotime(str_replace('/', '-', $item->CateValue)));
                        break;
                    case 4:
                        $DocCateValue = date('Y-m-d H:i', strtotime(str_replace('/', '-', $item->CateValue)));
                        break;
                    case 5:
                        $DocCateValue = $item->CateValue;
                        break;
                    case 6:
                        $DocCateValue = $item->CateValue;
                        break;
                    default:
                        break;
                }
                $arrInsert[] = [
                    'CateID' => $dcatelist->CateID,
                    'Description' => $item->Description,
                    'DataType' => $item->DataType,
                    'CateValue' => $DocCateValue
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('doc_cate_value')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $dcatelist->CateID;
        return json_encode($json);
    }


    public function edit($id){
        $docCateList = DocCateList::all();
        $docCateValue = DocCateValue::where('CateID', $id)->get();
        $data = DocCateList::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['docCateList'] = $docCateList;
        $json['data']['docCateValue'] = $docCateValue;

        return json_encode($json);
    }


    public function update(Request $request, $id){
        $master = $request->master;
        $detail = $request->detail;
        $validate = [];
        $master = (object) $master;
        $dcatelist = DocCateList::find($id);
        $dcatelist->CateName = $master->CateName;
        $dcatelist->ParentID = $master->ParentID;
        $dcatelist->Inactive = $master->Inactive;
        $dcatelist->save();
        DocCateValue::where('CateID', $id)->delete();
        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->Description)){
                $DocCateValue = '';
                switch ($item->DataType){
                    case 1:
                        $DocCateValue = $item->CateValue;
                        break;
                    case 2:
                        $DocCateValue = $item->CateValue;
                        break;
                    case 3:
                        $DocCateValue = date('Y-m-d', strtotime(str_replace('/', '-', $item->CateValue)));
                        break;
                    case 4:
                        $DocCateValue = date('Y-m-d H:i', strtotime(str_replace('/', '-', $item->CateValue)));
                        break;
                    case 5:
                        $DocCateValue = $item->CateValue;
                        break;
                    case 6:
                        $DocCateValue = $item->CateValue;
                        break;
                    default:
                        break;
                }
                $arrInsert[] = [
                    'CateID' => $dcatelist->CateID,
                    'Description' => $item->Description,
                    'DataType' => $item->DataType,
                    'CateValue' => $DocCateValue
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('doc_cate_value')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;

        return json_encode($json);
    }

    public function view($id){
        $docCateList = DocCateList::all();
        $docCateValue = DocCateValue::where('CateID', $id)->get();
        $data = DocCateList::find($id);


        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['docCateList'] = $docCateList;
        $json['data']['docCateValue'] = $docCateValue;

        return json_encode($json);
    }
    public function delete($id){
        DocCateValue::where('CateID', $id)->delete();
        $data = DocCateList::find($id);
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
        DocCateValue::whereIn('CateID', $IDs)->delete();
        DocCateList::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
