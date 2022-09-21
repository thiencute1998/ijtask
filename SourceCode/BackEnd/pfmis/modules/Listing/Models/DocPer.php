<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class DocPer extends Model{
    protected $table = 'doc_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'DocID',
        'EmployeeID',
        'EmployeeName',
        'Access',
        'AccessField',
        'Edit',
        'EditField',
        'Delete',
        'Create',
        'TypePer',
    ];
    public $timestamps = false;

}

?>
