<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class RevenueCate extends Model{
    protected $table = 'revenue_cate';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = RevenueCate::query();
        $where = [];
        if($request->post('CateName')){
            $query->where('CateName', 'like', $request->RevenueCateName . '%');
        }
        if($request->post('CateNo')){
            $query->where('CateNo', 'like', $request->RevenueCateNo . '%');
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

        'RevenueID',
        'CateID',
        'CateNo',
        'CateValue'
    ];
    public $timestamps = false;
}

?>
