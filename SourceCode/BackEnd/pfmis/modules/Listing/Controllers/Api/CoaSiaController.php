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
use Module\Listing\Models\CoaSia;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Employee;
use Module\Listing\Models\CoaSiaCate;
use Module\Listing\Models\CoaSiaCateList;
use Module\Listing\Models\CoaSiaCateValue;
use Module\Listing\Models\CoaSiaPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CoaSiaLink;

use function GuzzleHttp\Psr7\_parse_request_uri;

class CoaSiaController extends Controller{
    public function list(Request $request){
        $data = CoaSia::search($request);
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('CoaSia',['select'=>['AccountNo','AccountName'],'conditions' => []]),'HTTKQG.xlsx');
    }
    public function create(){
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        $coa_sia = CoaSia::all();
        $employee = Employee::all();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
        $json['data']['BalanceTypeOptions'] = $BalanceTypeOptions;
        $json['data']['coa_sia'] = $coa_sia;
        $json['data']['employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'AccountNo' => 'required|unique:coa_sia',
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
        if(empty($request->ParentID)){
            $level =1;
        } else {
            $coa_sia_parent = CoaSia::find($request->ParentID);
            $level = $coa_sia_parent->Level + 1;
        }
        $coa_sia = new CoaSia([
            'AccountNo' => $request->AccountNo,
            'AccountName' => $request->AccountName,
            'ParentID' => $request->ParentID,
            'Level' => $level,
            'BalanceType'=>$request->BalanceType,
            'Detail' => $request->Detail,
            'Note' => $request->Note,
            'Loked' => $request->Locked,
            'Inactive' => $request->Inactive,
            'AccessType'=> ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated'=> Auth::user()->UserID,
            'AuthorizedPerson'=> Auth::user()->UserID,
        ]);
        $coa_sia->save();
        $CoaSiaPer = new CoaSiaPer([
            'AccountID' => $coa_sia->AccountID,
            'AccountName'=>$coa_sia->AccountName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName'=> Auth::user()->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CoaSiaPer->save();

        // CoaSiaCate
        $arrInsert= [];
        if($request->post('CoaSiaCate')){
            foreach ($request->post('CoaSiaCate') as $key=>$item){
                if($item['CateID']){
                    $arrInsert[] = [
                        'AccountID'=>$coa_sia->AccountID,
                        'CateID'=>$item['CateID'],
                        'CateValue'=>$item['CateValue']
                    ];
                }
            }
            DB::table('coa_sia_cate')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $coa_sia->AccountID;
        return json_encode($json);
    }
    public function view($id){
        $data = CoaSia::find($id);
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $parent = CoaSia::find($data->ParentID);
                if(isset($parent->AccountNo) && isset($parent->AccountName)){
                    $arrParent = [
                        'AccountNo' => $parent->AccountNo,
                        'AccountName' => $parent->AccountName,
                    ];
                }
            }
            $CoaSiaPerEmployee = CoaSiaPer::where([
                'AccountID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID,
            ])->first();
            $EmployeeLogin = Employee::where('EmployeeID','=',Auth::user()->EmployeeID)->first();
            if(!$CoaSiaPerEmployee){
                if($data->AccessType==2){
                    // Tài liệu công khai
                    $CoaSiaPerEmployee = new CoaSiaPer([
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
                        $CoaSiaPerEmployee = new CoaSiaPer([
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
                    }
                }
            }
            else{
                if($EmployeeLogin->UserID == $data->UserIDCreated || $EmployeeLogin->UserID == $data->AuthorizedPerson){
                    $CoaSiaPerEmployee = new CoaSiaPer([
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

            if ($CoaSiaPerEmployee) {
                if (strpos(',' . $CoaSiaPerEmployee->AccessField . ',', ',all,')) {
                    $CoaSiaPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $CoaSiaPerEmployee->EditField . ',', ',all,')){
                    $CoaSiaPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $CoaSiaPer = CoaSiaPer::where('AccountID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $CoaSia = CoaSia::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $CoaSiaCate = CoaSiaCate::where('AccountID',$id)->get();
            $arrCate = [];
            foreach($CoaSiaCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $CoaSiaCateList = CoaSiaCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $CoaSiaCateValue = CoaSiaCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['CoaSia'] = $CoaSia;
            $json['Employee'] = $Employee;
            $json['CoaSiaPer'] = $CoaSiaPer;
            $json['CoaSiaPerEmployee'] = $CoaSiaPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
            $json['data']['BalanceTypeOptions']= $BalanceTypeOptions;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CoaSiaCate'] = $CoaSiaCate;
            $json['data']['CoaSiaCateList'] = $CoaSiaCateList;
            $json['data']['CoaSiaCateValue'] = $CoaSiaCateValue;
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
        $data = CoaSia::find($id);
        $coa_sia = CoaSia::all();
        if(old('AccountNo')){
            $data->AccountNo = old('AccountNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['coa_sia'] = $coa_sia;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function update(Request $request, $id){

        $coa_sia = CoaSia::find($id);
        if(!$coa_sia){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_sia->AccountNo !== $request->AccountNo){
            $validator = Validator::make($request->all(), [
                'AccountNo' => 'required',
                'AccountName'=> 'required',
            ],
                [
                    'AccountNo.required' => 'Mã hệ thống tài khoản quốc gia không được để trống',
                    'AccountName.required' => 'Tên hệ thống tài khoản quốc gia không được để trống',
                ]);




            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $coa_sia->AccountNo = $request->AccountNo;
            if(empty($request->ParentID)){
                $level = 1;
            } else{
                $parent = CoaSia::find($request->ParentID);
                $level = $parent->Level + 1;
            }
            $coa_sia->Level = $level;
        }
        $coa_sia->AccountName = $request->AccountName;
        $coa_sia->ParentID = $request->ParentID;
        $coa_sia->BalanceType = $request->BalanceType;
        $coa_sia->Note = $request->Note;
        $coa_sia->Detail = $request->Detail;
        $coa_sia->save();

        // coa_sia cate
        if($request->post('CoaSiaCate')){
            $CoaSiaCate = $request->post('CoaSiaCate');
            CoaSiaCate::where('AccountID',$id)->delete();
            $arrInsert = [];
            foreach($CoaSiaCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "AccountID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('coa_sia_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }
    public function delete($id){
        $CoaSia = CoaSia::find($id);
        $auth = Auth::user();
        if($CoaSia){
            if ($auth->UserType === 1) {
                $CoaSia->delete();
                CoaSiaCate::where('AccountID', $id)->delete();
                CoaSiaPer::where('AccountID', $id)->delete();
                CoaSiaLink::where('AccountID',$id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $isPer = CoaSiaPer::where([
                    'AccountID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($CoaSia->UserIDCreated === $auth->UserID)){
                    $CoaSia->delete();
                    CoaSiaCate::where('AccountID', $id)->delete();
                    CoaSiaPer::where('AccountID', $id)->delete();
                    CoaSiaLink::where('AccountID',$id)->delete();
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
        $auth = Auth::user();
        $idArray = $request->post('array_id');
        if ($auth->UserType === 1) {
            CoaSia::destroy($idArray);
            CoaSiaCate::whereIn('AccountID', $idArray)->delete();
            CoaSiaPer::whereIn('AccountID', $idArray)->delete();
            CoaSiaLink::where('AccountID',$idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $coaSiasPer = CoaSiaPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('coa_sia_per')
                    ->whereIn('AccountID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('AccountID');
            })->get();

            foreach ($coaSiasPer as $coaSiaPer) {
                if ($coaSiaPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                CoaSia::destroy($idArray);
                CoaSiaCate::whereIn('AccountID', $idArray)->delete();
                CoaSiaPer::whereIn('AccountID', $idArray)->delete();
                CoaSiaLink::whereIn('AccountID',$idArray)->delete();
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
        $CoaSiaPer = $request->post('CoaSiaPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CoaSiaPer as $value){
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
        CoaSiaPer::where(['AccountID' => $AccountID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('coa_sia_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $AccountID;
        return json_encode($json);
    }
    public function getCoaSiaCateList(){
        $coa_siaCateList = CoaSiaCateList::where('Inactive',0)->get();
        $coa_siaCateValue = CoaSiaCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CoaSiaCateList'] = $coa_siaCateList;
        $json['data']['CoaSiaCateValue'] = $coa_siaCateValue;
        return json_encode($json);
    }
    public function getCoaSiaLink($id){
        $data = CoaSiaLink::where('AccountID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function updateLink(Request $request){
        $LinkArray = $request->post('CoaSiaLink');
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
        CoaSiaLink::where(['AccountID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('coa_sia_link')->insert($arrLink);
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
        $data = CoaSia::search($request);
        $json['data'] = $data;
        return json_encode($json);
    }
}
?>
