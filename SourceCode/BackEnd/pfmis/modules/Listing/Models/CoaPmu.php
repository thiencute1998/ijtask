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

Class CoaPmu extends Model{
    use IjFullTextSearch;
    protected $table = 'coa_pmu';
    protected $primaryKey = 'AccountID';

    protected static   $searchable = ['AccountNo','AccountName'];



    public  static function search(Request $request){
        $data = null;
        $query = CoaPmu::query();
        $query->select('coa_pmu.*');
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('AccountName')){
            $query->where('coa_pmu.AccountName', 'like', '%'.$request->AccountName.'%');
        }
        if($request->post('AccountNo')){
            $query->where('coa_pmu.AccountNo', 'like', '%'.$request->AccountNo.'%');
        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Danh mục liên kết
        $CoaPmuLink = $request->post('CoaPmuLink');
        if($CoaPmuLink && count($CoaPmuLink) > 0){
            $query->leftJoin('coa_pmu_link','coa_pmu.AccountID','=','coa_pmu_link.AccountID');
            foreach($CoaPmuLink as $coaPmuLink){
                if ($coaPmuLink['LinkTable']) {
                    $query->where('coa_pmu_link.LinkTable', '=', $coaPmuLink['LinkTable']);
                }
                if ($coaPmuLink['LinkID']) {
                    $query->where('coa_pmu_link.LinkID', '=', $coaPmuLink['LinkID']);
                }
            }
        }

        // Quyền Xem
        $query->leftJoin('coa_pmu_per', 'coa_pmu.AccountID', '=', 'coa_pmu_per.AccountID');
        $query->where(function($q) use($auth){
            $q->where('coa_pmu_per.EmployeeID','=', $auth->EmployeeID)
                ->orWhere(function ($q){
                    $q->where('coa_pmu.AccessType', '=', 2);
                });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        //$query->groupBy('coa_pmu.AccountID');

        if($request->post('exportData')){
            $query->select('coa_pmu.AccountNo', 'coa_pmu.AccountName');
            return $query->orderBy('coa_pmu.AccountNo', 'ASC')->get();
        }
        //
        if($request->post('viewType') === 'tree'){
            $query-> where('coa_pmu.Level', '=', 1);
            $query-> groupBy('coa_pmu.AccountID')->orderBy('coa_pmu.AccountNo');
            return $query->paginate($per_page);
        }

        return  $query->orderBy('coa_pmu.AccountNo', 'ASC')->paginate($per_page);

    }




    protected $fillable = [
        'AccountNo',
        'AccountName',
        'ParentID',
        'ParentNo',
        'Level',
        'Detail',
        'CoaPmuType',
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
