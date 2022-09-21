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
use Module\Listing\Models\CoaAnu;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Employee;
use Module\Listing\Models\CoaAnuCate;
use Module\Listing\Models\CoaAnuCateList;
use Module\Listing\Models\CoaAnuCateValue;
use Module\Listing\Models\CoaAnuPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CoaAnuLink;

use function GuzzleHttp\Psr7\_parse_request_uri;

class CoaAnuController extends Controller{
    public function list(Request $request){
        $data = CoaAnu::search($request);
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('CoaAnu',['select'=>['AccountNo','AccountName'],'conditions' => []]),'HTTKQG.xlsx');
    }
    public function create(){
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        $coa_anu = CoaAnu::all();
        $employee = Employee::all();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
        $json['data']['BalanceTypeOptions'] = $BalanceTypeOptions;
        $json['data']['coa_anu'] = $coa_anu;
        $json['data']['employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'AccountNo' => 'required|unique:coa_anu',
            'AccountName' => 'required',
        ],[
            'AccountNo.required' => 'Mã HTTK hành chính sự nghiệp không được để trống',
            'AccountNo.unique' => 'Mã HTTK hành chính sự nghiệp đã tồn tại',
            'AccountName.required' => 'Tên HTTK hành chính sự nghiệp không được để trống',

        ]);
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Thất bại';
            $json['data'] = $validator->errors();
            return json_encode($json);
        }
        // set Parent and Level
        if($request->ParentID){
            $parent = CoaAnu::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
        } else {
            $level = 1;
        }
        $coa_anu = new CoaAnu([
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
        $coa_anu->save();
        $CoaAnuPer = new CoaAnuPer([
            'AccountID' => $coa_anu->AccountID,
            'AccountNo' => $coa_anu->AccountNo,
            'AccountName'=>$coa_anu->AccountName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName'=> Auth::user()->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CoaAnuPer->save();

        // CoaAnuCate
        $arrInsert= [];
        if($request->post('CoaAnuCate')){
            foreach ($request->post('CoaAnuCate') as $key=>$item){
                if($item['CateID']){
                    $arrInsert[] = [
                        'AccountID'=>$coa_anu->AccountID,
                        'CateID'=>$item['CateID'],
                        'CateValue'=>$item['CateValue']
                    ];
                }
            }
            DB::table('coa_anu_cate')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $coa_anu->AccountID;
        return json_encode($json);
    }
    public function view($id){
        $query = CoaAnu::query();
        $data = CoaAnu::find($id);
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $query->where('coa_anu.AccountID', '=', $data->ParentID);
                $parent = $query->first();
                if(isset($parent->AccountNo) && isset($parent->AccountName)){
                    $arrParent = [
                        'AccountNo' => $parent->AccountNo,
                        'AccountName' => $parent->AccountName,
                    ];
                }
            }
            $CoaAnuPerEmployee = CoaAnuPer::where([
                'AccountID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID,
            ])->first();
            $EmployeeLogin = Employee::where('EmployeeID','=',Auth::user()->EmployeeID)->first();
            if(!$CoaAnuPerEmployee){
                if($data->AccessType==2){
                    // Tài liệu công khai
                    $CoaAnuPerEmployee = new CoaAnuPer([
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
                        $CoaAnuPerEmployee = new CoaAnuPer([
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
                    $CoaAnuPerEmployee = new CoaAnuPer([
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

            if ($CoaAnuPerEmployee) {
                if (strpos(',' . $CoaAnuPerEmployee->AccessField . ',', ',all,')) {
                    $CoaAnuPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $CoaAnuPerEmployee->EditField . ',', ',all,')){
                    $CoaAnuPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $CoaAnuPer = CoaAnuPer::where('AccountID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $CoaAnu = CoaAnu::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $CoaAnuCate = CoaAnuCate::where('AccountID',$id)->get();
            $arrCate = [];
            foreach($CoaAnuCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $CoaAnuCateList = CoaAnuCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $CoaAnuCateValue = CoaAnuCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['CoaAnu'] = $CoaAnu;
            $json['Employee'] = $Employee;
            $json['CoaAnuPer'] = $CoaAnuPer;
            $json['CoaAnuPerEmployee'] = $CoaAnuPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
            $json['data']['BalanceTypeOptions']= $BalanceTypeOptions;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CoaAnuCate'] = $CoaAnuCate;
            $json['data']['CoaAnuCateList'] = $CoaAnuCateList;
            $json['data']['CoaAnuCateValue'] = $CoaAnuCateValue;
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
        $data = CoaAnu::find($id);
        $coa_anu = CoaAnu::all();
        if(old('AccountNo')){
            $data->AccountNo = old('AccountNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['coa_anu'] = $coa_anu;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function update(Request $request, $id){

        $coa_anu = CoaAnu::find($id);
        if(!$coa_anu){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($coa_anu->Detail == 0){
            if($coa_anu->AccountNo != $request->AccountNo || $coa_anu->ParentID != $request->ParentID ||  !$request->post('CoaAnuCate') ){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if($coa_anu->ParentID !== $request->ParentID){
            $validator = Validator::make($request->all(), [
                'AccountNo' => 'required:coa_anu',
                'AccountName'=> 'required:coa_anu',
            ],
                [
                    'AccountNo.required' => 'Mã hệ thống tài khoản hành chính sự nghiệp không được để trống',
                    'AccountName.required' => 'Tên hệ thống tài khoản hành chính sự nghiệp không được để trống',
                ]
            );

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $coa_anu->AccountNo = $request->AccountNo;
            // set for new Parent
            if($request->ParentID){
                $parent = CoaAnu::find($request->ParentID);
                $parent->Detail = 0;
                $parent->save();
                $level = $parent->Level + 1;
            }else{
                $level = 1;
            }
            $coa_anu->Level = $level;
            $coa_anu->Detail = 1;
            // set for old Parents
            if($coa_anu->ParentID){
                $parentOld = CoaAnu::find($coa_anu->ParentID);
                $count = DB::table('coa_anu')->where('ParentID','=', $parentOld->AccountID)->count();
                if($count < 2){
                    $parentOld->Detail = 1;
                    $parentOld->save();
                }
            }
        }
        $coa_anu->AccountName = $request->AccountName;
        $coa_anu->ParentID = $request->ParentID;
        $coa_anu->ParentNo = $request->ParentNo;
        $coa_anu->BalanceType = $request->BalanceType;
        $coa_anu->Note = $request->Note;
        $coa_anu->save();

        // coa_anu cate
        if($request->post('CoaAnuCate')){
            $CoaAnuCate = $request->post('CoaAnuCate');
            CoaAnuCate::where('AccountID',$id)->delete();
            $arrInsert = [];
            foreach($CoaAnuCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "AccountID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('coa_anu_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $coa_anu->AccountID;
        return $json;
    }
    public function delete($id){
        $CoaAnu = CoaAnu::find($id);
        $auth = Auth::user();
        if ($CoaAnu) {
            if($CoaAnu->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
            // set for old Parent
            if($CoaAnu->ParentID){
                $parent = CoaAnu::find($CoaAnu->ParentID);
                $count = DB::table('coa_anu')->where('ParentID', '=', $parent->AccountID)->count();
                if($count < 2){
                    $parent->Detail = 1;
                    $parent->save();
                }
            }
            DB::table('coa_anu_cate')->where('AccountID', '=', $CoaAnu->AccountID)->delete();
            $CoaAnu->delete();

        }
        if($CoaAnu){
            if ($auth->UserType === 1) {
                $CoaAnu->delete();
                CoaAnuCate::where('AccountID', $id)->delete();
                CoaAnuPer::where('AccountID', $id)->delete();
                CoaAnuLink::where('AccountID',$id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $isPer = CoaAnuPer::where([
                    'AccountID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($CoaAnu->UserIDCreated === $auth->UserID)){
                    $CoaAnu->delete();
                    CoaAnuCate::where('AccountID', $id)->delete();
                    CoaAnuPer::where('AccountID', $id)->delete();
                    CoaAnuLink::where('AccountID',$id)->delete();
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
        $count = DB::table('coa_anu')->whereNotIn('AccountID', $IDs)
            ->whereIn('ParentID', $IDs)
            ->count();
        if($count > 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại!';
            $json['data'] = [];
            return json_encode($json);
        }
        DB::table('coa_anu_cate')->whereIn('AccountID', $IDs)->delete();
        CoaAnu::destroy($IDs);
        $sql = "UPDATE coa_anu
                SET Detail = 1
                WHERE AccountID NOT IN (SELECT ParentID FROM (select ParentID from coa_anu WHERE ParentID is not null) t)";
        DB::statement($sql);
        CoaAnuCate::whereIn('AccountID',$IDs)->delete();
        CoaAnuLink::whereIn('AccountID',$IDs)->delete();
        CoaAnuPer::whereIn('AccountID',$IDs)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
        $auth = Auth::user();
        $idArray = $request->post('array_id');
        if ($auth->UserType === 1) {
            CoaAnu::destroy($idArray);
            CoaAnuCate::whereIn('AccountID', $idArray)->delete();
            CoaAnuPer::whereIn('AccountID', $idArray)->delete();
            CoaAnuLink::where('AccountID',$idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $coaAnusPer = CoaAnuPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('coa_anu_per')
                    ->whereIn('AccountID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('AccountID');
            })->get();

            foreach ($coaAnusPer as $coaAnuPer) {
                if ($coaAnuPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                CoaAnu::destroy($idArray);
                CoaAnuCate::whereIn('AccountID', $idArray)->delete();
                CoaAnuPer::whereIn('AccountID', $idArray)->delete();
                CoaAnuLink::whereIn('AccountID',$idArray)->delete();
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
        $CoaAnuPer = $request->post('CoaAnuPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CoaAnuPer as $value){
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
        CoaAnuPer::where(['AccountID' => $AccountID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('coa_anu_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $AccountID;
        return json_encode($json);
    }
    public function getCoaAnuCateList(){
        $coa_anuCateList = CoaAnuCateList::where('Inactive',0)->get();
        $coa_anuCateValue = CoaAnuCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CoaAnuCateList'] = $coa_anuCateList;
        $json['data']['CoaAnuCateValue'] = $coa_anuCateValue;
        return json_encode($json);
    }
    public function getCoaAnuLink($id){
        $data = CoaAnuLink::where('AccountID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function updateLink(Request $request){
        $LinkArray = $request->post('CoaAnuLink');
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
        CoaAnuLink::where(['AccountID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('coa_anu_link')->insert($arrLink);
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
        $data = CoaAnu::search($request);
        $json['data'] = $data;
        return json_encode($json);
    }
    //
    public  function  getListChild(Request $request){
        $auth = Auth::User();
        $ParentID = $request->ParentID;
        $query = CoaAnu::query();
        $query->select('coa_anu.*');
        $query->leftJoin('coa_anu_per', 'coa_anu.AccountID', '=', 'coa_anu_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_anu_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_anu.AccessType', '=', 2);
                });
        });
        $query->where('coa_anu.ParentID', $ParentID);
        $query->groupBy('coa_anu.AccountID')->orderBy('coa_anu.AccountNo');
        $data = $query->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
?>
