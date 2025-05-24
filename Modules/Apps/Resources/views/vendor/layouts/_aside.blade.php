<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu  page-header-fixed" data-keep-expanded="false" data-auto-scroll="true"
            data-slide-speed="200" style="padding-top: 20px">
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>

            <li class="nav-item {{ active_menu('home') }}">
                <a href="{{ url(route('vendor.home')) }}" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">{{ __('apps::vendor.home.title') }}</span>
                    <span class="selected"></span>
                </a>
            </li>


            @if (Module::isEnabled('Order'))
                @permission('show_orders')
                    <li class="heading">
                        <h3 class="uppercase">{{ __('apps::vendor.aside.tab.orders') }}</h3>
                    </li>

                    <li class="nav-item {{ active_menu('orders') }}">
                        <a href="{{ url(route('vendor.orders.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">{{ __('apps::vendor.aside.orders') }}</span>
                        </a>
                    </li>
                @endpermission
            @endif


            @if (Module::isEnabled('Catalog'))
                @permission('show_products')
                    <li class="heading">
                        <h3 class="uppercase">{{ __('apps::vendor.aside.tab.catalog') }}</h3>
                    </li>

                    <li class="nav-item {{ active_menu('products') }}">
                        <a href="{{ url(route('vendor.products.index')) }}" class="nav-link nav-toggle">
                            <i class="icon-settings"></i>
                            <span class="title">{{ __('apps::vendor.aside.products') }}</span>
                        </a>
                    </li>
                @endpermission
            @endif

        </ul>
    </div>
</div>
