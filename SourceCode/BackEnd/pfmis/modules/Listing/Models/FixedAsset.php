<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Module\SysAdmin\Models\SysSetup;

class FixedAsset extends Model{
    use IjFullTextSearch;
    protected $table = 'fixed_asset';
    protected $primaryKey = 'FixedAssetID';

    protected static $searchable = ['FixedAssetNo', 'FixedAssetName', 'Address'];


    public static function search(Request $request){
        $data = null;
        $query = FixedAsset::query()->select('fixed_asset.*');
        $auth = Auth::user();
        if($request->post('FixedAssetName')){
            $query->where('FixedAssetName', 'like', '%' . $request->FixedAssetName . '%');
        }
        if($request->post('FixedAssetNo')){
            $query->whereRaw('FixedAssetNo like("%' . $request->FixedAssetNo . '%")');
        }

        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $fixedAssetLinks = $request->post('FixedAssetLink');
        if ($fixedAssetLinks && (count($fixedAssetLinks) > 0)) {
            $query->join('fixed_asset_link', 'fixed_asset.FixedAssetID', '=', 'fixed_asset_link.FixedAssetID');
            foreach ($fixedAssetLinks as $fixedAssetLink) {
                if ($fixedAssetLink['LinkTable']) {
                    $query->where('fixed_asset_link.LinkTable', '=', $fixedAssetLink['LinkTable']);
                }
                if ($fixedAssetLink['LinkID']) {
                    $query->where('fixed_asset_link.LinkID', '=', $fixedAssetLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem tài liệu
        $query->leftJoin('fixed_asset_per', 'fixed_asset.FixedAssetID', '=', 'fixed_asset_per.FixedAssetID');
        $query->where(function ($q) use ($auth) {
            $q->where('fixed_asset_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('fixed_asset.AccessType', '=', 2);
            });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('fixed_asset.FixedAssetID');

        if ($request->has('exportData')) {
            $query->select('fixed_asset.FixedAssetID','fixed_asset.FixedAssetNo','fixed_asset.STT', 'fixed_asset.FixedAssetName', 'fixed_asset.UomName');
            return $query->orderBy('FixedAssetID', 'asc')->paginate($request->post('per_page'));
        }

        return $query->orderBy('FixedAssetID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'FixedAssetNo',
        'FixedAssetName',
        'Serialnumber',
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
