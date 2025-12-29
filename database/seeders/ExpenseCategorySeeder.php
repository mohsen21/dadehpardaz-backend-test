<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'حمل و نقل',
                'description' => 'هزینه‌های مربوط به حمل و نقل',
            ],
            [
                'name' => 'ایاب و ذهاب',
                'description' => 'هزینه‌های ایاب و ذهاب',
            ],
            [
                'name' => 'خرید تجهیزات',
                'description' => 'خرید تجهیزات و لوازم مورد نیاز',
            ],
            [
                'name' => 'غذا و پذیرایی',
                'description' => 'هزینه‌های غذا و پذیرایی',
            ],
            [
                'name' => 'سایر',
                'description' => 'سایر هزینه‌ها',
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create($category);
        }
    }
}
