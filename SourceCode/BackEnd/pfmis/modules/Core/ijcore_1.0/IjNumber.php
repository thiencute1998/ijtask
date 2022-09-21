<?php
namespace IjCore;

class IjNumber {
    //Convert String to Number
    static function ParseNumber($number, $decimalString){
        $number = preg_replace("/[^0-9".$decimalString."]/", "", $number);
        if($decimalString != '.'){
            $number = str_replace($decimalString, ".", $number);
        }
        return $number;
    }


    //Number Format
    static function NumberFormat($number, $decimalString, $thousandString){
        $arrNumber = explode('.', $number);

        $number = self::NumberFormatInt($arrNumber[0], $thousandString);
        if(isset($arrNumber[1])){
            $number .= $decimalString.$arrNumber[1];
        }
        return $number;
    }
    //Number Format Int
    static function NumberFormatInt($number, $thousandString){
        $lengthNumber = strlen($number);
        if($lengthNumber <= 3){
            return $number;
        }else{
            $Remainder = $lengthNumber%3;
            if($Remainder){
                $arrNumber[] = substr($number, 0, $Remainder);
            }
            for($i = 0; $i < ($lengthNumber - $Remainder)/3; $i++){
                $arrNumber[] = substr($number, $Remainder + $i * 3, 3);
            }
            return implode($thousandString, $arrNumber);
        }
    }
    //Convert Number to String Number VietNamese
    static function ConvertNumberToString($number){
        $arr_number = ['Không', 'Một', 'Hai', 'Ba', 'Bốn', 'Năm', 'Sáu', 'Bảy', 'Tám', 'Chín'];
        $arr_unit = ['', 'Nghìn', 'Triệu'];
        $modAll = strlen($number)%9;
        $intAll = (int)(strlen($number)/9);
        $str = '';
        if($modAll != 0){
            $number_begin = substr($number, 0, $modAll);
            $leng = strlen($number_begin);
            $sodu = $leng%3;
            if($sodu != 0){
                for ($i = 1; $i <= 3 - $sodu; $i++){
                    $number_begin = '0'.$number_begin;
                }
            }
            $number_unit = strlen($number_begin)/3;
            for ($i = 0; $i < $number_unit; $i++){
                $string = substr($number_begin, $i*3, 3);
                if((int)$string[0] + (int)$string[1] + (int)$string[2] != 0){
                    if($i != 0){
                        $str = $str . ' ' . $arr_number[$string[0]] .' Trăm';
                    }
                }
                if((int)$string[1] + (int)$string[2] != 0){
                    if($string[1] === '0'){
                        if($i != 0) {
                            $str = $str . ' Không';
                        }
                    }else if($string[1] === '1'){
                        $str = $str . ' Mười';
                    }else{
                        $str = $str . ' ' . $arr_number[$string[1]] . ' Mươi';
                    }
                }
                if($string[2] !== '0'){
                    if($string[2] == '5') {
                        if ($string[1] == '0') {
                            $str = $str . ' ' . $arr_number[$string[2]];
                        } else {
                            $str = $str . ' Lăm';
                        }
                    }else{
                        $str = $str . ' ' . $arr_number[$string[2]];
                    }
                }
                if($arr_unit[$number_unit - $i -1] && (int)$string[0] + (int)$string[1] + (int)$string[2] != 0){
                    $str = $str . ' ' . $arr_unit[$number_unit - $i -1];
                }
            }

            if($str){
                for($i = 1; $i <= $intAll; $i++){
                    $str = $str . ' Tỷ';
                }
            }
        }
        if($intAll > 0){
            for($k = 0; $k < $intAll; $k++){
                $leng = 9;
                $number_unit = 3;
                $stringTy = substr($number, $k*9 + $modAll, 9);
                for ($i = 0; $i < $number_unit; $i++){
                    $string = substr($stringTy,$i*3, 3);
                    if((int)$string[0] + (int)$string[1] + (int)$string[2] != 0){
                        if($i != 0) {
                            $str = $str . ' ' . $arr_number[$string[0]] . ' Trăm';
                        }
                    }
                    if((int)$string[1] + (int)$string[2] != 0){
                        if($string[1] === '0'){
                            if($i != 0) {
                                $str = $str . ' Không';
                            }
                        }else if($string[1] === '1'){
                            $str = $str . ' Mười';
                        }else{
                            $str = $str . ' ' . $arr_number[$string[1]] . ' Mươi';
                        }
                    }
                    if($string[2] !== '0'){
                        if($string[2] == '5') {
                            if ($string[1] == '0') {
                                $str = $str . ' ' . $arr_number[$string[2]];
                            } else {
                                $str = $str . ' Lăm';
                            }
                        }else{
                            $str = $str . ' ' . $arr_number[$string[2]];
                        }
                    }
                    if($string != '000'){
                        if($arr_unit[$number_unit - $i -1]){
                            $str = $str . ' ' . $arr_unit[$number_unit - $i -1];
                        }
                    }

                }


                for($h = 0; $h < $intAll - $k - 1; $h++){
                    $str = $str . ' Tỷ';
                }

            }
        }
        return $str;
    }

    //Convert Number to unit Thousand
    static function ConvertNumberToThousand($number){
        $number = round($number/1000);
        return $number;
    }

    //Convert Number to unit Million
    static function ConvertNumberToMillion($number){
        $number = round($number/1000000);
        return $number;
    }

    public static function ConvertNumberToRoman($number)
    {
        switch($number) {
            case 1: return 'I';
            case 2: return 'II';
            case 3: return 'III';
            case 4: return 'IV';
            case 5: return 'V';
            case 6: return 'VI';
            case 7: return 'VII';
            case 8: return 'VIII';
            case 9: return 'IX';
            case 10: return 'X';
            case 11: return 'XI';
            case 12: return 'XII';
            case 13: return 'XIII';
            case 14: return 'XIV';
            case 15: return 'XV';
            case 16: return 'XVI';
            case 17: return 'XVII';
            case 18: return 'XVIII';
            case 19: return 'XIX';
            case 20: return 'XX';
            case 21: return 'XXI';
            case 22: return 'XXII';
            case 23: return 'XXIII';
            case 24: return 'XXIV';
            case 25: return 'XXV';
            case 26: return 'XXVI';
            case 27: return 'XXVII';
            case 28: return 'XXVIII';
            case 29: return 'XXIX';
            case 30: return 'XXX';
            default: return $number;
        }
    }
    public static  function ConvertNumberToAlphaUpCase($number){
        switch($number) {
            case 1: return 'A';
            case 2: return 'B';
            case 3: return 'C';
            case 4: return 'D';
            case 5: return 'E';
            case 6: return 'F';
            case 7: return 'G';
            case 8: return 'H';
            case 9: return 'I';
            case 10: return 'J';
            case 11: return 'K';
            case 12: return 'L';
            case 13: return 'M';
            case 14: return 'N';
            case 15: return 'O';
            case 16: return 'P';
            case 17: return 'Q';
            case 17: return 'R';
            case 18: return 'S';
            case 19: return 'T';
            case 19: return 'U';
            case 20: return 'V';
            case 21: return 'W';
            case 22: return 'X';
            case 23: return 'Y';
            case 24: return 'Z';
            default: return $number;
        }
    }
    public static  function ConvertNumberToAlpha($number){
        switch($number) {
            case 1: return 'a';
            case 2: return 'b';
            case 3: return 'c';
            case 4: return 'd';
            case 5: return 'e';
            case 6: return 'f';
            case 7: return 'g';
            case 8: return 'h';
            case 9: return 'i';
            case 10: return 'j';
            case 11: return 'k';
            case 12: return 'l';
            case 13: return 'm';
            case 14: return 'n';
            case 15: return 'o';
            case 16: return 'p';
            case 17: return 'q';
            case 17: return 'r';
            case 18: return 's';
            case 19: return 't';
            case 19: return 'u';
            case 20: return 'v';
            case 21: return 'w';
            case 22: return 'x';
            case 23: return 'y';
            case 24: return 'z';
            default: return $number;
        }
    }
}
