<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-12 footer-logo-icon">
                <img class="footer-logo"
                     src="{{ config('setting.white_logo') ? url(config('setting.white_logo')) : url('frontend/images/footer-logo.png') }}"/>
                <div class="links">
                    <ul>
                        <li>{{ config('setting.address.' . locale()) }}</li>
                        <li>{{ config('setting.contact_us.email') }}</li>
                    </ul>
                </div>
                <div class="footer-social">
                    <a href="{{ config('setting.social.facebook') ?? '#' }}" target="_blank" class="social-icon">
                        <i class="ti-facebook"></i>
                    </a>
                    <a href="{{ config('setting.social.instagram') ?? '#' }}" target="_blank" class="social-icon">
                        <i class="ti-instagram"></i>
                    </a>
                    <a href="{{ config('setting.social.linkedin') ?? '#' }}" target="_blank" class="social-icon">
                        <i class="ti-linkedin"></i>
                    </a>
                    <a href="{{ config('setting.social.twitter') ?? '#' }}" target="_blank" class="social-icon">
                        <i class="ti-twitter-alt"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <h3 class="title-of-footer"> {{ __('apps::frontend.master.important_links') }}</h3>
                <div class="links">
                    <ul>

                        @foreach($pages as $k => $page)
                            <li>
                                <a href="{{ route('frontend.pages.index', $page->slug) }}">
                                    {{ $page->translate(locale())->title }}
                                </a>
                            </li>
                        @endforeach

                        @if(env('LOGIN'))
                            <li>
                                <a href="{{ route('frontend.login') }}"> {{ __('authentication::frontend.login.title') }}</a>
                            </li>
                        @endif

                    </ul>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <h3 class="title-of-footer">{{ __('apps::frontend.master.website_links') }}</h3>
                <div class="links">
                    <ul>
                        <li>
                            <a href="{{ route('frontend.home') }}">{{ __('apps::frontend.master.home') }}</a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ __('apps::frontend.master.new_products') }}</a>
                        </li>
                        {{--<li><a href="index.php?page=category"> الأقسام</a></li>--}}
                        <li>
                            <a href="{{ $aboutUs ? route('frontend.pages.index', $aboutUs->slug) : '#' }}">{{ __('apps::frontend.master.about_us') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('frontend.contact_us') }}">{{ __('apps::frontend.master.contact_us') }}</a>
                        </li>
                        {{--<li><a href="javascript:;"> {{ __('apps::frontend.master.offers') }}</a></li>--}}
                    </ul>
                </div>
            </div>
            <div class="col-md-4 col-12 footer-subscribe">
                <h3 class="title-of-footer"> {{ __('apps::frontend.master.mailing_list') }}</h3>
                <div class="subscribe-form">
                    <form>
                        <input type="text" class="form-control"
                               placeholder="{{ __('apps::frontend.contact_us.form.email')}}"/>
                        <button class="btn" type="submit">{{ __('apps::frontend.master.subscribe') }}</button>
                    </form>
                </div>
                <h3 class="title-of-footer"> {{ __('apps::frontend.master.payment_method') }}</h3>
                <div class="pay-men">
                    <a href="#"><img src="{{ url('frontend/images/payment.svg') }}" alt="pay1"></a>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="footer-copyright text-center">
    <p>{{ __('apps::frontend.footer.copyright')}}</p>
</div>
