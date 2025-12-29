<?php

namespace App\Services\BankStrategy;

interface BankInterface
{
    public function processPayment(string $shebaNumber, float $amount): array;
}

