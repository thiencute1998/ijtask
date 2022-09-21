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
    // Accounting
    Route::post('/actgvouctrans', AccountingController::Class.'@list');
    Route::get('/actgvouctrans/create', AccountingController::Class.'@create');
    Route::get('/actgvouctrans/view/{id}', AccountingController::Class.'@view');
    Route::get('/actgvouctrans/edit/{id}', AccountingController::Class.'@edit');
    Route::post('/actgvouctrans/update/{id}', AccountingController::Class.'@update');
    Route::post('/actgvouctrans/delete/{id}', AccountingController::Class.'@delete');
    Route::post('/actgvouctrans/delete', AccountingController::Class.'@deleteArray');
    Route::post('/actgvouctrans/store', AccountingController::Class.'@store');
    Route::post('/actgvouctrans/get-report-data', AccountingController::Class.'@getReportData');
    Route::post('/actgvouctrans/get-detail-report-data', AccountingController::Class.'@getDetailReportData');
    Route::post('/actgvouctrans/actbook', AccountingController::Class.'@actbook');

    // Accounting InTransType
    Route::post('/actintranstype', AccountingInTransTypeController::Class.'@list');
    Route::get('/actintranstype/create', AccountingInTransTypeController::Class.'@create');
    Route::get('/actintranstype/view/{id}', AccountingInTransTypeController::Class.'@view');
    Route::get('/actintranstype/edit/{id}', AccountingInTransTypeController::Class.'@edit');
    Route::post('/actintranstype/update/{id}', AccountingInTransTypeController::Class.'@update');
    Route::post('/actintranstype/delete/{id}', AccountingInTransTypeController::Class.'@delete');
    Route::post('/actintranstype/delete', AccountingInTransTypeController::Class.'@deleteArray');
    Route::post('/actintranstype/store', AccountingInTransTypeController::Class.'@store');
    Route::post('/actintranstype/get-report-data', AccountingInTransTypeController::Class.'@getReportData');
    Route::post('/actintranstype/get-detail-report-data', AccountingInTransTypeController::Class.'@getDetailReportData');

    // Accounting Autoact
    Route::post('/actautoact', AccountingAutoactController::Class.'@list');
    Route::get('/actautoact/create', AccountingAutoactController::Class.'@create');
    Route::get('/actautoact/view/{id}', AccountingAutoactController::Class.'@view');
    Route::get('/actautoact/edit/{id}', AccountingAutoactController::Class.'@edit');
    Route::post('/actautoact/update/{id}', AccountingAutoactController::Class.'@update');
    Route::post('/actautoact/delete/{id}', AccountingAutoactController::Class.'@delete');
    Route::post('/actautoact/delete', AccountingAutoactController::Class.'@deleteArray');
    Route::post('/actautoact/store', AccountingAutoactController::Class.'@store');
    Route::post('/actautoact/get-autoact-type', AccountingAutoactController::Class.'@getAutoactType');
    Route::post('/actautoact/get-report-data', AccountingAutoactController::Class.'@getReportData');
    Route::post('/actautoact/get-detail-report-data', AccountingAutoactController::Class.'@getDetailReportData');

    // Accounting CFAccount
    Route::post('/actcfaccount', AccountingCFAccountController::Class.'@list');
    Route::get('/actcfaccount/create', AccountingCFAccountController::Class.'@create');
    Route::get('/actcfaccount/view/{id}', AccountingCFAccountController::Class.'@view');
    Route::get('/actcfaccount/edit/{id}', AccountingCFAccountController::Class.'@edit');
    Route::post('/actcfaccount/update/{id}', AccountingCFAccountController::Class.'@update');
    Route::post('/actcfaccount/delete/{id}', AccountingCFAccountController::Class.'@delete');
    Route::post('/actcfaccount/delete', AccountingCFAccountController::Class.'@deleteArray');
    Route::post('/actcfaccount/store', AccountingCFAccountController::Class.'@store');
    Route::post('/actcfaccount/get-report-data', AccountingCFAccountController::Class.'@getReportData');
    Route::post('/actcfaccount/get-detail-report-data', AccountingCFAccountController::Class.'@getDetailReportData');

    // Accounting CCAccount
    Route::post('/actccaccount', AccountingCCAccountController::Class.'@list');
    Route::get('/actccaccount/create', AccountingCCAccountController::Class.'@create');
    Route::get('/actccaccount/view/{id}', AccountingCCAccountController::Class.'@view');
    Route::get('/actccaccount/edit/{id}', AccountingCCAccountController::Class.'@edit');
    Route::post('/actccaccount/update/{id}', AccountingCCAccountController::Class.'@update');
    Route::post('/actccaccount/delete/{id}', AccountingCCAccountController::Class.'@delete');
    Route::post('/actccaccount/delete', AccountingCCAccountController::Class.'@deleteArray');
    Route::post('/actccaccount/store', AccountingCCAccountController::Class.'@store');
    Route::post('/actccaccount/get-report-data', AccountingCCAccountController::Class.'@getReportData');
    Route::post('/actccaccount/get-detail-report-data', AccountingCCAccountController::Class.'@getDetailReportData');
});
