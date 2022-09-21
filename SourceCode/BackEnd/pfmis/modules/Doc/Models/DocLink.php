<?php
namespace Module\Doc\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class DocLink extends Model{
    protected $table = 'doc_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $TaskID){
        return 1;
    }
    protected $fillable = [
        'DocID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
    ];
    public $timestamps = false;

}

?>
