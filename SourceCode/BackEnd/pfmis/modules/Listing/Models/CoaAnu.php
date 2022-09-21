<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUser;
use phpDocumentor\Reflection\Types\Self_;
use function Psy\debug;

Class CoaAnu extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_anu';
    protected $primaryKey = 'AccountID';

    protected static   $searchable = ['AccountNo','AccountName'];



    public  static function search(Request $request){
        $data = null;
        $query = CoaAnu::query();
        $query->select('coa_anu.*');
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('AccountName')){
            $query->where('coa_anu.AccountName', 'like', '%'.$request->AccountName.'%');
        }
        if($request->post('AccountNo')){
            $query->where('coa_anu.AccountNo', 'like', '%'.$request->AccountNo.'%');
        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Danh mục liên kết
        $CoaAnuLink = $request->post('CoaAnuLink');
        if($CoaAnuLink && count($CoaAnuLink) > 0){
            $query->leftJoin('coa_anu_link','coa_anu.AccountID','=','coa_anu_link.AccountID');
            foreach($CoaAnuLink as $coaAnuLink){
                if ($coaAnuLink['LinkTable']) {
                    $query->where('coa_anu_link.LinkTable', '=', $coaAnuLink['LinkTable']);
                }
                if ($coaAnuLink['LinkID']) {
                    $query->where('coa_anu_link.LinkID', '=', $coaAnuLink['LinkID']);
                }
            }
        }

        // Quyền Xem
        $query->leftJoin('coa_anu_per', 'coa_anu.AccountID', '=', 'coa_anu_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_anu_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_anu.AccessType', '=', 2);
                });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        //$query->groupBy('coa_anu.AccountID');

        if($request->post('exportData')){
            $query->select('coa_anu.AccountNo', 'coa_anu.AccountName');
            return $query->orderBy('coa_anu.AccountNo', 'ASC')->get();
        }
        //
        if($request->post('viewType') === 'tree'){
            $query-> where('coa_anu.Level', '=', 1);
            $query-> groupBy('coa_anu.AccountID')->orderBy('coa_anu.AccountNo');
            return $query->paginate($per_page);
        }

        return  $query->orderBy('coa_anu.AccountNo', 'ASC')->paginate($per_page);

    }




    protected $fillable = [
        'AccountNo',
        'AccountName',
        'ParentID',
        'ParentNo',
        'Level',
        'Detail',
        'CoaAnuType',
        'BalanceType',
        'Note',
        'UserIDCreated',
        'AuthorizedPerson',
        'AccessType',
        'Inactive',
        'Locked',
        'Detail'
    ];
    public  $timestamps = false;
}
?>
