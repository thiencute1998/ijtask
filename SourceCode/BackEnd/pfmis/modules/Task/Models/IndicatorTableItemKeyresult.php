<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class IndicatorTableItemKeyresult extends Model{
    protected $table = 'task_indicator_table_item_keyresult';
    protected $primaryKey = 'KeyresultID';

    public static function search(Request $request){
    }

    protected $fillable = [
        'TableID',
        'TableItemID',
        'KeyresultName',
        'KeyresultType',
        'Rate',
        'UomID',
        'UomName',
        'ScaleRateID',
        'BinaryDataID',
        'Description',
        'IndicatorID',
        'IndicatorNo',
        'IndicatorName',
        'GradingMethod',
        'EvaluationMethod'
    ];
    public $timestamps = false;


}

?>
