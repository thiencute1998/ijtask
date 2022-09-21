<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class Bank extends Model{
    use IjFullTextSearch;
    protected $table = 'bank';
    protected $primaryKey = 'BankID';
    protected static $searchable = ['BankName'];

    public static function search(Request $request){
        $data = null;
        $query = Bank::query();
        $where = [];
        if($request->post('BankName')){
            $query->where('BankName', 'like','%'.$request->BankName.'%');
        }
        if($request->post('BankNo')){
            $query->where('BankNo', 'like','%'.$request->BankNo.'%');
        }
        if($request->post('BankAddress')){
            $query->where('BankAddress', 'like','%'.$request->BankAddress.'%');
        }
        if($request->post('Note')){
            $query->where('Note', 'like','%'.$request->Note.'%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if ($request->has('exportData')) {
            $query->select('bank.BankID', 'bank.BankName', 'bank.BankNo','bank.BankAddress','bank.STT');
            return $query->orderBy('BankID', 'asc')->paginate($request->post('per_page'));
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('BankID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'BankNo',
        'BankName',
        'BankType',
        'BankAddress',
        'Note',
        'Locked',
        'Inactive'
    ];
    public $timestamps = false;


}

?>
