<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class FixedAssetCate extends Model{
    protected $table = 'fixed_asset_cate';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = FixedAssetCate::query();
        $where = [];
        if($request->post('CateName')){
            $query->where('CateName', 'like', $request->FixedAssetCateName . '%');
        }
        if($request->post('CateNo')){
            $query->where('CateNo', 'like', $request->FixedAssetCateNo . '%');
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
        'FixedAssetID',
        'CateID',
        'CateValue'
    ];
    public $timestamps = false;
}

?>
