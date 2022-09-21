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
    //Uom
    Route::post('/doc', DocController::Class.'@list');
    Route::get('/doc/create', DocController::Class.'@create');
    Route::get('/doc/view/{id}', DocController::Class.'@view');
    Route::get('/doc/edit/{id}', DocController::Class.'@edit');
    Route::post('/doc/update/{id}', DocController::Class.'@update');
    Route::post('/doc/delete/{id}', DocController::Class.'@delete');
    Route::post('/doc/delete', DocController::Class.'@deleteArray');
    Route::post('/doc/store', DocController::Class.'@store');
    Route::post('/doc/get-list', DocController::Class.'@getList');
    Route::get('/doc/get-per-parent', DocController::Class.'@getPerParent');
    Route::post('/doc/get-list-child', DocController::Class.'@getListChild');
    Route::post('/doc/get-doc-per', DocController::Class.'@getDocPer');
    Route::post('/doc/get-list-employee', DocController::Class.'@getListEmployee');
    Route::post('/doc/update-per/{id}', DocController::Class.'@updatePer');
    Route::post('/doc/update-doc-link/{id}', DocController::Class.'@updateDocLink');
    Route::post('/doc/doc-upload-file/{id}', DocController::Class.'@uploadFile');
    Route::post('/doc/delete-file/{id}', DocController::Class.'@deleteFile');
    Route::get('/doc/download-file/{id}', DocController::Class.'@downloadFile');
    Route::get('/doc/download-all-file/{id}', DocController::Class.'@downloadAllFile');

    Route::post('/doc/doc-upload-video/{id}', DocController::Class.'@uploadVideo');
    Route::post('/doc/delete-video/{id}', DocController::Class.'@deleteVideo');
    Route::get('/doc/download-video/{id}', DocController::Class.'@downloadVideo');
    Route::post('/doc/update-doc-cate/{id}', DocController::Class.'@updateDocCate');
});
