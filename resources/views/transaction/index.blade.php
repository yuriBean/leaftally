@extends('layouts.admin')
@section('page-title')
    {{ __('Transaction Summary') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Report') }}</li>
    <li class="breadcrumb-item">{{ __('Transaction Summary') }}</li>
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/datatable/buttons.dataTables.min.css') }}">
    <style>
        .card-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid
        }

        .gray-text {
            color: #6c757d;
        }

        .report-text {
            font-size: 0.875rem;
        }

        .icon-container {
            padding: 8px 12px;
        }

        .icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #f8f9fa;
            border-radius: 50%;
            margin-right: 1rem;
        }

        .icon-container i {
            font-size: 1.25rem;
            color: #007C38;
        }
    </style>
@endpush

@push('script-page')
    {{--    <script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script> --}}
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script src="{{ asset('js/datatable/jszip.min.js') }}"></script>
    <script src="{{ asset('js/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/datatable/vfs_fonts.js') }}"></script>
    {{--    <script src="{{ asset('js/datatable/dataTables.buttons.min.js') }}"></script> --}}
    {{--    <script src="{{ asset('js/datatable/buttons.html5.min.js') }}"></script> --}}
    {{--    <script type="text/javascript" src="{{ asset('js/datatable/buttons.print.min.js') }}"></script> --}}

    <script>
        var filename = $('#filename').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
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
            html2pdf().set(opt).from(element).save();

        }
    </script>
@endpush

@section('action-btn')
    <div class="flex items-center gap-2 mt-2 sm:mt-0">
        <a href="{{ route('transaction.export') }}" data-bs-toggle="tooltip" title="{{ __('Export') }}"
            style="border: 1px solid #007C38 !important"
            class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
            {{ __('Export') }}
        </a>
        <a href="#" class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit" onclick="saveAsPDF()"data-bs-toggle="tooltip"
            title="{{ __('Download') }}" data-original-title="{{ __('Download') }}">
            <span class="btn-inner--icon"><i class="ti ti-download"></i>
                {{ __('Download') }}
            </span>
        </a>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class=" multi-collapse mt-2 " id="multiCollapseExample1">
                <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                    <div class="h-1 w-full" style="background:#007C38;"></div>
                    <div class="card-body">
                        {{ Form::open(['route' => ['transaction.index'], 'method' => 'get', 'id' => 'transaction_report']) }}
                        <div class="form-space-fix row d-flex align-items-center">
                            <div class="col-md-10 col-12">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('start_month', __('Start Month'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                                            {{ Form::month('start_month', null, ['class' => 'form-control month-btn appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] duration-200 w-full', 'placeholder' => 'Start Month']) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('end_month', __('End Month'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                                            {{ Form::month('end_month', null, ['class' => 'form-control month-btn appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] duration-200 w-full', 'placeholder' => 'Start Month']) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('account', __('Account'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                                            {{ Form::select('account', $account, isset($_GET['account']) ? $_GET['account'] : '', ['class' => 'form-control select appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full']) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('category', __('Category'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                                            {{ Form::select('category', $category, isset($_GET['category']) ? $_GET['category'] : '', ['class' => 'form-control select appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-2 col-12">
                            <div class="col-auto d-flex justify-content-end mt-4">
                                        <a href="#" class="btn btn-sm btn-primary me-2"
                                            onclick="document.getElementById('transaction_report').submit(); return false;"
                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                            data-original-title="{{ __('apply') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>

                                        <a href="{{ route('transaction.index') }}" class="btn btn-sm btn-danger "
                                            data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                            data-original-title="{{ __('Reset') }}">
                                            <span class="btn-inner--icon"><i
                                                    class="ti ti-refresh text-white-off "></i></span>
                                        </a>
                            </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div id="printableArea">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Report Summary Card -->
            <div class="bg-white rounded-xl card-shadow flex flex-col justify-between">
                <div class="flex flex-col mb-4">
                    <!-- Example icon - you can replace with relevant icons -->
                    <div class="icon-container bg-green-100 border-b-4 border-solid border-green-700">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M13 6.25034C13 6.80262 12.5523 7.25034 12 7.25034C11.4477 7.25034 11 6.80262 11 6.25034C11 5.69805 11.4477 5.25034 12 5.25034C12.5523 5.25034 13 5.69805 13 6.25034ZM13.0316 2.32465C12.417 1.87616 11.583 1.87616 10.9684 2.32465L3.54657 7.7406C2.56949 8.4536 3.07382 10.0003 4.2834 10.0003H4.5V15.8002C3.60958 16.2554 3 17.1817 3 18.2503V19.7503C3 20.1645 3.33579 20.5003 3.75 20.5003H20.25C20.6642 20.5003 21 20.1645 21 19.7503V18.2503C21 17.1817 20.3904 16.2554 19.5 15.8002V10.0003H19.7166C20.9262 10.0003 21.4305 8.45361 20.4534 7.7406L13.0316 2.32465ZM11.8526 3.53633C11.9404 3.47226 12.0596 3.47226 12.1474 3.53633L18.9499 8.50034H5.05011L11.8526 3.53633ZM18 10.0003V15.5003H16V10.0003H18ZM14.5 10.0003V15.5003H12.75V10.0003H14.5ZM11.25 10.0003V15.5003H9.5V10.0003H11.25ZM5.75 17.0003H18.25C18.9404 17.0003 19.5 17.56 19.5 18.2503V19.0003H4.5V18.2503C4.5 17.56 5.05964 17.0003 5.75 17.0003ZM6 15.5003V10.0003H8V15.5003H6Z"
                                fill="#323232" />
                        </svg>
                        <!-- Report icon -->
                    </div>
                    <div class="p-4">
                        <h7 class="text-[10px] text-[#727272] font-[600]">{{ __('Report') }} :</h7>
                        <h6 class="text-[12px] font-[600] text-[#323232] leading-[24px]">{{ __('Transaction Summary') }}
                        </h6>
                    </div>
                </div>
                <!-- This section would typically show a value, but based on your original snippet, it's just descriptive text.
                             If you have a value for 'Transaction Summary', you can add it here. -->
            </div>

            <!-- Account Card -->
            @if (isset($filter['account']) && $filter['account'] != __('All'))
                <div class="bg-white rounded-xl card-shadow flex flex-col justify-between">
                    <div class="flex flex-col mb-4">
                        <div class="icon-container bg-green-100 border-b-4 border-solid border-green-700">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15.75 14.5C15.3358 14.5 15 14.8358 15 15.25C15 15.6642 15.3358 16 15.75 16H18.25C18.6642 16 19 15.6642 19 15.25C19 14.8358 18.6642 14.5 18.25 14.5H15.75ZM2 8.25C2 6.45507 3.45507 5 5.25 5H18.75C20.5449 5 22 6.45507 22 8.25V15.75C22 17.5449 20.5449 19 18.75 19H5.25C3.45507 19 2 17.5449 2 15.75V8.25ZM20.5 9.5V8.25C20.5 7.2835 19.7165 6.5 18.75 6.5H5.25C4.2835 6.5 3.5 7.2835 3.5 8.25V9.5H20.5ZM3.5 11V15.75C3.5 16.7165 4.2835 17.5 5.25 17.5H18.75C19.7165 17.5 20.5 16.7165 20.5 15.75V11H3.5Z"
                                    fill="#212121" />
                            </svg>

                        </div>
                        <div class="p-4">
                            <h7 class="text-[10px] text-[#727272] font-[600]">{{ __('Account') }} :</h7>
                            <h6 class="text-[12px] font-[600] text-[#323232] leading-[24px]">{{ $filter['account'] }}</h6>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Category Card -->
            @if (isset($filter['category']) && $filter['category'] != __('All'))
                <div class="bg-white rounded-xl card-shadow flex flex-col justify-between">
                    <div class="flex flex-col mb-4">
                        <div class="icon-container bg-green-100 border-b-4 border-solid border-green-700">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15.75 14.5C15.3358 14.5 15 14.8358 15 15.25C15 15.6642 15.3358 16 15.75 16H18.25C18.6642 16 19 15.6642 19 15.25C19 14.8358 18.6642 14.5 18.25 14.5H15.75ZM2 8.25C2 6.45507 3.45507 5 5.25 5H18.75C20.5449 5 22 6.45507 22 8.25V15.75C22 17.5449 20.5449 19 18.75 19H5.25C3.45507 19 2 17.5449 2 15.75V8.25ZM20.5 9.5V8.25C20.5 7.2835 19.7165 6.5 18.75 6.5H5.25C4.2835 6.5 3.5 7.2835 3.5 8.25V9.5H20.5ZM3.5 11V15.75C3.5 16.7165 4.2835 17.5 5.25 17.5H18.75C19.7165 17.5 20.5 16.7165 20.5 15.75V11H3.5Z"
                                    fill="#212121" />
                            </svg>

                        </div>
                        <div class="p-4">
                            <h7 class="text-[10px] text-[#727272] font-[600]">{{ __('Category') }} :</h7>
                            <h6 class="text-[12px] font-[600] text-[#323232] leading-[24px]">{{ $filter['category'] }}</h6>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Duration Card -->
            <div class="bg-white rounded-xl card-shadow flex flex-col justify-between">
                <div class="flex flex-col mb-4">
                    <div class="icon-container bg-green-100 border-b-4 border-solid border-green-700">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.75 14.5C15.3358 14.5 15 14.8358 15 15.25C15 15.6642 15.3358 16 15.75 16H18.25C18.6642 16 19 15.6642 19 15.25C19 14.8358 18.6642 14.5 18.25 14.5H15.75ZM2 8.25C2 6.45507 3.45507 5 5.25 5H18.75C20.5449 5 22 6.45507 22 8.25V15.75C22 17.5449 20.5449 19 18.75 19H5.25C3.45507 19 2 17.5449 2 15.75V8.25ZM20.5 9.5V8.25C20.5 7.2835 19.7165 6.5 18.75 6.5H5.25C4.2835 6.5 3.5 7.2835 3.5 8.25V9.5H20.5ZM3.5 11V15.75C3.5 16.7165 4.2835 17.5 5.25 17.5H18.75C19.7165 17.5 20.5 16.7165 20.5 15.75V11H3.5Z"
                                fill="#212121" />
                        </svg>

                    </div>
                    <div class="p-4">
                        <h7 class="text-[10px] text-[#727272] font-[600]">{{ __('Duration') }} :</h7>
                        <h6 class="text-[12px] font-[600] text-[#323232] leading-[24px]">
                            {{ $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach ($accounts as $account)
                <div class="col-xl-3 col-md-6 col-lg-3">
                    <div class="card p-4 mb-4">
                        @if ($account->holder_name == 'Cash')
                            <h6 class="report-text gray-text mb-0">{{ $account->holder_name }}</h6>
                        @elseif(empty($account->holder_name))
                            <h6 class="report-text gray-text mb-0">{{ __('Stripe / Paypal') }}</h6>
                        @else
                            <h6 class="report-text gray-text mb-0">
                                {{ $account->holder_name . ' - ' . $account->bank_name }}
                            </h6>
                        @endif
                        <h7 class="report-text mb-0">{{ \Auth::user()->priceFormat($account->total) }}</h7>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                <div class="h-1 w-full" style="background:#007C38;"></div>

            <div class="card-body table-border-style">
                <div class="table-responsive table-new-design bg-white p-4">
                    <table class="table datatable border border-[#E5E5E5] rounded-[8px]">
                        <thead>
                            <tr>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Date') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Account') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Type') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Category') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Description') }}</th>
                                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">
                                    {{ __('Amount') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                        {{ \Auth::user()->dateFormat($transaction->date) }}</td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                        @if (!empty($transaction->bankAccount()) && $transaction->bankAccount()->holder_name == 'Cash')
                                            {{ $transaction->bankAccount()->holder_name }}
                                        @else
                                            {{ !empty($transaction->bankAccount()) ? $transaction->bankAccount()->bank_name . ' ' . $transaction->bankAccount()->holder_name : '-' }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $transaction->type }}
                                    </td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                        {{ $transaction->category }}</td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                        {{ !empty($transaction->description) ? $transaction->description : '-' }}</td>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                        {{ \Auth::user()->priceFormat($transaction->amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        </div>
    </div>
@endsection
