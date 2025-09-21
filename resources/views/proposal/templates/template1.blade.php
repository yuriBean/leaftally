@php
    use App\Models\Utility;
    $settings_data = \App\Models\Utility::settingsById($proposal->created_by);
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $settings_data['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $selectedFont = isset($font) ? $font : (isset($settings_data['proposal_font']) ? $settings_data['proposal_font'] : 'Lato');
        $fontUrl = '';
        switch($selectedFont) {
            case 'Inter':
                $fontUrl = 'Inter:wght@300;400;500;600;700;800';
                break;
            case 'Roboto':
                $fontUrl = 'Roboto:wght@300;400;500;700;900';
                break;
            case 'Montserrat':
                $fontUrl = 'Montserrat:wght@300;400;500;600;700;800';
                break;
            case 'Open Sans':
                $fontUrl = 'Open+Sans:wght@300;400;600;700;800';
                break;
            case 'Lato':
                $fontUrl = 'Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900';
                break;
            case 'Poppins':
                $fontUrl = 'Poppins:wght@300;400;500;600;700;800';
                break;
        }
    @endphp
    
    @if($fontUrl)
        <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $fontUrl) }}&display=swap" rel="stylesheet">
    @endif

    <style type="text/css">
        :root {
            --theme-color: {{ $color }};
            --white: #ffffff;
            --black: #000000;
        }

        body {
            font-family: '{{ $selectedFont }}', sans-serif;
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

        .proposal-preview-main {
            max-width: 700px;
            width: 100%;
            margin: 0 auto;
            background: #ffff;
            box-shadow: 0 0 10px #ddd;
        }

        .proposal-logo {
            max-width: 200px;
            width: 100%;
        }

        .proposal-header table td {
            padding: 15px 30px;
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

        .view-qrcode {
            max-width: 114px;
            height: 114px;
            margin-left: auto;
            margin-top: 15px;
            background: var(--white);
        }

        .view-qrcode img {
            width: 100%;
            height: 100%;
        }

        .proposal-body {
            padding: 30px 25px 0;
        }

        table.add-border tr {
            border-top: 1px solid var(--theme-color);
        }

        tfoot tr:first-of-type {
            border-bottom: 1px solid var(--theme-color);
        }

        .total-table tr:first-of-type td {
            padding-top: 0;
        }

        .total-table tr:first-of-type {
            border-top: 0;
        }

        .sub-total {
            padding-right: 0;
            padding-left: 0;
        }

        .border-0 {
            border: none !important;
        }

        .proposal-summary td,
        .proposal-summary th {
            font-size: 13px;
            font-weight: 600;
        }

        .total-table td:last-of-type {
            width: 146px;
        }

        .proposal-footer {
            padding: 15px 20px;
        }

        .itm-description td {
            padding-top: 0;
        }

        html[dir="rtl"] table tr td,
        html[dir="rtl"] table tr th {
            text-align: right;
        }

        html[dir="rtl"] .text-right {
            text-align: left;
        }

        html[dir="rtl"] .view-qrcode {
            margin-left: 0;
            margin-right: auto;
        }

        p:not(:last-of-type) {
            margin-bottom: 15px;
        }

        .proposal-summary p {
            margin-bottom: 0;
        }
    </style>

    @if ($settings_data['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
</head>

<body class="">
    <div class="proposal-preview-main" id="boxes">
        <div class="proposal-header">
            <table>
                <tbody>
                    <tr>
                        <td>
                            @if(!empty($img))
                                <img class="proposal-logo" src="{{ $img }}" alt="">
                            @else
                                <div class="proposal-logo d-flex align-items-center justify-content-center" style="width: 200px; height: 80px; border: 2px dashed rgba(0,0,0,0.3); border-radius: 8px; background-color: #f8f9fa;">
                                    <span style="color: rgba(0,0,0,0.5); font-size: 14px;">{{ __('No Logo') }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="text-right">
                            <div style="text-align: right;">
                                <h1 style="font-family: 'Playfair Display', serif; font-size: 48px; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    {{ __('PROPOSAL') }}
                                </h1>
                                <div style="font-size: 18px; font-weight: 500; margin-top: 8px; opacity: 0.9;">
                                    #{{ \App\Models\Utility::proposalNumberFormat($settings, $proposal->proposal_id) }}
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
                            <div class="proposal-meta">
                                <table>
                                    <tr>
                                        <td><strong>{{ __('Issue Date') }}:</strong></td>
                                        <td>{{ \App\Models\Utility::dateFormat($settings, $proposal->issue_date) }}</td>
                                    </tr>
                                    @if (!empty($customFields) && count($proposal->customField) > 0)
                                        @foreach ($customFields as $field)
                                            <tr>
                                                <td><strong>{{ $field->name }}:</strong></td>
                                                <td>{{ !empty($proposal->customField) ? $proposal->customField[$field->id] : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </table>
                                @if (isset($settings_data['qr_display']) && $settings_data['qr_display'] == 'on')
                                    <div class="view-qrcode" style="margin-top: 16px;">
                                        {!! DNS2D::getBarcodeHTML(route('pay.proposalpay', \Crypt::encrypt($proposal->proposal_id)), 'QRCODE', 2, 2) !!}
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="proposal-body">
            <div style="display: flex; gap: 32px; flex-wrap: wrap;">
                <div class="info-section" style="flex: 1; min-width: 250px;">
                    <div class="info-label">{{ __('Bill To') }}</div>
                    <div class="info-content">
                        <strong>{{ !empty($customer->billing_name) ? $customer->billing_name : '' }}</strong><br>
                        {{ !empty($customer->billing_address) ? $customer->billing_address : '' }}<br>
                        {{ !empty($customer->billing_city) ? $customer->billing_city : '' . ', ' }}, {{ !empty($customer->billing_state) ? $customer->billing_state : '' . ', ' }} {{ !empty($customer->billing_zip) ? $customer->billing_zip : '' }}<br>
                        {{ !empty($customer->billing_country) ? $customer->billing_country : '' }}<br>
                        @if(!empty($customer->billing_phone))
                            <strong>{{ __('Phone') }}:</strong> {{ $customer->billing_phone }}
                        @endif
                    </div>
                </div>
                @if ($settings['shipping_display'] == 'on')
                    <div class="info-section" style="flex: 1; min-width: 250px;">
                        <div class="info-label">{{ __('Ship To') }}</div>
                        <div class="info-content">
                            <strong>{{ !empty($customer->shipping_name) ? $customer->shipping_name : '' }}</strong><br>
                            {{ !empty($customer->shipping_address) ? $customer->shipping_address : '' }}<br>
                            {{ !empty($customer->shipping_city) ? $customer->shipping_city : '' . ', ' }}, {{ !empty($customer->shipping_state) ? $customer->shipping_state : '' . ', ' }} {{ !empty($customer->shipping_zip) ? $customer->shipping_zip : '' }}<br>
                            {{ !empty($customer->shipping_country) ? $customer->shipping_country : '' }}<br>
                            @if(!empty($customer->shipping_phone))
                                <strong>{{ __('Phone') }}:</strong> {{ $customer->shipping_phone }}
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>{{ __('Item') }}</th>
                        <th>{{ __('Qty') }}</th>
                        <th>{{ __('Rate') }}</th>
                        <th>{{ __('Discount') }}</th>
                        <th>{{ __('Tax') }}</th>
                        <th>{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($proposal->itemData) && count($proposal->itemData) > 0)
                        @foreach ($proposal->itemData as $key => $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ \App\Models\Utility::priceFormat($settings, $item->price) }}</td>
                                <td>{{ $item->discount != 0 ? \App\Models\Utility::priceFormat($settings, $item->discount) : '-' }}
                                </td>
                                <td>
                                    @if (!empty($item->itemTax))
                                        @php
                                            $itemtax = 0;
                                        @endphp
                                        @foreach ($item->itemTax as $taxes)
                                            @php
                                                $itemtax += $taxes['tax_price'];
                                            @endphp
                                            <p>{{ $taxes['name'] }} ({{ $taxes['rate'] }}) {{ $taxes['price'] }}</p>
                                        @endforeach
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                @php
                                    $itemtax = 0;
                                @endphp
                                <td>{{ \App\Models\Utility::priceFormat($settings, $item->price * $item->quantity - $item->discount + $itemtax) }}
                                </td>
                                @if (!empty($item->description))
                            <tr class="border-0 itm-description ">
                                <td colspan="6">{{ $item->description }}</td>
                            </tr>
                        @endif
                        </tr>
                    @endforeach
                @else
                    @endif

                </tbody>
                <tfoot>
                    <tr>
                        <td>{{ __('Total') }}</td>
                        <td>{{ $proposal->totalQuantity }}</td>
                        <td>{{ App\Models\Utility::priceFormat($settings, $proposal->totalRate) }}</td>
                        <td>{{ App\Models\Utility::priceFormat($settings, $proposal->totalDiscount) }}</td>
                        <td>{{ App\Models\Utility::priceFormat($settings, $proposal->totalTaxPrice) }}</td>
                        <td>{{ App\Models\Utility::priceFormat($settings, $proposal->getSubTotal()) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" style="padding: 0; border: none;">
                            <div class="total-section">
                                <table class="total-table">
                                    <tr>
                                        <td>{{ __('Subtotal') }}:</td>
                                        <td class="text-right">{{ \App\Models\Utility::priceFormat($settings, $proposal->getSubTotal()) }}</td>
                                    </tr>
                                    @if ($proposal->getTotalDiscount())
                                        <tr>
                                            <td>{{ __('Discount') }}:</td>
                                            <td class="text-right">-{{ \App\Models\Utility::priceFormat($settings, $proposal->getTotalDiscount()) }}</td>
                                        </tr>
                                    @endif
                                    @if (!empty($proposal->taxesData))
                                        @foreach ($proposal->taxesData as $taxName => $taxPrice)
                                            <tr>
                                                <td>{{ $taxName }}:</td>
                                                <td class="text-right">{{ \App\Models\Utility::priceFormat($settings, $taxPrice) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr>
                                        <td><strong>{{ __('Total') }}:</strong></td>
                                        <td class="text-right amount-highlight">{{ \App\Models\Utility::priceFormat($settings, $proposal->getSubTotal() - $proposal->getTotalDiscount() + $proposal->getTotalTax()) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="proposal-footer">
            @if($settings['footer_title'] || $settings['footer_notes'])
                <div style="text-align: center;">
                    @if($settings['footer_title'])
                        <strong style="display: block; margin-bottom: 8px; font-size: 14px; color: var(--gray-800);">{{ $settings['footer_title'] }}</strong>
                    @endif
                    @if($settings['footer_notes'])
                        <div style="color: var(--gray-600);">{!! $settings['footer_notes'] !!}</div>
                    @endif
                </div>
            @endif
            <div style="text-align: center; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--gray-200); font-size: 12px; color: var(--gray-500);">
                <em>Thank you for your business!</em>
            </div>
        </div>
    </div>
    @if (!isset($preview))
        @include('proposal.script');
    @endif

</body>

</html>
