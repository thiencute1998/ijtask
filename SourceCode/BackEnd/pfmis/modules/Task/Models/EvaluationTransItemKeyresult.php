<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class EvaluationTransItemKeyresult extends Model{
    protected $table = 'task_evaluation_trans_item_keyresult';
    protected $primaryKey = 'KeyresultID';

    public static function search(Request $request){
    }

    protected $fillable = [
        'TransID',
        'TransItemID',
        'KeyresultName',
        'KeyresultType',
        'Rate',
        'UomID',
        'UomName',
        'ScaleRateID',
        'BinaryDataID',
        'BinaryDataName',
        'BinaryDataKey',
        'BinaryDataValue',
        'ObjectiveIndex',
        'ActualIndex',
        'ActualRate',
        'Description',
    ];
    public $timestamps = false;


}

?>
