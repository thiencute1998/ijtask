<?php
namespace Module\SysAdmin\Controllers;


use Illuminate\Http\Request;
use Module\SysAdmin\Models\SysLeftMenu;
use Module\SysAdmin\Models\SysTopMenu;
use Module\SysAdmin\Models\SysUserFeaturePer;

class SysMenuController extends Controller{

    public function list(Request $request){
        $UserType = auth()->user()->UserType;

        $PerFeature = SysUserFeaturePer::where(['UserID' => auth()->user()->UserID])->get();
        $MenuLeft = SysLeftMenu::where(['status' => 1])->get();
        $MenuTop = SysTopMenu::where(['status' => 1])->get();
        $PerFeatureArr = null;
        $MenuLeftArr = null;
        $MenuTopArr = null;
        if($PerFeature){
            foreach ($PerFeature as $item){
                $PerFeatureArr[$item->FeatureID] = $item;
            }
        }

        if($MenuLeft){
            foreach ($MenuLeft as $item){
                $item->Allow = 1;
                if(isset($PerFeatureArr[$item->FeatureID])){
                    $item->Allow = $PerFeatureArr[$item->FeatureID]->Access;
                }
                $MenuLeftArr[] = $item;
            }
        }
        if($MenuTop){
            foreach ($MenuTop as $item){
                $item->Allow = 1;
                if(isset($PerFeatureArr[$item->FeatureID])){
                    $item->Allow = $PerFeatureArr[$item->FeatureID]->Access;
                }
                $MenuTopArr[] = $item;
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['MenuLeftArr'] = $MenuLeftArr;
        $json['data']['MenuTopArr'] = $MenuTopArr;
        return json_encode($json);
    }

}
