<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class IndicatorTableItemFeedback extends Model{
    protected $table = 'task_indicator_table_item_feedback';
    protected $primaryKey = 'FeedbackID';

    public static function search(Request $request){

    }

    protected $fillable = [
        'TableID',
        'TableItemID',
        'FeedbackName',
        'IsBinaryData',
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
