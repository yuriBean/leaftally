@extends('layouts.admin')
@section('page-title')
    {{ __('Expense Summary') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Report') }}</li>

    <li class="breadcrumb-item">{{ __('Expense Summary') }}</li>
@endsection

@push('theme-script')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
@endpush

@php
    if (isset($_GET['category']) && $_GET['period'] == 'yearly') {
        $chartArr = [];
        foreach ($chartExpenseArr as $innerArray) {
            foreach ($innerArray as $value) {
                $chartArr[] = $value;
            }
        }
    } else {
        $chartArr = $chartExpenseArr[0];
    }
@endphp
@push('script-page')
    <script>
        (function() {
            var chartBarOptions = {
                series: [{
                    name: '{{ __('Expense') }}',
                    data: {!! json_encode($chartArr) !!},

                }, ],

                chart: {
                    height: 300,
                    type: 'area',
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                title: {
                    text: '',
                    align: 'left'
                },
                xaxis: {
                    categories: {!! json_encode($monthList) !!},
                    title: {
                        text: '{{ __('Months') }}'
                    }
                },
                colors: ['#6fd944', '#6fd944'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                yaxis: {
                    title: {
                        text: '{{ __('Expense') }}'
                    },

                }

            };
            var arChart = new ApexCharts(document.querySelector("#chart-sales"), chartBarOptions);
            arChart.render();
        })();
    </script>
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var year = '{{ $currentYear }}';
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
    <div class="d-flex">

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
                        {{ Form::open(['route' => ['report.expense.summary'], 'method' => 'GET', 'id' => 'report_expense_summary']) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-md-10 col-12">
                                <div class="row">
                                    @if (isset($_GET['period']) && $_GET['period'] == 'yearly')
                                        
                                        <div class="col-md-3 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('period', __('Period'), ['class' => 'text-type']) }}
                                                {{ Form::select('period', $periods, isset($_GET['period']) ? $_GET['period'] : '', ['class' => 'form-control period', 'id' => 'period-select']) }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-3 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('period', __('Period'), ['class' => 'text-type']) }}
                                                {{ Form::select('period', $periods, isset($_GET['period']) ? $_GET['period'] : '', ['class' => 'form-control period', 'id' => 'period-select']) }}
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-12 col-12" id="year-select-box">
                                            <div class="btn-box">
                                                {{ Form::label('year', __('Year'), ['class' => 'text-type']) }}
                                                {{ Form::select('year', $yearList, isset($_GET['year']) ? $_GET['year'] : '', ['class' => 'form-control']) }}
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-3 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('category', __('Category'), ['class' => 'text-type']) }}
                                            {{ Form::select('category', $category, isset($_GET['category']) ? $_GET['category'] : '', ['class' => 'form-control']) }}
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12 col-12">
                                        <div class="btn-box">
                                            {{ Form::label('vender', __('Vendor'), ['class' => 'text-type']) }}
                                            {{ Form::select('vender', $vender, isset($_GET['vender']) ? $_GET['vender'] : '', ['class' => 'form-control']) }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-2 col-12">
                                <div class="row">
                                    <div class="col-auto d-flex mt-4">

                                        <a href="#" class="btn btn-sm btn-primary me-2"
                                            onclick="document.getElementById('report_expense_summary').submit(); return false;"
                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                            data-original-title="{{ __('apply') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>

                                        <a href="{{ route('report.expense.summary') }}" class="btn btn-sm btn-danger "
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
        {{-- <div class="row mt-3">
            <div class="col">
                <input type="hidden"
                    value="{{ $filter['category'] . ' ' . __('Expense Summary') . ' ' . 'Report of' . ' ' . $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}"
                    id="filename">
                <div class="card p-4 mb-4">
                    <h7 class="report-text gray-text mb-0">{{ __('Report') }} :</h7>
                    <h6 class="report-text mb-0">{{ __('Expense Summary') }}</h6>
                </div>
            </div>
            @if ($filter['category'] != __('All'))
                <div class="col">
                    <div class="card p-4 mb-4">
                        <h7 class="report-text gray-text mb-0">{{ __('Category') }} :</h7>
                        <h6 class="report-text mb-0">{{ $filter['category'] }}</h6>
                    </div>
                </div>
            @endif
            @if ($filter['vender'] != __('All'))
                <div class="col">
                    <div class="card p-4 mb-4">
                        <h7 class="report-text gray-text mb-0">{{ __('Vendor') }} :</h7>
                        <h6 class="report-text mb-0">{{ $filter['vender'] }}</h6>
                    </div>
                </div>
            @endif
            <div class="col">
                <div class="card p-4 mb-4">
                    <h7 class="report-text gray-text mb-0">{{ __('Duration') }} :</h7>
                    <h6 class="report-text mb-0">{{ $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}</h6>
                </div>
            </div>
        </div> --}}

        <div class="row">
            <div class="col-12" id="chart-container">
                <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                    <div class="h-1 w-full" style="background:#007C38;"></div>
                        <div class="card-body">
                        <div class="scrollbar-inner">
                            <div id="chart-sales" data-color="primary" data-height="300"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                    <div class="h-1 w-full" style="background:#007C38;"></div>
    
                    <div class="card-body table-border-style">
                        <div class="table-responsive table-new-design bg-white p-4">
                        <table id="products-table" class="table border border-[#E5E5E5] rounded-[8px] mt-4">
                            <thead>
                                    <tr>
                                        <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Category') }}</th>

                                        {{-- @if ($periods['yearly'] == 'Yearly')
                                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Year') }}</th>
                                        @else
                                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Category') }}</th>
                                        @endif --}}
                                        @foreach ($monthList as $month)
                                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ $month }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="13" class="px-4 py-3 border border-[#E5E5E5] text-gray-700"><span>{{ __('Payment :') }}</span></td>
                                    </tr>
                                    @foreach ($expenseArr as $i => $expense)
                                        <tr>
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $expense['category'] }}</td>
                                            @foreach ($expense['data'] as $j => $data)
                                                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($data) }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="13" class="px-4 py-3 border border-[#E5E5E5] text-gray-700"><span>{{ __('Bill :') }}</span></td>
                                    </tr>
                                    @foreach ($billArray as $i => $bill)
                                        <tr>
                                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $bill['category'] }}</td>
                                            @foreach ($bill['data'] as $j => $data)
                                                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($data) }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="13" class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                            <span>{{ __('Expense = Payment + Bill :') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                            <h6>{{ __('Total') }}</h6>
                                        </td>

                                        @foreach ($chartExpenseArr as $i => $expense)
                                            @foreach ($expense as $value)
                                                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($value) }}</td>
                                            @endforeach
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
                
            </div>
        </div>
    </div>
@endsection
