<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class FileModel extends Model{
    protected $table = 'file';
    protected $primaryKey = 'FileID';

    public static function search(Request $request){
        $data = null;
        $query = FileModel::query();
        return $query->paginate(SysSetup::getOption('NumberRowOnPage'));
    }

    protected $fillable = [
        'FileName',
        'FileType',
        'FileSize',
        'Description',
        'DateModified',
        'UserModified',
        'Link',
        'NOrder',
        'Inactive',

    ];
    public $timestamps = false;


}

?>