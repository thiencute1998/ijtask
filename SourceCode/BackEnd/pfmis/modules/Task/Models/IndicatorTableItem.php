<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class IndicatorTableItem extends Model{
    protected $table = 'task_indicator_table_item';
    protected $primaryKey = 'TableItemID';

    public static function search(Request $request){
        $data = null;
        $query = self::query();

        if ($request->post('IndicatorName')) {
            $query->where('IndicatorName', 'like', '%' . $request->post('IndicatorName') . '%');
        }

        if ($request->post('TableID')) {
            $query->where('TableID', '=', $request->post('TableID'));
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('TableItemID')->paginate($per_page);
    }

    public static function getIndicator(Request $request, $mergeEmployee = false){
        $taskID = $request->post('TaskID');
        if (!$taskID) {
            $fieldWhere = $request->post('FieldWhere');
            $taskID = $fieldWhere['TaskID'];
        }
        $taskAssigns = TaskAssign::where('TaskID', '=', $taskID)
            ->where(function ($q) {
                $q->where('IsResponsiblePerson', '=', 1);
                $q->orWhere('IsMainResponsiblePerson', '=', 1);
                $q->orWhere('IsExecutor', '=', 1);
            })->get();

        $employeeIDs = [];
        foreach ($taskAssigns as $taskAssign) {
            $employeeIDs[] = $taskAssign->EmployeeID;
        }

        $indicatorQuery = Indicator::query();
        $indicatorQuery->select(DB::raw('task_indicator.IndicatorID, task_indicator.IndicatorCalMethod, task_indicator.IndicatorName, task_indicator.IndicatorNo, task_indicator.ScaleRateID, task_indicator.ScaleRateName,
        T.TableID, T.TableName, T.EmployeeID, T.EmployeeName, T.TableNo,
        TI.TableItemID'));
        $indicatorQuery->join('task_indicator_table_item AS TI', 'task_indicator.IndicatorID', '=', 'TI.IndicatorID');
        $indicatorQuery->join('task_indicator_table AS T', 'TI.TableID', '=', 'T.TableID');
        $indicatorQuery->whereIn('T.EmployeeID', $employeeIDs);
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }

        if ($request->post('search')) {
            $indicatorQuery->where('task_indicator.IndicatorName', 'like', '%' . $request->post('search') . '%');
        }

        if (!$mergeEmployee) {
            $indicatorQuery->groupBy('task_indicator.IndicatorID');
            $indicator = $indicatorQuery->paginate($per_page);

        }else{
            $indicatorQuery->groupBy('task_indicator.IndicatorID', 'T.EmployeeID');
            $indicator = $indicatorQuery->get();
        }

        return $indicator;
    }

    protected $fillable = [
        'TableID',
        'TableNo',
        'TableName',
        'Description',
        'ParentID',
        'Level',
        'Detail',
        'UomID',
        'UomName',
        'GradingType',
        'ObjectiveRate',
        'FrequencyType',
        'ObjectiveIndex',
        'IndicatorID',
        'IndicatorNo',
        'IndicatorName',
        'GradingMethod',
        'EvaluationMethod'
    ];
    public $timestamps = false;


}

?>
