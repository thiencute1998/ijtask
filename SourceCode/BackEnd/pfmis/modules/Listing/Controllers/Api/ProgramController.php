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
use Module\Listing\Models\Program;
use Module\Listing\Models\Employee;
use Module\Listing\Models\ProgramCate;
use Module\Listing\Models\ProgramCateList;
use Module\Listing\Models\ProgramCateValue;
use Module\Listing\Models\ProgramFile;
use Module\Listing\Models\ProgramPer;
use Module\Listing\Models\ProgramVideo;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\ProgramLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class ProgramController extends Controller{

    public function list(Request $request){
        $data = Program::search($request);
        $ProgramTypeOption = config('dataoptions.Program.ProgramTypeOption');
        $ManagementLevelOption = config('dataoptions.ManagementLevelOption');
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        $json['ProgramTypeOption'] = $ProgramTypeOption;
        $json['ManagementLevelOption'] = $ManagementLevelOption;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('program', ['select' => ['ProgramNo', 'ProgramName'], 'conditions' => []]), 'ctmt.xlsx');
    }

    public function create(){
        if(old('ProgramNo')){
            $auto = old('ProgramNo');
        }else{
            $auto = AutoNo::genNewNumberNo('program');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'ProgramNo' => 'required|unique:program'
        ],
            [
                'ProgramNo.required' => 'Mã chương trình mục tiêu không được để trống',
                'ProgramNo.unique' => 'Mã chương trình mục tiêu đã tồn tại',
            ]);

        $autoModel = SysAutoNumber::where('NumberKey', 'program')->first();
        AutoNo::storeValidate($validator, $request, 'program', $autoModel, 'chương trình mục tiêu');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new Program([
            'ProgramNo' => $request->ProgramNo,
            'ProgramName' => $request->ProgramName,
            'ProgramType'=> $request->ProgramType,
            'ManagementLevel'=> $request->ManagementLevel,
            'Note' => $request->Note,
            'AccessType' => ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated' => Auth::user()->UserID,
            'AuthorizedPerson' => Auth::user()->UserID,
            'Inactive' => $request->Inactive,
        ]);
        $data->save();

        $ProgramPer = new ProgramPer([
            'ProgramID' => $data->ProgramID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $ProgramPer->save();

        // cate list
        if ($request->has('ProgramCate')) {
            $ProgramCate = $request->post('ProgramCate');
            $arrInsertCate = [];
            foreach ($ProgramCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'ProgramID' => $data->ProgramID,
                        'CateID' => $cate['CateID'],
                        'CateValue' => $cate['CateValue']
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('program_cate')->insert($arrInsertCate);
            }
        }

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');

            // program per
            $ProgramPerCopy = ProgramPer::where('ProgramID', $CopyID)->where('EmployeeID', '!=', Auth::user()->EmployeeID)->get();
            $programPerArrInsert = [];
            foreach ($ProgramPerCopy as $programPer) {
                $programPerArrInsert[] = [
                    'ProgramID' => $data->ProgramID,
                    'EmployeeID' => $programPer->EmployeeID,
                    'EmployeeName' => $programPer->EmployeeName,
                    'Access' => $programPer->Access,
                    'AccessField' => $programPer->AccessField,
                    'Edit' => $programPer->Edit,
                    'EditField' => $programPer->EditField,
                    'Delete' => $programPer->Delete,
                    'Create' => $programPer->Create
                ];
            }
            if (count($programPerArrInsert)) {
                DB::table('program_per')->insert($programPerArrInsert);
            }

            // program link
            $ProgramLinkCopy = ProgramLink::where('ProgramID', $CopyID)->get();
            $programLinkArrInsert = [];
            foreach ($ProgramLinkCopy as $programLink) {
                $programLinkArrInsert[] = [
                    'ProgramID' => $data->ProgramID,
                    'LinkID' => $programLink->LinkID,
                    'LinkNo' => $programLink->LinkNo,
                    'LinkName' => $programLink->LinkName,
                    'LinkTable' => $programLink->LinkTable,
                    'LinkTableName' => $programLink->LinkTableName
                ];
            }
            if (count($programLinkArrInsert)) {
                DB::table('program_link')->insert($programLinkArrInsert);
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->ProgramID;

        return json_encode($json);
    }

    public function view($id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $Program = Program::find($id);
        $ProgramPerEmployee = ProgramPer::where([
            'ProgramID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->first();
        $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        if(!$ProgramPerEmployee){
            if($Program->AccessType == 2){
                //Nếu tài liệu là công khai
                $ProgramPerEmployee = new ProgramPer([
                    'ProgramID' => $id,
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
                if($EmployeeLogin->UserID != $Program->UserIDCreated && $EmployeeLogin->UserID != $Program->AuthorizedPerson){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!';
                    return json_encode($json);
                }else{
                    $ProgramPerEmployee = new ProgramPer([
                        'ProgramID' => $id,
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
            if($EmployeeLogin->UserID == $Program->UserIDCreated || $EmployeeLogin->UserID == $Program->AuthorizedPerson){
                $ProgramPerEmployee = new ProgramPer([
                    'ProgramID' => $id,
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

        if ($ProgramPerEmployee) {
            if (strpos(',' . $ProgramPerEmployee->AccessField . ',', ',all,')) {
                $ProgramPerEmployee->AccessField = 'all';
            }
            if(strpos(',' . $ProgramPerEmployee->EditField . ',', ',all,')){
                $ProgramPerEmployee->EditField = 'all';
            }
        }

        $ProgramPer = ProgramPer::where('ProgramID', '=', $id)->groupBy('EmployeeID')->get();
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::all();

        $ProgramCate = ProgramCate::where('ProgramID', $id)->get();
        $arrCateID = [];
        foreach ($ProgramCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $ProgramCateList = ProgramCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $ProgramCateValue = ProgramCateValue::whereIn('CateID', $arrCateID)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $Program;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['ProgramPer'] = $ProgramPer;
        $json['ProgramPerEmployee'] = $ProgramPerEmployee;
        $json['data']['ProgramCate'] = $ProgramCate;
        $json['data']['ProgramCateList'] = $ProgramCateList;
        $json['data']['ProgramCateValue'] = $ProgramCateValue;
        return json_encode($json);
    }

    public function edit($id){
        $data = Program::find($id);
        if(old('ProgramNo')){
            $data->ProgramNo = old('ProgramNo');
        }else{
            $data->ProgramNo = $data->Prefix.$data->ProgramNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = Program::find($id);
        if($data->ProgramNo !== $request->ProgramNo){
            $validator = Validator::make($request->all(), [
                'ProgramNo' => 'required|unique:program'
            ],
                [
                    'ProgramNo.required' => 'Mã chương trình mục tiêu không được để trống',
                    'ProgramNo.unique' => 'Mã chương trình mục tiêu đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'program')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data, 'program', $autoModel, 'chương trình mục tiêu');
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->ProgramNo = $request->ProgramNo;
        }
        $data->ProgramName = $request->ProgramName;
        $data->ProgramType = $request->ProgramType;
        $data->ManagementLevel = $request->ManagementLevel;
        $data->Note = $request->Note;

        if ($data->AccessType !== $request->AccessType) {
            $data->AccessType = $request->AccessType;
        }

        $data->save();

        // program cate
        if ($request->has('ProgramCate')) {
            $ProgramCate = $request->post('ProgramCate');
            DB::table('program_cate')->where('ProgramID', $id)->delete();
            $arrInsertCate = [];
            foreach ($ProgramCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'ProgramID' => $id,
                        'CateID' => ($cate['CateID']) ? $cate['CateID'] : null,
                        'CateValue' => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('program_cate')->insert($arrInsertCate);
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
        $ProgramLink = ProgramLink::where('ProgramID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $ProgramLink;

        return json_encode($json);
    }

    //Link
    public function updateLink(Request $request){
        $LinkArray = $request->post('ProgramLink');
        $id = $request->post('ProgramID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'ProgramID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        ProgramLink::where(['ProgramID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('program_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getCate(Request $request){
        $programCateList = ProgramCateList::where('Inactive', '=', 0)->get();
        $programCateValue = ProgramCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['ProgramCateList'] = $programCateList;
        $json['data']['ProgramCateValue'] = $programCateValue;
        return json_encode($json);
    }

    public function getFile(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $ProgramFile = ProgramFile::where('ProgramID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $ProgramFile;

        return json_encode($json);
    }
    public function getVideo(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $ProgramVideo = ProgramVideo::where('ProgramID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $ProgramVideo;

        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $ProgramID = $request->post('ProgramID');
        $ProgramPer = $request->post('ProgramPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($ProgramPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'ProgramID' => $ProgramID,
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
        ProgramPer::where(['ProgramID' => $ProgramID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('program_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ProgramID;
        return json_encode($json);
    }

    //Upload File
    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $ProgramFile = ProgramFile::find($request->LineID);
        }else{
            $ProgramFile = new ProgramFile();
        }
        $ProgramFile->FileName = $request->FileName;
        $ProgramFile->FileNameRoot = $request->FileName;
        $ProgramFile->Description = $request->Description;
        $ProgramFile->FileType = $request->FileType;
        $ProgramFile->FileSize = $request->FileSize;
        $ProgramFile->ProgramID = $id;
        $ProgramFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $ProgramFile->UserModified = Auth::user()->username;
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

                    $ProgramFile->Link = $linkFileAttach;
                    $ProgramFile->DateModifiedRoot = $fileModel->DateModified;
                    $ProgramFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $ProgramFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ProgramFile;
        return json_encode($json);
    }

    //Delete File
    public function deleteFile(Request $request, $id){
        $dFile = ProgramFile::where(['LineID' => $id])->delete();
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
        $files = ProgramFile::where('ProgramID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $program = Program::find($id);
            $zipName = '';
            if ($program) {
                $zipName = IjString::CreateSlug($program->ProgramName, '-');
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
            $ProgramVideo = ProgramVideo::find($request->LineID);
        }else{
            $ProgramVideo = new ProgramVideo();
        }
        $ProgramVideo->VideoName = $request->VideoName;
        $ProgramVideo->Description = $request->Description;
        $ProgramVideo->VideoType = $request->VideoType;
        $ProgramVideo->VideoSize = $request->VideoSize;
        $ProgramVideo->ProgramID = $id;
        $ProgramVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $ProgramVideo->UserModified = Auth::user()->username;
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

                    $ProgramVideo->Link = $linkFileAttach;
                    $ProgramVideo->DateModifiedRoot = $fileModel->DateModified;
                    $ProgramVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $ProgramVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ProgramVideo;
        return json_encode($json);
    }
    //Delete Video
    public function deleteVideo(Request $request, $id){
        $tVideo = ProgramVideo::where(['LineID' => $id])->delete();
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
        $Program = Program::find($id);
        $auth = Auth::user();
        if($Program){
            if ($auth->UserType === 1) {
                $Program->delete();
                ProgramCate::where('ProgramID', $id)->delete();
                ProgramPer::where('ProgramID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $isPer = ProgramPer::where([
                    'ProgramID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($Program->UserIDCreated === $auth->UserID)){
                    $Program->delete();
                    ProgramCate::where('ProgramID', $id)->delete();
                    ProgramPer::where('ProgramID', $id)->delete();

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
            Program::destroy($idArray);
            ProgramCate::whereIn('ProgramID', $idArray)->delete();
            ProgramPer::whereIn('ProgramID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $programsPer = ProgramPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('program_per')
                    ->whereIn('ProgramID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('ProgramID');
            })->get();

            foreach ($programsPer as $programPer) {
                if ($programPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                Program::destroy($idArray);
                ProgramCate::whereIn('ProgramID', $idArray)->delete();
                ProgramPer::whereIn('ProgramID', $idArray)->delete();

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
        $data = Program::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
