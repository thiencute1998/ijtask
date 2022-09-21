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

Class CoaSna extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_sna';
    protected $primaryKey = 'AccountID';

    protected static   $searchable = ['AccountNo','AccountName'];



    public  static function search(Request $request){
        $data = null;
        $query = CoaSna::query();
        $where = [];
        $auth = Auth::user();
        $query->select('coa_sna.*');
        if(isset($request->Inactive)){
            $query->where('coa_sna.Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('AccountName')){
            $query->where('coa_sna.AccountName', 'like', '%'.$request->AccountName.'%');
        }
        if($request->post('AccountNo')){
            $query->where('coa_sna.AccountNo', 'like', '%'.$request->AccountNo.'%');
        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Danh mục liên kết
        $CoaSnaLink = $request->post('CoaSnaLink');
        if($CoaSnaLink && count($CoaSnaLink) > 0){
            $query->leftJoin('coa_sna_link','coa_sna.AccountID','=','coa_sna_link.AccountID');
            foreach($CoaSnaLink as $coaSnaLink){
                if ($coaSnaLink['LinkTable']) {
                    $query->where('coa_sna_link.LinkTable', '=', $coaSnaLink['LinkTable']);
                }
                if ($coaSnaLink['LinkID']) {
                    $query->where('coa_sna_link.LinkID', '=', $coaSnaLink['LinkID']);
                }
            }
        }

        // Quyền Xem
        $query->leftJoin('coa_sna_per', 'coa_sna.AccountID', '=', 'coa_sna_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_sna_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_sna.AccessType', '=', 2);
                });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('coa_sna.AccountID');

        if($request->post('exportData')){
            $query->select('coa_sna.AccountNo', 'coa_sna.AccountName');
            return $query->orderBy('coa_sna.AccountID', 'desc')->get();
        }
        if($request->post('viewType') === 'tree'){
            $query-> where('coa_sna.Level', '=', 1);
            $query-> groupBy('coa_sna.AccountID')->orderBy('coa_sna.AccountNo');
            $result = $query->paginate($per_page);
            $coaSnas = $result->items();
            // lấy tất cả con
            $allCoaSnaTree = [];
            foreach ($coaSnas as $coaSna){
                $coaSnaTree = self::populateChildrenCoaSnas($coaSna);
                foreach ($coaSnaTree as $con ){
                    $allCoaSnaTree[] = $con;
                }
            }
            // Loại bỏ trùng nhau
            $uniqueCoaSnas = [];
            $tmpUids = [];
            foreach ($allCoaSnaTree as $coaSna) {
                if (!in_array($coaSna->AccountID, $tmpUids)) {
                    $tmpUids[] = $coaSna->AccountID;
                    $uniqueCoaSnas[] = $coaSna;
                }
            }
            // sắp xếp
            usort($uniqueCoaSnas, function ($a, $b) {
                return strcmp($a->AccountNo, $b->AccountNo);
            });
            return [
                'total' => $result->total(),
                'per_page' => $result->perPage(),
                'current_page' => $result->currentPage(),
                'data' =>  $uniqueCoaSnas
            ];

        }
        return $query->orderBy('coa_sna.AccountID','desc')->paginate($per_page);
    }

    public static function populateChildrenCoaSnas($coaSna){
        $query = CoaSna::query();
        $query->where('AccountNo', 'like', $coaSna->AccountNo.'%' );
        $coaSna = $query->get();
        $coaSnaChild = $coaSna;

        return $coaSnaChild;
    }
    protected $fillable = [
        'AccountNo',
        'AccountName',
        'ParentID',
        'Level',
        'Detail',
        'CoaSnaType',
        'BalanceType',
        'Note',
        'UserIDCreated',
        'AuthorizedPerson',
        'AccessType'
    ];
    public  $timestamps = false;
}
?>
