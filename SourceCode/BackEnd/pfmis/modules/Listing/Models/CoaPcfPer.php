<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class CoaPcfPer extends Model{
    protected $table = 'coa_pcf_per';
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
