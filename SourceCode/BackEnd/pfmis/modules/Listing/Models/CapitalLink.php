<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CapitalLink extends Model{
    protected $table = 'capital_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $CapitalID){
        return 1;
    }
    protected $fillable = [
        'CapitalID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
