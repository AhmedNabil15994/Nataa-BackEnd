<?php

Route::group(['prefix' => 'orders'], function () {

  	Route::get('/' ,'Vendor\OrderController@index')
  	->name('vendor.orders.index')
    ->middleware(['permission:show_orders']);

  	Route::get('datatable'	,'Vendor\OrderController@datatable')
  	->name('vendor.orders.datatable')
  	->middleware(['permission:show_orders']);

  	Route::get('create'		,'Vendor\OrderController@create')
  	->name('vendor.orders.create')
    ->middleware(['permission:add_orders']);

  	Route::post('/'			,'Vendor\OrderController@store')
  	->name('vendor.orders.store')
    ->middleware(['permission:add_orders']);

  	Route::get('{id}/edit'	,'Vendor\OrderController@edit')
  	->name('vendor.orders.edit')
    ->middleware(['permission:edit_orders']);

  	Route::put('{id}'		,'Vendor\OrderController@update')
  	->name('vendor.orders.update')
    ->middleware(['permission:edit_orders']);

  	Route::delete('{id}'	,'Vendor\OrderController@destroy')
  	->name('vendor.orders.destroy')
    ->middleware(['permission:delete_orders']);

  	Route::get('deletes'	,'Vendor\OrderController@deletes')
  	->name('vendor.orders.deletes')
    ->middleware(['permission:delete_orders']);

  	Route::get('{id}','Vendor\OrderController@show')
  	->name('vendor.orders.show')
    ->middleware(['permission:show_orders']);

});
