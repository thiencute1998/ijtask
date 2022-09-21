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

Class CoaPcf extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_pcf';
    protected $primaryKey = 'AccountID';

    protected static   $searchable = ['AccountNo','AccountName'];



    public  static function search(Request $request){
        $data = null;
        $query = CoaPcf::query();
        $query->select('coa_pcf.*');
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('coa_pcf.Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('AccountName')){
            $query->where('coa_pcf.AccountName', 'like', '%'.$request->AccountName.'%');
        }
        if($request->post('AccountNo')){
            $query->where('coa_pcf.AccountNo', 'like', '%'.$request->AccountNo.'%');
        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Danh mục liên kết
        $CoaPcfLink = $request->post('CoaPcfLink');
        if($CoaPcfLink && count($CoaPcfLink) > 0){
            $query->leftJoin('coa_pcf_link','coa_pcf.AccountID','=','coa_pcf_link.AccountID');
            foreach($CoaPcfLink as $coaPcfLink){
                if ($coaPcfLink['LinkTable']) {
                    $query->where('coa_pcf_link.LinkTable', '=', $coaPcfLink['LinkTable']);
                }
                if ($coaPcfLink['LinkID']) {
                    $query->where('coa_pcf_link.LinkID', '=', $coaPcfLink['LinkID']);
                }
            }
        }

        // Quyền Xem
        $query->leftJoin('coa_pcf_per', 'coa_pcf.AccountID', '=', 'coa_pcf_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_pcf_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_pcf.AccessType', '=', 2);
                });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        //$query->groupBy('coa_pcf.AccountID');

        if($request->post('exportData')){
            $query->select('coa_pcf.AccountNo', 'coa_pcf.AccountName');
            return $query->orderBy('coa_pcf.AccountID', 'desc')->get();
        }
        if($request->post('viewType') === 'tree'){
            $query-> where('coa_pcf.Level', '=', 1);
            $query-> groupBy('coa_pcf.AccountID')->orderBy('coa_pcf.AccountNo');
            $result = $query->paginate($per_page);
            $coaPcfs = $result->items();
            // lấy tất cả con
            $allCoaPcfTree = [];
            foreach ($coaPcfs as $coaPcf){
                $coaPcfTree = self::populateChildrenCoaPcfs($coaPcf);
                foreach ($coaPcfTree as $con ){
                    $allCoaPcfTree[] = $con;
                }
            }
            // Loại bỏ trùng nhau
            $uniqueCoaPcfs = [];
            $tmpUids = [];
            foreach ($allCoaPcfTree as $coaPcf) {
                if (!in_array($coaPcf->AccountID, $tmpUids)) {
                    $tmpUids[] = $coaPcf->AccountID;
                    $uniqueCoaPcfs[] = $coaPcf;
                }
            }
            // sắp xếp
            usort($uniqueCoaPcfs, function ($a, $b) {
                return strcmp($a->AccountNo, $b->AccountNo);
            });
            return [
                'total' => $result->total(),
                'per_page' => $result->perPage(),
                'current_page' => $result->currentPage(),
                'data' =>  $uniqueCoaPcfs
            ];

        }
        return $query->orderBy('coa_pcf.AccountID','desc')->paginate($per_page);
    }
    public static function populateChildrenCoaPcfs($coaPcf){
        $query = CoaPcf::query();
        $query->where('AccountNo', 'like', $coaPcf->AccountNo.'%' );
        $coaPcf = $query->get();
        $coaPcfChild = $coaPcf;

        return $coaPcfChild;
    }
    protected $fillable = [
        'AccountNo',
        'AccountName',
        'ParentID',
        'Level',
        'Detail',
        'CoaPcfType',
        'BalanceType',
        'Note',
        'UserIDCreated',
        'AuthorizedPerson',
        'AccessType'
    ];
    public  $timestamps = false;
}
?>
