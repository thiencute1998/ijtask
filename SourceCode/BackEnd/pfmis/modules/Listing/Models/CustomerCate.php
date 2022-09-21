<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class CustomerCate extends Model{
    protected $table = 'customer_cate';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = CustomerCate::query();
        $where = [];
    }

    protected $fillable = [
        'CustomerID',
        'CateID',
        'CateValue'
    ];
    public $timestamps = false;


}

?>
