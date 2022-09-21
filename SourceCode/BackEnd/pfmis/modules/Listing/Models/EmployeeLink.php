<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class EmployeeLink extends Model{
    protected $table = 'employee_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $EmployeeID){
        return 1;
    }
    protected $fillable = [
        'EmployeeID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
