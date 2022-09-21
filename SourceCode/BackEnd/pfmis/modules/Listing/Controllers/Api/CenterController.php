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
use Module\Listing\Models\Ccy;
use Module\Listing\Models\Company;
use Module\Listing\Models\FileModel;
use Module\Listing\Models\Center;
use Module\Listing\Models\province;
use Module\Listing\Models\SysAutoNumber;

use Module\Listing\Models\Employee;
use Module\Listing\Models\CenterCate;
use Module\Listing\Models\CenterCateList;
use Module\Listing\Models\CenterCateValue;
use Module\Listing\Models\CenterFile;
use Module\Listing\Models\CenterPer;
use Module\Listing\Models\CenterVideo;
use Module\SysAdmin\Models\SysUserGroup;
use Module\Listing\Models\CenterLink;
use function GuzzleHttp\Psr7\_parse_request_uri;

class CenterController extends Controller{

    public function list(Request $request){
        $data = Center::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function export(Request $request){
        return Excel::download(new ModelExport('center', ['select' => ['CenterNo', 'CenterName'], 'conditions' => []]), 'nganh.xlsx');
    }

    public function create(){
        if(old('CenterNo')){
            $auto = old('CenterNo');
        }else{
            $auto = AutoNo::genNewNumberNo('center');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'CenterNo' => 'required',
            'CenterName'=> 'required',
        ],
            [
                'CenterNo.required' => 'Mã trung ương không được để trống',
                'CenterName.required' => 'Tên trung ương không được để trống',
            ]
        );
       /* $autoModel = SysAutoNumber::where('NumberKey', 'center')->first();
        AutoNo::storeValidate($validator, $request, 'center', $autoModel, 'ngành');*/
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }


        $center = new Center([
            'CenterNo' => $request->CenterNo,
            'CenterName' => $request->CenterName,
            'CenterAddress' => $request->CenterAddress,
            'CenterTel' => $request->CenterTel,
            'CenterHandPhone' => $request->CenterHandPhone,
            'CenterFax' => $request->CenterFax,
            'CenterEmail' => $request->CenterEmail,
            'CenterWebsite' => $request->CenterWebsite,
            'ContactName' => $request->ContactName,
            'ContactTitle' => $request->ContactTitle,
            'ContactTel' => $request->ContactTel,
            'ContactHandPhone' => $request->ContactHandPhone,
            'ContactEmail' => $request->ContactEmail,
            'Note' => $request->Note,
            'CenterType' => $request->CenterType,
        ]);

        $center->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $center->CenterID;

        return json_encode($json);
    }

    public function view($id){
        $data = Center::find($id);

        if($data){
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data']['data'] = $data;

            return json_encode($json);
        }else{

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }
    }

    public function edit($id){
        $data = Center::find($id);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['data'] = $data;
        return json_encode($json);
    }


    public function update(Request $request, $id){

        $center = Center::find($id);
        if(!$center){

            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = [];
            return json_encode($json);
        }

        $center->CenterName = $request->CenterName;
        $center->CenterNo = $request->CenterNo;
        $center->CenterAddress = $request->CenterAddress;
        $center->CenterTel = $request->CenterTel;
        $center->CenterHandPhone = $request->CenterHandPhone;
        $center->CenterFax = $request->CenterFax;
        $center->CenterEmail = $request->CenterEmail;
        $center->CenterWebsite = $request->CenterWebsite;
        $center->ContactName = $request->ContactName;
        $center->ContactTitle = $request->ContactTitle;
        $center->ContactTel = $request->ContactTel;
        $center->ContactHandPhone = $request->ContactHandPhone;
        $center->ContactEmail = $request->ContactEmail;
        $center->Note = $request->Note;
        $center->CenterType = $request->CenterType;
        $center->save();

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return $json;

    }

    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        Center::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function delete($id){
        $data = Center::find($id);
        if($data){
            $data->delete();
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = Center::search($request);
//        $Uom = Uom::select('UomID', 'UomName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
