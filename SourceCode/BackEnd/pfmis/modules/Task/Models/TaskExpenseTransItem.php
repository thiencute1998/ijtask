<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class TaskExpenseTransItem extends Model{
    protected $table = 'task_expense_trans_item';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $TaskID){
        return 1;
    }
    protected $fillable = [
        'TransID',
        'TransDate',
        'TransType',
        'ExpenseID',
        'ExpenseNo',
        'Description',
        'UomID',
        'Quantity',
        'UnitPrice',
        'Amount',
        'TaxRate',
        'TaxAmount',



    ];
    public $timestamps = false;

}

?>