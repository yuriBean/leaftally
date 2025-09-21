@extends('layouts.admin')
@section('page-title')
    {{ __('Invoice Detail') }}
@endsection
@php
    $settings_data = \App\Models\Utility::settingsById($invoice->created_by);
    $settings = App\Models\Utility::settings();
@endphp
@push('css-page')
    <style>
        #card-element {
            border: 1px solid #a3afbb !important;
            border-radius: 10px !important;
            padding: 10px !important;
        }
    </style>
@endpush

@push('script-page')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script type="text/javascript">
        @if (
            $invoice->getDue() > 0 &&
                !empty($company_payment_setting) &&
                $company_payment_setting['is_stripe_enabled'] == 'on' &&
                !empty($company_payment_setting['stripe_key']) &&
                !empty($company_payment_setting['stripe_secret']))

            var stripe = Stripe('{{ $company_payment_setting['stripe_key'] }}');
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            var style = {
                base: {
                    // Add your base input styles here. For example:
                    fontSize: '14px',
                    color: '#32325d',
                },
            };

            // Create an instance of the card Element.
            var card = elements.create('card', {
                style: style
            });

            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#card-element');

            // Create a token or display an error when the form is submitted.
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        $("#card-errors").html(result.error.message);
                        show_toastr('error', result.error.message, 'error');
                    } else {
                        // Send the token to your server.
                        stripeTokenHandler(result.token);
                    }
                });
            });

            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }
        @endif

        @if (isset($company_payment_setting['paystack_public_key']))
            $(document).on("click", "#pay_with_paystack", function() {
                $('#paystack-payment-form').ajaxForm(function(res) {
                    var amount = res.total_price;
                    if (res.flag == 1) {
                        // var paystack_callback = "{{ url('/invoice/paystack') }}";

                        var handler = PaystackPop.setup({
                            key: '{{ $company_payment_setting['paystack_public_key'] }}',
                            email: res.email,
                            amount: res.total_price * 100,
                            currency: res.currency,
                            ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                                1
                            ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                            metadata: {
                                custom_fields: [{
                                    display_name: "Email",
                                    variable_name: "email",
                                    value: res.email,
                                }]
                            },

                            callback: function(response) {

                                window.location.href =
                                    '{{ url('customer/invoice/paystack') }}' + '/' +
                                    '{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}' +
                                    '/' + amount + '/' + response.reference;

                                // window.location.href = paystack_callback + '/' + response.reference + '/' + '{{ encrypt($invoice->id) }}' + '?amount=' + amount;
                            },
                            onClose: function() {
                                alert('window closed');
                            }
                        });
                        handler.openIframe();
                    } else if (res.flag == 2) {
                        toastrs('Error', res.msg, 'msg');
                    } else {
                        toastrs('Error', res.message, 'msg');
                    }

                }).submit();
            });
        @endif

        @if (isset($company_payment_setting['flutterwave_public_key']))
            //    Flaterwave Payment
            $(document).on("click", "#pay_with_flaterwave", function() {
                $('#flaterwave-payment-form').ajaxForm(function(res) {

                    if (res.flag == 1) {
                        var amount = res.total_price;
                        var API_publicKey = '{{ $company_payment_setting['flutterwave_public_key'] }}';
                        var nowTim = "{{ date('d-m-Y-h-i-a') }}";

                        var x = getpaidSetup({
                            PBFPubKey: API_publicKey,
                            customer_email: '{{ Auth::user()->email }}',
                            amount: res.total_price,
                            currency: '{{ App\Models\Utility::getValByName('site_currency') }}',
                            txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) +
                                'fluttpay_online-' + '{{ date('Y-m-d') }}' + '?amount=' + amount,
                            meta: [{
                                metaname: "payment_id",
                                metavalue: "id"
                            }],
                            onclose: function() {},
                            callback: function(response) {

                                // console.log(response.tx.chargeResponseCode);

                                var txref = response.tx.txRef;
                                if (
                                    response.tx.chargeResponseCode == "00" ||
                                    response.tx.chargeResponseCode == "0"
                                ) {

                                    window.location.href =
                                        '{{ url('customer/invoice/flaterwave') }}' + '/' +
                                        '{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}' +
                                        '/' + txref;
                                } else {
                                    // redirect to a failure page.
                                }
                                x
                                    .close(); // use this to close the modal immediately after payment.
                            }
                        });
                    } else if (res.flag == 2) {
                        toastrs('Error', res.msg, 'msg');
                    } else {
                        toastrs('Error', data.message, 'msg');
                    }

                }).submit();
            });
        @endif

        @if (isset($company_payment_setting['razorpay_public_key']))
            // Razorpay Payment
            $(document).on("click", "#pay_with_razorpay", function() {
                $('#razorpay-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var amount = res.total_price;
                        // var razorPay_callback = '{{ url('/invoice/razorpay') }}';
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var options = {
                            "key": "{{ $company_payment_setting['razorpay_public_key'] }}", // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": 'Plan',
                            "currency": '{{ App\Models\Utility::getValByName('site_currency') }}',
                            "description": "",
                            "handler": function(response) {
                                window.location.href = '{{ url('customer/invoice/razorpay') }}' +
                                    '/' +
                                    '{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}' +
                                    '/' + amount;
                                // window.location.href = '{{ route('customer.invoice.razorpay', ['response.razorpay_payment_id', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)], 'amount') }}' ;
                                // window.location.href = razorPay_callback + '/' + response.razorpay_payment_id + '/' + '{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}' + '?amount=' + amount;
                            },
                            "theme": {
                                "color": "#528FF0"
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    } else if (res.flag == 2) {
                        toastrs('Error', res.msg, 'msg');
                    } else {
                        toastrs('Error', data.message, 'msg');
                    }

                }).submit();
            });
        @endif





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
                    // console.log(data);
                }
            });
        })
    </script>
@endpush
@section('breadcrumb')
    @if (\Auth::guard('customer')->check())
        <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    @endif
    @if (\Auth::user()->type == 'company')
        <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">{{ __('Invoice ') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('customer.invoice') }}">{{ __('Invoice ') }}</a></li>
    @endif

    <li class="breadcrumb-item">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="#" class="btn btn-sm btn-primary  cp_link"
            data-link="{{ route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)) }}"
            data-bs-toggle="tooltip" title="{{ __('Copy invoice') }}"
            data-original-title="{{ __('Click to copy invoice link') }}">
            <span class="btn-inner--icon text-white"><i class="ti ti-file"></i></span>
        </a>
    </div>
@endsection

@section('content')

    @can('send invoice')
        @if ($invoice->status != 4)
            <div class="row">
                <div class="card ">
                    <div class="card-body">
                        <div class="row timeline-wrapper">
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <div class="timeline-icons"><span class="timeline-dots"></span>
                                    <i class="ti ti-plus text-primary"></i>
                                </div>
                                <h6 class="text-primary my-3">{{ __('Create Invoice') }}</h6>
                                <p class="text-muted text-sm mb-3"><i
                                        class="ti ti-clock mr-2"></i>{{ __('Created on ') }}{{ \Auth::user()->dateFormat($invoice->issue_date) }}
                                </p>
                                @can('edit invoice')
                                    <a href="{{ route('invoice.edit', \Crypt::encrypt($invoice->id)) }}"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                        data-original-title="{{ __('Edit') }}"><i
                                            class="ti ti-pencil mr-2"></i>{{ __('Edit') }}</a>
                                @endcan
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <div class="timeline-icons"><span class="timeline-dots"></span>
                                    <i class="ti ti-mail text-warning"></i>
                                </div>
                                <h6 class="text-warning my-3">{{ __('Send Invoice') }}</h6>
                                <p class="text-muted text-sm mb-3">
                                    @if ($invoice->status != 0)
                                        <i class="ti ti-clock mr-2"></i>{{ __('Sent on') }}
                                        {{ \Auth::user()->dateFormat($invoice->send_date) }}
                                    @else
                                        @can('send invoice')
                                            <small>{{ __('Status') }} : {{ __('Not Sent') }}</small>
                                        @endcan
                                    @endif
                                </p>

                                @if ($invoice->status == 0)
                                    @can('send bill')
                                        <a href="{{ route('invoice.sent', $invoice->id) }}" class="btn btn-sm btn-warning"
                                            data-bs-toggle="tooltip" data-original-title="{{ __('Mark Sent') }}"><i
                                                class="ti ti-send mr-2"></i>{{ __('Send') }}</a>
                                    @endcan
                                @endif
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <div class="timeline-icons"><span class="timeline-dots"></span>
                                    <i class="ti ti-currency-naira text-info"></i>
                                </div>
                                <h6 class="text-info my-3">{{ __('Get Paid') }}</h6>
                                <p class="text-muted text-sm mb-3">{{ __('Status') }} : {{ __('Awaiting payment') }} </p>
                                @if ($invoice->status != 0)
                                    @can('create payment invoice')
                                        <a href="#" data-url="{{ route('invoice.payment', $invoice->id) }}"
                                            data-ajax-popup="true" data-title="{{ __('Add Payment') }}" class="btn btn-sm btn-info"
                                            data-original-title="{{ __('Add Payment') }}"><i
                                                class="ti ti-currency-naira mr-2"></i>{{ __('Add Payment') }}</a> <br>
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
        @if ($invoice->status != 0)
            <div class="row justify-content-between align-items-center mb-3">
                <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                    @if (!empty($invoicePayment))
                        <div class="all-button-box mx-2 mr-2">
                            <a href="#" class="btn btn-sm btn-primary"
                                data-url="{{ route('invoice.credit.note', $invoice->id) }}" data-ajax-popup="true"
                                data-title="{{ __('Add Credit Note') }}">
                                {{ __('Add Credit Note') }}
                            </a>
                        </div>
                    @endif
                    @if ($invoice->status != 4)
                        <div class="all-button-box mr-2">
                            <a href="{{ route('invoice.payment.reminder', $invoice->id) }}"
                                class="btn btn-sm btn-primary">{{ __('Receipt Reminder') }}</a>
                        </div>
                    @endif
                    <div class="all-button-box mr-2">
                        <a href="{{ route('invoice.resent', $invoice->id) }}"
                            class="btn btn-sm btn-primary bs-resend-confirm">{{ __('Resend Invoice') }}</a>
                    </div>
                    <div class="all-button-box">
                        <a href="{{ route('invoice.pdf', Crypt::encrypt($invoice->id)) }}" target="_blank"
                            class="btn btn-sm btn-primary box">
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
                    <a href="#" class="btn btn-xs btn-primary btn-icon-only width-auto"
                        data-url="{{ route('customer.invoice.send', $invoice->id) }}" data-ajax-popup="true"
                        data-title="{{ __('Send Invoice') }}">
                        {{ __('Send Mail') }}
                    </a>
                </div>
                <div class="all-button-box mx-2">
                    <a href="{{ route('invoice.pdf', Crypt::encrypt($invoice->id)) }}" target="_blank"
                        class="btn btn-xs btn-primary btn-icon-only width-auto">
                        {{ __('Download') }}
                    </a>
                </div>

                @if (
                    $invoice->getDue() > 0 &&
                        !empty($company_payment_setting) &&
                        ($company_payment_setting['is_stripe_enabled'] == 'on' ||
                            $company_payment_setting['is_paypal_enabled'] == 'on' ||
                            $company_payment_setting['is_paystack_enabled'] == 'on' ||
                            $company_payment_setting['is_flutterwave_enabled'] == 'on' ||
                            $company_payment_setting['is_razorpay_enabled'] == 'on' ||
                            $company_payment_setting['is_mercado_enabled'] == 'on' ||
                            $company_payment_setting['is_paytm_enabled'] == 'on' ||
                            $company_payment_setting['is_mollie_enabled'] == 'on' ||
                            $company_payment_setting['is_paypal_enabled'] == 'on' ||
                            $company_payment_setting['is_skrill_enabled'] == 'on' ||
                            $company_payment_setting['is_coingate_enabled'] == 'on' ||
                            $company_payment_setting['is_paymentwall_enabled'] == 'on' ||
                            $company_payment_setting['is_toyyibpay_enabled'] == 'on' ||
                            $company_payment_setting['is_payfast_enabled'] == 'on' ||
                            $company_payment_setting['is_bank_enabled'] == 'on' ||
                            $company_payment_setting['is_iyzipay_enabled'] == 'on' ||
                            $company_payment_setting['is_paytab_enabled'] == 'on' ||
                            $company_payment_setting['is_benefit_enabled'] == 'on' ||
                            $company_payment_setting['is_cashfree_enabled'] == 'on' ||
                            $company_payment_setting['is_aamarpay_enabled'] == 'on' ||
                            $company_payment_setting['is_paytr_enabled'] == 'on'))
                    <div class="all-button-box">
                        <a href="#" class="btn btn-xs btn-primary btn-icon-only width-auto" data-bs-toggle="modal"
                            data-bs-target="#paymentModal">
                            {{ __('Pay Now') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="row">
        <!-- <div class="col-12"> -->
        <div class="card">
            <div class="card-body">
                <div class="invoice">
                    <div class="invoice-print">
                        <div class="row invoice-title mt-2">
                            <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12">
                                <h2>{{ __('Invoice') }}</h2>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12 text-end">
                                <h3 class="invoice-number">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                                </h3>
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
                                            {{ \Auth::user()->dateFormat($invoice->issue_date) }}<br><br>
                                        </small>
                                    </div>
                                    <div>
                                        <small>
                                            <strong>{{ __('Due Date') }} :</strong><br>
                                            {{ \Auth::user()->dateFormat($invoice->due_date) }}<br><br>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <strong>
                                    {{ $customer->name }}
                                </strong>
                                <strong>
                                    {{ $customer->contact }}
                                </strong>
                            @if (!empty($customer->billing_name))
                                <div class="col">
                                    <small class="font-style">
                                        <strong>{{ __('Billed To') }} :</strong><br>
                                        {{ !empty($customer->billing_name) ? $customer->billing_name : '' }}<br>
                                        {{ !empty($customer->billing_address) ? $customer->billing_address : '' }}<br>
                                        {{ !empty($customer->billing_city) ? $customer->billing_city : '' . ', ' }},
                                        {{ !empty($customer->billing_state) ? $customer->billing_state : '' . ', ' }}
                                        {{ !empty($customer->billing_zip) ? $customer->billing_zip : '' }}<br>
                                        {{ !empty($customer->billing_country) ? $customer->billing_country : '' }}<br>
                                        {{ !empty($customer->billing_phone) ? $customer->billing_phone : '' }}<br>
                                        <strong>{{ __('Tax Number ') }} :
                                        </strong>{{ !empty($customer->tax_number) ? $customer->tax_number : '' }}<br>
                                        @if (!empty($settings['tax_type']) && !empty($settings['vat_number']))
                                            {{ $settings['tax_type'] . ' ' . __('Number') }} : {{ $settings['vat_number'] }}
                                            <br>
                                        @endif
                                    </small>
                                </div>
                            @endif
                            @if (App\Models\Utility::getValByName('shipping_display') == 'on')
                                <div class="col ">
                                    <small>
                                        <strong>{{ __('Shipped To') }} :</strong><br>
                                        {{ !empty($customer->shipping_name) ? $customer->shipping_name : '' }}<br>
                                        {{ !empty($customer->shipping_address) ? $customer->shipping_address : '' }}<br>
                                        {{ !empty($customer->shipping_city) ? $customer->shipping_city : '' . ', ' }},
                                        {{ !empty($customer->shipping_state) ? $customer->shipping_state : '' . ', ' }}
                                        {{ !empty($customer->shipping_zip) ? $customer->shipping_zip : '' }}<br>
                                        {{ !empty($customer->shipping_country) ? $customer->shipping_country : '' }}<br>
                                        {{ !empty($customer->shipping_phone) ? $customer->shipping_phone : '' }}<br>
                                        <strong>{{ __('Tax Number ') }} :
                                        </strong>{{ !empty($customer->tax_number) ? $customer->tax_number : '' }}

                                    </small>
                                </div>
                            @endif
                            <div class="col">
                                <div class="float-end mt-3">
                                    @if (isset($settings_data['invoice_qr_display']) && $settings_data['invoice_qr_display'] == 'on')
                                        {!! DNS2D::getBarcodeHTML(
                                            route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)),
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
                                    @if ($invoice->status == 0)
                                        <span
                                            class="badge fix_badge  p-1 px-3 bg-primary">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 1)
                                        <span
                                            class="badge fix_badge  p-1 px-3 bg-info">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 2)
                                        <span
                                            class="badge fix_badge  p-1 px-3 bg-secondary">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 3)
                                        <span
                                            class="badge fix_badge  p-1 px-3 bg-warning">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @elseif($invoice->status == 4)
                                        <span
                                            class="badge fix_badge  p-1 px-3 bg-danger">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                    @endif
                                </small>
                            </div>



                            @if (!empty($customFields) && count($invoice->customField) > 0)
                                @foreach ($customFields as $field)
                                    <div class="col text-md-right">
                                        <small>
                                            <strong>{{ $field->name }} :</strong><br>
                                            {{ !empty($invoice->customField) ? $invoice->customField[$field->id] : '-' }}
                                            <br><br>
                                        </small>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="font-weight-bold">{{ __('Product Summary') }}</div>
                                <small>{{ __('All items here cannot be deleted.') }}</small>
                                <div class="table-responsive table-new-design mt-2">
                                    <table class="table datatable border border-[#E5E5E5] rounded-[8px]">
                                        <tr>
                                            <th data-width="40" class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">#</th>
                                            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Product') }}</th>
                                            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Quantity') }}</th>
                                            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Rate') }}</th>
                                            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Discount') }}</th>
                                            <th class="text-dark">{{ __('Tax') }}</th>
                                            <th class="text-dark">{{ __('Description') }}</th>
                                            <th class="text-right text-dark" width="12%">{{ __('Price') }}<br>
                                                <small
                                                    class="text-danger font-weight-bold">{{ __('before tax & discount') }}</small>
                                            </th>
                                        </tr>
                                        @php
                                            $totalQuantity = 0;
                                            $totalRate = 0;
                                            $totalTaxPrice = 0;
                                            $totalDiscount = 0;
                                            $taxesData = [];
                                            $TaxPrice_array = [];
                                        @endphp
                                        @foreach ($iteams as $key => $iteam)
                                            @if (!empty($iteam->tax))
                                                @php
                                                    $taxes = App\Models\Utility::tax($iteam->tax);
                                                    $totalQuantity += $iteam->quantity;
                                                    $totalRate += $iteam->price;
                                                    $totalDiscount += $iteam->discount;
                                                    foreach ($taxes as $taxe) {
                                                        $taxDataPrice = App\Models\Utility::taxRate(
                                                            $taxe->rate,
                                                            $iteam->price,
                                                            $iteam->quantity,
                                                            $iteam->discount,
                                                        );
                                                        if (array_key_exists($taxe->name, $taxesData)) {
                                                            $taxesData[$taxe->name] =
                                                                $taxesData[$taxe->name] + $taxDataPrice;
                                                        } else {
                                                            $taxesData[$taxe->name] = $taxDataPrice;
                                                        }
                                                    }
                                                @endphp
                                            @endif
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ !empty($iteam->product) ? $iteam->product->name : '' }}</td>
                                                <td>{{ $iteam->quantity }} ({{ $iteam->product->unit->name }})</td>
                                                <td>{{ \Auth::user()->priceFormat($iteam->price) }}</td>
                                                <td>
                                                    {{ \Auth::user()->priceFormat($iteam->discount) }}
                                                </td>
                                                <td>
                                                    @if (!empty($iteam->tax))
                                                        <table>
                                                            @php
                                                                $totalTaxRate = 0;
                                                                $data = 0;
                                                            @endphp
                                                            @foreach ($taxes as $tax)
                                                                @php
                                                                    $taxPrice = App\Models\Utility::taxRate(
                                                                        $tax->rate,
                                                                        $iteam->price,
                                                                        $iteam->quantity,
                                                                        $iteam->discount,
                                                                    );
                                                                    $totalTaxPrice += $taxPrice;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $tax->name . ' (' . $tax->rate . '%)' }}</td>
                                                                    <td>{{ \Auth::user()->priceFormat($taxPrice) }}</td>
                                                                </tr>
                                                            @endforeach
                                                            @php
                                                                array_push($TaxPrice_array, $data);
                                                            @endphp
                                                        </table>
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td>{{ !empty($iteam->description) ? $iteam->description : '-' }}</td>
                                                @php
                                                    $tr_tex =
                                                        array_key_exists($key, $TaxPrice_array) == true
                                                            ? $TaxPrice_array[$key]
                                                            : 0;
                                                @endphp
                                                <td class="text-right">
                                                    {{ \Auth::user()->priceFormat($iteam->price * $iteam->quantity - $iteam->discount + $tr_tex) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td><b>{{ __('Total') }}</b></td>
                                                <td><b>{{ $totalQuantity }}</b></td>
                                                <td><b>{{ \Auth::user()->priceFormat($totalRate) }}</b></td>
                                                <td>
                                                    <b>{{ \Auth::user()->priceFormat($totalDiscount) }}</b>

                                                </td>
                                                <td><b>{{ \Auth::user()->priceFormat($totalTaxPrice) }}</b></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td class="text-right"><b>{{ __('Sub Total') }}</b></td>
                                                <td class="text-right">
                                                    {{ \Auth::user()->priceFormat($invoice->getSubTotal()) }}</td>
                                            </tr>

                                            <tr>
                                                <td colspan="6"></td>
                                                <td class="text-right"><b>{{ __('Discount') }}</b></td>
                                                <td class="text-right">
                                                    {{ \Auth::user()->priceFormat($invoice->getTotalDiscount()) }}</td>
                                            </tr>

                                            {{-- @if (!empty($taxesData))
                                                @php
                                                    $totalTaxRate = 0;
                                                    $totalTaxPrice = 0;
                                                @endphp
                                                @foreach ($taxes as $tax)
                                                    @php
                                                        $taxPrice = App\Models\Utility::taxRate($tax->rate, $iteam->price, $iteam->quantity, $iteam->discount);
                                                        $totalTaxPrice += $taxPrice;
                                                    @endphp
                                                    <tr>
                                                        <td colspan="6"></td>
                                                        <td class="text-right"><b>{{ $tax->name }}</b></td>
                                                        <td class="text-right">
                                                            {{ \Auth::user()->priceFormat($taxPrice) }}</td>

                                                    </tr>
                                                @endforeach
                                            @endif --}}
                                            @if (!empty($taxesData))
                                                @foreach ($taxesData as $taxName => $taxPrice)
                                                    <tr>
                                                        <td colspan="6"></td>
                                                        <td class="text-right"><b>{{ $taxName }}</b></td>
                                                        <td class="text-right">
                                                            {{ \Auth::user()->priceFormat($taxPrice) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            <tr>
                                                <td colspan="6"></td>
                                                <td class="blue-text text-right"><b>{{ __('Total') }}</b></td>
                                                <td class="blue-text text-right">
                                                    {{ \Auth::user()->priceFormat($invoice->getTotal()) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td class="text-right"><b>{{ __('Paid') }}</b></td>
                                                <td class="text-right">
                                                    {{ \Auth::user()->priceFormat($invoice->getTotal() - $invoice->getDue() - $invoice->invoiceTotalCreditNote()) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td class="text-right"><b>{{ __('Credit Note') }}</b></td>
                                                <td class="text-right">
                                                    {{ \Auth::user()->priceFormat($invoice->invoiceTotalCreditNote()) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td class="text-right"><b>{{ __('Due') }}</b></td>
                                                <td class="text-right">
                                                    {{ \Auth::user()->priceFormat($invoice->getDue()) }}</td>
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
        <!-- </div> -->


        <!-- <div class="col-12"> -->
        <h5 class="h4 d-inline-block font-weight-400 mb-2">{{ __('Receipt Summary') }}</h5>
        <div class="card">
            <div class="card-body table-border-style pt-0 px-3">
               <div class="table-responsive table-new-design mt-0">
    <table class="table datatable border border-[#E5E5E5] rounded-[8px]">
        <thead>
        <tr>
            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Payment receipt') }}</th>
            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Date') }}</th>
            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Amount') }}</th>
            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Payment type') }}</th>
            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Account') }}</th>
            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Reference') }}</th>
            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Description') }}</th>
            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Receipt') }}</th>
            <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('OrderId') }}</th>
            @can('delete payment invoice')
                <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Action') }}</th>
            @endcan
        </tr>
    </thead>
                        @foreach ($invoice->payments as $key => $payment)
                            @php
                                $path = \App\Models\Utility::get_file('/uploads/bank_receipt');
                            @endphp
                            <tr>
                                <td>
                                    @if (!empty($payment->add_receipt))
                                        <a href="{{ asset(Storage::url('uploads/payment/' . $payment->add_receipt)) }}" download=""
                                            class="btn btn-sm btn-primary btn-icon rounded-pill" target="_blank"><span
                                                class="btn-inner--icon"><i class="ti ti-file-text"></i></span></a>
                                        <a href="{{ asset(Storage::url('uploads/payment/' . $payment->add_receipt)) }}"
                                            class="btn btn-sm btn-secondary btn-icon rounded-pill" target="_blank"><span
                                                class="btn-inner--icon"><i class="ti ti-crosshair"></i></span></a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ \Auth::user()->dateFormat($payment->date) }}</td>
                                <td>{{ \Auth::user()->priceFormat($payment->amount) }}</td>
                                <td>{{ $payment->payment_type }}</td>
                                <td>{{ !empty($payment->bankAccount) ? $payment->bankAccount->bank_name . ' ' . $payment->bankAccount->holder_name : '--' }}
                                </td>
                                <td>{{ !empty($payment->reference) ? $payment->reference : '--' }}</td>
                                <td>{{ !empty($payment->description) ? $payment->description : '--' }}</td>
                                <td>
                                    @if (!empty($payment->receipt))
                                        <a href="{{ asset(Storage::url('uploads/bank_receipt/' . $payment->receipt)) }}" target="_blank">
                                            <i class="ti ti-file"></i></a>
                                    @elseif(!empty($payment->add_receipt))
                                        <a href="{{ asset(Storage::url('uploads/payment/' . $payment->add_receipt)) }}" target="_blank">
                                            <i class="ti ti-file"></i></a>
                                    @else
                                        --
                                    @endif
                                </td>
                                <td>{{ !empty($payment->order_id) ? $payment->order_id : '--' }}</td>
                                @can('delete invoice product')
                                    <td>
                                        <div class="action-btn bg-danger ms-2">
                                            {!! Form::open([
                                                'method' => 'post',
                                                'route' => ['invoice.payment.destroy', $invoice->id, $payment->id],
                                                'id' => 'delete-form-' . $payment->id,
                                            ]) !!}
                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para "
                                                data-bs-toggle="tooltip" title="Delete"
                                                data-original-title="{{ __('Delete') }}"
                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="document.getElementById('delete-form-{{ $payment->id }}').submit();">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>

                                            {!! Form::close() !!}
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                        @php
                            $path = \App\Models\Utility::get_file('/uploads/bank_receipt');
                        @endphp
                        @foreach ($invoice->bankpayment as $key => $bankpayment)
                            <tr>
                                <td>
                                    @if (!empty($payment->add_receipt))
                                        -
                                        {{-- <a href="{{ $paymentpath . '/' . $bankpayment->add_receipt }}" download=""
                                            class="btn btn-sm btn-primary btn-icon rounded-pill" target="_blank"><span
                                                class="btn-inner--icon"><i class="ti ti-download"></i></span></a>
                                        <a href="{{ $paymentpath . '/' . $bankpayment->add_receipt }}"
                                            class="btn btn-sm btn-secondary btn-icon rounded-pill" target="_blank"><span
                                                class="btn-inner--icon"><i class="ti ti-crosshair"></i></span></a> --}}
                                        {{-- <a href="{{asset(Storage::url('uploads/payment')).'/'.$payment->add_receipt}}"  class="btn btn-sm btn-secondary btn-icon rounded-pill" target="_blank"><span class="btn-inner--icon"><i class="ti ti-crosshair"></i></span></a> --}}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ \Auth::user()->dateFormat($bankpayment->created_at) }}</td>
                                <td>{{ \Auth::user()->priceFormat($bankpayment->amount) }}</td>
                                <td>{{ 'Bank Transfer' }}</td>
                                <td>{{ !empty($bankpayment->bankAccount) ? $bankpayment->bankAccount->bank_name . ' ' . $bankpayment->bankAccount->holder_name : '--' }}
                                </td>
                                <td>{{ !empty($bankpayment->reference) ? $bankpayment->reference : '--' }}</td>
                                <td>{{ !empty($bankpayment->description) ? $bankpayment->description : '--' }}</td>
                                <td>
                                    @if (!empty($bankpayment->receipt))
                                        <a href="{{ asset(Storage::url('uploads/bank_receipt/' . $bankpayment->receipt)) }}" target="_blank"> <i
                                                class="ti ti-file"></i></a>
                                    @else
                                        --
                                    @endif
                                </td>
                                <td>{{ !empty($bankpayment->order_id) ? $bankpayment->order_id : '--' }}</td>

                                @can('delete payment invoice')
                                    <td>
                                        {{-- @if ($payment->payment_status == 'pending' && $payment->payment_type == 'Bank Transfer') --}}
                                        <div class="action-btn me-2">
                                            <a href="#"
                                                class="mx-3 btn btn-sm d-inline-flex align-items-center bg-warning"
                                                data-bs-toggle="modal" data-size="lg" data-ajax-popup="true"
                                                data-url="{{ route('invoice.payment.show', [$bankpayment->id]) }}"
                                                data-title="{{ __('Payment Status') }}" data-size="lg">
                                                <span class="text-white"> <i class="ti ti-caret-right text-white"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('Payment Status') }}"></i></span></a>
                                        </div>
                                        {{-- @endif --}}
                                        <div class="action-btn">
                                            {!! Form::open([
                                                'method' => 'delete',
                                                'route' => ['invoice.delete', $bankpayment->id],
                                                'id' => 'delete-form-' . $bankpayment->id,
                                            ]) !!}
                                            <a href="#"
                                                class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                data-bs-toggle="tooltip" title="Delete"
                                                data-original-title="{{ __('Delete') }}"
                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="document.getElementById('delete-form-{{ $bankpayment->id }}').submit();">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>

                                            {!! Form::close() !!}
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <!-- </div> -->
        <div class="row"></div>
        <!-- <div class="col-12"> -->
        <h5 class="h4 d-inline-block font-weight-400 mb-2">{{ __('Credit Note Summary') }}</h5>
        <div class="card">
            <div class="card-body table-border-style table-border-style px-3 pt-0">
    <div class="table-responsive table-new-design mt-0">
        <table class="table datatable border border-[#E5E5E5] rounded-[8px]">
           <thead>
                <tr>
                <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Date') }}</th>
                <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Amount') }}</th>
                <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Description') }}</th>
                @if (Gate::check('edit credit note') || Gate::check('delete credit note'))
                    <th class="border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Action') }}</th>
                @endif
            </tr>
           </thead>
        </table>
    </div>
</div>

                        @forelse($invoice->creditNote as $key =>$creditNote)
                            <tr>
                                <td>{{ \Auth::user()->dateFormat($creditNote->date) }}</td>
                                <td class="">{{ \Auth::user()->priceFormat($creditNote->amount) }}</td>
                                <td class="">{{ $creditNote->description }}</td>
                                <td>
                                    @can('edit credit note')
                                        <div class="action-btn me-2">
                                            <a data-url="{{ route('invoice.edit.credit.note', [$creditNote->invoice, $creditNote->id]) }}"
                                                data-ajax-popup="true" title="{{ __('Edit') }}"
                                                data-original-title="{{ __('Credit Note') }}" href="#"
                                                class="mx-3 btn btn-sm align-items-center bg-primary" data-bs-toggle="tooltip"
                                                data-original-title="{{ __('Edit') }}">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>
                                    @endcan
                                    @can('delete credit note')
                                        <div class="action-btn">
                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['invoice.delete.credit.note', $creditNote->invoice, $creditNote->id],
                                                'id' => 'delete-form-' . $creditNote->id,
                                            ]) !!}
                                            <a href="#"
                                                class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                data-bs-toggle="tooltip" title="Delete"
                                                data-original-title="{{ __('Delete') }}"
                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="document.getElementById('delete-form-{{ $creditNote->id }}').submit();">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>
                                            {!! Form::close() !!}
                                        </div>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    <p class="text-dark">{{ __('No Data Found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
        <!-- </div> -->
    </div>

    @auth('customer')
        @if ($invoice->getDue() > 0)
            <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title font-[700] text-[16px] text-black leading-[24px]" id="paymentModalLabel">
                                {{ __('Add Payment') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="card bg-none card-box">
                                <section class="nav-tabs p-2">
                                    @if (
                                        !empty($company_payment_setting) &&
                                            ($company_payment_setting['is_stripe_enabled'] == 'on' ||
                                                $company_payment_setting['is_paypal_enabled'] == 'on' ||
                                                $company_payment_setting['is_paystack_enabled'] == 'on' ||
                                                $company_payment_setting['is_flutterwave_enabled'] == 'on' ||
                                                $company_payment_setting['is_razorpay_enabled'] == 'on' ||
                                                $company_payment_setting['is_mercado_enabled'] == 'on' ||
                                                $company_payment_setting['is_paytm_enabled'] == 'on' ||
                                                $company_payment_setting['is_mollie_enabled'] == 'on' ||
                                                $company_payment_setting['is_paypal_enabled'] == 'on' ||
                                                $company_payment_setting['is_skrill_enabled'] == 'on' ||
                                                $company_payment_setting['is_coingate_enabled'] == 'on' ||
                                                $company_payment_setting['is_paymentwall_enabled'] == 'on' ||
                                                $company_payment_setting['is_toyyibpay_enabled'] == 'on' ||
                                                $company_payment_setting['is_payfast_enabled'] == 'on' ||
                                                $company_payment_setting['is_bank_enabled'] == 'on' ||
                                                $company_payment_setting['is_iyzipay_enabled'] == 'on' ||
                                                $company_payment_setting['is_paytab_enabled'] == 'on' ||
                                                $company_payment_setting['is_benefit_enabled'] == 'on' ||
                                                $company_payment_setting['is_cashfree_enabled'] == 'on' ||
                                                $company_payment_setting['is_aamarpay_enabled'] == 'on' ||
                                                $company_payment_setting['is_paytr_enabled'] == 'on'))
                                        <ul class="nav nav-pills  mb-3" role="tablist">
                                            @if (
                                                $company_payment_setting['is_stripe_enabled'] == 'on' &&
                                                    !empty($company_payment_setting['stripe_key']) &&
                                                    !empty($company_payment_setting['stripe_secret']))
                                                <li class="nav-item mb-2">
                                                    <a class="btn btn-outline-primary btn-sm active" data-bs-toggle="tab"
                                                        href="#stripe-payment" role="tab" aria-controls="stripe"
                                                        aria-selected="true">{{ __('Stripe') }}</a>
                                                </li>
                                            @endif

                                            @if (
                                                $company_payment_setting['is_paypal_enabled'] == 'on' &&
                                                    !empty($company_payment_setting['paypal_client_id']) &&
                                                    !empty($company_payment_setting['paypal_secret_key']))
                                                <li class="nav-item mb-2">
                                                    <a class="btn btn-outline-primary btn-sm ml-1" data-bs-toggle="tab"
                                                        href="#paypal-payment" role="tab" aria-controls="paypal"
                                                        aria-selected="false">{{ __('Paypal') }}</a>
                                                </li>
                                            @endif

                                            @if (
                                                $company_payment_setting['is_paystack_enabled'] == 'on' &&
                                                    !empty($company_payment_setting['paystack_public_key']) &&
                                                    !empty($company_payment_setting['paystack_secret_key']))
                                                <li class="nav-item mb-2">
                                                    <a class="btn btn-outline-primary btn-sm ml-1" data-bs-toggle="tab"
                                                        href="#paystack-payment" role="tab" aria-controls="paystack"
                                                        aria-selected="false">{{ __('Paystack') }}</a>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_flutterwave_enabled']) &&
                                                    $company_payment_setting['is_flutterwave_enabled'] == 'on')
                                                <li class="nav-item mb-2">
                                                    <a class="btn btn-outline-primary btn-sm ml-1" data-bs-toggle="tab"
                                                        href="#flutterwave-payment" role="tab"
                                                        aria-controls="flutterwave"
                                                        aria-selected="false">{{ __('Flutterwave') }}</a>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_razorpay_enabled']) && $company_payment_setting['is_razorpay_enabled'] == 'on')
                                                <li class="nav-item mb-2">
                                                    <a class="btn btn-outline-primary btn-sm ml-1" data-bs-toggle="tab"
                                                        href="#razorpay-payment" role="tab" aria-controls="razorpay"
                                                        aria-selected="false">{{ __('Razorpay') }}</a>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_mercado_enabled']) && $company_payment_setting['is_mercado_enabled'] == 'on')
                                                <li class="nav-item mb-2">
                                                    <a class="btn btn-outline-primary btn-sm ml-1" data-bs-toggle="tab"
                                                        href="#mercado-payment" role="tab" aria-controls="mercado"
                                                        aria-selected="false">{{ __('Mercado') }}</a>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_paytm_enabled']) && $company_payment_setting['is_paytm_enabled'] == 'on')
                                                <li class="nav-item mb-2">
                                                    <a class="btn btn-outline-primary btn-sm ml-1" data-bs-toggle="tab"
                                                        href="#paytm-payment" role="tab" aria-controls="paytm"
                                                        aria-selected="false">{{ __('Paytm') }}</a>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_mollie_enabled']) && $company_payment_setting['is_mollie_enabled'] == 'on')
                                                <li class="nav-item mb-2">
                                                    <a class="btn btn-outline-primary btn-sm ml-1" data-bs-toggle="tab"
                                                        href="#mollie-payment" role="tab" aria-controls="mollie"
                                                        aria-selected="false">{{ __('Mollie') }}</a>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_skrill_enabled']) && $company_payment_setting['is_skrill_enabled'] == 'on')
                                                <li class="nav-item mb-2">
                                                    <a class="btn btn-outline-primary btn-sm ml-1" data-bs-toggle="tab"
                                                        href="#skrill-payment" role="tab" aria-controls="skrill"
                                                        aria-selected="false">{{ __('Skrill') }}</a>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_coingate_enabled']) && $company_payment_setting['is_coingate_enabled'] == 'on')
                                                <li class="nav-item mb-2">
                                                    <a class="btn btn-outline-primary btn-sm ml-1" data-bs-toggle="tab"
                                                        href="#coingate-payment" role="tab" aria-controls="coingate"
                                                        aria-selected="false">{{ __('Coingate') }}</a>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_paymentwall_enabled']) &&
                                                    $company_payment_setting['is_paymentwall_enabled'] == 'on')
                                                <li class="nav-item mb-2">
                                                    <a class="btn btn-outline-primary btn-sm ml-1" data-bs-toggle="tab"
                                                        href="#paymentwall-payment" role="tab"
                                                        aria-controls="paymentwall"
                                                        aria-selected="false">{{ __('PaymentWall') }}</a>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_toyyibpay_enabled']) && $company_payment_setting['is_toyyibpay_enabled'] == 'on')
                                                <li class="nav-item mb-2">
                                                    <button class=" btn btn-outline-success btn-sm" id="pills-home-tab"
                                                        data-bs-toggle="pill" data-bs-target="#toyyibpay-payment"
                                                        type="button" role="tab" aria-controls="pills-home"
                                                        aria-selected="true">{{ __('Toyyibpay') }}</button>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_toyyibpay_enabled']) && $company_payment_setting['is_toyyibpay_enabled'] == 'on')
                                                <li class="nav-item mb-2">

                                                    <button class=" btn btn-outline-success btn-sm" id="pills-home-tab"
                                                        data-bs-toggle="pill" data-bs-target="#toyyibpay-payment"
                                                        type="button" role="tab" aria-controls="pills-home"
                                                        aria-selected="true">{{ __('Toyyibpay') }}</button>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_payfast_enabled']) && $company_payment_setting['is_payfast_enabled'] == 'on')
                                                <li class="nav-item mb-2">

                                                    <button class=" btn btn-outline-success btn-sm" id="pills-home-tab"
                                                        data-bs-toggle="pill" data-bs-target="#payfast-payment"
                                                        type="button" role="tab" aria-controls="pills-home"
                                                        aria-selected="true"
                                                        onclick=get_payfast_status()>{{ __('PayFast') }}</button>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_iyzipay_enabled']) && $company_payment_setting['is_iyzipay_enabled'] == 'on')
                                                <li class="nav-item mb-2">

                                                    <button class=" btn btn-outline-success btn-sm" id="pills-home-tab"
                                                        data-bs-toggle="pill" data-bs-target="#iyzipay-payment"
                                                        type="button" role="tab" aria-controls="pills-home"
                                                        aria-selected="true">{{ __('IyziPay') }}</button>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_sspay_enabled']) && $company_payment_setting['is_sspay_enabled'] == 'on')
                                                <li class="nav-item mb-2">

                                                    <button class="btn btn-outline-success btn-sm" id="pills-home-tab"
                                                        data-bs-toggle="pill" data-bs-target="#sspay-payment" type="button"
                                                        role="tab" aria-controls="pills-home"
                                                        aria-selected="true">{{ __('Sspay') }}</button>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_paytab_enabled']) && $company_payment_setting['is_paytab_enabled'] == 'on')
                                                <li class="nav-item mb-2">

                                                    <button class="btn btn-outline-success btn-sm" id="pills-home-tab"
                                                        data-bs-toggle="pill" data-bs-target="#paytab-payment" type="button"
                                                        role="tab" aria-controls="pills-home"
                                                        aria-selected="true">{{ __('Paytab') }}</button>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_benefit_enabled']) && $company_payment_setting['is_benefit_enabled'] == 'on')
                                                <li class="nav-item mb-2">

                                                    <button class="btn btn-outline-success btn-sm" id="pills-home-tab"
                                                        data-bs-toggle="pill" data-bs-target="#benefit-payment"
                                                        type="button" role="tab" aria-controls="pills-home"
                                                        aria-selected="true">{{ __('Benefit') }}</button>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_cashfree_enabled']) && $company_payment_setting['is_cashfree_enabled'] == 'on')
                                                <li class="nav-item mb-2">

                                                    <button class="btn btn-outline-success btn-sm" id="pills-home-tab"
                                                        data-bs-toggle="pill" data-bs-target="#cashfree-payment"
                                                        type="button" role="tab" aria-controls="pills-home"
                                                        aria-selected="true">{{ __('Cashfree') }}</button>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_aamarpay_enabled']) && $company_payment_setting['is_aamarpay_enabled'] == 'on')
                                                <li class="nav-item mb-2">

                                                    <button class="btn btn-outline-success btn-sm" id="pills-home-tab"
                                                        data-bs-toggle="pill" data-bs-target="#aamarpay-payment"
                                                        type="button" role="tab" aria-controls="pills-home"
                                                        aria-selected="true">{{ __('Aamarpay') }}</button>
                                                </li>
                                            @endif

                                            @if (isset($company_payment_setting['is_paytr_enabled']) && $company_payment_setting['is_paytr_enabled'] == 'on')
                                                <li class="nav-item mb-2">

                                                    <button class="btn btn-outline-success btn-sm" id="pills-home-tab"
                                                        data-bs-toggle="pill" data-bs-target="#paytr-payment" type="button"
                                                        role="tab" aria-controls="pills-home"
                                                        aria-selected="true">{{ __('PayTR') }}</button>
                                                </li>
                                            @endif


                                        </ul>
                                    @endif
                                    <div class="tab-content">
                                        @if (
                                            !empty($company_payment_setting) &&
                                                ($company_payment_setting['is_stripe_enabled'] == 'on' &&
                                                    !empty($company_payment_setting['stripe_key']) &&
                                                    !empty($company_payment_setting['stripe_secret'])))
                                            <div class="tab-pane fade active show" id="stripe-payment" role="tabpanel"
                                                aria-labelledby="stripe-payment">
                                                <form method="post"
                                                    action="{{ route('customer.invoice.payment', $invoice->id) }}"
                                                    class="require-validation" id="payment-form">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <div class="custom-radio">
                                                                <label
                                                                    class="font-16 font-weight-bold">{{ __('Credit / Debit Card') }}</label>
                                                            </div>
                                                            <p class="mb-0 pt-1 text-sm">
                                                                {{ __('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.') }}
                                                            </p>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="card-name-on">{{ __('Name on card') }}</label>
                                                                <input type="text" name="name" id="card-name-on"
                                                                    class="form-control required"
                                                                    placeholder="{{ \Auth::user()->name }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div id="card-element">

                                                            </div>
                                                            <div id="card-errors" role="alert"></div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <br>
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="error" style="display: none;">
                                                                <div class='alert-danger alert'>
                                                                    {{ __('Please correct the errors and try again.') }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <button class="btn btn-sm btn-primary m-r-10"
                                                            type="submit">{{ __('Make Payment') }} </button>
                                                    </div>
                                                    <!-- <div class="form-group mt-3">
                                                                                                                                        <button class="btn btn-sm btn-primary rounded-pill" type="submit">{{ __('Make Payment') }}</button>
                                                                                                                                    </div> -->
                                                </form>
                                            </div>
                                        @endif

                                        @if (
                                            !empty($company_payment_setting) &&
                                                ($company_payment_setting['is_paypal_enabled'] == 'on' &&
                                                    !empty($company_payment_setting['paypal_client_id']) &&
                                                    !empty($company_payment_setting['paypal_secret_key'])))
                                            <div class="tab-pane fade " id="paypal-payment" role="tabpanel"
                                                aria-labelledby="paypal-payment">
                                                <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                                    id="payment-form"
                                                    action="{{ route('customer.invoice.with.paypal', $invoice->id) }}">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount">
                                                                @error('amount')
                                                                    <span class="invalid-amount" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <button class="btn btn-sm btn-primary m-r-10" name="submit"
                                                            type="submit">{{ __('Make Payment') }} </button>
                                                    </div>
                                                    <!-- <div class="form-group mt-3">
                                                                                                                                        <button class="btn btn-sm btn-primary rounded-pill" name="submit" type="submit">{{ __('Make Payment') }}</button>
                                                                                                                                    </div> -->
                                                </form>
                                            </div>
                                        @endif

                                        @if (isset($company_payment_setting['is_paystack_enabled']) &&
                                                $company_payment_setting['is_paystack_enabled'] == 'on' &&
                                                !empty($company_payment_setting['paystack_public_key']) &&
                                                !empty($company_payment_setting['paystack_secret_key']))
                                            <div class="tab-pane fade " id="paystack-payment" role="tabpanel"
                                                aria-labelledby="paypal-payment">
                                                <form class="w3-container w3-display-middle w3-card-4" method="POST"
                                                    id="paystack-payment-form"
                                                    action="{{ route('customer.invoice.pay.with.paystack') }}">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend"><span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}</span></span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">

                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <button class="btn btn-sm btn-primary m-r-10" id="pay_with_paystack"
                                                            name="submit" type="submit">{{ __('Make Payment') }} </button>
                                                    </div>
                                                    <!-- <div class="form-group mt-3">
                                                                                                                                        <input class="btn btn-sm btn-primary rounded-pill" id="pay_with_paystack" type="button" value="{{ __('Make Payment') }}">
                                                                                                                                    </div> -->

                                                </form>
                                            </div>
                                        @endif

                                        @if (isset($company_payment_setting['is_flutterwave_enabled']) &&
                                                $company_payment_setting['is_flutterwave_enabled'] == 'on' &&
                                                !empty($company_payment_setting['paystack_public_key']) &&
                                                !empty($company_payment_setting['paystack_secret_key']))
                                            <div class="tab-pane fade " id="flutterwave-payment" role="tabpanel"
                                                aria-labelledby="paypal-payment">
                                                <form role="form"
                                                    action="{{ route('customer.invoice.pay.with.flaterwave') }}"
                                                    method="post" class="require-validation" id="flaterwave-payment-form">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend"><span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}</span></span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">

                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <button class="btn btn-sm btn-primary m-r-10" id="pay_with_flaterwave"
                                                            name="submit" type="submit">{{ __('Make Payment') }} </button>
                                                    </div>
                                                    <!-- <div class="form-group mt-3">
                                                                                                                                        <input class="btn btn-sm btn-primary rounded-pill" id="pay_with_flaterwave" type="button" value="{{ __('Make Payment') }}">
                                                                                                                                    </div> -->

                                                </form>
                                            </div>
                                        @endif

                                        @if (isset($company_payment_setting['is_razorpay_enabled']) && $company_payment_setting['is_razorpay_enabled'] == 'on')
                                            <div class="tab-pane fade " id="razorpay-payment" role="tabpanel"
                                                aria-labelledby="paypal-payment">
                                                <form role="form"
                                                    action="{{ route('customer.invoice.pay.with.razorpay') }}"
                                                    method="post" class="require-validation" id="razorpay-payment-form">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend"><span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}</span></span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">

                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <button class="btn btn-sm btn-primary m-r-10" id="pay_with_razorpay"
                                                            name="submit" type="submit">{{ __('Make Payment') }} </button>
                                                    </div>
                                                    <!-- <div class="form-group mt-3">
                                                                                                                                        <input class="btn btn-sm btn-primary rounded-pill" id="pay_with_razorpay" type="button" value="{{ __('Make Payment') }}">
                                                                                                                                    </div> -->

                                                </form>
                                            </div>
                                        @endif

                                        @if (isset($company_payment_setting['is_mercado_enabled']) && $company_payment_setting['is_mercado_enabled'] == 'on')
                                            <div class="tab-pane fade " id="mercado-payment" role="tabpanel"
                                                aria-labelledby="mercado-payment">
                                                <form role="form"
                                                    action="{{ route('customer.invoice.pay.with.mercado') }}" method="post"
                                                    class="require-validation" id="mercado-payment-form">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend"><span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}</span></span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">

                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <button class="btn btn-sm btn-primary m-r-10" id="pay_with_mercado"
                                                            name="submit" type="submit">{{ __('Make Payment') }} </button>
                                                    </div>
                                                    <!-- <div class="form-group mt-3">
                                                                                                                                        <input type="submit" id="pay_with_mercado" value="{{ __('Make Payment') }}" class="btn btn-sm btn-primary rounded-pill">
                                                                                                                                    </div> -->

                                                </form>
                                            </div>
                                        @endif

                                        @if (isset($company_payment_setting['is_paytm_enabled']) && $company_payment_setting['is_paytm_enabled'] == 'on')
                                            <div class="tab-pane fade" id="paytm-payment" role="tabpanel"
                                                aria-labelledby="paytm-payment">
                                                <form role="form" action="{{ route('customer.invoice.pay.with.paytm') }}"
                                                    method="post" class="require-validation" id="paytm-payment-form">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend"><span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}</span></span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">

                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="flaterwave_coupon"
                                                                class=" text-dark">{{ __('Mobile Number') }}</label>
                                                            <input type="text" id="mobile" name="mobile"
                                                                class="form-control mobile" data-from="mobile"
                                                                placeholder="{{ __('Enter Mobile Number') }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <button class="btn btn-sm btn-primary m-r-10" id="pay_with_paytm"
                                                            name="submit" type="submit">{{ __('Make Payment') }} </button>
                                                    </div>
                                                    <!-- <div class="form-group mt-3">
                                                                                                                                        <input type="submit" id="pay_with_paytm" value="{{ __('Make Payment') }}" class="btn btn-sm btn-primary rounded-pill">
                                                                                                                                    </div> -->

                                                </form>
                                            </div>
                                        @endif

                                        @if (isset($company_payment_setting['is_mollie_enabled']) && $company_payment_setting['is_mollie_enabled'] == 'on')
                                            <div class="tab-pane fade " id="mollie-payment" role="tabpanel"
                                                aria-labelledby="mollie-payment">
                                                <form role="form"
                                                    action="{{ route('customer.invoice.pay.with.mollie') }}" method="post"
                                                    class="require-validation" id="mollie-payment-form">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend"><span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}</span></span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">

                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <button class="btn btn-sm btn-primary m-r-10" id="pay_with_mollie"
                                                            name="submit" type="submit">{{ __('Make Payment') }} </button>
                                                    </div>
                                                    <!-- <div class="form-group mt-3">
                                                                                                                                        <input type="submit" id="pay_with_mollie" value="{{ __('Make Payment') }}" class="btn btn-sm btn-primary rounded-pill">
                                                                                                                                    </div> -->

                                                </form>
                                            </div>
                                        @endif

                                        @if (isset($company_payment_setting['is_skrill_enabled']) && $company_payment_setting['is_skrill_enabled'] == 'on')
                                            <div class="tab-pane fade " id="skrill-payment" role="tabpanel"
                                                aria-labelledby="skrill-payment">
                                                <form role="form"
                                                    action="{{ route('customer.invoice.pay.with.skrill') }}" method="post"
                                                    class="require-validation" id="skrill-payment-form">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend"><span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}</span></span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">

                                                        </div>
                                                    </div>
                                                    @php
                                                        $skrill_data = [
                                                            'transaction_id' => md5(
                                                                date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id',
                                                            ),
                                                            'user_id' => 'user_id',
                                                            'amount' => 'amount',
                                                            'currency' => 'currency',
                                                        ];
                                                        session()->put('skrill_data', $skrill_data);

                                                    @endphp
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <button class="btn btn-sm btn-primary m-r-10" id="pay_with_skrill"
                                                            name="submit" type="submit">{{ __('Make Payment') }} </button>
                                                    </div>
                                                    <!-- <div class="form-group mt-3">
                                                                                                                                        <input type="submit" id="pay_with_skrill" value="{{ __('Make Payment') }}" class="btn btn-sm btn-primary rounded-pill">
                                                                                                                                    </div> -->

                                                </form>
                                            </div>
                                        @endif

                                        @if (isset($company_payment_setting['is_coingate_enabled']) && $company_payment_setting['is_coingate_enabled'] == 'on')
                                            <div class="tab-pane fade " id="coingate-payment" role="tabpanel"
                                                aria-labelledby="coingate-payment">
                                                <form role="form"
                                                    action="{{ route('customer.invoice.pay.with.coingate') }}"
                                                    method="post" class="require-validation" id="coingate-payment-form">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend"><span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}</span></span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">

                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <button class="btn btn-sm btn-primary m-r-10" id="pay_with_coingate"
                                                            name="submit" type="submit">{{ __('Make Payment') }} </button>
                                                    </div>
                                                    <!-- <div class="form-group mt-3">
                                                                                                                                        <input type="submit" id="pay_with_coingate" value="{{ __('Make Payment') }}" class="btn btn-sm btn-primary rounded-pill">
                                                                                                                                    </div> -->

                                                </form>
                                            </div>
                                        @endif
                                        {{-- @dd($company_payment_setting) --}}
                                        @if (
                                            !empty($company_payment_setting) &&
                                                $company_payment_setting['is_paymentwall_enabled'] == 'on' &&
                                                !empty($company_payment_setting['is_paymentwall_enabled']) &&
                                                !empty($company_payment_setting['paymentwall_secret_key']))
                                            <div class="tab-pane " id="paymentwall-payment">
                                                <!-- <div class="card"> -->
                                                <form class="w3-container w3-display-middle w3-card-4" method="POST"
                                                    id="paymentwall-payment-form"
                                                    action="{{ route('customer.invoice.paymentwallpayment') }}">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <!-- <div class="border p-3 mb-3 rounded">
                                                                                                                                            <div class="row">
                                                                                                                                                <div class="col-md-10">
                                                                                                                                                    <div class="form-group">
                                                                                                                                                        <label for="paypal_coupon" class="form-label">{{ __('Coupon') }}</label>
                                                                                                                                                        <input type="text" id="paymentwall_coupon" name="coupon" class="form-control coupon" data-from="paymentwall" placeholder="{{ __('Enter Coupon Code') }}">
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                                <div class="col-auto my-auto">
                                                                                                                                                    <a href="#" class="apply-btn apply-coupon" data-bs-toggle="tooltip" data-title="{{ __('Apply') }}"><i class="ti ti-save"></i></a>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                        <div class="form-group mt-3">
                                                                                                                                            <div class="col-sm-12">
                                                                                                                                                <div class="text-sm-right">
                                                                                                                                                    <input type="submit" id="pay_with_paymentwall" value="{{ __('Pay Now') }}" class="btn-create badge-blue">
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        </div> -->
                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend"><span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}</span></span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <button class="btn btn-sm btn-primary m-r-10"
                                                            id="pay_with_paymentwall" name="submit"
                                                            type="submit">{{ __('Make Payment') }} </button>
                                                    </div>
                                                    <!-- <div class="form-group mt-3">
                                                                                                                                            <input type="submit" id="pay_with_paymentwall" value="{{ __('Make Payment') }}" class="btn btn-sm btn-primary rounded-pill">
                                                                                                                                        </div> -->
                                                </form>
                                                <!-- </div> -->
                                            </div>
                                        @endif

                                        @if (
                                            !empty($company_payment_setting) &&
                                                isset($company_payment_setting['is_toyyibpay_enabled']) &&
                                                $company_payment_setting['is_toyyibpay_enabled'] == 'on' &&
                                                !empty($company_payment_setting['is_toyyibpay_enabled']))
                                            <div class="tab-pane fade " id="toyyibpay-payment" role="tabpanel"
                                                aria-labelledby="toyyibpay-payment">
                                                <form class="w3-container w3-display-middle w3-card-4" method="POST"
                                                    id="toyyibpay-payment-form"
                                                    action="{{ route('invoice.with.toyyibpay', $invoice->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend">
                                                                <span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}
                                                                </span>
                                                            </span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <input class="btn btn-sm btn-primary m-r-10" id="pay_with_paymentwall"
                                                            type="submit" value="{{ __('Make Payment') }}">
                                                    </div>

                                                </form>
                                            </div>
                                        @endif

                                        @if (
                                            !empty($company_payment_setting) &&
                                                isset($company_payment_setting['is_payfast_enabled']) &&
                                                $company_payment_setting['is_payfast_enabled'] == 'on' &&
                                                !empty($company_payment_setting['is_payfast_enabled']) &&
                                                !empty($company_payment_setting['is_payfast_enabled']))
                                            <div class="tab-pane fade " id="payfast-payment" role="tabpanel"
                                                aria-labelledby="payfast-payment">
                                                @php
                                                    $pfHost =
                                                        $company_payment_setting['payfast_mode'] == 'sandbox'
                                                            ? 'sandbox.payfast.co.za'
                                                            : 'www.payfast.co.za';
                                                @endphp
                                                <form role="form" action={{ 'https://' . $pfHost . '/eng/process' }}
                                                    method="post" id="payfast-payment-form">
                                                    @csrf


                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend">
                                                                <span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}
                                                                </span>
                                                            </span>

                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="pay_fast_amount" onchange=get_payfast_status()>
                                                        </div>
                                                    </div>
                                                    <div id="get-payfast-inputs"></div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <input type="hidden" name="invoice_id" id="invoice_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                        <input class="btn btn-sm btn-primary m-r-10" id="pay_with_payfast"
                                                            type="submit" value="{{ __('Make Payment') }}">
                                                    </div>

                                                </form>
                                            </div>
                                        @endif

                                        @if (
                                            !empty($company_payment_setting) &&
                                                ($company_payment_setting['is_iyzipay_enabled'] == 'on' &&
                                                    !empty($company_payment_setting['iyzipay_private_key']) &&
                                                    !empty($company_payment_setting['iyzipay_secret_key'])))
                                            <div class="tab-pane fade " id="iyzipay-payment" role="tabpanel"
                                                aria-labelledby="iyzipay-payment">
                                                <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                                    id="payment-form"
                                                    action="{{ route('invoice.with.iyzipay', $invoice->id) }}">

                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="amount">{{ __('Amount') }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-prepend"><span
                                                                        class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}</span></span>
                                                                <input class="form-control" required="required"
                                                                    min="0" name="amount" type="number"
                                                                    value="{{ $invoice->getDue() }}" min="0"
                                                                    step="0.01" max="{{ $invoice->getDue() }}"
                                                                    id="amount">
                                                                @error('amount')
                                                                    <span class="invalid-amount" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <button class="btn btn-sm btn-primary m-r-10" name="submit"
                                                            type="submit">{{ __('Make Payment') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif


                                        @if (
                                            !empty($company_payment_setting) &&
                                                isset($company_payment_setting['is_sspay_enabled']) &&
                                                $company_payment_setting['is_sspay_enabled'] == 'on' &&
                                                !empty($company_payment_setting['is_sspay_enabled']))
                                            <div class="tab-pane fade " id="sspay-payment" role="tabpanel"
                                                aria-labelledby="sspay-payment">
                                                <form class="w3-container w3-display-middle w3-card-4" method="POST"
                                                    id="sspay-payment-form"
                                                    action="{{ route('invoice.pay.with.sspay', $invoice->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend">
                                                                <span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}
                                                                </span>
                                                            </span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <input class="btn btn-sm btn-primary m-r-10" id="pay_with_sspay"
                                                            type="submit" value="{{ __('Make Payment') }}">
                                                    </div>

                                                </form>
                                            </div>
                                        @endif

                                        @if (
                                            !empty($company_payment_setting) &&
                                                isset($company_payment_setting['is_paytab_enabled']) &&
                                                $company_payment_setting['is_paytab_enabled'] == 'on' &&
                                                !empty($company_payment_setting['is_paytab_enabled']))
                                            <div class="tab-pane fade " id="paytab-payment" role="tabpanel"
                                                aria-labelledby="paytab-payment">
                                                <form class="w3-container w3-display-middle w3-card-4" method="POST"
                                                    id="paytab-payment-form"
                                                    action="{{ route('invoice.pay.with.paytab', $invoice->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend">
                                                                <span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}
                                                                </span>
                                                            </span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <input class="btn btn-sm btn-primary m-r-10" id="pay_with_paytab"
                                                            type="submit" value="{{ __('Make Payment') }}">
                                                    </div>

                                                </form>
                                            </div>
                                        @endif

                                        @if (
                                            !empty($company_payment_setting) &&
                                                isset($company_payment_setting['is_benefit_enabled']) &&
                                                $company_payment_setting['is_benefit_enabled'] == 'on' &&
                                                !empty($company_payment_setting['is_benefit_enabled']))
                                            <div class="tab-pane fade" id="benefit-payment" role="tabpanel"
                                                aria-labelledby="benefit-payment">
                                                <form class="w3-container w3-display-middle w3-card-4" method="POST"
                                                    id="benefit-payment-form"
                                                    action="{{ route('invoice.pay.with.benefit', $invoice->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend">
                                                                <span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}
                                                                </span>
                                                            </span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <input class="btn btn-sm btn-primary m-r-10" id="pay_with_benefit"
                                                            type="submit" value="{{ __('Make Payment') }}">
                                                    </div>

                                                </form>
                                            </div>
                                        @endif

                                        @if (
                                            !empty($company_payment_setting) &&
                                                isset($company_payment_setting['is_cashfree_enabled']) &&
                                                $company_payment_setting['is_cashfree_enabled'] == 'on' &&
                                                !empty($company_payment_setting['is_cashfree_enabled']))
                                            <div class="tab-pane fade" id="cashfree-payment" role="tabpanel"
                                                aria-labelledby="cashfree-payment">
                                                <form class="w3-container w3-display-middle w3-card-4" method="POST"
                                                    id="cashfree-payment-form"
                                                    action="{{ route('invoice.pay.with.cashfree', $invoice->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend">
                                                                <span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}
                                                                </span>
                                                            </span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <input class="btn btn-sm btn-primary m-r-10" id="pay_with_cashfree"
                                                            type="submit" value="{{ __('Make Payment') }}">
                                                    </div>

                                                </form>
                                            </div>
                                        @endif

                                        @if (
                                            !empty($company_payment_setting) &&
                                                isset($company_payment_setting['is_aamarpay_enabled']) &&
                                                $company_payment_setting['is_aamarpay_enabled'] == 'on' &&
                                                !empty($company_payment_setting['is_aamarpay_enabled']))
                                            <div class="tab-pane fade" id="aamarpay-payment" role="tabpanel"
                                                aria-labelledby="aamarpay-payment">
                                                <form class="w3-container w3-display-middle w3-card-4" method="POST"
                                                    id="aamarpay-payment-form"
                                                    action="{{ route('invoice.pay.aamarpay.payment', $invoice->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend">
                                                                <span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}
                                                                </span>
                                                            </span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <input class="btn btn-sm btn-primary m-r-10" id="pay_with_aarampay"
                                                            type="submit" value="{{ __('Make Payment') }}">
                                                    </div>

                                                </form>
                                            </div>
                                        @endif

                                        @if (
                                            !empty($company_payment_setting) &&
                                                isset($company_payment_setting['is_paytr_enabled']) &&
                                                $company_payment_setting['is_paytr_enabled'] == 'on' &&
                                                !empty($company_payment_setting['is_paytr_enabled']))
                                            <div class="tab-pane fade" id="paytr-payment" role="tabpanel"
                                                aria-labelledby="paytr-payment">
                                                <form class="w3-container w3-display-middle w3-card-4" method="POST"
                                                    id="paytr-payment-form"
                                                    action="{{ route('invoice.pay.paytr.payment', $invoice->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">

                                                    <div class="form-group col-md-12">
                                                        <label for="amount">{{ __('Amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-prepend">
                                                                <span
                                                                    class="input-group-text">{{ App\Models\Utility::getValByName('site_currency') }}
                                                                </span>
                                                            </span>
                                                            <input class="form-control" required="required" min="0"
                                                                name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <input class="btn btn-sm btn-primary m-r-10" id="pay_with_paytr"
                                                            type="submit" value="{{ __('Make Payment') }}">
                                                    </div>
                                                </form>
                                            </div>
                                        @endif

                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth

@endsection
