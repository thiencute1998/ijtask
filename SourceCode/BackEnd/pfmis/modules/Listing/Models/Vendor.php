<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Vendor extends Model{
    use IjFullTextSearch;
    protected $table = 'vendor';
    protected $primaryKey = 'VendorID';

    protected static $searchable = ['VendorNo', 'VendorName', 'Address'];


    public static function search(Request $request){
        $data = null;
        $query = Vendor::query()->select('vendor.*');
        $auth = Auth::user();
        if($request->post('VendorName')){
            $query->where('VendorName', 'like', '%' . $request->VendorName . '%');
        }
        if($request->post('VendorNo')){
            $query->whereRaw('VendorNo like("%' . $request->VendorNo . '%")');
        }
        if($request->post('Fax')){
            $query->where('Fax', 'like', '%' . $request->Fax . '%');
        }
        if($request->post('OfficePhone')){
            $query->where('OfficePhone', 'like', '%' . $request->OfficePhone . '%');
        }
        if($request->post('Email')){
            $query->where('Email', 'like', '%' . $request->Email . '%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $vendorLinks = $request->post('VendorLink');
        if ($vendorLinks && (count($vendorLinks) > 0)) {
            $query->join('vendor_link', 'vendor.VendorID', '=', 'vendor_link.VendorID');
            foreach ($vendorLinks as $vendorLink) {
                if ($vendorLink['LinkTable']) {
                    $query->where('vendor_link.LinkTable', '=', $vendorLink['LinkTable']);
                }
                if ($vendorLink['LinkID']) {
                    $query->where('vendor_link.LinkID', '=', $vendorLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem tài liệu
        $query->leftJoin('vendor_per', 'vendor.VendorID', '=', 'vendor_per.VendorID');
        $query->where(function ($q) use ($auth) {
            $q->where('vendor_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('vendor.AccessType', '=', 2);
            });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('vendor.VendorID');

        if ($request->has('exportData')) {
            $query->select('vendor.VendorID','vendor.VendorNo', 'vendor.VendorName', 'vendor.OfficePhone', 'vendor.Fax', 'vendor.Email','vendor.STT');
            return $query->orderBy('VendorID', 'asc')->paginate($request->post('per_page'));
        }

        return $query->orderBy('VendorID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'VendorNo',
        'VendorName',
        'Address',
        'BillTo',
        'ShipTo',
        'TaxCode',
        'BankAccount',
        'BankName',
        'ProvinceID',
        'ProvinceName',
        'DistrictID',
        'DistrictName',
        'CommuneID',
        'CommuneName',
        'OfficePhone',
        'Fax',
        'Email',
        'Website',
        'Note',
        'isCustomer',
        'AccessType',
        'UserIDCreated',
        'AuthorizedPerson',
        'Locked',
        'Inactive'
    ];
    public $timestamps = false;


}

?>
