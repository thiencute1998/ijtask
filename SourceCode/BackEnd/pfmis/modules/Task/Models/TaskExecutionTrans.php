<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class TaskExecutionTrans extends Model{
    protected $table = 'task_execution_trans';
    protected $primaryKey = 'TransID';

    public static function search(Request $request, $TaskID){
        return 1;
    }
    protected $fillable = [
        'TaskID',
        'ActualHour',
        'ActualQuantity',
        'CalMethod',
        'PercentCompleted',
        'Description',
        'TransDate',
        'EmployeeID',
        'EmployeeName',
        'StatusID',
        'StatusName',
        'StatusValue',
        'StatusDescription',


    ];
    public $timestamps = false;

}

?>