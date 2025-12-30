<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ExpenseRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('expense-requests')->group(function () {
    Route::post('/', [ExpenseRequestController::class, 'store']);
    Route::get('/', [ExpenseRequestController::class, 'index']);
    Route::get('/{id}', [ExpenseRequestController::class, 'show']);
});

Route::prefix('approvals')->group(function () {
    Route::get('/', [ApprovalController::class, 'index']);
    Route::post('/action', [ApprovalController::class, 'approve']);
    Route::get('/approved', [ApprovalController::class, 'approvedRequests']);
    Route::post('/process-payment', [ApprovalController::class, 'processPayment']);
    Route::get('/{id}/download', [ApprovalController::class, 'downloadAttachment']);
});

