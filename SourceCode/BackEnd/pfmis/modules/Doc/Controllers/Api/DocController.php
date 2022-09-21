<?php
namespace Module\Doc\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Defuse\Crypto\File;
use IjCore\IjDate;
use IjCore\IjFile;
use IjCore\IjString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Doc\Models\DocLink;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Company;
use Module\Listing\Models\Doc;
use Module\Listing\Models\DocCate;
use Module\Listing\Models\DocFile;
use Module\Listing\Models\DocPer;
use Module\Listing\Models\DocVideo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\Vendor;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysTable;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Task\Models\FileModel;
use Module\Task\Models\TaskLink;

class DocController extends Controller{

    public function index(Request $request){
        $data = Doc::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function list(Request $request){
        $json['status'] = 0;
        $json['msg'] = 'Thất bại';
        $json['data'] = [];

        $data = Doc::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function create(){
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::where('Inactive', '!=', 1)->get();
        $json['Company'] = $Company;
        $json['Group'] = $Group;
        $json['Employee'] = $Employee;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = null;
        return json_encode($json);
    }
    public function store(Request $request){
        $model = (object)$request->model;
        $auth = Auth::user();

        if($model->ParentID){
            $Parent = Doc::find($model->ParentID);
            if($Parent){
                $model->Level = $Parent->Level + 1;
                $model->Title = $Parent->Title.'/'.$Parent->DocName;
                if($Parent->Path){
                    $model->Path = $Parent->Path.$Parent->DocID.'-';
                }else{
                    $model->Path = '-'.$Parent->DocID.'-';
                }
                if($Parent->Detail == 1){
                    $Parent->Detail = 0;
                    $Parent->save();
                }
            }
        }
        if($model->AccessType != 2){
            $model->PublicCompanyID = '';
//            $model->PublicGroupID = '';
        }else{
            $model->PublicCompanyID = null;
            if (isset($model->PublicCompanyIDValue) && $model->PublicCompanyIDValue) {
                $model->PublicCompanyID = $model->PublicCompanyIDValue;
            }
        }
        $data = new Doc([
            'DocName' => $model->DocName,
            'ParentID' => $model->ParentID,
            'ParentName' => $model->ParentName,
            'Level' => $model->Level?$model->Level:1,
            'Detail' => 1,
            'DocDate' => IjDate::ConvertClientDateToServerDate($model->DocDate),
            'EffectiveDate' => IjDate::ConvertClientDateToServerDate($model->EffectiveDate),
            'SignerName' => $model->SignerName,
            'CompanyIssued' => $model->CompanyIssued,
            'AccessType' => $model->AccessType,
            'PublicCompanyID' => $model->PublicCompanyID,
//            'PublicGroupID' => $model->PublicGroupID,
            'Path' => $model->Path,
            'UserIDCreated' => $auth->EmployeeID,
            'Title' => $model->Title,
            'AuthorizedPerson' => $model->AuthorizedPerson,
        ]);
        $data->save();
        $DocPer = new DocPer([
            'DocID' => $data->DocID,
            'EmployeeID' => $auth->EmployeeID,
            'Access' => 1,
            'AccessField' => 'all',
            'Edit' => 1,
            'EditField' => 'all',
            'Delete' => 1,
            'Create' => 1,
            'TypePer' => 2,
        ]);
        $DocPer->save();
        if($data->UserIDCreated != $data->AuthorizedPerson){
            $DocPer = new DocPer([
                'DocID' => $data->DocID,
                'EmployeeID' => $data->AuthorizedPerson,
                'Access' => 1,
                'AccessField' => 'all',
                'Edit' => 1,
                'EditField' => 'all',
                'Delete' => 1,
                'Create' => 1,
                'TypePer' => 2,
            ]);
            $DocPer->save();
        }
        if($data->ParentID){
            $docPerParent = DocPer::where(['DocID' => $data->ParentID])
                ->where('EmployeeID', '!=', Auth::user()->EmployeeID)
                ->where('EmployeeID', '!=', $data->AuthorizedPerson)
                ->get();
            $arrInsert = [];
            foreach ($docPerParent as $value){
                if ($value->EmployeeID == $auth->EmployeeID || $value->EmployeeID == $data->AuthorizedPerson) {
                    continue;
                }
                $arrInsert[] = [
                    'DocID' => $data->DocID,
                    'EmployeeID' => $value->EmployeeID,
                    'Access' => $value->Access,
                    'AccessField' => $value->AccessField,
                    'Edit' => $value->Edit,
                    'EditField' => $value->EditField,
                    'Delete' => $value->Delete,
                    'Create' => $value->Create,
                    'TypePer' => $value->TypePer
                ];
            }

            if (count($arrInsert) > 0) {
                DB::table('doc_per')->insert($arrInsert);
            }
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->DocID;

        return json_encode($json);
    }

    public function view($id){
        $Doc = Doc::find($id);
        $DocPerEmployee = $this->getDocPerEmployee($id);
        $EmployeeLogin = Employee::where('UserID', '=', Auth::user()->UserID)->first();
        if(!$DocPerEmployee){
            if($Doc->AccessType == 2){//Nếu tài liệu là công khai
                if(!strpos(','.$Doc->PublicCompanyID.',', $EmployeeLogin->CompanyID.'')){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!'.','.$Doc->PublicCompanyID.',';
                    return json_encode($json);
                }else{
                    $DocPerEmployee = new DocPer([
                        'DocID' => $id,
                        'EmployeeID' => $EmployeeLogin->EmployeeID,
                        'Access' => 1,
                        'AccessField' => 'all',
                        'Edit' => 0,
                        'EditField' => '',
                        'Delete' => '0',
                        'Create' => 0,
                        'TypePer' => 2,
                    ]);
                }
            }else{//Nếu tài liệu không phải công khai
                if($EmployeeLogin->EmployeeID != $Doc->UserIDCreated && $EmployeeLogin->EmployeeID != $Doc->AuthorizedPerson){
                    $json['status'] = 3;
                    $json['msg'] = 'Bạn không có quyền truy cập!';
                    return json_encode($json);
                }else{
                    $DocPerEmployee = new DocPer([
                        'DocID' => $id,
                        'EmployeeID' => $EmployeeLogin->EmployeeID,
                        'Access' => 1,
                        'AccessField' => 'all',
                        'Edit' => 0,
                        'EditField' => '',
                        'Delete' => 0,
                        'Create' => 0,
                        'TypePer' => 2,
                    ]);
                }
            }
        }
        if($EmployeeLogin->EmployeeID == $Doc->UserIDCreated || $EmployeeLogin->EmployeeID == $Doc->AuthorizedPerson){
            $DocPerEmployee = new DocPer([
                'DocID' => $id,
                'EmployeeID' => $EmployeeLogin->EmployeeID,
                'Access' => 1,
                'AccessField' => 'all',
                'Edit' => 1,
                'EditField' => 'all',
                'Delete' => 1,
                'Create' => 1,
                'TypePer' => 2,
            ]);
        }
        if (strpos(',' . $DocPerEmployee->AccessField . ',', ',all,')) {
            $DocPerEmployee->AccessField = 'all';
        }
        if(strpos(','.$DocPerEmployee->EditField.',', ',all,')){
            $DocPerEmployee->EditField = 'all';
        }
        $DocPer = $this->getDocPer($id);
        $Employee = Employee::all();
        $Company = Company::all();
        $Group = SysUserGroup::all();
        $DocLink = DocLink::where('DocID', '=', $id)->get();
        $DocFile = DocFile::where('DocID', '=', $id)->get();
        $DocVideo = DocVideo::where('DocID', '=', $id)->get();
        $DocChild = Doc::select('doc.DocID', 'doc.DocName', 'doc.ParentID', 'doc.ParentName', 'doc.Level', 'doc.Path', 'doc.Title')
            ->leftJoin('doc_per', 'doc.DocID', '=', 'doc_per.DocID')
            ->where(function ($q) use ($EmployeeLogin){
                $q->where('doc_per.EmployeeID', '=', $EmployeeLogin->EmployeeID);
                $q->orWhere(function ($q) {
                    $q->where('doc.AccessType', '=', 2);
                    $q->whereNull('doc.PublicCompanyID');
                });

                $q->orWhere(function ($q) use ($EmployeeLogin) {
                    $q->where('doc.AccessType', '=', 2);
                    $q->whereNotNull('doc.PublicCompanyID');
                    $q->where('doc.PublicCompanyID', '=', $EmployeeLogin->CompanyID);
                });
            })
            ->where('doc.ParentID', '=', $Doc->DocID)->get();

        $DocCate = DB::select(DB::raw("
            SELECT DC.CateID, DC.CateValue, L.CateName, V.Description FROM doc_cate DC
            LEFT JOIN doc_cate_list L
            ON DC.CateID = L.CateID
            LEFT JOIN doc_cate_value V
            ON DC.CateID = V.CateID AND DC.CateValue = V.CateValue WHERE DC.DocID = ".$id));
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['Doc'] = $Doc;
        $json['DocChild'] = $DocChild;
        $json['DocLink'] = $DocLink;
        $json['DocFile'] = $DocFile;
        $json['DocVideo'] = $DocVideo;
        $json['DocPer'] = $DocPer;
        $json['DocCate'] = $DocCate;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['DocPer'] = $DocPer;
        $json['DocPerEmployee'] = $DocPerEmployee;
        return json_encode($json);
    }

    public function edit($id){
        $data = Doc::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $data = Doc::find($id);
        $OldParent = $data->ParentID;
        $model = (object)$request->model;
        if($model->ParentID != $OldParent){
            $parentOld = Doc::where('ParentID', '=', $data->ParentID)->where('DocID', '!=', $data->DocID)->first();
            if(!isset($parentOld->DocID)){
                Doc::find($data->ParentID)->update(['Detail' => 1]);
            }
            $parent = Doc::find($model->ParentID);
            $lenghPath = strlen($data->Path) + 1;
            if ($parent) {
                $oldLevel = $data->Level;
                $data->Level = empty($model->ParentID) ? 1 : $parent->Level + 1;
                $changeLevel = $oldLevel - $data->Level;
                $lenghTitleOld = $data->Title?strlen($data->Title):1;
                $Title = $parent->Title.'/'.$parent->DocName;
                DB::update(DB::raw("update doc
                                set Path = concat('" . $parent->Path . (empty($parent->Path)? "-" : "") . $parent->DocID . "', substring(Path, ".$lenghPath.", LENGTH(Path))),
                                Level = Level - " . $changeLevel . ",
                                Title = concat('".$Title."', substring(Title, ".$lenghTitleOld.", LENGTH(Title)))
                                WHERE Path like('" . $data->Path . (empty($data->Path)? "-" : "") . $data->DocID . "-%')"));

                $data->Path = empty($parent->Path)? '-'.$parent->DocID.'-' : $parent->Path . $parent->DocID . '-';
                if ($parent->Detail == 1) {
                    $parent->Detail = 0;
                    $parent->save();
                }
            } else {
                $lenghTitleOld = $data->Title?strlen($data->Title):1;
                $Title = $data->DocName;
                DB::update(DB::raw("update doc
                                set Path = substring(Path, ".$lenghPath." - 1, LENGTH(Path)),
                                Level = Level - " . $data->Level . " + 1,
                                Title = concat('".$Title."', substring(Title, ".$lenghTitleOld.", LENGTH(Title)))
                                WHERE Path like('" . $doc->Path . (empty($data->Path)? "-" : "") . $data->DocID . "-%')"));
                $data->Path = '';
                $data->Level = 1;
            }
            $data->Title = $Title;

        }
        if($model->AccessType != 2){
            $model->PublicCompanyID = '';
//            $model->PublicGroupID = '';
        }else{
            $model->PublicCompanyID = $model->PublicCompanyIDValue;
        }
        $data->DocName = $model->DocName;
        $data->AccessType =  $model->AccessType;
        $data->ParentID =  $model->ParentID;
        $data->ParentName =  $model->ParentName;
        $data->DocDate =  IjDate::ConvertClientDateToServerDate($model->DocDate);
        $data->EffectiveDate =  IjDate::ConvertClientDateToServerDate($model->EffectiveDate);
        $data->SignerName =  $model->SignerName;
        $data->CompanyIssued =  $model->CompanyIssued;
        $data->PublicCompanyID =  $model->PublicCompanyID;
        $data->AuthorizedPerson =  $model->AuthorizedPerson;
        $data->save();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->DocID;

        return json_encode($json);
    }

    public function delete($id){
        $isPer = DocPer::where([
            'DocID' => $id,
            'EmployeeID' => Auth::user()->EmployeeID,
            'Delete' => 1
        ])->exists();

        if($isPer){
            self::deleteDoc($id);
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
    public function deleteArray(Request $request){
        $auth = Auth::user();
        $idArray = $request->post('array_id');
        if ($auth->UserType === 1) {
            self::deleteDoc($idArray);
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            $isPer = false;
            // Kiểm tra có quyền xóa
            $docsPer = DocPer::whereIn('LineID', function ($q) use ($idArray, $auth) {
                $q->select(DB::raw('MAX(LineID)'))
                    ->from('doc_per')
                    ->whereIn('DocID', $idArray)
                    ->where('EmployeeID', $auth->EmployeeID)
                    ->groupBy('DocID');
            })->get();

            foreach ($docsPer as $docPer) {
                if ($docPer->Delete === 1) {
                    $isPer = true;
                } else {
                    $isPer = false;
                    break;
                }
            }

            if ($isPer) {
                self::deleteDoc($idArray);
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

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public static function deleteDoc($ids){
        if (is_array($ids)) {
            Doc::destroy($ids);
            DocCate::whereIn('DocID', $ids)->delete();
            DocFile::whereIn('DocID', $ids)->delete();
            DocLink::whereIn('DocID', $ids)->delete();
            DocVideo::whereIn('DocID', $ids)->delete();
            DocPer::whereIn('DocID', $ids)->delete();
        } else {
            Doc::destroy($ids);
            DocCate::where('DocID', $ids)->delete();
            DocFile::where('DocID', $ids)->delete();
            DocLink::where('DocID', $ids)->delete();
            DocVideo::where('DocID', $ids)->delete();
            DocPer::where('DocID', $ids)->delete();
        }
    }


    public function getList(Request $request){
        $data = null;
        $auth = Auth::user();
        $query = DB::table('doc')
            ->leftJoin('doc_per', 'doc.DocID', '=', 'doc_per.DocID')
            ->select(DB::raw("doc.DocID, doc.DocName, doc.Level, doc.Detail, 0 IsOpen, doc.Title, doc.Path, 0 IsHide, 0 IsLoad"));

        // quyền truy cập
//        $query->where(function ($q){
//            $q->whereRaw("doc.UserIDCreated = 25
//            OR doc.AuthorizedPerson = 25
//            OR (doc_per.Create = 1 AND doc_per.EmployeeID = 25)
//            OR CONCAT(',', doc.PublicCompanyID, ',') like ('%2%')
//            OR (doc.PublicCompanyID = '' AND doc.AccessType = 2)");
//        });

        $query->where(function ($q) use ($auth) {
            $q->where('doc_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('doc.AccessType', '=', 2);
                $q->whereNull('doc.PublicCompanyID');
            });

            $q->orWhere(function ($q) use ($auth) {
                $q->where('doc.AccessType', '=', 2);
                $q->whereNotNull('doc.PublicCompanyID');
                $q->where('doc.PublicCompanyID', '=', $auth->CompanyID);
            });
        });


        if($request->post('search')){
            $query->where('doc.DocName', 'like', '%'.$request->search.'%');
        }else{
            $query->where('doc.Level', '=', 1);
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('doc.DocID');
        $data = $query->paginate($per_page);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function getListChild(Request $request){
        $auth = Auth::user();
        $ParentID = $request->ParentID;
        $query = DB::table('doc')
            ->leftJoin('doc_per', 'doc.DocID', '=', 'doc_per.DocID')
            ->select(DB::raw("doc.DocID, doc.DocName, doc.Level, doc.Detail, 0 IsOpen, doc.Title, doc.Path, 0 IsHide, 0 IsLoad, doc.DocDate"));
//        $query->where(function ($q){
//            $q->whereRaw("doc.UserIDCreated = 25
//            OR doc.AuthorizedPerson = 25
//            OR (doc_per.Create = 1 AND doc_per.EmployeeID = 25)
//            OR CONCAT(',', doc.PublicCompanyID, ',') like ('%2%')
//            OR (doc.PublicCompanyID = '' AND doc.AccessType = 2)");
//        });

        $query->where(function ($q) use ($auth) {
            $q->where('doc_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('doc.AccessType', '=', 2);
                $q->whereNull('doc.PublicCompanyID');
            });

            $q->orWhere(function ($q) use ($auth) {
                $q->where('doc.AccessType', '=', 2);
                $q->whereNotNull('doc.PublicCompanyID');
                $q->where('doc.PublicCompanyID', '=', $auth->CompanyID);
            });
        });

        $query->where('doc.ParentID', '=', $ParentID);
        $query->orderBy('doc.DocID', 'desc');
        $query->groupBy('doc.DocID');
        $data = $query->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function getDocPer($DocID){
        $data = DocPer::query(DB::raw('SELECT DocID, EmployeeID, EmployeeName, MAX(Access) Access,
            GROUP_CONCAT(AccessField) AccessField, MAX(Edit) Edit, GROUP_CONCAT(EditField) EditField, MAX("Delete") "Delete", MAX("Create") "Create", FROM doc_per'))
            ->where('DocID', '=', $DocID)->groupBy('EmployeeID')->get();
        return $data;
    }
    public function getDocPerEmployee($DocID){
        $data = DocPer::query(DB::raw('SELECT DocID, EmployeeID, EmployeeName, MAX(Access) Access,
            GROUP_CONCAT(AccessField) AccessField, MAX(Edit) Edit, GROUP_CONCAT(EditField) EditField,
            MAX("Delete") "Delete", MAX("Create") "Create", MAX(TypePer) TypePer FROM doc_per'))
            ->where('DocID', '=', $DocID)->where('EmployeeID', '=', Auth::user()->EmployeeID)->groupBy('EmployeeID')->first();
        return $data;
    }
    public function getListEmployee(Request $request){
        $TypeSearch = $request->TypeSearch;
        $data = null;
        if($TypeSearch == 'Company'){
            $data = Employee::whereRaw('CompanyID IN ('.$request->CompanyID.')')->where('Inactive', '!=', 1)->get();
        }elseif ($TypeSearch == 'Group'){
            $data = DB::select(DB::raw("
                SELECT EmployeeID, EmployeeName FROM employee E
                INNER JOIN sys_group_member M
                ON E.UserID = M.UserID
                INNER JOIN sys_user_group G
                ON M.UserGroupID = G.UserGroupID
                WHERE G.UserGroupID IN (".$request->GroupID.")
            "));
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function updatePer(Request $request, $id){
        $DocID = $request->DocID;
        $DocPer = $request->DocPer;
        $arrInsert = [];
        $ArrEmployee = [];
        foreach ($DocPer as $value){
            if($value){
                if($value['Access']){
                    $arrInsert[] = [
                        'DocID' => $DocID,
                        'EmployeeID' => $value['EmployeeID'],
                        'Access' => $value['Access'],
                        'AccessField' => $value['AccessField'],
                        'Edit' => $value['Edit'],
                        'EditField' => $value['EditField'],
                        'Delete' => $value['Delete'],
                        'Create' => $value['Create'],
                        'TypePer' => 2
                    ];
                    $ArrEmployee[$value['EmployeeID']] = $value['EmployeeID'];
                }
            }
        }
        DocPer::where(['DocID' => $DocID])->delete();

        if (count($arrInsert) > 0) {
            DB::table('doc_per')->insert($arrInsert);
        }
        $data = Doc::find($DocID);
        if($data->ParentID){
            $Employee = [];
            ////////////////////////////
            if($data->AccessType == 2){
                if($data->PublicCompanyID){
                    $Employee = Employee::select('EmployeeID')->whereRaw('CompanyID IN ('.$data->PublicCompanyID.')')->where('Inactive', '!=', 1)->get();
                }else{
                    $Employee = Employee::select('EmployeeID')->where('Inactive', '!=', 1)->get();
                }
                foreach ($Employee as $item){
                    $ArrEmployee[$item->EmployeeID] = $item->EmployeeID;
                }
            }
            $ArrParentID = explode("-",$data->Path);
            $arrInsert = [];
            foreach ($ArrEmployee as $val){
                DocPer::where(['EmployeeID' => $val, 'TypePer' => 1])->whereIn('DocID', $ArrParentID)->delete();
                foreach ($ArrParentID as $value){
                    if($value){
                        $arrInsert[] = [
                            'DocID' => $value,
                            'EmployeeID' => $val,
                            'Access' => 1,
                            'AccessField' => '',
                            'Edit' => 0,
                            'EditField' => '',
                            'Delete' => 0,
                            'Create' => 0,
                            'TypePer' => 1
                        ];
                    }
                }
            }
            if (count($arrInsert) > 0) {
                DB::table('doc_per')->insert($arrInsert);
            }
        }

        $data = null;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $DocID;
        return json_encode($json);
    }


    //Link
    public function updateDocLink(Request $request, $id){
        $LinkArray = $request->post('DocLink');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'DocID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        DocLink::where(['DocID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('doc_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    //Cate
    public function updateDocCate(Request $request, $id){
        $CateArray = $request->post('DocCate');
        $arrCate = [];
        foreach ($CateArray as $item){
            if($item['CateID']){
                $arrCate[] = [
                    'DocID' => $id,
                    'CateID' => $item['CateID'],
                    'CateName' => $item['CateName'],
                    'CateValue' => $item['CateValue'],
                    'Description' => $item['Description'],
                ];
            }
        }
        DocCate::where(['DocID' => $id])->delete();
        if(!empty($arrCate)){
            DB::table('doc_cate')->insert($arrCate);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    //Upload File
    public function uploadFile(Request $request, $id){
        $json['status'] = 0;
        $json['msg'] = '';
        $json['data'] = [];

        if($request->LineID){
            $DocFile = DocFile::find($request->LineID);
        }else{
            $DocFile = new DocFile();
        }
        $DocFile->FileName = $request->FileName;
        $DocFile->FileNameRoot = $request->FileName;
        $DocFile->Description = $request->Description;
        $DocFile->FileType = $request->FileType;
        $DocFile->FileSize = $request->FileSize;
        $DocFile->DocID = $id;
        $DocFile->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $DocFile->UserModified = Auth::user()->username;
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

                    $DocFile->Link = $linkFileAttach;
                    $DocFile->DateModifiedRoot = $fileModel->DateModified;
                    $DocFile->FileID = $fileModel->FileID;
                }else {
                    $json['msg'] = 'Không hỗ trợ định dạng ' . $ext;
                    return json_encode($json);
                }
            }else {
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $DocFile->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $DocFile;
        return json_encode($json);
    }

    //Delete File
    public function deleteFile(Request $request, $id){
        $dFile = DocFile::where(['LineID' => $id])->delete();
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
        $files = DocFile::where('DocID', $id)->get();

        $arrLink = [];
        foreach ($files as $file) {
            $tmpArr = [];
            $tmpArr['Link'] = $file->Link;
            $tmpArr['FileName'] = $file->FileName;
            $arrLink[] = $tmpArr;
        }
        if (count($arrLink)) {
            $doc = Doc::find($id);
            $zipName = '';
            if ($doc) {
                $zipName = IjString::CreateSlug($doc->DocName, '-');
            }
            $zipLink = IjFile::CreateZipArchive($arrLink, true, $zipName);
            if ($zipLink) {
                $json['status'] = 1;
                $json['msg'] = 'Thành công';
                $json['data'] = $zipLink;
                return json_encode($json);
//                return response()->download(public_path($zipLink));
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
            $DocVideo = DocVideo::find($request->LineID);
        }else{
            $DocVideo = new DocVideo();
        }
        $DocVideo->VideoName = $request->VideoName;
        $DocVideo->Description = $request->Description;
        $DocVideo->VideoType = $request->VideoType;
        $DocVideo->VideoSize = $request->VideoSize;
        $DocVideo->DocID = $id;
        $DocVideo->DateModified = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request->DateModified)));
        $userId = Auth::user()->UserID;
        $DocVideo->UserModified = Auth::user()->username;
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

                    $DocVideo->Link = $linkFileAttach;
                    $DocVideo->DateModifiedRoot = $fileModel->DateModified;
                    $DocVideo->FileID = $fileModel->FileID;

                }else{
                    $json['msg'] = 'Định dạng ' . $ext . ' không phải là định dạng của video';
                    return json_encode($json);
                }
            }else{
                $json['msg'] = 'Vượt quá kích thước 350M';
                return json_encode($json);
            }
        }

        $DocVideo->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $DocVideo;
        return json_encode($json);
    }
    //Delete File
    public function deleteVideo(Request $request, $id){
        $tVideo = DocVideo::where(['LineID' => $id])->delete();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;
        return json_encode($json);
    }
    //Download File
    public function downloadVideo(Request $request, $id){
        $file = FileModel::find($id);
        return response()->download(public_path($file->Link));
    }

    public function getPerParent(Request $request){
        $ParentID = $request->ParentID;
        $json['status'] = 1;
        if($ParentID){
            $Doc = Doc::find($ParentID);
            if($Doc){
                if($Doc->UserIDCreated == Auth::user()->EmployeeID || $Doc->AuthorizedPerson == Auth::user()->EmployeeID){
                    $json['status'] = 1;
                }else{
                    $DocPerEmployee = $this->getDocPerEmployee($ParentID);
                    if($DocPerEmployee->Create == 1){
                        $json['status'] = 1;
                    }else{
                        $json['status'] = 3;
                    }
                }

            }

        }else{
            $json['status'] = 1;
        }
        $json['msg'] = 'Thành công!';
        $json['data'] = $ParentID;
        return json_encode($json);
    }
}
