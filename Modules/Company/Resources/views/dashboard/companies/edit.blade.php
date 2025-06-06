@extends('apps::dashboard.layouts.app')
@section('title', __('company::dashboard.companies.routes.update'))
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
                        <a href="{{ url(route('dashboard.companies.index')) }}">
                            {{__('company::dashboard.companies.routes.index')}}
                        </a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="#">{{__('company::dashboard.companies.routes.update')}}</a>
                    </li>
                </ul>
            </div>

            <h1 class="page-title"></h1>

            <div class="row">
                <form id="updateForm" page="form" class="form-horizontal form-row-seperated" method="post"
                      enctype="multipart/form-data" action="{{route('dashboard.companies.update',$company->id)}}">
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
                                                        {{ __('company::dashboard.companies.form.tabs.general') }}
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="#system" data-toggle="tab">
                                                        {{ __('company::dashboard.companies.form.tabs.system') }}
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="#availabilities" data-toggle="tab">
                                                        {{ __('company::dashboard.companies.form.tabs.availabilities') }}
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
                                    <h3 class="page-title">{{__('company::dashboard.companies.form.tabs.general')}}</h3>

                                    <ul class="nav nav-pills">
                                        @foreach (config('translatable.locales') as $k => $code)
                                            <li class="{{ $code == locale() ? 'active' : '' }}">
                                                <a id="{{$k}}-general-tab" data-toggle="tab"
                                                   aria-controls="general-tab-{{$k}}" href="#general-tab-{{$k}}"
                                                   aria-expanded="{{ $code == locale() ? 'true' : 'false' }}">{{ $code }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content px-1 pt-1">

                                        @foreach (config('translatable.locales') as $k => $code)
                                            <div role="tabpanel"
                                                 class="tab-pane {{ $code == locale() ? 'active' : '' }}"
                                                 id="general-tab-{{$k}}"
                                                 aria-expanded="{{ $code == locale() ? 'true' : 'false' }}"
                                                 aria-labelledby="{{$k}}-general-tab">

                                                <div class="col-md-12">

                                                    <div class="form-group">
                                                        <label class="col-md-2">
                                                            {{__('company::dashboard.companies.form.name')}}
                                                            - {{ $code }}
                                                        </label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="name[{{$code}}]"
                                                                   class="form-control"
                                                                   data-name="name.{{$code}}"
                                                                   value="{{ $company->translate($code)->name }}">
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-md-2">
                                                            {{__('company::dashboard.companies.form.description')}}
                                                            - {{ $code }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                <textarea name="description[{{$code}}]" rows="8"
                                                                          cols="80"
                                                                          class="form-control {{is_rtl($code)}}Editor"
                                                                          data-name="description.{{$code}}">{{ $company->translate($code)->description }}</textarea>
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        @endforeach

                                        <div class="col-md-12">

                                            <div class="form-group">
                                                <label class="col-md-2">
                                                    {{__('company::dashboard.companies.form.status')}}
                                                </label>
                                                <div class="col-md-9">
                                                    <input type="checkbox" class="make-switch" id="test"
                                                           data-size="small"
                                                           name="status" {{($company->status == 1) ? ' checked="" ' : ''}}>
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-2">
                                                    {{__('company::dashboard.companies.form.image')}}
                                                </label>
                                                <div class="col-md-9">
                                                    <div class="input-group">
                                                        <span class="input-group-btn">
                                                            <a data-input="image" data-preview="holder"
                                                               class="btn btn-primary lfm">
                                                                <i class="fa fa-picture-o"></i>
                                                                {{__('apps::dashboard.general.upload_btn')}}
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
                                                         <img src="{{url($company->image)}}" style="height: 15rem;">
                                                    </span>
                                                    <input type="hidden" data-name="image">
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="tab-pane fade in" id="system">
                                    <h3 class="page-title">{{__('company::dashboard.companies.form.tabs.system')}}</h3>
                                    <div class="col-md-12">
                                        <input type="hidden" value="{{$company->user_id}}">
                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('company::dashboard.companies.form.manager_name')}}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="text" name="manager_name" class="form-control"
                                                       data-name="manager_name" value="{{ $company->manager_name }}">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('company::dashboard.companies.form.email')}}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="email" name="email" class="form-control" data-name="email"
                                                       value="{{ $company->email }}">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('company::dashboard.companies.form.mobile')}}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="text" name="mobile" class="form-control"
                                                       data-name="mobile" value="{{ $company->mobile }}">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('company::dashboard.companies.form.password')}}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="password" name="password" class="form-control"
                                                       data-name="password">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('company::dashboard.companies.form.confirm_password')}}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="password" name="confirm_password" class="form-control"
                                                       data-name="confirm_password">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                @include('company::dashboard.availabilities._edit_times')

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
                                    <a href="{{url(route('dashboard.companies.index')) }}" class="btn btn-lg red">
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

@section('scripts')

    <script></script>

@endsection
