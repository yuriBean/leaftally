@extends('layouts.auth')
@php
    use App\Models\Utility;
    $logo = asset(Storage::url('uploads/logo/'));
    $settings = Utility::settings();
@endphp
@section('page-title')
    {{ __('Login') }}
@endsection

@push('css')
<style>
/* Custom Zameen.com theme button with forced visibility */
.btn-zameen,
button.btn-zameen,
#login_button.btn-zameen,
button#login_button.btn-zameen {
    background: #007C38 !important;
    background-color: #007C38 !important;
    background-image: none !important;
    border: 2px solid #007C38 !important;
    border-color: #007C38 !important;
    color: #ffffff !important;
    padding: 25px 50px !important;
    border-radius: 10px !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    text-align: center !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    min-height: 48px !important;
    line-height: 1.5 !important;
    box-shadow: 0 2px 4px rgba(0, 124, 56, 0.2) !important;
    text-transform: none !important;
    opacity: 1 !important;
    visibility: visible !important;
    position: relative !important;
    z-index: 1 !important;
}

.btn-zameen:link,
.btn-zameen:visited,
button.btn-zameen:link,
button.btn-zameen:visited,
#login_button.btn-zameen:link,
#login_button.btn-zameen:visited,
button#login_button.btn-zameen:link,
button#login_button.btn-zameen:visited {
    color: #fff !important;
}



.btn-zameen:focus,
button.btn-zameen:focus,
#login_button.btn-zameen:focus,
button#login_button.btn-zameen:focus {
    background: #006b30 !important;
    background-color: #006b30 !important;
    border-color: #006b30 !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 124, 56, 0.25) !important;
}

.btn-zameen:active,
button.btn-zameen:active,
#login_button.btn-zameen:active,
button#login_button.btn-zameen:active {
    background: #005a28 !important;
    background-color: #005a28 !important;
    border-color: #005a28 !important;
    color: #ffffff !important;
    transform: translateY(0) !important;
}

.btn-zameen:disabled,
button.btn-zameen:disabled,
#login_button.btn-zameen:disabled,
button#login_button.btn-zameen:disabled {
    background: #6c757d !important;
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    opacity: 0.65 !important;
    transform: none !important;
}

.form-control:focus {
    border-color: #007C38 !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 124, 56, 0.25) !important;
}

.text-primary {
    color: #007C38 !important;
}

.text-primary:hover {
    color: #006b30 !important;
}
</style>
@endpush

@section('auth-lang')
    @php
        $languages = App\Models\Utility::languages();
    @endphp
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text"> {{ ucFirst('en') }}
                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @foreach ($languages as $code => $language)
                    <a href="{{ route('login', $code) }}" tabindex="0"
                        class="dropdown-item {{ $code == $lang ? 'active' : '' }}">
                        <span>{{ ucFirst($language) }}</span>
                    </a>
                @endforeach
            </div>
        </li>
    </div>
@endsection

@section('content')
    <div class="d-flex align-items-center text-center justify-content-between">
        <h2 class="mb-3 w-100 f-w-600">{{ __('Login') }}</h2>
    </div>
    {{ Form::open(['route' => 'login.decide', 'method' => 'post', 'id' => 'loginForm', 'class'=>'needs-validation','novalidate'   ]) }}
    @csrf
    @if (session('status'))
        <div class="mb-4 font-medium text-lg text-green-600 text-danger">
            {{session('status') }}
        </div>
    @endif
    <div class="">
        <div class="form-group mb-3">
            <label for="email"
                class="form-label d-flex align-items-center justify-content-between">{{ __('Email or Username') }}</label>
            <input class="form-control @error('email') is-invalid @enderror" id="email" type="text" name="email"
                value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                <div class="invalid-feedback" role="alert">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="password"
                class="form-label d-flex align-items-center justify-content-between">{{ __('Password') }}</label>
            <input class="form-control @error('password') is-invalid @enderror" id="password" type="password"
                name="password" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback" role="alert">{{ $message }}</div>
            @enderror

        </div>

        @if ($settings['recaptcha_module'] == 'yes')
            @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2-checkbox')
                <div class="form-group mb-4">
                    {!! NoCaptcha::display($settings['cust_darklayout'] == 'on' ? ['data-theme' => 'dark'] : []) !!}
                    @error('g-recaptcha-response')
                        <span class="small text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            @else
                <div class="form-group mb-4">
                    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" class="form-control">
                    @error('g-recaptcha-response')
                        <span class="error small text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            @endif
        @endif
        <div class="form-group mb-4">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request', $lang) }}"
                    class=" d-flex align-items-center justify-content-between">{{ __('Forgot Your Password?') }}</a>
            @endif
        </div>
        <div class="d-grid">
            <button type="submit" class="btn-zameen" id="login_button" style="background:#007C38 !important; color:#fff !important; border:2px solid #007C38 !important; width:100% !important; padding: 6px 12px !important; border-radius: 6px !important;">{{ __('Login') }}</button>

        </div>
        @if ($settings['enable_signup'] == 'on')
            <p class="my-4 text-center">{{ __("Don't have an account?") }} <a href="{{ route('register',$lang) }}"
                    class="text-primary">{{ __('Register') }}</a></p>
        @endif

    </div>
    {{ Form::close() }}
@endsection

@push('custom-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#loginForm").submit(function(e) {
                $("#login_button").attr("disabled", true);
                return true;
            });
        });
    </script>
        {{-- @if ($settings['recaptcha_module'] == 'yes')
        {!! NoCaptcha::renderJs() !!}
        @endif --}}
    @if (isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'yes')
        @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2-checkbox')
            {!! NoCaptcha::renderJs() !!}
        @else
            <script src="https://www.google.com/recaptcha/api.js?render={{ $settings['google_recaptcha_key'] }}"></script>
            <script>
                $(document).ready(function() {
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ $settings['google_recaptcha_key'] }}', {
                            action: 'submit'
                        }).then(function(token) {
                            $('#g-recaptcha-response').val(token);
                        });
                    });
                });
            </script>
        @endif
    @endif
@endpush
