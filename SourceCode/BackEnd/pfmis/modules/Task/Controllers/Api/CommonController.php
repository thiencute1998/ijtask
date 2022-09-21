<?php
namespace Module\Task\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\Company;
use Module\Listing\Models\Contract;
use Module\Listing\Models\Customer;
use Module\Listing\Models\Doc;
use Module\Listing\Models\Employee;
use Module\Listing\Models\Item;
use Module\Listing\Models\ObjectModel;
use Module\Listing\Models\Project;
use Module\Listing\Models\TaskCateList;
use Module\Listing\Models\TaskCateValue;
use Module\Listing\Models\Uom;
use Module\Listing\Models\Vendor;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysStatusItem;
use Module\Task\Models\Indicator;
use Module\Task\Models\IndicatorTable;
use Module\Task\Models\IndicatorTableItem;

class CommonController extends Controller{

    public function index(Request $request){

    }

    //Get list Uom
    public function getAllUom(){
        $data = Uom::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function getEmployee(Request $request){
        $employee = Employee::search($request);
        $json['data'] = $employee;
        $json['page'] = $request->get('page');
        return json_encode($json);
    }

    public function getAllEmployee(){
        $employee = Employee::all();
        $json['data'] = $employee;
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        return json_encode($json);
    }

    public function getCompany(Request $request){
        $company = Company::search($request);
        if ($company) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $company;
            return json_encode($json);
        }
        return false;
    }

    public function getProject(Request $request){
        $project = Project::search($request);
        if ($project) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $project;
            return json_encode($json);
        }
        return false;
    }
    public function getContract(Request $request){
        $contract = Contract::getContract($request);
        if ($contract) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $contract;
            return json_encode($json);
        }
        return false;
    }
    public function getCustomer(Request $request){
        $contract = Customer::getCustomer($request);
        if ($contract) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $contract;
            return json_encode($json);
        }
        return false;
    }
    public function getVendor(Request $request){
        $vendor = Vendor::search($request);
        if ($vendor) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $vendor;
            return json_encode($json);
        }
        return false;
    }
    public function getObject(Request $request){
        $object = ObjectModel::search($request);
        if ($object) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $object;
            return json_encode($json);
        }
        return false;
    }
    public function getItem(Request $request){
        $item = Item::search($request);
        if ($item) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $item;
            return json_encode($json);
        }
        return false;
    }
    public function getDoc(Request $request){
        $doc = Doc::getDoc($request);
        if ($doc) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $doc;
            return json_encode($json);
        }
        return false;
    }

    public function getTaskCateList(){
        $taskCateList = TaskCateList::where('Inactive', '=', 0)->get();
        $taskCateValue = TaskCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['TaskCateList'] = $taskCateList;
        $json['data']['TaskCateValue'] = $taskCateValue;
        return json_encode($json);
    }
    public function getSysStatus(){
        $sysStatus = SysStatus::where('Inactive', '=', 0)->get();
        $sysStatusItem = SysStatusItem::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['SysStatus'] = $sysStatus;
        $json['data']['SysStatusItem'] = $sysStatusItem;
        return json_encode($json);
    }

    public function getTaskIndicatorTable(Request $request){
        $indicatorTable = IndicatorTable::search($request);
        $json['data'] = $indicatorTable;
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        return json_encode($json);
    }
    public function getTaskIndicatorTableItem(Request $request){
        $mergeEmployee = ($request->post('mergeEmployee')) ? true : false;
        $indicator = IndicatorTableItem::getIndicator($request, $mergeEmployee);
        $json['data'] = $indicator;
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        return json_encode($json);
    }

}
