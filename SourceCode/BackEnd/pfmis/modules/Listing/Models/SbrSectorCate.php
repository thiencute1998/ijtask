<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class SbrSectorCate extends Model{
    protected $table = 'sbr_sector_cate';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'SbrSectorID',
        'CateID',
        'CateValue'
    ];
    public $timestamps = false;
}

?>
