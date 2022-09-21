<?php
namespace IjCore;
class IjDate {
    static function StringToDate($StringDate){
        return date('Y-m-d', strtotime(str_replace('/', '-', $StringDate)));
    }

    static function DateToString($Date, $Format){
        return date($Format, strtotime($Date));
    }

    static function DateAddNumberDay($Date, $NumberDay){
        $Date = date('Y-m-d', strtotime($Date. ' + '.$NumberDay.' days'));
        return $Date;
    }

    static function DateMinusNumberDay($Date, $NumberDay){
        $Date = date('Y-m-d', strtotime($Date. ' - '.$NumberDay.' days'));
        return $Date;
    }

    static function MinusTwoDate($date1, $date2){
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);

        $hourDiff=round(abs($date2 - $date1) / (60*60*24),0);
        return $hourDiff;
    }

    static function ConvertClientDateToServerDate($str){
        $datetimeArr = explode(' ', $str);
        $date = date('Y-m-d', strtotime(str_replace('/', '-', $datetimeArr[0])));
        if (isset($datetimeArr[1])) {
            $date = $date . ' ' . $datetimeArr[1];
        }
        return $date;
    }
}