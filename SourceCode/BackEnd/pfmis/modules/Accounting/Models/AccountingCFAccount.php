<?php
namespace Module\Accounting\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class AccountingCFAccount extends Model{
    protected $table = 'act_cfaccount';
    protected $primaryKey = 'CFAccountID';

    public static function search(Request $request){
        $query = AccountingCFAccount::query();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('CFAccountType')){
            $query->where('CFAccountType', $request->CFAccountType);
        }
        if($request->post('CoaTypeName')){
            $query->where('CoaTypeName', 'like', '%'.$request->CoaTypeName.'%');
        }
        if($request->post('FromAccountNo')){
            $query->where('FromAccountNo', 'like', '%'.$request->FromAccountNo.'%');
        }
        if($request->post('ToAccountID')){
            $query->where('ToAccountID', 'like', '%'.$request->ToAccountID.'%');
        }
        if($request->post('Description')){
            $query->where('Description', 'like', '%'.$request->Description.'%');
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

        if ($request->has('exportData')) {
            $query->select('act_cfaccount.CFAccountID', 'act_cfaccount.CoaTypeName', 'act_cfaccount.Description');
            return $query->orderBy('CFAccountID')->get();
        }

        return $query->orderBy(DB::raw('CFAccountID'))->paginate($per_page);
    }
    protected $fillable = [
        'CoaTypeID', 'CoaTypeNo', 'CoaTypeName', 'Norder', 'FromAccountID', 'FromAccountNo', 'ToAccountID',
        'ToAccountNo', 'CFAccountType', 'Description', 'Locked', 'Inactive'
    ];
    public $timestamps = false;

}

?>
