<?php
namespace Module\Task\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class EvaluationTrans extends Model{
    use IjFullTextSearch;
    protected $table = 'task_evaluation_trans';
    protected $primaryKey = 'TransID';
    protected static $searchable = ['TransName'];

    public static function search(Request $request){
        $data = null;
        $query = EvaluationTrans::query();
        $where = [];
        if($request->post('TransNo')){
            $query->where('TransNo', 'like', $request->TransNo.'%');
        }
        if($request->post('TransName')){
            $query->where('TransName', 'like', $request->TransName.'%');
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
        return $query->orderBy('TransID')->paginate($per_page);
    }

    protected $fillable = [
        'TransNo',
        'TransName',
        'TransType',
        'TransDate',
        'CompanyID',
        'CompanyNo',
        'CompanyName',
        'EmployeeID',
        'EmployeeNo',
        'EmployeeName',
        'EvaluatorID',
        'EvaluatorNo',
        'EvaluatorName',
        'EvaluationMethod',
        'TotalActualRate',
        'EvaluationLevel',
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
