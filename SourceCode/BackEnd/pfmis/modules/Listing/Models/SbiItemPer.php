<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class SbiItemPer extends Model{
    protected $table = 'sbi_item_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'SbiItemID',
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
