<?php
namespace Module\Listing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Module\SysAdmin\Models\SysSetup;


class Sponsor extends Model{

    protected $table = 'sponsor';
    protected $primaryKey = 'SponsorID';

    protected static $searchable = ['SponsorName'];

    public static function search(Request $request){
        $data = null;
        $query = Sponsor::query();
        $where = [];
        if($request->post('SponsorNo')){
            $query->where('SponsorNo', 'like','%'.$request->SponsorNo.'%');
        }
        if($request->post('SponsorName')){
            $query->where('SponsorName', 'like','%'.$request->SponsorName.'%');
        }
        if($request->post('SponsorAddress')){
            $query->where('SponsorAddress', 'like','%'.$request->SponsorAddress.'%');
        }

        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }
        if ($request->has('exportData')) {
            $query->select('sponsor.SponsorID','sponsor.SponsorNo', 'sponsor.SponsorName', 'sponsor.SponsorAddress','sponsor.STT');
            return $query->orderBy('SponsorID', 'asc')->paginate($request->post('per_page'));
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('SponsorID', 'desc')->paginate($per_page);
    }


    protected $fillable = [
        'SponsorNo', 'SponsorName', 'SponsorAddress', 'SponsorType',
        'ProvinceID', 'ProvinceName', 'Note', 'Locked', 'Inactive',
        'OfficePhone'
    ];
    public $timestamps = false;


}

?>
