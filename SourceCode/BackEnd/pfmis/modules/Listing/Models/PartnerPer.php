<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class PartnerPer extends Model{
    protected $table = 'partner_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'PartnerID',
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
