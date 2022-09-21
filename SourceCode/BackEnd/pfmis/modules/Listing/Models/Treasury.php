<?php
namespace Module\Listing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Module\SysAdmin\Models\SysSetup;


class Treasury extends Model{

    protected $table = 'treasury';
    protected $primaryKey = 'TreasuryID';

    protected static $searchable = ['TreasuryName'];

    public static function search(Request $request){
        $data = null;
        $query = Treasury::query();
        $where = [];
        if($request->post('TreasuryNo')){
            $query->where('TreasuryNo', 'like','%'.$request->TreasuryNo.'%');
        }
        if($request->post('TreasuryName')){
            $query->where('TreasuryName', 'like','%'.$request->TreasuryName.'%');
        }
        if($request->post('TreasuryAddress')){
            $query->where('TreasuryAddress', 'like','%'.$request->TreasuryAddress.'%');
        }

        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
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
        return $query->orderBy('TreasuryID', 'desc')->paginate($per_page);
    }


    protected $fillable = [
        'TreasuryNo', 'TreasuryName', 'TreasuryAddress', 'BudgetLevel',
        'ProvinceID', 'ProvinceName', 'DistrictID', 'DistrictName', 'Note',
        'Locked', 'Inactive'
    ];
    public $timestamps = false;


}

?>
