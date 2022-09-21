<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class ProgramLink extends Model{
    protected $table = 'program_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $ProgramID){
        return 1;
    }
    protected $fillable = [
        'ProgramID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
