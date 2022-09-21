<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Uom extends Model{
    use IjFullTextSearch;
    protected $table = 'uom';
    protected $primaryKey = 'UomID';

    protected static $searchable = ['UomNo', 'UomName'];

    public static function search(Request $request){
        $data = null;
        $query = Uom::query();
        $where = [];
        if($request->post('UomName')){
            $query->where('UomName', 'like', $request->UomName.'%');
        }
        if($request->post('UomNo')){
            $query->where('UomNo', 'like', $request->UomNo.'%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if ($request->has('exportData')) {
            $query->select('uom.UomID','uom.UomNo', 'uom.UomName','uom.STT' );
            return $query->orderBy('UomID', 'asc')->paginate($request->post('per_page'));
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        //return $query->paginate($per_page);
        return $query->orderBy(DB::raw('CONCAT(UomNo, UomID)'))->paginate($per_page);
    }

    protected $fillable = [
        'UomNo', 'UomName', 'UomShortName','UomType', 'UomTime', 'UomCurrency', 'UomPoint', 'Note', 'NOrder', 'Inactive',
        'NumberValue', 'Prefix', 'Suffix'
    ];
    public $timestamps = false;


}

?>
