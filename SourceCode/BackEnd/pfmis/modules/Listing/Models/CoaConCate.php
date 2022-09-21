<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class CoaConCate extends Model{
    protected $table = 'coa_con_cate';
    protected $guarded = [];
    public $timestamps = false;
}
?>
