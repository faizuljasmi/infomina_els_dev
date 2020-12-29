<?php


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

Route::post('/leave/list/mobile','LeaveApplicationController@list');
Route::post('/leave/list/my-apl/mobile','LeaveApplicationController@list_my_pending');
Route::post('/leave/action/mobile','LeaveApplicationController@mobile_action');
Route::post('/leave/total-pending/mobile','LeaveApplicationController@pending_count');

