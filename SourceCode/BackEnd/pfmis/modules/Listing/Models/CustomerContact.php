<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model{
    protected $table = 'customer_contact';
    protected $primaryKey = 'LineID';


    protected $fillable = [
        'CustomerID',
        'CustomerName',
        'EmployeeID',
        'ContactName',
        'PositionName',
        'DepartmentName',
        'OfficePhone',
        'HandPhone',
        'Email',
        'FacebookID',
        'TiwtterID',
        'SkypeID',
        'ZaloID',

    ];
    public $timestamps = false;

}

?>