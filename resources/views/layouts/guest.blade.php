<!DOCTYPE html>
@php
    use \App\Models\Utility;
    $logo=asset(Storage::url('uploads/logo/'));
    $company_favicon=App\Models\Utility::getValByName('company_favicon');
    $seo_setting = App\Models\Utility::getSeoSetting();
@endphp
<html lang="en"  dir="{{env('SITE_RTL') == 'on'?'rtl':''}}">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

    <meta name="title" content={{$seo_setting['meta_keywords']}}>
    <meta name="description" content={{$seo_setting['meta_description']}}>

    <meta property="og:type" content="website">
    <meta property="og:url" content={{env('APP_URL')}}>
    <meta property="og:title" content={{$seo_setting['meta_keywords']}}>
    <meta property="og:description" content={{$seo_setting['meta_description']}}>
    <meta property="og:image" content={{asset(Storage::url('uploads/metaevent/'.$seo_setting['meta_image']))}}>

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content={{env('APP_URL')}}>
    <meta property="twitter:title" content={{$seo_setting['meta_keywords']}}>
    <meta property="twitter:description" content={{$seo_setting['meta_description']}}>
    <meta property="twitter:image" content={{asset(Storage::url('uploads/metaevent/'.$seo_setting['meta_image']))}}>

    <title>{{(App\Models\Utility::getValByName('title_text')) ? App\Models\Utility::getValByName('title_text') : config('app.name', 'AccountGo')}} - @yield('page-title')</title>
    <link rel="icon" href="{{ !empty($company_favicon) ? \App\Models\Utility::get_file('uploads/logo/' . $company_favicon) : asset(Storage::url('uploads/logo/favicon.png')) }}" type="image" sizes="16x16">

    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/libs/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ac.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/stylesheet.css') }}">

    <style>
        :root {
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
            background: var(--zameen-background-alt);
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

        .card {
            background: var(--zameen-background);
            border: 1px solid var(--zameen-border);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 185, 141, 0.1);
        }

        .form-control {
            border-color: var(--zameen-border);
            border-radius: 6px;
        }

        .form-control:focus {
            border-color: var(--zameen-primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 185, 141, 0.25);
        }

        a {
            color: var(--zameen-primary);
            text-decoration: none;
        }

        a:hover {
            color: var(--zameen-primary-dark);
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .select2-container--default .select2-selection--single {
            border-color: var(--zameen-border);
            border-radius: 6px;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: var(--zameen-primary);
        }
    </style>

    @if(env('SITE_RTL')=='on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
</head>

<body>
@yield('content')

<script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/nicescroll/jquery.nicescroll.min.js')}} "></script>
<script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>

<script>
    var dataTabelLang = {
        paginate: {previous: "{{__('Previous')}}", next: "{{__('Next')}}"},
        lengthMenu: "{{__('Show')}} _MENU_ {{__('entries')}}",
        zeroRecords: "{{__('No data available in table')}}",
        info: "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
        infoEmpty: " ",
        search: "{{__('Search:')}}"
    }
</script>
<script src="{{ asset('assets/js/custom.js')}}"></script>
</body>
</html>
