<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Contract extends Model{
    use IjFullTextSearch;
    protected $table = 'contract';
    protected $primaryKey = 'ContractID';

    protected static $searchable = ['ContractNo', 'ContractName', 'ProjectNo'];


    public static function search(Request $request){
        $data = null;
        $query = Contract::query()->select('contract.*');
        $auth = Auth::user();
        if($request->post('ContractName')){
            $query->where('ContractName', 'like', '%' . $request->ContractName . '%');
        }
        if($request->post('ContractNo')){
            $query->whereRaw('ContractNo like("%' . $request->ContractNo . '%")');
        }
        if($request->post('ProjectNo')){
            $query->where('ProjectNo', 'like', '%' . $request->ProjectNo . '%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $contractLinks = $request->post('ContractLink');
        if ($contractLinks && (count($contractLinks) > 0)) {
            $query->join('contract_link', 'contract.ContractID', '=', 'contract_link.ContractID');
            foreach ($contractLinks as $contractLink) {
                if ($contractLink['LinkTable']) {
                    $query->where('contract_link.LinkTable', '=', $contractLink['LinkTable']);
                }
                if ($contractLink['LinkID']) {
                    $query->where('contract_link.LinkID', '=', $contractLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem tài liệu
        $query->leftJoin('contract_per', 'contract.ContractID', '=', 'contract_per.ContractID');
        $query->where(function ($q) use ($auth) {
            $q->where('contract_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('contract.AccessType', '=', 2);
            });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('contract.ContractID');

        if ($request->has('exportData')) {
            $query->select('contract.ContractID','contract.ContractNo','contract.STT', 'contract.ContractName', 'contract.ProjectNo');
            return $query->orderBy('ContractID', 'asc')->paginate($request->post('per_page'));
        }

        return $query->orderBy('ContractID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'ContractNo',
        'ContractName',
        'ContractDate',
        'EffectiveDate',
        'FinishDate',
        'ContractAmount',
        'EmployeeID',
        'EmployeeName',
        'ProjectID',
        'ProjectName',
        'ProjectNo',
        'ContractID',
        'ContractName',
        'Note',
        'AccessType',
        'UserIDCreated',
        'AuthorizedPerson',
        'Inactive'
    ];

    public $timestamps = false;


}

?>
