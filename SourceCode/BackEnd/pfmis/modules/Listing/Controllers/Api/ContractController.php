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
use Module\Listing\Models\Contract;
use Module\Listing\Models\ContractCate;
use Module\Listing\Models\ContractCateList;
use Module\Listing\Models\ContractCateValue;
use Module\Listing\Models\ContractFile;
use Module\Listing\Models\ContractLink;
use Module\Listing\Models\ContractPer;
use Module\Listing\Models\ContractVideo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;

class ContractController extends Controller{

    public function list(Request $request){
        $data = Contract::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('contract', ['select' => ['ContractNo', 'ContractName'], 'conditions' => []]), 'hopdong.xlsx');
    }

    public function create(){
        $contract = Contract::all();
        $employee = Employee::all();
        if(old('ContractNo')){
            $auto = old('ContractNo');
        }else{
            $auto = AutoNo::genNewNumberNo('contract');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['contract'] = $contract;
        $json['data']['employee'] = $employee;
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'ContractNo' => 'required|unique:contract',
            'ContractName'=> 'required|unique:contract',
        ],
            [
                'ContractNo.required' => 'Mã hợp đồng không được để trống',
                'ContractNo.unique' => 'Mã hợp đồng đã tồn tại',
                'ContractName.required' => 'Tên hợp đồng không được để trống',
                'ContractName.unique' => 'Tên hợp đồng đã tồn tại',
            ]
        );

        $autoModel = SysAutoNumber::where('NumberKey', 'contract')->first();
        AutoNo::storeValidate($validator, $request, 'contract', $autoModel, 'hợp đồng');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $contract = new Contract([
            'ContractNo' => $request->ContractNo,
            'ContractName' => $request->ContractName,
            'ContractDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $request->ContractDate))),
            'EffectiveDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $request->EffectiveDate))),
            'FinishDate' => date('Y-m-d H:i', strtotime(str_replace('/', '-', $request->FinishDate))),
            'ContractAmount' => $request->ContractAmount,
            'EmployeeID' => $request->EmployeeID,
            'EmployeeName' => $request->EmployeeName,
            'ProjectID' => $request->ProjectID,
            'ProjectNo' => $request->ProjectNo,
            'ProjectName'=> $request->ProjectName,
            'VendorID'=> $request->VendorID,
            'VendorName'=> $request->VendorName,
            'Note'=> $request->Note,
            'Inactive'=> $request->Inactive,
            'AccessType'=> ($request->AccessType) ? $request->AccessType : 1,
            'UserIDCreated'=> Auth::user()->UserID,
            'AuthorizedPerson'=> Auth::user()->UserID,
        ]);

        $contract->save();

        $ContractPer = new ContractPer([
            'ContractID' => $contract->ContractID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $ContractPer->save();

        //contract cate
        $arrInsert = [];
        if($request->post('ContractCate')){
            foreach($request->post('ContractCate') as $key=> $item){
                if($item['CateID']) {
                    $arrInsert[] = [
                        'ContractID' => $contract->ContractID,
                        'CateID' => $item['CateID'],
                        'CateValue' => $item['CateValue']
                    ];
                }
            }

            DB::table('contract_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $contract->ContractID;

        return json_encode($json);
    }

    public function view($id){
        $data = Contract::find($id);
        if($data){
            $data->ContractNo = $data->Prefix.$data->ContractNo.$data->Suffix;
            $ContractPerEmployee = ContractPer::where([
                'ContractID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID
            ])->first();
            $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

            if(!$ContractPerEmployee){
                if($data->AccessType == 2){
                    //Nếu tài liệu là công khai
                    $ContractPerEmployee = new ContractPer([
                        'ContractID' => $id,
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
                        $ContractPerEmployee = new ContractPer([
                            'ContractID' => $id,
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
                    $ContractPerEmployee = new ContractPer([
                        'ContractID' => $id,
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

            if ($ContractPerEmployee) {
                if (strpos(',' . $ContractPerEmployee->AccessField . ',', ',all,')) {
                    $ContractPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $ContractPerEmployee->EditField . ',', ',all,')){
                    $ContractPerEmployee->EditField = 'all';
                }
            }
            $EmployeeCreated = Employee::where('UserID','=',$data->UserIDCreated)->first();

            $ContractPer = ContractPer::where('ContractID', '=', $id)->where(function($q) use($EmployeeLogin,$EmployeeCreated){
                $q->where('EmployeeID','!=',$EmployeeLogin->EmployeeID)
                    ->orWhere('EmployeeID','!=',$EmployeeCreated->EmployeeID);
            })->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $Contract = Contract::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $ContractCate = ContractCate::where('ContractID',$id)->get();
            $arrCate = [];
            foreach($ContractCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $ContractCateList = ContractCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $ContractCateValue = ContractCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['Contract'] = $Contract;
            $json['Employee'] = $Employee;
            $json['ContractPer'] = $ContractPer;
            $json['ContractPerEmployee'] = $ContractPerEmployee;
            $json['data']['data'] = $data;
            $json['data']['ContractCate'] = $ContractCate;
            $json['data']['ContractCateList'] = $ContractCateList;
            $json['data']['ContractCateValue'] = $ContractCateValue;
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
        $data = Contract::find($id);
        $contract = Contract::all();
        if(old('ContractNo')){
            $data->ContractNo = old('ContractNo');
        }else{
            $data->ContractNo = $data->Prefix.$data->ContractNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['contract'] = $contract;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $contract = Contract::find($id);
        if(!$contract){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($contract->ContractNo !== $request->ContractNo){
            $validator = Validator::make($request->all(), [
                'ContractNo' => 'required',
                'ContractName'=> 'required',
            ],
                [
                    'ContractNo.required' => 'Mã hợp đồng không được để trống',
                    'ContractName.required' => 'Tên hợp đồng không được để trống',
                ]);


            $autoModel = SysAutoNumber::where('NumberKey', 'contract')->first();
            AutoNo::UpdateValidate($validator, $request, $contract,'contract', $autoModel, 'hợp đồng');

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $contract->ContractNo = $request->ContractNo;
        }
        $contract->ContractName = $request->ContractName;
        $contract->Note = $request->Note;
        $contract->ContractDate = $request->ContractDate;
        $contract->EffectiveDate = $request->EffectiveDate;
        $contract->FinishDate = $request->FinishDate;
        $contract->ContractAmount = $request->ContractAmount;
        $contract->EmployeeID = $request->EmployeeID;
        $contract->EmployeeName = $request->EmployeeName;
        $contract->ProjectID = $request->ProjectID;
        $contract->ProjectNo = $request->ProjectNo;
        $contract->ProjectName = $request->ProjectName;
        $contract->VendorID = $request->VendorID;
        $contract->VendorName = $request->VendorName;
        $contract->ProjectNo = $request->ProjectNo;
        $contract->ProjectName = $request->ProjectName;
        $contract->VendorID = $request->VendorID;
        $contract->AccessType = ($request->AccessType) ? $request->AccessType : 1;
        $contract->Inactive = $request->Inactive;
        $contract->save();

        // contract cate
        if($request->post('ContractCate')){
            $ContractCate = $request->post('ContractCate');
            ContractCate::where('ContractID',$id)->delete();
            $arrInsert = [];
            foreach($ContractCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "ContractID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('contract_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;


    }

    public function delete($id){
        $data = Contract::find($id);
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
        Contract::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $ContractID = $request->post('ContractID');
        $ContractPer = $request->post('ContractPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($ContractPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'ContractID' => $ContractID,
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
        ContractPer::where(['ContractID' => $ContractID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('contract_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ContractID;
        return json_encode($json);
    }

    public function getCate(){
        $contractCateList = ContractCateList::where('Inactive',0)->get();
        $contractCateValue = ContractCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['ContractCateList'] = $contractCateList;
        $json['data']['ContractCateValue'] = $contractCateValue;
        return json_encode($json);
    }

    public function getLink($id){
        $data = ContractLink::where('ContractID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function updateLink(Request $request){
        $LinkArray = $request->post('ContractLink');
        $id = $request->post('ContractID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'ContractID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        ContractLink::where(['ContractID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('contract_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getFile($id){
        $data = ContractFile::where('ContractID',$id)->get();
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
            $ContractFile = ContractFile::find($request->LineID);
        }else{
            $ContractFile = new ContractFile();
        }
        $ContractFile->FileName = $request->FileName;
        $ContractFile->FileNameRoot = $request->FileName;
        $ContractFile->Description = $request->Description;
        $ContractFile->FileType = $request->FileType;
        $ContractFile->FileSize = $request->FileSize;
        $ContractFile->ContractID = $id;
        $ContractFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $ContractFile->UserModified = Auth::user()->username;
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

                    $ContractFile->Link = $linkFileAttach;
                    $ContractFile->DateModifiedRoot = $fileModel->DateModified;
                    $ContractFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $ContractFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ContractFile;
        return json_encode($json);
    }

    public function deleteFile(Request $request, $id){
        $dFile = ContractFile::where(['LineID' => $id])->delete();
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
        $files = ContractFile::where('ContractID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $contract = Contract::find($id);
            $zipName = '';
            if ($contract) {
                $zipName = IjString::CreateSlug($contract->ContractName, '-');
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
        $ContractVideo = ContractVideo::where('ContractID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $ContractVideo;

        return json_encode($json);
    }

    //Upload Video
    public function uploadVideo(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $ContractVideo = ContractVideo::find($request->LineID);
        }else{
            $ContractVideo = new ContractVideo();
        }
        $ContractVideo->VideoName = $request->VideoName;
        $ContractVideo->Description = $request->Description;
        $ContractVideo->VideoType = $request->VideoType;
        $ContractVideo->VideoSize = $request->VideoSize;
        $ContractVideo->ContractID = $id;
        $ContractVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $ContractVideo->UserModified = Auth::user()->username;
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

                    $ContractVideo->Link = $linkFileAttach;
                    $ContractVideo->DateModifiedRoot = $fileModel->DateModified;
                    $ContractVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $ContractVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ContractVideo;
        return json_encode($json);
    }
    //Delete Video
    public function deleteVideo(Request $request, $id){
        $tVideo = ContractVideo::where(['LineID' => $id])->delete();
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
        $data = Contract::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }

}
