<?php

namespace App\Repositories;

use App\Models\ExpenseRequest;
use Illuminate\Database\Eloquent\Collection;

interface ExpenseRequestRepositoryInterface
{
    public function create(array $data): ExpenseRequest;

    public function findById(int $id): ?ExpenseRequest;

    public function findByIdOrFail(int $id): ExpenseRequest;

    public function findPendingByIds(array $ids): Collection;

    public function findApprovedByIds(array $ids): Collection;

    public function getPendingWithRelations(): Collection;

    public function getApprovedWithRelations(): Collection;

    public function update(ExpenseRequest $expenseRequest, array $data): bool;
}

