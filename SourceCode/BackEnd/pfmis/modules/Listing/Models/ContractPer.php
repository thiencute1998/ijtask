<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ContractPer extends Model{
    protected $table = 'contract_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'ContractID',
        'EmployeeID',
        'EmployeeName',
        'Access',
        'AccessField',
        'Edit',
        'EditField',
        'TypePer'
    ];
    public $timestamps = false;

}

?>