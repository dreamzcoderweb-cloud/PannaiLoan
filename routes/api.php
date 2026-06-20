<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\EmiCollectionController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\InterestController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\LoanAssignController;
use App\Http\Controllers\Api\DailyReportApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API Routes (No Authentication Required)
Route::post('/login', [AuthController::class, 'login']);

// Protected API Routes (Requires Sanctum Authentication)
Route::middleware('auth:sanctum')->prefix('employee')->as('api.employee.')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        })->name('user');

        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Master Routes

        // Customer Routes
        Route::get('customers', [CustomerController::class, 'index']);
        Route::post('customers-store', [CustomerController::class, 'store']);
        Route::get('customers/{id}', [CustomerController::class, 'show']);
        Route::put('customers/{id}', [CustomerController::class, 'update']);
        Route::patch('customers/{id}', [CustomerController::class, 'update']);
        // Query param support
        Route::put('customers', [CustomerController::class, 'update']);
        Route::patch('customers', [CustomerController::class, 'update']);
        Route::delete('customers', [CustomerController::class, 'destroy']);
        //Route::get('/customer-masters', [CustomerController::class, 'getMasters']);
        Route::delete('customers/{id}', [CustomerController::class, 'destroy']);
        // Branch Routes
        Route::get('branches', [BranchController::class, 'index']);
        Route::post('branches-store', [BranchController::class, 'store']);
        Route::get('branches/{id}', [BranchController::class, 'show']);
        Route::put('branches/{id}', [BranchController::class, 'update']);
        Route::patch('branches/{id}', [BranchController::class, 'update']);
        // Query param support
        Route::put('branches', [BranchController::class, 'update']);
        Route::patch('branches', [BranchController::class, 'update']);
        Route::delete('branches', [BranchController::class, 'destroy']);
        // Route Routes
        Route::get('routes', [RouteController::class, 'index']);
        Route::post('routes-store', [RouteController::class, 'store']);
        Route::get('routes/{id}', [RouteController::class, 'show']);
        Route::put('routes/{id}', [RouteController::class, 'update']);
        Route::patch('routes/{id}', [RouteController::class, 'update']);
        // Query param support
        Route::put('routes', [RouteController::class, 'update']);
        Route::patch('routes', [RouteController::class, 'update']);
        Route::delete('routes', [RouteController::class, 'destroy']);

        // Loan Routes
        Route::get('loans', [LoanController::class, 'index']);
        Route::post('loans-store', [LoanController::class, 'store']);
        Route::get('loans/{id}', [LoanController::class, 'show']);
        Route::put('loans/{id}', [LoanController::class, 'update']);
        Route::patch('loans/{id}', [LoanController::class, 'update']);
        // Query param support
        Route::put('loans', [LoanController::class, 'update']);
        Route::patch('loans', [LoanController::class, 'update']);

        // Interest Routes
        Route::get('interests', [InterestController::class, 'index']);
        // Route::post('interests-store', [InterestController::class, 'store']);
        // Route::get('interests/{id}', [InterestController::class, 'show']);
        // Route::put('interests/{id}', [InterestController::class, 'update']);
        // Route::patch('interests/{id}', [InterestController::class, 'update']);
        // // Query param support
        // Route::put('interests', [InterestController::class, 'update']);
        // Route::patch('interests', [InterestController::class, 'update']);

        // Document Routes
        Route::get('documents', [DocumentController::class, 'index']);
        // Route::post('documents-store', [DocumentController::class, 'store']);
        // Route::get('documents/{id}', [DocumentController::class, 'show']);
        // Route::put('documents/{id}', [DocumentController::class, 'update']);
        // Route::patch('documents/{id}', [DocumentController::class, 'update']);
        // // Query param support
        // Route::put('documents', [DocumentController::class, 'update']);
        // Route::patch('documents', [DocumentController::class, 'update']);

        // Loan Assign Routes
        Route::get('loan-assigns', [LoanAssignController::class, 'index']);
        Route::post('loan-assigns-store', [LoanAssignController::class, 'store']);
        Route::get('loan-assigns/client-details/{id?}', [LoanAssignController::class, 'clientdetails']);
        Route::get('loan-assigns/{id}', [LoanAssignController::class, 'show']);
        Route::put('loan-assigns/{id}', [LoanAssignController::class, 'update']);
        Route::patch('loan-assigns/{id}', [LoanAssignController::class, 'update']);
        Route::delete('loan-assigns/{id}', [LoanAssignController::class, 'destroy']);
        // Query param support
        Route::put('loan-assigns', [LoanAssignController::class, 'update']);
        Route::patch('loan-assigns', [LoanAssignController::class, 'update']);
        Route::delete('loan-assigns', [LoanAssignController::class, 'destroy']);

        Route::get('/loan-assign-masters', [LoanAssignController::class, 'getMasters']);
        Route::post('/loan-assigns/foreclose', [LoanAssignController::class, 'foreclose']);


        //  Employee Mobile API (Existing)
        Route::get('/loan-assign-list', [EmiCollectionController::class, 'loanAssignList'])->name('loan-assign-list');
        Route::get('/emi-search-customers', [EmiCollectionController::class, 'emiSearchCustomers'])->name('emi-search-customers');
        Route::get('/loans-by-client/{clientId?}', [EmiCollectionController::class, 'getLoansByClient'])
            ->name('loans-by-client');
        Route::get('/loan-emi-details/{loanassignId?}', [EmiCollectionController::class, 'getLoanEmiDetails'])->name('loan-emi-details');
        Route::post('/collect-emi', [EmiCollectionController::class, 'collectEmi'])->name('collection-emi');
        Route::get('/emi-collection-history/{loanassignId?}', [EmiCollectionController::class, 'emiCollectionHistory'])->name('emi-collection-history');
        Route::get('/emi-collection-history/{loanassignId?}/download-pdf', [EmiCollectionController::class, 'emiCollectionHistoryPdf'])->name('emi-collection-history-download-pdf');

        // Daily Report Routes
        Route::get('/daily-report', [DailyReportApiController::class, 'dailyReport'])->name('daily-report');
        Route::get('/daily-report/download-pdf', [DailyReportApiController::class, 'downloadDailyReportPdf'])->name('daily-report-download-pdf');
        Route::get('/total-report', [DailyReportApiController::class, 'totalReport'])->name('total-report');

});
