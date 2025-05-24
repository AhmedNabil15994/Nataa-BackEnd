<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu  page-header-fixed" data-keep-expanded="false" data-auto-scroll="true"
            data-slide-speed="200" style="padding-top: 20px">
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>

            <li class="nav-item {{ active_menu(['home', '']) }}">
                <a href="{{ url(route('dashboard.home')) }}" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">{{ __('apps::dashboard.home.title') }}</span>
                    <span class="selected"></span>
                </a>
            </li>

            <li class="heading">
                <h3 class="uppercase">{{ __('apps::dashboard.aside.tab.roles_permissions') }}</h3>
            </li>

            @permission('show_roles')
            <li class="nav-item {{ active_menu('roles') }}">
                <a href="{{ url(route('dashboard.roles.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.roles') }}</span>
                </a>
            </li>
            @endpermission

            <li class="heading">
                <h3 class="uppercase">{{ __('apps::dashboard.aside.tab.users') }}</h3>
            </li>

            @permission('show_users')
            <li class="nav-item {{ active_menu('users') }}">
                <a href="{{ url(route('dashboard.users.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.users') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_admins')
            <li class="nav-item {{ active_menu('admins') }}">
                <a href="{{ url(route('dashboard.admins.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.admins') }}</span>
                </a>
            </li>
            @endpermission

            {{-- #############################################################################--}}

            @if (Module::isEnabled('Vendor'))

            <li class="heading" id="sideMenuVendorsHeadTitle"
                {{-- style="display: {{ toggleSideMenuItemsByVendorType() }}" --}}>
                <h3 class="uppercase">{{ __('apps::dashboard.aside.tab.vendors') }}</h3>
            </li>

            {{-- --}}{{--@permission('show_payments')--}}
            {{-- <li class="nav-item {{ active_menu('payments') }}">--}}
            {{-- <a href="{{ url(route('dashboard.payments.index')) }}" class="nav-link nav-toggle">--}}
            {{-- <i class="icon-settings"></i>--}}
            {{-- <span class="title">{{ __('apps::dashboard.aside.payments') }}</span>--}}
            {{-- </a>--}}
            {{-- </li>--}}
            {{-- @endpermission--}}

            {{-- @permission('show_vendor_statuses')--}}
            {{-- <li class="nav-item {{ active_menu('vendor_statuses') }}">--}}
            {{-- <a href="{{ url(route('dashboard.vendor_statuses.index')) }}" class="nav-link nav-toggle">--}}
            {{-- <i class="icon-settings"></i>--}}
            {{-- <span class="title">{{ __('apps::dashboard.aside.vendor_statuses') }}</span>--}}
            {{-- </a>--}}
            {{-- </li>--}}
            {{-- @endpermission--}}

            @permission('show_sellers')
            <li class="nav-item {{ active_menu('sellers') }}" id="sideMenuVendorsSeller"
                style="display: {{ toggleSideMenuItemsByVendorType() }}">
                <a href="{{ url(route('dashboard.sellers.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.sellers') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_vendors')
            <li class="nav-item {{ active_menu('vendors') }}" id="sideMenuVendors"
                style="display: {{ toggleSideMenuItemsByVendorType() }}">
                <a href="{{ url(route('dashboard.vendors.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.vendors') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_vendor_categories')
            <li class="nav-item {{ active_menu('vendor-categories') }}" id="sideMenuVendorsCategories"
                style="display: {{ toggleSideMenuItemsByVendorType() }}">
                <a href="{{ url(route('dashboard.vendor_categories.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.vendor_categories') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_sections')
            <li class="nav-item {{ active_menu('sections') }}" id="sideMenuVendorsSections"
                style="display: {{ toggleSideMenuItemsByVendorType() }}">
                <a href="{{ url(route('dashboard.sections.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.sections') }}</span>
                </a>
            </li>
            @endpermission

            {{-- @if(config('setting.other.enable_subscriptions') == 1)--}}
            @if (Module::isEnabled('Subscription'))

            @permission('show_subscriptions')
            <li class="nav-item {{ active_menu('subscriptions') }}">
                <a href="{{ url(route('dashboard.subscriptions.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.subscriptions') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_packages')
            <li class="nav-item {{ active_menu('packages') }}">
                <a href="{{ url(route('dashboard.packages.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.packages') }}</span>
                </a>
            </li>
            @endpermission

            @endif
            {{-- @endif--}}

            @endif

            {{-- ############################################################################# --}}

            @if (Module::isEnabled('Catalog'))

            <li class="heading">
                <h3 class="uppercase">{{ __('apps::dashboard.aside.tab.catalog') }}</h3>
            </li>

            @permission('show_products')
            <li class="nav-item {{ active_menu('products') }}">
                <a href="{{ url(route('dashboard.products.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.products') }}</span>
                </a>
            </li>
            @endpermission

            @permission('review_products')
            <li class="nav-item {{ active_menu('review-products') }}" id="sideMenuReviewProducts"
                style="display: {{ toggleSideMenuItemsByVendorType() }}">
                <a href="{{ url(route('dashboard.review_products.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.review_products') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_categories')
            <li class="nav-item {{ active_menu('categories') }}">
                <a href="{{ url(route('dashboard.categories.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.categories') }}</span>
                </a>
            </li>
            @endpermission

            @if(config('setting.products.toggle_variations') == 1)
            @permission('show_options')
            <li class="nav-item {{ active_menu('options') }}">
                <a href="{{ url(route('dashboard.options.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.options') }}</span>
                </a>
            </li>
            @endpermission
            @endif

            @permission('show_tags')
            <li class="nav-item {{ active_menu('tags') }}">
                <a href="{{ url(route('dashboard.tags.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-tag"></i>
                    <span class="title">{{ __('apps::dashboard.aside.tags') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_search_keywords')
            <li class="nav-item {{ active_menu('search_keywords') }}">
                <a href="{{ url(route('dashboard.search_keywords.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.search_keywords') }}</span>
                </a>
            </li>
            @endpermission

            @endif

            @if (Module::isEnabled('Order'))

            <li class="heading">
                <h3 class="uppercase">{{ __('apps::dashboard.aside.tab.orders') }}</h3>
            </li>

            @permission('show_orders')

            {{-- <li class="nav-item {{ active_menu('create-order') }}">--}}
            {{-- <a href="{{ url(route('dashboard.orders.create')) }}" class="nav-link nav-toggle">--}}
            {{-- <i class="icon-settings"></i>--}}
            {{-- <span class="title">{{ __('apps::dashboard.aside.create_order') }}</span>--}}
            {{-- </a>--}}
            {{-- </li>--}}

            <li class="nav-item {{ active_menu('orders') }}">
                <a href="{{ url(route('dashboard.orders.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.orders') }}</span>
                </a>
            </li>

            <li class="nav-item {{ active_menu('all-orders') }}">
                <a href="{{ url(route('dashboard.all_orders.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.all_orders') }}</span>
                </a>
            </li>

            @endpermission

            @permission('show_order_statuses')
            <li class="nav-item {{ active_menu('order-statuses') }}">
                <a href="{{ url(route('dashboard.order-statuses.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.order_statuses') }}</span>
                </a>
            </li>
            @endpermission

            @endif

            {{-- ############################################################################# --}}

            @if (Module::isEnabled('Company'))

            <li class="heading">
                <h3 class="uppercase">{{ __('apps::dashboard.aside.tab.companies') }}</h3>
            </li>

            @permission('show_companies')
            <li class="nav-item {{ active_menu('companies') }}">
                <a href="{{ url(route('dashboard.companies.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.companies') }}</span>
                </a>
            </li>
            @endpermission

            @if(config('setting.other.select_shipping_provider') == 'vendor_delivery')
            @permission('show_delivery_charges')
            <li class="nav-item {{ active_menu('vendor-delivery-charges') }}">
                <a href="{{ url(route('dashboard.vendor_delivery_charges.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.delivery_charges') }}</span>
                </a>
            </li>
            @endpermission
            @elseif(config('setting.other.select_shipping_provider') == 'shipping_company')
            @permission('show_delivery_charges')
            <li class="nav-item {{ active_menu('delivery-charges') }}">
                <a href="{{ url(route('dashboard.delivery-charges.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.delivery_charges') }}</span>
                </a>
            </li>
            @endpermission
            @endif


            @permission('show_drivers')
            <li class="nav-item {{ active_menu('drivers') }}">
                <a href="{{ url(route('dashboard.drivers.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.drivers') }}</span>
                </a>
            </li>
            @endpermission

            @endif

            {{-- @if (Module::isEnabled('Transaction'))

            <li class="heading">
                    <h3 class="uppercase">{{ __('apps::dashboard.aside.tab.transactions') }}</h3>
            </li>

            @permission('show_transactions')
            <li class="nav-item {{ active_menu('transactions') }}">
                <a href="{{ url(route('dashboard.transactions.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.transactions') }}</span>
                </a>
            </li>
            @endpermission

            @endif --}}

            @if (Module::isEnabled('Report'))

            <li class="heading">
                <h3 class="uppercase">{{ __('apps::dashboard.aside.tab.reports') }}</h3>
            </li>

            @permission('show_product_sale_reports')
            <li class="nav-item {{ active_menu('product-sales-reports') }}">
                <a href="{{ url(route('dashboard.reports.product_sale')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.product_sales') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_product_stock_reports')
            <li class="nav-item {{ active_menu('product-stock-reports') }}">
                <a href="{{ url(route('dashboard.reports.product_stock')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.product_stock') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_vendors_reports')
            <li class="nav-item {{ active_menu('vendors-reports') }}">
                <a href="{{ url(route('dashboard.reports.vendors')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.vendors_reports') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_order_sale_reports')
            <li class="nav-item {{ active_menu('order-sales-reports') }}">
                <a href="{{ url(route('dashboard.reports.order_sale')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.order_sales') }}</span>
                </a>
            </li>
            @endpermission
            @permission('show_drivers_reports')
            <li class="nav-item {{ active_menu('drivers-reports') }}">
                <a href="{{ url(route('dashboard.reports.drivers')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.drivers_reports') }}</span>
                </a>
            </li>
            @endpermission

            @endif

            <li class="heading">
                <h3 class="uppercase">{{ __('apps::dashboard.aside.setting') }}</h3>
            </li>

            @if (Module::isEnabled('Area'))

            @permission('show_countries')
            <li class="nav-item {{ active_menu('countries') }}">
                <a href="{{ url(route('dashboard.countries.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.countries') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_cities')
            <li class="nav-item {{ active_menu('cities') }}">
                <a href="{{ url(route('dashboard.cities.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.cities') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_states')
            <li class="nav-item {{ active_menu('states') }}">
                <a href="{{ url(route('dashboard.states.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.states') }}</span>
                </a>
            </li>
            @endpermission

            @endif

            @permission('show_pages')
            <li class="nav-item {{ active_menu('pages') }}">
                <a href="{{ url(route('dashboard.pages.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.pages') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_advertising')
            <li class="nav-item {{ active_menu('advertising-groups') }}">
                <a href="{{ url(route('dashboard.advertising_groups.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.advertising_groups') }}</span>
                </a>
            </li>
            @endpermission

            {{--@permission('show_advertising')
            <li class="nav-item {{ active_menu('advertising') }}">
            <a href="{{ url(route('dashboard.advertising.index')) }}" class="nav-link nav-toggle">
                <i class="icon-settings"></i>
                <span class="title">{{ __('apps::dashboard.aside.advertising') }}</span>
            </a>
            </li>
            @endpermission--}}

            @permission('show_slider')
            <li class="nav-item {{ active_menu('slider') }}">
                <a href="{{ url(route('dashboard.slider.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.slider') }}</span>
                </a>
            </li>
            @endpermission

            @permission('show_coupon')
            <li class="nav-item {{ active_menu('coupons') }}">
                <a href="{{ url(route('dashboard.coupons.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-calculator"></i>
                    <span class="title">{{ __('apps::dashboard.aside.coupons') }}</span>
                </a>
            </li>
            @endpermission

            {{-- <li class="nav-item {{ active_menu('setting') }}">
            <a href="{{ url(route('dashboard.setting.index')) }}" class="nav-link nav-toggle">
                <i class="icon-settings"></i>
                <span class="title">{{ __('apps::dashboard.aside.setting') }}</span>
            </a>
            </li> --}}

            {{--<li class="nav-item {{ active_menu('notifications') }}">
            <a href="{{ url(route('dashboard.notifications.create')) }}" class="nav-link nav-toggle">
                <i class="icon-settings"></i>
                <span class="title">{{ __('apps::dashboard.aside.notifications') }}</span>
            </a>
            </li>--}}

            @permission('show_notifications')
            <li class="nav-item {{ active_menu('notifications') }}">
                <a href="{{ url(route('dashboard.notifications.index')) }}" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">{{ __('apps::dashboard.aside.notifications') }}</span>
                </a>
            </li>
            @endpermission

            {{--<li class="nav-item {{ active_menu('telescope') }}">
            <a href="{{ url(route('telescope')) }}" class="nav-link nav-toggle">
                <i class="icon-settings"></i>
                <span class="title">{{ __('apps::dashboard.aside.telescope') }}</span>
            </a>
            </li>--}}

        </ul>
    </div>
</div>
