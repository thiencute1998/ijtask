<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Position extends Model{
    protected $table = 'position';
    protected $primaryKey = 'PositionID';

    public static function search(Request $request){
        $data = null;
        $query = Position::query();
        $where = [];
        if($request->post('PositionNo')){
            $query->where('PositionNo', 'like','%'. $request->PositionNo.'%');
        }
        if($request->post('PositionName')){
            $query->where('PositionName', 'like', '%'.$request->PositionName.'%');
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
        //return $query->paginate($per_page);
        return $query->orderBy(DB::raw('CONCAT(PositionNo, PositionID)'))->paginate($per_page);
    }

    protected $fillable = [
        'PositionNo',
        'PositionName',
        'Note',
        'Locked',
        'Inactive',
    ];
    public $timestamps = false;

}

?>
