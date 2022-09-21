<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class ContractCate extends Model{
    protected $table = 'contract_cate';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = ContractCate::query();
        $where = [];
        if($request->post('ContractCateName')){
            $query->where('ContractCateName', 'like', $request->ContractCateName.'%');
        }
        if($request->post('ContractCateNo')){
            $query->where('ContractCateNo', 'like', $request->ContractCateNo.'%');
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
        'ContractID',
        'CateID',
        'CateValue'
    ];
    public $timestamps = false;


}

?>