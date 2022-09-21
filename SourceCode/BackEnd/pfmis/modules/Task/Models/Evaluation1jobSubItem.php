<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Database\Eloquent\Model;

class Evaluation1jobSubItem extends Model{
    use IjFullTextSearch;
    protected $table = 'task_evaluation_1job_trans_subitem';
    protected $primaryKey = 'SubItemID';

    protected $fillable = [
        'LineID',
        'TransID',
        'EvaluatorID',
        'EvaluatorName',
        'EvaluatorTitle',
        'LevelInt',
        'LevelResult',
        'LevelText',
        'LevelChar',
        'LevelInt100',
        'EstimatedQuantity'
    ];
    public $timestamps = false;


}

?>
