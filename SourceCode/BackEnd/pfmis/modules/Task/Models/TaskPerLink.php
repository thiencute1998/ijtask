<?php
namespace Module\Task\Models;

use Illuminate\Database\Eloquent\Model;

class TaskPerLink extends Model{
    protected $table = 'task_per_link';
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
