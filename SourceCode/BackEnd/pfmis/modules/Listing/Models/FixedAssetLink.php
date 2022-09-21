<?php
namespace Module\Listing\Models;

use Illuminate\Database\Eloquent\Model;

class FixedAssetLink extends Model{
    protected $table = 'fixed_asset_link';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'FixedAssetID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable'
    ];
    public $timestamps = false;

}

?>
