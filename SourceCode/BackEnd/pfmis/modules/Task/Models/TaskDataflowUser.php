<?php
namespace Module\Task\Models;

use Illuminate\Database\Eloquent\Model;

class TaskDataflowUser extends Model{
    protected $table = 'task_dataflow_user';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'ScheduleID',
        'DFID',
        'UserID',
        'DFKey',
        'IsResponUser',
    ];

    public $timestamps = false;

}

?>