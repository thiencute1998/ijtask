<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class ContractRevennue extends Model{
    protected $table = 'contract_revennue';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $id){
        $data = null;
        $query = ContractRevennue::query();
        $query->select(DB::raw('contract_revennue.*'))
            ->where(['ContractID' => $id]);
        if($request->post('InvoiceNoSearch')){
            $query->where('InvoiceNo', 'like', '%'.$request->InvoiceNoSearch.'%');
        }

        if($request->post('DescriptionSearch')){
            $query->where('Description', 'like', '%'.$request->DescriptionSearch.'%');
        }

        if($request->post('SaleAmountSearch')){
            $query->where('contract_revennue.SaleAmount', '=', $request->SaleAmountSearch);
        }

        if($request->post('TaxAmountSearch')){
            $query->where('contract_revennue.TaxAmount', '=', $request->TaxAmountSearch);
        }

        if($request->post('PromotionAmountSearch')){
            $query->where('contract_revennue.PromotionAmount', '=', $request->PromotionAmountSearch);
        }

        if($request->post('DiscountAmountSearch')){
            $query->where('contract_revennue.DiscountAmount', '=', $request->DiscountAmountSearch);
        }

        if($request->post('TaxRateSearch')){
            $query->where('contract_revennue.TaxRate', '=', $request->DescriptionSearch);
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        if($request->post('InvoiceDateSearch')){
            $dateArr = explode('/', $request->InvoiceDateSearch);
            if(isset($dateArr[2])) {
                $todate = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];
                $query->where(DB::raw('Date(contract_revennue.InvoiceDate)'), str_replace('/', '-', $todate));
                $search = 1;
            }
        }

        if($request->post('fromdate')){
            $dateArr = explode('/', $request->fromdate);
            if(isset($dateArr[2])){
                $fromdate = $dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0];
                $query->where('contract_revennue.InvoiceDate', '>=', str_replace('/', '-', $fromdate) .' 00:00');
                $search = 1;
            }
        }
        if($request->post('todate')){
            $dateArr = explode('/', $request->todate);
            if(isset($dateArr[2])) {
                $todate = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];
                $query->where('contract_revennue.InvoiceDate', '<=', str_replace('/', '-', $todate) . ' 23:59');
                $search = 1;
            }
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
        'InvoiceDate',
        'InvoiceNo',
        'SaleAmount',
        'TaxRate',
        'TaxAmount',
        'PromotionMethod',
        'PromotionPercent',
        'PromotionAmount',
        'DiscountPercent',
        'DiscountAmount',
        'Description',

    ];
    public $timestamps = false;


}

?>