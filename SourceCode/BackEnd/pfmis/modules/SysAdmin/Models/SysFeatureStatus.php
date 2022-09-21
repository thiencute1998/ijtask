<?php
namespace Module\SysAdmin\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SysFeatureStatus extends Model{
    use IjFullTextSearch;
    protected $table = 'sys_status';
    protected $primaryKey = 'StatusID';

    protected static $searchable = ['StatusName'];
    public static function search(Request $request){
        $data = null;
        $query = SysFeatureStatus::query();
        $where = [];
        if($request->post('StatusName')){
            $query->where('StatusName', 'like', $request->StatusName.'%');
        }
        if($request->post('Inactive')){
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
        return $query->orderBy('StatusID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'StatusName',
        'Inactive',

    ];

    public $timestamps = false;

}

?>