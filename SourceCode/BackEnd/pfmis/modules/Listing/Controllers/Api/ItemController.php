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
use Module\Listing\Models\Item;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Company;
use Module\Listing\Models\Employee;
use Module\Listing\Models\ItemCate;
use Module\Listing\Models\ItemCateList;
use Module\Listing\Models\ItemCateValue;
use Module\Listing\Models\ItemFile;
use Module\Listing\Models\ItemPer;
use Module\Listing\Models\ItemVideo;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\ItemLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class ItemController extends Controller{

    public function list(Request $request){
        $data = Item::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('item', ['select' => ['ItemNo', 'ItemName'], 'conditions' => []]), 'nhacungcap.xlsx');
    }

    public function create(){
        if(old('ItemNo')){
            $auto = old('ItemNo');
        }else{
            $auto = AutoNo::genNewNumberNo('item');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'ItemNo' => 'required|unique:item'
        ],
            [
                'ItemNo.required' => 'Mã vật tư - hàng hóa - dịch vụ không được để trống',
                'ItemNo.unique' => 'Mã vật tư - hàng hóa - dịch vụ đã tồn tại',
            ]);

        /*$autoModel = SysAutoNumber::where('NumberKey', 'item')->first();
        AutoNo::storeValidate($validator, $request, 'item', $autoModel, 'vật tư - hàng hóa - dịch vụ');*/
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new Item([
            'ItemNo' => $request->ItemNo,
            'ItemName' => $request->ItemName,
            'ItemType' => $request->ItemType,
            'UomID' => $request->UomID,
            'UomName' => $request->UomName,
            'AccessType' => ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated' => Auth::user()->UserID,
            'AuthorizedPerson' => Auth::user()->UserID,
            'Inactive' => $request->Inactive ? $request->Inactive : 0,
        ]);
        $data->save();

        $ItemPer = new ItemPer([
            'ItemID' => $data->ItemID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Create' => 1,
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1
        ]);
        $ItemPer->save();

        // cate list
        if ($request->has('ItemCate')) {
            $ItemCate = $request->post('ItemCate');
            $arrInsertCate = [];
            foreach ($ItemCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'ItemID' => $data->ItemID,
                        'CateID' => $cate['CateID'],
                        'CateValue' => $cate['CateValue']
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('item_cate')->insert($arrInsertCate);
            }
        }

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');

            // item per
            $ItemPerCopy = ItemPer::where('ItemID', $CopyID)->where('EmployeeID', '!=', Auth::user()->EmployeeID)->get();
            $itemPerArrInsert = [];
            foreach ($ItemPerCopy as $itemPer) {
                $itemPerArrInsert[] = [
                    'ItemID' => $data->ItemID,
                    'EmployeeID' => $itemPer->EmployeeID,
                    'EmployeeName' => $itemPer->EmployeeName,
                    'Access' => $itemPer->Access,
                    'AccessField' => $itemPer->AccessField,
                    'Create' => $itemPer->Create,
                    'Edit' => $itemPer->Edit,
                    'EditField' => $itemPer->EditField,
                    'Delete' => $itemPer->Delete
                ];
            }
            if (count($itemPerArrInsert)) {
                DB::table('item_per')->insert($itemPerArrInsert);
            }

            // item link
            $ItemLinkCopy = ItemLink::where('ItemID', $CopyID)->get();
            $itemLinkArrInsert = [];
            foreach ($ItemLinkCopy as $itemLink) {
                $itemLinkArrInsert[] = [
                    'ItemID' => $data->ItemID,
                    'LinkID' => $itemLink->LinkID,
                    'LinkNo' => $itemLink->LinkNo,
                    'LinkName' => $itemLink->LinkName,
                    'LinkTable' => $itemLink->LinkTable,
                    'LinkTableName' => $itemLink->LinkTableName
                ];
            }
            if (count($itemLinkArrInsert)) {
                DB::table('item_link')->insert($itemLinkArrInsert);
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

        $Item = Item::find($id);
        $ItemPerEmployee = ItemPer::where([
            'ItemID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->first();
        $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        if(!$ItemPerEmployee){
            if($Item->AccessType == 2){
                //Nếu tài liệu là công khai
                $ItemPerEmployee = new ItemPer([
                    'ItemID' => $id,
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
                if($EmployeeLogin->UserID != $Item->UserIDCreated && $EmployeeLogin->UserID != $Item->AuthorizedPerson){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!';
                    return json_encode($json);
                }else{
                    $ItemPerEmployee = new ItemPer([
                        'ItemID' => $id,
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
            if($EmployeeLogin->UserID == $Item->UserIDCreated || $EmployeeLogin->UserID == $Item->AuthorizedPerson){
                $ItemPerEmployee = new ItemPer([
                    'ItemID' => $id,
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

        if ($ItemPerEmployee) {
            if (strpos(',' . $ItemPerEmployee->AccessField . ',', ',all,')) {
                $ItemPerEmployee->AccessField = 'all';
            }
            if(strpos(',' . $ItemPerEmployee->EditField . ',', ',all,')){
                $ItemPerEmployee->EditField = 'all';
            }
        }

        $ItemPer = ItemPer::where('ItemID', '=', $id)->groupBy('EmployeeID')->get();
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::all();

        $ItemCate = ItemCate::where('ItemID', $id)->get();
        $arrCateID = [];
        foreach ($ItemCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $ItemCateList = ItemCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $ItemCateValue = ItemCateValue::whereIn('CateID', $arrCateID)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $Item;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['ItemPer'] = $ItemPer;
        $json['ItemPerEmployee'] = $ItemPerEmployee;
        $json['data']['ItemCate'] = $ItemCate;
        $json['data']['ItemCateList'] = $ItemCateList;
        $json['data']['ItemCateValue'] = $ItemCateValue;
        return json_encode($json);
    }

    public function edit($id){
        $data = Item::find($id);
        if(old('ItemNo')){
            $data->ItemNo = old('ItemNo');
        }else{
            $data->ItemNo = $data->Prefix.$data->ItemNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = Item::find($id);
        if($data->ItemNo !== $request->ItemNo){
            $validator = Validator::make($request->all(), [
                'ItemNo' => 'required|unique:item'
            ],
                [
                    'ItemNo.required' => 'Mã vật tư - hàng hóa - dịch vụ không được để trống',
                    'ItemNo.unique' => 'Mã vật tư - hàng hóa - dịch vụ đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'item')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data, 'item', $autoModel, 'vật tư - hàng hóa - dịch vụ');
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->ItemNo = $request->ItemNo;
        }
        $data->ItemName = $request->ItemName;
        $data->ItemType = $request->ItemType;
        $data->UomID = $request->UomID;
        $data->UomName = $request->UomName;

        if ($data->AccessType !== $request->AccessType) {
            $data->AccessType = $request->AccessType;
        }

        $data->save();

        // item cate
        if ($request->has('ItemCate')) {
            $ItemCate = $request->post('ItemCate');
            DB::table('item_cate')->where('ItemID', $id)->delete();
            $arrInsertCate = [];
            foreach ($ItemCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'ItemID' => $id,
                        'CateID' => ($cate['CateID']) ? $cate['CateID'] : null,
                        'CateValue' => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('item_cate')->insert($arrInsertCate);
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
        $ItemLink = ItemLink::where('ItemID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $ItemLink;

        return json_encode($json);
    }

    //Link
    public function updateLink(Request $request){
        $LinkArray = $request->post('ItemLink');
        $id = $request->post('ItemID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'ItemID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        ItemLink::where(['ItemID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('item_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getCate(Request $request){
        $itemCateList = ItemCateList::where('Inactive', '=', 0)->get();
        $itemCateValue = ItemCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['ItemCateList'] = $itemCateList;
        $json['data']['ItemCateValue'] = $itemCateValue;
        return json_encode($json);
    }

    public function getFile(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $ItemFile = ItemFile::where('ItemID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $ItemFile;

        return json_encode($json);
    }
    public function getVideo(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $ItemVideo = ItemVideo::where('ItemID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $ItemVideo;

        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $ItemID = $request->post('ItemID');
        $ItemPer = $request->post('ItemPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($ItemPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'ItemID' => $ItemID,
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
        ItemPer::where(['ItemID' => $ItemID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('item_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ItemID;
        return json_encode($json);
    }

    //Upload File
    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $ItemFile = ItemFile::find($request->LineID);
        }else{
            $ItemFile = new ItemFile();
        }
        $ItemFile->FileName = $request->FileName;
        $ItemFile->FileNameRoot = $request->FileName;
        $ItemFile->Description = $request->Description;
        $ItemFile->FileType = $request->FileType;
        $ItemFile->FileSize = $request->FileSize;
        $ItemFile->ItemID = $id;
        $ItemFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $ItemFile->UserModified = Auth::user()->username;
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

                    $ItemFile->Link = $linkFileAttach;
                    $ItemFile->DateModifiedRoot = $fileModel->DateModified;
                    $ItemFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $ItemFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ItemFile;
        return json_encode($json);
    }

    //Delete File
    public function deleteFile(Request $request, $id){
        $dFile = ItemFile::where(['LineID' => $id])->delete();
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
        $files = ItemFile::where('ItemID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $item = Item::find($id);
            $zipName = '';
            if ($item) {
                $zipName = IjString::CreateSlug($item->ItemName, '-');
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
            $ItemVideo = ItemVideo::find($request->LineID);
        }else{
            $ItemVideo = new ItemVideo();
        }
        $ItemVideo->VideoName = $request->VideoName;
        $ItemVideo->Description = $request->Description;
        $ItemVideo->VideoType = $request->VideoType;
        $ItemVideo->VideoSize = $request->VideoSize;
        $ItemVideo->ItemID = $id;
        $ItemVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $ItemVideo->UserModified = Auth::user()->username;
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

                    $ItemVideo->Link = $linkFileAttach;
                    $ItemVideo->DateModifiedRoot = $fileModel->DateModified;
                    $ItemVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $ItemVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ItemVideo;
        return json_encode($json);
    }
    //Delete Video
    public function deleteVideo(Request $request, $id){
        $tVideo = ItemVideo::where(['LineID' => $id])->delete();
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
        $data = Item::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
    public function delete($id){
        $Item = Item::find($id);
        $auth = Auth::user();
        if($Item){
            if ($auth->UserType === 1) {
                $Item->delete();
                ItemCate::where('ItemID', $id)->delete();
                ItemPer::where('ItemID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $isPer = ItemPer::where([
                    'ItemID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($Item->UserIDCreated === $auth->UserID)){
                    $Item->delete();
                    ItemCate::where('ItemID', $id)->delete();
                    ItemPer::where('ItemID', $id)->delete();

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
            Item::destroy($idArray);
            ItemCate::whereIn('ItemID', $idArray)->delete();
            ItemPer::whereIn('ItemID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $itemsPer = ItemPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('item_per')
                    ->whereIn('ItemID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('ItemID');
            })->get();

            foreach ($itemsPer as $itemPer) {
                if ($itemPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                Item::destroy($idArray);
                ItemCate::whereIn('ItemID', $idArray)->delete();
                ItemPer::whereIn('ItemID', $idArray)->delete();

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
