<?php
namespace Module\Customer\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomerPer extends Model{
    protected $table = 'customer_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'CustomerID',
        'EmployeeID',
        'EmployeeName',
        'Access',
        'AccessField',
        'Addnew',
        'Edit',
        'EditField',
        'Delete'
    ];
    public $timestamps = false;

    public static function getCustomerPer($CustomerID){
        $data = CustomerPer::query(DB::raw('SELECT CustomerID, EmployeeID, EmployeeName, MAX(Access) Access,
            GROUP_CONCAT(AccessField) AccessField, MAX(Edit) Edit, GROUP_CONCAT(EditField) EditField, MAX("Delete") "Delete", MAX("Create") "Create", FROM customer_per'))
            ->where('CustomerID', '=', $CustomerID)->groupBy('EmployeeID')->get();
        return $data;
    }
}

?>
