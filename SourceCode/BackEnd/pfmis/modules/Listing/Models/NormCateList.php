<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class NormCateList extends Model{
    use IjFullTextSearch;
    protected $table = 'norm_cate_list';
    protected $primaryKey = 'CateID';
    protected static $searchable = ['CateName'];

    public static function search(Request $request){
        $data = null;
        $query = NormCateList::query();
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
        if($request->post('viewType') === 'tree'){
            $query-> where('Level', '=', 1);
            $query-> groupBy('CateID');
            return $query->paginate($per_page);
        }
        return $query->orderBy('CateID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
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
