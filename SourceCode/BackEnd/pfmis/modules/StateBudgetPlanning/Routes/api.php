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
    // StateBudgetPlanning
    // Make
    Route::post('/sbpmakeplan', SbpmakeplanController::Class.'@list');
//    Route::post('/sbpmakeplan', function (){
//        \Illuminate\Support\Facades\Log::debug('adsdsd');
//    });
    Route::get('/sbpmakeplan/create', SbpmakeplanController::Class.'@create');
    Route::get('/sbpmakeplan/view/{id}', SbpmakeplanController::Class.'@view');
    Route::get('/sbpmakeplan/edit/{id}', SbpmakeplanController::Class.'@edit');
    Route::post('/sbpmakeplan/update/{id}', SbpmakeplanController::Class.'@update');
    Route::post('/sbpmakeplan/delete/{id}', SbpmakeplanController::Class.'@delete');
    Route::post('/sbpmakeplan/delete', SbpmakeplanController::Class.'@deleteArray');
    Route::post('/sbpmakeplan/store', SbpmakeplanController::Class.'@store');
    Route::post('/sbpmakeplan/get-report-data', SbpmakeplanController::Class.'@getReportData');
    Route::post('/sbpmakeplan/get-detail-report-data', SbpmakeplanController::Class.'@getDetailReportData');
    Route::post('/sbpmakeplan/actbook', SbpmakeplanController::Class.'@actbook');
    Route::post('/sbpmakeplan/getNormTableItem', SbpmakeplanController::Class.'@getNormTableItem');
    Route::post('/sbpmakeplan/get-list', SbpmakeplanController::Class.'@getList');
    Route::get('/sbpmakeplan/load-temp/{id}', SbpmakeplanController::Class.'@loadTemp');
    // Review
    Route::post('/sbpreviewplan', SbpreviewplanController::Class.'@list');
    Route::get('/sbpreviewplan/create', SbpreviewplanController::Class.'@create');
    Route::get('/sbpreviewplan/view/{id}', SbpreviewplanController::Class.'@view');
    Route::get('/sbpreviewplan/edit/{id}', SbpreviewplanController::Class.'@edit');
    Route::post('/sbpreviewplan/update/{id}', SbpreviewplanController::Class.'@update');
    Route::post('/sbpreviewplan/delete/{id}', SbpreviewplanController::Class.'@delete');
    Route::post('/sbpreviewplan/delete', SbpreviewplanController::Class.'@deleteArray');
    Route::post('/sbpreviewplan/store', SbpreviewplanController::Class.'@store');
    Route::post('/sbpreviewplan/get-report-data', SbpreviewplanController::Class.'@getReportData');
    Route::post('/sbpreviewplan/get-detail-report-data', SbpreviewplanController::Class.'@getDetailReportData');
    Route::post('/sbpreviewplan/actbook', SbpreviewplanController::Class.'@actbook');
    Route::post('/sbpreviewplan/getNormTableItem', SbpreviewplanController::Class.'@getNormTableItem');
    Route::post('/sbpreviewplan/get-list', SbpreviewplanController::Class.'@getList');
    Route::get('/sbpreviewplan/load-temp/{id}', SbpreviewplanController::Class.'@loadTemp');
    //approval
    Route::post('/sbpapprovalplan', SbpapprovalplanController::Class.'@list');
    Route::get('/sbpapprovalplan/create', SbpapprovalplanController::Class.'@create');
    Route::get('/sbpapprovalplan/view/{id}', SbpapprovalplanController::Class.'@view');
    Route::get('/sbpapprovalplan/edit/{id}', SbpapprovalplanController::Class.'@edit');
    Route::post('/sbpapprovalplan/update/{id}', SbpapprovalplanController::Class.'@update');
    Route::post('/sbpapprovalplan/delete/{id}', SbpapprovalplanController::Class.'@delete');
    Route::post('/sbpapprovalplan/delete', SbpapprovalplanController::Class.'@deleteArray');
    Route::post('/sbpapprovalplan/store', SbpapprovalplanController::Class.'@store');
    Route::post('/sbpapprovalplan/get-report-data', SbpapprovalplanController::Class.'@getReportData');
    Route::post('/sbpapprovalplan/get-detail-report-data', SbpapprovalplanController::Class.'@getDetailReportData');
    Route::post('/sbpapprovalplan/actbook', SbpapprovalplanController::Class.'@actbook');
    Route::post('/sbpapprovalplan/getNormTableItem', SbpapprovalplanController::Class.'@getNormTableItem');
    Route::post('/sbpapprovalplan/get-list', SbpapprovalplanController::Class.'@getList');
    Route::get('/sbpapprovalplan/load-temp/{id}', SbpapprovalplanController::Class.'@loadTemp');
    //assign
    Route::post('/sbpassignplan', SbpassignplanController::Class.'@list');
    Route::get('/sbpassignplan/create', SbpassignplanController::Class.'@create');
    Route::get('/sbpassignplan/view/{id}', SbpassignplanController::Class.'@view');
    Route::get('/sbpassignplan/edit/{id}', SbpassignplanController::Class.'@edit');
    Route::post('/sbpassignplan/update/{id}', SbpassignplanController::Class.'@update');
    Route::post('/sbpassignplan/delete/{id}', SbpassignplanController::Class.'@delete');
    Route::post('/sbpassignplan/delete', SbpassignplanController::Class.'@deleteArray');
    Route::post('/sbpassignplan/store', SbpassignplanController::Class.'@store');
    Route::post('/sbpassignplan/get-report-data', SbpassignplanController::Class.'@getReportData');
    Route::post('/sbpassignplan/get-detail-report-data', SbpassignplanController::Class.'@getDetailReportData');
    Route::post('/sbpassignplan/actbook', SbpassignplanController::Class.'@actbook');
    Route::post('/sbpassignplan/getNormTableItem', SbpassignplanController::Class.'@getNormTableItem');
    Route::post('/sbpassignplan/get-list', SbpassignplanController::Class.'@getList');
    Route::get('/sbpassignplan/load-temp/{id}', SbpassignplanController::Class.'@loadTemp');
    //give
    Route::post('/sbpgiveplan', SbpgiveplanController::Class.'@list');
    Route::get('/sbpgiveplan/create', SbpgiveplanController::Class.'@create');
    Route::get('/sbpgiveplan/view/{id}', SbpgiveplanController::Class.'@view');
    Route::get('/sbpgiveplan/edit/{id}', SbpgiveplanController::Class.'@edit');
    Route::post('/sbpgiveplan/update/{id}', SbpgiveplanController::Class.'@update');
    Route::post('/sbpgiveplan/delete/{id}', SbpgiveplanController::Class.'@delete');
    Route::post('/sbpgiveplan/delete', SbpgiveplanController::Class.'@deleteArray');
    Route::post('/sbpgiveplan/store', SbpgiveplanController::Class.'@store');
    Route::post('/sbpgiveplan/get-report-data', SbpgiveplanController::Class.'@getReportData');
    Route::post('/sbpgiveplan/get-detail-report-data', SbpgiveplanController::Class.'@getDetailReportData');
    Route::post('/sbpgiveplan/actbook', SbpgiveplanController::Class.'@actbook');
    Route::post('/sbpgiveplan/getNormTableItem', SbpgiveplanController::Class.'@getNormTableItem');
    Route::post('/sbpgiveplan/get-list', SbpgiveplanController::Class.'@getList');
    Route::get('/sbpgiveplan/load-temp/{id}', SbpgiveplanController::Class.'@loadTemp');
    //estimate
    Route::post('/sbpestimateplan', SbpestimateplanController::Class.'@list');
    Route::get('/sbpestimateplan/create', SbpestimateplanController::Class.'@create');
    Route::get('/sbpestimateplan/view/{id}', SbpestimateplanController::Class.'@view');
    Route::get('/sbpestimateplan/edit/{id}', SbpestimateplanController::Class.'@edit');
    Route::post('/sbpestimateplan/update/{id}', SbpestimateplanController::Class.'@update');
    Route::post('/sbpestimateplan/delete/{id}', SbpestimateplanController::Class.'@delete');
    Route::post('/sbpestimateplan/delete', SbpestimateplanController::Class.'@deleteArray');
    Route::post('/sbpestimateplan/store', SbpestimateplanController::Class.'@store');
    Route::post('/sbpestimateplan/get-report-data', SbpestimateplanController::Class.'@getReportData');
    Route::post('/sbpestimateplan/get-detail-report-data', SbpestimateplanController::Class.'@getDetailReportData');
    Route::post('/sbpestimateplan/actbook', SbpestimateplanController::Class.'@actbook');
    Route::post('/sbpestimateplan/getNormTableItem', SbpestimateplanController::Class.'@getNormTableItem');
    Route::post('/sbpestimateplan/get-list', SbpestimateplanController::Class.'@getList');
    Route::get('/sbpestimateplan/load-temp/{id}', SbpestimateplanController::Class.'@loadTemp');
    Route::post('/sbpestimateplan/get-list-regu', SbpestimateplanController::Class.'@getlistregu');
    //regu
    Route::get('/sbpreguplan/create', SbpreguplanController::Class.'@create');
    Route::post('/sbpreguplan/get-list-regu', SbpreguplanController::Class.'@getlistregu');
    Route::post('/sbpreguplan/create-trans', SbpreguplanController::Class.'@createTrans');

});
