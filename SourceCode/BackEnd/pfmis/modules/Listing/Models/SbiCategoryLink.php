<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class SbiCategoryLink extends Model{
    protected $table = 'sbi_category_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $SbiCategoryID){
        return 1;
    }
    protected $fillable = [
        'SbiCategoryID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
