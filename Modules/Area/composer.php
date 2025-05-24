<?php

view()->composer(['area::dashboard.cities.*'], \Modules\Area\ViewComposers\Dashboard\CountryComposer::class);

view()->composer([
    'area::dashboard.states.*',
    'company::dashboard.delivery-charges.*',
    'vendor::dashboard.delivery-charges.*',
    'vendor::dashboard.vendors.*',
    'user::dashboard.drivers.create',
    'user::dashboard.drivers.edit',
],
    \Modules\Area\ViewComposers\Dashboard\CityComposer::class);

view()->composer([
    'apps::frontend.index',
    'vendor::frontend.vendors.*',
    'user::frontend.profile.*',
],
    \Modules\Area\ViewComposers\FrontEnd\StateComposer::class);

view()->composer([
    'catalog::frontend.address.*',
    'catalog::frontend.address.index',
    'user::frontend.profile.addresses.address',
    'user::frontend.profile.addresses.create',
],
    \Modules\Area\ViewComposers\FrontEnd\CityComposer::class);

view()->composer([
    'catalog::frontend.address.*',
    'catalog::frontend.address.index',
    'user::frontend.profile.addresses.address',
    'user::frontend.profile.addresses.create',
    'catalog::frontend.checkout.*',
],
    \Modules\Area\ViewComposers\FrontEnd\StateComposer::class);

view()->composer([
    'catalog::frontend.address.*',
    'catalog::frontend.address.index',
    'user::frontend.profile.addresses.address',
    'user::frontend.profile.addresses.create',
    'catalog::frontend.checkout.*',
],
    \Modules\Area\ViewComposers\FrontEnd\CountryComposer::class);
