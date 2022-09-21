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
use Module\Listing\Models\CoaEas;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Employee;
use Module\Listing\Models\CoaEasCate;
use Module\Listing\Models\CoaEasCateList;
use Module\Listing\Models\CoaEasCateValue;
use Module\Listing\Models\CoaEasPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CoaEasLink;

use function GuzzleHttp\Psr7\_parse_request_uri;

class CoaEasController extends Controller{
    public function list(Request $request){
        $data = CoaEas::search($request);
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('CoaEas',['select'=>['AccountNo','AccountName'],'conditions' => []]),'HTTKQG.xlsx');
    }
    public function create(){
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        $coa_eas = CoaEas::all();
        $employee = Employee::all();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
        $json['data']['BalanceTypeOptions'] = $BalanceTypeOptions;
        $json['data']['coa_eas'] = $coa_eas;
        $json['data']['employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'AccountNo' => 'required|unique:coa_eas',
            'AccountName' => 'required',
        ],[
            'AccountNo.required' => 'Mã HTTK Doanh nghiệp không được để trống',
            'AccountNo.unique' => 'Mã HTTK Doanh nghiệp đã tồn tại',
            'AccountName.required' => 'Tên HTTK Doanh nghiệp không được để trống',

        ]);
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Thất bại';
            $json['data'] = $validator->errors();
            return json_encode($json);
        }
        // set Parent and Level
        if($request->ParentID){
            $parent = CoaEas::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
        } else {
            $level = 1;
        }
        $coa_eas = new CoaEas([
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
        $coa_eas->save();
        $CoaEasPer = new CoaEasPer([
            'AccountID' => $coa_eas->AccountID,
            'AccountNo' => $coa_eas->AccountNo,
            'AccountName'=>$coa_eas->AccountName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName'=> Auth::user()->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CoaEasPer->save();

        // CoaEasCate
        $arrInsert= [];
        if($request->post('CoaEasCate')){
            foreach ($request->post('CoaEasCate') as $key=>$item){
                if($item['CateID']){
                    $arrInsert[] = [
                        'AccountID'=>$coa_eas->AccountID,
                        'CateID'=>$item['CateID'],
                        'CateValue'=>$item['CateValue']
                    ];
                }
            }
            DB::table('coa_eas_cate')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $coa_eas->AccountID;
        return json_encode($json);
    }
    public function view($id){
        $query = CoaEas::query();
        $data = CoaEas::find($id);
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $query->where('coa_eas.AccountID', '=', $data->ParentID);
                $parent = $query->first();
                if(isset($parent->AccountNo) && isset($parent->AccountName)){
                    $arrParent = [
                        'AccountNo' => $parent->AccountNo,
                        'AccountName' => $parent->AccountName,
                    ];
                }
            }
            $CoaEasPerEmployee = CoaEasPer::where([
                'AccountID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID,
            ])->first();
            $EmployeeLogin = Employee::where('EmployeeID','=',Auth::user()->EmployeeID)->first();
            if(!$CoaEasPerEmployee){
                if($data->AccessType==2){
                    // Tài liệu công khai
                    $CoaEasPerEmployee = new CoaEasPer([
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
                        $CoaEasPerEmployee = new CoaEasPer([
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
                    $CoaEasPerEmployee = new CoaEasPer([
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

            if ($CoaEasPerEmployee) {
                if (strpos(',' . $CoaEasPerEmployee->AccessField . ',', ',all,')) {
                    $CoaEasPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $CoaEasPerEmployee->EditField . ',', ',all,')){
                    $CoaEasPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $CoaEasPer = CoaEasPer::where('AccountID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $CoaEas = CoaEas::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $CoaEasCate = CoaEasCate::where('AccountID',$id)->get();
            $arrCate = [];
            foreach($CoaEasCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $CoaEasCateList = CoaEasCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $CoaEasCateValue = CoaEasCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['CoaEas'] = $CoaEas;
            $json['Employee'] = $Employee;
            $json['CoaEasPer'] = $CoaEasPer;
            $json['CoaEasPerEmployee'] = $CoaEasPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
            $json['data']['BalanceTypeOptions']= $BalanceTypeOptions;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CoaEasCate'] = $CoaEasCate;
            $json['data']['CoaEasCateList'] = $CoaEasCateList;
            $json['data']['CoaEasCateValue'] = $CoaEasCateValue;
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
        $data = CoaEas::find($id);
        $coa_eas = CoaEas::all();
        if(old('AccountNo')){
            $data->AccountNo = old('AccountNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['coa_eas'] = $coa_eas;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function update(Request $request, $id){

        $coa_eas = CoaEas::find($id);
        if(!$coa_eas){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_eas->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_eas->ParentID !== $request->ParentID){
            $validator = Validator::make($request->all(), [
                'AccountNo' => 'required:coa_eas',
                'AccountName'=> 'required:coa_eas',
            ],
                [
                    'AccountNo.required' => 'Mã hệ thống tài khoản doanh nghiệp không được để trống',
                    'AccountName.required' => 'Tên hệ thống tài khoản doanh nghiệp không được để trống',
                ]
            );

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $coa_eas->AccountNo = $request->AccountNo;
            // set for new Parent
            if($request->ParentID){
                $parent = CoaEas::find($request->ParentID);
                $parent->Detail = 0;
                $parent->save();
                $level = $parent->Level + 1;
            }else{
                $level = 1;
            }
            $coa_eas->Level = $level;
            $coa_eas->Detail = 1;
            // set for old Parents
            if($coa_eas->ParentID){
                $parentOld = CoaEas::find($coa_eas->ParentID);
                $count = DB::table('coa_eas')->where('ParentID','=', $parentOld->AccountID)->count();
                if($count < 2){
                    $parentOld->Detail = 1;
                    $parentOld->save();
                }
            }
        }
        $coa_eas->AccountName = $request->AccountName;
        $coa_eas->ParentID = $request->ParentID;
        $coa_eas->ParentNo = $request->ParentNo;
        $coa_eas->BalanceType = $request->BalanceType;
        $coa_eas->Note = $request->Note;
        $coa_eas->save();

        // coa_eas cate
        if($request->post('CoaEasCate')){
            $CoaEasCate = $request->post('CoaEasCate');
            CoaEasCate::where('AccountID',$id)->delete();
            $arrInsert = [];
            foreach($CoaEasCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "AccountID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('coa_eas_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $coa_eas->AccountID;
        return $json;
    }
    public function delete($id){
        $CoaEas = coaEas::find($id);
        $auth = Auth::user();
        if ($CoaEas) {
            if($CoaEas->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
            // set for old Parent
            if($CoaEas->ParentID){
                $parent = coaEas::find($CoaEas->ParentID);
                $count = DB::table('coa_eas')->where('ParentID', '=', $parent->AccountID)->count();
                if($count < 2){
                    $parent->Detail = 1;
                    $parent->save();
                }
            }
            DB::table('coa_eas_cate')->where('AccountID', '=', $CoaEas->AccountID)->delete();
            $CoaEas->delete();

        }
        if($CoaEas){
            if ($auth->UserType === 1) {
                $CoaEas->delete();
                CoaEasCate::where('AccountID', $id)->delete();
                CoaEasPer::where('AccountID', $id)->delete();
                CoaEasLink::where('AccountID',$id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $isPer = CoaEasPer::where([
                    'AccountID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($CoaEas->UserIDCreated === $auth->UserID)){
                    $CoaEas->delete();
                    CoaEasCate::where('AccountID', $id)->delete();
                    CoaEasPer::where('AccountID', $id)->delete();
                    CoaEasLink::where('AccountID',$id)->delete();
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
            $data = CoaEas::find($id);
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        DB::table('coa_eas_cate')->whereIn('AccountID', $IDs)->delete();
        CoaEas::destroy($IDs);
        $sql = "UPDATE coa_eas
                SET Detail = 1
                WHERE AccountID NOT IN (SELECT ParentID FROM (select ParentID from coa_eas WHERE ParentID is not null) t)";
        DB::statement($sql);
        CoaEasCate::whereIn('AccountID',$IDs)->delete();
        CoaEasLink::whereIn('AccountID',$IDs)->delete();
        CoaEasPer::whereIn('AccountID',$IDs)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
        $auth = Auth::user();
        $idArray = $request->post('array_id');
        if ($auth->UserType === 1) {
            CoaEas::destroy($idArray);
            CoaEasCate::whereIn('AccountID', $idArray)->delete();
            CoaEasPer::whereIn('AccountID', $idArray)->delete();
            CoaEasLink::where('AccountID',$idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $coaEassPer = CoaEasPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('coa_eas_per')
                    ->whereIn('AccountID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('AccountID');
            })->get();

            foreach ($coaEassPer as $coaEasPer) {
                if ($coaEasPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                CoaEas::destroy($idArray);
                CoaEasCate::whereIn('AccountID', $idArray)->delete();
                CoaEasPer::whereIn('AccountID', $idArray)->delete();
                CoaEasLink::whereIn('AccountID',$idArray)->delete();
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
        $CoaEasPer = $request->post('CoaEasPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CoaEasPer as $value){
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
        CoaEasPer::where(['AccountID' => $AccountID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('coa_eas_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $AccountID;
        return json_encode($json);
    }
    public function getCoaEasCateList(){
        $coa_easCateList = CoaEasCateList::where('Inactive',0)->get();
        $coa_easCateValue = CoaEasCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CoaEasCateList'] = $coa_easCateList;
        $json['data']['CoaEasCateValue'] = $coa_easCateValue;
        return json_encode($json);
    }
    public function getCoaEasLink($id){
        $data = CoaEasLink::where('AccountID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function updateLink(Request $request){
        $LinkArray = $request->post('CoaEasLink');
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
        CoaEasLink::where(['AccountID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('coa_eas_link')->insert($arrLink);
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
        $data = CoaEas::search($request);
        $json['data'] = $data;
        return json_encode($json);
    }
    //
    public  function  getListChild(Request $request){
        $auth = Auth::User();
        $ParentID = $request->ParentID;
        $query = CoaEas::query();
        $query->select('coa_eas.*');
        $query->leftJoin('coa_eas_per', 'coa_eas.AccountID', '=', 'coa_eas_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_eas_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_eas.AccessType', '=', 2);
                });
        });
        $query->where('coa_eas.ParentID', $ParentID);
        $query->groupBy('coa_eas.AccountID')->orderBy('coa_eas.AccountNo');
        $data = $query->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
?>
