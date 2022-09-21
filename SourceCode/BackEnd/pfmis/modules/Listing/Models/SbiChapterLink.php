<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class SbiChapterLink extends Model{
    protected $table = 'sbi_chapter_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $SbiChapterID){
        return 1;
    }
    protected $fillable = [
        'SbiChapterID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
