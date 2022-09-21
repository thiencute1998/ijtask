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
use Module\Listing\Models\Customer;
use Module\Listing\Models\Employee;
use Module\Listing\Models\CustomerCate;
use Module\Listing\Models\CustomerCateList;
use Module\Listing\Models\CustomerCateValue;
use Module\Listing\Models\CustomerFile;
use Module\Listing\Models\CustomerPer;
use Module\Listing\Models\CustomerVideo;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CustomerLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class CustomerController extends Controller{

    public function list(Request $request){
        $data = Customer::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('customer', ['select' => ['CustomerNo', 'CustomerName'], 'conditions' => []]), 'khachhang.xlsx');
    }

    public function create(){
        if(old('CustomerNo')){
            $auto = old('CustomerNo');
        }else{
            $auto = AutoNo::genNewNumberNo('customer');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'CustomerNo' => 'required|unique:customer'
        ],
            [
                'CustomerNo.required' => 'Mã khách hàng không được để trống',
                'CustomerNo.unique' => 'Mã khách hàng đã tồn tại',
            ]);

        $autoModel = SysAutoNumber::where('NumberKey', 'customer')->first();
        AutoNo::storeValidate($validator, $request, 'customer', $autoModel, 'khách hàng');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $array_custom = [
            'AccessType'=> ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated'=> Auth::user()->UserID,
            'AuthorizedPerson'=> Auth::user()->UserID
        ];

        $request->merge($array_custom);

        $data = Customer::create($request->all());

        $CustomerPer = new CustomerPer([
            'CustomerID' => $data->CustomerID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $CustomerPer->save();

        // cate list
        if ($request->has('CustomerCate')) {
            $CustomerCate = $request->post('CustomerCate');
            $arrInsertCate = [];
            foreach ($CustomerCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'CustomerID' => $data->CustomerID,
                        'CateID' => $cate['CateID'],
                        'CateValue' => $cate['CateValue']
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('customer_cate')->insert($arrInsertCate);
            }
        }

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');

            // customer per
            $CustomerPerCopy = CustomerPer::where('CustomerID', $CopyID)->where('EmployeeID', '!=', Auth::user()->EmployeeID)->get();
            $customerPerArrInsert = [];
            foreach ($CustomerPerCopy as $customerPer) {
                $customerPerArrInsert[] = [
                    'CustomerID' => $data->CustomerID,
                    'EmployeeID' => $customerPer->EmployeeID,
                    'EmployeeName' => $customerPer->EmployeeName,
                    'Access' => $customerPer->Access,
                    'AccessField' => $customerPer->AccessField,
                    'Edit' => $customerPer->Edit,
                    'EditField' => $customerPer->EditField,
                    'Delete' => $customerPer->Delete,
                    'Create' => $customerPer->Create
                ];
            }
            if (count($customerPerArrInsert)) {
                DB::table('customer_per')->insert($customerPerArrInsert);
            }

            // customer link
            $CustomerLinkCopy = CustomerLink::where('CustomerID', $CopyID)->get();
            $customerLinkArrInsert = [];
            foreach ($CustomerLinkCopy as $customerLink) {
                $customerLinkArrInsert[] = [
                    'CustomerID' => $data->CustomerID,
                    'LinkID' => $customerLink->LinkID,
                    'LinkNo' => $customerLink->LinkNo,
                    'LinkName' => $customerLink->LinkName,
                    'LinkTable' => $customerLink->LinkTable,
                    'LinkTableName' => $customerLink->LinkTableName
                ];
            }
            if (count($customerLinkArrInsert)) {
                DB::table('customer_link')->insert($customerLinkArrInsert);
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->CustomerID;

        return json_encode($json);
    }

    public function view($id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $Customer = Customer::find($id);
        $CustomerPerEmployee = CustomerPer::where([
            'CustomerID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID
        ])->first();
        $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

        if(!$CustomerPerEmployee){
            if($Customer->AccessType == 2){
                //Nếu tài liệu là công khai
                $CustomerPerEmployee = new CustomerPer([
                    'CustomerID' => $id,
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
                if($EmployeeLogin->UserID != $Customer->UserIDCreated && $EmployeeLogin->UserID != $Customer->AuthorizedPerson){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!';
                    return json_encode($json);
                }else{
                    $CustomerPerEmployee = new CustomerPer([
                        'CustomerID' => $id,
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
            if($EmployeeLogin->UserID == $Customer->UserIDCreated || $EmployeeLogin->UserID == $Customer->AuthorizedPerson){
                $CustomerPerEmployee = new CustomerPer([
                    'CustomerID' => $id,
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

        if ($CustomerPerEmployee) {
            if (strpos(',' . $CustomerPerEmployee->AccessField . ',', ',all,')) {
                $CustomerPerEmployee->AccessField = 'all';
            }
            if(strpos(',' . $CustomerPerEmployee->EditField . ',', ',all,')){
                $CustomerPerEmployee->EditField = 'all';
            }
        }

        $CustomerPer = CustomerPer::where('CustomerID', '=', $id)->groupBy('EmployeeID')->get();
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::all();

        $CustomerCate = CustomerCate::where('CustomerID', $id)->get();
        $arrCateID = [];
        foreach ($CustomerCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $CustomerCateList = CustomerCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $CustomerCateValue = CustomerCateValue::whereIn('CateID', $arrCateID)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $Customer;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['CustomerPer'] = $CustomerPer;
        $json['CustomerPerEmployee'] = $CustomerPerEmployee;
        $json['data']['CustomerCate'] = $CustomerCate;
        $json['data']['CustomerCateList'] = $CustomerCateList;
        $json['data']['CustomerCateValue'] = $CustomerCateValue;
        return json_encode($json);
    }

    public function edit($id){
        $data = Customer::find($id);
        if(old('CustomerNo')){
            $data->CustomerNo = old('CustomerNo');
        }else{
            $data->CustomerNo = $data->Prefix.$data->CustomerNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = Customer::find($id);
        if($data->CustomerNo !== $request->CustomerNo){
            $validator = Validator::make($request->all(), [
                'CustomerNo' => 'required|unique:customer'
            ],
                [
                    'CustomerNo.required' => 'Mã khách hàng không được để trống',
                    'CustomerNo.unique' => 'Mã khách hàng đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'customer')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data, 'customer', $autoModel, 'khách hàng');
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }

        }
        $array_custom = [
            'AccessType'=> ($request->AccessType) ? $request->AccessType : 1
        ];
        $request->merge($array_custom);
        $data->fill($request->all())->save();

        // customer cate
        if ($request->has('CustomerCate')) {
            $CustomerCate = $request->post('CustomerCate');
            DB::table('customer_cate')->where('CustomerID', $id)->delete();
            $arrInsertCate = [];
            foreach ($CustomerCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'CustomerID' => $id,
                        'CateID' => ($cate['CateID']) ? $cate['CateID'] : null,
                        'CateValue' => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('customer_cate')->insert($arrInsertCate);
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
        $CustomerLink = CustomerLink::where('CustomerID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $CustomerLink;

        return json_encode($json);
    }

    //Link
    public function updateLink(Request $request){
        $LinkArray = $request->post('CustomerLink');
        $id = $request->post('CustomerID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'CustomerID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        CustomerLink::where(['CustomerID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('customer_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getCate(Request $request){
        $customerCateList = CustomerCateList::where('Inactive', '=', 0)->get();
        $customerCateValue = CustomerCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CustomerCateList'] = $customerCateList;
        $json['data']['CustomerCateValue'] = $customerCateValue;
        return json_encode($json);
    }

    public function getFile(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $CustomerFile = CustomerFile::where('CustomerID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $CustomerFile;

        return json_encode($json);
    }
    public function getVideo(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $CustomerVideo = CustomerVideo::where('CustomerID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $CustomerVideo;

        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $CustomerID = $request->post('CustomerID');
        $CustomerPer = $request->post('CustomerPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($CustomerPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'CustomerID' => $CustomerID,
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
        CustomerPer::where(['CustomerID' => $CustomerID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('customer_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $CustomerID;
        return json_encode($json);
    }

    //Upload File
    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $CustomerFile = CustomerFile::find($request->LineID);
        }else{
            $CustomerFile = new CustomerFile();
        }
        $CustomerFile->FileName = $request->FileName;
        $CustomerFile->FileNameRoot = $request->FileName;
        $CustomerFile->Description = $request->Description;
        $CustomerFile->FileType = $request->FileType;
        $CustomerFile->FileSize = $request->FileSize;
        $CustomerFile->CustomerID = $id;
        $CustomerFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $CustomerFile->UserModified = Auth::user()->username;
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

                    $CustomerFile->Link = $linkFileAttach;
                    $CustomerFile->DateModifiedRoot = $fileModel->DateModified;
                    $CustomerFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $CustomerFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $CustomerFile;
        return json_encode($json);
    }

    //Delete File
    public function deleteFile(Request $request, $id){
        $dFile = CustomerFile::where(['LineID' => $id])->delete();
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
        $files = CustomerFile::where('CustomerID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $customer = Customer::find($id);
            $zipName = '';
            if ($customer) {
                $zipName = IjString::CreateSlug($customer->CustomerName, '-');
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
            $CustomerVideo = CustomerVideo::find($request->LineID);
        }else{
            $CustomerVideo = new CustomerVideo();
        }
        $CustomerVideo->VideoName = $request->VideoName;
        $CustomerVideo->Description = $request->Description;
        $CustomerVideo->VideoType = $request->VideoType;
        $CustomerVideo->VideoSize = $request->VideoSize;
        $CustomerVideo->CustomerID = $id;
        $CustomerVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $CustomerVideo->UserModified = Auth::user()->username;
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

                    $CustomerVideo->Link = $linkFileAttach;
                    $CustomerVideo->DateModifiedRoot = $fileModel->DateModified;
                    $CustomerVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $CustomerVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $CustomerVideo;
        return json_encode($json);
    }
    //Delete Video
    public function deleteVideo(Request $request, $id){
        $tVideo = CustomerVideo::where(['LineID' => $id])->delete();
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
        $data = Customer::search($request);
//        $Uom = Uom::select('UomID', 'UomName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }

    public function delete($id){
        $Customer = Customer::find($id);
        $auth = Auth::user();
        if($Customer){
            if ($auth->UserType === 1) {
                $Customer->delete();
                CustomerCate::where('CustomerID', $id)->delete();
                CustomerPer::where('CustomerID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $isPer = CustomerPer::where([
                    'CustomerID' => $id,
                    'EmployeeID' => $auth->EmployeeID,
                    'Delete' => 1
                ])->exists();

                if($isPer || ($Customer->UserIDCreated === $auth->UserID)){
                    $Customer->delete();
                    CustomerCate::where('CustomerID', $id)->delete();
                    CustomerPer::where('CustomerID', $id)->delete();

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
            Customer::destroy($idArray);
            CustomerCate::whereIn('CustomerID', $idArray)->delete();
            CustomerPer::whereIn('CustomerID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $customersPer = CustomerPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('customer_per')
                    ->whereIn('CustomerID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('CustomerID');
            })->get();

            foreach ($customersPer as $customerPer) {
                if ($customerPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                Customer::destroy($idArray);
                CustomerCate::whereIn('CustomerID', $idArray)->delete();
                CustomerPer::whereIn('CustomerID', $idArray)->delete();

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
