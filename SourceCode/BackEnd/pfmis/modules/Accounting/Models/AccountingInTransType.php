<?php
namespace Module\Accounting\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class AccountingInTransType extends Model{
    protected $table = 'act_intranstype';
    protected $primaryKey = 'InTransTypeID';

    public static function search(Request $request){
        $data = null;
        $query = AccountingInTransType::query();
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('Comment')){
            $query->where('Comment', 'like', '%'.$request->Comment.'%');
        }
        if($request->post('InTransTypeName')){
            $query->where('InTransTypeName', 'like', '%'.$request->InTransTypeName.'%');
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

        if ($request->has('exportData')) {
            $query->select('act_intranstype.InTransTypeID', 'act_intranstype.InTransTypeName', 'act_intranstype.Comment');
            return $query->orderBy('InTransTypeID')->get();
        }

        return $query->orderBy(DB::raw('InTransTypeID'))->paginate($per_page);
    }
    protected $fillable = [
        'InTransTypeName',
        'Comment',
        'Norder',
        'Locked',
        'Inactive',
    ];
    public $timestamps = false;

}

?>
