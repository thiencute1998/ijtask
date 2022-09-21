<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class CompanyPer extends Model{
    protected $table = 'company_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'CompanyID',
        'EmployeeID',
        'EmployeeName',
        'Access',
        'AccessField',
        'Edit',
        'EditField',
        'Delete',
        'Create',
    ];
    public $timestamps = false;

}

?>
