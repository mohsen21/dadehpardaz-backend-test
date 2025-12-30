<?php

namespace App\Services\BankStrategy;

use InvalidArgumentException;

class BankFactory implements BankFactoryInterface
{
    public function create(string $shebaNumber): BankInterface
    {
        if (strlen($shebaNumber) < 2) {
            throw new InvalidArgumentException('Invalid sheba number');
        }

        $prefix = substr($shebaNumber, 0, 2);

        return match ($prefix) {
            '11' => new Bank1Service(),
            '22' => new Bank2Service(),
            '33' => new Bank3Service(),
            default => throw new InvalidArgumentException("Bank not found for sheba prefix: {$prefix}"),
        };
    }
}

