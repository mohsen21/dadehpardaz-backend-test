<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidShebaNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || strlen($value) < 2) {
            $fail('The :attribute must be a valid sheba number.');
            return;
        }

        $prefix = substr($value, 0, 2);
        $validPrefixes = ['11', '22', '33'];

        if (!in_array($prefix, $validPrefixes, true)) {
            $fail('The :attribute must start with 11, 22, or 33.');
        }
    }
}
