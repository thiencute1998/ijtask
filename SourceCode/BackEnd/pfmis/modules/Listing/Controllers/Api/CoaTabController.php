<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use IjCore\IjFile;
use IjCore\IjString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\CoaTab;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Employee;
use Module\Listing\Models\CoaTabCate;
use Module\Listing\Models\CoaTabCateList;
use Module\Listing\Models\CoaTabCateValue;
use Module\Listing\Models\CoaTabPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CoaTabLink;

use function GuzzleHttp\Psr7\_parse_request_uri;

class CoaTabController extends Controller{
    public function list(Request $request){
        $data = CoaTab::search($request);
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('CoaTab',['select'=>['AccountNo','AccountName'],'conditions' => []]),'HTTKQG.xlsx');
    }
    public function create(){
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        $coa_tab = CoaTab::all();
        $employee = Employee::all();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
        $json['data']['BalanceTypeOptions'] = $BalanceTypeOptions;
        $json['data']['coa_tab'] = $coa_tab;
        $json['data']['employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'AccountNo' => 'required|unique:coa_tab',
            'AccountName' => 'required',
        ],[
            'AccountNo.required' => 'Mã HTTK Quốc Gia không được để trống',
            'AccountNo.unique' => 'Mã HTTK Quốc Gia đã tồn tại',
            'AccountName.required' => 'Tên HTTK Quốc Gia không được để trống',

        ]);
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Thất bại';
            $json['data'] = $validator->errors();
            return json_encode($json);
        }
        // set Parent and Level
        if($request->ParentID){
            $parent = CoaTab::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
        } else {
            $level = 1;
        }
        $coa_tab = new CoaTab([
            'AccountNo' => $request->AccountNo,
            'AccountName' => $request->AccountName,
            'ParentID' => $request->ParentID,
            'ParentNo' => $request->ParentNo,
            'Level' => $level,
            'BalanceType'=>$request->BalanceType,
            'Detail' => 1,
            'Note' => $request->Note,
            'Loked' => $request->Locked,
            'Inactive' => $request->Inactive,
            'AccessType'=> ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated'=> Auth::user()->UserID,
            'AuthorizedPerson'=> Auth::user()->UserID,
        ]);
        $coa_tab->save();
        $CoaTabPer = new CoaTabPer([
            'AccountID' => $coa_tab->AccountID,
            'AccountNo' => $coa_tab->AccountNo,
            'AccountName'=>$coa_tab->AccountName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName'=> Auth::user()->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CoaTabPer->save();

        // CoaTabCate
        $arrInsert= [];
        if($request->post('CoaTabCate')){
            foreach ($request->post('CoaTabCate') as $key=>$item){
                if($item['CateID']){
                    $arrInsert[] = [
                        'AccountID'=>$coa_tab->AccountID,
                        'CateID'=>$item['CateID'],
                        'CateValue'=>$item['CateValue']
                    ];
                }
            }
            DB::table('coa_tab_cate')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $coa_tab->AccountID;
        return json_encode($json);
    }
    public function view($id){
        $query = CoaTab::query();
        $data = CoaTab::find($id);
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $query->where('coa_tab.AccountID', '=', $data->ParentID);
                $parent = $query->first();
                if(isset($parent->AccountNo) && isset($parent->AccountName)){
                    $arrParent = [
                        'AccountNo' => $parent->AccountNo,
                        'AccountName' => $parent->AccountName,
                    ];
                }
            }
            $CoaTabPerEmployee = CoaTabPer::where([
                'AccountID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID,
            ])->first();
            $EmployeeLogin = Employee::where('EmployeeID','=',Auth::user()->EmployeeID)->first();
            if(!$CoaTabPerEmployee){
                if($data->AccessType==2){
                    // Tài liệu công khai
                    $CoaTabPerEmployee = new CoaTabPer([
                        'AccountID' => $id,
                        'EmployeeID' => $EmployeeLogin->EmployeeID,
                        'EmployeeName' => $EmployeeLogin->EmployeeName,
                        'Access' => 1,
                        'AccessField' => 'all',
                        'Edit' => 0,
                        'EditField' => '',
                        'Delete' => 0,
                        'Create' => 0
                    ]);
                } else {
                    // Không phải là tài liệu công khai
                    if($EmployeeLogin->UserID != $data->UserIDCreated && $EmployeeLogin->UserID != $data->AuthorizedPerson){
                        $json['status'] = 3;
                        $json['msg'] = 'Bạn không có quyền truy cập!';
                        return json_encode($json);
                    }
                    else{
                        $CoaTabPerEmployee = new CoaTabPer([
                            'AccountID' => $id,
                            'EmployeeID'
                            => $EmployeeLogin->EmployeeID,
                            'EmployeeName' => $EmployeeLogin->EmployeeName,
                            'Access' => 1,
                            'AccessField' => 'all',
                            'Edit' => 0,
                            'EditField' => '',
                            'Delete' => 0,
                            'Create' => 0
                        ]);
                    }
                }
            }
            else{
                if($EmployeeLogin->UserID == $data->UserIDCreated || $EmployeeLogin->UserID == $data->AuthorizedPerson){
                    $CoaTabPerEmployee = new CoaTabPer([
                        'AccountID' => $id,
                        'EmployeeID' => $EmployeeLogin->EmployeeID,
                        'EmployeeName' => $EmployeeLogin->EmployeeName,
                        'Access' => 1,
                        'AccessField' => 'all',
                        'Edit' => 1,
                        'EditField' => 'all',
                        'Delete' => 1,
                        'Create' => 1
                    ]);
                }
            }

            if ($CoaTabPerEmployee) {
                if (strpos(',' . $CoaTabPerEmployee->AccessField . ',', ',all,')) {
                    $CoaTabPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $CoaTabPerEmployee->EditField . ',', ',all,')){
                    $CoaTabPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $CoaTabPer = CoaTabPer::where('AccountID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $CoaTab = CoaTab::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $CoaTabCate = CoaTabCate::where('AccountID',$id)->get();
            $arrCate = [];
            foreach($CoaTabCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $CoaTabCateList = CoaTabCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $CoaTabCateValue = CoaTabCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['CoaTab'] = $CoaTab;
            $json['Employee'] = $Employee;
            $json['CoaTabPer'] = $CoaTabPer;
            $json['CoaTabPerEmployee'] = $CoaTabPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
            $json['data']['BalanceTypeOptions']= $BalanceTypeOptions;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CoaTabCate'] = $CoaTabCate;
            $json['data']['CoaTabCateList'] = $CoaTabCateList;
            $json['data']['CoaTabCateValue'] = $CoaTabCateValue;
            return json_encode($json);
        }else{

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public function edit($id){
        $employee = Employee::all();
        $data = CoaTab::find($id);
        $coa_tab = CoaTab::all();
        if(old('AccountNo')){
            $data->AccountNo = old('AccountNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['coa_tab'] = $coa_tab;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function update(Request $request, $id){

        $coa_tab = CoaTab::find($id);
        if(!$coa_tab){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_tab->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_tab->ParentID !== $request->ParentID){
            $validator = Validator::make($request->all(), [
                'AccountNo' => 'required:coa_tab',
                'AccountName'=> 'required:coa_tab',
            ],
                [
                    'AccountNo.required' => 'Mã hệ thống tài khoản quốc gia không được để trống',
                    'AccountName.required' => 'Tên hệ thống tài khoản quốc gia không được để trống',
                ]
            );

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $coa_tab->AccountNo = $request->AccountNo;
            // set for new Parent
            if($request->ParentID){
                $parent = CoaTab::find($request->ParentID);
                $parent->Detail = 0;
                $parent->save();
                $level = $parent->Level + 1;
            }else{
                $level = 1;
            }
            $coa_tab->Level = $level;
            $coa_tab->Detail = 1;
            // set for old Parents
            if($coa_tab->ParentID){
                $parentOld = CoaTab::find($coa_tab->ParentID);
                $count = DB::table('coa_tab')->where('ParentID','=', $parentOld->AccountID)->count();
                if($count < 2){
                    $parentOld->Detail = 1;
                    $parentOld->save();
                }
            }
        }
        $coa_tab->AccountName = $request->AccountName;
        $coa_tab->ParentID = $request->ParentID;
        $coa_tab->ParentNo = $request->ParentNo;
        $coa_tab->BalanceType = $request->BalanceType;
        $coa_tab->Note = $request->Note;
        $coa_tab->save();

        // coa_tab cate
        if($request->post('CoaTabCate')){
            $CoaTabCate = $request->post('CoaTabCate');
            CoaTabCate::where('AccountID',$id)->delete();
            $arrInsert = [];
            foreach($CoaTabCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "AccountID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('coa_tab_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $coa_tab->AccountID;
        return $json;
    }
    public function delete($id){
        $CoaTab = coaTab::find($id);
        $auth = Auth::user();
        if ($CoaTab) {
            if($CoaTab->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
            // set for old Parent
            if($CoaTab->ParentID){
                $parent = coaTab::find($CoaTab->ParentID);
                $count = DB::table('coa_tab')->where('ParentID', '=', $parent->AccountID)->count();
                if($count < 2){
                    $parent->Detail = 1;
                    $parent->save();
                }
            }
            DB::table('coa_tab_cate')->where('AccountID', '=', $CoaTab->AccountID)->delete();
            $CoaTab->delete();

        }
        if($CoaTab){
            if ($auth->UserType === 1) {
                $CoaTab->delete();
                CoaTabCate::where('AccountID', $id)->delete();
                CoaTabPer::where('AccountID', $id)->delete();
                CoaTabLink::where('AccountID',$id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $isPer = CoaTabPer::where([
                    'AccountID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($CoaTab->UserIDCreated === $auth->UserID)){
                    $CoaTab->delete();
                    CoaTabCate::where('AccountID', $id)->delete();
                    CoaTabPer::where('AccountID', $id)->delete();
                    CoaTabLink::where('AccountID',$id)->delete();
                    $json['status'] = 1;
                    $json['msg'] = 'Thành công!';
                    $json['data'] = [];
                    return json_encode($json);
                }else{
                    $json['status'] = 0;
                    $json['msg'] = 'Bạn không có quyền xóa';
                    $json['data'] = [];
                    return json_encode($json);
                }
            }
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        foreach ($IDs as $id){
            $data = CoaTab::find($id);
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        DB::table('coa_tab_cate')->whereIn('AccountID', $IDs)->delete();
        CoaTab::destroy($IDs);
        $sql = "UPDATE coa_tab
                SET Detail = 1
                WHERE AccountID NOT IN (SELECT ParentID FROM (select ParentID from coa_tab WHERE ParentID is not null) t)";
        DB::statement($sql);
        CoaTabCate::whereIn('AccountID',$IDs)->delete();
        CoaTabLink::whereIn('AccountID',$IDs)->delete();
        CoaTabPer::whereIn('AccountID',$IDs)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
        $auth = Auth::user();
        $idArray = $request->post('array_id');
        if ($auth->UserType === 1) {
            CoaTab::destroy($idArray);
            CoaTabCate::whereIn('AccountID', $idArray)->delete();
            CoaTabPer::whereIn('AccountID', $idArray)->delete();
            CoaTabLink::where('AccountID',$idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $coaTabsPer = CoaTabPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('coa_tab_per')
                    ->whereIn('AccountID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('AccountID');
            })->get();

            foreach ($coaTabsPer as $coaTabPer) {
                if ($coaTabPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                CoaTab::destroy($idArray);
                CoaTabCate::whereIn('AccountID', $idArray)->delete();
                CoaTabPer::whereIn('AccountID', $idArray)->delete();
                CoaTabLink::whereIn('AccountID',$idArray)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $json['status'] = 0;
                $json['msg'] = 'Bạn không có quyền xóa';
                $json['data'] = [];
                return json_encode($json);
            }

        }
    }
    public function updatePer(Request $request, $id){
        $AccountID = $request->post('AccountID');
        $CoaTabPer = $request->post('CoaTabPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CoaTabPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'AccountID' => $AccountID,
                        'EmployeeID' => $value['EmployeeID'],
                        'Access' => ($value['Access']) ? 1 : 0,
                        'AccessField' => $value['AccessField'],
                        'Edit' => ($value['Edit']) ? 1 : 0,
                        'EditField' => $value['EditField'],
                        'Delete' => ($value['Delete']) ? 1 : 0,
                        'Create' => ($value['Create']) ? 1 : 0
                    ];
                    $ArrEmployee[$value['EmployeeID']] = $value['EmployeeID'];
                }
            }
        }
        CoaTabPer::where(['AccountID' => $AccountID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('coa_tab_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $AccountID;
        return json_encode($json);
    }
    public function getCoaTabCateList(){
        $coa_tabCateList = CoaTabCateList::where('Inactive',0)->get();
        $coa_tabCateValue = CoaTabCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CoaTabCateList'] = $coa_tabCateList;
        $json['data']['CoaTabCateValue'] = $coa_tabCateValue;
        return json_encode($json);
    }
    public function getCoaTabLink($id){
        $data = CoaTabLink::where('AccountID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function updateLink(Request $request){
        $LinkArray = $request->post('CoaTabLink');
        $id = $request->post('AccountID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'AccountID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        CoaTabLink::where(['AccountID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('coa_tab_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = CoaTab::search($request);
        $json['data'] = $data;
        return json_encode($json);
    }
    //
    public  function  getListChild(Request $request){
        $auth = Auth::User();
        $ParentID = $request->ParentID;
        $query = CoaTab::query();
        $query->select('coa_tab.*');
        $query->leftJoin('coa_tab_per', 'coa_tab.AccountID', '=', 'coa_tab_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_tab_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_tab.AccessType', '=', 2);
                });
        });
        $query->where('coa_tab.ParentID', $ParentID);
        $query->groupBy('coa_tab.AccountID')->orderBy('coa_tab.AccountNo');
        $data = $query->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
?>
