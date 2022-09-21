<?php
namespace Module\Task\Models;

use Illuminate\Database\Eloquent\Model;

class TaskPerExpenseDetail extends Model{
    protected $table = 'task_per_expense_detail';
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
