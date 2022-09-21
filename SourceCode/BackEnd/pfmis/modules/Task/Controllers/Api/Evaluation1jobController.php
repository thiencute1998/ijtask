<?php
namespace Module\Task\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Task\Models\Evaluation1job;
use Module\Task\Models\ScaleRate;


class Evaluation1jobController extends Controller{

    public function list(Request $request){
        $data = Evaluation1job::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('task_evaluation_1job_trans', ['select' => ['EmployeeID', 'EmployeeName'], 'conditions' => []]), 'evaluation_1job.xlsx');
    }

    public function create(){
        $Evaluation1job = Evaluation1job::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['Evaluation1job'] = $Evaluation1job;
        return json_encode($json);
    }
    public function store(Request $request){
        $ScaleRate = ScaleRate::where('Inactive', '=', 0)->first();
        $validator = Validator::make($request->all(), [
            'EmployeeID' => 'required:task_evaluation_1job_trans'
        ],
            [
                'EmployeeID.required' => 'Mã người đánh giá không được để trống',
            ]
        );

        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $ScaleRateID = $ScaleRate->ScaleRateID;
        $Evaluation1job = new Evaluation1job([
            'EmployeeID' => $request->EmployeeID,
            'EmployeeName' => $request->EmployeeName,
            'EmployeeTitle' => $request->EmployeeTitle,
            'LevelInt' => $request->LevelInt,
            'LevelResult' => $request->LevelResult,
            'ScaleRateID' => $ScaleRateID,
            'Inactive' => $request->Inactive,
        ]);

        $Evaluation1job->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $Evaluation1job->LineID;

        return json_encode($json);
    }

    public function view($id){
        $data = Evaluation1job::find($id);
        if($data){
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;
            return json_encode($json);
        }else{

            $json['status'] = 2;
            $json['msg'] = 'Không tồi tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id){
        $data = Evaluation1job::find($id);
        $Evaluation1job = Evaluation1job::all();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['Evaluation1job'] = $Evaluation1job;
        $json['data']['data'] = $data;
        return json_encode($json);
    }

    public function update(Request $request, $id){
        DB::beginTransaction();
        $Evaluation1job = Evaluation1job::find($id);
        if(!$Evaluation1job){
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }

        $Evaluation1job->EmployeeID = $request->EmployeeID;
        $Evaluation1job->EmployeeName = $request->EmployeeName;
        $Evaluation1job->EmployeeTitle = $request->EmployeeTitle;
        $Evaluation1job->LevelInt = $request->LevelInt;
        $Evaluation1job->LevelResult = $request->LevelResult;
        $Evaluation1job->ScaleRateID = $request->ScaleRateID;
        $Evaluation1job->Inactive = $request->Inactive;

        if($Evaluation1job->save()){
            DB::commit();
        }else{
            DB::rollBack();
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;
    }

    public function delete($id){
        $data = Evaluation1job::find($id);
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
        Evaluation1job::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
}
