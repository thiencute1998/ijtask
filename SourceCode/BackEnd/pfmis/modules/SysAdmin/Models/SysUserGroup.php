<?php
namespace Module\SysAdmin\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class SysUserGroup extends Model{
    use IjFullTextSearch;
    protected $table = 'sys_user_group';
    protected $primaryKey = 'UserGroupID';

    protected static $searchable = ['UserGroupName', 'Note'];

    public static function search(Request $request){
        $data = null;
        $query = SysUserGroup::query();
        if($request->get('UserGroupName')){
            $query->where('UserGroupName', 'like', '%'.$request->UserGroupName.'%');
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
        return $query->paginate($per_page);
    }

    protected $fillable = [
        'UserGroupName', 'NOrder', 'Note', 'UserGroupType', 'Inactive'
    ];
    public $timestamps = false;


}

?>