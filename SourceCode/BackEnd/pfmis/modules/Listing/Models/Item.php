<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Module\SysAdmin\Models\SysSetup;

class Item extends Model{
    use IjFullTextSearch;
    protected $table = 'item';
    protected $primaryKey = 'ItemID';

    protected static $searchable = ['ItemNo', 'ItemName', 'Address'];


    public static function search(Request $request){
        $data = null;
        $query = Item::query()->select('item.*');
        $auth = Auth::user();
        if($request->post('ItemName')){
            $query->where('ItemName', 'like', '%' . $request->ItemName . '%');
        }
        if($request->post('ItemNo')){
            $query->whereRaw('ItemNo like("%' . $request->ItemNo . '%")');
        }

        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $itemLinks = $request->post('ItemLink');
        if ($itemLinks && (count($itemLinks) > 0)) {
            $query->join('item_link', 'item.ItemID', '=', 'item_link.ItemID');
            foreach ($itemLinks as $itemLink) {
                if ($itemLink['LinkTable']) {
                    $query->where('item_link.LinkTable', '=', $itemLink['LinkTable']);
                }
                if ($itemLink['LinkID']) {
                    $query->where('item_link.LinkID', '=', $itemLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem tài liệu
        $query->leftJoin('item_per', 'item.ItemID', '=', 'item_per.ItemID');
        $query->where(function ($q) use ($auth) {
            $q->where('item_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('item.AccessType', '=', 2);
            });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('item.ItemID');

        if ($request->has('exportData')) {
            $query->select('item.ItemID','item.ItemNo','item.STT', 'item.ItemName', 'item.UomName');
            return $query->orderBy('ItemID', 'asc')->paginate($request->post('per_page'));
        }
        if($request->post('viewType')==='tree'){
            $query->where('Level', '=', 1)->groupBy('item.ItemID')->orderBy('item.ItemNo', 'asc');
            return $query->paginate($per_page);
        }
        return $query->orderBy('ItemID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'ItemNo',
        'ItemName',
        'ItemType',
        'UomID',
        'UomName',
        'Locked',
        'Inactive',
        'AccessType',
        'UserIDCreated',
        'AuthorizedPerson'
    ];
    public $timestamps = false;


}

?>
