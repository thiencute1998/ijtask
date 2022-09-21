<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class IndicatorCateValue extends Model{
    protected $table = 'task_indicator_cate_value';
    protected $primaryKey = 'CateID';

    public static function search(Request $request){
        $data = null;
        $where = [];
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
        'ConvertedValue',
        'Description',
        'DataType'

    ];
    public $timestamps = false;


}

?>
