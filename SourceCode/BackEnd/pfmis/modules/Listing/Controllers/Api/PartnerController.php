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
use Module\Listing\Models\Partner;
use Module\Listing\Models\Employee;
use Module\Listing\Models\PartnerCate;
use Module\Listing\Models\PartnerCateList;
use Module\Listing\Models\PartnerCateValue;
use Module\Listing\Models\PartnerFile;
use Module\Listing\Models\PartnerPer;
use Module\Listing\Models\PartnerVideo;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\PartnerLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class PartnerController extends Controller{

    public function list(Request $request){
        $data = Partner::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('partner', ['select' => ['PartnerNo', 'PartnerName'], 'conditions' => []]), 'nhacungcap.xlsx');
    }

    public function create(){
        if(old('PartnerNo')){
            $auto = old('PartnerNo');
        }else{
            $auto = AutoNo::genNewNumberNo('partner');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'PartnerNo' => 'required|unique:partner'
        ],
            [
                'PartnerNo.required' => 'Mã nhà đối tác không được để trống',
                'PartnerNo.unique' => 'Mã nhà đối tác đã tồn tại',
            ]);

        $autoModel = SysAutoNumber::where('NumberKey', 'partner')->first();
        AutoNo::storeValidate($validator, $request, 'partner', $autoModel, 'nhà đối tác');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new Partner([
            'PartnerNo' => $request->PartnerNo,
            'PartnerName' => $request->PartnerName,
            'PartnerAddress' => $request->PartnerAddress,
            'FirstName' => $request->FirstName ,
            'MiddleName' => $request->MiddleName,
            'LastName' => $request->LastName,
            'FullName' => $request->FullName,
            'PartnerIdNo'=>$request->PartnerIdNo,
            'BirthDay' => $request->BirthDay,
            'PartnerIdIssuedDate' => $request->PartnerIdIssuedDate,
            'ProvinceID' => $request->ProvinceID,
            'ProvinceName' => $request->ProvinceName,
            'DistrictID' => $request->DistrictID,
            'DistrictName' => $request->DistrictName,
            'CommuneID' => $request->CommuneID,
            'CommuneName' => $request->CommuneName,
            'Nationality' => $request->Nationality,
            'NativeCountry' => $request->NativeCountry,
            'PermanceAddress' => $request->PermanceAddress,
            'ResidenceAddress' => $request->ResidenceAddress,
            'Tel' => $request->Tel,
            'Email' => $request->Email,
            'Note' => $request->Note,
            'AccessType' => ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated' => Auth::user()->UserID,
            'AuthorizedPerson' => Auth::user()->UserID,
            'Inactive' => $request->Inactive,
        ]);
        $data->save();

        $PartnerPer = new PartnerPer([
            'PartnerID' => $data->PartnerID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $PartnerPer->save();

        // cate list
        if ($request->has('PartnerCate')) {
            $PartnerCate = $request->post('PartnerCate');
            $arrInsertCate = [];
            foreach ($PartnerCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'PartnerID' => $data->PartnerID,
                        'CateID' => $cate['CateID'],
                        'CateValue' => $cate['CateValue']
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('partner_cate')->insert($arrInsertCate);
            }
        }

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');

            // partner per
            $PartnerPerCopy = PartnerPer::where('PartnerID', $CopyID)->where('EmployeeID', '!=', Auth::user()->EmployeeID)->get();
            $partnerPerArrInsert = [];
            foreach ($PartnerPerCopy as $partnerPer) {
                $partnerPerArrInsert[] = [
                    'PartnerID' => $data->PartnerID,
                    'EmployeeID' => $partnerPer->EmployeeID,
                    'EmployeeName' => $partnerPer->EmployeeName,
                    'Access' => $partnerPer->Access,
                    'AccessField' => $partnerPer->AccessField,
                    'Edit' => $partnerPer->Edit,
                    'EditField' => $partnerPer->EditField,
                    'Delete' => $partnerPer->Delete,
                    'Create' => $partnerPer->Create
                ];
            }
            if (count($partnerPerArrInsert)) {
                DB::table('partner_per')->insert($partnerPerArrInsert);
            }

            // partner link
            $PartnerLinkCopy = PartnerLink::where('PartnerID', $CopyID)->get();
            $partnerLinkArrInsert = [];
            foreach ($PartnerLinkCopy as $partnerLink) {
                $partnerLinkArrInsert[] = [
                    'PartnerID' => $data->PartnerID,
                    'LinkID' => $partnerLink->LinkID,
                    'LinkNo' => $partnerLink->LinkNo,
                    'LinkName' => $partnerLink->LinkName,
                    'LinkTable' => $partnerLink->LinkTable,
                    'LinkTableName' => $partnerLink->LinkTableName
                ];
            }
            if (count($partnerLinkArrInsert)) {
                DB::table('partner_link')->insert($partnerLinkArrInsert);
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;

        return json_encode($json);
    }

    public function view($id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $Partner = Partner::find($id);
        $PartnerPerEmployee = PartnerPer::where([
            'PartnerID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->first();
        $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        if(!$PartnerPerEmployee){
            if($Partner->AccessType == 2){
                //Nếu tài liệu là công khai
                $PartnerPerEmployee = new PartnerPer([
                    'PartnerID' => $id,
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
                if($EmployeeLogin->UserID != $Partner->UserIDCreated && $EmployeeLogin->UserID != $Partner->AuthorizedPerson){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!';
                    return json_encode($json);
                }else{
                    $PartnerPerEmployee = new PartnerPer([
                        'PartnerID' => $id,
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
            if($EmployeeLogin->UserID == $Partner->UserIDCreated || $EmployeeLogin->UserID == $Partner->AuthorizedPerson){
                $PartnerPerEmployee = new PartnerPer([
                    'PartnerID' => $id,
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

        if ($PartnerPerEmployee) {
            if (strpos(',' . $PartnerPerEmployee->AccessField . ',', ',all,')) {
                $PartnerPerEmployee->AccessField = 'all';
            }
            if(strpos(',' . $PartnerPerEmployee->EditField . ',', ',all,')){
                $PartnerPerEmployee->EditField = 'all';
            }
        }

        $PartnerPer = PartnerPer::where('PartnerID', '=', $id)->groupBy('EmployeeID')->get();
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::all();

        $PartnerCate = PartnerCate::where('PartnerID', $id)->get();
        $arrCateID = [];
        foreach ($PartnerCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $PartnerCateList = PartnerCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $PartnerCateValue = PartnerCateValue::whereIn('CateID', $arrCateID)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $Partner;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['PartnerPer'] = $PartnerPer;
        $json['PartnerPerEmployee'] = $PartnerPerEmployee;
        $json['data']['PartnerCate'] = $PartnerCate;
        $json['data']['PartnerCateList'] = $PartnerCateList;
        $json['data']['PartnerCateValue'] = $PartnerCateValue;
        return json_encode($json);
    }

    public function edit($id){
        $data = Partner::find($id);
        if(old('PartnerNo')){
            $data->PartnerNo = old('PartnerNo');
        }else{
            $data->PartnerNo = $data->Prefix.$data->PartnerNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = Partner::find($id);
        if($data->PartnerNo !== $request->PartnerNo){
            $validator = Validator::make($request->all(), [
                'PartnerNo' => 'required|unique:partner'
            ],
                [
                    'PartnerNo.required' => 'Mã nhà cung cấp không được để trống',
                    'PartnerNo.unique' => 'Mã nhà cung cấp đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'partner')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data, 'partner', $autoModel, 'nhà cung cấp');
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->PartnerNo = $request->PartnerNo;
        }
        $data->PartnerName = $request->PartnerName;
        $data->PartnerAddress = $request->PartnerAddress;
        $data->FirstName = $request->FirstName;
        $data-> MiddleName  = $request->MiddleName;
        $data->LastName = $request->LastName;
        $data->FullName = $request->FullName;
        $data->PartnerIdNo  = $request->PartnerIdNo;
        $data->BirthDay = $request->BirthDay;
        $data->PartnerIdIssuedDate = $request->PartnerIdIssuedDate;
        $data->ProvinceID = $request->ProvinceID;
        $data->ProvinceName = $request->ProvinceName;
        $data->DistrictID = $request->DistrictID;
        $data->DistrictName = $request->DistrictName;
        $data->CommuneID = $request->CommuneID;
        $data->CommuneName = $request->CommuneName;
        $data->Nationality = $request->Nationality;
        $data->NativeCountry = $request->NativeCountry;
        $data->PermanceAddress = $request->PermanceAddress;
        $data->ResidenceAddress = $request->ResidenceAddress;
        $data->Tel = $request->Tel;
        $data->Email = $request->Email;
        $data->Note = $request->Note;
        $data->AccessType = $request->AccessType;

        if ($data->AccessType !== $request->AccessType) {
            $data->AccessType = $request->AccessType;
        }

        $data->save();

        // partner cate
        if ($request->has('PartnerCate')) {
            $PartnerCate = $request->post('PartnerCate');
            DB::table('partner_cate')->where('PartnerID', $id)->delete();
            $arrInsertCate = [];
            foreach ($PartnerCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'PartnerID' => $id,
                        'CateID' => ($cate['CateID']) ? $cate['CateID'] : null,
                        'CateValue' => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('partner_cate')->insert($arrInsertCate);
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
        $PartnerLink = PartnerLink::where('PartnerID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $PartnerLink;

        return json_encode($json);
    }

    //Link
    public function updateLink(Request $request){
        $LinkArray = $request->post('PartnerLink');
        $id = $request->post('PartnerID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'PartnerID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        PartnerLink::where(['PartnerID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('partner_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getCate(Request $request){
        $partnerCateList = PartnerCateList::where('Inactive', '=', 0)->get();
        $partnerCateValue = PartnerCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['PartnerCateList'] = $partnerCateList;
        $json['data']['PartnerCateValue'] = $partnerCateValue;
        return json_encode($json);
    }

    public function getFile(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $PartnerFile = PartnerFile::where('PartnerID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $PartnerFile;

        return json_encode($json);
    }
    public function getVideo(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $PartnerVideo = PartnerVideo::where('PartnerID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $PartnerVideo;

        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $PartnerID = $request->post('PartnerID');
        $PartnerPer = $request->post('PartnerPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($PartnerPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'PartnerID' => $PartnerID,
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
        PartnerPer::where(['PartnerID' => $PartnerID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('partner_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $PartnerID;
        return json_encode($json);
    }

    //Upload File
    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $PartnerFile = PartnerFile::find($request->LineID);
        }else{
            $PartnerFile = new PartnerFile();
        }
        $PartnerFile->FileName = $request->FileName;
        $PartnerFile->FileNameRoot = $request->FileName;
        $PartnerFile->Description = $request->Description;
        $PartnerFile->FileType = $request->FileType;
        $PartnerFile->FileSize = $request->FileSize;
        $PartnerFile->PartnerID = $id;
        $PartnerFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $PartnerFile->UserModified = Auth::user()->username;
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

                    $PartnerFile->Link = $linkFileAttach;
                    $PartnerFile->DateModifiedRoot = $fileModel->DateModified;
                    $PartnerFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $PartnerFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $PartnerFile;
        return json_encode($json);
    }

    //Delete File
    public function deleteFile(Request $request, $id){
        $dFile = PartnerFile::where(['LineID' => $id])->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }
    //Download File
    public function downloadFile(Request $request, $id){
        $file = FileModel::find($id);
        return response()->download(public_path($file->Link));
    }

    //Download All File
    public function downloadAllFile(Request $request, $id){

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
        $files = PartnerFile::where('PartnerID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $partner = Partner::find($id);
            $zipName = '';
            if ($partner) {
                $zipName = IjString::CreateSlug($partner->PartnerName, '-');
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

    //Upload Video
    public function uploadVideo(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $PartnerVideo = PartnerVideo::find($request->LineID);
        }else{
            $PartnerVideo = new PartnerVideo();
        }
        $PartnerVideo->VideoName = $request->VideoName;
        $PartnerVideo->Description = $request->Description;
        $PartnerVideo->VideoType = $request->VideoType;
        $PartnerVideo->VideoSize = $request->VideoSize;
        $PartnerVideo->PartnerID = $id;
        $PartnerVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $PartnerVideo->UserModified = Auth::user()->username;
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

                    $PartnerVideo->Link = $linkFileAttach;
                    $PartnerVideo->DateModifiedRoot = $fileModel->DateModified;
                    $PartnerVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $PartnerVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $PartnerVideo;
        return json_encode($json);
    }
    //Delete Video
    public function deleteVideo(Request $request, $id){
        $tVideo = PartnerVideo::where(['LineID' => $id])->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }
    //Download Video
    public function downloadVideo(Request $request, $id){
        $file = FileModel::find($id);
        return response()->download(public_path($file->Link));
    }

    public function delete($id){
        $Partner = Partner::find($id);
        $auth = Auth::user();
        if($Partner){
            if ($auth->UserType === 1) {
                $Partner->delete();
                PartnerCate::where('PartnerID', $id)->delete();
                PartnerPer::where('PartnerID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $isPer = PartnerPer::where([
                    'PartnerID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($Partner->UserIDCreated === $auth->UserID)){
                    $Partner->delete();
                    PartnerCate::where('PartnerID', $id)->delete();
                    PartnerPer::where('PartnerID', $id)->delete();

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
            Partner::destroy($idArray);
            PartnerCate::whereIn('PartnerID', $idArray)->delete();
            PartnerPer::whereIn('PartnerID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $partnersPer = PartnerPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('partner_per')
                    ->whereIn('PartnerID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('PartnerID');
            })->get();

            foreach ($partnersPer as $partnerPer) {
                if ($partnerPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                Partner::destroy($idArray);
                PartnerCate::whereIn('PartnerID', $idArray)->delete();
                PartnerPer::whereIn('PartnerID', $idArray)->delete();

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
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = Partner::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
