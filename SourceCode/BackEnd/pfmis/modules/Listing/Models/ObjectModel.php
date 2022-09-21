<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class ObjectModel extends Model{
    use IjFullTextSearch;
    protected $table = 'object';
    protected $primaryKey = 'ObjectID';

    protected static $searchable = ['ObjectNo', 'ObjectName', 'Address', 'OfficePhone', 'Fax', 'Email', 'Website', 'Note'];


    public static function search(Request $request){
        $data = null;
        $query = ObjectModel::query();
        $where = [];
        if($request->post('ObjectName')){
            $query->where('ObjectName', 'like', '%'.$request->ObjectName.'%');
        }
        if($request->post('ObjectNo')){
            $query->whereRaw('concat(Prefix, ObjectNo, Suffix) like("%'.$request->ObjectNo.'%")');
        }
        if($request->post('Fax')){
            $query->where('Fax', 'like', '%'.$request->Fax.'%');
        }
        if($request->post('OfficePhone')){
            $query->where('OfficePhone', 'like', '%'.$request->OfficePhone.'%');
        }
        if($request->post('Email')){
            $query->where('Email', 'like', '%'.$request->Email.'%');
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
        return $query->orderBy(DB::raw('concat(Prefix, ObjectNo, Suffix)'))->paginate($per_page);
    }

    protected $fillable = [
        'ObjectNo', 'ObjectName', 'Address', 'Inactive', 'OfficePhone', 'Fax', 'Email', 'Website', 'Note', 'NOrder', 'Prefix', 'Suffix', 'NumberValue'
    ];

    public $timestamps = false;


}

?>
