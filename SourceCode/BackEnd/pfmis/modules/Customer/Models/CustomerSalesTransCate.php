<?php
namespace Module\Customer\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CustomerSalestransCate extends Model{
    protected $table = 'customer_sales_trans_cate';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $CustomerID){
        return 1;
    }
    protected $fillable = [
        'CustomerID',
        'TransID',
        'CateID',
        'CateValue',
    ];
    public $timestamps = false;

}

?>
