<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class ToolCate extends Model{
    protected $table = 'tool_cate';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = ToolCate::query();
        $where = [];
        if($request->post('CateName')){
            $query->where('CateName', 'like', $request->ToolCateName . '%');
        }
        if($request->post('CateNo')){
            $query->where('CateNo', 'like', $request->ToolCateNo . '%');
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
        'ToolID',
        'CateID',
        'CateValue'
    ];
    public $timestamps = false;
}

?>
