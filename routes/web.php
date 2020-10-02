<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', function () {
    return view('adminlte::login');
});

Route::get('/', function () {
    if (Auth::user() != null && (Auth::user()->user_type == 'Admin' || Auth::user()->user_type == 'Management')) {
        return redirect('/admin');
    }
    else{
        return redirect('/login');
    }
});


Auth::routes();

// Route::get('/home', function() {
//     return view('home');
// })->name('home')->middleware('auth');

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');
Route::get('/admin','HomeController@admin')->name('admin')->middleware('can:admin-dashboard');
Route::get('/admin/search', 'HomeController@search')->name('admin__leave_search')->middleware('auth');
Route::get('/admin/add-remark', 'HomeController@store_remarks')->name('add_remark')->middleware('auth'); // Added
Route::get('/delete-remarks', 'HomeController@delete_remarks')->middleware('auth'); // Added
Route::get('/load-remarks', 'HomeController@load_remarks')->middleware('auth'); // Added

Route::get('/myprofile','UserController@index')->name('view_profile')->middleware('auth');
Route::get('/myprofile/edit','UserController@edit')->name('edit_profile')->middleware('auth');
Route::post('/myprofile/update','UserController@update')->name('update_profile')->middleware('auth');
//Change Password
Route::get('/change-password', 'ChangePasswordController@index')->name('change_password');
Route::post('/change-password', 'ChangePasswordController@store')->name('change.password');


//Create, Edit, Delete User
Route::middleware('can:employee-data')->group(function(){
    Route::get('/create', 'RegistrationController@create')->name('user_create')->middleware('auth');
    Route::post('create', 'RegistrationController@store')->name('user_store')->middleware('auth');
    Route::get('/edit/{user}','RegistrationController@edit')->name('user_edit')->middleware('auth');
    Route::post('/update/{user}','RegistrationController@update')->name('user_update')->middleware('auth');
    Route::get('/user/{user}','RegistrationController@profile')->name('user_view')->middleware('auth');
    Route::get('/user/delete/{user}','RegistrationController@destroy')->name('user_delete')->middleware('auth');
    Route::get('/user/deactivate/{user}','RegistrationController@deactivate')->name('user_deactivate')->middleware('auth');
    Route::get('/search', 'RegistrationController@search')->name('user_search')->middleware('auth');
    Route::get('/apply/for/{user}','LeaveApplicationController@applyFor')->name('apply_for')->middleware('auth');
    Route::post('apply/for/{user}','LeaveApplicationController@submitApplyFor')->name('submit_apply_for')->middleware('auth');

     //Admin Control
     Route::post('load-history','AdminController@view_history')->middleware('auth');
     Route::post('load-approver','AdminController@view_approver')->middleware('auth');
     Route::get('reports', 'AdminController@index')->name('excel_transfer')->middleware('auth');
     Route::get('reports/search', 'AdminController@search')->name('search')->middleware('auth');
     Route::get('reports/change-status/', 'AdminController@change_status')->name('change_status')->middleware('auth');
     Route::post('reports/import', 'AdminController@import')->name('excel_import')->middleware('auth');
     Route::get('reports/export-all', 'AdminController@export_all')->name('excel_export_all')->middleware('auth');
     Route::get('reports/export-search', 'AdminController@export_search')->name('excel_export_search')->middleware('auth');
     Route::get('reports/export-balance', 'AdminController@export_leave_balance')->name('excel_export_bal')->middleware('auth');
     Route::get('reports/autocomplete', 'AdminController@autocomplete');

    // Route::get('deduct/burnt', 'AdminController@deduct_burnt')->name('deduct-burnt');
});
//Route::get('deduct/burnt', 'AdminController@deduct_burnt')->name('deduct-burnt');

Route::middleware('can:edit_settings')->group(function() {

    //Leave Type
    Route::get('/leavetype/create', 'LeaveTypeController@create')->name('leavetype_create')->middleware('auth');
    Route::post('leavetype/create', 'LeaveTypeController@store')->name('leavetype_store')->middleware('auth');
    Route::get('/delete/leave_type/{leaveType}', 'LeaveTypeController@destroy')->name('leavetype_delete')->middleware('auth');

    //Employee Type
    Route::get('/emptype/create', 'EmpTypeController@create')->middleware('auth');
    Route::post('emptype/create', 'EmpTypeController@store')->name('emptype_store')->middleware('auth');
    Route::get('/emptype/edit/{empType}','EmpTypeController@edit')->name('emptype_edit')->middleware('auth');
    Route::post('emptype/update/{empType}','EmpTypeController@update')->name('emptype_update')->middleware('auth');
    Route::get('/delete/emp_type/{empType}','EmpTypeController@destroy')->name('emptype_delete')->middleware('auth');

    //Employee Group
    Route::get('/empgroup/create','EmpGroupController@create')->middleware('auth');
    Route::post('empgroup/create','EmpGroupController@store')->name('empgroup_store')->middleware('auth');
    Route::get('/empgroup/edit/{empGroup}','EmpGroupController@edit')->name('empgroup_edit')->middleware('auth');
    Route::post('empgroup/update/{empGroup}','EmpGroupController@update')->name('empgroup_update')->middleware('auth');
    Route::get('/delete/emp_group/{empGroup}','EmpGroupController@destroy')->name('empgroup_delete')->middleware('auth');

    //Leave Entitlement
    Route::get('/entitlement/create/{empType}','LeaveEntitlementController@create')->name('leaveent_create')->middleware('auth');
    Route::post('entitlement/create/{empType}', 'LeaveEntitlementController@store')->name('leaveent_store')->middleware('auth');

    //Set Leave Earnings amount settings
    Route::post('/leave/earnings/set/{user}','LeaveController@setEarnings')->name('earnings_set')->middleware('auth');
    //Set Brough Forward Leave amount settings
    Route::post('/leave/broughtforward/set/{user}','LeaveController@setBroughtForward')->name('brought_fwd_set')->middleware('auth');

    //Approval authority
    Route::post('/create/approval_authority/{user}','ApprovalAuthorityController@store')->name('approval_auth_create')->middleware('auth');
    Route::post('update/approval_authority/{approvalAuthority}','ApprovalAuthorityController@update')->name('approval_auth_update')->middleware('auth');

    //Holiday
    Route::get('/holiday/view','HolidayController@index')->middleware('auth');
    Route::post('/holiday/create','HolidayController@store')->name('holiday_create')->middleware('auth');
    Route::get('/holiday/edit/{holiday}','HolidayController@edit')->name('holiday_edit')->middleware('auth');
    Route::post('/holiday/update/{holiday}','HolidayController@update')->name('holiday_update')->middleware('auth');
    Route::get('/holiday/delete/{holiday}','HolidayController@delete')->name('holiday_delete')->middleware('auth');
});




//Leave Application
Route::get('/leave/apply','LeaveApplicationController@create')->middleware('auth');
Route::post('leave/apply','LeaveApplicationController@store')->name('leaveapp_store')->middleware('auth');
Route::get('/leave/apply/view/{leaveApplication}','LeaveApplicationController@view')->name('view_application')->middleware('auth');
Route::get('/leave/apply/edit/{leaveApplication}','LeaveApplicationController@edit')->name('edit_application')->middleware('auth');
Route::post('/leave/apply/update/{leaveApplication}','LeaveApplicationController@update')->name('update_application')->middleware('auth');
Route::get('/leave/apply/approve/{leaveApplication}','LeaveApplicationController@approve')->name('approve_application')->middleware('auth');
Route::get('/leave/apply/deny/{leaveApplication}','LeaveApplicationController@deny')->name('deny_application')->middleware('auth');
Route::post('/leave/apply/cancel/{leaveApplication}','LeaveApplicationController@cancel')->name('cancel_application')->middleware('auth');

//Replacement leave
Route::get('/leave/replacement/apply','ReplacementLeaveController@create')->middleware('auth');

//Excel Import & Export
//Route::get('import-excel', 'ExcelController@index');
//Route::post('import-excel', 'ExcelController@import');


