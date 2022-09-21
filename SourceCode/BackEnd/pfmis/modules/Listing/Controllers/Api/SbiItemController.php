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
use Module\Listing\Models\Company;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\SbiItem;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SbiItemPer;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\SbiItemLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class SbiItemController extends Controller{

    public function list(Request $request){
        $data = SbiItem::search($request);
        $ItemTypeOption = config('dataoptions.SbiItem.ItemType');
        $ItemGroupOption = config('dataoptions.SbiItem.ItemGroup');
        $json['ItemTypeOption'] = $ItemTypeOption;
        $json['ItemGroupOption'] = $ItemGroupOption;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('sbi_item', ['select' => ['SbiItemNo', 'SbiItemName'], 'conditions' => []]), 'muctieumuc.xlsx');
    }

    public function create(){
        $ItemTypeOption = config('dataoptions.SbiItem.ItemType');
        $ItemGroupOption = config('dataoptions.SbiItem.ItemGroup');
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['ItemTypeOption'] = $ItemTypeOption;
        $json['ItemGroupOption'] = $ItemGroupOption;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'SbiItemNo' => 'required|unique:sbi_item'
        ],
            [
                'SbiItemNo.required' => 'Mã mục - tiểu mục không được để trống',
                'SbiItemNo.unique' => 'Mã mục - tiểu mục đã tồn tại',
            ]);

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        if(!empty($request->post('ParentID'))){
            $sbiItemParent = SbiItem::find($request->post('ParentID'));
            $level = $sbiItemParent->Level + 1;
        }
        else{
            $level = 1;
        }

        $array_custom = [
            'Level'=> $level,
            'AccessType'=> ($request->AccessType) ? $request->AccessType : 1,
            'NumberValue'=> 1,
            'UserIDCreated'=> Auth::user()->UserID,
            'AuthorizedPerson'=> Auth::user()->UserID
        ];

        $request->merge($array_custom);

        $data = SbiItem::create($request->all());

        $SbiItemPer = new SbiItemPer([
            'SbiItemID' => $data->SbiItemID,
            'SbiItemName'=> $data->SbiItemName,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $SbiItemPer->save();

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');

            // sbi_item per
            $SbiItemPerCopy = SbiItemPer::where('SbiItemID', $CopyID)->where('EmployeeID', '!=', Auth::user()->EmployeeID)->get();
            $sbi_itemPerArrInsert = [];
            foreach ($SbiItemPerCopy as $sbi_itemPer) {
                $sbi_itemPerArrInsert[] = [
                    'SbiItemID' => $data->SbiItemID,
                    'EmployeeID' => $sbi_itemPer->EmployeeID,
                    'EmployeeName' => $sbi_itemPer->EmployeeName,
                    'Access' => $sbi_itemPer->Access,
                    'AccessField' => $sbi_itemPer->AccessField,
                    'Edit' => $sbi_itemPer->Edit,
                    'EditField' => $sbi_itemPer->EditField,
                    'Delete' => $sbi_itemPer->Delete,
                    'Create' => $sbi_itemPer->Create
                ];
            }
            if (count($sbi_itemPerArrInsert)) {
                DB::table('sbi_item_per')->insert($sbi_itemPerArrInsert);
            }

            // sbi_item link
            $SbiItemLinkCopy = SbiItemLink::where('SbiItemID', $CopyID)->get();
            $sbi_itemLinkArrInsert = [];
            foreach ($SbiItemLinkCopy as $sbi_itemLink) {
                $sbi_itemLinkArrInsert[] = [
                    'SbiItemID' => $data->SbiItemID,
                    'LinkID' => $sbi_itemLink->LinkID,
                    'LinkNo' => $sbi_itemLink->LinkNo,
                    'LinkName' => $sbi_itemLink->LinkName,
                    'LinkTable' => $sbi_itemLink->LinkTable,
                    'LinkTableName' => $sbi_itemLink->LinkTableName
                ];
            }
            if (count($sbi_itemLinkArrInsert)) {
                DB::table('sbi_item_link')->insert($sbi_itemLinkArrInsert);
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->SbiItemID;

        return json_encode($json);
    }

    public function view($id){
        $data = SbiItem::find($id);
        if($data){
            $data->SbiItemNo = $data->Prefix.$data->SbiItemNo.$data->Suffix;
            $arrParent = [];
            if($data->ParentID){
                $parent = SbiItem::find($data->ParentID);
                if(isset($parent->SbiItemNo) && isset($parent->SbiItemName)){
                    $arrParent = [
                        "ParentNo" => $parent->SbiItemNo,
                        "ParentName" => $parent->SbiItemName
                    ];
                }
            }
            $SbiItemPerEmployee = SbiItemPer::where([
                'SbiItemID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID
            ])->first();
            $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

            if(!$SbiItemPerEmployee){
                if($data->AccessType == 2){
                    //Nếu tài liệu là công khai
                    $SbiItemPerEmployee = new SbiItemPer([
                        'SbiItemID' => $id,
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
                else{
                    //Nếu tài liệu không phải công khai
                    if($EmployeeLogin->UserID != $data->UserIDCreated && $EmployeeLogin->UserID != $data->AuthorizedPerson){
                        $json['status'] = 3;
                        $json['msg'] = 'Bạn không có quyền truy cập!';
                        return json_encode($json);
                    }else{
                        $SbiItemPerEmployee = new SbiItemPer([
                            'SbiItemID' => $id,
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
                    $SbiItemPerEmployee = new SbiItemPer([
                        'SbiItemID' => $id,
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

            if ($SbiItemPerEmployee) {
                if (strpos(',' . $SbiItemPerEmployee->AccessField . ',', ',all,')) {
                    $SbiItemPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $SbiItemPerEmployee->EditField . ',', ',all,')){
                    $SbiItemPerEmployee->EditField = 'all';
                }
            }

            $SbiItemPer = SbiItemPer::where('SbiItemID', '=', $id)->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $Company = Company::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();
            $ItemTypeOption = config('dataoptions.SbiItem.ItemType');
            $ItemGroupOption = config('dataoptions.SbiItem.ItemGroup');

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['Company'] = $Company;
            $json['Employee'] = $Employee;
            $json['SbiItemPer'] = $SbiItemPer;
            $json['SbiItemPerEmployee'] = $SbiItemPerEmployee;
            $json['ItemTypeOption'] = $ItemTypeOption;
            $json['ItemGroupOption'] = $ItemGroupOption;
            $json['data']['data'] = $data;
            $json['data']['Parent'] = $arrParent;
            return json_encode($json);
        }else{

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id){
        $data = SbiItem::find($id);
        if(old('SbiItemNo')){
            $data->SbiItemNo = old('SbiItemNo');
        }else{
            $data->SbiItemNo = $data->Prefix.$data->SbiItemNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = SbiItem::find($id);
        if($data->SbiItemNo !== $request->SbiItemNo){
            $validator = Validator::make($request->all(), [
                'SbiItemNo' => 'required|unique:sbi_item'
            ],
                [
                    'SbiItemNo.required' => 'Mã mục - tiểu mục không được để trống',
                    'SbiItemNo.unique' => 'Mã mục - tiểu mục đã tồn tại',
                ]);

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
        }

        if(!empty($request->post('ParentID'))){
            $sbiItemParent = SbiItem::find($request->post('ParentID'));
            $level = $sbiItemParent->Level + 1;
        }
        else{
            $level = 1;
        }
        $request->merge(['Level'=> $level]);

        $array_custom = [
            'AccessType'=> ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated'=> Auth::user()->UserID,
            'AuthorizedPerson'=> Auth::user()->UserID
        ];

        $request->merge($array_custom);
        $data->fill($request->all())->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = null;

        return json_encode($json);
    }

    public function getLink(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $SbiItemLink = SbiItemLink::where('SbiItemID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $SbiItemLink;

        return json_encode($json);
    }

    //Link
    public function updateLink(Request $request){
        $LinkArray = $request->post('SbiItemLink');
        $id = $request->post('SbiItemID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'SbiItemID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        SbiItemLink::where(['SbiItemID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('sbi_item_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $SbiItemID = $request->post('SbiItemID');
        $SbiItemName = $request->post('SbiItemName');
        $SbiItemPer = $request->post('SbiItemPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($SbiItemPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'SbiItemID' => $SbiItemID,
                        'SbiItemName' => $SbiItemName,
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
        SbiItemPer::where(['SbiItemID' => $SbiItemID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('sbi_item_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $SbiItemID;
        return json_encode($json);
    }


    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = SbiItem::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }

    public function delete($id){
        $SbiItem = SbiItem::find($id);
        $auth = Auth::user();
        if($SbiItem){
            if ($auth->UserType === 1) {
                $SbiItem->delete();
                SbiItemPer::where('SbiItemID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $isPer = SbiItemPer::where([
                    'SbiItemID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($SbiItem->UserIDCreated === $auth->UserID)){
                    $SbiItem->delete();
                    SbiItemPer::where('SbiItemID', $id)->delete();

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
            SbiItem::destroy($idArray);
            SbiItemPer::whereIn('SbiItemID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $sbi_itemsPer = SbiItemPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('sbi_item_per')
                    ->whereIn('SbiItemID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('SbiItemID');
            })->get();

            foreach ($sbi_itemsPer as $sbi_itemPer) {
                if ($sbi_itemPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                SbiItem::destroy($idArray);
                SbiItemPer::whereIn('SbiItemID', $idArray)->delete();

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
}
