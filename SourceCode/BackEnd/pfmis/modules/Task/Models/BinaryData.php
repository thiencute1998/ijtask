<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class BinaryData extends Model{
    use IjFullTextSearch;
    protected $table = 'sys_binary_data';
    protected $primaryKey = 'BinaryDataID';

    protected static $searchable = ['BinaryDataNo', 'BinaryDataName'];

    public static function search(Request $request){
        $data = null;
        $query = BinaryData::query();
        $where = [];
        if($request->post('BinaryDataName')){
            $query->where('BinaryDataName', 'like', $request->BinaryDataName.'%');
        }
        if($request->post('BinaryDataNo')){
            $query->where('BinaryDataNo', 'like', $request->BinaryDataNo.'%');
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
        return $query->paginate($per_page);
    }

    protected $fillable = [
        'BinaryDataNo', 'BinaryDataName', 'BinaryDataKey', 'BinaryData1', 'BinaryData0'
    ];
    public $timestamps = false;


}

?>
