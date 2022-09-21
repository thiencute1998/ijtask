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
use Module\Listing\Models\CoaSna;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Employee;
use Module\Listing\Models\CoaSnaCate;
use Module\Listing\Models\CoaSnaCateList;
use Module\Listing\Models\CoaSnaCateValue;
use Module\Listing\Models\CoaSnaPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CoaSnaLink;

use function GuzzleHttp\Psr7\_parse_request_uri;

class CoaSnaController extends Controller{
    public function list(Request $request){
        $data = CoaSna::search($request);
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
       return Excel::download(new ModelExport('CoaSna',['select'=>['AccountNo','AccountName'],'conditions' => []]),'HTTKQG.xlsx');
    }
    public function create(){
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        $coa_sna = CoaSna::all();
        $employee = Employee::all();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
        $json['data']['BalanceTypeOptions'] = $BalanceTypeOptions;
        $json['data']['coa_sna'] = $coa_sna;
        $json['data']['employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'AccountNo' => 'required|unique:coa_sna',
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
            $coa_sna_parent = CoaSna::find($request->ParentID);
            $level = $coa_sna_parent->Level + 1;
        }
        $coa_sna = new CoaSna([
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
        $coa_sna->save();
        $CoaSnaPer = new CoaSnaPer([
            'AccountID' => $coa_sna->AccountID,
            'AccountName'=>$coa_sna->AccountName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName'=> Auth::user()->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CoaSnaPer->save();

        // CoaSnaCate
        $arrInsert= [];
        if($request->post('CoaSnaCate')){
            foreach ($request->post('CoaSnaCate') as $key=>$item){
                if($item['CateID']){
                    $arrInsert[] = [
                        'AccountID'=>$coa_sna->AccountID,
                        'CateID'=>$item['CateID'],
                        'CateValue'=>$item['CateValue']
                    ];
                }
            }
            DB::table('coa_sna_cate')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $coa_sna->AccountID;
        return json_encode($json);
    }
    public function view($id){
        $data = CoaSna::find($id);
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $parent = CoaSna::find($data->ParentID);
                if(isset($parent->AccountNo) && isset($parent->AccountName)){
                    $arrParent = [
                      'AccountNo' => $parent->AccountNo,
                      'AccountName' => $parent->AccountName,
                    ];
                }
            }
            $CoaSnaPerEmployee = CoaSnaPer::where([
                'AccountID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID,
            ])->first();
            $EmployeeLogin = Employee::where('EmployeeID','=',Auth::user()->EmployeeID)->first();
            if(!$CoaSnaPerEmployee){
                if($data->AccessType==2){
                    // Tài liệu công khai
                    $CoaSnaPerEmployee = new CoaSnaPer([
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
                        $CoaSnaPerEmployee = new CoaSnaPer([
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
                    $CoaSnaPerEmployee = new CoaSnaPer([
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

             if ($CoaSnaPerEmployee) {
                if (strpos(',' . $CoaSnaPerEmployee->AccessField . ',', ',all,')) {
                    $CoaSnaPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $CoaSnaPerEmployee->EditField . ',', ',all,')){
                    $CoaSnaPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $CoaSnaPer = CoaSnaPer::where('AccountID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $CoaSna = CoaSna::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $CoaSnaCate = CoaSnaCate::where('AccountID',$id)->get();
            $arrCate = [];
            foreach($CoaSnaCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $CoaSnaCateList = CoaSnaCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $CoaSnaCateValue = CoaSnaCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['CoaSna'] = $CoaSna;
            $json['Employee'] = $Employee;
            $json['CoaSnaPer'] = $CoaSnaPer;
            $json['CoaSnaPerEmployee'] = $CoaSnaPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
            $json['data']['BalanceTypeOptions']= $BalanceTypeOptions;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CoaSnaCate'] = $CoaSnaCate;
            $json['data']['CoaSnaCateList'] = $CoaSnaCateList;
            $json['data']['CoaSnaCateValue'] = $CoaSnaCateValue;
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
        $data = CoaSna::find($id);
        $coa_sna = CoaSna::all();
        if(old('AccountNo')){
            $data->AccountNo = old('AccountNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['coa_sna'] = $coa_sna;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function update(Request $request, $id){

        $coa_sna = CoaSna::find($id);
        if(!$coa_sna){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_sna->AccountNo !== $request->AccountNo){
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
            $coa_sna->AccountNo = $request->AccountNo;
           if(empty($request->ParentID)){
                $level = 1;
           } else{
               $parent = CoaSna::find($request->ParentID);
               $level = $parent->Level + 1;
           }
           $coa_sna->Level = $level;
        }
        $coa_sna->AccountName = $request->AccountName;
        $coa_sna->ParentID = $request->ParentID;
        $coa_sna->BalanceType = $request->BalanceType;
        $coa_sna->Note = $request->Note;
        $coa_sna->Detail = $request->Detail;
        $coa_sna->save();

        // coa_sna cate
        if($request->post('CoaSnaCate')){
            $CoaSnaCate = $request->post('CoaSnaCate');
            CoaSnaCate::where('AccountID',$id)->delete();
            $arrInsert = [];
            foreach($CoaSnaCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "AccountID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('coa_sna_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }
    public function delete($id){
        $CoaSna = CoaSna::find($id);
        $auth = Auth::user();
        if($CoaSna){
            if ($auth->UserType === 1) {
                $CoaSna->delete();
                CoaSnaCate::where('AccountID', $id)->delete();
                CoaSnaPer::where('AccountID', $id)->delete();
                CoaSnaLink::where('AccountID',$id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $isPer = CoaSnaPer::where([
                    'AccountID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($CoaSna->UserIDCreated === $auth->UserID)){
                    $CoaSna->delete();
                    CoaSnaCate::where('AccountID', $id)->delete();
                    CoaSnaPer::where('AccountID', $id)->delete();
                    CoaSnaLink::where('AccountID',$id)->delete();
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
            CoaSna::destroy($idArray);
            CoaSnaCate::whereIn('AccountID', $idArray)->delete();
            CoaSnaPer::whereIn('AccountID', $idArray)->delete();
            CoaSnaLink::where('AccountID',$idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $coaSnasPer = CoaSnaPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('coa_sna_per')
                    ->whereIn('AccountID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('AccountID');
            })->get();

            foreach ($coaSnasPer as $coaSnaPer) {
                if ($coaSnaPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                CoaSna::destroy($idArray);
                CoaSnaCate::whereIn('AccountID', $idArray)->delete();
                CoaSnaPer::whereIn('AccountID', $idArray)->delete();
                CoaSnaLink::whereIn('AccountID',$idArray)->delete();
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
        $CoaSnaPer = $request->post('CoaSnaPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CoaSnaPer as $value){
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
        CoaSnaPer::where(['AccountID' => $AccountID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('coa_sna_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $AccountID;
        return json_encode($json);
    }
    public function getCoaSnaCateList(){
        $coa_snaCateList = CoaSnaCateList::where('Inactive',0)->get();
        $coa_snaCateValue = CoaSnaCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CoaSnaCateList'] = $coa_snaCateList;
        $json['data']['CoaSnaCateValue'] = $coa_snaCateValue;
        return json_encode($json);
    }
    public function getCoaSnaLink($id){
        $data = CoaSnaLink::where('AccountID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function updateLink(Request $request){
        $LinkArray = $request->post('CoaSnaLink');
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
        CoaSnaLink::where(['AccountID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('coa_sna_link')->insert($arrLink);
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
        $data = CoaSna::search($request);
        $json['data'] = $data;
        return json_encode($json);
    }
}
?>
