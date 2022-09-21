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
use Module\Listing\Models\Department;
use Module\Listing\Models\Company;
use Module\Listing\Models\Employee;
use Module\Listing\Models\EmployeeCate;
use Module\Listing\Models\EmployeeCateList;
use Module\Listing\Models\EmployeeCateValue;
use Module\Listing\Models\EmployeeFile;
use Module\Listing\Models\EmployeeLink;
use Module\Listing\Models\EmployeeVideo;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\Position;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;

class EmployeeController extends Controller{

    public function list(Request $request){
        $data = Employee::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('employee', ['select' => ['EmployeeNo', 'EmployeeName'], 'conditions' => []]), 'employee.xlsx');
    }

    public function create(){
        $employee = Employee::all();
        if(old('EmployeeNo')){
            $auto = old('EmployeeNo');
        }else{
            $auto = AutoNo::genNewNumberNo('employee');
        }
        $employeeNameType = config('dataoptions.Employee.EmployeeNameDisplayType');
        $position = Position::where('Inactive','!=',1)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['employee'] = $employee;
        $json['data']['auto'] = $auto;
        $json['data']['employeeNameType'] = $employeeNameType;
        $json['data']['position'] = $position;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'EmployeeNo' => 'required|unique:employee',
            'EmployeeName'=> 'required|unique:employee',
        ],
            [
                'EmployeeNo.required' => 'Mã nhân viên không được để trống',
                'EmployeeNo.unique' => 'Mã nhân viên đã tồn tại',
                'EmployeeName.required' => 'Tên nhân viên không được để trống',
                'EmployeeName.unique' => 'Tên nhân viên đã tồn tại',
            ]
        );

        $autoModel = SysAutoNumber::where('NumberKey', 'employee')->first();
        AutoNo::storeValidate($validator, $request, 'employee', $autoModel, 'nhân viên');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $employee = new Employee([
            'EmployeeNo' => $request->EmployeeNo,
            'FirstName'=>$request->FirstName,
            'MiddleName'=>$request->MiddleName,
            'LastName'=>$request->LastName,
            'EmployeeName' => $request->EmployeeName,
            'CompanyID'=> $request->CompanyID,
            'CompanyName'=> $request->CompanyName,
            'DepartmentID'=>$request->DepartmentID,
            'DepartmentNo'=>$request->DepartmentNo,
            'DepartmentName'=>$request->DepartmentName,
            'PositionName'=>$request->PositionName,
            'BirthDay'=>$request->BirthDay,
            'CitizenIdNo'=>$request->CitizenIdNo,
            'CitizenIdDate'=>$request->CitizenIdDate,
            'CitizenIdAt'=>$request->CitizenIdAt,
            'OfficePhone'=>$request->OfficePhone,
            'HandPhone'=>$request->HandPhone,
            'FacebookID'=>$request->FacebookID,
            'TwitterID'=>$request->TwitterID,
            'SkypeID'=>$request->SkypeID,
            'ZaloID'=>$request->ZaloID,
            'Note' => $request->Note,
            'Email' => $request->Email,
            'Inactive' => $request->Inactive,
            'UserIDCreated'=> Auth::user()->UserID,
            'AuthorizedPerson'=> Auth::user()->UserID,
        ]);

        $employee->save();

        //employee cate
        $arrInsert = [];
        if($request->post('EmployeeCate')){
            foreach($request->post('EmployeeCate') as $key=> $item){
                if($item['CateID']) {
                    $arrInsert[] = [
                        'EmployeeID' => $employee->EmployeeID,
                        'CateID' => $item['CateID'],
                        'CateValue' => $item['CateValue']
                    ];
                }
            }

            DB::table('employee_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $employee->EmployeeID;

        return json_encode($json);
    }

    public function view($id){
        $data = Employee::find($id);
        if($data){
            $data->EmployeeNo = $data->Prefix.$data->EmployeeNo.$data->Suffix;
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $Company = Company::where('Inactive', '!=', 1)->get();
            $Group = SysUserGroup::all();

            $EmployeeCate = EmployeeCate::where('EmployeeID',$id)->get();
            $arrCate = [];
            foreach($EmployeeCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $EmployeeCateList = EmployeeCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $EmployeeCateValue = EmployeeCateValue::whereIn('CateID',$arrCate)->get();
            $EmployeeNameType = config('dataoptions.Employee.EmployeeNameDisplayType');
            $Position = Position::where('Inactive','!=',1)->get();
            $CompanyByEmployee = Company::find($data->CompanyID);
            if($CompanyByEmployee){
                $json['CompanyName'] = $CompanyByEmployee->CompanyName;
            }
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['Employee'] = $Employee;
            $json['Company'] = $Company;
            $json['EmployeeNameType'] = $EmployeeNameType;
            $json['Position'] = $Position;
            $json['data']['data'] = $data;
            $json['data']['EmployeeCate'] = $EmployeeCate;
            $json['data']['EmployeeCateList'] = $EmployeeCateList;
            $json['data']['EmployeeCateValue'] = $EmployeeCateValue;
            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id){
        $employee = Employee::all();
        $data = Employee::find($id);
        $employee = Employee::all();
        if(old('EmployeeNo')){
            $data->EmployeeNo = old('EmployeeNo');
        }else{
            $data->EmployeeNo = $data->Prefix.$data->EmployeeNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['employee'] = $employee;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $employee = Employee::find($id);
        if(!$employee){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($employee->EmployeeNo !== $request->EmployeeNo){
            $validator = Validator::make($request->all(), [
                'EmployeeNo' => 'required',
                'EmployeeName'=> 'required',
            ],
                [
                    'EmployeeNo.required' => 'Mã nhân viên không được để trống',
                    'EmployeeName.required' => 'Tên nhân viên không được để trống',
                ]);


            $autoModel = SysAutoNumber::where('NumberKey', 'employee')->first();
            AutoNo::UpdateValidate($validator, $request, $employee,'employee', $autoModel, 'nhân viên');

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $employee->EmployeeNo = $request->EmployeeNo;

        }
        $employee->FirstName = $request->FirstName;
        $employee->MiddleName = $request->MiddleName;
        $employee->LastName = $request->LastName;
        $employee->EmployeeName = $request->EmployeeName;
        $employee->CompanyID = $request->CompanyID;
        $employee->CompanyName = $request->CompanyName;
        $employee->DepartmentID = $request->DepartmentID;
        $employee->DepartmentNo = $request->DepartmentNo;
        $employee->DepartmentName = $request->DepartmentName;
        $employee->PositionName = $request->PositionName;
        $employee->BirthDay = $request->BirthDay;
        $employee->CitizenIdNo = $request->CitizenIdNo;
        $employee->CitizenIdDate = $request->CitizenIdDate;
        $employee->CitizenIdAt = $request->CitizenIdAt;
        $employee->OfficePhone = $request->OfficePhone;
        $employee->HandPhone = $request->HandPhone;
        $employee->FacebookID = $request->FacebookID;
        $employee->TwitterID = $request->TwitterID;
        $employee->SkypeID = $request->SkypeID;
        $employee->Note = $request->Note;
        $employee->Email = $request->Email;
        $employee->Inactive = $request->Inactive;
        $employee->save();

        // employee cate
        if($request->post('EmployeeCate')){
            $EmployeeCate = $request->post('EmployeeCate');
            EmployeeCate::where('EmployeeID',$id)->delete();
            $arrInsert = [];
            foreach($EmployeeCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "EmployeeID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('employee_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;


    }

    public function delete($id){
        $data = Employee::find($id);
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        Employee::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }


    public function getCate(){
        $employeeCateList = EmployeeCateList::where('Inactive',0)->get();
        $employeeCateValue = EmployeeCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['EmployeeCateList'] = $employeeCateList;
        $json['data']['EmployeeCateValue'] = $employeeCateValue;
        return json_encode($json);
    }

    public function getLink($id){
        $data = EmployeeLink::where('EmployeeID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function updateLink(Request $request){
        $LinkArray = $request->post('EmployeeLink');
        $id = $request->post('EmployeeID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'EmployeeID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        EmployeeLink::where(['EmployeeID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('employee_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getFile($id){
        $data = EmployeeFile::where('EmployeeID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $EmployeeFile = EmployeeFile::find($request->LineID);
        }else{
            $EmployeeFile = new EmployeeFile();
        }
        $EmployeeFile->FileName = $request->FileName;
        $EmployeeFile->FileNameRoot = $request->FileName;
        $EmployeeFile->Description = $request->Description;
        $EmployeeFile->FileType = $request->FileType;
        $EmployeeFile->FileSize = $request->FileSize;
        $EmployeeFile->EmployeeID = $id;
        $EmployeeFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $EmployeeFile->UserModified = Auth::user()->username;
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

                    $EmployeeFile->Link = $linkFileAttach;
                    $EmployeeFile->DateModifiedRoot = $fileModel->DateModified;
                    $EmployeeFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $EmployeeFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $EmployeeFile;
        return json_encode($json);
    }

    public function deleteFile(Request $request, $id){
        $dFile = EmployeeFile::where(['LineID' => $id])->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }

    //download File
    public function downloadFile(Request $request, $id){
        $file = FileModel::find($id);
        return response()->download(public_path($file->Link));
    }

    //download all file
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
        $files = EmployeeFile::where('EmployeeID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $employee = Employee::find($id);
            $zipName = '';
            if ($employee) {
                $zipName = IjString::CreateSlug($employee->EmployeeName, '-');
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

    public function getVideo(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $EmployeeVideo = EmployeeVideo::where('EmployeeID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $EmployeeVideo;

        return json_encode($json);
    }

    //Upload Video
    public function uploadVideo(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $EmployeeVideo = EmployeeVideo::find($request->LineID);
        }else{
            $EmployeeVideo = new EmployeeVideo();
        }
        $EmployeeVideo->VideoName = $request->VideoName;
        $EmployeeVideo->Description = $request->Description;
        $EmployeeVideo->VideoType = $request->VideoType;
        $EmployeeVideo->VideoSize = $request->VideoSize;
        $EmployeeVideo->EmployeeID = $id;
        $EmployeeVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $EmployeeVideo->UserModified = Auth::user()->username;
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

                    $EmployeeVideo->Link = $linkFileAttach;
                    $EmployeeVideo->DateModifiedRoot = $fileModel->DateModified;
                    $EmployeeVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $EmployeeVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $EmployeeVideo;
        return json_encode($json);
    }
    //Delete Video
    public function deleteVideo(Request $request, $id){
        $tVideo = EmployeeVideo::where(['LineID' => $id])->delete();
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
        $data = Employee::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }

    public function getUser(Request $request){
        $query = DB::table('sys_user');
        $query->whereNull('EmployeeID');
        if($request->post('search')) {
            $query->where('FullName', 'like', '%' . $request->search . '%');
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }

        $data = $query->paginate($per_page);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

}
