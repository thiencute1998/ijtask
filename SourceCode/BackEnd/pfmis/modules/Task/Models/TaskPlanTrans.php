<?php
namespace Module\Task\Models;

use Illuminate\Database\Eloquent\Model;

class TaskPlanTrans extends Model{
    protected $table = 'task_plan_trans';
    protected $primaryKey = 'TransID';


    protected $fillable = [
        'TaskID',
        'TransDate',
        'StartDate',
        'DueDate',
        'EstimatedQuantity',
        'TransComment',
    ];
    public $timestamps = false;


}

?>
