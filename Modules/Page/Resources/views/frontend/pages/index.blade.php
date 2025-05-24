@extends('apps::frontend.layouts.master')
@section('title', $page->translate(locale())->title)
@section('content')

    <div class="container">
        <div class="page-crumb mt-30">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('frontend.home') }}">
                            <i class="ti-home"></i> {{ __('apps::frontend.master.home') }}</a>
                    </li>
                    <li class="breadcrumb-item active text-muted"
                        aria-current="page">{{ $page->translate(locale())->title }}</li>
                </ol>
            </nav>
        </div>
        <div class="inner-page">
            <div class="single-page">
                <h1>{{ $page->translate(locale())->title }}</h1>
                {!! $page->translate(locale())->description !!}
            </div>

        </div>
    </div>

@endsection

@section('externalJs')

    <script></script>

@endsection