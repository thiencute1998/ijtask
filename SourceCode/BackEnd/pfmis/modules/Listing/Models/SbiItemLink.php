<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class SbiItemLink extends Model{
    protected $table = 'sbi_item_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $SbiItemID){
        return 1;
    }
    protected $fillable = [
        'SbiItemID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
