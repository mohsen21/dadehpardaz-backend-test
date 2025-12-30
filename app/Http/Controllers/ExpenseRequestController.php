<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequestRequest;
use App\Http\Resources\ExpenseRequestCollection;
use App\Http\Resources\ExpenseRequestResource;
use App\Http\Traits\ApiResponseTrait;
use App\Repositories\ExpenseRequestRepositoryInterface;
use App\Services\ExpenseRequestService;
use Illuminate\Http\JsonResponse;

class ExpenseRequestController extends Controller
{
    use ApiResponseTrait;
    public function __construct(
        private ExpenseRequestService $expenseRequestService,
        private ExpenseRequestRepositoryInterface $expenseRequestRepository
    ) {
    }

    public function store(StoreExpenseRequestRequest $request): JsonResponse
    {
        $expenseRequest = $this->expenseRequestService->create(
            $request->validated(),
            $request->file('attachment')
        );

        // Eager loading to prevent N+1 query problem
        $expenseRequest->load(['user', 'expenseCategory']);

        return $this->createdResponse(
            new ExpenseRequestResource($expenseRequest),
            'Expense request created successfully'
        );
    }

    public function index(): JsonResponse
    {
        $expenseRequests = $this->expenseRequestService->getPendingRequests();

        return $this->collectionResponse(new ExpenseRequestCollection($expenseRequests));
    }

    public function show(int $id): JsonResponse
    {
        $expenseRequest = $this->expenseRequestRepository->findByIdOrFail($id);
        // Eager loading to prevent N+1 query problem
        $expenseRequest->load(['user', 'expenseCategory']);

        return $this->resourceResponse(new ExpenseRequestResource($expenseRequest));
    }
}
