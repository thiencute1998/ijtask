<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class CustomerCateValue extends Model{
    protected $table = 'customer_cate_value';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = CustomerCateValue::query();
        $where = [];
        if($request->post('CateName')){
            $query->where('CateName', 'like', $request->CateName.'%');
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('LineID')->paginate($per_page);
    }

    protected $fillable = [
        'CateID',
        'CateName',
        'CateValue',
        'NOrder',
        'Description',
        'DataType',
        'CateNo'

    ];
    public $timestamps = false;


}

?>
