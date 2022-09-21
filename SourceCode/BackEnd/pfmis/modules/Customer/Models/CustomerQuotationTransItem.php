<?php
namespace Module\Customer\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CustomerQuotationTransItem extends Model{
    protected $table = 'customer_quotation_trans_item';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $CustomerID){
        return 1;
    }
    protected $fillable = [
        'TransID',
        'ItemID',
        'ItemNo',
        'Description',
        'UomID',
        'UomName',
        'Quantity',
        'FCUnitPrice',
        'LCUnitPrice',
        'FCAmount',
        'LCAmount',
        'TaxRate',
        'FCTaxAmount',
        'LCTaxAmount',
        'PromotionMethod',
        'PromotionPercent',
        'FCPromotionAmount',
        'FCPromotionAmount',
        'DiscountPercent',
        'FCDiscountAmount',
        'LCDiscountAmount',
    ];
    public $timestamps = false;

}

?>
