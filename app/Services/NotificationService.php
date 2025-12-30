<?php

namespace App\Services;

use App\Models\ExpenseRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function notifyRequestRejected(ExpenseRequest $expenseRequest, ?User $user = null): void
    {
        // Accept user as parameter to prevent N+1 query issue
        $user = $user ?? $expenseRequest->user;

        // TODO: Send SMS via API
       

        Log::info('Notification sent for rejected expense request', [
            'expense_request_id' => $expenseRequest->id,
            'user_id' => $user->id,
        ]);
    }

    public function notifyRequestApproved(ExpenseRequest $expenseRequest, ?User $user = null): void
    {
        // Accept user as parameter to prevent N+1 query issue
        $user = $user ?? $expenseRequest->user;

        // TODO: Send SMS via API
       
        Log::info('Notification sent for approved expense request', [
            'expense_request_id' => $expenseRequest->id,
            'user_id' => $user->id,
            'amount' => $expenseRequest->amount,
        ]);
    }

    public function notifyPaymentSuccessful(ExpenseRequest $expenseRequest, ?User $user = null): void
    {
        // Accept user as parameter to prevent N+1 query issue
        $user = $user ?? $expenseRequest->user;

        // TODO: Send SMS via API
        
        Log::info('Notification sent for successful payment', [
            'expense_request_id' => $expenseRequest->id,
            'user_id' => $user->id,
            'amount' => $expenseRequest->amount,
        ]);
    }
}

