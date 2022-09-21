<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Sector extends Model{
    use IjFullTextSearch;
    protected $table = 'sector';
    protected $primaryKey = 'SectorID';

    protected static $searchable = ['SectorNo', 'SectorName'];
    public static function search(Request $request){
        $data = null;
        $query = Sector::query()->select('sector.*');
        $auth = Auth::user();
        if($request->post('SectorName')){
            $query->where('SectorName', 'like', '%' . $request->SectorName . '%');
        }
        if($request->post('SectorNo')){
            $query->whereRaw('SectorNo like("%' . $request->SectorNo . '%")');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $sectorLinks = $request->post('SectorLink');
        if ($sectorLinks && (count($sectorLinks) > 0)) {
            $query->join('sector_link', 'sector.SectorID', '=', 'sector_link.SectorID');
            foreach ($sectorLinks as $sectorLink) {
                if ($sectorLink['LinkTable']) {
                    $query->where('sector_link.LinkTable', '=', $sectorLink['LinkTable']);
                }
                if ($sectorLink['LinkID']) {
                    $query->where('sector_link.LinkID', '=', $sectorLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem ngành
        $query->leftJoin('sector_per', 'sector.SectorID', '=', 'sector_per.SectorID');
        $query->where(function ($q) use ($auth) {
            $q->where('sector_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('sector.AccessType', '=', 2);
            });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('sector.SectorID');

        if ($request->has('exportData')) {
            $query->select('sector.SectorID','sector.SectorNo','sector.STT', 'sector.SectorName');
            return $query->orderBy('SectorID', 'asc')->paginate($request->post('per_page'));
        }

        return $query->orderBy('SectorID', 'asc')->paginate($per_page);
    }

    protected $fillable = [
        'SectorNo',
        'SectorName',
        'SbiCategoryID',
        'SbiCategoryNo',
        'SbiCategoryName',
        'Note',
        'AccessType',
        'UserIDCreated',
        'AuthorizedPerson',
        'Locked',
        'Inactive',
        'ParentID',
        'ParentNo',
        'ParentName',
        'Path',
        'Detail',
        'Level'
    ];
    public $timestamps = false;


}

?>
