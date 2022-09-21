<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class RevenueLink extends Model{
    protected $table = 'revenue_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $RevenueID){
        return 1;
    }
    protected $fillable = [
        'RevenueID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
