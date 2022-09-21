<?php
namespace Module\SysAdmin\Controllers;
use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysStatusItem;
use Module\SysAdmin\Models\SysFeature;

class SysStatusController extends Controller
{
    public function list(Request $request){
        $data = SysStatus::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('company', ['select' => ['CompanyNo', 'CompanyName'], 'conditions' => []]), 'donvi.xlsx');
    }

    public function create(){
        $data = SysStatus::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function store(Request $request){
        $master = (object)$request->master;
        $detail = $request->detail;
        $data = new SysStatus([
            'StatusName' => $master->StatusName,
            'Inactive' => (isset($master->Inactive)) ? $master->Inactive : 0
        ]);
        $data->save();
        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->StatusDescription)){
                switch ($item->DataType){
                    case 1:
                        $StatusValue = $item->StatusValue;
                        break;
                    case 2:
                        $StatusValue = $item->StatusValue;
                        break;
                    case 3:
                        $StatusValue = date('Y-m-d', strtotime(str_replace('/', '-', $item->StatusValue)));
                        break;
                    case 4:
                        $StatusValue = date('Y-m-d H:i', strtotime(str_replace('/', '-', $item->StatusValue)));
                        break;
                    case 5:
                        $StatusValue = $item->StatusValue;
                        break;
                    case 6:
                        $StatusValue = $item->StatusValue;
                        break;
                    default:
                        break;
                }
                $arrInsert[] = [
                    'ExecutionStatus' => (isset($item->ExecutionStatus)) ? $item->ExecutionStatus : 0,
                    'StatusID' => $data->StatusID,
                    'StatusDescription' => (isset($item->StatusDescription)) ? $item->StatusDescription : '',
                    'DataType' => (isset($item->DataType)) ? $item->DataType : 0,
                    'StatusValue' => $StatusValue,
                    'Norder' => (isset($item->Norder)) ? $item->Norder : 0,
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('sys_status_item')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->StatusID;
        return json_encode($json);
    }


    public function edit($id){
        $SysStatusItem = SysStatusItem::where('StatusID', $id)->get();
        $data = SysStatus::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['SysStatusItem'] = $SysStatusItem;

        return json_encode($json);
    }


    public function update(Request $request, $id){
        $master = $request->master;
        $detail = $request->detail;
        $validate = [];
        $master = (object) $master;
        $SysStatus = SysStatus::find($id);
        $SysStatus->StatusName = $master->StatusName;
        $SysStatus->Inactive = $master->Inactive;
        $SysStatus->save();
        SysStatusItem::where('StatusID', $id)->delete();
        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->StatusDescription)){
//                $SysStatusItem = $item->SysStatusItem;
                switch ($item->DataType){
                    case 1:
                        $StatusValue = $item->StatusValue;
                        break;
                    case 2:
                        $StatusValue = $item->StatusValue;
                        break;
                    case 3:
                        $StatusValue = date('Y-m-d', strtotime(str_replace('/', '-', $item->StatusValue)));
                        break;
                    case 4:
                        $StatusValue = date('Y-m-d H:i', strtotime(str_replace('/', '-', $item->StatusValue)));
                        break;
                    case 5:
                        $StatusValue = $item->StatusValue;
                        break;
                    case 6:
                        $StatusValue = $item->StatusValue;
                        break;
                    default:
                        break;
                }
                $arrInsert[] = [
                    'ExecutionStatus' => $item->ExecutionStatus,
                    'StatusID' => $SysStatus->StatusID,
                    'StatusValue' => $item->StatusValue,
                    'StatusDescription' => $item->StatusDescription,
                    'DataType' => $item->DataType,
                    'StatusValue' => $StatusValue,
                    'Norder' => $item->Norder,
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('sys_status_item')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;

        return json_encode($json);
    }

    public function view($id){
        $SysStatusItem = SysStatusItem::where('StatusID', $id)->get();
        $data = SysStatus::find($id);

        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['SysStatusItem'] = $SysStatusItem;

        return json_encode($json);
    }
    public function delete($id){
        SysStatusItem::where('StatusID', $id)->delete();
        $data = SysStatus::find($id);
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
        SysStatusItem::whereIn('StatusID', $IDs)->delete();
        SysStatus::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
