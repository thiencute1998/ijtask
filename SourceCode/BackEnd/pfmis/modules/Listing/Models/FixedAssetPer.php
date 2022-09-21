<?php
namespace Module\Listing\Models;

use Illuminate\Database\Eloquent\Model;

class FixedAssetPer extends Model{
    protected $table = 'fixed_asset_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'FixedAssetID',
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
