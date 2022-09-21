<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class DocComment extends Model{
    protected $table = 'doc_comment';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = DocComment::query();
        $where = [];
        if($request->post('DocName')){
            $query->where('DocName', 'like', $request->DocName.'%');
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->paginate($per_page);
    }

    protected $fillable = [
        'DocID',
        'DocName',
        'EmployeeID',
        'EmployeeName',
        'CommentDate',
        'CommentContent',
        'FileAttach',
        'IsImg',
        'FileAttachName',
        'ParentID'
    ];
    public $timestamps = false;

}

?>