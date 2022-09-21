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
    //Feature Status List
    Route::post('/dataflow', TaskDataflowController::Class.'@list');
    Route::post('/dataflow/getAllDataflow', TaskDataflowController::Class.'@getAllDataflow');
    Route::get('/dataflow/workflow', TaskDataflowController::Class.'@workflow');
    Route::post('/dataflow/taskDetail', TaskDataflowController::Class.'@taskDetail');
    Route::post('/dataflow/taskSchedule', TaskDataflowController::Class.'@taskSchedule');
    Route::post('/dataflow/redoDataflow', TaskDataflowController::Class.'@redoDataflow');
    Route::post('/dataflow/createDataflow', TaskDataflowController::Class.'@createDataflow');
    Route::post('/dataflow/createFeature', TaskDataflowController::Class.'@createFeature');
    Route::post('/dataflow/updateFeatureStatus', TaskDataflowController::Class.'@updateFeatureStatus');
    Route::post('/dataflow/endDataflow', TaskDataflowController::Class.'@endDataflow');
    Route::get('/dataflow/updateStatusDataflow/{id}', TaskDataflowController::Class.'@updateStatusDataflow');
    Route::get('/dataflow/edit/{id}', TaskDataflowController::Class.'@edit');
    Route::post('/dataflow/update/{id}', TaskDataflowController::Class.'@update');
    Route::post('/dataflow/get-trans', TaskDataflowController::Class.'@getTrans');
    Route::post('/dataflow/update-trans', TaskDataflowController::Class.'@updateTrans');
    Route::post('/dataflow/get-list-dataflow', TaskDataflowController::Class.'@getListDataflow');
//    Route::get('/dataflow/create', TaskDataflow::Class.'@create');
//    Route::get('/dataflow/create', TaskDataflow::Class.'@create');
//    Route::get('/dataflow/create', TaskDataflow::Class.'@create');
//    Route::get('/dataflow/create', TaskDataflow::Class.'@create');
//    Route::get('/dataflow/create', TaskDataflow::Class.'@create');
//    Route::get('/dataflow/create', TaskDataflow::Class.'@create');
    Route::post('/dataflow/delete/{id}', TaskDataflowController::Class.'@delete');
    Route::post('/dataflow/delete', TaskDataflowController::Class.'@deleteArray');

    //Task
    Route::post('/task', TaskController::Class.'@list');
    Route::post('/dashboard-statistic', TaskController::Class.'@dashboardStatistic');
    Route::post('/task/create', TaskController::Class.'@create');
    Route::post('/task/store', TaskController::Class.'@store');
    Route::get('/task/view/{id}', TaskController::Class.'@view');
    Route::post('/task/update/{id}', TaskController::Class.'@update');
    Route::post('/task/delete', TaskController::Class.'@delete');
    Route::post('/task/list-modal', TaskController::Class.'@listModal');
    Route::post('/task/get-hour', TaskController::Class.'@getHour');
    Route::post('/task/task-assign/{id}', TaskController::Class.'@updateAssign');
    Route::post('/task/task-plan/{id}', TaskController::Class.'@updatePlan');
    Route::post('/task/get-task-plan/{id}', TaskController::Class.'@getPlan');
    Route::post('/task/get-plan-estimated-quantity', TaskController::Class.'@getPlanEstimatedQuantity');
    Route::post('/task/task-request/{id}', TaskController::Class.'@updateRequest');
    Route::post('/task/task-check-list/{id}', TaskController::Class.'@updateCheckList');
    Route::post('/task/task-expense/{id}', TaskController::Class.'@updateExpense');
    Route::post('/task/task-execution/{id}', TaskController::Class.'@updateExecution');
    Route::post('/task/task-execution-delete', TaskController::Class.'@deleteExecution');
    Route::post('/task/task-data-list/{id}', TaskController::Class.'@updateLink');
    Route::post('/task/task-upload-file/{id}', TaskController::Class.'@uploadFile');
    Route::post('/task/delete-file/{id}', TaskController::Class.'@deleteFile');
    Route::get('/task/download-file/{id}', TaskController::Class.'@downloadFile');
    Route::get('/task/download-all-file/{id}', TaskController::Class.'@downloadAllFile');
    Route::post('/task/table-field-per', TaskController::Class.'@tableFieldPer');
    Route::post('/task/task-per-content', TaskController::Class.'@taskPerContent');
    Route::post('/task/task-per-update', TaskController::Class.'@taskPerUpdate');
    Route::post('/task/update-evaluation/{id}', TaskController::Class.'@updateEvaluation');
    Route::post('/task/get-evaluation-1job', TaskController::Class.'@getEvaluation1Job');
    Route::post('/task/fetch-evaluation-1job', TaskController::Class.'@fetchEvaluation1Job');
    Route::post('/task/fetch-evaluation-1job-content', TaskController::Class.'@fetchEvaluation1JobContent');
    Route::post('/task/task-indicator', TaskController::Class.'@updateIndicator');
    Route::post('/task/get-list-child', TaskController::Class.'@getListChild');

    Route::post('/task/task-upload-video/{id}', TaskController::Class.'@uploadVideo');
    Route::post('/task/delete-video/{id}', TaskController::Class.'@deleteVideo');
    Route::get('/task/download-video/{id}', TaskController::Class.'@downloadVideo');

    Route::post('/task/comment', TaskController::Class.'@comment');
    Route::post('/task/storeComment', TaskController::Class.'@storeComment');
    Route::post('/task/editComment', TaskController::Class.'@editComment');
    Route::post('/task/deleteComment', TaskController::Class.'@deleteComment');

    //Get list table
    Route::get('/task/get-table', TaskController::Class.'@getAllTable');
    //Get list uom
    Route::get('/task/get-uom', TaskController::Class.'@getAllUom');

    //Indicator
    Route::post('/indicator', IndicatorController::Class.'@list');
//    Route::post('/indicator', function (){
//        return 'erro';
//    });
    Route::get('/indicator/create', IndicatorController::Class.'@create');
    Route::get('/indicator/view/{id}', IndicatorController::Class.'@view');
    Route::get('/indicator/edit/{id}', IndicatorController::Class.'@edit');
    Route::post('/indicator/update/{id}', IndicatorController::Class.'@update');
    Route::post('/indicator/delete/{id}', IndicatorController::Class.'@delete');
    Route::post('/indicator/delete', IndicatorController::Class.'@deleteArray');
    Route::post('/indicator/store', IndicatorController::Class.'@store');

    //Indicator Cate List
    Route::post('/indicator-cate-list', IndicatorCateListController::Class.'@list');
//    Route::post('/indicator-cate-list', function (){return 'zzzzz';});
    Route::get('/indicator-cate-list/create', IndicatorCateListController::Class.'@create');
    Route::get('/indicator-cate-list/view/{id}', IndicatorCateListController::Class.'@view');
    Route::get('/indicator-cate-list/edit/{id}', IndicatorCateListController::Class.'@edit');
    Route::post('/indicator-cate-list/update/{id}', IndicatorCateListController::Class.'@update');
    Route::post('/indicator-cate-list/delete/{id}', IndicatorCateListController::Class.'@delete');
    Route::post('/indicator-cate-list/delete', IndicatorCateListController::Class.'@deleteArray');
    Route::post('/indicator-cate-list/store', IndicatorCateListController::Class.'@store');

    //Indicator temp
    Route::post('/indicator-temp', IndicatorTempController::Class.'@list');
    Route::get('/indicator-temp/create', IndicatorTempController::Class.'@create');
    Route::get('/indicator-temp/view/{id}', IndicatorTempController::Class.'@view');
    Route::get('/indicator-temp/edit/{id}', IndicatorTempController::Class.'@edit');
    Route::post('/indicator-temp/update/{id}', IndicatorTempController::Class.'@update');
    Route::post('/indicator-temp/delete/{id}', IndicatorTempController::Class.'@delete');
    Route::post('/indicator-temp/delete', IndicatorTempController::Class.'@deleteArray');
    Route::post('/indicator-temp/store', IndicatorTempController::Class.'@store');
    Route::post('/indicator-temp/get-list', IndicatorTempController::Class.'@getList');

    //Indicator tempitem
    Route::post('/indicator-tempitem', IndicatorTempItemController::Class.'@list');
    Route::get('/indicator-tempitem/create', IndicatorTempItemController::Class.'@create');
    Route::get('/indicator-tempitem/view/{id}', IndicatorTempItemController::Class.'@view');
    Route::get('/indicator-tempitem/edit/{id}', IndicatorTempItemController::Class.'@edit');
    Route::post('/indicator-tempitem/update/{id}', IndicatorTempItemController::Class.'@update');
    Route::post('/indicator-tempitem/delete/{id}', IndicatorTempItemController::Class.'@delete');
    Route::post('/indicator-tempitem/delete', IndicatorTempItemController::Class.'@deleteArray');
    Route::post('/indicator-tempitem/store', IndicatorTempItemController::Class.'@store');

    // Common
    Route::post('/common/get-employee', CommonController::Class.'@getEmployee');
    Route::get('/common/get-all-employee', CommonController::Class.'@getAllEmployee');
    Route::post('/common/get-company', CommonController::Class.'@getCompany');
    Route::post('/common/get-project', CommonController::Class.'@getProject');
    Route::post('/common/get-contract', CommonController::Class.'@getContract');
    Route::post('/common/get-customer', CommonController::Class.'@getCustomer');
    Route::post('/common/get-vendor', CommonController::Class.'@getVendor');
    Route::post('/common/get-object', CommonController::Class.'@getObject');
    Route::post('/common/get-item', CommonController::Class.'@getItem');
    Route::post('/common/get-doc', CommonController::Class.'@getDoc');
    Route::get('/common/get-task-cate-list', CommonController::Class.'@getTaskCateList');
    Route::get('/common/get-sys-status', CommonController::Class.'@getSysStatus');
    Route::post('/common/get-task-indicator-table', CommonController::Class.'@getTaskIndicatorTable');
    Route::post('/common/get-task-indicator-table-item', CommonController::Class.'@getTaskIndicatorTableItem');

    //Indicator tempitemkeyresult
    Route::post('/indicator-tempitemkeyresult', IndicatorTempItemKeyresultController::Class.'@list');
    Route::get('/indicator-tempitemkeyresult/create', IndicatorTempItemKeyresultController::Class.'@create');
    Route::get('/indicator-tempitemkeyresult/view/{id}', IndicatorTempItemKeyresultController::Class.'@view');
    Route::get('/indicator-tempitemkeyresult/edit/{id}', IndicatorTempItemKeyresultController::Class.'@edit');
    Route::post('/indicator-tempitemkeyresult/update/{id}', IndicatorTempItemKeyresultController::Class.'@update');
    Route::post('/indicator-tempitemkeyresult/delete/{id}', IndicatorTempItemKeyresultController::Class.'@delete');
    Route::post('/indicator-tempitemkeyresult/delete', IndicatorTempItemKeyresultController::Class.'@deleteArray');
    Route::post('/indicator-tempitemkeyresult/store', IndicatorTempItemKeyresultController::Class.'@store');

    //Indicator Table
    Route::post('/indicator-table', IndicatorTableController::Class.'@list');
    Route::get('/indicator-table/create', IndicatorTableController::Class.'@create');
    Route::get('/indicator-table/view/{id}', IndicatorTableController::Class.'@view');
    Route::get('/indicator-table/edit/{id}', IndicatorTableController::Class.'@edit');
    Route::post('/indicator-table/update/{id}', IndicatorTableController::Class.'@update');
    Route::post('/indicator-table/delete/{id}', IndicatorTableController::Class.'@delete');
    Route::post('/indicator-table/delete', IndicatorTableController::Class.'@deleteArray');
    Route::post('/indicator-table/store', IndicatorTableController::Class.'@store');
    Route::get('/indicator-table/load-temp/{id}', IndicatorTableController::Class.'@loadTemp');
    Route::post('/indicator-table/table-per', IndicatorTableController::Class.'@getTablePer');
    Route::post('/indicator-table/update-per', IndicatorTableController::Class.'@updateTablePer');

    //Evaluation_1job
    Route::post('/evaluation-1job', Evaluation1jobController::Class.'@list');
    Route::get('/evaluation-1job/create', Evaluation1jobController::Class.'@create');
    Route::get('/evaluation-1job/view/{id}', Evaluation1jobController::Class.'@view');
    Route::get('/evaluation-1job/edit/{id}', Evaluation1jobController::Class.'@edit');
    Route::post('/evaluation-1job/update/{id}', Evaluation1jobController::Class.'@update');
    Route::post('/evaluation-1job/delete/{id}', Evaluation1jobController::Class.'@delete');
    Route::post('/evaluation-1job/delete', Evaluation1jobController::Class.'@deleteArray');
    Route::post('/evaluation-1job/store', Evaluation1jobController::Class.'@store');

    //Evaluation Trans
    Route::post('/evaluation-trans', EvaluationTransController::Class.'@list');
    Route::post('/evaluation-trans/create', EvaluationTransController::Class.'@create');
    Route::get('/evaluation-trans/view/{id}', EvaluationTransController::Class.'@view');
    Route::get('/evaluation-trans/edit/{id}', EvaluationTransController::Class.'@edit');
    Route::post('/evaluation-trans/update/{id}', EvaluationTransController::Class.'@update');
    Route::post('/evaluation-trans/delete/{id}', EvaluationTransController::Class.'@delete');
    Route::post('/evaluation-trans/delete', EvaluationTransController::Class.'@deleteArray');
    Route::post('/evaluation-trans/store', EvaluationTransController::Class.'@store');
    Route::post('/evaluation-trans/get-temp', EvaluationTransController::Class.'@getTemp');
    Route::post('/evaluation-trans/load-temp', EvaluationTransController::Class.'@loadTemp');
    Route::post('/evaluation-trans/load-indicator', EvaluationTransController::Class.'@loadIndicator');
    Route::post('/evaluation-trans/trans-per', EvaluationTransController::Class.'@getTransPer');
    Route::post('/evaluation-trans/get-list', EvaluationTransController::Class.'@getList');
    Route::post('/evaluation-trans/get-task', EvaluationTransController::Class.'@getTask');
    Route::post('/evaluation-trans/cal-evaluation', EvaluationTransController::Class.'@calEvaluation');

    //Scale Rate
    Route::post('/scalerate', ScaleRateController::Class.'@list');
    Route::get('/scalerate/create', ScaleRateController::Class.'@create');
    Route::get('/scalerate/view/{id}', ScaleRateController::Class.'@view');
    Route::get('/scalerate/edit/{id}', ScaleRateController::Class.'@edit');
    Route::post('/scalerate/update/{id}', ScaleRateController::Class.'@update');
    Route::post('/scalerate/delete/{id}', ScaleRateController::Class.'@delete');
    Route::post('/scalerate/delete', ScaleRateController::Class.'@deleteArray');
    Route::post('/scalerate/store', ScaleRateController::Class.'@store');

});
