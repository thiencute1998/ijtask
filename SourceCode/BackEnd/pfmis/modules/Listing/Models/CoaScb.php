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

Class CoaScb extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_scb';
    protected $primaryKey = 'AccountID';

    protected static   $searchable = ['AccountNo','AccountName'];



    public  static function search(Request $request){
        $data = null;
        $query = CoaScb::query();
        $query->select('coa_scb.*');
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('AccountName')){
            $query->where('coa_scb.AccountName', 'like', '%'.$request->AccountName.'%');
        }
        if($request->post('AccountNo')){
            $query->where('coa_scb.AccountNo', 'like', '%'.$request->AccountNo.'%');
        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Danh mục liên kết
        $CoaScbLink = $request->post('CoaScbLink');
        if($CoaScbLink && count($CoaScbLink) > 0){
            $query->leftJoin('coa_scb_link','coa_scb.AccountID','=','coa_scb_link.AccountID');
            foreach($CoaScbLink as $coaScbLink){
                if ($coaScbLink['LinkTable']) {
                    $query->where('coa_scb_link.LinkTable', '=', $coaScbLink['LinkTable']);
                }
                if ($coaScbLink['LinkID']) {
                    $query->where('coa_scb_link.LinkID', '=', $coaScbLink['LinkID']);
                }
            }
        }

        // Quyền Xem
        $query->leftJoin('coa_scb_per', 'coa_scb.AccountID', '=', 'coa_scb_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_scb_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_scb.AccessType', '=', 2);
                });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        //$query->groupBy('coa_scb.AccountID');

        if($request->post('exportData')){
            $query->select('coa_scb.AccountNo', 'coa_scb.AccountName');
            return $query->orderBy('coa_scb.AccountNo', 'ASC')->get();
        }
        //
        if($request->post('viewType') === 'tree'){
            $query-> where('coa_scb.Level', '=', 1);
            $query-> groupBy('coa_scb.AccountID')->orderBy('coa_scb.AccountNo');
            return $query->paginate($per_page);
        }

        return  $query->orderBy('coa_scb.AccountNo', 'ASC')->paginate($per_page);

    }




    protected $fillable = [
        'AccountNo',
        'AccountName',
        'ParentID',
        'ParentNo',
        'Level',
        'Detail',
        'CoaScbType',
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
