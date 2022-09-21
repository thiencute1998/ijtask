<?php
namespace Module\SysAdmin\Controllers;


use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\SysAdmin\Models\BinaryData;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysReport;
use function GuzzleHttp\Psr7\_parse_request_uri;

class SysReportController extends  Controller{
    public  function list(Request  $request){
        $data = SysReport::search($request);
        $rptPara = DB::table('rpt_para')->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        $json['data']['RptPara'] = $rptPara;
        return json_encode($json);
    }

    public function getReportPara(){
        $data = DB::table('rpt_report_para')->orderBy('NOrder')->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
}
