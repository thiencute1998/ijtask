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
use Module\Listing\Models\InvestAsset;
use Module\Listing\Models\Employee;
use Module\Listing\Models\InvestAssetCate;
use Module\Listing\Models\InvestAssetCateList;
use Module\Listing\Models\InvestAssetCateValue;
use Module\Listing\Models\InvestAssetFile;
use Module\Listing\Models\InvestAssetPer;
use Module\Listing\Models\InvestAssetVideo;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\InvestAssetLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class InvestAssetController extends Controller{

    public function list(Request $request){
        $data = InvestAsset::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('invest_asset', ['select' => ['InvestAssetNo', 'InvestAssetName'], 'conditions' => []]), 'nhacungcap.xlsx');
    }

    public function create(){
        if(old('InvestAssetNo')){
            $auto = old('InvestAssetNo');
        }else{
            $auto = AutoNo::genNewNumberNo('invest_asset');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'InvestAssetNo' => 'required|unique:invest_asset'
        ],
            [
                'InvestAssetNo.required' => 'Mã tài sản đầu tư không được để trống',
                'InvestAssetNo.unique' => 'Mã tài sản đầu tư đã tồn tại',
            ]);

        /*$autoModel = SysAutoNumber::where('NumberKey', 'invest_asset')->first();
        AutoNo::storeValidate($validator, $request, 'invest_asset', $autoModel, 'tài sản đầu tư');*/
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new InvestAsset([
            'InvestAssetNo' => $request->InvestAssetNo,
            'InvestAssetName' => $request->InvestAssetName,
            'UomID' => $request->UomID,
            'UomName' => $request->UomName,
            'AccessType' => ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated' => Auth::user()->UserID,
            'AuthorizedPerson' => Auth::user()->UserID,
            'Inactive' => $request->Inactive ? $request->Inactive : 0,
        ]);
        $data->save();

        $InvestAssetPer = new InvestAssetPer([
            'InvestAssetID' => $data->InvestAssetID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Create' => 1,
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1
        ]);
        $InvestAssetPer->save();

        // cate list
        if ($request->has('InvestAssetCate')) {
            $InvestAssetCate = $request->post('InvestAssetCate');
            $arrInsertCate = [];
            foreach ($InvestAssetCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'InvestAssetID' => $data->InvestAssetID,
                        'CateID' => $cate['CateID'],
                        'CateValue' => $cate['CateValue']
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('invest_asset_cate')->insert($arrInsertCate);
            }
        }

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');

            // invest_asset per
            $InvestAssetPerCopy = InvestAssetPer::where('InvestAssetID', $CopyID)->where('EmployeeID', '!=', Auth::user()->EmployeeID)->get();
            $investAssetPerArrInsert = [];
            foreach ($InvestAssetPerCopy as $investAssetPer) {
                $investAssetPerArrInsert[] = [
                    'InvestAssetID' => $data->InvestAssetID,
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
                DB::table('invest_asset_per')->insert($investAssetPerArrInsert);
            }

            // investAsset link
            $InvestAssetLinkCopy = InvestAssetLink::where('InvestAssetID', $CopyID)->get();
            $investAssetLinkArrInsert = [];
            foreach ($InvestAssetLinkCopy as $investAssetLink) {
                $investAssetLinkArrInsert[] = [
                    'InvestAssetID' => $data->InvestAssetID,
                    'LinkID' => $investAssetLink->LinkID,
                    'LinkNo' => $investAssetLink->LinkNo,
                    'LinkName' => $investAssetLink->LinkName,
                    'LinkTable' => $investAssetLink->LinkTable,
                    'LinkTableName' => $investAssetLink->LinkTableName
                ];
            }
            if (count($investAssetLinkArrInsert)) {
                DB::table('invest_asset_link')->insert($investAssetLinkArrInsert);
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

        $InvestAsset = InvestAsset::find($id);
        $InvestAssetPerEmployee = InvestAssetPer::where([
            'InvestAssetID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->first();
        $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        if(!$InvestAssetPerEmployee){
            if($InvestAsset->AccessType == 2){
                //Nếu tài liệu là công khai
                $InvestAssetPerEmployee = new InvestAssetPer([
                    'InvestAssetID' => $id,
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
                if($EmployeeLogin->UserID != $InvestAsset->UserIDCreated && $EmployeeLogin->UserID != $InvestAsset->AuthorizedPerson){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!';
                    return json_encode($json);
                }else{
                    $InvestAssetPerEmployee = new InvestAssetPer([
                        'InvestAssetID' => $id,
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
            if($EmployeeLogin->UserID == $InvestAsset->UserIDCreated || $EmployeeLogin->UserID == $InvestAsset->AuthorizedPerson){
                $InvestAssetPerEmployee = new InvestAssetPer([
                    'InvestAssetID' => $id,
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

        if ($InvestAssetPerEmployee) {
            if (strpos(',' . $InvestAssetPerEmployee->AccessField . ',', ',all,')) {
                $InvestAssetPerEmployee->AccessField = 'all';
            }
            if(strpos(',' . $InvestAssetPerEmployee->EditField . ',', ',all,')){
                $InvestAssetPerEmployee->EditField = 'all';
            }
        }

        $InvestAssetPer = InvestAssetPer::where('InvestAssetID', '=', $id)->groupBy('EmployeeID')->get();
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::all();

        $InvestAssetCate = InvestAssetCate::where('InvestAssetID', $id)->get();
        $arrCateID = [];
        foreach ($InvestAssetCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $InvestAssetCateList = InvestAssetCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $InvestAssetCateValue = InvestAssetCateValue::whereIn('CateID', $arrCateID)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $InvestAsset;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['InvestAssetPer'] = $InvestAssetPer;
        $json['InvestAssetPerEmployee'] = $InvestAssetPerEmployee;
        $json['data']['InvestAssetCate'] = $InvestAssetCate;
        $json['data']['InvestAssetCateList'] = $InvestAssetCateList;
        $json['data']['InvestAssetCateValue'] = $InvestAssetCateValue;
        return json_encode($json);
    }

    public function edit($id){
        $data = InvestAsset::find($id);
        if(old('InvestAssetNo')){
            $data->InvestAssetNo = old('InvestAssetNo');
        }else{
            $data->InvestAssetNo = $data->Prefix.$data->InvestAssetNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = InvestAsset::find($id);
        if($data->InvestAssetNo !== $request->InvestAssetNo){
            $validator = Validator::make($request->all(), [
                'InvestAssetNo' => 'required|unique:invest_asset'
            ],
                [
                    'InvestAssetNo.required' => 'Mã tài sản đầu tư không được để trống',
                    'InvestAssetNo.unique' => 'Mã tài sản đầu tư đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'invest_asset')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data, 'invest_asset', $autoModel, 'tài sản đầu tư');
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->InvestAssetNo = $request->InvestAssetNo;
        }
        $data->InvestAssetName = $request->InvestAssetName;
        $data->UomID = $request->UomID;
        $data->UomName = $request->UomName;

        if ($data->AccessType !== $request->AccessType) {
            $data->AccessType = $request->AccessType;
        }

        $data->save();

        // invest_asset cate
        if ($request->has('InvestAssetCate')) {
            $InvestAssetCate = $request->post('InvestAssetCate');
            DB::table('invest_asset_cate')->where('InvestAssetID', $id)->delete();
            $arrInsertCate = [];
            foreach ($InvestAssetCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'InvestAssetID' => $id,
                        'CateID' => ($cate['CateID']) ? $cate['CateID'] : null,
                        'CateValue' => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('invest_asset_cate')->insert($arrInsertCate);
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
        $InvestAssetLink = InvestAssetLink::where('InvestAssetID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $InvestAssetLink;

        return json_encode($json);
    }

    //Link
    public function updateLink(Request $request){
        $LinkArray = $request->post('InvestAssetLink');
        $id = $request->post('InvestAssetID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'InvestAssetID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        InvestAssetLink::where(['InvestAssetID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('invest_asset_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getCate(Request $request){
        $investAssetCateList = InvestAssetCateList::where('Inactive', '=', 0)->get();
        $investAssetCateValue = InvestAssetCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['InvestAssetCateList'] = $investAssetCateList;
        $json['data']['InvestAssetCateValue'] = $investAssetCateValue;
        return json_encode($json);
    }

    public function getFile(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $InvestAssetFile = InvestAssetFile::where('InvestAssetID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $InvestAssetFile;

        return json_encode($json);
    }
    public function getVideo(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $InvestAssetVideo = InvestAssetVideo::where('InvestAssetID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $InvestAssetVideo;

        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $InvestAssetID = $request->post('InvestAssetID');
        $InvestAssetPer = $request->post('InvestAssetPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($InvestAssetPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'InvestAssetID' => $InvestAssetID,
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
        InvestAssetPer::where(['InvestAssetID' => $InvestAssetID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('invest_asset_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $InvestAssetID;
        return json_encode($json);
    }

    //Upload File
    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $InvestAssetFile = InvestAssetFile::find($request->LineID);
        }else{
            $InvestAssetFile = new InvestAssetFile();
        }
        $InvestAssetFile->FileName = $request->FileName;
        $InvestAssetFile->FileNameRoot = $request->FileName;
        $InvestAssetFile->Description = $request->Description;
        $InvestAssetFile->FileType = $request->FileType;
        $InvestAssetFile->FileSize = $request->FileSize;
        $InvestAssetFile->InvestAssetID = $id;
        $InvestAssetFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $InvestAssetFile->UserModified = Auth::user()->username;
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

                    $InvestAssetFile->Link = $linkFileAttach;
                    $InvestAssetFile->DateModifiedRoot = $fileModel->DateModified;
                    $InvestAssetFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $InvestAssetFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $InvestAssetFile;
        return json_encode($json);
    }

    //Delete File
    public function deleteFile(Request $request, $id){
        $dFile = InvestAssetFile::where(['LineID' => $id])->delete();
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
        $files = InvestAssetFile::where('InvestAssetID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $investAsset = InvestAsset::find($id);
            $zipName = '';
            if ($investAsset) {
                $zipName = IjString::CreateSlug($investAsset->InvestAssetName, '-');
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
            $InvestAssetVideo = InvestAssetVideo::find($request->LineID);
        }else{
            $InvestAssetVideo = new InvestAssetVideo();
        }
        $InvestAssetVideo->VideoName = $request->VideoName;
        $InvestAssetVideo->Description = $request->Description;
        $InvestAssetVideo->VideoType = $request->VideoType;
        $InvestAssetVideo->VideoSize = $request->VideoSize;
        $InvestAssetVideo->InvestAssetID = $id;
        $InvestAssetVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $InvestAssetVideo->UserModified = Auth::user()->username;
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

                    $InvestAssetVideo->Link = $linkFileAttach;
                    $InvestAssetVideo->DateModifiedRoot = $fileModel->DateModified;
                    $InvestAssetVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $InvestAssetVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $InvestAssetVideo;
        return json_encode($json);
    }
    //Delete Video
    public function deleteVideo(Request $request, $id){
        $tVideo = InvestAssetVideo::where(['LineID' => $id])->delete();
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
        $data = InvestAsset::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
    public function delete($id){
        $InvestAsset = InvestAsset::find($id);
        $auth = Auth::user();
        if($InvestAsset){
            if ($auth->UserType === 1) {
                $InvestAsset->delete();
                InvestAssetCate::where('InvestAssetID', $id)->delete();
                InvestAssetPer::where('InvestAssetID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $isPer = InvestAssetPer::where([
                    'InvestAssetID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($InvestAsset->UserIDCreated === $auth->UserID)){
                    $InvestAsset->delete();
                    InvestAssetCate::where('InvestAssetID', $id)->delete();
                    InvestAssetPer::where('InvestAssetID', $id)->delete();

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
            InvestAsset::destroy($idArray);
            InvestAssetCate::whereIn('InvestAssetID', $idArray)->delete();
            InvestAssetPer::whereIn('InvestAssetID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $investAssetsPer = InvestAssetPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('invest_asset_per')
                    ->whereIn('InvestAssetID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('InvestAssetID');
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
                InvestAsset::destroy($idArray);
                InvestAssetCate::whereIn('InvestAssetID', $idArray)->delete();
                InvestAssetPer::whereIn('InvestAssetID', $idArray)->delete();

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
