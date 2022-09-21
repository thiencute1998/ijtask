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
    // chat
    Route::post('/chat', ChatController::Class.'@index');
    Route::post('/chat/load-message', ChatController::Class.'@loadMessage');
    Route::post('/chat/store-message', ChatController::Class.'@storeMessage');
    Route::post('/chat/update-message', ChatController::Class.'@updateMessage');
    Route::get('/chat/create-group', ChatController::Class.'@createGroup');
    Route::post('/chat/store-group', ChatController::Class.'@storeGroup');
    Route::post('/chat/read-message', ChatController::Class.'@readMessage');
    Route::get('/chat/get-table', ChatController::Class.'@getAllTable');
    Route::post('/chat/update-category-key', ChatController::Class.'@updateCategoryKey');
    Route::post('/chat/add-members', ChatController::Class.'@addMembers');
    Route::post('/chat/remove-member', ChatController::Class.'@removeMember');
    Route::post('/chat/leave-group', ChatController::Class.'@leaveGroup');
    Route::post('/chat/set-member-role', ChatController::Class.'@setMemberRole');
    Route::post('/chat/get-members', ChatController::Class.'@getMembers');
    Route::post('/chat/update-group', ChatController::Class.'@updateGroup');
    Route::post('/chat/delete-group', ChatController::Class.'@deleteGroup');
    Route::post('/chat/delete-message', ChatController::Class.'@deleteMessage');
    Route::post('/chat/get-message', ChatController::Class.'@getMessage');
    Route::get('/chat/download-file/{id}', ChatController::Class.'@downloadFile');
    Route::post('/chat/get-task-status-value', ChatController::Class.'@getTaskStatusValue');
    Route::post('/chat/update-task-status-value', ChatController::Class.'@updateTaskStatusValue');
    Route::get('/chat/get-all-employee', ChatController::Class.'@getAllEmployee');

    //category comment
    Route::post('/chat/category-comment', ChatController::Class.'@getCategoryComment');
    Route::post('/chat/category-reply', ChatController::Class.'@getCategoryReply');

    // social
    Route::post('/social', SocialController::Class.'@index');
    Route::post('/social/load-post', SocialController::Class.'@loadPost');
    Route::post('/social/load-more-comment', SocialController::Class.'@loadMoreComment');


    // notification
    Route::post('/notice', NotificationController::Class . '@index');
    Route::post('/notice/store-notice', NotificationController::Class . '@storeNotice');
    Route::post('/notice/store-task-notice', NotificationController::Class . '@storeTaskNotice');
    Route::post('/notice/store-task-dataflow-notice', NotificationController::Class . '@storeTaskDataflowNotice');
    Route::post('/notice/update-status-notice', NotificationController::Class . '@updateStatusNotice');
    Route::post('/notice/get-user-receive', NotificationController::Class . '@getUserReceive');
});
