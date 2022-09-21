<?php
namespace Module\Listing\Controllers;

namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Commune extends Model{
    use IjFullTextSearch;
    protected $table = "commune";
    protected $primaryKey = "CommuneID";
    protected static $searchable = ["CommuneName", "DistrictName", "ProvinceName"];
    public static  function search (Request $request){
        $data = null;
        $query =  Commune::query();
        if($request->post('ProvinceID')){
            $query->where('ProvinceID',$request->ProvinceID);
        }
        if($request->post('DistrictID')){
            $query->where('DistrictID',$request->DistrictID);
        }
        if($request->post('CommuneName')){
            $query->where('CommuneName','like','%'.$request->CommuneName.'%');
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
            $query->select('commune.CommuneID','commune.DistrictName', 'commune.CommuneName', 'commune.ProvinceName','commune.STT');
            return $query->orderBy('CommuneID', 'asc')->paginate($request->post('per_page'));
        }


        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('CommuneID')->paginate($per_page);
    }
    protected $fillable =[
        'CommuneID', 'CommuneNo', 'CommuneName', 'DistrictID', 'DistrictName', 'ProvinceID', 'ProvinceName'
    ];
    public $timestamps = false;
}
?>
