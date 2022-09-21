<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class EvaluationTransPer extends Model{
    protected $table = 'task_evaluation_trans_per';
    protected $primaryKey = 'TransID';

    public static function search(Request $request){
    }

    protected $fillable = [
        'TransID',
        'EmployeeID',
        'EmployeeName',
        'EvaluatorGroup',
        'Access',
        'AccessField',
        'Edit',
        'EditField',
        'Delete',
    ];
    public $timestamps = false;


}

?>
