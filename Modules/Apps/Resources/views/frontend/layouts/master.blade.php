<!DOCTYPE html>
<html dir="{{ locale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ locale() == 'ar' ? 'ar' : 'en' }}">

@include('apps::frontend.layouts._header')

<body>
<div class="main-content">

    @include('apps::frontend.layouts.header-section')

    <div class="site-main">
        @yield('content')
    </div>

    @include('apps::frontend.layouts.footer-section')

</div>

<a href="https://wa.me/{{ config('setting.contact_us.whatsapp') }}" data-toggle="tooltip" data-placement="top"
   title="{{ __('apps::frontend.contact_us.info.technical_support')}}" target="_blank"
   class="whatsapp-chat">
    <img src="{{ url('frontend/images/whatsapp.png') }}" alt=""/>
</a>

@include('apps::frontend.layouts.scripts')

</body>
</html>