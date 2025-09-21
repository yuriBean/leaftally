@extends('layouts.admin')
@section('page-title')
    {{ __('Dashboard') }}
@endsection

@push('theme-script')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
@endpush

@push('script-page')
    <script>
        (function() {
            var chartBarOptions = {
                series: [{
                    name: '{{ __('Order') }}',
                    data: {!! json_encode($chartData['data']) !!},

                }, ],

                chart: {
                    height: 300,
                    type: 'area',
                    // type: 'line',
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
                    categories: {!! json_encode($chartData['label']) !!},
                    title: {
                        text: ''
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
                        text: ''
                    },

                }

            };
            var arChart = new ApexCharts(document.querySelector("#chart-sales"), chartBarOptions);
            arChart.render();
        })();
    </script>
@endpush

@section('breadcrumb')
    {{-- <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li> --}}
@endsection
@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="bg-gradient-to-br from-white to-[#F9FCFA] rounded-xl shadow-md hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 border border-[#E5E5E5]/70 p-5">
            <div class="flex items-center justify-between mb-4">
              <span class="text-lg font-semibold text-gray-800 tracking-wide uppercase">
                {{ __('Companies Statistics') }}
              </span>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex w-12 h-12 items-center justify-center rounded-full bg-gradient-to-tr from-yellow-50 to-yellow-100 shadow-sm ring-1 ring-yellow-200/60">
                    <i class="ti ti-users text-xl text-yellow-600"></i>
                </div>
            <div class="flex-1">
                <div class="font-medium text-gray-500 uppercase mb-1">{{ __('Paid Companies') }}</div>
                <div class="text-4xl font-extrabold text-gray-900">{{ $user['total_paid_user'] ?? '0' }}</div>
                <div class="text-gray-500 mt-1">{{ __('Total Companies') }}: {{ $user['total_user'] ?? '0' }}</div>
                <a href="{{ route('users.index') }}" class="inline-block mt-2  font-medium text-yellow-600 hover:text-yellow-700 bg-yellow-50 rounded-md hover:underline p-1 transition-colors">
                  {{ __('View All') }}
                </a>
              </div>
            </div>
          </div>
          

          <div class="bg-gradient-to-br from-white to-[#F9FCFA] rounded-xl shadow-md hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 border border-[#E5E5E5]/70 p-5">
            <div class="flex items-center justify-between mb-4">
              <span class="text-lg font-semibold text-gray-800 tracking-wide uppercase">
                    {{ __('Orders Statistics') }}
                </span>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex w-12 h-12 items-center justify-center rounded-full bg-gradient-to-tr from-blue-50 to-blue-100 shadow-sm ring-1 ring-blue-200/60">
                    <i class="ti ti-users text-xl text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-500 uppercase mb-1">{{ __('Total Order Amount') }}</div>
                    <div class="text-4xl font-extrabold text-gray-900">{{ \Auth::user()->priceFormat($user['total_orders_price'] ?? 0) }}</div>
                    <div class="text-gray-500 mt-1">{{ __('Total Orders') }}: {{ $user['total_orders'] ?? '0' }}</div>
                    <a href="{{ route('order.index') }}"
                        class="inline-block mt-2  font-medium text-blue-600 hover:text-blue-700 bg-blue-50 rounded-md hover:underline p-1 transition-colors">{{ __('View All Orders') }}</a>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-white to-[#F9FCFA] rounded-xl shadow-md hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 border border-[#E5E5E5]/70 p-5">
            <div class="flex items-center justify-between mb-4">
              <span class="text-lg font-semibold text-gray-800 tracking-wide uppercase">
                    {{ __('Plans Statistics') }}
                </span>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex w-12 h-12 items-center justify-center rounded-full bg-gradient-to-tr from-red-50 to-red-100 shadow-sm ring-1 ring-red-200/60">
                    <i class="ti ti-users text-xl text-red-600"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-500 uppercase mb-1">{{ __('Most Purchased Plan') }}</div>
                    <div class="text-4xl font-extrabold text-gray-900">{{ $user['most_purchese_plan'] ?? '0' }}</div>
                    <div class=" text-gray-500 mt-1">{{ __('Total Plans') }}: {{ $user['total_plan'] ?? '0' }}</div>
                    <a href="{{ route('plans.index') }}"
                        class="inline-block mt-2  font-medium text-red-600 hover:text-red-700 bg-red-50 rounded-md hover:underline p-1 transition-colors">{{ __('View All Plans') }}</a>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-6">
        <div class="col-12">
          <div class="bg-gradient-to-br from-white to-[#FAFCFB] border border-gray-200/70 rounded-xl shadow-md hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100/80">
              <h4 class="text-lg font-semibold text-gray-800 tracking-tight flex items-center gap-2">
                <i class="ti ti-shopping-cart text-green-600 text-xl"></i>
                {{ __('Recent Order') }}
              </h4>
              <span class="px-3 py-1 text-sm font-medium bg-green-50 text-green-700 rounded-full shadow-sm">
                {{ __('Last 7 Days') }}
              </span>
            </div>
            <div class="p-6">
              <div class="rounded-lg bg-gradient-to-br from-gray-50 to-white shadow-inner p-4">
                <div id="chart-sales" data-color="primary" data-height="280" class="w-full h-[280px]"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
@endsection
