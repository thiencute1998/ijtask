<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class IndicatorTablePer extends Model{
    protected $table = 'task_indicator_table_per';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
    }

    protected $fillable = [
        'TableID',
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
