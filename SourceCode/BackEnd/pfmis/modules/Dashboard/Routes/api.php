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
    // Dashboard
    Route::post('/dashboard', DashboardController::Class.'@index');
    Route::post('/dashboard/get-district', DashboardController::Class.'@getDistrict');
    Route::post('/dashboard/get-commune', DashboardController::Class.'@getCommune');
    Route::post('/dashboard/get-revenue-cate', DashboardController::Class.'@getRevenueCate');

    // StateBudgetEstimates
    Route::post('/state-budget-estimate', StateBudgetEstimate::Class.'@index');
    Route::post('/state-budget-estimate/revenue-pie-chart', StateBudgetEstimate::Class.'@revenuePieChart');
    Route::post('/state-budget-estimate/expense-pie-chart', StateBudgetEstimate::Class.'@revenuePieChart');

    // thu
    Route::post('/state-budget-estimate/estimate-revenue', StateBudgetEstimate::Class.'@estimateRevenue');

    // chi
    Route::post('/state-budget-estimate/estimate-expense', StateBudgetEstimate::Class.'@estimateExpense');
    Route::post('/state-budget-estimate/estimate-expense/bieu-do-co-cau-chi', StateBudgetEstimate::Class.'@estimateExpenseBieuDoCoCauChi');

});
