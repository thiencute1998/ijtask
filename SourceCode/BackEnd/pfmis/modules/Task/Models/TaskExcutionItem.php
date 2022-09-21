<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class TaskExcutionItem extends Model{
    protected $table = 'task_excution_items';
    protected $primaryKey = 'TransID';

    public static function search(Request $request, $TaskID){
        return 1;
    }
    protected $fillable = [
        'TransID',
        'TaskID',
        'EmployeeExcute'


    ];
    public $timestamps = false;

}

?>