<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Projectdemo extends Model{
    use IjFullTextSearch;
    protected $table = 'project';
    protected $primaryKey = 'ProjectID';

    protected static $searchable = ['ProjectNo', 'ProjectName'];

    public static function search(Request $request){
        $data = null;
        $query = Project::query();
        $where = [];
        if($request->post('ProjectName')){
            $query->where('ProjectName', 'like', $request->ProjectName.'%');
        }
        if($request->post('ProjectNo')){
            $query->where('ProjectNo', 'like', $request->ProjectNo.'%');
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
        return $query->orderBy(DB::raw('concat(Prefix, ProjectNo, Suffix)'))->paginate($per_page);
    }

    protected $fillable = [
        'ProjectNo', 'ProjectName', 'ParentID', 'Level', 'Detail', 'NOrder', 'Inactive', 'CompanyID', 'Prefix', 'Suffix', 'NumberValue'
    ];
    public $timestamps = false;


}

?>
