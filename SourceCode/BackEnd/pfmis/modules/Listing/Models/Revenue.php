<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Module\Listing\Traits\RevenueCacheClear;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUser;
use phpDocumentor\Reflection\Types\Self_;
use function foo\func;

class Revenue extends Model{
    use IjFullTextSearch, RevenueCacheClear;
    protected $table = 'revenue';
    protected $primaryKey = 'RevenueID';

    protected static $searchable = ['RevenueNo', 'RevenueName' ];

    public static function search(Request $request){
        $data = null;
        $query = Revenue::query();
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('RevenueName')){
            $query->where('RevenueName', 'like', '%'.$request->RevenueName.'%');
        }
        if($request->post('RevenueNo')){
            $query->where('RevenueNo', 'like', '%'.$request->RevenueNo.'%');
        }


        //Danh mục liên kết
        $RevenueLink = $request->post('RevenueLink');
        if($RevenueLink && count($RevenueLink) > 0){
            $query->leftJoin('revenue_link','revenue.RevenueID','=','revenue_link.RevenueID');
            foreach($RevenueLink as $revenueLink){
                if ($revenueLink['LinkTable']) {
                    $query->where('revenue_link.LinkTable', '=', $revenueLink['LinkTable']);
                }
                if ($revenueLink['LinkID']) {
                    $query->where('revenue_link.LinkID', '=', $revenueLink['LinkID']);
                }
            }
        }

        // Loại khoản thu
        $RevenueCateList = $request->post('RevenueCateList');
        if($RevenueCateList && count($RevenueCateList) > 0){
            $query->leftJoin('revenue_cate','revenue.RevenueID','=','revenue_cate.RevenueID');
            $query->where(function ($query) use ($RevenueCateList) {
                foreach($RevenueCateList as $revenueCateList){
                    $arrCateID[]= $revenueCateList['CateID'];
                    if($revenueCateList['CateValue']){
                        $arrCateValue[]=$revenueCateList['CateValue'];
                    };
                    $query->orWhere(function($q) use ($revenueCateList){
                        $q->Where('CateID', $revenueCateList['CateID']);
                        if($revenueCateList['CateValue']){
                            $q->Where('CateValue', $revenueCateList['CateValue']);
                        }
                    });
                }
            });


        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

//        // Quyền xem
//        $query->leftJoin('revenue_per','revenue_per.RevenueID','=','revenue.RevenueID');
//        $query->where(function ($q) use ($auth){
//            $q->where('revenue_per.EmployeeID',$auth->EmployeeID)
//                ->orWhere('revenue.AccessType',2);
//        });
        if ($request->has('exportData')) {
            $query->select('revenue.RevenueID','revenue.RevenueNo', 'revenue.RevenueName','revenue.STT');
            return $query->orderBy('RevenueID', 'asc')->paginate($request->post('per_page'));
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        if($request->post('viewType') === 'tree'){
            $query-> where('revenue.Level', '=', 1);
            $query-> groupBy('revenue.RevenueID')->orderBy('revenue.RevenueNo');
            return $query->paginate($per_page);
        }
        $query->groupBy('revenue.RevenueID');
        return $query->orderBy(DB::raw('RevenueNo'))->paginate($per_page);
    }

    public static function getRevenue($request)
    {
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        if ($request->get('RevenueName')) {
            $revenueName = $request->get('RevenueName');
            $query->where('RevenueName', 'like', '%' . $revenueName . '%');
        }

        $query->orderBy('RevenueID', 'DESC');
        $revenue = $query->paginate($perPage);
        return $revenue;
    }

    public static function getAllChildRevenue($listID){
        $result = [];
        $revenues = Revenue::whereIn('ParentID', $listID)->get();
        if (count($revenues)) {
            $revenueIDs = [];
            foreach ($revenues as $revenue) {
                $revenueIDs[] = $revenue->RevenueID;
                $result[] = $revenue;
            }
            $records = Revenue::getAllChildRevenue($revenueIDs);
            $result = array_merge($result, $records);
        }

        return $result;
    }

    protected $fillable = [
        'RevenueID',
        'RevenueNo',
        'RevenueName',
        'ParentID',
        'ParentNo',
        'ParentName',
        'Level',
        'Path',
        'Detail',
        'UomID',
        'UomName',
        'NumberValue',
        'Note',
        'SbiItemID',
        'SbiItemNo',
        'SbiItemName',
        'NormID',
        'NormNo',
        'NormName',
        'BudgetBalanceType',
        'BudgetStateType',
        'isRevenueRegulationRate',
        'SbrSectorID',
        'SbrSectorNo',
        'SbrSectorName',
    ];
    public $timestamps = false;


}

?>
