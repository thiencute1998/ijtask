<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUser;
use phpDocumentor\Reflection\Types\Self_;

class Indicator extends Model{
    use IjFullTextSearch;
    protected $table = 'task_indicator';
    protected $primaryKey = 'IndicatorID';

    protected static $searchable = ['IndicatorName'];

    public static function search(Request $request){
        $data = null;
        $query = Indicator::query();
        $where = [];
        if(isset($request->Inactive)){
            $query->where('task_indicator.Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('IndicatorName')){
            $query->where('task_indicator.IndicatorName', 'like', '%'.$request->IndicatorName.'%');
        }
        if($request->post('IndicatorNo')){
            $query->where('task_indicator.IndicatorNo', 'like', $request->IndicatorNo . '%');
        }

        $term = $request->post('term');
        if ($term) {
            $query->where('task_indicator.IndicatorName', 'like', '%' . $term . '%');
            $query->orWhere('task_indicator.IndicatorNo', 'like', $term . '%');
        }

        if ($request->post('IndicatorCate')) {
            $IndicatorCate = $request->post('IndicatorCate');
            if (count($IndicatorCate)) {
                $query->join('task_indicator_cate AS TC', 'task_indicator.IndicatorID', '=', 'TC.IndicatorID');
                $query->where(function ($q) use ($IndicatorCate) {
                    foreach ($IndicatorCate as $item) {
                        if (!empty($item['CateID'])) {
                            $q->where('TC.CateID', '=', $item['CateID']);
                        }
                        if (!empty($item['CateValue'])) {
                            $q->where('TC.CateValue', '=', $item['CateValue']);
                        }


//                        $q->where(function($q) use ($item) {
//                            if (isset($item['CateID']) && $item['CateID']) {
//                                $q->where('task.StatusID', '=', $item['StatusID']);
//                            }
//                            if (isset($item['StatusValue']) && $item['StatusValue']) {
//                                $q->where('task.StatusValue', '=', $item['StatusValue']);
//                            }
//                        });
                    }
                });
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
//            return $query->toSql();
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy(DB::raw('CONCAT(task_indicator.IndicatorNo)'))->paginate($per_page);
    }

    protected $fillable = [
        'IndicatorNo', 'IndicatorName', 'Description', 'ParentID', 'Level', 'Detail', 'UomID', 'UomName', 'FrequencyValue', 'ScaleRateID', 'ScaleRateName', 'GradingMethod', 'EvaluationMethod', 'IndicatorCalMethod', 'Locked', 'Inactive'
    ];
    public $timestamps = false;


}

?>
