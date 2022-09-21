<?php
namespace Module\Accounting\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class AccountingCCAccount extends Model{
    protected $table = 'act_ccaccount';
    protected $primaryKey = 'CCAccountID';

    public static function search(Request $request){
        $query = AccountingCCAccount::query();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('CoaTypeName')){
            $query->where('CoaTypeName', 'like', '%'.$request->CoaTypeName.'%');
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
            $query->select('act_ccaccount.CCAccountID', 'act_ccaccount.CoaTypeName');
            return $query->orderBy('CCAccountID')->get();
        }

        return $query->orderBy(DB::raw('CCAccountID'))->paginate($per_page);
    }
    protected $fillable = [
        'CoaTypeID', 'CoaTypeNo', 'CoaTypeName', 'EntryDebitAccountID', 'EntryDebitAccountNo', 'EntryCreditAccountID', 'EntryCreditAccountNo',
        'EntryInTransTypeID', 'EntryInTransTypeName', 'ConditionalExpression', 'CCDebitAccountID', 'CCDebitAccountNo', 'CCCreditAccountID', 'CCCreditAccountNo',
        'CCInTransTypeID', 'CCInTransTypeName', 'EnterNegativeValue', 'Description', 'CCDebitAccountID2', 'CCDebitAccountNo2', 'CCCreditAccountID2',
        'CCCreditAccountNo2', 'CCInTransTypeID2', 'CCInTransTypeName2', 'EnterNegativeValue2', 'Description2', 'CCDebitAccountID3', 'CCDebitAccountNo3',
        'CCCreditAccountID3', 'CCCreditAccountNo3', 'CCInTransTypeID3', 'CCInTransTypeName3', 'EnterNegativeValue3', 'Description3', 'Norder', 'Inactive'
    ];
    public $timestamps = false;

}

?>
