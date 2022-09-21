<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class FundCate extends Model{
    protected $table = 'fund_cate';
    protected $guarded = [];
    public $timestamps = false;
}
?>
