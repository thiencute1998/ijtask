<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class CustomerPer extends Model{
    protected $table = 'customer_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'CustomerID',
        'EmployeeID',
        'EmployeeName',
        'Access',
        'AccessField',
        'Edit',
        'EditField',
        'Delete',
        'Create'
    ];
    public $timestamps = false;

}

?>
