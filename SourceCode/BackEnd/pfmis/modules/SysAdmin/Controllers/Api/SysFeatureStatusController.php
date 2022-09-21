<?php
namespace Module\SysAdmin\Controllers;
use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysFeatureStatus;
use Module\SysAdmin\Models\SysFeatureStatusItem;
use Module\SysAdmin\Models\SysFeature;

class SysFeatureStatusController extends Controller
{
    public function list(Request $request){
        $data = SysFeatureStatus::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('company', ['select' => ['CompanyNo', 'CompanyName'], 'conditions' => []]), 'donvi.xlsx');
    }

    public function create(){
        $data = SysFeatureStatus::all();
        $feature = SysFeature::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['feature'] = $feature;
        return json_encode($json);
    }
    public function store(Request $request){
        $master = (object)$request->master;
        $detail = $request->detail;
        $data = new SysFeatureStatus([
            'StatusName' => $master->StatusName,
            'FeatureID' => $master->FeatureID,
            'Inactive' => $master->Inactive
        ]);
        $data->save();
        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->StatusDescription)){
//                $SysFeatureStatusItem = $item->SysFeatureStatusItem;
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
                    'FeatureID' => $data->FeatureID,
                    'StatusID' => $data->StatusID,
                    'StatusValue' => $item->StatusValue,
                    'StatusDescription' => $item->StatusDescription,
                    'DataType' => $item->DataType,
                    'StatusValue' => $StatusValue
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('sys_feature_status_item')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->StatusID;
        return json_encode($json);
    }


    public function edit($id){
        $SysFeatureStatusItem = SysFeatureStatusItem::where('StatusID', $id)->get();
        $data = SysFeatureStatus::find($id);
        $feature = SysFeature::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['feature'] = $feature;
        $json['data']['SysFeatureStatusItem'] = $SysFeatureStatusItem;

        return json_encode($json);
    }


    public function update(Request $request, $id){
        $master = $request->master;
        $detail = $request->detail;
        $validate = [];
        $master = (object) $master;
        $SysFeatureStatus = SysFeatureStatus::find($id);
        $SysFeatureStatus->StatusName = $master->StatusName;
        $SysFeatureStatus->FeatureID = $master->FeatureID;
        $SysFeatureStatus->Inactive = $master->Inactive;
        $SysFeatureStatus->save();
        SysFeatureStatusItem::where('StatusID', $id)->delete();
        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            if(!empty($item->StatusDescription)){
//                $SysFeatureStatusItem = $item->SysFeatureStatusItem;
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
                    'FeatureID' => $SysFeatureStatus->FeatureID,
                    'StatusID' => $SysFeatureStatus->StatusID,
                    'StatusValue' => $item->StatusValue,
                    'StatusDescription' => $item->StatusDescription,
                    'DataType' => $item->DataType,
                    'StatusValue' => $StatusValue
                ];
            }
        }

        if(!empty($arrInsert)){
            DB::table('sys_feature_status_item')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;

        return json_encode($json);
    }

    public function view($id){
        $SysFeatureStatusItem = SysFeatureStatusItem::where('StatusID', $id)->get();
        $feature = SysFeature::all();
        $data = SysFeatureStatus::find($id);

        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['feature'] = $feature;
        $json['data']['data'] = $data;
        $json['data']['SysFeatureStatusItem'] = $SysFeatureStatusItem;

        return json_encode($json);
    }
    public function delete($id){
        SysFeatureStatusItem::where('StatusID', $id)->delete();
        $data = SysFeatureStatus::find($id);
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
        SysFeatureStatusItem::whereIn('StatusID', $IDs)->delete();
        SysFeatureStatus::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
