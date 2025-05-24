<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'orders'], function () {

    Route::get('/', 'Dashboard\OrderController@index')
        ->name('dashboard.orders.index')
        ->middleware(['permission:show_orders']);

    Route::get('datatable', 'Dashboard\OrderController@datatable')
        ->name('dashboard.orders.datatable')
        ->middleware(['permission:show_orders']);

//	Route::get('create', 'Dashboard\OrderController@create')
//		->name('dashboard.orders.create')
//		->middleware(['permission:add_orders']);

    Route::post('store', 'Dashboard\OrderController@store')
        ->name('dashboard.orders.store')
        ->middleware(['permission:add_orders']);

    Route::get('{id}/edit', 'Dashboard\OrderController@edit')
        ->name('dashboard.orders.edit')
        ->middleware(['permission:edit_orders']);

    Route::put('{id}', 'Dashboard\OrderController@update')
        ->name('dashboard.orders.update')
        ->middleware(['permission:edit_orders']);

    Route::get('bulk/update-order-status', 'Dashboard\OrderController@updateBulkOrderStatus')
        ->name('dashboard.orders.update_bulk_order_status')
        ->middleware(['permission:edit_orders']);

    Route::delete('{id}', 'Dashboard\OrderController@destroy')
        ->name('dashboard.orders.destroy')
        ->middleware(['permission:delete_orders']);

    Route::get('deletes', 'Dashboard\OrderController@deletes')
        ->name('dashboard.orders.deletes')
        ->middleware(['permission:delete_orders']);

    Route::get('{id}', 'Dashboard\OrderController@show')
        ->name('dashboard.orders.show')
        ->middleware(['permission:show_orders']);

    Route::get('print/selected-items', 'Dashboard\OrderController@printSelectedItems')
        ->name('dashboard.orders.print_selected_items')
        ->middleware(['permission:show_orders']);
});

Route::get('create-order', 'Dashboard\OrderController@create')
    ->name('dashboard.orders.create')
    ->middleware(['permission:add_orders']);

Route::group(['prefix' => 'all-orders'], function () {

    Route::get('/', 'Dashboard\OrderController@getAllOrders')
        ->name('dashboard.all_orders.index')
        ->middleware(['permission:show_all_orders']);

    Route::get('datatable', 'Dashboard\OrderController@allOrdersDatatable')
        ->name('dashboard.all_orders.datatable')
        ->middleware(['permission:show_all_orders']);

    Route::get('{id}', 'Dashboard\OrderController@show')
        ->name('dashboard.all_orders.show')
        ->middleware(['permission:show_all_orders']);
});
