<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class PartnerLink extends Model{
    protected $table = 'partner_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $PartnerID){
        return 1;
    }
    protected $fillable = [
        'PartnerID',
        'LineID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
