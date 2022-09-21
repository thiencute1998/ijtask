<?php
namespace Module\StateBudgetPlanning\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Sysperiod extends Model{
    protected $table = 'sys_period';

    public static function search(Request $request, $TransID){
        return 1;
    }
    protected $fillable = [
        'PeriodType',
        'PeriodName',
        'FromDate',
        'ToDate',
        'Norder',
        'Inactive',
    ];
    public $timestamps = false;

}

?>
