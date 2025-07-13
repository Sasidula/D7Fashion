<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\EmployeeBonusAdjustment;
use App\Models\ExternalProductItem;
use App\Models\InternalProductItem;
use App\Models\MaterialAssignment;
use App\Models\MaterialStock;
use App\Models\MonthlyExpensesList;
use App\Models\MonthlyExpensesRecord;
use App\Models\PettyCash;
use App\Models\ProductSale;
use App\Models\ProductSalesItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            MaterialSeeder::class,
            InternalProductSeeder::class,
            ExternalProductSeeder::class,
        ]);

        User::factory()->count(10)->create();
        MaterialStock::factory(50)->create();
        MaterialAssignment::factory(30)->create();
        Attendance::factory(50)->create();
        InternalProductItem::factory(30)->create();
        ExternalProductItem::factory(30)->create();
        ProductSale::factory(20)->create();
        ProductSalesItem::factory(50)->create();
        EmployeeBonusAdjustment::factory(20)->create();
        PettyCash::factory(15)->create();
        MonthlyExpensesList::factory(5)->create();
        MonthlyExpensesRecord::factory(10)->create();
    }
}
