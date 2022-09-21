<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class TaskCheckList extends Model{
    protected $table = 'task_check_list';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'TaskID',
        'CheckListName',
        'Status',
        'CompletedDate',
        'NOrder',
    ];
    public $timestamps = false;

}

?>