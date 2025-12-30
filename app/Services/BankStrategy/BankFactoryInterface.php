<?php

namespace App\Services\BankStrategy;

interface BankFactoryInterface
{
    public function create(string $shebaNumber): BankInterface;
}

