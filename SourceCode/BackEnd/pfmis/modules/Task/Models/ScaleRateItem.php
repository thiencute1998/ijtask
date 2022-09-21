<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class ScaleRateItem extends Model{
    protected $table = 'task_scale_rate_item';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $where = [];

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('LineID')->paginate($per_page);
    }

    protected $fillable = [
        'LineID',
        'ScaleRateID',
        'LevelInt',
        'LevelChar',
        'FromPoint',
        'ToPoint',
        'FromPoint100',
        'ToPoint100',
        'LevelInt100',
        'LevelText'
    ];
    public $timestamps = false;


}

?>
