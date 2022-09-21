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
use Module\Listing\Models\Company;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\Revenue;
use Module\Listing\Models\RevenueNormTable;
use Module\Listing\Models\RevenueNormTableItem;
use Module\Listing\Models\SysAutoNumber;
use Module\Listing\Models\RevenueNorm;
use Module\Listing\Models\Employee;
use Module\Listing\Models\RevenueNormCate;
use Module\Listing\Models\RevenueNormCateList;
use Module\Listing\Models\RevenueNormCateValue;
use Module\Listing\Models\Uom;
use Module\SysAdmin\Models\SysUserGroup;
use function GuzzleHttp\Psr7\_parse_request_uri;

class RevenueNormController extends Controller{

    public function list(Request $request){
        $data = RevenueNorm::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('revenue_norm', ['select' => ['RevenueNormNo', 'RevenueNormName'], 'conditions' => []]), 'dinhmucthu.xlsx');
    }

    public function create(){
        if(old('RevenueNormNo')){
            $auto = old('RevenueNormNo');
        }else{
            $auto = AutoNo::genNewNumberNo('revenue_norm');
        }

        $uom = Uom::where('Inactive','!=',1)->get();
        $revenue = Revenue::where('Inactive','!=',1)->get();
        $revenueNormTable = RevenueNormTable::where('Inactive','!=',1)->get();
        $revenueNormTableItem = RevenueNormTableItem::where('Inactive','!=',1)->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        $json['data']['uom'] = $uom;
        $json['data']['revenue'] = $revenue;
        $json['data']['revenueNormTable'] = $revenueNormTable;
        $json['data']['revenueNormTableItem'] = $revenueNormTableItem;
        return json_encode($json);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'RevenueNormNo' => 'required|unique:revenue_norm'
        ],
            [
                'RevenueNormNo.required' => 'Mã định mức thu không được để trống',
                'RevenueNormNo.unique' => 'Mã định mức thu đã tồn tại',
            ]);

        $autoModel = SysAutoNumber::where('NumberKey', 'revenue_norm')->first();
        AutoNo::storeValidate($validator, $request, 'revenue_norm', $autoModel, 'định mức thu');
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }
        $data = new RevenueNorm([
            'RevenueNormNo' => $request->RevenueNormNo,
            'RevenueNormName' => $request->RevenueNormName,
            'Note' => $request->Note,
            'Inactive' => $request->Inactive,
        ]);
        $data->save();

        // cate list
        if ($request->has('RevenueNormCate')) {
            $RevenueNormCate = $request->post('RevenueNormCate');
            $arrInsertCate = [];
            foreach ($RevenueNormCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'RevenueNormID' => $data->RevenueNormID,
                        'CateID' => $cate['CateID'],
                        'CateValue' => $cate['CateValue']
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('revenue_norm_cate')->insert($arrInsertCate);
            }
        }

        if ($request->post('CopyID')) {
            $CopyID = $request->post('CopyID');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data->RevenueNormID;

        return json_encode($json);
    }

    public function view($id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => []
        ];

        $RevenueNorm = RevenueNorm::find($id);
        $Employee = Employee::where('Inactive', '!=', 1)->get();
        $Company = Company::where('Inactive', '!=', 1)->get();
        $Group = SysUserGroup::all();

        $RevenueNormCate = RevenueNormCate::where('RevenueNormID', $id)->get();
        $arrCateID = [];
        foreach ($RevenueNormCate as $Cate) {
            $arrCateID[] = $Cate->CateID;
        }
        $RevenueNormCateList = RevenueNormCateList::where('Inactive', '!=', 1)->whereIn('CateID', $arrCateID)->get();
        $RevenueNormCateValue = RevenueNormCateValue::whereIn('CateID', $arrCateID)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $RevenueNorm;
        $json['Group'] = $Group;
        $json['Company'] = $Company;
        $json['Employee'] = $Employee;
        $json['data']['RevenueNormCate'] = $RevenueNormCate;
        $json['data']['RevenueNormCateList'] = $RevenueNormCateList;
        $json['data']['RevenueNormCateValue'] = $RevenueNormCateValue;
        return json_encode($json);
    }

    public function edit($id){
        $data = RevenueNorm::find($id);
        if(old('RevenueNormNo')){
            $data->RevenueNormNo = old('RevenueNormNo');
        }else{
            $data->RevenueNormNo = $data->Prefix.$data->RevenueNormNo.$data->Suffix;
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){
        $validate = [];
        $data = RevenueNorm::find($id);
        if($data->RevenueNormNo !== $request->RevenueNormNo){
            $validator = Validator::make($request->all(), [
                'RevenueNormNo' => 'required|unique:revenue_norm'
            ],
                [
                    'RevenueNormNo.required' => 'Mã định mức thu không được để trống',
                    'RevenueNormNo.unique' => 'Mã định mức thu đã tồn tại',
                ]);

            $autoModel = SysAutoNumber::where('NumberKey', 'revenue_norm')->first();
            AutoNo::UpdateValidateNotParentAndChild($validator, $request, $data, 'revenue_norm', $autoModel, 'định mức thu');
            if($validator->fails()){
                $json['status'] = 2;
                $json['msg'] = 'Lỗi!';
                $json['data'] = $validator->errors();
                return $json;
            }
            $data->RevenueNormNo = $request->RevenueNormNo;
        }
        $data->RevenueNormName = $request->RevenueNormName;
        $data->Note = $request->Note;

        $data->save();

        // revenue_norm cate
        if ($request->has('RevenueNormCate')) {
            $RevenueNormCate = $request->post('RevenueNormCate');
            DB::table('revenue_norm_cate')->where('RevenueNormID', $id)->delete();
            $arrInsertCate = [];
            foreach ($RevenueNormCate as $key => $cate) {
                if ($cate['CateID']) {
                    $arrInsertCate[] = [
                        'RevenueNormID' => $id,
                        'CateID' => ($cate['CateID']) ? $cate['CateID'] : null,
                        'CateValue' => ($cate['CateValue']) ? $cate['CateValue'] : null
                    ];
                }
            }

            if (count($arrInsertCate)) {
                DB::table('revenue_norm_cate')->insert($arrInsertCate);
            }

        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = null;

        return json_encode($json);
    }

    public function getCate(Request $request){
        $revenue_normCateList = RevenueNormCateList::where('Inactive', '=', 0)->get();
        $revenue_normCateValue = RevenueNormCateValue::all();
        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['RevenueNormCateList'] = $revenue_normCateList;
        $json['data']['RevenueNormCateValue'] = $revenue_normCateValue;
        return json_encode($json);
    }

    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = RevenueNorm::search($request);
//        $RevenueNorm = RevenueNorm::select('RevenueNormID', 'RevenueNormName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function delete($id){
        $RevenueNorm = RevenueNorm::find($id);
        $auth = Auth::user();
        if($RevenueNorm){
            if ($auth->UserType === 1) {
                $RevenueNorm->delete();
                RevenueNormCate::where('RevenueNormID', $id)->delete();

                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }else{
                $RevenueNorm->delete();
                RevenueNormCate::where('RevenueNormID', $id)->delete();
                $json['status'] = 1;
                $json['msg'] = 'Thành công!';
                $json['data'] = [];
                return json_encode($json);
            }
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public function deleteArray(Request $request){
        $auth = Auth::user();
        $idArray = $request->post('array_id');
        if ($auth->UserType === 1) {
            RevenueNorm::destroy($idArray);
            RevenueNormCate::whereIn('RevenueNormID', $idArray)->delete();

            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);
        }else{
            RevenueNorm::destroy($idArray);
            RevenueNormCate::whereIn('RevenueNormID', $idArray)->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = [];
            return json_encode($json);

        }
    }

    public function getTableItem(Request $request){
        $data = RevenueNormTableItem::whereIn('RevenueNormTableID',$request->post('RevenueNormTableID'))->get();
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }
}
