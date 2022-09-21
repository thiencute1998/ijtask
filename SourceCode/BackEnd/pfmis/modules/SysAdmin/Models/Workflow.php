<?php
namespace Module\SysAdmin\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class  Workflow extends Model{
    use IjFullTextSearch;

    protected $table = 'task_workflow';
    protected $primaryKey = 'WFID';

    protected static $searchable = ['WFNo', 'WFName'];

    public static function search(Request $request){
        $data = null;
        $query = Workflow::query();
        $where = [];

        if($request->post('WFName')){
            $query->where('WFName', 'like', $request->WFName.'%');
        }
        if($request->post('WFNo')){
            $query->where('WFNo', 'like', $request->WFNo.'%');
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

        $direction = ($request->has('direction')) ? $request->post('direction') : 'desc';

        return $query->orderBy('WFID', $direction)->paginate($per_page);
    }
    protected $fillable = [
        'WFNo',
        'WFName',
        'Prefix',
        'Suffix',
        'JsonFlowchart',
        'Inactive'
    ];

    public $timestamps = false;

}

?>
