<?php


/*
|================================================================================
|                             VENDOR ROUTES
|================================================================================
*/
Route::prefix('vendor-dashboard')->middleware(['vendor.auth', 'permission:seller_access'])->group(function () {

    /*foreach (File::allFiles(module_path('Catalog', 'Routes/Vendor')) as $file) {
        require_once($file->getPathname());
    }*/

    foreach (["products.php"] as $value) {
        require_once(module_path('Catalog', 'Routes/Vendor/' . $value));
    }

});


/*
|================================================================================
|                             Back-END ROUTES
|================================================================================
*/
Route::prefix('dashboard')->middleware(['dashboard.auth', 'permission:dashboard_access'])->group(function () {

    /*foreach (File::allFiles(module_path('Catalog', 'Routes/Dashboard')) as $file) {
        require_once($file->getPathname());
    }*/

    foreach (["categories.php", "products.php", "search-keywords.php"] as $value) {
        require_once(module_path('Catalog', 'Routes/Dashboard/' . $value));
    }

});

/*
|================================================================================
|                             FRONT-END ROUTES
|================================================================================
*/
Route::prefix('/')->group(function () {

    /*foreach (File::allFiles(module_path('Catalog', 'Routes/FrontEnd')) as $file) {
        require_once($file->getPathname());
    }*/

    foreach (["categories.php", "address.php", "checkout.php", "filter.php", "search.php", "shopping-cart.php", "products.php"] as $value) {
        require_once(module_path('Catalog', 'Routes/FrontEnd/' . $value));
    }

});
