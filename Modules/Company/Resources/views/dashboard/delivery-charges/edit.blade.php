@extends('apps::dashboard.layouts.app')
@section('title', __('company::dashboard.delivery_charges.update.title'))
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
                    <a href="{{ url(route('dashboard.delivery-charges.index')) }}">
                        {{__('company::dashboard.delivery_charges.index.title')}}
                    </a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <a href="#">{{__('company::dashboard.delivery_charges.update.title')}}</a>
                </li>
            </ul>
        </div>

        <h1 class="page-title"></h1>

        <div class="row">
            <form id="updateForm" page="form" class="form-horizontal form-row-seperated" method="post"
                enctype="multipart/form-data" action="{{route('dashboard.delivery-charges.update',$company->id)}}">
                @csrf
                @method('PUT')
                <div class="col-md-12">

                    <h3 class="page-title">{{ $company->translate(locale())->name }}</h3>

                    {{-- RIGHT SIDE --}}
                    <div class="col-md-3">
                        <div class="panel-group accordion scrollable" id="accordion2">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                </div>
                                <div id="collapse_2_1" class="panel-collapse in">
                                    <div class="panel-body">
                                        <ul class="nav nav-pills nav-stacked">
                                            @foreach ($cities as $key => $city)
                                            <li class="{{ $key == 0 ? 'active' : '' }}">
                                                <a href="#cities_{{ $key }}" data-toggle="tab">
                                                    {{ $city->translate(locale())->title }}
                                                </a>
                                            </li>
                                            @endforeach
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
                            @foreach ($cities as $key2 => $city2)
                            <div class="tab-pane fade in {{ $key2 == 0 ? 'active' : '' }}" id="cities_{{ $key2 }}">
                                <h3 class="page-title">{{ $city2->translate(locale())->title }}</h3>
                                <div class="col-md-12">
                                    @foreach ($city2->states as $key3 => $state)
                                    <div class="form-group">
                                        <label class="col-md-2">
                                            {{ $state->translate(locale())->title }}
                                        </label>
                                        <div class="col-md-4">
                                            <input type="text" name="delivery[]" class="form-control"
                                                value="{{!array_key_exists($state->id, $charges) ? "" : $charges[$state->id]}}"
                                                placeholder="{{__('company::dashboard.delivery_charges.update.charge')}}">
                                            <input type="hidden" name="state[]" value="{{ $state->id }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="delivery_time[]" class="form-control"
                                                value="{{!array_key_exists($state->id, $times) ? "" : $times[$state->id]}}"
                                                placeholder="{{__('company::dashboard.delivery_charges.update.time')}}">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                            {{-- END UPDATE FORM --}}

                        </div>
                    </div>

                    {{-- PAGE ACTION --}}
                    <div class="col-md-12">
                        <div class="form-actions">
                            @include('apps::dashboard.layouts._ajax-msg')
                            <div class="form-group">
                                <button type="submit" id="submit" class="btn btn-lg green">
                                    {{__('apps::dashboard.general.edit_btn')}}
                                </button>
                                <a href="{{url(route('dashboard.delivery-charges.index')) }}" class="btn btn-lg red">
                                    {{__('apps::dashboard.general.back_btn')}}
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