<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class TaskExpenseTrans extends Model{
    protected $table = 'task_expense_trans';
    protected $primaryKey = 'TransID';

    public static function search(Request $request, $TaskID){
        return 1;
    }
    protected $fillable = [
        'TaskID',
        'TaskNo',
        'TaskName',
        'TransType',
        'TransDate',
        'Comment',


    ];
    public $timestamps = false;

}

?>