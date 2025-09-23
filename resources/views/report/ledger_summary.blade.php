@extends('layouts.admin')
@section('page-title')
    {{ __('Ledger Summary') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Ledger Summary') }}</li>
@endsection
@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
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
                    format: 'A2'
                }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
@endpush

@section('action-btn')
    <div class="float-end">

        <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()"data-bs-toggle="tooltip"
            title="{{ __('Download PDF') }}" data-original-title="{{ __('Download PDF') }}">
            <span class="btn-inner--icon"><i class="fas fa-file-pdf"></i></span>
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
                        {{ Form::open(['route' => ['report.ledger'], 'method' => 'GET', 'id' => 'report_ledger']) }}
                        <div class="row align-items-center justify-content-start">
                            <div class="col-md-10 col-12">
                                <div class="row">

                                    <div class="col-md-4 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('start_date', __('Start Date'), ['class' => 'text-type']) }}
                                            {{ Form::date('start_date', $filter['startDateRange'], ['class' => 'month-btn form-control']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('end_date', __('End Date'), ['class' => 'text-type']) }}
                                            {{ Form::date('end_date', $filter['endDateRange'], ['class' => 'month-btn form-control']) }}
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('account', __('Account'), ['class' => 'text-type']) }}
                                            {{-- {{ Form::select('account',$accounts,isset($_GET['account'])?$_GET['account']:'', array('class' => 'form-control select')) }}                                         --}}
                                            <select name="account" class="form-control" required="required">
                                                @foreach ($accounts as $chartAccount)
                                                    <option value="{{ $chartAccount['id'] }}" class="subAccount"
                                                        {{ isset($_GET['account']) && $chartAccount['id'] == $_GET['account'] ? 'selected' : '' }}>
                                                        {{ $chartAccount['name'] }}</option>
                                                    @foreach ($subAccounts as $subAccount)
                                                        @if ($chartAccount['id'] == $subAccount['account'])
                                                            <option value="{{ $subAccount['id'] }}" class="ms-5"
                                                                {{ isset($_GET['account']) && $_GET['account'] == $subAccount['id'] ? 'selected' : '' }}>
                                                                &nbsp; &nbsp;&nbsp; {{ $subAccount['name'] }}</option>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-12">
                                <div class="row">
                                    <div class="col-auto mt-3">

                                        <a href="#" class="btn btn-sm btn-primary me-2"
                                            onclick="document.getElementById('report_ledger').submit(); return false;"
                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                            data-original-title="{{ __('Apply') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>

                                        <a href="{{ route('report.ledger') }}" class="btn btn-sm btn-danger "
                                            data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                            data-original-title="{{ __('Reset') }}">
                                            <span class="btn-inner--icon"><i
                                                    class="ti ti-refresh text-white-off "></i></span>
                                        </a>

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

    <div id="printableArea">
        <div class="row mb-4">
            <div class="col-12 mb-4">
                <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                    <div class="h-1 w-full" style="background:#007C38;"></div>
    
                    <div class="card-body table-border-style">
                        <div class="table-responsive table-new-design bg-white p-4">
                        <table class="table border border-[#E5E5E5] rounded-[8px] mt-4">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Account name') }}</th>
                                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Name') }}</th>
                                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Transaction type') }}</th>
                                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Transaction date') }}</th>
                                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Debit') }}</th>
                                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Credit') }}</th>
                                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Balance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $balance = 0;
                                        $debit = 0;
                                        $credit = 0;
                                        $totalCredit = 0;

                                        $accountArrays = [];
                                        foreach ($chart_accounts as $key => $account) {
                                            $chartDatas = App\Models\Utility::getAccountData($account['id'], $filter['startDateRange'], $filter['endDateRange']);

                                            $chartDatas = $chartDatas->toArray();
                                            $accountArrays[] = $chartDatas;
                                        }
                                    @endphp
                                    @foreach ($accountArrays as $accounts)
                                        @foreach ($accounts as $account)
                                            @if ($account->reference == 'Invoice')
                                                <tr>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->account_name }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->user_name }}</td>
                                                    </td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->invoiceNumberFormat($account->ids) }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->date }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                                    @php
                                                        $total = $account->debit + $account->credit;
                                                        $balance += $total;
                                                        $totalCredit += $total;
                                                    @endphp
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($balance) }}</td>
                                                </tr>
                                            @endif

                                            @if ($account->reference == 'Invoice Payment')
                                                <tr>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->account_name }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->user_name }}</td>
                                                    </td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->invoiceNumberFormat($account->ids) }}{{ __(' Manually Payment') }}
                                                    </td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->date }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                                    @php
                                                        $total = $account->debit + $account->credit;
                                                        $balance -= $total;
                                                    @endphp
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($balance) }}</td>
                                                </tr>
                                            @endif

                                            @if ($account->reference == 'Revenue')
                                                <tr>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->account_name }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->user_name }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ __(' Revenue') }}
                                                    </td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->date }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                                    @php
                                                        $total = $account->debit + $account->credit;
                                                        $balance += $total;
                                                    @endphp
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($balance) }}</td>
                                                </tr>
                                            @endif

                                            @if (
                                                $account->reference == 'Bill' ||
                                                    $account->reference == 'Bill Account' ||
                                                    $account->reference == 'Expense' ||
                                                    $account->reference == 'Expense Account')
                                                <tr>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->account_name }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->user_name }}</td>
                                                    @if ($account->reference == 'Bill' || $account->reference == 'Bill Account')
                                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->billNumberFormat($account->ids) }}
                                                        @else
                                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->expenseNumberFormat($account->ids) }}
                                                    @endif
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->date }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                                    @php
                                                        $total = $account->debit + $account->credit;
                                                        $balance -= $total;
                                                    @endphp
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($balance) }}</td>
                                                </tr>
                                            @endif

                                            @if ($account->reference == 'Bill Payment' || $account->reference == 'Expense Payment')
                                                <tr>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->account_name }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->user_name }}</td>
                                                    @if ($account->reference == 'Bill Payment')
                                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->billNumberFormat($account->ids) }}{{ __(' Manually Payment') }}
                                                        @else
                                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->expenseNumberFormat($account->ids) }}{{ __(' Manually Payment') }}
                                                    @endif
                                                    </td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->date }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                                    @php
                                                        $total = $account->debit + $account->credit;
                                                        $balance -= $total;
                                                    @endphp
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($balance) }}</td>
                                                </tr>
                                            @endif

                                            @if ($account->reference == 'Payment')
                                                <tr>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->account_name }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->user_name }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ __('Payment') }}
                                                    </td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->date }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                                    @php
                                                        $total = $account->debit + $account->credit;
                                                        $balance -= $total;
                                                    @endphp
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($balance) }}</td>
                                                </tr>
                                            @endif

                                            @if ($account->reference == 'Journal')
                                                <tr>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->account_name }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ AUth::user()->journalNumberFormat($account->reference_id) }}
                                                    </td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->date }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                                    @php
                                                        $total = $account->credit - $account->debit;
                                                        $balance += $total;
                                                    @endphp
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($balance) }}</td>
                                                </tr>
                                            @endif

                                            @if ($account->reference == 'Bank Account')
                                                <tr>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->account_name }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->user_name }}</td>
                                                    </td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ AUth::user()->journalNumberFormat($account->reference_id) }}
                                                    </td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->date }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                                    @php
                                                        $total = $account->credit - $account->debit;
                                                        $balance += $total;
                                                    @endphp
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($balance) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </div>
        </div>
    </div>
@endsection
