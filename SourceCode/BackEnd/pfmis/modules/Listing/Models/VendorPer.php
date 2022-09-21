<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class VendorPer extends Model{
    protected $table = 'vendor_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'VendorID',
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
