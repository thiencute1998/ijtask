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

Class CoaSnr extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_snr';
    protected $primaryKey = 'AccountID';

    protected static   $searchable = ['AccountNo','AccountName'];



    public  static function search(Request $request){
        $data = null;
        $query = CoaSnr::query();
        $where = [];
        $auth = Auth::user();
        $query->select('coa_snr.*');
        if(isset($request->Inactive)){
            $query->where('coa_snr.Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('AccountName')){
            $query->where('coa_snr.AccountName', 'like', '%'.$request->AccountName.'%');
        }
        if($request->post('AccountNo')){
            $query->where('coa_snr.AccountNo', 'like', '%'.$request->AccountNo.'%');
        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Danh mục liên kết
        $CoaSnrLink = $request->post('CoaSnrLink');
        if($CoaSnrLink && count($CoaSnrLink) > 0){
            $query->leftJoin('coa_snr_link','coa_snr.AccountID','=','coa_snr_link.AccountID');
            foreach($CoaSnrLink as $coaSnrLink){
                if ($coaSnrLink['LinkTable']) {
                    $query->where('coa_snr_link.LinkTable', '=', $coaSnrLink['LinkTable']);
                }
                if ($coaSnrLink['LinkID']) {
                    $query->where('coa_snr_link.LinkID', '=', $coaSnrLink['LinkID']);
                }
            }
        }

        // Quyền Xem
        $query->leftJoin('coa_snr_per', 'coa_snr.AccountID', '=', 'coa_snr_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_snr_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_snr.AccessType', '=', 2);
                });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('coa_snr.AccountID');

        if($request->post('exportData')){
            $query->select('coa_snr.AccountNo', 'coa_snr.AccountName');
            return $query->orderBy('coa_snr.AccountID', 'desc')->get();
        }
        if($request->post('viewType') === 'tree'){
            $query-> where('coa_snr.Level', '=', 1);
            $query-> groupBy('coa_snr.AccountID')->orderBy('coa_snr.AccountNo');
            $result = $query->paginate($per_page);
            $coaSnrs = $result->items();
            // lấy tất cả con
            $allCoaSnrTree = [];
            foreach ($coaSnrs as $coaSnr){
                $coaSnrTree = self::populateChildrenCoaSnrs($coaSnr);
                foreach ($coaSnrTree as $con ){
                    $allCoaSnrTree[] = $con;
                }
            }
            // Loại bỏ trùng nhau
            $uniqueCoaSnrs = [];
            $tmpUids = [];
            foreach ($allCoaSnrTree as $coaSnr) {
                if (!in_array($coaSnr->AccountID, $tmpUids)) {
                    $tmpUids[] = $coaSnr->AccountID;
                    $uniqueCoaSnrs[] = $coaSnr;
                }
            }
            // sắp xếp
            usort($uniqueCoaSnrs, function ($a, $b) {
                return strcmp($a->AccountNo, $b->AccountNo);
            });
            return [
                'total' => $result->total(),
                'per_page' => $result->perPage(),
                'current_page' => $result->currentPage(),
                'data' =>  $uniqueCoaSnrs
            ];

        }
        return $query->orderBy('coa_snr.AccountID','desc')->paginate($per_page);
    }

    public static function populateChildrenCoaSnrs($coaSnr){
        $query = CoaSnr::query();
        $query->where('AccountNo', 'like', $coaSnr->AccountNo.'%' );
        $coaSnr = $query->get();
        $coaSnrChild = $coaSnr;

        return $coaSnrChild;
    }
    protected $fillable = [
        'AccountNo',
        'AccountName',
        'ParentID',
        'Level',
        'Detail',
        'CoaSnrType',
        'BalanceType',
        'Note',
        'UserIDCreated',
        'AuthorizedPerson',
        'AccessType'
    ];
    public  $timestamps = false;
}
?>
