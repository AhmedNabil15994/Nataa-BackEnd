<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DashboardSeeder::class,
            ProductCategorySeeder::class,
            /*CategorySeeder::class,
            ProductSeeder::class,*/
            PaymentStatusSeeder::class,
            OrderStatusSeeder::class,
            TagsSeeder::class,
            SliderSeeder::class,
        ]);
    }
}
