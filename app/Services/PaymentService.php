<?php

namespace App\Services;

use App\Exceptions\BankException;
use App\Exceptions\PaymentException;
use App\Models\ExpenseRequest;
use App\Services\BankStrategy\BankFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    public function processPayment(ExpenseRequest $expenseRequest): void
    {
        if ($expenseRequest->status !== 'approved') {
            throw new PaymentException('Expense request must be approved before payment');
        }

        if ($expenseRequest->status === 'paid') {
            throw new PaymentException('Expense request already paid');
        }

        DB::beginTransaction();

        try {
            $lock = DB::table('expense_requests')
                ->where('id', $expenseRequest->id)
                ->lockForUpdate()
                ->first();

            if (!$lock || $lock->status !== 'approved') {
                throw new PaymentException('Expense request status changed');
            }

            $bank = BankFactory::create($expenseRequest->sheba_number);
            $result = $bank->processPayment($expenseRequest->sheba_number, (float) $expenseRequest->amount);

            if (!$result['success']) {
                throw new BankException('Bank payment failed: ' . ($result['message'] ?? 'Unknown error'));
            }

            $expenseRequest->update(['status' => 'paid']);

            Log::info('Payment processed successfully', [
                'expense_request_id' => $expenseRequest->id,
                'transaction_id' => $result['transaction_id'] ?? null,
                'amount' => $expenseRequest->amount,
            ]);

            DB::commit();

            $this->notificationService->notifyPaymentSuccessful($expenseRequest);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Payment processing failed', [
                'expense_request_id' => $expenseRequest->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function processPayments(array $expenseRequestIds): array
    {
        $results = [];
        $expenseRequests = ExpenseRequest::whereIn('id', $expenseRequestIds)
            ->where('status', 'approved')
            ->get();

        foreach ($expenseRequests as $expenseRequest) {
            try {
                $this->processPayment($expenseRequest);
                $results[$expenseRequest->id] = ['success' => true];
            } catch (\Exception $e) {
                $results[$expenseRequest->id] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}

