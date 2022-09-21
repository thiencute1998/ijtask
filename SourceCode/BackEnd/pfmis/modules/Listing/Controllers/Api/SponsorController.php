<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Sponsor;
use Module\Listing\Models\SponsorLink;

class SponsorController extends Controller
{

    public function list(Request $request)
    {
        $data = Sponsor::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function create(){
        if(old('SponsorNo')){
            $auto = old('SponsorNo');
        }else{
            $auto = AutoNo::genNewNumberNo('sponsor');
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data']['auto'] = $auto;
        return json_encode($json);
    }

    public function store(Request $request){
        $master = (object) $request->master;
        $detail = $request->detail;
        $validator = Validator::make($request->master, [
            'SponsorNo' => 'required',
            'SponsorName'=> 'required',
        ],
            [
                'SponsorNo.required' => 'Mã nhà tài trợ không được để trống',
                'SponsorName.required' => 'Tên nhà tài trợ không được để trống',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $sponsor = new Sponsor([
            'SponsorNo' => $master->SponsorNo,
            'SponsorName' => $master->SponsorName,
            'SponsorAddress' => $master->SponsorAddress,
            'SponsorType' => $master->SponsorType,
            'Note' => $master->Note,
            'OfficePhone' => $master->OfficePhone,
            'ProvinceID' => $master->ProvinceID,
            'ProvinceName' => $master->ProvinceName,
        ]);

        $sponsor->save();

        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            $linkNo = $item->LinkNo;
            $linkName = $item->LinkName;
            $linkTable = $item->LinkTable;

            $arrInsert[] = [
                'SponsorID' => $sponsor->SponsorID,
                'LinkNo' => $item->LinkNo,
                'LinkName' => $item->LinkName,
                'LinkTable' => $item->LinkTable
            ];
        }
        if(!empty($arrInsert)){
            DB::table('sponsor_link')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $sponsor->SponsorID;

        return json_encode($json);
    }

    public function view($id){
        $sponsor = Sponsor::all();
        $sponsorLink = SponsorLink::where('SponsorID', $id)->get();
        $data = Sponsor::find($id);


        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['Sponsor'] = $sponsor;
        $json['data']['SponsorLink'] = $sponsorLink;

        return json_encode($json);
    }

    public function delete($id){
        SponsorLink::where('SponsorID', $id)->delete();
        $data = Sponsor::find($id);
        if($data){
            $data->delete();
            $json['status'] = 1;
            $json['msg'] = 'Thành công!';
            $json['data'] = $id;

            return json_encode($json);
        }else{
            $json['status'] = 2;
            $json['msg'] = 'Không tồn tại!';
            $json['data'] = $id;

            return json_encode($json);
        }
    }
    public function deleteArray(Request $request){
        $IDs = $request->post('array_id');
        SponsorLink::whereIn('SponsorID', $IDs)->delete();
        Sponsor::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function edit($id){
        $sponsor = Sponsor::all();
        $sponsorLink = SponsorLink::where('SponsorID', $id)->get();
        $data = Sponsor::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['SponsorLink'] = $sponsorLink;
        $json['data']['Sponsor'] = $sponsor;

        return json_encode($json);
    }

    public function update(Request $request, $id){
        $master = $request->master;
        $master = (object) $master;
        $sponsor = Sponsor::find($id);
        $sponsor->SponsorNo = $master->SponsorNo;
        $sponsor->SponsorType = $master->SponsorType;
        $sponsor->SponsorName = $master->SponsorName;
        $sponsor->SponsorAddress = $master->SponsorAddress;
        $sponsor->Note = $master->Note;
        $sponsor->OfficePhone = $master->OfficePhone;
        $sponsor->ProvinceID = $master->ProvinceID ? $master->ProvinceID : null;
        $sponsor->ProvinceName = $master->ProvinceName ? $master->ProvinceName : '';
        $sponsor->save();

        SponsorLink::where('SponsorID', $id)->delete();
        $arrInsert = [];
        $detail = $request->detail;
        foreach ($detail as $item){
            $item = (object) $item;

            $arrInsert[] = [
                'SponsorID' => $sponsor->SponsorID,
                'LinkNo' => $item->LinkNo,
                'LinkName' => $item->LinkName,
                'LinkTable' => $item->LinkTable,
            ];
        }

        if(!empty($arrInsert)){
            DB::table('sponsor_link')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;

        return json_encode($json);
    }
    public function getReportData(Request $request){
        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = Sponsor::search($request);
//        $Vendor = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data']['detail'] = $data->items();
        $json['data']['total'] = $data->total();
        $json['data']['per_page'] = $data->perPage();
        $json['data']['current_page'] = $data->currentPage();
        return json_encode($json);
    }
}
