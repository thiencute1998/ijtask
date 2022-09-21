<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CoaTabLink extends Model{
    protected $table = 'coa_tab_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $AccountID){
        return 1;
    }
    protected $fillable = [
        'AccountID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
