<?php

namespace Module\Listing\Controllers;

use IjCore\IjDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Module\Listing\Models\NormAllotLevel;
use Module\Listing\Models\NormLevelCateList;
use Module\Listing\Models\NormLevelCateValue;
use Illuminate\Support\Facades\Validator;

class NormAllotLevelController extends Controller
{
    //
    public function list(Request $request){
        $data = NormAllotLevel::search($request);

        $json['status'] = 1;
        $json['msg'] = 'Thành công!';
        $json['data'] = $data;
        return json_encode($json);

    }

    public function create(){
        $json['data'] = [];
        $json['status'] = 1;
        $json['msg'] = "Thành công";
        return json_encode($json);
    }
    public function store(Request $request){
        $master = (object)$request->master;
        $detail = $request->post('detail');
        $level = 1;
        if(!empty($master->ParentID)){
            $parent = NormAllotLevel::find($master->ParentID);
            $parent->Detail = 0;
            $parent->save();
            $level = $parent->Level + 1;
        }

        $normAllotLevel = new NormAllotLevel([
            'NormAllotLevelNo' => $master->NormAllotLevelNo,
            'NormAllotLevelName' => $master->NormAllotLevelName,
            'NormAllotLevelType' => $master->NormAllotLevelType,
            'ParentID' => $master->ParentID,
            'ParentNo' => $master->ParentNo,
            'ParentName' => $master->ParentName,
            'Level' => $level,
            'Detail' => 1,
            'NormID' => $master->NormID,
            'NormNo' => $master->NormNo,
            'NormName' => $master->NormName,
            'EffectiveDate' => ($master->EffectiveDate) ? IjDate::ConvertClientDateToServerDate($master->EffectiveDate) : null,
            'ExpirationDate' => ($master->ExpirationDate) ? IjDate::ConvertClientDateToServerDate($master->ExpirationDate) : null,
            'UomID' => $master->UomID,
            'UomNo' => $master->UomNo,
            'UomName' => $master->UomName,
            'FCUnitPrice' => $master->FCUnitPrice,
            'LCUnitPrice' => $master->LCUnitPrice
        ]);
        $normAllotLevel->save();
        $normAllot = $master->NormAllot;
        if (count($normAllot)) {
            $arrInsertMap = [];
            foreach ($normAllot as $map) {
                $arrInsertMap[] = [
                    'NormAllotLevelID' => $normAllotLevel->NormAllotLevelID,
                    'NormAllotLevelNo' => $normAllotLevel->NormAllotLevelNo,
                    'NormAllotLevelName' => $normAllotLevel->NormAllotLevelName,
                    'NormAllotID' => $map['NormAllotID'],
                    'NormAllotNo' => $map['NormAllotNo'],
                    'NormAllotName' => $map['NormAllotName'],
                    'NormID' => $map['NormID'],
                    'NormNo' => $map['NormNo'],
                    'NormName' => $map['NormName']
                ];
            }
            DB::table('norm_allot_level_map')->insert($arrInsertMap);
        }

        if (count($detail)) {
            $arrInsertItem = [];
            foreach ($detail as $item) {
                $arrInsertItem[] = [
                    'NormAllotLevelID' => $normAllotLevel->NormAllotLevelID,
                    'NormAllotLevelNo' => $normAllotLevel->NormAllotLevelNo,
                    'NormAllotLevelName' => $normAllotLevel->NormAllotLevelName,
                    'EffectiveDate' => ($item['EffectiveDate']) ? IjDate::ConvertClientDateToServerDate($item['EffectiveDate']) : null,
                    'ExpirationDate' => ($item['ExpirationDate']) ? IjDate::ConvertClientDateToServerDate($item['ExpirationDate']) : null,
                    'UomID' => $item['UomID'],
                    'UomNo' => $item['UomNo'],
                    'UomName' => $item['UomName'],
                    'CcyID' => $item['CcyID'],
                    'CcyNo' => $item['CcyNo'],
                    'CcyName' => $item['CcyName'],
                    'ExchangeRate' => $item['ExchangeRate'],
                    'FCMinUnitPrice' => $item['FCMinUnitPrice'],
                    'LCMinUnitPrice' => $item['LCMinUnitPrice'],
                    'FCMaxUnitPrice' => $item['FCMaxUnitPrice'],
                    'LCMaxUnitPrice' => $item['LCMaxUnitPrice'],
                    'UnitPriceType' => $item['UnitPriceType'],
                    'FCUnitPrice' => $item['FCUnitPrice'],
                    'LCUnitPrice' => $item['LCUnitPrice']
                ];
            }

            DB::table('norm_allot_level_item')->insert($arrInsertItem);
        }

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data'] = $normAllotLevel->NormAllotLevelID;
        return json_encode($json);
    }
    public function view($id){
        $data = NormAllotLevel::find($id);
        $normAllotLevelMap = DB::table('norm_allot_level_map')->where('NormAllotLevelID', $id)->get();
        $normAllotLevelItem = DB::table('norm_allot_level_item')->where('NormAllotLevelID', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['NormAllotLevelMap'] = $normAllotLevelMap;
        $json['data']['NormAllotLevelItem'] = $normAllotLevelItem;

        return json_encode($json);
    }
    public function edit($id){
        $data = NormAllotLevel::find($id);
        $normAllotLevelMap = DB::table('norm_allot_level_map')->where('NormAllotLevelID', $id)->get();
        $normAllotLevelItem = DB::table('norm_allot_level_item')->where('NormAllotLevelID', $id)->get();

        $json['status'] = 1;
        $json['msg'] = 'Thành công';
        $json['data']['data'] = $data;
        $json['data']['NormAllotLevelMap'] = $normAllotLevelMap;
        $json['data']['NormAllotLevelItem'] = $normAllotLevelItem;

        return json_encode($json);
    }

    public function update(Request $request, $id){
        $json = [
            'status' => 0,
            'msg' => '',
            'data' => null
        ];
        $master = (object)$request->master;
        $detail = $request->post('detail');
        // set for newParent
        $normAllotLevel = NormAllotLevel::find($id);
        if($master->ParentID){
            $Parent = NormAllotLevel::find($master->ParentID);
            $Parent->Detail = 0;
            $normAllotLevel->Level = $Parent->Level + 1;
            $Parent->save();
        }

        // set for old Parent
        if($normAllotLevel->ParentID != $master->ParentID && $normAllotLevel->ParentID){
            $ParentOld = NormAllotLevel::find($normAllotLevel->ParentID);
            $count = NormAllotLevel::where('ParentID', $ParentOld->NormAllotLevelID)->count();
            if($count < 2){
                $ParentOld->Detail = 1;
                $ParentOld->save();
            }
        }

        $normAllotLevel->NormAllotLevelNo = $master->NormAllotLevelNo;
        $normAllotLevel->NormAllotLevelName = $master->NormAllotLevelName;
        $normAllotLevel->NormAllotLevelType = $master->NormAllotLevelType;
        $normAllotLevel->ParentID = $master->ParentID;
        $normAllotLevel->ParentNo = $master->ParentNo;
        $normAllotLevel->ParentName = $master->ParentName;
        $normAllotLevel->NormID = $master->NormID;
        $normAllotLevel->NormNo = $master->NormNo;
        $normAllotLevel->NormName = $master->NormName;
        $normAllotLevel->EffectiveDate = ($master->EffectiveDate) ? IjDate::ConvertClientDateToServerDate($master->EffectiveDate) : null;
        $normAllotLevel->ExpirationDate = ($master->ExpirationDate) ? IjDate::ConvertClientDateToServerDate($master->ExpirationDate) : null;
        $normAllotLevel->UomID = $master->UomID;
        $normAllotLevel->UomNo = $master->UomNo;
        $normAllotLevel->UomName = $master->UomName;
        $normAllotLevel->FCUnitPrice = $master->FCUnitPrice;
        $normAllotLevel->LCUnitPrice = $master->LCUnitPrice;

        $normAllotLevel->save();

        DB::beginTransaction();
        try {
            DB::table('norm_allot_level_map')->where('NormAllotLevelID', $id)->delete();
            $normAllot = $master->NormAllot;
            if (count($normAllot)) {
                $arrInsert = [];
                foreach ($normAllot as $map) {
                    $arrInsert[] = [
                        'NormAllotLevelID' => $normAllotLevel->NormAllotLevelID,
                        'NormAllotLevelNo' => $normAllotLevel->NormAllotLevelNo,
                        'NormAllotLevelName' => $normAllotLevel->NormAllotLevelName,
                        'NormAllotID' => $map['NormAllotID'],
                        'NormAllotNo' => $map['NormAllotNo'],
                        'NormAllotName' => $map['NormAllotName'],
                        'NormID' => $map['NormID'],
                        'NormNo' => $map['NormNo'],
                        'NormName' => $map['NormName']
                    ];
                }
                DB::table('norm_allot_level_map')->insert($arrInsert);
            }

            DB::table('norm_allot_level_item')->where('NormAllotLevelID', $id)->delete();
            if (count($detail)) {
                $arrInsertItem = [];
                foreach ($detail as $item) {
                    $arrInsertItem[] = [
                        'NormAllotLevelID' => $normAllotLevel->NormAllotLevelID,
                        'NormAllotLevelNo' => $normAllotLevel->NormAllotLevelNo,
                        'NormAllotLevelName' => $normAllotLevel->NormAllotLevelName,
                        'EffectiveDate' => ($item['EffectiveDate']) ? IjDate::ConvertClientDateToServerDate($item['EffectiveDate']) : null,
                        'ExpirationDate' => ($item['ExpirationDate']) ? IjDate::ConvertClientDateToServerDate($item['ExpirationDate']) : null,
                        'UomID' => $item['UomID'],
                        'UomNo' => $item['UomNo'],
                        'UomName' => $item['UomName'],
                        'CcyID' => $item['CcyID'],
                        'CcyNo' => $item['CcyNo'],
                        'CcyName' => $item['CcyName'],
                        'ExchangeRate' => $item['ExchangeRate'],
                        'FCMinUnitPrice' => $item['FCMinUnitPrice'],
                        'LCMinUnitPrice' => $item['LCMinUnitPrice'],
                        'FCMaxUnitPrice' => $item['FCMaxUnitPrice'],
                        'LCMaxUnitPrice' => $item['LCMaxUnitPrice'],
                        'UnitPriceType' => $item['UnitPriceType'],
                        'FCUnitPrice' => $item['FCUnitPrice'],
                        'LCUnitPrice' => $item['LCUnitPrice']
                    ];
                }
                DB::table('norm_allot_level_item')->insert($arrInsertItem);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $json['msg'] = $e->getMessage();
            throw new Exception($e->getMessage());
        }


        $json['status'] = 1;
        $json['data'] = $normAllotLevel->NormAllotLevelID;
        return json_encode($json);
    }

    public function delete($id){
        $data = NormAllotLevel::find($id);
        DB::table('norm_allot_level_map')->where('NormAllotLevelID', $id)->delete();
        DB::table('norm_allot_level_item')->where('NormAllotLevelID', $id)->delete();

        if($data->Detail == 0){
            $json['status'] = 4;
            $json['msg'] = 'Thất bại';
            $json['data'] = [];
            return json_encode($json);
        }

        if($data){
            // set detail Parent
            if($data->ParentID){
                $count = DB::table('norm_allot_level')->where('ParentID', '=', $data->ParentID)->count();
                if($count < 2){
                    $Parent = NormAllotLevel::find($data->ParentID);
                    $Parent->Detail = 1;
                    $Parent->save();
                }
            }
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
        $normAllotLevel =  DB::table('norm_allot_level')->whereIn('NormAllotLevelID', $IDs)->get();
        foreach ($normAllotLevel as $item){
            if($item->Detail == 0){
                $json['status'] = 4;
                $json['msg'] = 'Thất bại';
                $json['data'] = [];
                return json_encode($json);
            }
        }
        if(!empty($IDs)){
            DB::table('norm_allot_level_map')->whereIn('NormAllotLevelID', $IDs)->delete();
            DB::table('norm_allot_level_item')->whereIn('NormAllotLevelID', $IDs)->delete();

            NormAllotLevel::destroy($IDs);
            $sql = "UPDATE norm_allot_level
                SET Detail = 1
                WHERE NormAllotLevelID NOT IN (SELECT ParentID FROM (select ParentID from norm_allot_level WHERE ParentID is not null) t)";
            DB::statement($sql);
            $json['status'] = 1;
            $json['msg'] = 'Thành công';
            $json['data'] = [];
            return json_encode($json);
        }
    }
    public  function  getListChild(Request $request){
        $ParentID = $request->ParentID;
        $query = NormAllotLevel::query();
        $query->where('ParentID', $ParentID);
        $query->groupBy('NormAllotLevelID');
        $data = $query->orderBy('NormAllotLevelID', 'desc')->get();
        $json['status'] = 1;
        $json['msg'] = 'thành công';
        $json['data'] = $data;
        return json_encode($json);
    }
}
