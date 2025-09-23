<!DOCTYPE html>
@php
    use \App\Models\Utility;
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_logo = \App\Models\Utility::get_superadmin_logo();
    $seo_setting = App\Models\Utility::getSeoSetting();
    $company_favicon = Utility::getValByName('company_favicon');
    $setting = \App\Models\Utility::getLayoutsSetting();
    $currantLang = basename(App::getLocale());
    if ($currantLang == 'ar' || $currantLang == 'he') {
        $setting['SITE_RTL'] = 'on';
    }
    $settings = \App\Models\Utility::settings();

    $data = \App\Models\Utility::admin_color();

    $color_image = 'theme-3';

    $color = !empty($settings['color']) ? $settings['color'] : 'theme-3';

    if (isset($settings['color_flag']) && $settings['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }

    $SITE_RTL = 'theme-3';
    if (!empty($setting['SITE_RTL'])) {
        $SITE_RTL = $setting['SITE_RTL'];
    }
    $mode_setting = \App\Models\Utility::mode_layout();
    $set_cookie = Utility::cookies();

@endphp

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<head>
    <title>
        {{ Utility::getValByName('title_text') ? Utility::getValByName('title_text') : config('app.name', 'Accountgo') }}
        - @yield('page-title')</title>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Dashboard Template Description" />
    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="Workdo" />

    <meta name="title" content={{ $seo_setting['meta_keywords'] }}>
    <meta name="description" content={{ $seo_setting['meta_description'] }}>

    <meta property="og:type" content="website">
    <meta property="og:url" content={{ env('APP_URL') }}>
    <meta property="og:title" content={{ $seo_setting['meta_keywords'] }}>
    <meta property="og:description" content={{ $seo_setting['meta_description'] }}>
    <meta property="og:image" content={{ asset('/' . $seo_setting['meta_image']) }}>

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content={{ env('APP_URL') }}>
    <meta property="twitter:title" content={{ $seo_setting['meta_keywords'] }}>
    <meta property="twitter:description" content={{ $seo_setting['meta_description'] }}>
    <meta property="twitter:image"
        content={{ asset(Storage::url('uploads/metaevent/' . $seo_setting['meta_image'])) }}>
    <style>
        :root {
            --color-customColor: <?=$color ?>;

            --zameen-primary:
            --zameen-primary-light:
            --zameen-primary-dark:
            --zameen-primary-lighter:
            --zameen-secondary:
            --zameen-secondary-light:
            --zameen-text-dark:
            --zameen-text-medium:
            --zameen-text-light:
            --zameen-background:
            --zameen-background-alt:
            --zameen-background-section:
            --zameen-border:
            --zameen-border-medium:
            --zameen-success:
            --zameen-warning:
            --zameen-danger:
            --zameen-info:
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Helvetica Neue', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: var(--zameen-text-dark);
            background: linear-gradient(135deg, var(--zameen-primary-lighter) 0%, var(--zameen-background) 100%);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: inherit;
            font-weight: 600;
            color: var(--zameen-text-dark);
            margin-bottom: 0.5rem;
        }

        h1 { font-size: 2.5rem; font-weight: 700; }
        h2 { font-size: 2rem; font-weight: 600; }
        h3 { font-size: 1.75rem; font-weight: 600; }
        h4 { font-size: 1.5rem; font-weight: 600; }
        h5 { font-size: 1.25rem; font-weight: 600; }
        h6 { font-size: 1rem; font-weight: 600; }

        .bg-primary, .btn-primary {
            background-color: var(--zameen-primary) !important;
            border-color: var(--zameen-primary) !important;
        }

        .bg-primary:hover, .btn-primary:hover {
            background-color: var(--zameen-primary-dark) !important;
            border-color: var(--zameen-primary-dark) !important;
        }

        .text-primary {
            color: var(--zameen-primary) !important;
        }

        .border-primary {
            border-color: var(--zameen-primary) !important;
        }
        }

        .text-primary {
            color: var(--zameen-primary) !important;
        }

        .border-primary {
            border-color: var(--zameen-primary) !important;
        }

        .custom-login .card {
            background: var(--zameen-background);
            border: 1px solid var(--zameen-border);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 185, 141, 0.15);
        }

        .custom-login-inner {
            background: linear-gradient(135deg, var(--zameen-background) 0%, var(--zameen-background-alt) 100%);
        }

        .form-control {
            border-color: var(--zameen-border);
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: var(--zameen-primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 185, 141, 0.25);
        }

        a {
            color: var(--zameen-primary);
        }

        a:hover {
            color: var(--zameen-primary-dark);
        }

        .auth-footer {
            background: var(--zameen-background-alt);
            border-top: 1px solid var(--zameen-border);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    
    <link rel="icon"
        href="{{ $logo . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') }}"
        type="image" sizes="16x16">

    @if ($setting['cust_darklayout'] == 'on')
        @if (isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
        @endif
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
    @else
        @if (isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
        @else
            <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
        @endif
    @endif

    @if (isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-login-rtl.css') }}" id="main-style-link">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/custom-login.css') }}" id="main-style-link">
    @endif
    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-login-dark.css') }}" id="main-style-link">
        <script>
            document.addEventListener('DOMContentLoaded', (event) => {
                const recaptcha = document.querySelector('.g-recaptcha');
                recaptcha.setAttribute("data-theme", "dark");
            });
        </script>
    @endif
</head>

<body class="{{ $themeColor }}">
    
    <div class="custom-login">
        <div class="login-bg-img">
        </div>
        <div class="custom-login-inner">
              <div class="flex items-center justify-between">
                <div class="d-flex justify-content-center items-center gap-2">
                <img src="{{ asset('web-assets/dashboard/icons/logo.svg') }}" alt="">
                <h1 class="h3 fw-medium text-black mb-0">LeafTally</h1>
                </div>
              </div>
            <main class="custom-wrapper">
                <div class="custom-row justify-content-center">
                    <div class="card">
                        <div class="card-body">
                            @yield('content')
                        </div>
                    </div>

                </div>
            </main>
            <footer>
                <div class="auth-footer">
                    <div class="container">
                        <div class="row">
                            <ul class="d-flex list-unstyled w-100 justify-content-center">
                                <li class="list-group-item mx-2"><a href="https://leaftally.com/privacy-policy/" target="_blank"><span>Privacy Policy</span></a></li>
                                <li class="list-group-item mx-2"><a href="https://leaftally.com/terms-conditions/" target="_blank"><span>Terms & Conditions</span></a></li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <span>{{ __('©') }} {{ date('Y') }}
                                    {{ Utility::getValByName('footer_text') ? Utility::getValByName('footer_text') : config('app.name', 'AccountGo') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
        <div id="liveToast" class="toast text-white  fade" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    @if ($set_cookie['enable_cookie'] == 'on')
        @include('layouts.cookie_consent')
    @endif

    <script src="{{ asset('assets/js/vendor-all.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>

    <script>
        feather.replace();
    </script>
    <script>
        feather.replace();
        var pctoggle = document.querySelector("#pct-toggler");
        if (pctoggle) {
            pctoggle.addEventListener("click", function() {
                if (
                    !document.querySelector(".pct-customizer").classList.contains("active")
                ) {
                    document.querySelector(".pct-customizer").classList.add("active");
                } else {
                    document.querySelector(".pct-customizer").classList.remove("active");
                }
            });
        }

        var themescolors = document.querySelectorAll(".themes-color > a");
        for (var h = 0; h < themescolors.length; h++) {
            var c = themescolors[h];

            c.addEventListener("click", function(event) {
                var targetElement = event.target;
                if (targetElement.tagName == "SPAN") {
                    targetElement = targetElement.parentNode;
                }
                var temp = targetElement.getAttribute("data-value");
                removeClassByPrefix(document.querySelector("body"), "theme-");
                document.querySelector("body").classList.add(temp);
            });
        }

        var custthemebg = document.querySelector("#cust-theme-bg");
        custthemebg.addEventListener("click", function() {
            if (custthemebg.checked) {
                document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.add("transprent-bg");
            } else {
                document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.remove("transprent-bg");
            }
        });

        var custdarklayout = document.querySelector("#cust-darklayout");
        custdarklayout.addEventListener("click", function() {
            if (custdarklayout.checked) {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{ asset('assets/images/logo.svg') }}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
            } else {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{ asset('assets/images/logo-dark.svg') }}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style.css') }}");
            }
        });

        function removeClassByPrefix(node, prefix) {
            for (let i = 0; i < node.classList.length; i++) {
                let value = node.classList[i];
                if (value.startsWith(prefix)) {
                    node.classList.remove(value);
                }
            }
        }
    </script>

    @stack('custom-scripts')
    <script>
        validation();
    </script>
</body>

</html>
