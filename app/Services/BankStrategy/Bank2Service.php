<?php

namespace App\Services\BankStrategy;

class Bank2Service implements BankInterface
{
    public function processPayment(string $shebaNumber, float $amount): array
    {
        // TODO: Call Bank 2 API here


        return [
            'success' => true,
            'transaction_id' => uniqid('bank2_'),
            'message' => 'Payment processed successfully',
        ];
    }
}

