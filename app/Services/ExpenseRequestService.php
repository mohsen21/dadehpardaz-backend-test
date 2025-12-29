<?php

namespace App\Services;

use App\Models\ExpenseRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseRequestService
{
    public function __construct(
        private FileService $fileService,
        private NotificationService $notificationService
    ) {
    }

    public function create(array $data, ?UploadedFile $attachment = null): ExpenseRequest
    {
        $user = User::where('national_code', $data['national_code'])->firstOrFail();

        DB::beginTransaction();

        try {
            $attachmentPath = null;
            if ($attachment) {
                $attachmentPath = $this->fileService->upload($attachment);
            }

            $expenseRequest = ExpenseRequest::create([
                'user_id' => $user->id,
                'expense_category_id' => $data['expense_category_id'],
                'description' => $data['description'],
                'amount' => $data['amount'],
                'sheba_number' => $data['sheba_number'],
                'attachment_path' => $attachmentPath,
                'status' => 'pending',
            ]);

            DB::commit();

            Log::info('Expense request created', [
                'expense_request_id' => $expenseRequest->id,
                'user_id' => $user->id,
            ]);

            return $expenseRequest;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($attachmentPath) {
                $this->fileService->delete($attachmentPath);
            }

            throw $e;
        }
    }

    public function approve(array $expenseRequestIds): array
    {
        $results = [];
        $expenseRequests = ExpenseRequest::whereIn('id', $expenseRequestIds)
            ->where('status', 'pending')
            ->get();

        foreach ($expenseRequests as $expenseRequest) {
            try {
                $expenseRequest->update(['status' => 'approved']);
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

    public function reject(array $expenseRequestIds, string $rejectionReason): array
    {
        $results = [];
        $expenseRequests = ExpenseRequest::whereIn('id', $expenseRequestIds)
            ->where('status', 'pending')
            ->get();

        foreach ($expenseRequests as $expenseRequest) {
            try {
                $expenseRequest->update([
                    'status' => 'rejected',
                    'rejection_reason' => $rejectionReason,
                ]);

                $this->notificationService->notifyRequestRejected($expenseRequest);

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

    public function getPendingRequests(): \Illuminate\Database\Eloquent\Collection
    {
        return ExpenseRequest::with(['user', 'expenseCategory'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getApprovedRequests(): \Illuminate\Database\Eloquent\Collection
    {
        return ExpenseRequest::with(['user', 'expenseCategory'])
            ->approved()
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

