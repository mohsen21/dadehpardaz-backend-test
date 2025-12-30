<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\ExpenseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseRequestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_expense_request(): void
    {
        $user = User::factory()->create(['national_code' => '1234567890']);
        $category = ExpenseCategory::factory()->create();

        $data = [
            'national_code' => '1234567890',
            'expense_category_id' => $category->id,
            'description' => 'Test expense',
            'amount' => 5000,
            'sheba_number' => '110000000000000000000001',
        ];

        $response = $this->postJson('/api/expense-requests', $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'user', 'expense_category', 'status'],
            ]);

        $this->assertDatabaseHas('expense_requests', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_can_list_pending_expense_requests(): void
    {
        $user = User::factory()->create();
        $category = ExpenseCategory::factory()->create();
        
        ExpenseRequest::factory()->pending()->count(2)->create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/expense-requests');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(2, 'data.data');
    }

    public function test_can_approve_expense_requests(): void
    {
        $user = User::factory()->create();
        $category = ExpenseCategory::factory()->create();
        
        $expenseRequest = ExpenseRequest::factory()->pending()->create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
        ]);

        $data = [
            'action' => 'approve',
            'expense_request_ids' => [$expenseRequest->id],
        ];

        $response = $this->postJson('/api/approvals/action', $data);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals('approved', $expenseRequest->fresh()->status);
    }
}

