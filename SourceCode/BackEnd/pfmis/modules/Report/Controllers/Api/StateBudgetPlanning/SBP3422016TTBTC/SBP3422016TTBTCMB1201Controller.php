<?php

namespace Module\Report\Controllers\StateBudgetPlanning\SBP3422016TTBTC;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\Company;
use Module\Report\Controllers\Controller;

class SBP3422016TTBTCMB1201Controller extends Controller
{
    public function SBP3422016TTBTC_MB1201(Request $request)
    {
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => []
        ];

        $createTable = "CREATE TEMPORARY TABLE IF NOT EXISTS `table_tmp` (
          `TableID` int(11) NOT NULL AUTO_INCREMENT,
          `STT` varchar(10) DEFAULT '',
          `ItemID` varchar(11) DEFAULT '',
          `ParentID` varchar(100) DEFAULT '',
          `Level` int(2) DEFAULT 1,
          `ItemName` varchar(255) DEFAULT NULL,
          `FontWeight` tinyint(1) DEFAULT 0,
          `Italic` tinyint(1) DEFAULT 0,
          `Center` tinyint(1) DEFAULT 0,
          `RevenueCateList` varchar(20) DEFAULT '',
          `ConAccountNoI1` varchar(20) DEFAULT '',
          `ConAccountNoI2` varchar(20) DEFAULT '',
          `ConAccountNoI3` varchar(20) DEFAULT '',
          `ConAccountNoI4` varchar(20) DEFAULT '',
          `CapitalCateList` varchar(20) DEFAULT '',
          `ProjectCateList` varchar(20) DEFAULT '',
          `RevenueCateValue` varchar(20) DEFAULT '',
          `NormCateList` varchar(20) DEFAULT '',
          `NormCateValue` varchar(20) DEFAULT '',
          `I1` double(20,4) DEFAULT NULL,
          `I2` double(20,4) DEFAULT NULL,
          `I3` double(20,4) DEFAULT NULL,
          `I4` double(20,4) DEFAULT NULL,
          PRIMARY KEY (`TableID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        DB::statement($createTable);
        $templateTable = DB::table('report_sbp_3422016ttbtc_mb1201')->get();
        $insertArr = [];
        foreach ($templateTable as $template) {
            $insertArr[] = [
                'STT' => $template->STT,
                'ItemID' => $template->ItemID,
                'ParentID' => $template->ParentID,
                'Level' => $template->Level,
                'ItemName' => $template->ItemName,
                'FontWeight' => $template->FontWeight,
                'Italic' => $template->Italic,
                'Center' => $template->Center,
                'I1' => $template->I1,
                'I2' => $template->I2,
                'I3' => $template->I3,
                'I4' => $template->I4,
                'RevenueCateList' => $template->RevenueCateList,
                'ConAccountNoI1' => $template->ConAccountNoI1,
                'ConAccountNoI2' => $template->ConAccountNoI2,
                'ConAccountNoI3' => $template->ConAccountNoI3,
                'ConAccountNoI4' => $template->ConAccountNoI4,
                'CapitalCateList' => $template->CapitalCateList,
                'ProjectCateList' => $template->ProjectCateList,
                'RevenueCateValue' => $template->RevenueCateValue,
                  'NormCateList' => $template->NormCateList,
                'NormCateValue' => $template->NormCateValue,

            ];
        }

        DB::table('table_tmp')->insert($insertArr);
        $data = DB::table('table_tmp')->get();
        return json_encode($json);
    }
}
