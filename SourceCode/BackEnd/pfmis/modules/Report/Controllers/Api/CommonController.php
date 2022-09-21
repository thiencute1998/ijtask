<?php
namespace Module\Report\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Company;

class CommonController extends Controller{
    public function getReportTemplate(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $reportFolderName = $request->post('reportFolderName');
        $reportName = $request->post('reportName');

        $reportPath = public_path() . '/file-report/report/' . $reportFolderName . '/' . $reportName . '.rdlx-json';
        $report = file_get_contents($reportPath, true);
        if ($report) {
            $json['status'] = 1;
            $json['data'] = $report;
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại file';
        }
        return json_encode($json);
    }

    public function getLevelCompany(Request $request){
        $maxLevel = DB::table('sys_autonumber_setting')->where('NumberKey','=','company')->first()->MaxLevel;
        $levelCompany = Company::where('CompanyID','=',$request->post('CompanyID'))->first()->Level;
        $json['status'] = 1;
        $json['level'] = $levelCompany;
        $json['maxLevel'] = $maxLevel;
        return $json;
    }


}
