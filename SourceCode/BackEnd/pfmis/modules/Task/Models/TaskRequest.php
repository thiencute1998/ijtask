<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class TaskRequest extends Model{
    protected $table = 'task_request';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $TaskID){
        return 1;
    }
    protected $fillable = [
        'TaskID',
        'RequestDate',
        'RequestDueDate',
        'RequestName',
        'Description',
    ];
    public $timestamps = false;

}

?>
