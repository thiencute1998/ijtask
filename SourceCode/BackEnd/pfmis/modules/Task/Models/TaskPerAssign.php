<?php
namespace Module\Task\Models;

use Illuminate\Database\Eloquent\Model;

class TaskPerAssign extends Model{
    protected $table = 'task_per_assign';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'TaskID',
        'EmployeeID',
        'EmployeeName',
        'Access',
        'AccessField',
        'Edit',
        'EditField',
        'Delete',
        'TypePer'
    ];
    public $timestamps = false;

}

?>
