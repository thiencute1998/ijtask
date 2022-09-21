<?php
namespace Module\Listing\Models;

use Illuminate\Database\Eloquent\Model;

class InvestAssetLink extends Model{
    protected $table = 'invest_asset_link';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'InvestAssetID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable'
    ];
    public $timestamps = false;

}

?>
