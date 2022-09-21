<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUser;
use phpDocumentor\Reflection\Types\Self_;

Class CoaLdi extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_ldi';
    protected $primaryKey = 'AccountID';

    protected static   $searchable = ['AccountNo','AccountName'];



    public  static function search(Request $request){
        $data = null;
        $query = CoaLdi::query();
        $query->select('coa_ldi.*');
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('coa_lid.Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('AccountName')){
            $query->where('coa_lid.AccountName', 'like', '%'.$request->AccountName.'%');
        }
        if($request->post('AccountNo')){
            $query->where('coa_lid.AccountNo', 'like', '%'.$request->AccountNo.'%');
        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Danh mục liên kết
        $CoaLdiLink = $request->post('CoaLdiLink');
        if($CoaLdiLink && count($CoaLdiLink) > 0){
            $query->leftJoin('coa_ldi_link','coa_ldi.AccountID','=','coa_ldi_link.AccountID');
            foreach($CoaLdiLink as $coaLdiLink){
                if ($coaLdiLink['LinkTable']) {
                    $query->where('coa_ldi_link.LinkTable', '=', $coaLdiLink['LinkTable']);
                }
                if ($coaLdiLink['LinkID']) {
                    $query->where('coa_ldi_link.LinkID', '=', $coaLdiLink['LinkID']);
                }
            }
        }

        // Quyền Xem
        $query->leftJoin('coa_ldi_per', 'coa_ldi.AccountID', '=', 'coa_ldi_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_ldi_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_ldi.AccessType', '=', 2);
                });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        //$query->groupBy('coa_ldi.AccountID');

        if($request->post('exportData')){
            $query->select('coa_ldi.AccountNo', 'coa_ldi.AccountName');
            return $query->orderBy('coa_ldi.AccountID', 'desc')->get();
        }
        if($request->post('viewType') === 'tree'){
            $query-> where('coa_ldi.Level', '=', 1);
            $query-> groupBy('coa_ldi.AccountID')->orderBy('coa_ldi.AccountNo');
            $result = $query->paginate($per_page);
            $coaLdis = $result->items();
            // lấy tất cả con
            $allCoaLdiTree = [];
            foreach ($coaLdis as $coaLdi){
                $coaLdiTree = self::populateChildrenCoaLdis($coaLdi);
                foreach ($coaLdiTree as $con ){
                    $allCoaLdiTree[] = $con;
                }
            }
            // Loại bỏ trùng nhau
            $uniqueCoaLdis = [];
            $tmpUids = [];
            foreach ($allCoaLdiTree as $coaLdi) {
                if (!in_array($coaLdi->AccountID, $tmpUids)) {
                    $tmpUids[] = $coaLdi->AccountID;
                    $uniqueCoaLdis[] = $coaLdi;
                }
            }
            // sắp xếp
            usort($uniqueCoaLdis, function ($a, $b) {
                return strcmp($a->AccountNo, $b->AccountNo);
            });
            return [
                'total' => $result->total(),
                'per_page' => $result->perPage(),
                'current_page' => $result->currentPage(),
                'data' =>  $uniqueCoaLdis
            ];

        }
        return $query->orderBy('coa_ldi.AccountID','desc')->paginate($per_page);
    }
    public static function populateChildrenCoaLdis($coaLdi){
        $query = CoaLdi::query();
        $query->where('AccountNo', 'like', $coaLdi->AccountNo.'%' );
        $coaLdi = $query->get();
        $coaLdiChild = $coaLdi;

        return $coaLdiChild;
    }

    protected $fillable = [
        'AccountNo',
        'AccountName',
        'ParentID',
        'Level',
        'Detail',
        'CoaLdiType',
        'BalanceType',
        'Note',
        'UserIDCreated',
        'AuthorizedPerson',
        'AccessType'
    ];
    public  $timestamps = false;
}
?>
