@php
    use \App\Models\Utility;
    $settings_data = \App\Models\Utility::settingsById($bill->created_by);

@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $settings_data['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">


    <style type="text/css">
        :root {
            --theme-color: {{ $color }};
            --white: #ffffff;
            --black: #000000;
        }

        body {
            font-family: 'Lato', sans-serif;
        }
.text-gray-700 {
    color: #374151; /* Tailwind's gray-700 */
}

.px-4 {
    padding-left: 1rem;
    padding-right: 1rem;
}

.py-1 {
    padding-top: 0.25rem;
    padding-bottom: 0.25rem;
}

.py-3 {
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
}

.border {
    border: 1px solid #E5E5E5;
}

.border-0 {
    border: none !important;
}

.bg-gray-100 {
    background-color: #F6F6F6;
}

.font-semibold {
    font-weight: 600;
}

.text-sm {
    font-size: 0.875rem;
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

        .bill-preview-main {
            max-width: 700px;
            width: 100%;
            margin: 0 auto;
            background: #ffff;
            box-shadow: 0 0 10px #ddd;
        }

        .bill-logo {
            max-width: 200px;
            width: 100%;
        }

        .bill-header table td {
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

        .bill-body {
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

        .bill-summary td,
        .bill-summary th {
            font-size: 13px;
            font-weight: 600;
        }

        .total-table td:last-of-type {
            width: 146px;
        }

        .bill-footer {
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

        .bill-summary p {
            margin-bottom: 0;
        }
    </style>

    @if (env('SITE_RTL') == 'on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
</head>

<body>
    <div class="bill-preview-main" id="boxes">
        <div class="bill-header">
            <table class="vertical-align-top">
                <tbody>
                    <tr>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >
                            <h3
                                style=" display: block; text-transform: uppercase; font-size: 30px; font-weight: bold; padding: 15px; background: {{ $color }};color:{{ $font_color }} ">
                                {{ __('BILL') }}</h3>
                                @if(isset($settings_data['bill_qr_display']) && $settings_data['bill_qr_display'] == 'on')
                            <div class="view-qrcode" style="margin-left: 0; margin-bottom: 15px; ">
                                {!! DNS2D::getBarcodeHTML(route('pay.billpay', \Crypt::encrypt($bill->bill_id)), 'QRCODE', 2, 2) !!}
                            </div>
                            @endif
                            <table class="no-space">
                                <tbody>
                                    <tr>
                                        <td style="color: {{ $color }};">{{ __('Number') }}:</td>
                                        <td class="text-right">{{ Utility::billNumberFormat($settings, $bill->bill_id) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color: {{ $color }};">{{ __('Issue Date') }}:</td>
                                        <td class="text-right">{{ Utility::dateFormat($settings, $bill->issue_date) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color: {{ $color }};">{{ __('Due Date') }}:</td>
                                        <td class="text-right">{{ Utility::dateFormat($settings, $bill->due_date) }}
                                        </td>
                                    </tr>
                                    @if (!empty($customFields) && count($bill->customField) > 0)
                                        @foreach ($customFields as $field)
                                            <tr>
                                                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ $field->name }} :</td>
                                                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" > {{ !empty($bill->customField) ? $bill->customField[$field->id] : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </td>

                        <td class="text-right">
                            <img class="bill-logo" src="{{ $img }}" alt="">
                            <p>
                                @if ($settings['company_name'])
                                    {{ $settings['company_name'] }}
                                @endif
                                <br>
                                @if ($settings['company_email'])
                                    {{ $settings['company_email'] }}
                                @endif
                                <br>
                                @if ($settings['company_telephone'])
                                    {{ $settings['company_telephone'] }}
                                @endif
                                <br>
                                @if ($settings['company_address'])
                                    {{ $settings['company_address'] }}
                                @endif
                                @if ($settings['company_city'])
                                    <br> {{ $settings['company_city'] }},
                                @endif
                                @if ($settings['company_state'])
                                    {{ $settings['company_state'] }}
                                @endif
                                @if ($settings['company_country'])
                                    <br>{{ $settings['company_country'] }}
                                @endif
                                @if ($settings['company_zipcode'])
                                    - {{ $settings['company_zipcode'] }}
                                @endif
                                <br>
                                @if (!empty($settings['registration_number']))
                                    {{ __('Registration Number') }} : {{ $settings['registration_number'] }}
                                @endif

                                @if (App\Models\Utility::getValByName('tax_number') == 'on')
                                    @if (!empty($settings['tax_type']) && !empty($settings['vat_number']))<br>
                                        {{ $settings['tax_type'] . ' ' . __('Number') }} : {{ $settings['vat_number'] }}
                                        <br>
                                    @endif
                                @endif
                            </p>
                        </td>

                    </tr>
                </tbody>
            </table>

        </div>
        <div class="bill-body">
            <table>
                <tbody>
                    <tr>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >
                            <strong style="margin-bottom: 10px; display:block;">{{ __('Bill To') }}:</strong>
                            <p>
                                {{!empty($vendor->billing_name)?$vendor->billing_name:''}}<br>
                                {{!empty($vendor->billing_address)?$vendor->billing_address:''}}<br>
                                {{!empty($vendor->billing_city)?$vendor->billing_city:'' .', '}}, {{!empty($vendor->billing_state)?$vendor->billing_state:'' . ', '}} {{!empty($vendor->billing_zip)?$vendor->billing_zip:''}}<br>
                                {{!empty($vendor->billing_country)?$vendor->billing_country:''}}<br>
                                {{!empty($vendor->billing_phone)?$vendor->billing_phone:''}}<br>
                            </p>
                        </td>
                        @if ($settings['shipping_display'] == 'on')
                            <td class="text-right">
                                <strong style="margin-bottom: 10px; display:block;">{{ __('Ship To') }}:</strong>
                                <p>
                                    {{!empty($vendor->shipping_name)?$vendor->shipping_name:''}}<br>
                                    {{!empty($vendor->shipping_address)?$vendor->shipping_address:''}}<br>
                                    {{!empty($vendor->shipping_city)?$vendor->shipping_city:'' . ', '}}, {{!empty($vendor->shipping_state)?$vendor->shipping_state:'' .', '}} {{!empty($vendor->shipping_zip)?$vendor->shipping_zip:''}}<br>
                                    {{!empty($vendor->shipping_country)?$vendor->shipping_country:''}}<br>
                                    {{!empty($vendor->shipping_phone)?$vendor->shipping_phone:''}}<br>
                                </p>
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
            <table class=" bill-summary" style="margin-top: 30px;">
                <thead style="background: {{ $color }};color:{{ $font_color }}">
                    <tr style="border-bottom:1px solid {{ $color }};">
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Item') }}</th>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Quantity') }}</th>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Rate') }}</th>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Discount') }}</th>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Tax') }} (%)</th>
                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Price') }} <small>after tax & discount</small></th>
                    </tr>
                </thead>
                <tbody style="border-bottom:1px solid {{ $color }};">
                    @if (isset($bill->itemData) && count($bill->itemData) > 0)
                        @foreach ($bill->itemData as $key => $item)
                            <tr>
                                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ $item->name }}</td>
                                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ $item->quantity }}</td>
                                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $item->price) }}</td>
                                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ $item->discount != 0 ? Utility::priceFormat($settings, $item->discount) : '-' }}</td>
                                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >
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
                                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $item->price * $item->quantity - $item->discount + $itemtax) }}
                                </td>
                                @if (!empty($item->description))
                            <tr class="border-0 itm-description">
                                <td colspan="6" style="border-bottom:1px solid {{ $color }};">
                                    {{ $item->description }}</td>
                            </tr>
                        @endif
                        </tr>
                    @endforeach
                @else
                    @endif
                </tbody>
                <tfoot>
                    <tr style="border-bottom:1px solid {{ $color }};">
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ __('Total') }}</td>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ $bill->totalQuantity }}</td>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $bill->totalRate) }}</td>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $bill->totalDiscount) }}</td>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $bill->totalTaxPrice) }}</td>
                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $bill->getSubTotal()) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td colspan="2" class="sub-total">
                            <table class="total-table">
                                <tr style="border-bottom:1px solid {{ $color }};">
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ __('Subtotal') }}:</td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $bill->getSubTotal()) }}</td>
                                </tr>
                                @if ($bill->getTotalDiscount())
                                    <tr style="border-bottom:1px solid {{ $color }};">
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ __('Discount') }}:</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $bill->getTotalDiscount()) }}</td>
                                    </tr>
                                @endif
                                @if (!empty($bill->taxesData))
                                    @foreach ($bill->taxesData as $taxName => $taxPrice)
                                        <tr style="border-bottom:1px solid {{ $color }};">
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ $taxName }} :</td>
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $taxPrice) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr style="border-bottom:1px solid {{ $color }};">
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ __('Total') }}:</td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $bill->getSubTotal() - $bill->getTotalDiscount() + $bill->getTotalTax()) }}
                                    </td>
                                </tr>
                                <tr style="border-bottom:1px solid {{ $color }};">
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ __('Paid') }}:</td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $bill->getTotal() - $bill->getDue() - $bill->billTotalDebitNote()) }}
                                    </td>
                                </tr>
                                <tr style="border-bottom:1px solid {{ $color }};">
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ __('Debit Note') }}:</td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $bill->billTotalDebitNote()) }}</td>
                                </tr>
                                <tr style="border-bottom:1px solid {{ $color }};">
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ __('Amount Due') }}:</td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700" >{{ Utility::priceFormat($settings, $bill->getDue()) }}</td>
                                </tr>

                            </table>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div class="bill-footer">
                <p>
                    {{ $settings['footer_title'] }} <br>
                    {!! $settings['footer_notes'] !!}
                </p>
            </div>
        </div>
    </div>
    @if (!isset($preview))
        @include('bill.script');
    @endif

</body>

</html>
