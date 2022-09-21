<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class EvaluationTransItemFeedback extends Model{
    protected $table = 'task_evaluation_trans_item_feedback';
    protected $primaryKey = 'FeedbackID';

    public static function search(Request $request){

    }

    protected $fillable = [
        'TransID',
        'TransItemID',
        'FeedbackName',
        'FeedbackDate',
        'FeedbackValue',
        'FeedbackContent',
        'IsBinaryData',
        'BinaryDataID',
        'BinaryDataName',
        'BinaryDataValue',
        'Description',
    ];
    public $timestamps = false;


}

?>
