<?php
namespace Module\Listing\Models;

use Illuminate\Database\Eloquent\Model;

class ToolPer extends Model{
    protected $table = 'tool_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'ToolID',
        'EmployeeID',
        'EmployeeName',
        'Access',
        'AccessField',
        'Create',
        'Edit',
        'EditField',
        'Delete'
    ];
    public $timestamps = false;

}

?>
