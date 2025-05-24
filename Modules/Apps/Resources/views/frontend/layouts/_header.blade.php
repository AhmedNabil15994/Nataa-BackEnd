<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '--') || {{ config('app.name') }} </title>
    <meta name="description" content="@yield('meta_description', '')">
    <meta name="keywords" content="@yield('meta_keywords', '')">
    <meta name="author" content="{{ config('setting.app_name.'.locale()) ?? '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <!-- Font Icons -->
    <link rel="stylesheet" href="{{ url('frontend/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ url('frontend/css/themify-icons.css') }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ url('frontend/css/bootstrap.min.css') }}">
    <!-- Animation -->
    <link rel="stylesheet" href="{{ url('frontend/css/animate.min.css') }}">
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="{{ url('frontend/css/owl.carousel.min.css') }}">
    <!-- Smooth Product -->
    <link rel="stylesheet" href="{{ url('frontend/css/select2.min.css') }}" type="text/css">
    <!-- Select 2 -->
    <link rel="stylesheet" href="{{ url('frontend/css/smoothproducts.css') }}" type="text/css">

{{-- Start - Bind Css Code From Dashboard Daynamic --}}
{!! config('setting.custom_codes.css_in_head') ?? null !!}
{{-- End - Bind Css Code From Dashboard Daynamic --}}

<!-- Template style -->
    @if(locale() == 'ar')
        <link rel="stylesheet" href="{{ url('frontend/css/style-ar.css') }}">
    @else
        <link rel="stylesheet" href="{{ url('frontend/css/style-en.css') }}">
    @endif

    <link rel="icon"
          href="{{ config('setting.favicon') ? url(config('setting.favicon')) : url('frontend/favicon.png') }}"/>

    <style>
        /* start loader style */

        #loaderDiv,
        #headerLoaderDiv {
            display: none;
            margin: 15px auto;
            justify-content: center;
        }

        .generalLoaderDiv {
            display: none;
            margin: 15px 100px;
            justify-content: center;
        }

        #loaderCouponDiv {
            display: none;
            margin: 15px 100px;
            justify-content: center;
        }

        #loaderDiv .my-loader,
        #headerLoaderDiv .my-loader,
        .generalLoaderDiv .my-loader,
        #loaderCouponDiv .my-loader {
            border: 10px solid #f3f3f3;
            border-radius: 50%;
            border-top: 10px solid #3498db;
            width: 70px;
            height: 70px;
            -webkit-animation: spin 2s linear infinite;
            /* Safari */
            animation: spin 2s linear infinite;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* end loader style */

        .empty-cart-title {
            text-align: center;
        }

        .label-tag-product {
            position: relative;
            color: #fff;
            margin: 10px;
            border-radius: 20px;
            padding: 2px 15px;
            font-size: 12px;
        }

        .percentage-discount {
            color: red;
        }
    </style>

    @yield('externalStyle')

    {{-- Start - Bind Js Code From Dashboard Daynamic --}}
    {!! config('setting.custom_codes.js_before_head') ?? null !!}
    {{-- End - Bind Js Code From Dashboard Daynamic --}}

</head>
