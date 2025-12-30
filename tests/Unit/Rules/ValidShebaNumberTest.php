<?php

namespace Tests\Unit\Rules;

use App\Rules\ValidShebaNumber;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidShebaNumberTest extends TestCase
{
    public function test_validates_valid_sheba_numbers(): void
    {
        $rule = new ValidShebaNumber();

        $validator1 = Validator::make(['sheba' => '110000000000000000000001'], ['sheba' => [$rule]]);
        $this->assertTrue($validator1->passes());

        $validator2 = Validator::make(['sheba' => '220000000000000000000001'], ['sheba' => [$rule]]);
        $this->assertTrue($validator2->passes());

        $validator3 = Validator::make(['sheba' => '330000000000000000000001'], ['sheba' => [$rule]]);
        $this->assertTrue($validator3->passes());
    }

    public function test_fails_for_invalid_prefix(): void
    {
        $rule = new ValidShebaNumber();
        $validator = Validator::make(['sheba' => '440000000000000000000001'], ['sheba' => [$rule]]);
        
        $this->assertFalse($validator->passes());
    }

    public function test_fails_for_short_number(): void
    {
        $rule = new ValidShebaNumber();
        $validator = Validator::make(['sheba' => '1'], ['sheba' => [$rule]]);
        
        $this->assertFalse($validator->passes());
    }
}

