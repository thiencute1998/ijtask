<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Norm extends Model{
    use IjFullTextSearch;
    protected $table = 'norm';
    protected $primaryKey = 'NormID';

    protected $fillable = [
        'NormNo','NormName','NormType', 'RevenueID', 'RevenueNo', 'RevenueName','Level','Detail','ParentNo','ParentID',
        'ExpenseID','ExpenseNo','ExpenseName','UomID','UomNo','UomName','Comment', 'Inactive'
    ];
    public $timestamps = false;

    protected static $searchable = ['NormNo', 'NormName'];


    public static function search(Request $request){
        $data = null;
        $query = Norm::query()->select('norm.*');
        $auth = Auth::user();
        if($request->post('NormName')){
            $query->where('NormName', 'like', '%' . $request->NormName . '%');
        }
        if($request->post('NormNo')){
            $query->whereRaw('NormNo like("%' . $request->NormNo . '%")');
        }
        if($request->post('NormType')){
            $query->where('NormType',$request->NormType);
        }
        if($request->post('UomID')){
            $query->where('UomID',$request->UomID);
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
        $query->groupBy('norm.NormID');

        if ($request->has('exportData')) {
            $query->select('norm.NormID', 'norm.NormName', 'norm.OfficePhone', 'norm.Fax', 'norm.Email');
            return $query->orderBy('NormID', 'desc')->get();
        }
        if($request->post('viewType')==='tree'){
            $query->where('Level', '=', 1)->groupBy('norm.NormID')->orderBy('norm.NormNo', 'asc');
            return $query->paginate($per_page);
        }

        return $query->orderBy('NormNo','Asc')->paginate($per_page);
    }


}

?>
