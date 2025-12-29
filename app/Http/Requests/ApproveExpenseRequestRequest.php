<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApproveExpenseRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_request_ids' => ['required', 'array', 'min:1'],
            'expense_request_ids.*' => ['required', 'exists:expense_requests,id'],
            'action' => ['required', Rule::in(['approve', 'reject'])],
            'rejection_reason' => ['required_if:action,reject', 'string', 'max:500'],
        ];
    }
}
