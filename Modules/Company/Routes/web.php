<?php


/*
|================================================================================
|                             VENDOR ROUTES
|================================================================================
*/
Route::prefix('vendor-dashboard')->middleware(['vendor.auth', 'permission:seller_access'])->group(function () {

    /*foreach (File::allFiles(module_path('Company', 'Routes/Vendor')) as $file) {
        require_once($file->getPathname());
    }*/

    foreach (["company.php"] as $value) {
        require_once(module_path('Company', 'Routes/Vendor/' . $value));
    }

});


/*
|================================================================================
|                             Back-END ROUTES
|================================================================================
*/
Route::prefix('dashboard')->middleware(['dashboard.auth', 'permission:dashboard_access'])->group(function () {

    /*foreach (File::allFiles(module_path('Company', 'Routes/Dashboard')) as $file) {
        require_once($file->getPathname());
    }*/

    foreach (["company.php", "delivery-charges.php"] as $value) {
        require_once(module_path('Company', 'Routes/Dashboard/' . $value));
    }

});

/*
|================================================================================
|                             FRONT-END ROUTES
|================================================================================
*/
Route::prefix('/')->group(function () {

    /*foreach (File::allFiles(module_path('Company', 'Routes/FrontEnd')) as $file) {
        require_once($file->getPathname());
    }*/

    foreach (["company.php"] as $value) {
        require_once(module_path('Company', 'Routes/FrontEnd/' . $value));
    }

});
