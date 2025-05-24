@extends('apps::dashboard.layouts.app')
@section('title', __('user::dashboard.drivers.create.title'))
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
                        <a href="{{ url(route('dashboard.drivers.index')) }}">
                            {{ __('user::dashboard.drivers.index.title') }}
                        </a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="#">{{ __('user::dashboard.drivers.create.title') }}</a>
                    </li>
                </ul>
            </div>

            <h1 class="page-title"></h1>

            <div class="row">
                <form id="form" role="form" class="form-horizontal form-row-seperated" method="post"
                    enctype="multipart/form-data" action="{{ route('dashboard.drivers.store') }}">
                    @csrf
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
                                                        {{ __('user::dashboard.drivers.create.form.general') }}
                                                    </a>
                                                </li>

                                                <li class="">
                                                    <a href="#statesTab" data-toggle="tab">
                                                        {{ __('user::dashboard.drivers.tabs.states') }}
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

                                <div class="tab-pane active fade in" id="global_setting">
                                    {{-- <h3 class="page-title">{{ __('user::dashboard.drivers.create.form.general') }}</h3> --}}
                                    <div class="col-md-10">

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('user::dashboard.drivers.create.form.company')}}
                                            </label>
                                            <div class="col-md-9">
                                                <select name="company_id" id="single" class="form-control select2"
                                                        data-name="company_id">
                                                    <option value=""></option>
                                                    @foreach ($companies as $company)
                                                        <option value="{{ $company['id'] }}" {{ $company['id'] == old('company_id') ? 'selected' : '' }}>
                                                            {{ $company->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        {{-- <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('user::dashboard.drivers.create.form.vendors')}}
                                            </label>
                                            <div class="col-md-9">
                                                <select name="vendor_id" id="single" class="form-control select2"
                                                        data-name="vendor_id">
                                                    <option value=""></option>
                                                    @foreach ($activeVendors as $vendor)
                                                        <option
                                                            value="{{ $vendor['id'] }}" {{ $vendor['id'] == old('vendor_id') ? 'selected' : '' }}>
                                                            {{ $vendor->translate(locale())->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="help-block"></div>
                                            </div>
                                        </div> --}}

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('user::dashboard.drivers.create.form.name') }}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="text" name="name" class="form-control" data-name="name">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('user::dashboard.drivers.create.form.email') }}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="email" name="email" class="form-control" data-name="email">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('user::dashboard.drivers.create.form.mobile') }}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="text" name="mobile" class="form-control"
                                                    data-name="mobile">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('user::dashboard.drivers.create.form.password') }}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="password" name="password" class="form-control"
                                                    data-name="password">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('user::dashboard.drivers.create.form.confirm_password') }}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="password" name="confirm_password" class="form-control"
                                                    data-name="confirm_password">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('user::dashboard.drivers.create.form.image') }}
                                            </label>
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                    <span class="input-group-btn">
                                                        <a data-input="image" data-preview="holder"
                                                            class="btn btn-primary lfm">
                                                            <i class="fa fa-picture-o"></i>
                                                            {{ __('apps::dashboard.general.upload_btn') }}
                                                        </a>
                                                    </span>
                                                    <input name="image" class="form-control image" type="text"
                                                        readonly>
                                                    <span class="input-group-btn">
                                                        <a data-input="image" data-preview="holder"
                                                            class="btn btn-danger delete">
                                                            <i class="glyphicon glyphicon-remove"></i>
                                                        </a>
                                                    </span>
                                                </div>
                                                <span class="holder" style="margin-top:15px;max-height:100px;">
                                                </span>
                                                <input type="hidden" data-name="image">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{ __('user::dashboard.drivers.create.form.roles') }}
                                            </label>
                                            <div class="col-md-9">
                                                <div class="mt-checkbox-list">
                                                    @foreach ($roles as $role)
                                                        <label class="mt-checkbox">
                                                            <input type="checkbox" name="roles[]"
                                                                value="{{ $role->id }}">
                                                            {{ $role->translate(locale())->display_name }}
                                                            <span></span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="tab-pane fade in" id="statesTab">
                                    <div class="tabbable tabbable-tabdrop">
                                        <ul class="nav nav-pills">

                                            @foreach ($cities as $key => $city)
                                                <li class="{{ $key == 0 ? 'active' : '' }}">
                                                    <a href="#cities_{{ $key }}" data-toggle="tab">
                                                        {{ $city->translate(locale())->title }}
                                                    </a>
                                                </li>
                                            @endforeach

                                        </ul>
                                        <div class="tab-content">

                                            @foreach ($cities as $key2 => $city2)
                                                <div class="tab-pane {{ $key2 == 0 ? 'active' : '' }}"
                                                    id="cities_{{ $key2 }}">

                                                    <div class="col-md-5">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped table-bordered table-hover">
                                                                <thead>
                                                                    <th style="padding: 15px 5px 15px 0;">
                                                                        {{ __('user::dashboard.drivers.tabs.form.state') }}
                                                                    </th>
                                                                    <th style="width: 140px;">
                                                                        {{-- <span>{{ __('user::dashboard.drivers.tabs.form.status') }}</span> --}}
                                                                        <div class="pull-right"
                                                                            title="{{ __('user::dashboard.drivers.tabs.form.btn.activate_all') }}">
                                                                            <input type="checkbox"
                                                                                class="make-switch makeAllActiveCheckbox"
                                                                                data-size="small"
                                                                                name="active_all_statuses">
                                                                        </div>
                                                                    </th>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($city2->states as $key3 => $state)
                                                                        <tr>
                                                                            <td>{{ $state->translate(locale())->title }}
                                                                            </td>
                                                                            <td>
                                                                                <input type="checkbox"
                                                                                    class="make-switch status-input"
                                                                                    data-size="small"
                                                                                    name="statuses[{{ $state->id }}]">
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- PAGE ACTION --}}
                        <div class="col-md-12">
                            <div class="form-actions">
                                @include('apps::dashboard.layouts._ajax-msg')
                                <div class="form-group">
                                    <button type="submit" id="submit" class="btn btn-lg blue">
                                        {{ __('apps::dashboard.general.add_btn') }}
                                    </button>
                                    <a href="{{ url(route('dashboard.drivers.index')) }}" class="btn btn-lg red">
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
        jQuery(document).ready(function() {
            $('.makeAllActiveCheckbox').on('switchChange.bootstrapSwitch', function(e) {
                $('.makeAllActiveCheckbox').not(this).prop('checked', e.target.checked).change();
                $('.status-input').each(function(event) {
                    $(this).prop('checked', e.target.checked).change();
                });
            });
        });
    </script>
@stop
