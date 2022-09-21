<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class EvaluationTransItem extends Model{
    protected $table = 'task_evaluation_trans_item';
    protected $primaryKey = 'TransItemID';

    public static function search(Request $request){
        $data = null;
        $where = [];
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('TransItemID')->paginate($per_page);
    }

    protected $fillable = [
        'TransID',
        'TransNo',
        'TransName',
        'Description',
        'ParentID',
        'Level',
        'Detail',
        'MannerDescription',
        'UomID',
        'UomName',
        'GradingMethod',
        'EvaluationMethod',
        'ObjectiveRate',
        'ObjectiveIndex',
        'AcualIndex',
        'ActualRate',
        'ScaleRateID',
        'YesNo',
        'FeedbackContent',
        'TestResult',
        'LineIDTemp'

    ];
    public $timestamps = false;


}

?>
