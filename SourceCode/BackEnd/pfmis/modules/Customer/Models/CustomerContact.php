<?php
namespace Module\Customer\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CustomerContact extends Model{
    protected $table = 'customer_contact';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $CustomerID){
        return 1;
    }
    protected $fillable = [
        'CustomerID',
        'CustomerName',
        'ContactName',
        'PositionName',
        'DepartmentName',
        'OfficePhone',
        'HandPhone',
        'Email',
        'FacebookID',
        'TiwtterID',
        'SkypeID',
        'ZaloID',
    ];
    public $timestamps = false;

}

?>
