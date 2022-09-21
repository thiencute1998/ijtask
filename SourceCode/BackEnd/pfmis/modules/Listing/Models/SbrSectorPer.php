<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class SbrSectorPer extends Model{
    protected $table = 'sbr_sector_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'SbrSectorID',
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
