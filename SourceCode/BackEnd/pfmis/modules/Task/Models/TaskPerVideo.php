<?php
namespace Module\Task\Models;

use Illuminate\Database\Eloquent\Model;

class TaskPerVideo extends Model{
    protected $table = 'task_per_video';
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
