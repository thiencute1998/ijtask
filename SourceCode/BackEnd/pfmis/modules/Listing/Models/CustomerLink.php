<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CustomerLink extends Model{
    protected $table = 'customer_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $CustomerID){
        return 1;
    }
    protected $fillable = [
        'CustomerID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
