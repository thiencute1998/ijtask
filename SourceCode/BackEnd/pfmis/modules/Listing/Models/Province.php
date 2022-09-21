<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class province extends  Model{
    use IjFullTextSearch;
    protected $table = 'province';
    protected $primaryKey = 'ProvinceID';
    protected static $searchable= ['ProvinceID','ProvinceName'];

    public static function search(Request $request){
        $data = null;
        $query = Province::query();
        $where = [];
        if($request->post('ProvinceName')){
            $query->where('ProvinceName', 'like','%'. $request->ProvinceName.'%');
        }
        if($request->post('ProvinceID')){
            $query->where('ProvinceID', $request->ProvinceID);
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if ($request->has('exportData')) {
            $query->select('province.ProvinceID','province.ProvinceNo', 'province.ProvinceName','province.STT');
            return $query->orderBy('ProvinceID', 'asc')->paginate($request->post('per_page'));
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        //return $query->paginate($per_page);
        return $query->orderBy(DB::raw('CONCAT(ProvinceID, ProvinceID)'))->paginate($per_page);
    }
    protected  $fillable = [
        'ProvinceID', 'ProvinceNo', 'ProvinceName', 'ProvinceID',
        'ProvinceName', 'CountryID', 'CountryName', 'Inactive',
        'AreaID', 'AreaName'
    ];
    public  $timestamps = false;
}
?>
