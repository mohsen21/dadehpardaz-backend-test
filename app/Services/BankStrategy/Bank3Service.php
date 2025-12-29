<?php

namespace App\Services\BankStrategy;

class Bank3Service implements BankInterface
{
    public function processPayment(string $shebaNumber, float $amount): array
    {
        // TODO: Call Bank 3 API here


        return [
            'success' => true,
            'transaction_id' => uniqid('bank3_'),
            'message' => 'Payment processed successfully',
        ];
    }
}

