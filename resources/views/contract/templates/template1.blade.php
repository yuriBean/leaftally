@php
    use App\Models\Utility;
    $logo = asset(\Storage::url('uploads/logo/'));
    $dark_logo = Utility::getValByName('company_logo_dark');
    $img = isset($dark_logo) && !empty($dark_logo) ? asset($logo . '/' . $dark_logo) . '?v=' . time() : '';
    $settings = Utility::settings();
    $color = !empty($settings['theme_color']) ? $settings['theme_color'] : '#6676ef';
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $settings['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;600;700&display=swap"
        rel="stylesheet">

    <style type="text/css">
        :root {
            --theme-color: {{ $color }};
            --theme-light: {{ $color }}20;
            --theme-gradient: linear-gradient(135deg, {{ $color }} 0%, {{ $color }}DD 100%);
            --white:
            --black:
            --gray-50:
            --gray-100:
            --gray-200:
            --gray-300:
            --gray-600:
            --gray-800:
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: var(--gray-800);
            background: linear-gradient(135deg,
            min-height: 100vh;
            padding: 20px 0;
        }

        p,
        li,
        ul,
        ol {
            margin: 0;
            padding: 0;
            list-style: none;
            line-height: 1.5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr th {
            padding: 0.75rem;
            text-align: left;
        }

        table tr td {
            padding: 0.75rem;
            text-align: left;
        }

        table th small {
            display: block;
            font-size: 12px;
        }

        .contract-preview-main {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            background: var(--white);
            overflow: hidden;
            box-shadow: var(--shadow-xl);
            position: relative;
        }

        .contract-preview-main::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 6px;
            height: 100%;
            background: var(--theme-gradient);
        }

        .contract-logo {
            max-width: 200px;
            width: 100%;
        }

        .contract-header {
            background: var(--theme-gradient);
            color: var(--white);
            position: relative;
            overflow: hidden;
        }

        .contract-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .contract-header table td {
            padding: 10px 20px;
            position: relative;
            z-index: 1;
        }

        .text-right {
            text-align: right;
        }

        .no-space tr td {
            padding: 0;
        }

        .vertical-align-top td {
            vertical-align: top;
        }

        .contract-body {
            padding: 40px;
        }

        .info-section {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 32px;
            border-left: 4px solid var(--theme-color);
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--theme-color);
            margin-bottom: 8px;
        }

        .info-content {
            font-size: 14px;
            line-height: 1.6;
            color: var(--gray-800);
        }

        .contract-footer {
            background: var(--gray-50);
            padding: 32px 40px;
            margin-top: 40px;
            border-top: 1px solid var(--gray-200);
            font-size: 13px;
            line-height: 1.6;
            color: var(--gray-600);
        }

        .company-details {
            font-size: 14px;
            line-height: 1.6;
            color: var(--white);
        }

        .company-details strong {
            font-weight: 600;
            font-size: 16px;
            display: block;
            margin-bottom: 4px;
        }

        .contract-meta {
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 16px;
            margin-top: 16px;
        }

        .contract-meta table {
            margin: 0;
        }

        .contract-meta td {
            padding: 4px 0;
            font-size: 13px;
            border: none;
        }

        .contract-meta td:first-child {
            font-weight: 500;
            width: 120px;
        }

        .content-section {
            background: var(--white);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .content-section h4 {
            color: var(--theme-color);
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--theme-light);
        }

        .signature-section {
            display: flex;
            gap: 32px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .signature-item {
            flex: 1;
            min-width: 200px;
            text-align: center;
            padding: 24px;
            background: var(--gray-50);
            border-radius: 12px;
            border: 2px dashed var(--gray-300);
        }

        .signature-item img {
            max-width: 150px;
            height: auto;
            margin-bottom: 16px;
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
        }

        .signature-item h5 {
            color: var(--gray-800);
            font-size: 14px;
            font-weight: 600;
            margin: 0;
        }

        html[dir="rtl"] table tr td,
        html[dir="rtl"] table tr th {
            text-align: right;
        }

        html[dir="rtl"] .text-right {
            text-align: left;
        }

        p:not(:last-of-type) {
            margin-bottom: 15px;
        }
    </style>

    @if ($settings['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
</head>

<body class="">
    <div class="contract-preview-main" id="boxes">
        <div class="contract-header">
            <table>
                <tbody>
                    <tr>
                        <td>
                            @if(!empty($img))
                                <img class="contract-logo" src="{{ $img }}" alt="">
                            @else
                                <div class="contract-logo d-flex align-items-center justify-content-center" style="width: 200px; height: 80px; border: 2px dashed rgba(255,255,255,0.3); border-radius: 8px;">
                                    <span style="color: rgba(255,255,255,0.7); font-size: 14px;">{{ __('No Logo') }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="text-right">
                            <div style="text-align: right;">
                                <h1 style="font-family: 'Playfair Display', serif; font-size: 48px; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    {{ __('CONTRACT') }}
                                </h1>
                                <div style="font-size: 18px; font-weight: 500; margin-top: 8px; opacity: 0.9;">
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="vertical-align-top">
                <tbody>
                    <tr>
                        <td>
                            <div class="company-details">
                                @if ($settings['company_name'])
                                    <strong>{{ $settings['company_name'] }}</strong>
                                @endif
                                @if ($settings['company_email'])
                                    {{ $settings['company_email'] }}<br>
                                @endif
                                @if ($settings['company_telephone'])
                                    {{ $settings['company_telephone'] }}<br>
                                @endif
                                @if ($settings['company_address'])
                                    {{ $settings['company_address'] }}<br>
                                @endif
                                @if ($settings['company_city'])
                                    {{ $settings['company_city'] }},
                                @endif
                                @if ($settings['company_state'])
                                    {{ $settings['company_state'] }}
                                @endif
                                @if ($settings['company_country'])
                                    <br>{{ $settings['company_country'] }}
                                @endif
                                @if ($settings['company_zipcode'])
                                    {{ $settings['company_zipcode'] }}
                                @endif
                                <br>
                                @if (!empty($settings['registration_number']))
                                    <small>{{ __('Registration Number') }}: {{ $settings['registration_number'] }}</small><br>
                                @endif
                                @if (App\Models\Utility::getValByName('tax_number') == 'on')
                                    @if (!empty($settings['tax_type']) && !empty($settings['vat_number']))
                                        <small>{{ $settings['tax_type'] . ' ' . __('Number') }}: {{ $settings['vat_number'] }}</small>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="contract-meta">
                                <table>
                                    <tr>
                                        <td><strong>{{ __('Type') }}:</strong></td>
                                        <td>{{ $contract->types->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Value') }}:</strong></td>
                                        <td>{{ Auth::user()->priceFormat($contract->value) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Start Date') }}:</strong></td>
                                        <td>{{ Auth::user()->dateFormat($contract->start_date) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('End Date') }}:</strong></td>
                                        <td>{{ Auth::user()->dateFormat($contract->end_date) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="contract-body">
            @if(!empty($contract->description))
                <div class="content-section">
                    <h4>{{ __('Contract Description') }}</h4>
                    <div class="info-content">
                        {!! $contract->description !!}
                    </div>
                </div>
            @endif

            @if(!empty($contract->notes))
                <div class="content-section">
                    <h4>{{ __('Contract Notes') }}</h4>
                    <div class="info-content">
                        {!! $contract->notes !!}
                    </div>
                </div>
            @endif

            <div class="signature-section">
                <div class="signature-item">
                    @if(!empty($contract->company_signature))
                        <img src="{{ $contract->company_signature }}" alt="Company Signature">
                    @endif
                    <h5>{{ __('Company Signature') }}</h5>
                </div>
                <div class="signature-item">
                    @if(!empty($contract->customer_signature))
                        <img src="{{ $contract->customer_signature }}" alt="Customer Signature">
                    @endif
                    <h5>{{ __('Customer Signature') }}</h5>
                </div>
            </div>
        </div>
        <div class="contract-footer">
            <div style="text-align: center;">
                @if(!empty($settings['footer_title']))
                    <strong style="display: block; margin-bottom: 8px; font-size: 14px; color: var(--gray-800);">{{ $settings['footer_title'] }}</strong>
                @endif
                @if(!empty($settings['footer_notes']))
                    <div style="color: var(--gray-600);">{!! $settings['footer_notes'] !!}</div>
                @endif
            </div>
            <div style="text-align: center; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--gray-200); font-size: 12px; color: var(--gray-500);">
                <em>{{ __('Thank you for your business!') }}</em>
            </div>
        </div>
    </div>
    @if (!isset($preview))
        <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
        <script>
            function closeScript() {
                setTimeout(function() {
                    window.open(window.location, '_self').close();
                }, 1000);
            }

            $(window).on('load', function() {
                var element = document.getElementById('boxes');
                var opt = {
                    filename: '{{ $usr->contractNumberFormat($contract->contract_id) }}',
                    image: {
                        type: 'jpeg',
                        quality: 1
                    },
                    html2canvas: {
                        scale: 4,
                        dpi: 72,
                        letterRendering: true
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'A4'
                    }
                };

                html2pdf().set(opt).from(element).save().then(closeScript);
            });
        </script>
    @endif

</body>

</html>
