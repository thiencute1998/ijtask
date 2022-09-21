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
    //Company
    Route::post('/company', CompanyController::Class.'@list');
    Route::get('/company/create', CompanyController::Class.'@create');
    Route::get('/company/view/{id}', CompanyController::Class.'@view');
    Route::get('/company/edit/{id}', CompanyController::Class.'@edit');
    Route::post('/company/update/{id}', CompanyController::Class.'@update');
    Route::post('/company/delete/{id}', CompanyController::Class.'@delete');
    Route::post('/company/delete', CompanyController::Class.'@deleteArray');
    Route::post('/company/store', CompanyController::Class.'@store');
    Route::get('/company/get-company-cate-list',CompanyController::Class.'@getCompanyCateList');
    Route::get('/company/get-company-link/{id}',CompanyController::Class.'@getCompanyLink');
    Route::post('/company/company-data-list',CompanyController::Class.'@updateLink');
    Route::get('/company/get-company-file/{id}',CompanyController::Class.'@getCompanyFile');
    Route::post('/company/company-upload-file/{id}', CompanyController::Class.'@uploadFile');
    Route::post('/company/delete-file/{id}', CompanyController::Class.'@deleteFile');
    Route::get('/company/download-file/{id}', CompanyController::Class.'@downloadFile');
    Route::get('/company/download-all-file/{id}', CompanyController::Class.'@downloadAllFile');
    Route::get('/company/get-company-video/{id}', CompanyController::Class.'@getVideo');
    Route::post('/company/company-upload-video/{id}', CompanyController::Class.'@uploadVideo');
    Route::post('/company/delete-video/{id}', CompanyController::Class.'@deleteVideo');
    Route::get('/company/download-video/{id}', CompanyController::Class.'@downloadVideo');
    Route::post('/company/update-per/{id}', CompanyController::Class.'@updatePer');
    Route::post('/company/get-report-data', CompanyController::Class.'@getReportData');
    Route::post('/company/get-cate-value',CompanyController::Class.'@getCateValueCompany');
    Route::post('/company/get-list-child',CompanyController::Class.'@getListChild');
    Route::post('/company/get-org',CompanyController::Class.'@getOrg');

    //Employee
    Route::post('/employee', EmployeeController::Class.'@list');
    Route::get('/employee/create', EmployeeController::Class.'@create');
    Route::get('/employee/view/{id}', EmployeeController::Class.'@view');
    Route::get('/employee/edit/{id}', EmployeeController::Class.'@edit');
    Route::post('/employee/update/{id}', EmployeeController::Class.'@update');
    Route::post('/employee/delete/{id}', EmployeeController::Class.'@delete');
    Route::post('/employee/delete', EmployeeController::Class.'@deleteArray');
    Route::post('/employee/store', EmployeeController::Class.'@store');
    Route::post('/employee/employee-data-list', EmployeeController::Class.'@updateLink');
    Route::get('/employee/get-employee-link/{id}', EmployeeController::Class.'@getLink');
    Route::get('/employee/get-employee-file/{id}', EmployeeController::Class.'@getFile');
    Route::get('/employee/get-employee-cate-list', EmployeeController::Class.'@getCate');
    Route::post('/employee/employee-upload-file/{id}', EmployeeController::Class.'@uploadFile');
    Route::post('/employee/delete-file/{id}', EmployeeController::Class.'@deleteFile');
    Route::get('/employee/download-file/{id}', EmployeeController::Class.'@downloadFile');
    Route::get('/employee/download-all-file/{id}', EmployeeController::Class.'@downloadAllFile');
    Route::get('/employee/get-employee-video/{id}', EmployeeController::Class.'@getVideo');
    Route::post('/employee/employee-upload-video/{id}', EmployeeController::Class.'@uploadVideo');
    Route::post('/employee/delete-video/{id}', EmployeeController::Class.'@deleteVideo');
    Route::get('/employee/download-video/{id}', EmployeeController::Class.'@downloadVideo');
    Route::post('/employee/update-doc-cate/{id}', EmployeeController::Class.'@updateDocCate');
    Route::post('/employee/get-report-data', EmployeeController::Class.'@getReportData');
    Route::post('/employee/get-user', EmployeeController::Class.'@getUser');

    //Vendor
    Route::post('/vendor', VendorController::Class.'@list');
    Route::get('/vendor/create', VendorController::Class.'@create');
    Route::get('/vendor/view/{id}', VendorController::Class.'@view');
    Route::get('/vendor/edit/{id}', VendorController::Class.'@edit');
    Route::post('/vendor/update/{id}', VendorController::Class.'@update');
    Route::post('/vendor/delete/{id}', VendorController::Class.'@delete');
    Route::post('/vendor/delete', VendorController::Class.'@deleteArray');
    Route::post('/vendor/store', VendorController::Class.'@store');
    Route::post('/vendor/vendor-data-list', VendorController::Class.'@updateLink');
    Route::get('/vendor/get-vendor-link/{id}', VendorController::Class.'@getLink');
    Route::get('/vendor/get-vendor-file/{id}', VendorController::Class.'@getFile');
    Route::get('/vendor/get-cate-list', VendorController::Class.'@getCate');
    Route::post('/vendor/vendor-upload-file/{id}', VendorController::Class.'@uploadFile');
    Route::post('/vendor/delete-file/{id}', VendorController::Class.'@deleteFile');
    Route::get('/vendor/download-file/{id}', VendorController::Class.'@downloadFile');
    Route::get('/vendor/download-all-file/{id}', VendorController::Class.'@downloadAllFile');
    Route::get('/vendor/get-vendor-video/{id}', VendorController::Class.'@getVideo');
    Route::post('/vendor/vendor-upload-video/{id}', VendorController::Class.'@uploadVideo');
    Route::post('/vendor/delete-video/{id}', VendorController::Class.'@deleteVideo');
    Route::get('/vendor/download-video/{id}', VendorController::Class.'@downloadVideo');
    Route::post('/vendor/update-doc-cate/{id}', VendorController::Class.'@updateDocCate');
    Route::post('/vendor/update-per/{id}', VendorController::Class.'@updatePer');
    Route::post('/vendor/get-report-data', VendorController::Class.'@getReportData');
    Route::post('/vendor/get-list-child',VendorController::Class.'@getListChild');
    //customer
    Route::post('/customer', CustomerController::Class.'@list');
    Route::get('/customer/create', CustomerController::Class.'@create');
    Route::get('/customer/view/{id}', CustomerController::Class.'@view');
    Route::get('/customer/edit/{id}', CustomerController::Class.'@edit');
    Route::post('/customer/update/{id}', CustomerController::Class.'@update');
    Route::post('/customer/delete/{id}', CustomerController::Class.'@delete');
    Route::post('/customer/delete', CustomerController::Class.'@deleteArray');
    Route::post('/customer/store', CustomerController::Class.'@store');
    Route::post('/customer/customer-data-list', CustomerController::Class.'@updateLink');
    Route::get('/customer/get-customer-link/{id}', CustomerController::Class.'@getLink');
    Route::get('/customer/get-customer-file/{id}', CustomerController::Class.'@getFile');
    Route::get('/customer/get-customer-cate-list', CustomerController::Class.'@getCate');
    Route::post('/customer/customer-upload-file/{id}', CustomerController::Class.'@uploadFile');
    Route::post('/customer/delete-file/{id}', CustomerController::Class.'@deleteFile');
    Route::get('/customer/download-file/{id}', CustomerController::Class.'@downloadFile');
    Route::get('/customer/download-all-file/{id}', CustomerController::Class.'@downloadAllFile');
    Route::get('/customer/get-customer-video/{id}', CustomerController::Class.'@getVideo');
    Route::post('/customer/customer-upload-video/{id}', CustomerController::Class.'@uploadVideo');
    Route::post('/customer/delete-video/{id}', CustomerController::Class.'@deleteVideo');
    Route::get('/customer/download-video/{id}', CustomerController::Class.'@downloadVideo');
    Route::post('/customer/update-doc-cate/{id}', CustomerController::Class.'@updateDocCate');
    Route::post('/customer/update-per/{id}', CustomerController::Class.'@updatePer');
    Route::post('/customer/get-report-data', CustomerController::Class.'@getReportData');


    //Get list table
    Route::get('/customer/get-table', CustomerController::Class.'@getAllTable');
    //Get list uom
    Route::get('/customer/get-uom', CustomerController::Class.'@getAllUom');
    Route::get('/customer/get-allquotation', CustomerController::Class.'@getAllQuotation');
    //Object
    Route::post('/object', ObjectController::Class.'@list');
    Route::get('/object/create', ObjectController::Class.'@create');
    Route::get('/object/view/{id}', ObjectController::Class.'@view');
    Route::get('/object/edit/{id}', ObjectController::Class.'@edit');
    Route::post('/object/update/{id}', ObjectController::Class.'@update');
    Route::post('/object/delete/{id}', ObjectController::Class.'@delete');
    Route::post('/object/delete', ObjectController::Class.'@deleteArray');
    Route::post('/object/store', ObjectController::Class.'@store');



    //Uom
    Route::post('/uom', UomController::Class.'@list');
    Route::get('/uom/create', UomController::Class.'@create');
    Route::get('/uom/view/{id}', UomController::Class.'@view');
    Route::get('/uom/edit/{id}', UomController::Class.'@edit');
    Route::post('/uom/update/{id}', UomController::Class.'@update');
    Route::post('/uom/delete/{id}', UomController::Class.'@delete');
    Route::post('/uom/delete', UomController::Class.'@deleteArray');
    Route::post('/uom/store', UomController::Class.'@store');
    Route::post('/uom/get-report-data', UomController::Class.'@getReportData');
    //Task Cate List
    Route::post('/task-cate-list', TaskCateListController::Class.'@list');
    Route::get('/task-cate-list/create', TaskCateListController::Class.'@create');
    Route::get('/task-cate-list/view/{id}', TaskCateListController::Class.'@view');
    Route::get('/task-cate-list/edit/{id}', TaskCateListController::Class.'@edit');
    Route::post('/task-cate-list/update/{id}', TaskCateListController::Class.'@update');
    Route::post('/task-cate-list/delete/{id}', TaskCateListController::Class.'@delete');
    Route::post('/task-cate-list/delete', TaskCateListController::Class.'@deleteArray');
    Route::post('/task-cate-list/store', TaskCateListController::Class.'@store');

    //Customer Cate List
    Route::post('/customer-cate-list', CustomerCateListController::Class.'@list');
    Route::get('/customer-cate-list/create', CustomerCateListController::Class.'@create');
    Route::get('/customer-cate-list/view/{id}', CustomerCateListController::Class.'@view');
    Route::get('/customer-cate-list/edit/{id}', CustomerCateListController::Class.'@edit');
    Route::post('/customer-cate-list/update/{id}', CustomerCateListController::Class.'@update');
    Route::post('/customer-cate-list/delete/{id}', CustomerCateListController::Class.'@delete');
    Route::post('/customer-cate-list/delete', CustomerCateListController::Class.'@deleteArray');
    Route::post('/customer-cate-list/store', CustomerCateListController::Class.'@store');
    Route::post('/customer-cate-list/get-list-child', CustomerCateListController::Class.'@getListChild');

    //Contract Cate List
    Route::post('/contract-cate-list', ContractCateListController::Class.'@list');
    Route::get('/contract-cate-list/create', ContractCateListController::Class.'@create');
    Route::get('/contract-cate-list/view/{id}', ContractCateListController::Class.'@view');
    Route::get('/contract-cate-list/edit/{id}', ContractCateListController::Class.'@edit');
    Route::post('/contract-cate-list/update/{id}', ContractCateListController::Class.'@update');
    Route::post('/contract-cate-list/delete/{id}', ContractCateListController::Class.'@delete');
    Route::post('/contract-cate-list/get-list-child', ContractCateListController::Class.'@getListChild');
    Route::post('/contract-cate-list/delete/', ContractCateListController::Class.'@deleteArray');
    Route::post('/contract-cate-list/store', ContractCateListController::Class.'@store');
    Route::post('/contract-cate-list/get-list-child', ContractCateListController::Class.'@getListChild');

    //Doc Cate List
    Route::post('/doc-cate-list', DocCateListController::Class.'@list');
    Route::get('/doc-cate-list/create', DocCateListController::Class.'@create');
    Route::get('/doc-cate-list/view/{id}', DocCateListController::Class.'@view');
    Route::get('/doc-cate-list/edit/{id}', DocCateListController::Class.'@edit');
    Route::post('/doc-cate-list/update/{id}', DocCateListController::Class.'@update');
    Route::post('/doc-cate-list/delete/{id}', DocCateListController::Class.'@delete');
    Route::post('/doc-cate-list/delete', DocCateListController::Class.'@deleteArray');
    Route::post('/doc-cate-list/store', DocCateListController::Class.'@store');

    //Project Demo
    Route::post('/projectdemo', ProjectdemoController::Class.'@list');
    Route::get('/projectdemo/create', ProjectdemoController::Class.'@create');
    Route::get('/projectdemo/view/{id}', ProjectdemoController::Class.'@view');
    Route::get('/projectdemo/edit/{id}', ProjectdemoController::Class.'@edit');
    Route::post('/projectdemo/update/{id}', ProjectdemoController::Class.'@update');
    Route::post('/projectdemo/delete/{id}', ProjectdemoController::Class.'@delete');
    Route::post('/projectdemo/delete', ProjectdemoController::Class.'@deleteArray');
    Route::post('/projectdemo/store', ProjectdemoController::Class.'@store');

    // Common
    Route::post('/common/status', CommonController::Class.'@status');
    Route::post('/common/list', CommonController::Class.'@list');
    Route::post('/common/list2', CommonController::Class.'@list2');
    Route::post('/common/list3', CommonController::Class.'@list3');
    Route::post('/common/listCate', CommonController::Class.'@listCate');
    Route::post('/common/auto-child', CommonController::Class.'@getAutoNoChild');
    Route::post('/common/auto-childtable', CommonController::Class.'@getAutoNoChildTable');
    Route::get('/common/get-table', CommonController::Class.'@getTable');
    Route::post('/common/get-province', CommonController::Class . '@getProvince');
    Route::post('/common/get-district', CommonController::Class . '@getDistrict');
    Route::post('/common/get-commune', CommonController::Class . '@getCommune');
    Route::post('/common/get-sector', CommonController::Class . '@getSector');
    Route::post('/common/get-list-employee', CommonController::Class.'@getListEmployee');
    Route::post('/common/get-report-template', CommonController::Class.'@getReportTemplate');
    Route::get('/common/get-workdate', CommonController::Class.'@getWorkdate');
    Route::post('/common/get-parent', CommonController::Class.'@getParent');
    Route::post('/common/get-norm-table',CommonController::Class.'@getNormTable');

    //Customer Sales Trans Cate List
    Route::post('/customer-sales-trans-cate-list', CustomerSalesTransCateListController::Class.'@list');
    Route::get('/customer-sales-trans-cate-list/create', CustomerSalesTransCateListController::Class.'@create');
    Route::get('/customer-sales-trans-cate-list/view/{id}', CustomerSalesTransCateListController::Class.'@view');
    Route::get('/customer-sales-trans-cate-list/edit/{id}', CustomerSalesTransCateListController::Class.'@edit');
    Route::post('/customer-sales-trans-cate-list/update/{id}', CustomerSalesTransCateListController::Class.'@update');
    Route::post('/customer-sales-trans-cate-list/delete/{id}', CustomerSalesTransCateListController::Class.'@delete');
    Route::post('/customer-sales-trans-cate-list/delete', CustomerSalesTransCateListController::Class.'@deleteArray');
    Route::post('/customer-sales-trans-cate-list/store', CustomerSalesTransCateListController::Class.'@store');

    //Customer Quotation Trans Cate List
    Route::post('/customer-quotation-trans-cate-list', CustomerQuotationTransCateListController::Class.'@list');
    Route::get('/customer-quotation-trans-cate-list/create', CustomerQuotationTransCateListController::Class.'@create');
    Route::get('/customer-quotation-trans-cate-list/view/{id}', CustomerQuotationTransCateListController::Class.'@view');
    Route::get('/customer-quotation-trans-cate-list/edit/{id}', CustomerQuotationTransCateListController::Class.'@edit');
    Route::post('/customer-quotation-trans-cate-list/update/{id}', CustomerQuotationTransCateListController::Class.'@update');
    Route::post('/customer-quotation-trans-cate-list/delete/{id}', CustomerQuotationTransCateListController::Class.'@delete');
    Route::post('/customer-quotation-trans-cate-list/delete', CustomerQuotationTransCateListController::Class.'@deleteArray');
    Route::post('/customer-quotation-trans-cate-list/store', CustomerQuotationTransCateListController::Class.'@store');

    //Customer Support Trans Cate List
    Route::post('/customer-support-trans-cate-list', CustomerSupportTransCateListController::Class.'@list');
    Route::get('/customer-support-trans-cate-list/create', CustomerSupportTransCateListController::Class.'@create');
    Route::get('/customer-support-trans-cate-list/view/{id}', CustomerSupportTransCateListController::Class.'@view');
    Route::get('/customer-support-trans-cate-list/edit/{id}', CustomerSupportTransCateListController::Class.'@edit');
    Route::post('/customer-support-trans-cate-list/update/{id}', CustomerSupportTransCateListController::Class.'@update');
    Route::post('/customer-support-trans-cate-list/delete/{id}', CustomerSupportTransCateListController::Class.'@delete');
    Route::post('/customer-support-trans-cate-list/delete', CustomerSupportTransCateListController::Class.'@deleteArray');
    Route::post('/customer-support-trans-cate-list/store', CustomerSupportTransCateListController::Class.'@store');

    //Customer Contract Trans Cate List
    Route::post('/customer-contract-trans-cate-list', CustomerContractTransCateListController::Class.'@list');
    Route::get('/customer-contract-trans-cate-list/create', CustomerContractTransCateListController::Class.'@create');
    Route::get('/customer-contract-trans-cate-list/view/{id}', CustomerContractTransCateListController::Class.'@view');
    Route::get('/customer-contract-trans-cate-list/edit/{id}', CustomerContractTransCateListController::Class.'@edit');
    Route::post('/customer-contract-trans-cate-list/update/{id}', CustomerContractTransCateListController::Class.'@update');
    Route::post('/customer-contract-trans-cate-list/delete/{id}', CustomerContractTransCateListController::Class.'@delete');
    Route::post('/customer-contract-trans-cate-list/delete', CustomerContractTransCateListController::Class.'@deleteArray');
    Route::post('/customer-contract-trans-cate-list/store', CustomerContractTransCateListController::Class.'@store');

    //Ccy
    Route::post('/ccy', CcyController::Class.'@list');
    Route::get('/ccy/create', CcyController::Class.'@create');
    Route::get('/ccy/view/{id}', CcyController::Class.'@view');
    Route::get('/ccy/edit/{id}', CcyController::Class.'@edit');
    Route::post('/ccy/update/{id}', CcyController::Class.'@update');
    Route::post('/ccy/delete/{id}', CcyController::Class.'@delete');
    Route::post('/ccy/delete', CcyController::Class.'@deleteArray');
    Route::post('/ccy/store', CcyController::Class.'@store');
    Route::post('/ccy/get-report-data', CcyController::Class.'@getReportData');

    //company Cate List
    Route::post('/company-cate-list', CompanyCateListController::Class.'@list');
    Route::get('/company-cate-list/create', CompanyCateListController::Class.'@create');
    Route::get('/company-cate-list/view/{id}', CompanyCateListController::Class.'@view');
    Route::get('/company-cate-list/edit/{id}', CompanyCateListController::Class.'@edit');
    Route::post('/company-cate-list/update/{id}', CompanyCateListController::Class.'@update');
    Route::post('/company-cate-list/delete/{id}', CompanyCateListController::Class.'@delete');
    Route::post('/company-cate-list/get-list-child', CompanyCateListController::Class.'@getListChild');
    Route::post('/company-cate-list/delete', CompanyCateListController::Class.'@deleteArray');
    Route::post('/company-cate-list/store', CompanyCateListController::Class.'@store');

    // Vendor Cate List
    Route::post('/vendor-cate-list', VendorCateListController::Class.'@list');
    Route::get('/vendor-cate-list/create', VendorCateListController::Class.'@create');
    Route::get('/vendor-cate-list/view/{id}', VendorCateListController::Class.'@view');
    Route::get('/vendor-cate-list/edit/{id}', VendorCateListController::Class.'@edit');
    Route::post('/vendor-cate-list/update/{id}', VendorCateListController::Class.'@update');
    Route::post('/vendor-cate-list/delete/{id}', VendorCateListController::Class.'@delete');
    Route::post('/vendor-cate-list/get-list-child', VendorCateListController::Class.'@getListChild');
    Route::post('/vendor-cate-list/delete', VendorCateListController::Class.'@deleteArray');
    Route::post('/vendor-cate-list/store', VendorCateListController::Class.'@store');

    // Partner
    Route::post('/partner', PartnerController::Class.'@list');
    Route::get('/partner/create', PartnerController::Class.'@create');
    Route::get('/partner/view/{id}', PartnerController::Class.'@view');
    Route::get('/partner/edit/{id}', PartnerController::Class.'@edit');
    Route::post('/partner/update/{id}', PartnerController::Class.'@update');
    Route::post('/partner/delete/{id}', PartnerController::Class.'@delete');
    Route::post('/partner/delete', PartnerController::Class.'@deleteArray');
    Route::post('/partner/store', PartnerController::Class.'@store');
    Route::post('/partner/partner-data-list', PartnerController::Class.'@updateLink');
    Route::get('/partner/get-partner-link/{id}', PartnerController::Class.'@getLink');
    Route::get('/partner/get-partner-file/{id}', PartnerController::Class.'@getFile');
    Route::get('/partner/get-cate-list', PartnerController::Class.'@getCate');
    Route::post('/partner/partner-upload-file/{id}', PartnerController::Class.'@uploadFile');
    Route::post('/partner/delete-file/{id}', PartnerController::Class.'@deleteFile');
    Route::get('/partner/download-file/{id}', PartnerController::Class.'@downloadFile');
    Route::get('/partner/download-all-file/{id}', PartnerController::Class.'@downloadAllFile');
    Route::get('/partner/get-partner-video/{id}', PartnerController::Class.'@getVideo');
    Route::post('/partner/partner-upload-video/{id}', PartnerController::Class.'@uploadVideo');
    Route::post('/partner/delete-video/{id}', PartnerController::Class.'@deleteVideo');
    Route::get('/partner/download-video/{id}', PartnerController::Class.'@downloadVideo');
    Route::post('/partner/update-doc-cate/{id}', PartnerController::Class.'@updateDocCate');
    Route::post('/partner/update-per/{id}', PartnerController::Class.'@updatePer');
    Route::post('/partner/get-report-data', PartnerController::Class.'@getReportData');
    Route::post('/partner/get-list-child',PartnerController::Class.'@getListChild');


    // ============================ Partner Cate List - Loại Đối Tác ============================================
    Route::post('/partner-cate-list', PartnerCateListController::Class.'@list');
    Route::get('/partner-cate-list/create', PartnerCateListController::Class.'@create');
    Route::get('/partner-cate-list/view/{id}', PartnerCateListController::Class.'@view');
    Route::get('/partner-cate-list/edit/{id}', PartnerCateListController::Class.'@edit');
    Route::post('/partner-cate-list/update/{id}', PartnerCateListController::Class.'@update');
    Route::post('/partner-cate-list/delete/{id}', PartnerCateListController::Class.'@delete');
    Route::post('/partner-cate-list/delete', PartnerCateListController::Class.'@deleteArray');
    Route::post('/partner-cate-list/store', PartnerCateListController::Class.'@store');

    // Department
    Route::post('/department', DepartmentController::Class.'@list');
    Route::get('/department/create', DepartmentController::Class.'@create');
    Route::get('/department/view/{id}', DepartmentController::Class.'@view');
    Route::get('/department/edit/{id}', DepartmentController::Class.'@edit');
    Route::post('/department/update/{id}', DepartmentController::Class.'@update');
    Route::post('/department/delete/{id}', DepartmentController::Class.'@delete');
    Route::post('/department/delete', DepartmentController::Class.'@deleteArray');
    Route::post('/department/store', DepartmentController::Class.'@store');
    Route::post('/department/get-report-data', DepartmentController::Class.'@getReportData');

    // ============================= Revenue- khoản thu =======================================
    Route::post('/revenue', RevenueController::Class.'@list');
    Route::get('/revenue/create', RevenueController::Class.'@create');
    Route::get('/revenue/view/{id}', RevenueController::Class.'@view');
    Route::get('/revenue/edit/{id}', RevenueController::Class.'@edit');
    Route::post('/revenue/update/{id}', RevenueController::Class.'@update');
    Route::post('/revenue/delete/{id}', RevenueController::Class.'@delete');
    Route::post('/revenue/delete', RevenueController::Class.'@deleteArray');
    Route::post('/revenue/store', RevenueController::Class.'@store');
    Route::post('/revenue/get-list-child', RevenueController::Class.'@getListChild');
    Route::get('/revenue/get-revenue-cate-list',RevenueController::Class.'@getRevenueCateList');
    Route::get('/revenue/get-revenue-cate-list-search',RevenueController::Class.'@getRevenueCateListSerach');
    Route::get('/revenue/get-revenue-link/{id}',RevenueController::Class.'@getRevenueLink');
    Route::post('/revenue/revenue-data-list',RevenueController::Class.'@updateLink');
    Route::post('/revenue/get-list-child', RevenueController::Class.'@getListChild');
    Route::post('/revenue/get-revenue-regu-item', RevenueController::Class.'@getRevenueReguItem');
    Route::post('/revenue/get-report-data', RevenueController::Class.'@getReportData');

    // ============================= Revenue cate list - Loại khoản thu =========================
    Route::post('/revenue-cate-list',RevenueCateListController::Class.'@list');
    Route::get('/revenue-cate-list/create',RevenueCateListController::Class.'@create');
    Route::get('/revenue-cate-list/view/{id}',RevenueCateListController::Class.'@view');
    Route::get('/revenue-cate-list/edit/{id}',RevenueCateListController::Class.'@edit');
    Route::post('/revenue-cate-list/get-list-child',RevenueCateListController::Class.'@getListChild');
    Route::post('/revenue-cate-list/update/{id}',RevenueCateListController::Class.'@update');
    Route::post('/revenue-cate-list/delete/{id}',RevenueCateListController::Class.'@delete');
    Route::post('/revenue-cate-list/delete',RevenueCateListController::Class.'@deleteArray');
    Route::post('/revenue-cate-list/store',RevenueCateListController::Class.'@store');

    // ============================= Expense- khoản chi =======================================
    Route::post('/expense', ExpenseController::Class.'@list');
    Route::get('/expense/create', ExpenseController::Class.'@create');
    Route::get('/expense/view/{id}', ExpenseController::Class.'@view');
    Route::get('/expense/edit/{id}', ExpenseController::Class.'@edit');
    Route::post('/expense/update/{id}', ExpenseController::Class.'@update');
    Route::post('/expense/delete/{id}', ExpenseController::Class.'@delete');
    Route::post('/expense/delete', ExpenseController::Class.'@deleteArray');
    Route::post('/expense/store', ExpenseController::Class.'@store');
    Route::post('/expense/get-list-child', ExpenseController::Class.'@getListChild');
    Route::get('/expense/get-expense-cate-list',ExpenseController::Class.'@getExpenseCateList');
    Route::get('/expense/get-expense-link/{id}',ExpenseController::Class.'@getExpenseLink');
    Route::post('/expense/expense-data-list',ExpenseController::Class.'@updateLink');
    Route::post('/expense/get-report-data', ExpenseController::Class.'@getReportData');

    // ============================= Expense cate list - Loại khoản chi =========================
    Route::post('/expense-cate-list',ExpenseCateListController::Class.'@list');
    Route::get('/expense-cate-list/create',ExpenseCateListController::Class.'@create');
    Route::get('/expense-cate-list/view/{id}',ExpenseCateListController::Class.'@view');
    Route::get('/expense-cate-list/edit/{id}',ExpenseCateListController::Class.'@edit');
    Route::post('/expense-cate-list/get-list-child',ExpenseCateListController::Class.'@getListChild');
    Route::post('/expense-cate-list/update/{id}',ExpenseCateListController::Class.'@update');
    Route::post('/expense-cate-list/delete/{id}',ExpenseCateListController::Class.'@delete');
    Route::post('/expense-cate-list/delete',ExpenseCateListController::Class.'@deleteArray');
    Route::post('/expense-cate-list/store',ExpenseCateListController::Class.'@store');

    // ============================= Capital- nguồn vốn =======================================
    Route::post('/capital', CapitalController::Class.'@list');
    Route::get('/capital/create', CapitalController::Class.'@create');
    Route::get('/capital/view/{id}', CapitalController::Class.'@view');
    Route::get('/capital/edit/{id}', CapitalController::Class.'@edit');
    Route::post('/capital/update/{id}', CapitalController::Class.'@update');
    Route::post('/capital/delete/{id}', CapitalController::Class.'@delete');
    Route::post('/capital/delete', CapitalController::Class.'@deleteArray');
    Route::post('/capital/store', CapitalController::Class.'@store');
    Route::post('/capital/get-list-child',CapitalController::Class.'@getListChild');
    Route::get('/capital/get-capital-cate-list',CapitalController::Class.'@getCapitalCateList');
    Route::get('/capital/get-capital-link/{id}',CapitalController::Class.'@getCapitalLink');
    Route::post('/capital/capital-data-list',CapitalController::Class.'@updateLink');
    Route::post('/capital/get-report-data', CapitalController::Class.'@getReportData');

    // ============================= Capital cate list - Loại nguồn vốn =========================
    Route::post('/capital-cate-list',CapitalCateListController::Class.'@list');
    Route::get('/capital-cate-list/create',CapitalCateListController::Class.'@create');
    Route::get('/capital-cate-list/view/{id}',CapitalCateListController::Class.'@view');
    Route::get('/capital-cate-list/edit/{id}',CapitalCateListController::Class.'@edit');
    Route::post('/capital-cate-list/update/{id}',CapitalCateListController::Class.'@update');
    Route::post('/capital-cate-list/delete/{id}',CapitalCateListController::Class.'@delete');
    Route::post('/capital-cate-list/get-list-child',CapitalCateListController::Class.'@getListChild');
    Route::post('/capital-cate-list/delete',CapitalCateListController::Class.'@deleteArray');
    Route::post('/capital-cate-list/store',CapitalCateListController::Class.'@store');

    // ============================= Employee cate list - Loại nhân viên =========================
    Route::post('/employee-cate-list',EmployeeCateListController::Class.'@list');
    Route::get('/employee-cate-list/create',EmployeeCateListController::Class.'@create');
    Route::get('/employee-cate-list/view/{id}',EmployeeCateListController::Class.'@view');
    Route::get('/employee-cate-list/edit/{id}',EmployeeCateListController::Class.'@edit');
    Route::post('/employee-cate-list/update/{id}',EmployeeCateListController::Class.'@update');
    Route::post('/employee-cate-list/delete/{id}',EmployeeCateListController::Class.'@delete');
    Route::post('/employee-cate-list/delete',EmployeeCateListController::Class.'@deleteArray');
    Route::post('/employee-cate-list/store',EmployeeCateListController::Class.'@store');
    Route::post('/employee-cate-list/get-list-child', EmployeeCateListController::Class.'@getListChild');

    // ============================= Sector - Lĩnh vực chi =======================================
    Route::post('/sector', SectorController::Class.'@list');
    Route::get('/sector/create', SectorController::Class.'@create');
    Route::get('/sector/view/{id}', SectorController::Class.'@view');
    Route::get('/sector/edit/{id}', SectorController::Class.'@edit');
    Route::post('/sector/update/{id}', SectorController::Class.'@update');
    Route::post('/sector/delete/{id}', SectorController::Class.'@delete');
    Route::post('/sector/delete', SectorController::Class.'@deleteArray');
    Route::post('/sector/store', SectorController::Class.'@store');
    Route::post('/sector/sector-data-list', SectorController::Class.'@updateLink');
    Route::get('/sector/get-sector-link/{id}', SectorController::Class.'@getLink');
    Route::get('/sector/get-sector-file/{id}', SectorController::Class.'@getFile');
    Route::get('/sector/get-sector-cate-list', SectorController::Class.'@getCate');
    Route::post('/sector/sector-upload-file/{id}', SectorController::Class.'@uploadFile');
    Route::post('/sector/delete-file/{id}', SectorController::Class.'@deleteFile');
    Route::get('/sector/download-file/{id}', SectorController::Class.'@downloadFile');
    Route::get('/sector/download-all-file/{id}', SectorController::Class.'@downloadAllFile');
    Route::get('/sector/get-sector-video/{id}', SectorController::Class.'@getVideo');
    Route::post('/sector/sector-upload-video/{id}', SectorController::Class.'@uploadVideo');
    Route::post('/sector/delete-video/{id}', SectorController::Class.'@deleteVideo');
    Route::get('/sector/download-video/{id}', SectorController::Class.'@downloadVideo');
    Route::post('/sector/update-doc-cate/{id}', SectorController::Class.'@updateDocCate');
    Route::post('/sector/update-per/{id}', SectorController::Class.'@updatePer');
    Route::post('/sector/get-report-data', SectorController::Class.'@getReportData');

    // ============================= Sector cate list - Loại lĩnh vực chi =========================
    Route::post('/sector-cate-list',SectorCateListController::Class.'@list');
    Route::get('/sector-cate-list/create',SectorCateListController::Class.'@create');
    Route::get('/sector-cate-list/view/{id}',SectorCateListController::Class.'@view');
    Route::get('/sector-cate-list/edit/{id}',SectorCateListController::Class.'@edit');
    Route::post('/sector-cate-list/update/{id}',SectorCateListController::Class.'@update');
    Route::post('/sector-cate-list/delete/{id}',SectorCateListController::Class.'@delete');
    Route::post('/sector-cate-list/delete',SectorCateListController::Class.'@deleteArray');
    Route::post('/sector-cate-list/store',SectorCateListController::Class.'@store');
    Route::post('/sector-cate-list/get-list-child', SectorCateListController::Class.'@getListChild');

    // ============================= SbrSector - Lĩnh vực thu =======================================
    Route::post('/sbr-sector', SbrSectorController::Class.'@list');
    Route::get('/sbr-sector/create', SbrSectorController::Class.'@create');
    Route::get('/sbr-sector/view/{id}', SbrSectorController::Class.'@view');
    Route::get('/sbr-sector/edit/{id}', SbrSectorController::Class.'@edit');
    Route::post('/sbr-sector/update/{id}', SbrSectorController::Class.'@update');
    Route::post('/sbr-sector/delete/{id}', SbrSectorController::Class.'@delete');
    Route::post('/sbr-sector/delete', SbrSectorController::Class.'@deleteArray');
    Route::post('/sbr-sector/store', SbrSectorController::Class.'@store');
    Route::post('/sbr-sector/sbr-sector-data-list', SbrSectorController::Class.'@updateLink');
    Route::get('/sbr-sector/get-sbr-sector-link/{id}', SbrSectorController::Class.'@getLink');
    Route::get('/sbr-sector/get-sbr-sector-cate-list', SbrSectorController::Class.'@getCate');
    Route::post('/sbr-sector/update-doc-cate/{id}', SbrSectorController::Class.'@updateDocCate');
    Route::post('/sbr-sector/update-per/{id}', SbrSectorController::Class.'@updatePer');
    Route::post('/sbr-sector/get-report-data', SbrSectorController::Class.'@getReportData');

    // ============================= SbrSector cate list - Loại lĩnh vực thu =========================
    Route::post('/sbr-sector-cate-list',SbrSectorCateListController::Class.'@list');
    Route::get('/sbr-sector-cate-list/create',SbrSectorCateListController::Class.'@create');
    Route::get('/sbr-sector-cate-list/view/{id}',SbrSectorCateListController::Class.'@view');
    Route::get('/sbr-sector-cate-list/edit/{id}',SbrSectorCateListController::Class.'@edit');
    Route::post('/sbr-sector-cate-list/update/{id}',SbrSectorCateListController::Class.'@update');
    Route::post('/sbr-sector-cate-list/delete/{id}',SbrSectorCateListController::Class.'@delete');
    Route::post('/sbr-sector-cate-list/delete',SbrSectorCateListController::Class.'@deleteArray');
    Route::post('/sbr-sector-cate-list/store',SbrSectorCateListController::Class.'@store');
    Route::post('/sbr-sector-cate-list/get-list-child', SbrSectorCateListController::Class.'@getListChild');

    // ============================= Program - Chương trình mục tiêu =======================================
    Route::post('/program', ProgramController::Class.'@list');
    Route::get('/program/create', ProgramController::Class.'@create');
    Route::get('/program/view/{id}', ProgramController::Class.'@view');
    Route::get('/program/edit/{id}', ProgramController::Class.'@edit');
    Route::post('/program/update/{id}', ProgramController::Class.'@update');
    Route::post('/program/delete/{id}', ProgramController::Class.'@delete');
    Route::post('/program/delete', ProgramController::Class.'@deleteArray');
    Route::post('/program/store', ProgramController::Class.'@store');
    Route::post('/program/program-data-list', ProgramController::Class.'@updateLink');
    Route::get('/program/get-program-link/{id}', ProgramController::Class.'@getLink');
    Route::get('/program/get-program-file/{id}', ProgramController::Class.'@getFile');
    Route::get('/program/get-program-cate-list', ProgramController::Class.'@getCate');
    Route::post('/program/program-upload-file/{id}', ProgramController::Class.'@uploadFile');
    Route::post('/program/delete-file/{id}', ProgramController::Class.'@deleteFile');
    Route::get('/program/download-file/{id}', ProgramController::Class.'@downloadFile');
    Route::get('/program/download-all-file/{id}', ProgramController::Class.'@downloadAllFile');
    Route::get('/program/get-program-video/{id}', ProgramController::Class.'@getVideo');
    Route::post('/program/program-upload-video/{id}', ProgramController::Class.'@uploadVideo');
    Route::post('/program/delete-video/{id}', ProgramController::Class.'@deleteVideo');
    Route::get('/program/download-video/{id}', ProgramController::Class.'@downloadVideo');
    Route::post('/program/update-doc-cate/{id}', ProgramController::Class.'@updateDocCate');
    Route::post('/program/update-per/{id}', ProgramController::Class.'@updatePer');
    Route::post('/program/get-report-data', ProgramController::Class.'@getReportData');

    // ============================= Program cate list - Loại ngành =========================
    Route::post('/program-cate-list',ProgramCateListController::Class.'@list');
    Route::get('/program-cate-list/create',ProgramCateListController::Class.'@create');
    Route::get('/program-cate-list/view/{id}',ProgramCateListController::Class.'@view');
    Route::get('/program-cate-list/edit/{id}',ProgramCateListController::Class.'@edit');
    Route::post('/program-cate-list/update/{id}',ProgramCateListController::Class.'@update');
    Route::post('/program-cate-list/delete/{id}',ProgramCateListController::Class.'@delete');
    Route::post('/program-cate-list/delete',ProgramCateListController::Class.'@deleteArray');
    Route::post('/program-cate-list/store',ProgramCateListController::Class.'@store');
    Route::post('/program-cate-list/get-list-child', ProgramCateListController::Class.'@getListChild');

    // ============================= Project cate list - Loại dự án =========================
    Route::post('/project-cate-list',ProjectCateListController::Class.'@list');
    Route::get('/project-cate-list/create',ProjectCateListController::Class.'@create');
    Route::get('/project-cate-list/view/{id}',ProjectCateListController::Class.'@view');
    Route::get('/project-cate-list/edit/{id}',ProjectCateListController::Class.'@edit');
    Route::post('/project-cate-list/update/{id}',ProjectCateListController::Class.'@update');
    Route::post('/project-cate-list/delete/{id}',ProjectCateListController::Class.'@delete');
    Route::post('/project-cate-list/delete',ProjectCateListController::Class.'@deleteArray');
    Route::post('/project-cate-list/get-list-child',ProjectCateListController::Class.'@getListChild');
    Route::post('/project-cate-list/store',ProjectCateListController::Class.'@store');

    // ============================= Project - Dự án =======================================
    Route::post('/project', ProjectController::Class.'@list');
    Route::get('/project/create', ProjectController::Class.'@create');
    Route::get('/project/view/{id}', ProjectController::Class.'@view');
    Route::get('/project/edit/{id}', ProjectController::Class.'@edit');
    Route::post('/project/update/{id}', ProjectController::Class.'@update');
    Route::post('/project/delete/{id}', ProjectController::Class.'@delete');
    Route::post('/project/delete', ProjectController::Class.'@deleteArray');
    Route::post('/project/store', ProjectController::Class.'@store');
    Route::post('/project/project-data-list', ProjectController::Class.'@updateLink');
    Route::get('/project/get-project-link/{id}', ProjectController::Class.'@getLink');
    Route::get('/project/get-project-file/{id}', ProjectController::Class.'@getFile');
    Route::get('/project/get-project-cate-list', ProjectController::Class.'@getCate');
    Route::post('/project/project-upload-file/{id}', ProjectController::Class.'@uploadFile');
    Route::post('/project/delete-file/{id}', ProjectController::Class.'@deleteFile');
    Route::get('/project/download-file/{id}', ProjectController::Class.'@downloadFile');
    Route::get('/project/download-all-file/{id}', ProjectController::Class.'@downloadAllFile');
    Route::get('/project/get-project-video/{id}', ProjectController::Class.'@getVideo');
    Route::post('/project/project-upload-video/{id}', ProjectController::Class.'@uploadVideo');
    Route::post('/project/delete-video/{id}', ProjectController::Class.'@deleteVideo');
    Route::get('/project/download-video/{id}', ProjectController::Class.'@downloadVideo');
    Route::post('/project/update-doc-cate/{id}', ProjectController::Class.'@updateDocCate');
    Route::post('/project/update-per/{id}', ProjectController::Class.'@updatePer');
    Route::post('/project/get-report-data', ProjectController::Class.'@getReportData');

    // ============================= Contract - Hợp đồng =======================================
    Route::post('/contract', ContractController::Class.'@list');
    Route::get('/contract/create', ContractController::Class.'@create');
    Route::get('/contract/view/{id}', ContractController::Class.'@view');
    Route::get('/contract/edit/{id}', ContractController::Class.'@edit');
    Route::post('/contract/update/{id}', ContractController::Class.'@update');
    Route::post('/contract/delete/{id}', ContractController::Class.'@delete');
    Route::post('/contract/delete', ContractController::Class.'@deleteArray');
    Route::post('/contract/store', ContractController::Class.'@store');
    Route::post('/contract/contract-data-list', ContractController::Class.'@updateLink');
    Route::get('/contract/get-contract-link/{id}', ContractController::Class.'@getLink');
    Route::get('/contract/get-contract-file/{id}', ContractController::Class.'@getFile');
    Route::get('/contract/get-contract-cate-list', ContractController::Class.'@getCate');
    Route::post('/contract/contract-upload-file/{id}', ContractController::Class.'@uploadFile');
    Route::post('/contract/delete-file/{id}', ContractController::Class.'@deleteFile');
    Route::get('/contract/download-file/{id}', ContractController::Class.'@downloadFile');
    Route::get('/contract/download-all-file/{id}', ContractController::Class.'@downloadAllFile');
    Route::get('/contract/get-contract-video/{id}', ContractController::Class.'@getVideo');
    Route::post('/contract/contract-upload-video/{id}', ContractController::Class.'@uploadVideo');
    Route::post('/contract/delete-video/{id}', ContractController::Class.'@deleteVideo');
    Route::get('/contract/download-video/{id}', ContractController::Class.'@downloadVideo');
    Route::post('/contract/update-doc-cate/{id}', ContractController::Class.'@updateDocCate');
    Route::post('/contract/update-per/{id}', ContractController::Class.'@updatePer');
    Route::post('/contract/get-report-data', ContractController::Class.'@getReportData');

    // ============================= SbiChapter - Chương =======================================
    Route::post('/sbi-chapter', SbiChapterController::Class.'@list');
    Route::get('/sbi-chapter/create', SbiChapterController::Class.'@create');
    Route::get('/sbi-chapter/view/{id}', SbiChapterController::Class.'@view');
    Route::get('/sbi-chapter/edit/{id}', SbiChapterController::Class.'@edit');
    Route::post('/sbi-chapter/update/{id}', SbiChapterController::Class.'@update');
    Route::post('/sbi-chapter/delete/{id}', SbiChapterController::Class.'@delete');
    Route::post('/sbi-chapter/delete', SbiChapterController::Class.'@deleteArray');
    Route::post('/sbi-chapter/store', SbiChapterController::Class.'@store');
    Route::post('/sbi-chapter/sbi-chapter-data-list', SbiChapterController::Class.'@updateLink');
    Route::get('/sbi-chapter/get-sbi-chapter-link/{id}', SbiChapterController::Class.'@getLink');
    Route::get('/sbi-chapter/get-sbi-chapter-file/{id}', SbiChapterController::Class.'@getFile');
    Route::get('/sbi-chapter/get-cate-list', SbiChapterController::Class.'@getCate');
    Route::post('/sbi-chapter/sbi-chapter-upload-file/{id}', SbiChapterController::Class.'@uploadFile');
    Route::post('/sbi-chapter/delete-file/{id}', SbiChapterController::Class.'@deleteFile');
    Route::get('/sbi-chapter/download-file/{id}', SbiChapterController::Class.'@downloadFile');
    Route::get('/sbi-chapter/download-all-file/{id}', SbiChapterController::Class.'@downloadAllFile');
    Route::get('/sbi-chapter/get-sbi-chapter-video/{id}', SbiChapterController::Class.'@getVideo');
    Route::post('/sbi-chapter/sbi-chapter-upload-video/{id}', SbiChapterController::Class.'@uploadVideo');
    Route::post('/sbi-chapter/delete-video/{id}', SbiChapterController::Class.'@deleteVideo');
    Route::get('/sbi-chapter/download-video/{id}', SbiChapterController::Class.'@downloadVideo');
    Route::post('/sbi-chapter/update-doc-cate/{id}', SbiChapterController::Class.'@updateDocCate');
    Route::post('/sbi-chapter/update-per/{id}', SbiChapterController::Class.'@updatePer');
    Route::post('/sbi-chapter/get-report-data', SbiChapterController::Class.'@getReportData');

    // ============================= SbiCategory - Loại - Khoản =======================================
    Route::post('/sbi-category', SbiCategoryController::Class.'@list');
    Route::get('/sbi-category/create', SbiCategoryController::Class.'@create');
    Route::get('/sbi-category/view/{id}', SbiCategoryController::Class.'@view');
    Route::get('/sbi-category/edit/{id}', SbiCategoryController::Class.'@edit');
    Route::post('/sbi-category/update/{id}', SbiCategoryController::Class.'@update');
    Route::post('/sbi-category/delete/{id}', SbiCategoryController::Class.'@delete');
    Route::post('/sbi-category/delete', SbiCategoryController::Class.'@deleteArray');
    Route::post('/sbi-category/store', SbiCategoryController::Class.'@store');
    Route::post('/sbi-category/get-list-child', SbiCategoryController::Class.'@getListChild');
    Route::get('/sbi-category/get-sbi-category-cate-list',SbiCategoryController::Class.'@getSbiCategoryCateList');
    Route::get('/sbi-category/get-sbi-category-link/{id}',SbiCategoryController::Class.'@getSbiCategoryLink');
    Route::post('/sbi-category/sbi-category-data-list',SbiCategoryController::Class.'@updateLink');
    Route::post('/sbi-category/get-list-child', SbiCategoryController::Class.'@getListChild');
    Route::post('/sbi-category/get-report-data', SbiCategoryController::Class.'@getReportData');

    // ============================= SbiItem - Mục - Tiểu mục =======================================
    Route::post('/sbi-item', SbiItemController::Class.'@list');
    Route::get('/sbi-item/create', SbiItemController::Class.'@create');
    Route::get('/sbi-item/view/{id}', SbiItemController::Class.'@view');
    Route::get('/sbi-item/edit/{id}', SbiItemController::Class.'@edit');
    Route::post('/sbi-item/update/{id}', SbiItemController::Class.'@update');
    Route::post('/sbi-item/delete/{id}', SbiItemController::Class.'@delete');
    Route::post('/sbi-item/delete', SbiItemController::Class.'@deleteArray');
    Route::post('/sbi-item/store', SbiItemController::Class.'@store');
    Route::post('/sbi-item/sbi-item-data-list', SbiItemController::Class.'@updateLink');
    Route::get('/sbi-item/get-sbi-item-link/{id}', SbiItemController::Class.'@getLink');
    Route::post('/sbi-item/update-per/{id}', SbiItemController::Class.'@updatePer');
    Route::post('/sbi-item/get-report-data', SbiItemController::Class.'@getReportData');


    //============================== Coa Type - Loại hệ thống tài khoản ==================================
    Route::post('/coa_type', CoaTypeController::Class.'@list');
    Route::get('/coa_type/create', CoaTypeController::Class.'@create');
    Route::get('/coa_type/view/{id}', CoaTypeController::Class.'@view');
    Route::get('/coa_type/edit/{id}', CoaTypeController::Class.'@edit');
    Route::post('/coa_type/update/{id}', CoaTypeController::Class.'@update');
    Route::post('/coa_type/delete/{id}', CoaTypeController::Class.'@delete');
    Route::post('/coa_type/delete', CoaTypeController::Class.'@deleteArray');
    Route::post('/coa_type/store', CoaTypeController::Class.'@store');



    //======================================== province ===============================================================
    Route::post('/province', ProvinceController::Class.'@list');
    Route::post('/province/store', ProvinceController::Class.'@store');
    Route::get('/province/view/{id}', ProvinceController::Class.'@view');
    Route::get('/province/edit/{id}', ProvinceController::Class.'@edit');
    Route::post('/province/update/{id}', ProvinceController::Class.'@update');
    Route::post('/province/delete', ProvinceController::Class.'@deleteArray');
    Route::post('/province/delete/{id}', ProvinceController::Class.'@delete');
    Route::post('/province/get-area', ProvinceController::Class.'@getArea');
    Route::post('/province/get-country', ProvinceController::Class.'@getCountry');
    Route::post('/province/get-report-data', ProvinceController::Class.'@getReportData');


    //======================================== District ===============================================================
    Route::post('/district', DistrictController::class.'@list');
    Route::post('/district/store', DistrictController::Class.'@store');
    Route::get('/district/view/{id}', DistrictController::Class.'@view');
    Route::get('/district/edit/{id}', DistrictController::Class.'@edit');
    Route::post('/district/update/{id}', DistrictController::Class.'@update');
    Route::post('/district/delete', DistrictController::Class.'@deleteArray');
    Route::post('/district/delete/{id}', DistrictController::Class.'@delete');
    Route::post('/district/get-area', DistrictController::Class.'@getArea');
    Route::post('/district/get-province', DistrictController::Class.'@getProvince');
    Route::post('/district/get-report-data', DistrictController::Class.'@getReportData');

    //========================================= Commune ==============================================================
    Route::post('/commune', CommuneController::class.'@list');
    Route::post('/commune/store', CommuneController::Class.'@store');
    Route::get('/commune/view/{id}', CommuneController::Class.'@view');
    Route::get('/commune/edit/{id}', CommuneController::Class.'@edit');
    Route::post('/commune/update/{id}', CommuneController::Class.'@update');
    Route::post('/commune/delete', CommuneController::Class.'@deleteArray');
    Route::post('/commune/delete/{id}', CommuneController::Class.'@delete');
    Route::post('/commune/get-district', CommuneController::Class.'@getDistrict');
    Route::post('/commune/get-province', CommuneController::Class.'@getProvince');
    Route::post('/commune/get-report-data', CommuneController::Class.'@getReportData');

    //========================================= Area ==================================================================
    Route::post('/area', AreaController::class.'@list');
    Route::post('/area/store', AreaController::Class.'@store');
    Route::get('/area/view/{id}', AreaController::Class.'@view');
    Route::get('/area/edit/{id}', AreaController::Class.'@edit');
    Route::post('/area/update/{id}', AreaController::Class.'@update');
    Route::post('/area/delete', AreaController::Class.'@deleteArray');
    Route::post('/area/delete/{id}', AreaController::Class.'@delete');
    Route::post('/area/get-country', AreaController::Class.'@getCountry');
    Route::post('/area/get-report-data', AreaController::Class.'@getReportData');

    // ====================================== CoaSna- hệ thống tài khoản quốc gia ==========================
    Route::post('/coa-sna', CoaSnaController::Class.'@list');
    Route::get('/coa-sna/create', CoaSnaController::Class.'@create');
    Route::get('/coa-sna/view/{id}', CoaSnaController::Class.'@view');
    Route::get('/coa-sna/edit/{id}', CoaSnaController::Class.'@edit');
    Route::post('/coa-sna/update/{id}', CoaSnaController::Class.'@update');
    Route::post('/coa-sna/delete/{id}', CoaSnaController::Class.'@delete');
    Route::post('/coa-sna/delete', CoaSnaController::Class.'@deleteArray');
    Route::post('/coa-sna/store', CoaSnaController::Class.'@store');
    Route::get('/coa-sna/get-coa-sna-cate-list',CoaSnaController::Class.'@getCoaSnaCateList');
    Route::get('/coa-sna/get-coa-sna-link/{id}',CoaSnaController::Class.'@getCoaSnaLink');
    Route::post('/coa-sna/coa-sna-data-list',CoaSnaController::Class.'@updateLink');
    Route::post('/coa-sna/update-per/{id}', CoaSnaController::Class.'@updatePer');
    Route::post('/coa-sna/get-report-data', CoaSnaController::Class.'@getReportData');

    // ================================== CoaSna Cate List - loại hê thống tài khoản quốc gia ===============
    Route::post('/coa-sna-cate-list', CoaSnaCateListController::Class.'@list');
    Route::get('/coa-sna-cate-list/create', CoaSnaCateListController::Class.'@create');
    Route::get('/coa-sna-cate-list/view/{id}', CoaSnaCateListController::Class.'@view');
    Route::get('/coa-sna-cate-list/edit/{id}', CoaSnaCateListController::Class.'@edit');
    Route::post('/coa-sna-cate-list/update/{id}', CoaSnaCateListController::Class.'@update');
    Route::post('/coa-sna-cate-list/delete/{id}', CoaSnaCateListController::Class.'@delete');
    Route::post('/coa-sna-cate-list/get-list-child', CoaSnaCateListController::Class.'@getListChild');
    Route::post('/coa-sna-cate-list/delete', CoaSnaCateListController::Class.'@deleteArray');
    Route::post('/coa-sna-cate-list/store', CoaSnaCateListController::Class.'@store');

    // ====================================== CoaCon- hệ thống tài khoản hợp nhất ==========================
    Route::post('/coa-con', CoaConController::Class.'@list');
    Route::get('/coa-con/create', CoaConController::Class.'@create');
    Route::get('/coa-con/view/{id}', CoaConController::Class.'@view');
    Route::get('/coa-con/edit/{id}', CoaConController::Class.'@edit');
    Route::post('/coa-con/update/{id}', CoaConController::Class.'@update');
    Route::post('/coa-con/delete/{id}', CoaConController::Class.'@delete');
    Route::post('/coa-con/delete', CoaConController::Class.'@deleteArray');
    Route::post('/coa-con/store', CoaConController::Class.'@store');
    Route::post('/coa-con/get-list-child', CoaConController::Class.'@getListChild');
    Route::get('/coa-con/get-coa-con-cate-list',CoaConController::Class.'@getCoaConCateList');
    Route::get('/coa-con/get-coa-con-link/{id}',CoaConController::Class.'@getCoaConLink');
    Route::post('/coa-con/coa-con-data-list',CoaConController::Class.'@updateLink');
    Route::post('/coa-con/update-per/{id}', CoaConController::Class.'@updatePer');
    Route::post('/coa-con/get-report-data', CoaConController::Class.'@getReportData');

    // ================================== CoaCon Cate List - loại hê thống tài khoản hợp nhất ===============
    Route::post('/coa-con-cate-list', CoaConCateListController::Class.'@list');
    Route::get('/coa-con-cate-list/create', CoaConCateListController::Class.'@create');
    Route::get('/coa-con-cate-list/view/{id}', CoaConCateListController::Class.'@view');
    Route::get('/coa-con-cate-list/edit/{id}', CoaConCateListController::Class.'@edit');
    Route::post('/coa-con-cate-list/update/{id}', CoaConCateListController::Class.'@update');
    Route::post('/coa-con-cate-list/delete/{id}', CoaConCateListController::Class.'@delete');
    Route::post('/coa-con-cate-list/get-list-child', CoaConCateListController::Class.'@getListChild');
    Route::post('/coa-con-cate-list/delete', CoaConCateListController::Class.'@deleteArray');
    Route::post('/coa-con-cate-list/store', CoaConCateListController::Class.'@store');

    // ====================================== CoaTab-Hệ thống tài khoản KBNN ==========================
    Route::post('/coa-tab', CoaTabController::Class.'@list');
    Route::get('/coa-tab/create', CoaTabController::Class.'@create');
    Route::get('/coa-tab/view/{id}', CoaTabController::Class.'@view');
    Route::get('/coa-tab/edit/{id}', CoaTabController::Class.'@edit');
    Route::post('/coa-tab/update/{id}', CoaTabController::Class.'@update');
    Route::post('/coa-tab/delete/{id}', CoaTabController::Class.'@delete');
    Route::post('/coa-tab/delete', CoaTabController::Class.'@deleteArray');
    Route::post('/coa-tab/store', CoaTabController::Class.'@store');
    Route::post('/coa-tab/get-list-child', CoaTabController::Class.'@getListChild');
    Route::get('/coa-tab/get-coa-tab-cate-list',CoaTabController::Class.'@getCoaTabCateList');
    Route::get('/coa-tab/get-coa-tab-link/{id}',CoaTabController::Class.'@getCoaTabLink');
    Route::post('/coa-tab/coa-tab-data-list',CoaTabController::Class.'@updateLink');
    Route::post('/coa-tab/update-per/{id}', CoaTabController::Class.'@updatePer');
    Route::post('/coa-tab/get-report-data', CoaTabController::Class.'@getReportData');

    // ================================== CoaTab Cate List - loại hê thống tài khoản KBNN ===============
    Route::post('/coa-tab-cate-list',CoaTabCateListController::Class.'@list');
    Route::get('/coa-tab-cate-list/create',CoaTabCateListController::Class.'@create');
    Route::get('/coa-tab-cate-list/view/{id}',CoaTabCateListController::Class.'@view');
    Route::get('/coa-tab-cate-list/edit/{id}',CoaTabCateListController::Class.'@edit');
    Route::post('/coa-tab-cate-list/update/{id}',CoaTabCateListController::Class.'@update');
    Route::post('/coa-tab-cate-list/delete/{id}',CoaTabCateListController::Class.'@delete');
    Route::post('/coa-tab-cate-list/get-list-child',CoaTabCateListController::Class.'@getListChild');
    Route::post('/coa-tab-cate-list/delete',CoaTabCateListController::Class.'@deleteArray');
    Route::post('/coa-tab-cate-list/store',CoaTabCateListController::Class.'@store');

    // ====================================== CoaAnu- hệ thống tài khoản HCSN ==========================
    Route::post('/coa-anu', CoaAnuController::Class.'@list');
    Route::get('/coa-anu/create', CoaAnuController::Class.'@create');
    Route::get('/coa-anu/view/{id}', CoaAnuController::Class.'@view');
    Route::get('/coa-anu/edit/{id}', CoaAnuController::Class.'@edit');
    Route::post('/coa-anu/update/{id}', CoaAnuController::Class.'@update');
    Route::post('/coa-anu/delete/{id}', CoaAnuController::Class.'@delete');
    Route::post('/coa-anu/delete', CoaAnuController::Class.'@deleteArray');
    Route::post('/coa-anu/store', CoaAnuController::Class.'@store');
    Route::post('/coa-anu/get-list-child', CoaAnuController::Class.'@getListChild');
    Route::get('/coa-anu/get-coa-anu-cate-list',CoaAnuController::Class.'@getCoaAnuCateList');
    Route::get('/coa-anu/get-coa-anu-link/{id}',CoaAnuController::Class.'@getCoaAnuLink');
    Route::post('/coa-anu/coa-anu-data-list',CoaAnuController::Class.'@updateLink');
    Route::post('/coa-anu/update-per/{id}', CoaAnuController::Class.'@updatePer');
    Route::post('/coa-anu/get-report-data', CoaAnuController::Class.'@getReportData');

    // ================================== CoaAnu Cate List - loại hê thống tài khoản HCSN ===============
    Route::post('/coa-anu-cate-list', CoaAnuCateListController::Class.'@list');
    Route::get('/coa-anu-cate-list/create', CoaAnuCateListController::Class.'@create');
    Route::get('/coa-anu-cate-list/view/{id}', CoaAnuCateListController::Class.'@view');
    Route::get('/coa-anu-cate-list/edit/{id}', CoaAnuCateListController::Class.'@edit');
    Route::post('/coa-anu-cate-list/update/{id}', CoaAnuCateListController::Class.'@update');
    Route::post('/coa-anu-cate-list/delete/{id}', CoaAnuCateListController::Class.'@delete');
    Route::post('/coa-anu-cate-list/delete', CoaAnuCateListController::Class.'@deleteArray');
    Route::post('/coa-anu-cate-list/store', CoaAnuCateListController::Class.'@store');
    Route::post('/coa-anu-cate-list/get-list-child', CoaAnuCateListController::Class.'@getListChild');

    // ====================================== CoaPmu- hệ thống tài khoản BQLDA ==========================
    Route::post('/coa-pmu', CoaPmuController::Class.'@list');
    Route::get('/coa-pmu/create', CoaPmuController::Class.'@create');
    Route::get('/coa-pmu/view/{id}', CoaPmuController::Class.'@view');
    Route::get('/coa-pmu/edit/{id}', CoaPmuController::Class.'@edit');
    Route::post('/coa-pmu/update/{id}', CoaPmuController::Class.'@update');
    Route::post('/coa-pmu/delete/{id}', CoaPmuController::Class.'@delete');
    Route::post('/coa-pmu/delete', CoaPmuController::Class.'@deleteArray');
    Route::post('/coa-pmu/store', CoaPmuController::Class.'@store');
    Route::get('/coa-pmu/get-coa-pmu-cate-list',CoaPmuController::Class.'@getCoaPmuCateList');
    Route::get('/coa-pmu/get-coa-pmu-link/{id}',CoaPmuController::Class.'@getCoaPmuLink');
    Route::post('/coa-pmu/coa-pmu-data-list',CoaPmuController::Class.'@updateLink');
    Route::post('/coa-pmu/update-per/{id}', CoaPmuController::Class.'@updatePer');
    Route::post('/coa-pmu/get-report-data', CoaPmuController::Class.'@getReportData');

    // ================================== CoaPmu Cate List - loại hê thống tài khoản BQLDA ===============
    Route::post('/coa-pmu-cate-list', CoaPmuCateListController::Class.'@list');
    Route::get('/coa-pmu-cate-list/create', CoaPmuCateListController::Class.'@create');
    Route::get('/coa-pmu-cate-list/view/{id}', CoaPmuCateListController::Class.'@view');
    Route::get('/coa-pmu-cate-list/edit/{id}', CoaPmuCateListController::Class.'@edit');
    Route::post('/coa-pmu-cate-list/update/{id}', CoaPmuCateListController::Class.'@update');
    Route::post('/coa-pmu-cate-list/delete/{id}', CoaPmuCateListController::Class.'@delete');
    Route::post('/coa-pmu-cate-list/get-list-child', CoaPmuCateListController::Class.'@getListChild');
    Route::post('/coa-pmu-cate-list/delete', CoaPmuCateListController::Class.'@deleteArray');
    Route::post('/coa-pmu-cate-list/store', CoaPmuCateListController::Class.'@store');

    // ====================================== CoaScb- hệ thống tài khoản xã ==========================
    Route::post('/coa-scb', CoaScbController::Class.'@list');
    Route::get('/coa-scb/create', CoaScbController::Class.'@create');
    Route::get('/coa-scb/view/{id}', CoaScbController::Class.'@view');
    Route::get('/coa-scb/edit/{id}', CoaScbController::Class.'@edit');
    Route::post('/coa-scb/update/{id}', CoaScbController::Class.'@update');
    Route::post('/coa-scb/delete/{id}', CoaScbController::Class.'@delete');
    Route::post('/coa-scb/delete', CoaScbController::Class.'@deleteArray');
    Route::post('/coa-scb/store', CoaScbController::Class.'@store');
    Route::get('/coa-scb/get-coa-scb-cate-list',CoaScbController::Class.'@getCoaScbCateList');
    Route::get('/coa-scb/get-coa-scb-link/{id}',CoaScbController::Class.'@getCoaScbLink');
    Route::post('/coa-scb/coa-scb-data-list',CoaScbController::Class.'@updateLink');
    Route::post('/coa-scb/update-per/{id}', CoaScbController::Class.'@updatePer');
    Route::post('/coa-scb/get-report-data', CoaScbController::Class.'@getReportData');
    Route::post('/coa-scb/get-list-child', CoaScbController::Class.'@getListChild');


    // ================================== CoaScb Cate List - loại hê thống tài khoản xã ===============
    Route::post('/coa-scb-cate-list', CoaScbCateListController::Class.'@list');
    Route::get('/coa-scb-cate-list/create', CoaScbCateListController::Class.'@create');
    Route::get('/coa-scb-cate-list/view/{id}', CoaScbCateListController::Class.'@view');
    Route::get('/coa-scb-cate-list/edit/{id}', CoaScbCateListController::Class.'@edit');
    Route::post('/coa-scb-cate-list/update/{id}', CoaScbCateListController::Class.'@update');
    Route::post('/coa-scb-cate-list/delete/{id}', CoaScbCateListController::Class.'@delete');
    Route::post('/coa-scb-cate-list/get-list-child', CoaScbCateListController::Class.'@getListChild');
    Route::post('/coa-scb-cate-list/delete', CoaScbCateListController::Class.'@deleteArray');
    Route::post('/coa-scb-cate-list/store', CoaScbCateListController::Class.'@store');

    // ====================================== CoaEas- hệ thống tài khoản doanh nghiệp ==========================
    Route::post('/coa-eas', CoaEasController::Class.'@list');
    Route::get('/coa-eas/create', CoaEasController::Class.'@create');
    Route::get('/coa-eas/view/{id}', CoaEasController::Class.'@view');
    Route::get('/coa-eas/edit/{id}', CoaEasController::Class.'@edit');
    Route::post('/coa-eas/update/{id}', CoaEasController::Class.'@update');
    Route::post('/coa-eas/delete/{id}', CoaEasController::Class.'@delete');
    Route::post('/coa-eas/delete', CoaEasController::Class.'@deleteArray');
    Route::post('/coa-eas/store', CoaEasController::Class.'@store');
    Route::get('/coa-eas/get-coa-eas-cate-list',CoaEasController::Class.'@getCoaEasCateList');
    Route::get('/coa-eas/get-coa-eas-link/{id}',CoaEasController::Class.'@getCoaEasLink');
    Route::post('/coa-eas/coa-eas-data-list',CoaEasController::Class.'@updateLink');
    Route::post('/coa-eas/update-per/{id}', CoaEasController::Class.'@updatePer');
    Route::post('/coa-eas/get-report-data', CoaEasController::Class.'@getReportData');
    Route::post('/coa-eas/get-list-child',CoaEasController::Class.'@getListChild');
    // ================================== CoaEas Cate List - loại hê thống tài khoản doanh nghiệp ===============
    Route::post('/coa-eas-cate-list', CoaEasCateListController::Class.'@list');
    Route::get('/coa-eas-cate-list/create', CoaEasCateListController::Class.'@create');
    Route::get('/coa-eas-cate-list/view/{id}', CoaEasCateListController::Class.'@view');
    Route::get('/coa-eas-cate-list/edit/{id}', CoaEasCateListController::Class.'@edit');
    Route::post('/coa-eas-cate-list/update/{id}', CoaEasCateListController::Class.'@update');
    Route::post('/coa-eas-cate-list/delete/{id}', CoaEasCateListController::Class.'@delete');
    Route::post('/coa-eas-cate-list/get-list-child', CoaEasCateListController::Class.'@getListChild');
    Route::post('/coa-eas-cate-list/delete', CoaEasCateListController::Class.'@deleteArray');
    Route::post('/coa-eas-cate-list/store', CoaEasCateListController::Class.'@store');

    // ====================================== CoaSnr- hệ thống tài khoản DTQG ==========================
    Route::post('/coa-snr', CoaSnrController::Class.'@list');
    Route::get('/coa-snr/create', CoaSnrController::Class.'@create');
    Route::get('/coa-snr/view/{id}', CoaSnrController::Class.'@view');
    Route::get('/coa-snr/edit/{id}', CoaSnrController::Class.'@edit');
    Route::post('/coa-snr/update/{id}', CoaSnrController::Class.'@update');
    Route::post('/coa-snr/delete/{id}', CoaSnrController::Class.'@delete');
    Route::post('/coa-snr/delete', CoaSnrController::Class.'@deleteArray');
    Route::post('/coa-snr/store', CoaSnrController::Class.'@store');
    Route::get('/coa-snr/get-coa-snr-cate-list',CoaSnrController::Class.'@getCoaSnrCateList');
    Route::get('/coa-snr/get-coa-snr-link/{id}',CoaSnrController::Class.'@getCoaSnrLink');
    Route::post('/coa-snr/coa-snr-data-list',CoaSnrController::Class.'@updateLink');
    Route::post('/coa-snr/update-per/{id}', CoaSnrController::Class.'@updatePer');
    Route::post('/coa-snr/get-report-data', CoaSnrController::Class.'@getReportData');

    // ================================== CoaSnr Cate List - loại hê thống tài khoản DTQG ===============
    Route::post('/coa-snr-cate-list', CoaSnrCateListController::Class.'@list');
    Route::get('/coa-snr-cate-list/create', CoaSnrCateListController::Class.'@create');
    Route::get('/coa-snr-cate-list/view/{id}', CoaSnrCateListController::Class.'@view');
    Route::get('/coa-snr-cate-list/edit/{id}', CoaSnrCateListController::Class.'@edit');
    Route::post('/coa-snr-cate-list/update/{id}', CoaSnrCateListController::Class.'@update');
    Route::post('/coa-snr-cate-list/delete/{id}', CoaSnrCateListController::Class.'@delete');
    Route::post('/coa-snr-cate-list/get-list-child', CoaSnrCateListController::Class.'@getListChild');
    Route::post('/coa-snr-cate-list/delete', CoaSnrCateListController::Class.'@deleteArray');
    Route::post('/coa-snr-cate-list/store', CoaSnrCateListController::Class.'@store');

    // ====================================== CoaSia- hệ thống tài khoản BHXH ==========================
    Route::post('/coa-sia', CoaSiaController::Class.'@list');
    Route::get('/coa-sia/create', CoaSiaController::Class.'@create');
    Route::get('/coa-sia/view/{id}', CoaSiaController::Class.'@view');
    Route::get('/coa-sia/edit/{id}', CoaSiaController::Class.'@edit');
    Route::post('/coa-sia/update/{id}', CoaSiaController::Class.'@update');
    Route::post('/coa-sia/delete/{id}', CoaSiaController::Class.'@delete');
    Route::post('/coa-sia/delete', CoaSiaController::Class.'@deleteArray');
    Route::post('/coa-sia/store', CoaSiaController::Class.'@store');
    Route::get('/coa-sia/get-coa-sia-cate-list',CoaSiaController::Class.'@getCoaSiaCateList');
    Route::get('/coa-sia/get-coa-sia-link/{id}',CoaSiaController::Class.'@getCoaSiaLink');
    Route::post('/coa-sia/coa-sia-data-list',CoaSiaController::Class.'@updateLink');
    Route::post('/coa-sia/update-per/{id}', CoaSiaController::Class.'@updatePer');
    Route::post('/coa-sia/get-report-data', CoaSiaController::Class.'@getReportData');

    // ================================== CoaSia Cate List - loại hê thống tài khoản BHXH ===============
    Route::post('/coa-sia-cate-list', CoaSiaCateListController::Class.'@list');
    Route::get('/coa-sia-cate-list/create', CoaSiaCateListController::Class.'@create');
    Route::get('/coa-sia-cate-list/view/{id}', CoaSiaCateListController::Class.'@view');
    Route::get('/coa-sia-cate-list/edit/{id}', CoaSiaCateListController::Class.'@edit');
    Route::post('/coa-sia-cate-list/update/{id}', CoaSiaCateListController::Class.'@update');
    Route::post('/coa-sia-cate-list/delete/{id}', CoaSiaCateListController::Class.'@delete');
    Route::post('/coa-sia-cate-list/get-list-child', CoaSiaCateListController::Class.'@getListChild');
    Route::post('/coa-sia-cate-list/delete', CoaSiaCateListController::Class.'@deleteArray');
    Route::post('/coa-sia-cate-list/store', CoaSiaCateListController::Class.'@store');

    // ====================================== CoaLdi- hệ thống tài khoản QĐTPTĐP ==========================
    Route::post('/coa-ldi', CoaLdiController::Class.'@list');
    Route::get('/coa-ldi/create', CoaLdiController::Class.'@create');
    Route::get('/coa-ldi/view/{id}', CoaLdiController::Class.'@view');
    Route::get('/coa-ldi/edit/{id}', CoaLdiController::Class.'@edit');
    Route::post('/coa-ldi/update/{id}', CoaLdiController::Class.'@update');
    Route::post('/coa-ldi/delete/{id}', CoaLdiController::Class.'@delete');
    Route::post('/coa-ldi/delete', CoaLdiController::Class.'@deleteArray');
    Route::post('/coa-ldi/store', CoaLdiController::Class.'@store');
    Route::get('/coa-ldi/get-coa-ldi-cate-list',CoaLdiController::Class.'@getCoaLdiCateList');
    Route::get('/coa-ldi/get-coa-ldi-link/{id}',CoaLdiController::Class.'@getCoaLdiLink');
    Route::post('/coa-ldi/coa-ldi-data-list',CoaLdiController::Class.'@updateLink');
    Route::post('/coa-ldi/update-per/{id}', CoaLdiController::Class.'@updatePer');
    Route::post('/coa-ldi/get-report-data', CoaLdiController::Class.'@getReportData');

    // ================================== CoaLdi Cate List - loại hê thống tài khoản QĐTPTĐP ===============
    Route::post('/coa-ldi-cate-list', CoaLdiCateListController::Class.'@list');
    Route::get('/coa-ldi-cate-list/create', CoaLdiCateListController::Class.'@create');
    Route::get('/coa-ldi-cate-list/view/{id}', CoaLdiCateListController::Class.'@view');
    Route::get('/coa-ldi-cate-list/edit/{id}', CoaLdiCateListController::Class.'@edit');
    Route::post('/coa-ldi-cate-list/update/{id}', CoaLdiCateListController::Class.'@update');
    Route::post('/coa-ldi-cate-list/delete/{id}', CoaLdiCateListController::Class.'@delete');
    Route::post('/coa-ldi-cate-list/get-list-child', CoaLdiCateListController::Class.'@getListChild');
    Route::post('/coa-ldi-cate-list/delete', CoaLdiCateListController::Class.'@deleteArray');
    Route::post('/coa-ldi-cate-list/store', CoaLdiCateListController::Class.'@store');

    // ====================================== CoaPcf- hệ thống tài khoản QTDND ==========================
    Route::post('/coa-pcf', CoaPcfController::Class.'@list');
    Route::get('/coa-pcf/create', CoaPcfController::Class.'@create');
    Route::get('/coa-pcf/view/{id}', CoaPcfController::Class.'@view');
    Route::get('/coa-pcf/edit/{id}', CoaPcfController::Class.'@edit');
    Route::post('/coa-pcf/update/{id}', CoaPcfController::Class.'@update');
    Route::post('/coa-pcf/delete/{id}', CoaPcfController::Class.'@delete');
    Route::post('/coa-pcf/delete', CoaPcfController::Class.'@deleteArray');
    Route::post('/coa-pcf/store', CoaPcfController::Class.'@store');
    Route::get('/coa-pcf/get-coa-pcf-cate-list',CoaPcfController::Class.'@getCoaPcfCateList');
    Route::get('/coa-pcf/get-coa-pcf-link/{id}',CoaPcfController::Class.'@getCoaPcfLink');
    Route::post('/coa-pcf/coa-pcf-data-list',CoaPcfController::Class.'@updateLink');
    Route::post('/coa-pcf/update-per/{id}', CoaPcfController::Class.'@updatePer');
    Route::post('/coa-pcf/get-report-data', CoaPcfController::Class.'@getReportData');

    // ================================== CoaPcf Cate List - loại hê thống tài khoản QTDND ===============
    Route::post('/coa-pcf-cate-list', CoaPcfCateListController::Class.'@list');
    Route::get('/coa-pcf-cate-list/create', CoaPcfCateListController::Class.'@create');
    Route::get('/coa-pcf-cate-list/view/{id}', CoaPcfCateListController::Class.'@view');
    Route::get('/coa-pcf-cate-list/edit/{id}', CoaPcfCateListController::Class.'@edit');
    Route::post('/coa-pcf-cate-list/update/{id}', CoaPcfCateListController::Class.'@update');
    Route::post('/coa-pcf-cate-list/delete/{id}', CoaPcfCateListController::Class.'@delete');
    Route::post('/coa-pcf-cate-list/get-list-child', CoaPcfCateListController::Class.'@getListChild');
    Route::post('/coa-pcf-cate-list/delete', CoaPcfCateListController::Class.'@deleteArray');
    Route::post('/coa-pcf-cate-list/store', CoaPcfCateListController::Class.'@store');

    //Norm Table
    Route::post('/norm-table', NormTableController::Class.'@list');
    Route::get('/norm-table/create', NormTableController::Class.'@create');
    Route::get('/norm-table/view/{id}', NormTableController::Class.'@view');
    Route::get('/norm-table/edit/{id}', NormTableController::Class.'@edit');
    Route::post('/norm-table/update/{id}', NormTableController::Class.'@update');
    Route::post('/norm-table/delete/{id}', NormTableController::Class.'@delete');
    Route::post('/norm-table/delete', NormTableController::Class.'@deleteArray');
    Route::post('/norm-table/store', NormTableController::Class.'@store');
    Route::post('/norm-table/calculator-table-item', NormTableController::Class.'@calculatorTableItem');
    Route::post('/norm-table/re-calculator-table-item', NormTableController::Class.'@reCalculatorTableItem');
    Route::get('/norm-table/load-temp/{id}', NormTableController::Class.'@loadTemp');
    Route::post('/norm-table/table-per', NormTableController::Class.'@getTablePer');
    Route::post('/norm-table/update-per', NormTableController::Class.'@updateTablePer');
    Route::post('/norm-table/get-report-data', NormTableController::Class.'@getReportData');
    Route::get('/norm-table/get-norm-allot-level/{id}', NormTableController::Class.'@getNormAllotLevel');

    // ================================== Norm Cate List - loại chi tiêu định mức ===============
    Route::post('/norm-cate-list', NormCateListController::Class.'@list');
    Route::get('/norm-cate-list/create', NormCateListController::Class.'@create');
    Route::get('/norm-cate-list/view/{id}', NormCateListController::Class.'@view');
    Route::get('/norm-cate-list/edit/{id}', NormCateListController::Class.'@edit');
    Route::post('/norm-cate-list/get-list-child', NormCateListController::Class.'@getListChild');
    Route::post('/norm-cate-list/update/{id}', NormCateListController::Class.'@update');
    Route::post('/norm-cate-list/delete/{id}', NormCateListController::Class.'@delete');
    Route::post('/norm-cate-list/delete', NormCateListController::Class.'@deleteArray');
    Route::post('/norm-cate-list/store', NormCateListController::Class.'@store');

    // Norm
    Route::post('/norm', NormController::Class.'@list');
    Route::get('/norm/create', NormController::Class.'@create');
    Route::get('/norm/view/{id}', NormController::Class.'@view');
    Route::get('/norm/edit/{id}', NormController::Class.'@edit');
    Route::post('/norm/update/{id}', NormController::Class.'@update');
    Route::post('/norm/delete/{id}', NormController::Class.'@delete');
    Route::post('/norm/delete', NormController::Class.'@deleteArray');
    Route::post('/norm/store', NormController::Class.'@store');
    Route::get('/norm/get-norm-cate-list',NormController::Class.'@getCate');
    Route::post('norm/get-table-item', NormController::Class.'@getTableItem');
    Route::post('/norm/get-list-child',NormController::Class.'@getListChild');
    Route::get('/norm/get-norm-allot/{id}',NormController::Class.'@getNormAllot');
    Route::post('/norm/update-norm-allot',NormController::Class.'@updateNormAllot');

    //Item
    Route::post('/item', ItemController::Class.'@list');
    Route::get('/item/create', ItemController::Class.'@create');
    Route::get('/item/view/{id}', ItemController::Class.'@view');
    Route::get('/item/edit/{id}', ItemController::Class.'@edit');
    Route::post('/item/update/{id}', ItemController::Class.'@update');
    Route::post('/item/delete/{id}', ItemController::Class.'@delete');
    Route::post('/item/delete', ItemController::Class.'@deleteArray');
    Route::post('/item/store', ItemController::Class.'@store');
    Route::post('/item/item-data-list', ItemController::Class.'@updateLink');
    Route::get('/item/get-item-link/{id}', ItemController::Class.'@getLink');
    Route::get('/item/get-item-file/{id}', ItemController::Class.'@getFile');
    Route::get('/item/get-cate-list', ItemController::Class.'@getCate');
    Route::post('/item/item-upload-file/{id}', ItemController::Class.'@uploadFile');
    Route::post('/item/delete-file/{id}', ItemController::Class.'@deleteFile');
    Route::get('/item/download-file/{id}', ItemController::Class.'@downloadFile');
    Route::get('/item/download-all-file/{id}', ItemController::Class.'@downloadAllFile');
    Route::get('/item/get-item-video/{id}', ItemController::Class.'@getVideo');
    Route::post('/item/item-upload-video/{id}', ItemController::Class.'@uploadVideo');
    Route::post('/item/delete-video/{id}', ItemController::Class.'@deleteVideo');
    Route::get('/item/download-video/{id}', ItemController::Class.'@downloadVideo');
    Route::post('/item/update-doc-cate/{id}', ItemController::Class.'@updateDocCate');
    Route::post('/item/update-per/{id}', ItemController::Class.'@updatePer');
    Route::post('/item/get-report-data', ItemController::Class.'@getReportData');
    Route::post('/item/get-uom', ItemController::Class.'@getUom');

     //Item Cate List
    Route::post('/item-cate-list', ItemCateListController::Class.'@list');
    Route::get('/item-cate-list/create', ItemCateListController::Class.'@create');
    Route::get('/item-cate-list/view/{id}', ItemCateListController::Class.'@view');
    Route::get('/item-cate-list/edit/{id}', ItemCateListController::Class.'@edit');
    Route::post('/item-cate-list/update/{id}', ItemCateListController::Class.'@update');
    Route::post('/item-cate-list/delete/{id}', ItemCateListController::Class.'@delete');
    Route::post('/item-cate-list/delete', ItemCateListController::Class.'@deleteArray');
    Route::post('/item-cate-list/store', ItemCateListController::Class.'@store');

    // ================================== RevenueNorm Cate List - loại định mức thu===============
    Route::post('/revenue-norm-cate-list', RevenueNormCateListController::Class.'@list');
    Route::get('/revenue-norm-cate-list/create', RevenueNormCateListController::Class.'@create');
    Route::get('/revenue-norm-cate-list/view/{id}', RevenueNormCateListController::Class.'@view');
    Route::get('/revenue-norm-cate-list/edit/{id}', RevenueNormCateListController::Class.'@edit');
    Route::get('/revenue-norm-cate-list/edit/{id}', RevenueNormCateListController::Class.'@edit');
    Route::post('/revenue-norm-cate-list/update/{id}', RevenueNormCateListController::Class.'@update');
    Route::post('/revenue-norm-cate-list/delete/{id}', RevenueNormCateListController::Class.'@delete');
    Route::post('/revenue-norm-cate-list/delete', RevenueNormCateListController::Class.'@deleteArray');
    Route::post('/revenue-norm-cate-list/store', RevenueNormCateListController::Class.'@store');

    // ============================= Position - Chức vụ ====================================
    Route::post('/position', PositionController::Class.'@list');
    Route::get('/position/create', PositionController::Class.'@create');
    Route::post('/position/store', PositionController::Class.'@store');
    Route::get('/position/view/{id}', PositionController::Class.'@view');
    Route::post('/position/update/{id}', PositionController::Class.'@update');
    Route::post('/position/delete/{id}', PositionController::Class.'@delete');
    Route::post('/position/delete', PositionController::Class.'@deleteArray');
    Route::get('/position/edit/{id}', PositionController::Class.'@edit');

    // ============================ Bank - Ngân hàng =======================================
    Route::post('/bank', BankController::Class.'@list');
    Route::post('/bank/store', BankController::Class.'@store');
    Route::get('/bank/view/{id}', BankController::Class.'@view');
    Route::get('/bank/create', BankController::Class.'@create');
    Route::post('/bank/delete/{id}', BankController::Class.'@delete');
    Route::post('/bank/delete', BankController::Class.'@deleteArray');
    Route::get('/bank/edit/{id}', BankController::Class.'@edit');
    Route::post('/bank/update/{id}', BankController::Class.'@update');
    Route::post('/bank/get-report-data', BankController::Class.'@getReportData');

    // ============================ Treasury - Kho bạc =================================
    Route::post('/treasury', TreasuryController::Class.'@list');
    Route::post('/common/get-province', CommonController::Class.'@getProvince');
    Route::post('/common/get-district', CommonController::Class.'@getDistrict');
    Route::get('/treasury/create', TreasuryController::Class.'@create');
    Route::post('/treasury/store', TreasuryController::Class.'@store');
    Route::get('/treasury/view/{id}', TreasuryController::Class.'@view');
    Route::get('/treasury/edit/{id}', TreasuryController::Class.'@edit');
    Route::post('/treasury/update/{id}', TreasuryController::Class.'@update');
    Route::post('/treasury/delete/{id}', TreasuryController::Class.'@delete');
    Route::post('/treasury/delete', TreasuryController::Class.'@deleteArray');
    Route::post('/treasury/get-tre-org', TreasuryController::Class.'@getTreOrg');

    // ============================ Sponsor - Nhà tài trợ =================================
    Route::post('/sponsor', SponsorController::Class.'@list');
    Route::post('/common/get-province', CommonController::Class.'@getProvince');
    Route::post('/common/get-district', CommonController::Class.'@getDistrict');
    Route::get('/sponsor/create', SponsorController::Class.'@create');
    Route::post('/sponsor/store', SponsorController::Class.'@store');
    Route::get('/sponsor/view/{id}', SponsorController::Class.'@view');
    Route::get('/sponsor/edit/{id}', SponsorController::Class.'@edit');
    Route::post('/sponsor/update/{id}', SponsorController::Class.'@update');
    Route::post('/sponsor/delete/{id}', SponsorController::Class.'@delete');
    Route::post('/sponsor/delete', SponsorController::Class.'@deleteArray');
    Route::post('/sponsor/get-report-data', SponsorController::Class.'@getReportData');

    // ====================================== Fund - Danh mục quỹ ==========================
    Route::post('/fund', FundController::Class.'@list');
    Route::get('/fund/create', FundController::Class.'@create');
    Route::get('/fund/view/{id}', FundController::Class.'@view');
    Route::get('/fund/edit/{id}', FundController::Class.'@edit');
    Route::post('/fund/update/{id}', FundController::Class.'@update');
    Route::post('/fund/delete/{id}', FundController::Class.'@delete');
    Route::post('/fund/delete', FundController::Class.'@deleteArray');
    Route::post('/fund/store', FundController::Class.'@store');
    Route::get('/fund/get-fund-cate-list',FundController::Class.'@getFundCateList');
    Route::get('/fund/get-fund-link/{id}',FundController::Class.'@getFundLink');
    Route::post('/fund/fund-data-list',FundController::Class.'@updateLink');
    Route::post('/fund/update-per/{id}', FundController::Class.'@updatePer');
    Route::post('/fund/get-report-data', FundController::Class.'@getReportData');

    //========================================= Country - Quốc gia ==================================================================
    Route::post('/country', CountryController::class.'@list');
    Route::post('/country/store', CountryController::Class.'@store');
    Route::get('/country/view/{id}', CountryController::Class.'@view');
    Route::get('/country/edit/{id}', CountryController::Class.'@edit');
    Route::post('/country/update/{id}', CountryController::Class.'@update');
    Route::post('/country/delete', CountryController::Class.'@deleteArray');
    Route::post('/country/delete/{id}', CountryController::Class.'@delete');

    // ============================= Direction cate list - Loại chỉ thị =========================
    Route::post('/direction-cate-list',DirectionCateListController::Class.'@list');
    Route::get('/direction-cate-list/create',DirectionCateListController::Class.'@create');
    Route::get('/direction-cate-list/view/{id}',DirectionCateListController::Class.'@view');
    Route::get('/direction-cate-list/edit/{id}',DirectionCateListController::Class.'@edit');
    Route::post('/direction-cate-list/update/{id}',DirectionCateListController::Class.'@update');
    Route::post('/direction-cate-list/delete/{id}',DirectionCateListController::Class.'@delete');
    Route::post('/direction-cate-list/delete',DirectionCateListController::Class.'@deleteArray');
    Route::post('/direction-cate-list/store',DirectionCateListController::Class.'@store');

    // ============================= Direction - Dự án =======================================
    Route::post('/direction', DirectionController::Class.'@list');
    Route::get('/direction/create', DirectionController::Class.'@create');
    Route::get('/direction/view/{id}', DirectionController::Class.'@view');
    Route::get('/direction/edit/{id}', DirectionController::Class.'@edit');
    Route::post('/direction/update/{id}', DirectionController::Class.'@update');
    Route::post('/direction/delete/{id}', DirectionController::Class.'@delete');
    Route::post('/direction/delete', DirectionController::Class.'@deleteArray');
    Route::post('/direction/store', DirectionController::Class.'@store');
    Route::get('/direction/get-direction-file/{id}', DirectionController::Class.'@getFile');
    Route::get('/direction/get-direction-cate-list', DirectionController::Class.'@getCate');
    Route::post('/direction/direction-upload-file/{id}', DirectionController::Class.'@uploadFile');
    Route::post('/direction/delete-file/{id}', DirectionController::Class.'@deleteFile');
    Route::get('/direction/download-file/{id}', DirectionController::Class.'@downloadFile');
    Route::get('/direction/download-all-file/{id}', DirectionController::Class.'@downloadAllFile');
    Route::post('/direction/update-per/{id}', DirectionController::Class.'@updatePer');
    Route::post('/direction/get-report-data', DirectionController::Class.'@getReportData');

    //========================================= Fixed Asset - Tài sản cố định ========================================================
    Route::post('/fixed-asset', FixedAssetController::Class.'@list');
    Route::get('/fixed-asset/create', FixedAssetController::Class.'@create');
    Route::get('/fixed-asset/view/{id}', FixedAssetController::Class.'@view');
    Route::get('/fixed-asset/edit/{id}', FixedAssetController::Class.'@edit');
    Route::post('/fixed-asset/update/{id}', FixedAssetController::Class.'@update');
    Route::post('/fixed-asset/delete/{id}', FixedAssetController::Class.'@delete');
    Route::post('/fixed-asset/delete', FixedAssetController::Class.'@deleteArray');
    Route::post('/fixed-asset/store', FixedAssetController::Class.'@store');
    Route::post('/fixed-asset/fixed-asset-data-list', FixedAssetController::Class.'@updateLink');
    Route::get('/fixed-asset/get-fixed-asset-link/{id}', FixedAssetController::Class.'@getLink');
    Route::get('/fixed-asset/get-fixed-asset-file/{id}', FixedAssetController::Class.'@getFile');
    Route::get('/fixed-asset/get-cate-list', FixedAssetController::Class.'@getCate');
    Route::post('/fixed-asset/fixed-asset-upload-file/{id}', FixedAssetController::Class.'@uploadFile');
    Route::post('/fixed-asset/delete-file/{id}', FixedAssetController::Class.'@deleteFile');
    Route::get('/fixed-asset/download-file/{id}', FixedAssetController::Class.'@downloadFile');
    Route::get('/fixed-asset/download-all-file/{id}', FixedAssetController::Class.'@downloadAllFile');
    Route::get('/fixed-asset/get-fixed-asset-video/{id}', FixedAssetController::Class.'@getVideo');
    Route::post('/fixed-asset/fixed-asset-upload-video/{id}', FixedAssetController::Class.'@uploadVideo');
    Route::post('/fixed-asset/delete-video/{id}', FixedAssetController::Class.'@deleteVideo');
    Route::get('/fixed-asset/download-video/{id}', FixedAssetController::Class.'@downloadVideo');
    Route::post('/fixed-asset/update-doc-cate/{id}', FixedAssetController::Class.'@updateDocCate');
    Route::post('/fixed-asset/update-per/{id}', FixedAssetController::Class.'@updatePer');
    Route::post('/fixed-asset/get-report-data', FixedAssetController::Class.'@getReportData');
    Route::post('/fixed-asset/get-uom', FixedAssetController::Class.'@getUom');

    //========================================= Invest Asset - Tài sản đầu tư ========================================================
    Route::post('/invest-asset', InvestAssetController::Class.'@list');
    Route::get('/invest-asset/create', InvestAssetController::Class.'@create');
    Route::get('/invest-asset/view/{id}', InvestAssetController::Class.'@view');
    Route::get('/invest-asset/edit/{id}', InvestAssetController::Class.'@edit');
    Route::post('/invest-asset/update/{id}', InvestAssetController::Class.'@update');
    Route::post('/invest-asset/delete/{id}', InvestAssetController::Class.'@delete');
    Route::post('/invest-asset/delete', InvestAssetController::Class.'@deleteArray');
    Route::post('/invest-asset/store', InvestAssetController::Class.'@store');
    Route::post('/invest-asset/invest-asset-data-list', InvestAssetController::Class.'@updateLink');
    Route::get('/invest-asset/get-invest-asset-link/{id}', InvestAssetController::Class.'@getLink');
    Route::get('/invest-asset/get-invest-asset-file/{id}', InvestAssetController::Class.'@getFile');
    Route::get('/invest-asset/get-cate-list', InvestAssetController::Class.'@getCate');
    Route::post('/invest-asset/invest-asset-upload-file/{id}', InvestAssetController::Class.'@uploadFile');
    Route::post('/invest-asset/delete-file/{id}', InvestAssetController::Class.'@deleteFile');
    Route::get('/invest-asset/download-file/{id}', InvestAssetController::Class.'@downloadFile');
    Route::get('/invest-asset/download-all-file/{id}', InvestAssetController::Class.'@downloadAllFile');
    Route::get('/invest-asset/get-invest-asset-video/{id}', InvestAssetController::Class.'@getVideo');
    Route::post('/invest-asset/invest-asset-upload-video/{id}', InvestAssetController::Class.'@uploadVideo');
    Route::post('/invest-asset/delete-video/{id}', InvestAssetController::Class.'@deleteVideo');
    Route::get('/invest-asset/download-video/{id}', InvestAssetController::Class.'@downloadVideo');
    Route::post('/invest-asset/update-doc-cate/{id}', InvestAssetController::Class.'@updateDocCate');
    Route::post('/invest-asset/update-per/{id}', InvestAssetController::Class.'@updatePer');
    Route::post('/invest-asset/get-report-data', InvestAssetController::Class.'@getReportData');
    Route::post('/invest-asset/get-uom', InvestAssetController::Class.'@getUom');
    Route::post('/invest-asset/get-list-child',InvestAssetController::Class.'@getListChild');
    //========================================= Tool - Công cụ dụng cụ ========================================================
    Route::post('/tool', ToolController::Class.'@list');
    Route::get('/tool/create', ToolController::Class.'@create');
    Route::get('/tool/view/{id}', ToolController::Class.'@view');
    Route::get('/tool/edit/{id}', ToolController::Class.'@edit');
    Route::post('/tool/update/{id}', ToolController::Class.'@update');
    Route::post('/tool/delete/{id}', ToolController::Class.'@delete');
    Route::post('/tool/delete', ToolController::Class.'@deleteArray');
    Route::post('/tool/store', ToolController::Class.'@store');
    Route::post('/tool/tool-data-list', ToolController::Class.'@updateLink');
    Route::get('/tool/get-tool-link/{id}', ToolController::Class.'@getLink');
    Route::get('/tool/get-tool-file/{id}', ToolController::Class.'@getFile');
    Route::get('/tool/get-cate-list', ToolController::Class.'@getCate');
    Route::post('/tool/tool-upload-file/{id}', ToolController::Class.'@uploadFile');
    Route::post('/tool/delete-file/{id}', ToolController::Class.'@deleteFile');
    Route::get('/tool/download-file/{id}', ToolController::Class.'@downloadFile');
    Route::get('/tool/download-all-file/{id}', ToolController::Class.'@downloadAllFile');
    Route::get('/tool/get-tool-video/{id}', ToolController::Class.'@getVideo');
    Route::post('/tool/tool-upload-video/{id}', ToolController::Class.'@uploadVideo');
    Route::post('/tool/delete-video/{id}', ToolController::Class.'@deleteVideo');
    Route::get('/tool/download-video/{id}', ToolController::Class.'@downloadVideo');
    Route::post('/tool/update-doc-cate/{id}', ToolController::Class.'@updateDocCate');
    Route::post('/tool/update-per/{id}', ToolController::Class.'@updatePer');
    Route::post('/tool/get-report-data', ToolController::Class.'@getReportData');
    Route::post('/tool/get-uom', ToolController::Class.'@getUom');

    // ============================= Center - Trung ương ====================================
    Route::post('/center', CenterController::Class.'@list');
    Route::get('/center/create', CenterController::Class.'@create');
    Route::post('/center/store', CenterController::Class.'@store');
    Route::get('/center/view/{id}', CenterController::Class.'@view');
    Route::post('/center/update/{id}', CenterController::Class.'@update');
    Route::post('/center/delete/{id}', CenterController::Class.'@delete');
    Route::post('/center/delete', CenterController::Class.'@deleteArray');
    Route::get('/center/edit/{id}', CenterController::Class.'@edit');
    Route::post('/center/get-report-data', CenterController::Class.'@getReportData');

    // ============================= SbiChapter cate list - Loại chương =========================
    Route::post('/sbi-chapter-cate-list',SbiChapterCateListController::Class.'@list');
    Route::get('/sbi-chapter-cate-list/create',SbiChapterCateListController::Class.'@create');
    Route::get('/sbi-chapter-cate-list/view/{id}',SbiChapterCateListController::Class.'@view');
    Route::get('/sbi-chapter-cate-list/edit/{id}',SbiChapterCateListController::Class.'@edit');
    Route::post('/sbi-chapter-cate-list/update/{id}',SbiChapterCateListController::Class.'@update');
    Route::post('/sbi-chapter-cate-list/delete/{id}',SbiChapterCateListController::Class.'@delete');
    Route::post('/sbi-chapter-cate-list/delete',SbiChapterCateListController::Class.'@deleteArray');
    Route::post('/sbi-chapter-cate-list/store',SbiChapterCateListController::Class.'@store');
    Route::post('/sbi-chapter-cate-list/get-list-child',SbiChapterCateListController::Class.'@getListChild');

    // ============================= SbiCategory cate list - Loại Loại khoản =========================
    Route::post('/sbi-category-cate-list',SbiCategoryCateListController::Class.'@list');
    Route::get('/sbi-category-cate-list/create',SbiCategoryCateListController::Class.'@create');
    Route::get('/sbi-category-cate-list/view/{id}',SbiCategoryCateListController::Class.'@view');
    Route::get('/sbi-category-cate-list/edit/{id}',SbiCategoryCateListController::Class.'@edit');
    Route::post('/sbi-category-cate-list/get-list-child',SbiCategoryCateListController::Class.'@getListChild');
    Route::post('/sbi-category-cate-list/update/{id}',SbiCategoryCateListController::Class.'@update');
    Route::post('/sbi-category-cate-list/delete/{id}',SbiCategoryCateListController::Class.'@delete');
    Route::post('/sbi-category-cate-list/delete',SbiCategoryCateListController::Class.'@deleteArray');
    Route::post('/sbi-category-cate-list/store',SbiCategoryCateListController::Class.'@store');

    // ===================================== NormAllot - Định mức phân bổ dự toán =============================
    Route::post('/norm-allot', NormAllotController::Class.'@list');
    Route::get('/norm-allot/create', NormAllotController::Class.'@create');
    Route::get('/norm-allot/view/{id}', NormAllotController::Class.'@view');
    Route::get('/norm-allot/edit/{id}', NormAllotController::Class.'@edit');
    Route::post('/norm-allot/update/{id}', NormAllotController::Class.'@update');
    Route::post('/norm-allot/delete/{id}', NormAllotController::Class.'@delete');
    Route::post('/norm-allot/delete', NormAllotController::Class.'@deleteArray');
    Route::post('/norm-allot/store', NormAllotController::Class.'@store');
    Route::post('/norm-allot/get-list-child',NormAllotController::Class.'@getListChild');
    Route::get('/norm-allot/get-norm-allot-cate-list',NormAllotController::Class.'@getNormAllotCateList');
    Route::post('/norm-allot/get-report-data', NormAllotController::Class.'@getReportData');

    // ============================= NormAllot cate list - Loại định mức phân bổ dự toán =========================
    Route::post('/norm-allot-cate-list',NormAllotCateListController::Class.'@list');
    Route::get('/norm-allot-cate-list/create',NormAllotCateListController::Class.'@create');
    Route::get('/norm-allot-cate-list/view/{id}',NormAllotCateListController::Class.'@view');
    Route::get('/norm-allot-cate-list/edit/{id}',NormAllotCateListController::Class.'@edit');
    Route::post('/norm-allot-cate-list/update/{id}',NormAllotCateListController::Class.'@update');
    Route::post('/norm-allot-cate-list/delete/{id}',NormAllotCateListController::Class.'@delete');
    Route::post('/norm-allot-cate-list/get-list-child',NormAllotCateListController::Class.'@getListChild');
    Route::post('/norm-allot-cate-list/delete',NormAllotCateListController::Class.'@deleteArray');
    Route::post('/norm-allot-cate-list/store',NormAllotCateListController::Class.'@store');

    // ============================= norm_allot_level - Định mức phân bổ dự toán =========================
    Route::post('/norm-allot-level',NormAllotLevelController::Class.'@list');
    Route::get('/norm-allot-level/create',NormAllotLevelController::Class.'@create');
    Route::get('/norm-allot-level/view/{id}',NormAllotLevelController::Class.'@view');
    Route::get('/norm-allot-level/edit/{id}',NormAllotLevelController::Class.'@edit');
    Route::post('/norm-allot-level/update/{id}',NormAllotLevelController::Class.'@update');
    Route::post('/norm-allot-level/delete/{id}',NormAllotLevelController::Class.'@delete');
    Route::post('/norm-allot-level/get-list-child',NormAllotLevelController::Class.'@getListChild');
    Route::post('/norm-allot-level/delete',NormAllotLevelController::Class.'@deleteArray');
    Route::post('/norm-allot-level/store',NormAllotLevelController::Class.'@store');


});

Route::post('/client-db', function (Request $request) {
    $json['status'] = 1;
    $json['msg'] = 'Thành công!';
    $json['data'] = [];
    $json['per_page'] = $request->post('per_page');
    $json['term'] = $request->post('term');
    return json_encode($json);
});
