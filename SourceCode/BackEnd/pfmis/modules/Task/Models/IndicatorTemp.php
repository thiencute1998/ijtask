<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class IndicatorTemp extends Model{
    use IjFullTextSearch;
    protected $table = 'task_indicator_temp';
    protected $primaryKey = 'TemplateID';
    protected static $searchable = ['TemplateName'];

    public static function search(Request $request){
        $data = null;
        $query = IndicatorTemp::query();
        $where = [];
        if($request->post('TemplateNo')){
            $query->where('TemplateNo', 'like', $request->TemplateNo.'%');
        }
        if($request->post('TemplateName')){
            $query->where('TemplateName', 'like', $request->TemplateName.'%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        $query->where('Locked', '=', 0);
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('TemplateID')->paginate($per_page);
    }

    protected $fillable = [
        'TemplateNo',
        'TemplateName',
        'IndicatorType',
        'EvaluationMethod',
        'Inactive'
    ];
    public $timestamps = false;


}

?>
