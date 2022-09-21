<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class IndicatorTableEmployeeMap extends Model{
    protected $table = 'task_indicator_table_employee_map';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
    }

    protected $fillable = [
        'TaskID',
        'MapID',
        'EmployeeID',
        'EmployeeNo',
        'LastName',
        'EmployeeName',
    ];
    public $timestamps = false;


}

?>
