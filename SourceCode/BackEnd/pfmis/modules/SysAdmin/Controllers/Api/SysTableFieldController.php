<?php
namespace Module\SysAdmin\Controllers;
use App\Company;
use App\Http\Controllers\Controller;
use Module\SysAdmin\Models\SysTable;
use function foo\func;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysFeatureStatus;
use Module\SysAdmin\Models\SysFeatureStatusItem;
use Module\SysAdmin\Models\SysFeature;
use Module\SysAdmin\Models\Workflow;
use Module\SysAdmin\Models\WorkflowConstraint;
use Module\SysAdmin\Models\WorkflowItem;

class SysTableFieldController extends Controller
{
    public function list(Request $request){
        $data = SysTable::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

}
