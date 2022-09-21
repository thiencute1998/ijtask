<?php
namespace Module\Task\Models;

use Illuminate\Database\Eloquent\Model;

class TaskDataflowSchedule extends Model{
    protected $table = 'task_dataflow_schedule';
    protected $primaryKey = 'ScheduleID';

    protected $fillable = [
        'DFID',
        'DFKey',
        'Datetime',
        'Content',
        'FeatureStatusID',
        'FeatureStatusValue',
        'FeatureStatusTitle'
    ];

    public $timestamps = false;

}

?>