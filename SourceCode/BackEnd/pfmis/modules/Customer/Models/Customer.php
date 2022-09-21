<?php
namespace Module\Customer\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\Listing\Models\Employee;
use Module\SysAdmin\Models\SysSetup;

class Customer extends Model{
    use IjFullTextSearch;
    protected $table = 'customer';
    protected $primaryKey = 'CustomerID';
    protected static $searchable = ['customer.CustomerNo', 'customer.CustomerName', 'customer.ProvinceName', 'customer.DistrictName', 'customer.CommuneName'];

    public static function search(Request $request){
        $data = null;
        $query = Customer::query();
        $filter = $request->get('filter');
        $auth = Auth::user();
        $query->select('customer.*');

        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('CustomerName')){
            $query->where('customer.CustomerName', 'like', '%'.$request->CustomerName.'%');
        }
        if($request->post('CustomerNo')){
            $query->whereRaw('concat(customer.CustomerNo) like("%'.$request->CustomerNo.'%")');
        }

        if($request->post('OfficePhone')){
            $query->whereRaw('concat(customer.OfficePhone) like("%'.$request->OfficePhone.'%")');
        }
        if($request->post('Fax')){
            $query->whereRaw('concat(customer.Fax) like("%'.$request->Fax.'%")');
        }
        if($request->post('Email')){
            $query->whereRaw('concat(customer.Email) like("%'.$request->Email.'%")');
        }
        $term = $request->post('term');
        if ($term) {
            $query->where('customer.CustomerName', 'like', '%' . $term . '%');
            $query->orWhere('customer.CustomerNo', 'like', '%' . $term . '%');
        }
        if(isset($filter['ProvinceID']) && $filter['ProvinceID']){
            $query->where('customer.ProvinceID', '=', $filter['ProvinceID']);
        }
        if(isset($filter['DistrictID']) && $filter['DistrictID']){
            $query->where('customer.DistrictID', '=', $filter['DistrictID']);
        }
        if(isset($filter['CommuneID']) && $filter['CommuneID']){
            $query->where('customer.CommuneID', '=', $filter['CommuneID']);
        }

        // loại khách hàng
        if (isset($filter['CustomerCateList']) && count($filter['CustomerCateList'])) {
            $sqlRaw = '(SELECT C.* FROM (SELECT customer_cate.* FROM customer_cate WHERE ';
            foreach ($filter['CustomerCateList'] as $key => $customerCateList) {
                if ($customerCateList['CateID'] || $customerCateList['CateValue']) {
                    $sqlRaw .= '(';
                    if ($customerCateList['CateID']) {
                        $sqlRaw .= 'CateID = ' . $customerCateList['CateID'];
                    }
                    if ($customerCateList['CateValue']) {
                        $sqlRaw .= ' AND ';
                        $sqlRaw .= 'CateValue = ' . $customerCateList['CateValue'];
                    }
                    $sqlRaw .= ')';
                }
                if ((count($filter['CustomerCateList']) - 1) > $key) {
                    $sqlRaw .= ' OR ';
                }
            }
            $sqlRaw .= ' GROUP BY CustomerID, CateID, CateValue ) AS C GROUP BY C.CustomerID HAVING COUNT(*) = ' . count($filter['CustomerCateList']);
            $sqlRaw .= ') AS CC';
            $query->join(DB::raw($sqlRaw), 'customer.CustomerID', '=', 'CC.CustomerID');
        }

        // danh mục liên kết
        if (isset($filter['CustomerLink']) && (count($filter['CustomerLink']))) {
            $query->join('customer_link', 'customer.CustomerID', '=', 'customer_link.CustomerID');
            foreach ($filter['CustomerLink'] as $customerLink) {
                if ($customerLink['LinkTable']) {
                    $query->where('customer_link.LinkTable', '=', $customerLink['LinkTable']);
                }
                if ($customerLink['LinkID']) {
                    $query->where('customer_link.LinkID', '=', $customerLink['LinkID']);
                }
            }
        }

        // Tài liệu
        if(isset($filter['DocID']) && $filter['DocID']){
            $query->leftJoin(DB::raw('customer_file CF'), 'customer.CustomerID', '=', 'CF.CustomerID');
            $query->leftJoin(DB::raw('customer_video CV'), 'customer.CustomerID', '=', 'CV.CustomerID');
            $query->where(function($q) use ($filter) {
                $q->where('CF.DocID', '=', $filter['DocID']);
                $q->orWhere('CV.DocID', '=', $filter['DocID']);
            });
        }

        // TODO: Quyền được xem khách hàng
//        if ($auth->UserType !== 1) {
//            $query->join('customer_per', 'customer.CustomerID', '=', 'customer_per.CustomerID');
//            $query->where(function ($q) use ($auth) {
//                $q->where('customer_per.EmployeeID', '=', $auth->EmployeeID);
//                $q->orWhere('customer.AuthorizedPerson', '=', $auth->UserID);
//            });
//        }

        // DateRange
//        if(isset($filter['fromDate']) && $filter['fromDate'] !== ''){
//            $fromDate = Date::StringToDate($filter['fromDate']);
//            $query->where(DB::raw('DATE_FORMAT(customer.CreateDate, "%Y-%m-%d")'), '>=', $fromDate);
//        }
//        if(isset($filter['toDate']) && $filter['toDate'] !== ''){
//            $toDate = Date::StringToDate($filter['toDate']);
//            $query->where(DB::raw('DATE_FORMAT(task.CreateDate, "%Y-%m-%d")'), '<=', $toDate);
//        }


        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->orderBy('customer.CustomerID', 'desc');

        if ($request->has('exportExcel')) {
            return $query->get();
        }

        return $query->paginate($per_page);
    }
    public static function search1(Request $request){
        $data = null;
        $select = DB::raw("customer.*");
        $leftJoinCB = DB::raw('customer_business_trans CB');
        $leftJoinCS = DB::raw('customer_support_trans CS');
        $leftJoinT = DB::raw('customer_cate T');
        $query = Customer::query();
        $where = [];

        if($request->CustomerCate){
            $arrCustomerCate = explode('_', $request->CustomerCate);
//            $query->leftJoin($leftJoinT, 'customer.CustomerID', '=', 'T.CustomerID');
            $arrCateStrWhere = null;
            for ($i = 0; $i < sizeof($arrCustomerCate); $i++) {
                if ($arrCustomerCate[$i]) {
                    $arrCateValue = explode(':', $arrCustomerCate[$i]);

                    if (isset($arrCateValue[0])) {
                        if (isset($arrCateStrWhere[$arrCateValue[0]])) {
                            $arrCateStrWhere[$arrCateValue[0]] .= " OR ((T".$arrCateValue[0].".CustomerCateID = " . $arrCateValue[0] . ")";
                        } else {
                            $arrCateStrWhere[$arrCateValue[0]] = "(((T".$arrCateValue[0].".CustomerCateID = " . $arrCateValue[0] . ")";
                        }
                        if (isset($arrCateValue[1]) && $arrCateValue[1]) {
                            $arrCateStrWhere[$arrCateValue[0]] .= " AND (T".$arrCateValue[0].".CateValue = " . $arrCateValue[1] . ")";
                        }
                        $arrCateStrWhere[$arrCateValue[0]] .= ")";
                    }
                }
            }
//            if($arrCateStrWhere){
//                $CateStrWhere = implode(") AND ", $arrCateStrWhere);
//                $CateStrWhere .=")";
//                $query->whereRaw($CateStrWhere);
//            }
            foreach ($arrCateStrWhere as $key=>$value){
                $query->join(DB::raw('customer_cate T'.$key), 'customer.CustomerID', '=', 'T'.$key.'.CustomerID');
                $query->whereRaw($arrCateStrWhere[$key].')');
            }
        }
        if($request->post('CustomerCateID') && $request->post('CateValue')){
            $query->leftJoin($leftJoinT, 'customer.CustomerID', '=', 'T.CustomerID');
            $query->where('T.CustomerCateID', '=', $request->CustomerCateID);
            $query->where('T.CateValue', '=', $request->CateValue);
            $search = 1;
        }elseif ($request->post('CustomerCateID')){
            $query->leftJoin($leftJoinT, 'customer.CustomerID', '=', 'T.CustomerID');
            $query->where('T.CustomerCateID', '=', $request->CustomerCateID);
        }
        if($request->post('DocID')){
            $query->leftJoin(DB::raw('customer_file TF'), 'customer.CustomerID', '=', 'TF.CustomerID');
            $query->leftJoin(DB::raw('customer_video TV'), 'customer.CustomerID', '=', 'TV.CustomerID');
            $query->where(function($q) use ($request) {
                $q->where('TF.DocID', '=', $request->post('DocID'));
                $q->orWhere('TV.DocID', '=', $request->post('DocID'));
            });
            $search = 1;
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('CustomerName')){
            $query->where('customer.CustomerName', 'like', '%' . $request->CustomerName . '%');
        }
        if($request->post('CustomerNo')){
            $query->whereRaw('concat(customer.Prefix, customer.CustomerNo, customer.Suffix) like("%' . $request->CustomerNo . '%")');
        }
        if($request->post('ProvinceID')){
            $query->where('customer.ProvinceID', '=', $request->ProvinceID);
        }
        if($request->post('DistrictID')){
            $query->where('customer.DistrictID', '=', $request->DistrictID);
        }
        if($request->post('CommuneID')){
            $query->where('customer.CommuneID', '=', $request->CommuneID);
        }
        if($request->post('Fax')){
            $query->where('customer.Fax', 'like', $request->Fax.'%');
        }
        if($request->post('OfficePhone')){
            $query->where('customer.OfficePhone', 'like', $request->OfficePhone.'%');
        }
        if($request->post('Email')){
            $query->where('customer.Email', 'like', $request->Email.'%');
        }
        $leftJoin = 0;
        if($request->post('EmployeeID')){
            if($request->post('TransType') == 1){
                $query->where('CB.EmployeeID', '=', $request->EmployeeID);
            }elseif ($request->post('TransType') == 2){
                $query->where('CS.EmployeeID', '=', $request->EmployeeID);
            }else{
                $query->where(function($q) use ($request) {
                    $q->where('CB.EmployeeID', '=', $request->EmployeeID);
                    $q->orWhere('CS.EmployeeID', '=', $request->EmployeeID);
                });
            }
            $leftJoin = 1;
            $search = 1;
        }
        if($request->post('ProjectID')){

            if($request->post('TransType') == 1){
                $query->where('CB.ProjectID', '=', $request->ProjectID);
            }elseif ($request->post('TransType') == 2){
                $query->where('CS.ProjectID', '=', $request->ProjectID);
            }else{
                $query->where(function($q) use ($request) {
                    $q->where('CB.ProjectID', '=', $request->ProjectID);
                    $q->orWhere('CS.ProjectID', '=', $request->ProjectID);
                });
            }

            $leftJoin = 1;
            $search = 1;
        }
        if($request->post('ContractID')){

            if($request->post('TransType') == 1){
                $query->where('CB.ContractID', '=', $request->ContractID);
            }elseif ($request->post('TransType') == 2){
                $query->where('CS.ContractID', '=', $request->ContractID);
            }else{
                $query->where(function($q) use ($request) {
                    $q->where('CB.ContractID', '=', $request->ContractID);
                    $q->orWhere('CS.ContractID', '=', $request->ContractID);
                });
            }

            $leftJoin = 1;
            $search = 1;
        }

        if($request->post('CompanyID')){
            $company = Company::find($request->post('CompanyID'));
            $CompanyLevel = 0;
            if($company){
                $CompanyLevel = $company->Level;
            }
            switch ($CompanyLevel){
                case 1:
                    $query->where(function($q) use ($request) {
                        $q->where('customer.CompanyID', '=', $request->CompanyID);
                        $q->orWhere('customer.ParentCompanyID1', '=', $request->CompanyID);
                    });
                    break;
                case 2:
                    $query->where(function($q) use ($request) {
                        $q->where('customer.CompanyID', '=', $request->CompanyID);
                        $q->orWhere('customer.ParentCompanyID2', '=', $request->CompanyID);
                    });
                    break;
                case 3:
                    $query->where(function($q) use ($request) {
                        $q->where('customer.CompanyID', '=', $request->CompanyID);
                        $q->orWhere('customer.ParentCompanyID3', '=', $request->CompanyID);
                    });
                    break;
                default:
                    $query->where('customer.CompanyID', '=', $request->CompanyID);
                    break;
            }
            $search = 1;
        }
        if($request->post('ItemID')){

            if($request->post('TransType') == 1){
                $query->where('CB.ItemID', '=', $request->ItemID);
            }elseif ($request->post('TransType') == 2){
                $query->where('CS.ItemID', '=', $request->ItemID);
            }else{
                $query->where(function($q) use ($request) {
                    $q->where('CB.ItemID', '=', $request->ItemID);
                    $q->orWhere('CS.ItemID', '=', $request->ItemID);
                });
            }
            $leftJoin = 1;
            $search = 1;
        }
        if($request->post('fromdate')){
            $dateArr = explode('/', $request->fromdate);
            if(isset($dateArr[2])){
                $fromdate = $dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0];


                if($request->post('TransType') == 1){
                    $query->where('CB.TransDate', '>=', str_replace('/', '-', $fromdate) .' 00:00');
                }elseif ($request->post('TransType') == 2){
                    $query->where('CS.TransDate', '>=', str_replace('/', '-', $fromdate) .' 00:00');
                }else{
                    $query->where(function($q) use ($fromdate) {
                        $q->where('CB.TransDate', '>=', str_replace('/', '-', $fromdate) .' 00:00');
                        $q->orWhere('CS.TransDate', '>=', str_replace('/', '-', $fromdate) .' 00:00');
                    });
                }

                $search = 1;
                $leftJoin = 1;
            }
        }
        if($request->post('todate')){
            $dateArr = explode('/', $request->todate);
            if(isset($dateArr[2])) {
                $todate = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];

                if($request->post('TransType') == 1){
                    $query->where('CB.TransDate', '<=', str_replace('/', '-', $todate) .' 23:59');
                }elseif ($request->post('TransType') == 2){
                    $query->where('CS.TransDate', '<=', str_replace('/', '-', $todate) .' 23:59');
                }else{
                    $query->where(function($q) use ($todate) {
                        $q->where('CB.TransDate', '<=', str_replace('/', '-', $todate) .' 23:59');
                        $q->orWhere('CS.TransDate', '<=', str_replace('/', '-', $todate) .' 23:59');
                    });
                }
                $search = 1;
                $leftJoin = 1;
            }
        }
        if($leftJoin){
            if($request->post('TransType') == 1){
                $query->leftJoin($leftJoinCB, 'customer.CustomerID', '=', 'CB.CustomerID');
            }elseif ($request->post('TransType') == 2){
                $query->leftJoin($leftJoinCS, 'customer.CustomerID', '=', 'CS.CustomerID');
            }else{
                $query->leftJoin($leftJoinCB, 'customer.CustomerID', '=', 'CB.CustomerID');
                $query->leftJoin($leftJoinCS, 'customer.CustomerID', '=', 'CS.CustomerID');
            }
        }
        $query->select($select);
        $query->groupBy('customer.CustomerID');
        $data = $query->get();
        return ['data' => $query->orderBy('CustomerNo')->paginate(SysSetup::getOption('NumberRowOnPage')), 'total' => $data->count()];
    }
    public static function getCustomer($request){
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        if ($request->post('CustomerName')) {
            $CustomerName = $request->post('CustomerName');
            $query->where('CustomerName', 'like', '%' . $CustomerName . '%');
        }
        if ($request->post('CustomerNo')) {
            $CustomerNo = $request->post('CustomerNo');
            $query->where('CustomerNo', '=', $CustomerNo);
        }

        $contract = $query->paginate($perPage);
        return $contract;
    }
    public static function searchExcel(Request $request){
        $data = null;
        $select = DB::raw("customer.CustomerName, customer.Address, customer.BillTo, customer.ShipTo, customer.TaxCate
        , customer.BankAccount, customer.BankName, customer.OfficePhone, customer.Fax, customer.Email, customer.Website, customer.ProvinceName, customer.Note
        , CT.ContactName, CT.PositionName, CT.OfficePhone, CT.HandPhone, CT.Email");
        $leftJoinCB = DB::raw('customer_business CB');
        $leftJoinCS = DB::raw('customer_support CS');
        $leftJoinT = DB::raw('customer_cate T');
        $query = Customer::query();
        $where = [];
        $query->leftJoin(DB::raw('customer_contact CT'), 'customer.CustomerID', '=', 'CT.CustomerID');
        if($request->post('CustomerCate')){
            $arrCustomerCate = explode('_', $request->post('CustomerCate'));
            $query->leftJoin($leftJoinT, 'customer.CustomerID', '=', 'T.CustomerID');
            $arrCateStrWhere = null;
            for ($i = 0; $i < sizeof($arrCustomerCate); $i++) {
                if ($arrCustomerCate[$i]) {
                    $arrCateValue = explode(':', $arrCustomerCate[$i]);

                    if (isset($arrCateValue[0])) {
                        if (isset($arrCateStrWhere[$arrCateValue[0]])) {
                            $arrCateStrWhere[$arrCateValue[0]] .= " OR ((T.CustomerCateID = " . $arrCateValue[0] . ")";
                        } else {
                            $arrCateStrWhere[$arrCateValue[0]] = "(((T.CustomerCateID = " . $arrCateValue[0] . ")";
                        }
                        if (isset($arrCateValue[1]) && $arrCateValue[1]) {
                            $arrCateStrWhere[$arrCateValue[0]] .= " AND (T.CateValue = " . $arrCateValue[1] . ")";
                        }
                        $arrCateStrWhere[$arrCateValue[0]] .= ")";
                    }
                }
            }
            if($arrCateStrWhere){
                $CateStrWhere = implode(") AND ", $arrCateStrWhere);
                $CateStrWhere .=")";
                $query->whereRaw($CateStrWhere);
            }
        }
        if($request->post('CustomerCateID') && $request->post('CateValue')){
            $query->leftJoin($leftJoinT, 'customer.CustomerID', '=', 'T.CustomerID');
            $query->where('T.CustomerCateID', '=', $request->CustomerCateID);
            $query->where('T.CateValue', '=', $request->CateValue);
            $search = 1;
        }
        if($request->post('DocID')){
            $query->leftJoin(DB::raw('customer_file TF'), 'customer.CustomerID', '=', 'TF.CustomerID');
            $query->leftJoin(DB::raw('customer_video TV'), 'customer.CustomerID', '=', 'TV.CustomerID');
            $query->where(function($q) use ($request) {
                $q->where('TF.DocID', '=', $request->post('DocID'));
                $q->orWhere('TV.DocID', '=', $request->post('DocID'));
            });
            $search = 1;
        }
        if($request->post('CustomerName')){
            $query->where('customer.CustomerName', 'like', '%'.$request->CustomerName.'%');
        }
        if($request->post('CustomerNo')){
            $query->whereRaw('concat(customer.Prefix, customer.CustomerNo, customer.Suffix) like("%'.$request->CustomerNo.'%")');
        }
        if($request->post('ProvinceID')){
            $query->where('customer.ProvinceID', '=', $request->ProvinceID);
        }
        if($request->post('DistrictID')){
            $query->where('customer.DistrictID', '=', $request->DistrictID);
        }
        if($request->post('CommuneID')){
            $query->where('customer.CommuneID', '=', $request->CommuneID);
        }
        if($request->post('Fax')){
            $query->where('customer.Fax', 'like', $request->Fax.'%');
        }
        if($request->post('OfficePhone')){
            $query->where('customer.OfficePhone', 'like', $request->OfficePhone.'%');
        }
        if($request->post('Email')){
            $query->where('customer.Email', 'like', $request->Email.'%');
        }
        $leftJoin = 0;
        if($request->post('EmployeeID')){
            if($request->post('TransType') == 1){
                $query->where('CB.EmployeeID', '=', $request->EmployeeID);
            }elseif ($request->post('TransType') == 2){
                $query->where('CS.EmployeeID', '=', $request->EmployeeID);
            }else{
                $query->where(function($q) use ($request) {
                    $q->where('CB.EmployeeID', '=', $request->EmployeeID);
                    $q->orWhere('CS.EmployeeID', '=', $request->EmployeeID);
                });
            }
            $leftJoin = 1;
            $search = 1;
        }
        if($request->post('ProjectID')){

            if($request->post('TransType') == 1){
                $query->where('CB.ProjectID', '=', $request->ProjectID);
            }elseif ($request->post('TransType') == 2){
                $query->where('CS.ProjectID', '=', $request->ProjectID);
            }else{
                $query->where(function($q) use ($request) {
                    $q->where('CB.ProjectID', '=', $request->ProjectID);
                    $q->orWhere('CS.ProjectID', '=', $request->ProjectID);
                });
            }

            $leftJoin = 1;
            $search = 1;
        }
        if($request->post('ContractID')){

            if($request->post('TransType') == 1){
                $query->where('CB.ContractID', '=', $request->ContractID);
            }elseif ($request->post('TransType') == 2){
                $query->where('CS.ContractID', '=', $request->ContractID);
            }else{
                $query->where(function($q) use ($request) {
                    $q->where('CB.ContractID', '=', $request->ContractID);
                    $q->orWhere('CS.ContractID', '=', $request->ContractID);
                });
            }

            $leftJoin = 1;
            $search = 1;
        }

        if($request->post('CompanyID')){
            $company = Company::find($request->post('CompanyID'));
            $CompanyLevel = 0;
            if($company){
                $CompanyLevel = $company->Level;
            }
            switch ($CompanyLevel){
                case 1:
                    $query->where(function($q) use ($request) {
                        $q->where('customer.CompanyID', '=', $request->CompanyID);
                        $q->orWhere('customer.ParentCompanyID1', '=', $request->CompanyID);
                    });
                    break;
                case 2:
                    $query->where(function($q) use ($request) {
                        $q->where('customer.CompanyID', '=', $request->CompanyID);
                        $q->orWhere('customer.ParentCompanyID2', '=', $request->CompanyID);
                    });
                    break;
                case 3:
                    $query->where(function($q) use ($request) {
                        $q->where('customer.CompanyID', '=', $request->CompanyID);
                        $q->orWhere('customer.ParentCompanyID3', '=', $request->CompanyID);
                    });
                    break;
                default:
                    $query->where('customer.CompanyID', '=', $request->CompanyID);
                    break;
            }
            $search = 1;
        }
        if($request->post('ItemID')){

            if($request->post('TransType') == 1){
                $query->where('CB.ItemID', '=', $request->ItemID);
            }elseif ($request->post('TransType') == 2){
                $query->where('CS.ItemID', '=', $request->ItemID);
            }else{
                $query->where(function($q) use ($request) {
                    $q->where('CB.ItemID', '=', $request->ItemID);
                    $q->orWhere('CS.ItemID', '=', $request->ItemID);
                });
            }
            $leftJoin = 1;
            $search = 1;
        }
        if($request->post('fromdate')){
            $dateArr = explode('/', $request->fromdate);
            if(isset($dateArr[2])){
                $fromdate = $dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0];


                if($request->post('TransType') == 1){
                    $query->where('CB.TransDate', '>=', str_replace('/', '-', $fromdate) .' 00:00');
                }elseif ($request->post('TransType') == 2){
                    $query->where('CS.TransDate', '>=', str_replace('/', '-', $fromdate) .' 00:00');
                }else{
                    $query->where(function($q) use ($fromdate) {
                        $q->where('CB.TransDate', '>=', str_replace('/', '-', $fromdate) .' 00:00');
                        $q->orWhere('CS.TransDate', '>=', str_replace('/', '-', $fromdate) .' 00:00');
                    });
                }

                $search = 1;
                $leftJoin = 1;
            }
        }
        if($request->post('todate')){
            $dateArr = explode('/', $request->todate);
            if(isset($dateArr[2])) {
                $todate = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];

                if($request->post('TransType') == 1){
                    $query->where('CB.TransDate', '<=', str_replace('/', '-', $todate) .' 23:59');
                }elseif ($request->post('TransType') == 2){
                    $query->where('CS.TransDate', '<=', str_replace('/', '-', $todate) .' 23:59');
                }else{
                    $query->where(function($q) use ($todate) {
                        $q->where('CB.TransDate', '<=', str_replace('/', '-', $todate) .' 23:59');
                        $q->orWhere('CS.TransDate', '<=', str_replace('/', '-', $todate) .' 23:59');
                    });
                }
                $search = 1;
                $leftJoin = 1;
            }
        }
        if($leftJoin){
            if($request->post('TransType') == 1){
                $query->leftJoin($leftJoinCB, 'customer.CustomerID', '=', 'CB.CustomerID');
            }elseif ($request->post('TransType') == 2){
                $query->leftJoin($leftJoinCS, 'customer.CustomerID', '=', 'CS.CustomerID');
            }else{
                $query->leftJoin($leftJoinCB, 'customer.CustomerID', '=', 'CB.CustomerID');
                $query->leftJoin($leftJoinCS, 'customer.CustomerID', '=', 'CS.CustomerID');
            }
        }
        $query->select($select);
        $query->groupBy('customer.CustomerID','CT.LineID');
        $data = $query->get();
        return $data;
    }

    protected $fillable = [
        'CustomerID',
        'CustomerNo',
        'CustomerName',
        'Address',
        'BillTo',
        'ShipTo',
        'TaxCate',
        'BankAccount',
        'BankName',
        'OfficePhone',
        'Fax',
        'Email',
        'Website',
        'ProvinceID',
        'ProvinceName',
        'DistrictID',
        'DistrictName',
        'CommuneID',
        'CommuneName',
        'Note',
        'IsVendor',
        'NOrder',
        'Inactive',
        'NumberValue',
        'Prefix',
        'Suffix',
//        'AuthorizedPerson'
        ];
    public $timestamps = false;


}

?>
