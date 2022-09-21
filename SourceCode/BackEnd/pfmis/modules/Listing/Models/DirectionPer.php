<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class DirectionPer extends Model{
    protected $table = 'direction_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'DirectionID',
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
