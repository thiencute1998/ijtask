<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class IndicatorTableMap extends Model{
    protected $table = 'task_indicator_table_map';
    protected $primaryKey = 'MapID';

    public static function search(Request $request){
        $data = null;
        $query = self::query();

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('LineID')->paginate($per_page);
    }

    protected $fillable = [
        'MapID',
        'TaskID',
        'TaskNo',
        'IndicatorID',
        'IndicatorNo',
        'IndicatorCalMethod',
        'TableID',
        'TableNo',
        'TableItemID',
        'IndicatorName',
        'ScaleRateID',
        'ScaleRateName'
    ];
    public $timestamps = false;


}

?>
