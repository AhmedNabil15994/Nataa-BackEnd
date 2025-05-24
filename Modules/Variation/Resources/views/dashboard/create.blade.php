@extends('apps::dashboard.layouts.app')
@section('title', __('variation::dashboard.options.routes.create'))
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
                        <a href="{{ url(route('dashboard.options.index')) }}">
                            {{__('variation::dashboard.options.routes.index')}}
                        </a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="#">{{__('variation::dashboard.options.routes.create')}}</a>
                    </li>
                </ul>
            </div>

            <h1 class="page-title"></h1>

            <div class="row">
                <form id="form" role="form" class="form-horizontal form-row-seperated" method="post"
                      enctype="multipart/form-data" action="{{route('dashboard.options.store')}}">
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
                                                    <a href="#general" data-toggle="tab">
                                                        {{ __('variation::dashboard.options.form.tabs.general') }}
                                                    </a>
                                                </li>
                                                <li class="">
                                                    <a href="#option_values" data-toggle="tab">
                                                        {{ __('variation::dashboard.options.form.tabs.option_values') }}
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

                                {{-- CREATE FORM --}}
                                <div class="tab-pane active fade in" id="general">
                                    <h3 class="page-title">{{__('variation::dashboard.options.form.tabs.general')}}</h3>
                                    <div class="col-md-10">

                                        @foreach (config('translatable.locales') as $code)
                                            <div class="form-group">
                                                <label class="col-md-2">
                                                    {{__('variation::dashboard.options.form.title')}} - {{ $code }}
                                                </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="title[{{$code}}]" class="form-control"
                                                           data-name="title.{{$code}}">
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('variation::dashboard.options.form.status')}}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="checkbox" class="make-switch" id="test" data-size="small"
                                                       name="status">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('variation::dashboard.options.form.option_as_filter')}}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="checkbox" class="make-switch" data-size="small"
                                                       name="option_as_filter">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                <div class="tab-pane fade in" id="option_values">
                                    <h3 class="page-title">{{__('variation::dashboard.options.form.tabs.option_values')}}</h3>
                                    <div class="col-md-10">
                                        <div class="option_values_form">
                                            <div class="content">
                                                @foreach (config('translatable.locales') as $code)
                                                    <div class="form-group">
                                                        <label class="col-md-2">
                                                            {{__('variation::dashboard.options.form.title')}}
                                                            - {{ $code }}
                                                        </label>
                                                        <div class="col-md-9">
                                                            <input type="text" name="option_value_title[{{$code}}][]"
                                                                   class="form-control"
                                                                   data-name="option_value_title.{{$code}}">
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <div class="form-group">
                                                    <label class="col-md-2">
                                                        {{__('variation::dashboard.options.form.status')}}
                                                    </label>
                                                    <div class="col-md-9">
                                                        <input type="checkbox" class="ischecked"
                                                               name="option_value_status[]" value="1"
                                                               onclick="checkFunction()">
                                                        <input type="hidden" class="isUnchecked"
                                                               name="option_value_status[]" value="0" checked>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <a href="javascript:;" class="remove_html btn red">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </div>
                                                <hr>
                                            </div>
                                        </div>

                                        <button id="copy" type="button"
                                                class="btn green btn-lg mt-ladda-btn ladda-button btn-circle btn-outline"
                                                data-style="slide-down" data-spinner-color="#333">
                                        <span class="ladda-label">
                                            <i class="icon-plus"></i>
                                        </span>
                                        </button>
                                    </div>
                                </div>
                                {{-- END CREATE FORM --}}
                            </div>
                        </div>

                        {{-- PAGE ACTION --}}
                        <div class="col-md-12">
                            <div class="form-actions">
                                @include('apps::dashboard.layouts._ajax-msg')
                                <div class="form-group">
                                    <button type="submit" id="submit" class="btn btn-lg blue">
                                        {{__('apps::dashboard.general.add_btn')}}
                                    </button>
                                    <a href="{{url(route('dashboard.options.index')) }}" class="btn btn-lg red">
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
    <script>
        $(function () {
            var html = $("div.option_values_form").html();
            $('#copy').click(function (event) {
                $(".option_values_form").append(html);
            });

            $(".option_values_form").on("click", ".remove_html", function (e) {
                e.preventDefault();
                $(this).closest('.content').remove();
            });

        });


        function checkFunction() {

            $('[name="option_value_status[]"]').change(function () {
                if ($(this).is(':checked'))
                    $(this).next().prop('disabled', true);
                else
                    $(this).next().prop('disabled', false);
            });

        }

    </script>
@stop
