<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class DirectionCate extends Model{
    protected $table = 'direction_cate';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'DirectionID',
        'CateID',
        'CateValue'
    ];
    public $timestamps = false;
}

?>
