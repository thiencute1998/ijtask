<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class ContractItem extends Model{
    protected $table = 'contract_item';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $id){
        $data = null;
        $query = ContractItem::query();
        $query->select(DB::raw('contract_item.*'))
            ->where(['ContractID' => $id]);
        if($request->post('ItemNameSearch')){
            $query->where('ItemName', 'like', '%'.$request->ItemNameSearch.'%');
        }

        if($request->post('UomNameSearch')){
            $query->where('contract_item.UomName', 'like', '%'.$request->UomNameSearch.'%');
        }

        if($request->post('QuantitySearch')){
            $query->where('contract_item.Quantity', '=', $request->QuantitySearch);
        }

        if($request->post('UnitPriceSearch')){
            $query->where('contract_item.UnitPrice', '=', $request->UnitPriceSearch);
        }

        if($request->post('AmountSearch')){
            $query->where('contract_item.Amount', '=', $request->AmountSearch);
        }

        if($request->post('TaxRateSearch')){
            $query->where('contract_item.TaxRate', '=', $request->TaxRateSearch);
        }

        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('TaxAmountSearch')){
            $query->where('contract_item.TaxAmount', '=', $request->TaxAmountSearch);
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return ['data' => $query->orderBy('LineID', 'DESC')->paginate($per_page), 'total' => $query->count()];
    }

    protected $fillable = [
        'ContractID',
        'ContractName',
        'ItemID',
        'ItemName',
        'UomID',
        'UomName',
        'Quantity',
        'UnitPrice',
        'Amount',
        'TaxRate',
        'TaxAmount',

    ];
    public $timestamps = false;


}

?>