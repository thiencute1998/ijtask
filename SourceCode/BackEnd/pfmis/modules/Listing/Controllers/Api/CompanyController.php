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
use Module\Listing\Models\CompanyCate;
use Module\Listing\Models\CompanyCateList;
use Module\Listing\Models\CompanyCateValue;
use Module\Listing\Models\CompanyFile;
use Module\Listing\Models\CompanyLink;
use Module\Listing\Models\CompanyPer;
use Module\Listing\Models\CompanyVideo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SbiChapter;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;
use phpDocumentor\Reflection\Types\Object_;

class CompanyController extends Controller
{

    public function list(Request $request)
    {
        $data = Company::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request)
    {
        return Excel::download(new ModelExport('company', ['select' => ['CompanyNo', 'CompanyName'], 'conditions' => []]), 'donvi.xlsx');
    }

    public function create()
    {
        $company = Company::all();
        $employee = Employee::all();
        if (old('CompanyNo')) {
            $auto = old('CompanyNo');
        } else {
            $auto = AutoNo::genNewNumberNo('company');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['company'] = $company;
        $json['data']['employee'] = $employee;
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'CompanyNo' => 'required|unique:company',
            'CompanyName' => 'required:company',
        ],
            [
                'CompanyNo.required' => 'Mã đơn vị không được để trống',
                'CompanyNo.unique' => 'Mã đơn vị đã tồn tại',
                'CompanyName.required' => 'Tên đơn vị không được để trống',
            ]
        );

        $autoModel = SysAutoNumber::where('NumberKey', 'company')->first();
        AutoNo::storeValidate($validator, $request, 'company', $autoModel, 'đơn vị');
        if ($validator->fails()) {
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        if(!$request->ParentID){
            if($request->ManagementLevel == 3 || $request->ManagementLevel == 4){
                $json['status'] = 5;
                $json['msg'] = 'Lỗi!';
                $json['data'] = [];
                return $json;
            }
        }
        // set Parent and Level
        if($request->ParentID){
            $parent = Company::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
            $pathParent = $parent->Path;
            $path = $pathParent. '_' . $request->CompanyNo;

        } else {
            $level = 1;
            $path = $request->CompanyNo;
        }
        $array_custom = [
            'Level' => $level,
            'Detail' => 1,
            'Path' => $path,
            'AccessType'=> ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated'=> Auth::user()->UserID,
            'AuthorizedPerson'=> Auth::user()->UserID
        ];
        $request->merge($array_custom);
        $company = Company::create($request->all());
        $company->save();
        $company->save();
        $CompanyPer = new CompanyPer([
            'CompanyID' => $company->CompanyID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CompanyPer->save();
        //company cate
        $arrInsert = [];
        if ($request->post('CompanyCate')) {
            foreach ($request->post('CompanyCate') as $key => $item) {
                if ($item['CateID']) {
                    $arrInsert[] = [
                        'CompanyID' => $company->CompanyID,
                        'CateID' => $item['CateID'],
                        'CateValue' => $item['CateValue']
                    ];
                }
            }

            DB::table('company_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $company->CompanyID;

        return json_encode($json);
    }

    public function view($id)
    {
        $data = Company::find($id);
        if ($data) {
//            $data->CompanyNo = $data->Prefix.$data->CompanyNo.$data->Suffix;
            $arrParent = [];
            if ($data->ParentID) {
                $parent = Company::find($data->ParentID);
                if (isset($parent->CompanyNo) && isset($parent->CompanyName)) {
                    $arrParent = [
                        "ParentID" => $data->ParentID,
                        "ParentNo" => $parent->CompanyNo,
                        "ParentName" => $parent->CompanyName
                    ];
                }
            }
            $CompanyPerEmployee = CompanyPer::where([
                'CompanyID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID
            ])->first();
            $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

            if (!$CompanyPerEmployee) {
                if ($data->AccessType == 2) {
                    //Nếu tài liệu là công khai
                    $CompanyPerEmployee = new CompanyPer([
                        'CompanyID' => $id,
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
                    //Nếu tài liệu không phải công khai
                    if ($EmployeeLogin->UserID != $data->UserIDCreated && $EmployeeLogin->UserID != $data->AuthorizedPerson) {
                        $json['status'] = 3;
                        $json['msg'] = 'Bạn không có quyền truy cập!';
                        return json_encode($json);
                    } else {
                        $CompanyPerEmployee = new CompanyPer([
                            'CompanyID' => $id,
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
            } else {
                if ($EmployeeLogin->UserID == $data->UserIDCreated || $EmployeeLogin->UserID == $data->AuthorizedPerson) {
                    $CompanyPerEmployee = new CompanyPer([
                        'CompanyID' => $id,
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

            if ($CompanyPerEmployee) {
                if (strpos(',' . $CompanyPerEmployee->AccessField . ',', ',all,')) {
                    $CompanyPerEmployee->AccessField = 'all';
                }
                if (strpos(',' . $CompanyPerEmployee->EditField . ',', ',all,')) {
                    $CompanyPerEmployee->EditField = 'all';
                }
            }
            $CompanyPer = CompanyPer::where('CompanyID', '=', $id)->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $Company = Company::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $CompanyCate = CompanyCate::where('CompanyID', $id)->get();
            $arrCate = [];
            foreach ($CompanyCate as $Cate) {
                $arrCate[] = $Cate->CateID;
            }
            $CompanyCateList = CompanyCateList::whereIn('CateID', $arrCate)->where('Inactive', 0)->get();
            $CompanyCateValue = CompanyCateValue::whereIn('CateID', $arrCate)->get();
            $Chapter = SbiChapter::find($data->SbiChapterID);
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['Chapter'] = $Chapter;
            $json['Company'] = $Company;
            $json['Employee'] = $Employee;
            $json['CompanyPer'] = $CompanyPer;
            $json['CompanyPerEmployee'] = $CompanyPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CompanyCate'] = $CompanyCate;
            $json['data']['CompanyCateList'] = $CompanyCateList;
            $json['data']['CompanyCateValue'] = $CompanyCateValue;
            return json_encode($json);
        } else {

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id)
    {
        $employee = Employee::all();
        $data = Company::find($id);
        $company = Company::all();
        if (old('CompanyNo')) {
            $data->CompanyNo = old('CompanyNo');
        } else {
            $data->CompanyNo = $data->Prefix . $data->CompanyNo . $data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['company'] = $company;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id)
    {

        $company = Company::find($id);
        if (!$company) {

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($company->Detail == 0){
            if($request->CompanyNo != $company->CompanyNo || $request->ParentID != $company->ParentID){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }

        }
        if(!$request->ParentID){
            if($request->ManagementLevel == 3 || $request->ManagementLevel == 4){
                $json['status'] = 5;
                $json['msg'] = 'Lỗi!';
                $json['data'] = [];
                return $json;
            }
        }
        if ($company->CompanyNo !== $request->CompanyNo) {
            $validator = Validator::make($request->all(), [
                'CompanyNo' => 'required',
                'CompanyName' => 'required',
            ],
                [
                    'CompanyNo.required' => 'Mã đơn vị không được để trống',
                    'CompanyName.required' => 'Tên đơn vị không được để trống',
                ]);


            $autoModel = SysAutoNumber::where('NumberKey', 'company')->first();
            AutoNo::UpdateValidate($validator, $request, $company, 'company', $autoModel, 'đơn vị');

            if ($validator->fails()) {
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $company->CompanyNo = $request->CompanyNo;


            // set for new Parent
            if($request->ParentID){
                $parent = Company::find($request->ParentID);
                $parent->Detail = 0;
                $parent->save();
                $level = $parent->Level + 1;
                $company->Path = $parent->Path . '_' . $request->CompanyNo;
            }else{
                $level = 1;
                $company->Path = $company->CompanyNo;
            }
            $company->Level = $level;
            $company->Detail = 1;
            // set for old Parents
            if($company->ParentID){
                $parentOld = Company::find($company->ParentID);
                $count = DB::table('company')->where('ParentID','=', $parentOld->CompanyID)->count();
                if($count < 2){
                    $parentOld->Detail = 1;
                    $parentOld->save();
                }
            }
        }

        $company->CompanyName = $request->CompanyName;
        $company->ParentID = $request->ParentID;
        $company->ParentNo = $request->ParentNo;
        $company->Note = $request->Note;
        $company->Address = $request->Address;
        $company->Tel = $request->Tel;
        $company->Fax = $request->Fax;
        $company->Email = $request->Email;
        $company->EmployeeID = $request->EmployeeID;
        $company->ContactName = $request->ContactName;
        $company->ContactTel = $request->ContactTel;
        $company->SectorID = $request->SectorID;
        $company->SectorName = $request->SectorName;
        $company->Inactive = $request->Inactive;
        $company->ProvinceID = $request->ProvinceID;
        $company->ProvinceName = $request->ProvinceName;
        $company->DistrictID = $request->DistrictID;
        $company->DistrictName = $request->DistrictName;
        $company->CommuneID = $request->CommuneID;
        $company->CommuneName = $request->CommuneName;
        $company->AccessType = $request->AccessType;
        $company->IsFinancialCompany = $request->IsFinancialCompany;
        $company->ManagementLevel = $request->ManagementLevel;
        $company->CenterID = $request->CenterID;
        $company->CenterNo = $request->CenterNo;
        $company->CenterName = $request->CenterName;
        $company->SumCompanyType = $request->SumCompanyType;
        $company->save();

        // company cate
        if ($request->post('CompanyCate')) {
            $CompanyCate = $request->post('CompanyCate');
            CompanyCate::where('CompanyID', $id)->delete();
            $arrInsert = [];
            foreach ($CompanyCate as $cate) {
                if ($cate['CateID']) {
                    $arrInsert[] = [
                        "CompanyID" => $id,
                        "CateID" => $cate['CateID'],
                        "CateValue" => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if (count($arrInsert)) DB::table('company_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $company->CompanyID;
        return $json;


    }

    public function delete($id)
    {
        $data = Company::find($id);
        if ($data) {
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
            // set for old Parent
            if($data->ParentID){
                $parent = Company::find($data->ParentID);
                $count = DB::table('company')->where('ParentID', '=', $parent->CompanyID)->count();
                if($count < 2){
                    $parent->Detail = 1;
                    $parent->save();
                }
            }
            DB::table('company_cate')->where('CompanyID', '=', $data->CompanyID)->delete();
            DB::table('company_per')->where('CompanyID', '=', $data->CopanyID)->delete();
            DB::table('company_link')->where('CompanyID', '=', $data->CopanyID)->delete();
            $data->delete();

        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function deleteArray(Request $request)
    {
        $IDs = $request->post('array_id');
        foreach ($IDs as $id){
            $data = Company::find($id);
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại!';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        DB::table('company_cate')->whereIn('CompanyID', $IDs)->delete();
        Company::destroy($IDs);
        $sql = "UPDATE company
                SET Detail = 1
                WHERE CompanyID NOT IN (SELECT ParentID FROM (select ParentID from company WHERE ParentID is not null) t)";
        DB::statement($sql);
        DB::table('company_cate')->whereIn('CompanyID', $IDs)->delete();
        DB::table('company_link')->whereIn('CompanyID', $IDs)->delete();
        DB::table('company_per')->whereIn('CompanyID', $IDs)->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function updatePer(Request $request, $id)
    {
        $CompanyID = $request->post('CompanyID');
        $CompanyPer = $request->post('CompanyPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CompanyPer as $value) {
            if ($value) {
                if ($value['Access']) {
                    $arrInsert[] = [
                        'CompanyID' => $CompanyID,
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
        CompanyPer::where(['CompanyID' => $CompanyID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('company_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $CompanyID;
        return json_encode($json);
    }

    public function getCompanyCateList()
    {
        $companyCateList = CompanyCateList::where('Inactive', 0)->get();
        $companyCateValue = CompanyCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CompanyCateList'] = $companyCateList;
        $json['data']['CompanyCateValue'] = $companyCateValue;
        return json_encode($json);
    }

    public function getCompanyLink($id)
    {
        $data = CompanyLink::where('CompanyID', $id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function updateLink(Request $request)
    {
        $LinkArray = $request->post('CompanyLink');
        $id = $request->post('CompanyID');
        $arrLink = [];
        foreach ($LinkArray as $item) {
            if ($item['LinkTable']) {
                $arrLink[] = [
                    'CompanyID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        CompanyLink::where(['CompanyID' => $id])->delete();
        if (!empty($arrLink)) {
            DB::table('company_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getCompanyFile($id)
    {
        $data = CompanyFile::where('CompanyID', $id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function uploadFile(Request $request, $id)
    {
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if ($request->LineID) {
            $CompanyFile = CompanyFile::find($request->LineID);
        } else {
            $CompanyFile = new CompanyFile();
        }
        $CompanyFile->FileName = $request->FileName;
        $CompanyFile->FileNameRoot = $request->FileName;
        $CompanyFile->Description = $request->Description;
        $CompanyFile->FileType = $request->FileType;
        $CompanyFile->FileSize = $request->FileSize;
        $CompanyFile->CompanyID = $id;
        $CompanyFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $CompanyFile->UserModified = Auth::user()->username;
        $fileattach = $request->FileUpload;

        if ($fileattach && $request->changeFile == 1) {
            $ext = strtolower($fileattach->getClientOriginalExtension());
            $size = $fileattach->getSize();
            $name = $fileattach->getClientOriginalName();
            if ($size <= 400000000) {
                $arr_ext_img = ["gif", "jpeg", "jpg", "png", "ico", "psd", "ai"];
                $arr_ext_doc = ["pptx", "ppt", "pps", "xls", "xlsx", "csv", "doc", "docx", "pdf", "txt", "zip", "rar", "cif"];
                $arr_ext_other = ['aif', 'cda', 'mid', 'midi', 'mp3', 'mpa', 'ogg', 'wav', 'wma', 'wpl', '7z', 'arj', 'deb', 'pkg', 'rar', 'rpm', 'tar', '.gz', 'zip', 'z',
                    'bin', 'dmg', 'iso', 'toast', 'vcd', 'csv', 'dat', 'db', 'dbf', 'log', 'mdb', 'sav', 'sql', 'tar', 'xml', 'email', 'eml', 'emlx', 'msg', 'oft', 'ost',
                    'pst', 'vcf', 'fnt', 'fon', 'otf', 'ttf', 'ai', 'bmp', 'gif', 'ico', 'ps', 'psd', 'svg', 'tif', 'tiff', '3g2', '3gp', 'avi', 'flv', 'h264', 'm4v', 'mkv', 'mov',
                    'mp4', 'mpg', 'mpeg', 'rm', 'swf', 'vob', 'wmv', 'm4a'
                ];
                $is_doc = in_array($ext, $arr_ext_doc);
                $is_img = in_array($ext, $arr_ext_img);
                $is_other = in_array($ext, $arr_ext_other);
                if ($is_doc || $is_img || $is_other) {
                    $nameFile = Auth::user()->username . '_' . (date('YmdHis') . gettimeofday()['usec']) . "." . $ext;
                    $linkFileAttach = '/files-attach/' . $nameFile;
                    $fileModel = new FileModel(
                        [
                            'FileName' => $name,
                            'FileType' => $ext,
                            'FileSize' => $size,
                            'DateModified' => date('Y-m-d h:i:s'),
                            'UserModified' => $userId,
                            'Link' => $linkFileAttach,
                        ]
                    );

                    $fileModel->save();
                    $fileattach->move('files-attach', $nameFile);

                    $CompanyFile->Link = $linkFileAttach;
                    $CompanyFile->DateModifiedRoot = $fileModel->DateModified;
                    $CompanyFile->FileID = $fileModel->FileID;
                } else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            } else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $CompanyFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $CompanyFile;
        return json_encode($json);
    }

    public function deleteFile(Request $request, $id)
    {
        $dFile = CompanyFile::where(['LineID' => $id])->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }

    //download File
    public function downloadFile(Request $request, $id)
    {
        $file = FileModel::find($id);
        return response()->download(public_path($file->Link));
    }

    //download all file
    public function downloadAllFile(Request $request, $id)
    {

        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        if (!$id) {
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại tài liệu';
            return json_encode($json);
        }
        $files = CompanyFile::where('CompanyID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $company = Company::find($id);
            $zipName = '';
            if ($company) {
                $zipName = IjString::CreateSlug($company->CompanyName, '-');
            }
            $zipLink = IjFile::CreateZipArchive($arrLink, true, $zipName);
            if ($zipLink) {
                $json['status'] = 1;
                $json['msg'] = 'Thành công';
                $json['data'] = $zipLink;
                return json_encode($json);
            }
        }

        $json['msg'] = 'Không tồn tại file zip';
        return json_encode($json);
    }

    public function getVideo(Request $request, $id)
    {
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $CompanyVideo = CompanyVideo::where('CompanyID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $CompanyVideo;

        return json_encode($json);
    }

    //Upload Video
    public function uploadVideo(Request $request, $id)
    {
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if ($request->LineID) {
            $CompanyVideo = CompanyVideo::find($request->LineID);
        } else {
            $CompanyVideo = new CompanyVideo();
        }
        $CompanyVideo->VideoName = $request->VideoName;
        $CompanyVideo->Description = $request->Description;
        $CompanyVideo->VideoType = $request->VideoType;
        $CompanyVideo->VideoSize = $request->VideoSize;
        $CompanyVideo->CompanyID = $id;
        $CompanyVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $CompanyVideo->UserModified = Auth::user()->username;
        $fileattach = $request->VideoUpload;
        if ($fileattach && $request->changeVideo == 1) {
            $ext = strtolower($fileattach->getClientOriginalExtension());
            $size = $fileattach->getSize();
            $name = $fileattach->getClientOriginalName();
            if ($size <= 400000000) {
                $arr_ext_video = ['mpg', 'mpeg', 'avi', 'wmv', 'mov', 'rm', 'ram', 'swf', 'flv', 'ogg', 'webm', '3g2', '3gp', 'avi', 'h264', 'm4v', 'mkv', 'vob',
                    'mp4', 'aif', 'cda', 'mid', 'midi', 'mp3', 'mpa', 'ogg', 'wav', 'wma', 'wpl', 'm4a'];
                $is_video = in_array($ext, $arr_ext_video);
                if ($is_video) {
                    $nameVideo = Auth::user()->username . '_' . (date('YmdHis') . gettimeofday()['usec']) . "." . $ext;
                    $linkFileAttach = '/files-attach/' . $nameVideo;
                    $fileModel = new FileModel(
                        [
                            'FileName' => $name,
                            'FileType' => $ext,
                            'FileSize' => $size,
                            'DateModified' => date('Y-m-d h:i:s'),
                            'UserModified' => $userId,
                            'Link' => $linkFileAttach,
                        ]
                    );

                    $fileModel->save();
                    $fileattach->move('files-attach', $nameVideo);

                    $CompanyVideo->Link = $linkFileAttach;
                    $CompanyVideo->DateModifiedRoot = $fileModel->DateModified;
                    $CompanyVideo->FileID = $fileModel->FileID;

                } else {
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            } else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $CompanyVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $CompanyVideo;
        return json_encode($json);
    }

    //Delete Video
    public function deleteVideo(Request $request, $id)
    {
        $tVideo = CompanyVideo::where(['LineID' => $id])->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }

    //Download Video
    public function downloadVideo(Request $request, $id)
    {
        $file = FileModel::find($id);
        return response()->download(public_path($file->Link));
    }

    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $filter = $request->post('filter');
        $data = Company::query();

        if(isset($filter['CompanyName'])){
            $data->where('CompanyName', 'like', '%'.$filter['CompanyName'].'%');
        }
        if(isset($filter['CompanyNo'])){
            $data->whereRaw('concat(Prefix, CompanyNo, Suffix) like("%'.$filter['CompanyNo'].'%")');
        }
        if(isset($filter['Fax'])){
            $data->where('Fax', 'like', '%'.$filter['Fax'].'%');
        }
        if(isset($filter['Tel'])){
            $data->where('Tel', 'like', '%'.$filter['Tel'].'%');
        }
        if(isset($filter['Email'])){
            $data->where('Email', 'like', '%'.$filter['Email'].'%');
        }
        $perPage = (int)$request->post('per_page');
        $data->orderBy('company.CompanyID', 'desc');
        $data->paginate($perPage);
        $json['data']['detail'] = $data->paginate($perPage)->items();
        $json['data']['total'] = $data->paginate($perPage)->total();
        $json['data']['per_page'] = $data->paginate($perPage)->perPage();
        $json['data']['current_page'] = $data->paginate($perPage)->currentPage();
        return json_encode($json);
    }

    public function getCateValueCompany(Request $request)
    {
        $data = null;
        $auth = Auth::user();
        $FieldWhere = $request->post('FieldWhere');
        $query = Company::query();
        $query->select('company.*');

        if ($request->post('search')) {
            $query->where('CompanyName', 'like', '%' . $request->search . '%');
        }
        if ($FieldWhere) {
            foreach ($FieldWhere as $key => $value) {
                $query->where($key, '=', $value);
            }
        }
        $query->join('company_cate', 'company.CompanyID', '=', 'company_cate.CompanyID');
        $query->where('company_cate.CateID', $request->CateID);
        if ($request->CateValue) {
            $query->where(function ($q) use ($request) {
                foreach ($request->CateValue as $cate_value) {
                    $q->orWhere('company_cate.CateValue', $cate_value);
                }
            });
        }

        $query->groupBy('company.CompanyID');

        if ($request->post('per_page')) {
            $per_page = $request->post('per_page');
        } else {
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $data = $query->paginate($per_page);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = Company::query();
        $query->where('company.ParentID', $ParentID);
        $query->groupBy('company.CompanyID');
        $data = $query->orderBy('company.CompanyNo', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    //get AutOrg
    public function getOrg(Request $request){
        $query = Company::query();
        $position = null;
        if($request->post('isAutOrg')){
            $query->where('isAutOrg', '=', $request->post('isAutOrg'));
        }
        if($request->post('isFinOrg')){
            $query->where('isFinOrg', '=', $request->post('isFinOrg'));
        }
        if($request->post('ManagementLevel')){
            $query->where('ManagementLevel', '=', $request->post('ManagementLevel'));
        }
        if($request->post('ProvinceID')){
            $query->where('ProvinceID', '=', $request->post('ProvinceID'));
        }
        if($request->post('DistrictID')){
            $query->where('DistrictID', '=', $request->post('DistrictID'));
        }
        $data = $query->first();
        if($data && $data->EmployeeID){
            $position = DB::table('employee')->where('EmployeeID', '=',  $data->EmployeeID)->first();
        }
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        $json['position'] = $position;
        return json_encode($json);
    }
}
