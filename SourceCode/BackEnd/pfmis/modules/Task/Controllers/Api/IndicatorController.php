<?php
namespace Module\Task\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Uom;
use Module\Task\Models\BinaryData;
use Module\Task\Models\Indicator;
use Module\Listing\Models\SysAutoNumber;
use Module\Task\Models\ScaleRate;

class IndicatorController extends Controller{

    public function list(Request $request){
        $data = Indicator::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('indicator', ['select' => ['IndicatorNo', 'IndicatorName'], 'conditions' => []]), 'Indicator.xlsx');
    }

    public function create(){
        $Indicator = Indicator::all();
        $Uom = Uom::all();
        if(old('IndicatorNo')){
            $auto = old('IndicatorNo');
        }else{
            $auto = AutoNo::genNewNumberNoTable('task_indicator', 'IndicatorID', 'IndicatorNo');
        }
        $ScaleRate = ScaleRate::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['indicator'] = $Indicator;
        $json['data']['Uom'] = $Uom;
        $json['data']['auto'] = $auto;
        $json['data']['ScaleRate'] = $ScaleRate;
        $json['data']['BinaryData'] = BinaryData::all();
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'IndicatorNo' => 'required|unique:task_indicator'
        ],
            [
                'IndicatorNo.required' => 'Mã chỉ tiêu không được để trống',
                'IndicatorNo.unique' => 'Mã chỉ tiêu đã tồn tại',
            ]
        );
        $autoModel = SysAutoNumber::where('NumberKey', 'task_indicator')->first();
        AutoNo::storeValidateIDNo($validator, $request, 'task_indicator','IndicatorID', 'IndicatorNo', $autoModel, 'chỉ tiêu');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $ScaleRateName="";
        if($request->ScaleRateID){
            $ScaleRate = ScaleRate::find($request->ScaleRateID);
            if(isset($ScaleRate->ScaleRateName)){
                $ScaleRateName = $ScaleRate->ScaleRateName;
            }
        }

        $Indicator = new Indicator([
            'IndicatorNo' => $request->IndicatorNo,
            'IndicatorName' => $request->IndicatorName,
            'Description' => $request->Description,
            'ParentID' => $request->ParentID,
            'Level' => empty($request->ParentID)? 1 : $validator->ParentLevel + 1,
            'Detail' => 1,
            'UomID' => $request->UomID,
            'UomName' => $request->UomName,
            'FrequencyValue' => $request->FrequencyValue,
            'ScaleRateID' => $request->ScaleRateID,
            'ScaleRateName' => $ScaleRateName,
            'GradingMethod' => $request->GradingMethod,
            'EvaluationMethod' => $request->EvaluationMethod,
            'IndicatorCalMethod' => $request->IndicatorCalMethod,
            'Locked' => isset($request->Locked) ? $request->Locked : 0,
            'Inactive' => $request->Inactive,
        ]);

        $Indicator->save();

        $IndicatorCate = $request->post('IndicatorCate');
        if (count($IndicatorCate)) {
            $insertArr = [];
            foreach ($IndicatorCate as $item) {
                if ($item['CateID']) {
                    $insertArr[] = [
                        'CateID' => $item['CateID'],
                        'CateName' => $item['CateName'],
                        'CateValue' => $item['CateValue'],
                        'ValueID' => $item['ValueID'],
                        'ConvertedValue' => $item['ConvertedValue'],
                        'IndicatorID' => $Indicator->IndicatorID
                    ];
                }
            }

            if (count($insertArr)) {
                DB::table('task_indicator_cate')->where('IndicatorID', '=', $Indicator->IndicatorID)->delete();
                DB::table('task_indicator_cate')->insert($insertArr);
            }
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $Indicator->IndicatorID;

        return json_encode($json);
    }

    public function view($id){
        $data = Indicator::find($id);
        if($data){
            $data->IndicatorNo = $data->Prefix.$data->IndicatorNo.$data->Suffix;
            $ParentName = '';
            if($data->ParentID){
                $parent = Indicator::find($data->ParentID);
                if(isset($parent->IndicatorName)){
                    $ParentName = $parent->IndicatorName;
                }
            }
            $UomName = '';
            if($data->UomID){
                $parent = Uom::find($data->UomID);
                if(isset($parent->UomName)){
                    $UomName = $parent->UomName;
                }
            }
            $ScaleRateName = '';
            if($data->ScaleRateID){
                $ScaleRate = ScaleRate::find($data->ScaleRateID);
                if(isset($ScaleRate->ScaleRateName)){
                    $ScaleRateName = $ScaleRate->ScaleRateName;
                }
            }

            $IndicatorCate = DB::table('task_indicator_cate')->where('IndicatorID', '=', $data->IndicatorID)->get();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;
            $json['data']['ParentName'] = $ParentName;
            $json['data']['UomName'] = $UomName;
            $json['data']['ScaleRateName'] = $ScaleRateName;
            $json['data']['IndicatorCate'] = $IndicatorCate;
            return json_encode($json);
        }else{

            $json['status'] = 2;
            $json['msg'] = 'Không tồi tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id){
        $data = Indicator::find($id);
        $Indicator = Indicator::all();
        $Uom= Uom::all();
        if(old('IndicatorNo')){
            $data->IndicatorNo = old('IndicatorNo');
        }else{
            $data->IndicatorNo = $data->Prefix.$data->IndicatorNo.$data->Suffix;
        }
        if($data->ScaleRateID){
            $ScaleRate = ScaleRate::find($data->ScaleRateID);
            if(isset($ScaleRate->ScaleRateName)){
                $ScaleRateName = $ScaleRate->ScaleRateName;
            }
        }
        $IndicatorCate = DB::table('task_indicator_cate')->where('IndicatorID', '=', $data->IndicatorID)->get();

        $ScaleRate = ScaleRate::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['indicator'] = $Indicator;
        $json['data']['data'] = $data;
        $json['data']['Uom'] = $Uom;
        $json['data']['ScaleRate'] = $ScaleRate;
        $json['data']['BinaryData'] = BinaryData::all();
        $json['data']['IndicatorCate'] = $IndicatorCate;
        return json_encode($json);
    }

    public function update(Request $request, $id){
        DB::beginTransaction();
        $Indicator = Indicator::find($id);
        if(!$Indicator){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        $LevelOld = $Indicator->Level;
        if($Indicator->Prefix.$Indicator->IndicatorNo.$Indicator->Suffix != $request->IndicatorNo){
            $validator = Validator::make($request->all(), [
                'IndicatorNo' => 'required|unique:task_indicator'
            ],
                [
                    'IndicatorNo.required' => 'Mã chỉ tiêu không được để trống',
                    'IndicatorNo.unique' => 'Mã chỉ tiêu đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'task_indicator')->first();
            AutoNo::UpdateValidateIDNO($validator, $request, $Indicator,'task_indicator', $autoModel, 'Chỉ tiêu đánh giá công việc', 'IndicatorNo', 'IndicatorID');

            if(!empty($validator->failed())){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }

            $Indicator->IndicatorNo = $request->IndicatorNo;
            $Indicator->Level = empty($request->ParentID)? 1 : $validator->ParentLevel + 1;
        }
        $IndicatorParentLevel = 0;
        if($request->ParentID){
            $IndicatorParent = Indicator::find($request->ParentID);
            if($IndicatorParent){
                $IndicatorParentLevel = $IndicatorParent->Level;
                $IndicatorParent->Detail = 0;
                $IndicatorParent->save();
                $Indicator->Level = $IndicatorParentLevel + 1;
            }
        }
        $ScaleRateName='';
        if($request->ScaleRateID){
            $ScaleRate = ScaleRate::find($request->ScaleRateID);
            if(isset($ScaleRate->ScaleRateName)){
                $ScaleRateName = $ScaleRate->ScaleRateName;
            }
        }
        $OldParentID = $Indicator->ParentID;
        $Indicator->IndicatorName = $request->IndicatorName;
        $Indicator->ParentID = $request->ParentID;
        $Indicator->Description = $request->Description;
        $Indicator->UomID = $request->UomID;
        $Indicator->UomName = $request->UomName;
        $Indicator->FrequencyValue = $request->FrequencyValue;
        $Indicator->ScaleRateID = $request->ScaleRateID;
        $Indicator->ScaleRateName = $ScaleRateName;
        $Indicator->GradingMethod = $request->GradingMethod;
        $Indicator->EvaluationMethod = $request->EvaluationMethod;
        $Indicator->IndicatorCalMethod = $request->IndicatorCalMethod;
        $Indicator->Locked = $request->Locked;
        $Indicator->Inactive = $request->Inactive;


        if($Indicator->save()){
            DB::commit();

            $IndicatorCate = $request->post('IndicatorCate');
            DB::table('task_indicator_cate')->where('IndicatorID', '=', $Indicator->IndicatorID)->delete();
            if (count($IndicatorCate)) {
                $insertArr = [];
                foreach ($IndicatorCate as $item) {
                    if ($item['CateID']) {
                        $insertArr[] = [
                            'CateID' => $item['CateID'],
                            'CateName' => $item['CateName'],
                            'CateValue' => $item['CateValue'],
                            'ValueID' => $item['ValueID'],
                            'ConvertedValue' => $item['ConvertedValue'],
                            'IndicatorID' => $Indicator->IndicatorID
                        ];
                    }
                }

                if (count($insertArr)) {
                    DB::table('task_indicator_cate')->insert($insertArr);
                }
            }

        }else{
            DB::rollBack();
        }

        //Cập nhật cha cũ
        if($OldParentID){
            $ChildParentOld = Indicator::where('ParentID', '=', $OldParentID)->get();
            if($ChildParentOld){
                $updateParentOld = Indicator::where(['IndicatorID' => $OldParentID])->update(['Detail' => 0]);
            }else{
                $updateParentOld = Indicator::where(['IndicatorID' => $OldParentID])->update(['Detail' => 1]);
            }
        }
        //Cập nhật Level con
        $ChangeLevel = $Indicator->Level - $LevelOld;

        if($ChangeLevel != 0){
                $ArrParentID = [$Indicator->IndicatorID];
                $ArrParentID = $this->getArrayParentID($ArrParentID, 0);
//                $ArrParentID = $ArrParentID;
                if($ArrParentID){
                    DB::table('task_indicator')->whereIn('ParentID', $ArrParentID)->update(['Level' => DB::raw('Level + '.$ChangeLevel)]);
                }
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }

    public function getArrayParentID($ArrParentID, $Index){
        if(!isset($ArrParentID[$Index])){
            return $ArrParentID;
        }

        $Indicator = Indicator::where('ParentID', $ArrParentID[$Index])->get();
        if($Indicator){
            foreach ($Indicator as $item){
                if($item->Detail != 1){
                    $ArrParentID[] = $item->IndicatorID;
                }
            }
        }
        return $this->getArrayParentID($ArrParentID, $Index + 1);
    }

    public function delete($id){
        $data = Indicator::find($id);
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
        Indicator::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
