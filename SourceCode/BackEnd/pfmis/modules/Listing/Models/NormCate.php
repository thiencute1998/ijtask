<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class NormCate extends Model{
    protected $table = 'norm_cate';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'NormID',
        'CateID',
        'CateValue'
    ];
    public $timestamps = false;
}

?>
