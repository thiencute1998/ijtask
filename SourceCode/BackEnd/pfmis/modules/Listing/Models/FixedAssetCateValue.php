<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class FixedAssetCateValue extends Model{
    protected $table = 'fixed_asset_cate_value';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = FixedAssetCateValue::query();
        $where = [];
        if($request->post('Name')){
            $query->where('Name', 'like', $request->Name.'%');
        }

        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->paginate($per_page);
    }

    protected $fillable = [
        'CateID',
        'Name',
        'DataType',
        'CateValue',
        'Description'
    ];
    public $timestamps = false;


}

?>
