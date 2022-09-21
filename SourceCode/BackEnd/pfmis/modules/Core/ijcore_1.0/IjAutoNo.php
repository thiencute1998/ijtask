<?php
namespace IjCore;

class IjAutoNo {
    static function genNewNumberNo($table, $auto_no, $employee){
        if(isset($auto_no->Prefix)) {
            if ($auto_no->Prefix == 'MDV') {
                $Prefix = Session::get('CompanyNo');
            } elseif ($auto_no->Prefix == 'MNV') {
                $Prefix = Session::get('EmployeeNo');
            } elseif ($auto_no->Prefix == 'DD') {
                $Prefix = date('d');
            } elseif ($auto_no->Prefix == 'MM') {
                $Prefix = date('m');
            } elseif ($auto_no->Prefix == 'YYYY') {
                $Prefix = date('Y');
            } elseif ($auto_no->Prefix == 'YY') {
                $Prefix = date('y');
            } else {
                $Prefix = $auto_no->Prefix;
            }
            if ($auto_no->Suffix == 'MDV') {
                $Suffix = Session::get('CompanyNo');
            } elseif ($auto_no->Suffix == 'MNV') {
                $Suffix = Session::get('EmployeeNo');
            } elseif ($auto_no->Suffix == 'DD') {
                $Suffix = date('d');
            } elseif ($auto_no->Suffix == 'MM') {
                $Suffix = date('m');
            } elseif ($auto_no->Suffix == 'YYYY') {
                $Suffix = date('Y');
            } elseif ($auto_no->Suffix == 'YY') {
                $Suffix = date('y');
            } else {
                $Suffix = $auto_no->Suffix;
            }
            if ($auto_no->Prefix == 'MNV') {
                $AutoNoField = 'Auto' . ucfirst($table) . 'No';
                $auto = $employee->$AutoNoField;
                $employee->$AutoNoField = $auto + 1;
                $employee->save();
            } else {
                $auto = $auto_no->NumberValue;
                $auto_no->NumberValue = (int)$auto + 1;
                $auto_no->save();
            }
            $value = $auto;
            $valueArr = [];
            for ($k = 1; $k <= $auto_no->MaxLevel; $k++) {
                $number = strlen($value);
                $PadLeng = 'PadLeng' . $k;
                $lengh = $auto_no->$PadLeng;
                if ($number < $lengh && $lengh > 0) {
                    for ($i = 1; $i <= $lengh - $number; $i++) {
                        $value = '0' . $value;
                    }
                }
                $valueArr[] = $value;
                $value = 0;
            }
            if ($valueArr) {
                $value = $Prefix . implode($auto_no->SignChar, $valueArr) . $Suffix;
            } else {
                $value = '';
            }
            return $value;
        }else{
            return '';
        }
    }
}