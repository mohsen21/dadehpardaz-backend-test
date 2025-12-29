<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequestRequest;
use App\Http\Resources\ExpenseRequestCollection;
use App\Http\Resources\ExpenseRequestResource;
use App\Models\ExpenseRequest;
use App\Services\ExpenseRequestService;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExpenseRequestController extends Controller
{
    public function __construct(
        private ExpenseRequestService $expenseRequestService
    ) {
    }

    public function store(StoreExpenseRequestRequest $request): JsonResponse
    {
        $expenseRequest = $this->expenseRequestService->create(
            $request->validated(),
            $request->file('attachment')
        );

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
        $expenseRequest = ExpenseRequest::with(['user', 'expenseCategory'])
            ->findOrFail($id);

        return response()->json(new ExpenseRequestResource($expenseRequest));
    }
}
