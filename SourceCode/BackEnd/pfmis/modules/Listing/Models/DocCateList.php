<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class DocCateList extends Model{
    use IjFullTextSearch;
    protected $table = 'doc_cate_list';
    protected $primaryKey = 'CateID';
    protected static $searchable = ['CateName'];

    public static function search(Request $request){
        $data = null;
        $query = DocCateList::query();
        $where = [];
        if($request->post('CateName')){
            $query->where('CateName', 'like', $request->CateName.'%');
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
        'ParentID',
        'Level',
        'Detail',
        'NOrder',
        'Inactive'
    ];
    public $timestamps = false;


}

?>