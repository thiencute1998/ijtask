<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class CustomerContractTransCateValue extends Model{
    protected $table = 'customer_contract_trans_cate_value';
    protected $primaryKey = 'ValueID';

    public static function search(Request $request){
        $data = null;
        $query = CustomerContractTransCateValue::query();
        $where = [];
        if($request->post('CateValue')){
            $query->where('CateValue', 'like', $request->CateValue.'%');
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('ValueID')->paginate($per_page);
    }

    protected $fillable = [
        'CateID',
        'CateValue',
        'DataType',
        'ConvertedValue',
        'NOrder'
    ];
    public $timestamps = false;


}

?>
