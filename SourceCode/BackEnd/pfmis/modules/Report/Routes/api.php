<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware(['api', 'auth:api'])->group(function () {
    // common
    Route::post('/common/get-report-template', CommonController::Class.'@getReportTemplate');
    Route::post('/common/get-level-company', CommonController::Class.'@getLevelCompany');


    // Report
    Route::post('/SBS1372017TTBTC/SBS1372017TTBTC_PL01', SBS1372017TTBTCController::Class.'@SBS1372017TTBTC_PL01');
    Route::post('/RCETT107/RCETT107B04BCTC', SBS1372017TTBTCController::Class.'@RCETT107B04BCTC');
    Route::post('/RCETT107/RCETT107B01BCTC', SBS1372017TTBTCController::Class.'@RCETT107B01BCTC');
    Route::post('/RCETT107/RCETT107B02BCTC', SBS1372017TTBTCController::Class.'@RCETT107B02BCTC');
    Route::post('/RCETT107/RCETT107B03aBCTC', SBS1372017TTBTCController::Class.'@RCETT107B03aCTC');
    Route::post('/RCETT107/RCETT107B03bBCTC', SBS1372017TTBTCController::Class.'@RCETT107B03bCTC');
    //liên
    Route::post('/RCETT252017BCTC/RCETT252017BCTC2KQKD', SBS1372017TTBTCController::Class.'@RCETT252017BCTC2KQKD');
    Route::post('/RCETT252017BCTC/RCETT252017BCTCCDKT', SBS1372017TTBTCController::Class.'@RCETT252017BCTCCDKT');
    Route::post('/RCETT252017BCTC/RCETT252017BCTCLCTT', SBS1372017TTBTCController::Class.'@RCETT252017BCTCLCTT');

    Route::post('/RCETT252017BCTC/RCETT252017BCTCSocai', GeneralLedger\RCETT252017BCTC\RCETT252017BCTCSocaiController::Class.'@RCETT252017BCTCSocai');
    Route::post('/RCETT252017BCTC/RCETT252017BCTCBangCanDoi', GeneralLedger\RCETT252017BCTC\RCETT252017BCTCBangCanDoiController::Class.'@RCETT252017BCTCBangCanDoi');
    Route::post('/RCETT252017BCTC/RCETT252017BCTCUynhiemchi', SBS1372017TTBTCController::Class.'@RCETT252017BCTCUynhiemchi');

    Route::post('/RCETT252017BCTC/RCETT252017BCTCtest', SBS1372017TTBTCController::Class.'@RCETT252017BCTCtest');
    Route::post('/SBPTT3422016TTBTC/SBPTT3422016TTBTCMB23', SBS1372017TTBTCController::Class.'@SBPTT3422016TTBTCMB23');


    Route::post('/SBSTT992018TTBTC/SBSTT992018TTBTCB04BCTCTH', SBS1372017TTBTCController::Class.'@SBSTT992018TTBTCB04BCTCTH');
    Route::post('/SBSTT992018TTBTC/SBSTT992018TTBTCS01BTH', SBS1372017TTBTCController::Class.'@SBSTT992018TTBTCS01BTH');
    Route::post('/SBSTT992018TTBTC/SBSTT992018TTBTCS02BTH', SBS1372017TTBTCController::Class.'@SBSTT992018TTBTCS02BTH');


    Route::post('/SBPTT3432016TTBTC/SBPTT3432016TTBTCBS69', SBS1372017TTBTCController::Class.'@SBPTT3432016TTBTCBS69');
    Route::post('/SBPTT3432016TTBTC/SBPTT3432016TTBTCBS70', SBS1372017TTBTCController::Class.'@SBPTT3432016TTBTCBS70');
    Route::post('/SBPTT3432016TTBTC/SBPTT3432016TTBTCBS71', SBS1372017TTBTCController::Class.'@SBPTT3432016TTBTCBS71');
    Route::post('/SBPTT3432016TTBTC/SBPTT3432016TTBTCBS72', SBS1372017TTBTCController::Class.'@SBPTT3432016TTBTCBS72');
    Route::post('/SBPTT3432016TTBTC/SBPTT3432016TTBTCBS73', SBS1372017TTBTCController::Class.'@SBPTT3432016TTBTCBS73');
    Route::post('/SBPTT3432016TTBTC/SBPTT3432016TTBTCBS74', SBS1372017TTBTCController::Class.'@SBPTT3432016TTBTCBS74');
    Route::post('/SBPTT3432016TTBTC/SBPTT3432016TTBTCBS75', SBS1372017TTBTCController::Class.'@SBPTT3432016TTBTCBS75');
    Route::post('/SBPTT3432016TTBTC/SBPTT3432016TTBTCBS76', SBS1372017TTBTCController::Class.'@SBPTT3432016TTBTCBS76');

    Route::post('/StateBudgetPlanning/UTH/SBPUTHDMCSDMDV/SBPUTHDMCSDMDV01', SBS1372017TTBTCController::Class.'@SBPUTHDMCSDMDV01');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHDMCSDMDV/SBPUTHDMCSDMDV02', SBS1372017TTBTCController::Class.'@SBPUTHDMCSDMDV02');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHSOKHDT/SBPUTHSOKHDT01', SBS1372017TTBTCController::Class.'@SBPUTHSOKHDT01');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHSOKHDT/SBPUTHSOKHDT02', SBS1372017TTBTCController::Class.'@SBPUTHSOKHDT02');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHSOKHDT/SBPUTHSOKHDT03', SBS1372017TTBTCController::Class.'@SBPUTHSOKHDT03');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHSOKHDT/SBPUTHSOKHDT04', SBS1372017TTBTCController::Class.'@SBPUTHSOKHDT04');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHSOKHDT/SBPUTHSOKHDT05', SBS1372017TTBTCController::Class.'@SBPUTHSOKHDT05');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHSOKHDT/SBPUTHSOKHDT06', SBS1372017TTBTCController::Class.'@SBPUTHSOKHDT06');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHSOKHDT/SBPUTHSOKHDT07', SBS1372017TTBTCController::Class.'@SBPUTHSOKHDT07');

    Route::post('/DULIEUDEMOVINHTHANH/DULIEUDEMOVINHTHANHPHEDUYET', SBS1372017TTBTCController::Class.'@DULIEUDEMOVINHTHANHPHEDUYET');
    Route::post('/UocThucHien/DongNai', SBS1372017TTBTCController::Class.'@DongNai');
    //Duy
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCMB02', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB02');
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCMB2a', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB2a');
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCMB2b', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB2b');

    Route::post('/StateBudgetPlanning/UTH/SBPUTHDemomoduldutoan/SBPUTHDemomoduldutoan00', SBS1372017TTBTCController::Class.'@SBPUTHDemomoduldutoan00');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHDemomoduldutoan/SBPUTHDemomoduldutoan01', SBS1372017TTBTCController::Class.'@SBPUTHDemomoduldutoan01');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHDemomoduldutoan/SBPUTHDemomoduldutoan02', SBS1372017TTBTCController::Class.'@SBPUTHDemomoduldutoan02');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHDemomoduldutoan/SBPUTHDemomoduldutoan03', SBS1372017TTBTCController::Class.'@SBPUTHDemomoduldutoan03');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHDemomoduldutoan/SBPUTHDemomoduldutoan04', SBS1372017TTBTCController::Class.'@SBPUTHDemomoduldutoan04');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHDemomoduldutoan/SBPUTHDemomoduldutoan05', SBS1372017TTBTCController::Class.'@SBPUTHDemomoduldutoan05');

    Route::post('/dutoan3009/dutoan300901', SBS1372017TTBTCController::Class.'@dutoan300901');
    Route::post('/dutoan3009/dutoan300902', SBS1372017TTBTCController::Class.'@dutoan300902');
    Route::post('/dutoan3009/dutoan300903', SBS1372017TTBTCController::Class.'@dutoan300903');
    Route::post('/dutoan3009/dutoan300904', SBS1372017TTBTCController::Class.'@dutoan300904');

    Route::post('/DemomodulKHDT/DemomodulKHDT00', SBS1372017TTBTCController::Class.'@DemomodulKHDT00');
    Route::post('/DemomodulKHDT/DemomodulKHDT01', SBS1372017TTBTCController::Class.'@DemomodulKHDT01');
    Route::post('/DemomodulKHDT/DemomodulKHDT02', SBS1372017TTBTCController::Class.'@DemomodulKHDT02');
    Route::post('/DemomodulKHDT/DemomodulKHDT03', SBS1372017TTBTCController::Class.'@DemomodulKHDT03');
    Route::post('/DemomodulKHDT/DemomodulKHDT04', SBS1372017TTBTCController::Class.'@DemomodulKHDT04');
    Route::post('/DemomodulKHDT/DemomodulKHDT05', SBS1372017TTBTCController::Class.'@DemomodulKHDT05');
    Route::post('/DemomodulKHDT/DemomodulKHDT06', SBS1372017TTBTCController::Class.'@DemomodulKHDT06');

    Route::post('/StateBudgetPlanning/PB/SBPPB/SBPPB01', SBS1372017TTBTCController::Class.'@SBPPB01');
    Route::post('/StateBudgetPlanning/PB/SBPPB/SBPPB02', SBS1372017TTBTCController::Class.'@SBPPB02');


    Route::post('/DULIEUDEMOHUYENVINHTHANH/xemxet', SBS1372017TTBTCController::Class.'@xemxet');
    Route::post('/DULIEUDEMOHUYENVINHTHANH/UTH', SBS1372017TTBTCController::Class.'@UTH');

    Route::post('/ThuyetMinhDuToan/ThuyetMinh', SBS1372017TTBTCController::Class.'@ThuyetMinh');


    //thanh
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCPL01', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCPL01');
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCPL02', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCPL02');
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCPL03', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCPL03');
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCMB01', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB01');
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCMB1a', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB1a');
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCMB1b', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB1b');
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCMB1c', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB1c');
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCMB2c', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB2c');


    Route::post('/SBSTT992018TTBTC/SBSTT992018TTBTCB01BCTCTH', SBS1372017TTBTCController::Class.'@SBSTT992018TTBTCB01BCTCTH');
    Route::post('/SBSTT992018TTBTC/SBSTT992018TTBTCB01BSTT', SBS1372017TTBTCController::Class.'@SBSTT992018TTBTCB01BSTT');
    Route::post('/SBSTT992018TTBTC/SBSTT992018TTBTCB02BCTCTH', SBS1372017TTBTCController::Class.'@SBSTT992018TTBTCB02BCTCTH');
    Route::post('/SBSTT992018TTBTC/SBSTT992018TTBTCB03BCTCTH', SBS1372017TTBTCController::Class.'@SBSTT992018TTBTCB03BCTCTH');

    //Nghị định 31 - Thanh, Dũng
//----------------------nghị định 31----------------------------------
    //Thanh
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM01', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM01');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM02', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM02');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM03', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM03');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM04', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM04');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM08', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM08');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM09', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM09');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM10', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM10');
    //Dũng
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM21', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM21');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM22', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM22');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM23', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM23');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM24', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM24');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM25', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM25');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM26', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM26');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM27', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM27');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM28', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM28');
    Route::post('/SBPND312017NDCP/SBPND312017NDCPBM29', SBS1372017TTBTCController::Class.'@SBPND312017NDCPBM29');

    //Demo ===================== Thanh =====================================================
    Route::post('/DemoThanh/bang1', SBS1372017TTBTCController::Class.'@bang1');
    Route::post('/DemoThanh/bang2', SBS1372017TTBTCController::Class.'@bang2');
    Route::post('/DemoThanh/bang3', SBS1372017TTBTCController::Class.'@bang3');
    Route::post('/DemoThanh/bang4', SBS1372017TTBTCController::Class.'@bang4');
    Route::post('/DemoThanh/bang5', SBS1372017TTBTCController::Class.'@bang5');
    Route::post('/DemoThanh/bang6', SBS1372017TTBTCController::Class.'@bang6');

    Route::post('/BanVeDemo/QDGiaoBoSungDuToanNguonSuNghiep', SBS1372017TTBTCController::Class.'@QDGiaoBoSungDuToanNguonSuNghiep');
    Route::post('/BanVeDemo/QDGiaoDuToanNguonSuNghiep', SBS1372017TTBTCController::Class.'@QDGiaoDuToanNguonSuNghiep');
    Route::post('/BanVeDemo/QDGiaoDuToanNguonQLNN', SBS1372017TTBTCController::Class.'@QDGiaoDuToanNguonQLNN');
    Route::post('/BanVeDemo/QDPhanBoDuToanVPUBNDTinh', SBS1372017TTBTCController::Class.'@QDPhanBoDuToanVPUBNDTinh');
    Route::post('/BanVeDemo/ThuyetMinhDuToan2021BanHanh309', SBS1372017TTBTCController::Class.'@ThuyetMinhDuToan2021BanHanh309');
    // ============================Demo Thanh làm 15/3 =========================================
    Route::post('/SBP3422016TTBTC/MB05', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB05Controller@SBP3422016TTBTC_MB05');
    Route::post('/SBP3422016TTBTC/MB12_1', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB12_1Controller@SBP3422016TTBTC_MB12_1');
    Route::post('/SBP3422016TTBTC/MB12_2', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB12Controller@SBP3422016TTBTC_MB12_2');
    Route::post('/SBP3422016TTBTC/MB12_3', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB12Controller@SBP3422016TTBTC_MB12_3');
    Route::post('/SBP3422016TTBTC/MB12_4', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB12Controller@SBP3422016TTBTC_MB12_4');
    Route::post('/SBP3422016TTBTC/MB12_5', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB12Controller@SBP3422016TTBTC_MB12_5');
    Route::post('/SBP3422016TTBTC/MB11_1', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB11Controller@SBP3422016TTBTC_MB11_1');
    Route::post('/SBP3422016TTBTC/MB11_2', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB11_2Controller@SBP3422016TTBTC_MB11_2');
    Route::post('/SBP3422016TTBTC/MB50', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB50Controller@SBP3422016TTBTC_MB50');
    Route::post('/SBP3422016TTBTC/MB52', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB52Controller@SBP3422016TTBTC_MB52');
    Route::post('/SBP3422016TTBTC/MB53', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB53Controller@SBP3422016TTBTC_MB53');
    Route::post('/SBP3422016TTBTC/MB54', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB54Controller@SBP3422016TTBTC_MB54');
    Route::post('/SBP3422016TTBTC/MB55', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB55Controller@SBP3422016TTBTC_MB55');
    Route::post('/SBP3422016TTBTC/MB57', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB57Controller@SBP3422016TTBTC_MB57');

    //duc ND31
    Route::post('/SBPND312017TTBTC/MB01', 'StateBudgetPlanning\SBPND312017NDCP\SBPND3122017NDCPMB01Controller@SBPND312017TTBTC_MB01');
//end
    Route::post('/SBP3422016TTBTC/MB09', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB09Controller@SBP3422016TTBTC_MB09');
    Route::post('/SBP3422016TTBTC/MB14', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB14Controller@SBP3422016TTBTC_MB14');
    Route::post('/SBP3422016TTBTC/MB16', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB16Controller@SBP3422016TTBTC_MB16');
    Route::post('/SBP3422016TTBTC/MB18', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB18Controller@SBP3422016TTBTC_MB18');
    Route::post('/SBP3422016TTBTC/MB15_1', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB15_1Controller@SBP3422016TTBTC_MB15_1');
    Route::post('/SBP3422016TTBTC/MB13_1', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB13_1Controller@SBP3422016TTBTC_MB13_1');
    Route::post('/SBP3422016TTBTC/MB15_2', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB15_2Controller@SBP3422016TTBTC_MB15_2');
    Route::post('/SBP3422016TTBTC/MB13_2', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB13_2Controller@SBP3422016TTBTC_MB13_2');
    Route::post('/SBP3422016TTBTC/MB13_3', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB13_3Controller@SBP3422016TTBTC_MB13_3');
    Route::post('/SBP3422016TTBTC/MB13_5', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB13_5Controller@SBP3422016TTBTC_MB13_5');
    Route::post('/SBP3422016TTBTC/MB13_4', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB13_4Controller@SBP3422016TTBTC_MB13_4');
    Route::post('/SBP3422016TTBTC/MB13_6', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB13_6Controller@SBP3422016TTBTC_MB13_6');
    Route::post('/SBP3422016TTBTC/MB13_11', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB13_11Controller@SBP3422016TTBTC_MB13_11');
    Route::post('/SBP3422016TTBTC/MB13_7', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB13_7Controller@SBP3422016TTBTC_MB13_7');
    Route::post('/SBP3422016TTBTC/MB13_12', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB13_12Controller@SBP3422016TTBTC_MB13_12');
    Route::post('/SBP3422016TTBTC/MB13_8', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB13_8Controller@SBP3422016TTBTC_MB13_8');
    Route::post('/SBP3422016TTBTC/MB13_10', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB13_10Controller@SBP3422016TTBTC_MB13_10');

    Route::post('/SBP3422016TTBTC/MB25', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB25Controller@SBP3422016TTBTC_MB25');
    Route::post('/SBP3422016TTBTC/MB26', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB26Controller@SBP3422016TTBTC_MB26');
    Route::post('/SBP3422016TTBTC/MB27', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB27Controller@SBP3422016TTBTC_MB27');

    Route::post('/SBP3422016TTBTC/MB29_1', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB29_1Controller@SBP3422016TTBTC_MB29_1');
    Route::post('/SBP3422016TTBTC/MB29_2', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB29_2Controller@SBP3422016TTBTC_MB29_2');
    Route::post('/SBP3422016TTBTC/MB51', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB51Controller@SBP3422016TTBTC_MB51');
    Route::post('/SBP3422016TTBTC/MB56', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB56Controller@SBP3422016TTBTC_MB56');
    Route::post('/SBP3422016TTBTC/MB57', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB57Controller@SBP3422016TTBTC_MB57');
    Route::post('/SBP3422016TTBTC/MB32', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB32Controller@SBP3422016TTBTC_MB32');
    Route::post('/SBP3422016TTBTC/MB31', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB31Controller@SBP3422016TTBTC_MB31');
    Route::post('/SBP3422016TTBTC/MB35', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB35Controller@SBP3422016TTBTC_MB35');
    Route::post('/SBP3422016TTBTC/MB36', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB36Controller@SBP3422016TTBTC_MB36');
    Route::post('/SBP3422016TTBTC/MB48', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB48Controller@SBP3422016TTBTC_MB48');
    Route::post('/SBP3422016TTBTC/MB49', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB49Controller@SBP3422016TTBTC_MB49');
//liên test
    Route::post('/SBP3422016TTBTC/MB04', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB04Controller@SBP3422016TTBTC_MB04');
    Route::post('/SBP3422016TTBTC/MB01', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB01Controller@SBP3422016TTBTC_MB01');
    Route::post('/SBP3422016TTBTC/MB02', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB02Controller@SBP3422016TTBTC_MB02');
    Route::post('/SBP3422016TTBTC/MB03', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB03Controller@SBP3422016TTBTC_MB03');
    Route::post('/SBP3422016TTBTC/MB06', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB06Controller@SBP3422016TTBTC_MB06');
    Route::post('/SBP3422016TTBTC/MB07', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB07Controller@SBP3422016TTBTC_MB07');
    Route::post('/SBP3422016TTBTC/MB08', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB08Controller@SBP3422016TTBTC_MB08');
    Route::post('/SBP3422016TTBTC/MB10', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB10Controller@SBP3422016TTBTC_MB10');
    Route::post('/SBP3422016TTBTC/MB23', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB23Controller@SBP3422016TTBTC_MB23');
    Route::post('/SBP3422016TTBTC/MB24', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB24Controller@SBP3422016TTBTC_MB24');
    Route::post('/SBP3422016TTBTC/MB33', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB33Controller@SBP3422016TTBTC_MB33');
    Route::post('/SBP3422016TTBTC/MB34', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCMB34Controller@SBP3422016TTBTC_MB34');
    //SBP-ND31
    Route::post('/SBPND312017NDCP/MB22', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB22Controller@SBPND312017NDCP_MB22');
    Route::post('/SBPND312017NDCP/MB23', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB23Controller@SBPND312017NDCP_MB23');
    Route::post('/SBPND312017NDCP/MB25', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB25Controller@SBPND312017NDCP_MB25');
    Route::post('/SBPND312017NDCP/MB26', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB26Controller@SBPND312017NDCP_MB26');
    Route::post('/SBPND312017NDCP/MB27', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB27Controller@SBPND312017NDCP_MB27');
    Route::post('/SBPND312017NDCP/MB30', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB30Controller@SBPND312017NDCP_MB30');
    Route::post('/SBPND312017NDCP/MB31', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB31Controller@SBPND312017NDCP_MB31');
    Route::post('/SBPND312017NDCP/MB32', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB32Controller@SBPND312017NDCP_MB32');
    Route::post('/SBPND312017NDCP/MB33', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB33Controller@SBPND312017NDCP_MB33');
    Route::post('/SBPND312017NDCP/MB34', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB34Controller@SBPND312017NDCP_MB34');
    Route::post('/SBPND312017NDCP/MB35', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB35Controller@SBPND312017NDCP_MB35');
    Route::post('/SBPND312017NDCP/MB09', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB09Controller@SBPND312017NDCP_MB09');
    Route::post('/SBPND312017NDCP/MB22', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB22Controller@SBPND312017NDCP_MB22');
    Route::post('/SBPND312017NDCP/MB13', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB13Controller@SBPND312017NDCP_MB13');
    Route::post('/SBPND312017NDCP/MB15', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB15Controller@SBPND312017NDCP_MB15');
    Route::post('/SBPND312017NDCP/MB14', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB14Controller@SBPND312017NDCP_MB14');
    Route::post('/SBPND312017NDCP/MB16', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB16Controller@SBPND312017NDCP_MB16');
    Route::post('/SBPND312017NDCP/MB17', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB17Controller@SBPND312017NDCP_MB17');
    Route::post('/SBPND312017NDCP/MB19', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB19Controller@SBPND312017NDCP_MB19');
    Route::post('/SBPND312017NDCP/MB10', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB10Controller@SBPND312017NDCP_MB10');
    Route::post('/SBPND312017NDCP/MB12', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB12Controller@SBPND312017NDCP_MB12');
    Route::post('/SBPND312017NDCP/MB03', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB03Controller@SBPND312017NDCP_MB03');
    Route::post('/SBPND312017NDCP/MB05', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB05Controller@SBPND312017NDCP_MB05');
    Route::post('/SBPND312017NDCP/MB20', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB20Controller@SBPND312017NDCP_MB20');
    Route::post('/SBPND312017NDCP/MB21', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB21Controller@SBPND312017NDCP_MB21');
    Route::post('/SBPND312017NDCP/MB06', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB06Controller@SBPND312017NDCP_MB06');
    Route::post('/SBPND312017NDCP/MB36', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB36Controller@SBPND312017NDCP_MB36');
    Route::post('/SBPND312017NDCP/MB37', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB37Controller@SBPND312017NDCP_MB37');
    Route::post('/SBPND312017NDCP/MB04', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB04Controller@SBPND312017NDCP_MB04');
    Route::post('/SBPND312017NDCP/MB38', 'StateBudgetPlanning\SBPND312017NDCP\SBPND312017NDCPMB38Controller@SBPND312017NDCP_MB38');
    //TT343
    Route::post('/SBP3432016TTBTC/MB103', 'StateBudgetPlanning\SBP3432016TTBTC\SBP3432016TTBTCMB103Controller@SBP3432016TTBTC_MB103');
    Route::post('/SBP3432016TTBTC/MB104', 'StateBudgetPlanning\SBP3432016TTBTC\SBP3432016TTBTCMB104Controller@SBP3432016TTBTC_MB104');
    Route::post('/SBP3432016TTBTC/MB105', 'StateBudgetPlanning\SBP3432016TTBTC\SBP3432016TTBTCMB105Controller@SBP3432016TTBTC_MB105');
    Route::post('/SBP3432016TTBTC/MB106', 'StateBudgetPlanning\SBP3432016TTBTC\SBP3432016TTBTCMB106Controller@SBP3432016TTBTC_MB106');
    Route::post('/SBP3432016TTBTC/MB107', 'StateBudgetPlanning\SBP3432016TTBTC\SBP3432016TTBTCMB107Controller@SBP3432016TTBTC_MB107');

    //TT344
    Route::post('/SBP3442016TTBTC/MB01', 'StateBudgetPlanning\SBP3442016TTBTC\SBP3442016TTBTCMB01Controller@SBP3442016TTBTC_MB01');
    Route::post('/SBP3442016TTBTC/MB02', 'StateBudgetPlanning\SBP3442016TTBTC\SBP3442016TTBTCMB02Controller@SBP3442016TTBTC_MB02');
    Route::post('/SBP3442016TTBTC/MB03', 'StateBudgetPlanning\SBP3442016TTBTC\SBP3442016TTBTCMB03Controller@SBP3442016TTBTC_MB03');
    Route::post('/SBP3442016TTBTC/MB04', 'StateBudgetPlanning\SBP3442016TTBTC\SBP3442016TTBTCMB04Controller@SBP3442016TTBTC_MB04');

    //TT61-2021
    Route::post('/SBP612021TTBTC/MB01', 'StateBudgetPlanning\SBP612021TTBTC\SBP612021TTBTCMB01Controller@SBP612021TTBTC_MB01');
    Route::post('/SBP612021TTBTC/MB03', 'StateBudgetPlanning\SBP612021TTBTC\SBP612021TTBTCMB03Controller@SBP612021TTBTC_MB03');
    Route::post('/SBP612021TTBTC/MB04', 'StateBudgetPlanning\SBP612021TTBTC\SBP612021TTBTCMB04Controller@SBP612021TTBTC_MB04');
    Route::post('/SBP612021TTBTC/MB05', 'StateBudgetPlanning\SBP612021TTBTC\SBP612021TTBTCMB05Controller@SBP612021TTBTC_MB05');

    Route::post('/Operating/BCDHB02DHCH', Operating\OperatingController::Class.'@BCDHB02DHCH');
    Route::post('/Operating/STDS02DHCH', Operating\OperatingController::Class.'@STDS02DHCH');
    Route::post('/Operating/STDTHDTTNSNN02', Operating\OperatingController::Class.'@STDTHDTTNSNN02');
    Route::post('/Operating/STDDTGTGT03', Operating\OperatingController::Class.'@STDDTGTGT03');
    //    Route::post('/ChicucBVTTguiSoNN/Demo05TH', SBS1372017TTBTCController::Class.'@Demo05TH');
    Route::post('/ChicucBVTTguiSoNN/Demo07thu', SBS1372017TTBTCController::Class.'@Demo07thu');
    Route::post('/ChicucBVTTguiSoNN/Demo138cshdkt', SBS1372017TTBTCController::Class.'@Demo138cshdkt');
    Route::post('/ChicucBVTTguiSoNN/Demo14QLNN', SBS1372017TTBTCController::Class.'@Demo14QLNN');
    Route::post('/ChicucBVTTguiSoNN/Demo262020CVCCBVTV', SBS1372017TTBTCController::Class.'@Demo262020CVCCBVTV');
    Route::post('/ChicucBVTTguiSoNN/ThuyetMinhDuToan2021CCTTbVTV', SBS1372017TTBTCController::Class.'@ThuyetMinhDuToan2021CCTTbVTV');
    //===============================Ước thực hiện ngày 29/3========================
    Route::post('/StateBudgetPlanning/UTH/SBPUTHVPSoNN/SBPUTHVPSoNN01', SBS1372017TTBTCController::Class.'@SBPUTHVPSoNN01');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHVPSoNN/SBPUTHVPSoNN02', SBS1372017TTBTCController::Class.'@SBPUTHVPSoNN02');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHVPSoNN/SBPUTHVPSoNN03', SBS1372017TTBTCController::Class.'@SBPUTHVPSoNN03');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHVPSoNN/SBPUTHVPSoNN04', SBS1372017TTBTCController::Class.'@SBPUTHVPSoNN04');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHVPSoNN/SBPUTHVPSoNN05', SBS1372017TTBTCController::Class.'@SBPUTHVPSoNN05');
    Route::post('/StateBudgetPlanning/UTH/SBPUTHVPSoNN/SBPUTHVPSoNN06', SBS1372017TTBTCController::Class.'@SBPUTHVPSoNN06');
    //============================= CV-STC gửi đơn vị xem xét dự toán  ===================
    Route::post('/CVSTCguicacdonvixemxetdutoan/CVSTCguicacdonvixemxetdutoan', SBS1372017TTBTCController::Class.'@CVSTCguicacdonvixemxetdutoan');
    Route::post('/CVSTCguicacdonvixemxetdutoan/CVSTCguicacdonvixemxetdutoanvong2', SBS1372017TTBTCController::Class.'@CVSTCguicacdonvixemxetdutoanvong2');

    //============================Demo huyện vĩnh thạnh=====================
    Route::post('/DULIEUDEMOHUYENVINHTHANH/Giao', SBS1372017TTBTCController::Class.'@Giao');
    Route::post('/DULIEUDEMOHUYENVINHTHANH/UocTH', SBS1372017TTBTCController::Class.'@UocTH');
    //==================================END======================================
    //------------------------------------------------------------------------------------
    //-----------------------------------DEMO-dung----------------------------------------
    Route::post('/DEMO/bcc1', SBS1372017TTBTCController::Class.'@DemoBc1');
    Route::post('/DEMO/bcc2', SBS1372017TTBTCController::Class.'@DemoBc2');
    Route::post('/DEMO/bcc3', SBS1372017TTBTCController::Class.'@DemoBc3');
    Route::post('/DEMO/bcc4', SBS1372017TTBTCController::Class.'@DemoBc4');
    Route::post('/DEMO/bcc5', SBS1372017TTBTCController::Class.'@DemoBc5');
    Route::post('/DEMO/bcc6', SBS1372017TTBTCController::Class.'@DemoBc6');
    Route::post('/DEMO/bcc14', SBS1372017TTBTCController::Class.'@DemoBc14');
    Route::post('/DEMO/bcc17', SBS1372017TTBTCController::Class.'@DemoBc17');
    Route::post('/DEMO/bcc18', SBS1372017TTBTCController::Class.'@DemoBc18');
    Route::post('/DEMO/bcc18a', SBS1372017TTBTCController::Class.'@DemoBc18a');
    Route::post('/DEMO/bcc18b', SBS1372017TTBTCController::Class.'@DemoBc18b');
    Route::post('/DEMO/bcc18c', SBS1372017TTBTCController::Class.'@DemoBc18c');
    Route::post('/DEMO/bcc19', SBS1372017TTBTCController::Class.'@DemoBc19');
    Route::post('/DEMO/bcc19a', SBS1372017TTBTCController::Class.'@DemoBc19a');
    Route::post('/DEMO/bcc22', SBS1372017TTBTCController::Class.'@DemoBc22');




    //--------------------------------------END-------------------------------------------


    //dũng
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCMB3b', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB3b');
    Route::post('/SBSTT1372017TTBTC/SBSTT1372017TTBTCMB4b', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB4b');

    //Tuấn
    Route::post('/SBSTT3442016TTBTC/SBSTT3442016TTBTCMBS07', SBS1372017TTBTCController::Class.'@SBSTT3442016TTBTCMBS07');
    Route::post('/SBSTT3442016TTBTC/SBSTT3442016TTBTCMBS08', SBS1372017TTBTCController::Class.'@SBSTT3442016TTBTCMBS08');
    Route::post('/SBSTT3442016TTBTC/SBSTT3442016TTBTCMBS09', SBS1372017TTBTCController::Class.'@SBSTT3442016TTBTCMBS09');
    Route::post('/SBSTT3442016TTBTC/SBSTT3442016TTBTCMBS10', SBS1372017TTBTCController::Class.'@SBSTT3442016TTBTCMBS10');
    Route::post('/SBSTT3442016TTBTC/SBSTT3442016TTBTCMBS11', SBS1372017TTBTCController::Class.'@SBSTT3442016TTBTCMBS11');
    Route::post('/SBSTT3442016TTBTC/SBSTT3442016TTBTCMBS12', SBS1372017TTBTCController::Class.'@SBSTT3442016TTBTCMBS12');


    Route::post('/SBS1372017TTBTC/SBSTT1372017TTBTCMB03', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB03');

    Route::post('/SBS1372017TTBTC/SBSTT1372017TTBTCMB3a', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB3a');
    Route::post('/SBS1372017TTBTC/SBSTT1372017TTBTCMB4a', SBS1372017TTBTCController::Class.'@SBSTT1372017TTBTCMB4a');


    Route::post('/ReportTuandemo/ReportTuandemo13', SBS1372017TTBTCController::Class.'@ReportTuandemo13');
    Route::post('/ReportTuandemo/ReportTuandemo12', SBS1372017TTBTCController::Class.'@ReportTuandemo12');
    Route::post('/ReportTuandemo/ReportTuandemo11', SBS1372017TTBTCController::Class.'@ReportTuandemo11');
    Route::post('/ReportTuandemo/ReportTuandemo10', SBS1372017TTBTCController::Class.'@ReportTuandemo10');
    Route::post('/ReportTuandemo/ReportTuandemo9', SBS1372017TTBTCController::Class.'@ReportTuandemo9');
    Route::post('/ReportTuandemo/ReportTuandemo8', SBS1372017TTBTCController::Class.'@ReportTuandemo8');
    Route::post('/ReportTuandemo/ReportTuandemo7', SBS1372017TTBTCController::Class.'@ReportTuandemo7');
    Route::post('/ReportTuandemo/ReportTuandemo6', SBS1372017TTBTCController::Class.'@ReportTuandemo6');
    //Tuan - Phe duyet
    Route::post('/StateBudgetPlanning/PD/SBPPDQDUBCboDTNS01', SBS1372017TTBTCController::Class.'@SBPPDQDUBCboDTNS01');
    Route::post('/StateBudgetPlanning/PD/SBPPDQDUBCboDTNS02', SBS1372017TTBTCController::Class.'@SBPPDQDUBCboDTNS02');
    Route::post('/StateBudgetPlanning/PD/SBPPDQDUBCboDTNS03', SBS1372017TTBTCController::Class.'@SBPPDQDUBCboDTNS03');
    Route::post('/StateBudgetPlanning/PD/SBPPDQDUBCboDTNS04', SBS1372017TTBTCController::Class.'@SBPPDQDUBCboDTNS04');
    Route::post('/StateBudgetPlanning/PD/SBPPDQDUBCboDTNS05', SBS1372017TTBTCController::Class.'@SBPPDQDUBCboDTNS05');

    Route::post('/test55UBND/test55UBNDQ3', SBS1372017TTBTCController::Class.'@test55UBNDQ3');
    Route::post('/test55UBND/test55UBNDQ3MS21', SBS1372017TTBTCController::Class.'@test55UBNDQ3MS21');
    Route::post('/test55UBND/test55UBNDQ3MS22', SBS1372017TTBTCController::Class.'@test55UBNDQ3MS22');
    Route::post('/test55UBND/test55UBNDQ3MS23', SBS1372017TTBTCController::Class.'@test55UBNDQ3MS23');
    Route::post('/test55UBND/test55UBNDQ3MS24', SBS1372017TTBTCController::Class.'@test55UBNDQ3MS24');
    Route::post('/test55UBND/test55UBNDQ3MS25', SBS1372017TTBTCController::Class.'@test55UBNDQ3MS25');
    Route::post('/test55UBND/test55UBNDQ3MS26', SBS1372017TTBTCController::Class.'@test55UBNDQ3MS26');
    Route::post('/test55UBND/test55UBNDQ3MS29', SBS1372017TTBTCController::Class.'@test55UBNDQ3MS29');
    Route::post('/test55UBND/test55UBNDQ3MS30', SBS1372017TTBTCController::Class.'@test55UBNDQ3MS30');
    Route::post('/test55UBND/test55UBNDQ3MS31', SBS1372017TTBTCController::Class.'@test55UBNDQ3MS31');

    //StateBudgetPlanning -> SBPESTIMATEPLAN
    Route::post('/StateBudgetPlanning/SBPESTIMATEPLAN/SBPESTIMATEPLAN_01', 'StateBudgetPlanning\SBPESTIMATEPLAN\SBPESTIMATEPLANController@SBPESTIMATEPLAN_SBPESTIMATEPLAN_01');
    Route::post('/StateBudgetPlanning/SBPESTIMATEPLAN/SBPESTIMATEPLANtest', 'StateBudgetPlanning\SBPESTIMATEPLAN\SBPESTIMATEPLANtestController@SBPESTIMATEPLAN02');

    //StateBudgetPlanning -> SBPMAKEPLAN
    Route::post('/StateBudgetPlanning/SBPMAKEPLAN/SBPMAKEPLAN_01', 'StateBudgetPlanning\SBPMAKEPLAN\SBPMAKEPLANController@SBPMAKEPLAN_SBPMAKEPLAN_01');
    //Operating budget estimates
    Route::post('/StateBudgetPlanning/Operating/SBP_B200_DH', StateBudgetPlanning\Operating\OperatingController::Class.'@SBPB200DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B201_DH', StateBudgetPlanning\Operating\OperatingB201DHController::Class.'@SBPB201DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B202_DH',  StateBudgetPlanning\Operating\OperatingB202DHController::Class.'@SBPB202DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B203_DH',  StateBudgetPlanning\Operating\OperatingB203DHController::Class.'@SBPB203DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B204_DH',  StateBudgetPlanning\Operating\OperatingB204DHController::Class.'@SBPB204DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B205_DH',  StateBudgetPlanning\Operating\OperatingB205DHController::Class.'@SBPB205DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B245_DH',  StateBudgetPlanning\Operating\OperatingB245DHController::Class.'@SBPB245DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B206_DH',  StateBudgetPlanning\Operating\OperatingB206DHController::Class.'@SBPB206DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B208_DH',  StateBudgetPlanning\Operating\OperatingB208DHController::Class.'@SBPB208DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B209_DH',  StateBudgetPlanning\Operating\OperatingB209DHController::Class.'@SBPB209DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B220_DH',  StateBudgetPlanning\Operating\OperatingB220DHController ::Class.'@SBPB220DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B221_DH',  StateBudgetPlanning\Operating\OperatingB221DHController ::Class.'@SBPB221DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B222_DH',  StateBudgetPlanning\Operating\OperatingB222DHController::Class.'@SBPB222DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B223_DH',  StateBudgetPlanning\Operating\OperatingB223DHController ::Class.'@SBPB223DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B224_DH',  StateBudgetPlanning\Operating\OperatingB224DHController ::Class.'@SBPB224DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B225_DH',  StateBudgetPlanning\Operating\OperatingB225DHController ::Class.'@SBPB225DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B228_DH',  StateBudgetPlanning\Operating\OperatingB228DHController::Class.'@OPTB228DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B229_DH',  StateBudgetPlanning\Operating\OperatingB229DHController::Class.'@OPTB229DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B231_DH',  StateBudgetPlanning\Operating\OperatingB231DHController::Class.'@SBPB231DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B240_DH',  StateBudgetPlanning\Operating\OperatingB240DHController::Class.'@SBPB240DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B241_DH',  StateBudgetPlanning\Operating\OperatingB241DHController::Class.'@SBPB241DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B244_DH',  StateBudgetPlanning\Operating\OperatingB244DHController::Class.'@SBPB244DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B211_DH',  StateBudgetPlanning\Operating\OperatingB211DHController::Class.'@SBPB211DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B242_DH',  StateBudgetPlanning\Operating\OperatingB242DHController ::Class.'@SBPB242DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B243_DH',  StateBudgetPlanning\Operating\OperatingB243DHController ::Class.'@SBPB243DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B226_DH',  StateBudgetPlanning\Operating\OperatingB226DHController ::Class.'@SBPB226DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B227_DH',  StateBudgetPlanning\Operating\OperatingB227DHController ::Class.'@SBPB227DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B246_DH',  StateBudgetPlanning\Operating\OperatingB246DHController ::Class.'@SBPB246DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B247_DH',  StateBudgetPlanning\Operating\OperatingB247DHController ::Class.'@SBPB247DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B248_DH',  StateBudgetPlanning\Operating\OperatingB248DHController ::Class.'@SBPB248DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B249_DH',  StateBudgetPlanning\Operating\OperatingB249DHController ::Class.'@SBPB249DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B251_DH',  StateBudgetPlanning\Operating\OperatingB251DHController ::Class.'@SBPB251DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B252_DH',  StateBudgetPlanning\Operating\OperatingB252DHController ::Class.'@SBPB252DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B253_DH',  StateBudgetPlanning\Operating\OperatingB253DHController ::Class.'@SBPB253DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B254_DH',  StateBudgetPlanning\Operating\OperatingB254DHController ::Class.'@SBPB254DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B258_DH',  StateBudgetPlanning\Operating\OperatingB258DHController ::Class.'@SBPB258DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B259_DH', StateBudgetPlanning\Operating\OperatingB259DHController ::Class . '@SBPB259DH');
    Route::post('/StateBudgetPlanning/Operating/SBP_B262_DH', StateBudgetPlanning\Operating\OperatingB262DHController ::Class . '@SBPB262DH');

    //StateBudgetPlanning -> SBPESTIMATE
    Route::post('/StateBudgetPlanning/SBPESTIMATE/SBPESTIMATE_B01-BH-DT', 'StateBudgetPlanning\SBPESTIMATE\SBPESTIMATEB01BHDTController@SBPESTIMATEB01BHDT');
    Route::post('/StateBudgetPlanning/SBPESTIMATE/SBPESTIMATE_F01a-DH-DT', 'StateBudgetPlanning\SBPESTIMATE\SBPESTIMATEF01aDHDTController@SBPESTIMATEF01aDHDT');
    Route::post('/StateBudgetPlanning/SBPESTIMATE/SBPESTIMATE_F01b-DH-DT', 'StateBudgetPlanning\SBPESTIMATE\SBPESTIMATEF01bDHDTController@SBPESTIMATEF01bDHDT');

    //============ làm ngày 15/5 ===============
    Route::post('/Ngay125/PFMISDUTOANCVSTCXEMXETVONG1', SBS1372017TTBTCController::Class.'@PFMISDUTOANCVSTCXEMXETVONG1');
    Route::post('/Ngay125/PFMISDUTOANCVSTCXEMXETVONG2', SBS1372017TTBTCController::Class.'@PFMISDUTOANCVSTCXEMXETVONG2');
//    Route::post('/Ngay125/PFMISDUTOANCVTTĐVDTCQTCCV', SBS1372017TTBTCController::Class.'@PFMISDUTOANCVTTĐVDTCQTCCV');
    Route::post('/Ngay125/PFMISDUTOANCVTTĐVDTCQTCTTr', SBS1372017TTBTCController::Class.'@PFMISDUTOANCVTTĐVDTCQTCTTr');
    Route::post('/Ngay125/PFMISDUTOANTOTRINHSTCUBNDtinh', SBS1372017TTBTCController::Class.'@PFMISDUTOANTOTRINHSTCUBNDtinh');
    Route::post('/Ngay125/PFMISDUTOANCVĐVSTC', SBS1372017TTBTCController::Class.'@PFMISDUTOANCVĐVSTC');
    Route::post('/Ngay125/PFMISDTNSNNCHUNGTUGIAODTMS01', SBS1372017TTBTCController::Class.'@PFMISDTNSNNCHUNGTUGIAODTMS01');
    Route::post('/Ngay125/PFMISDTNSNNCHUNGTUGIAODTMS02', SBS1372017TTBTCController::Class.'@PFMISDTNSNNCHUNGTUGIAODTMS02');
    Route::post('/Ngay125/PFMISDTNSNNCHUNGTUGIAODTMS03', SBS1372017TTBTCController::Class.'@PFMISDTNSNNCHUNGTUGIAODTMS03');

//    Route::post('/xdebug/sbp_tt3422016ttbtc_mb04', SBP3422016TTBTCB4Controller::Class.'@sbptt3422016ttbtcmb04');
    Route::post('/xdebug/sbptt3422016ttbtcmb04', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCB4Controller@sbptt3422016ttbtcmb04');
    Route::post('/xdebug/sbptt3422016ttbtcmb01', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCB4Controller@sbptt3422016ttbtcmb01');

    //công văn
    Route::post('/CongVan/DuToan1', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCCCV1ontroller@sbptt3422016ttbtccv1');
    Route::post('/CongVan/DuToan2', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCCCV2Controller@sbptt3422016ttbtccv2');
    Route::post('/Ngay125/PFMISDTNSNNCHUNGTUGIAODTMS01', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCCCVChungTuGiaoDuToanController@sbptt3422016ttbtcchungtugiaodutoan');
    Route::post('/Ngay125/PFMISDTNSNNCHUNGTUGIAODTBM48', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCCCVChungTuGiaoDuToanController@sbptt3422016ttbtcchungtugiaodutoanbm48');
    Route::post('/Ngay125/PFMISDUTOANCVTTĐVDTCQTCTTr', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCXXToTrinhController@sbptt3422016ttbtcXXtotrinh');
    Route::post('/Ngay125/PFMISDUTOANTOTRINHSTCUBNDtinh', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCToTrinhUBNDTinhController@sbptt3422016ttbtctotrinhUBNDtinh');
    Route::post('/Ngay125/PFMISDUTOANCVĐVSTC', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCCCVChungTuLDTmb02Controller@sbptt3422016ttbtcchungtuLDTmb02');
    Route::post('/Ngay125/PFMISDTNSNNCHUNGTUGIAODTMS02', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCCCVChungTuGiaoDuToanmb02Controller@sbptt3422016ttbtcchungtugiaodutoanmb02');
    Route::post('/Ngay125/PFMISDTNSNNCHUNGTUGIAODTMS03', 'StateBudgetPlanning\SBP3422016TTBTC\SBP3422016TTBTCCCVChungTuGiaoDuToanmb03Controller@sbptt3422016ttbtcchungtugiaodutoanmb03');

    //công văn
});

