<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class Center extends Model{
    use IjFullTextSearch;
    protected $table = 'center';
    protected $primaryKey = 'CenterID';
    protected static $searchable = ['CenterName'];

    public static function search(Request $request){
        $data = null;
        $query = Center::query();
        $where = [];
        if($request->post('CenterName')){
            $query->where('CenterName', 'like','%'.$request->CenterName.'%');
        }
        if($request->post('CenterNo')){
            $query->where('CenterNo', 'like','%'.$request->CenterNo.'%');
        }
        if($request->post('CenterTel')){
            $query->where('CenterTel', 'like','%'.$request->CenterTel.'%');
        }
        if($request->post('CenterFax')){
            $query->where('CenterFax', 'like','%'.$request->CenterFax.'%');
        }
        if($request->post('CenterEmail')){
            $query->where('CenterEmail', 'like','%'.$request->CenterEmail.'%');
        }
        if($request->post('Note')){
            $query->where('Note', 'like','%'.$request->Note.'%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if ($request->has('exportData')) {
            $query->select('center.CenterID','center.CenterNo', 'center.CenterName', 'center.CenterTel','center.CenterFax', 'center.CenterEmail','center.STT');
            return $query->orderBy('CenterID', 'asc')->paginate($request->post('per_page'));
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('CenterID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'CenterNo',
        'CenterName',
        'CenterAddress',
        'CenterTel',
        'CenterHandPhone',
        'CenterFax',
        'CenterEmail',
        'CenterWebsite',
        'ContactName',
        'ContactTitle',
        'ContactTel',
        'ContactHandPhone',
        'ContactEmail',
        'Note',
        'Locked',
        'Inactive',
        'CenterType',
    ];
    public $timestamps = false;


}

?>
