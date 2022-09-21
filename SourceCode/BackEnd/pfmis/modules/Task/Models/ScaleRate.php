<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class ScaleRate extends Model{
    use IjFullTextSearch;
    protected $table = 'task_scale_rate';
    protected $primaryKey = 'ScaleRateID';

    protected static $searchable = ['ScaleRateName'];

    public static function search(Request $request){
        $data = null;
        $query = ScaleRate::query();
        $where = [];
        if($request->post('ScaleRateName')){
            $query->where('ScaleRateName', 'like', '%'.$request->ScaleRateName.'%');
        }
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        $query->where('Locked', '=', 0);
        $query->orderBy('MaxLevel', 'ASC');
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->paginate($per_page);
    }

    protected $fillable = [
        'ScaleRateName', 'MaxLevel', 'UsingEvaluationTask', 'isDefault', 'Comment', 'Inactive', 'Locked'
    ];
    public $timestamps = false;


}

?>
