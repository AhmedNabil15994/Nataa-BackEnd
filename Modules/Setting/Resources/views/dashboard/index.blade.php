@extends('apps::dashboard.layouts.app')
@section('title', __('setting::dashboard.settings.routes.index'))

@section('content')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <a href="{{ url(route('dashboard.home')) }}">{{ __('apps::dashboard.home.title') }}</a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="#">{{ __('setting::dashboard.settings.routes.index') }}</a>
                    </li>
                </ul>
            </div>

            <h1 class="page-title"></h1>

            @include('apps::dashboard.layouts._msg')

            <div class="row">
                <form role="form" class="form-horizontal form-row-seperated" method="post"
                    action="{{ route('dashboard.setting.update') }}" enctype="multipart/form-data">
                    <div class="col-md-12">
                        @csrf
                        <div class="col-md-3">
                            <div class="panel-group accordion scrollable" id="accordion2">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle">
                                                {{ __('setting::dashboard.settings.form.tabs.info') }}
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_2_1" class="panel-collapse in">
                                        <div class="panel-body">
                                            <ul class="nav nav-pills nav-stacked">
                                                <li class="active">
                                                    <a href="#global_setting" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.general') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#app" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.app') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#mail" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.mail') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#logo" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.logo') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#social_media" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.social_media') }}
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="#products" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.products') }}
                                                    </a>
                                                </li>

                                                {{-- <li>
                                                    <a href="#order_status" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.order_status') }}
                                            </a>
                                            </li> --}}

                                                <li>
                                                    <a href="#custom_codes" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.custom_codes') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#payment_gateway" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.payment_gateway') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#other" data-toggle="tab">
                                                        {{ __('setting::dashboard.settings.form.tabs.other') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="tab-content">
                                @include('setting::dashboard.tabs.general')
                                @include('setting::dashboard.tabs.app')
                                @include('setting::dashboard.tabs.mail')
                                @include('setting::dashboard.tabs.logo')
                                @include('setting::dashboard.tabs.social')
                                @include('setting::dashboard.tabs.products')
                                {{-- @include('setting::dashboard.tabs.order_status') --}}
                                @include('setting::dashboard.tabs.custom_codes')
                                @include('setting::dashboard.tabs.payment_gateway')
                                @include('setting::dashboard.tabs.other')
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-9">
                                <button type="submit" id="submit" class="btn btn-lg blue">
                                    {{ __('apps::dashboard.general.edit_btn') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('scripts')

    <script>
        var selectVendorRow = $('#selectVendorRow');
        var selectVendorLoader = $('#selectVendorLoader');

        var sideMenuVendorsHeadTitle = $('#sideMenuVendorsHeadTitle');
        var sideMenuVendorsSeller = $('#sideMenuVendorsSeller');
        var sideMenuVendors = $('#sideMenuVendors');
        var sideMenuVendorsSections = $('#sideMenuVendorsSections');
        var sideMenuVendorsCategories = $('#sideMenuVendorsCategories');
        var sideMenuReviewProducts = $('#sideMenuReviewProducts');

        @if (config('setting.other.is_multi_vendors') == 0)
            getAllActiveVendors();
        @endif

        $('input[name="other[is_multi_vendors]"]').change(function() {
            var value = $(this).val();
            // console.log('value:::', value);
            if (value == 1) {
                selectVendorRow.hide();
                selectVendorLoader.hide();

                sideMenuVendorsHeadTitle.show();
                sideMenuVendorsSeller.show();
                sideMenuVendors.show();
                sideMenuVendorsSections.show();
                sideMenuVendorsCategories.show();
                sideMenuReviewProducts.show();
            } else {
                $('#selectVendors').empty();
                getAllActiveVendors();

                sideMenuVendorsHeadTitle.hide();
                sideMenuVendorsSeller.hide();
                sideMenuVendors.hide();
                sideMenuVendorsSections.hide();
                sideMenuVendorsCategories.hide();
                sideMenuReviewProducts.hide();
            }
        });

        function getAllActiveVendors() {

            selectVendorLoader.show();

            $.ajax({
                url: "{{ route('dashboard.vendors.get_all_active_vendors') }}",
                type: 'get',
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,

                beforeSend: function() {

                },
                success: function(data) {

                    if (data[0] == true) {

                        selectVendorLoader.hide();
                        // console.log('data.data::', data.data);
                        var vendorID = "{{ config('setting.default_vendor') }}";
                        // console.log('vendorID::', vendorID);
                        $.each(data.data, function(i, item) {
                            $('#selectVendors').append(`
                                <option value="${item.id}"
                                    ${item.id == vendorID ? 'selected' : ''}>
                                    ${item.title}
                                </option>`);
                        });

                        selectVendorRow.show();

                        /* Toggle Vendors Containers Based On Single OR Multi-Vendors */
                    }

                },
                error: function(data) {
                    displayErrors(data);
                },
            });

        }


        /* $('input[name="payment_gateway[upayment][payment_mode]"]').change(function () {
            var value = $(this).val();
            if (value == 'test_mode') {
                $('#testModelData').show();
                $('#liveModelData').hide();
            } else {
                $('#testModelData').hide();
                $('#liveModelData').show();
            }
        }); */

        function onChangePaymentMode(paymentMode, key) {
            if (paymentMode == 'test_mode') {
                $('#testModelData-' + key).show();
                $('#liveModelData-' + key).hide();
            } else {
                $('#testModelData-' + key).hide();
                $('#liveModelData-' + key).show();
            }
        }
    </script>

@stop
