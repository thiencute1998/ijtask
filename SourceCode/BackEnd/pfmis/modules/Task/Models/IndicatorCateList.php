<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class IndicatorCateList extends Model{
    use IjFullTextSearch;
    protected $table = 'task_indicator_cate_list';
    protected $primaryKey = 'CateID';
    protected static $searchable = ['CateName'];

    public static function search(Request $request){
        $data = null;
        $query = IndicatorCateList::query();
        $where = [];
        if($request->post('CateName')){
            $query->where('CateName', 'like', $request->CateName.'%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
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
        return $query->orderBy('nOrder')->paginate($per_page);
    }

    protected $fillable = [
        'CateName',
        'NOrder',
        'Inactive'
    ];
    public $timestamps = false;


}

?>
