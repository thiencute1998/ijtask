<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class CoaPcfCateList extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_pcf_cate_list';
    protected $primaryKey = 'CateID';

    protected static $searchable = 'CateName';

    public static function search(Request $request){
        $data = null;
        $query = CoaPcfCateList::query();

        if($request->post('CateName')){
            $query->where('CateName', 'like', $request->CateName.'%');
        }
        if($request->post('CateNo')){
            $query->where('CateNo', 'like', $request->CateNo.'%');
        }
        if($request->post('Inactive')){
            $query->where('Inactive','=',$request->Inactive);
        }
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        } else {
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        if($request->post('viewType') === 'tree'){
            $query-> where('Level', '=', 1);
            $query-> groupBy('CateID')->orderBy('CateNo');
            return $query->paginate($per_page);
        }
        return $query->orderBy('CateNo')->paginate($per_page);
    }
    protected $fillable = [
        'CateID',
        'CateNo',
        'CateName',
        'ParentID',
        'Level',
        'Detail',
        'isSystemSetting',
        'Locked',
        'Inactive'
    ];
    public $timestamps = false;
}
?>
