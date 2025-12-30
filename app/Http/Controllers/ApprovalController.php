<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveExpenseRequestRequest;
use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Resources\ExpenseRequestCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Repositories\ExpenseRequestRepositoryInterface;
use App\Services\ExpenseRequestService;
use App\Services\FileService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApprovalController extends Controller
{
    use ApiResponseTrait;
    public function __construct(
        private ExpenseRequestService $expenseRequestService,
        private PaymentService $paymentService,
        private FileService $fileService,
        private ExpenseRequestRepositoryInterface $expenseRequestRepository
    ) {
    }

    public function index(): JsonResponse
    {
        $expenseRequests = $this->expenseRequestService->getPendingRequests();

        return $this->collectionResponse(new ExpenseRequestCollection($expenseRequests));
    }

    public function approve(ApproveExpenseRequestRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($validated['action'] === 'approve') {
            $results = $this->expenseRequestService->approve($validated['expense_request_ids']);
        } else {
            $results = $this->expenseRequestService->reject(
                $validated['expense_request_ids'],
                $validated['rejection_reason']
            );
        }

        return $this->successResponse($results, 'Action completed');
    }

    public function downloadAttachment(int $id): StreamedResponse|JsonResponse
    {
        $expenseRequest = $this->expenseRequestRepository->findByIdOrFail($id);

        if (!$expenseRequest->attachment_path) {
            return $this->notFoundResponse('No attachment found');
        }

        $url = $this->fileService->getPresignedUrl($expenseRequest->attachment_path);

        return $this->successResponse(['download_url' => $url]);
    }

    public function processPayment(ProcessPaymentRequest $request): JsonResponse
    {
        $results = $this->paymentService->processPayments($request->validated()['expense_request_ids']);

        return $this->successResponse($results, 'Payment processing completed');
    }

    public function approvedRequests(): JsonResponse
    {
        $expenseRequests = $this->expenseRequestService->getApprovedRequests();

        return $this->collectionResponse(new ExpenseRequestCollection($expenseRequests));
    }
}
