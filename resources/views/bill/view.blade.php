@extends('layouts.admin')
@section('page-title')
    {{ __('Bill Detail') }}
@endsection
@php
    use App\Models\Utility;
    $settings = App\Models\Utility::settings();
@endphp
@push('script-page')
    <script>
        $(document).on('click', '#shipping', function() {
            var url = $(this).data('url');
            var is_display = $("#shipping").is(":checked");
            $.ajax({
                url: url,
                type: 'get',
                data: {
                    'is_display': is_display,
                },
                success: function(data) {
                }
            });
        })

        $('.cp_link').on('click', function() {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            show_toastr('success', '{{ __('Link Copy on Clipboard') }}', 'success')
        });
    </script>
@endpush
@section('breadcrumb')
    @if (\Auth::guard('vender')->check())
        <li class="breadcrumb-item"><a href="{{ route('vender.dashboard') }}">{{ __('Dashboard') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    @endif
    @if (\Auth::user()->type == 'company')
        <li class="breadcrumb-item"><a href="{{ route('bill.index') }}">{{ __('Bill') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('vender.bill') }}">{{ __('Bill') }}</a></li>
    @endif

    <li class="breadcrumb-item">{{ Auth::user()->billNumberFormat($bill->bill_id) }}</li>
@endsection
@section('action-btn')
    <div class="d-flex">
        <a href="#" class="btn btn-sm btn-primary cp_link"
            data-link="{{ route('pay.billpay', \Illuminate\Support\Facades\Crypt::encrypt($bill->id)) }}"
            data-bs-toggle="tooltip" title="{{ __('copy bill') }}"
            data-original-title="{{ __('Click to copy invoice link') }}">
            <span class="btn-inner--icon text-white"><i class="ti ti-file"></i></span>
        </a>
    </div>
@endsection
@section('content')

    @can('send bill')
        @if ($bill->status != 4)
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <div class="row timeline-wrapper">
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <div class="timeline-icons"><span class="timeline-dots"></span>
                                    <i class="ti ti-plus text-primary"></i>
                                </div>
                                <h6 class="text-primary my-3">{{ __('Create Bill') }}</h6>
                                <p class="text-muted text-sm mb-3"><i
                                        class="ti ti-clock mr-2"></i>{{ __('Created on ') }}{{ \Auth::user()->dateFormat($bill->bill_date) }}
                                </p>
                                @can('edit bill')
                                    <a href="{{ route('bill.edit', \Crypt::encrypt($bill->id)) }}" class="btn btn-sm btn-primary"
                                        data-bs-toggle="tooltip" data-original-title="{{ __('Edit') }}"><i
                                            class="ti ti-pencil mr-2"></i>{{ __('Edit') }}</a>
                                @endcan
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <div class="timeline-icons"><span class="timeline-dots"></span>
                                    <i class="ti ti-mail text-warning"></i>
                                </div>
                                <h6 class="text-warning my-3">{{ __('Send Bill') }}</h6>
                                <p class="text-muted text-sm mb-3">
                                    @if ($bill->status != 0)
                                        <i class="ti ti-clock mr-2"></i>{{ __('Sent on') }}
                                        {{ \Auth::user()->dateFormat($bill->send_date) }}
                                    @else
                                        @can('send bill')
                                            <small>{{ __('Status') }} : {{ __('Not Sent') }}</small>
                                        @endcan
                                    @endif
                                </p>

                                @if ($bill->status == 0)
                                    @can('send bill')
                                        <a href="{{ route('bill.sent', $bill->id) }}" class="btn btn-sm btn-warning"
                                            data-bs-toggle="tooltip" data-original-title="{{ __('Mark Sent') }}"><i
                                                class="ti ti-send mr-2"></i>{{ __('Send') }}</a>
                                    @endcan
                                @endif
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <div class="timeline-icons"><span class="timeline-dots"></span>
                                    <i class="ti ti-report-money text-info"></i>
                                </div>
                                <h6 class="text-info my-3">{{ __('Get Paid') }}</h6>
                                <p class="text-muted text-sm mb-3">{{ __('Status') }} : {{ __('Awaiting payment') }} </p>
                                @if ($bill->status != 0)
                                    @can('create payment bill')
                                        <a href="#" data-url="{{ route('bill.payment', $bill->id) }}" data-ajax-popup="true"
                                            data-title="{{ __('Add Payment') }}" class="btn btn-sm btn-info"
                                            data-original-title="{{ __('Add Payment') }}"><i
                                                class="ti ti-report-money mr-2"></i>{{ __('Add Payment') }}</a> <br>
                                    @endcan
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @if (\Auth::user()->type == 'company')
        @if ($bill->status != 0)
            <div class="row justify-content-between align-items-center mb-3">
                <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                    @if (!empty($billPayment))
                        <div class="all-button-box mx-2">
                            <a href="#" data-url="{{ route('bill.debit.note', $bill->id) }}" data-ajax-popup="true"
                                data-title="{{ __('Add Debit Note') }}" class="btn btn-sm btn-primary">
                                {{ __('Add Debit Note') }}
                            </a>
                        </div>
                    @endif
                    <div class="all-button-box mx-2">
                        <a href="{{ route('bill.resent', $bill->id) }}" class="btn btn-sm btn-primary bs-resend-confirm">
                            {{ __('Resend Bill') }}
                        </a>
                    </div>
                    <div class="all-button-box">
                        <a href="{{ route('bill.pdf', Crypt::encrypt($bill->id)) }}" target="_blank"
                            class="btn btn-sm btn-primary">
                            <i class="fas fa-file-pdf me-1"></i>{{ __('Download') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="row justify-content-between align-items-center mb-3">
            <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                <div class="all-button-box mx-2">
                    <a href="#" data-url="{{ route('vender.bill.send', $bill->id) }}" data-ajax-popup="true"
                        data-title="{{ __('Send Bill') }}" class="btn btn-sm btn-primary">
                        {{ __('Send Mail') }}
                    </a>
                </div>
                <div class="all-button-box mx-2">
                    <a href="{{ route('bill.pdf', Crypt::encrypt($bill->id)) }}" target="_blank"
                        class="btn btn-sm btn-primary">
                        {{ __('Download') }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice">
                        <div class="invoice-print">
                            <div class="row invoice-title mt-2">
                                <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12">
                                    <h2>{{ __('Bill') }}</h2>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12 text-end">
                                    <h3 class="invoice-number">{{ Auth::user()->billNumberFormat($bill->bill_id) }}</h3>
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col text-end">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div class="me-4">
                                            <small>
                                                <strong>{{ __('Issue Date') }} :</strong><br>
                                                {{ \Auth::user()->dateFormat($bill->bill_date) }}<br><br>
                                            </small>
                                        </div>
                                        <div>
                                            <small>
                                                <strong>{{ __('Due Date') }} :</strong><br>
                                                {{ \Auth::user()->dateFormat($bill->due_date) }}<br><br>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <strong>
                                    {{ $vendor->name }}
                                </strong>
                                <strong>
                                    {{ $vendor->contact }}
                                </strong>
                                @if (!empty($vendor->billing_name))
                                    <div class="col">
                                        <small class="font-style">
                                            <strong>{{ __('Billed To') }} :</strong><br>
                                            {{ !empty($vendor->billing_name) ? $vendor->billing_name : '' }}<br>
                                            {{ !empty($vendor->billing_address) ? $vendor->billing_address : '' }}<br>
                                            {{ !empty($vendor->billing_city) ? $vendor->billing_city : '' . ', ' }},
                                            {{ !empty($vendor->billing_state) ? $vendor->billing_state : '' . ', ' }}
                                            {{ !empty($vendor->billing_zip) ? $vendor->billing_zip : '' }}<br>
                                            {{ !empty($vendor->billing_country) ? $vendor->billing_country : '' }}<br>
                                            {{ !empty($vendor->billing_phone) ? $vendor->billing_phone : '' }}<br>
                                            @if (App\Models\Utility::getValByName('tax_number') == 'on')
                                                <strong>{{ __('Tax Number ') }} :
                                                </strong>{{ !empty($vendor->tax_number) ? $vendor->tax_number : '' }}<br>
                                            @endif
                                            @if (!empty($settings['tax_type']) && !empty($settings['vat_number']))
                                                {{ $settings['tax_type'] . ' ' . __('Number') }} :
                                                {{ $settings['vat_number'] }} <br>
                                            @endif
                                        </small>
                                    </div>
                                @endif
                                @if (App\Models\Utility::getValByName('shipping_display') == 'on')
                                    <div class="col">
                                        <small>
                                            <strong>{{ __('Shipped To') }} :</strong><br>
                                            {{ !empty($vendor->shipping_name) ? $vendor->shipping_name : '' }}<br>
                                            {{ !empty($vendor->shipping_address) ? $vendor->shipping_address : '' }}<br>
                                            {{ !empty($vendor->shipping_city) ? $vendor->shipping_city : '' . ', ' }},
                                            {{ !empty($vendor->shipping_state) ? $vendor->shipping_state : '' . ', ' }}
                                            {{ !empty($vendor->shipping_zip) ? $vendor->shipping_zip : '' }}<br>
                                            {{ !empty($vendor->shipping_country) ? $vendor->shipping_country : '' }}<br>
                                            {{ !empty($vendor->shipping_phone) ? $vendor->shipping_phone : '' }}<br>
                                            @if (App\Models\Utility::getValByName('tax_number') == 'on')
                                                <strong>{{ __('Tax Number ') }} :
                                                </strong>{{ !empty($vendor->tax_number) ? $vendor->tax_number : '' }}
                                            @endif

                                        </small>
                                    </div>
                                @endif

                                <div class="col">
                                    <div class="float-end mt-3">
                                        @if ($settings['bill_qr_display'] == 'on')
                                            {!! DNS2D::getBarcodeHTML(
                                                route('pay.billpay', \Illuminate\Support\Facades\Crypt::encrypt($bill->id)),
                                                'QRCODE',
                                                2,
                                                2,
                                            ) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <small>
                                        <strong>{{ __('Status') }} :</strong><br>
                                        @if ($bill->status == 0)
                                            <span
                                                class="badge fix_badge bg-primary p-2 px-3">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                        @elseif($bill->status == 1)
                                            <span
                                                class="badge fix_badge bg-info p-2 px-3">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                        @elseif($bill->status == 2)
                                            <span
                                                class="badge fix_badge bg-danger p-2 px-3">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                        @elseif($bill->status == 3)
                                            <span
                                                class="badge fix_badge bg-warning p-2 px-3">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                        @elseif($bill->status == 4)
                                            <span
                                                class="badge fix_badge bg-danger p-2 px-3">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                        @endif
                                    </small>
                                </div>

                                @if (!empty($customFields) && count($bill->customField) > 0)
                                    @foreach ($customFields as $field)
                                        <div class="col text-md-end">
                                            <small>
                                                <strong>{{ $field->name }} :</strong><br>
                                                {{ !empty($bill->customField) ? $bill->customField[$field->id] : '-' }}
                                                <br><br>
                                            </small>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="font-bold mb-2">{{ __('Product Summary') }}</div>
                                    <small class="mb-2">{{ __('All items here cannot be deleted.') }}</small>
                                   <div class="table-responsive table-new-design mt-3">
    <table class="table datatable border border-[#E5E5E5] rounded-[8px] mb-0">
        <tr>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]" data-width="40">
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Product') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Quantity') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Rate') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Discount') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Tax') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Chart Of Account') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Account Amount') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Description') }}
            </th>
            <th class="text-end px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]" width="16%">
                {{ __('Price') }}<br>
                <small class="text-danger font-weight-bold">{{ __('after tax & discount') }}</small>
            </th>
            <th class="border border-[#E5E5E5] bg-[#F6F6F6]"></th>
        </tr>
                                            @php
                                                $totalQuantity = 0;
                                                $totalRate = 0;
                                                $totalTaxPrice = 0;
                                                $totalDiscount = 0;
                                                $taxesData = [];
                                            @endphp

                                            @foreach ($items as $key => $item)
                                                @if (!empty($item->product_id))
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>

                                                        @php
                                                            $productName = $item->product;
                                                            $totalQuantity += $item->quantity;
                                                            $totalRate += $item->price;
                                                            $totalDiscount += $item->discount;
                                                        @endphp
                                                        <td>{{ !empty($productName) ? $productName->name : '-' }}</td>
                                                        <td>{{ $item->quantity . ' (' . $productName?->unit?->name . ')' }}
                                                        </td>
                                                        <td>{{ \Auth::user()->priceFormat($item->price) }}</td>
                                                        <td>{{ \Auth::user()->priceFormat($item->discount) }}</td>

                                                        <td>
                                                            @if (!empty($item->tax))
                                                                <table>
                                                                    @php
                                                                        $itemTaxes = [];
                                                                        $getTaxData = \App\Models\Utility::getTaxData();

                                                                        if (!empty($item->tax)) {
                                                                            foreach (explode(',', $item->tax) as $tax) {
                                                                                $taxPrice = Utility::taxRate(
                                                                                    $getTaxData[$tax]['rate'],
                                                                                    $item->price,
                                                                                    $item->quantity,
                                                                                );
                                                                                $totalTaxPrice += $taxPrice;
                                                                                $itemTax['name'] =
                                                                                    $getTaxData[$tax]['name'];
                                                                                $itemTax['rate'] =
                                                                                    $getTaxData[$tax]['rate'] . '%';
                                                                                $itemTax[
                                                                                    'price'
                                                                                ] = \Auth::user()->priceFormat(
                                                                                    $taxPrice,
                                                                                );

                                                                                $itemTaxes[] = $itemTax;
                                                                                if (
                                                                                    array_key_exists(
                                                                                        $getTaxData[$tax]['name'],
                                                                                        $taxesData,
                                                                                    )
                                                                                ) {
                                                                                    $taxesData[
                                                                                        $getTaxData[$tax]['name']
                                                                                    ] =
                                                                                        $taxesData[
                                                                                            $getTaxData[$tax]['name']
                                                                                        ] + $taxPrice;
                                                                                } else {
                                                                                    $taxesData[
                                                                                        $getTaxData[$tax]['name']
                                                                                    ] = $taxPrice;
                                                                                }
                                                                            }
                                                                            $item->itemTax = $itemTaxes;
                                                                        } else {
                                                                            $item->itemTax = [];
                                                                        }
                                                                    @endphp
                                                                    @foreach ($item->itemTax as $tax)
                                                                        <tr>
                                                                            <td>{{ $tax['name'] . ' (' . $tax['rate'] . '%)' }}
                                                                            </td>
                                                                            <td>{{ $tax['price'] }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>

                                                        @php
                                                            $chartAccount = \App\Models\ChartOfAccount::find(
                                                                $item->chart_account_id,
                                                            );
                                                        @endphp

                                                        <td>{{ !empty($chartAccount) ? $chartAccount->name : '-' }}</td>
                                                        <td>{{ \Auth::user()->priceFormat($item->amount) }}</td>

                                                        <td>{{ !empty($item->description) ? $item->description : '-' }}
                                                        </td>

                                                        <td class="text-end">
                                                            {{ \Auth::user()->priceFormat($item->price * $item->quantity - $item->discount + $totalTaxPrice) }}
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        @php
                                                            $chartAccount = \App\Models\ChartOfAccount::find(
                                                                $item['chart_account_id'],
                                                            );
                                                        @endphp
                                                        <td>{{ !empty($chartAccount) ? $chartAccount->name : '-' }}</td>
                                                        <td>{{ \Auth::user()->priceFormat($item['amount']) }}</td>
                                                        <td>-</td>
                                                        <td class="text-end">
                                                            {{ \Auth::user()->priceFormat($item['amount']) }}</td>
                                                        <td></td>

                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td><b>{{ __('Total') }}</b></td>
                                                    <td><b>{{ $totalQuantity }}</b></td>
                                                    <td><b>{{ \Auth::user()->priceFormat($totalRate) }}</b></td>
                                                    <td><b>{{ \Auth::user()->priceFormat($totalDiscount) }}</b></td>
                                                    <td><b>{{ \Auth::user()->priceFormat($totalTaxPrice) }}</b></td>
                                                    <td></td>
                                                    <td><b>{{ \Auth::user()->priceFormat($bill->getAccountTotal()) }}</b>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="text-end"><b>{{ __('Sub Total') }}</b></td>
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($bill->getSubTotal()) }}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="text-end"><b>{{ __('Discount') }}</b></td>
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($bill->getTotalDiscount()) }}</td>
                                                </tr>

                                                @if (!empty($taxesData))
                                                    @foreach ($taxesData as $taxName => $taxPrice)
                                                        <tr>
                                                            <td colspan="8"></td>
                                                            <td class="text-end"><b>{{ $taxName }}</b></td>
                                                            <td class="text-end">
                                                                {{ \Auth::user()->priceFormat($taxPrice) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="blue-text text-end"><b>{{ __('Total') }}</b></td>
                                                    <td class="blue-text text-end">
                                                        {{ \Auth::user()->priceFormat($bill->getTotal()) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="text-end"><b>{{ __('Paid') }}</b></td>
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($bill->getTotal() - $bill->getDue() - $bill->billTotalDebitNote()) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="text-end"><b>{{ __('Debit Note') }}</b></td>
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($bill->billTotalDebitNote()) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8"></td>
                                                    <td class="text-end"><b>{{ __('Due') }}</b></td>
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($bill->getDue()) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <h5 class="h4 d-inline-block mb-3">{{ __('Payment Summary') }}</h5>
            <div class="card">
                <div class="card-body table-border-style pt-0">
                    <div class="table-responsive table-new-design mt-0">
    <table class="table datatable border border-[#E5E5E5] rounded-[8px]">
       <thead>
            <tr>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Payment Receipt') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Date') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Amount') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Account') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Reference') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Description') }}
            </th>
            @can('delete payment bill')
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                    {{ __('Action') }}
                </th>
            @endcan
        </tr>
       </thead>
                            @forelse($bill->payments as $key =>$payment)
                                <tr>
                                    <td>
                                        @if (!empty($payment->add_receipt))
                                            <a href="{{ asset(Storage::url('uploads/payment/' . $payment->add_receipt)) }}" download=""
                                                class="btn btn-sm btn-primary btn-icon rounded-pill" target="_blank"><span
                                                    class="btn-inner--icon"><i class="ti ti-file-text"></i></span></a>
                                            <a href="{{ asset(Storage::url('uploads/payment/' . $payment->add_receipt)) }}"
                                                class="btn btn-sm btn-secondary btn-icon rounded-pill"
                                                target="_blank"><span class="btn-inner--icon"><i
                                                        class="ti ti-crosshair"></i></span></a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ \Auth::user()->dateFormat($payment->date) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($payment->amount) }}</td>
                                    <td>{{ !empty($payment->bankAccount) ? $payment->bankAccount->bank_name . ' ' . $payment->bankAccount->holder_name : '' }}
                                    </td>
                                    <td>{{ $payment->reference }}</td>
                                    <td>{{ $payment->description }}</td>
                                    <td class="text-dark">
                                        <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                                            type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div
                                            class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                            @can('delete bill product')
                                                <li>
                                                    {!! Form::open([
                                                        'method' => 'post',
                                                        'route' => ['bill.payment.destroy', $bill->id, $payment->id],
                                                        'id' => 'delete-form-' . $payment->id,
                                                    ]) !!}
                                                    <a href="#"
                                                        class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                        data-original-title="{{ __('Delete') }}"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $payment->id }}').submit();">
                                                        <i class="ti ti-trash"></i>
                                                        <span>{{ __('Delete') }}</span>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </li>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-dark">
                                        <p>{{ __('No Data Found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <h5 class="h4 d-inline-block mb-3">{{ __('Debit Note Summary') }}</h5>
            <div class="card">
                <div class="card-body table-border-style pt-0">
                   <div class="table-responsive table-new-design mt-0">
    <table class="table datatable border border-[#E5E5E5] rounded-[8px]">
       <thead>
            <tr>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Date') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Amount') }}
            </th>
            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                {{ __('Description') }}
            </th>
            @if (Gate::check('edit debit note') || Gate::check('delete debit note'))
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                    {{ __('Action') }}
                </th>
            @endif
        </tr>
       </thead>

                            @forelse($bill->debitNote as $key =>$debitNote)
                                <tr>
                                    <td>{{ \Auth::user()->dateFormat($debitNote->date) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($debitNote->amount) }}</td>
                                    <td>{{ $debitNote->description }}</td>
                                    <td>
                                        <button
                                            class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                                            type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div
                                            class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                            @can('edit debit note')
                                                <li>
                                                    <a data-url="{{ route('bill.edit.debit.note', [$debitNote->bill, $debitNote->id]) }}"
                                                        data-ajax-popup="true" title="{{ __('Edit') }}" href="#"
                                                        class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                                        data-bs-toggle="tooltip" data-original-title="{{ __('Edit') }}">
                                                        <i class="ti ti-pencil"></i>
                                                        <span>{{ __('Edit') }}</span>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('delete debit note')
                                                <li>
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['bill.delete.debit.note', $debitNote->bill, $debitNote->id],
                                                        'id' => 'delete-form-' . $debitNote->id,
                                                    ]) !!}
                                                    <a href="#"
                                                        class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                        data-original-title="{{ __('Delete') }}"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $debitNote->id }}').submit();">
                                                        <i class="ti ti-trash"></i>
                                                        <span>{{ __('Delete') }}</span>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </li>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-dark">
                                        <p>{{ __('No Data Found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
