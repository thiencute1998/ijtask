<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class TaskVideo extends Model{
    protected $table = 'task_video';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = TaskVideo::query();
        return $query->paginate(SysSetup::getOption('NumberRowOnPage'));
    }

    protected $fillable = [
        'TaskID',
        'VideoID',
        'VideoName',
        'Description',
        'VideoType',
        'VideoSize',
        'DateModified',
        'UserModified',
        'Link',
        'FileID',
        'DateModifiedRoot',
        'DocID',
        'DocNo',
        'DocName'
    ];
    public $timestamps = false;

}

?>