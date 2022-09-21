<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class CustomerComment extends Model{
    protected $table = 'customer_comment';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = CustomerComment::query();
        $where = [];
        if($request->post('CustomerName')){
            $query->where('CustomerName', 'like', $request->CustomerName.'%');
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->paginate($per_page);
    }

    protected $fillable = [
        'CustomerID',
        'CustomerName',
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