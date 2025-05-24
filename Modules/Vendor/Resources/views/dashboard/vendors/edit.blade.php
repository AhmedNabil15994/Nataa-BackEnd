@extends('apps::dashboard.layouts.app')
@section('title', __('vendor::dashboard.vendors.update.title'))
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
                        <a href="{{ url(route('dashboard.vendors.index')) }}">
                            {{ __('vendor::dashboard.vendors.index.title') }}
                        </a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="#">{{ __('vendor::dashboard.vendors.update.title') }}</a>
                    </li>
                </ul>
            </div>

            <h1 class="page-title"></h1>

            <div class="row">
                <form id="updateForm" page="form" class="form-horizontal form-row-seperated" method="post"
                    enctype="multipart/form-data" action="{{ route('dashboard.vendors.update', $vendor->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="col-md-12">

                        {{-- RIGHT SIDE --}}
                        <div class="col-md-3">
                            <div class="panel-group accordion scrollable" id="accordion2">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><a class="accordion-toggle"></a></h4>
                                    </div>
                                    <div id="collapse_2_1" class="panel-collapse in">
                                        <div class="panel-body">
                                            <ul class="nav nav-pills nav-stacked">
                                                <li class="active">
                                                    <a href="#global_setting" data-toggle="tab">
                                                        {{ __('vendor::dashboard.vendors.update.form.general') }}
                                                    </a>
                                                </li>

                                                <li class="">
                                                    <a href="#categories" data-toggle="tab">
                                                        {{ __('vendor::dashboard.vendors.update.form.categories') }}
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="#other" data-toggle="tab">
                                                        {{ __('vendor::dashboard.vendors.update.form.other') }}
                                                    </a>
                                                </li>

                                                @if (config('setting.other.select_shipping_provider') == 'vendor_delivery')
                                                    <li>
                                                        <a href="#availabilities" data-toggle="tab">
                                                            {{ __('vendor::dashboard.vendors.tabs.availabilities') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                {{-- <li>
                                                        <a href="#companies" data-toggle="tab">
                                                            {{ __('vendor::dashboard.vendors.create.form.companies_and_states') }}
                                                </a>
                                                </li> --}}

                                                <li>
                                                    <a href="#seo" data-toggle="tab">
                                                        {{ __('vendor::dashboard.vendors.update.form.seo') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PAGE CONTENT --}}
                        <div class="col-md-9">
                            <div class="tab-content">

                                {{-- UPDATE FORM --}}
                                <div class="tab-pane active fade in" id="global_setting">
                                    {{-- <h3 class="page-title">{{ __('vendor::dashboard.vendors.update.form.general') }}</h3> --}}
                                    <div class="col-md-10">


                                        {{--  tab for lang --}}
                                        <ul class="nav nav-tabs">
                                            @foreach (config('translatable.locales') as $code)
                                                <li class="@if ($loop->first) active @endif"><a
                                                        data-toggle="tab"
                                                        href="#first_{{ $code }}">{{ __('catalog::dashboard.products.form.tabs.input_lang', ['lang' => $code]) }}</a>
                                                </li>
                                            @endforeach
                                        </ul>

                                        {{--  tab for content --}}
                                        <div class="tab-content">

                                            @foreach (config('translatable.locales') as $code)
                                                <div id="first_{{ $code }}"
                                                    class="tab-pane fade @if ($loop->first) in active @endif">

                                                    <div class="form-group">
                                                        <label class="col-md-2">
                                                            {{ __('vendor::dashboard.vendors.update.form.title') }}
                                                            - {{ $code }}
                                                        </label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="title[{{ $code }}]"
                                                                class="form-control" data-name="title.{{ $code }}"
                                                                value="{{ $vendor->translate($code)->title }}">
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label class="col-md-2">
                                                            {{ __('vendor::dashboard.vendors.update.form.description') }}
                                                            - {{ $code }}
                                                        </label>
                                                        <div class="col-md-9">
                                                            <textarea name="description[{{ $code }}]" rows="8" cols="80"
                                                                class="form-control {{ is_rtl($code) }}Editor" data-name="description.{{ $code }}">{{ $vendor->translate($code)->description }}</textarea>
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach

                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">

                                                <div class="form-group">
                                                    <label class="col-md-12">
                                                        {{ __('latitude') }}
                                                    </label>
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control"  name="lat" value="{{ $vendor->lat }}">
                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-lg-6">

                                                <div class="form-group">
                                                    <label class="col-md-12">
                                                        {{ __('longitude') }}
                                                    </label>
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control" name="long" value="{{ $vendor->long }}">
                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('vendor::dashboard.vendors.update.form.status') }}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="checkbox" class="make-switch" id="test" data-size="small"
                                                    name="status" {{ $vendor->status == 1 ? ' checked="" ' : '' }}>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('vendor::dashboard.vendors.update.form.use_app_drivers') }}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="checkbox" class="make-switch" id="use_app_drivers"
                                                    data-size="small" name="use_app_drivers"
                                                    {{ $vendor->use_app_drivers == 1 ? ' checked="" ' : '' }}>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        {{-- <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('vendor::dashboard.vendors.update.form.is_trusted')}}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="checkbox" class="make-switch" id="test" data-size="small"
                                                       name="is_trusted" {{($vendor->is_trusted == 1) ? ' checked="" ' : ''}}>
                                                <div class="help-block"></div>
                                            </div>
                                        </div> --}}

                                        @if ($vendor->trashed())
                                            <div class="form-group">
                                                <label class="col-md-2">
                                                    {{ __('area::dashboard.vendors.update.form.restore') }}
                                                </label>
                                                <div class="col-md-9">
                                                    <input type="checkbox" class="make-switch" id="test"
                                                        data-size="small" name="restore">
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>

                                <div class="tab-pane fade in" id="categories">
                                    {{-- <h3 class="page-title">{{__('vendor::dashboard.vendors.update.form.categories')}}
                                    </h3> --}}
                                    <div id="jstree">
                                        @include('vendor::dashboard.tree.vendors.edit', [
                                            'mainVendorCategories' => $mainVendorCategories,
                                        ])
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="vendor_category_id" id="root_category"
                                            value="" data-name="vendor_category_id">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="tab-pane fade in" id="other">
                                    {{-- <h3 class="page-title">{{ __('vendor::dashboard.vendors.update.form.other') }}</h3> --}}
                                    <div class="col-md-10">

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('vendor::dashboard.vendors.update.form.commission') }} %
                                            </label>
                                            <div class="col-md-9">
                                                <input type="text" name="commission" class="form-control"
                                                    data-name="commission" value="{{ $vendor->commission }}">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('vendor::dashboard.vendors.create.form.fixed_commission') }}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="text" name="fixed_commission" class="form-control"
                                                    data-name="fixed_commission" value="{{ $vendor->fixed_commission }}">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        {{-- <div class="form-group">
                                                        <label class="col-md-2">
                                                            {{__('vendor::dashboard.vendors.update.form.payments')}}
                                        </label>
                                        <div class="col-md-9">
                                            <div class="mt-checkbox-list">
                                                @foreach ($payments as $payment)
                                                <label class="mt-checkbox">
                                                    <input type="checkbox" name="payment_id[]" value="{{$payment->id}}"
                                                        {{ $vendor->payments->contains($payment->id) ? 'checked=""' : ''}}>
                                                    <img src="{{ url($payment->image) }}" alt="" style="width: 26px;">
                                                    {{ $payment->translate(locale())->title }}
                                                    <span></span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div> --}}

                                        {{-- <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('vendor::dashboard.vendors.create.form.vendor_statuses') }}
                                            </label>
                                            <div class="col-md-9">
                                                <select name="vendor_status_id" id="single"
                                                    class="form-control select2-allow-clear">
                                                    <option value=""></option>
                                                    @foreach ($vendorStatuses as $vendorStatus)
                                                        <option value="{{ $vendorStatus['id'] }}"
                                                            @if ($vendor->vendor_status_id == $vendorStatus['id']) selected @endif>
                                                            {{ $vendorStatus->translate(locale())->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> --}}

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('vendor::dashboard.vendors.update.form.order_limit') }}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="text" name="order_limit" class="form-control"
                                                    data-name="order_limit" value="{{ $vendor->order_limit }}">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('vendor::dashboard.vendors.create.form.fixed_delivery') }}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="text" name="fixed_delivery" class="form-control"
                                                    data-name="fixed_delivery" value="{{ $vendor->fixed_delivery }}">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('vendor::dashboard.vendors.update.form.sections') }}
                                            </label>
                                            <div class="col-md-9">
                                                <select name="section_id[]" id="single"
                                                    class="form-control select2-allow-clear" data-name="section_id"
                                                    multiple>
                                                    <option value=""></option>
                                                    @foreach ($sections as $section)
                                                        <option value="{{ $section['id'] }}"
                                                            {{ $vendor->sections->contains($section->id) ? 'selected=""' : '' }}>
                                                            {{ $section->translate(locale())->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('vendor::dashboard.vendors.update.form.sellers') }}
                                            </label>
                                            <div class="col-md-9">
                                                <select name="seller_id[]" id="single"
                                                    class="form-control select2-allow-clear" multiple>
                                                    <option value=""></option>
                                                    @foreach ($sellers as $seller)
                                                        <option value="{{ $seller['id'] }}"
                                                            {{ $vendor->sellers->contains($seller->id) ? 'selected=""' : '' }}>
                                                            {{ $seller['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('user::dashboard.drivers.create.form.company')}}
                                            </label>
                                            <div class="col-md-9">
                                                @inject('driverCompanies','Modules\User\Repositories\Dashboard\DriverRepository')
                                                <select name="companies[]" id="companies" class="form-control select2-allow-clear" multiple>
                                                    <option value=""></option>
                                                    @foreach ($companies as $company)
                                                        <option value="{{ $company['id'] }}" {{ $vendor->companies->contains($company['id']) ? 'selected' : '' }}>
                                                            {{ $company->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('vendor::dashboard.vendors.update.form.image') }}
                                            </label>
                                            <div class="col-md-9">
                                                @include('core::dashboard.shared.file_upload', [
                                                    'image' => $vendor->image,
                                                ])
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('vendor::dashboard.vendors.create.form.vendor_email') }}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="text" name="vendor_email" class="form-control"
                                                    data-name="vendor_email" value="{{ $vendor->vendor_email }}">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('vendor::dashboard.vendors.update.form.mobile')}}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="text" name="mobile" class="form-control" data-name="mobile"
                                                       value="{{ $vendor->mobile }}">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        {{-- <div class="form-group">
                                                            <label class="col-md-2">
                                                                {{__('vendor::dashboard.vendors.create.form.receive_question')}}
                                        </label>
                                        <div class="col-md-9">
                                            <input type="checkbox" class="make-switch" id="test" data-size="small"
                                                name="receive_question" {{($vendor->receive_question == 1) ? ' checked="" ' : ''}}>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-2">
                                            {{__('vendor::dashboard.vendors.create.form.receive_prescription')}}
                                        </label>
                                        <div class="col-md-9">
                                            <input type="checkbox" class="make-switch" id="test" data-size="small"
                                                name="receive_prescription"
                                                {{($vendor->receive_prescription == 1) ? ' checked="" ' : ''}}>
                                            <div class="help-block"></div>
                                        </div>
                                    </div> --}}

                                    </div>
                                </div>

                                @if (config('setting.other.select_shipping_provider') == 'vendor_delivery')
                                    @include('vendor::dashboard.vendors.availabilities._edit_times')
                                @endif

                                {{-- @include('vendor::dashboard.vendors._companies') --}}

                                <div class="tab-pane fade in" id="seo">
                                    {{-- <h3 class="page-title">{{ __('vendor::dashboard.vendors.update.form.seo') }}</h3> --}}
                                    <div class="col-md-10">

                                        {{--  tab for lang --}}
                                        <ul class="nav nav-tabs">
                                            @foreach (config('translatable.locales') as $code)
                                                <li class="@if ($loop->first) active @endif"><a
                                                        data-toggle="tab"
                                                        href="#second_{{ $code }}">{{ __('catalog::dashboard.products.form.tabs.input_lang', ['lang' => $code]) }}</a>
                                                </li>
                                            @endforeach
                                        </ul>

                                        {{--  tab for content --}}
                                        <div class="tab-content">

                                            @foreach (config('translatable.locales') as $code)
                                                <div id="second_{{ $code }}"
                                                    class="tab-pane fade @if ($loop->first) in active @endif">

                                                    <div class="form-group">
                                                        <label class="col-md-2">
                                                            {{ __('vendor::dashboard.vendors.update.form.meta_keywords') }}
                                                            - {{ $code }}
                                                        </label>
                                                        <div class="col-md-9">
                                                            <textarea name="seo_keywords[{{ $code }}]" rows="8" cols="80" class="form-control"
                                                                data-name="seo_keywords.{{ $code }}">{{ $vendor->translate($code)->seo_keywords }}</textarea>
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-md-2">
                                                            {{ __('vendor::dashboard.vendors.update.form.meta_description') }}
                                                            - {{ $code }}
                                                        </label>
                                                        <div class="col-md-9">
                                                            <textarea name="seo_description[{{ $code }}]" rows="8" cols="80" class="form-control"
                                                                data-name="seo_description.{{ $code }}">{{ $vendor->translate($code)->seo_description }}</textarea>
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach

                                        </div>


                                    </div>
                                </div>

                                {{-- END UPDATE FORM --}}

                            </div>
                        </div>

                        {{-- PAGE ACTION --}}
                        <div class="col-md-12">
                            <div class="form-actions">
                                @include('apps::dashboard.layouts._ajax-msg')
                                <div class="form-group">
                                    <button type="submit" id="submit" class="btn btn-lg green">
                                        {{ __('apps::dashboard.general.edit_btn') }}
                                    </button>
                                    <a href="{{ url(route('dashboard.vendors.index')) }}" class="btn btn-lg red">
                                        {{ __('apps::dashboard.general.back_btn') }}
                                    </a>
                                </div>
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
        $(function() {
            $('#jstree').jstree();

            $('#jstree').on("changed.jstree", function(e, data) {
                $('#root_category').val(data.selected);
            });
        });
    </script>

    <script>
        var timePicker = $(".timepicker");
        timePicker.timepicker({
            timeFormat: 'HH',
        });

        var rowCountsArray = [];

        function hideCustomTime(id) {
            $("#collapse-" + id).hide();
        }

        function showCustomTime(id) {
            $("#collapse-" + id).show();
        }

        function addMoreDayTimes(e, dayCode) {

            if (e.preventDefault) {
                e.preventDefault();
            } else {
                e.returnValue = false;
            }

            var rowCount = Math.floor(Math.random() * 9000000000) + 1000000000;
            rowCountsArray.push(rowCount);

            var divContent = $('#div-content-' + dayCode);
            var newRow = `
        <div class="row times-row" id="rowId-${dayCode}-${rowCount}">
            <div class="col-md-3">
                <div class="input-group">
                    <input type="text" class="form-control timepicker 24_format" name="availability[time_from][${dayCode}][]"
                           data-name="availability[time_from][${dayCode}][]" value="00">
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-clock-o"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <input type="text" class="form-control timepicker 24_format" name="availability[time_to][${dayCode}][]"
                           data-name="availability[time_to][${dayCode}][]" value="23">
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-clock-o"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-danger" onclick="removeDayTimes('${dayCode}', ${rowCount}, 'row')">X</button>
            </div>
        </div>
        `;

            divContent.append(newRow);

            $(".timepicker").timepicker({
                timeFormat: 'HH',
            });
        }

        function removeDayTimes(dayCode, index, flag = '') {

            if (flag === 'row') {
                $('#rowId-' + dayCode + '-' + index).remove();
                const i = rowCountsArray.indexOf(index);
                if (i > -1) {
                    rowCountsArray.splice(i, 1);
                }
            }

        }
    </script>
@endsection
