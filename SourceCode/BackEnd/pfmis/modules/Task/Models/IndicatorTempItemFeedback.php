<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class IndicatorTempItemFeedback extends Model{
    use IjFullTextSearch;
    protected $table = 'task_indicator_temp_item_feedback';
    protected $primaryKey = 'FeedbackID';

    public static function search(Request $request){

    }

    protected $fillable = [
        'TemplateID',
        'TransItemID',
        'IndicatorID',
        'IndicatorNo',
        'IndicatorName',
        'ScaleRateID',
        'GradingMethod',
        'EvaluationMethod',
        'BinaryDataID',
        'FeedbackName',
        'Description',
        'isBinaryData',
        'LineIDTemp',

    ];
    public $timestamps = false;


}

?>
