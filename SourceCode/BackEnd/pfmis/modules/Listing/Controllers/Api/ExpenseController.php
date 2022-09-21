<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use IjCore\IjFile;
use IjCore\IjString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Expense;
use Module\Listing\Models\ExpenseCate;
use Module\Listing\Models\ExpenseCateList;
use Module\Listing\Models\ExpenseCateValue;
use Module\Listing\Models\ExpenseFile;
use Module\Listing\Models\ExpenseLink;
use Module\Listing\Models\ExpensePer;
use Module\Listing\Models\ExpenseVideo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\Uom;

class ExpenseController extends Controller{

    public function list(Request $request){
        $data = Expense::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('expense', ['select' => ['ExpenseNo', 'ExpenseName'], 'conditions' => []]), 'donvi.xlsx');
    }

    public function create(){
        $expense = Expense::all();
        $json['status'] = 1;
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['expense'] = $expense;
        return json_encode($json);
    }
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'ExpenseNo' => 'required|unique:expense',
            'ExpenseName'=> 'required|unique:expense',
        ],
            [
                'ExpenseNo.required' => 'Mã khoản chi không được để trống',
                'ExpenseNo.unique' => 'Mã khoản chi đã tồn tại',
                'ExpenseName.required' => 'Tên khoản chi không được để trống',
                'ExpenseName.unique' => 'Tên khoản chi đã tồn tại',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        if($request->ParentID){
            $Parent = Expense::find($request->ParentID);
            $Parent->Detail = 0;
            $Parent->save();
            $level = $Parent->Level+ 1;
            $path = $Parent->Path .'#'.$request->ExpenseNo;

        } else {
            $level = 1;
            $path = $request->ExpenseNo;
        }
        $expense = new Expense([
            'ExpenseNo' => $request->ExpenseNo,
            'ExpenseName' => $request->ExpenseName,
            'ParentID' => $request->ParentID,
            'ParentNo' => $request->ParentNo,
            'ParentName' => $request->ParentName,
            'Level' => $level,
            'Detail' => 1,
            'UomID' => $request->UomID,
            'UomName'=> $request->UomName,
            'Note' => $request->Note,
            'Inactive' => $request->Inactive,
            'SbiItemID' => $request->SbiItemID,
            'SbiItemNo' => $request->SbiItemNo,
            'SbiItemName' => $request->SbiItemName,
            'BudgetBalanceType' => $request->BudgetBalanceType,
            'BudgetStateType' => $request->BudgetStateType,
            'NormID' => $request->NormID,
            'NormNo' => $request->NormNo,
            'NormName' => $request->NormName,
            'Path' => $path,
            'SectorID' => $request->SectorID,
            'SectorNo' => $request->SectorNo,
            'SectorName' => $request->SectorName,
        ]);

        $expense->save();

        //expense cate
        $arrInsert = [];
        if($request->post('ExpenseCate')){
            foreach($request->post('ExpenseCate') as $key=> $item){
                if($item['CateID']) {
                    $arrInsert[] = [
                        'ExpenseID' => $expense->ExpenseID,
                        'CateID' => $item['CateID'],
                        'CateNo' => $item['CateNo'],
                        'CateValue' => $item['CateValue']
                    ];
                }
            }

            DB::table('expense_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $expense->ExpenseID;
        return json_encode($json);
    }

    public function view($id){
        $data = Expense::find($id);

        if($data){

            $arrParent = [];
            if($data->ParentID){
                $parent = Expense::find($data->ParentID);
                if(isset($parent->ExpenseNo) && isset($parent->ExpenseName)){
                    $arrParent = [
                        "ParentNo" => $parent->ExpenseNo,
                        "ParentName" => $parent->ExpenseName
                    ];
                }
            }
            $Uom = Uom::find($data->UomID);
            $UomName = isset($Uom->UomName)? $Uom->UomName : '';
            $ExpenseCate = ExpenseCate::where('ExpenseID',$id)->get();
            $arrCate = [];
            foreach($ExpenseCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $ExpenseCateList = ExpenseCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $ExpenseCateValue = ExpenseCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;
            $json['data']['Parent'] = $arrParent;
            $json['data']['UomName'] = $UomName;
            $json['data']['ExpenseCate'] = $ExpenseCate;
            $json['data']['ExpenseCateList'] = $ExpenseCateList;
            $json['data']['ExpenseCateValue'] = $ExpenseCateValue;
            return json_encode($json);
        }else{

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id){
        $data = Expense::find($id);
        $expense = Expense::all();
        if(old('ExpenseNo')){
            $data->ExpenseNo = old('ExpenseNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['expense'] = $expense;
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $expense = Expense::find($id);
        if(!$expense){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($expense->ExpenseNo !== $request->ExpenseNo){
            $validator = Validator::make($request->all(), [
                'ExpenseNo' => 'required',
                'ExpenseName'=> 'required',
            ],
                [
                    'ExpenseNo.required' => 'Mã khoản chi không được để trống',
                    'ExpenseName.required' => 'Tên khoản chi không được để trống',
                ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            if($expense->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $expense;
                return $json;
            }
        }

        if($request->ParentID !== $expense->ParentID && $expense->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $expense;
            return $json;
        }
        // set detail new Parent
        if($request->ParentID){
            $parent = Expense::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
            $path = $parent->Path . '#' . $request->ExpenseNo;
        } else {
            $level = 1;
            $path = $request->ExpenseNo;
        }
        // set detail parentOld
        if($expense->ParentID != null && $expense->ParentID != $request->ParentID ){
            $parentOld = Expense::find($expense->ParentID);
            $count = DB::table('expense')->where('ParentID', '=', $request->ParentID)->count();
            if($count < 2){
                $parentOld->Detail =1;
                $parentOld->save();
            }
        }

        $expense->ExpenseName = $request->ExpenseName;
        $expense->SbiItemID = $request->SbiItemID;
        $expense->SbiItemNo = $request->SbiItemNo;
        $expense->SbiItemName = $request->SbiItemName;
        $expense->ParentID = $request->ParentID;
        $expense->UomID  = $request->UomID;
        $expense->UomName = $request ->UomName;
        $expense->Note  = $request->Note;
        $expense->Level = $level;
        $expense->Path = $path;
        $expense->NormID = $request->NormID;
        $expense->NormNo = $request->NormNo;
        $expense->NormName = $request->NormName;
        $expense->BudgetBalanceType = $request->BudgetBalanceType;
        $expense->BudgetStateType = $request->BudgetStateType;
        $expense->SectorID = $request->SectorID;
        $expense->SectorNo = $request->SectorNo;
        $expense->SectorName = $request->SectorName;
        $expense->save();

        // expense cate
        if($request->post('ExpenseCate')){
            $ExpenseCate = $request->post('ExpenseCate');
            ExpenseCate::where('ExpenseID',$id)->delete();
            $arrInsert = [];
            foreach($ExpenseCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "ExpenseID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateNo"=> $cate['CateNo'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('expense_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $expense->ExpenseID;
        return $json;


    }

    public function delete($id){
        $data = Expense::find($id);
        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Lỗi!';
            $json['data'] = [];
            return $json;
        }
        if($data){
            // set Detail oldParent
            $count = DB::table('expense')->where('ParentID', $data->ParentID)->count();
            if($count < 2){
                $oldParent = Expense::find($data->ParentID);
                $oldParent->Detail = 1;
                $oldParent->save();
            }
            DB::table('expense_cate')->where('ExpenseID', '=', $data->ExpenseID)->delete();
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        foreach ($IDs as $id){
            $data = Expense::find($id);
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Lỗi!';
                $json['data'] = [];
                return $json;
            }
        }
        DB::table('expense_cate')->whereIn('ExpenseID', $IDs)->delete();
        Expense::destroy($IDs);
        $sql = "UPDATE expense
                SET Detail = 1
                WHERE ExpenseID NOT IN (SELECT ParentID FROM (select ParentID from expense WHERE ParentID is not null) as r)";
        DB::statement($sql);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }


    public function getExpenseCateList(){
        $expenseCateList = ExpenseCateList::where('Inactive',0)
            ->where('Detail', 1)
            ->get();
        $expenseCateValue = ExpenseCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['ExpenseCateList'] = $expenseCateList;
        $json['data']['ExpenseCateValue'] = $expenseCateValue;
        return json_encode($json);
    }

    public function getExpenseLink($id){
        $data = ExpenseLink::where('ExpenseID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function updateLink(Request $request){
        $LinkArray = $request->post('ExpenseLink');
        $id = $request->post('ExpenseID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'ExpenseID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        ExpenseLink::where(['ExpenseID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('expense_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = Expense::query();
        $query->where('expense.ParentID', $ParentID);
        $query->groupBy('expense.ExpenseID')->orderBy('expense.ExpenseNo',"desc");
        $data = $query->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $data = Expense::query();
        $data->leftJoin('expense_cate','expense.ExpenseID','=','expense_cate.ExpenseID');
        if(isset($filter['ExpenseName'])){
            $data->where('ExpenseName', 'like', '%'.$filter['ExpenseName'].'%');
        }
        if(isset($filter['ExpenseNo'])){
            $data->where('ExpenseNo', 'like', '%'.$filter['ExpenseNo'].'%');
        }
        if(isset($filter['ExpenseCateList'])){
            $ExpenseCateList = $filter['ExpenseCateList'];
            if($ExpenseCateList && count($ExpenseCateList) > 0){
                $data->where(function($query) use ($ExpenseCateList){
                    foreach ($ExpenseCateList as $ExpenseCate){
                        $query->orWhere(function ($q) use ($ExpenseCate){
                            $q->where('expense_cate.CateID', $ExpenseCate['CateID']);
                            if($ExpenseCate['CateValue']){
                                $q->where('expense_cate.CateValue', $ExpenseCate['CateValue']);
                            }
                        });
                    }
                });
            }
        }
        $data->leftJoin('expense_cate_list', 'expense_cate.CateID', '=','expense_cate_list.CateID');
        $data->selectRaw("expense.STT ,expense.ExpenseNo, expense.ExpenseName, CONCAT(expense_cate_list.CateNo,'-',expense_cate_list.CateName) as ExpenseCate");
        $perPage = (int)$request->post('per_page');
        $data->orderBy('expense.ExpenseNo', 'asc')->orderBy('expense_cate_list.CateNo', 'asc');
        $data->paginate($perPage);
        $json['data']['detail'] = $data->paginate($perPage)->items();
        $json['data']['total'] = $data->paginate($perPage)->total();
        $json['data']['per_page'] = $data->paginate($perPage)->perPage();
        $json['data']['current_page'] = $data->paginate($perPage)->currentPage();
        return json_encode($json);
    }

}
