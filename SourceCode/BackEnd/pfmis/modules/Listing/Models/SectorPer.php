<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class SectorPer extends Model{
    protected $table = 'sector_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'SectorID',
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
