<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class TaskWorkflowItemRelation extends Model{
    protected $table = 'task_workflow_item_relation';
    protected $primaryKey = 'WFRelationID';

    protected $fillable = [
        'WFItemID',
        'WFID',
        'PreviousItem',
    ];

    public $timestamps = false;

}

?>