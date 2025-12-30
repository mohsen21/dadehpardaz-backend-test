<?php

namespace Tests\Unit\Services;

use App\Services\BankStrategy\Bank1Service;
use App\Services\BankStrategy\Bank2Service;
use App\Services\BankStrategy\Bank3Service;
use App\Services\BankStrategy\BankFactory;
use InvalidArgumentException;
use Tests\TestCase;

class BankFactoryTest extends TestCase
{
    private BankFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new BankFactory();
    }

    public function test_creates_correct_bank_service_for_different_prefixes(): void
    {
        $bank1 = $this->factory->create('110000000000000000000001');
        $this->assertInstanceOf(Bank1Service::class, $bank1);

        $bank2 = $this->factory->create('220000000000000000000001');
        $this->assertInstanceOf(Bank2Service::class, $bank2);

        $bank3 = $this->factory->create('330000000000000000000001');
        $this->assertInstanceOf(Bank3Service::class, $bank3);
    }

    public function test_throws_exception_for_invalid_prefix(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->create('440000000000000000000001');
    }

    public function test_throws_exception_for_short_sheba_number(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->create('1');
    }
}
