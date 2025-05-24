<?php


/*
|================================================================================
|                             VENDOR ROUTES
|================================================================================
*/
Route::prefix('vendor-dashboard')->middleware(['vendor.auth', 'permission:seller_access'])->group(function () {

    /*foreach (File::allFiles(module_path('Occasion', 'Routes/Vendor')) as $file) {
        require_once($file->getPathname());
    }*/

    foreach (["occasion.php"] as $value) {
        require_once(module_path('Occasion', 'Routes/Vendor/' . $value));
    }

});


/*
|================================================================================
|                             Back-END ROUTES
|================================================================================
*/
Route::prefix('dashboard')->middleware(['dashboard.auth', 'permission:dashboard_access'])->group(function () {

    /*foreach (File::allFiles(module_path('Occasion', 'Routes/Dashboard')) as $file) {
        require_once($file->getPathname());
    }*/

    foreach (["occasion.php"] as $value) {
        require_once(module_path('Occasion', 'Routes/Dashboard/' . $value));
    }

});

/*
|================================================================================
|                             FRONT-END ROUTES
|================================================================================
*/
Route::prefix('/')->group(function () {

    /*foreach (File::allFiles(module_path('Occasion', 'Routes/FrontEnd')) as $file) {
        require_once($file->getPathname());
    }*/

    foreach (["occasion.php"] as $value) {
        require_once(module_path('Occasion', 'Routes/FrontEnd/' . $value));
    }

});
