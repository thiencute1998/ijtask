<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class CoaAnuPer extends Model{
    protected $table = 'coa_anu_per';
    protected $primaryKey = 'LineID';
    protected $fillable = [
        'AccountID',
        'AccountName',
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
