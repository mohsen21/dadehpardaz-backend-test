<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByNationalCode(string $nationalCode): ?User;

    public function findByNationalCodeOrFail(string $nationalCode): User;
}

