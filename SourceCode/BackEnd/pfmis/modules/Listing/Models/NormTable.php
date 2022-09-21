<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class NormTable extends Model{
    use IjFullTextSearch;
    protected $table = 'norm_table';
    protected $primaryKey = 'NormTableID';
    protected static $searchable = ['NormTableName'];

    public static function search(Request $request){
        $data = null;
        $query = NormTable::query();
        if($request->post('NormTableNo')){
            $query->where('NormTableNo', 'like', '%' . $request->NormTableNo . '%');
        }
        if($request->post('ExpenseName')){
            $query->where('ExpenseName', 'like', '%' . $request->ExpenseName . '%');
        }
        if($request->post('NormTableName')){
            $query->where('NormTableName', 'like', '%' . $request->NormTableName . '%');
        }

        // Ngày ban hành
        $effectiveDate = $request->post('EffectiveDate');
        if (isset($effectiveDate) && !empty($effectiveDate) && !empty($effectiveDate['dateTime'])) {
            $tmpDate = Date::StringToDate($effectiveDate['dateTime']);
            $query->where(DB::raw('DATE_FORMAT(norm_table.EffectiveDate, "%Y-%m-%d")'), $effectiveDate['operator'], $tmpDate);
        }

        // Ngày hiệu lực
        $normTableDate = $request->post('NormTableDate');
        if (isset($normTableDate) && !empty($normTableDate) && !empty($normTableDate['dateTime'])) {
            $tmpDate = Date::StringToDate($normTableDate['dateTime']);
            $query->where(DB::raw('DATE_FORMAT(norm_table.NormTableDate, "%Y-%m-%d")'), $normTableDate['operator'], $tmpDate);
        }

        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        $term = $request->post('term');
        if ($term) {
            $query->where(function($q) use ($term){
                $q->where('NormTableName', 'like', '%' . $term . '%')
                    ->orWhere('NormTableNo', 'like', '%' . $term . '%');
            });
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('NormTableID')->paginate($per_page);
    }

    protected $fillable = [
        'NormTableNo',
        'NormTableName',
        'NormType',
        'NormTableDate',
        'EffectiveDate',
        'ExpirationDate',
        'PeriodID',
        'PeriodName',
        'FromDate',
        'ToDate',
        'Inactive',
        'PeriodValue',
        'PeriodValueName'
    ];
    public $timestamps = false;


}

?>
