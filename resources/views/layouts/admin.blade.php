@php
    use \App\Models\Utility;
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_favicon = Utility::getValByName('company_favicon');
    $SITE_RTL = env('SITE_RTL');
    $seo_setting = App\Models\Utility::getSeoSetting();
    $setting = \App\Models\Utility::getLayoutsSetting();
    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';
    $settings = \App\Models\Utility::settings();
    if(isset($settings['color_flag']) && $settings['color_flag'] == 'true')
    {
        $themeColor = 'custom-color';
    }
    else {
        $themeColor = $color;
    }
    $SITE_RTL = 'theme-3';
    if (!empty($setting['SITE_RTL'])) {
        $SITE_RTL = $setting['SITE_RTL'];
    }

    $mode_setting = \App\Models\Utility::mode_layout();
    $set_cookie = Utility::cookies();
@endphp

<!DOCTYPE html>

<html lang="en" dir="{{ $SITE_RTL == 'on' ? 'rtl' : '' }}">

<head>
    <title>
        {{ Utility::getValByName('title_text') ? Utility::getValByName('title_text') : config('app.name', 'AccountGo SaaS') }}
        - @yield('page-title')</title>
    <script src="{{ asset('js/html5shiv.js') }}"></script>
    <script src="{{ asset('js/respond.min.js') }}"></script>
    
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="url" content="{{ url('') . '/' . config('chatify.path') }}" data-user="{{ Auth::user()->id }}">

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    
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
    <link rel="icon"
        href="{{ !empty($company_favicon) ? \App\Models\Utility::get_file('uploads/logo/' . $company_favicon) : asset(Storage::url('uploads/logo/favicon.png')) }}"
        type="image" sizes="16x16">
    
    {{-- @if (\Auth::user()->type == 'owner')

        <link rel="icon" href="{{$logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')}}" type="image" sizes="16x16">
    @else
        <link rel="icon" href="{{$logo.'/'.(isset($favicon) && !empty($favicon)? $favicon:'favicon.png')}}" type="image" sizes="16x16">

    @endif --}}
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/animate.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/datepicker-bs5.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">

    @if ($SITE_RTL == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @endif
    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}" id="style">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="style">
    @endif

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" id="main-style-link">
    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('css/custom-dark.css') }}" id="style">
    @endif

    @if ($SITE_RTL == 'on')
        <link rel="stylesheet" href="{{ asset('css/custom-rtl.css') }}">
    @endif

    <style>
        :root {
            --color-customColor: <?= $color ?>;

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

            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15);

            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Helvetica Neue', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: var(--zameen-text-dark);
            background: var(--zameen-background-section);
            font-weight: 400;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: inherit;
            font-weight: 600;
            color: var(--zameen-text-dark);
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }

        h1 { font-size: 2.25rem; font-weight: 700; }
        h2 { font-size: 1.875rem; font-weight: 650; }
        h3 { font-size: 1.5rem; font-weight: 600; }
        h4 { font-size: 1.25rem; font-weight: 600; }
        h5 { font-size: 1.125rem; font-weight: 600; }
        h6 { font-size: 1rem; font-weight: 600; }

        .dash-sidebar {
            background: linear-gradient(180deg,
            border-right: 1px solid var(--zameen-border);
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dash-sidebar .navbar-wrapper {
            padding: 1rem 0;
        }

        .dash-navbar .dash-item {
            margin: 0.25rem 0.75rem;
            border-radius: var(--radius-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dash-navbar .dash-item a {
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            color: var(--zameen-text-medium);
            font-weight: 500;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .dash-navbar .dash-item a:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--zameen-primary);
            transform: scaleY(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dash-navbar .dash-item.active a:before {
            transform: scaleY(1);
        }

        .dash-navbar .dash-item a:hover {
            background: linear-gradient(135deg, var(--zameen-primary-lighter) 0%, rgba(0, 185, 141, 0.08) 100%);
            color: var(--zameen-primary-dark);
            transform: translateX(4px);
            box-shadow: var(--shadow-sm);
        }

        .dash-navbar .dash-item.active a {
            background: linear-gradient(135deg, var(--zameen-primary) 0%, var(--zameen-primary-dark) 100%);
            color: white;
            font-weight: 600;
            box-shadow: var(--shadow-md);
        }

        .dash-navbar .dash-item.active a:hover {
            background: linear-gradient(135deg, var(--zameen-primary-dark) 0%,
            transform: translateX(2px);
        }

        .dash-header {
            background: linear-gradient(135deg,
            border-bottom: 1px solid var(--zameen-border);
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(10px);
            padding: 0 1.5rem;
        }

        .dash-header .header-wrapper {
            padding: 1rem 0;
        }

        .card {
            background: white;
            border: 1px solid var(--zameen-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
            border-color: var(--zameen-primary-lighter);
        }

        .card-header {
            background: linear-gradient(135deg, var(--zameen-background) 0%, var(--zameen-background-alt) 100%);
            border-bottom: 1px solid var(--zameen-border);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--zameen-text-dark);
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn {
            border-radius: var(--radius-md);
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--zameen-primary) 0%, var(--zameen-primary-dark) 100%);
            border-color: var(--zameen-primary);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--zameen-primary-dark) 0%,
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            color: white;
        }

        .btn-secondary {
            background: white;
            border-color: var(--zameen-border-medium);
            color: var(--zameen-text-medium);
        }

        .btn-secondary:hover {
            background: var(--zameen-background-alt);
            border-color: var(--zameen-primary);
            color: var(--zameen-primary);
            transform: translateY(-1px);
        }

        .form-control {
            border: 1px solid var(--zameen-border-medium);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            font-size: 0.875rem;
        }

        .form-control:focus {
            border-color: var(--zameen-primary);
            box-shadow: 0 0 0 3px rgba(0, 185, 141, 0.1);
            outline: none;
        }

        .zameen-form-group {
            margin-bottom: 1.5rem;
        }

        .zameen-form-label {
            display: block;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--zameen-text-dark);
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .zameen-form-input,
        .zameen-form-select {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1px solid var(--zameen-border-medium);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            color: var(--zameen-text-dark);
            background: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .zameen-form-input:focus,
        .zameen-form-select:focus {
            outline: none;
            border-color: var(--zameen-primary);
            box-shadow: 0 0 0 3px rgba(0, 185, 141, 0.08);
            transform: translateY(-1px);
        }

        .zameen-form-input::placeholder {
            color: var(--zameen-text-light);
        }

        .zameen-form-error {
            display: block;
            color: var(--zameen-danger);
            font-size: 0.75rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }

        .zameen-file-upload {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .zameen-file-upload.compact {
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }

        .zameen-avatar-preview {
            position: relative;
            cursor: pointer;
        }

        .zameen-avatar-preview.small {
            width: 60px;
            height: 60px;
        }

        .zameen-avatar-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--zameen-border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .zameen-avatar-preview.small .zameen-avatar-img {
            width: 60px;
            height: 60px;
        }

        .zameen-avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 185, 141, 0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: white;
        }

        .zameen-avatar-preview:hover .zameen-avatar-overlay {
            opacity: 1;
        }

        .zameen-file-input {
            display: none;
        }

        .zameen-file-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: var(--zameen-primary);
            color: white;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
        }

        .zameen-file-label.compact {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
        }

        .zameen-file-label:hover {
            background: var(--zameen-primary-dark);
            transform: translateY(-1px);
        }

        .zameen-file-info {
            flex: 1;
        }

        .zameen-file-hint {
            font-size: 0.75rem;
            color: var(--zameen-text-light);
            margin: 0.25rem 0 0 0;
            line-height: 1.4;
        }

        .zameen-toggle-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: var(--zameen-background-alt);
            border-radius: var(--radius-md);
            border: 1px solid var(--zameen-border);
        }

        .zameen-toggle-wrapper {
            position: relative;
        }

        .zameen-toggle-input {
            display: none;
        }

        .zameen-toggle-label {
            display: block;
            width: 48px;
            height: 24px;
            background:
            border-radius: 12px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .zameen-toggle-slider {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .zameen-toggle-input:checked + .zameen-toggle-label {
            background: var(--zameen-primary);
        }

        .zameen-toggle-input:checked + .zameen-toggle-label .zameen-toggle-slider {
            transform: translateX(24px);
        }

        .zameen-form-section {
            padding-top: 1.5rem;
            border-top: 1px solid var(--zameen-border);
        }

        .zameen-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--zameen-text-dark);
            margin-bottom: 1rem;
        }

        .zameen-custom-fields {
            background: var(--zameen-background-alt);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--zameen-border);
        }

        .zameen-modal-footer {
            background: linear-gradient(135deg,
            border-top: 1px solid var(--zameen-border);
            padding: 1.5rem 2.5rem;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .zameen-btn-primary {
            background: linear-gradient(135deg, var(--zameen-primary) 0%, var(--zameen-primary-light) 100%);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .zameen-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: white;
        }

        .zameen-btn-secondary {
            background: white;
            border: 1px solid var(--zameen-border-medium);
            color: var(--zameen-text-medium);
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .zameen-btn-secondary:hover {
            background: var(--zameen-background-alt);
            border-color: var(--zameen-primary);
            color: var(--zameen-primary);
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .zameen-file-upload {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .zameen-toggle-group {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .zameen-modal-footer {
                padding: 1rem;
                flex-direction: column;
            }
        }

        .stats-card {
            background: linear-gradient(135deg, white 0%,
            border: 1px solid var(--zameen-border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stats-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--zameen-primary) 0%, var(--zameen-secondary) 100%);
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .dash-container {
            background: transparent;
        }

        .dash-content {
            padding: 1.5rem;
            background: transparent;
        }

        .page-header {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--zameen-border);
        }

        .content-wrapper {
            display: grid;
            gap: 1.5rem;
        }

        .zameen-tabs {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--zameen-border);
            overflow: hidden;
        }

        .zameen-tab-nav {
            display: flex;
            background: linear-gradient(135deg, var(--zameen-background-alt) 0%,
            border-bottom: 1px solid var(--zameen-border);
            padding: 0;
            margin: 0;
            list-style: none;
            overflow-x: auto;
        }

        .zameen-tab-nav::-webkit-scrollbar {
            height: 2px;
        }

        .zameen-tab-nav::-webkit-scrollbar-thumb {
            background: var(--zameen-primary);
            border-radius: 1px;
        }

        .zameen-tab-item {
            flex: 0 0 auto;
        }

        .zameen-tab-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            color: var(--zameen-text-medium);
            font-weight: 500;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 3px solid transparent;
            position: relative;
            white-space: nowrap;
        }

        .zameen-tab-link:hover {
            color: var(--zameen-primary);
            background: rgba(0, 185, 141, 0.05);
        }

        .zameen-tab-link.active {
            color: var(--zameen-primary);
            border-bottom-color: var(--zameen-primary);
            background: white;
            font-weight: 600;
        }

        .zameen-tab-link.active:before {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--zameen-primary) 0%, var(--zameen-primary-light) 100%);
            border-radius: 2px 2px 0 0;
        }

        .zameen-tab-content {
            padding: 2rem;
        }

        .zameen-tab-pane {
            display: none;
            animation: fadeInUp 0.4s ease-out;
        }

        .zameen-tab-pane.active {
            display: block;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .zameen-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .zameen-stat-card {
            background: white;
            border: 1px solid var(--zameen-border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .zameen-stat-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--zameen-primary) 0%, var(--zameen-secondary) 100%);
        }

        .zameen-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .zameen-stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .zameen-stat-title {
            color: var(--zameen-text-medium);
            font-size: 0.875rem;
            font-weight: 500;
            margin: 0;
        }

        .zameen-stat-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--zameen-primary-lighter) 0%, rgba(0, 185, 141, 0.1) 100%);
            color: var(--zameen-primary);
        }

        .zameen-stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--zameen-text-dark);
            margin: 0.5rem 0;
        }

        .zameen-stat-change {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .zameen-stat-change.positive {
            color: var(--zameen-success);
        }

        .zameen-stat-change.negative {
            color: var(--zameen-danger);
        }

        .zameen-chart-container {
            background: white;
            border: 1px solid var(--zameen-border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            height: 400px;
        }

        @media (max-width: 768px) {
            .zameen-tab-nav {
                flex-wrap: nowrap;
                overflow-x: auto;
            }

            .zameen-tab-link {
                padding: 0.75rem 1rem;
                font-size: 0.8rem;
            }

            .zameen-tab-content {
                padding: 1.5rem;
            }

            .zameen-stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--zameen-background-alt);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--zameen-border-medium);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--zameen-primary);
        }

        input[type="file"] {
            border: 1px solid var(--zameen-border-medium);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            background-color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        input[type="file"]:hover {
            border-color: var(--zameen-primary);
            background-color: var(--zameen-primary-lighter);
        }

        h3.title-of-dashboard {
            font-size: 12px;
            background: linear-gradient(135deg, var(--zameen-primary) 0%, var(--zameen-primary-dark) 100%);
            color:
            border-radius: var(--radius-md);
            padding: 0.5rem 1rem;
            margin: 1rem 0.75rem 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-locked {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .nav-locked i {
            font-size: 0.9rem;
            margin-left: 0.25rem;
        }

        [dir="rtl"] .dash-sidebar {
            left: auto !important;
        }

        [dir="rtl"] .dash-header {
            left: 0;
            right: 280px;
        }

        [dir="rtl"] .dash-header:not(.transprent-bg) .header-wrapper {
            padding: 0 0 0 30px;
        }

        [dir="rtl"] .dash-header:not(.transprent-bg):not(.dash-mob-header)~.dash-container {
            margin-left: 0px !important;
        }

        [dir="rtl"] .me-auto.dash-mob-drp {
            margin-right: 10px !important;
        }

        [dir="rtl"] .me-auto {
            margin-left: 10px !important;
        }

        .badge {
            border-radius: var(--radius-sm);
            font-weight: 500;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .badge-primary {
            background: var(--zameen-primary);
            color: white;
        }

        .badge-success {
            background: var(--zameen-success);
            color: white;
        }

        .table {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table thead th {
            background: var(--zameen-background-alt);
            border-bottom: 1px solid var(--zameen-border);
            font-weight: 600;
            color: var(--zameen-text-dark);
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--zameen-border);
        }

        .table tbody tr:hover {
            background: var(--zameen-primary-lighter);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-standardization.css') }}">
    @stack('css-page')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileButtons = document.querySelectorAll('button[onclick*="choose"], button[onclick*="file"], .btn[onclick*="choose"], .btn[onclick*="file"], input[type="file"] + .btn, label[for*="file"]');

            fileButtons.forEach(button => {
                button.style.background = 'linear-gradient(135deg, var(--zameen-primary) 0%, var(--zameen-primary-dark) 100%)';
                button.style.borderColor = 'var(--zameen-primary)';
                button.style.color = 'white';
                button.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                button.style.borderRadius = 'var(--radius-md)';
                button.style.fontWeight = '500';

                button.addEventListener('mouseenter', function() {
                    this.style.background = 'linear-gradient(135deg, var(--zameen-primary-dark) 0%, #006b52 100%)';
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 10px 15px rgba(0, 185, 141, 0.3)';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.background = 'linear-gradient(135deg, var(--zameen-primary) 0%, var(--zameen-primary-dark) 100%)';
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 4px 6px rgba(0, 185, 141, 0.2)';
                });
            });

            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                    this.style.boxShadow = 'var(--shadow-xl)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'var(--shadow-sm)';
                });
            });

            function initZameenTabs() {
                const tabLinks = document.querySelectorAll('.zameen-tab-link');
                const tabPanes = document.querySelectorAll('.zameen-tab-pane');

                tabLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();

                        tabLinks.forEach(l => l.classList.remove('active'));
                        tabPanes.forEach(p => p.classList.remove('active'));

                        this.classList.add('active');

                        const targetId = this.getAttribute('data-tab');
                        const targetPane = document.getElementById(targetId);
                        if (targetPane) {
                            targetPane.classList.add('active');
                        }

                        targetPane.style.animation = 'none';
                        setTimeout(() => {
                            targetPane.style.animation = 'fadeInUp 0.4s ease-out';
                        }, 10);
                    });
                });

                const firstTab = document.querySelector('.zameen-tab-link');
                const activeTab = document.querySelector('.zameen-tab-link.active');
                if (firstTab && !activeTab) {
                    firstTab.click();
                }
            }

            initZameenTabs();

            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length > 0) {
                        initZameenTabs();
                    }
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            document.documentElement.style.scrollBehavior = 'smooth';

            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(255, 255, 255, 0.5)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                    ripple.style.pointerEvents = 'none';

                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-standardization.css') }}">
    @stack('css-page')

      <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
  <script src="https://unpkg.com/lucide@latest" defer></script>
    <script>
            document.addEventListener("DOMContentLoaded", () => {
            lucide.createIcons();
        });
    </script>
</head>

<body class="{{ $themeColor }}">
    
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    @include('partials.admin.menu')

    @include('partials.admin.header')

    <div class="modal notification-modal fade" id="notification-modal" tabindex="-1" role="dialog" aria-hidden="true">

        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close float-end" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <h6 class="mt-2">
                        <i data-feather="monitor" class="me-2"></i>Desktop settings
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting1" checked />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting1">Allow desktop
                            notification</label>
                    </div>
                    <p class="text-muted ms-5">
                        you get lettest content at a time when data will updated
                    </p>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting2" />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting2">Store Cookie</label>
                    </div>
                    <h6 class="mb-0 mt-5">
                        <i data-feather="save" class="me-2"></i>Application settings
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting3" />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting3">Backup Storage</label>
                    </div>
                    <p class="text-muted mb-4 ms-5">
                        Automatically take backup as per schedule
                    </p>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting4" />cookie
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting4">Allow guest to print
                            file</label>
                    </div>
                    <h6 class="mb-0 mt-5">
                        <i data-feather="cpu" class="me-2"></i>System settings
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting5" checked />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting5">View other user chat</label>
                    </div>
                    <p class="text-muted ms-5">Allow to show public user message</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-danger btn-sm" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-light-primary btn-sm">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="dash-container">
        <div class="dash-content">
            
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="page-header-title">
                                <h4 class="m-b-10" style="color: var(--zameen-text-dark); font-weight: 600;">@yield('page-title')</h4>
                            </div>
                            <ul class="breadcrumb" style="margin: 0; padding: 0; background: transparent;">
                                @yield('breadcrumb')
                            </ul>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            @yield('action-btn')
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                @yield('content')
            </div>
            
        </div>
    </div>
    <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title font-[700] text-[16px] text-black leading-[24px]" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="body">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="commonModalOver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title font-[700] text-[16px] text-black leading-[24px]" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
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
    @include('partials.admin.footer')
</body>
@if ($set_cookie['enable_cookie'] == 'on')
    @include('layouts.cookie_consent')
@endif
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/68c110bd0e49f131d3b57e5d/1j4p2ipoo';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const nav = document.querySelector('nav.dash-sidebar');
  if (!nav) return;

  function isScrollable(el){
    const cs = getComputedStyle(el);
    const canScrollY = /(auto|scroll)/.test(cs.overflowY);
    return (el.scrollHeight - el.clientHeight) > 5 && (canScrollY || cs.overflowY !== 'visible');
  }

  const all = [nav, ...nav.querySelectorAll('*')];
  const candidates = all.filter(isScrollable);

  let scroller = nav, bestScore = -1, bestDepth = -1;
  for (const el of candidates){
    const score = el.scrollHeight - el.clientHeight;
    let depth = 0, p = el;
    while (p && p !== nav){ depth++; p = p.parentElement; }
    if (score > bestScore || (score === bestScore && depth > bestDepth)){
      bestScore = score; bestDepth = depth; scroller = el;
    }
  }

  scroller.scrollTop = scroller.scrollHeight;

  ['.navbar-wrapper', '.navbar-content', '.dash-navbar'].forEach(sel => {
    const el = nav.querySelector(sel);
    if (el && el.scrollHeight > el.clientHeight) el.scrollTop = el.scrollHeight;
  });

  const active = nav.querySelector('.dash-item.active');
  function centerActive(){
    if (!active) return;
    const aRect = active.getBoundingClientRect();
    const cRect = scroller.getBoundingClientRect();
    const targetTop = scroller.scrollTop + (aRect.top - cRect.top) - (cRect.height/2 - aRect.height/2);
    scroller.scrollTop = Math.max(0, Math.min(targetTop, scroller.scrollHeight));
  }

  requestAnimationFrame(centerActive);
  setTimeout(centerActive, 120);
  window.addEventListener('load', centerActive);
});
</script>
<script>
$(document).ready(function () {
    function setDefaultValue(input) {
        if (!$(input).val().trim()) {
            $(input).val("0.00");
        }
    }

    function applyDefaults(root) {
        $(root).find('input[name="sale_price"], input[name="purchase_price"]').each(function () {
            setDefaultValue(this);
        });
    }

    applyDefaults(document);

    $(document).on('change blur', 'input[name="sale_price"], input[name="purchase_price"]', function () {
        setDefaultValue(this);
    });

    $(document).on('shown.bs.modal', '.modal', function () {
        applyDefaults(this);
    });

    $(document).ajaxComplete(function () {
        applyDefaults(document);
    });

    new MutationObserver(function (muts) {
        muts.forEach(function (m) {
            m.addedNodes && m.addedNodes.forEach(function (n) {
                if (n.nodeType === 1) applyDefaults(n);
            });
        });
    }).observe(document.body, { childList: true, subtree: true });
});
</script>

<script>
  $(document).on('click', '#billing_data', function() {
      $("[name='shipping_name']").val($("[name='billing_name']").val());
      $("[name='shipping_country']").val($("[name='billing_country']").val());
      $("[name='shipping_state']").val($("[name='billing_state']").val());
      $("[name='shipping_city']").val($("[name='billing_city']").val());
      $("[name='shipping_phone']").val($("[name='billing_phone']").val());
      $("[name='shipping_zip']").val($("[name='billing_zip']").val());
      $("[name='shipping_address']").val($("[name='billing_address']").val());
  });
     $(document).on('change', '#password_switch', function() {
       if ($(this).is(':checked')) {
           $('.ps_div').removeClass('d-none');
           $('#password').attr("required", true);
           $('#user_name').attr("required", true);

       } else {
           $('.ps_div').addClass('d-none');
           $('#password').val(null);
           $('#user_name').val(null);
           $('#password').removeAttr("required");
           $('#user_name').removeAttr("required");
       }
   });

  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){ e.stopPropagation(); });
  $(document).on('click', 'tr.cust_tr', function(){ const url=$(this).data('url'); if(url) window.location=url; });

  $(document).on('click','[data-export-selected][data-scope="customers"]',function(e){
    e.preventDefault();
    const scope = $(this).data('scope');
    const route = $(this).data('route');
    const key   = 'bulk:'+scope;
    let ids = [];
    try { ids = JSON.parse(localStorage.getItem(key) || '[]'); } catch(e) {}

    if(!ids.length){
      Swal.fire({ icon:'info', title:'{{ __('No selection') }}', text:'{{ __('Please select at least one row.') }}' });
      return;
    }
    const token = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
    const $f = $('<form>', { method:'POST', action:route, target:'_blank' });
    $f.append($('<input>',{type:'hidden', name:'_token', value:token}));
    ids.forEach(id => $f.append($('<input>',{type:'hidden', name:'ids[]', value:id})));
    $(document.body).append($f);
    $f.trigger('submit').remove();
  });

</script>
</html>
