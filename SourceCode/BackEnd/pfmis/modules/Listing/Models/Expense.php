<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Module\Listing\Traits\ExpenseCacheClear;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUser;
use phpDocumentor\Reflection\Types\Self_;

class Expense extends Model{
    use IjFullTextSearch, ExpenseCacheClear;
    protected $table = 'expense';
    protected $primaryKey = 'ExpenseID';

    protected static $searchable = ['ExpenseNo', 'ExpenseName' ];

    public static function search(Request $request){
        $data = null;
        $query = Expense::query();
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('ExpenseName')){
            $query->where('ExpenseName', 'like', '%'.$request->ExpenseName.'%');
        }
        if($request->post('ExpenseNo')){
            $query->where('ExpenseNo', 'like', '%'.$request->ExpenseNo.'%');
        }


        //Danh mục liên kết
        $ExpenseLink = $request->post('ExpenseLink');
        if($ExpenseLink && count($ExpenseLink) > 0){
            $query->leftJoin('expense_link','expense.ExpenseID','=','expense_link.ExpenseID');
            foreach($ExpenseLink as $expenseLink){
                if ($expenseLink['LinkTable']) {
                    $query->where('expense_link.LinkTable', '=', $expenseLink['LinkTable']);
                }
                if ($expenseLink['LinkID']) {
                    $query->where('expense_link.LinkID', '=', $expenseLink['LinkID']);
                }
            }
        }
        // Loại khoản thu
        $ExpenseCateList = $request->post('ExpenseCateList');
        if($ExpenseCateList && count($ExpenseCateList) > 0){
            $query->leftJoin('expense_cate','expense.ExpenseID','=','expense_cate.ExpenseID');
            $query->where(function ($query) use ($ExpenseCateList) {
                foreach($ExpenseCateList as $expenseCateList){
                    $arrCateID[]= $expenseCateList['CateID'];
                    if($expenseCateList['CateValue']){
                        $arrCateValue[]=$expenseCateList['CateValue'];
                    };
                    $query->orWhere(function($q) use ($expenseCateList){
                        $q->Where('CateID', $expenseCateList['CateID']);
                        if($expenseCateList['CateValue']){
                            $q->Where('CateValue', $expenseCateList['CateValue']);
                        }
                    });
                }
            });


        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

//        // Quyền xem
//        $query->leftJoin('expense_per','expense_per.ExpenseID','=','expense.ExpenseID');
//        $query->where(function ($q) use ($auth){
//            $q->where('expense_per.EmployeeID',$auth->EmployeeID)
//                ->orWhere('expense.AccessType',2);
//        });
        if ($request->has('exportData')) {
            $query->select('expense.ExpenseID','expense.ExpenseNo', 'expense.ExpenseName','expense.STT');
            return $query->orderBy('ExpenseID', 'asc')->paginate($request->post('per_page'));
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        if($request->post('viewType') === 'tree'){
            $query-> where('Level', '=', 1);
            $query-> groupBy('expense.ExpenseID')->orderBy('expense.ExpenseNo');
            return $query->paginate($per_page);
        }
        $query->groupBy('expense.ExpenseID');
        return $query->orderBy(DB::raw('ExpenseNo'))->paginate($per_page);
    }

    public static function getExpense($request)
    {
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        if ($request->get('ExpenseName')) {
            $expenseName = $request->get('ExpenseName');
            $query->where('ExpenseName', 'like', '%' . $expenseName . '%');
        }

        $query->orderBy('ExpenseID', 'DESC');
        $expense = $query->paginate($perPage);
        return $expense;
    }

    public static function getAllChildExpense($listID){
        $result = [];
        $expenses = Expense::whereIn('ParentID', $listID)->get();
        if (count($expenses)) {
            $expenseIDs = [];
            foreach ($expenses as $expense) {
                $expenseIDs[] = $expense->ExpenseID;
                $result[] = $expense;
            }
            $records = Expense::getAllChildExpense($expenseIDs);
            $result = array_merge($result, $records);
        }

        return $result;
    }

    protected $fillable = [
        'ExpenseID',
        'ExpenseNo',
        'ExpenseName',
        'ParentID',
        'ParentNo',
        'ParentName',
        'Level',
        'Detail',
        'UomID',
        'UomName',
        'NumberValue',
        'Note',
        'SbiItemID',
        'SbiItemNo',
        'SbiItemName',
        'NormID',
        'NormNo',
        'NormName',
        'BudgetBalanceType',
        'BudgetStateType',
        'Path',
        'SectorID',
        'SectorNo',
        'SectorName',
    ];
    public $timestamps = false;


}

?>
