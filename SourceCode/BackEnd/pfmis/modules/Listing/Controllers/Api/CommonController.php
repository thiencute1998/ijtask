<?php
namespace Module\Listing\Controllers;

use Carbon\Traits\Date;
use IjCore\IjDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Module\Listing\Models;
use Module\Listing\Models\Employee;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysStatusItem;
use Module\SysAdmin\Models\SysTable;
use Illuminate\Support\Facades\Auth;
use Module\Listing\Models\NormTable;
use phpDocumentor\Reflection\Types\Boolean;
use function Psy\debug;

class CommonController extends Controller{

    public function status(Request $request){
        $IDs = $request->post('array_id');
        $FieldID = $request->post('FieldID');
        $Table = $request->post('table');
        $Inactive = $request->post('Inactive');
        DB::table($Table)->whereIn($FieldID, $IDs)
            ->update(['Inactive' => $Inactive]);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public function list(Request $request){
        $data = null;
        $auth = Auth::user();
        $table = $request->post('table');
        $FieldName = $request->post('FieldName');
        $FieldID = $request->post('FieldID');
        $FieldNo = $request->post('FieldNo');

        $FieldWhere = $request->post('FieldWhere');
        $query = DB::table($table);
        $autoModel = SysAutoNumber::where('NumberKey', $table)->first();
        if($autoModel  && Schema::hasColumn($table, 'Level')){
//            $query->where('Level','<',$autoModel->MaxLevel);
        }
        if($request->post('CurrentID')){
            $query->where($table.'.'.$FieldID, '!=',$request->post('CurrentID'));
        }
        if($request->post('search')){
            $query->where(function($q) use($FieldNo,$FieldName,$request, $table){
                if ($FieldName) {
                    $q->where($table . '.' . $FieldName, 'like', '%' . $request->search . '%');
                }
                if ($FieldNo) {
                    $q->orWhere($table . '.' . $FieldNo, 'like', $request->search . '%');
                }
            });
        }
        if($FieldWhere){
            foreach ($FieldWhere as $key => $value){
                if ($value) {
                    $query->where($key, '=', $value);
                }
            }
        }

        // Phân quyền
        $tablePer = $table.'_per';
        $hasTable = Schema::hasTable($tablePer);
        if($hasTable){
            $query->select($table.'.*')->leftJoin($tablePer,$table.'.'.$FieldID,'=',$tablePer.'.'.$FieldID);
            $query->where(function ($q) use ($auth,$table,$tablePer,$FieldID){
                $q->where($tablePer.'.EmployeeID',$auth->EmployeeID)
                    ->orWhere($table.'.AccessType',2);
            });
            $query->groupBy($table.'.'.$FieldID);
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        if ($table == 'Task' || $table == 'task') {
            $query->orderBy('TaskID', 'DESC');
        }else{
            if ($FieldNo) {
                $query->orderBy($FieldNo);
            }
        }
        $data = $query->paginate($per_page);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function list2(Request $request){
        $table = $request->post('table');
        $FieldName = $request->post('FieldName');
        $FieldID = $request->post('FieldID');
        if($request->post('FieldNo')){
            $FieldNo = $request->post('FieldNo');
        }
        else{
            $FieldNo = $FieldID;
        }

        $FieldWhere = $request->post('FieldWhere');
        $FieldUpdate = $request->post('FieldUpdate');
        $select = $FieldID . ', ' . $FieldName;
        if ($FieldNo) {
            $select .= ', ' . $FieldNo;
        }
        if (isset($FieldUpdate) && $FieldUpdate) {
            foreach ($FieldUpdate as $key => $field) {
                $select .= ', ';
                $select .= $field;
            }
        }

        $query = DB::table($table);
        $query->select(DB::raw($select));
        if($request->post('CurrentID')){
            $query->where($table . '.' . $FieldID, '!=', $request->post('CurrentID'));
        }
        if($request->post('term')){
            $query->where(function($q) use($FieldNo, $FieldName, $request, $table){
                $q->where($table . '.' . $FieldName, 'like', '%' . $request->term . '%')
                    ->orWhere($table . '.' . $FieldNo, 'like', $request->term . '%');
            });
        }
        if($FieldWhere){
            foreach ($FieldWhere as $key => $value){
                if ($value) {
                    $query->where($key, '=', $value);
                }
            }
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }

        if ($request->has('getAll') && $request->post('getAll')) {
            $data = $query->limit(1000)->get();
        }else{
            $data = $query->paginate($per_page);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        $json['page'] = $request->post('page');
        return json_encode($json);
    }

    public function list3(Request $request){
        $data = null;
        $auth = Auth::user();
        $tabletrans = $request->post('tabletrans');
        $table = $request->post('table');
        $FieldName = $request->post('FieldName');
        $FieldID = $request->post('FieldID');
        if($request->post('FieldNo')){
            $FieldNo = $request->post('FieldNo');
        }
        else{
            $FieldNo = $FieldID;
        }

        $FieldWhere = $request->post('FieldWhere');
        $query = DB::table($table)->join($tabletrans, $table.'.CompanyID', '=', $tabletrans.'.InputCompanyID')->select($table.'.CompanyID', $table.'.CompanyNo', $table.'.CompanyName')->groupBy('InputCompanyID');
        $autoModel = SysAutoNumber::where('NumberKey', $table)->first();
        if($autoModel  && Schema::hasColumn($table, 'Level')){
            $query->where('Level','<',$autoModel->MaxLevel);
        }
        if($request->post('CurrentID')){
            $query->where($table.'.'.$FieldID, '!=',$request->post('CurrentID'));
        }
        if($request->post('search')){
            $query->where(function($q) use($FieldNo,$FieldName,$request, $table){
                $q->where($table.'.'.$FieldName, 'like', '%'.$request->search.'%')
                    ->orWhere($table.'.'.$FieldNo, 'like', '%'.$request->search.'%');
            });
        }
        if($FieldWhere){
            foreach ($FieldWhere as $key => $value){
                if ($value) {
                    $query->where($key, '=', $value);
                }
            }
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $data = $query->orderBy($table.'.'.$FieldNo)->paginate($per_page);Log::debug($query->toSql());
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    // Loại danh mục
    public  function  listCate(Request $request){
        $data = null;
        $table = $request->post('table').'_cate_list';
        $FieldName = $request->post('FieldName');
        $FieldID = $request->post('FieldID');
        if($request->post('FieldNo')){
            $FieldNo = $request->post('FieldNo');
        }
        else{
            $FieldNo = $FieldID;
        }

        $FieldWhere = $request->post('FieldWhere');
        $query = DB::table($table);

        if($request->post('CurrentID')){
            $query->where($table.'.'.$FieldID, '!=',$request->post('CurrentID'));
        }
        if($request->post('search')){
            $query->where(function($q) use($FieldNo,$FieldName,$request, $table){
                $q->where($table.'.'.$FieldName, 'like', '%'.$request->search.'%')
                    ->orWhere($table.'.'.$FieldNo, 'like', '%'.$request->search.'%');
            });
        }
        if($FieldWhere){
            foreach ($FieldWhere as $key => $value){
                if ($value) {
                    $query->where($key, '=', $value);
                }
            }
        }
        // Descirption none
        $tableCateVale = $request->post('table').'_cate_value';
        $queryCateValue = DB::table($tableCateVale);
        $arrListCateValue =$queryCateValue->whereNotNull($FieldID)->groupBy($FieldID)->get();
        $arrCateValue = [];
        foreach ($arrListCateValue as $arr){
            $arrCateValue[] = $arr->$FieldID;
        }
        $query->whereNotIn($FieldID, $arrCateValue);
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        if ($table == 'Task' || $table == 'task') {
            $query->orderBy('TaskID', 'DESC');
        }
        $data = $query->orderBy('CateNo', 'ASC')->paginate($per_page);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }
    //
    public function getAutoNoChild(Request $request){
        $table = $request->post('table');
        $tableUpFirst = ucfirst($table);
        $ParentID = $request->post('ParentID');
        if($ParentID){

            $auto = DB::transaction(function () use ($table, $ParentID, $tableUpFirst){
                $autoModel = SysAutoNumber::where('NumberKey', $table)->first();
                $auto_no = DB::table($table)->where($tableUpFirst.'ID', $ParentID)->first();
                if($auto_no->Level >= $autoModel->MaxLevel){
                    if($tableUpFirst == 'Doc'){
                        $Path = trim($auto_no->Path, '-');
                        $arrPath = explode('-', $Path);
                        if(isset($arrPath[$autoModel->MaxLevel - 2])){
                            $ParentRoot = $arrPath[$autoModel->MaxLevel - 2];
                            $auto_no = DB::table($table)->where($tableUpFirst.'ID', $ParentRoot)->first();
                            $auto = $auto_no->NumberValue;
                            DB::table($table)->where($tableUpFirst.'ID', $ParentRoot)->update(['NumberValue' => (int)$auto + 1]);
                        }
                    }else{
                        if($auto_no->ParentID){
                            $ParentIDTemp = $auto_no->ParentID;
                            $auto_no = DB::table($table)->where($tableUpFirst.'ID', $auto_no->ParentID)->first();
                            $auto = $auto_no->NumberValue;
                            DB::table($table)->where($tableUpFirst.'ID', $ParentIDTemp)->update(['NumberValue' => (int)$auto + 1]);
                        }else{
                            $auto = $autoModel->NumberValue;
                            $autoModel->NumberValue = (int)$auto + 1;
                            $autoModel->save();
                        }
                    }
                }else{
                    $auto = $auto_no->NumberValue;
                    DB::table($table)->where($tableUpFirst.'ID', $ParentID)->update(['NumberValue' => (int)$auto + 1]);
                }

                $FieldNo = $tableUpFirst.'No';
                $parentNo = $auto_no->$FieldNo;
                $arrParentNo = [];
                if($autoModel->SignChar){
                    $arrParentNo = explode($autoModel->SignChar, $parentNo);
                }else{
                    $str = $parentNo;
                    for($i = 1; $i <= $autoModel->MaxLevel; $i++){
                        $lengh = 'PadLeng'.$i;
                        if($autoModel->$lengh){
                            $strTemp = substr($str, 0, (int)$autoModel->$lengh);
                            $str = substr($str, (int)$autoModel->$lengh);
                            $arrParentNo[] = $strTemp;
                        }else{
                            break;
                        }
                    }
                }
                $k = 0;
                $number = strlen($auto);
                $value = $auto;
                foreach ($arrParentNo as $item){
                    if($k == $auto_no->Level){
                        $lengh = strlen($item);
                        if($number < $lengh){
                            for($i = 1; $i <= $lengh - $number; $i++){
                                $value = '0'.$value;
                            }
                        }
                        $arrParentNo[$k] = $value;
                        break;
                    }
                    $k++;
                }
                if($autoModel->Suffix == 'MDV'){
                    $Suffix = Session::get('CompanyNo');
                }elseif($autoModel->Suffix == 'MNV'){
                    $Suffix = Session::get('EmployeeNo');
                }elseif($autoModel->Suffix == 'DD'){
                    $Suffix = date('d');
                }elseif ($autoModel->Suffix == 'MM'){
                    $Suffix = date('m');
                }elseif ($autoModel->Suffix == 'YYYY'){
                    $Suffix = date('Y');
                }elseif ($autoModel->Suffix == 'YY'){
                    $Suffix = date('y');
                }else{
                    $Suffix = $auto_no->Suffix;
                }
                $companyNo = implode($autoModel->SignChar, $arrParentNo);
                return $auto_no->Prefix.$companyNo.$Suffix;
            });
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = $auto;
            return json_encode($json);
        }else{
            $auto = DB::transaction(function () use ($table) {
                $auto_no = SysAutoNumber::where('NumberKey', $table)->first();
                if($auto_no->Prefix == 'MDV'){
                    $Prefix = Session::get('CompanyNo');
                }elseif($auto_no->Prefix == 'MNV'){
                    $Prefix = Session::get('EmployeeNo');
                }elseif($auto_no->Prefix == 'DD'){
                    $Prefix = date('d');
                }elseif ($auto_no->Prefix == 'MM'){
                    $Prefix = date('m');
                }elseif ($auto_no->Prefix == 'YYYY'){
                    $Prefix = date('Y');
                }elseif ($auto_no->Prefix == 'YY'){
                    $Prefix = date('y');
                }else{
                    $Prefix = $auto_no->Prefix;
                }
                if($auto_no->Suffix == 'MDV'){
                    $Suffix = Session::get('CompanyNo');
                }elseif($auto_no->Suffix == 'MNV'){
                    $Suffix = Session::get('EmployeeNo');
                }elseif($auto_no->Suffix == 'DD'){
                    $Suffix = date('d');
                }elseif ($auto_no->Suffix == 'MM'){
                    $Suffix = date('m');
                }elseif ($auto_no->Suffix == 'YYYY'){
                    $Suffix = date('Y');
                }elseif ($auto_no->Suffix == 'YY'){
                    $Suffix = date('y');
                }else{
                    $Suffix = $auto_no->Suffix;
                }
                if($auto_no->Prefix == 'MNV'){
                    $employee = Employee::where(['UserID' => Auth::user()->UserID])->first();
                    $AutoNoField = 'Auto'.ucfirst($table).'No';
                    $auto = $employee->$AutoNoField;
                    $employee->$AutoNoField = $auto + 1;
                    $employee->save();
                }else{

                    $auto = $auto_no->NumberValue;
                    $auto_no->NumberValue = (int)$auto + 1;
                    $auto_no->save();
                }
                $value = $auto;
                $valueArr = [];
                for($k = 1; $k <= 9; $k++){
                    $number = strlen($value);
                    $PadLeng = 'PadLeng'.$k;
                    $lengh = $auto_no->$PadLeng;
                    if($number < $lengh && $lengh > 0){
                        for($i = 1; $i <= $lengh - $number; $i++){
                            $value = '0'.$value;
                        }
                        $valueArr[] = $value;
                        $value = 0;
                    }
                }
                if($valueArr){
                    $value = $Prefix.implode($auto_no->SignChar,$valueArr).$Suffix;
                }else{
                    $value = '';
                }

                return $value;
            });
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = $auto;
            return json_encode($json);
        }
    }
//Lấy mã con theo mã cha, truyền tên trường ID và NO
    public function getAutoNoChildTable(Request $request){
        $table = $request->post('table');
        $TableID = $request->post('TableID');
        $TableNo = $request->post('TableNo');
        $tableUpFirst = ucfirst($table);
        $ParentID = $request->post('ParentID');

        if($ParentID){

            $auto = DB::transaction(function () use ($table, $ParentID, $TableID, $TableNo){
                $autoModel = SysAutoNumber::where('NumberKey', $table)->first();
                $auto_no = DB::table($table)->where($TableID, $ParentID)->first();
                if($auto_no->Level >= $autoModel->MaxLevel){
                        if($auto_no->ParentID){
                            $ParentIDTemp = $auto_no->ParentID;
                            $auto_no = DB::table($table)->where($TableID, $auto_no->ParentID)->first();
                            $auto = $auto_no->NumberValue;
                            DB::table($table)->where($TableID, $ParentIDTemp)->update(['NumberValue' => (int)$auto + 1]);
                        }else{
                            $auto = $autoModel->NumberValue;
                            $autoModel->NumberValue = (int)$auto + 1;
                            $autoModel->save();
                        }
                }else{
                    $auto = $auto_no->NumberValue;
                    DB::table($table)->where($TableID, $ParentID)->update(['NumberValue' => (int)$auto + 1]);
                }
                $FieldNo = $TableNo;
                $parentNo = $auto_no->$FieldNo;
                $arrParentNo = [];
                if($autoModel->SignChar){
                    $arrParentNo = explode($autoModel->SignChar, $parentNo);
                }else{
                    $str = $parentNo;
                    for($i = 1; $i <= $autoModel->MaxLevel; $i++){
                        $lengh = 'PadLeng'.$i;
                        if($autoModel->$lengh){
                            $strTemp = substr($str, 0, (int)$autoModel->$lengh);
                            $str = substr($str, (int)$autoModel->$lengh);
                            $arrParentNo[] = $strTemp;
                        }else{
                            break;
                        }
                    }
                }
                $k = 0;
                $number = strlen($auto);
                $value = $auto;
                foreach ($arrParentNo as $item){
                    if($k == $auto_no->Level){
                        $lengh = strlen($item);
                        if($number < $lengh){
                            for($i = 1; $i <= $lengh - $number; $i++){
                                $value = '0'.$value;
                            }
                        }
                        $arrParentNo[$k] = $value;
                        break;
                    }
                    $k++;
                }
                if($autoModel->Suffix == 'MDV'){
                    $Suffix = Session::get('CompanyNo');
                }elseif($autoModel->Suffix == 'MNV'){
                    $Suffix = Session::get('EmployeeNo');
                }elseif($autoModel->Suffix == 'DD'){
                    $Suffix = date('d');
                }elseif ($autoModel->Suffix == 'MM'){
                    $Suffix = date('m');
                }elseif ($autoModel->Suffix == 'YYYY'){
                    $Suffix = date('Y');
                }elseif ($autoModel->Suffix == 'YY'){
                    $Suffix = date('y');
                }else{
                    $Suffix = $auto_no->Suffix;
                }
                $companyNo = implode($autoModel->SignChar, $arrParentNo);
                return $auto_no->Prefix.$companyNo.$Suffix;
            });
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = $auto;
            return json_encode($json);
        }else{
            $auto = DB::transaction(function () use ($table) {
                $auto_no = SysAutoNumber::where('NumberKey', $table)->first();
                if($auto_no->Prefix == 'MDV'){
//                    $Prefix = Session::get('CompanyNo');
                }elseif($auto_no->Prefix == 'MNV'){
//                    $Prefix = Session::get('EmployeeNo');
                }elseif($auto_no->Prefix == 'DD'){
                    $Prefix = date('d');
                }elseif ($auto_no->Prefix == 'MM'){
                    $Prefix = date('m');
                }elseif ($auto_no->Prefix == 'YYYY'){
                    $Prefix = date('Y');
                }elseif ($auto_no->Prefix == 'YY'){
                    $Prefix = date('y');
                }else{
                    $Prefix = $auto_no->Prefix;
                }
                if($auto_no->Suffix == 'MDV'){
//                    $Suffix = Session::get('CompanyNo');
                }elseif($auto_no->Suffix == 'MNV'){
//                    $Suffix = Session::get('EmployeeNo');
                }elseif($auto_no->Suffix == 'DD'){
                    $Suffix = date('d');
                }elseif ($auto_no->Suffix == 'MM'){
                    $Suffix = date('m');
                }elseif ($auto_no->Suffix == 'YYYY'){
                    $Suffix = date('Y');
                }elseif ($auto_no->Suffix == 'YY'){
                    $Suffix = date('y');
                }else{
                    $Suffix = $auto_no->Suffix;
                }
                if($auto_no->Prefix == 'MNV'){
                    $employee = Employee::where(['UserID' => Auth::user()->UserID])->first();
                    $AutoNoField = 'Auto'.ucfirst($table).'No';
                    $auto = $employee->$AutoNoField;
                    $employee->$AutoNoField = $auto + 1;
                    $employee->save();
                }else{
                    $auto = $auto_no->NumberValue;
                    $auto_no->NumberValue = (int)$auto + 1;
                    $auto_no->save();
                }
                $value = $auto;
                $valueArr = [];
                for($k = 1; $k <= 9; $k++){
                    $number = strlen($value);
                    $PadLeng = 'PadLeng'.$k;
                    $lengh = $auto_no->$PadLeng;
                    if($number <= $lengh && $lengh > 0){
                        for($i = 1; $i <= $lengh - $number; $i++){
                            $value = '0'.$value;
                        }
                        $valueArr[] = $value;
                        $value = '0';
                    }
                }
                if($valueArr){
                    $value = $Prefix.implode($auto_no->SignChar,$valueArr).$Suffix;
                }else{
                    $value = '';
                }

                return $value;
            });
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = $auto;
            return json_encode($json);
        }
    }

    //Get list Table
    public function getTable(){
        $table = SysTable::where(['isTaskLink' => 1])->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $table;
        return json_encode($json);
    }

    public function getProvince(Request $request)
    {
        $ProvinceName = $request->post('ProvinceName');
        $provinceQuery = DB::table('province');
        if ($ProvinceName) {
            $provinceQuery->where('ProvinceName', 'like', '%' . $ProvinceName . '%');
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $province = $provinceQuery->paginate($per_page);

        if ($province) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $province;
            return json_encode($json);
        }
        $json['status'] = 0;
        $json['msg'] = '';
        return json_encode($json);
    }

    public function getDistrict(Request $request)
    {
        $DistrictName = $request->post('DistrictName');
        $ProvinceID = $request->post('ProvinceID');
        $districtQuery = DB::table('district');
        if ($DistrictName) {
            $districtQuery->where('DistrictName', 'like', '%' . $DistrictName . '%');
        }

        if ($ProvinceID) {
            $districtQuery->where('ProvinceID', '=', $ProvinceID);
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $district = $districtQuery->paginate($per_page);

        if ($district) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $district;
            return json_encode($json);
        }
        $json['status'] = 0;
        $json['msg'] = '';
        return json_encode($json);
    }

    public function getCommune(Request $request){
        $CommuneName = $request->post('CommuneName');
        $ProvinceID = $request->post('ProvinceID');
        $DistrictID = $request->post('DistrictID');
        $communeQuery = DB::table('commune');
        if ($CommuneName) {
            $communeQuery->where('CommuneName', 'like', '%' . $CommuneName . '%');
        }
        if ($ProvinceID) {
            $communeQuery->where('ProvinceID', '=', $ProvinceID);
        }
        if ($DistrictID) {
            $communeQuery->where('DistrictID', '=', $DistrictID);
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $commune = $communeQuery->paginate($per_page);

        if ($commune) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $commune;
            return json_encode($json);
        }
        $json['status'] = 0;
        $json['msg'] = '';
        return json_encode($json);
    }

    public function getSector(Request $request){
        $SectorName = $request->post('SectorName');
        $sectorQuery = DB::table('sector');
        if ($SectorName) {
            $sectorQuery->where('SectorName', 'like', '%' . $SectorName . '%');
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $sector = $sectorQuery->paginate($per_page);

        if ($sector) {
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = $sector;
            return json_encode($json);
        }
        $json['status'] = 0;
        $json['msg'] = '';
        return json_encode($json);
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

    public function getReportTemplate(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $name = $request->post('name');

        $reportPath = public_path() . '/file-report/listing/' . $name . '.rdlx-json';
        $report = file_get_contents($reportPath, true);
        if ($report) {
            $json['status'] = 1;
            $json['data'] = $report;
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại file';
        }
        return json_encode($json);
    }

    public function getWorkdate(){
        $json = [
            'status' => 1,
            'msg' => 'Thanh công',
            'data' => \date('Y-m-d')
        ];
        return json_encode($json);
    }

    public function getParent(Request $request){
        $data = null;
        $auth = Auth::user();
        $table = $request->post('table');
        $FieldName = $request->post('FieldName');
        $FieldID = $request->post('FieldID');
        if($request->post('FieldNo')){
            $FieldNo = $request->post('FieldNo');
        }
        else{
            $FieldNo = $FieldID;
        }

        $query = DB::table($table);
        $autoModel = SysAutoNumber::where('NumberKey', $table)->first();
        if($autoModel){
            $query->where('Level','<',$autoModel->MaxLevel);
        }
        if($request->post('CurrentID')){
            $query->where($table.'.'.$FieldID, '!=',$request->post('CurrentID'));
        }
        if($request->post('search')){
            $query->where(function($q) use($FieldNo,$FieldName,$request, $table){
                $q->where($table.'.'.$FieldName, 'like', '%'.$request->search.'%')
                    ->orWhere($table.'.'.$FieldNo, 'like', '%'.$request->search.'%');
            });
        }

        // Phân quyền
        $tablePer = $table.'_per';
        $hasTable = Schema::hasTable($tablePer);
        if($hasTable){
            $query->select($table.'.*')->leftJoin($tablePer,$table.'.'.$FieldID,'=',$tablePer.'.'.$FieldID);
            $query->where(function ($q) use ($auth,$table,$tablePer,$FieldID){
                $q->where($tablePer.'.EmployeeID',$auth->EmployeeID)
                    ->orWhere($table.'.AccessType',2);
            });
            $query->groupBy($table.'.'.$FieldID);
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        if ($table == 'Task' || $table == 'task') {
            $query->orderBy('TaskID', 'DESC');
        }
        $data = $query->paginate($per_page);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function getNormTable(Request $request){
        $NormTable = NormTable::search($request);
        $json['data'] = $NormTable;
        $json['page'] = $request->get('page');
        return json_encode($json);
    }
}
