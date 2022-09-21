<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Module\SysAdmin\Models\SysSetup;

class InvestAsset extends Model{
    use IjFullTextSearch;
    protected $table = 'invest_asset';
    protected $primaryKey = 'InvestAssetID';

    protected static $searchable = ['InvestAssetNo', 'InvestAssetName', 'Address'];


    public static function search(Request $request){
        $data = null;
        $query = InvestAsset::query()->select('invest_asset.*');
        $auth = Auth::user();
        if($request->post('InvestAssetName')){
            $query->where('InvestAssetName', 'like', '%' . $request->InvestAssetName . '%');
        }
        if($request->post('InvestAssetNo')){
            $query->whereRaw('InvestAssetNo like("%' . $request->InvestAssetNo . '%")');
        }

        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $investAssetLinks = $request->post('InvestAssetLink');
        if ($investAssetLinks && (count($investAssetLinks) > 0)) {
            $query->join('invest_asset_link', 'invest_asset.InvestAssetID', '=', 'invest_asset_link.InvestAssetID');
            foreach ($investAssetLinks as $investAssetLink) {
                if ($investAssetLink['LinkTable']) {
                    $query->where('invest_asset_link.LinkTable', '=', $investAssetLink['LinkTable']);
                }
                if ($investAssetLink['LinkID']) {
                    $query->where('invest_asset_link.LinkID', '=', $investAssetLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem tài liệu
        $query->leftJoin('invest_asset_per', 'invest_asset.InvestAssetID', '=', 'invest_asset_per.InvestAssetID');
        $query->where(function ($q) use ($auth) {
            $q->where('invest_asset_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('invest_asset.AccessType', '=', 2);
            });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('invest_asset.InvestAssetID');

        if ($request->has('exportData')) {
            $query->select('invest_asset.InvestAssetID','invest_asset.InvestAssetNo','invest_asset.STT', 'invest_asset.InvestAssetName', 'invest_asset.UomName');
            return $query->orderBy('InvestAssetID', 'asc')->paginate($request->post('per_page'));
        }



        return $query->orderBy('InvestAssetID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'InvestAssetNo',
        'InvestAssetName',
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
