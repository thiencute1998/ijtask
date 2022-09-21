<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class IndicatorTempItem extends Model{
    protected $table = 'task_indicator_temp_item';
    protected $primaryKey = 'TempitemID';

    public static function search(Request $request){
        $data = null;
        $where = [];
//        if(isset($request->Inactive)){
//            $query->where('Inactive', '=', $request->post('Inactive'));
//        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('TransItemID')->paginate($per_page);
    }

    protected $fillable = [
        'TransItemID',
        'TemplateID',
        'IndicatorID',
        'IndicatorNo',
        'IndicatorName',
        'Description',
        'Level',
        'Detail',
        'UomID',
        'UomName',
        'GradingType',
        'FrequencyType',
        'GradingMethod',
        'EvaluationMethod',
        'Rate',
        'LineIDTemp'

    ];
    public $timestamps = false;


}

?>
