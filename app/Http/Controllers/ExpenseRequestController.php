<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequestRequest;
use App\Http\Resources\ExpenseRequestCollection;
use App\Http\Resources\ExpenseRequestResource;
use App\Repositories\ExpenseRequestRepositoryInterface;
use App\Services\ExpenseRequestService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExpenseRequestController extends Controller
{
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

        return response()->json([
            'message' => 'Expense request created successfully',
            'data' => new ExpenseRequestResource($expenseRequest),
        ], Response::HTTP_CREATED);
    }

    public function index(): JsonResponse
    {
        $expenseRequests = $this->expenseRequestService->getPendingRequests();

        return response()->json(new ExpenseRequestCollection($expenseRequests));
    }

    public function show(int $id): JsonResponse
    {
        $expenseRequest = $this->expenseRequestRepository->findByIdOrFail($id);
        // Eager loading to prevent N+1 query problem
        $expenseRequest->load(['user', 'expenseCategory']);

        return response()->json(new ExpenseRequestResource($expenseRequest));
    }
}
