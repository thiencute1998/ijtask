<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class ExpenseLink extends Model{
    protected $table = 'expense_link';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $ExpenseID){
        return 1;
    }
    protected $fillable = [
        'ExpenseID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable',
        'LinkTableName'
    ];
    public $timestamps = false;

}

?>
