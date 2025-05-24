<?php

return [
    'cart' => [
        'product' => [
            'not_found' => 'This product is not available now, with id:',
            'select_single_addons' => 'Please choose from single product addons of addon',
            'product_times' => 'Sorry This product is not available now, you can try again later',
        ],
    ],
    'validations' => [
        'cart' => [
            'vendor_not_match' => 'Items in cart not match with this vendor , clear the cart and try again',
        ],
        'user_token' => [
            'required' => 'Enter User Token',
        ],
        'state_id' => [
            'required' => 'Enter State Id',
            'exists' => 'This State is not found',
        ],
        'address_id' => [
            'required' => 'Enter Address Id',
            'exists' => 'This address is not found',
        ],
        'vendor_id' => [
            'required' => 'Choose Vendor',
            'exists' => 'This vendor is not found',
        ],
        'addons' => [
            'selected_options_greater_than_options_count' => 'Selected options is greater than available options of addons',
            'selected_options_less_than_options_count' => 'Selected options is less than available options of addons',
            'addons_not_found' => 'This addons is not available now',
            'option_not_found' => 'This option is not available now',
            'addons_number' => 'Id',
            'select_required_product_addon_category' => 'You must choose from these add-ons',
            'options' => [
                'required' => 'Select from addon options',
            ],
        ],
    ],
];
