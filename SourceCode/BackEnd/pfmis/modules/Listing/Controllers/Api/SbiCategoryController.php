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
use Module\Listing\Models\SbiCategory;
use Module\Listing\Models\SbiCategoryCate;
use Module\Listing\Models\SbiCategoryCateList;
use Module\Listing\Models\SbiCategoryCateValue;
use Module\Listing\Models\SbiCategoryFile;
use Module\Listing\Models\SbiCategoryLink;
use Module\Listing\Models\SbiCategoryPer;
use Module\Listing\Models\SbiCategoryVideo;
use Module\Listing\Models\Employee;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\SysAutoNumber;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUserGroup;

class SbiCategoryController extends Controller{

    public function list(Request $request){
        $data = SbiCategory::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('sbi_category', ['select' => ['SbiCategoryNo', 'SbiCategoryName'], 'conditions' => []]), 'donvi.xlsx');
    }

    public function create(){
        $sbiCategory = SbiCategory::all();
        $json['status'] = 1;


        $json['msg'] = 'Thành công!';
        $json['data']['SbiCategory'] = $sbiCategory;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'SbiCategoryNo' => 'required|unique:sbi_category',
            'SbiCategoryName'=> 'required|unique:sbi_category',
        ],
            [
                'SbiCategoryNo.required' => 'Mã loại khoản không được để trống',
                'SbiCategoryNo.unique' => 'Mã loại khoản đã tồn tại',
                'SbiCategoryName.required' => 'Tên loại khoản không được để trống',
                'SbiCategoryName.unique' => 'Tên loại khoản đã tồn tại',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        if(empty($request->ParentID)){
            $level = 1;
        } else {
            $Parent = SbiCategory::find($request->ParentID);
            $Parent->Detail = 0;
            $Parent->save();
            $level = $Parent->Level+ 1;
        }
        $sbiCategory = new SbiCategory([
            'SbiCategoryNo' => $request->SbiCategoryNo,
            'SbiCategoryName' => $request->SbiCategoryName,
            'ParentID' => $request->ParentID,
            'Level' => $level,
            'Detail' => 1,
            'Note' => $request->Note,
            'Inactive' => $request->Inactive,
        ]);

        $sbiCategory->save();

        //sbiCategory cate
        $arrInsert = [];
        if($request->post('SbiCategoryCate')){
            foreach($request->post('SbiCategoryCate') as $key=> $item){
                if($item['CateID']) {
                    $arrInsert[] = [
                        'SbiCategoryID' => $sbiCategory->SbiCategoryID,
                        'CateID' => $item['CateID'],
                        'CateNo' => $item['CateNo'],
                        'CateValue' => $item['CateValue']
                    ];
                }
            }

            DB::table('sbi_category_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $sbiCategory->SbiCategoryID;

        return json_encode($json);
    }

    public function view($id){
        $data = SbiCategory::find($id);

        if($data){

            $arrParent = [];
            if($data->ParentID){
                $parent = SbiCategory::find($data->ParentID);
                if(isset($parent->SbiCategoryNo) && isset($parent->SbiCategoryName)){
                    $arrParent = [
                        "ParentNo" => $parent->SbiCategoryNo,
                        "ParentName" => $parent->SbiCategoryName
                    ];
                }
            }

            $SbiCategoryCate = SbiCategoryCate::where('SbiCategoryID',$id)->get();
            $arrCate = [];
            foreach($SbiCategoryCate as $Cate){
                $arrCate[] = $Cate->CateID;
            }
            $SbiCategoryCateList = SbiCategoryCateList::whereIn('CateID',$arrCate)->where('Inactive',0)->get();
            $SbiCategoryCateValue = SbiCategoryCateValue::whereIn('CateID',$arrCate)->get();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;
            $json['data']['Parent'] = $arrParent;
            $json['data']['SbiCategoryCate'] = $SbiCategoryCate;
            $json['data']['SbiCategoryCateList'] = $SbiCategoryCateList;
            $json['data']['SbiCategoryCateValue'] = $SbiCategoryCateValue;
            return json_encode($json);
        }else{

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id){
        $data = SbiCategory::find($id);
        $sbiCategory = SbiCategory::all();
        if(old('SbiCategoryNo')){
            $data->SbiCategoryNo = old('SbiCategoryNo');
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['SbiCategory'] = $sbiCategory;
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $sbiCategory = SbiCategory::find($id);
        if(!$sbiCategory){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
        if($sbiCategory->SbiCategoryNo !== $request->SbiCategoryNo){
            $validator = Validator::make($request->all(), [
                'SbiCategoryNo' => 'required',
                'SbiCategoryName'=> 'required',
            ],
                [
                    'SbiCategoryNo.required' => 'Mã loại khoản không được để trống',
                    'SbiCategoryName.required' => 'Tên loại khoản không được để trống',
                ]);
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $sbiCategory->SbiCategoryNo = $request->SbiCategoryNo;
            $sbiCategory->Level = empty($request->ParentID)? 1 : $validator->ParentLevel + 1;

        }
        $sbiCategory->SbiCategoryName = $request->SbiCategoryName;
        $sbiCategory->ParentID = $request->ParentID;
        $sbiCategory->Note  = $request->Note;
        $sbiCategory->save();

        // sbiCategory cate
        if($request->post('SbiCategoryCate')){
            $SbiCategoryCate = $request->post('SbiCategoryCate');
            SbiCategoryCate::where('SbiCategoryID',$id)->delete();
            $arrInsert = [];
            foreach($SbiCategoryCate as $cate){
                if($cate['CateID']){
                    $arrInsert[] = [
                        "SbiCategoryID"=> $id,
                        "CateID"=> $cate['CateID'],
                        "CateID"=> $cate['CateNo'],
                        "CateValue"=> ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }
            if(count($arrInsert)) DB::table('sbi_category_cate')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;


    }

    public function delete($id){
        $data = SbiCategory::find($id);
        DB::table('sbi_category_cate')->where('SbiCategoryID', '=', $id)->delete();
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        DB::table('sbi_category_cate')->whereIn('SbiCategoryID', $IDs);
        SbiCategory::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }


    public function getSbiCategoryCateList(){
        $sbiCategoryCateList = SbiCategoryCateList::where('Inactive',0)->get();
        $sbiCategoryCateValue = SbiCategoryCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['SbiCategoryCateList'] = $sbiCategoryCateList;
        $json['data']['SbiCategoryCateValue'] = $sbiCategoryCateValue;
        return json_encode($json);
    }

    public function getSbiCategoryLink($id){
        $data = SbiCategoryLink::where('SbiCategoryID',$id)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function updateLink(Request $request){
        $LinkArray = $request->post('SbiCategoryLink');
        $id = $request->post('SbiCategoryID');
        $arrLink = [];
        foreach ($LinkArray as $item){
            if($item['LinkTable']){
                $arrLink[] = [
                    'SbiCategoryID' => $id,
                    'LinkID' => $item['LinkID'],
                    'LinkNo' => $item['LinkNo'],
                    'LinkName' => $item['LinkName'],
                    'LinkTable' => $item['LinkTable'],
                    'LinkTableName' => $item['LinkTableName'],
                ];
            }
        }
        SbiCategoryLink::where(['SbiCategoryID' => $id])->delete();
        if(!empty($arrLink)){
            DB::table('sbi_category_link')->insert($arrLink);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = SbiCategory::query();
        $query->where('sbi_category.ParentID', $ParentID);
        $query->groupBy('sbi_category.SbiCategoryID')->orderBy('sbi_category.SbiCategoryNo',"desc");
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
        $data = SbiCategory::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }

}
