<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class TaskLink extends Model{
    protected $table = 'task_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $TaskID){
        return 1;
    }
    protected $fillable = [
        'TaskID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
    ];
    public $timestamps = false;

}

?>
