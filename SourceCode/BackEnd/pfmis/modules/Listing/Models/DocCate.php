<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class DocCate extends Model{
    protected $table = 'doc_cate';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = DocCate::query();
        $where = [];
        if($request->post('DocCateName')){
            $query->where('DocCateName', 'like', $request->DocCateName.'%');
        }
        if($request->post('DocCateNo')){
            $query->where('DocCateNo', 'like', $request->DocCateNo.'%');
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('nOrder')->paginate($per_page);
    }

    protected $fillable = [
        'DocID',
        'CateID',
        'CateValue'
    ];
    public $timestamps = false;


}

?>