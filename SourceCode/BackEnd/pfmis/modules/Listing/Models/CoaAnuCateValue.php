<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class CoaAnuCateValue extends Model{
    protected $table = 'coa_anu_cate_value';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = CoaAnuCateValue::query();

        if($request->post('CateName')){
            $query->where('CateName', 'like', $request->CateName.'%');
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
    protected  $fillable = [
        'LineID',
        'CateID',
        'CateName',
        'CateValue',
        'DataType',
        'Description'
    ];
}
?>
