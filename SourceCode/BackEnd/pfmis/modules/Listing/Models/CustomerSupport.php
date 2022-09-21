<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CustomerSupport extends Model{
    protected $table = 'customer_support';
    protected $primaryKey = 'LineID';

    public static function search(Request $request, $id){
        $data = null;
        CustomerSupport::select(DB::raw('customer_support.*, employee.EmployeeName'))
            ->leftJoin('employee', 'customer_support.EmployeeID', '=', 'employee.EmployeeID')
            ->where(['CustomerID' => $id])->get();
        $query = CustomerSupport::query();
        $query->select(DB::raw('customer_support.*, employee.EmployeeName'))
            ->leftJoin('employee', 'customer_support.EmployeeID', '=', 'employee.EmployeeID')
            ->where(['CustomerID' => $id]);
        if($request->post('TransContentSearch')){
            $query->where('TransContent', 'like', '%'.$request->TransContentSearch.'%');
        }
        if($request->post('EmployeeNameSearch')){
            $query->where('customer_support.EmployeeName', 'like', '%'.$request->EmployeeNameSearch.'%');
        }

        if($request->post('TransDateSearch')){
            $dateArr = explode('/', $request->TransDateSearch);
            if(isset($dateArr[2])) {
                $todate = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];
                $query->where(DB::raw('Date(customer_support.TransDate)'), str_replace('/', '-', $todate));
                $search = 1;
            }
        }


        if($request->post('TimeSearch')){
            $dau = substr($request->TimeSearch, 0, 2);
            $value = $request->TimeSearch;
            if($dau == '<=' || $dau == '>=' || $dau == '!='){
                $value = substr($request->TimeSearch, 2);
            }else{
                $dau = substr($request->TimeSearch, 0, 1);
                if($dau == '<' || $dau == '>' || $dau == '='){
                    $value = substr($request->TimeSearch, 1);
                }else{
                    $dau = '=';
                }
            }
            $query->where('Time', $dau, $value);
        }



        if($request->post('fromdate')){
            $dateArr = explode('/', $request->fromdate);
            if(isset($dateArr[2])){
                $fromdate = $dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0];
                $query->where('customer_support.TransDate', '>=', str_replace('/', '-', $fromdate) .' 00:00');
                $search = 1;
            }
        }
        if($request->post('todate')){
            $dateArr = explode('/', $request->todate);
            if(isset($dateArr[2])) {
                $todate = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];
                $query->where('customer_support.TransDate', '<=', str_replace('/', '-', $todate) . ' 23:59');
                $search = 1;
            }
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return ['data' => $query->orderBy('TransDate', 'DESC')->paginate($per_page), 'total' => $query->count()];
    }




    protected $fillable = [
        'CustomerID',
        'CustomerName',
        'TransDate',
        'TransContent',
        'Time',
        'EmployeeID',
        'EmployeeName',
        'ItemID',
        'ItemName',
        'ProjectID',
        'ProjectName',
        'ContractID',
        'ContractName',


    ];

    public $timestamps = false;

}

?>