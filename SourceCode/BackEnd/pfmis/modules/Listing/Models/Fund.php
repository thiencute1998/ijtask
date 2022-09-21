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

Class Fund extends Model{
    use IjFullTextSearch;
    protected $table = 'fund';
    protected $primaryKey = 'FundID';

    protected static   $searchable = ['FundNo','FundName'];



    public static function search(Request $request){
        $data = null;
        $query = Fund::query();
        $query->select('fund.*');
        $where = [];
        $auth = Auth::user();

        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('FundName')){
            $query->where('fund.FundName', 'like', '%'.$request->FundName.'%');
        }
        if($request->post('FundNo')){
            $query->where('fund.FundNo', 'like', '%'.$request->FundNo.'%');
        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Danh mục liên kết
        $FundLink = $request->post('FundLink');
        if($FundLink && count($FundLink) > 0){
            $query->leftJoin('fund_link','fund.FundID','=','fund_link.FundID');
            foreach($FundLink as $fundLink){
                if ($fundLink['LinkTable']) {
                    $query->where('fund_link.LinkTable', '=', $fundLink['LinkTable']);
                }
                if ($fundLink['LinkID']) {
                    $query->where('fund_link.LinkID', '=', $fundLink['LinkID']);
                }
            }
        }

        // Quyền Xem
        $query->leftJoin('fund_per', 'fund.FundID', '=', 'fund_per.FundID');

        $query->where(function($q) use($auth){
            $q->where('fund_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('fund.AccessType', '=', 2);
                });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        //$query->groupBy('fund.FundID');

        if ($request->has('exportData')) {
            $query->select('fund.FundID','fund.FundNo', 'fund.FundName','fund.STT');
            return $query->orderBy('FundID', 'asc')->paginate($request->post('per_page'));
        }
        return $query->orderBy('fund.FundID','desc')->paginate($per_page);
    }

    protected $fillable = [
        'FundNo',
        'FundName',
        'ParentID',
        'Level',
        'Detail',
        'FundType',
        'BalanceType',
        'Note',
        'UserIDCreated',
        'AuthorizedPerson',
        'AccessType',
        'Inactive',
        'Locked'
    ];
    public  $timestamps = false;
}
?>
