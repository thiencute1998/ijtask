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
use Module\Listing\Models\Revenue;
use Module\Listing\Models\RevenueCate;
use Module\Listing\Models\RevenueCateList;
use Module\Listing\Models\RevenueCateValue;
use Module\Listing\Models\RevenueFile;
use Module\Listing\Models\RevenueLink;
use Module\Listing\Models\RevenuePer;
use Module\Listing\Models\RevenueVideo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\Uom;


class RevenueController extends Controller{

    public function list(Request $request){
        $data = Revenue::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('revenue', ['select' => ['RevenueNo', 'RevenueName'], 'conditions' => []]), 'donvi.xlsx');
    }

    public function create(){
        $revenue = Revenue::all();
        $json['status'] = 1;

        $json['msg'] = 'Thành công!';
        $json['data']['revenue'] = $revenue;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'RevenueNo' => 'required|unique:revenue',
            'RevenueName'=> 'required|unique:revenue',
        ],
            [
                'RevenueNo.required' => 'Mã khoản thu không được để trống',
                'RevenueNo.unique' => 'Mã khoản thu đã tồn tại',
                'RevenueName.required' => 'Tên khoản thu không được để trống',
                'RevenueName.unique' => 'Tên khoản thu đã tồn tại',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        // set for parent
       if($request->ParentID){
           $parent = Revenue::find($request->ParentID);
           $parent->Detail = 0;
           $parent->save();
           $level = $parent->Level + 1;
           $path = $parent->Path. '#' . $request->RevenueNo;
       } else {
           $level = 1;
           $path = $request->RevenueNo;
       }

        $revenue = new Revenue([
            'RevenueNo' => $request->RevenueNo,
            'RevenueName' => $request->RevenueName,
            'ParentID' => $request->ParentID,
            'ParentNo' => $request->ParentNo,
            'ParentName' => $request->ParentName,
            'Level' => $level,
            'Path' => $path,
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
            'isRevenueRegulationRate'=> 0,
            'SbrSectorID'=>$request->SbrSectorID,
            'SbrSectorNo'=>$request->SbrSectorNo,
            'SbrSectorName'=>$request->SbrSectorName,

        ]);

        $revenue->save();

        //revenue cate
        $arrInsert = [];
        if($request->post('RevenueCate')){
            foreach($request->post('RevenueCate') as $key=> $item){
                if($item['CateID']) {
                    $arrInsert[] = [
                        'RevenueID' => $revenue->RevenueID,
                        'CateID' => $item['CateID'],
                        'CateNo' => $item['CateNo'],
                        'CateValue' => $item['CateValue']
                    ];
                }
            }

            DB::table('revenue_cate')->insert($arrInsert);
        }
        // RevenueReguItem
        $arrRegu = [];
        if($request->post('RevenueReguItem')){
            foreach ($request->post('RevenueReguItem') as $key=>$item){
                if($item['EffectiveDate']){
                    $revenue->isRevenueRegulationRate = 1;
                    $revenue->save();
                    if($item['BudgetLevel'] == 0) $revenueReguName = 'Để lại đơn vị';
                    if($item['BudgetLevel'] == 1) $revenueReguName = 'Ngân sách TW';
                    if($item['BudgetLevel'] == 2) $revenueReguName = 'Ngân sách Tỉnh';
                    if($item['BudgetLevel'] == 3) $revenueReguName = 'Ngân sách Huyện';
                    if($item['BudgetLevel'] == 4) $revenueReguName = 'Ngân sách xã';
                    $effectiveDate =  date('Y-m-d',strtotime(str_replace('/','-', $item['EffectiveDate'])));
                    $expirationDate =  date('Y-m-d',strtotime(str_replace('/','-', $item['ExpirationDate'])));
                    $arrRegu[] =[
                        'RevenueReguName' => $revenueReguName,
                        'RevenueID' => $revenue->RevenueID,
                        'RevenueNo' => $revenue->RevenueNo,
                        'EffectiveDate'=> $effectiveDate,
                        'ExpirationDate' => $expirationDate,
                        'BudgetLevel' => $item['BudgetLevel'],
                        'RevenueReguActive'=>$item['RevenueReguActive'],
                        'ReguRate' => $item['ReguRate']
                    ];
                }
            }
            if(count($arrRegu)) DB::table('revenue_regu')->insert($arrRegu);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $revenue->RevenueID;
        return json_encode($json);
    }

    public function view($id){
        $data = Revenue::find($id);

        if($data){

            $arrParent = [];
            if($data->ParentID){
                $parent = Revenue::find($data->ParentID);
                if(isset($parent->RevenueNo) && isset($parent->RevenueName)){
                    $arrParent = [
                        "ParentNo" => $parent->RevenueNo,
                        "ParentName" => $parent->RevenueName
                    ];
                }
            }
            $Uom = Uom::find($data->UomID);
            $UomName = isset($Uom->UomName)? $Uom->UomName : '';
            $RevenueCate = RevenueCate::where('RevenueID',$id)->get();
            $arrCate = [];
            foreach($RevenueCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $RevenueCateList = RevenueCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $RevenueCateValue = RevenueCateValue::whereIn('CateID',$arrCate)->get();
            $RevenueReguItem = DB::table('revenue_regu')->where('RevenueID', '=', $id)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;
            $json['data']['Parent'] = $arrParent;
            $json['data']['UomName'] = $UomName;
            $json['data']['RevenueCate'] = $RevenueCate;
            $json['data']['RevenueCateList'] = $RevenueCateList;
            $json['data']['RevenueCateValue'] = $RevenueCateValue;
            $json['data']['RevenueReguItem'] = $RevenueReguItem;
            return json_encode($json);
        }else{

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id){
        $data = Revenue::find($id);
        $revenue = Revenue::all();
        if(old('RevenueNo')){
            $data->RevenueNo = old('RevenueNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['revenue'] = $revenue;
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $revenue = Revenue::find($id);
        if(!$revenue){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($revenue->RevenueNo !== $request->RevenueNo){
            $validator = Validator::make($request->all(), [
                'RevenueNo' => 'required',
                'RevenueName'=> 'required',
            ],
                [
                    'RevenueNo.required' => 'Mã khoản thu không được để trống',
                    'RevenueName.required' => 'Tên khoản thu không được để trống',
                ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            if($revenue->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $revenue;
                return $json;
            }
        }

        if($request->ParentID !== $revenue->ParentID && $revenue->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $revenue;
            return $json;
        }
        // set detail new Parent
        if($request->ParentID){
            $parent = Revenue::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
            $path = $parent->Path . '#' . $request->RevenueNo;
        } else {
            $level = 1;
            $path = $request->RevenueNo;
        }
        // set detail parentOld
        if($revenue->ParentID != null && $revenue->ParentID != $request->ParentID ){
            $parentOld = Revenue::find($revenue->ParentID);
            $count = DB::table('revenue')->where('ParentID', '=', $request->ParentID)->count();
            if($count < 2){
                $parentOld->Detail =1;
                $parentOld->save();
            }
        }

        $revenue->RevenueName = $request->RevenueName;
        $revenue->RevenueNo = $request->RevenueNo;
        $revenue->SbiItemID = $request->SbiItemID;
        $revenue->SbiItemNo = $request->SbiItemNo;
        $revenue->SbiItemName = $request->SbiItemName;
        $revenue->ParentID = $request->ParentID;
        $revenue->ParentNo = $request->ParentName;
        $revenue->ParentName = $request->ParentName;
        $revenue->UomID  = $request->UomID;
        $revenue->UomName = $request ->UomName;
        $revenue->Note  = $request->Note;
        $revenue->Level = $level;
        $revenue->Path = $path;
        $revenue->NormID = $request->NormID;
        $revenue->NormNo = $request->NormNo;
        $revenue->NormName = $request->NormName;
        $revenue->BudgetBalanceType = $request->BudgetBalanceType;
        $revenue->BudgetStateType = $request->BudgetStateType;
        $revenue->isRevenueRegulationRate = 0;
        $revenue->SbrSectorID = $request->SbrSectorID;
        $revenue->SbrSectorNo = $request->SbrSectorNo;
        $revenue->SbrSectorName = $request->SbrSectorName;
        $revenue->save();

        // revenue cate
        if($request->post('RevenueCate')){
            $RevenueCate = $request->post('RevenueCate');
            RevenueCate::where('RevenueID',$id)->delete();
            $arrInsert = [];
            foreach($RevenueCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "RevenueID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateNo"=> $cate['CateNo'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('revenue_cate')->insert($arrInsert);
        }
        if($request->post('RevenueReguItem')){
            $RevenueRegu = $request->post('RevenueReguItem');
            DB::table('revenue_regu')->where('RevenueID', '=', $id)->delete();
            $arrRegu = [];
            foreach ($RevenueRegu as $item){
                if($item['ReguRate']){
                    $revenue->isRevenueRegulationRate = 1;
                    $revenue->save();
                    if($item['BudgetLevel'] == 0) $revenueReguName = 'Để lại đơn vị';
                    if($item['BudgetLevel'] == 1) $revenueReguName = 'Ngân sách TW';
                    if($item['BudgetLevel'] == 2) $revenueReguName = 'Ngân sách Tỉnh';
                    if($item['BudgetLevel'] == 3) $revenueReguName = 'Ngân sách Huyện';
                    if($item['BudgetLevel'] == 4) $revenueReguName = 'Ngân sách xã';
                    $effectiveDate =  date('Y-m-d',strtotime(str_replace('/','-', $item['EffectiveDate'])));
                    $expirationDate =  date('Y-m-d',strtotime(str_replace('/','-', $item['ExpirationDate'])));
                    $arrRegu[] =[
                        'RevenueReguName' => $revenueReguName,
                        'RevenueID' => $revenue->RevenueID,
                        'RevenueNo' => $revenue->RevenueNo,
                        'EffectiveDate'=> $effectiveDate,
                        'ExpirationDate' => $expirationDate,
                        'BudgetLevel' => $item['BudgetLevel'],
                        'ReguRate' => $item['ReguRate'],
                        'RevenueReguActive' => $item['RevenueReguActive'],
                    ];
                }
            }
            if(count($arrRegu)) DB::table('revenue_regu')->insert($arrRegu);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $revenue->RevenueID;
        return $json;


    }

    public function delete($id){
        $data = Revenue::find($id);
        if($data){
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Lỗi!';
                $json['data'] = [];
                return $json;
            }
            // set Detail oldParent
            if($data->ParentID){
                $count = DB::table('revenue')->where('ParentID', $data->ParentID)->count();
                if($count < 2){
                    $oldParent = Revenue::find($data->ParentID);
                    $oldParent->Detail = 1;
                    $oldParent->save();
                }
            }
            DB::table('revenue_cate')->where('RevenueID', '=', $data->RevenueID)->delete();
            DB::table('revenue_regu')->where('RevenueID', '=', $data->RevenueID)->delete();
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
             $data = Revenue::find($id);
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Lỗi!';
                $json['data'] = [];
                return $json;
            }
        }
        Revenue::destroy($IDs);
        DB::table('revenue_cate')->whereIn('RevenueID', $IDs)->delete();
        DB::table('revenue_regu')->whereIn('RevenueID', $IDs)->delete();
        $sql = "UPDATE revenue
                SET Detail = 1
                WHERE RevenueID NOT IN (SELECT ParentID FROM (select ParentID from revenue WHERE ParentID is not null) as r)";
        DB::statement($sql);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }


    public function getRevenueCateList(){
        $revenueCateList = RevenueCateList::where('Inactive',0)
                                            ->where('Detail', 1)
                                            ->get();
        $revenueCateValue = RevenueCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['RevenueCateList'] = $revenueCateList;
        $json['data']['RevenueCateValue'] = $revenueCateValue;
        return json_encode($json);
    }
    public function getRevenueCateListSerach(){
        $revenueCateList = RevenueCateList::where('Inactive', 0)->orderBy('CateNo')->get();
        $revenueCateValue = RevenueCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['RevenueCateList'] = $revenueCateList;
        $json['data']['RevenueCateValue'] = $revenueCateValue;
        return json_encode($json);
    }


    public function getRevenueLink($id){
        $data = RevenueLink::where('RevenueID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function updateLink(Request $request){
        $LinkArray = $request->post('RevenueLink');
        $id = $request->post('RevenueID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'RevenueID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        RevenueLink::where(['RevenueID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('revenue_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = Revenue::query();
        $query->where('revenue.ParentID', $ParentID);
        $query->groupBy('revenue.RevenueID')->orderBy('revenue.RevenueNo',"desc");
        $data = $query->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
    public function getRevenueReguItem(Request $request){
        $RevenueID = $request->post('RevenueID');
        $effectiveDate = DB::table('revenue_regu')->where('RevenueID', '=', $RevenueID)->max('EffectiveDate');
        $revenueReguItem = DB::table('revenue_regu')->whereDate('EffectiveDate', $effectiveDate)->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $revenueReguItem;
        return json_encode($json);
    }
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];

        $filter = $request->post('filter');
        $data = Revenue::query();
        $data->leftJoin('revenue_cate','revenue.RevenueID','=','revenue_cate.RevenueID');
        if(isset($filter['RevenueName'])){
            $data->where('RevenueName', 'like', '%'.$filter['RevenueName'].'%');
        }
        if(isset($filter['RevenueNo'])){
            $data->where('RevenueNo', 'like', '%'.$filter['RevenueNo'].'%');
        }
        if(isset($filter['RevenueCateList'])){
            $RevenueCateList = $filter['RevenueCateList'];
            if($RevenueCateList && count($RevenueCateList) > 0){
               $data->where(function($query) use ($RevenueCateList){
                   foreach ($RevenueCateList as $RevenueCate){
                       $query->orWhere(function ($q) use ($RevenueCate){
                           $q->where('revenue_cate.CateID', $RevenueCate['CateID']);
                           if($RevenueCate['CateValue']){
                               $q->where('revenue_cate.CateValue', $RevenueCate['CateValue']);
                           }
                       });
                   }
               });
            }
        }
        $data->leftJoin('revenue_cate_list', 'revenue_cate.CateID', '=','revenue_cate_list.CateID');
        $data->selectRaw("revenue.STT ,revenue.RevenueNo, revenue.RevenueName, CONCAT(revenue_cate_list.CateNo,'-',revenue_cate_list.CateName) as RevenueCate");
        $perPage = (int)$request->post('per_page');
        $data->orderBy('revenue.RevenueNo', 'asc')->orderBy('revenue_cate_list.CateNo', 'asc');
        $data->paginate($perPage);
        $json['data']['detail'] = $data->paginate($perPage)->items();
        $json['data']['total'] = $data->paginate($perPage)->total();
        $json['data']['per_page'] = $data->paginate($perPage)->perPage();
        $json['data']['current_page'] = $data->paginate($perPage)->currentPage();
        return json_encode($json);
    }

}
