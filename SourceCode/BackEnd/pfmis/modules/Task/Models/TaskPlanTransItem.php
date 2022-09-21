<?php
namespace Module\Task\Models;

use Illuminate\Database\Eloquent\Model;

class TaskPlanTransItem extends Model{
    protected $table = 'task_plan_trans_item';
    protected $primaryKey = 'TransItemID';

    protected $fillable = [
        'TaskID',
        'TransID',
        'EmployeeID',
        'EmployeeName',
        'EstimatedQuantity',
        'EstimatedQuantityRate',
        'TransDate'
    ];
    public $timestamps = false;


}

?>
