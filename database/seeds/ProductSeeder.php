<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Entities\Category;
use Modules\Tags\Entities\Tag;
use Faker\Factory;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {

            $faker = Factory::create();
            $count = Product::count();

            if ($count == 0) {
                for ($i = 1; $i <= 20; $i++) {

                    $title = [
                        'ar' => $faker->sentence(4) . ' - Ar',
                        'en' => $faker->sentence(4) . ' - En',
                    ];
                    $short_description = [
                        'ar' => $faker->paragraph(50) . ' - Ar',
                        'en' => $faker->paragraph(50) . ' - En',
                    ];
                    $description = [
                        'ar' => $faker->paragraph(100) . ' - Ar',
                        'en' => $faker->paragraph(100) . ' - En',
                    ];

                    $defaultVendorId = config('setting.default_vendor') ?? 8;

                    $p = Product::create([
                        'price' => rand(20, 150),
                        'sku' => Str::upper(Str::random(6)),
                        'qty' => 1500,
                        'image' => path_without_domain(url('storage/photos/shares/test_products/' . $i . '.jpg')),
                        'vendor_id' => $defaultVendorId,
                        'status' => 1,
                        'featured' => rand(0, 1),
                        'pending_for_approval' => true,
                    ]);

                    foreach ($title as $locale => $value) {
                        $p->translateOrNew($locale)->title = $value;
                        $p->translateOrNew($locale)->short_description = $short_description[$locale];
                        $p->translateOrNew($locale)->description = $description[$locale];
                    }
                    $p->save();

                    $p->categories()->attach(Category::inRandomOrder()->mainCategories()->first()->id);
                    $p->tags()->attach(Tag::inRandomOrder()->take(2)->pluck('id')->toArray());

                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

}
