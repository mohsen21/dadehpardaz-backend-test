<?php

namespace App\Services;

use App\Models\ExpenseRequest;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function notifyRequestRejected(ExpenseRequest $expenseRequest): void
    {
        $user = $expenseRequest->user;

        // TODO: Send SMS via API
       

        Log::info('Notification sent for rejected expense request', [
            'expense_request_id' => $expenseRequest->id,
            'user_id' => $user->id,
        ]);
    }

    public function notifyPaymentSuccessful(ExpenseRequest $expenseRequest): void
    {
        $user = $expenseRequest->user;

        // TODO: Send SMS via API
        
        Log::info('Notification sent for successful payment', [
            'expense_request_id' => $expenseRequest->id,
            'user_id' => $user->id,
            'amount' => $expenseRequest->amount,
        ]);
    }
}

