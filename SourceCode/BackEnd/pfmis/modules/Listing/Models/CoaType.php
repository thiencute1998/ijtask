<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class CoaType extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_type';
    protected $primaryKey = 'CoaTypeID';

    protected static $searchable = ['CoaTypeNo', 'CoaTypeName'];

    public static function search(Request $request){
        $data = null;
        $query = CoaType::query();
        $where = [];
        if($request->post('CoaTypeName')){
            $query->where('CoaTypeName', 'like', '%'.$request->CoaTypeName.'%');
        }
        if($request->post('CoaTypeNo')){
            $query->whereRaw('concat(Prefix, CoaTypeNo, Suffix) like("%'.$request->CoaTypeNo.'%")');
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
        return $query->orderBy('CoaTypeNo')->paginate($per_page);
    }

    protected $fillable = [
        'CoaTypeNo',
        'CoaTypeName',
        'Comment',
        'Norder',
        'Inactive'
    ];
    public $timestamps = false;


}

?>
