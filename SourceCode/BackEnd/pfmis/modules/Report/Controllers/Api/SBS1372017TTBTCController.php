<?php
namespace Module\Report\Controllers;

use Module\Listing\Models\Vendor;
// namespace App\Http\Controllers;
// use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class SBS1372017TTBTCController extends Controller{
    public function SBS1372017TTBTC_PL01(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = Vendor::select('VendorID', 'VendorName', 'OfficePhone', 'Fax', 'Email')->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function RCETT107B04BCTC(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        // $reponseData = [
        //     'A' => [],
        //     'B' => [],
        //     'C' => [],
        //     'D' => [],
        //     'E' => [],
        // ];
        $data = DB::table("report_rce_107b04bctc")->get();
        // foreach($data as $receivedata){
        //     switch($receivedata->Type){
        //         case '1': {
        //            $reponseData['A'][]=$receivedata;
        //         }
        //     }
        // }

        $json['data'] = $data;
        return json_encode($json);
    }
    public function RCETT107B01BCTC(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_rce_107b01bctc")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function RCETT107B02BCTC(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_rce_107b02bctc")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function RCETT107B03aCTC(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_rce_107b03abctc")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function RCETT107B03bCTC(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_rce_107b03bbctc")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    ///liên
    public function RCETT252017BCTC2KQKD(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_252017bctc_2kqkd")->get();
        $json['data'] = $data;
        return json_encode($json);
    }


    public function RCETT252017BCTCCDKT(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_252017bctc_cdkt")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function RCETT252017BCTCLCTT(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_252017bctc_lctt")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function RCETT252017BCTCSocai(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_252017bctc_lctt")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function RCETT252017BCTCUynhiemchi(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_252017bctc_lctt")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT1372017TTBTCMB02(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb02")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT1372017TTBTCMB2a(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb2a")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT1372017TTBTCMB2b(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb2b")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
   // Lien test
    public function RCETT252017BCTCtest(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_252017bctc_test")->orderBy('ItemID', 'ASC')->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function SBSTT992018TTBTCB04BCTCTH(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_992018ttbtc_b04bctcth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT992018TTBTCS01BTH(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_992018ttbtc_s01bth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT992018TTBTCS02BTH(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_992018ttbtc_s02bth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPTT3432016TTBTCBS69(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_tt-3432016ttbtc_bs69")->orderBy('ItemID', 'ASC')->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPTT3432016TTBTCBS70(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_tt-3432016ttbtc_bs70")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPTT3432016TTBTCBS71(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_tt-3432016ttbtc_bs71")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPTT3432016TTBTCBS72(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_tt-3432016ttbtc_bs72")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPTT3432016TTBTCBS73(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_tt-3432016ttbtc_bs73")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPTT3432016TTBTCBS74(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_tt-3432016ttbtc_bs74")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPTT3432016TTBTCBS75(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_tt-3432016ttbtc_bs75")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPTT3432016TTBTCBS76()
    {

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_tt-3432016ttbtc_bs76")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    //uth

    public function SBPUTHDMCSDMDV01(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_dmdv_dmcs")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHDMCSDMDV02(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_dmdv_ccbvth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHSOKHDT01(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_sokhdt_dmdv")->get();
        $json['data'] = $data;
        return json_encode($json);

    }
    public function SBPUTHSOKHDT02(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_sokhdt_uth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHSOKHDT03(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_sokhdt_lapdt")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHSOKHDT04(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_sokhdt_xemxet")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHSOKHDT05(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_sokhdt_pheduyet")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHSOKHDT06(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_sokhdt_giao")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHSOKHDT07(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_sokhdt_cap")->get();
        $json['data'] = $data;
        return json_encode($json);
    }


    //thanh

    public function SBSTT1372017TTBTCPL01(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_pl01")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT1372017TTBTCPL02(){


        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_pl02")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT1372017TTBTCPL03(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_pl03")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT1372017TTBTCMB01(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb01")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT1372017TTBTCMB1a(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb1a")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT1372017TTBTCMB1b(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb1b")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT1372017TTBTCMB1c(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb1c")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT1372017TTBTCMB2c(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb2c")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT992018TTBTCB01BCTCTH(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_992018ttbtc_b01bctcth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT992018TTBTCB01BSTT(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_992018ttbtc_b01bstt")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT992018TTBTCB02BCTCTH(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_992018ttbtc_b02bctcth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT992018TTBTCB03BCTCTH(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_992018ttbtc_b03bctcth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    //----------------------nghị định 31----------------------------------
        //Thanh
    public function SBPND312017NDCPBM01(){

            $json = [
                'status' => 1,
                'msg' => '',
                'data' => null
            ];
            $data = DB::table("report_sbp_312017ndcp_bm01")->get();
            $json['data'] = $data;
            return json_encode($json);
    }
    public function SBPND312017NDCPBM02(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm02")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM03(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm03")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM04(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm04")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM08(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm08")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM09(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm09")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM10(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm10")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
        //Dũng
    public function SBPND312017NDCPBM21(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm21")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM22(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm22")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM23(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm23")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM24(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm24")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM25(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm25")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM26(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm26")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM27(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm27")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM28(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm28")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPND312017NDCPBM29(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_312017ndcp_bm29")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    //------------------------------------------------------------------------------------
    //-------------------------------------DEMO-------------------------------------------
                    //---------------------dung------------------------
    public function DemoBc1(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc1")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc2(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc2")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc3(){

    $json = [
        'status' => 1,
        'msg' => '',
        'data' => null
    ];
    $data = DB::table("report_dung_demo_bc3")->get();
    $json['data'] = $data;
    return json_encode($json);
    }
    public function DemoBc4(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc4")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc5(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc5")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc6(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc6")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc14(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc4")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc17(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc15")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc18(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc16")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc18a(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc16a")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc18b(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc16b")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc18c(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc16c")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc19(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc17")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc19a(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dung_demo_bc17b")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemoBc22(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_DuLieuDemo_LAPDT")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    //--------------------------------------END-------------------------------------------
    //dũng
    public function SBSTT1372017TTBTCMB3b(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb3b")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBSTT1372017TTBTCMB4b(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb4b")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPTT3422016TTBTCMB23(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb4b")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    //342
    public function SBPTT342TTBTCdutoanchidautu(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_du_toan_2021_chidautu")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DongNai(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_du_toan_2021_chidautu")->get();
        $json['data'] = $data;
        return json_encode($json);
    }



    //Tuấn
    public function SBSTT3442016TTBTCMBS07(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_3442016ttbtc_mbs07")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    //================================Demo-Thanh========================================
    public function bang1(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_thanh_demo_table1")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function bang2(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_thanh_demo_table2")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function bang3(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_thanh_demo_table3")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function bang4(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_thanh_demo_table4")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function bang5(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_thanh_demo_table5")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function bang6(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_thanh_demo_table6")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function QDGiaoBoSungDuToanNguonSuNghiep(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_thanh_demo_table6")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function QDGiaoDuToanNguonSuNghiep(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_thanh_demo_table6")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function QDGiaoDuToanNguonQLNN(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_thanh_demo_table6")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function QDPhanBoDuToanVPUBNDTinh(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_thanh_demo_table6")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function ThuyetMinhDuToan2021BanHanh309(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_thanh_demo_table6")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    //Demo làm ngày 15/3
//    public function Demo05TH(){
//
//        $json = [
//            'status' => 1,
//            'msg' => '',
//            'data' => null
//        ];
//        $data = DB::table("report_05-th")->get();
//        $json['data'] = $data;
//        return json_encode($json);
//    }
    public function SBP3422016TTBTC_MB05(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbp_3422016ttbtc_mb05")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function Demo07thu(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_07-thu")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function Demo138cshdkt(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_13-8-cshdkt")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function Demo14QLNN(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_14-qlnn")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function Demo262020CVCCBVTV(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_14-qlnn")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function ThuyetMinhDuToan2021CCTTbVTV(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_14-qlnn")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    //=============== Ước thực hiện làm ngày 29/3===========================
    public function SBPUTHVPSoNN01(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_vpsonn_uocth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHVPSoNN02(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_vpsonn_lapdt")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHVPSoNN03(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_vpsonn_xemxet")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHVPSoNN04(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_vpsonn_pheduyet")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHVPSoNN05(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_vpsonn_giao")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHVPSoNN06(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_vpsonn_cap")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    //====================== CV-STC gửi đơn vị xem xét dự toán ===============

    public function CVSTCguicacdonvixemxetdutoan(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_vpsonn_cap")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function CVSTCguicacdonvixemxetdutoanvong2(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_uth_vpsonn_cap")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    //================Demo huyện Vĩnh Thạnh =======================
    public function Giao(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemohuyenvinhthanh_giao")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function UocTH(){

        $json = [
            'status' => 1,
            'msg' => '',
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemohuyenvinhthanh_uocth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    //=============================  END Demo Thanh ==============================

    //--------------------------------------
    public function SBSTT3442016TTBTCMBS08(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_3442016ttbtc_mbs08")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function SBSTT3442016TTBTCMBS09(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_3442016ttbtc_mbs09")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function SBSTT3442016TTBTCMBS10(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_3442016ttbtc_mbs10")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function SBSTT3442016TTBTCMBS11(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_3442016ttbtc_mbs11")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function SBSTT3442016TTBTCMBS12(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_3442016ttbtc_mbs12")->get();
        $json['data'] = $data;
        return json_encode($json);
    }


    public function SBSTT1372017TTBTCMB03(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb03")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function SBSTT1372017TTBTCMB3a(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb3a")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function SBSTT1372017TTBTCMB4a(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_sbs_1372017ttbtc_mb4a")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function test55UBNDQ3(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_q3_2015_ms21")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function test55UBNDQ3MS21(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_q3_2015_ms21")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function test55UBNDQ3MS22(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_q3_2015_ms22")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function test55UBNDQ3MS23(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_q3_2015_ms23")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function test55UBNDQ3MS24(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_q3_2015_ms24")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function test55UBNDQ3MS25(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_q3_2015_ms25")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function test55UBNDQ3MS26(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_q3_2015_ms26")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function test55UBNDQ3MS29(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_q3_2015_ms29")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function test55UBNDQ3MS30(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_q3_2015_ms30")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function test55UBNDQ3MS31(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_q3_2015_ms31")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPPB01(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_phan_bo_01")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function SBPPB02(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_phan_bo_01")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    //tuan demo
    public function ReportTuandemo6(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_tuan_demo_tb1")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function ReportTuandemo7(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_tuan_demo_tb2")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function ReportTuandemo8(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_tuan_demo_tb3")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function ReportTuandemo9(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_tuan_demo_tb4")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function ReportTuandemo10(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_tuan_demo_tb5")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function ReportTuandemo11(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_tuan_demo_tb6")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function ReportTuandemo12(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_tuan_demo_tb7")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function ReportTuandemo13(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_tuan_demo_tb8")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHDemomoduldutoan00(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_duy_tt_bvtt_00")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHDemomoduldutoan01(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_duy_tt_bvtt_01")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHDemomoduldutoan02(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_duy_tt_bvtt_02")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHDemomoduldutoan03(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_duy_tt_bvtt_03")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHDemomoduldutoan04(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_duy_tt_bvtt_04")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPUTHDemomoduldutoan05(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_duy_tt_bvtt_05")->get();
        $json['data'] = $data;
        return json_encode($json);
    }


    //Tuan - Phe duyet
    public function SBPPDQDUBCboDTNS01(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_qdub_bs52")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPPDQDUBCboDTNS02(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_qdub_bs53")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPPDQDUBCboDTNS03(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_qdub_bs54")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPPDQDUBCboDTNS04(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_qdub_bs55")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function SBPPDQDUBCboDTNS05(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_qdub_bs56")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function dutoan300901(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_du_toan_3009_01")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function dutoan300902(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_du_toan_3009_02")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function dutoan300903(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_du_toan_3009_03")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function dutoan300904(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_du_toan_3009_04")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemomodulKHDT00(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_modul_du_toan_khdt_00")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemomodulKHDT01(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_modul_du_toan_khdt_01")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemomodulKHDT02(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_modul_du_toan_khdt_02")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemomodulKHDT03(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_modul_du_toan_khdt_03")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemomodulKHDT04(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_modul_du_toan_khdt_04")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemomodulKHDT05(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_modul_du_toan_khdt_05")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function DemomodulKHDT06(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_modul_du_toan_khdt_06")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function xemxet(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemohuyenvinhthanh_xem_xet")->get();
        $json['data'] = $data;
        return json_encode($json);
    }

    public function DULIEUDEMOVINHTHANHPHEDUYET(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemovinhthanh_pheduyet")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    //Tuan
    public function UTH(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemovinhthanh_uth")->get();
        $json['data'] = $data;
        return json_encode($json);
    } //Tuan

    public function ThuyetMinh(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemovinhthanh_uth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    //test xdebug
    public function sbp_tt3422016ttbtc_mb04(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_tt342_b4")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    // Làm ngày 15/5 ===================================
    public function PFMISDUTOANCVSTCXEMXETVONG1(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemovinhthanh_uth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function PFMISDUTOANCVSTCXEMXETVONG2(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemovinhthanh_uth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function PFMISDUTOANCVTTĐVDTCQTCCV(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemovinhthanh_uth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function PFMISDUTOANCVTTĐVDTCQTCTTr(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemovinhthanh_uth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function PFMISDUTOANTOTRINHSTCUBNDtinh(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemovinhthanh_uth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function PFMISDUTOANCVĐVSTC(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemovinhthanh_uth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function PFMISDTNSNNCHUNGTUGIAODTMS01(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemovinhthanh_uth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function PFMISDTNSNNCHUNGTUGIAODTMS02(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemovinhthanh_uth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
    public function PFMISDTNSNNCHUNGTUGIAODTMS03(){

        $json = [
            'status' => 1,
            'msg' => '',
            'data' => null
        ];
        $data = DB::table("report_dulieudemovinhthanh_uth")->get();
        $json['data'] = $data;
        return json_encode($json);
    }
}
