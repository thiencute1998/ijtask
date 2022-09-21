<?php
namespace Module\SysAdmin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Module\SysAdmin\Models\SysAutoNumber;

class SysAutoNumberController extends Controller{

    public function list(Request $request){
        $autoNumber = SysAutoNumber::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $autoNumber;
        return json_encode($json);
    }
    public function addnew1(){
        return view('SysAdmin::autonumber.addnew');
    }
    public function store1(Request $request){
        $validator = Validator::make($request->all(),
            [
                'NumberKey' => 'required|unique:sys_auto_number',
                'NumberName' => 'required|unique:sys_auto_number'
            ],
            [
                'NumberKey.required' => 'Khóa không được để trống',
                'NumberKey.unique' => 'Khóa đã tồn tại',
                'NumberName.required' => 'Tên không được để trống',
                'NumberName.unique' => 'Tên đã tồn tại',
            ]
        );
        $validator->after(function ($validator) use ($request) {
            $sign = '';
            $str = $request->NumberMask;
            if(empty($str)){
                $validator->errors()->add('NumberMask', 'Định dạng mã số không được để trống!');
            }
            $sign1 = strpos($str, '.');
            $sign2 = strpos($str, '-');
            $sign3 = strpos($str, '_');
            if(($sign1 !== false && $sign2 !== false) || ($sign2 !== false && $sign3 !== false) || ($sign1 !== false && $sign3 !== false)){
                $validator->errors()->add('NumberMask', 'Định dạng mã số không phù hợp!');
            }else{
                if($sign1 !== false){
                    $sign = '.';
                }elseif($sign2 !== false){
                    $sign = '-';
                }elseif($sign3 !== false){
                    $sign = '.';
                }
                $numberLevelArr = [];
                if($sign){
                    $arrStr = explode($sign, $str);
                    $validate = true;
                    $validator->numberLevel = sizeof($arrStr);
                    for($i = 0; $i < sizeof($arrStr); $i++){
                        $StrTemp = str_replace('N'.($i+1),"", $arrStr[$i]);
                        if($StrTemp){
                            $validator->errors()->add('NumberMask', 'Định dạng mã số không phù hợp!');
                            $validate = false;
                            break;
                        }
                        $numberLevelArr[] = strlen($arrStr[$i])/2;
                    }
                    $validator->numberLevelArr = $numberLevelArr;
                }else{
                    $StrTemp = str_replace('N',"", $str);
                    $k = 1;
                    $count = 0;
                    for($i = 0; $i < strlen($StrTemp); $i++){
                        if($StrTemp[$i] == $k){
                            $count++;
                        }else{
                            if((int)$StrTemp[$i] == $k + 1){
                                $numberLevelArr[] = $count;
                                $count = 1;
                                $k++;
                            }else{
                                $validator->errors()->add('NumberMask', 'Định dạng mã số không phù hợp!');
                                break;
                            }
                        }
                    }
                    $numberLevelArr[] = $count;
                    $validator->numberLevelArr = $numberLevelArr;
                }
            }
            $validator->sign = $sign;
        });
        $validator->validate();
        $arrAutoNumber = [
            'NumberKey' => $request->NumberKey,
            'NumberName' => $request->NumberName,
            'NumberValue' => $request->NumberValue,
            'Prefix' => $request->Prefix,
            'Suffix' => $request->Suffix,
            'NumberMask' => $request->NumberMask,
            'SignChar' => $validator->sign,
        ];
        $i = 1;
        $PadLeng = 0;
        foreach ($validator->numberLevelArr as $item){
            $arrAutoNumber['PadLeng'.$i] = $item;
            $PadLeng += $item;
            $i++;
        }
        if($validator->sign){
            $PadLeng += $i - 2;
        }
        $arrAutoNumber['PadLeng'] = $PadLeng;
        $arrAutoNumber['MaxLevel'] = $i - 1;
        $data = new SysAutoNumber($arrAutoNumber);
        $data->save();
        return redirect()->route('autonumber.index');
    }

    public function create(){
        $autoNumber = SysAutoNumber::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $autoNumber;
        return json_encode($json);
//        return view('SysAdmin::autonumber.addnew', ['autoNumber' => $autoNumber]);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),
            [
                'NumberKey' => 'required|unique:sys_auto_number',
                'NumberName' => 'required|unique:sys_auto_number'
            ],
            [
                'NumberKey.required' => 'Khóa không được để trống',
                'NumberKey.unique' => 'Khóa đã tồn tại',
                'NumberName.required' => 'Tên không được để trống',
                'NumberName.unique' => 'Tên đã tồn tại',
            ]
        );
        $validator->after(function ($validator) use ($request) {
            $sign = '';
            $str = $request->NumberMask;
            if(empty($str)){
                $validator->errors()->add('NumberMask', 'Định dạng mã số không được để trống!');
            }
            $sign1 = strpos($str, '.');
            $sign2 = strpos($str, '-');
            $sign3 = strpos($str, '_');
            if(($sign1 !== false && $sign2 !== false) || ($sign2 !== false && $sign3 !== false) || ($sign1 !== false && $sign3 !== false)){
                $validator->errors()->add('NumberMask', 'Định dạng mã số không phù hợp!');
            }else{
                if($sign1 !== false){
                    $sign = '.';
                }elseif($sign2 !== false){
                    $sign = '-';
                }elseif($sign3 !== false){
                    $sign = '.';
                }
                $numberLevelArr = [];
                if($sign){
                    $arrStr = explode($sign, $str);
                    $validate = true;
                    $validator->numberLevel = sizeof($arrStr);
                    for($i = 0; $i < sizeof($arrStr); $i++){
                        $StrTemp = str_replace('N'.($i+1),"", $arrStr[$i]);
                        if($StrTemp){
                            $validator->errors()->add('NumberMask', 'Định dạng mã số không phù hợp!');
                            $validate = false;
                            break;
                        }
                        $numberLevelArr[] = strlen($arrStr[$i])/2;
                    }
                    $validator->numberLevelArr = $numberLevelArr;
                }else{
                    $StrTemp = str_replace('N',"", $str);
                    $k = 1;
                    $count = 0;
                    for($i = 0; $i < strlen($StrTemp); $i++){
                        if($StrTemp[$i] == $k){
                            $count++;
                        }else{
                            if((int)$StrTemp[$i] == $k + 1){
                                $numberLevelArr[] = $count;
                                $count = 1;
                                $k++;
                            }else{
                                $validator->errors()->add('NumberMask', 'Định dạng mã số không phù hợp!');
                                break;
                            }
                        }
                    }
                    $numberLevelArr[] = $count;
                    $validator->numberLevelArr = $numberLevelArr;
                }
            }
            $validator->sign = $sign;
        });
        $validator->validate();
        $arrAutoNumber = [
            'NumberKey' => $request->NumberKey,
            'NumberName' => $request->NumberName,
            'NumberValue' => $request->NumberValue,
            'Prefix' => $request->Prefix,
            'Suffix' => $request->Suffix,
            'NumberMask' => $request->NumberMask,
            'SignChar' => $validator->sign,
        ];
        $i = 1;
        $PadLeng = 0;
        foreach ($validator->numberLevelArr as $item){
            $arrAutoNumber['PadLeng'.$i] = $item;
            $PadLeng += $item;
            $i++;
        }
        if($validator->sign){
            $PadLeng += $i - 2;
        }
        $arrAutoNumber['PadLeng'] = $PadLeng;
        $arrAutoNumber['MaxLevel'] = $i - 1;
        $data = new SysAutoNumber($arrAutoNumber);
        $data->save();
        return redirect()->route('autonumber.index');
    }
    public function edit(Request $request, $id){

        $data = SysAutoNumber::find($id);
        if($data){
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Mã không tồn tại!';
        }
        $json['data'] = $data;
        return json_encode($json);
    }
    public function saveAutoNumber(Request $request, $id){
        if(auth()->user()->UserType == 1) {
            $data = SysAutoNumber::find($id);
            $rule = [];
            $message = [];
            //        if($request->NumberKey != $data->NumberKey){
            //            $rule['NumberKey'] = 'required|unique:sys_auto_number';
            //            $message['NumberKey.required'] = 'Khóa không được để trống!';
            //            $message['NumberKey.unique'] = 'Khóa đã tồn tại!';
            //        }

            //        if($request->NumberName != $data->NumberName){
            //            $rule['NumberName'] = 'required|unique:sys_auto_number';
            //            $message['NumberName.required'] = 'Tên không được để trống!';
            //            $message['NumberName.unique'] = 'Tên đã tồn tại!';
            //        }
            $validator = Validator::make($request->all(),
                $rule,
                $message
            );
            $validator->after(function ($validator) use ($request) {
                $sign = '';
                $str = $request->NumberMask;
                if (empty($str)) {
                    $validator->errors()->add('NumberMask', 'Định dạng mã số không được để trống!');
                }
                $sign1 = strpos($str, '.');
                $sign2 = strpos($str, '-');
                $sign3 = strpos($str, '_');
                if (($sign1 !== false && $sign2 !== false) || ($sign2 !== false && $sign3 !== false) || ($sign1 !== false && $sign3 !== false)) {
                    $validator->errors()->add('NumberMask', 'Định dạng mã số không phù hợp!');
                } else {
                    if ($sign1 !== false) {
                        $sign = '.';
                    } elseif ($sign2 !== false) {
                        $sign = '-';
                    } elseif ($sign3 !== false) {
                        $sign = '.';
                    }
                    $numberLevelArr = [];
                    if ($sign) {
                        $arrStr = explode($sign, $str);
                        $validate = true;
                        $validator->numberLevel = sizeof($arrStr);
                        for ($i = 0; $i < sizeof($arrStr); $i++) {
                            $StrTemp = str_replace('N' . ($i + 1), "", $arrStr[$i]);
                            if ($StrTemp) {
                                $validator->errors()->add('NumberMask', 'Định dạng mã số không phù hợp!');
                                $validate = false;
                                break;
                            }
                            $numberLevelArr[] = strlen($arrStr[$i]) / 2;
                        }
                        $validator->numberLevelArr = $numberLevelArr;
                    } else {
                        $k = 1;
                        $count = 0;
                        $StrTemp = str_replace('N', "", $str);
                        if (strlen($str) / 2 != strlen($StrTemp)) {
                            $validator->errors()->add('NumberMask', 'Định dạng mã số không phù hợp!');
                        } else {
                            for ($i = 0; $i < strlen($StrTemp); $i++) {
                                if ($StrTemp[$i] == $k) {
                                    $count++;
                                } else {
                                    if ((int)$StrTemp[$i] == $k + 1) {
                                        $numberLevelArr[] = $count;
                                        $count = 1;
                                        $k++;
                                    } else {
                                        $json['status'] = 0;
                                        $json['msg'] = 'Định dạng mã số không phù hợp!';
                                        $json['data'] = [];
                                        return json_encode($json);
                                        break;
                                    }
                                }
                            }
                            $numberLevelArr[] = $count;
                        }
                        $validator->numberLevelArr = $numberLevelArr;
                    }
                }
                $validator->sign = $sign;
            });
            if ($validator->fails()) {

                $json['status'] = 0;
                $json['msg'] = 'Lỗi!';
                $json['data']['name'] = $request->NumberName;
                $json['data']['msg'] = $validator->errors();
                return json_encode($json);
            }
            $i = 1;
            $PadLeng = 0;
            foreach ($validator->numberLevelArr as $item) {
                $PadLengTemp = 'PadLeng' . $i;
                $data->$PadLengTemp = $item;
                $PadLeng += $item;
                $i++;
            }
            if ($validator->sign) {
                $PadLeng += $i - 2;
            }
            if ($data) {
                $data->PadLeng = $PadLeng;
                $data->MaxLevel = $i - 1;
                $data->Prefix = $request->Prefix;
                $data->Suffix = $request->Suffix;
                $data->NumberMask = $request->NumberMask;
                $data->NumberValue = $request->NumberValue;
                $data->SignChar = $validator->sign;
                for ($m = $i; $i <= 9; $i++) {
                    $PadLengTemp = 'PadLeng' . $i;
                    $data->$PadLengTemp = 0;
                }
                $data->save();
            }
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = $request->NumberName;
            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Bạn không có quyền thay đổi!';
            $json['data'] = $request->NumberName;
            return json_encode($json);
        }
    }
}
