<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowItem extends Model{
    protected $table = 'task_workflow_item';
    protected $primaryKey = 'WFItemID';

    protected $fillable = [
        'WFItemName',
        'WFID',
        'FeatureID',
        'IsTaskFeature',
        'LineIDTemp',
        'ProcessType',
        'NOrder'
    ];

    public $timestamps = false;

}

?>