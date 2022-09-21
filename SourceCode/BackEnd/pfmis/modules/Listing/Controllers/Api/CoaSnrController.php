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
use Module\Listing\Models\CoaSnr;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Employee;
use Module\Listing\Models\CoaSnrCate;
use Module\Listing\Models\CoaSnrCateList;
use Module\Listing\Models\CoaSnrCateValue;
use Module\Listing\Models\CoaSnrPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CoaSnrLink;

use function GuzzleHttp\Psr7\_parse_request_uri;

class CoaSnrController extends Controller{
    public function list(Request $request){
        $data = CoaSnr::search($request);
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('CoaSnr',['select'=>['AccountNo','AccountName'],'conditions' => []]),'HTTKQG.xlsx');
    }
    public function create(){
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        $coa_snr = CoaSnr::all();
        $employee = Employee::all();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
        $json['data']['BalanceTypeOptions'] = $BalanceTypeOptions;
        $json['data']['coa_snr'] = $coa_snr;
        $json['data']['employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'AccountNo' => 'required|unique:coa_snr',
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
            $coa_snr_parent = CoaSnr::find($request->ParentID);
            $level = $coa_snr_parent->Level + 1;
        }
        $coa_snr = new CoaSnr([
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
        $coa_snr->save();
        $CoaSnrPer = new CoaSnrPer([
            'AccountID' => $coa_snr->AccountID,
            'AccountName'=>$coa_snr->AccountName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName'=> Auth::user()->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CoaSnrPer->save();

        // CoaSnrCate
        $arrInsert= [];
        if($request->post('CoaSnrCate')){
            foreach ($request->post('CoaSnrCate') as $key=>$item){
                if($item['CateID']){
                    $arrInsert[] = [
                        'AccountID'=>$coa_snr->AccountID,
                        'CateID'=>$item['CateID'],
                        'CateValue'=>$item['CateValue']
                    ];
                }
            }
            DB::table('coa_snr_cate')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $coa_snr->AccountID;
        return json_encode($json);
    }
    public function view($id){
        $data = CoaSnr::find($id);
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $parent = CoaSnr::find($data->ParentID);
                if(isset($parent->AccountNo) && isset($parent->AccountName)){
                    $arrParent = [
                        'AccountNo' => $parent->AccountNo,
                        'AccountName' => $parent->AccountName,
                    ];
                }
            }
            $CoaSnrPerEmployee = CoaSnrPer::where([
                'AccountID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID,
            ])->first();
            $EmployeeLogin = Employee::where('EmployeeID','=',Auth::user()->EmployeeID)->first();
            if(!$CoaSnrPerEmployee){
                if($data->AccessType==2){
                    // Tài liệu công khai
                    $CoaSnrPerEmployee = new CoaSnrPer([
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
                        $CoaSnrPerEmployee = new CoaSnrPer([
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
                    $CoaSnrPerEmployee = new CoaSnrPer([
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

            if ($CoaSnrPerEmployee) {
                if (strpos(',' . $CoaSnrPerEmployee->AccessField . ',', ',all,')) {
                    $CoaSnrPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $CoaSnrPerEmployee->EditField . ',', ',all,')){
                    $CoaSnrPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $CoaSnrPer = CoaSnrPer::where('AccountID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $CoaSnr = CoaSnr::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $CoaSnrCate = CoaSnrCate::where('AccountID',$id)->get();
            $arrCate = [];
            foreach($CoaSnrCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $CoaSnrCateList = CoaSnrCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $CoaSnrCateValue = CoaSnrCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['CoaSnr'] = $CoaSnr;
            $json['Employee'] = $Employee;
            $json['CoaSnrPer'] = $CoaSnrPer;
            $json['CoaSnrPerEmployee'] = $CoaSnrPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
            $json['data']['BalanceTypeOptions']= $BalanceTypeOptions;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CoaSnrCate'] = $CoaSnrCate;
            $json['data']['CoaSnrCateList'] = $CoaSnrCateList;
            $json['data']['CoaSnrCateValue'] = $CoaSnrCateValue;
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
        $data = CoaSnr::find($id);
        $coa_snr = CoaSnr::all();
        if(old('AccountNo')){
            $data->AccountNo = old('AccountNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['coa_snr'] = $coa_snr;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function update(Request $request, $id){

        $coa_snr = CoaSnr::find($id);
        if(!$coa_snr){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_snr->AccountNo !== $request->AccountNo){
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
            $coa_snr->AccountNo = $request->AccountNo;
            if(empty($request->ParentID)){
                $level = 1;
            } else{
                $parent = CoaSnr::find($request->ParentID);
                $level = $parent->Level + 1;
            }
            $coa_snr->Level = $level;
        }
        $coa_snr->AccountName = $request->AccountName;
        $coa_snr->ParentID = $request->ParentID;
        $coa_snr->BalanceType = $request->BalanceType;
        $coa_snr->Note = $request->Note;
        $coa_snr->Detail = $request->Detail;
        $coa_snr->save();

        // coa_snr cate
        if($request->post('CoaSnrCate')){
            $CoaSnrCate = $request->post('CoaSnrCate');
            CoaSnrCate::where('AccountID',$id)->delete();
            $arrInsert = [];
            foreach($CoaSnrCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "AccountID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('coa_snr_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }
    public function delete($id){
        $CoaSnr = CoaSnr::find($id);
        $auth = Auth::user();
        if($CoaSnr){
            if ($auth->UserType === 1) {
                $CoaSnr->delete();
                CoaSnrCate::where('AccountID', $id)->delete();
                CoaSnrPer::where('AccountID', $id)->delete();
                CoaSnrLink::where('AccountID',$id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $isPer = CoaSnrPer::where([
                    'AccountID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($CoaSnr->UserIDCreated === $auth->UserID)){
                    $CoaSnr->delete();
                    CoaSnrCate::where('AccountID', $id)->delete();
                    CoaSnrPer::where('AccountID', $id)->delete();
                    CoaSnrLink::where('AccountID',$id)->delete();
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
            CoaSnr::destroy($idArray);
            CoaSnrCate::whereIn('AccountID', $idArray)->delete();
            CoaSnrPer::whereIn('AccountID', $idArray)->delete();
            CoaSnrLink::where('AccountID',$idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $coaSnrsPer = CoaSnrPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('coa_snr_per')
                    ->whereIn('AccountID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('AccountID');
            })->get();

            foreach ($coaSnrsPer as $coaSnrPer) {
                if ($coaSnrPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                CoaSnr::destroy($idArray);
                CoaSnrCate::whereIn('AccountID', $idArray)->delete();
                CoaSnrPer::whereIn('AccountID', $idArray)->delete();
                CoaSnrLink::whereIn('AccountID',$idArray)->delete();
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
        $CoaSnrPer = $request->post('CoaSnrPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CoaSnrPer as $value){
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
        CoaSnrPer::where(['AccountID' => $AccountID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('coa_snr_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $AccountID;
        return json_encode($json);
    }
    public function getCoaSnrCateList(){
        $coa_snrCateList = CoaSnrCateList::where('Inactive',0)->get();
        $coa_snrCateValue = CoaSnrCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CoaSnrCateList'] = $coa_snrCateList;
        $json['data']['CoaSnrCateValue'] = $coa_snrCateValue;
        return json_encode($json);
    }
    public function getCoaSnrLink($id){
        $data = CoaSnrLink::where('AccountID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function updateLink(Request $request){
        $LinkArray = $request->post('CoaSnrLink');
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
        CoaSnrLink::where(['AccountID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('coa_snr_link')->insert($arrLink);
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
        $data = CoaSnr::search($request);
        $json['data'] = $data;
        return json_encode($json);
    }
}
?>
