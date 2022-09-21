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
use Module\Listing\Models\CoaPmu;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Employee;
use Module\Listing\Models\CoaPmuCate;
use Module\Listing\Models\CoaPmuCateList;
use Module\Listing\Models\CoaPmuCateValue;
use Module\Listing\Models\CoaPmuPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CoaPmuLink;

use function GuzzleHttp\Psr7\_parse_request_uri;

class CoaPmuController extends Controller{
    public function list(Request $request){
        $data = CoaPmu::search($request);
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('CoaPmu',['select'=>['AccountNo','AccountName'],'conditions' => []]),'HTTKQG.xlsx');
    }
    public function create(){
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        $coa_pmu = CoaPmu::all();
        $employee = Employee::all();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
        $json['data']['BalanceTypeOptions'] = $BalanceTypeOptions;
        $json['data']['coa_pmu'] = $coa_pmu;
        $json['data']['employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'AccountNo' => 'required|unique:coa_pmu',
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
            $parent = CoaPmu::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
        } else {
            $level = 1;
        }
        $coa_pmu = new CoaPmu([
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
        $coa_pmu->save();
        $CoaPmuPer = new CoaPmuPer([
            'AccountID' => $coa_pmu->AccountID,
            'AccountNo' => $coa_pmu->AccountNo,
            'AccountName'=>$coa_pmu->AccountName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName'=> Auth::user()->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CoaPmuPer->save();

        // CoaPmuCate
        $arrInsert= [];
        if($request->post('CoaPmuCate')){
            foreach ($request->post('CoaPmuCate') as $key=>$item){
                if($item['CateID']){
                    $arrInsert[] = [
                        'AccountID'=>$coa_pmu->AccountID,
                        'CateID'=>$item['CateID'],
                        'CateValue'=>$item['CateValue']
                    ];
                }
            }
            DB::table('coa_pmu_cate')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $coa_pmu->AccountID;
        return json_encode($json);
    }
    public function view($id){
        $query = CoaPmu::query();
        $data = CoaPmu::find($id);
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $query->where('coa_pmu.AccountID', '=', $data->ParentID);
                $parent = $query->first();
                if(isset($parent->AccountNo) && isset($parent->AccountName)){
                    $arrParent = [
                        'AccountNo' => $parent->AccountNo,
                        'AccountName' => $parent->AccountName,
                    ];
                }
            }
            $CoaPmuPerEmployee = CoaPmuPer::where([
                'AccountID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID,
            ])->first();
            $EmployeeLogin = Employee::where('EmployeeID','=',Auth::user()->EmployeeID)->first();
            if(!$CoaPmuPerEmployee){
                if($data->AccessType==2){
                    // Tài liệu công khai
                    $CoaPmuPerEmployee = new CoaPmuPer([
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
                        $CoaPmuPerEmployee = new CoaPmuPer([
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
                    $CoaPmuPerEmployee = new CoaPmuPer([
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

            if ($CoaPmuPerEmployee) {
                if (strpos(',' . $CoaPmuPerEmployee->AccessField . ',', ',all,')) {
                    $CoaPmuPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $CoaPmuPerEmployee->EditField . ',', ',all,')){
                    $CoaPmuPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $CoaPmuPer = CoaPmuPer::where('AccountID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $CoaPmu = CoaPmu::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $CoaPmuCate = CoaPmuCate::where('AccountID',$id)->get();
            $arrCate = [];
            foreach($CoaPmuCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $CoaPmuCateList = CoaPmuCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $CoaPmuCateValue = CoaPmuCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['CoaPmu'] = $CoaPmu;
            $json['Employee'] = $Employee;
            $json['CoaPmuPer'] = $CoaPmuPer;
            $json['CoaPmuPerEmployee'] = $CoaPmuPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
            $json['data']['BalanceTypeOptions']= $BalanceTypeOptions;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CoaPmuCate'] = $CoaPmuCate;
            $json['data']['CoaPmuCateList'] = $CoaPmuCateList;
            $json['data']['CoaPmuCateValue'] = $CoaPmuCateValue;
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
        $data = CoaPmu::find($id);
        $coa_pmu = CoaPmu::all();
        if(old('AccountNo')){
            $data->AccountNo = old('AccountNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['coa_pmu'] = $coa_pmu;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function update(Request $request, $id){

        $coa_pmu = CoaPmu::find($id);
        if(!$coa_pmu){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_pmu->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_pmu->ParentID !== $request->ParentID){
            $validator = Validator::make($request->all(), [
                'AccountNo' => 'required:coa_pmu',
                'AccountName'=> 'required:coa_pmu',
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
            $coa_pmu->AccountNo = $request->AccountNo;
            // set for new Parent
            if($request->ParentID){
                $parent = CoaPmu::find($request->ParentID);
                $parent->Detail = 0;
                $parent->save();
                $level = $parent->Level + 1;
            }else{
                $level = 1;
            }
            $coa_pmu->Level = $level;
            $coa_pmu->Detail = 1;
            // set for old Parents
            if($coa_pmu->ParentID){
                $parentOld = CoaPmu::find($coa_pmu->ParentID);
                $count = DB::table('coa_pmu')->where('ParentID','=', $parentOld->AccountID)->count();
                if($count < 2){
                    $parentOld->Detail = 1;
                    $parentOld->save();
                }
            }
        }
        $coa_pmu->AccountName = $request->AccountName;
        $coa_pmu->ParentID = $request->ParentID;
        $coa_pmu->ParentNo = $request->ParentNo;
        $coa_pmu->BalanceType = $request->BalanceType;
        $coa_pmu->Note = $request->Note;
        $coa_pmu->save();

        // coa_pmu cate
        if($request->post('CoaPmuCate')){
            $CoaPmuCate = $request->post('CoaPmuCate');
            CoaPmuCate::where('AccountID',$id)->delete();
            $arrInsert = [];
            foreach($CoaPmuCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "AccountID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('coa_pmu_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $coa_pmu->AccountID;
        return $json;
    }
    public function delete($id){
        $CoaPmu = coaPmu::find($id);
        $auth = Auth::user();
        if ($CoaPmu) {
            if($CoaPmu->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
            // set for old Parent
            if($CoaPmu->ParentID){
                $parent = coaPmu::find($CoaPmu->ParentID);
                $count = DB::table('coa_pmu')->where('ParentID', '=', $parent->AccountID)->count();
                if($count < 2){
                    $parent->Detail = 1;
                    $parent->save();
                }
            }
            DB::table('coa_pmu_cate')->where('AccountID', '=', $CoaPmu->AccountID)->delete();
            $CoaPmu->delete();

        }
        if($CoaPmu){
            if ($auth->UserType === 1) {
                $CoaPmu->delete();
                CoaPmuCate::where('AccountID', $id)->delete();
                CoaPmuPer::where('AccountID', $id)->delete();
                CoaPmuLink::where('AccountID',$id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $isPer = CoaPmuPer::where([
                    'AccountID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($CoaPmu->UserIDCreated === $auth->UserID)){
                    $CoaPmu->delete();
                    CoaPmuCate::where('AccountID', $id)->delete();
                    CoaPmuPer::where('AccountID', $id)->delete();
                    CoaPmuLink::where('AccountID',$id)->delete();
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
            $data = CoaPmu::find($id);
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        DB::table('coa_pmu_cate')->whereIn('AccountID', $IDs)->delete();
        CoaPmu::destroy($IDs);
        $sql = "UPDATE coa_pmu
                SET Detail = 1
                WHERE AccountID NOT IN (SELECT ParentID FROM (select ParentID from coa_pmu WHERE ParentID is not null) t)";
        DB::statement($sql);
        CoaPmuCate::whereIn('AccountID',$IDs)->delete();
        CoaPmuLink::whereIn('AccountID',$IDs)->delete();
        CoaPmuPer::whereIn('AccountID',$IDs)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
        $auth = Auth::user();
        $idArray = $request->post('array_id');
        if ($auth->UserType === 1) {
            CoaPmu::destroy($idArray);
            CoaPmuCate::whereIn('AccountID', $idArray)->delete();
            CoaPmuPer::whereIn('AccountID', $idArray)->delete();
            CoaPmuLink::where('AccountID',$idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $coaPmusPer = CoaPmuPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('coa_pmu_per')
                    ->whereIn('AccountID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('AccountID');
            })->get();

            foreach ($coaPmusPer as $coaPmuPer) {
                if ($coaPmuPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                CoaPmu::destroy($idArray);
                CoaPmuCate::whereIn('AccountID', $idArray)->delete();
                CoaPmuPer::whereIn('AccountID', $idArray)->delete();
                CoaPmuLink::whereIn('AccountID',$idArray)->delete();
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
        $CoaPmuPer = $request->post('CoaPmuPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CoaPmuPer as $value){
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
        CoaPmuPer::where(['AccountID' => $AccountID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('coa_pmu_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $AccountID;
        return json_encode($json);
    }
    public function getCoaPmuCateList(){
        $coa_pmuCateList = CoaPmuCateList::where('Inactive',0)->get();
        $coa_pmuCateValue = CoaPmuCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CoaPmuCateList'] = $coa_pmuCateList;
        $json['data']['CoaPmuCateValue'] = $coa_pmuCateValue;
        return json_encode($json);
    }
    public function getCoaPmuLink($id){
        $data = CoaPmuLink::where('AccountID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function updateLink(Request $request){
        $LinkArray = $request->post('CoaPmuLink');
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
        CoaPmuLink::where(['AccountID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('coa_pmu_link')->insert($arrLink);
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
        $data = CoaPmu::search($request);
        $json['data'] = $data;
        return json_encode($json);
    }
    //
    public  function  getListChild(Request $request){
        $auth = Auth::User();
        $ParentID = $request->ParentID;
        $query = CoaPmu::query();
        $query->select('coa_pmu.*');
        $query->leftJoin('coa_pmu_per', 'coa_pmu.AccountID', '=', 'coa_pmu_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_pmu_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_pmu.AccessType', '=', 2);
                });
        });
        $query->where('coa_pmu.ParentID', $ParentID);
        $query->groupBy('coa_pmu.AccountID')->orderBy('coa_pmu.AccountNo');
        $data = $query->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
?>
