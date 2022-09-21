<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Database\Eloquent\Model;

class Evaluation1jobItem extends Model{
    use IjFullTextSearch;
    protected $table = 'task_evaluation_1job_trans_item';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'TransID',
        'ScaleRateID',
        'ScaleRateName',
        'IndicatorID',
        'IndicatorNo',
        'IndicatorName',
        'FrequencyType',
        'IndicatorCalMethod',
        'LevelResult',
        'LevelInt100',
        'EstimatedQuantity'
    ];
    public $timestamps = false;


}

?>
