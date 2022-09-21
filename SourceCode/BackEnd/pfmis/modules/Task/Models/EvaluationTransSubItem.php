<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class EvaluationTransSubItem extends Model{
    protected $table = 'task_evaluation_trans_subitem';
    protected $primaryKey = 'SubItemID';

    public static function search(Request $request){
    }

    protected $fillable = [
        'TransItemID',
        'TransID',
        'ActualIndex',
        'ActualRate',
        'LineIDTemp',
        'EvaluatorID',
        'EvaluatorName',
    ];
    public $timestamps = false;


}

?>
