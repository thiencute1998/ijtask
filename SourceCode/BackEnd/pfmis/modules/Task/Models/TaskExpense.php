<?php
namespace Module\Task\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class TaskExpense extends Model{
    protected $table = 'task_expense';
    protected $primaryKey = 'ExpenseID';

    protected $fillable = [
        'TaskID',
        'LineID',
        'ExpenseNo',
        'Description',
        'UomID',
        'PlanQuantity',
        'PlanUnitPrice',
        'PlanAmount',
        'PlanTaxRate',
        'PlanTaxAmount',
        'ActualQuantity',
        'ActualUnitPrice',
        'ActualAmount',
        'ActualTaxRate',
        'ActualTaxAmount'

    ];
    public $timestamps = false;

}

?>