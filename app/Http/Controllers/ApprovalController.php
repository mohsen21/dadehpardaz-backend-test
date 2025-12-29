<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveExpenseRequestRequest;
use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Resources\ExpenseRequestCollection;
use App\Models\ExpenseRequest;
use App\Services\ExpenseRequestService;
use App\Services\FileService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApprovalController extends Controller
{
    public function __construct(
        private ExpenseRequestService $expenseRequestService,
        private PaymentService $paymentService,
        private FileService $fileService
    ) {
    }

    public function index(): JsonResponse
    {
        $expenseRequests = $this->expenseRequestService->getPendingRequests();

        return response()->json(new ExpenseRequestCollection($expenseRequests));
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

        return response()->json([
            'message' => 'Action completed',
            'results' => $results,
        ]);
    }

    public function downloadAttachment(int $id): StreamedResponse|JsonResponse
    {
        $expenseRequest = ExpenseRequest::findOrFail($id);

        if (!$expenseRequest->attachment_path) {
            return response()->json(['message' => 'No attachment found'], Response::HTTP_NOT_FOUND);
        }

        $url = $this->fileService->getPresignedUrl($expenseRequest->attachment_path);

        return response()->json([
            'download_url' => $url,
        ]);
    }

    public function processPayment(ProcessPaymentRequest $request): JsonResponse
    {
        $results = $this->paymentService->processPayments($request->validated()['expense_request_ids']);

        return response()->json([
            'message' => 'Payment processing completed',
            'results' => $results,
        ]);
    }

    public function approvedRequests(): JsonResponse
    {
        $expenseRequests = $this->expenseRequestService->getApprovedRequests();

        return response()->json(new ExpenseRequestCollection($expenseRequests));
    }
}
