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
use Module\Listing\Models\CoaLdi;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Employee;
use Module\Listing\Models\CoaLdiCate;
use Module\Listing\Models\CoaLdiCateList;
use Module\Listing\Models\CoaLdiCateValue;
use Module\Listing\Models\CoaLdiPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CoaLdiLink;

use function GuzzleHttp\Psr7\_parse_request_uri;

class CoaLdiController extends Controller{
    public function list(Request $request){
        $data = CoaLdi::search($request);
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('CoaLdi',['select'=>['AccountNo','AccountName'],'conditions' => []]),'HTTKQG.xlsx');
    }
    public function create(){
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        $coa_ldi = CoaLdi::all();
        $employee = Employee::all();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
        $json['data']['BalanceTypeOptions'] = $BalanceTypeOptions;
        $json['data']['coa_ldi'] = $coa_ldi;
        $json['data']['employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'AccountNo' => 'required|unique:coa_ldi',
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
            $coa_ldi_parent = CoaLdi::find($request->ParentID);
            $level = $coa_ldi_parent->Level + 1;
        }
        $coa_ldi = new CoaLdi([
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
        $coa_ldi->save();
        $CoaLdiPer = new CoaLdiPer([
            'AccountID' => $coa_ldi->AccountID,
            'AccountName'=>$coa_ldi->AccountName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName'=> Auth::user()->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CoaLdiPer->save();

        // CoaLdiCate
        $arrInsert= [];
        if($request->post('CoaLdiCate')){
            foreach ($request->post('CoaLdiCate') as $key=>$item){
                if($item['CateID']){
                    $arrInsert[] = [
                        'AccountID'=>$coa_ldi->AccountID,
                        'CateID'=>$item['CateID'],
                        'CateValue'=>$item['CateValue']
                    ];
                }
            }
            DB::table('coa_ldi_cate')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $coa_ldi->AccountID;
        return json_encode($json);
    }
    public function view($id){
        $data = CoaLdi::find($id);
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $parent = CoaLdi::find($data->ParentID);
                if(isset($parent->AccountNo) && isset($parent->AccountName)){
                    $arrParent = [
                        'AccountNo' => $parent->AccountNo,
                        'AccountName' => $parent->AccountName,
                    ];
                }
            }
            $CoaLdiPerEmployee = CoaLdiPer::where([
                'AccountID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID,
            ])->first();
            $EmployeeLogin = Employee::where('EmployeeID','=',Auth::user()->EmployeeID)->first();
            if(!$CoaLdiPerEmployee){
                if($data->AccessType==2){
                    // Tài liệu công khai
                    $CoaLdiPerEmployee = new CoaLdiPer([
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
                        $CoaLdiPerEmployee = new CoaLdiPer([
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
                    $CoaLdiPerEmployee = new CoaLdiPer([
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

            if ($CoaLdiPerEmployee) {
                if (strpos(',' . $CoaLdiPerEmployee->AccessField . ',', ',all,')) {
                    $CoaLdiPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $CoaLdiPerEmployee->EditField . ',', ',all,')){
                    $CoaLdiPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $CoaLdiPer = CoaLdiPer::where('AccountID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $CoaLdi = CoaLdi::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $CoaLdiCate = CoaLdiCate::where('AccountID',$id)->get();
            $arrCate = [];
            foreach($CoaLdiCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $CoaLdiCateList = CoaLdiCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $CoaLdiCateValue = CoaLdiCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['CoaLdi'] = $CoaLdi;
            $json['Employee'] = $Employee;
            $json['CoaLdiPer'] = $CoaLdiPer;
            $json['CoaLdiPerEmployee'] = $CoaLdiPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
            $json['data']['BalanceTypeOptions']= $BalanceTypeOptions;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CoaLdiCate'] = $CoaLdiCate;
            $json['data']['CoaLdiCateList'] = $CoaLdiCateList;
            $json['data']['CoaLdiCateValue'] = $CoaLdiCateValue;
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
        $data = CoaLdi::find($id);
        $coa_ldi = CoaLdi::all();
        if(old('AccountNo')){
            $data->AccountNo = old('AccountNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['coa_ldi'] = $coa_ldi;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function update(Request $request, $id){

        $coa_ldi = CoaLdi::find($id);
        if(!$coa_ldi){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_ldi->AccountNo !== $request->AccountNo){
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
            $coa_ldi->AccountNo = $request->AccountNo;
            if(empty($request->ParentID)){
                $level = 1;
            } else{
                $parent = CoaLdi::find($request->ParentID);
                $level = $parent->Level + 1;
            }
            $coa_ldi->Level = $level;
        }
        $coa_ldi->AccountName = $request->AccountName;
        $coa_ldi->ParentID = $request->ParentID;
        $coa_ldi->BalanceType = $request->BalanceType;
        $coa_ldi->Note = $request->Note;
        $coa_ldi->Detail = $request->Detail;
        $coa_ldi->save();

        // coa_ldi cate
        if($request->post('CoaLdiCate')){
            $CoaLdiCate = $request->post('CoaLdiCate');
            CoaLdiCate::where('AccountID',$id)->delete();
            $arrInsert = [];
            foreach($CoaLdiCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "AccountID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('coa_ldi_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }
    public function delete($id){
        $CoaLdi = CoaLdi::find($id);
        $auth = Auth::user();
        if($CoaLdi){
            if ($auth->UserType === 1) {
                $CoaLdi->delete();
                CoaLdiCate::where('AccountID', $id)->delete();
                CoaLdiPer::where('AccountID', $id)->delete();
                CoaLdiLink::where('AccountID',$id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $isPer = CoaLdiPer::where([
                    'AccountID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($CoaLdi->UserIDCreated === $auth->UserID)){
                    $CoaLdi->delete();
                    CoaLdiCate::where('AccountID', $id)->delete();
                    CoaLdiPer::where('AccountID', $id)->delete();
                    CoaLdiLink::where('AccountID',$id)->delete();
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
            CoaLdi::destroy($idArray);
            CoaLdiCate::whereIn('AccountID', $idArray)->delete();
            CoaLdiPer::whereIn('AccountID', $idArray)->delete();
            CoaLdiLink::where('AccountID',$idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $coaLdisPer = CoaLdiPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('coa_ldi_per')
                    ->whereIn('AccountID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('AccountID');
            })->get();

            foreach ($coaLdisPer as $coaLdiPer) {
                if ($coaLdiPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                CoaLdi::destroy($idArray);
                CoaLdiCate::whereIn('AccountID', $idArray)->delete();
                CoaLdiPer::whereIn('AccountID', $idArray)->delete();
                CoaLdiLink::whereIn('AccountID',$idArray)->delete();
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
        $CoaLdiPer = $request->post('CoaLdiPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CoaLdiPer as $value){
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
        CoaLdiPer::where(['AccountID' => $AccountID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('coa_ldi_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $AccountID;
        return json_encode($json);
    }
    public function getCoaLdiCateList(){
        $coa_ldiCateList = CoaLdiCateList::where('Inactive',0)->get();
        $coa_ldiCateValue = CoaLdiCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CoaLdiCateList'] = $coa_ldiCateList;
        $json['data']['CoaLdiCateValue'] = $coa_ldiCateValue;
        return json_encode($json);
    }
    public function getCoaLdiLink($id){
        $data = CoaLdiLink::where('AccountID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function updateLink(Request $request){
        $LinkArray = $request->post('CoaLdiLink');
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
        CoaLdiLink::where(['AccountID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('coa_ldi_link')->insert($arrLink);
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
        $data = CoaLdi::search($request);
        $json['data'] = $data;
        return json_encode($json);
    }
}
?>
