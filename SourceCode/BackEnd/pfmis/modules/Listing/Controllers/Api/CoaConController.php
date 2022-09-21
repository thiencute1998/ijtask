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
use Module\Listing\Models\CoaCon;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Employee;
use Module\Listing\Models\CoaConCate;
use Module\Listing\Models\CoaConCateList;
use Module\Listing\Models\CoaConCateValue;
use Module\Listing\Models\CoaConPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CoaConLink;

use function GuzzleHttp\Psr7\_parse_request_uri;

class CoaConController extends Controller{
    public function list(Request $request){
        $data = CoaCon::search($request);
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('CoaCon',['select'=>['AccountNo','AccountName'],'conditions' => []]),'HTTKQG.xlsx');
    }
    public function create(){
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        $coa_con = CoaCon::all();
        $employee = Employee::all();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
        $json['data']['BalanceTypeOptions'] = $BalanceTypeOptions;
        $json['data']['coa_con'] = $coa_con;
        $json['data']['employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'AccountNo' => 'required|unique:coa_con',
            'AccountName' => 'required',
        ],[
            'AccountNo.required' => 'Mã HTTK hợp nhất không được để trống',
            'AccountNo.unique' => 'Mã HTTK hợp nhất đã tồn tại',
            'AccountName.required' => 'Tên HTTK hợp nhất không được để trống',

        ]);
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Thất bại';
            $json['data'] = $validator->errors();
            return json_encode($json);
        }
        // set Parent and Level
        if($request->ParentID){
            $parent = CoaCon::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
        } else {
            $level = 1;
        }
        $coa_con = new CoaCon([
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
        $coa_con->save();
        $CoaConPer = new CoaConPer([
            'AccountID' => $coa_con->AccountID,
            'AccountNo' => $coa_con->AccountNo,
            'AccountName'=>$coa_con->AccountName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName'=> Auth::user()->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CoaConPer->save();

        // CoaConCate
        $arrInsert= [];
        if($request->post('CoaConCate')){
            foreach ($request->post('CoaConCate') as $key=>$item){
                if($item['CateID']){
                    $arrInsert[] = [
                        'AccountID'=>$coa_con->AccountID,
                        'CateID'=>$item['CateID'],
                        'CateValue'=>$item['CateValue']
                    ];
                }
            }
            DB::table('coa_con_cate')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $coa_con->AccountID;
        return json_encode($json);
    }
    public function view($id){
        $query = CoaCon::query();
        $data = CoaCon::find($id);
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $query->where('coa_con.AccountID', '=', $data->ParentID);
                $parent = $query->first();
                if(isset($parent->AccountNo) && isset($parent->AccountName)){
                    $arrParent = [
                        'AccountNo' => $parent->AccountNo,
                        'AccountName' => $parent->AccountName,
                    ];
                }
            }
            $CoaConPerEmployee = CoaConPer::where([
                'AccountID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID,
            ])->first();
            $EmployeeLogin = Employee::where('EmployeeID','=',Auth::user()->EmployeeID)->first();
            if(!$CoaConPerEmployee){
                if($data->AccessType==2){
                    // Tài liệu công khai
                    $CoaConPerEmployee = new CoaConPer([
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
                        $CoaConPerEmployee = new CoaConPer([
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
                    $CoaConPerEmployee = new CoaConPer([
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

            if ($CoaConPerEmployee) {
                if (strpos(',' . $CoaConPerEmployee->AccessField . ',', ',all,')) {
                    $CoaConPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $CoaConPerEmployee->EditField . ',', ',all,')){
                    $CoaConPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $CoaConPer = CoaConPer::where('AccountID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $CoaCon = CoaCon::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $CoaConCate = CoaConCate::where('AccountID',$id)->get();
            $arrCate = [];
            foreach($CoaConCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $CoaConCateList = CoaConCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $CoaConCateValue = CoaConCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['CoaCon'] = $CoaCon;
            $json['Employee'] = $Employee;
            $json['CoaConPer'] = $CoaConPer;
            $json['CoaConPerEmployee'] = $CoaConPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
            $json['data']['BalanceTypeOptions']= $BalanceTypeOptions;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CoaConCate'] = $CoaConCate;
            $json['data']['CoaConCateList'] = $CoaConCateList;
            $json['data']['CoaConCateValue'] = $CoaConCateValue;
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
        $data = CoaCon::find($id);
        $coa_con = CoaCon::all();
        if(old('AccountNo')){
            $data->AccountNo = old('AccountNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['coa_con'] = $coa_con;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function update(Request $request, $id){

        $coa_con = CoaCon::find($id);
        if(!$coa_con){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_con->Detail == 0){
            if($coa_con->AccountNo != $request->AccountNo || $coa_con->ParentID != $request->ParentID ||  !$request->post('CoaConCate') ){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if($coa_con->ParentID !== $request->ParentID){
            $validator = Validator::make($request->all(), [
                'AccountNo' => 'required:coa_con',
                'AccountName'=> 'required:coa_con',
            ],
                [
                    'AccountNo.required' => 'Mã hệ thống tài khoản hợp nhất không được để trống',
                    'AccountName.required' => 'Tên hệ thống tài khoản hợp nhất không được để trống',
                ]
            );

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $coa_con->AccountNo = $request->AccountNo;
            // set for new Parent
            if($request->ParentID){
                $parent = CoaCon::find($request->ParentID);
                $parent->Detail = 0;
                $parent->save();
                $level = $parent->Level + 1;
            }else{
                $level = 1;
            }
            $coa_con->Level = $level;
            $coa_con->Detail = 1;
            // set for old Parents
            if($coa_con->ParentID){
                $parentOld = CoaCon::find($coa_con->ParentID);
                $count = DB::table('coa_con')->where('ParentID','=', $parentOld->AccountID)->count();
                if($count < 2){
                    $parentOld->Detail = 1;
                    $parentOld->save();
                }
            }
        }
        $coa_con->AccountName = $request->AccountName;
        $coa_con->ParentID = $request->ParentID;
        $coa_con->ParentNo = $request->ParentNo;
        $coa_con->BalanceType = $request->BalanceType;
        $coa_con->Note = $request->Note;
        $coa_con->save();

        // coa_con cate
        if($request->post('CoaConCate')){
            $CoaConCate = $request->post('CoaConCate');
            CoaConCate::where('AccountID',$id)->delete();
            $arrInsert = [];
            foreach($CoaConCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "AccountID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('coa_con_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $coa_con->AccountID;
        return $json;
    }
    public function delete($id){
        $CoaCon = coaCon::find($id);
        $auth = Auth::user();
        if ($CoaCon) {
            if($CoaCon->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
            // set for old Parent
            if($CoaCon->ParentID){
                $parent = coaCon::find($CoaCon->ParentID);
                $count = DB::table('coa_con')->where('ParentID', '=', $parent->AccountID)->count();
                if($count < 2){
                    $parent->Detail = 1;
                    $parent->save();
                }
            }
            DB::table('coa_con_cate')->where('AccountID', '=', $CoaCon->AccountID)->delete();
            $CoaCon->delete();

        }
        if($CoaCon){
            if ($auth->UserType === 1) {
                $CoaCon->delete();
                CoaConCate::where('AccountID', $id)->delete();
                CoaConPer::where('AccountID', $id)->delete();
                CoaConLink::where('AccountID',$id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $isPer = CoaConPer::where([
                    'AccountID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($CoaCon->UserIDCreated === $auth->UserID)){
                    $CoaCon->delete();
                    CoaConCate::where('AccountID', $id)->delete();
                    CoaConPer::where('AccountID', $id)->delete();
                    CoaConLink::where('AccountID',$id)->delete();
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
        $count = DB::table('coa_con')->whereNotIn('AccountID', $IDs)
                                            ->whereIn('ParentID', $IDs)
                                            ->count();
        if($count > 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại!';
            $json['data'] = [];
            return json_encode($json);
        }
        DB::table('coa_con_cate')->whereIn('AccountID', $IDs)->delete();
        CoaCon::destroy($IDs);
        $sql = "UPDATE coa_con
                SET Detail = 1
                WHERE AccountID NOT IN (SELECT ParentID FROM (select ParentID from coa_con WHERE ParentID is not null) t)";
        DB::statement($sql);
        CoaConCate::whereIn('AccountID',$IDs)->delete();
        CoaConLink::whereIn('AccountID',$IDs)->delete();
        CoaConPer::whereIn('AccountID',$IDs)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
        $auth = Auth::user();
        $idArray = $request->post('array_id');
        if ($auth->UserType === 1) {
            CoaCon::destroy($idArray);
            CoaConCate::whereIn('AccountID', $idArray)->delete();
            CoaConPer::whereIn('AccountID', $idArray)->delete();
            CoaConLink::where('AccountID',$idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $coaConsPer = CoaConPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('coa_con_per')
                    ->whereIn('AccountID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('AccountID');
            })->get();

            foreach ($coaConsPer as $coaConPer) {
                if ($coaConPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                CoaCon::destroy($idArray);
                CoaConCate::whereIn('AccountID', $idArray)->delete();
                CoaConPer::whereIn('AccountID', $idArray)->delete();
                CoaConLink::whereIn('AccountID',$idArray)->delete();
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
        $CoaConPer = $request->post('CoaConPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CoaConPer as $value){
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
        CoaConPer::where(['AccountID' => $AccountID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('coa_con_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $AccountID;
        return json_encode($json);
    }
    public function getCoaConCateList(){
        $coa_conCateList = CoaConCateList::where('Inactive',0)->get();
        $coa_conCateValue = CoaConCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CoaConCateList'] = $coa_conCateList;
        $json['data']['CoaConCateValue'] = $coa_conCateValue;
        return json_encode($json);
    }
    public function getCoaConLink($id){
        $data = CoaConLink::where('AccountID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function updateLink(Request $request){
        $LinkArray = $request->post('CoaConLink');
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
        CoaConLink::where(['AccountID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('coa_con_link')->insert($arrLink);
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
        $data = CoaCon::search($request);
        $json['data'] = $data;
        return json_encode($json);
    }
    //
    public  function  getListChild(Request $request){
        $auth = Auth::User();
        $ParentID = $request->ParentID;
        $query = CoaCon::query();
        $query->select('coa_con.*');
        $query->leftJoin('coa_con_per', 'coa_con.AccountID', '=', 'coa_con_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_con_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_con.AccessType', '=', 2);
                });
        });
        $query->where('coa_con.ParentID', $ParentID);
        $query->groupBy('coa_con.AccountID')->orderBy('coa_con.AccountNo');
        $data = $query->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
?>
