<?php

namespace App\Jobs;

use App\Repositories\ExpenseRequestRepositoryInterface;
use App\Services\PaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessPaymentJob implements ShouldQueue
{
    use Queueable;

    public function handle(
        PaymentService $paymentService,
        ExpenseRequestRepositoryInterface $expenseRequestRepository
    ): void {
        $expenseRequests = $expenseRequestRepository->getApprovedWithRelations()
            ->filter(fn($request) => $request->status !== 'paid');

        if ($expenseRequests->isEmpty()) {
            Log::info('No approved expense requests to process');
            return;
        }

        $expenseRequestIds = $expenseRequests->pluck('id')->toArray();
        $results = $paymentService->processPayments($expenseRequestIds);

        Log::info('Automatic payment processing completed', [
            'processed_count' => count($results),
            'results' => $results,
        ]);
    }
}
