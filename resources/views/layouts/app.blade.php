@php
    $seo_setting = App\Models\Utility::getSeoSetting();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta name="title" content={{$seo_setting['meta_keywords']}}>
        <meta name="description" content={{$seo_setting['meta_description']}}>

        <meta property="og:type" content="website">
        <meta property="og:url" content={{env('APP_URL')}}>
        <meta property="og:title" content={{$seo_setting['meta_keywords']}}>
        <meta property="og:description" content={{$seo_setting['meta_description']}}>
        <meta property="og:image" content={{asset('/'.$seo_setting['meta_image'])}}>

        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content={{env('APP_URL')}}>
        <meta property="twitter:title" content={{$seo_setting['meta_keywords']}}>
        <meta property="twitter:description" content={{$seo_setting['meta_description']}}>
        <meta property="twitter:image" content={{asset(Storage::url('uploads/metaevent/'.$seo_setting['meta_image']))}}>
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
  <script src="https://unpkg.com/lucide@latest" defer></script>

        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

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
                color: var(--zameen-text-dark);
                background: var(--zameen-background-alt);
            }

            h1, h2, h3, h4, h5, h6 {
                font-family: inherit;
                font-weight: 600;
                color: var(--zameen-text-dark);
            }

            .bg-gray-100 {
                background-color: var(--zameen-background-alt) !important;
            }

            .bg-white {
                background-color: var(--zameen-background) !important;
            }

            .text-gray-900 {
                color: var(--zameen-text-dark) !important;
            }

            .text-gray-600 {
                color: var(--zameen-text-medium) !important;
            }

            .text-gray-500 {
                color: var(--zameen-text-light) !important;
            }

            .bg-indigo-500, .bg-blue-500 {
                background-color: var(--zameen-primary) !important;
            }

            .bg-indigo-600, .bg-blue-600 {
                background-color: var(--zameen-primary-dark) !important;
            }

            .text-indigo-600, .text-blue-600 {
                color: var(--zameen-primary) !important;
            }

            .border-indigo-500, .border-blue-500 {
                border-color: var(--zameen-primary) !important;
            }

            .focus\:ring-indigo-500:focus, .focus\:ring-blue-500:focus {
                --tw-ring-color: rgba(0, 185, 141, 0.5) !important;
            }

            .focus\:border-indigo-500:focus, .focus\:border-blue-500:focus {
                border-color: var(--zameen-primary) !important;
            }

            header.shadow {
                background: var(--zameen-background);
                border-bottom: 1px solid var(--zameen-border);
                box-shadow: 0 1px 3px 0 rgba(0, 185, 141, 0.1);
            }

            a {
                color: var(--zameen-primary);
            }

            a:hover {
                color: var(--zameen-primary-dark);
            }
        </style>

        <script src="{{ asset('js/app.js') }}" defer></script>
        <script>
        document.addEventListener("DOMContentLoaded", () => {
            lucide.createIcons();
        });
    </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>

            <main>
                {{ $slot }}
            </main>

        </div>
    </body>
</html>
