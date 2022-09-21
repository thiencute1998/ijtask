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
use Module\Listing\Models\Sector;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SectorCate;
use Module\Listing\Models\SectorCateList;
use Module\Listing\Models\SectorCateValue;
use Module\Listing\Models\SectorFile;
use Module\Listing\Models\SectorPer;
use Module\Listing\Models\SectorVideo;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\SectorLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class SectorController extends Controller{

    public function list(Request $request){
        $data = Sector::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('sector', ['select' => ['SectorNo', 'SectorName'], 'conditions' => []]), 'nganh.xlsx');
    }

    public function create(){


        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = [];
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'SectorNo' => 'required|unique:sector',
            'SectorName' => 'required:sector',
        ],
            [
                'SectorNo.required' => 'Mã lĩnh vực thu không được để trống',
                'SectorNo.unique' => 'Mã lĩnh vực thu đã tồn tại',
                'SectorName.required' => 'Tên lĩnh vực thu không được để trống',
            ]);
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        if($request->ParentID){
            $parent = Sector::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
            $path = $parent->Path. '#' . $request->SectorNo;
        } else {
            $level = 1;
            $path = $request->SectorNo;
        }
        $data = new Sector([
            'SectorNo' => $request->SectorNo,
            'SectorName' => $request->SectorName,
            'ParentID' => $request->ParentID,
            'ParentNo' =>$request->ParentNo,
            'ParentName' =>$request->ParentName,
            'Level' => $level,
            'Path' => $path,
            'Detail' => 1,
            'SbiCategoryID' => $request->SbiCategoryID,
            'SbiCategoryNo' => $request->SbiCategoryNo,
            'SbiCategoryName' => $request->SbiCategoryName,
            'Note' => $request->Note,
            'AccessType' => ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated' => Auth::user()->UserID,
            'AuthorizedPerson' => Auth::user()->UserID,
            'Inactive' => $request->Inactive,
        ]);
        $data->save();

        $SectorPer = new SectorPer([
            'SectorID' => $data->SectorID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $SectorPer->save();

        // cate list
        if ($request->has('SectorCate')) {
            $SectorCate = $request->post('SectorCate');
            $arrInsertCate = [];
            foreach ($SectorCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'SectorID' => $data->SectorID,
                        'CateID' => $cate['CateID'],
                        'CateNo' => $cate['CateNo'],
                        'CateName' => $cate['CateName'],
                        'CateValue' => $cate['CateValue'],
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('sector_cate')->insert($arrInsertCate);
            }
        }

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');
            // sector_per
            $SectorPerCopy = SectorPer::where('SectorID', $CopyID)->where('EmployeeID', '!=', Auth::user()->EmployeeID)->get();
            $sectorPerArrInsert = [];
            foreach ($SectorPerCopy as $sectorPer) {
                $sectorPerArrInsert[] = [
                    'SectorID' => $data->SectorID,
                    'EmployeeID' => $sectorPer->EmployeeID,
                    'EmployeeName' => $sectorPer->EmployeeName,
                    'Access' => $sectorPer->Access,
                    'AccessField' => $sectorPer->AccessField,
                    'Edit' => $sectorPer->Edit,
                    'EditField' => $sectorPer->EditField,
                    'Delete' => $sectorPer->Delete,
                    'Create' => $sectorPer->Create
                ];
            }
            if (count($sectorPerArrInsert)) {
                DB::table('sector_per')->insert($sectorPerArrInsert);
            }

            // sector_link
            $SectorLinkCopy = SectorLink::where('SectorID', $CopyID)->get();
            $sectorLinkArrInsert = [];
            foreach ($SectorLinkCopy as $sectorLink) {
                $sectorLinkArrInsert[] = [
                    'SectorID' => $data->SectorID,
                    'LinkID' => $sectorLink->LinkID,
                    'LinkNo' => $sectorLink->LinkNo,
                    'LinkName' => $sectorLink->LinkName,
                    'LinkTable' => $sectorLink->LinkTable,
                    'LinkTableName' => $sectorLink->LinkTableName
                ];
            }
            if (count($sectorLinkArrInsert)) {
                DB::table('sector_link')->insert($sectorLinkArrInsert);
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->SectorID;

        return json_encode($json);
    }

    public function view($id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $Sector = Sector::find($id);
        $SectorPerEmployee = SectorPer::where([
            'SectorID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->first();
        $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        if(!$SectorPerEmployee){
            if($Sector->AccessType == 2){
                //Nếu tài liệu là công khai
                $SectorPerEmployee = new SectorPer([
                    'SectorID' => $id,
                    'EmployeeID' => $EmployeeLogin->EmployeeID,
                    'EmployeeName' => $EmployeeLogin->EmployeeName,
                    'Access' => 1,
                    'AccessField' => 'all',
                    'Edit' => 0,
                    'EditField' => '',
                    'Delete' => 0,
                    'Create' => 0
                ]);
            }else{
                //Nếu tài liệu không phải công khai
                if($EmployeeLogin->UserID != $Sector->UserIDCreated && $EmployeeLogin->UserID != $Sector->AuthorizedPerson){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!';
                    return json_encode($json);
                }else{
                    $SectorPerEmployee = new SectorPer([
                        'SectorID' => $id,
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
        }else{
            if($EmployeeLogin->UserID == $Sector->UserIDCreated || $EmployeeLogin->UserID == $Sector->AuthorizedPerson){
                $SectorPerEmployee = new SectorPer([
                    'SectorID' => $id,
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

        if ($SectorPerEmployee) {
            if (strpos(',' . $SectorPerEmployee->AccessField . ',', ',all,')) {
                $SectorPerEmployee->AccessField = 'all';
            }
            if(strpos(',' . $SectorPerEmployee->EditField . ',', ',all,')){
                $SectorPerEmployee->EditField = 'all';
            }
        }

        $SectorPer = SectorPer::where('SectorID', '=', $id)->groupBy('EmployeeID')->get();
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::all();

        $SectorCate = SectorCate::where('SectorID', $id)->get();
        $arrCateID = [];
        foreach ($SectorCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $SectorCateList = SectorCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $SectorCateValue = SectorCateValue::whereIn('CateID', $arrCateID)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $Sector;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['SectorPer'] = $SectorPer;
        $json['SectorPerEmployee'] = $SectorPerEmployee;
        $json['data']['SectorCate'] = $SectorCate;
        $json['data']['SectorCateList'] = $SectorCateList;
        $json['data']['SectorCateValue'] = $SectorCateValue;
        return json_encode($json);
    }

    public function edit($id){
        $data = Sector::find($id);
        if(old('SectorNo')){
            $data->SectorNo = old('SectorNo');
        }else{
            $data->SectorNo = $data->Prefix.$data->SectorNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = Sector::find($id);
        if($data->SectorNo !== $request->SectorNo){
            $validator = Validator::make($request->all(), [
                'SectorNo' => 'required|unique:sector',
                'SectorName' => 'required:sector'
            ],
                [
                    'SectorNo.required' => 'Mã lĩnh vực thu không được để trống',
                    'SectorName.required' => 'Tên lĩnh vực thu không được để trống',
                    'SectorNo.unique' => 'Mã lĩnh vực thu đã tồn tại',
                ]);

            $data->SectorNo = $request->SectorNo;
        }
        if($data->Detail == 0){
            if ($data->SectorNo != $request->SectorNo || $data->ParentID != $request->SectorParentID || $data->SectorCate != null ){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if($request->ParentID){
            $parent = Sector::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
            $path = $parent->Path . '#' . $request->SectorNo;
        } else {
            $level = 1;
            $path = $request->SectorNo;
        }
        // set detail parentOld
        if($data->ParentID != null && $data->ParentID != $request->ParentID ){
            $parentOld = Sector::find($data->ParentID);
            $count = DB::table('sector')->where('ParentID', '=', $request->ParentID)->count();
            if($count < 2){
                $parentOld->Detail =1;
                $parentOld->save();
            }
        }
        $data->SectorName = $request->SectorName;
        $data->ParentID = $request->ParentID;
        $data->ParentNo = $request->ParentNo;
        $data->Path = $path;
        $data->Level = $level;
        $data->ParentName = $request->ParentName;
        $data->SbiCategoryID = $request->SbiCategoryID;
        $data->SbiCategoryNo = $request->SbiCategoryNo;
        $data->SbiCategoryName = $request->SbiCategoryName;


        $data->Note = $request->Note;

        if ($data->AccessType !== $request->AccessType) {
            $data->AccessType = $request->AccessType;
        }

        $data->save();

        // sector cate
        if ($request->has('SectorCate')) {
            $SectorCate = $request->post('SectorCate');
            DB::table('sector_cate')->where('SectorID', $id)->delete();
            $arrInsertCate = [];
            foreach ($SectorCate as $key => $cate){
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'SectorID' => $id,
                        'CateID' => $cate['CateID'],
                        'CateNo' => $cate['CateNo'],
                        'CateName' => $cate['CateName'],
                        'CateValue' => $cate['CateValue'],
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('sector_cate')->insert($arrInsertCate);
            }

        }

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
        $SectorLink = SectorLink::where('SectorID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $SectorLink;

        return json_encode($json);
    }

    //Link
    public function updateLink(Request $request){
        $LinkArray = $request->post('SectorLink');
        $id = $request->post('SectorID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'SectorID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        SectorLink::where(['SectorID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('sector_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getCate(Request $request){
        $sectorCateList = SectorCateList::where('Inactive', '=', 0)->get();
        $sectorCateValue = SectorCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['SectorCateList'] = $sectorCateList;
        $json['data']['SectorCateValue'] = $sectorCateValue;
        return json_encode($json);
    }

    public function getFile(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $SectorFile = SectorFile::where('SectorID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $SectorFile;

        return json_encode($json);
    }
    public function getVideo(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $SectorVideo = SectorVideo::where('SectorID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $SectorVideo;

        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $SectorID = $request->post('SectorID');
        $SectorPer = $request->post('SectorPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($SectorPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'SectorID' => $SectorID,
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
        SectorPer::where(['SectorID' => $SectorID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('sector_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $SectorID;
        return json_encode($json);
    }

    public function delete($id){
        $Sector = Sector::find($id);
        $auth = Auth::user();
        if($Sector){
            if ($auth->UserType === 1) {
                $Sector->delete();
                SectorCate::where('SectorID', $id)->delete();
                SectorPer::where('SectorID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $isPer = SectorPer::where([
                    'SectorID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($Sector->UserIDCreated === $auth->UserID)){
                    $Sector->delete();
                    SectorCate::where('SectorID', $id)->delete();
                    SectorPer::where('SectorID', $id)->delete();
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
            Sector::destroy($idArray);
            SectorCate::whereIn('SectorID', $idArray)->delete();
            SectorPer::whereIn('SectorID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $sectorsPer = SectorPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('sector_per')
                    ->whereIn('SectorID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('SectorID');
            })->get();

            foreach ($sectorsPer as $sectorPer) {
                if ($sectorPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                Sector::destroy($idArray);
                SectorCate::whereIn('SectorID', $idArray)->delete();
                SectorPer::whereIn('SectorID', $idArray)->delete();

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
