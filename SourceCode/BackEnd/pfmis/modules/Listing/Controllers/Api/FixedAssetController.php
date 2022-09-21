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
use Module\Listing\Models\FixedAsset;
use Module\Listing\Models\Employee;
use Module\Listing\Models\FixedAssetCate;
use Module\Listing\Models\FixedAssetCateList;
use Module\Listing\Models\FixedAssetCateValue;
use Module\Listing\Models\FixedAssetFile;
use Module\Listing\Models\FixedAssetPer;
use Module\Listing\Models\FixedAssetVideo;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\FixedAssetLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class FixedAssetController extends Controller{

    public function list(Request $request){
        $data = FixedAsset::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('fixed_asset', ['select' => ['FixedAssetNo', 'FixedAssetName'], 'conditions' => []]), 'nhacungcap.xlsx');
    }

    public function create(){
        if(old('FixedAssetNo')){
            $auto = old('FixedAssetNo');
        }else{
            $auto = AutoNo::genNewNumberNo('fixed_asset');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'FixedAssetNo' => 'required|unique:fixed_asset',
            'Serialnumber' => 'required|unique:fixed_asset',
        ],
            [
                'FixedAssetNo.required' => 'Mã tài sản cố định không được để trống',
                'FixedAssetNo.unique' => 'Mã tài sản cố định đã tồn tại',
                'Serialnumber.required' => 'Số hiệu sản phẩm không được để trống',
                'Serialnumber.unique' => 'Số hiệu sản phẩm đã tồn tại',
            ]);

        /*$autoModel = SysAutoNumber::where('NumberKey', 'fixed_asset')->first();
        AutoNo::storeValidate($validator, $request, 'fixed_asset', $autoModel, 'tài sản cố định');*/
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new FixedAsset([
            'FixedAssetNo' => $request->FixedAssetNo,
            'FixedAssetName' => $request->FixedAssetName,
            'UomID' => $request->UomID,
            'UomName' => $request->UomName,
            'AccessType' => ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated' => Auth::user()->UserID,
            'AuthorizedPerson' => Auth::user()->UserID,
            'Inactive' => $request->Inactive ? $request->Inactive : 0,
            'Serialnumber' => $request->Serialnumber
        ]);
        $data->save();

        $FixedAssetPer = new FixedAssetPer([
            'FixedAssetID' => $data->FixedAssetID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Create' => 1,
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1
        ]);
        $FixedAssetPer->save();

        // cate list
        if ($request->has('FixedAssetCate')) {
            $FixedAssetCate = $request->post('FixedAssetCate');
            $arrInsertCate = [];
            foreach ($FixedAssetCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'FixedAssetID' => $data->FixedAssetID,
                        'CateID' => $cate['CateID'],
                        'CateValue' => $cate['CateValue']
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('fixed_asset_cate')->insert($arrInsertCate);
            }
        }

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');

            // fixed_asset per
            $FixedAssetPerCopy = FixedAssetPer::where('FixedAssetID', $CopyID)->where('EmployeeID', '!=', Auth::user()->EmployeeID)->get();
            $fixedAssetPerArrInsert = [];
            foreach ($FixedAssetPerCopy as $fixedAssetPer) {
                $fixedAssetPerArrInsert[] = [
                    'FixedAssetID' => $data->FixedAssetID,
                    'EmployeeID' => $fixedAssetPer->EmployeeID,
                    'EmployeeName' => $fixedAssetPer->EmployeeName,
                    'Access' => $fixedAssetPer->Access,
                    'AccessField' => $fixedAssetPer->AccessField,
                    'Create' => $fixedAssetPer->Create,
                    'Edit' => $fixedAssetPer->Edit,
                    'EditField' => $fixedAssetPer->EditField,
                    'Delete' => $fixedAssetPer->Delete
                ];
            }
            if (count($fixedAssetPerArrInsert)) {
                DB::table('fixed_asset_per')->insert($fixedAssetPerArrInsert);
            }

            // fixedAsset link
            $FixedAssetLinkCopy = FixedAssetLink::where('FixedAssetID', $CopyID)->get();
            $fixedAssetLinkArrInsert = [];
            foreach ($FixedAssetLinkCopy as $fixedAssetLink) {
                $fixedAssetLinkArrInsert[] = [
                    'FixedAssetID' => $data->FixedAssetID,
                    'LinkID' => $fixedAssetLink->LinkID,
                    'LinkNo' => $fixedAssetLink->LinkNo,
                    'LinkName' => $fixedAssetLink->LinkName,
                    'LinkTable' => $fixedAssetLink->LinkTable,
                    'LinkTableName' => $fixedAssetLink->LinkTableName
                ];
            }
            if (count($fixedAssetLinkArrInsert)) {
                DB::table('fixed_asset_link')->insert($fixedAssetLinkArrInsert);
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

        $FixedAsset = FixedAsset::find($id);
        $FixedAssetPerEmployee = FixedAssetPer::where([
            'FixedAssetID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->first();
        $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        if(!$FixedAssetPerEmployee){
            if($FixedAsset->AccessType == 2){
                //Nếu tài liệu là công khai
                $FixedAssetPerEmployee = new FixedAssetPer([
                    'FixedAssetID' => $id,
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
                if($EmployeeLogin->UserID != $FixedAsset->UserIDCreated && $EmployeeLogin->UserID != $FixedAsset->AuthorizedPerson){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!';
                    return json_encode($json);
                }else{
                    $FixedAssetPerEmployee = new FixedAssetPer([
                        'FixedAssetID' => $id,
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
            if($EmployeeLogin->UserID == $FixedAsset->UserIDCreated || $EmployeeLogin->UserID == $FixedAsset->AuthorizedPerson){
                $FixedAssetPerEmployee = new FixedAssetPer([
                    'FixedAssetID' => $id,
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

        if ($FixedAssetPerEmployee) {
            if (strpos(',' . $FixedAssetPerEmployee->AccessField . ',', ',all,')) {
                $FixedAssetPerEmployee->AccessField = 'all';
            }
            if(strpos(',' . $FixedAssetPerEmployee->EditField . ',', ',all,')){
                $FixedAssetPerEmployee->EditField = 'all';
            }
        }

        $FixedAssetPer = FixedAssetPer::where('FixedAssetID', '=', $id)->groupBy('EmployeeID')->get();
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::all();

        $FixedAssetCate = FixedAssetCate::where('FixedAssetID', $id)->get();
        $arrCateID = [];
        foreach ($FixedAssetCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $FixedAssetCateList = FixedAssetCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $FixedAssetCateValue = FixedAssetCateValue::whereIn('CateID', $arrCateID)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $FixedAsset;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['FixedAssetPer'] = $FixedAssetPer;
        $json['FixedAssetPerEmployee'] = $FixedAssetPerEmployee;
        $json['data']['FixedAssetCate'] = $FixedAssetCate;
        $json['data']['FixedAssetCateList'] = $FixedAssetCateList;
        $json['data']['FixedAssetCateValue'] = $FixedAssetCateValue;
        return json_encode($json);
    }

    public function edit($id){
        $data = FixedAsset::find($id);
        if(old('FixedAssetNo')){
            $data->FixedAssetNo = old('FixedAssetNo');
        }else{
            $data->FixedAssetNo = $data->Prefix.$data->FixedAssetNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = FixedAsset::find($id);
        if($data->InvestAssetNo !== $request->InvestAssetNo) {
            $validator = Validator::make($request->all(), [
                'FixedAssetNo' => 'required|unique:fixed_asset'
            ],
                [
                    'FixedAssetNo.required' => 'Mã tài sản cố định không được để trống',
                    'FixedAssetNo.unique' => 'Mã tài sản cố định đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'fixed_asset')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data, 'fixed_asset', $autoModel, 'tài sản cố định');
            if ($validator->fails()) {
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }

            $data->FixedAssetNo = $request->FixedAssetNo;
        }
        $data->FixedAssetName = $request->FixedAssetName;
        $data->UomID = $request->UomID;
        $data->UomName = $request->UomName;
        $data->Serialnumber = $request->Serialnumber;

        if ($data->AccessType !== $request->AccessType) {
            $data->AccessType = $request->AccessType;
        }

        $data->save();

        // fixed_asset cate
        if ($request->has('FixedAssetCate')) {
            $FixedAssetCate = $request->post('FixedAssetCate');
            DB::table('fixed_asset_cate')->where('FixedAssetID', $id)->delete();
            $arrInsertCate = [];
            foreach ($FixedAssetCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'FixedAssetID' => $id,
                        'CateID' => ($cate['CateID']) ? $cate['CateID'] : null,
                        'CateValue' => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('fixed_asset_cate')->insert($arrInsertCate);
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
        $FixedAssetLink = FixedAssetLink::where('FixedAssetID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $FixedAssetLink;

        return json_encode($json);
    }

    //Link
    public function updateLink(Request $request){
        $LinkArray = $request->post('FixedAssetLink');
        $id = $request->post('FixedAssetID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'FixedAssetID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        FixedAssetLink::where(['FixedAssetID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('fixed_asset_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getCate(Request $request){
        $fixedAssetCateList = FixedAssetCateList::where('Inactive', '=', 0)->get();
        $fixedAssetCateValue = FixedAssetCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['FixedAssetCateList'] = $fixedAssetCateList;
        $json['data']['FixedAssetCateValue'] = $fixedAssetCateValue;
        return json_encode($json);
    }

    public function getFile(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $FixedAssetFile = FixedAssetFile::where('FixedAssetID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $FixedAssetFile;

        return json_encode($json);
    }
    public function getVideo(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $FixedAssetVideo = FixedAssetVideo::where('FixedAssetID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $FixedAssetVideo;

        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $FixedAssetID = $request->post('FixedAssetID');
        $FixedAssetPer = $request->post('FixedAssetPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($FixedAssetPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'FixedAssetID' => $FixedAssetID,
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
        FixedAssetPer::where(['FixedAssetID' => $FixedAssetID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('fixed_asset_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $FixedAssetID;
        return json_encode($json);
    }

    //Upload File
    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $FixedAssetFile = FixedAssetFile::find($request->LineID);
        }else{
            $FixedAssetFile = new FixedAssetFile();
        }
        $FixedAssetFile->FileName = $request->FileName;
        $FixedAssetFile->FileNameRoot = $request->FileName;
        $FixedAssetFile->Description = $request->Description;
        $FixedAssetFile->FileType = $request->FileType;
        $FixedAssetFile->FileSize = $request->FileSize;
        $FixedAssetFile->FixedAssetID = $id;
        $FixedAssetFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $FixedAssetFile->UserModified = Auth::user()->username;
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

                    $FixedAssetFile->Link = $linkFileAttach;
                    $FixedAssetFile->DateModifiedRoot = $fileModel->DateModified;
                    $FixedAssetFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $FixedAssetFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $FixedAssetFile;
        return json_encode($json);
    }

    //Delete File
    public function deleteFile(Request $request, $id){
        $dFile = FixedAssetFile::where(['LineID' => $id])->delete();
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
        $files = FixedAssetFile::where('FixedAssetID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $fixedAsset = FixedAsset::find($id);
            $zipName = '';
            if ($fixedAsset) {
                $zipName = IjString::CreateSlug($fixedAsset->FixedAssetName, '-');
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
            $FixedAssetVideo = FixedAssetVideo::find($request->LineID);
        }else{
            $FixedAssetVideo = new FixedAssetVideo();
        }
        $FixedAssetVideo->VideoName = $request->VideoName;
        $FixedAssetVideo->Description = $request->Description;
        $FixedAssetVideo->VideoType = $request->VideoType;
        $FixedAssetVideo->VideoSize = $request->VideoSize;
        $FixedAssetVideo->FixedAssetID = $id;
        $FixedAssetVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $FixedAssetVideo->UserModified = Auth::user()->username;
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

                    $FixedAssetVideo->Link = $linkFileAttach;
                    $FixedAssetVideo->DateModifiedRoot = $fileModel->DateModified;
                    $FixedAssetVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $FixedAssetVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $FixedAssetVideo;
        return json_encode($json);
    }
    //Delete Video
    public function deleteVideo(Request $request, $id){
        $tVideo = FixedAssetVideo::where(['LineID' => $id])->delete();
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
        $data = FixedAsset::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }


    public function delete($id){
        $FixedAsset = FixedAsset::find($id);
        $auth = Auth::user();
        if($FixedAsset){
            if ($auth->UserType === 1) {
                $FixedAsset->delete();
                FixedAssetCate::where('FixedAssetID', $id)->delete();
                FixedAssetPer::where('FixedAssetID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $isPer = FixedAssetPer::where([
                    'FixedAssetID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($FixedAsset->UserIDCreated === $auth->UserID)){
                    $FixedAsset->delete();
                    FixedAssetCate::where('FixedAssetID', $id)->delete();
                    FixedAssetPer::where('FixedAssetID', $id)->delete();

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
            FixedAsset::destroy($idArray);
            FixedAssetCate::whereIn('FixedAssetID', $idArray)->delete();
            FixedAssetPer::whereIn('FixedAssetID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $fixedAssetsPer = FixedAssetPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('fixed_asset_per')
                    ->whereIn('FixedAssetID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('FixedAssetID');
            })->get();

            foreach ($fixedAssetsPer as $fixedAssetPer) {
                if ($fixedAssetPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                FixedAsset::destroy($idArray);
                FixedAssetCate::whereIn('FixedAssetID', $idArray)->delete();
                FixedAssetPer::whereIn('FixedAssetID', $idArray)->delete();

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
