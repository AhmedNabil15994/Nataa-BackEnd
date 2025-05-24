<?php

Route::group(['prefix' => 'setting'], function () {

    // Show Settings Form
    Route::get('/', 'Dashboard\SettingController@index')
    ->name('dashboard.setting.index');

    // Update Settings
    Route::post('/', 'Dashboard\SettingController@update')
    ->name('dashboard.setting.update');

});
