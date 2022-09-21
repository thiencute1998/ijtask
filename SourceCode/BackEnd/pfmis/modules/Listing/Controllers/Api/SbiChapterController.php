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
use Module\Listing\Models\SbiChapter;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SbiChapterCate;
use Module\Listing\Models\SbiChapterCateList;
use Module\Listing\Models\SbiChapterCateValue;
use Module\Listing\Models\SbiChapterFile;
use Module\Listing\Models\SbiChapterPer;
use Module\Listing\Models\SbiChapterVideo;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\SbiChapterLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class SbiChapterController extends Controller{

    public function list(Request $request){
        $data = SbiChapter::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('sbi_chapter', ['select' => ['SbiChapterNo', 'SbiChapterName'], 'conditions' => []]), 'nhacungcap.xlsx');
    }

    public function create(){
        if(old('SbiChapterNo')){
            $auto = old('SbiChapterNo');
        }else{
            $auto = AutoNo::genNewNumberNo('sbi_chapter');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'SbiChapterNo' => 'required|unique:sbi_chapter'
        ],
            [
                'SbiChapterNo.required' => 'Mã chương không được để trống',
                'SbiChapterNo.unique' => 'Mã chương đã tồn tại',
            ]);

        /*$autoModel = SysAutoNumber::where('NumberKey', 'sbi_chapter')->first();
        AutoNo::storeValidate($validator, $request, 'sbi_chapter', $autoModel, 'chương');*/
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new SbiChapter([
            'SbiChapterNo' => $request->SbiChapterNo,
            'SbiChapterName' => $request->SbiChapterName,
            'Note' => $request->Note,
            'AccessType' => ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated' => Auth::user()->UserID,
            'AuthorizedPerson' => Auth::user()->UserID,
            'Inactive' => $request->Inactive ? $request->Inactive : 0,
        ]);
        $data->save();

        $SbiChapterPer = new SbiChapterPer([
            'SbiChapterID' => $data->SbiChapterID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Create' => 1,
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1
        ]);
        $SbiChapterPer->save();

        // cate list
        if ($request->has('SbiChapterCate')) {
            $SbiChapterCate = $request->post('SbiChapterCate');
            $arrInsertCate = [];
            foreach ($SbiChapterCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'SbiChapterID' => $data->SbiChapterID,
                        'CateID' => $cate['CateID'],
                        'CateValue' => $cate['CateValue']
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('sbi_chapter_cate')->insert($arrInsertCate);
            }
        }

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');

            // sbi_chapter per
            $SbiChapterPerCopy = SbiChapterPer::where('SbiChapterID', $CopyID)->where('EmployeeID', '!=', Auth::user()->EmployeeID)->get();
            $investAssetPerArrInsert = [];
            foreach ($SbiChapterPerCopy as $investAssetPer) {
                $investAssetPerArrInsert[] = [
                    'SbiChapterID' => $data->SbiChapterID,
                    'EmployeeID' => $investAssetPer->EmployeeID,
                    'EmployeeName' => $investAssetPer->EmployeeName,
                    'Access' => $investAssetPer->Access,
                    'AccessField' => $investAssetPer->AccessField,
                    'Create' => $investAssetPer->Create,
                    'Edit' => $investAssetPer->Edit,
                    'EditField' => $investAssetPer->EditField,
                    'Delete' => $investAssetPer->Delete
                ];
            }
            if (count($investAssetPerArrInsert)) {
                DB::table('sbi_chapter_per')->insert($investAssetPerArrInsert);
            }

            // investAsset link
            $SbiChapterLinkCopy = SbiChapterLink::where('SbiChapterID', $CopyID)->get();
            $investAssetLinkArrInsert = [];
            foreach ($SbiChapterLinkCopy as $investAssetLink) {
                $investAssetLinkArrInsert[] = [
                    'SbiChapterID' => $data->SbiChapterID,
                    'LinkID' => $investAssetLink->LinkID,
                    'LinkNo' => $investAssetLink->LinkNo,
                    'LinkName' => $investAssetLink->LinkName,
                    'LinkTable' => $investAssetLink->LinkTable,
                    'LinkTableName' => $investAssetLink->LinkTableName
                ];
            }
            if (count($investAssetLinkArrInsert)) {
                DB::table('sbi_chapter_link')->insert($investAssetLinkArrInsert);
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

        $SbiChapter = SbiChapter::find($id);
        $SbiChapterPerEmployee = SbiChapterPer::where([
            'SbiChapterID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->first();
        $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        if(!$SbiChapterPerEmployee){
            if($SbiChapter->AccessType == 2){
                //Nếu tài liệu là công khai
                $SbiChapterPerEmployee = new SbiChapterPer([
                    'SbiChapterID' => $id,
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
                if($EmployeeLogin->UserID != $SbiChapter->UserIDCreated && $EmployeeLogin->UserID != $SbiChapter->AuthorizedPerson){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!';
                    return json_encode($json);
                }else{
                    $SbiChapterPerEmployee = new SbiChapterPer([
                        'SbiChapterID' => $id,
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
            if($EmployeeLogin->UserID == $SbiChapter->UserIDCreated || $EmployeeLogin->UserID == $SbiChapter->AuthorizedPerson){
                $SbiChapterPerEmployee = new SbiChapterPer([
                    'SbiChapterID' => $id,
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

        if ($SbiChapterPerEmployee) {
            if (strpos(',' . $SbiChapterPerEmployee->AccessField . ',', ',all,')) {
                $SbiChapterPerEmployee->AccessField = 'all';
            }
            if(strpos(',' . $SbiChapterPerEmployee->EditField . ',', ',all,')){
                $SbiChapterPerEmployee->EditField = 'all';
            }
        }

        $SbiChapterPer = SbiChapterPer::where('SbiChapterID', '=', $id)->groupBy('EmployeeID')->get();
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::all();

        $SbiChapterCate = SbiChapterCate::where('SbiChapterID', $id)->get();
        $arrCateID = [];
        foreach ($SbiChapterCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $SbiChapterCateList = SbiChapterCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $SbiChapterCateValue = SbiChapterCateValue::whereIn('CateID', $arrCateID)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $SbiChapter;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['SbiChapterPer'] = $SbiChapterPer;
        $json['SbiChapterPerEmployee'] = $SbiChapterPerEmployee;
        $json['data']['SbiChapterCate'] = $SbiChapterCate;
        $json['data']['SbiChapterCateList'] = $SbiChapterCateList;
        $json['data']['SbiChapterCateValue'] = $SbiChapterCateValue;
        return json_encode($json);
    }

    public function edit($id){
        $data = SbiChapter::find($id);
        if(old('SbiChapterNo')){
            $data->SbiChapterNo = old('SbiChapterNo');
        }else{
            $data->SbiChapterNo = $data->Prefix.$data->SbiChapterNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = SbiChapter::find($id);
        if($data->SbiChapterNo !== $request->SbiChapterNo){
            $validator = Validator::make($request->all(), [
                'SbiChapterNo' => 'required|unique:sbi_chapter'
            ],
                [
                    'SbiChapterNo.required' => 'Mã chương không được để trống',
                    'SbiChapterNo.unique' => 'Mã chương đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'sbi_chapter')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data, 'sbi_chapter', $autoModel, 'chương');
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->SbiChapterNo = $request->SbiChapterNo;
        }
        $data->SbiChapterName = $request->SbiChapterName;
        $data->Note = $request->Note;

        if ($data->AccessType !== $request->AccessType) {
            $data->AccessType = $request->AccessType;
        }

        $data->save();

        // sbi_chapter cate
        if ($request->has('SbiChapterCate')) {
            $SbiChapterCate = $request->post('SbiChapterCate');
            DB::table('sbi_chapter_cate')->where('SbiChapterID', $id)->delete();
            $arrInsertCate = [];
            foreach ($SbiChapterCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'SbiChapterID' => $id,
                        'CateID' => ($cate['CateID']) ? $cate['CateID'] : null,
                        'CateValue' => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('sbi_chapter_cate')->insert($arrInsertCate);
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
        $SbiChapterLink = SbiChapterLink::where('SbiChapterID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $SbiChapterLink;

        return json_encode($json);
    }

    //Link
    public function updateLink(Request $request){
        $LinkArray = $request->post('SbiChapterLink');
        $id = $request->post('SbiChapterID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'SbiChapterID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        SbiChapterLink::where(['SbiChapterID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('sbi_chapter_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getCate(Request $request){
        $investAssetCateList = SbiChapterCateList::where('Inactive', '=', 0)->get();
        $investAssetCateValue = SbiChapterCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['SbiChapterCateList'] = $investAssetCateList;
        $json['data']['SbiChapterCateValue'] = $investAssetCateValue;
        return json_encode($json);
    }

    public function getFile(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $SbiChapterFile = SbiChapterFile::where('SbiChapterID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $SbiChapterFile;

        return json_encode($json);
    }
    public function getVideo(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $SbiChapterVideo = SbiChapterVideo::where('SbiChapterID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $SbiChapterVideo;

        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $SbiChapterID = $request->post('SbiChapterID');
        $SbiChapterPer = $request->post('SbiChapterPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($SbiChapterPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'SbiChapterID' => $SbiChapterID,
                        'EmployeeID' => $value['EmployeeID'],
                        'Access' => ($value['Access']) ? 1 : 0,
                        'AccessField' => $value['AccessField'],
                        'Create' => ($value['Create']) ? 1 : 0,
                        'Edit' => ($value['Edit']) ? 1 : 0,
                        'EditField' => $value['EditField'],
                        'Delete' => ($value['Delete']) ? 1 : 0
                    ];
                    $ArrEmployee[$value['EmployeeID']] = $value['EmployeeID'];
                }
            }
        }
        SbiChapterPer::where(['SbiChapterID' => $SbiChapterID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('sbi_chapter_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $SbiChapterID;
        return json_encode($json);
    }

    //Upload File
    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $SbiChapterFile = SbiChapterFile::find($request->LineID);
        }else{
            $SbiChapterFile = new SbiChapterFile();
        }
        $SbiChapterFile->FileName = $request->FileName;
        $SbiChapterFile->FileNameRoot = $request->FileName;
        $SbiChapterFile->Description = $request->Description;
        $SbiChapterFile->FileType = $request->FileType;
        $SbiChapterFile->FileSize = $request->FileSize;
        $SbiChapterFile->SbiChapterID = $id;
        $SbiChapterFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $SbiChapterFile->UserModified = Auth::user()->username;
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

                    $SbiChapterFile->Link = $linkFileAttach;
                    $SbiChapterFile->DateModifiedRoot = $fileModel->DateModified;
                    $SbiChapterFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $SbiChapterFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $SbiChapterFile;
        return json_encode($json);
    }

    //Delete File
    public function deleteFile(Request $request, $id){
        $dFile = SbiChapterFile::where(['LineID' => $id])->delete();
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
        $files = SbiChapterFile::where('SbiChapterID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $investAsset = SbiChapter::find($id);
            $zipName = '';
            if ($investAsset) {
                $zipName = IjString::CreateSlug($investAsset->SbiChapterName, '-');
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
            $SbiChapterVideo = SbiChapterVideo::find($request->LineID);
        }else{
            $SbiChapterVideo = new SbiChapterVideo();
        }
        $SbiChapterVideo->VideoName = $request->VideoName;
        $SbiChapterVideo->Description = $request->Description;
        $SbiChapterVideo->VideoType = $request->VideoType;
        $SbiChapterVideo->VideoSize = $request->VideoSize;
        $SbiChapterVideo->SbiChapterID = $id;
        $SbiChapterVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $SbiChapterVideo->UserModified = Auth::user()->username;
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

                    $SbiChapterVideo->Link = $linkFileAttach;
                    $SbiChapterVideo->DateModifiedRoot = $fileModel->DateModified;
                    $SbiChapterVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $SbiChapterVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $SbiChapterVideo;
        return json_encode($json);
    }
    //Delete Video
    public function deleteVideo(Request $request, $id){
        $tVideo = SbiChapterVideo::where(['LineID' => $id])->delete();
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

    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = SbiChapter::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }

    public function delete($id){
        $SbiChapter = SbiChapter::find($id);
        $auth = Auth::user();
        if($SbiChapter){
            if ($auth->UserType === 1) {
                $SbiChapter->delete();
                SbiChapterCate::where('SbiChapterID', $id)->delete();
                SbiChapterPer::where('SbiChapterID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $isPer = SbiChapterPer::where([
                    'SbiChapterID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($SbiChapter->UserIDCreated === $auth->UserID)){
                    $SbiChapter->delete();
                    SbiChapterCate::where('SbiChapterID', $id)->delete();
                    SbiChapterPer::where('SbiChapterID', $id)->delete();

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
            SbiChapter::destroy($idArray);
            SbiChapterCate::whereIn('SbiChapterID', $idArray)->delete();
            SbiChapterPer::whereIn('SbiChapterID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $investAssetsPer = SbiChapterPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('sbi_chapter_per')
                    ->whereIn('SbiChapterID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('SbiChapterID');
            })->get();

            foreach ($investAssetsPer as $investAssetPer) {
                if ($investAssetPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                SbiChapter::destroy($idArray);
                SbiChapterCate::whereIn('SbiChapterID', $idArray)->delete();
                SbiChapterPer::whereIn('SbiChapterID', $idArray)->delete();

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
