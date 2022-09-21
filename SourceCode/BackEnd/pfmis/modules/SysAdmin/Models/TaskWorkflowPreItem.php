<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class TaskWorkflowPreItem extends Model{
    protected $table = 'task_workflow_preitem';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'WFItemID',
        'WFPreItemID',
        'Type',
    ];

    public $timestamps = false;

}

?>