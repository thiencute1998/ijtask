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
use Module\Listing\Models\Tool;
use Module\Listing\Models\Employee;
use Module\Listing\Models\ToolCate;
use Module\Listing\Models\ToolCateList;
use Module\Listing\Models\ToolCateValue;
use Module\Listing\Models\ToolFile;
use Module\Listing\Models\ToolPer;
use Module\Listing\Models\ToolVideo;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\ToolLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class ToolController extends Controller{

    public function list(Request $request){
        $data = Tool::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('tool', ['select' => ['ToolNo', 'ToolName'], 'conditions' => []]), 'nhacungcap.xlsx');
    }

    public function create(){
        if(old('ToolNo')){
            $auto = old('ToolNo');
        }else{
            $auto = AutoNo::genNewNumberNo('tool');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'ToolNo' => 'required|unique:tool'
        ],
            [
                'ToolNo.required' => 'Mã công cụ dụng cụ không được để trống',
                'ToolNo.unique' => 'Mã công cụ dụng cụ đã tồn tại',
            ]);

        /*$autoModel = SysAutoNumber::where('NumberKey', 'tool')->first();
        AutoNo::storeValidate($validator, $request, 'tool', $autoModel, 'công cụ dụng cụ');*/
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new Tool([
            'ToolNo' => $request->ToolNo,
            'ToolName' => $request->ToolName,
            'UomID' => $request->UomID,
            'UomName' => $request->UomName,
            'AccessType' => ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated' => Auth::user()->UserID,
            'AuthorizedPerson' => Auth::user()->UserID,
            'Inactive' => $request->Inactive ? $request->Inactive : 0,
        ]);
        $data->save();

        $ToolPer = new ToolPer([
            'ToolID' => $data->ToolID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Create' => 1,
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1
        ]);
        $ToolPer->save();

        // cate list
        if ($request->has('ToolCate')) {
            $ToolCate = $request->post('ToolCate');
            $arrInsertCate = [];
            foreach ($ToolCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'ToolID' => $data->ToolID,
                        'CateID' => $cate['CateID'],
                        'CateValue' => $cate['CateValue']
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('tool_cate')->insert($arrInsertCate);
            }
        }

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');

            // tool per
            $ToolPerCopy = ToolPer::where('ToolID', $CopyID)->where('EmployeeID', '!=', Auth::user()->EmployeeID)->get();
            $investAssetPerArrInsert = [];
            foreach ($ToolPerCopy as $investAssetPer) {
                $investAssetPerArrInsert[] = [
                    'ToolID' => $data->ToolID,
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
                DB::table('tool_per')->insert($investAssetPerArrInsert);
            }

            // investAsset link
            $ToolLinkCopy = ToolLink::where('ToolID', $CopyID)->get();
            $investAssetLinkArrInsert = [];
            foreach ($ToolLinkCopy as $investAssetLink) {
                $investAssetLinkArrInsert[] = [
                    'ToolID' => $data->ToolID,
                    'LinkID' => $investAssetLink->LinkID,
                    'LinkNo' => $investAssetLink->LinkNo,
                    'LinkName' => $investAssetLink->LinkName,
                    'LinkTable' => $investAssetLink->LinkTable,
                    'LinkTableName' => $investAssetLink->LinkTableName
                ];
            }
            if (count($investAssetLinkArrInsert)) {
                DB::table('tool_link')->insert($investAssetLinkArrInsert);
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

        $Tool = Tool::find($id);
        $ToolPerEmployee = ToolPer::where([
            'ToolID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->first();
        $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        if(!$ToolPerEmployee){
            if($Tool->AccessType == 2){
                //Nếu tài liệu là công khai
                $ToolPerEmployee = new ToolPer([
                    'ToolID' => $id,
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
                if($EmployeeLogin->UserID != $Tool->UserIDCreated && $EmployeeLogin->UserID != $Tool->AuthorizedPerson){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!';
                    return json_encode($json);
                }else{
                    $ToolPerEmployee = new ToolPer([
                        'ToolID' => $id,
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
            if($EmployeeLogin->UserID == $Tool->UserIDCreated || $EmployeeLogin->UserID == $Tool->AuthorizedPerson){
                $ToolPerEmployee = new ToolPer([
                    'ToolID' => $id,
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

        if ($ToolPerEmployee) {
            if (strpos(',' . $ToolPerEmployee->AccessField . ',', ',all,')) {
                $ToolPerEmployee->AccessField = 'all';
            }
            if(strpos(',' . $ToolPerEmployee->EditField . ',', ',all,')){
                $ToolPerEmployee->EditField = 'all';
            }
        }

        $ToolPer = ToolPer::where('ToolID', '=', $id)->groupBy('EmployeeID')->get();
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::all();

        $ToolCate = ToolCate::where('ToolID', $id)->get();
        $arrCateID = [];
        foreach ($ToolCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $ToolCateList = ToolCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $ToolCateValue = ToolCateValue::whereIn('CateID', $arrCateID)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $Tool;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['ToolPer'] = $ToolPer;
        $json['ToolPerEmployee'] = $ToolPerEmployee;
        $json['data']['ToolCate'] = $ToolCate;
        $json['data']['ToolCateList'] = $ToolCateList;
        $json['data']['ToolCateValue'] = $ToolCateValue;
        return json_encode($json);
    }

    public function edit($id){
        $data = Tool::find($id);
        if(old('ToolNo')){
            $data->ToolNo = old('ToolNo');
        }else{
            $data->ToolNo = $data->Prefix.$data->ToolNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = Tool::find($id);
        if($data->ToolNo !== $request->ToolNo){
            $validator = Validator::make($request->all(), [
                'ToolNo' => 'required|unique:tool'
            ],
                [
                    'ToolNo.required' => 'Mã công cụ dụng cụ không được để trống',
                    'ToolNo.unique' => 'Mã công cụ dụng cụ đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'tool')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data, 'tool', $autoModel, 'công cụ dụng cụ');
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->ToolNo = $request->ToolNo;
        }
        $data->ToolName = $request->ToolName;
        $data->UomID = $request->UomID;
        $data->UomName = $request->UomName;

        if ($data->AccessType !== $request->AccessType) {
            $data->AccessType = $request->AccessType;
        }

        $data->save();

        // tool cate
        if ($request->has('ToolCate')) {
            $ToolCate = $request->post('ToolCate');
            DB::table('tool_cate')->where('ToolID', $id)->delete();
            $arrInsertCate = [];
            foreach ($ToolCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'ToolID' => $id,
                        'CateID' => ($cate['CateID']) ? $cate['CateID'] : null,
                        'CateValue' => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('tool_cate')->insert($arrInsertCate);
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
        $ToolLink = ToolLink::where('ToolID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $ToolLink;

        return json_encode($json);
    }

    //Link
    public function updateLink(Request $request){
        $LinkArray = $request->post('ToolLink');
        $id = $request->post('ToolID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'ToolID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        ToolLink::where(['ToolID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('tool_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getCate(Request $request){
        $investAssetCateList = ToolCateList::where('Inactive', '=', 0)->get();
        $investAssetCateValue = ToolCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['ToolCateList'] = $investAssetCateList;
        $json['data']['ToolCateValue'] = $investAssetCateValue;
        return json_encode($json);
    }

    public function getFile(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $ToolFile = ToolFile::where('ToolID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $ToolFile;

        return json_encode($json);
    }
    public function getVideo(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $ToolVideo = ToolVideo::where('ToolID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $ToolVideo;

        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $ToolID = $request->post('ToolID');
        $ToolPer = $request->post('ToolPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($ToolPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'ToolID' => $ToolID,
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
        ToolPer::where(['ToolID' => $ToolID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('tool_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ToolID;
        return json_encode($json);
    }

    //Upload File
    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $ToolFile = ToolFile::find($request->LineID);
        }else{
            $ToolFile = new ToolFile();
        }
        $ToolFile->FileName = $request->FileName;
        $ToolFile->FileNameRoot = $request->FileName;
        $ToolFile->Description = $request->Description;
        $ToolFile->FileType = $request->FileType;
        $ToolFile->FileSize = $request->FileSize;
        $ToolFile->ToolID = $id;
        $ToolFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $ToolFile->UserModified = Auth::user()->username;
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

                    $ToolFile->Link = $linkFileAttach;
                    $ToolFile->DateModifiedRoot = $fileModel->DateModified;
                    $ToolFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $ToolFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ToolFile;
        return json_encode($json);
    }

    //Delete File
    public function deleteFile(Request $request, $id){
        $dFile = ToolFile::where(['LineID' => $id])->delete();
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
        $files = ToolFile::where('ToolID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $investAsset = Tool::find($id);
            $zipName = '';
            if ($investAsset) {
                $zipName = IjString::CreateSlug($investAsset->ToolName, '-');
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
            $ToolVideo = ToolVideo::find($request->LineID);
        }else{
            $ToolVideo = new ToolVideo();
        }
        $ToolVideo->VideoName = $request->VideoName;
        $ToolVideo->Description = $request->Description;
        $ToolVideo->VideoType = $request->VideoType;
        $ToolVideo->VideoSize = $request->VideoSize;
        $ToolVideo->ToolID = $id;
        $ToolVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $ToolVideo->UserModified = Auth::user()->username;
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

                    $ToolVideo->Link = $linkFileAttach;
                    $ToolVideo->DateModifiedRoot = $fileModel->DateModified;
                    $ToolVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $ToolVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ToolVideo;
        return json_encode($json);
    }
    //Delete Video
    public function deleteVideo(Request $request, $id){
        $tVideo = ToolVideo::where(['LineID' => $id])->delete();
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
        $data = Tool::search($request);
//        $Tool = Tool::select('ToolID', 'ToolName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function delete($id){
        $Tool = Tool::find($id);
        $auth = Auth::user();
        if($Tool){
            if ($auth->UserType === 1) {
                $Tool->delete();
                ToolCate::where('ToolID', $id)->delete();
                ToolPer::where('ToolID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $isPer = ToolPer::where([
                    'ToolID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($Tool->UserIDCreated === $auth->UserID)){
                    $Tool->delete();
                    ToolCate::where('ToolID', $id)->delete();
                    ToolPer::where('ToolID', $id)->delete();

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
            Tool::destroy($idArray);
            ToolCate::whereIn('ToolID', $idArray)->delete();
            ToolPer::whereIn('ToolID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $investAssetsPer = ToolPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('tool_per')
                    ->whereIn('ToolID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('ToolID');
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
                Tool::destroy($idArray);
                ToolCate::whereIn('ToolID', $idArray)->delete();
                ToolPer::whereIn('ToolID', $idArray)->delete();

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

    public function getUom(Request $request)
    {
        $UomName = $request->post('UomName');
        $uomQuery = DB::table('uom');
        if ($UomName) {
            $uomQuery->where('UomName', 'like', '%' . $UomName . '%');
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $uom = $uomQuery->paginate($per_page);

        if ($uom) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $uom;
            return json_encode($json);
        }
        $json['status'] = 0;
        $json['msg'] = '';
        return json_encode($json);
    }
}
