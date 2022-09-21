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
use Module\Listing\Models\SbrSector;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SbrSectorCate;
use Module\Listing\Models\SbrSectorCateList;
use Module\Listing\Models\SbrSectorCateValue;
use Module\Listing\Models\SbrSectorFile;
use Module\Listing\Models\SbrSectorPer;
use Module\Listing\Models\SbrSectorVideo;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\SbrSectorLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class SbrSectorController extends Controller{

    public function list(Request $request){
        $data = SbrSector::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('sbr_sector', ['select' => ['SbrSectorNo', 'SbrSectorName'], 'conditions' => []]), 'nganh.xlsx');
    }

    public function create(){


        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = [];
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'SbrSectorNo' => 'required|unique:sbr_sector',
            'SbrSectorName' => 'required:sbr_sector',
        ],
            [
                'SbrSectorNo.required' => 'Mã lĩnh vực thu không được để trống',
                'SbrSectorNo.unique' => 'Mã lĩnh vực thu đã tồn tại',
                'SbrSectorName.required' => 'Tên lĩnh vực thu không được để trống',
            ]);
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        if($request->ParentID){
            $parent = SbrSector::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
            $path = $parent->Path. '#' . $request->SbrSectorNo;
        } else {
            $level = 1;
            $path = $request->SbrSectorNo;
        }
        $data = new SbrSector([
            'SbrSectorNo' => $request->SbrSectorNo,
            'SbrSectorName' => $request->SbrSectorName,
            'ParentID' => $request->ParentID,
            'ParentNo' =>$request->ParentNo,
            'ParentName' =>$request->ParentName,
            'Level' => $level,
            'Path' => $path,
            'Detail' => 1,
            'SbiChapterID' => $request->SbiChapterID,
            'SbiChapterNo' => $request->SbiChapterNo,
            'SbiChapterName' => $request->SbiChapterName,
            'Note' => $request->Note,
            'AccessType' => ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated' => Auth::user()->UserID,
            'AuthorizedPerson' => Auth::user()->UserID,
            'Inactive' => $request->Inactive,
        ]);
        $data->save();

        $SbrSectorPer = new SbrSectorPer([
            'SbrSectorID' => $data->SbrSectorID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $SbrSectorPer->save();

        // cate list
        if ($request->has('SbrSectorCate')) {
            $SbrSectorCate = $request->post('SbrSectorCate');
            $arrInsertCate = [];
            foreach ($SbrSectorCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'SbrSectorID' => $data->SbrSectorID,
                        'CateID' => $cate['CateID'],
                        'CateNo' => $cate['CateNo'],
                        'CateName' => $cate['CateName'],
                        'CateValue' => $cate['CateValue'],
                        'Description' => $cate['Description'],
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('sbr_sector_cate')->insert($arrInsertCate);
            }
        }

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');
            // sbr_sector_per
            $SbrSectorPerCopy = SbrSectorPer::where('SbrSectorID', $CopyID)->where('EmployeeID', '!=', Auth::user()->EmployeeID)->get();
            $sbrSectorPerArrInsert = [];
            foreach ($SbrSectorPerCopy as $sbrSectorPer) {
                $sbrSectorPerArrInsert[] = [
                    'SbrSectorID' => $data->SbrSectorID,
                    'EmployeeID' => $sbrSectorPer->EmployeeID,
                    'EmployeeName' => $sbrSectorPer->EmployeeName,
                    'Access' => $sbrSectorPer->Access,
                    'AccessField' => $sbrSectorPer->AccessField,
                    'Edit' => $sbrSectorPer->Edit,
                    'EditField' => $sbrSectorPer->EditField,
                    'Delete' => $sbrSectorPer->Delete,
                    'Create' => $sbrSectorPer->Create
                ];
            }
            if (count($sbrSectorPerArrInsert)) {
                DB::table('sbr_sector_per')->insert($sbrSectorPerArrInsert);
            }

            // sbr_sector_link
            $SbrSectorLinkCopy = SbrSectorLink::where('SbrSectorID', $CopyID)->get();
            $sbrSectorLinkArrInsert = [];
            foreach ($SbrSectorLinkCopy as $sbrSectorLink) {
                $sbrSectorLinkArrInsert[] = [
                    'SbrSectorID' => $data->SbrSectorID,
                    'LinkID' => $sbrSectorLink->LinkID,
                    'LinkNo' => $sbrSectorLink->LinkNo,
                    'LinkName' => $sbrSectorLink->LinkName,
                    'LinkTable' => $sbrSectorLink->LinkTable,
                    'LinkTableName' => $sbrSectorLink->LinkTableName
                ];
            }
            if (count($sbrSectorLinkArrInsert)) {
                DB::table('sbr_sector_link')->insert($sbrSectorLinkArrInsert);
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->SbrSectorID;

        return json_encode($json);
    }

    public function view($id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $SbrSector = SbrSector::find($id);
        $SbrSectorPerEmployee = SbrSectorPer::where([
            'SbrSectorID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->first();
        $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        if(!$SbrSectorPerEmployee){
            if($SbrSector->AccessType == 2){
                //Nếu tài liệu là công khai
                $SbrSectorPerEmployee = new SbrSectorPer([
                    'SbrSectorID' => $id,
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
                if($EmployeeLogin->UserID != $SbrSector->UserIDCreated && $EmployeeLogin->UserID != $SbrSector->AuthorizedPerson){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!';
                    return json_encode($json);
                }else{
                    $SbrSectorPerEmployee = new SbrSectorPer([
                        'SbrSectorID' => $id,
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
            if($EmployeeLogin->UserID == $SbrSector->UserIDCreated || $EmployeeLogin->UserID == $SbrSector->AuthorizedPerson){
                $SbrSectorPerEmployee = new SbrSectorPer([
                    'SbrSectorID' => $id,
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

        if ($SbrSectorPerEmployee) {
            if (strpos(',' . $SbrSectorPerEmployee->AccessField . ',', ',all,')) {
                $SbrSectorPerEmployee->AccessField = 'all';
            }
            if(strpos(',' . $SbrSectorPerEmployee->EditField . ',', ',all,')){
                $SbrSectorPerEmployee->EditField = 'all';
            }
        }

        $SbrSectorPer = SbrSectorPer::where('SbrSectorID', '=', $id)->groupBy('EmployeeID')->get();
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::all();

        $SbrSectorCate = SbrSectorCate::where('SbrSectorID', $id)->get();
        $arrCateID = [];
        foreach ($SbrSectorCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $SbrSectorCateList = SbrSectorCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $SbrSectorCateValue = SbrSectorCateValue::whereIn('CateID', $arrCateID)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $SbrSector;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['SbrSectorPer'] = $SbrSectorPer;
        $json['SbrSectorPerEmployee'] = $SbrSectorPerEmployee;
        $json['data']['SbrSectorCate'] = $SbrSectorCate;
        $json['data']['SbrSectorCateList'] = $SbrSectorCateList;
        $json['data']['SbrSectorCateValue'] = $SbrSectorCateValue;
        return json_encode($json);
    }

    public function edit($id){
        $data = SbrSector::find($id);
        if(old('SbrSectorNo')){
            $data->SbrSectorNo = old('SbrSectorNo');
        }else{
            $data->SbrSectorNo = $data->Prefix.$data->SbrSectorNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = SbrSector::find($id);
        if($data->SbrSectorNo !== $request->SbrSectorNo){
            $validator = Validator::make($request->all(), [
                'SbrSectorNo' => 'required|unique:sbr_sector',
                'SbrSectorName' => 'required:sbr_sector'
            ],
                [
                    'SbrSectorNo.required' => 'Mã lĩnh vực thu không được để trống',
                    'SbrSectorName.required' => 'Tên lĩnh vực thu không được để trống',
                    'SbrSectorNo.unique' => 'Mã lĩnh vực thu đã tồn tại',
                ]);

            $data->SbrSectorNo = $request->SbrSectorNo;
        }
        if($data->Detail == 0){
            if ($data->SbrSectorNo != $request->SbrSectorNo || $data->ParentID != $request->SbrSectorParentID || $data->SbrSectorCate != null ){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if($request->ParentID){
            $parent = SbrSector::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
            $path = $parent->Path . '#' . $request->SbrSectorNo;
        } else {
            $level = 1;
            $path = $request->SbrSectorNo;
        }
        // set detail parentOld
        if($data->ParentID != null && $data->ParentID != $request->ParentID ){
            $parentOld = SbrSector::find($data->ParentID);
            $count = DB::table('sbr_sector')->where('ParentID', '=', $request->ParentID)->count();
            if($count < 2){
                $parentOld->Detail =1;
                $parentOld->save();
            }
        }
        $data->SbrSectorName = $request->SbrSectorName;
        $data->ParentID = $request->ParentID;
        $data->ParentNo = $request->ParentNo;
        $data->Path = $path;
        $data->Level = $level;
        $data->ParentName = $request->ParentName;
        $data->SbiChapterID = $request->SbiChapterID;
        $data->SbiChapterNo = $request->SbiChapterNo;
        $data->SbiChapterName = $request->SbiChapterName;


        $data->Note = $request->Note;

        if ($data->AccessType !== $request->AccessType) {
            $data->AccessType = $request->AccessType;
        }

        $data->save();

        // sbr_sector cate
        if ($request->has('SbrSectorCate')) {
            $SbrSectorCate = $request->post('SbrSectorCate');
            DB::table('sbr_sector_cate')->where('SbrSectorID', $id)->delete();
            $arrInsertCate = [];
            foreach ($SbrSectorCate as $key => $cate){
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'SbrSectorID' => $id,
                        'CateID' => $cate['CateID'],
                        'CateNo' => $cate['CateNo'],
                        'CateName' => $cate['CateName'],
                        'CateValue' => $cate['CateValue'],
                        'Description' => $cate['Description'],
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('sbr_sector_cate')->insert($arrInsertCate);
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
        $SbrSectorLink = SbrSectorLink::where('SbrSectorID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $SbrSectorLink;

        return json_encode($json);
    }

    //Link
    public function updateLink(Request $request){
        $LinkArray = $request->post('SbrSectorLink');
        $id = $request->post('SbrSectorID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'SbrSectorID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        SbrSectorLink::where(['SbrSectorID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('sbr_sector_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getCate(Request $request){
        $sbrSectorCateList = SbrSectorCateList::where('Inactive', '=', 0)->get();
        $sbrSectorCateValue = SbrSectorCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['SbrSectorCateList'] = $sbrSectorCateList;
        $json['data']['SbrSectorCateValue'] = $sbrSectorCateValue;
        return json_encode($json);
    }

    public function getFile(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $SbrSectorFile = SbrSectorFile::where('SbrSectorID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $SbrSectorFile;

        return json_encode($json);
    }
    public function getVideo(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $SbrSectorVideo = SbrSectorVideo::where('SbrSectorID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $SbrSectorVideo;

        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $SbrSectorID = $request->post('SbrSectorID');
        $SbrSectorPer = $request->post('SbrSectorPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($SbrSectorPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'SbrSectorID' => $SbrSectorID,
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
        SbrSectorPer::where(['SbrSectorID' => $SbrSectorID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('sbr_sector_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $SbrSectorID;
        return json_encode($json);
    }

    public function delete($id){
        $SbrSector = SbrSector::find($id);
        $auth = Auth::user();
        if($SbrSector){
            if ($auth->UserType === 1) {
                $SbrSector->delete();
                SbrSectorCate::where('SbrSectorID', $id)->delete();
                SbrSectorPer::where('SbrSectorID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $isPer = SbrSectorPer::where([
                    'SbrSectorID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($SbrSector->UserIDCreated === $auth->UserID)){
                    $SbrSector->delete();
                    SbrSectorCate::where('SbrSectorID', $id)->delete();
                    SbrSectorPer::where('SbrSectorID', $id)->delete();
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
            SbrSector::destroy($idArray);
            SbrSectorCate::whereIn('SbrSectorID', $idArray)->delete();
            SbrSectorPer::whereIn('SbrSectorID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $sbrSectorsPer = SbrSectorPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('sbr_sector_per')
                    ->whereIn('SbrSectorID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('SbrSectorID');
            })->get();

            foreach ($sbrSectorsPer as $sbrSectorPer) {
                if ($sbrSectorPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                SbrSector::destroy($idArray);
                SbrSectorCate::whereIn('SbrSectorID', $idArray)->delete();
                SbrSectorPer::whereIn('SbrSectorID', $idArray)->delete();

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
