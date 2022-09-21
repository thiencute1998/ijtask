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

class Capital extends Model{
    use IjFullTextSearch;
    protected $table = 'capital';
    protected $primaryKey = 'CapitalID';

    protected static $searchable = ['CapitalNo', 'CapitalName' ];

    public static function search(Request $request){
        $data = null;
        $query = Capital::query();
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('CapitalName')){
            $query->where('CapitalName', 'like', '%'.$request->CapitalName.'%');
        }
        if($request->post('CapitalNo')){
            $query->whereRaw('CapitalNo like("%'.$request->CapitalNo.'%")');
        }

        //Danh mục liên kết
        $CapitalLink = $request->post('CapitalLink');
        if($CapitalLink && count($CapitalLink) > 0){
            $query->leftJoin('capital_link','capital.CapitalID','=','capital_link.CapitalID');
            foreach($CapitalLink as $capitalLink){
                if ($capitalLink['LinkTable']) {
                    $query->where('capital_link.LinkTable', '=', $capitalLink['LinkTable']);
                }
                if ($capitalLink['LinkID']) {
                    $query->where('capital_link.LinkID', '=', $capitalLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

//        // Quyền xem
//        $query->leftJoin('capital_per','capital_per.CapitalID','=','capital.CapitalID');
//        $query->where(function ($q) use ($auth){
//            $q->where('capital_per.EmployeeID',$auth->EmployeeID)
//                ->orWhere('capital.AccessType',2);
//        });
        if ($request->has('exportData')) {
            $query->select('capital.CapitalID','capital.CapitalNo', 'capital.CapitalName','capital.STT');
            return $query->orderBy('CapitalID', 'asc')->paginate($request->post('per_page'));
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        if($request->post('viewType')==='tree'){
            $query->where('Level', '=', 1)->groupBy('capital.CapitalID')->orderBy('capital.CapitalNo', 'asc');
            return $query->paginate($per_page);
        }
        $query->groupBy('capital.CapitalID');
        return $query->orderBy(DB::raw('CapitalNo'))->paginate($per_page);
    }

    public static function getCapital($request)
    {
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        if ($request->get('CapitalName')) {
            $capitalName = $request->get('CapitalName');
            $query->where('CapitalName', 'like', '%' . $capitalName . '%');
        }

        $query->orderBy('CapitalID', 'DESC');
        $capital = $query->paginate($perPage);
        return $capital;
    }

    public static function getAllChildCapital($listID){
        $result = [];
        $capitals = Capital::whereIn('ParentID', $listID)->get();
        if (count($capitals)) {
            $capitalIDs = [];
            foreach ($capitals as $capital) {
                $capitalIDs[] = $capital->CapitalID;
                $result[] = $capital;
            }
            $records = Capital::getAllChildCapital($capitalIDs);
            $result = array_merge($result, $records);
        }

        return $result;
    }

    protected $fillable = [
        'CapitalID',
        'CapitalNo',
        'CapitalName',
        'ParentID',
        'ParentNo',
        'ParentName',
        'Level',
        'NumberValue',
        'Note',
        'Detail',
        'Loked',
        'CapitalInOut',
        'BudgetStateType',
        'Path',
        'SectorID',
        'SectorNo',
        'SectorName',
    ];
    public $timestamps = false;


}

?>
