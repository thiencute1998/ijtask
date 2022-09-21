<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class TaskCateValue extends Model{
    protected $table = 'task_cate_value';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = Company::query();
        $where = [];
        if($request->post('TaskCateName')){
            $query->where('TaskCateName', 'like', $request->TaskCateName.'%');
        }
        if($request->post('TaskCateNo')){
            $query->where('TaskCateNo', 'like', $request->TaskCateNo.'%');
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
        'CateID',
        'CateValue',
        'NOrder',
        'Description',
        'DataType'

    ];
    public $timestamps = false;


}

?>
