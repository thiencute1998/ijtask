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
use Module\Listing\Models\Project;
use Module\Listing\Models\ProjectCate;
use Module\Listing\Models\ProjectCateList;
use Module\Listing\Models\ProjectCateValue;
use Module\Listing\Models\ProjectFile;
use Module\Listing\Models\ProjectLink;
use Module\Listing\Models\ProjectPer;
use Module\Listing\Models\ProjectVideo;
use Module\Listing\Models\Company;
use Module\Listing\Models\Employee;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysStatus;
use Module\SysAdmin\Models\SysStatusItem;
use Module\SysAdmin\Models\SysUserGroup;

class ProjectController extends Controller{

    public function list(Request $request){
        $data = Project::search($request);
        $data->load('doc');
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
        return Excel::download(new ModelExport('project', ['select' => ['ProjectNo', 'ProjectName'], 'conditions' => []]), 'duan.xlsx');
    }

    public function create(){
        $project = Project::all();
        $employee = Employee::all();
        if(old('ProjectNo')){
            $auto = old('ProjectNo');
        }else{
            $auto = AutoNo::genNewNumberNo('project');
        }
        $StatusItem = SysStatusItem::where('StatusID',2)->get();
        $ManagementLevelOption = config('dataoptions.Project.ManagementLevelOption');
        $MPeriodOption = $this->getMperiod();
        $GroupOption = config('dataoptions.Project.Group');
        $StatusCate = ProjectCateValue::where('CateNo','=','009.01')->first();
        $CateDefault = ProjectCateValue::where('CateNo','a')->where('CateValue',1)->first();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['project'] = $project;
        $json['data']['employee'] = $employee;
        $json['data']['auto'] = $auto;
        $json['data']['StatusItem'] = $StatusItem;
        $json['data']['MPeriodOption'] = $MPeriodOption;
        $json['data']['ManagementLevelOption'] = $ManagementLevelOption;
        $json['data']['GroupOption'] = $GroupOption;
        $json['data']['StatusCate'] = $StatusCate;
        $json['data']['CateDefault'] = $CateDefault;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'ProjectNo' => 'required|unique:project',
            'ProjectName'=> 'required|unique:project',
        ],
            [
                'ProjectNo.required' => 'Mã dự án không được để trống',
                'ProjectNo.unique' => 'Mã dự án đã tồn tại',
                'ProjectName.required' => 'Tên dự án không được để trống',
                'ProjectName.unique' => 'Tên dự án đã tồn tại',
            ]
        );

        $autoModel = SysAutoNumber::where('NumberKey', 'project')->first();
        AutoNo::storeValidate($validator, $request, 'project', $autoModel, 'dự án');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        if($request->post('TabmisDate')){
            $request['TabmisDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('TabmisDate'))));
        }
        if($request->post('ExpectedStartDate')){
            $request['ExpectedStartDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('ExpectedStartDate'))));
        }
        if($request->post('ExpectedFinishDate')){
            $request['ExpectedFinishDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('ExpectedFinishDate'))));
        }
        if($request->post('ExpectedHandoverDate')){
            $request['ExpectedHandoverDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('ExpectedHandoverDate'))));
        }
        if($request->post('StartedDate')){
            $request['StartedDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('StartedDate'))));
        }
        if($request->post('HandoverDate')){
            $request['HandoverDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('HandoverDate'))));
        }
        if($request->post('FinishedDate')){
            $request['FinishedDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('FinishedDate'))));
        }
        if($request->post('SettlementDate')){
            $request['SettlementDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('SettlementDate'))));
        }
        if($request->post('ClosedDate')){
            $request['ClosedDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('ClosedDate'))));
        }
        if($request->post('InvestdocDate')){
            $request['InvestdocDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('InvestdocDate'))));
        }
        if($request->post('PacttdocDate')){
            $request['PacttdocDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('PacttdocDate'))));
        }
        $array_custom = [
                'Level'=> empty($request->ParentID)? 1 : $validator->ParentLevel + 1,
                'AccessType'=> ($request->AccessType) ? $request->AccessType : 1,
                'UserIDCreated'=> Auth::user()->UserID,
                'AuthorizedPerson'=> Auth::user()->UserID
        ];
        $request->merge($array_custom);
        $project = Project::create($request->all());
//
        $ProjectPer = new ProjectPer([
            'ProjectID' => $project->ProjectID,
            'EmployeeID' => Auth::user()->EmployeeID,
            'EmployeeName' => Auth::user()->EmployeeName,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1
        ]);
        $ProjectPer->save();

        //project cate
        $arrInsert = [];
        if($request->post('ProjectCate')){
            foreach($request->post('ProjectCate') as $key=> $item){
                if($item['CateID']) {
                    $arrInsert[] = [
                        'ProjectID' => $project->ProjectID,
                        'CateID' => $item['CateID'],
                        'CateNo' => isset($item['CateNo']) ? $item['CateNo'] : null,
                        'CateName' => $item['CateName'],
                        'CateValue' => $item['CateValue'],
                    ];
                }
            }

            DB::table('project_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $project->ProjectID;

        return json_encode($json);
    }

    public function view($id){
        $data = Project::find($id);
        if($data){
            $data->ProjectNo = $data->Prefix.$data->ProjectNo.$data->Suffix;
            $arrParent = [];
            if($data->ParentID){
                $parent = Project::find($data->ParentID);
                if(isset($parent->ProjectNo) && isset($parent->ProjectName)){
                    $arrParent = [
                        "ParentID" => $data->ParentID,
                        "ParentNo" => $parent->ProjectNo,
                        "ParentName" => $parent->ProjectName
                    ];
                }
            }
            $ProjectPerEmployee = ProjectPer::where([
                'ProjectID' => $id,
                'EmployeeID' => Auth::user()->EmployeeID
            ])->first();
            $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();

            if(!$ProjectPerEmployee){
                if($data->AccessType == 2){
                    //Nếu tài liệu là công khai
                    $ProjectPerEmployee = new ProjectPer([
                        'ProjectID' => $id,
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
                        $ProjectPerEmployee = new ProjectPer([
                            'ProjectID' => $id,
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
                    $ProjectPerEmployee = new ProjectPer([
                        'ProjectID' => $id,
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

            if ($ProjectPerEmployee) {
                if (strpos(',' . $ProjectPerEmployee->AccessField . ',', ',all,')) {
                    $ProjectPerEmployee->AccessField = 'all';
                }
                if(strpos(',' . $ProjectPerEmployee->EditField . ',', ',all,')){
                    $ProjectPerEmployee->EditField = 'all';
                }
            }

            $ProjectPer = ProjectPer::where('ProjectID', '=', $id)->groupBy('EmployeeID')->get();
            $Employee = Employee::where('Inactive', '!=', 1)->get();
            $Company = Company::where('Inactive', '!='

                , 1)->get();
            $Group = SysUserGroup::all();

            $ProjectCate = ProjectCate::where('ProjectID',$id)->get();
            $arrCate = [];
            foreach($ProjectCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $ProjectCateList = ProjectCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $ProjectCateValue = ProjectCateValue::whereIn('CateID',$arrCate)->get();
            $ManagementLevelOption = config('dataoptions.ManagementLevelOption');
            $StatusItem = SysStatusItem::where('StatusID',2)->get();
            $MPeriodOption = $this->getMperiod();
            $GroupOption = config('dataoptions.Project.Group');
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['Group'] = $Group;
            $json['Company'] = $Company;
            $json['Employee'] = $Employee;
            $json['ProjectPer'] = $ProjectPer;
            $json['ProjectPerEmployee'] = $ProjectPerEmployee;
            $json['ManagementLevelOption'] = $ManagementLevelOption;
            $json['MPeriodOption'] = $MPeriodOption;
            $json['StatusItem'] = $StatusItem;
            $json['GroupOption'] = $GroupOption;
            $json['data']['data'] = $data;
            $json['data']['Parent'] = $arrParent;
            $json['data']['ProjectCate'] = $ProjectCate;
            $json['data']['ProjectCateList'] = $ProjectCateList;
            $json['data']['ProjectCateValue'] = $ProjectCateValue;
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
        $data = Project::find($id);
        $project = Project::all();
        if(old('ProjectNo')){
            $data->ProjectNo = old('ProjectNo');
        }else{
            $data->ProjectNo = $data->Prefix.$data->ProjectNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['project'] = $project;
        $json['data']['employee'] = $employee;
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $project = Project::find($id);
        if(!$project){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($project->ProjectNo !== $request->ProjectNo){
            $validator = Validator::make($request->all(), [
                'ProjectNo' => 'required',
                'ProjectName'=> 'required',
            ],
                [
                    'ProjectNo.required' => 'Mã dự án không được để trống',
                    'ProjectName.required' => 'Tên dự án không được để trống',
                ]);


            $autoModel = SysAutoNumber::where('NumberKey', 'project')->first();
            AutoNo::UpdateValidate($validator, $request, $project,'project', $autoModel, 'dự án');

            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $array_custom = [
                'Level'=> empty($request->ParentID)? 1 : $validator->ParentLevel + 1,
                'AccessType'=> ($request->AccessType) ? $request->AccessType : 1,
            ];
        }
        else{
            $array_custom = [
                'AccessType'=> ($request->AccessType) ? $request->AccessType : 1,
            ];
        }

        if($request->post('TabmisDate')){
            $request['TabmisDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('TabmisDate'))));
        }
        if($request->post('ExpectedStartDate')){
            $request['ExpectedStartDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('ExpectedStartDate'))));
        }
        if($request->post('ExpectedFinishDate')){
            $request['ExpectedFinishDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('ExpectedFinishDate'))));
        }
        if($request->post('ExpectedHandoverDate')){
            $request['ExpectedHandoverDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('ExpectedHandoverDate'))));
        }
        if($request->post('StartedDate')){
            $request['StartedDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('StartedDate'))));
        }
        if($request->post('HandoverDate')){
            $request['HandoverDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('HandoverDate'))));
        }
        if($request->post('FinishedDate')){
            $request['FinishedDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('FinishedDate'))));
        }
        if($request->post('SettlementDate')){
            $request['SettlementDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('SettlementDate'))));
        }
        if($request->post('ClosedDate')){
            $request['ClosedDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('ClosedDate'))));
        }
        if($request->post('InvestdocDate')){
            $request['InvestdocDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('InvestdocDate'))));
        }
        if($request->post('PacttdocDate')){
            $request['PacttdocDate'] = date('Y-m-d',strtotime(str_replace('/','-',$request->post('PacttdocDate'))));
        }

        $request->merge($array_custom);

        $project->fill($request->all())->save();
        // project cate
        if($request->post('ProjectCate')){
            $ProjectCate = $request->post('ProjectCate');
            ProjectCate::where('ProjectID',$id)->delete();
            $arrInsert = [];
            foreach($ProjectCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "ProjectID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null,
                        "CateNo" => isset($cate['CateNo']) ? $cate['CateNo'] : null,
                    ];
                }
            }
            if(count($arrInsert)) DB::table('project_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;


    }

    public function delete($id){
        $data = Project::find($id);
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
        Project::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $ProjectID = $request->post('ProjectID');
        $ProjectPer = $request->post('ProjectPer');
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($ProjectPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'ProjectID' => $ProjectID,
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
        ProjectPer::where(['ProjectID' => $ProjectID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('project_per')->insert($arrInsert);
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ProjectID;
        return json_encode($json);
    }

    public function getCate(){
        $projectCateList = ProjectCateList::whereNull('ParentNo')->orWhere('ParentNo','!=','009')->get();
        $projectCateValue = ProjectCateValue::all();
        $statusCateListOption = ProjectCateList::where('ParentNo','=','009')->get();
        $arrStatusCateList = [];
        foreach ($statusCateListOption as $item){
            $arrStatusCateList[] = $item->CateID;
        }
        $statusCateValueOption = ProjectCateValue::whereIn('CateID',$arrStatusCateList)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['ProjectCateList'] = $projectCateList;
        $json['data']['ProjectCateValue'] = $projectCateValue;
        $json['data']['StatusCateListOption'] = $statusCateListOption;
        $json['data']['StatusCateValueOption'] = $statusCateValueOption;
        return json_encode($json);
    }

    public function getLink($id){
        $data = ProjectLink::where('ProjectID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function updateLink(Request $request){
        $LinkArray = $request->post('ProjectLink');
        $id = $request->post('ProjectID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'ProjectID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        ProjectLink::where(['ProjectID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('project_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function getFile($id){
        $data = ProjectFile::where('ProjectID',$id)->get();
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
            $ProjectFile = ProjectFile::find($request->LineID);
        }else{
            $ProjectFile = new ProjectFile();
        }
        $ProjectFile->FileName = $request->FileName;
        $ProjectFile->FileNameRoot = $request->FileName;
        $ProjectFile->Description = $request->Description;
        $ProjectFile->FileType = $request->FileType;
        $ProjectFile->FileSize = $request->FileSize;
        $ProjectFile->ProjectID = $id;
        $ProjectFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $ProjectFile->UserModified = Auth::user()->username;
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

                    $ProjectFile->Link = $linkFileAttach;
                    $ProjectFile->DateModifiedRoot = $fileModel->DateModified;
                    $ProjectFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $ProjectFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ProjectFile;
        return json_encode($json);
    }

    public function deleteFile(Request $request, $id){
        $dFile = ProjectFile::where(['LineID' => $id])->delete();
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
        $files = ProjectFile::where('ProjectID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $project = Project::find($id);
            $zipName = '';
            if ($project) {
                $zipName = IjString::CreateSlug($project->ProjectName, '-');
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
        $ProjectVideo = ProjectVideo::where('ProjectID', '=', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $ProjectVideo;

        return json_encode($json);
    }

    //Upload Video
    public function uploadVideo(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $ProjectVideo = ProjectVideo::find($request->LineID);
        }else{
            $ProjectVideo = new ProjectVideo();
        }
        $ProjectVideo->VideoName = $request->VideoName;
        $ProjectVideo->Description = $request->Description;
        $ProjectVideo->VideoType = $request->VideoType;
        $ProjectVideo->VideoSize = $request->VideoSize;
        $ProjectVideo->ProjectID = $id;
        $ProjectVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $ProjectVideo->UserModified = Auth::user()->username;
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

                    $ProjectVideo->Link = $linkFileAttach;
                    $ProjectVideo->DateModifiedRoot = $fileModel->DateModified;
                    $ProjectVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $ProjectVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $ProjectVideo;
        return json_encode($json);
    }
    //Delete Video
    public function deleteVideo(Request $request, $id){
        $tVideo = ProjectVideo::where(['LineID' => $id])->delete();
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
        $data = Project::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }

    public function getMperiod(){
        $StartYearOfMPeriod = config('dataoptions.Project.StartYearOfMPeriod');
        $NumbersOfMPeriod = config('dataoptions.Project.NumbersOfMPeriod');
        $MPeriodDistance = config('dataoptions.Project.MPeriodDistance');
        $MPeriodOption = [];
        for($i = 0; $i < $NumbersOfMPeriod ; $i++){
            $start = $StartYearOfMPeriod + $MPeriodDistance * $i - $i;
            $finish = ($StartYearOfMPeriod + $MPeriodDistance - 1) + $MPeriodDistance * $i - $i;
            $MPeriodOption[$i+1] =  $start. '-'. $finish;

        }
        return $MPeriodOption;
    }

    public function test_report(){
        $group = config('dataoptions.Project.Group');
        $arr_level_1 = [
            'Giao thông', 'Nông nghiệp, lâm nghiệp, thủy lợi, thủy sản','Giáo dục đào tạo và tay nghề', 'Văn hóa thông tin', 'Ngành khác'
        ];
//        $arr_level_2 = [
//             'Các dự án dự kiến hoàn thành năm 2019',
//             'Các dự án chuyển tiếp hoàn thành sau năm 2019',
//             'Các dự án khởi công mới năm 2019'
//        ];

        $arr_level_3 = config('dataoptions.Project.Group');

//        $test = [
//            'ItemID'=> 'I',
//            'ParentID'=> 0,
//            'Level'=> 1,
//            'ItemName'=> 'Tổng số'
//        ];
        $Project = Project::all();
        foreach ($arr_level_1 as $key1 => $arr1){
            $key1 = sprintf("%02d", $key1+1);
            $test[] = [
                'ItemID'=> 'II' . $key1,
                'ParentID'=> 1,
                'Level'=> 2,
                'ItemName'=> $arr1
            ];
            $i3 = 1;
            foreach($arr_level_3 as $key3 => $arr3){
                $test[] = [
                    'ItemID'=> 'II' . $key1 . '' . sprintf("%02d", $i3),
                    'ParentID'=> 2,
                    'Level'=> 3,
                    'ItemName'=> $arr3
                ];
                $Project = $Project->filter(function ($item, $key){
//                    if($item->SectorName == )
                });
                $i3++;
            }
        }

        return $test;

//        $arr = [
//                '1'=> 'Tổng số',
//                '2'=> 'Giao Thông',
//                '2'=> 'THỰC HIỆN DỰ ÁN',
//                '3'=> 'Các dự án hoàn thành năm 2019',
//                '4'=> json_decode($group),
//        ];
//
//        $arr = [
//            '1'=> [
//                'name'=> 'Tổng số',
//                '1'=> [
//                    'name'=> 'Giao thông',
//                    $arr_level_3
//                ],
//                '2'=> [
//                    'name'=> 'THỰC HIỆN DỰ ÁN',
//                    $arr_level_3
//                ]
//            ]
//        ];
    }

}
