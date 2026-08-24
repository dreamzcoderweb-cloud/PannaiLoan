<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoanAssignController;
use App\Http\Controllers\Admin\InterestController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EmiCollectionController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Report\DailyReportController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Report\CollectionSummaryController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Login page

//Route::view('/', 'landing')->name('landing');
Route::view('/', 'auth.login')->name('login');
Route::get('/server-commands/clear-cache', function () {

    // // Laravel caches
    Artisan::call('optimize:clear');

    // Spatie permission cache (IMPORTANT)
    Artisan::call('permission:cache-reset');

    return 'All caches cleared successfully!';
});
// Authenticate
Route::post('/authenticate', [LoginController::class, 'authenticate'])
    ->name('admin.authenticate');

// Forgot Password
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/verify-otp', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showOtpForm'])->name('password.otp');
Route::post('/verify-otp', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])->name('password.update');


// Protected Admin Routes
Route::middleware(['redirectmiddleware'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'home'])->name('admin.dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('admin.profile-update');

    //Staff CRUD
    Route::get('/staff/create', [StaffController::class, 'create'])->name('admin.staff-create')->middleware('check.permission:staff-create');
    Route::post('/staff/store', [StaffController::class, 'store'])->name('admin.staff-store')->middleware('check.permission:staff-store');
    Route::get('/staff/list', [StaffController::class, 'index'])->name('admin.staff-list')->middleware('check.permission:staff-list');
    Route::get('/staff/edit/{id}', [StaffController::class, 'edit'])
        ->name('admin.staff-edit')->middleware('check.permission:staff-edit');

    Route::post('/staff/update/{id}', [StaffController::class, 'update'])
        ->name('admin.staff-update')->middleware('check.permission:staff-update');

    //Role CRUD
    Route::get('/role/create', [RoleController::class, 'create'])->name('admin.role-create')->middleware('check.permission:role-create');
    Route::post('/role/store', [RoleController::class, 'store'])->name('admin.role-store')->middleware('check.permission:role-store');
    Route::get('/role/list', [RoleController::class, 'index'])->name('admin.role-list')->middleware('check.permission:role-list');
    Route::get('/role/edit/{id}', [RoleController::class, 'edit'])->name('admin.role-edit')->middleware('check.permission:role-edit');
    Route::post('/role/update/{id}', [RoleController::class, 'update'])->name('admin.role-update')->middleware('check.permission:role-update');


    // Loan Assign CRUD
    Route::get('/loanassign/create', [LoanAssignController::class, 'create'])->name('admin.loan-assign-create')->middleware('check.permission:loanassign-create');
    Route::post('/loanassign/store', [LoanAssignController::class, 'store'])->name('admin.loan-assign-store')->middleware('check.permission:loanassign-store');
    Route::get('/loanassign/list', [LoanAssignController::class, 'index'])->name('admin.loan-assign-list')->middleware('check.permission:loanassign-list');
    Route::get('/loanassign/edit/{id}', [LoanAssignController::class, 'edit'])->name('admin.loan-assign-edit')->middleware('check.permission:loanassign-edit');
    Route::put('/loanassign/update/{id}', [LoanAssignController::class, 'update'])->name('admin.loan-assign-update')->middleware('check.permission:loanassign-update');
    Route::get('/loanassign/search-customers', [LoanAssignController::class, 'loanSearchCustomers'])
        ->name('admin.loansearch-customers')->middleware('check.permission:loanassign-create');
    Route::get('/loanassign/delete/{id}', [LoanAssignController::class, 'delete'])->name('admin.loan-assign-delete')->middleware('check.permission:loanassign-delete');
    Route::get('/loan-assign/export-excel', [LoanAssignController::class, 'exportExcel'])
        ->name('admin.loan-assign-export-excel')->middleware('check.permission:loan-assign-export-excel');
    Route::get('/loan-assign/export-pdf', [LoanAssignController::class, 'exportPdf'])
        ->name('admin.loan-assign-export-pdf')->middleware('check.permission:loan-assign-export-pdf');
    Route::get('/clientgetdetails/{id}', [LoanAssignController::class, 'clientdetails'])->name('admin.loan-assign-clientdetails')->middleware('check.permission:loanassign-list');
    Route::post('/loan/foreclose', [LoanAssignController::class, 'foreclose'])->name('admin.loan.foreclose')->middleware('check.permission:loanassign-list');

    //interest crud - UPDATED WITH PROPER PERMISSION MIDDLEWARE
    Route::get('/interest/create', [InterestController::class, 'create'])->name('admin.interest-create')->middleware('check.permission:interest-create');
    Route::post('/interest/store', [InterestController::class, 'store'])->name('admin.interest-store')->middleware('check.permission:interest-store');
    Route::get('/interest/list', [InterestController::class, 'index'])->name('admin.interest-list')->middleware('check.permission:interest-list');
    Route::get('/interest/edit/{id}', [InterestController::class, 'edit'])->name('admin.interest-edit')->middleware('check.permission:interest-edit');
    Route::post('/interest/update/{id}', [InterestController::class, 'update'])->name('admin.interest-update')->middleware('check.permission:interest-update');
    Route::get('/interest/view/{id}', [InterestController::class, 'view'])->name('admin.interest-view')->middleware('check.permission:interest-view');
    // Consider adding delete route if needed:
    // Route::get('/interest/delete/{id}', [InterestController::class, 'delete'])->name('admin.interest-delete')->middleware('check.permission:interest-delete');

    //Loan CRUD
    Route::get('/loan/create', [LoanController::class, 'create'])->name('admin.loan-create')->middleware('check.permission:loan-create');
    Route::post('/loan/store', [LoanController::class, 'store'])->name('admin.loan-store')->middleware('check.permission:loan-store');
    Route::get('/loan/list', [LoanController::class, 'index'])->name('admin.loan-list')->middleware('check.permission:loan-list');
    Route::get('/loan/edit/{id}', [LoanController::class, 'edit'])->name('admin.loan-edit')->middleware('check.permission:loan-edit');
    Route::post('/loan/update/{id}', [LoanController::class, 'update'])->name('admin.loan-update')->middleware('check.permission:loan-update');
    Route::get('/loan/view/{id}', [LoanController::class, 'view'])->name('admin.loan-view')->middleware('check.permission:loan-view');
    Route::get('/loan/export-pdf', [LoanController::class, 'exportPdf'])->name('admin.loan-export-pdf')->middleware('check.permission:loan-export-pdf');
    Route::get('/loan/export-excel', [LoanController::class, 'exportExcel'])->name('admin.loan-export-excel')->middleware('check.permission:loan-export-excel');
    // Consider adding delete route if needed:
    // Route::get('/loan/delete/{id}', [LoanController::class, 'delete'])->name('admin.loan-delete')->middleware('check.permission:loan-delete');

    //Branch CRUD
    Route::get('/branch/create', [BranchController::class, 'create'])->name('admin.branch-create')->middleware('check.permission:branch-create');
    Route::post('/branch/store', [BranchController::class, 'store'])->name('admin.branch-store')->middleware('check.permission:branch-store');
    Route::get('/branch/list', [BranchController::class, 'index'])->name('admin.branch-list')->middleware('check.permission:branch-list');
    Route::get('/branch/edit/{id}', [BranchController::class, 'edit'])->name('admin.branch-edit')->middleware('check.permission:branch-edit');
    Route::post('/branch/update/{id}', [BranchController::class, 'update'])->name('admin.branch-update')->middleware('check.permission:branch-update');
    Route::get('/branch/view/{id}', [BranchController::class, 'view'])->name('admin.branch-view')->middleware('check.permission:branch-view');
    // Consider adding delete route if needed:
    // Route::get('/branch/delete/{id}', [BranchController::class, 'delete'])->name('admin.branch-delete')->middleware('check.permission:branch-delete');

    //Route CRUD
    Route::get('/route/create', [RouteController::class, 'create'])->name('admin.route-create')->middleware('check.permission:route-create');
    Route::post('/route/store', [RouteController::class, 'store'])->name('admin.route-store')->middleware('check.permission:route-store');
    Route::get('/route/list', [RouteController::class, 'index'])->name('admin.route-list')->middleware('check.permission:route-list');
    Route::get('/route/edit/{id}', [RouteController::class, 'edit'])->name('admin.route-edit')->middleware('check.permission:route-edit');
    Route::post('/route/update/{id}', [RouteController::class, 'update'])->name('admin.route-update')->middleware('check.permission:route-update');
    // Consider adding delete route if needed:
    // Route::get('/route/delete/{id}', [RouteController::class, 'delete'])->name('admin.route-delete')->middleware('check.permission:route-delete');

    // Document CRUD
    Route::get('/document/create', [DocumentController::class, 'create'])->name('admin.document-create')->middleware('check.permission:document-create');
    Route::post('/document/store', [DocumentController::class, 'store'])->name('admin.document-store')->middleware('check.permission:document-store');
    Route::get('/document/list', [DocumentController::class, 'index'])->name('admin.document-list')->middleware('check.permission:document-list');
    Route::get('/document/edit/{id}', [DocumentController::class, 'edit'])->name('admin.document-edit')->middleware('check.permission:document-edit');
    Route::post('/document/update/{id}', [DocumentController::class, 'update'])->name('admin.document-update')->middleware('check.permission:document-update');
    // Consider adding delete route if needed:
    // Route::get('/document/delete/{id}', [DocumentController::class, 'delete'])->name('admin.document-delete')->middleware('check.permission:document-delete');

    // Customer CRUD
    Route::get('/customer/create', [CustomerController::class, 'create'])->name('admin.customer-create')->middleware('check.permission:customer-create');
    Route::post('/customer/store', [CustomerController::class, 'store'])->name('admin.customer-store')->middleware('check.permission:customer-store');
    Route::get('/customer/list', [CustomerController::class, 'index'])->name('admin.customer-list')->middleware('check.permission:customer-list');
    Route::get('/customer/edit/{id}', [CustomerController::class, 'edit'])->name('admin.customer-edit')->middleware('check.permission:customer-edit');
    Route::post('/customer/update/{id}', [CustomerController::class, 'update'])->name('admin.customer-update')->middleware('check.permission:customer-update');
    Route::get('/customer/view/{id}', [CustomerController::class, 'view'])->name('admin.customer-view')->middleware('check.permission:customer-view');
    // Consider adding delete route if needed:
    // Route::get('/customer/delete/{id}', [CustomerController::class, 'delete'])->name('admin.customer-delete')->middleware('check.permission:customer-delete');

    // Employee CRUD
    Route::get('/employee/create', [EmployeeController::class, 'create'])->name('admin.employee-create')->middleware('check.permission:employee-create');
    Route::post('/employee/store', [EmployeeController::class, 'store'])->name('admin.employee-store')->middleware('check.permission:employee-store');
    Route::get('/employee/list', [EmployeeController::class, 'index'])->name('admin.employee-list')->middleware('check.permission:employee-list');
    Route::get('/employee/edit/{id}', [EmployeeController::class, 'edit'])->name('admin.employee-edit')->middleware('check.permission:employee-edit');
    Route::post('/employee/update/{id}', [EmployeeController::class, 'update'])->name('admin.employee-update')->middleware('check.permission:employee-update');
    Route::get('/employee/delete/{id}', [EmployeeController::class, 'delete'])->name('admin.employee-delete')->middleware('check.permission:employee-delete');


    //Group CRUD
    Route::get('/group/list', [GroupController::class, 'index'])->name('admin.group-list')->middleware('check.permission:group-list');
    Route::get('/group/create', [GroupController::class, 'create'])->name('admin.group-create')->middleware('check.permission:group-create');
    Route::post('/group/store', [GroupController::class, 'store'])->name('admin.group-store')->middleware('check.permission:group-store');
    Route::get('/group/view/{id}', [GroupController::class, 'show'])->name('admin.group-view')->middleware('check.permission:group-list');
    Route::get('/group/edit/{id}', [GroupController::class, 'edit'])->name('admin.group-edit')->middleware('check.permission:group-edit');
    Route::post('/group/update/{id}', [GroupController::class, 'update'])->name('admin.group-update')->middleware('check.permission:group-update');
    Route::get('/group/delete/{id}', [GroupController::class, 'delete'])->name('admin.group-delete')->middleware('check.permission:group-delete');
    Route::get('/group/search-customers', [GroupController::class, 'searchCustomers'])->name('admin.group-search-customers');
    Route::get('getclient-emidetails/{id}', [GroupController::class, 'emidetails'])->name('admin.getclient-emidetails');

    //emi collection CRUD
    Route::get('/emicollection/create', [EmiCollectionController::class, 'create'])->name('admin.emicollection-create')->middleware('check.permission:emicollection-create');
    Route::post('/emicollection/store', [EmiCollectionController::class, 'store'])->name('admin.emicollection-store')->middleware('check.permission:emicollection-store');
    Route::get('getcustomertype/{id}', [EmiCollectionController::class, 'getcustomertype'])
        ->name('admin.getcustomertype')->middleware('check.permission:emicollection-create'); // Or a more specific permission
    Route::get('get-client-loans/{id}', [EmiCollectionController::class, 'getLoansByClient'])
        ->name('admin.get-client-loans')->middleware('check.permission:emicollection-create');
    Route::get('get-loan-emi-details/{id}', [EmiCollectionController::class, 'getLoanEmiDetails'])
        ->name('admin.get-loan-emi-details')->middleware('check.permission:emicollection-create');
    Route::get('getcustomertype/{id}', [EmiCollectionController::class, 'getcustomertype'])
        ->name('admin.getcustomertype')->middleware('check.permission:emicollection-create'); // Or a more specific permission
    Route::get('/emicollection/list', [EmiCollectionController::class, 'index'])->name('admin.emicollection-list')->middleware('check.permission:emicollection-list');
    Route::get('/emicollection/view/{id}', [EmiCollectionController::class, 'view'])->name('admin.emicollection-view')->middleware('check.permission:emicollection-view');
    Route::delete('/emicollection/delete/{id}', [EmiCollectionController::class, 'destroy'])
        ->name('admin.emicollection-delete')
        ->middleware('check.permission:emicollection-delete');
    Route::get('/search-customers', [EmiCollectionController::class, 'emisearchcustomers'])->name('admin.search-customers')->middleware('check.permission:emicollection-create'); // Or a more specific permission
    // Consider adding edit/update/delete routes if needed:
    // Route::get('/emicollection/edit/{id}', [EmiCollectionController::class, 'edit'])->name('admin.emicollection-edit')->middleware('check.permission:emicollection-edit');
    // Route::post('/emicollection/update/{id}', [EmiCollectionController::class, 'update'])->name('admin.emicollection-update')->middleware('check.permission:emicollection-update');

    //expense crud
    Route::get('/expense/create', [ExpenseController::class, 'create'])->name('admin.expense-create')->middleware('check.permission:expense-create');
    Route::post('/expense/store', [ExpenseController::class, 'store'])->name('admin.expense-store')->middleware('check.permission:expense-store');
    Route::get('/expense/list', [ExpenseController::class, 'index'])->name('admin.expense-list')->middleware('check.permission:expense-list');
    Route::get('/expense/edit/{id}', [ExpenseController::class, 'edit'])->name('admin.expense-edit')->middleware('check.permission:expense-edit');
    Route::post('/expense/update/{id}', [ExpenseController::class, 'update'])->name('admin.expense-update')->middleware('check.permission:expense-update');
    Route::delete('/expense/delete/{id}', [ExpenseController::class, 'destroy'])->name('admin.expense-delete')->middleware('check.permission:expense-delete');



    Route::get('report-daily', [DailyReportController::class, 'daily_rep'])->name('admin.report-daily')->middleware('check.permission:report-daily');
    // For form submission (POST)
    Route::post('report-daily', [DailyReportController::class, 'dailyFilter'])->name('admin.report-daily.filter')->middleware('check.permission:report-daily');
    Route::get('report-daily/routes/{branchId}', [DailyReportController::class, 'getRoutesByBranch'])->name('admin.report-daily.routes-by-branch')->middleware('check.permission:report-daily');
    Route::get('report-loan', [DailyReportController::class, 'loan_collection'])->name('admin.report-loan')->middleware('check.permission:report-loan');
    Route::get('report-loan-filter', [DailyReportController::class, 'loanFilter'])->name('admin.report-loan.filter')->middleware('check.permission:report-loan');

    Route::get('report-total', [DailyReportController::class, 'total'])->name('admin.report-total')->middleware('check.permission:report-total');
    // For form submission (POST)
    Route::post('report-total', [DailyReportController::class, 'filter'])->name('admin.report-total.filter')->middleware('check.permission:report-total');

    // Collection Summary (Saved from Daily Report)
    Route::post('collection-summary/save', [CollectionSummaryController::class, 'saveFromDailyReport'])
        ->name('admin.collection-summary.save')
        ->middleware('check.permission:report-daily');
    // Logout
    Route::get('/logout', [LoginController::class, 'logout'])->name('admin.logout');
});
