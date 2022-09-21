<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model{
    protected $table = 'task_comment';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = TaskComment::query();
        $where = [];
        if($request->get('TaskName')){
            $query->where('TaskName', 'like', $request->TaskName.'%');
        }
        return $query->paginate(SysSetup::getOption('NumberRowOnPage'));
    }

    protected $fillable = [
        'TaskID',
        'TaskName',
        'EmployeeID',
        'EmployeeName',
        'CommentDate',
        'LastCommentDate',
        'CommentContent',
        'FileAttach',
        'IsImg',
        'FileAttachName',
        'ParentID'
    ];
    public $timestamps = false;

}

?>