<?php
namespace Module\Listing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AutoNo extends Model{
    protected $table = 'auto_no';

    protected $fillable = [
        'TableName', 'ValueAuto'
    ];
    public $timestamps = false;

    public static function UpdateAutoNo($ParentID, $ChildID, $table, $request){
        $tableUpFirst = ucfirst($table);
        $FieldNo = $tableUpFirst.'No';
        $FieldID = $tableUpFirst.'ID';
        $Parent = DB::table($table)->where($tableUpFirst.'ID', $ParentID)->first();
        $childModel = DB::table($table)->where($tableUpFirst.'ID', $ChildID)->first();
        $autoModel = SysAutoNumber::where('NumberKey', $table)->first();
        $NoOld = $childModel->$FieldNo;
        $LevelOld = $childModel->Level;
        $LenghHead = 0;
        for($i = 1; $i <= $LevelOld; $i++){
            $PadLeng = 'PadLeng'.$i;
            $LenghHead += $autoModel->$PadLeng;
        }
        if($autoModel->SignChar){
            $LenghHead = $LenghHead + $LevelOld - 1;
        }
        $HeadNo = substr($NoOld, 0, $LenghHead);
        $child = DB::table($table)->where($tableUpFirst.'No', 'like', $HeadNo.'%')->OrderBy('Level')->get();
        $LevelParentOld = $Parent->Level;
        $HeadParentNo = $Parent->$FieldNo;
        $LenghParentHead = 0;
        for($i = 1; $i <= $LevelParentOld; $i++){
            $PadLeng = 'PadLeng'.$i;
            $LenghParentHead += $autoModel->$PadLeng;
        }
        if($autoModel->SignChar){
            $LenghParentHead = $LenghParentHead + $LevelParentOld - 1;
        }
        $PadLeng = 'PadLeng'.$Parent->Level;
        $HeadParentNo = substr($Parent->$FieldNo, 0, $LenghParentHead);
        $positionEnd = $LenghParentHead + $autoModel->$PadLeng;
        if($autoModel->SignChar){
            $positionEnd = $positionEnd + 2;
        }
        $EndModelNo = substr($Parent->$FieldNo, $positionEnd);
        $ArrChild = [];
        foreach ($child as $item){
            $value = '';
            if($item->$FieldID == $childModel->$FieldID){
                $item->Head = $HeadParentNo;
                $item->ParentID = $Parent->$FieldID;
                $item->Level = $Parent->Level + 1;
                $item->NumberValue = 1;
                $valueNo = $Parent->NumberValue;
                $item->$FieldNo = $request->$FieldNo;
                $item->No = $valueNo;
                for($i = 1; $i <= $item->Level; $i++){
                    $PadLeng = 'PadLeng'.$i;
                    $LenghHead += $autoModel->PadLeng;
                }
                if($autoModel->SignChar){
                    $LenghHead = $LenghHead + $autoModel->MaxLevel - 1;
                }
                $item->Head = substr($request->$FieldNo, 0, $LenghHead);
            }else{
                $ParentItem = $ArrChild[$item->ParentID];
                $item->Level = $ParentItem->Level + 1;
                $ParentNumberValue = $ParentItem->NumberValue;

                $valueNo = $ParentNumberValue;
                $number = strlen($valueNo);
                $PadLeng = 'PadLeng'.$item->Level;
                $lengh = $autoModel->$PadLeng;
                for($i = 1; $i <= $lengh - $number; $i++){
                    $valueNo = '0'.$valueNo;
                }
                $arr = [];
                for($k = $item->Level + 1; $k <= $autoModel->MaxLevel; $k++){
                    $value = '';
                    $PadLeng = 'PadLeng'.$k;
                    $lengh = $autoModel->$PadLeng;
                    for($i = 1; $i <= $lengh; $i++){
                        $value = '0'.$value;
                    }
                    $arr[] = $value;
                }
                $End = implode($autoModel->SignChar, $arr);
                $item->Head = $ParentItem->Head.$autoModel->SignChar.$valueNo;
                if($End){
                    $item->$FieldNo = $ParentItem->Head.$autoModel->SignChar.$valueNo.$autoModel->SignChar.$End;
                }else{
                    $item->$FieldNo = $ParentItem->Head.$autoModel->SignChar.$valueNo;
                }
                $item->No = $valueNo;
                $item->NumberValue = 1;
                $item->Check = 1;
                $ArrChild[$item->ParentID]->NumberValue = $ArrChild[$item->ParentID]->NumberValue + 1;
            }
            $ArrChild[$item->$FieldID] = $item;
            if($item->Level > $autoModel->MaxLevel){
                return false;
            }
        }
        foreach($ArrChild as $item){
            $check[] = DB::table($table)->where(''.$FieldID.'', $item->$FieldID)
                ->update(['NumberValue' => $item->NumberValue, ''.$FieldNo.'' => $item->$FieldNo, 'Level' => $item->Level, 'ParentID' => $item->ParentID]);
        }
        return true;
    }

    public static function UpdateValidate($validator, $request, $model, $table, $autoModel, $texttable){

        $tableUpFirst = ucfirst($table);
        $FieldNo = $tableUpFirst.'No';
        $FieldID = $tableUpFirst.'ID';

        $Prefix = substr($request->$FieldNo, 0, strlen($model->Prefix));
        $CompanyNoRequest = substr($request->$FieldNo, strlen($model->Prefix), $autoModel->PadLeng);
        $Suffix = substr($request->$FieldNo, strlen($model->Prefix) + $autoModel->PadLeng);
        $request->$FieldNo = $CompanyNoRequest;
        if($autoModel->Suffix == 'MNV'){
            $Suffix = auth()->user()->EmployeeNo;
        }
        $tableUpFirst = ucfirst($table);
        $FieldNo = $tableUpFirst.'No';
        $FieldID = $tableUpFirst.'ID';
        $validator->ParentLevel = 0;
        $validator->after(function ($validator) use ($request, $autoModel, $FieldNo, $FieldID, $Prefix, $Suffix, $model, $texttable) {
            $ModelNo = $request->$FieldNo;
            $ModelNoTemp = str_replace($autoModel->SignChar, '', $ModelNo);
            if(!is_numeric($ModelNoTemp) || strlen($ModelNo) != $autoModel->PadLeng){
                $validator->errors()->add($FieldNo, 'Mã '.$texttable.' không phù hợp!');
            }else{
                if ($Prefix !== $model->Prefix || $Suffix !== $model->Suffix) {
                    $validator->errors()->add($FieldNo, 'Mã '.$texttable.' không phù hợp!');
                }
            }
        });
        if(!$validator->fails()) {
            $validator->after(function ($validator) use ($request, $autoModel, $model, $FieldNo, $table, $FieldID, $texttable) {
                if ($request->ParentID) {
                    $Parent = DB::table($table)->where($FieldID, $request->ParentID)->first();
                    $ParentNo = $Parent->$FieldNo;
                    $ModelNo = $request->$FieldNo;
                    $validator->ParentLevel = $Parent->Level;
                    $ModelNoTemp = str_replace($autoModel->SignChar, '', $ModelNo);
                    $ModelNoParentTemp = str_replace($autoModel->SignChar, '', $ParentNo);
                    $ModelNoTempToParent = '';
                    $arrParentNoTemp = [];
                    for ($i = 1; $i <= 9; $i++) {
                        $lengh = 'PadLeng' . $i;
                        $strTemp = substr($ModelNoTemp, 0, (int)$autoModel->$lengh);
                        $ModelNoTemp = substr($ModelNoTemp, (int)$autoModel->$lengh);
                        if ($i == $Parent->Level + 1) {
                            $strZero = '';
                            for ($k = 1; $k <= (int)$autoModel->$lengh; $k++) {
                                $strZero .= '0';
                            }
                            $arrParentNoTemp[] = $strZero;
                            $ModelNoTempToParent .= $autoModel->SignChar.$strZero . $ModelNoTemp;
                            $validator->HeadNo = $ModelNoTempToParent;
                            break;
                        } else {
                            $SignChar = !empty($ModelNoTempToParent)? $autoModel->SignChar: '';
                            $ModelNoTempToParent .= $SignChar.$strTemp;
                            $arrParentNoTemp[] .= $strTemp;
                        }
                    }
                    if ($ParentNo != $ModelNoTempToParent) {
                        $validator->errors()->add("" . $FieldNo . "", 'Mã '.$texttable.' không phù hợp!');
                    }

                    if ($request->ParentID != $model->ParentID) {
                        $check = AutoNo::UpdateAutoNo($request->ParentID, $model->$FieldID, $table, $request);
                        if (!$check) {
                            DB::rollBack();
                        }
                    }else{
                        $LenghHead = 0;
                        $Level = $model->Level;
                        for ($i = 1; $i <= $Level; $i++) {
                            $PadLeng = 'PadLeng' . $i;
                            $LenghHead += $autoModel->$PadLeng;
                        }
                        if ($autoModel->SignChar) {
                            $LenghHead += $Level - 1;
                        }
                        $PadLengLevel = 'PadLeng' . $Level;
                        $LengLevel = $autoModel->$PadLengLevel;
                        $FromEnd = $LenghHead + 1;
                        $FileNoTempLevel = substr($request->$FieldNo, $LenghHead - $LengLevel, $LengLevel);
                        DB::update(DB::raw("update " . $table . "
                            set " . $FieldNo . " = concat(substring(" . $FieldNo . ", 1, " . ($LenghHead - $LengLevel) . "), '".$FileNoTempLevel."', substring(" . $FieldNo . ", " . ($LenghHead + 1) . "))
                            WHERE " . $FieldNo . " like('" . substr($model->$FieldNo, 0, $LenghHead) . "%')"));
                    }
                } else {
                    $ModelNo = $request->$FieldNo;
                    if ($autoModel->SignChar) {
                        $arrModelNo = explode($autoModel->SignChar, $ModelNo);
                        if (sizeof($arrModelNo) != $autoModel->MaxLevel) {
                            $validator->errors()->add($FieldNo, 'Mã '.$texttable.' không phù hợp!');
                        } else {
                            $i = 1;
                            foreach ($arrModelNo as $item) {
                                $lengh = 'PadLeng' . $i;
                                if ($autoModel->$lengh != strlen($item)) {
                                    $validator->errors()->add($FieldNo, 'Mã '.$texttable.' không phù hợp!');
                                    break;
                                }
                                if($i > 1 && (int)$item != 0){
                                    $validator->errors()->add($FieldNo, 'Mã '.$texttable.' không phù hợp!');
                                    break;
                                }
                                $i++;
                            }
                        }
                    }else{
                        for($i = $autoModel->PadLeng1; $i < $autoModel->PadLeng - 1; $i++){
                            if($ModelNo[$i] != '0'){
                                $validator->errors()->add($FieldNo, 'Mã '.$texttable.' không phù hợp!');
                                break;
                            }
                        }
                    }
                    $LenghHead = 0;
                    $Level = $model->Level;
                    for ($i = 1; $i <= $Level; $i++) {
                        $PadLeng = 'PadLeng' . $i;
                        $LenghHead += $autoModel->$PadLeng;
                    }
                    if ($autoModel->SignChar) {
                        $LenghHead += $Level - 1;
                    }
                    $PadLengLevel = 'PadLeng' . $Level;
                    $LengLevel = $autoModel->$PadLengLevel;
                    $FromEnd = $LenghHead + 1;
                    $FileNoTempLevel = substr($request->$FieldNo, $LenghHead - $LengLevel, $LengLevel);
                    DB::update(DB::raw("update " . $table . "
                            set " . $FieldNo . " = concat(substring(" . $FieldNo . ", 1, " . ($LenghHead - $LengLevel) . "), '".$FileNoTempLevel."', substring(" . $FieldNo . ", " . ($LenghHead + 1) . "))
                            WHERE " . $FieldNo . " like('" . substr($model->$FieldNo, 0, $LenghHead) . "%')"));
                }
            });
        }
    }
    public static function UpdateValidateIDNO($validator, $request, $model, $table, $autoModel, $texttable, $FieldNo, $FieldID){

        $tableUpFirst = ucfirst($table);
        $FieldNo = $FieldNo;
        $FieldID = $FieldID;

        $Prefix = substr($request->$FieldNo, 0, strlen($model->Prefix));
        $CompanyNoRequest = substr($request->$FieldNo, strlen($model->Prefix), $autoModel->PadLeng);
        $Suffix = substr($request->$FieldNo, strlen($model->Prefix) + $autoModel->PadLeng);
        $request->$FieldNo = $CompanyNoRequest;
        if($autoModel->Suffix == 'MNV'){
            $Suffix = auth()->user()->EmployeeNo;
        }
        $tableUpFirst = ucfirst($table);
        $validator->ParentLevel = 0;
        $validator->after(function ($validator) use ($request, $autoModel, $FieldNo, $Prefix, $Suffix, $model, $texttable) {
            $ModelNo = $request->$FieldNo;
            $ModelNoTemp = str_replace($autoModel->SignChar, '', $ModelNo);
            if(!is_numeric($ModelNoTemp) || strlen($ModelNo) != $autoModel->PadLeng){
                $validator->errors()->add($FieldNo, 'Mã '.$texttable.' không phù hợp!');
            }else{
                if ($Prefix !== $model->Prefix || $Suffix !== $model->Suffix) {
                    $validator->errors()->add($FieldNo, 'Mã '.$texttable.' không phù hợp!');
                }
            }
        });
        if(!$validator->fails()) {
            $validator->after(function ($validator) use ($request, $autoModel, $model, $FieldNo, $table, $FieldID, $texttable) {
                if ($request->ParentID) {
                    $Parent = DB::table($table)->where($FieldID, $request->ParentID)->first();
                    $ParentNo = $Parent->$FieldNo;
                    $ModelNo = $request->$FieldNo;
                    $validator->ParentLevel = $Parent->Level;
                    $ModelNoTemp = str_replace($autoModel->SignChar, '', $ModelNo);
                    $ModelNoParentTemp = str_replace($autoModel->SignChar, '', $ParentNo);
                    $ModelNoTempToParent = '';
                    $arrParentNoTemp = [];
                    for ($i = 1; $i <= 9; $i++) {
                        $lengh = 'PadLeng' . $i;
                        $strTemp = substr($ModelNoTemp, 0, (int)$autoModel->$lengh);
                        $ModelNoTemp = substr($ModelNoTemp, (int)$autoModel->$lengh);
                        if ($i == $Parent->Level + 1) {
                            $strZero = '';
                            for ($k = 1; $k <= (int)$autoModel->$lengh; $k++) {
                                $strZero .= '0';
                            }
                            $arrParentNoTemp[] = $strZero;
                            $ModelNoTempToParent .= $autoModel->SignChar.$strZero . $ModelNoTemp;
                            $validator->HeadNo = $ModelNoTempToParent;
                            break;
                        } else {
                            $SignChar = !empty($ModelNoTempToParent)? $autoModel->SignChar: '';
                            $ModelNoTempToParent .= $SignChar.$strTemp;
                            $arrParentNoTemp[] .= $strTemp;
                        }
                    }
                    if ($ParentNo != $ModelNoTempToParent) {
                        $validator->errors()->add("" . $FieldNo . "", 'Mã '.$texttable.' không phù hợp!');
                    }

                    if ($request->ParentID != $model->ParentID) {
                        $check = AutoNo::UpdateAutoNo($request->ParentID, $model->$FieldID, $table, $request);
                        if (!$check) {
                            DB::rollBack();
                        }
                    }else{
                        $LenghHead = 0;
                        $Level = $model->Level;
                        for ($i = 1; $i <= $Level; $i++) {
                            $PadLeng = 'PadLeng' . $i;
                            $LenghHead += $autoModel->$PadLeng;
                        }
                        if ($autoModel->SignChar) {
                            $LenghHead += $Level - 1;
                        }
                        $PadLengLevel = 'PadLeng' . $Level;
                        $LengLevel = $autoModel->$PadLengLevel;
                        $FromEnd = $LenghHead + 1;
                        $FileNoTempLevel = substr($request->$FieldNo, $LenghHead - $LengLevel, $LengLevel);
                        DB::update(DB::raw("update " . $table . "
                            set " . $FieldNo . " = concat(substring(" . $FieldNo . ", 1, " . ($LenghHead - $LengLevel) . "), '".$FileNoTempLevel."', substring(" . $FieldNo . ", " . ($LenghHead + 1) . "))
                            WHERE " . $FieldNo . " like('" . substr($model->$FieldNo, 0, $LenghHead) . "%')"));
                    }
                } else {
                    $ModelNo = $request->$FieldNo;
                    if ($autoModel->SignChar) {
                        $arrModelNo = explode($autoModel->SignChar, $ModelNo);
                        if (sizeof($arrModelNo) != $autoModel->MaxLevel) {
                            $validator->errors()->add($FieldNo, 'Mã '.$texttable.' không phù hợp!');
                        } else {
                            $i = 1;
                            foreach ($arrModelNo as $item) {
                                $lengh = 'PadLeng' . $i;
                                if ($autoModel->$lengh != strlen($item)) {
                                    $validator->errors()->add($FieldNo, 'Mã '.$texttable.' không phù hợp!');
                                    break;
                                }
                                if($i > 1 && (int)$item != 0){
                                    $validator->errors()->add($FieldNo, 'Mã '.$texttable.' không phù hợp!');
                                    break;
                                }
                                $i++;
                            }
                        }
                    }else{
                        for($i = $autoModel->PadLeng1; $i < $autoModel->PadLeng - 1; $i++){
                            if($ModelNo[$i] != '0'){
                                $validator->errors()->add($FieldNo, 'Mã '.$texttable.' không phù hợp!');
                                break;
                            }
                        }
                    }
                    $LenghHead = 0;
                    $Level = $model->Level;
                    for ($i = 1; $i <= $Level; $i++) {
                        $PadLeng = 'PadLeng' . $i;
                        $LenghHead += $autoModel->$PadLeng;
                    }
                    if ($autoModel->SignChar) {
                        $LenghHead += $Level - 1;
                    }
                    $PadLengLevel = 'PadLeng' . $Level;
                    $LengLevel = $autoModel->$PadLengLevel;
                    $FromEnd = $LenghHead + 1;
                    $FileNoTempLevel = substr($request->$FieldNo, $LenghHead - $LengLevel, $LengLevel);
                    DB::update(DB::raw("update " . $table . "
                            set " . $FieldNo . " = concat(substring(" . $FieldNo . ", 1, " . ($LenghHead - $LengLevel) . "), '".$FileNoTempLevel."', substring(" . $FieldNo . ", " . ($LenghHead + 1) . "))
                            WHERE " . $FieldNo . " like('" . substr($model->$FieldNo, 0, $LenghHead) . "%')"));
                }
            });
        }
    }
    public static function genNewNumberNo($table){
        $auto = DB::transaction(function () use ($table){
            $auto_no = SysAutoNumber::where('NumberKey', $table)->first();
            if($auto_no->Prefix == 'MDV'){
                $Prefix = auth()->user()->CompanyNo;
            }elseif($auto_no->Prefix == 'MNV'){
                $Prefix = auth()->user()->EmployeeNo;
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
                $Suffix = auth()->user()->CompanyNo;
            }elseif($auto_no->Suffix == 'MNV'){
                $Suffix = auth()->user()->EmployeeNo;
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
            for($k = 1; $k <= $auto_no->MaxLevel; $k++){
                $number = strlen($value);
                $PadLeng = 'PadLeng'.$k;
                $lengh = $auto_no->$PadLeng;
                if($number < $lengh && $lengh > 0){
                    for($i = 1; $i <= $lengh - $number; $i++){
                        $value = '0'.$value;
                    }
                }
                $valueArr[] = $value;
                $value = 0;
            }
            if($valueArr){
                $value = $Prefix.implode($auto_no->SignChar,$valueArr).$Suffix;
            }else{
                $value = '';
            }
            return $value;
        });
        return $auto;
    }
    public static function genNewNumberNoTable($table, $tableID, $tableNo){
        $auto = DB::transaction(function () use ($table, $tableID, $tableNo){
            $auto_no = SysAutoNumber::where('NumberKey', $table)->first();
            if($auto_no->Prefix == 'MDV'){
                $Prefix = auth()->user()->CompanyNo;
            }elseif($auto_no->Prefix == 'MNV'){
                $Prefix = auth()->user()->EmployeeNo;
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
                $Suffix = auth()->user()->CompanyNo;
            }elseif($auto_no->Suffix == 'MNV'){
                $Suffix = auth()->user()->EmployeeNo;
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
                $AutoNoField = $tableNo;
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
            for($k = 1; $k <= $auto_no->MaxLevel; $k++){
                $number = strlen($value);
                $PadLeng = 'PadLeng'.$k;
                $lengh = $auto_no->$PadLeng;
                if($number < $lengh && $lengh > 0){
                    for($i = 1; $i <= $lengh - $number; $i++){
                        $value = '0'.$value;
                    }
                }
                $valueArr[] = $value;
                $value = 0;
            }
            if($valueArr){
                $value = $Prefix.implode($auto_no->SignChar,$valueArr).$Suffix;
            }else{
                $value = '';
            }
            return $value;
        });
        return $auto;
    }

    public static function storeValidate($validator, $request, $table, $autoModel, $textkey){
        if($autoModel->Prefix == 'MDV'){
            $autoModel->Prefix = auth()->user()->CompanyNo;//auth()->user()->CompanyNo;
        }elseif($autoModel->Prefix == 'MNV'){
            $autoModel->Prefix = auth()->user()->EmployeeNo;//auth()->user()->EmployeeNo;
        }elseif($autoModel->Prefix == 'DD'){
            $autoModel->Prefix = date('d');
        }elseif ($autoModel->Prefix == 'MM'){
            $autoModel->Prefix = date('m');
        }elseif ($autoModel->Prefix == 'YYYY'){
            $autoModel->Prefix = date('Y');
        }elseif ($autoModel->Prefix == 'YY'){
            $autoModel->Prefix = date('y');
        }else{
            $autoModel->Prefix = $autoModel->Prefix;
        }
        if($autoModel->Suffix == 'MDV'){
            $autoModel->Suffix = auth()->user()->CompanyNo;//auth()->user()->CompanyNo;
        }elseif($autoModel->Suffix == 'MNV'){
            $autoModel->Suffix = auth()->user()->EmployeeNo;//auth()->user()->EmployeeNo;
        }elseif($autoModel->Suffix == 'DD'){
            $autoModel->Suffix = date('d');
        }elseif ($autoModel->Suffix == 'MM'){
            $autoModel->Suffix = date('m');
        }elseif ($autoModel->Suffix == 'YYYY'){
            $autoModel->Suffix = date('Y');
        }elseif ($autoModel->Suffix == 'YY'){
            $autoModel->Suffix = date('y');
        }else{
            $autoModel->Suffix = $autoModel->Suffix;
        }
        $tableUpFirst = ucfirst($table);
        $FieldNo = $tableUpFirst.'No';
        $FieldID = $tableUpFirst.'ID';
        $Prefix = substr($request->$FieldNo, 0, strlen($autoModel->Prefix));
        $ModelNoRequest = substr($request->$FieldNo, strlen($autoModel->Prefix), $autoModel->PadLeng);
        $Suffix = substr($request->$FieldNo, strlen($autoModel->Prefix) + $autoModel->PadLeng);
        $request->$FieldNo = $ModelNoRequest;
        $validator->after(function ($validator) use ($request, $autoModel, $FieldNo, $Suffix, $Prefix, $textkey) {
            $ModelNo = $request->$FieldNo;
            $ModelNoTemp = str_replace($autoModel->SignChar, '', $ModelNo);
            if(!is_numeric($ModelNoTemp) || strlen($ModelNo) != $autoModel->PadLeng){
                $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
            }else{
                if ($Prefix.'' != $autoModel->Prefix.'' || $Suffix.'' != $autoModel->Suffix.'') {
                    $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
                }
            }

        });

        if(!$validator->fails()) {
            $validator->after(function ($validator) use ($request, $autoModel, $FieldNo, $FieldID, $table, $textkey) {
                if ($request->ParentID) {
                    $Parent = DB::table($table)->where($FieldID, $request->ParentID)->first();
                    if($Parent){
                        $ParentNo = $Parent->$FieldNo;
                        $ModelNo = $request->$FieldNo;
                        $validator->ParentLevel = $Parent->Level;
                        $ModelNoTemp = str_replace($autoModel->SignChar, '', $ModelNo);
                        $ModelNoParentTemp = str_replace($autoModel->SignChar, '', $ParentNo);
                        $ModelNoTempToParent = '';
                        $arrParentNoTemp = [];
                        $SignChar = '';
                        for ($i = 1; $i <= $autoModel->MaxLevel; $i++) {
                            $lengh = 'PadLeng' . $i;
                            $strTemp = substr($ModelNoTemp, 0, (int)$autoModel->$lengh);
                            $ModelNoTemp = substr($ModelNoTemp, (int)$autoModel->$lengh);
                            if ($i == $Parent->Level + 1) {
                                $strZero = '';
                                for ($k = 1; $k <= (int)$autoModel->$lengh; $k++) {
                                    $strZero .= '0';
                                }
                                $arrParentNoTemp[] = $strZero;
                                $ModelNoTempToParent .= $autoModel->SignChar.$strZero;
                            } else {
                                $arrParentNoTemp[] = $strTemp;
                                $SignChar = !empty($ModelNoTempToParent)? $autoModel->SignChar: '';
                                $ModelNoTempToParent = $ModelNoTempToParent.$SignChar.$strTemp;
                            }

                        }
                        if ($ParentNo != $ModelNoTempToParent) {
                            $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!2'.$ModelNoTempToParent);
                        }
                    }else{
                        $validator->errors()->add($FieldNo, 'Mã cha không tồn tại!4');
                    }


                } else {
                    $ModelNo = $request->$FieldNo;
                    if ($autoModel->SignChar) {
                        $arrModelNo = explode($autoModel->SignChar, $ModelNo);
                        if (sizeof($arrModelNo) != $autoModel->MaxLevel) {
                            $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!5');
                        } else {
                            $i = 1;
                            foreach ($arrModelNo as $item) {
                                $lengh = 'PadLeng' . $i;
                                if ($autoModel->$lengh != strlen($item)) {
                                    $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!6');
                                    break;
                                }
                                $i++;
                            }
                        }
                    }else{
                        for($i = $autoModel->PadLeng1; $i < $autoModel->PadLeng - 1; $i++){
                            if($ModelNo[$i] != '0'){
                                $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!8');
                                break;
                            }
                        }
                    }
                }
            });
        }
    }
    public static function storeValidateIDNo($validator, $request, $table, $tableID, $tableNo, $autoModel, $textkey){
        if($autoModel->Prefix == 'MDV'){
            $autoModel->Prefix = auth()->user()->CompanyNo;//auth()->user()->CompanyNo;
        }elseif($autoModel->Prefix == 'MNV'){
            $autoModel->Prefix = auth()->user()->EmployeeNo;//auth()->user()->EmployeeNo;
        }elseif($autoModel->Prefix == 'DD'){
            $autoModel->Prefix = date('d');
        }elseif ($autoModel->Prefix == 'MM'){
            $autoModel->Prefix = date('m');
        }elseif ($autoModel->Prefix == 'YYYY'){
            $autoModel->Prefix = date('Y');
        }elseif ($autoModel->Prefix == 'YY'){
            $autoModel->Prefix = date('y');
        }else{
            $autoModel->Prefix = $autoModel->Prefix;
        }
        if($autoModel->Suffix == 'MDV'){
            $autoModel->Suffix = auth()->user()->CompanyNo;//auth()->user()->CompanyNo;
        }elseif($autoModel->Suffix == 'MNV'){
            $autoModel->Suffix = auth()->user()->EmployeeNo;//auth()->user()->EmployeeNo;
        }elseif($autoModel->Suffix == 'DD'){
            $autoModel->Suffix = date('d');
        }elseif ($autoModel->Suffix == 'MM'){
            $autoModel->Suffix = date('m');
        }elseif ($autoModel->Suffix == 'YYYY'){
            $autoModel->Suffix = date('Y');
        }elseif ($autoModel->Suffix == 'YY'){
            $autoModel->Suffix = date('y');
        }else{
            $autoModel->Suffix = $autoModel->Suffix;
        }
        $tableUpFirst = ucfirst($table);
        $FieldNo = $tableNo;
        $FieldID = $tableID;
        $Prefix = substr($request->$FieldNo, 0, strlen($autoModel->Prefix));
        $ModelNoRequest = substr($request->$FieldNo, strlen($autoModel->Prefix), $autoModel->PadLeng);
        $Suffix = substr($request->$FieldNo, strlen($autoModel->Prefix) + $autoModel->PadLeng);
        $request->$FieldNo = $ModelNoRequest;
        $validator->after(function ($validator) use ($request, $autoModel, $FieldNo, $Suffix, $Prefix, $textkey) {
            $ModelNo = $request->$FieldNo;
            $ModelNoTemp = str_replace($autoModel->SignChar, '', $ModelNo);
            if(!is_numeric($ModelNoTemp) || strlen($ModelNo) != $autoModel->PadLeng){
                $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
            }else{
                if ($Prefix.'' != $autoModel->Prefix.'' || $Suffix.'' != $autoModel->Suffix.'') {
                    $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
                }
            }

        });
        if(!$validator->fails()) {
            $validator->after(function ($validator) use ($request, $autoModel, $FieldNo, $FieldID, $table, $textkey) {
                if (isset($request->ParentID) && $request->ParentID) {
                    $Parent = DB::table($table)->where($FieldID, $request->ParentID)->first();
                    if($Parent){
                        $ParentNo = $Parent->$FieldNo;
                        $ModelNo = $request->$FieldNo;
                        $validator->ParentLevel = $Parent->Level;
                        $ModelNoTemp = str_replace($autoModel->SignChar, '', $ModelNo);
                        $ModelNoParentTemp = str_replace($autoModel->SignChar, '', $ParentNo);
                        $ModelNoTempToParent = '';
                        $arrParentNoTemp = [];
                        $SignChar = '';
                        for ($i = 1; $i <= $autoModel->MaxLevel; $i++) {
                            $lengh = 'PadLeng' . $i;
                            $strTemp = substr($ModelNoTemp, 0, (int)$autoModel->$lengh);
                            $ModelNoTemp = substr($ModelNoTemp, (int)$autoModel->$lengh);
                            if ($i == $Parent->Level + 1) {
                                $strZero = '';
                                for ($k = 1; $k <= (int)$autoModel->$lengh; $k++) {
                                    $strZero .= '0';
                                }
                                $arrParentNoTemp[] = $strZero;
                                $ModelNoTempToParent .= $autoModel->SignChar.$strZero;
                            } else {
                                $arrParentNoTemp[] = $strTemp;
                                $SignChar = !empty($ModelNoTempToParent)? $autoModel->SignChar: '';
                                $ModelNoTempToParent = $ModelNoTempToParent.$SignChar.$strTemp;
                            }

                        }
                        if ($ParentNo != $ModelNoTempToParent) {
                            $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!'.$ModelNoTempToParent);
                        }
                    }else{
                        $validator->errors()->add($FieldNo, 'Mã cha không tồn tại!4');
                    }


                } else {
                    $ModelNo = $request->$FieldNo;
                    if ($autoModel->SignChar) {
                        $arrModelNo = explode($autoModel->SignChar, $ModelNo);
                        if (sizeof($arrModelNo) != $autoModel->MaxLevel) {
                            $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
                        } else {
                            $i = 1;
                            foreach ($arrModelNo as $item) {
                                $lengh = 'PadLeng' . $i;
                                if ($autoModel->$lengh != strlen($item)) {
                                    $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
                                    break;
                                }
                                $i++;
                            }
                        }
                    }else{
                        for($i = $autoModel->PadLeng1; $i < $autoModel->PadLeng - 1; $i++){
                            if($ModelNo[$i] != '0'){
                                $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
                                break;
                            }
                        }
                    }
                }
            });
        }
    }
    public static function UpdateValidateNotParentAndChild($validator, $request, $model, $table, $autoModel, $textkey){

        $tableUpFirst = ucfirst($table);
        $FieldNo = $tableUpFirst.'No';
        $FieldID = $tableUpFirst.'ID';
        $Prefix = substr($request->$FieldNo, 0, strlen($model->Prefix));
        $ModelNoRequest = substr($request->$FieldNo, strlen($model->Prefix), $autoModel->PadLeng);
        $Suffix = substr($request->$FieldNo, strlen($model->Prefix) + $autoModel->PadLeng);
        $request->$FieldNo = $ModelNoRequest;

        $validator->after(function ($validator) use ($request, $autoModel, $FieldNo, $FieldID, $Prefix, $Suffix, $model, $textkey) {
            $ModelNo = $request->$FieldNo;
            $ModelNoTemp = str_replace($autoModel->SignChar, '', $ModelNo);
            if(!is_numeric($ModelNoTemp) || strlen($ModelNo) != $autoModel->PadLeng){
                $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
            }else{
                if ($Prefix !== $model->Prefix || $Suffix !== $model->Suffix) {
                    $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
                }
            }
        });
        if(!$validator->fails()) {
            $validator->after(function ($validator) use ($request, $autoModel, $model, $FieldNo, $table, $FieldID, $textkey) {

                $ModelNo = $request->$FieldNo;
                if ($autoModel->SignChar) {
                    $arrModelNo = explode($autoModel->SignChar, $ModelNo);
                    if (sizeof($arrModelNo) != $autoModel->MaxLevel) {
                        $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
                    } else {
                        $i = 1;
                        foreach ($arrModelNo as $item) {
                            $lengh = 'PadLeng' . $i;
                            if ($autoModel->$lengh != strlen($item)) {
                                $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
                                break;
                            }
                            if($i > 1 && (int)$item != 0){
                                $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
                                break;
                            }
                            $i++;
                        }
                    }
                }else{
                    for($i = $autoModel->PadLeng1; $i < $autoModel->PadLeng - 1; $i++){
                        if($ModelNo[$i] != '0'){
                            $validator->errors()->add($FieldNo, 'Mã '.$textkey.' không phù hợp!');
                            break;
                        }
                    }
                }

            });
        }
    }
}

?>
