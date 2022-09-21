<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Partner extends Model{
    use IjFullTextSearch;
    protected $table = 'partner';
    protected $primaryKey = 'PartnerID';

    protected static $searchable = ['PartnerNo', 'PartnerName', 'Tel','Email'];


    public static function search(Request $request){
        $data = null;
        $query = Partner::query()->select('partner.*');
        $auth = Auth::user();
        if($request->post('PartnerName')){
            $query->where('PartnerName', 'like', '%' . $request->PartnerName . '%');
        }
        if($request->post('PartnerNo')){
            $query->whereRaw('PartnerNo like("%' . $request->PartnerNo . '%")');
        }
        if($request->post('Tel')){
            $query->where('Tel', 'like', '%' . $request->Tel . '%');
        }
        if($request->post('FullName ')){
            $query->where('FullName ', 'like', '%' . $request->FullName  . '%');
        }
        if($request->post('Email')){
            $query->where('Email', 'like', '%' . $request->Email . '%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $partnerLinks = $request->post('PartnerLink');
        if ($partnerLinks && (count($partnerLinks) > 0)) {
            $query->join('partner_link', 'partner.PartnerID', '=', 'partner_link.PartnerID');
            foreach ($partnerLinks as $partnerLink) {
                if ($partnerLink['LinkTable']) {
                    $query->where('partner_link.LinkTable', '=', $partnerLink['LinkTable']);
                }
                if ($partnerLink['LinkID']) {
                    $query->where('partner_link.LinkID', '=', $partnerLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem
        $query->leftJoin('partner_per', 'partner.PartnerID', '=', 'partner_per.PartnerID');
        $query->where(function ($q) use ($auth) {
            $q->where('partner_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('partner.AccessType', '=', 2);
            });
        });
        if ($request->has('exportData')) {
            $query->select('partner.PartnerID','partner.PartnerNo', 'partner.PartnerName', 'partner.FullName', 'partner.Tel', 'partner.Email', 'partner.STT');
            return $query->orderBy('PartnerID', 'asc')->paginate($request->post('per_page'));
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }

        $query->groupBy('partner.PartnerID');
        return $query->orderBy('PartnerID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'PartnerID',
        'PartnerNo',
        'PartnerName',
        'PartnerAddress',
        'FirstName',
        'MiddleName',
        'LastName',
        'FullName',
        'BirthDay',
        'PartnerIdNo',
        'PartnerIdIssuedDate',
        'ProvinceID',
        'ProvinceName',
        'DistrictID',
        'DistrictName',
        'CommuneID',
        'CommuneName',
        'Nationality',
        'NativeCountry',
        'PermanceAddress',
        'ResidenceAddress',
        'Tel',
        'Email',
        'Note',
        'Locked',
        'Inactive',
        'AuthorizedPerson',
        'UserIDCreated',
        'AccessType'
    ];
    public $timestamps = false;


}

?>
