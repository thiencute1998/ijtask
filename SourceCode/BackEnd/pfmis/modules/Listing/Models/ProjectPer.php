<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ProjectPer extends Model{
    protected $table = 'project_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'ProjectID',
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
