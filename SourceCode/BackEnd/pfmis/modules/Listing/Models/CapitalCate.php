<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class CapitalCate extends Model{
    protected $table = 'capital_cate';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = CapitalCate::query();
        $where = [];
        if($request->post('CateName')){
            $query->where('CateName', 'like', $request->CapitalCateName . '%');
        }
        if($request->post('CateNo')){
            $query->where('CateNo', 'like', $request->CapitalCateNo . '%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('nOrder')->paginate($per_page);
    }

    protected $fillable = [
        'CapitalID',
        'CateID',
        'CateNo',
        'CateValue'
    ];
    public $timestamps = false;
}

?>
