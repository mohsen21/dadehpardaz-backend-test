<?php

namespace App\Services\BankStrategy;

class Bank1Service implements BankInterface
{
    public function processPayment(string $shebaNumber, float $amount): array
    {
        // TODO: Call Bank 1 API here
      
        return [
            'success' => true,
            'transaction_id' => uniqid('bank1_'),
            'message' => 'Payment processed successfully',
        ];
    }
}

