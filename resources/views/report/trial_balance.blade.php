@extends('layouts.admin')
@section('page-title')
    {{ __('Trial Balance') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Trial Balance') }}</li>
@endsection

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <style type="text/css">
        :root {
            --theme-color: #007C38;
            --theme-light: #007C3820;
            --theme-gradient: linear-gradient(135deg, #007C38 0%, #007C38DD 100%);
            --white: #ffffff;
            --black: #1a1a1a;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .trial-balance-container {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: transparent;
            padding: 0;
        }

        .trial-balance-container.printable {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .trial-balance-main {
            width: 100%;
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            position: relative;
            margin-top: 20px;
        }

        .trial-balance-container.printable .trial-balance-main {
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 16px;
            box-shadow: var(--shadow-xl);
        }

        .trial-balance-main::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 6px;
            height: 100%;
            background: var(--theme-gradient);
        }

        .trial-balance-header {
            background: var(--theme-gradient);
            color: var(--white);
            position: relative;
            overflow: hidden;
            padding: 30px 20px;
            text-align: center;
        }

        .trial-balance-container.printable .trial-balance-header {
            padding: 40px;
        }

        .trial-balance-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .trial-balance-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
        }

        .trial-balance-container.printable .trial-balance-title {
            font-size: 48px;
            letter-spacing: 2px;
        }

        .trial-balance-subtitle {
            font-size: 16px;
            font-weight: 500;
            margin-top: 12px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .trial-balance-container.printable .trial-balance-subtitle {
            font-size: 18px;
        }

        .company-details {
            font-size: 14px;
            line-height: 1.6;
            color: var(--white);
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }

        .company-details strong {
            font-weight: 600;
            font-size: 16px;
            display: block;
            margin-bottom: 4px;
        }

        .trial-balance-body {
            padding: 20px;
        }

        .trial-balance-container.printable .trial-balance-body {
            padding: 40px;
        }

        .accounts-table {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            margin-top: 24px;
        }

        .accounts-table-header {
            background: var(--theme-gradient);
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--white);
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .account-section {
            border-bottom: 1px solid var(--gray-100);
        }

        .account-section:last-child {
            border-bottom: none;
        }

        .account-type-header {
            background: var(--gray-50);
            padding: 16px 20px;
            font-weight: 600;
            font-size: 14px;
            color: var(--theme-color);
            border-left: 4px solid var(--theme-color);
        }

        .account-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            transition: background-color 0.2s ease;
            border-bottom: 1px solid var(--gray-50);
        }

        .account-row:hover {
            background-color: var(--gray-50);
        }

        .account-row:last-child {
            border-bottom: none;
        }

        .account-name {
            flex: 1;
            font-size: 14px;
            color: var(--white);
        }

        .account-name a {
            color: var(--theme-color);
            text-decoration: none;
            font-weight: 500;
        }

        .account-name a:hover {
            text-decoration: underline;
        }

        .account-code {
            width: 120px;
            text-align: center;
            font-size: 12px;
            color: var(--white);
        }

        .account-debit, .account-credit {
            width: 120px;
            text-align: right;
            font-size: 14px;
            font-weight: 500;
            color: var(--white);
        }

        .parent-account {
            font-weight: 600;
            font-size: 15px;
        }

        .parent-account .account-name a {
            font-weight: 600;
        }

        .sub-account {
            padding-left: 40px;
        }

        .parent-total {
            background: var(--gray-50);
            font-weight: 600;
            border-top: 1px solid var(--gray-200);
        }

        .parent-total .account-name {
            color: var(--gray-800);
        }

        .total-section {
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
            border-radius: 12px;
            padding: 24px;
            margin-top: 24px;
            border: 1px solid var(--gray-200);
        }

        .total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            font-size: 18px;
            font-weight: 700;
            color: var(--theme-color);
            border-top: 2px solid var(--theme-color);
            background: var(--theme-light);
            border-radius: 8px;
        }

        .total-label {
            flex: 1;
            font-weight: 700;
        }

        .total-debit, .total-credit {
            width: 120px;
            text-align: right;
            font-weight: 700;
        }

        .account-arrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .account-icon {
            font-size: 16px;
            color: var(--theme-color);
        }

        .collapse-view .sub-account {
            display: none;
        }

        /* Print Styles */
        @media print {
            .trial-balance-container {
                background: white !important;
                padding: 20px 0 !important;
            }
            
            .trial-balance-container .trial-balance-main {
                box-shadow: none !important;
                border-radius: 0 !important;
                max-width: none !important;
                margin: 0 !important;
            }
            
            .trial-balance-main::before {
                display: none !important;
            }
            
            .trial-balance-header {
                padding: 40px !important;
            }
            
            .trial-balance-title {
                font-size: 42px !important;
            }
            
            .trial-balance-body {
                padding: 30px !important;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .trial-balance-main {
                margin: 0 16px;
                max-width: calc(100vw - 32px);
            }
            
            .trial-balance-header {
                padding: 24px;
            }
            
            .trial-balance-title {
                font-size: 32px;
            }
            
            .trial-balance-subtitle {
                font-size: 16px;
            }
            
            .trial-balance-body {
                padding: 24px;
            }
            
            .accounts-table-header {
                padding: 16px;
                font-size: 12px;
            }
            
            .account-row {
                padding: 12px 16px;
            }
            
            .account-name {
                font-size: 13px;
            }
            
            .account-code {
                font-size: 11px;
                width: 80px;
            }
            
            .account-debit, .account-credit {
                font-size: 13px;
                width: 100px;
            }
            
            .total-row {
                padding: 12px 16px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .trial-balance-title {
                font-size: 24px;
                letter-spacing: 1px;
            }
            
            .account-code {
                width: 60px;
            }
            
            .account-debit, .account-credit {
                width: 80px;
                font-size: 12px;
            }
        }
    </style>


@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var filename = $('#filename').val();

        function saveAsPDF() {
            var printableArea = document.getElementById('printableArea');
            printableArea.classList.add('printable');
            
            var printContents = printableArea.outerHTML;
            var originalContents = document.body.innerHTML;
            
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#filter").click(function() {
                $("#show_filter").toggle();
            });
        });
    </script>
@endpush

@section('action-btn')
    <div class="flex items-center align-items-start gap-2 mt-2 sm:mt-0">
        <div class="float-end">
            <a href="#" onclick="saveAsPDF()" class="btn bg-[#007C38] text-white px-4 py-1.5 rounded-md text-sm hover:bg-green-700" data-bs-toggle="tooltip"
                title="{{ __('Print') }}" data-original-title="{{ __('Print') }}"><i class="ti ti-printer"></i>
                {{ __('Print') }}
            </a>
        </div>

        <div class="float-end me-2">
            {{ Form::open(['route' => ['trial.balance.export']]) }}
            <input type="hidden" name="start_date" class="start_date">
            <input type="hidden" name="end_date" class="end_date">
            <button type="submit" style="border: 1px solid #007C38 !important" class="flex items-center gap-1 border border-[#007C38] text-[#007C38] px-3 py-1.5 rounded-md text-sm hover:bg-green-50" data-bs-toggle="tooltip" title="{{ __('Export') }}"
                data-original-title="{{ __('Export') }}">
                <img src="{{ asset('web-assets/dashboard/icons/export.svg') }}" alt="Import">
                {{ __('Export') }}
            </button>
            {{ Form::close() }}
        </div>
        <div class="float-end me-2" id="filter">
            <button id="filter" style="border: 1px solid #007C38 !important" class="flex items-center gap-1 border border-[#007C38] text-[#007C38] px-3 py-1.5 rounded-md text-sm hover:bg-green-50" data-bs-toggle="tooltip" title="{{ __('Filter') }}"
                data-original-title="{{ __('Filter') }}"><i class="ti ti-filter"></i>
                {{ __('Filter') }}
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="">
            <div class="mt-2" id="multiCollapseExample1">
                <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3" id="show_filter" style="display:none;">
                        <div class="h-1 w-full" style="background:#007C38;"></div>
    
                    <div class="card-body">
                        {{ Form::open(['route' => ['trial.balance'], 'method' => 'GET', 'id' => 'report_trial_balance']) }}
                        <div class="col-xl-12">

                            <div class="row justify-content-between">
                                {{-- <div class="col-xl-3 mt-4">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons"
                                        aria-label="Basic radio toggle button group">
                                        <label class="btn btn-primary month-label">
                                            <a href="{{ route('trial.balance', ['collapse']) }}" class="text-white"
                                                id="collapse"> {{ __('Collapse') }} </a>
                                        </label>

                                        <label class="btn btn-primary year-label active">
                                            <a href="{{ route('trial.balance', ['expand']) }}" class="text-white">
                                                {{ __('Expand') }} </a>
                                        </label>
                                    </div>
                                </div> --}}
                                <div class="col-xl-9">
                                    <div class="row justify-content-end align-items-center">
                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                                {{ Form::date('start_date', $filter['startDateRange'], ['class' => 'startDate form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                                {{ Form::date('end_date', $filter['endDateRange'], ['class' => 'endDate form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-auto mt-4">
                                            <a href="#" class="btn btn-sm btn-primary"
                                                onclick="document.getElementById('report_trial_balance').submit(); return false;"
                                                data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                                data-original-title="{{ __('apply') }}">
                                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                            </a>

                                            <a href="{{ route('trial.balance') }}" class="btn btn-sm btn-danger "
                                                data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                                data-original-title="{{ __('Reset') }}">
                                                <span class="btn-inner--icon"><i
                                                        class="ti ti-refresh text-white-off"></i></span>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    @php
        $authUser = \Auth::user()->creatorId();
        $user = App\Models\User::find($authUser);
    @endphp

    <div class="trial-balance-container" id="printableArea">
        <div class="trial-balance-main">
            <div class="trial-balance-header">
                <h1 class="trial-balance-title">TRIAL BALANCE</h1>
                <div class="trial-balance-subtitle">
                    {{ 'Trial Balance of ' . $user->name . ' as of ' . $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}
                </div>
                <div class="company-details">
                    @if($user->name)
                        <strong>{{ $user->name }}</strong>
                    @endif
                    @if($user->email)
                        {{ $user->email }}<br>
                    @endif
                </div>
            </div>
            <div class="trial-balance-body {{ $view == 'collapse' ? 'collapse-view' : '' }}">
                <div class="accounts-table">
                    <div class="accounts-table-header">
                        <div class="account-name">{{ __('Account') }}</div>
                        <div class="account-code">{{ __('Account Code') }}</div>
                        <div class="account-debit">{{ __('Debit') }}</div>
                        <div class="account-credit">{{ __('Credit') }}</div>
                    </div>
                    @php
                        $totalCredit = 0;
                        $totalDebit = 0;
                    @endphp
                    @foreach ($totalAccounts as $type => $accounts)
                        <div class="account-section">
                            <div class="account-type-header">{{ $type }}</div>
                            @if ($view == 'collapse')
                                @foreach ($accounts as $key => $record)
                                    @if ($record['account'] == 'parentTotal')
                                        <div class="account-row parent-account">
                                            <div class="account-name">
                                                <div class="account-arrow">
                                                    <a href="{{ route('trial.balance', ['expand']) }}">
                                                        <i class="ti ti-chevron-down account-icon"></i>
                                                    </a>
                                                    <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}">
                                                        {{ str_replace('Total ', '', $record['account_name']) }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="account-code">{{ $record['account_code'] }}</div>
                                            <div class="account-debit">{{ \Auth::user()->priceFormat($record['totalDebit']) }}</div>
                                            <div class="account-credit">{{ \Auth::user()->priceFormat($record['totalCredit']) }}</div>
                                        </div>
                                    @elseif($record['account'] == 'parentTotal' || $record['account'] == '')
                                        <div class="account-row sub-account">
                                            <div class="account-name">
                                                <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}">
                                                    {{ $record['account_name'] }}
                                                </a>
                                            </div>
                                            <div class="account-code">{{ $record['account_code'] }}</div>
                                            <div class="account-debit">{{ \Auth::user()->priceFormat($record['totalDebit']) }}</div>
                                            <div class="account-credit">{{ \Auth::user()->priceFormat($record['totalCredit']) }}</div>
                                        </div>
                                    @endif
                                    @php
                                        if ($record['account'] != 'parent' && $record['account'] != 'subAccount') {
                                            $totalDebit += $record['totalDebit'];
                                            $totalCredit += $record['totalCredit'];
                                        }
                                    @endphp
                                @endforeach
                            @else
                                @foreach ($accounts as $key => $record)
                                    @if ($record['account'] == 'parent')
                                        <div class="account-row parent-account">
                                            <div class="account-name">
                                                <div class="account-arrow">
                                                    <a href="{{ route('trial.balance', ['collapse']) }}">
                                                        <i class="ti ti-chevron-down account-icon"></i>
                                                    </a>
                                                    <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}">
                                                        {{ str_replace('Total ', '', $record['account_name']) }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="account-code">{{ $record['account_code'] }}</div>
                                            <div class="account-debit">{{ \Auth::user()->priceFormat($record['totalDebit']) }}</div>
                                            <div class="account-credit">{{ \Auth::user()->priceFormat($record['totalCredit']) }}</div>
                                        </div>
                                    @elseif($record['account'] == 'parentTotal')
                                        <div class="account-row parent-total">
                                            <div class="account-name">
                                                <a href="#">{{ $record['account_name'] }}</a>
                                            </div>
                                            <div class="account-code">{{ $record['account_code'] }}</div>
                                            <div class="account-debit">{{ \Auth::user()->priceFormat($record['totalDebit']) }}</div>
                                            <div class="account-credit">{{ \Auth::user()->priceFormat($record['totalCredit']) }}</div>
                                        </div>
                                    @else
                                        <div class="account-row sub-account">
                                            <div class="account-name">
                                                <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}">
                                                    {{ $record['account_name'] }}
                                                </a>
                                            </div>
                                            <div class="account-code">{{ $record['account_code'] }}</div>
                                            <div class="account-debit">{{ \Auth::user()->priceFormat($record['totalDebit']) }}</div>
                                            <div class="account-credit">{{ \Auth::user()->priceFormat($record['totalCredit']) }}</div>
                                        </div>
                                    @endif
                                    @php
                                        if ($record['account'] != 'parent' && $record['account'] != 'subAccount') {
                                            $totalDebit += $record['totalDebit'];
                                            $totalCredit += $record['totalCredit'];
                                        }
                                    @endphp
                                @endforeach
                            @endif
                        </div>
                    @endforeach

                </div>
                
                @if ($totalAccounts != [])
                    <div class="total-section">
                        <div class="total-row">
                            <div class="total-label">{{ __('Total') }}</div>
                            <div class="account-code"></div>
                            <div class="total-debit">{{ \Auth::user()->priceFormat($totalDebit) }}</div>
                            <div class="total-credit">{{ \Auth::user()->priceFormat($totalCredit) }}</div>
                        </div>
                    </div>
                @endif
                
            </div>
            
            <div style="text-align: center; padding: 32px 40px; margin-top: 40px; border-top: 1px solid var(--gray-200); font-size: 13px; line-height: 1.6; color: var(--gray-600); background: var(--gray-50);">
                <em>Generated on {{ date('Y-m-d H:i:s') }}</em>
            </div>
        </div>
    </div>
@endsection


@push('script-page')
    <script>
        $(document).ready(function() {
            callback();

            function callback() {
                var start_date = $(".startDate").val();
                var end_date = $(".endDate").val();

                $('.start_date').val(start_date);
                $('.end_date').val(end_date);

            }
        });
    </script>
@endpush