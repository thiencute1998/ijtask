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

Class CoaCon extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_con';
    protected $primaryKey = 'AccountID';

    protected static   $searchable = ['AccountNo','AccountName'];



    public  static function search(Request $request){
        $data = null;
        $query = CoaCon::query();
        $query->select('coa_con.*');
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('AccountName')){
            $query->where('coa_con.AccountName', 'like', '%'.$request->AccountName.'%');
        }
        if($request->post('AccountNo')){
            $query->where('coa_con.AccountNo', 'like', '%'.$request->AccountNo.'%');
        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Danh mục liên kết
        $CoaConLink = $request->post('CoaConLink');
        if($CoaConLink && count($CoaConLink) > 0){
            $query->leftJoin('coa_con_link','coa_con.AccountID','=','coa_con_link.AccountID');
            foreach($CoaConLink as $coaConLink){
                if ($coaConLink['LinkTable']) {
                    $query->where('coa_con_link.LinkTable', '=', $coaConLink['LinkTable']);
                }
                if ($coaConLink['LinkID']) {
                    $query->where('coa_con_link.LinkID', '=', $coaConLink['LinkID']);
                }
            }
        }

        // Quyền Xem
        $query->leftJoin('coa_con_per', 'coa_con.AccountID', '=', 'coa_con_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_con_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_con.AccessType', '=', 2);
                });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        //$query->groupBy('coa_con.AccountID');

        if($request->post('exportData')){
            $query->select('coa_con.AccountNo', 'coa_con.AccountName');
            return $query->orderBy('coa_con.AccountNo', 'ASC')->get();
        }
        //
        if($request->post('viewType') === 'tree'){
           $query-> where('coa_con.Level', '=', 1);
           $query-> groupBy('coa_con.AccountID')->orderBy('coa_con.AccountNo');
           return $query->paginate($per_page);
        }

        return  $query->orderBy('coa_con.AccountNo', 'ASC')->paginate($per_page);

    }




    protected $fillable = [
        'AccountNo',
        'AccountName',
        'ParentID',
        'ParentNo',
        'Level',
        'Detail',
        'CoaConType',
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
