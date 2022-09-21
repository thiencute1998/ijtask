<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\Listing\Traits\SbiItemCacheClear;
use Module\SysAdmin\Models\SysSetup;

class SbiItem extends Model{
    use IjFullTextSearch, SbiItemCacheClear;
    protected $table = 'sbi_item';
    protected $primaryKey = 'SbiItemID';

    protected static $searchable = ['SbiItemNo', 'SbiItemName'];


    public static function search(Request $request){
        $data = null;
        $query = SbiItem::query()->select('sbi_item.*');
        $auth = Auth::user();
        if($request->post('SbiItemName')){
            $query->where('sbi_item.SbiItemName', 'like', '%' . $request->SbiItemName . '%');
        }
        if($request->post('SbiItemNo')){
            $query->whereRaw('sbi_item.SbiItemNo like("%' . $request->SbiItemNo . '%")');
        }
        if($request->post('SbiItemType')){
            $query->where('SbiItemType',$request->SbiItemType);
        }
        if($request->post('SbiItemGroup')){
            $query->where('SbiItemGroup',$request->SbiItemGroup);
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $sbi_itemLinks = $request->post('SbiItemLink');
        if ($sbi_itemLinks && (count($sbi_itemLinks) > 0)) {
            $query->join('sbi_item_link', 'sbi_item.SbiItemID', '=', 'sbi_item_link.SbiItemID');
            foreach ($sbi_itemLinks as $sbi_itemLink) {
                if ($sbi_itemLink['LinkTable']) {
                    $query->where('sbi_item_link.LinkTable', '=', $sbi_itemLink['LinkTable']);
                }
                if ($sbi_itemLink['LinkID']) {
                    $query->where('sbi_item_link.LinkID', '=', $sbi_itemLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem tài liệu
        $query->leftJoin('sbi_item_per', 'sbi_item.SbiItemID', '=', 'sbi_item_per.SbiItemID');
        $query->where(function ($q) use ($auth) {
            $q->where('sbi_item_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('sbi_item.AccessType', '=', 2);
            });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }

        $viewType = $request->post('viewType');

        if($viewType == 'tree'){
            $query->groupBy('sbi_item.SbiItemID')
                ->orderBy('sbi_item.SbiItemID');

            $results = $query->paginate($per_page);
            $sbiItems = $results->items();

            // lấy tất cả cha
            $allSbiItemParent = self::populateParentSbiItems($sbiItems);
            foreach ($allSbiItemParent as $sbiItemParent) {
                $sbiItems[] = $sbiItemParent;
            }

            // Lấy tất cả con
            $allSbiItemChild = self::populateChildrenSbiItems($sbiItems);
            foreach ($allSbiItemChild as $sbiItemChild) {
                $sbiItems[] = $sbiItemChild;
            }

            // Loai bo sbiItem trùng nhau
            $uniqueSbiItems = [];
            $tmpUids = [];
            foreach ($sbiItems as $sbiItem) {
                if (!in_array($sbiItem->SbiItemID, $tmpUids)) {
                    $tmpUids[] = $sbiItem->SbiItemID;
                    $uniqueSbiItems[] = $sbiItem;
                }
            }

            // Xắp sếp theo thứ tự
//                    usort($uniqueSbiItems, function ($a, $b) {
//                        return strcmp($a->Level, $b->Level);
//                    });

            return [
                'total' => $results->total(),
                'per_page' => $results->perPage(),
                'current_page' => $results->currentPage(),
                'data' => $uniqueSbiItems
            ];
        }

        $query->groupBy('sbi_item.SbiItemID');

        if ($request->has('exportData')) {
            $query->select('sbi_item.SbiItemID','sbi_item.SbiItemNo', 'sbi_item.SbiItemName', 'sbi_item.SbiItemType', 'sbi_item.SbiItemGroup','sbi_item.STT');
            return $query->orderBy('SbiItemID', 'asc')->paginate($request->post('per_page'));
        }

        return $query->orderBy('SbiItemID')->paginate($per_page);
    }
    public static function populateChildrenSbiItems($sbiItems){
        $listSbiItemChild = [];
        $sbiItemIDArr = [];
        foreach ($sbiItems as $sbiItem) {
            $sbiItemIDArr[] = $sbiItem->SbiItemID;
        }
        if (count($sbiItemIDArr)) {
            $query = SbiItem::query();
            $query->whereIn('ParentID', $sbiItemIDArr);
            $sbiItemsChild = $query->get();
            if (count($sbiItemsChild)) {
                self::populateChildrenSbiItems($sbiItemsChild);
                foreach ($sbiItemsChild as $sbiItemChild) {
                    $listSbiItemChild[] = $sbiItemChild;
                }
            }
        }

        return $listSbiItemChild;

    }

    public static function populateParentSbiItems($sbiItems){
        $listSbiItemParent = [];
        $sbiItemIDArr = [];
        foreach ($sbiItems as $sbiItem) {
            if ($sbiItem->ParentID) {
                $sbiItemIDArr[] = $sbiItem->ParentID;
            }
        }
        if (count($sbiItemIDArr)) {
            $query = SbiItem::query();
            $query->whereIn('SbiItemID', $sbiItemIDArr);
            $sbiItemsParent = $query->get();
            if (count($sbiItemsParent)) {
                self::populateChildrenSbiItems($sbiItemsParent);
                foreach ($sbiItemsParent as $sbiItemParent) {
                    $listSbiItemParent[] = $sbiItemParent;
                }
            }
        }

        return $listSbiItemParent;

    }
    protected $fillable = [
        'SbiItemNo', 'SbiItemName', 'ParentID', 'Level', 'Detail','SbiItemType','SbiItemGroup','RevenueID','RevenueName',
        'ExpenseID','ExpenseName','Note', 'AccessType', 'NumberValue', 'UserIDCreated', 'AuthorizedPerson', 'Locked', 'Inactive'
    ];
    public $timestamps = false;


}

?>
