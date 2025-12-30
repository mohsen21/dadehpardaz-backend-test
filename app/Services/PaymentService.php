<?php

namespace App\Services;

use App\Exceptions\BankException;
use App\Exceptions\PaymentException;
use App\Models\ExpenseRequest;
use App\Repositories\ExpenseRequestRepositoryInterface;
use App\Services\BankStrategy\BankFactoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        private NotificationService $notificationService,
        private ExpenseRequestRepositoryInterface $expenseRequestRepository,
        private BankFactoryInterface $bankFactory
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

            $bank = $this->bankFactory->create($expenseRequest->sheba_number);
            $result = $bank->processPayment($expenseRequest->sheba_number, (float) $expenseRequest->amount);

            if (!$result['success']) {
                throw new BankException('Bank payment failed: ' . ($result['message'] ?? 'Unknown error'));
            }

            $this->expenseRequestRepository->update($expenseRequest, ['status' => 'paid']);

            Log::info('Payment processed successfully', [
                'expense_request_id' => $expenseRequest->id,
                'transaction_id' => $result['transaction_id'] ?? null,
                'amount' => $expenseRequest->amount,
            ]);

            DB::commit();

            // Eager load user to prevent N+1 query issue
            $expenseRequest->load('user');
            $this->notificationService->notifyPaymentSuccessful($expenseRequest, $expenseRequest->user);
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
        $expenseRequests = $this->expenseRequestRepository->findApprovedByIds($expenseRequestIds);

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

