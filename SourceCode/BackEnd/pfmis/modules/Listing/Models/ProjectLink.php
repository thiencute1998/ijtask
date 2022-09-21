<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class ProjectLink extends Model{
    protected $table = 'project_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $ProjectID){
        return 1;
    }
    protected $fillable = [
        'ProjectID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
