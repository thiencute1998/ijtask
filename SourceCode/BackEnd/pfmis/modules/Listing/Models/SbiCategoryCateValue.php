<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class SbiCategoryCateValue extends Model{
    protected $table = 'sbi_category_cate_value';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = Company::query();
        $where = [];
        if($request->post('CateName')){
            $query->where('CateName', 'like', $request->CateName.'%');
        }
        if($request->post('CateNo')){
            $query->where('CateNo', 'like', $request->CateNo.'%');
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
        'CateValue',
        'CateName',
        'NOrder',
        'Description',
        'DataType'
    ];
    public $timestamps = false;


}

?>
