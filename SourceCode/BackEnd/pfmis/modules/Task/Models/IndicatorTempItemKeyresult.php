<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class IndicatorTempItemKeyresult extends Model{
    use IjFullTextSearch;
    protected $table = 'task_indicator_temp_item_keyresult';
    protected $primaryKey = 'KeyresultID';
    protected static $searchable = ['KeyresultName'];

    public static function search(Request $request){
        $data = null;
        $query = IndicatorTempItemKeyresult::query();
        $where = [];

        if($request->post('KeyresultName')){
            $query->where('KeyresultName', 'like', $request->KeyresultName.'%');
        }
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('KeyresultID')->paginate($per_page);
        //return $query->orderBy(DB::raw('CONCAT(IndicatorNo)'))->paginate($per_page);
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
        'KeyresultName',
        'Description',
        'KeyresultType',
        'Rate',
        'Detail',
        'UomID',
        'UomName'
    ];
    public $timestamps = false;


}

?>
