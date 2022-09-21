<?php
namespace Module\SysAdmin\Controllers;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Module\SysAdmin\Models\SysAdminCompanySetting;
use Module\SysAdmin\Models\SysAdminGlobalSetting;
use Module\SysAdmin\Models\SysAdminInitCompanySetting;
use Module\SysAdmin\Models\SysAdminInitUserSetting;
use Module\SysAdmin\Models\SysAutoNumberSetting;
use Module\SysAdmin\Models\SysFeature;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysAdminUserSetting;

class SysSetupController extends Controller{

    public function list(Request $request){
        $UserType = auth()->user()->UserType;
        $SysGlobalSetting = SysAdminGlobalSetting::where(['AllowDisplay' => 1])->get()->toArray();

        $CompanyID = auth()->user()->CompanyID;
        $SysCompanySetting = DB::table('sys_admin_init_company_setting AS SI')
            ->select('SI.SettingID AS InitSettingID', 'SI.SettingKey AS InitSettingKey', 'SI.DataType AS InitDataType',
                'SI.AllowChange AS InitAllowChange', 'SI.AllowDisplay AS InitAllowDisplay',
                'SI.SettingName AS InitSettingName', 'SI.SettingValue AS InitSettingValue', 'SI.SettingValueMeta AS InitSettingValueMeta', 'SI.ModuleID AS InitModuleID',
                'S.*')
            ->leftJoin(DB::raw("(SELECT * FROM sys_admin_company_setting WHERE CompanyID = $CompanyID) AS S"), 'S.SettingKey', '=', 'SI.SettingKey')
            ->get()->toArray();

        $UserID = auth()->user()->UserID;
        $SysUserSetting = DB::table('sys_admin_init_user_setting AS SI')
            ->select('SI.SettingID AS InitSettingID', 'SI.SettingKey AS InitSettingKey', 'SI.DataType AS InitDataType',
                'SI.AllowChange AS InitAllowChange', 'SI.AllowDisplay AS InitAllowDisplay',
                'SI.SettingName AS InitSettingName', 'SI.SettingValue AS InitSettingValue', 'SI.SettingValueMeta AS InitSettingValueMeta', 'SI.ModuleID AS InitModuleID',
                'S.*')
            ->leftJoin(DB::raw("(SELECT * FROM sys_admin_user_setting WHERE UserID = $UserID) AS S"), 'S.SettingKey', '=', 'SI.SettingKey')
            ->get()->toArray();

        switch ($UserType){
            case 1:
                $globalAllowChange = 1;
                $companyAllowChange = 1;
                $userAllowChange = 1;
                $autoNumberAllowChange = 1;
                break;
            case 2:
                $globalAllowChange = 0;
                $companyAllowChange = 1;
                $userAllowChange = 1;
                $autoNumberAllowChange = 0;
                break;
            case 3:
            case 4:
                $globalAllowChange = 0;
                $companyAllowChange = 0;
                $userAllowChange = 1;
                $autoNumberAllowChange = 0;
                break;
            default:
                $globalAllowChange = 0;
                $companyAllowChange = 0;
                $userAllowChange = 0;
                $autoNumberAllowChange = 0;
                break;
        }

        $optionSetting = [];
        foreach ($SysUserSetting as $userSetting) {
            $userSetting = (array)$userSetting;
            if (!$userSetting['SettingID']) {
                $userSetting['SettingKey'] = $userSetting['InitSettingKey'];
                $userSetting['SettingName'] = $userSetting['InitSettingName'];
                $userSetting['SettingValue'] = $userSetting['InitSettingValue'];
                $userSetting['SettingValueMeta'] = $userSetting['InitSettingValueMeta'];
                $userSetting['DataType'] = $userSetting['InitDataType'];
                $userSetting['AllowChange'] = $userSetting['InitAllowChange'];
                $userSetting['ModuleID'] = $userSetting['InitModuleID'];
            }

            if ($userSetting['AllowChange'] && $userAllowChange) {
                $userSetting['AllowChange'] = 1;
            } else {
                $userSetting['AllowChange'] = 0;
            }
            $userSetting['SettingType'] = 3;

            $optionSetting[] = $userSetting;
        }
        foreach ($SysCompanySetting as $companySetting) {
            $companySetting = (array)$companySetting;
            if (!$companySetting['SettingID']) {
                $companySetting['SettingKey'] = $companySetting['InitSettingKey'];
                $companySetting['SettingName'] = $companySetting['InitSettingName'];
                $companySetting['SettingValue'] = $companySetting['InitSettingValue'];
                $companySetting['SettingValueMeta'] = $companySetting['InitSettingValueMeta'];
                $companySetting['DataType'] = $companySetting['InitDataType'];
                $companySetting['AllowChange'] = $companySetting['InitAllowChange'];
                $companySetting['ModuleID'] = $companySetting['InitModuleID'];
            }
            if ($companySetting['AllowChange'] && $companyAllowChange) {
                $companySetting['AllowChange'] = 1;
            } else {
                $companySetting['AllowChange'] = 0;
            }
            $companySetting['SettingType'] = 2;
            $optionSetting[] = $companySetting;
        }
        foreach ($SysGlobalSetting as $globalSetting) {
            if ($globalSetting['AllowChange'] && $globalAllowChange) {
                $globalSetting['AllowChange'] = 1;
            } else {
                $globalSetting['AllowChange'] = 0;
            }
            $globalSetting['SettingType'] = 1;
            $optionSetting[] = $globalSetting;
        }


//        switch ($UserType){
//            case 1:
//                $json['data']['SysGlobalSetting']['data'] = $SysGlobalSetting;
//                $json['data']['SysGlobalSetting']['AllowChange'] = 1;
//                $json['data']['SysCompanySetting']['data'] = $SysCompanySetting;
//                $json['data']['SysCompanySetting']['AllowChange'] = 1;
//                $json['data']['SysAutoNumber']['data'] = $SysAutoNumber;
//                $json['data']['SysAutoNumber']['AllowChange'] = 1;
//                $json['data']['SysUserSetting']['data'] = $SysUserSetting;
//                $json['data']['SysUserSetting']['AllowChange'] = 1;
//                break;
//            case 2:
//                $json['data']['SysGlobalSetting']['data'] = $SysGlobalSetting;
//                $json['data']['SysGlobalSetting']['AllowChange'] = 0;
//                $json['data']['SysCompanySetting']['data'] = $SysCompanySetting;
//                $json['data']['SysCompanySetting']['AllowChange'] = 1;
//                $json['data']['SysAutoNumber']['data'] = $SysAutoNumber;
//                $json['data']['SysAutoNumber']['AllowChange'] = 0;
//                $json['data']['SysUserSetting']['data'] = $SysUserSetting;
//                $json['data']['SysUserSetting']['AllowChange'] = 1;
//                break;
//            case 3:
//            case 4:
//                $json['data']['SysGlobalSetting']['data'] = $SysGlobalSetting;
//                $json['data']['SysGlobalSetting']['AllowChange'] = 0;
//                $json['data']['SysCompanySetting']['data'] = $SysCompanySetting;
//                $json['data']['SysCompanySetting']['AllowChange'] = 0;
//                $json['data']['SysAutoNumber']['data'] = $SysAutoNumber;
//                $json['data']['SysAutoNumber']['AllowChange'] = 0;
//                $json['data']['SysUserSetting']['data'] = $SysUserSetting;
//                $json['data']['SysUserSetting']['AllowChange'] = 1;
//                break;
//            default:
//                $json['data']['SysGlobalSetting']['data'] = $SysGlobalSetting;
//                $json['data']['SysGlobalSetting']['AllowChange'] = 0;
//                $json['data']['SysCompanySetting']['data'] = $SysCompanySetting;
//                $json['data']['SysCompanySetting']['AllowChange'] = 0;
//                $json['data']['SysAutoNumber']['data'] = $SysAutoNumber;
//                $json['data']['SysAutoNumber']['AllowChange'] = 0;
//                $json['data']['SysUserSetting']['data'] = $SysUserSetting;
//                $json['data']['SysUserSetting']['AllowChange'] = 0;
//                break;
//        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $optionSetting;
        return json_encode($json);
    }

    public function edit($id)
    {
        $data = SysSetup::find($id);
        $dataType = [
            'DATE' => 'DATE',
            'DATETIME' => 'DATETIME',
            'DOUBLE' => 'DOUBLE',
            'FLOAT' => 'FLOAT',
            'INT' => 'INT',
            'TINYINT' => 'TINYINT',
            'VARCHAR' => 'VARCHAR',
            'YEAR' => 'YEAR'
        ];

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        $json['type'] = $dataType;
        return json_encode($json);
    }
    public function update(Request $request, $id){
        $sysSetup = SysSetup::find($id);
        $sysSetup->OptionValue = $request->post('OptionValue');
        $sysSetup->save();


        $optionGlobal = SysSetup::all();
        $arrOpion = [];
        foreach ($optionGlobal as $item){
            $arrOpion[$item->OptionKey] = $item->OptionValue;
        }



        Storage::put('config.json', json_encode($arrOpion));

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function setupPermission(){
        $data = SysFeature::raw("SELECT FeatureID, if(ParentId = 0, FeatureID, ParentId) as parent_id, FeatureName, FeatureKey FROM sys_feature ORDER BY parent_id, ParentId")->get();
        return view('SysAdmin::syssetup.permission', ['data' => $data]);
    }

    public function updateAll(Request $request)
    {
        $optionSetting = $request->post('optionSetting');
        foreach ($optionSetting as $key => $option) {
            if ($option['SettingType'] == 1) {
                if ($option['AllowChange']) {
                    $data = SysAdminGlobalSetting::find($option['SettingID']);
                    $data->SettingValue = $option['SettingValue'];
                    $data->SettingValueMeta = json_encode($option['SettingValueMeta']);
                    $data->save();
                }
            } elseif ($option['SettingType'] == 2) {
                if ($option['SettingID']) {
                    if ($option['AllowChange']) {
                        $data = SysAdminCompanySetting::find($option['SettingID']);
                        $data->SettingValue = $option['SettingValue'];
                        $data->SettingValueMeta = json_encode($option['SettingValueMeta']);
                        $data->save();
                    }
                } else {
                    $companyOption = new SysAdminCompanySetting([
                        'CompanyID' => Auth::user()->CompanyID,
                        'SettingKey' => $option['SettingKey'],
                        'SettingName' => $option['SettingName'],
                        'SettingValue' => $option['SettingValue'],
                        'SettingValueMeta' => json_encode($option['SettingValueMeta']),
                        'ModuleID' => $option['ModuleID'],
                        'DataType' => $option['DataType'],
                        'AllowChange' => $option['AllowChange'],
                        'AllowDisplay' => 1
                    ]);
                    $companyOption->save();
                }
            } elseif ($option['SettingType'] == 3) {
                if ($option['SettingID']) {
                    if ($option['AllowChange']) {
                        $data = SysAdminUserSetting::find($option['SettingID']);
                        $data->SettingValue = $option['SettingValue'];
                        $data->SettingValueMeta = json_encode($option['SettingValueMeta']);
                        $data->save();
                    }
                }else{
                    $userOption = new SysAdminUserSetting([
                        'UserID' => Auth::user()->UserID,
                        'SettingKey' => $option['SettingKey'],
                        'SettingName' => $option['SettingName'],
                        'SettingValue' => $option['SettingValue'],
                        'SettingValueMeta' => json_encode($option['SettingValueMeta']),
                        'ModuleID' => $option['ModuleID'],
                        'DataType' => $option['DataType'],
                        'AllowChange' => $option['AllowChange'],
                        'AllowDisplay' => 1
                    ]);
                    $userOption->save();
                }
            }
        }

//        $Settings = $request->Settings;
//        $QueryUpdate = "";
//        if($Settings){
//            foreach ($Settings as $item)
//            {
//                $is_user = 1;
//                switch ($UserType){
//                    case 1:
//                        //Có thể cập nhật tất
//                        if($item['Type'] == 1){
//                            $QueryUpdate .= "Update sys_global_setting set SettingValue = ".$item['SettingValue']." WHERE SettingID = ".$item['SettingID']." AND AllowDisplay = 1  AND AllowChange = 1;";
//                        }
//
//                        if($item['Type'] == 2){
//                            $QueryUpdate .= "Update sys_company_setting set SettingValue = ".$item['SettingValue']." WHERE SettingID = ".$item['SettingID']." AND AllowChange = 1 AND CompanyID = ".auth()->user()->CompanyID.";";
//                        }
//
//                        break;
//                    case 2:
//                        //Có thể cập nhật từ mức Company
//                        if($item['Type'] == 2){
//                            $QueryUpdate .= "Update sys_company_setting set SettingValue = ".$item['SettingValue']." WHERE SettingID = ".$item['SettingID']." AND AllowChange = 1 AND CompanyID = ".auth()->user()->CompanyID.";";
//                        }
//                        break;
//                    case 3:
//                    case 4:
//                        break;
//                    default:
//                        //Không cập nhật gì
//                        $is_user = 0;
//                        break;
//                }
//
//                //Cập nhật mức User
//                if($is_user == 1){
//                    $QueryUpdate .= "Update sys_user_setting set SettingValue = ".$item['SettingValue']." WHERE SettingID = ".$item['SettingID']." AND AllowChange = 1 AND UserID = ".auth()->user()->UserID.";";
//                }
//            }
//        }
//        if(!empty($QueryUpdate)){
//            DB::unprepared($QueryUpdate);
//        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
