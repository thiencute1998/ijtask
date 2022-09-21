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
use Module\Listing\Models\CoaPcf;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Employee;
use Module\Listing\Models\CoaPcfCate;
use Module\Listing\Models\CoaPcfCateList;
use Module\Listing\Models\CoaPcfCateValue;
use Module\Listing\Models\CoaPcfPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CoaPcfLink;

use function GuzzleHttp\Psr7\_parse_request_uri;

class CoaPcfController extends Controller{
    public function list(Request $request){
        $data = CoaPcf::search($request);
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('CoaPcf',['select'=>['AccountNo','AccountName'],'conditions' => []]),'HTTKQG.xlsx');
    }
    public function create(){
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        $coa_pcf = CoaPcf::all();
        $employee = Employee::all();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
        $json['data']['BalanceTypeOptions'] = $BalanceTypeOptions;
        $json['data']['coa_pcf'] = $coa_pcf;
        $json['data']['employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'AccountNo' => 'required|unique:coa_pcf',
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
            $coa_pcf_parent = CoaPcf::find($request->ParentID);
            $level = $coa_pcf_parent->Level + 1;
        }
        $coa_pcf = new CoaPcf([
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
        $coa_pcf->save();
        $CoaPcfPer = new CoaPcfPer([
            'AccountID' => $coa_pcf->AccountID,
            'AccountName'=>$coa_pcf->AccountName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName'=> Auth::user()->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CoaPcfPer->save();

        // CoaPcfCate
        $arrInsert= [];
        if($request->post('CoaPcfCate')){
            foreach ($request->post('CoaPcfCate') as $key=>$item){
                if($item['CateID']){
                    $arrInsert[] = [
                        'AccountID'=>$coa_pcf->AccountID,
                        'CateID'=>$item['CateID'],
                        'CateValue'=>$item['CateValue']
                    ];
                }
            }
            DB::table('coa_pcf_cate')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $coa_pcf->AccountID;
        return json_encode($json);
    }
    public function view($id){
        $data = CoaPcf::find($id);
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $parent = CoaPcf::find($data->ParentID);
                if(isset($parent->AccountNo) && isset($parent->AccountName)){
                    $arrParent = [
                        'AccountNo' => $parent->AccountNo,
                        'AccountName' => $parent->AccountName,
                    ];
                }
            }
            $CoaPcfPerEmployee = CoaPcfPer::where([
                'AccountID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID,
            ])->first();
            $EmployeeLogin = Employee::where('EmployeeID','=',Auth::user()->EmployeeID)->first();
            if(!$CoaPcfPerEmployee){
                if($data->AccessType==2){
                    // Tài liệu công khai
                    $CoaPcfPerEmployee = new CoaPcfPer([
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
                        $CoaPcfPerEmployee = new CoaPcfPer([
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
                    $CoaPcfPerEmployee = new CoaPcfPer([
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

            if ($CoaPcfPerEmployee) {
                if (strpos(',' . $CoaPcfPerEmployee->AccessField . ',', ',all,')) {
                    $CoaPcfPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $CoaPcfPerEmployee->EditField . ',', ',all,')){
                    $CoaPcfPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $CoaPcfPer = CoaPcfPer::where('AccountID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $CoaPcf = CoaPcf::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $CoaPcfCate = CoaPcfCate::where('AccountID',$id)->get();
            $arrCate = [];
            foreach($CoaPcfCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $CoaPcfCateList = CoaPcfCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $CoaPcfCateValue = CoaPcfCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['CoaPcf'] = $CoaPcf;
            $json['Employee'] = $Employee;
            $json['CoaPcfPer'] = $CoaPcfPer;
            $json['CoaPcfPerEmployee'] = $CoaPcfPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
            $json['data']['BalanceTypeOptions']= $BalanceTypeOptions;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CoaPcfCate'] = $CoaPcfCate;
            $json['data']['CoaPcfCateList'] = $CoaPcfCateList;
            $json['data']['CoaPcfCateValue'] = $CoaPcfCateValue;
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
        $data = CoaPcf::find($id);
        $coa_pcf = CoaPcf::all();
        if(old('AccountNo')){
            $data->AccountNo = old('AccountNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['coa_pcf'] = $coa_pcf;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function update(Request $request, $id){

        $coa_pcf = CoaPcf::find($id);
        if(!$coa_pcf){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_pcf->AccountNo !== $request->AccountNo){
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
            $coa_pcf->AccountNo = $request->AccountNo;
            if(empty($request->ParentID)){
                $level = 1;
            } else{
                $parent = CoaPcf::find($request->ParentID);
                $level = $parent->Level + 1;
            }
            $coa_pcf->Level = $level;
        }
        $coa_pcf->AccountName = $request->AccountName;
        $coa_pcf->ParentID = $request->ParentID;
        $coa_pcf->BalanceType = $request->BalanceType;
        $coa_pcf->Note = $request->Note;
        $coa_pcf->Detail = $request->Detail;
        $coa_pcf->save();

        // coa_pcf cate
        if($request->post('CoaPcfCate')){
            $CoaPcfCate = $request->post('CoaPcfCate');
            CoaPcfCate::where('AccountID',$id)->delete();
            $arrInsert = [];
            foreach($CoaPcfCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "AccountID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('coa_pcf_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }
    public function delete($id){
        $CoaPcf = CoaPcf::find($id);
        $auth = Auth::user();
        if($CoaPcf){
            if ($auth->UserType === 1) {
                $CoaPcf->delete();
                CoaPcfCate::where('AccountID', $id)->delete();
                CoaPcfPer::where('AccountID', $id)->delete();
                CoaPcfLink::where('AccountID',$id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $isPer = CoaPcfPer::where([
                    'AccountID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($CoaPcf->UserIDCreated === $auth->UserID)){
                    $CoaPcf->delete();
                    CoaPcfCate::where('AccountID', $id)->delete();
                    CoaPcfPer::where('AccountID', $id)->delete();
                    CoaPcfLink::where('AccountID',$id)->delete();
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
            CoaPcf::destroy($idArray);
            CoaPcfCate::whereIn('AccountID', $idArray)->delete();
            CoaPcfPer::whereIn('AccountID', $idArray)->delete();
            CoaPcfLink::where('AccountID',$idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $coaPcfsPer = CoaPcfPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('coa_pcf_per')
                    ->whereIn('AccountID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('AccountID');
            })->get();

            foreach ($coaPcfsPer as $coaPcfPer) {
                if ($coaPcfPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                CoaPcf::destroy($idArray);
                CoaPcfCate::whereIn('AccountID', $idArray)->delete();
                CoaPcfPer::whereIn('AccountID', $idArray)->delete();
                CoaPcfLink::whereIn('AccountID',$idArray)->delete();
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
        $CoaPcfPer = $request->post('CoaPcfPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CoaPcfPer as $value){
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
        CoaPcfPer::where(['AccountID' => $AccountID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('coa_pcf_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $AccountID;
        return json_encode($json);
    }
    public function getCoaPcfCateList(){
        $coa_pcfCateList = CoaPcfCateList::where('Inactive',0)->get();
        $coa_pcfCateValue = CoaPcfCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CoaPcfCateList'] = $coa_pcfCateList;
        $json['data']['CoaPcfCateValue'] = $coa_pcfCateValue;
        return json_encode($json);
    }
    public function getCoaPcfLink($id){
        $data = CoaPcfLink::where('AccountID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function updateLink(Request $request){
        $LinkArray = $request->post('CoaPcfLink');
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
        CoaPcfLink::where(['AccountID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('coa_pcf_link')->insert($arrLink);
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
        $data = CoaPcf::search($request);
        $json['data'] = $data;
        return json_encode($json);
    }
}
?>
