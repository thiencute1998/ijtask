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
use Module\Listing\Models\Direction;
use Module\Listing\Models\DirectionCate;
use Module\Listing\Models\DirectionCateList;
use Module\Listing\Models\DirectionCateValue;
use Module\Listing\Models\DirectionFile;
use Module\Listing\Models\DirectionPer;
use Module\Listing\Models\Company;
use Module\Listing\Models\Employee;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysStatusItem;
use Module\SysAdmin\Models\SysUserGroup;

class DirectionController extends Controller{

    public function list(Request $request){
        $data = Direction::search($request);
        $StatusItem = SysStatusItem::where('StatusID',2)->get();
        $ManagementLevelOption = config('dataoptions.ManagementLevelOption');
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        $json['StatusItem'] = $StatusItem;
        $json['ManagementLevelOption'] = $ManagementLevelOption;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('direction', ['select' => ['DirectionNo', 'DirectionName'], 'conditions' => []]), 'duan.xlsx');
    }

    public function create(){
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'DirectionNo' => 'required|unique:direction',
            'DirectionName'=> 'required',
        ],
            [
                'DirectionNo.required' => 'Mã chỉ thị không được để trống',
                'DirectionNo.unique' => 'Mã chỉ thị đã tồn tại',
                'DirectionName.required' => 'Tên chỉ thị không được để trống',
            ]
        );

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        if($request->ParentID != ''){
            $leveParentID = Direction::find($request->ParentID)->Level;
            $level = $leveParentID+ 1;
        } else {
            $level = 1;
        }

        if($request->post('DirectionDate')){
            $request['DirectionDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('DirectionDate'))));
        }
        if($request->post('ClosedDate')){
            $request['ClosedDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('ClosedDate'))));
        }
        $array_custom = [
            'Level'=> $level,
            'Closed' => ($request->Closed) ? 1 : 0,
            'AccessType'=> ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated'=> Auth::user()->UserID,
            'AuthorizedPerson'=> Auth::user()->UserID
        ];
        $request->merge($array_custom);
        $direction = Direction::create($request->all());
//
        $DirectionPer = new DirectionPer([
            'DirectionID' => $direction->DirectionID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $DirectionPer->save();

        //direction cate
        $arrInsert = [];
        if($request->post('DirectionCate')){
            foreach($request->post('DirectionCate') as $key=> $item){
                if($item['CateID']) {
                    $arrInsert[] = [
                        'DirectionID' => $direction->DirectionID,
                        'CateID' => $item['CateID'],
                        'CateValue' => $item['CateValue']
                    ];
                }
            }

            DB::table('direction_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $direction->DirectionID;

        return json_encode($json);
    }

    public function view($id){
        $data = Direction::find($id);
        if($data){
            $data->DirectionNo = $data->Prefix.$data->DirectionNo.$data->Suffix;
            $arrParent = [];
            if($data->ParentID){
                $parent = Direction::find($data->ParentID);
                if(isset($parent->DirectionNo) && isset($parent->DirectionName)){
                    $arrParent = [
                        "ParentNo" => $parent->DirectionNo,
                        "ParentName" => $parent->DirectionName
                    ];
                }
            }
            $DirectionPerEmployee = DirectionPer::where([
                'DirectionID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID
            ])->first();
            $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

            if(!$DirectionPerEmployee){
                if($data->AccessType == 2){
                    //Nếu tài liệu là công khai
                    $DirectionPerEmployee = new DirectionPer([
                        'DirectionID' => $id,
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
                else{
                    //Nếu tài liệu không phải công khai
                    if($EmployeeLogin->UserID != $data->UserIDCreated && $EmployeeLogin->UserID != $data->AuthorizedPerson){
                        $json['status'] = 3;
                        $json['msg'] = 'Bạn không có quyền truy cập!';
                        return json_encode($json);
                    }else{
                        $DirectionPerEmployee = new DirectionPer([
                            'DirectionID' => $id,
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
            }
            else{
                if($EmployeeLogin->UserID == $data->UserIDCreated || $EmployeeLogin->UserID == $data->AuthorizedPerson){
                    $DirectionPerEmployee = new DirectionPer([
                        'DirectionID' => $id,
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

            if ($DirectionPerEmployee) {
                if (strpos(',' . $DirectionPerEmployee->AccessField . ',', ',all,')) {
                    $DirectionPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $DirectionPerEmployee->EditField . ',', ',all,')){
                    $DirectionPerEmployee->EditField = 'all';
                }
            }

            $DirectionPer = DirectionPer::where('DirectionID', '=', $id)->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $Company = Company::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $DirectionCate = DirectionCate::where('DirectionID',$id)->get();
            $arrCate = [];
            foreach($DirectionCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $DirectionCateList = DirectionCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $DirectionCateValue = DirectionCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['Company'] = $Company;
            $json['Employee'] = $Employee;
            $json['DirectionPer'] = $DirectionPer;
            $json['DirectionPerEmployee'] = $DirectionPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['Parent'] = $arrParent;
            $json['data']['DirectionCate'] = $DirectionCate;
            $json['data']['DirectionCateList'] = $DirectionCateList;
            $json['data']['DirectionCateValue'] = $DirectionCateValue;
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
        $data = Direction::find($id);
        $direction = Direction::all();
        if(old('DirectionNo')){
            $data->DirectionNo = old('DirectionNo');
        }else{
            $data->DirectionNo = $data->Prefix.$data->DirectionNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['direction'] = $direction;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $direction = Direction::find($id);
        if(!$direction){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($direction->DirectionNo !== $request->DirectionNo){
            $validator = Validator::make($request->all(), [
                'DirectionNo' => 'required',
                'DirectionName'=> 'required',
            ],
                [
                    'DirectionNo.required' => 'Mã chỉ thị không được để trống',
                    'DirectionName.required' => 'Tên chỉ thị không được để trống',
                ]);


            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            if($request->ParentID != ''){
                $leveParentID = Direction::find($request->ParentID)->Level;
                $level = $leveParentID+ 1;
            } else {
                $level = 1;
            }
            $request['Level'] = $level;
        }

        if($request->post('DirectionDate')){
            $request['DirectionDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('DirectionDate'))));
        }
        if($request->post('ClosedDate')){
            $request['ClosedDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('ClosedDate'))));
        }
        $request['Closed'] = ($request->Closed) ? 1 : 0;
        $request['AccessType'] = ($request->AccessType) ? $request->AccessType : 1;

        $direction->fill($request->all())->save();
        // direction cate
        if($request->post('DirectionCate')){
            $DirectionCate = $request->post('DirectionCate');
            DirectionCate::where('DirectionID',$id)->delete();
            $arrInsert = [];
            foreach($DirectionCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "DirectionID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('direction_cate')->insert($arrInsert);
        }
        else{
            DirectionCate::where('DirectionID',$id)->delete();
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;


    }

    public function delete($id){
        $data = Direction::find($id);
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
        Direction::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $DirectionID = $request->post('DirectionID');
        $DirectionPer = $request->post('DirectionPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($DirectionPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'DirectionID' => $DirectionID,
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
        DirectionPer::where(['DirectionID' => $DirectionID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('direction_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $DirectionID;
        return json_encode($json);
    }

    public function getCate(){
        $directionCateList = DirectionCateList::where('Inactive',0)->get();
        $directionCateValue = DirectionCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['DirectionCateList'] = $directionCateList;
        $json['data']['DirectionCateValue'] = $directionCateValue;
        return json_encode($json);
    }

    public function getFile($id){
        $data = DirectionFile::where('DirectionID',$id)->get();
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
            $DirectionFile = DirectionFile::find($request->LineID);
        }else{
            $DirectionFile = new DirectionFile();
        }
        $DirectionFile->FileName = $request->FileName;
        $DirectionFile->FileNameRoot = $request->FileName;
        $DirectionFile->Description = $request->Description;
        $DirectionFile->FileType = $request->FileType;
        $DirectionFile->FileSize = $request->FileSize;
        $DirectionFile->DirectionID = $id;
        $DirectionFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $DirectionFile->UserModified = Auth::user()->username;
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

                    $DirectionFile->Link = $linkFileAttach;
                    $DirectionFile->DateModifiedRoot = $fileModel->DateModified;
                    $DirectionFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $DirectionFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $DirectionFile;
        return json_encode($json);
    }

    public function deleteFile(Request $request, $id){
        $dFile = DirectionFile::where(['LineID' => $id])->delete();
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
        $files = DirectionFile::where('DirectionID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $direction = Direction::find($id);
            $zipName = '';
            if ($direction) {
                $zipName = IjString::CreateSlug($direction->DirectionName, '-');
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

    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = Direction::search($request);
//        $Uom = Uom::select('UomID', 'UomName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }


}
