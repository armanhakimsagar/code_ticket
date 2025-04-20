@extends('layouts.auth')

@section('page-title')
    {{ __('Login') }}
@endsection
@php
    $logo = Utility::get_superadmin_logo();
    $logos = \App\Models\Utility::get_file('uploads/logo/');

    $lang = app()->getLocale();
    if ($lang == 'ar' || $lang == 'he') {
        $settings['SITE_RTL'] = 'on';
    }
    $LangName = \App\Models\Languages::where('code', $lang)->first();
    if (empty($LangName)) {
        $LangName = new App\Models\Utility();
        $LangName->fullName = 'English';
    }
    $setting = App\Models\Settings::colorset();
    $color = 'theme-3';
    if (!empty($setting['color'])) {
        $color = $setting['color'];
    }
    $settings = \App\Models\Utility::settings();

    config([
        'captcha.secret' => $settings['NOCAPTCHA_SECRET'],
        'captcha.sitekey' => $settings['NOCAPTCHA_SITEKEY'],
        'options' => [
            'timeout' => 30,
        ],
    ]);
@endphp
@section('content')
    <div class="custom-login">
        <div class="bg-login bg-primary"></div>
        <div class="custom-login-inner">

            <nav class="navbar navbar-expand-md default">
                <div class="container pe-2">
                    <div class="navbar-brand">
                        <a href="#">
                            <img src="{{ $logos . $logo . '?timestamp=' . time() }}"
                                alt="{{ config('app.name', 'TicketGo Saas') }}" alt="logo" loading="lazy"
                                class="logo" />
                        </a>
                    </div>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarlogin">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarlogin">
                        <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">{{ __('Create Ticket') }}</a>
                            </li>

                            <li class="nav-item">
                                @if ($settings['FAQ'] == 'on')
                                    <a class="nav-link" href="{{ route('faq') }}">{{ __('FAQ') }}</a>
                                @endif
                            </li>
                            <li class="nav-item">
                                @if ($settings['Knowlwdge_Base'] == 'on')
                                    <a href="{{ route('knowledge') }}" class="nav-link">{{ __('Knowledge') }}</a>
                                @endif
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('search') }}">{{ __('Search Ticket') }}</a>
                            </li>
                            <div class="lang-dropdown-only-desk">
                                <li class="dropdown dash-h-item drp-language">
                                    <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <span class="drp-text"> {{ ucfirst($LangName->fullName) }}
                                        </span>
                                    </a>
                                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                                        @foreach (App\Models\Utility::languages() as $code => $language)
                                            <a href="{{ route('login', $code) }}" tabindex="0"
                                                class="dropdown-item dropdown-item {{ $LangName->code == $code ? 'active' : '' }}">
                                                <span>{{ ucFirst($language) }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </li>
                            </div>
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="custom-wrapper">
                <div class="custom-row">
                    <div class="col-xl-7 col-12">
                        <div class="custom-img">
                            <img src="{{ asset('assets/images/auth/' . $color . '.png') }}" alt="login-banner"
                                width="100%" loading="lazy">

                        </div>
                    </div>
                    <div class="col-xl-5 col-12">
                        <div class="card">

                            <div class="card-body">
                                <div>
                                    <h2 class="mb-3 f-w-600">{{ __('Welcome') }} <span
                                            class="text-primary">{{ __('to login!') }}</span></h2>
                                </div>
                                <form method="POST" action="{{ route('login') }}" id="form_data">
                                    @csrf
                                    @if (session()->has('info'))
                                        <div class="alert alert-success">
                                            {{ session()->get('info') }}
                                        </div>
                                    @endif
                                    @if (session()->has('status'))
                                        <div class="alert alert-info">
                                            {{ session()->get('status') }}
                                        </div>
                                    @endif

                                    <div class="custom-login-form">
                                        <div class="form-group mb-3">
                                            <label for="email" class="form-label d-flex">{{ __('Email') }}</label>
                                            <input type="email"
                                                class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                                id="email" name="email" placeholder="{{ __('Enter your email') }}"
                                                required="" value="{{ old('email') }}">
                                            <div class="invalid-feedback d-block">
                                                {{ $errors->first('email') }}
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label d-flex">{{ __('Password') }}</label>
                                            <input type="password"
                                                class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                id="password" name="password" placeholder="{{ __('Enter Password') }}"
                                                required="" value="{{ old('password') }}">
                                            <div class="invalid-feedback d-block">
                                                {{ $errors->first('password') }}
                                            </div>
                                        </div>
                                        <div class="form-group mb-4">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between">

                                                <span><a href="{{ route('password.request') }}"
                                                        tabindex="0">{{ __('Forgot your password?') }}</a></span>
                                            </div>
                                        </div>
                                        @if (Utility::getSettingValByName('RECAPTCHA_MODULE') == 'yes')
                                            <div class="form-group mb-4">
                                                {!! NoCaptcha::display() !!}
                                                @error('g-recaptcha-response')
                                                    <span class="small text-danger" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        @endif
                                </form>
                                <div class="d-grid">
                                    <button class="btn btn-primary mt-2 login-do-btn"
                                        id="login_button">{{ __('Login') }}</button>
                                </div>



                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <footer>
                <div class="auth-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <span>&copy; {{ date('Y') }}
                                    {{ App\Models\Utility::getValByName('footer_text') ? App\Models\Utility::getValByName('footer_text') : config('app.name', 'TicketGo') }}
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
@endsection

@push('scripts')
    @if (Utility::getSettingValByName('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
    @endif
    <script>
        $(document).ready(function() {
            $("#form_data").submit(function(e) {
                $("#login_button").attr("disabled", true);
                return true;
            });
        });
    </script>
@endpush
