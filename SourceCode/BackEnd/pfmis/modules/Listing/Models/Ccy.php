<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class Ccy extends Model{
    use IjFullTextSearch;
    protected $table = 'ccy';
    protected $primaryKey = 'CcyID';

    protected static $searchable = ['CcyNo', 'CcyName'];

    public static function search(Request $request){
        $data = null;
        $query = Ccy::query();
        $where = [];
        if($request->post('CcyName')){
            $query->where('CcyName', 'like', '%'.$request->CcyName.'%');
        }
        if($request->post('CcyNo')){
            $query->whereRaw('concat(Prefix, CcyNo, Suffix) like("%'.$request->CcyNo.'%")');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        if ($request->has('exportData')) {
            $query->select('ccy.CcyID','ccy.CcyNo', 'ccy.CcyName', 'ccy.STT');
            return $query->orderBy('STT', 'asc')->paginate($request->post('per_page'));
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('CcyNo')->paginate($per_page);
    }

    protected $fillable = [
        'CcyNo', 'CcyName', 'Locked', 'LockedDate', 'LockedUserID', 'Inactive', 'Prefix', 'Suffix'
    ];
    public $timestamps = false;


}

?>
