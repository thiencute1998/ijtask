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
use Module\Listing\Models\Capital;
use Module\Listing\Models\CapitalCate;
use Module\Listing\Models\CapitalCateList;
use Module\Listing\Models\CapitalCateValue;
use Module\Listing\Models\CapitalFile;
use Module\Listing\Models\CapitalLink;
use Module\Listing\Models\CapitalPer;
use Module\Listing\Models\CapitalVideo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;

class CapitalController extends Controller{

    public function list(Request $request){
        $data = Capital::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('capital', ['select' => ['CapitalNo', 'CapitalName'], 'conditions' => []]), 'donvi.xlsx');
    }

    public function create(){
        $capital = Capital::all();
        $json['status'] = 1;

        $json['msg'] = 'Thành công!';
        $json['data']['capital'] = $capital;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'CapitalNo' => 'required|unique:capital',
            'CapitalName'=> 'required|unique:capital',
        ],
            [
                'CapitalNo.required' => 'Mã nguồn vốn không được để trống',
                'CapitalNo.unique' => 'Mã nguồn vốn đã tồn tại',
                'CapitalName.required' => 'Tên nguồn vốn không được để trống',
                'CapitalName.unique' => 'Tên nguồn vốn đã tồn tại',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        if($request->ParentID){
            $Parent = Capital::find($request->ParentID);
            $Parent->Detail = 0;
            $Parent->save();
            $level = $Parent->Level+ 1;
            $path = $Parent->Path .'#'.$request->CapitalNo;

        } else {
            $level = 1;
            $path = $request->CapitalNo;
        }

        $capital = new Capital([
            'CapitalNo' => $request->CapitalNo,
            'CapitalName' => $request->CapitalName,
            'ParentID' => $request->ParentID,
            'ParentNo' => $request->ParentNo,
            'ParentName' => $request->ParentName,
            'Level' => $level,
            'Detail' => 1,
            'Note' => $request->Note,
            'Inactive' => $request->Inactive,
            'BudgetStateType' => $request->BudgetStateType,

            'Path' => $path,
        ]);

        $capital->save();

        //capital cate
        $arrInsert = [];
        if($request->post('CapitalCate')){
            foreach($request->post('CapitalCate') as $key=> $item){
                if($item['CateID']) {
                    $arrInsert[] = [
                        'CapitalID' => $capital->CapitalID,
                        'CateID' => $item['CateID'],
                        'CateNo' => $item['CateNo'],
                        'CateValue' => $item['CateValue']
                    ];
                }
            }

            DB::table('capital_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $capital->CapitalID;
        return json_encode($json);
    }

    public function view($id){
        $data = Capital::find($id);

        if($data){

            $arrParent = [];
            if($data->ParentID){
                $parent = Capital::find($data->ParentID);
                if(isset($parent->CapitalNo) && isset($parent->CapitalName)){
                    $arrParent = [
                        "ParentNo" => $parent->CapitalNo,
                        "ParentName" => $parent->CapitalName
                    ];
                }
            }

            $CapitalCate = CapitalCate::where('CapitalID',$id)->get();
            $arrCate = [];
            foreach($CapitalCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $CapitalCateList = CapitalCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $CapitalCateValue = CapitalCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;
            $json['data']['Parent'] = $arrParent;
            $json['data']['CapitalCate'] = $CapitalCate;
            $json['data']['CapitalCateList'] = $CapitalCateList;
            $json['data']['CapitalCateValue'] = $CapitalCateValue;
            return json_encode($json);
        }else{

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id){
        $data = Capital::find($id);
        $capital = Capital::all();
        if(old('CapitalNo')){
            $data->CapitalNo = old('CapitalNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['capital'] = $capital;
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $capital = Capital::find($id);
        if(!$capital){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($capital->CapitalNo !== $request->CapitalNo){
            $validator = Validator::make($request->all(), [
                'CapitalNo' => 'required',
                'CapitalName'=> 'required',
            ],
                [
                    'CapitalNo.required' => 'Mã nguồn vốn không được để trống',
                    'CapitalName.required' => 'Tên nguồn vốn không được để trống',
                ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            if($capital->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $capital;
                return $json;
            }
        }

        if($request->ParentID !== $capital->ParentID && $capital->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $capital;
            return $json;
        }
        // set detail new Parent
        if($request->ParentID){
            $parent = Capital::find($request->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
            $path = $parent->Path . '#' . $request->CapitalNo;
        } else {
            $level = 1;
            $path = $request->CapitalNo;
        }
        // set detail parentOld
        if($capital->ParentID != null && $capital->ParentID != $request->ParentID ){
            $parentOld = Capital::find($capital->ParentID);
            $count = DB::table('capital')->where('ParentID', '=', $request->ParentID)->count();
            if($count < 2){
                $parentOld->Detail =1;
                $parentOld->save();
            }
        }

        $capital->CapitalName = $request->CapitalName;
        $capital->ParentID = $request->ParentID;
        $capital->ParentNo = $request->ParentNo;
        $capital->ParentName = $request->ParentName;
        $capital->Note  = $request->Note;
        $capital->Level = $level;
        $capital->BudgetStateType = $request->BudgetStateType;
        $capital->Path = $path;
        $capital->save();

        // capital cate
        if($request->post('CapitalCate')){
            $CapitalCate = $request->post('CapitalCate');
            CapitalCate::where('CapitalID',$id)->delete();
            $arrInsert = [];
            foreach($CapitalCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "CapitalID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateNo"=> $cate['CateNo'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('capital_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $capital->CapitalID;
        return $json;


    }

    public function delete($id){
        $data = Capital::find($id);
        if($data){
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Lỗi!';
                $json['data'] = [];
                return $json;
            }
            // set Detail oldParent
            if($data->ParentID){
                $count = DB::table('capital')->where('ParentID', $data->ParentID)->count();
                if($count < 2){
                    $oldParent = Capital::find($data->ParentID);
                    $oldParent->Detail = 1;
                    $oldParent->save();
                }
            }
            DB::table('capital_cate')->where('CapitalID', '=', $data->CapitalID)->delete();
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
            $data = Capital::find($id);
            if($data->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Lỗi!';
                $json['data'] = [];
                return $json;
            }
        }
        Capital::destroy($IDs);
        DB::table('capital_cate')->whereIn('CapitalID', $IDs)->delete();
        $sql = "UPDATE capital
                SET Detail = 1
                WHERE CapitalID NOT IN (SELECT ParentID FROM (select ParentID from capital WHERE ParentID is not null) as r)";
        DB::statement($sql);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }


    public function getCapitalCateList(){
        $capitalCateList = CapitalCateList::where('Inactive',0)
            ->where('Detail', 1)
            ->get();
        $capitalCateValue = CapitalCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CapitalCateList'] = $capitalCateList;
        $json['data']['CapitalCateValue'] = $capitalCateValue;
        return json_encode($json);
    }
    public function getCapitalCateListSerach(){
        $capitalCateList = CapitalCateList::where('Inactive', 0)->orderBy('CateNo')->get();
        $capitalCateValue = CapitalCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['CapitalCateList'] = $capitalCateList;
        $json['data']['CapitalCateValue'] = $capitalCateValue;
        return json_encode($json);
    }


    public function getCapitalLink($id){
        $data = CapitalLink::where('CapitalID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function updateLink(Request $request){
        $LinkArray = $request->post('CapitalLink');
        $id = $request->post('CapitalID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'CapitalID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        CapitalLink::where(['CapitalID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('capital_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = Capital::query();
        $query->where('capital.ParentID', $ParentID);
        $query->groupBy('capital.CapitalID')->orderBy('capital.CapitalNo',"desc");
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
        $data = Capital::query();
        $data->leftJoin('capital_cate','capital.CapitalID','=','capital_cate.CapitalID');
        if(isset($filter['CapitalName'])){
            $data->where('CapitalName', 'like', '%'.$filter['CapitalName'].'%');
        }
        if(isset($filter['CapitalNo'])){
            $data->where('CapitalNo', 'like', '%'.$filter['CapitalNo'].'%');
        }
        if(isset($filter['CapitalCateList'])){
            $CapitalCateList = $filter['CapitalCateList'];
            if($CapitalCateList && count($CapitalCateList) > 0){
                $data->where(function($query) use ($CapitalCateList){
                    foreach ($CapitalCateList as $CapitalCate){
                        $query->orWhere(function ($q) use ($CapitalCate){
                            $q->where('capital_cate.CateID', $CapitalCate['CateID']);
                            if($CapitalCate['CateValue']){
                                $q->where('capital_cate.CateValue', $CapitalCate['CateValue']);
                            }
                        });
                    }
                });
            }
        }
        $data->leftJoin('capital_cate_list', 'capital_cate.CateID', '=','capital_cate_list.CateID');
        $data->selectRaw("capital.STT ,capital.CapitalNo, capital.CapitalName, CONCAT(capital_cate_list.CateNo,'-',capital_cate_list.CateName) as CapitalCate");
        $perPage = (int)$request->post('per_page');
        $data->orderBy('capital.CapitalNo', 'asc')->orderBy('capital_cate_list.CateNo', 'asc');
        $data->paginate($perPage);
        $json['data']['detail'] = $data->paginate($perPage)->items();
        $json['data']['total'] = $data->paginate($perPage)->total();
        $json['data']['per_page'] = $data->paginate($perPage)->perPage();
        $json['data']['current_page'] = $data->paginate($perPage)->currentPage();
        return json_encode($json);
    }

}
