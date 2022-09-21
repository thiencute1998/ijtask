<?php
namespace Module\Listing\Models;

use Illuminate\Database\Eloquent\Model;

class InvestAssetPer extends Model{
    protected $table = 'invest_asset_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'InvestAssetID',
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
