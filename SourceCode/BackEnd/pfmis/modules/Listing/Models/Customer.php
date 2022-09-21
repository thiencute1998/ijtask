<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Customer extends Model{
    use IjFullTextSearch;
    protected $table = 'customer';
    protected $primaryKey = 'CustomerID';

    protected static $searchable = ['CustomerNo', 'CustomerName', 'Fax', 'Email', 'Address'];


    public static function search(Request $request){
        $data = null;
        $query = Customer::query()->select('customer.*');
        $auth = Auth::user();
        if($request->post('CustomerName')){
            $query->where('CustomerName', 'like', '%' . $request->CustomerName . '%');
        }
        if($request->post('CustomerNo')){
            $query->whereRaw('CustomerNo like("%' . $request->CustomerNo . '%")');
        }
        if($request->post('Fax')){
            $query->where('Fax', 'like', '%' . $request->Fax . '%');
        }
        if($request->post('Email')){
            $query->where('Email', 'like', '%' . $request->Email . '%');
        }
        if($request->post('Address')){
            $query->where('Address', 'like', '%' . $request->Address . '%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $customerLinks = $request->post('CustomerLink');
        if ($customerLinks && (count($customerLinks) > 0)) {
            $query->join('customer_link', 'customer.CustomerID', '=', 'customer_link.CustomerID');
            foreach ($customerLinks as $customerLink) {
                if ($customerLink['LinkTable']) {
                    $query->where('customer_link.LinkTable', '=', $customerLink['LinkTable']);
                }
                if ($customerLink['LinkID']) {
                    $query->where('customer_link.LinkID', '=', $customerLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem tài liệu
        $query->select('customer.*')->leftJoin('customer_per', 'customer.CustomerID', '=', 'customer_per.CustomerID');
        $query->where(function ($q) use ($auth) {
            $q->where('customer_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('customer.AccessType', '=', 2);
            });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('customer.CustomerID');

        if ($request->has('exportData')) {
            $query->select('customer.CustomerID', 'customer.CustomerNo', 'customer.Address', 'customer.CustomerName', 'customer.OfficePhone', 'customer.Fax', 'customer.Email','customer.STT');
            return $query->orderBy('CustomerID', 'asc')->paginate($request->post('per_page'));
        }

        return $query->orderBy('CustomerID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'CustomerNo', 'CustomerName', 'Address', 'BillTo', 'ShipTo', 'TaxCode', 'BankAccount', 'BankName',
        'OfficePhone', 'Fax', 'Email','ProvinceID','ProvinceName','DistrictID','DistrictName','CommuneID','CommuneName',
        'Website','isVendor','Note','AccessType', 'UserIDCreated', 'AuthorizedPerson', 'Locked', 'Inactive'
    ];
    public $timestamps = false;


}

?>
