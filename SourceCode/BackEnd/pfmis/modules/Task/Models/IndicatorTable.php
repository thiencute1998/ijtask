<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class IndicatorTable extends Model{
    use IjFullTextSearch;
    protected $table = 'task_indicator_table';
    protected $primaryKey = 'TableID';
    protected static $searchable = ['TableName'];

    public static function search(Request $request){
        $data = null;
        $query = IndicatorTable::query();
        $where = [];
        if($request->post('TableNo')){
            $query->where('TableNo', 'like', '%' . $request->TableNo . '%');
        }
        if($request->post('TableName')){
            $query->where('TableName', 'like', '%' . $request->TableName . '%');
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
        return $query->orderBy('TableID')->paginate($per_page);
    }

    protected $fillable = [
        'TableNo',
        'TableName',
        'IndicatorType',
        'CompanyID',
        'CompanyNo',
        'CompanyName',
        'EmployeeID',
        'EmployeeNo',
        'EmployeeName',
        'PeriodType',
        'FromDate',
        'ToDate',
        'AccessType',
        'Locked',
        'Inactive',
    ];
    public $timestamps = false;


}

?>
