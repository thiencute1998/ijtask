<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class TaskFile extends Model{
    protected $table = 'task_file';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = TaskFile::query();
        return $query->paginate(20);
    }

    protected $fillable = [
        'TaskID',
        'FileID',
        'FileName',
        'Description',
        'FileType',
        'FileSize',
        'DateModified',
        'UserModified',
        'Link',
        'DateModifiedRoot',
        'FileNameRoot',
        'DocID',
        'DocNo',
        'DocName'
    ];
    public $timestamps = false;

}

?>