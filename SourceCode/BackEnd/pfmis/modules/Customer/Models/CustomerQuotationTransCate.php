<?php
namespace Module\Customer\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CustomerQuotationtransCate extends Model{
    protected $table = 'customer_quotation_trans_cate';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $CustomerID){
        return 1;
    }
    protected $fillable = [
        'CustomerID',
        'TransID',
        'CateID',
        'CateName',
        'CateValue',
    ];
    public $timestamps = false;

}

?>
