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

Class CoaEas extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_eas';
    protected $primaryKey = 'AccountID';

    protected static   $searchable = ['AccountNo','AccountName'];



    public  static function search(Request $request){
        $data = null;
        $query = CoaEas::query();
        $query->select('coa_eas.*');
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('AccountName')){
            $query->where('coa_eas.AccountName', 'like', '%'.$request->AccountName.'%');
        }
        if($request->post('AccountNo')){
            $query->where('coa_eas.AccountNo', 'like', '%'.$request->AccountNo.'%');
        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Danh mục liên kết
        $CoaEasLink = $request->post('CoaEasLink');
        if($CoaEasLink && count($CoaEasLink) > 0){
            $query->leftJoin('coa_eas_link','coa_eas.AccountID','=','coa_eas_link.AccountID');
            foreach($CoaEasLink as $coaEasLink){
                if ($coaEasLink['LinkTable']) {
                    $query->where('coa_eas_link.LinkTable', '=', $coaEasLink['LinkTable']);
                }
                if ($coaEasLink['LinkID']) {
                    $query->where('coa_eas_link.LinkID', '=', $coaEasLink['LinkID']);
                }
            }
        }

        // Quyền Xem
        $query->leftJoin('coa_eas_per', 'coa_eas.AccountID', '=', 'coa_eas_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_eas_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_eas.AccessType', '=', 2);
                });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        //$query->groupBy('coa_eas.AccountID');

        if($request->post('exportData')){
            $query->select('coa_eas.AccountNo', 'coa_eas.AccountName');
            return $query->orderBy('coa_eas.AccountNo', 'ASC')->get();
        }
        //
        if($request->post('viewType') === 'tree'){
            $query-> where('coa_eas.Level', '=', 1);
            $query-> groupBy('coa_eas.AccountID')->orderBy('coa_eas.AccountNo');
            return $query->paginate($per_page);
        }

        return  $query->orderBy('coa_eas.AccountNo', 'ASC')->paginate($per_page);

    }




    protected $fillable = [
        'AccountNo',
        'AccountName',
        'ParentID',
        'ParentNo',
        'Level',
        'Detail',
        'CoaEasType',
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
