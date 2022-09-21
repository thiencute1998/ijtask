<?php
namespace Module\SysAdmin\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class SysReport extends Model{
    use IjFullTextSearch;
    protected $table = 'sys_report';
    protected $primaryKey = 'ReportID';

    protected static $searchable = ['ReportNo', 'ReportName'];


    public static function search(Request $request){
        $data = null;
        $query = SysReport::query()->select('sys_report.*');

        if($request->post('ReportName')){
            $query->where('sys_report.ReportName', 'like', '%' . $request->ReportName . '%');
            $query->where('sys_report.Detail', '=', 1);
        }
        if($request->post('ReportNo')){
            $query->whereRaw('sys_report.ReportNo like("%' . $request->ReportNo . '%")');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);

        }
        return $query->orderBy('ReportNo')->groupBy('ReportID')->get();
    }



    protected $fillable = [
        'ReportNo',
        'ReportName',
        'ReportLink',
        'ParentID',
        'Level',
        'Detail',
        'DefaulAccess',
        'DefaulPrint',
        'DefaulExport',
        'Detail',
        'NumberValue',
        'Locked',
        'Inactive'
    ];
    public $timestamps = false;


}

?>
