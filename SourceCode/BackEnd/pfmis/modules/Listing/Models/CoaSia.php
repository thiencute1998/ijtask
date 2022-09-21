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

Class CoaSia extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_sia';
    protected $primaryKey = 'AccountID';

    protected static   $searchable = ['AccountNo','AccountName'];



    public  static function search(Request $request){
        $data = null;
        $query = CoaSia::query();
        $where = [];
        $auth = Auth::user();
        $query->select('coa_sia.*');
        if(isset($request->Inactive)){
            $query->where('coa_sia.Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('AccountName')){
            $query->where('coa_sia.AccountName', 'like', '%'.$request->AccountName.'%');
        }
        if($request->post('AccountNo')){
            $query->where('coa_sia.AccountNo', 'like', '%'.$request->AccountNo.'%');
        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Danh mục liên kết
        $CoaSiaLink = $request->post('CoaSiaLink');
        if($CoaSiaLink && count($CoaSiaLink) > 0){
            $query->leftJoin('coa_sia_link','coa_sia.AccountID','=','coa_sia_link.AccountID');
            foreach($CoaSiaLink as $coaSiaLink){
                if ($coaSiaLink['LinkTable']) {
                    $query->where('coa_sia_link.LinkTable', '=', $coaSiaLink['LinkTable']);
                }
                if ($coaSiaLink['LinkID']) {
                    $query->where('coa_sia_link.LinkID', '=', $coaSiaLink['LinkID']);
                }
            }
        }

        // Quyền Xem
        $query->leftJoin('coa_sia_per', 'coa_sia.AccountID', '=', 'coa_sia_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_sia_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_sia.AccessType', '=', 2);
                });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('coa_sia.AccountID');

        if($request->post('exportData')){
            $query->select('coa_sia.AccountNo', 'coa_sia.AccountName');
            return $query->orderBy('coa_sia.AccountID', 'desc')->get();
        }
        if($request->post('viewType') === 'tree'){
            $query-> where('coa_sia.Level', '=', 1);
            $query-> groupBy('coa_sia.AccountID')->orderBy('coa_sia.AccountNo');
            $result = $query->paginate($per_page);
            $coaSias = $result->items();
            // lấy tất cả con
            $allCoaSiaTree = [];
            foreach ($coaSias as $coaSia){
                $coaSiaTree = self::populateChildrenCoaSias($coaSia);
                foreach ($coaSiaTree as $con ){
                    $allCoaSiaTree[] = $con;
                }
            }
            // Loại bỏ trùng nhau
            $uniqueCoaSias = [];
            $tmpUids = [];
            foreach ($allCoaSiaTree as $coaSia) {
                if (!in_array($coaSia->AccountID, $tmpUids)) {
                    $tmpUids[] = $coaSia->AccountID;
                    $uniqueCoaSias[] = $coaSia;
                }
            }
            // sắp xếp
            usort($uniqueCoaSias, function ($a, $b) {
                return strcmp($a->AccountNo, $b->AccountNo);
            });
            return [
                'total' => $result->total(),
                'per_page' => $result->perPage(),
                'current_page' => $result->currentPage(),
                'data' =>  $uniqueCoaSias
            ];

        }
        return $query->orderBy('coa_sia.AccountID','desc')->paginate($per_page);
    }

    public static function populateChildrenCoaSias($coaSia){
        $query = CoaSia::query();
        $query->where('AccountNo', 'like', $coaSia->AccountNo.'%' );
        $coaSia = $query->get();
        $coaSiaChild = $coaSia;

        return $coaSiaChild;
    }
    protected $fillable = [
        'AccountNo',
        'AccountName',
        'ParentID',
        'Level',
        'Detail',
        'CoaSiaType',
        'BalanceType',
        'Note',
        'UserIDCreated',
        'AuthorizedPerson',
        'AccessType'
    ];
    public  $timestamps = false;
}
?>
