<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class district extends Model{
    use IjFullTextSearch;
    protected $table = "district";
    protected  $primaryKey = "DistrictID";
    protected static $searchable =['DistrictName','ProvinceName' ];
    public  static function search (Request $request){
        $data = null;
        $query = District::query();
        $where = [];
        if($request->post('ProvinceID')){
            $query->where('ProvinceID', $request->ProvinceID);
        }
        if($request->post('DistrictName')){
            $query->where('DistrictName', 'like','%'.$request->DistrictName.'%');
        }
        if($request->post('ProvinceName')){
            $query->where('ProvinceName','like','%'.$request->ProvinceName.'%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if ($request->has('exportData')) {
            $query->select('district.DistrictID','district.ProvinceName', 'district.DistrictName','district.STT');
            return $query->orderBy('DistrictID', 'asc')->paginate($request->post('per_page'));
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('DistrictID')->paginate($per_page);
    }
    protected $fillable = [
        'DistrictID', 'DistrictNo', 'DistrictName', 'ProvinceID', 'ProvinceName', 'Inactive'
    ];
    public  $timestamps = false;
}
?>
