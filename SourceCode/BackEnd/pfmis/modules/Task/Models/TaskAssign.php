<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class TaskAssign extends Model{
    protected $table = 'task_assign';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $TaskID){
        return 1;
    }
    protected $fillable = [
        'TaskID',
        'TaskName',
        'EmployeeID',
        'EmployeeName',
        'StartDate',
        'FinishDate',
        'DueDate',
        'EstimatedHour',
        'EstimatedQuantity',
        'EstimatedQuantityRate',
        'Description',
        'IsChecker',
        'isMainResponsiblePerson',
        'IsResponsiblePerson',
        'IsAssignee',
        'IsCreator',
        'IsFollower',
        'IsExecutor',
    ];
    public $timestamps = false;

}

?>
