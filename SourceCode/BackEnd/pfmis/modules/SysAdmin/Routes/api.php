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
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/user/create', \UserController::Class.'@store');
    Route::get('/logout', \AuthController::Class.'@logout');
    Route::post('/users', UserController::Class.'@list');
    Route::post('/users/info', UserController::Class.'@getInfoUser');
    Route::get('/users/create', UserController::Class.'@create');
    Route::post('/users/store', UserController::Class.'@store');
    Route::post('/users/update/{id}', UserController::Class.'@update');
    Route::get('/users/change-password', UserController::Class.'@changePassword');
    Route::post('/users/update-password', UserController::Class.'@updatePassword');
    Route::get('/users/edit/{id}', UserController::Class.'@edit');
    Route::get('/users/view/{id}', UserController::Class.'@view');
    Route::post('/users/set-group', UserController::Class.'@setGroup');
    Route::post('/users/delete/{id}', UserController::Class.'@delete');
    Route::post('/users/delete', UserController::Class.'@deleteArray');

    //Group User
    Route::post('/group-user', SysUserGroupController::Class.'@list');
    Route::get('/group-user/create', SysUserGroupController::Class.'@create');
    Route::get('/group-user/edit/{id}', SysUserGroupController::Class.'@edit');
    Route::get('/group-user/view/{id}', SysUserGroupController::Class.'@edit');
    Route::post('/group-user/store', SysUserGroupController::Class.'@store');
    Route::post('/group-user/update/{id}', SysUserGroupController::Class.'@update');
    Route::post('/group-user/delete', SysUserGroupController::Class.'@deleteArray');

    //Setup
    Route::post('/setup', SysSetupController::Class.'@list');
    Route::get('/setup/edit/{id}', SysSetupController::Class.'@edit');
    Route::post('/setup/update/{id}', SysSetupController::Class.'@update');
    Route::post('/setup/update', SysSetupController::Class.'@updateAll');
    //Auto Number
    Route::post('/auto-number', SysAutoNumberController::Class.'@list');
    Route::post('/auto-number/update/{id}', SysAutoNumberController::Class.'@saveAutoNumber');
    Route::get('/auto-number/edit/{id}', SysAutoNumberController::Class.'@edit');

    Route::get('/menu', SysMenuController::Class.'@list');


    //Feature Status List
    Route::post('/fstatuslist', SysFeatureStatusController::Class.'@list');
    Route::get('/fstatuslist/create', SysFeatureStatusController::Class.'@create');
    Route::get('/fstatuslist/view/{id}', SysFeatureStatusController::Class.'@view');
    Route::get('/fstatuslist/edit/{id}', SysFeatureStatusController::Class.'@edit');
    Route::post('/fstatuslist/update/{id}', SysFeatureStatusController::Class.'@update');
    Route::post('/fstatuslist/delete/{id}', SysFeatureStatusController::Class.'@delete');
    Route::post('/fstatuslist/delete', SysFeatureStatusController::Class.'@deleteArray');
    Route::post('/fstatuslist/store', SysFeatureStatusController::Class.'@store');

    //Sys Status
    Route::post('/sys-status', SysStatusController::Class.'@list');
    Route::get('/sys-status/create', SysStatusController::Class.'@create');
    Route::get('/sys-status/view/{id}', SysStatusController::Class.'@view');
    Route::get('/sys-status/edit/{id}', SysStatusController::Class.'@edit');
    Route::post('/sys-status/update/{id}', SysStatusController::Class.'@update');
    Route::post('/sys-status/delete/{id}', SysStatusController::Class.'@delete');
    Route::post('/sys-status/delete', SysStatusController::Class.'@deleteArray');
    Route::post('/sys-status/store', SysStatusController::Class.'@store');


    //Feature Status List
    Route::post('/workflow', WorkflowController::Class.'@list');
    Route::get('/workflow/create', WorkflowController::Class.'@create');
    Route::get('/workflow/view/{id}', WorkflowController::Class.'@view');
    Route::get('/workflow/edit/{id}', WorkflowController::Class.'@edit');
    Route::post('/workflow/update/{id}', WorkflowController::Class.'@update');
    Route::post('/workflow/delete/{id}', WorkflowController::Class.'@delete');
    Route::post('/workflow/delete', WorkflowController::Class.'@deleteArray');
    Route::post('/workflow/store', WorkflowController::Class.'@store');


    //Sys Table List
    Route::post('/table', SysTableFiledController::Class.'@list');

    //Sys Binary Data
    Route::post('/binarydata', BinaryDataController::Class.'@list');
    Route::get('/binarydata/create', BinaryDataController::Class.'@create');
    Route::get('/binarydata/view/{id}', BinaryDataController::Class.'@view');
    Route::get('/binarydata/edit/{id}', BinaryDataController::Class.'@edit');
    Route::post('/binarydata/update/{id}', BinaryDataController::Class.'@update');
    Route::post('/binarydata/delete/{id}', BinaryDataController::Class.'@delete');
    Route::post('/binarydata/delete', BinaryDataController::Class.'@deleteArray');
    Route::post('/binarydata/store', BinaryDataController::Class.'@store');

    // SysReport
    Route::post('/report', SysReportController::class.'@list');
    Route::get('/report/get-report-para', SysReportController::class.'@getReportPara');

});

Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');
