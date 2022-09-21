<?php

namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class NormAllotLevel extends Model
{
    use IjFullTextSearch;

    public $table = 'norm_allot_level';
    protected $primaryKey = 'NormAllotLevelID';
    public $timestamps = false;
    protected $fillable = [
        'NormAllotLevelNo','NormAllotLevelName','NormAllotLevelType', 'ParentID', 'ParentNo', 'ParentName','Level','Detail',
        'NormID','NormNo', 'NormName','NormAllotID','NormAllotNo','NormAllotName','EffectiveDate','ExpirationDate',
        'UomID', 'UomNo', 'UomName', 'FCUnitPrice', 'LCUnitPrice', 'Inactive'
    ];
    protected static $searchable = ['NormAllotLevelName', 'UomName'];

    public static function search($request){
        $query = NormAllotLevel::query();

        if($request->post('NormAllotLevelName')){
            $query->where('NormAllotLevelName','like','%'. $request->post('NormAllotLevelName'). '%');
        }
        if($request->post('NormAllotLevelNo')){
            $query->where('NormAllotLevelNo','like','%'. $request->post('NormAllotLevelNo'). '%');
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

        if (!empty($request->post('LCUnitPrice'))) {
            $LCUnitPrice = $request->post('LCUnitPrice');
            if (!empty($LCUnitPrice['number'])) {
                $query->where('LCUnitPrice', $LCUnitPrice['operator'], $LCUnitPrice['number']);
            }
        }

        if($request->post('Inactive')){
            $query->where('Inactive',$request->post('Inactive'));
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        if($request->post('viewType') === 'tree'){
            $query-> where('Level', '=', 1);
            $query-> groupBy('NormAllotLevelID')->orderBy('NormAllotLevelNo');
            return $query->paginate($per_page);
        }
        return $query->orderBy('NormAllotLevelNo')->paginate($per_page);
    }
}
