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

Class CoaTab extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_tab';
    protected $primaryKey = 'AccountID';

    protected static   $searchable = ['AccountNo','AccountName'];



    public  static function search(Request $request){
        $data = null;
        $query = CoaTab::query();
        $query->select('coa_tab.*');
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('AccountName')){
            $query->where('coa_tab.AccountName', 'like', '%'.$request->AccountName.'%');
        }
        if($request->post('AccountNo')){
            $query->where('coa_tab.AccountNo', 'like', '%'.$request->AccountNo.'%');
        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Danh mục liên kết
        $CoaTabLink = $request->post('CoaTabLink');
        if($CoaTabLink && count($CoaTabLink) > 0){
            $query->leftJoin('coa_tab_link','coa_tab.AccountID','=','coa_tab_link.AccountID');
            foreach($CoaTabLink as $coaTabLink){
                if ($coaTabLink['LinkTable']) {
                    $query->where('coa_tab_link.LinkTable', '=', $coaTabLink['LinkTable']);
                }
                if ($coaTabLink['LinkID']) {
                    $query->where('coa_tab_link.LinkID', '=', $coaTabLink['LinkID']);
                }
            }
        }

        // Quyền Xem
        $query->leftJoin('coa_tab_per', 'coa_tab.AccountID', '=', 'coa_tab_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_tab_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_tab.AccessType', '=', 2);
                });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        //$query->groupBy('coa_tab.AccountID');

        if($request->post('exportData')){
            $query->select('coa_tab.AccountNo', 'coa_tab.AccountName');
            return $query->orderBy('coa_tab.AccountNo', 'ASC')->get();
        }
        //
        if($request->post('viewType') === 'tree'){
            $query-> where('coa_tab.Level', '=', 1);
            $query-> groupBy('coa_tab.AccountID')->orderBy('coa_tab.AccountNo');
            return $query->paginate($per_page);
        }

        return  $query->orderBy('coa_tab.AccountNo', 'ASC')->paginate($per_page);

    }




    protected $fillable = [
        'AccountNo',
        'AccountName',
        'ParentID',
        'ParentNo',
        'Level',
        'Detail',
        'CoaTabType',
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
