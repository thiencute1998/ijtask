<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Department extends Model{
    use IjFullTextSearch;
    protected $table = 'department';
    protected $primaryKey = 'DepartmentID';

    protected $fillable = [
        'DepartmentNo',
        'DepartmentName',
        'ParentID',
        'Level',
        'Detail',
        'Note',
        'Locked',
        'NumberValue',
        'Prefix',
        'Suffix',
        'Inactive',

    ];

    protected static $searchable = ['DepartmentNo', 'DepartmentName'];

    public $timestamps = false;

    public static function search(Request $request){
        $data = null;
        $query = Department::query();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('DepartmentName')){
            $query->where('DepartmentName', 'like', '%'.$request->DepartmentName.'%');
        }
        if($request->post('DepartmentNo')){
            $query->whereRaw('concat(Prefix, DepartmentNo, Suffix) like("%'.$request->DepartmentNo.'%")');
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if ($request->has('exportData')) {
            $query->select('department.DepartmentID', 'department.DepartmentName','department.STT');
            return $query->orderBy('DepartmentID', 'asc')->paginate($request->post('per_page'));
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('DepartmentNo','DESC')->paginate($per_page);
    }

}

?>
