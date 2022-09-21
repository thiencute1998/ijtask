<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TaskWorkflowRelation extends Model{
    protected $table = 'task_workflow_relation';
    protected $primaryKey = 'WFRID';

    protected $fillable = [
        'TWID',
        'TWIID',
        'RelationID',
        'RelationType',
        'RelationTitle',
        'DirectionType',
        'NOrder',
        'Inactive',
    ];

    public $timestamps = false;

}

?>