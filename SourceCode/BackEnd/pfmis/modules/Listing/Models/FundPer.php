<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class FundPer extends Model{
    protected $table = 'fund_per';
    protected $primaryKey = 'LineID';
    protected $fillable = [
        'FundID',
        'FundName',
        'Access',
        'AccessField',
        'Addnew',
        'Edit',
        'EditField',
        'Delete',
        'EmployeeID',
        'EmployeeName',
        'Create',
    ];
    public $timestamps = false;
}
?>
