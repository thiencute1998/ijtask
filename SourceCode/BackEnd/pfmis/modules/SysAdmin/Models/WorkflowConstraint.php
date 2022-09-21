<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowConstraint extends Model{
    protected $table = 'task_workflow_constraint';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'WFID',
        'WFItemID',
        'WFPreItemID',
        'WFPreItemValue',
        'ConstraintType',
        'ConstraintLabel',
        'ConstraintCondition',
        'SourceAnchors',
        'TargetAnchors'
    ];

    public $timestamps = false;

}

?>
