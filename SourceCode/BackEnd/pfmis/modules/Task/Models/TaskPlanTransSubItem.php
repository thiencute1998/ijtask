<?php
namespace Module\Task\Models;

use Illuminate\Database\Eloquent\Model;

class TaskPlanTransSubItem extends Model{
    protected $table = 'task_plan_trans_subitem';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'TaskID',
        'TransID',
        'TransItemID',
        'EmployeeID',
        'EstimatedQuantity',
        'PeriodValue',
        'PeriodType',
        'FromDate',
        'ToDate',
        'TransDate'

    ];
    public $timestamps = false;


}

?>
