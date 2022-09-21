<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\SysAdmin\Models\SysSetup;

class NormTableItem extends Model{
    protected $table = 'norm_table_item';
    protected $primaryKey = 'NormTableItemID';

    public static function search(Request $request){
        $data = null;
        $query = self::query();

        if ($request->post('NormName')) {
            $query->where('NormName', 'like', '%' . $request->post('NormName') . '%');
        }

        if ($request->post('NormTableID')) {
            $query->where('NormTableID', '=', $request->post('NormTableID'));
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('NormTableItemID')->paginate($per_page);
    }

    public static function calculatorFormula($formula){
        $formulaPieces = preg_split('/(&|\*|%)/', $formula);
        $result = [
            'FCMinUnitPrice' => 0,
            'FCMaxUnitPrice' => 0,
            'LCMinUnitPrice' => 0,
            'LCMaxUnitPrice' => 0,
        ];
        if ($formulaPieces[0] === '@DMDT' && $formulaPieces[1] && $formulaPieces[2]) {
            $normTableItem = NormTableItem::where([
                'NormTableNo' => $formulaPieces[1],
                'NormTableItemNo' => $formulaPieces[2]
            ])->first();

            if ($normTableItem) {
                $result['FCMinUnitPrice'] = ($normTableItem->FCMinUnitPrice) ? $normTableItem->FCMinUnitPrice : 0;
                $result['FCMaxUnitPrice'] = ($normTableItem->FCMaxUnitPrice) ? $normTableItem->FCMaxUnitPrice : 0;
                $result['LCMinUnitPrice'] = ($normTableItem->LCMinUnitPrice) ? $normTableItem->LCMinUnitPrice : 0;
                $result['LCMaxUnitPrice'] = ($normTableItem->LCMaxUnitPrice) ? $normTableItem->LCMaxUnitPrice : 0;
            }
        }

        return $result;
    }

    protected $fillable = [
        'NormTableItemNo',
        'NormType',
        'NormTableItemName',
        'ParentID',
        'Level',
        'Detail',
        'NormTableID',
        'NormTableName',
        'NormTableNo',
        'NormID',
        'NormNo',
        'NormName',
        'UomID',
        'UomNo',
        'UomName',
        'Percentage',
        'CcyID',
        'CcyNo',
        'CcyName',
        'ExchangeRate',
        'Formula',
        'Quantity',
        'FCUnitPrice',
        'LCUnitPrice',
        'FCMinUnitPrice',
        'FCMaxUnitPrice',
        'LCMinUnitPrice',
        'LCMaxUnitPrice',
        'FCAmount',
        'LCAmount',
        'UnitPriceType',
        'FCBaseUnitPrice',
        'LCBaseUnitPrice',
        'SaveRate',
        'FCSaveAmount',
        'LCSaveAmount',
        'LineIDTmp',
        'NOrder'
    ];
    public $timestamps = false;


}

?>
