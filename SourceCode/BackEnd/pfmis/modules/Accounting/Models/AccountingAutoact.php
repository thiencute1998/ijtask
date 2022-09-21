<?php
namespace Module\Accounting\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class AccountingAutoact extends Model{
    protected $table = 'act_autoact';
    protected $primaryKey = 'AutoactID';

    public static function search(Request $request){
        $query = AccountingAutoact::query();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('AutoactName')){
            $query->where('AutoactName', 'like', '%'.$request->AutoactName.'%');
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
            $query->select('act_autoact.AutoactID', 'act_autoact.AutoactName', 'act_autoact.Description');
            return $query->orderBy('AutoactID')->get();
        }

        return $query->orderBy('AutoactID','DESC')->paginate($per_page);
    }
    protected $fillable = [
        'AutoactName', 'DebitAccountID', 'DebitAccountNo', 'CreditAccountID', 'CreditAccountNo', 'Description',
        'CoaTypeID', 'CoaTypeNo', 'CoaTypeName', 'Norder','STT', 'Inactive', 'SysAutoactTypeID', 'SysAutoactTypeName','AutoactType'
    ];
    public $timestamps = false;

}

?>
