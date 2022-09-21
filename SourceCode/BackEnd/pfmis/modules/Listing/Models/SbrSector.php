<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class SbrSector extends Model{
    use IjFullTextSearch;
    protected $table = 'sbr_sector';
    protected $primaryKey = 'SbrSectorID';

    protected static $searchable = ['SbrSectorNo', 'SbrSectorName'];


    public static function search(Request $request){
        $data = null;
        $query = SbrSector::query()->select('sbr_sector.*');
        $auth = Auth::user();
        if($request->post('SbrSectorName')){
            $query->where('SbrSectorName', 'like', '%' . $request->SbrSectorName . '%');
        }
        if($request->post('SbrSectorNo')){
            $query->whereRaw('SbrSectorNo like("%' . $request->SbrSectorNo . '%")');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $sbrSectorLinks = $request->post('SbrSectorLink');
        if ($sbrSectorLinks && (count($sbrSectorLinks) > 0)) {
            $query->join('sbr_sector_link', 'sbr_sector.SbrSectorID', '=', 'sbr_sector_link.SbrSectorID');
            foreach ($sbrSectorLinks as $sbrSectorLink) {
                if ($sbrSectorLink['LinkTable']) {
                    $query->where('sbr_sector_link.LinkTable', '=', $sbrSectorLink['LinkTable']);
                }
                if ($sbrSectorLink['LinkID']) {
                    $query->where('sbr_sector_link.LinkID', '=', $sbrSectorLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem ngành
        $query->leftJoin('sbr_sector_per', 'sbr_sector.SbrSectorID', '=', 'sbr_sector_per.SbrSectorID');
        $query->where(function ($q) use ($auth) {
            $q->where('sbr_sector_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('sbr_sector.AccessType', '=', 2);
            });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('sbr_sector.SbrSectorID');

        if ($request->has('exportData')) {
            $query->select('sbr_sector.SbrSectorID','sbr_sector.SbrSectorNo','sbr_sector.STT', 'sbr_sector.SbrSectorName');
            return $query->orderBy('SbrSectorID', 'asc')->paginate($request->post('per_page'));
        }

        return $query->orderBy('SbrSectorID', 'asc')->paginate($per_page);
    }

    protected $fillable = [
        'SbrSectorNo',
        'SbrSectorName',
        'ParentID',
        'ParentNo',
        'ParentName',
        'Level',
        'Path',
        'Detail',
        'Note',
        'AccessType',
        'UserIDCreated',
        'AuthorizedPerson',
        'Locked',
        'Inactive',
        'SbiChapterID',
        'SbiChapterName',
        'SbiChapterNo'
    ];
    public $timestamps = false;


}

?>
