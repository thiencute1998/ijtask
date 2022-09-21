<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use IjSmartBooks\IjCore\IjFunction\Date;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;
use function foo\func;

class Doc extends Model{
    use IjFullTextSearch;
    protected $table = 'doc';
    protected $primaryKey = 'DocID';
    protected static $searchable = ['doc.DocName', 'doc.Title', 'doc.Path'];
    public static function search(Request $request){

        $auth = Auth::user();
        $query = Doc::query();
        $query->select('doc.*');

        if ($request->post('ViewType') === 'tree') {
            $query->where('doc.Level', '=', 1);
        }

        if ($request->post('DocName')) {
            $query->where('doc.DocName', 'like', '%' . $request->post('DocName') . '%');
        }

        if ($request->post('fromDate')) {
            $fromDate = Date::StringToDate($request->post('fromDate'));
            $query->where(DB::raw('DATE_FORMAT(doc.DocDate, "%Y-%m-%d")'), '>=', $fromDate);
        }
        if ($request->post('toDate')) {
            $toDate = Date::StringToDate($request->post('toDate'));
            $query->where(DB::raw('DATE_FORMAT(doc.DocDate, "%Y-%m-%d")'), '<=', $toDate);
        }

        // Danh mục liên kết
        $docLinks = $request->post('DocLink');
        if ($docLinks && (count($docLinks) > 0)) {
            $query->join('doc_link', 'doc.DocID', '=', 'doc_link.DocID');
            foreach ($docLinks as $docLink) {
                if ($docLink['LinkTable']) {
                    $query->where('doc_link.LinkTable', '=', $docLink['LinkTable']);
                }
                if ($docLink['LinkID']) {
                    $query->where('doc_link.LinkID', '=', $docLink['LinkID']);
                }
            }
        }

        // full text search
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem tài liệu
        $query->leftJoin('doc_per', 'doc.DocID', '=', 'doc_per.DocID');
        $query->where(function ($q) use ($auth) {
            $q->where('doc_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('doc.AccessType', '=', 2);
                $q->whereNull('doc.PublicCompanyID');
            });

            $q->orWhere(function ($q) use ($auth) {
                $q->where('doc.AccessType', '=', 2);
                $q->whereNotNull('doc.PublicCompanyID');
                $q->where('doc.PublicCompanyID', '=', $auth->CompanyID);
            });
        });


        $query->groupBy('doc.DocID');
        $query->orderBy('doc.DocID', 'DESC');
        $per_page = SysSetup::getOption('NumberRowOnPage');
        if($request->get('per_page')){
            $per_page = $request->get('per_page');
        }
        return $query->paginate($per_page);



//        $data = null;
//        $leftJoinC = DB::raw('doc_per L');
//        $select = DB::raw("doc.*");
//        $query = Doc::query("doc.*")->leftJoin($leftJoinC, 'doc.DocID', '=', 'L.DocID')->select($select);
//        $where = [];
//        $search = 0;
//
//        $leftJoinT = DB::raw('doc_cate T');
//        if($request->DocCate){
//            $arrDocCate = explode('_', $request->DocCate);
//            $query->leftJoin($leftJoinT, 'doc.DocID', '=', 'T.DocID');
//            $arrCateStrWhere = null;
//            for ($i = 0; $i < sizeof($arrDocCate); $i++) {
//                if ($arrDocCate[$i]) {
//                    $arrCateValue = explode(':', $arrDocCate[$i]);
//
//                    if (isset($arrCateValue[0])) {
//                        if (isset($arrCateStrWhere[$arrCateValue[0]])) {
//                            $arrCateStrWhere[$arrCateValue[0]] .= " OR ((T.DocCateID = " . $arrCateValue[0] . ")";
//                        } else {
//                            $arrCateStrWhere[$arrCateValue[0]] = "(((T.DocCateID = " . $arrCateValue[0] . ")";
//                        }
//                        if (isset($arrCateValue[1]) && $arrCateValue[1]) {
//                            $arrCateStrWhere[$arrCateValue[0]] .= " AND (T.DocCateValue = " . $arrCateValue[1] . ")";
//                        }
//                        $arrCateStrWhere[$arrCateValue[0]] .= ")";
//                    }
//                }
//            }
//            if($arrCateStrWhere){
//                $CateStrWhere = implode(") AND ", $arrCateStrWhere);
//                $CateStrWhere .=")";
//                $query->whereRaw($CateStrWhere);
//            }
//        }
//        if($request->post('DocCateID') && $request->post('DocCateValue')){
//            $query->leftJoin($leftJoinT, 'doc.DocID', '=', 'T.DocID');
//            $query->where('T.DocCateID', '=', $request->DocCateID);
//            $query->where('T.DocCateValue', '=', $request->DocCateValue);
//            $search = 1;
//        }elseif ($request->post('DocCateID')){
//            $query->leftJoin($leftJoinT, 'doc.DocID', '=', 'T.DocID');
//            $query->where('T.DocCateID', '=', $request->DocCateID);
//        }
//        if($request->post('DocName')){
//            $query->where('doc.DocName', 'like', '%'.$request->DocName.'%');
//            $search = 1;
//        }
//
//        if($request->post('ProjectID')){
//            $query->where('doc.ProjectID', '=', $request->post('ProjectID'));
//            $search = 1;
//        }
//
//        if($request->post('EmployeeID')){
//            $query->where('doc.EmployeeID', '=', $request->post('EmployeeID'));
//            $search = 1;
//        }
//
//        if($request->post('VendorID')){
//            $query->where('doc.VendorID', '=', $request->post('VendorID'));
//            $search = 1;
//        }
//
//        if($request->post('ObjectID')){
//            $query->where('doc.ObjectID', '=', $request->post('ObjectID'));
//            $search = 1;
//        }
//
//        if($request->post('ExpenseID')){
//            $query->where('doc.ExpenseID', '=', $request->post('ExpenseID'));
//            $search = 1;
//        }
//
//        if($request->post('ItemID')){
//            $query->where('doc.ItemID', '=', $request->post('ItemID'));
//            $search = 1;
//        }
//        if($request->post('TaskID')){
//            $leftJoinTF = DB::raw('task_file TF');
//            $leftJoinTV = DB::raw('task_video TV');
//            $query->leftJoin($leftJoinTF, 'doc.DocID', '=', 'TF.DocID');
//            $query->leftJoin($leftJoinTV, 'doc.DocID', '=', 'TV.DocID');
//
//            $query->where(function($q) use ($request) {
//                $q->where('TF.TaskID', '=', $request->post('TaskID'));
//                $q->orWhere('TV.TaskID', '=', $request->post('TaskID'));
//            });
//            $search = 1;
//        }
//        if($request->post('CustomerID')){
//            $leftJoinCF = DB::raw('customer_file CF');
//            $leftJoinCV = DB::raw('customer_video CV');
//            $query->leftJoin($leftJoinCF, 'doc.DocID', '=', 'CF.DocID');
//            $query->leftJoin($leftJoinCV, 'doc.DocID', '=', 'CV.DocID');
//
//            $query->where(function($q) use ($request) {
//                $q->where('CF.CustomerID', '=', $request->post('CustomerID'));
//                $q->orWhere('CV.CustomerID', '=', $request->post('CustomerID'));
//            });
//            $search = 1;
//        }
//
//        if($request->post('ContractID')){
//            $leftJoinCTF = DB::raw('contract_file CTF');
//            $leftJoinCTV = DB::raw('contract_video CTV');
//            $query->leftJoin($leftJoinCTF, 'doc.DocID', '=', 'CTF.DocID');
//            $query->leftJoin($leftJoinCTV, 'doc.DocID', '=', 'CTV.DocID');
//
//            $query->where(function($q) use ($request) {
//                $q->where('CTF.ContractID', '=', $request->post('ContractID'));
//                $q->orWhere('CTV.ContractID', '=', $request->post('ContractID'));
//            });
//            $search = 1;
//        }
//        if($request->ShowType == 2){//Hiển thị kiểu folder
//            if($search == 0){
//                $query->where('doc.Level', '=', 1);
//            }
//        }
//        if($search == 1){
//            $query->where('doc.Detail', '=', 1);
//        }
//        $employeeUser = Employee::where(['UserID' => Auth::user()->UserID])->first();
//
//        $query->where(function($q) use ($employeeUser) {
//            $q->where('L.EmployeeID', '=', $employeeUser->EmployeeID);
//
//            $q->orWhere(function($q1) use ($employeeUser) {
//                $q1->where('doc.AccessType', '=', 3);
//                $q1->where('doc.PublicCompanyID', '=', $employeeUser->CompanyID);
//            });
//        });
//        $query->groupBy('doc.DocID');
//        $query->orderBy(DB::raw("if(doc.Path = '', concat('-', doc.DocID, '-'), concat(doc.Path, doc.DocID, '-'))"));
//
//        $data = $query->get();
//        if($request->post('per_page')){
//            $per_page = $request->post('per_page');
//        }else{
//            $per_page = SysSetup::getOption('NumberRowOnPage');
//        }
//        return ['data' => $query->orderBy('DocNo')->paginate($per_page), 'total' => $data->count(), 'search' => $search];
    }

    public static function getDocPer($doc, $employeeUser){
        $data = null;
        if($doc->AccessType == 3 && $doc->PublicCompanyID == $employeeUser->CompanyID){
            return true;
        }else{
            $docPer = DocPer::where(['EmployeeID' => $employeeUser->EmployeeID, 'DocID' => $doc->DocID])->first();
            if(isset($docPer->DocID)){
                return true;
            }else{
                return false;
            }
        }
    }
    public static function getDoc($request){
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        if ($request->post('DocName')) {
            $DocName = $request->post('DocName');
            $query->where('DocName', 'like', '%' . $DocName . '%');
        }
        if ($request->post('DocNo')) {
            $DocNo = $request->post('DocNo');
            $query->where('DocNo', 'like', '%' . $DocNo . '%');
        }

        $doc = $query->paginate($perPage);
        return $doc;
    }
    protected $fillable = [
        'DocNo',
        'DocName',
        'ParentID',
        'ParentName',
        'Level',
        'Detail',
        'DocDate',
        'EffectiveDate',
        'SignerName',
        'CompanyIssued',
        'Inactive',
        'NumberValue',
        'Prefix',
        'Suffix',
        'ProjectID',
        'ProjectName',
        'ProjectNo',
        'CustomerID',
        'CustomerName',
        'CustomerNo',
        'VendorID',
        'VendorName',
        'VendorNo',
        'ObjectID',
        'ObjectName',
        'ObjectNo',
        'ContractID',
        'ContractName',
        'ContractNo',
        'AccessType',
        'PublicCompanyID',
        'TaskID',
        'TaskName',
        'TaskNo',
        'ExpenseID',
        'ExpenseName',
        'ExpenseNo',
        'ItemID',
        'ItemName',
        'ItemNo',
        'EmployeeID',
        'EmployeeName',
        'EmployeeNo',
        'Path',
        'UserIDCreated',
        'Title',
        'AuthorizedPerson'

    ];
    public $timestamps = false;

}

?>
