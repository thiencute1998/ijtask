<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Module\SysAdmin\Models\SysSetup;

class Direction extends Model{
    use IjFullTextSearch;
    protected $table = 'direction';
    protected $primaryKey = 'DirectionID';

    protected static $searchable = ['DirectionNo', 'DirectionName'];

    public static function search(Request $request){
        $data = null;
        $query = Direction::query();
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('DirectionName')){
            $query->where('DirectionName', 'like', '%'.$request->DirectionName.'%');
        }
        if($request->post('DirectionNo')){
            $query->whereRaw('concat(Prefix, DirectionNo, Suffix) like("%'.$request->DirectionNo.'%")');
        }
        if($request->post('DirectionDate') && $request->post('DirectionDate')['dateTime']){
            $query->whereDate('DirectionDate', date('Y-m-d',strtotime(str_replace('/','-',$request->post('DirectionDate')['dateTime']))));
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // Quyền xem chỉ thị
        $query->select('direction.*')->leftJoin('direction_per','direction_per.DirectionID','=','direction.DirectionID');
        $query->where(function ($q) use ($auth){
            $q->where('direction_per.EmployeeID',$auth->EmployeeID)
                ->orWhere('direction.AccessType',2);
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('direction.DirectionID');

        if ($request->has('exportData')) {
            $query->select('direction.DirectionID','direction.DirectionNo', 'direction.DirectionName','direction.STT','direction.DirectionDate');
            return $query->orderBy('DirectionID', 'asc')->paginate($request->post('per_page'));
        }
        return $query->orderBy('DirectionID', 'desc')->paginate($per_page);
    }

    public static function getDirection($request)
    {
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        if ($request->get('DirectionName')) {
            $directionName = $request->get('DirectionName');
            $query->where('DirectionName', 'like', '%' . $directionName . '%');
        }

        $query->orderBy('DirectionID', 'DESC');
        $direction = $query->paginate($perPage);
        return $direction;
    }

    public static function getAllChildDirection($listID){
        $result = [];
        $companies = Direction::whereIn('ParentID', $listID)->get();
        if (count($companies)) {
            $directionIDs = [];
            foreach ($companies as $direction) {
                $directionIDs[] = $direction->DirectionID;
                $result[] = $direction;
            }
            $records = Direction::getAllChildDirection($directionIDs);
            $result = array_merge($result, $records);
        }

        return $result;
    }

    protected $fillable = [
        'DirectionNo', 'DirectionName', 'ParentID', 'Level', 'Detail', 'Address', 'DirectionDate', 'CompanyIssuedID',
        'CompanyIssuedName', 'SignerIssuedID', 'SignerIssuedName', 'Description', 'Closed', 'ClosedDate', 'Locked', 'Note', 'Inactive',
        'Prefix', 'Suffix','NumberValue','AccessType','UserIDCreated','AuthorizedPerson'
    ];
    public $timestamps = false;


}

?>
