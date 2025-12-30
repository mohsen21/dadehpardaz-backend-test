<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function findByNationalCode(string $nationalCode): ?User
    {
        return User::where('national_code', $nationalCode)->first();
    }

    public function findByNationalCodeOrFail(string $nationalCode): User
    {
        return User::where('national_code', $nationalCode)->firstOrFail();
    }
}

