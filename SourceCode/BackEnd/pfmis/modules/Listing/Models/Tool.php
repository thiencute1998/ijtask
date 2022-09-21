<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Module\SysAdmin\Models\SysSetup;

class Tool extends Model{
    use IjFullTextSearch;
    protected $table = 'tool';
    protected $primaryKey = 'ToolID';

    protected static $searchable = ['ToolNo', 'ToolName', 'Address'];


    public static function search(Request $request){
        $data = null;
        $query = Tool::query()->select('tool.*');
        $auth = Auth::user();
        if($request->post('ToolName')){
            $query->where('ToolName', 'like', '%' . $request->ToolName . '%');
        }
        if($request->post('ToolNo')){
            $query->whereRaw('ToolNo like("%' . $request->ToolNo . '%")');
        }

        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $investAssetLinks = $request->post('ToolLink');
        if ($investAssetLinks && (count($investAssetLinks) > 0)) {
            $query->join('tool_link', 'tool.ToolID', '=', 'tool_link.ToolID');
            foreach ($investAssetLinks as $investAssetLink) {
                if ($investAssetLink['LinkTable']) {
                    $query->where('tool_link.LinkTable', '=', $investAssetLink['LinkTable']);
                }
                if ($investAssetLink['LinkID']) {
                    $query->where('tool_link.LinkID', '=', $investAssetLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem tài liệu
        $query->leftJoin('tool_per', 'tool.ToolID', '=', 'tool_per.ToolID');
        $query->where(function ($q) use ($auth) {
            $q->where('tool_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('tool.AccessType', '=', 2);
            });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('tool.ToolID');

        if ($request->has('exportData')) {
            $query->select('tool.ToolNo', 'tool.ToolName', 'tool.OfficePhone', 'tool.Fax', 'tool.Email');
            return $query->orderBy('ToolID', 'desc')->get();
        }

        return $query->orderBy('ToolID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'ToolNo',
        'ToolName',
        'UomID',
        'UomName',
        'Locked',
        'Inactive',
        'AccessType',
        'UserIDCreated',
        'AuthorizedPerson'
    ];
    public $timestamps = false;


}

?>
