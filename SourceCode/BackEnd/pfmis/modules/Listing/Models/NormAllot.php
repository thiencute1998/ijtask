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

class NormAllot extends Model{
    use IjFullTextSearch;
    protected $table = 'norm_allot';
    protected $primaryKey = 'NormAllotID';

    protected static $searchable = ['NormAllotNo', 'NormAllotName', 'UomName'];

    public static function search(Request $request){
        $query = NormAllot::query();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('NormAllotName')){
            $query->where('NormAllotName', 'like', '%'.$request->NormAllotName.'%');
        }
        if($request->post('NormAllotNo')){
            $query->whereRaw('NormAllotNo like("%'.$request->NormAllotNo.'%")');
        }
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if($request->post('EffectiveDate') && $request->post('EffectiveDate')['dateTime']){
            $query->whereDate('EffectiveDate',$request->post('EffectiveDate')['operator'] ,date('Y-m-d',strtotime(str_replace('/','-',$request->post('EffectiveDate')['dateTime']))));
        }
        if($request->post('ExpirationDate') && $request->post('ExpirationDate')['dateTime']){
            $query->whereDate('ExpirationDate', $request->post('ExpirationDate')['operator'],date('Y-m-d',strtotime(str_replace('/','-',$request->post('ExpirationDate')['dateTime']))));
        }
        if($request->post('UomID')){
            $query->where('UomID', '=', $request->post('UomID'));
        }
        if ($request->has('exportData')) {
            $query->select('norm_allot.NormAllotID','norm_allot.NormAllotNo', 'norm_allot.NormAllotName','norm_allot.STT');
            return $query->orderBy('NormAllotID', 'asc')->paginate($request->post('per_page'));
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        if($request->post('viewType')==='tree'){
            $query->where('Level', '=', 1)->groupBy('norm_allot.NormAllotID')->orderBy('norm_allot.NormAllotNo', 'asc');
            return $query->paginate($per_page);
        }
        $query->groupBy('norm_allot.NormAllotID');
        return $query->orderBy(DB::raw('NormAllotNo'))->paginate($per_page);
    }

    public static function getNormAllot($request)
    {
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        if ($request->get('NormAllotName')) {
            $normAllotName = $request->get('NormAllotName');
            $query->where('NormAllotName', 'like', '%' . $normAllotName . '%');
        }

        $query->orderBy('NormAllotID', 'DESC');
        $normAllot = $query->paginate($perPage);
        return $normAllot;
    }



    protected $fillable = [
        'NormAllotID',
        'NormAllotNo',
        'NormAllotName',
        'ParentID',
        'ParentNo',
        'Level',
        'Detail',
        'UomID',
        'UomNo',
        'UomName',
        'EffectiveDate',
        'ExpirationDate',
        'Inactive'
    ];
    public $timestamps = false;
}

?>
