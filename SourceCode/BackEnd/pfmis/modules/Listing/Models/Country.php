<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class country extends  Model{
    use IjFullTextSearch;
    protected $table = 'country';
    protected $primaryKey = 'CountryID';
    //protected static $searchable= ['AreaID','CountryName'];

    public static function search(Request $request){
        $data = null;
        $query = Country::query();
        $where = [];
        if($request->post('CountryName')){
            $query->where('CountryName', 'like','%'. $request->CountryName.'%');
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
        //return $query->paginate($per_page);
        return $query->orderBy('CountryID')->paginate($per_page);
    }
    protected  $fillable = [
        'CountryID', 'CountryNo', 'CountryName', 'Inactive'
    ];
    public  $timestamps = false;
}
?>
