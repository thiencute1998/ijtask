<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;


class Area extends Model{
    use IjFullTextSearch;
    protected $table = 'area';
    protected $primaryKey = 'AreaID';

    protected static $searchable = 'AreaID';

    public  static  function search (Request $request){
        $data = null;
        $query = Area::query();
        $where = [];
        if($request->post('AreaName')){
            $query->where('AreaName', 'like', '%'.$request->AreaName.'%');
        }
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if ($request->has('exportData')) {
            $query->select('area.AreaID', 'area.AreaName', 'area.STT');
            return $query->orderBy('AreaID', 'asc')->paginate($request->post('per_page'));
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('AreaID')->paginate($per_page);
    }
    protected $fillable = [
        'AreaID', 'AreaNo', 'AreaName','Inactive', 'CountryID', 'CountryName'
    ];
    public $timestamps = false;
}
?>
