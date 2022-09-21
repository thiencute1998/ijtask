<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use IjCore\IjFile;
use IjCore\IjString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Fund;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Employee;
use Module\Listing\Models\FundCate;
use Module\Listing\Models\FundCateList;
use Module\Listing\Models\FundCateValue;
use Module\Listing\Models\FundPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\FundLink;

use function GuzzleHttp\Psr7\_parse_request_uri;

class FundController extends Controller{
    public function list(Request $request){

        $data = Fund::search($request);
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function export(Request $request){
        return Excel::download(new ModelExport('Fund',['select'=>['FundNo','FundName'],'conditions' => []]),'HTTKQG.xlsx');
    }
    public function create(){
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        $fund = Fund::all();
        $employee = Employee::all();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
        $json['data']['BalanceTypeOptions'] = $BalanceTypeOptions;
        $json['data']['fund'] = $fund;
        $json['data']['employee'] = $employee;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'FundNo' => 'required|unique:fund',
            'FundName' => 'required',
        ],[
            'FundNo.required' => 'Mã danh mục quỹ không được để trống',
            'FundNo.unique' => 'Mã danh mục quỹ đã tồn tại',
            'FundName.required' => 'Tên danh mục quỹ không được để trống',

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
            $fund_parent = Fund::find($request->ParentID);
            $level = $fund_parent->Level + 1;
        }
        $fund = new Fund([
            'FundNo' => $request->FundNo,
            'FundName' => $request->FundName,
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
        $fund->save();
        $FundPer = new FundPer([
            'FundID' => $fund->FundID,
            'FundName'=>$fund->FundName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName'=> Auth::user()->FullName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $FundPer->save();

        // FundCate
        $arrInsert= [];
        if($request->post('FundCate')){
            foreach ($request->post('FundCate') as $key=>$item){
                if($item['CateID']){
                    $arrInsert[] = [
                        'FundID'=>$fund->FundID,
                        'CateID'=>$item['CateID'],
                        'CateValue'=>$item['CateValue']
                    ];
                }
            }
            DB::table('fund_cate')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $fund->FundID;
        return json_encode($json);
    }
    public function view($id){
        $data = Fund::find($id);
        $AccessTypeOptions = config('dataoptions.Common.AccessTypeOptions');
        $BalanceTypeOptions = config('dataoptions.Coa.BalanceTypeOptions');
        if($data){
            $arrParent = [];
            if($data->ParentID){
                $parent = Fund::find($data->ParentID);
                if(isset($parent->FundNo) && isset($parent->FundName)){
                    $arrParent = [
                        'FundNo' => $parent->FundNo,
                        'FundName' => $parent->FundName,
                    ];
                }
            }
            $FundPerEmployee = FundPer::where([
                'FundID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID,
            ])->first();
            $EmployeeLogin = Employee::where('UserID','=',Auth::user()->UserID)->first();
            if(!$FundPerEmployee){
                if($data->AccessType==2){
                    // Tài liệu công khai
                    $FundPerEmployee = new FundPer([
                        'FundID' => $id,
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
                        $FundPerEmployee = new FundPer([
                            'FundID' => $id,
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
                    $FundPerEmployee = new FundPer([
                        'FundID' => $id,
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

            if ($FundPerEmployee) {
                if (strpos(',' . $FundPerEmployee->AccessField . ',', ',all,')) {
                    $FundPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $FundPerEmployee->EditField . ',', ',all,')){
                    $FundPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $FundPer = FundPer::where('FundID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $Fund = Fund::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $FundCate = FundCate::where('FundID',$id)->get();
            $arrCate = [];
            foreach($FundCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $FundCateList = FundCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $FundCateValue = FundCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['Fund'] = $Fund;
            $json['Employee'] = $Employee;
            $json['FundPer'] = $FundPer;
            $json['FundPerEmployee'] = $FundPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['AccessTypeOptions'] = $AccessTypeOptions;
            $json['data']['BalanceTypeOptions']= $BalanceTypeOptions;
            $json['data']['Parent'] = $arrParent;
            $json['data']['FundCate'] = $FundCate;
            $json['data']['FundCateList'] = $FundCateList;
            $json['data']['FundCateValue'] = $FundCateValue;
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
        $data = Fund::find($id);
        $fund = Fund::all();
        if(old('FundNo')){
            $data->FundNo = old('FundNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['fund'] = $fund;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }
    public function update(Request $request, $id){

        $fund = Fund::find($id);
        if(!$fund){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($fund->FundNo !== $request->FundNo){
            $validator = Validator::make($request->all(), [
                'FundNo' => 'required',
                'FundName'=> 'required',
            ],
                [
                    'FundNo.required' => 'Mã danh mục quỹ không được để trống',
                    'FundName.required' => 'Tên danh mục quỹ không được để trống',
                ]);




            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $fund->FundNo = $request->FundNo;
            if(empty($request->ParentID)){
                $level = 1;
            } else{
                $parent = Fund::find($request->ParentID);
                $level = $parent->Level + 1;
            }
            $fund->Level = $level;
        }
        $fund->FundName = $request->FundName;
        $fund->ParentID = $request->ParentID;
        $fund->BalanceType = $request->BalanceType;
        $fund->Note = $request->Note;
        $fund->Detail = $request->Detail;
        $fund->AccessType = $request->AccessType;
        $fund->save();

        // fund cate
        if($request->post('FundCate')){
            $FundCate = $request->post('FundCate');
            FundCate::where('FundID',$id)->delete();
            $arrInsert = [];
            foreach($FundCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "FundID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('fund_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }
    public function delete($id){
        $Fund = Fund::find($id);
        $auth = Auth::user();
        if($Fund){
            if ($auth->UserType === 1) {
                $Fund->delete();
                FundCate::where('FundID', $id)->delete();
                FundPer::where('FundID', $id)->delete();
                FundLink::where('FundID',$id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            } else {
                $isPer = FundPer::where([
                    'FundID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($Fund->UserIDCreated === $auth->UserID)){
                    $Fund->delete();
                    FundCate::where('FundID', $id)->delete();
                    FundPer::where('FundID', $id)->delete();
                    FundLink::where('FundID',$id)->delete();
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
            Fund::destroy($idArray);
            FundCate::whereIn('FundID', $idArray)->delete();
            FundPer::whereIn('FundID', $idArray)->delete();
            FundLink::where('FundID',$idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $coaConsPer = FundPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('fund_per')
                    ->whereIn('FundID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('FundID');
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
                Fund::destroy($idArray);
                FundCate::whereIn('FundID', $idArray)->delete();
                FundPer::whereIn('FundID', $idArray)->delete();
                FundLink::whereIn('FundID',$idArray)->delete();
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
        $FundID = $request->post('FundID');
        $FundPer = $request->post('FundPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($FundPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'FundID' => $FundID,
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
        FundPer::where(['FundID' => $FundID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('fund_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $FundID;
        return json_encode($json);
    }
    public function getFundCateList(){
        $fundCateList = FundCateList::where('Inactive',0)->get();
        $fundCateValue = FundCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['FundCateList'] = $fundCateList;
        $json['data']['FundCateValue'] = $fundCateValue;
        return json_encode($json);
    }
    public function getFundLink($id){
        $data = FundLink::where('FundID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function updateLink(Request $request){
        $LinkArray = $request->post('FundLink');
        $id = $request->post('FundID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'FundID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        FundLink::where(['FundID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('fund_link')->insert($arrLink);
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
        $data = Fund::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
?>
