<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'national_code' => $this->user->national_code,
            ],
            'expense_category' => [
                'id' => $this->expenseCategory->id,
                'name' => $this->expenseCategory->name,
            ],
            'description' => $this->description,
            'amount' => (float) $this->amount,
            'sheba_number' => $this->sheba_number,
            'attachment_path' => $this->attachment_path,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
