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
use Module\Listing\Models\Vendor;
use Module\Listing\Models\Employee;
use Module\Listing\Models\VendorCate;
use Module\Listing\Models\VendorCateList;
use Module\Listing\Models\VendorCateValue;
use Module\Listing\Models\VendorFile;
use Module\Listing\Models\VendorPer;
use Module\Listing\Models\VendorVideo;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\VendorLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class VendorController extends Controller{

    public function list(Request $request){
        $data = Vendor::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('vendor', ['select' => ['VendorNo', 'VendorName'], 'conditions' => []]), 'nhacungcap.xlsx');
    }

    public function create(){
        if(old('VendorNo')){
            $auto = old('VendorNo');
        }else{
            $auto = AutoNo::genNewNumberNo('vendor');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'VendorNo' => 'required|unique:vendor'
        ],
            [
                'VendorNo.required' => 'Mã vật tư - hàng hóa - dịch vụ không được để trống',
                'VendorNo.unique' => 'Mã vật tư - hàng hóa - dịch vụ đã tồn tại',
            ]);

        /*$autoModel = SysAutoNumber::where('NumberKey', 'vendor')->first();
        AutoNo::storeValidate($validator, $request, 'vendor', $autoModel, 'vật tư - hàng hóa - dịch vụ');*/
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new Vendor([
            'VendorNo' => $request->VendorNo,
            'VendorName' => $request->VendorName,
            'VendorType' => $request->VendorType,
            'UomID' => $request->UomID,
            'UomName' => $request->UomName,
            'AccessType' => ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated' => Auth::user()->UserID,
            'AuthorizedPerson' => Auth::user()->UserID,
            'Inactive' => $request->Inactive ? $request->Inactive : 0,
        ]);
        $data->save();

        $VendorPer = new VendorPer([
            'VendorID' => $data->VendorID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Create' => 1,
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1
        ]);
        $VendorPer->save();

        // cate list
        if ($request->has('VendorCate')) {
            $VendorCate = $request->post('VendorCate');
            $arrInsertCate = [];
            foreach ($VendorCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'VendorID' => $data->VendorID,
                        'CateID' => $cate['CateID'],
                        'CateValue' => $cate['CateValue']
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('vendor_cate')->insert($arrInsertCate);
            }
        }

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');

            // vendor per
            $VendorPerCopy = VendorPer::where('VendorID', $CopyID)->where('EmployeeID', '!=', Auth::user()->EmployeeID)->get();
            $vendorPerArrInsert = [];
            foreach ($VendorPerCopy as $vendorPer) {
                $vendorPerArrInsert[] = [
                    'VendorID' => $data->VendorID,
                    'EmployeeID' => $vendorPer->EmployeeID,
                    'EmployeeName' => $vendorPer->EmployeeName,
                    'Access' => $vendorPer->Access,
                    'AccessField' => $vendorPer->AccessField,
                    'Create' => $vendorPer->Create,
                    'Edit' => $vendorPer->Edit,
                    'EditField' => $vendorPer->EditField,
                    'Delete' => $vendorPer->Delete
                ];
            }
            if (count($vendorPerArrInsert)) {
                DB::table('vendor_per')->insert($vendorPerArrInsert);
            }

            // vendor link
            $VendorLinkCopy = VendorLink::where('VendorID', $CopyID)->get();
            $vendorLinkArrInsert = [];
            foreach ($VendorLinkCopy as $vendorLink) {
                $vendorLinkArrInsert[] = [
                    'VendorID' => $data->VendorID,
                    'LinkID' => $vendorLink->LinkID,
                    'LinkNo' => $vendorLink->LinkNo,
                    'LinkName' => $vendorLink->LinkName,
                    'LinkTable' => $vendorLink->LinkTable,
                    'LinkTableName' => $vendorLink->LinkTableName
                ];
            }
            if (count($vendorLinkArrInsert)) {
                DB::table('vendor_link')->insert($vendorLinkArrInsert);
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

        $Vendor = Vendor::find($id);
        $VendorPerEmployee = VendorPer::where([
            'VendorID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->first();
        $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        if(!$VendorPerEmployee){
            if($Vendor->AccessType == 2){
                //Nếu tài liệu là công khai
                $VendorPerEmployee = new VendorPer([
                    'VendorID' => $id,
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
                if($EmployeeLogin->UserID != $Vendor->UserIDCreated && $EmployeeLogin->UserID != $Vendor->AuthorizedPerson){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!';
                    return json_encode($json);
                }else{
                    $VendorPerEmployee = new VendorPer([
                        'VendorID' => $id,
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
            if($EmployeeLogin->UserID == $Vendor->UserIDCreated || $EmployeeLogin->UserID == $Vendor->AuthorizedPerson){
                $VendorPerEmployee = new VendorPer([
                    'VendorID' => $id,
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

        if ($VendorPerEmployee) {
            if (strpos(',' . $VendorPerEmployee->AccessField . ',', ',all,')) {
                $VendorPerEmployee->AccessField = 'all';
            }
            if(strpos(',' . $VendorPerEmployee->EditField . ',', ',all,')){
                $VendorPerEmployee->EditField = 'all';
            }
        }

        $VendorPer = VendorPer::where('VendorID', '=', $id)->groupBy('EmployeeID')->get();
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::all();

        $VendorCate = VendorCate::where('VendorID', $id)->get();
        $arrCateID = [];
        foreach ($VendorCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $VendorCateList = VendorCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $VendorCateValue = VendorCateValue::whereIn('CateID', $arrCateID)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $Vendor;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['VendorPer'] = $VendorPer;
        $json['VendorPerEmployee'] = $VendorPerEmployee;
        $json['data']['VendorCate'] = $VendorCate;
        $json['data']['VendorCateList'] = $VendorCateList;
        $json['data']['VendorCateValue'] = $VendorCateValue;
        return json_encode($json);
    }

    public function edit($id){
        $data = Vendor::find($id);
        if(old('VendorNo')){
            $data->VendorNo = old('VendorNo');
        }else{
            $data->VendorNo = $data->Prefix.$data->VendorNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = Vendor::find($id);
        if($data->VendorNo !== $request->VendorNo){
            $validator = Validator::make($request->all(), [
                'VendorNo' => 'required|unique:vendor'
            ],
                [
                    'VendorNo.required' => 'Mã vật tư - hàng hóa - dịch vụ không được để trống',
                    'VendorNo.unique' => 'Mã vật tư - hàng hóa - dịch vụ đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'vendor')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data, 'vendor', $autoModel, 'vật tư - hàng hóa - dịch vụ');
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->VendorNo = $request->VendorNo;
        }
        $data->VendorName = $request->VendorName;
        $data->VendorType = $request->VendorType;
        $data->UomID = $request->UomID;
        $data->UomName = $request->UomName;

        if ($data->AccessType !== $request->AccessType) {
            $data->AccessType = $request->AccessType;
        }

        $data->save();

        // vendor cate
        if ($request->has('VendorCate')) {
            $VendorCate = $request->post('VendorCate');
            DB::table('vendor_cate')->where('VendorID', $id)->delete();
            $arrInsertCate = [];
            foreach ($VendorCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'VendorID' => $id,
                        'CateID' => ($cate['CateID']) ? $cate['CateID'] : null,
                        'CateValue' => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('vendor_cate')->insert($arrInsertCate);
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
        $VendorLink = VendorLink::where('VendorID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $VendorLink;

        return json_encode($json);
    }

    //Link
    public function updateLink(Request $request){
        $LinkArray = $request->post('VendorLink');
        $id = $request->post('VendorID');
        $arrLink = [];
        foreach ($LinkArray as $vendor){
            if($vendor['LinkTable']){
                $arrLink[] = [
                    'VendorID' => $id,
                    'LinkID' => $vendor['LinkID'],
                    'LinkNo' => $vendor['LinkNo'],
                    'LinkName' => $vendor['LinkName'],
                    'LinkTable' => $vendor['LinkTable'],
                    'LinkTableName' => $vendor['LinkTableName'],
                ];
            }
        }
        VendorLink::where(['VendorID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('vendor_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getCate(Request $request){
        $vendorCateList = VendorCateList::where('Inactive', '=', 0)->get();
        $vendorCateValue = VendorCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['VendorCateList'] = $vendorCateList;
        $json['data']['VendorCateValue'] = $vendorCateValue;
        return json_encode($json);
    }

    public function getFile(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $VendorFile = VendorFile::where('VendorID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $VendorFile;

        return json_encode($json);
    }
    public function getVideo(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $VendorVideo = VendorVideo::where('VendorID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $VendorVideo;

        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $VendorID = $request->post('VendorID');
        $VendorPer = $request->post('VendorPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($VendorPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'VendorID' => $VendorID,
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
        VendorPer::where(['VendorID' => $VendorID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('vendor_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $VendorID;
        return json_encode($json);
    }

    //Upload File
    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $VendorFile = VendorFile::find($request->LineID);
        }else{
            $VendorFile = new VendorFile();
        }
        $VendorFile->FileName = $request->FileName;
        $VendorFile->FileNameRoot = $request->FileName;
        $VendorFile->Description = $request->Description;
        $VendorFile->FileType = $request->FileType;
        $VendorFile->FileSize = $request->FileSize;
        $VendorFile->VendorID = $id;
        $VendorFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $VendorFile->UserModified = Auth::user()->username;
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

                    $VendorFile->Link = $linkFileAttach;
                    $VendorFile->DateModifiedRoot = $fileModel->DateModified;
                    $VendorFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $VendorFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $VendorFile;
        return json_encode($json);
    }

    //Delete File
    public function deleteFile(Request $request, $id){
        $dFile = VendorFile::where(['LineID' => $id])->delete();
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
        $files = VendorFile::where('VendorID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $vendor = Vendor::find($id);
            $zipName = '';
            if ($vendor) {
                $zipName = IjString::CreateSlug($vendor->VendorName, '-');
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
            $VendorVideo = VendorVideo::find($request->LineID);
        }else{
            $VendorVideo = new VendorVideo();
        }
        $VendorVideo->VideoName = $request->VideoName;
        $VendorVideo->Description = $request->Description;
        $VendorVideo->VideoType = $request->VideoType;
        $VendorVideo->VideoSize = $request->VideoSize;
        $VendorVideo->VendorID = $id;
        $VendorVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $VendorVideo->UserModified = Auth::user()->username;
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

                    $VendorVideo->Link = $linkFileAttach;
                    $VendorVideo->DateModifiedRoot = $fileModel->DateModified;
                    $VendorVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $VendorVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $VendorVideo;
        return json_encode($json);
    }
    //Delete Video
    public function deleteVideo(Request $request, $id){
        $tVideo = VendorVideo::where(['LineID' => $id])->delete();
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
        $data = Vendor::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->vendors();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
    public function delete($id){
        $Vendor = Vendor::find($id);
        $auth = Auth::user();
        if($Vendor){
            if ($auth->UserType === 1) {
                $Vendor->delete();
                VendorCate::where('VendorID', $id)->delete();
                VendorPer::where('VendorID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $isPer = VendorPer::where([
                    'VendorID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($Vendor->UserIDCreated === $auth->UserID)){
                    $Vendor->delete();
                    VendorCate::where('VendorID', $id)->delete();
                    VendorPer::where('VendorID', $id)->delete();

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
            Vendor::destroy($idArray);
            VendorCate::whereIn('VendorID', $idArray)->delete();
            VendorPer::whereIn('VendorID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $vendorsPer = VendorPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('vendor_per')
                    ->whereIn('VendorID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('VendorID');
            })->get();

            foreach ($vendorsPer as $vendorPer) {
                if ($vendorPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                Vendor::destroy($idArray);
                VendorCate::whereIn('VendorID', $idArray)->delete();
                VendorPer::whereIn('VendorID', $idArray)->delete();

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
