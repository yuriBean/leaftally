@extends('layouts.admin')
@section('page-title')
    {{ __('Balance Sheet') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Balance Sheet') }}</li>
@endsection

@push('script-page')
<style type="text/css">
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;600;700&display=swap');

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

    .balance-sheet-container {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 20px 0;
    }

    .balance-sheet-main {
        max-width: 900px;
        margin: 0 auto;
        background: var(--white);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-xl);
        position: relative;
    }

    .balance-sheet-main::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 6px;
        height: 100%;
        background: var(--theme-gradient);
    }

    .balance-sheet-header {
        background: var(--theme-gradient);
        color: var(--white);
        position: relative;
        overflow: hidden;
        padding: 40px;
        text-align: center;
    }

    .balance-sheet-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .balance-sheet-header::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -5%;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }

    .balance-sheet-title {
        font-family: 'Playfair Display', serif;
        font-size: 48px;
        font-weight: 700;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: relative;
        z-index: 1;
    }

    .balance-sheet-subtitle {
        font-size: 18px;
        font-weight: 500;
        margin-top: 16px;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    .balance-sheet-body {
        padding: 40px;
    }

    .company-info-section {
        background: var(--gray-50);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 32px;
        border-left: 4px solid var(--theme-color);
        text-align: center;
    }

    .company-info-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--theme-color);
        margin-bottom: 8px;
    }

    .company-info-content {
        font-size: 16px;
        line-height: 1.6;
        color: var(--gray-800);
        font-weight: 600;
    }

    .date-range-info {
        font-size: 14px;
        color: var(--gray-600);
        margin-top: 8px;
    }

    .account-section {
        background: var(--white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
        margin-bottom: 24px;
    }

    /* ------- FIX: align header & rows on same 3-column grid ------- */
    .account-header,
    .account-row {
        display: grid;
        grid-template-columns: 1fr 140px 160px; /* name | code | amount */
        gap: 12px;
        align-items: center;
    }

    .account-header {
        background: var(--theme-gradient);
        color: var(--white);
        padding: 16px 24px;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        /* removed flex spacing so columns align under headers */
    }

    .account-type-title {
        background: var(--gray-100);
        padding: 16px 24px;
        font-weight: 600;
        font-size: 16px;
        color: var(--gray-800);
        border-bottom: 1px solid var(--gray-200);
    }

    .account-row {
        padding: 12px 24px;
        border-bottom: 1px solid var(--gray-100);
        transition: background-color 0.2s ease;
    }

    .account-row:hover { background-color: var(--gray-50); }
    .account-row:last-child { border-bottom: none; }

    .account-name {
        font-size: 14px;
        color: var(--gray-800);
        /* removed flex:1 — grid handles sizing */
    }

    .account-name a {
        color: var(--theme-color);
        text-decoration: none;
        font-weight: 500;
    }
    .account-name a:hover { text-decoration: underline; }

    .account-code {
        text-align: center;
        font-size: 13px;
        color: var(--gray-600);
        font-family: 'Courier New', monospace;
        /* removed fixed width — grid column provides width */
    }

    .account-amount {
        text-align: right;
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-800);
        /* removed fixed width — grid column provides width */
    }

    /* Indent child rows without breaking grid */
    .sub-account .account-name { padding-left: 24px; }
    .parent-account { font-weight: 600; }

    .total-row {
        background: var(--gray-50);
        border-top: 2px solid var(--theme-color);
        font-weight: 700;
        color: var(--theme-color);
    }

    .grand-total-row {
        background: var(--theme-light);
        border-top: 3px solid var(--theme-color);
        font-weight: 700;
        font-size: 16px;
        color: var(--theme-color);
    }

    .account-arrow { display: inline-flex; align-items: center; gap: 8px; }
    .account-icon { color: var(--theme-color); font-size: 16px; }

    .balance-sheet-footer {
        background: var(--gray-50);
        padding: 24px 40px;
        margin-top: 32px;
        border-top: 1px solid var(--gray-200);
        text-align: center;
        font-size: 13px;
        line-height: 1.6;
        color: var(--gray-600);
    }

    .modern-border {
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    /* Print styles (existing) */
    @media print {
        .balance-sheet-container { background: white; padding: 0; }
        .balance-sheet-main { box-shadow: none; max-width: none; }
        .balance-sheet-main::before { display: none; }
    }

    /* ---------------- Added: Print like Trial Balance ---------------- */
    @media print {
        :root { color-scheme: light; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        body * { visibility: hidden !important; }
        #printableArea, #printableArea * { visibility: visible !important; }
        #printableArea { position: absolute; inset: 0; width: 100%; }

        .balance-sheet-container { background: #fff !important; padding: 20px 0 !important; }
        .balance-sheet-main { box-shadow: none !important; border-radius: 0 !important; max-width: none !important; margin: 0 !important; }
        .balance-sheet-main::before { display: block !important; } /* keep right color stripe */
        .balance-sheet-header { padding: 40px !important; }
        .balance-sheet-title { font-size: 42px !important; letter-spacing: 2px; }
        .balance-sheet-body { padding: 30px !important; }
    }

    @page {
        size: A4 portrait;
        margin: 12mm;
    }

    /* Optional: stack columns on small screens */
    @media (max-width: 640px) {
        .account-header, .account-row {
            grid-template-columns: 1fr;
            text-align: left;
        }
        .account-code, .account-amount { text-align: left; }
    }
</style>

@endpush
@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var filename = $('#filename').val();

        function saveAsPDF() {
            var area = document.getElementById('printableArea');
            area.classList.add('printable');
            var after = function(){ area.classList.remove('printable'); };
            window.addEventListener('afterprint', after, { once: true });
            window.print();
        }
        html2pdf().set().from().save();
    </script>

    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/custom/js/html2pdf.bundle.min.js') }}"></script>
    <script>
        function closeScript() {
            setTimeout(function() {
                window.open(window.location, '_self').close();
            }, 1000);
        }
        $(document).ready(function() {
            $("#pdf").on('click', function(e) {
                var element = document.getElementById('printableArea');
                var opt = {
                    margin: [0.20, 0.20, 0.20, 0.20],
                    filename: 'abc',
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
        });
    </script> --}}

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#filter").click(function() {
                $("#show_filter").toggle();
            });
        });
    </script>
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

@section('action-btn')
    <div class="flex items-center gap-2 mt-2 sm:mt-0">
        <div class="float-end">
            <a href="#" onclick="saveAsPDF()"
                class="btn bg-[#007C38] text-white px-4 py-1.5 rounded-md text-sm hover:bg-green-700" data-bs-toggle="tooltip"
                title="{{ __('Print') }}" data-original-title="{{ __('Print') }}" id="pdf"><i
                    class="ti ti-printer"></i>
                {{ __('Print') }}
            </a>
        </div>
        <div class="float-end ">
            {{ Form::open(['route' => ['balance.sheet.export']]) }}
            <input type="hidden" name="start_date" class="start_date">
            <input type="hidden" name="end_date" class="end_date">
            <button type="submit" style="border: 1px solid #007C38 !important"
                class="flex items-center gap-1 border border-[#007C38] text-[#007C38] px-3 py-1.5 rounded-md text-sm hover:bg-green-50"
                data-bs-toggle="tooltip" title="{{ __('Export') }}" data-original-title="{{ __('Export') }}">
                <img src="{{ asset('web-assets/dashboard/icons/export.svg') }}" alt="Import">
                {{ __('Export') }}
            </button>
            {{ Form::close() }}
        </div>

        <div class="float-end" id="filter">
            <button id="filter" style="border: 1px solid #007C38 !important"
                class="flex items-center gap-1 border border-[#007C38] text-[#007C38] px-3 py-1.5 rounded-md text-sm hover:bg-green-50"
                data-bs-toggle="tooltip" title="{{ __('Filter') }}" data-original-title="{{ __('Filter') }}"><i
                    class="ti ti-filter"></i>
                {{ __('Filter') }}
            </button>
        </div>

        <div class="float-end">
            <a href="{{ route('report.balance.sheet', 'horizontal') }}" style="border: 1px solid #007C38 !important"
                class="flex items-center gap-1 border border-[#007C38] text-[#007C38] px-3 py-1.5 rounded-md text-sm hover:bg-green-50"
                data-bs-toggle="tooltip" title="{{ __('Horizontal View') }}"
                data-original-title="{{ __('Horizontal View') }}"><i class="ti ti-separator-vertical"></i>
                {{ __('Horizontal View') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="mt-2" id="multiCollapseExample1">
                    <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3" id="show_filter" style="display:none;">
                        <div class="h-1 w-full" style="background:#007C38;"></div>
                        <div class="card-body">
                            {{ Form::open(['route' => ['report.balance.sheet'], 'method' => 'GET', 'id' => 'report_balancesheet']) }}
                            <div class="col-xl-12">

                                <div class="row justify-content-between">
                                    {{-- <div class="col-xl-3 mt-4">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons"
                                            aria-label="Basic radio toggle button group">
                                            <label class="btn btn-primary month-label">
                                                <a href="{{ route('report.balance.sheet', ['vertical', 'collapse']) }}"
                                                    class="text-white" id="collapse"> {{ __('Collapse') }} </a>
                                            </label>

                                            <label class="btn btn-primary year-label active">
                                                <a href="{{ route('report.balance.sheet', ['vertical', 'expand']) }}"
                                                    class="text-white"> {{ __('Expand') }} </a>
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
                                                    onclick="document.getElementById('report_balancesheet').submit(); return false;"
                                                    data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                                    data-original-title="{{ __('apply') }}">
                                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                                </a>

                                                <a href="{{ route('report.balance.sheet') }}"
                                                    class="btn btn-sm btn-danger " data-bs-toggle="tooltip"
                                                    title="{{ __('Reset') }}" data-original-title="{{ __('Reset') }}">
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

        <div class="balance-sheet-container" id="printableArea">
            <div class="balance-sheet-main">
                <div class="balance-sheet-header">
                    <h1 class="balance-sheet-title">Balance Sheet</h1>
                    <div class="balance-sheet-subtitle">
                        Financial Position Report
                    </div>
                </div>
                
                <div class="balance-sheet-body {{ $collapseview == 'expand' ? 'collapse-view' : '' }}">
                    <div class="company-info-section">
                        <div class="company-info-label">Balance Sheet</div>
                        <div class="company-info-content">
                            {{ $user->name }}
                        </div>
                        <div class="date-range-info">
                            {{ 'Period: ' . $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}
                        </div>
                    </div>
                    
                    <div class="account-section">
                        <div class="account-header">
                            <span>{{ __('Account') }}</span>
                            <span>{{ __('Account Code') }}</span>
                            <span class="text-end">{{ __('Total') }}</span>
                        </div>
                            @php
                                $totalAmount = 0;
                            @endphp

                        @foreach ($totalAccounts as $type => $accounts)
                            @if ($accounts != [])
                                <div class="account-main-inner">
                                    @if ($type == 'Liabilities')
                                        <div class="account-type-title">{{ __('Liabilities & Equity') }}</div>
                                    @endif
                                    <div class="account-type-title">{{ $type }}</div>

                                        @php
                                            $total = 0;
                                        @endphp
                                    @foreach ($accounts as $account)
                                        <div class="account-sub-section">
                                            @if ($account['subType'])
                                                <div class="account-row parent-account">
                                                    <div class="account-name">{{ $account['subType'] }}</div>
                                                    <div class="account-code"></div>
                                                    <div class="account-amount"></div>
                                                </div>
                                            @endif
                                            @foreach ($account['account'] as $records)
                                                @if ($collapseview == 'collapse')
                                                    @foreach ($records as $key => $record)
                                                        @if ($record['account'] == 'parentTotal')
                                                            <div class="account-row parent-account">
                                                                <div class="account-name">
                                                                    <div class="account-arrow">
                                                                        <a href="{{ route('report.balance.sheet', ['vertical', 'expand']) }}">
                                                                            <i class="ti ti-chevron-down account-icon"></i>
                                                                        </a>
                                                                        <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}">
                                                                            {{ str_replace('Total ', '', $record['account_name']) }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="account-code">{{ $record['account_code'] }}</div>
                                                                <div class="account-amount">{{ \Auth::user()->priceFormat($record['netAmount']) }}</div>
                                                            </div>
                                                        @endif

                                                        @if (!preg_match('/\btotal\b/i', $record['account_name']) && $record['account'] == '' && $record['account'] != 'subAccount')
                                                            <div class="account-row sub-account">
                                                                <div class="account-name">
                                                                    <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}">
                                                                        {{ $record['account_name'] }}
                                                                    </a>
                                                                </div>
                                                                <div class="account-code">{{ $record['account_code'] }}</div>
                                                                <div class="account-amount">{{ \Auth::user()->priceFormat($record['netAmount']) }}</div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    @foreach ($records as $key => $record)
                                                        @if ($record['account'] == 'parent' || $record['account'] == 'parentTotal')
                                                            <div class="account-row parent-account">
                                                                <div class="account-name">
                                                                    @if ($record['account'] == 'parent')
                                                                        <div class="account-arrow">
                                                                            <a href="{{ route('report.balance.sheet', ['vertical', 'collapse']) }}">
                                                                                <i class="ti ti-chevron-down account-icon"></i>
                                                                            </a>
                                                                            <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}">
                                                                                {{ $record['account_name'] }}
                                                                            </a>
                                                                        </div>
                                                                    @else
                                                                        <a href="#">{{ $record['account_name'] }}</a>
                                                                    @endif
                                                                </div>
                                                                <div class="account-code">{{ $record['account_code'] }}</div>
                                                                <div class="account-amount">{{ \Auth::user()->priceFormat($record['netAmount']) }}</div>
                                                            </div>
                                                        @endif

                                                        @if ((!preg_match('/\btotal\b/i', $record['account_name']) && $record['account'] == '') || $record['account'] == 'subAccount')
                                                            <div class="account-row sub-account">
                                                                <div class="account-name">
                                                                    <a href="{{ route('report.ledger', $record['account_id']) }}?account={{ $record['account_id'] }}">
                                                                        {{ $record['account_name'] }}
                                                                    </a>
                                                                </div>
                                                                <div class="account-code">{{ $record['account_code'] }}</div>
                                                                <div class="account-amount">{{ \Auth::user()->priceFormat($record['netAmount']) }}</div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </div>

                                        @php
                                            $total += $record['netAmount'] ? $record['netAmount'] : 0;
                                        @endphp
                                    @endforeach
                                    <div class="account-row total-row">
                                        <div class="account-name">{{ 'Total for ' . $type }}</div>
                                        <div class="account-code"></div>
                                        <div class="account-amount">{{ \Auth::user()->priceFormat($total) }}</div>
                                    </div>
                                    @php
                                        if ($type != 'Assets') {
                                            $totalAmount += $total;
                                        }
                                    @endphp
                                </div>
                            @endif
                        @endforeach

                        @foreach ($totalAccounts as $type => $accounts)
                            @php
                                if ($type == 'Assets') {
                                    continue;
                                }
                            @endphp

                            @if ($accounts != [])
                                <div class="account-row grand-total-row">
                                    <div class="account-name">{{ 'Total for Liabilities & Equity' }}</div>
                                    <div class="account-code"></div>
                                    <div class="account-amount">{{ \Auth::user()->priceFormat($totalAmount) }}</div>
                                </div>
                            @endif
                            @php
                                if ($type == 'Liabilities' || $type == 'Equity') {
                                    break;
                                }
                            @endphp
                        @endforeach

                    </div>
                    
                    <div class="balance-sheet-footer">
                        <div style="text-align: center;">
                            <strong style="display: block; margin-bottom: 8px; font-size: 14px; color: var(--gray-800);">Financial Report</strong>
                            <div style="color: var(--gray-600);">Generated on {{ date('F j, Y') }} at {{ date('g:i A') }}</div>
                        </div>
                        <div style="text-align: center; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--gray-200); font-size: 12px; color: var(--gray-500);">
                            <em>This balance sheet represents the financial position as of the specified date range.</em>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection


@push('script-page')
    <script>
        $(document).on('click', '#collapse', function() {
            view = "collapse";
            $.ajax({
                url: '{{ route('report.balance.sheet', 'vertical') }}',
                type: 'GET',
                data: {
                    "view": view,
                },
                success: function(data) {
                    return false;

                    $('#employee_id').empty();
                    $('#employee_id').append('<option value="">{{ __('Select Employee') }}</option>');
                    $('#employee_id').append('<option value="0"> {{ __('All Employee') }} </option>');

                    $.each(data, function(key, value) {
                        $('#employee_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                }
            });
        });
    </script>
@endpush