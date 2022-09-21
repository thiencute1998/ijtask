<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class DocVideo extends Model{
    protected $table = 'doc_video';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = DocVideo::query();
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->paginate($per_page);
    }

    protected $fillable = [
        'DocID',
        'VideoID',
        'VideoName',
        'Description',
        'VideoType',
        'VideoSize',
        'DateModified',
        'UserModified',
        'Link',
        'FileID',
        'DateModifiedRoot'
    ];
    public $timestamps = false;

}

?>