<?php

namespace App\Services;

use App\Models\ExpenseRequest;
use App\Repositories\ExpenseRequestRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseRequestService
{
    public function __construct(
        private FileService $fileService,
        private NotificationService $notificationService,
        private ExpenseRequestRepositoryInterface $expenseRequestRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function create(array $data, ?UploadedFile $attachment = null): ExpenseRequest
    {
        $user = $this->userRepository->findByNationalCodeOrFail($data['national_code']);

        DB::beginTransaction();

        try {
            $attachmentPath = null;
            if ($attachment) {
                $attachmentPath = $this->fileService->upload($attachment);
            }

            $expenseRequest = $this->expenseRequestRepository->create([
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
        $expenseRequests = $this->expenseRequestRepository->findPendingByIds($expenseRequestIds);

        foreach ($expenseRequests as $expenseRequest) {
            DB::beginTransaction();
            try {
                $this->expenseRequestRepository->update($expenseRequest, ['status' => 'approved']);

                // Eager load user to prevent N+1 query issue
                $expenseRequest->load('user');
                $this->notificationService->notifyRequestApproved($expenseRequest, $expenseRequest->user);

                DB::commit();
                $results[$expenseRequest->id] = ['success' => true];
            } catch (\Exception $e) {
                DB::rollBack();
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
        $expenseRequests = $this->expenseRequestRepository->findPendingByIds($expenseRequestIds);

        foreach ($expenseRequests as $expenseRequest) {
            DB::beginTransaction();
            try {
                $this->expenseRequestRepository->update($expenseRequest, [
                    'status' => 'rejected',
                    'rejection_reason' => $rejectionReason,
                ]);

                // Eager load user to prevent N+1 query issue
                $expenseRequest->load('user');
                $this->notificationService->notifyRequestRejected($expenseRequest, $expenseRequest->user);

                DB::commit();
                $results[$expenseRequest->id] = ['success' => true];
            } catch (\Exception $e) {
                DB::rollBack();
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
        return $this->expenseRequestRepository->getPendingWithRelations();
    }

    public function getApprovedRequests(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->expenseRequestRepository->getApprovedWithRelations();
    }
}

