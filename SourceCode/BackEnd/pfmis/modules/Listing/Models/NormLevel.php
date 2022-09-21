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

class NormLevel extends Model{
    use IjFullTextSearch;
    protected $table = 'norm_level';
    protected $primaryKey = 'NormLevelID';

    protected static $searchable = ['NormLevelNo', 'NormLevelName', 'EffectiveDate', 'ExpirationDate', 'UomID'];

    public static function search(Request $request){
        $data = null;
        $query = NormLevel::query();
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('NormLevelName')){
            $query->where('NormLevelName', 'like', '%'.$request->NormLevelName.'%');
        }
        if($request->post('NormLevelNo')){
            $query->whereRaw('NormLevelNo like("%'.$request->NormLevelNo.'%")');
        }

        if($request->post('UomID')){
            $query->where('UomID', '=', $request->UomID );
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }


        if ($request->has('exportData')) {
            $query->select('norm_level.NormLevelID','norm_level.NormLevelNo', 'norm_level.NormLevelName','norm_level.STT');
            return $query->orderBy('NormLevelID', 'asc')->paginate($request->post('per_page'));
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        if($request->post('viewType')==='tree'){
            $query->where('Level', '=', 1)->groupBy('norm_level.NormLevelID')->orderBy('norm_level.NormLevelNo', 'asc');
            return $query->paginate($per_page);
        }
        $query->groupBy('norm_level.NormLevelID');
        return $query->orderBy(DB::raw('NormLevelNo'))->paginate($per_page);
    }

    public static function getNormLevel($request)
    {
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        if ($request->get('NormLevelName')) {
            $normLevelName = $request->get('NormLevelName');
            $query->where('NormLevelName', 'like', '%' . $normLevelName . '%');
        }

        $query->orderBy('NormLevelID', 'DESC');
        $normLevel = $query->paginate($perPage);
        return $normLevel;
    }

    public static function getAllChildNormLevel($listID){
        $result = [];
        $normLevels = NormLevel::whereIn('ParentID', $listID)->get();
        if (count($normLevels)) {
            $normLevelIDs = [];
            foreach ($normLevels as $normLevel) {
                $normLevelIDs[] = $normLevel->NormLevelID;
                $result[] = $normLevel;
            }
            $records = NormLevel::getAllChildNormLevel($normLevelIDs);
            $result = array_merge($result, $records);
        }

        return $result;
    }

    protected $fillable = [
        'NormLevelID',
        'NormLevelNo',
        'NormLevelName',
        'ParentID',
        'ParentNo',
        'Level',
        'Detail',
        'EffectiveDate',
        'ExpirationDate',
        'UomID',
        'UomNo',
        'UomName',
        'Formula',
        'CcyID',
        'CcyNo',
        'CcyName',
        'ExchangeRate',
        'Quantity',
        'FCMinUnitPrice',
        'LCMinUnitPrice',
        'FCMaxUnitPrice',
        'LCMaxUnitPrice',
        'UnitPriceType',
        'FCUnitPrice',
        'LCUnitPrice',
        'Inactive',
    ];
    public $timestamps = false;


}

?>
