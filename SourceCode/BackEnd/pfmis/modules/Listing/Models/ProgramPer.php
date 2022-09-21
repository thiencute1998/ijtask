<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ProgramPer extends Model{
    protected $table = 'program_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'ProgramID',
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
