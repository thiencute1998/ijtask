<?php
namespace Module\Listing\Controllers;

use App\Events\RedisEvent;
use App\Exports\ModelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Module\Listing\Models\AutoNo;
use Module\Listing\Models\Treasury;
use Module\Listing\Models\TreasuryLink;

class TreasuryController extends Controller
{

    public function list(Request $request)
    {
        $data = Treasury::search($request);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);
    }

    public function create(){
        if(old('TreasuryNo')){
            $auto = old('TreasuryNo');
        }else{
            $auto = AutoNo::genNewNumberNo('treasury');
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
            'TreasuryNo' => 'required',
            'TreasuryName'=> 'required',
        ],
            [
                'TreasuryNo.required' => 'Mã kho bạc không được để trống',
                'TreasuryName.required' => 'Tên kho bạc không được để trống',
            ]
        );
        if($validator->fails()){
            $json['status'] = 2;
            $json['msg'] = 'Lỗi!';
            $json['data'] = $validator->errors();
            return $json;
        }

        $treasury = new Treasury([
            'TreasuryNo' => $master->TreasuryNo,
            'TreasuryName' => $master->TreasuryName,
            'TreasuryAddress' => $master->TreasuryAddress,
            'BudgetLevel' => $master->BudgetLevel,
            'ProvinceName' => $request->ProvinceName,
            'DistrictName' => $request->DistrictName,
            'Note' => $master->Note,
            'ProvinceID' => $request->ProvinceID,
            'DistrictID' => $request->DistrictID
        ]);

        $treasury->save();

        $arrInsert = [];
        foreach ($detail as $item){
            $item = (object) $item;
            $linkNo = $item->LinkNo;
            $linkName = $item->LinkName;
            $linkTable = $item->LinkTable;

            $arrInsert[] = [
                'TreasuryID' => $treasury->TreasuryID,
                'LinkNo' => $item->LinkNo,
                'LinkName' => $item->LinkName,
                'LinkTable' => $item->LinkTable
            ];
        }
        if(!empty($arrInsert)){
            DB::table('treasury_link')->insert($arrInsert);
        }
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $treasury->TreasuryID;

        return json_encode($json);
    }

    public function view($id){
        $treasury = Treasury::all();
        $treasuryLink = TreasuryLink::where('TreasuryID', $id)->get();
        $data = Treasury::find($id);


        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['Treasury'] = $treasury;
        $json['data']['TreasuryLink'] = $treasuryLink;

        return json_encode($json);
    }

    public function delete($id){
        TreasuryLink::where('TreasuryID', $id)->delete();
        $data = Treasury::find($id);
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
        TreasuryLink::whereIn('TreasuryID', $IDs)->delete();
        Treasury::destroy($IDs);
        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = [];
        return json_encode($json);
    }

    public function edit($id){
        $treasury = Treasury::all();
        $treasuryLink = TreasuryLink::where('TreasuryID', $id)->get();
        $data = Treasury::find($id);
        $json['status'] = 1;
        $json['msg'] = 'Thành Công!';
        $json['data']['data'] = $data;
        $json['data']['TreasuryLink'] = $treasuryLink;
        $json['data']['Treasury'] = $treasury;

        return json_encode($json);
    }

    public function update(Request $request, $id){

        $master = $request->master;
        $master = (object) $master;

        $treasury = Treasury::find($id);

        $treasury->TreasuryNo = $master->TreasuryNo;
        $treasury->BudgetLevel = $master->BudgetLevel;
        $treasury->TreasuryName = $master->TreasuryName;
        $treasury->TreasuryAddress = $master->TreasuryAddress;
        $treasury->ProvinceName = $request->ProvinceName ? $request->ProvinceName : '';
        $treasury->DistrictName = $request->DistrictName ? $request->DistrictName : '';
        $treasury->Note = $master->Note;
        $treasury->ProvinceID = $request->ProvinceID ? $request->ProvinceID : 0;
        $treasury->DistrictID = $request->DistrictID ? $request->DistrictID : 0;

        $treasury->save();

        TreasuryLink::where('TreasuryID', $id)->delete();
        $arrInsert = [];
        $detail = $request->detail;
        foreach ($detail as $item){
            $item = (object) $item;

            $arrInsert[] = [
                'TreasuryID' => $treasury->TreasuryID,
                'LinkNo' => $item->LinkNo,
                'LinkName' => $item->LinkName,
                'LinkTable' => $item->LinkTable,
            ];
        }

        if(!empty($arrInsert)){
            DB::table('treasury_link')->insert($arrInsert);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $id;

        return json_encode($json);
    }
    public function getTreOrg(Request  $request){
        $query = Treasury::query();
        if($request->post('ManagementLevel')){
            if($request->post('ManagementLevel') == 4){
                $json['status'] = 4;
                $json['msg'] = 'thất bại';
                $json['data'] = [];
            }
            $query->where('BudgetLevel', '=', $request->post('ManagementLevel'));
        }
        if($request->post('ProvinceID')){
            $query->where('ProvinceID', '=', $request->post('ProvinceID'));
        }
        $data = $query->first();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
