@extends('layouts.auth')
@php
    use App\Models\Utility;
    $logo = asset(Storage::url('uploads/logo/'));
    $company_logo = App\Models\Utility::getValByName('company_logo');
    $settings = Utility::settings();
    $setting = \Modules\LandingPage\Entities\LandingPageSetting::settings();
@endphp

@section('page-title')
    {{ __('Register') }}
@endsection

@section('auth-lang')
    @php $languages = App\Models\Utility::languages(); @endphp
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text">{{ ucfirst($languages[$lang] ?? '') }}</span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @foreach ($languages as $code => $language)
                    <a href="{{ route('register', ['ref' => $ref, 'lang' => $code]) }}" tabindex="0" class="dropdown-item {{ $code == $lang ? 'active' : '' }}">
                        <span>{{ ucfirst($language) }}</span>
                    </a>
                @endforeach
            </div>
        </li>
    </div>
@endsection

@section('content')
<div class="d-flex text-center align-items-center justify-content-between">
    <h2 class="mb-3 w-100 f-w-600">{{ __('Register') }}</h2>
</div>
<form method="POST" action="{{ route('register.store', ['plan' => $plan]) }}" class="needs-validation" novalidate>
    @csrf
    <div class="">
        @if (session('status'))
            <div class="mb-4 font-medium text-lg text-danger">
                {{ session('status') }}
            </div>
        @endif

        <div class="form-group">
            <label for="name" class="form-label d-flex align-items-center justify-content-between">{{ __('Full Name') }}</label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="{{ __('Enter Your Full Name') }}" required autocomplete="name" autofocus>
            @error('name')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label d-flex align-items-center justify-content-between">{{ __('Email') }}</label>
            <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Enter Your Email') }}" required autocomplete="email" autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-group">
            <label for="industry" class="form-label d-flex align-items-center justify-content-between">{{ __('Industry') }}</label>
            <select id="industry" name="industry" class="form-control @error('industry') is-invalid @enderror" required>
                @php
                    $industries = [
                        'Agriculture','Aerospace','Automotive','Banking','Construction','Consulting','Defence','Education','E-commerce','Energy','Entertainment','Fashion','Financial Services','Food Processing','Government','Healthcare','Hospitality','Insurance','Legal Services','Logistics','Manufacturing','Maritime','Media','Mining','Nonprofits','Pharmaceuticals','Professional Services','Public Sector','Real Estate','Retail','SaaS Providers','Social Services','Technology','Telecommunications','Tourism','Transportation','Utilities','Wholesale','Other'
                    ];
                @endphp
                <option value="" disabled {{ old('industry') ? '' : 'selected' }}>{{ __('Select your industry') }}</option>
                @foreach($industries as $opt)
                    <option value="{{ $opt }}" {{ old('industry') === $opt ? 'selected' : '' }}>{{ __($opt) }}</option>
                @endforeach
            </select>
            @error('industry')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-group" id="industry_other_wrap" style="{{ old('industry') === 'Other' ? '' : 'display:none' }}">
            <label for="industry_other" class="form-label d-flex align-items-center justify-content-between">{{ __('Other Industry') }}</label>
            <input id="industry_other" type="text" class="form-control @error('industry_other') is-invalid @enderror" name="industry_other" value="{{ old('industry_other') }}" placeholder="{{ __('Write here') }}">
            @error('industry_other')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-group">
            <label for="referral_source" class="form-label d-flex align-items-center justify-content-between">{{ __('How did you hear about us?') }}</label>
            <select id="referral_source" name="referral_source" class="form-control @error('referral_source') is-invalid @enderror">
                @php
                    $sources = [
                        'Google search','FaceBook','LinkedIn','Instagram','Twitter','A friend','Whatsapp/ Telegram/ Other community groups','Other'
                    ];
                @endphp
                <option value="" {{ old('referral_source') ? '' : 'selected' }} disabled>{{ __('Select an option') }}</option>
                @foreach($sources as $opt)
                    <option value="{{ $opt }}" {{ old('referral_source') === $opt ? 'selected' : '' }}>{{ __($opt) }}</option>
                @endforeach
            </select>
            @error('referral_source')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-group" id="referral_other_wrap" style="{{ old('referral_source') === 'Other' ? '' : 'display:none' }}">
            <label for="referral_other" class="form-label d-flex align-items-center justify-content-between">{{ __('Other (please specify)') }}</label>
            <input id="referral_other" type="text" class="form-control @error('referral_other') is-invalid @enderror" name="referral_other" value="{{ old('referral_other') }}" placeholder="{{ __('Write here') }}">
            @error('referral_other')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label d-flex align-items-center justify-content-between">
                {{ __('Password') }}
                <small class="text-muted ms-2" id="pwStrengthLabel">— {{ __('Very weak') }}</small>
            </label>
            <div class="input-group">
                <input id="password" type="password" data-indicator="pwindicator" class="form-control pwstrength @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Enter Your Password') }}" required autocomplete="new-password" aria-describedby="pwHelp">
                <button type="button" class="btn btn-outline-secondary" id="togglePw" tabindex="-1" aria-label="{{ __('Show/Hide password') }}">
                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="d-none" style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M9.88 9.88A3 3 0 0012 15a3 3 0 002.12-5.12M6.23 6.23A9.956 9.956 0 0012 5c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-4.224 5.223M9.88 9.88L6.23 6.23M4.458 12A9.956 9.956 0 0112 5"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
            <div class="mt-2">
                <div class="progress pw-progress" style="height: 10px; background: #eef2f7; border-radius: 9999px; overflow: hidden;">
                    <div id="pwBar" class="progress-bar" role="progressbar" style="width: 0%; transition: width .25s ease;"></div>
                </div>
            </div>
            <div id="pwChecklist" class="row g-2 mt-3 small text-muted">
                <div class="col-6">
                    <span class="pw-req d-flex align-items-center gap-1" data-req="len">
                        <svg class="icon-pending" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><circle cx="10" cy="10" r="8"></circle></svg>
                        <svg class="icon-ok d-none" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;color:#198754" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ __('At least 12 characters') }}
                    </span>
                </div>
                <div class="col-6">
                    <span class="pw-req d-flex align-items-center gap-1" data-req="lower">
                        <svg class="icon-pending" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><circle cx="10" cy="10" r="8"></circle></svg>
                        <svg class="icon-ok d-none" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;color:#198754" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ __('Lowercase letter') }}
                    </span>
                </div>
                <div class="col-6">
                    <span class="pw-req d-flex align-items-center gap-1" data-req="upper">
                        <svg class="icon-pending" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><circle cx="10" cy="10" r="8"></circle></svg>
                        <svg class="icon-ok d-none" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;color:#198754" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ __('Uppercase letter') }}
                    </span>
                </div>
                <div class="col-6">
                    <span class="pw-req d-flex align-items-center gap-1" data-req="num">
                        <svg class="icon-pending" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><circle cx="10" cy="10" r="8"></circle></svg>
                        <svg class="icon-ok d-none" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;color:#198754" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ __('Number') }}
                    </span>
                </div>
                <div class="col-6">
                    <span class="pw-req d-flex align-items-center gap-1" data-req="sym">
                        <svg class="icon-pending" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><circle cx="10" cy="10" r="8"></circle></svg>
                        <svg class="icon-ok d-none" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;color:#198754" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ __('Symbol (!@#$… )') }}
                    </span>
                </div>
            </div>
            <div id="pwHelp" class="form-text mt-2">
                {{ __('Use a mix of upper & lower case letters, numbers, and symbols. Minimum 12 characters. “Strong” is required to register.') }}
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label d-flex align-items-center justify-content-between">{{ __('Password Confirmation') }}</label>
            <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" placeholder="{{ __('Enter Your Confirm Password') }}" required autocomplete="new-password">
            @error('password_confirmation')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-check custom-checkbox mb-3">
            <input type="checkbox" class="form-check-input" id="termsCheckbox" name="terms" required>
            <label class="form-check-label text-sm" for="termsCheckbox">
                {{ __('I agree to the ') }}
                <a href="https://leaftally.com/terms-conditions/" target="_blank">{{ __('Terms and Conditions') }}</a>
                {{ __(' and the ') }}
                <a href="https://leaftally.com/privacy-policy/" target="_blank">{{ __('Privacy Policy') }}</a>
            </label>
        </div>

        @if ($settings['recaptcha_module'] == 'yes')
            @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2-checkbox')
                <div class="form-group mb-4">
                    {!! NoCaptcha::display($settings['cust_darklayout'] == 'on' ? ['data-theme' => 'dark'] : []) !!}
                    @error('g-recaptcha-response')
                        <span class="small text-danger" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            @else
                <div class="form-group mb-4">
                    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" class="form-control">
                    @error('g-recaptcha-response')
                        <span class="error small text-danger" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            @endif
        @endif

        <div class="d-grid">
            <input type="hidden" name="ref_code" value="{{ !empty($ref) ? $ref : '' }}">
            <button type="submit" class="btn-login btn btn-primary btn-block mt-2" id="login_button">{{ __('Register') }}</button>
        </div>
        <p class="my-4 text-center">{{ __("Already' have an account?") }} <a href="{{ route('login', $lang) }}" class="text-primary">{{ __('Login') }}</a></p>
    </div>
</form>
@endsection

@push('custom-scripts')
    @if (isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'yes')
        @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2-checkbox')
            {!! NoCaptcha::renderJs() !!}
        @else
            <script src="https://www.google.com/recaptcha/api.js?render={{ $settings['google_recaptcha_key'] }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ $settings['google_recaptcha_key'] }}', { action: 'submit' }).then(function(token) {
                            var el = document.getElementById('g-recaptcha-response');
                            if (el) el.value = token;
                        });
                    });
                });
            </script>
        @endif
    @endif

    <style>
        .pw-progress .progress-bar { background-image: linear-gradient(90deg, #ff6b6b, #feca57, #48dbfb, #1dd1a1); }
        #togglePw { border-top-left-radius: 0; border-bottom-left-radius: 0; }
    </style>

    <script>
        (function () {
            const $pw   = document.getElementById('password');
            const $pw2  = document.getElementById('password_confirmation');
            const $bar  = document.getElementById('pwBar');
            const $lab  = document.getElementById('pwStrengthLabel');
            const $btn  = document.getElementById('login_button');
            const $industry = document.getElementById('industry');
            const $industryOtherWrap = document.getElementById('industry_other_wrap');
            const $industryOther = document.getElementById('industry_other');
            const $referral = document.getElementById('referral_source');
            const $referralOtherWrap = document.getElementById('referral_other_wrap');
            const $referralOther = document.getElementById('referral_other');

            const reqs  = {
                len:   { test: v => v.length >= 12 },
                lower: { test: v => /[a-z]/.test(v) },
                upper: { test: v => /[A-Z]/.test(v) },
                num:   { test: v => /[0-9]/.test(v) },
                sym:   { test: v => /[^A-Za-z0-9]/.test(v) },
            };

            const labels = ['{{ __("Very weak") }}','{{ __("Weak") }}','{{ __("Fair") }}','{{ __("Good") }}','{{ __("Strong") }}'];
            const colors = ['#dc3545','#fd7e14','#0dcaf0','#20c997','#198754'];

            function scorePassword(v) {
                let score = 0, met = 0;
                for (const key in reqs) { if (reqs[key].test(v)) met++; }
                score += met;
                if (v.length >= 16) score++;
                if (v.length >= 20) score++;
                if (/([a-zA-Z0-9])\1{2,}/.test(v)) score = Math.max(0, score - 1);
                if (/^(?:0123|1234|2345|3456|4567|5678|6789|abcd|qwer|asdf)/i.test(v)) score = Math.max(0, score - 1);
                return Math.max(0, Math.min(5, score));
            }

            function updateChecklist(v) {
                document.querySelectorAll('.pw-req').forEach(function(el){
                    const key = el.getAttribute('data-req');
                    const ok  = reqs[key].test(v);
                    const iconOk = el.querySelector('.icon-ok');
                    const iconPending = el.querySelector('.icon-pending');
                    if (ok) { iconOk.classList.remove('d-none'); iconPending.classList.add('d-none'); }
                    else { iconOk.classList.add('d-none'); iconPending.classList.remove('d-none'); }
                });
            }

            function render(v) {
                const s   = scorePassword(v);
                const pct = [0,25,50,75,100][Math.max(0, Math.min(4, s-1))] || (v ? 10 : 0);
                const idx = Math.max(0, Math.min(4, s-1));
                $bar.style.width = (s === 0 && !v) ? '0%' : pct + '%';
                $bar.style.backgroundColor = colors[idx];
                $lab.textContent = '— ' + labels[idx];
                updateChecklist(v);
                $btn.disabled = (s < 5);
            }

            document.getElementById('togglePw').addEventListener('click', function(){
                const type = $pw.getAttribute('type') === 'password' ? 'text' : 'password';
                $pw.setAttribute('type', type);
                document.getElementById('eyeOpen').classList.toggle('d-none', type === 'text');
                document.getElementById('eyeClosed').classList.toggle('d-none', type !== 'text');
            });

            ['input','change','blur','keyup'].forEach(evt => { $pw.addEventListener(evt, () => render($pw.value)); });

            if ($pw2) {
                const checkMatch = () => {
                    if ($pw2.value && $pw.value && $pw2.value !== $pw.value) { $pw2.setCustomValidity('Passwords do not match'); }
                    else { $pw2.setCustomValidity(''); }
                };
                ['input','change','blur','keyup'].forEach(evt => { $pw2.addEventListener(evt, checkMatch); $pw.addEventListener(evt, checkMatch); });
            }

            function toggleOther(selectEl, wrapEl, inputEl) {
                const isOther = selectEl && selectEl.value === 'Other';
                if (wrapEl) wrapEl.style.display = isOther ? '' : 'none';
                if (inputEl) { inputEl.required = isOther; if (!isOther) inputEl.value = ''; }
            }

            if ($industry) {
                $industry.addEventListener('change', function(){ toggleOther($industry, $industryOtherWrap, $industryOther); });
                toggleOther($industry, $industryOtherWrap, $industryOther);
            }

            if ($referral) {
                $referral.addEventListener('change', function(){ toggleOther($referral, $referralOtherWrap, $referralOther); });
                toggleOther($referral, $referralOtherWrap, $referralOther);
            }

            render('');
        })();
    </script>
@endpush
