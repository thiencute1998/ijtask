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
use Module\Listing\Models\CoaScb;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Employee;
use Module\Listing\Models\CoaScbCate;
use Module\Listing\Models\CoaScbCateList;
use Module\Listing\Models\CoaScbCateValue;
use Module\Listing\Models\CoaScbPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CoaScbLink;

use function GuzzleHttp\Psr7\_parse_request_uri;

class CoaScbController extends Controller{
    public function list(Request $request){
        $data = CoaScb::search($request);
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('CoaScb',['select'=>['AccountNo','AccountName'],'conditions' => []]),'HTTKQG.xlsx');
    }
    public function create(){
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        $coa_scb = CoaScb::all();
        $employee = Employee::all();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
        $json['data']['BalanceTypeOptions'] = $BalanceTypeOptions;
        $json['data']['coa_scb'] = $coa_scb;
        $json['data']['employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'AccountNo' => 'required|unique:coa_scb',
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
            $parent = CoaScb::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
        } else {
            $level = 1;
        }
        $coa_scb = new CoaScb([
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
        $coa_scb->save();
        $CoaScbPer = new CoaScbPer([
            'AccountID' => $coa_scb->AccountID,
            'AccountNo' => $coa_scb->AccountNo,
            'AccountName'=>$coa_scb->AccountName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName'=> Auth::user()->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CoaScbPer->save();

        // CoaScbCate
        $arrInsert= [];
        if($request->post('CoaScbCate')){
            foreach ($request->post('CoaScbCate') as $key=>$item){
                if($item['CateID']){
                    $arrInsert[] = [
                        'AccountID'=>$coa_scb->AccountID,
                        'CateID'=>$item['CateID'],
                        'CateValue'=>$item['CateValue']
                    ];
                }
            }
            DB::table('coa_scb_cate')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $coa_scb->AccountID;
        return json_encode($json);
    }
    public function view($id){
        $query = CoaScb::query();
        $data = CoaScb::find($id);
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $query->where('coa_scb.AccountID', '=', $data->ParentID);
                $parent = $query->first();
                if(isset($parent->AccountNo) && isset($parent->AccountName)){
                    $arrParent = [
                        'AccountNo' => $parent->AccountNo,
                        'AccountName' => $parent->AccountName,
                    ];
                }
            }
            $CoaScbPerEmployee = CoaScbPer::where([
                'AccountID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID,
            ])->first();
            $EmployeeLogin = Employee::where('EmployeeID','=',Auth::user()->EmployeeID)->first();
            if(!$CoaScbPerEmployee){
                if($data->AccessType==2){
                    // Tài liệu công khai
                    $CoaScbPerEmployee = new CoaScbPer([
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
                        $CoaScbPerEmployee = new CoaScbPer([
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
                    $CoaScbPerEmployee = new CoaScbPer([
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

            if ($CoaScbPerEmployee) {
                if (strpos(',' . $CoaScbPerEmployee->AccessField . ',', ',all,')) {
                    $CoaScbPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $CoaScbPerEmployee->EditField . ',', ',all,')){
                    $CoaScbPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $CoaScbPer = CoaScbPer::where('AccountID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $CoaScb = CoaScb::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $CoaScbCate = CoaScbCate::where('AccountID',$id)->get();
            $arrCate = [];
            foreach($CoaScbCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $CoaScbCateList = CoaScbCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $CoaScbCateValue = CoaScbCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['CoaScb'] = $CoaScb;
            $json['Employee'] = $Employee;
            $json['CoaScbPer'] = $CoaScbPer;
            $json['CoaScbPerEmployee'] = $CoaScbPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
            $json['data']['BalanceTypeOptions']= $BalanceTypeOptions;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CoaScbCate'] = $CoaScbCate;
            $json['data']['CoaScbCateList'] = $CoaScbCateList;
            $json['data']['CoaScbCateValue'] = $CoaScbCateValue;
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
        $data = CoaScb::find($id);
        $coa_scb = CoaScb::all();
        if(old('AccountNo')){
            $data->AccountNo = old('AccountNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['coa_scb'] = $coa_scb;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function update(Request $request, $id){

        $coa_scb = CoaScb::find($id);
        if(!$coa_scb){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_scb->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_scb->ParentID !== $request->ParentID){
            $validator = Validator::make($request->all(), [
                'AccountNo' => 'required:coa_scb',
                'AccountName'=> 'required:coa_scb',
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
            $coa_scb->AccountNo = $request->AccountNo;
            // set for new Parent
            if($request->ParentID){
                $parent = CoaScb::find($request->ParentID);
                $parent->Detail = 0;
                $parent->save();
                $level = $parent->Level + 1;
            }else{
                $level = 1;
            }
            $coa_scb->Level = $level;
            $coa_scb->Detail = 1;
            // set for old Parents
            if($coa_scb->ParentID){
                $parentOld = CoaScb::find($coa_scb->ParentID);
                $count = DB::table('coa_scb')->where('ParentID','=', $parentOld->AccountID)->count();
                if($count < 2){
                    $parentOld->Detail = 1;
                    $parentOld->save();
                }
            }
        }
        $coa_scb->AccountName = $request->AccountName;
        $coa_scb->ParentID = $request->ParentID;
        $coa_scb->ParentNo = $request->ParentNo;
        $coa_scb->BalanceType = $request->BalanceType;
        $coa_scb->Note = $request->Note;
        $coa_scb->save();

        // coa_scb cate
        if($request->post('CoaScbCate')){
            $CoaScbCate = $request->post('CoaScbCate');
            CoaScbCate::where('AccountID',$id)->delete();
            $arrInsert = [];
            foreach($CoaScbCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "AccountID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('coa_scb_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $coa_scb->AccountID;
        return $json;
    }
    public function delete($id){
        $CoaScb = coaScb::find($id);
        $auth = Auth::user();
        if ($CoaScb) {
            if($CoaScb->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
            // set for old Parent
            if($CoaScb->ParentID){
                $parent = coaScb::find($CoaScb->ParentID);
                $count = DB::table('coa_scb')->where('ParentID', '=', $parent->AccountID)->count();
                if($count < 2){
                    $parent->Detail = 1;
                    $parent->save();
                }
            }
            DB::table('coa_scb_cate')->where('AccountID', '=', $CoaScb->AccountID)->delete();
            $CoaScb->delete();

        }
        if($CoaScb){
            if ($auth->UserType === 1) {
                $CoaScb->delete();
                CoaScbCate::where('AccountID', $id)->delete();
                CoaScbPer::where('AccountID', $id)->delete();
                CoaScbLink::where('AccountID',$id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $isPer = CoaScbPer::where([
                    'AccountID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($CoaScb->UserIDCreated === $auth->UserID)){
                    $CoaScb->delete();
                    CoaScbCate::where('AccountID', $id)->delete();
                    CoaScbPer::where('AccountID', $id)->delete();
                    CoaScbLink::where('AccountID',$id)->delete();
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
            $data = CoaScb::find($id);
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        DB::table('coa_scb_cate')->whereIn('AccountID', $IDs)->delete();
        CoaScb::destroy($IDs);
        $sql = "UPDATE coa_scb
                SET Detail = 1
                WHERE AccountID NOT IN (SELECT ParentID FROM (select ParentID from coa_scb WHERE ParentID is not null) t)";
        DB::statement($sql);
        CoaScbCate::whereIn('AccountID',$IDs)->delete();
        CoaScbLink::whereIn('AccountID',$IDs)->delete();
        CoaScbPer::whereIn('AccountID',$IDs)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
        $auth = Auth::user();
        $idArray = $request->post('array_id');
        if ($auth->UserType === 1) {
            CoaScb::destroy($idArray);
            CoaScbCate::whereIn('AccountID', $idArray)->delete();
            CoaScbPer::whereIn('AccountID', $idArray)->delete();
            CoaScbLink::where('AccountID',$idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $coaScbsPer = CoaScbPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('coa_scb_per')
                    ->whereIn('AccountID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('AccountID');
            })->get();

            foreach ($coaScbsPer as $coaScbPer) {
                if ($coaScbPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                CoaScb::destroy($idArray);
                CoaScbCate::whereIn('AccountID', $idArray)->delete();
                CoaScbPer::whereIn('AccountID', $idArray)->delete();
                CoaScbLink::whereIn('AccountID',$idArray)->delete();
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
        $CoaScbPer = $request->post('CoaScbPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CoaScbPer as $value){
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
        CoaScbPer::where(['AccountID' => $AccountID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('coa_scb_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $AccountID;
        return json_encode($json);
    }
    public function getCoaScbCateList(){
        $coa_scbCateList = CoaScbCateList::where('Inactive',0)->get();
        $coa_scbCateValue = CoaScbCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CoaScbCateList'] = $coa_scbCateList;
        $json['data']['CoaScbCateValue'] = $coa_scbCateValue;
        return json_encode($json);
    }
    public function getCoaScbLink($id){
        $data = CoaScbLink::where('AccountID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function updateLink(Request $request){
        $LinkArray = $request->post('CoaScbLink');
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
        CoaScbLink::where(['AccountID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('coa_scb_link')->insert($arrLink);
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
        $data = CoaScb::search($request);
        $json['data'] = $data;
        return json_encode($json);
    }
    //
    public  function  getListChild(Request $request){
        $auth = Auth::User();
        $ParentID = $request->ParentID;
        $query = CoaScb::query();
        $query->select('coa_scb.*');
        $query->leftJoin('coa_scb_per', 'coa_scb.AccountID', '=', 'coa_scb_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_scb_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_scb.AccessType', '=', 2);
                });
        });
        $query->where('coa_scb.ParentID', $ParentID);
        $query->groupBy('coa_scb.AccountID')->orderBy('coa_scb.AccountNo');
        $data = $query->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
?>
