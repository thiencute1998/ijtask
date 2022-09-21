<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUser;
use phpDocumentor\Reflection\Types\Self_;

class Evaluation1job extends Model{
    use IjFullTextSearch;
    protected $table = 'task_evaluation_1job_trans';
    protected $primaryKey = 'TransID';

    protected static $searchable = ['EmployeeName'];

    public static function search(Request $request){
        $data = null;
        $query = Evaluation1job::query();
        $where = [];
//        if(isset($request->Inactive)){
//            $query->where('Inactive', '=', $request->post('Inactive'));
//        }
//        if($request->post('EmployeeID')){
//            $query->where('EmployeeID', 'like', '%'.$request->EmployeeID.'%');
//        }
        if($request->post('EmployeeName')){
            $query->where('EmployeeName', 'like', '%'.$request->EmployeeName.'%');
        }
        if($request->post('EmployeeTitle')){
            $query->where('EmployeeTitle', 'like', '%'.$request->EmployeeTitle.'%');
        }

        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('EmployeeID')){
            $query->where('EmployeeID', 'like', '%'.$request->EmployeeID.'%');
        }
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
//            return $query->toSql();
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy(DB::raw('CONCAT(LineID)'))->paginate($per_page);
    }

    protected $fillable = [
        'TransDate',
        'TaskID',
        'IndicatorID',
        'IndicatorNo',
        'IndicatorName',
        'EmployeeID',
        'EmployeeName',
        'LevelInt',
        'LevelResult',
        'LevelInt100',
        'ScaleRateID',
        'ScaleRateName',
        'Inactive',
        'FrequencyType'



    ];
    public $timestamps = false;


}

?>
