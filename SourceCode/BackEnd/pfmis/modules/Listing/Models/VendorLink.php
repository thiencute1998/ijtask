<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class VendorLink extends Model{
    protected $table = 'vendor_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $VendorID){
        return 1;
    }
    protected $fillable = [
        'VendorID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
