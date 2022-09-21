<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Program extends Model{
    use IjFullTextSearch;
    protected $table = 'program';
    protected $primaryKey = 'ProgramID';

    protected static $searchable = ['ProgramNo', 'ProgramName'];


    public static function search(Request $request){
        $data = null;
        $query = Program::query()->select('program.*');
        $auth = Auth::user();
        if($request->post('ProgramName')){
            $query->where('ProgramName', 'like', '%' . $request->ProgramName . '%');
        }
        if($request->post('ProgramNo')){
            $query->whereRaw('ProgramNo like("%' . $request->ProgramNo . '%")');
        }
        if($request->post('ProgramType')){
            $query->where('ProgramType', $request->ProgramType );
        }
        if($request->post('ManagementLevel')){
            $query->where('ManagementLevel', $request->ManagementLevel );
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $programLinks = $request->post('ProgramLink');
        if ($programLinks && (count($programLinks) > 0)) {
            $query->join('program_link', 'program.ProgramID', '=', 'program_link.ProgramID');
            foreach ($programLinks as $programLink) {
                if ($programLink['LinkTable']) {
                    $query->where('program_link.LinkTable', '=', $programLink['LinkTable']);
                }
                if ($programLink['LinkID']) {
                    $query->where('program_link.LinkID', '=', $programLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem tài liệu
        $query->leftJoin('program_per', 'program.ProgramID', '=', 'program_per.ProgramID');
        $query->where(function ($q) use ($auth) {
            $q->where('program_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('program.AccessType', '=', 2);
            });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('program.ProgramID');

        if ($request->has('exportData')) {
            $query->select('program.ProgramID','program.ProgramNo','program.STT', 'program.ProgramName','program.ProgramType','program.ManagementLevel');
            return $query->orderBy('ProgramID', 'asc')->paginate($request->post('per_page'));
        }

        return $query->orderBy('ProgramID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'ProgramNo',
        'ProgramName',
        'ProgramType',
        'ManagementLevel',
        'Note',
        'AccessType',
        'UserIDCreated',
        'AuthorizedPerson',
        'Locked',
        'Inactive'
    ];
    public $timestamps = false;


}

?>
