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

    //Customer
    Route::post('/customer', CustomerController::Class.'@list');
    Route::get('/customer/create', CustomerController::Class.'@create');
    Route::get('/customer/view/{id}', CustomerController::Class.'@view');
    Route::get('/customer/edit/{id}', CustomerController::Class.'@edit');
    Route::post('/customer/update/{id}', CustomerController::Class.'@update');
    Route::post('/customer/delete/{id}', CustomerController::Class.'@delete');
    Route::post('/customer/delete', CustomerController::Class.'@deleteArray');
    Route::post('/customer/store', CustomerController::Class.'@store');
    Route::post('/customer/customer-contact/{id}', CustomerController::Class.'@updateContact');
    Route::post('/customer/customer-link/{id}', CustomerController::Class.'@updateLink');
    Route::post('/customer/customer-sales/{id}', CustomerController::Class.'@updateSales');
    Route::post('/customer/customer-salestrans-delete', CustomerController::Class.'@deleteSalesTrans');
    Route::post('/customer/customer-contract/{id}', CustomerController::Class.'@updateContract');
    Route::post('/customer/customer-contracttrans-delete', CustomerController::Class.'@deleteContractTrans');
    Route::post('/customer/customer-quotation/{id}', CustomerController::Class.'@updateQuotation');
    Route::post('/customer/customer-quotationtrans-delete', CustomerController::Class.'@deleteQuotationTrans');
    Route::post('/customer/customer-upload-file/{id}', CustomerController::Class.'@uploadFile');
    Route::post('/customer/delete-file/{id}', CustomerController::Class.'@deleteFile');
    Route::get('/customer/download-file/{id}', CustomerController::Class.'@downloadFile');
    Route::get('/customer/download-all-file/{id}', CustomerController::Class.'@downloadAllFile');
    Route::post('/customer/customer-upload-video/{id}', CustomerController::Class.'@uploadVideo');
    Route::post('/customer/delete-video/{id}', CustomerController::Class.'@deleteVideo');
    Route::get('/customer/download-video/{id}', CustomerController::Class.'@downloadVideo');
    Route::post('/customer/update-per/{id}', CustomerController::Class.'@updatePer');
    Route::get('/customer/select-all-file-transid/{id}', CustomerController::Class.'@selectFileTransID');
    Route::post('/customer/customer-support/{id}', CustomerController::Class.'@updateSupport');
    Route::post('/customer/customer-supporttrans-delete', CustomerController::Class.'@deleteSupportTrans');
    Route::get('/customer/customer-cate', CustomerController::Class.'@getCustomerCate');
    Route::post('/customer/export-excel', CustomerController::Class.'@exportExcel');

    //Get list table
    Route::get('/customer/get-table', CustomerController::Class.'@getAllTable');
    //Get list uom
    Route::get('/customer/get-uom', CustomerController::Class.'@getAllUom');
    Route::get('/customer/get-allquotation', CustomerController::Class.'@getAllQuotation');

    //Opportunity
    Route::post('/opportunity', OpportunityController::Class . '@list');
    Route::get('/opportunity/create', OpportunityController::Class . '@create');
    Route::get('/opportunity/view/{id}', OpportunityController::Class . '@view');
    Route::get('/opportunity/edit/{id}', OpportunityController::Class . '@edit');
    Route::post('/opportunity/update/{id}', OpportunityController::Class . '@update');
    Route::post('/opportunity/delete/{id}', OpportunityController::Class . '@delete');
    Route::post('/opportunity/delete', OpportunityController::Class . '@deleteArray');
    Route::post('/opportunity/store', OpportunityController::Class . '@store');

    //SalesTrans
    Route::post('/salestrans', SalesTransController::Class . '@list');
    Route::get('/salestrans/create', SalesTransController::Class . '@create');
    Route::get('/salestrans/view/{id}', SalesTransController::Class . '@view');
    Route::get('/salestrans/edit/{id}', SalesTransController::Class . '@edit');
    Route::post('/salestrans/update/{id}', SalesTransController::Class . '@update');
    Route::post('/salestrans/delete/{id}', SalesTransController::Class . '@delete');
    Route::post('/salestrans/delete', SalesTransController::Class . '@deleteArray');
    Route::post('/salestrans/store', SalesTransController::Class . '@store');


    // commom
    Route::post('/common/get-province', CommonController::Class . '@getProvince');
    Route::post('/common/get-district', CommonController::Class . '@getDistrict');
    Route::post('/common/get-commune', CommonController::Class . '@getCommune');

});
