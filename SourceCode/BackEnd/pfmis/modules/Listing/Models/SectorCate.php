<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class SectorCate extends Model{
    protected $table = 'sector_cate';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'SectorID',
        'CateID',
        'CateValue'
    ];
    public $timestamps = false;
}

?>
