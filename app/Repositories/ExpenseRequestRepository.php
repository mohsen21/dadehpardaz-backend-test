<?php

namespace App\Repositories;

use App\Models\ExpenseRequest;
use Illuminate\Database\Eloquent\Collection;

class ExpenseRequestRepository implements ExpenseRequestRepositoryInterface
{
    public function create(array $data): ExpenseRequest
    {
        return ExpenseRequest::create($data);
    }

    public function findById(int $id): ?ExpenseRequest
    {
        return ExpenseRequest::find($id);
    }

    public function findByIdOrFail(int $id): ExpenseRequest
    {
        return ExpenseRequest::findOrFail($id);
    }

    public function findPendingByIds(array $ids): Collection
    {
        return ExpenseRequest::whereIn('id', $ids)
            ->where('status', 'pending')
            ->get();
    }

    public function findApprovedByIds(array $ids): Collection
    {
        return ExpenseRequest::whereIn('id', $ids)
            ->where('status', 'approved')
            ->get();
    }

    public function getPendingWithRelations(): Collection
    {
        return ExpenseRequest::with(['user', 'expenseCategory'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getApprovedWithRelations(): Collection
    {
        return ExpenseRequest::with(['user', 'expenseCategory'])
            ->approved()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function update(ExpenseRequest $expenseRequest, array $data): bool
    {
        return $expenseRequest->update($data);
    }
}

