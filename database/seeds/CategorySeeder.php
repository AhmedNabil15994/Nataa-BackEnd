<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("
        INSERT INTO `categories` (`id`, `image`, `slug`, `status`, `show_in_home`, `category_id`, `sort`, `deleted_at`, `created_at`, `updated_at`) VALUES
                    (1, '/storage/photos/shares/logo/logo.png', NULL, 1, 1, NULL, 1, NULL, '2020-10-07 23:12:16', '2020-10-07 23:12:16'),
                    (2, '/storage/photos/shares/categories/1.jpg', NULL, 1, 1, NULL, 2, NULL, '2020-10-07 23:12:16', '2020-10-07 23:12:16'),
                    (3, '/storage/photos/shares/categories/5.jpg', NULL, 1, 0, NULL, 3, NULL, '2020-10-07 23:13:21', '2020-10-07 23:15:26'),
                    (4, '/storage/photos/shares/categories/2.jpg', NULL, 1, 1, NULL, 4, NULL, '2020-10-07 23:14:56', '2020-10-07 23:14:56'),
                    (5, '/storage/photos/shares/categories/4.jpg', NULL, 1, 1, NULL, 5, NULL, '2020-10-07 23:16:41', '2020-10-07 23:16:41'),
                    (6, '/storage/photos/shares/categories/3.jpg', NULL, 1, 0, NULL, 6, NULL, '2020-10-07 23:17:10', '2020-10-07 23:17:10');
        ");

        DB::statement("
        INSERT INTO `category_translations` (`id`, `slug`, `title`, `seo_keywords`, `seo_description`, `locale`, `category_id`, `created_at`, `updated_at`) VALUES
            (1, 'main-category', 'Main Category', NULL, NULL, 'en', 1, '2020-10-07 23:12:16', '2020-10-07 23:12:16'),
            (2, 'qasam-rayiysaa', 'قسم رئيسى', NULL, NULL, 'ar', 1, '2020-10-07 23:12:16', '2020-10-07 23:12:16'),
            (3, 'clothes', 'Clothes', NULL, NULL, 'en', 2, '2020-10-07 23:12:16', '2020-10-07 23:12:16'),
            (4, 'almalabis', 'الملابس', NULL, NULL, 'ar', 2, '2020-10-07 23:12:16', '2020-10-07 23:12:16'),
            (5, 'health_materials', 'Health Materials', NULL, NULL, 'en', 3, '2020-10-07 23:13:21', '2020-10-07 23:13:21'),
            (6, 'mawadun-sihiya', 'مواد صحية', NULL, NULL, 'ar', 3, '2020-10-07 23:13:21', '2020-10-07 23:13:21'),
            (7, 'acrylic', 'Acrylic', NULL, NULL, 'en', 4, '2020-10-07 23:14:56', '2020-10-07 23:14:56'),
            (8, 'acrylic', 'الإلكريلك', NULL, NULL, 'ar', 4, '2020-10-07 23:14:56', '2020-10-07 23:14:56'),
            (9, 'electronics', 'Electronics', NULL, NULL, 'en', 5, '2020-10-07 23:16:41', '2020-10-07 23:16:41'),
            (10, 'electronics', 'الإلكترونيات', NULL, NULL, 'ar', 5, '2020-10-07 23:16:41', '2020-10-07 23:16:41'),
            (11, 'perfume', 'Perfume', NULL, NULL, 'en', 6, '2020-10-07 23:17:10', '2020-10-07 23:17:10'),
            (12, 'perfume', 'العطور', NULL, NULL, 'ar', 6, '2020-10-07 23:17:10', '2020-10-07 23:17:10');
        ");
    }

}
