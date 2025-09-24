@extends('layouts.admin')
@section('page-title')
{{ __('Dashboard') }}
@endsection

@push('theme-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

<style>
   /* Zameen Tabs Styling - Green/Teal Color Scheme */
   .zameen-tabs {
       background: #fff;
       border-radius: 12px;
       box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
       overflow: hidden;
   }

   .zameen-tab-nav {
       display: flex;
       background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
       border-bottom: 1px solid #d1fae5;
       margin: 0;
       padding: 0;
       list-style: none;
       flex-wrap: wrap;
   }

   .zameen-tab-item {
       margin: 0;
   }

   .zameen-tab-link {
       display: flex;
       align-items: center;
       padding: 16px 20px;
       color: #374151;
       text-decoration: none;
       font-weight: 500;
       border-bottom: 3px solid transparent;
       transition: all 0.3s ease;
       white-space: nowrap;
   }

   .zameen-tab-link:hover {
       background: #d1fae5;
       color: #065f46;
   }

   .zameen-tab-link.active {
       background: #fff;
       color: #007c38;
       border-bottom-color: #39b549;
       box-shadow: 0 2px 4px rgba(16, 185, 129, 0.1);
   }

   .zameen-tab-link i {
       margin-right: 8px;
       font-size: 18px;
   }

   .zameen-tab-content {
       padding: 24px;
       background: linear-gradient(135deg, #f9fafb 0%, #f0fdf4 100%);
       min-height: 600px;
   }

   .zameen-tab-pane {
       display: none;
   }

   .zameen-tab-pane.active {
       display: block;
   }

   /* Stats Grid Styling */
   .zameen-stats-grid {
       display: grid;
       grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
       gap: 20px;
       margin-bottom: 30px;
   }

   .zameen-stat-card {
       background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
       border: 1px solid #d1fae5;
       border-radius: 12px;
       padding: 24px;
       transition: all 0.3s ease;
       box-shadow: 0 1px 3px rgba(16, 185, 129, 0.1);
   }

   .zameen-stat-card:hover {
       box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
       transform: translateY(-3px);
       border-color: #a7f3d0;
   }

   .zameen-stat-header {
       display: flex;
       justify-content: space-between;
       align-items: center;
       margin-bottom: 16px;
   }

   .zameen-stat-title {
       font-size: 14px;
       font-weight: 600;
       color: #374151;
       margin: 0;
       text-transform: uppercase;
       letter-spacing: 0.5px;
   }

   .zameen-stat-icon {
       width: 48px;
       height: 48px;
       border-radius: 12px;
       background: linear-gradient(135deg, #39b549 0%, #007c38 100%);
       display: flex;
       align-items: center;
       justify-content: center;
       color: #fff;
       font-size: 20px;
       box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
   }

   .zameen-stat-value {
       font-size: 36px;
       font-weight: 800;
       color: #065f46;
       margin-bottom: 8px;
       background: linear-gradient(135deg, #007c38 0%, #047857 100%);
       -webkit-background-clip: text;
       -webkit-text-fill-color: transparent;
       background-clip: text;
   }

   .zameen-stat-change {
       display: flex;
       align-items: center;
       font-size: 13px;
       font-weight: 600;
       padding: 4px 8px;
       border-radius: 6px;
       background: #d1fae5;
       color: #065f46;
       width: fit-content;
   }

   .zameen-stat-change.positive {
       background: #d1fae5;
       color: #065f46;
   }

   .zameen-stat-change.negative {
       background: #fee2e2;
       color: #dc2626;
   }

   .zameen-stat-change i {
       margin-right: 4px;
       font-size: 14px;
   }

   /* Chart Container - Green Theme */
   .zameen-chart-container {
       background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
       border: 1px solid #d1fae5;
       border-radius: 12px;
       padding: 24px;
       margin-bottom: 20px;
       box-shadow: 0 2px 8px rgba(16, 185, 129, 0.08);
   }

   .zameen-chart-container:hover {
       border-color: #a7f3d0;
       box-shadow: 0 4px 16px rgba(16, 185, 129, 0.12);
   }

   /* Tab Content Background */
   .zameen-tab-content {
       padding: 24px;
       background: linear-gradient(135deg, #f9fafb 0%, #f0fdf4 100%);
       min-height: 600px;
   }

   /* Additional Green Theme Enhancements */
   .card {
       border: 1px solid #d1fae5;
       box-shadow: 0 1px 3px rgba(16, 185, 129, 0.1);
   }

   .card:hover {
       border-color: #a7f3d0;
       box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
   }

   .btn-primary {
       background: linear-gradient(135deg, #39b549 0%, #007c38 100%);
       border-color: #39b549;
   }

   .btn-primary:hover {
       background: linear-gradient(135deg, #007c38 0%, #047857 100%);
       border-color: #007c38;
   }

   .text-primary {
       color: #007c38 !important;
   }

   /* Quick Actions Tab Styling */
   #settings .btn {
       border: none !important;
       box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
       transition: all 0.3s ease;
   }

   #settings .btn:hover {
       transform: translateY(-1px);
       box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
       border: none !important;
   }

   #settings .btn:focus,
   #settings .btn:active {
       border: none !important;
       box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2) !important;
   }

   #settings .card {
       border: 1px solid #d1fae5;
       transition: all 0.3s ease;
       min-height: 240px;
       margin-bottom: 2rem;
   }

   #settings .card:hover {
       border-color: #a7f3d0;
       box-shadow: 0 4px 16px rgba(16, 185, 129, 0.15);
   }

   /* Enhanced card body padding for Quick Actions */
   #settings .card-body {
       padding: 2.5rem 2rem !important;
       display: flex;
       flex-direction: column;
       justify-content: center;
       align-items: center;
       min-height: 200px;
   }

   #settings .zameen-stat-icon {
       margin: 0 auto 2rem !important;
       width: 80px !important;
       height: 80px !important;
   }

   #settings .zameen-stat-icon i {
       font-size: 2rem !important;
   }

   #settings h5 {
       margin-bottom: 1.5rem !important;
       font-size: 1.1rem !important;
       font-weight: 600 !important;
       color: #374151 !important;
   }

   #settings .btn {
       margin-top: 0.5rem;
       padding: 0.75rem 1.5rem !important;
       font-size: 0.9rem !important;
       font-weight: 600 !important;
   }

   /* Page header styling */
   .page-header {
       background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
       border-bottom: 1px solid #d1fae5;
   }

   /* Legacy analytics styles for compatibility */
   #analytics .zameen-stats-grid {
       display: grid;
       grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
       gap: 1.5rem;
       margin-bottom: 3rem;
   }

   #analytics .zameen-stat-card {
       min-height: 140px;
       display: flex;
       flex-direction: column;
       justify-content: space-between;
   }

   #analytics .zameen-chart-container {
       margin-bottom: 2rem;
   }

   #analytics .row {
       margin-left: -15px;
       margin-right: -15px;
   }

   #analytics .col-md-6,
   #analytics .col-md-4 {
       padding-left: 15px;
       padding-right: 15px;
   }

   /* Prevent chart container overlap and sizing issues */
   #analytics canvas {
       max-width: 100%;
       height: auto !important;
   }

   /* Chart sizing fixes for tab switching */
   .zameen-tab-pane canvas {
       max-width: 100% !important;
       height: 400px !important;
       max-height: 400px !important;
   }

   .zameen-chart-container canvas {
       width: 100% !important;
       height: 400px !important;
       max-height: 400px !important;
   }

   /* Specific chart container sizing */
   #cashflowChart,
   #incomeExpenseChart,
   #revenueChart,
   #expenseChart {
       max-width: 100% !important;
       height: 400px !important;
   }

   /* Card equal heights */
   #analytics .card {
       height: 100%;
       display: flex;
       flex-direction: column;
   }

   #analytics .card-body {
       flex: 1;
   }

   /* Dashboard module cards styles */
   .dashboard-module-cards {
   position: relative;
   transition: opacity 0.3s;
   }

   /* Dashboard module cards styles */
   .dashboard-module-cards-hidden {
   opacity: 0.3;
   pointer-events: none;
   }

   /* Dashboard module cards styles */
   .dashboard-module-cards.customize-mode {
   opacity: 0.5;
   pointer-events: auto;
   display: block !important;
   }

   /* Dashboard module cards styles */
   .dashboard-module-cards.customize-mode .customize-toggle {
   display: inline-block !important;
   }
   .customize-toggle {
   cursor: pointer;
   position: absolute;
   top: 10px;
   right: 20px;
   z-index: 10;
   font-size: 1.2em;
   }
   .hidden-module {
   display: none;
   }
   .div-box-height{
   height:100%;
   }
</style>
@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
   const cashflowCtx = document.getElementById('cashflowChart').getContext('2d');
   const currencySymbol = '{{ \Auth::user()->currencySymbol() }}';

   // Store chart instances globally for tab switching
   window.dashboardCharts = window.dashboardCharts || {};

   window.dashboardCharts['cashflowChart'] = new Chart(cashflowCtx, {
       type: 'line',
       data: {
           labels: {!! json_encode($incExpLineChartData['day']) !!},
           datasets: [{
                   label: 'Income',
                   data: {!! json_encode($incExpLineChartData['income']) !!},
                   borderColor: '#39b549',
                   backgroundColor: 'rgba(16, 185, 129, 0.1)',
                   tension: 0.4,
                   fill: false,
                   pointRadius: 0,
                   borderWidth: 3,
               },
               {
                   label: 'Expense',
                   data: {!! json_encode($incExpLineChartData['expense']) !!},
                   borderColor: '#007c38',
                   backgroundColor: 'rgba(5, 150, 105, 0.2)',
                   tension: 0.4,
                   fill: true,
                   pointRadius: 0,
                   borderWidth: 3,
               }
           ]
       },
       options: {
           responsive: true,
           plugins: {
               legend: {
                   display: false
               }
           },
           scales: {
               y: {
                   beginAtZero: true,
                   ticks: {
                       callback: value => `${currencySymbol}${(value / 100000).toFixed(0)}K`,
                       color: '#6b7280'
                   },
                   grid: {
                       color: '#e5e7eb'
                   }
               },
               x: {
                   ticks: {
                       color: '#6b7280',
                       maxRotation: 0,
                       minRotation: 0,
                   },
                   grid: {
                       display: false

                   }
               }
           }
       }
   });

const incExpBarCtx = document.getElementById('incomeExpenseChart').getContext('2d');
window.dashboardCharts['incomeExpenseChart'] = new Chart(incExpBarCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($incExpBarChartData['month']) !!},
        datasets: [
            {
                label: 'Income',
                data: {!! json_encode($incExpBarChartData['income']) !!},
                backgroundColor: '#15803D',
                borderRadius: 0,
            },
            {
                label: 'Expense',
                data: {!! json_encode($incExpBarChartData['expense']) !!},
                backgroundColor: '#333333',
                borderRadius: 0,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => `${currencySymbol}${(value / 100000).toFixed(0)}K`,
                    color: '#4B5563'
                },
                grid: {
                    display: false,
                    drawTicks: false,
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    color: '#4B5563',
                    autoSkip: false,
                    maxRotation: 0,
                    minRotation: 0,
                    callback: function(value) {
                        let month = this.getLabelForValue(value);
                        return month.length > 3 ? month.slice(0, 3) : month;
                    }
                },
                grid: {
                    display: false,
                    drawTicks: false,
                    drawBorder: false
                },
                barPercentage: 0.4,
                categoryPercentage: 0.7
            }
        }
    },
    plugins: [{
        id: 'customDots',
        beforeDraw: (chart) => {
            const { ctx, chartArea: { top, bottom, left, right } } = chart;
            ctx.save();
            ctx.fillStyle = '#E5E7EB';

            const dotSpacing = 12;
            const rowOffset = 6;

            let rowIndex = 0;
            for (let y = top; y <= bottom; y += dotSpacing) {
                const offset = (rowIndex % 2 === 0) ? 0 : rowOffset;
                for (let x = left; x <= right; x += dotSpacing) {
                    ctx.beginPath();
                    ctx.arc(x + offset, y, 1.2, 0, Math.PI * 2);
                    ctx.fill();
                }
                rowIndex++;
            }
            ctx.restore();
        }
    }]
});

   const expenseDonutCtx = document.getElementById('expenseChart').getContext('2d');
   window.dashboardCharts['expenseChart'] = new Chart(expenseDonutCtx, {
       type: 'doughnut',
       data: {

           datasets: [{
               data: {!! json_encode($expenseCatAmount) !!},
               backgroundColor: {!! json_encode($expenseCategoryColor) !!},
               borderWidth: 0
           }]
       },
       options: {
           responsive: true,
           cutout: '70%',
           plugins: {
               legend: {
                   display: true
               }
           }
       }
   });

   const incomeDonutCtx = document.getElementById('incomeChart').getContext('2d');
   window.dashboardCharts['incomeChart'] = new Chart(incomeDonutCtx, {
       type: 'doughnut',
       data: {

           datasets: [{
               data: {!! json_encode($incomeCatAmount) !!},
               backgroundColor: {!! json_encode($incomeCategoryColor) !!},
               borderWidth: 0
           }]
       },
       options: {
           responsive: true,
           cutout: '70%',
           plugins: {
               legend: {
                   display: true
               }
           }
       }
   });

</script>
@endpush
@php
use App\Models\Utility;
use Carbon\Carbon;
$showAccountBalance = !isset($customization['account_balance']) || $customization['account_balance'];
$showCashflow = !isset($customization['cashflow']) || $customization['cashflow'];
$showInvoices = !isset($customization['invoices']) || $customization['invoices'];
$showBills = !isset($customization['bills']) || $customization['bills'];
$showBusinessOverview = !isset($customization['business_overview']) || $customization['business_overview'];
$showIncomeExpense = !isset($customization['income_expense']) || $customization['income_expense'];
$showCategory = !isset($customization['category']) || $customization['category'];
$showGoal = !isset($customization['goal']) || $customization['goal'];
$showRecentBills = !isset($customization['recent_bills']) || $customization['recent_bills'];
$showLatestIncome = !isset($customization['latest_income']) || $customization['latest_income'];
$showRecentInvoices = !isset($customization['recent_invoices']) || $customization['recent_invoices'];
$showLatestExpense = !isset($customization['latest_expense']) || $customization['latest_expense'];
@endphp
{{-- @section('breadcrumb')
@endsection --}}
@section('content')

<div class="zameen-tabs">

    <ul class="zameen-tab-nav">
        <li class="zameen-tab-item">
            <a href="#overview" class="zameen-tab-link active" data-tab="overview">
                <i class="ti ti-chart-bar"></i>
                {{ __('Business Overview') }}
            </a>
        </li>
        <li class="zameen-tab-item">
            <a href="#finance" class="zameen-tab-link" data-tab="finance">
                <i class="ti ti-currency-dollar"></i>
                {{ __('Financial Summary') }}
            </a>
        </li>
        <li class="zameen-tab-item">
            <a href="#cashflow" class="zameen-tab-link" data-tab="cashflow">
                <i class="ti ti-trending-up"></i>
                {{ __('Cashflow') }}
            </a>
        </li>
        <li class="zameen-tab-item">
            <a href="#goals" class="zameen-tab-link" data-tab="goals">
                <i class="ti ti-target"></i>
                {{ __('Goals') }}
            </a>
        </li>
        <li class="zameen-tab-item">
            <a href="#invoices" class="zameen-tab-link" data-tab="invoices">
                <i class="ti ti-file-invoice"></i>
                {{ __('Invoices') }}
            </a>
        </li>
        <li class="zameen-tab-item">
            <a href="#bills" class="zameen-tab-link" data-tab="bills">
                <i class="ti ti-receipt"></i>
                {{ __('Bills') }}
            </a>
        </li>
        <li class="zameen-tab-item">
            <a href="#transactions" class="zameen-tab-link" data-tab="transactions">
                <i class="ti ti-receipt"></i>
                {{ __('Recent Transactions') }}
            </a>
        </li>
        <li class="zameen-tab-item">
            <a href="#settings" class="zameen-tab-link" data-tab="settings">
                <i class="ti ti-settings"></i>
                {{ __('Quick Actions') }}
            </a>
        </li>
    </ul>

    <div class="zameen-tab-content">

        <div id="overview" class="zameen-tab-pane active">
            <div class="zameen-stats-grid">

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Total Customers') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-users"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">{{ \Auth::user()->countCustomers() }}</div>
                    <div class="zameen-stat-change positive">
                        <i class="ti ti-arrow-up"></i>
                        <span>+12% from last month</span>
                    </div>
                </div>

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Total Vendors') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-building-store"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">{{ \Auth::user()->countVenders() }}</div>
                    <div class="zameen-stat-change positive">
                        <i class="ti ti-arrow-up"></i>
                        <span>+8% from last month</span>
                    </div>
                </div>

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Total Invoices') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-file-invoice"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">{{ \Auth::user()->countInvoices() }}</div>
                    <div class="zameen-stat-change positive">
                        <i class="ti ti-arrow-up"></i>
                        <span>+15% from last month</span>
                    </div>
                </div>

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Total Bills') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-receipt"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">{{ \Auth::user()->countBills() }}</div>
                    <div class="zameen-stat-change positive">
                        <i class="ti ti-arrow-up"></i>
                        <span>+5% from last month</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="finance" class="zameen-tab-pane">
            <div class="zameen-stats-grid">

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Monthly Income') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-trending-up"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">{{ \Auth::user()->priceFormat(\Auth::user()->incomeCurrentMonth()) }}</div>
                    <div class="zameen-stat-change positive">
                        <i class="ti ti-arrow-up"></i>
                        <span>+18% from last month</span>
                    </div>
                </div>

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Monthly Expense') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-trending-down"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">{{ \Auth::user()->priceFormat(\Auth::user()->expenseCurrentMonth()) }}</div>
                    <div class="zameen-stat-change negative">
                        <i class="ti ti-arrow-down"></i>
                        <span>-5% from last month</span>
                    </div>
                </div>

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Account Balance') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-wallet"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">
                        @php
                            $totalBalance = 0;
                            foreach($bankAccountDetail as $account) {
                                $totalBalance += $account->opening_balance;
                            }
                        @endphp
                        {{ \Auth::user()->priceFormat($totalBalance) }}
                    </div>
                    <div class="zameen-stat-change positive">
                        <i class="ti ti-arrow-up"></i>
                        <span>+3% from last month</span>
                    </div>
                </div>

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Due Invoices') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-clock"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">{{ \Auth::user()->priceFormat($monthlyInvoice['invoiceDue']) }}</div>
                    <div class="zameen-stat-change negative">
                        <i class="ti ti-arrow-down"></i>
                        <span>Overdue amount</span>
                    </div>
                </div>
            </div>

            <div class="zameen-chart-container">
                <h3 style="margin-bottom: 1rem; color: var(--zameen-text-dark); font-weight: 600;">Income vs Expense Chart</h3>
                <canvas id="incomeExpenseChart" style="width: 100%; height: 300px;"></canvas>
            </div>

            <div style="margin-top: 2rem;">

                <div class="zameen-content-card">
                    <div class="zameen-card-header">
                        <h3 style="color: var(--zameen-text-dark); font-weight: 600; margin: 0;">
                            <i class="ti ti-wallet" style="margin-right: 0.5rem; color: var(--zameen-primary);"></i>
                            {{ __('Account Details') }}
                        </h3>
                    </div>
                    <div class="zameen-card-content">
                        @if($bankAccountDetail->count() > 0)
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                @foreach($bankAccountDetail as $account)
                                    <div style="padding: 1rem; background: var(--zameen-bg-light); border-radius: 8px; border: 1px solid var(--zameen-border);">
                                        <div style="display: flex; justify-content: space-between; align-items: start;">
                                            <div>
                                                <h4 style="font-weight: 600; color: var(--zameen-text-dark); margin: 0 0 0.25rem;">{{ $account->bank_name }}</h4>
                                                <p style="margin: 0; font-size: 0.875rem; color: var(--zameen-text-medium);">{{ $account->holder_name }}</p>
                                            </div>
                                            <span style="font-weight: 700; color: var(--zameen-primary); font-size: 1.1rem;">{{ \Auth::user()->priceFormat($account->opening_balance) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="text-align: center; padding: 3rem 1rem; color: var(--zameen-text-medium);">
                                <i class="ti ti-building-bank" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                                <p style="margin: 0; font-size: 1.1rem;">{{ __('No bank accounts') }}</p>
                                <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">{{ __('Add bank accounts to track balances') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div id="cashflow" class="zameen-tab-pane">

            <div class="zameen-stats-grid" style="margin-bottom: 3rem;">

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Cash Inflow') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-arrow-down-circle"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">{{ \Auth::user()->priceFormat(\Auth::user()->incomeCurrentMonth()) }}</div>
                    <div class="zameen-stat-change positive">
                        <i class="ti ti-arrow-up"></i>
                        <span>This month</span>
                    </div>
                </div>

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Cash Outflow') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-arrow-up-circle"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">{{ \Auth::user()->priceFormat(\Auth::user()->expenseCurrentMonth()) }}</div>
                    <div class="zameen-stat-change negative">
                        <i class="ti ti-arrow-down"></i>
                        <span>This month</span>
                    </div>
                </div>

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Net Cashflow') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-trending-up"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">{{ \Auth::user()->priceFormat(\Auth::user()->incomeCurrentMonth() - \Auth::user()->expenseCurrentMonth()) }}</div>
                    <div class="zameen-stat-change {{ (\Auth::user()->incomeCurrentMonth() - \Auth::user()->expenseCurrentMonth()) >= 0 ? 'positive' : 'negative' }}">
                        <i class="ti ti-arrow-{{ (\Auth::user()->incomeCurrentMonth() - \Auth::user()->expenseCurrentMonth()) >= 0 ? 'up' : 'down' }}"></i>
                        <span>{{ (\Auth::user()->incomeCurrentMonth() - \Auth::user()->expenseCurrentMonth()) >= 0 ? 'Positive' : 'Negative' }} flow</span>
                    </div>
                </div>

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Operating CF') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-cash"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">{{ \Auth::user()->priceFormat(\Auth::user()->incomeCurrentMonth() * 0.8) }}</div>
                    <div class="zameen-stat-change positive">
                        <i class="ti ti-arrow-up"></i>
                        <span>80% of revenue</span>
                    </div>
                </div>
            </div>

            <div class="zameen-chart-container">
                <h3 style="margin-bottom: 1rem; color: var(--zameen-text-dark); font-weight: 600;">Monthly Cashflow Trend</h3>
                <canvas id="cashflowChart" style="width: 100%; height: 350px;"></canvas>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
                <div class="zameen-content-card">
                    <div class="zameen-card-header">
                        <h3 style="color: var(--zameen-text-dark); font-weight: 600; margin: 0;">
                            <i class="ti ti-trending-up" style="margin-right: 0.5rem; color: var(--zameen-primary);"></i>
                            {{ __('Cashflow Insights') }}
                        </h3>
                    </div>
                    <div class="zameen-card-content">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="padding: 1rem; background: var(--zameen-bg-light); border-radius: 8px; border-left: 4px solid var(--zameen-primary);">
                                <h4 style="margin: 0 0 0.5rem; color: var(--zameen-text-dark); font-weight: 600;">{{ __('Best Month') }}</h4>
                                <p style="margin: 0; color: var(--zameen-text-medium); font-size: 0.875rem;">{{ __('Your highest cashflow month generated') }} <strong>{{ \Auth::user()->priceFormat(\Auth::user()->incomeCurrentMonth()) }}</strong></p>
                            </div>
                            <div style="padding: 1rem; background: var(--zameen-bg-light); border-radius: 8px; border-left: 4px solid #f59e0b;">
                                <h4 style="margin: 0 0 0.5rem; color: var(--zameen-text-dark); font-weight: 600;">{{ __('Average Monthly') }}</h4>
                                <p style="margin: 0; color: var(--zameen-text-medium); font-size: 0.875rem;">{{ __('Your average monthly cashflow is') }} <strong>{{ \Auth::user()->priceFormat((\Auth::user()->incomeCurrentMonth() + \Auth::user()->expenseCurrentMonth()) / 2) }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="zameen-content-card">
                    <div class="zameen-card-header">
                        <h3 style="color: var(--zameen-text-dark); font-weight: 600; margin: 0;">
                            <i class="ti ti-alert-triangle" style="margin-right: 0.5rem; color: #f59e0b;"></i>
                            {{ __('Cashflow Health') }}
                        </h3>
                    </div>
                    <div class="zameen-card-content">
                        @php
                            $cashflowRatio = \Auth::user()->incomeCurrentMonth() > 0 ? (\Auth::user()->incomeCurrentMonth() - \Auth::user()->expenseCurrentMonth()) / \Auth::user()->incomeCurrentMonth() * 100 : 0;
                        @endphp
                        <div style="text-align: center; padding: 2rem 0;">
                            <div style="width: 120px; height: 120px; margin: 0 auto 1rem; border-radius: 50%; background: conic-gradient(var(--zameen-primary) {{ $cashflowRatio }}%, #e5e7eb {{ $cashflowRatio }}%); display: flex; align-items: center; justify-content: center; position: relative;">
                                <div style="width: 80px; height: 80px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--zameen-text-dark);">
                                    {{ number_format($cashflowRatio, 1) }}%
                                </div>
                            </div>
                            <h4 style="margin: 0 0 0.5rem; color: var(--zameen-text-dark);">
                                @if($cashflowRatio > 20)
                                    {{ __('Excellent') }}
                                @elseif($cashflowRatio > 10)
                                    {{ __('Good') }}
                                @elseif($cashflowRatio > 0)
                                    {{ __('Moderate') }}
                                @else
                                    {{ __('Needs Attention') }}
                                @endif
                            </h4>
                            <p style="margin: 0; color: var(--zameen-text-medium); font-size: 0.875rem;">{{ __('Cashflow efficiency rating') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="goals" class="zameen-tab-pane">

            <div class="zameen-stats-grid" style="margin-bottom: 3rem;">

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Total Goals') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-target"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">{{ $goals->count() }}</div>
                    <div class="zameen-stat-change positive">
                        <i class="ti ti-arrow-up"></i>
                        <span>Active goals</span>
                    </div>
                </div>

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Completed') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-check"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">
                        @php
                            $completedGoals = $goals->filter(function($goal) {
                                $percentage = $goal->target($goal->type, $goal->from, $goal->to, $goal->amount)['percentage'];
                                return $percentage >= 100;
                            })->count();
                        @endphp
                        {{ $completedGoals }}
                    </div>
                    <div class="zameen-stat-change positive">
                        <i class="ti ti-arrow-up"></i>
                        <span>100% achieved</span>
                    </div>
                </div>

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('In Progress') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-clock"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">
                        @php
                            $inProgressGoals = $goals->filter(function($goal) {
                                $percentage = $goal->target($goal->type, $goal->from, $goal->to, $goal->amount)['percentage'];
                                return $percentage > 0 && $percentage < 100;
                            })->count();
                        @endphp
                        {{ $inProgressGoals }}
                    </div>
                    <div class="zameen-stat-change neutral">
                        <i class="ti ti-arrow-right"></i>
                        <span>Working on it</span>
                    </div>
                </div>

                <div class="zameen-stat-card">
                    <div class="zameen-stat-header">
                        <h3 class="zameen-stat-title">{{ __('Avg Progress') }}</h3>
                        <div class="zameen-stat-icon">
                            <i class="ti ti-percentage"></i>
                        </div>
                    </div>
                    <div class="zameen-stat-value">
                        @php
                            $totalProgress = $goals->sum(function($goal) {
                                return $goal->target($goal->type, $goal->from, $goal->to, $goal->amount)['percentage'];
                            });
                            $avgProgress = $goals->count() > 0 ? $totalProgress / $goals->count() : 0;
                        @endphp
                        {{ number_format($avgProgress, 1) }}%
                    </div>
                    <div class="zameen-stat-change {{ $avgProgress >= 70 ? 'positive' : ($avgProgress >= 40 ? 'neutral' : 'negative') }}">
                        <i class="ti ti-arrow-{{ $avgProgress >= 70 ? 'up' : ($avgProgress >= 40 ? 'right' : 'down') }}"></i>
                        <span>Overall progress</span>
                    </div>
                </div>
            </div>

            @if($goals->count() > 0)
                <div class="zameen-content-card">
                    <div class="zameen-card-header">
                        <h3 style="color: var(--zameen-text-dark); font-weight: 600; margin: 0;">
                            <i class="ti ti-list" style="margin-right: 0.5rem; color: var(--zameen-primary);"></i>
                            {{ __('Your Business Goals') }}
                        </h3>
                    </div>
                    <div class="zameen-card-content">
                        <div style="display: grid; gap: 1.5rem;">
                            @foreach($goals as $goal)
                                @php
                                    $total = $goal->target($goal->type, $goal->from, $goal->to, $goal->amount)['total'];
                                    $percentage = $goal->target($goal->type, $goal->from, $goal->to, $goal->amount)['percentage'];
                                    $status = $percentage >= 100 ? 'completed' : ($percentage > 0 ? 'progress' : 'not-started');
                                @endphp
                                <div style="padding: 2rem; background: var(--zameen-bg-light); border-radius: 16px; border: 1px solid var(--zameen-border); position: relative; overflow: hidden;">

                                    <div style="position: absolute; top: 0; left: 0; height: 100%; background: linear-gradient(90deg, var(--zameen-primary)10 0%, var(--zameen-primary)05 100%); width: {{ $percentage }}%; transition: width 0.3s ease;"></div>

                                    <div style="position: relative; z-index: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                            <div>
                                                <h4 style="font-weight: 700; color: var(--zameen-text-dark); margin: 0 0 0.5rem; font-size: 1.25rem;">{{ $goal->name }}</h4>
                                                <div style="display: flex; gap: 1rem; font-size: 0.875rem; color: var(--zameen-text-medium);">
                                                    <span><strong>{{ __('Type:') }}</strong> {{ __(\App\Models\Goal::$goalType[$goal->type]) }}</span>
                                                    <span><strong>{{ __('Period:') }}</strong> {{ $goal->from }} - {{ $goal->to }}</span>
                                                </div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-size: 2rem; font-weight: 800; color: var(--zameen-primary); line-height: 1;">{{ number_format($percentage, 1) }}%</div>
                                                <div style="font-size: 0.75rem; color: var(--zameen-text-medium); margin-top: 0.25rem;">
                                                    @if($status === 'completed')
                                                        <span style="color: #007c38; font-weight: 600;">✓ {{ __('Completed') }}</span>
                                                    @elseif($status === 'progress')
                                                        <span style="color: #f59e0b; font-weight: 600;">⚡ {{ __('In Progress') }}</span>
                                                    @else
                                                        <span style="color: #6b7280; font-weight: 600;">⏸ {{ __('Not Started') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div style="width: 100%; background: #e5e7eb; border-radius: 999px; height: 12px; overflow: hidden; margin-bottom: 1rem;">
                                            <div style="background: linear-gradient(90deg, var(--zameen-primary) 0%, #007c38 100%); height: 100%; border-radius: 999px; transition: width 0.3s ease; width: {{ $percentage }}%; position: relative;">
                                                @if($percentage > 10)
                                                    <div style="position: absolute; top: 50%; right: 8px; transform: translateY(-50%); color: white; font-size: 0.75rem; font-weight: 600;">{{ number_format($percentage, 0) }}%</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; color: var(--zameen-text-medium);">
                                            <span><strong>{{ __('Current:') }}</strong> {{ \Auth::user()->priceFormat($total) }}</span>
                                            <span><strong>{{ __('Target:') }}</strong> {{ \Auth::user()->priceFormat($goal->amount) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else

                <div class="zameen-content-card">
                    <div class="zameen-card-content">
                        <div style="text-align: center; padding: 4rem 2rem; color: var(--zameen-text-medium);">
                            <i class="ti ti-target" style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.3;"></i>
                            <h3 style="margin: 0 0 0.5rem; color: var(--zameen-text-dark); font-weight: 600;">{{ __('No Goals Set Yet') }}</h3>
                            <p style="margin: 0 0 2rem; font-size: 1.1rem;">{{ __('Set your first business goal to start tracking your progress') }}</p>
                            <a href="{{ route('goal.index') }}" style="background: var(--zameen-primary); color: white; border: none; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block;">
                                <i class="ti ti-plus" style="margin-right: 0.5rem;"></i>
                                {{ __('Create Your First Goal') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div id="invoices" class="zameen-tab-pane">
            <div class="card" style="border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 2rem;">
                <div class="card-header" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); color: #1f2937; border-radius: 12px 12px 0 0; padding: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 style="margin: 0; font-weight: 600; font-size: 18px;">{{ __('Invoices') }}</h5>
                            <p style="margin: 0; font-size: 14px; color: #6b7280;">{{ __('See all statistics') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group" style="background: #f3f4f6; border-radius: 8px; padding: 2px;">
                                <button type="button" class="btn btn-sm invoice-period-btn active" data-period="weekly" style="background: #39b549; color: white; border: none; border-radius: 6px; padding: 6px 16px; font-size: 12px;">{{ __('Weekly') }}</button>
                                <button type="button" class="btn btn-sm invoice-period-btn" data-period="monthly" style="background: transparent; color: #6b7280; border: none; border-radius: 6px; padding: 6px 16px; font-size: 12px;">{{ __('Monthly') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding: 2rem;">

                    <div class="row">

                        <div class="col-md-4">
                            <div class="card" style="border-radius: 10px; border: 1px solid #e5e7eb; box-shadow: 0 2px 6px rgba(0,0,0,0.04); background: #fff; height: 100%;">
                                <div class="card-body" style="padding: 1.5rem; text-align: center;">
                                    <div style="margin-bottom: 0.5rem;">
                                        <span style="font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('TOTAL') }}</span>
                                    </div>
                                    <div style="margin-bottom: 0.75rem;">
                                        <span style="font-size: 14px; color: #1f2937; font-weight: 500;">{{ __('Invoice Generated') }}</span>
                                    </div>
                                    <div>
                                        <span class="invoice-total-amount" style="font-size: 18px; font-weight: 700; color: #1f2937;">{{ \Auth::user()->priceFormat($monthlyInvoice['invoiceTotal']) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card" style="border-radius: 10px; border: 1px solid #e5e7eb; box-shadow: 0 2px 6px rgba(0,0,0,0.04); background: #fff; height: 100%;">
                                <div class="card-body" style="padding: 1.5rem; text-align: center;">
                                    <div style="margin-bottom: 0.5rem;">
                                        <span style="font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('TOTAL') }}</span>
                                    </div>
                                    <div style="margin-bottom: 0.75rem;">
                                        <span style="font-size: 14px; color: #1f2937; font-weight: 500;">{{ __('Paid') }}</span>
                                    </div>
                                    <div>
                                        <span class="invoice-paid-amount" style="font-size: 18px; font-weight: 700; color: #39b549;">{{ \Auth::user()->priceFormat($monthlyInvoice['invoicePaid']) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card" style="border-radius: 10px; border: 1px solid #e5e7eb; box-shadow: 0 2px 6px rgba(0,0,0,0.04); background: #fff; height: 100%;">
                                <div class="card-body" style="padding: 1.5rem; text-align: center;">
                                    <div style="margin-bottom: 0.5rem;">
                                        <span style="font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('TOTAL') }}</span>
                                    </div>
                                    <div style="margin-bottom: 0.75rem;">
                                        <span style="font-size: 14px; color: #1f2937; font-weight: 500;">{{ __('Due') }}</span>
                                    </div>
                                    <div>
                                        <span class="invoice-due-amount" style="font-size: 18px; font-weight: 700; color: #ef4444;">{{ \Auth::user()->priceFormat($monthlyInvoice['invoiceDue']) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($recentInvoice) > 0)
                <div class="card" style="border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    <div class="card-header" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); color: #1f2937; border-radius: 12px 12px 0 0; padding: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 style="margin: 0; font-weight: 600; font-size: 18px;">{{ __('Recent Invoices') }}</h5>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="{{ route('invoice.index') }}" class="btn btn-primary btn-sm" style="background: var(--zameen-primary); border: none; border-radius: 8px;">
                                    <i class="ti ti-eye"></i> {{ __('View All') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Invoice') }}</th>
                                        <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Customer') }}</th>
                                        <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Amount') }}</th>
                                        <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentInvoice as $invoice)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 12px 8px; font-size: 13px; color: #1f2937; border: none;">{{ \Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}</td>
                                        <td style="padding: 12px 8px; font-size: 13px; color: #1f2937; border: none;">{{ !empty($invoice->customer) ? $invoice->customer->name : '' }}</td>
                                        <td style="padding: 12px 8px; font-size: 13px; color: #1f2937; font-weight: 600; border: none;">{{ \Auth::user()->priceFormat($invoice->getTotal()) }}</td>
                                        <td style="padding: 12px 8px; border: none;">
                                            @if($invoice->status == 0)
                                                <span class="badge badge-primary">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 1)
                                                <span class="badge badge-warning">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 2)
                                                <span class="badge badge-danger">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 3)
                                                <span class="badge badge-info">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 4)
                                                <span class="badge badge-success">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center" style="padding: 4rem 2rem; color: #9ca3af;">
                    <i class="ti ti-file-invoice" style="font-size: 4rem; margin-bottom: 1.5rem; color: #d1d5db;"></i>
                    <h6 style="font-size: 18px; margin-bottom: 0.5rem; color: #6b7280;">{{ __('No Invoices Yet') }}</h6>
                    <p style="margin: 0; font-size: 14px;">{{ __('Create your first invoice to get started') }}</p>
                </div>
            @endif
        </div>

        <div id="bills" class="zameen-tab-pane">
            <div class="card" style="border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 2rem;">
                <div class="card-header" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); color: #1f2937; border-radius: 12px 12px 0 0; padding: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 style="margin: 0; font-weight: 600; font-size: 18px;">{{ __('Bills') }}</h5>
                            <p style="margin: 0; font-size: 14px; color: #6b7280;">{{ __('See all statistics') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group" style="background: #f3f4f6; border-radius: 8px; padding: 2px;">
                                <button type="button" class="btn btn-sm bill-period-btn active" data-period="weekly" style="background: #39b549; color: white; border: none; border-radius: 6px; padding: 6px 16px; font-size: 12px;">{{ __('Weekly') }}</button>
                                <button type="button" class="btn btn-sm bill-period-btn" data-period="monthly" style="background: transparent; color: #6b7280; border: none; border-radius: 6px; padding: 6px 16px; font-size: 12px;">{{ __('Monthly') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding: 2rem;">

                    <div class="row">

                        <div class="col-md-4">
                            <div class="card" style="border-radius: 10px; border: 1px solid #e5e7eb; box-shadow: 0 2px 6px rgba(0,0,0,0.04); background: #fff; height: 100%;">
                                <div class="card-body" style="padding: 1.5rem; text-align: center;">
                                    <div style="margin-bottom: 0.5rem;">
                                        <span style="font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('TOTAL') }}</span>
                                    </div>
                                    <div style="margin-bottom: 0.75rem;">
                                        <span style="font-size: 14px; color: #1f2937; font-weight: 500;">{{ __('Bills generated') }}</span>
                                    </div>
                                    <div>
                                        <span class="bill-total-amount" style="font-size: 18px; font-weight: 700; color: #1f2937;">{{ \Auth::user()->priceFormat($monthlyBill['billTotal']) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card" style="border-radius: 10px; border: 1px solid #e5e7eb; box-shadow: 0 2px 6px rgba(0,0,0,0.04); background: #fff; height: 100%;">
                                <div class="card-body" style="padding: 1.5rem; text-align: center;">
                                    <div style="margin-bottom: 0.5rem;">
                                        <span style="font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('TOTAL') }}</span>
                                    </div>
                                    <div style="margin-bottom: 0.75rem;">
                                        <span style="font-size: 14px; color: #1f2937; font-weight: 500;">{{ __('Paid') }}</span>
                                    </div>
                                    <div>
                                        <span class="bill-paid-amount" style="font-size: 18px; font-weight: 700; color: #39b549;">{{ \Auth::user()->priceFormat($monthlyBill['billPaid']) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card" style="border-radius: 10px; border: 1px solid #e5e7eb; box-shadow: 0 2px 6px rgba(0,0,0,0.04); background: #fff; height: 100%;">
                                <div class="card-body" style="padding: 1.5rem; text-align: center;">
                                    <div style="margin-bottom: 0.5rem;">
                                        <span style="font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('TOTAL') }}</span>
                                    </div>
                                    <div style="margin-bottom: 0.75rem;">
                                        <span style="font-size: 14px; color: #1f2937; font-weight: 500;">{{ __('Due') }}</span>
                                    </div>
                                    <div>
                                        <span class="bill-due-amount" style="font-size: 18px; font-weight: 700; color: #ef4444;">{{ \Auth::user()->priceFormat($monthlyBill['billDue']) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($recentBill) > 0)
                <div class="card" style="border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    <div class="card-header" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); color: #1f2937; border-radius: 12px 12px 0 0; padding: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 style="margin: 0; font-weight: 600; font-size: 18px;">{{ __('Recent Bills') }}</h5>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="{{ route('bill.index') }}" class="btn btn-primary btn-sm" style="background: var(--zameen-primary); border: none; border-radius: 8px;">
                                    <i class="ti ti-eye"></i> {{ __('View All') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Bill') }}</th>
                                        <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Vendor') }}</th>
                                        <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Amount') }}</th>
                                        <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBill as $bill)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 12px 8px; font-size: 13px; color: #1f2937; border: none;">{{ \Auth::user()->billNumberFormat($bill->bill_id) }}</td>
                                        <td style="padding: 12px 8px; font-size: 13px; color: #1f2937; border: none;">{{ !empty($bill->vender) ? $bill->vender->name : '' }}</td>
                                        <td style="padding: 12px 8px; font-size: 13px; color: #1f2937; font-weight: 600; border: none;">{{ \Auth::user()->priceFormat($bill->getTotal()) }}</td>
                                        <td style="padding: 12px 8px; border: none;">
                                            @if($bill->status == 0)
                                                <span class="badge badge-primary">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                            @elseif($bill->status == 1)
                                                <span class="badge badge-warning">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                            @elseif($bill->status == 2)
                                                <span class="badge badge-danger">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                            @elseif($bill->status == 3)
                                                <span class="badge badge-info">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                            @elseif($bill->status == 4)
                                                <span class="badge badge-success">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center" style="padding: 4rem 2rem; color: #9ca3af;">
                    <i class="ti ti-receipt" style="font-size: 4rem; margin-bottom: 1.5rem; color: #d1d5db;"></i>
                    <h6 style="font-size: 18px; margin-bottom: 0.5rem; color: #6b7280;">{{ __('No Bills Yet') }}</h6>
                    <p style="margin: 0; font-size: 14px;">{{ __('Record your first bill to track expenses') }}</p>
                </div>
            @endif
        </div>

        <div id="transactions" class="zameen-tab-pane">

            <div class="row" style="margin-bottom: 2rem;">
                <div class="col-md-6" style="margin-bottom: 1.5rem;">

                    <div class="card" style="border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.06); height: 100%;">
                        <div class="card-header" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); color: #1f2937; border-radius: 12px 12px 0 0; padding: 1.25rem; border-bottom: 1px solid #e5e7eb;">
                            <h5 style="margin: 0; font-weight: 600; font-size: 16px;">{{ __('Recent Invoices') }}</h5>
                        </div>
                        <div class="card-body" style="padding: 1.5rem; flex: 1;">
                            @if(count($recentInvoice) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Invoice') }}</th>
                                                <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Customer') }}</th>
                                                <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Amount') }}</th>
                                                <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentInvoice as $invoice)
                                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                                <td style="padding: 12px 8px; font-size: 13px; color: #1f2937; border: none;">{{ \Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}</td>
                                                <td style="padding: 12px 8px; font-size: 13px; color: #1f2937; border: none;">{{ !empty($invoice->customer) ? $invoice->customer->name : '' }}</td>
                                                <td style="padding: 12px 8px; font-size: 13px; color: #1f2937; font-weight: 600; border: none;">{{ \Auth::user()->priceFormat($invoice->getTotal()) }}</td>
                                                <td style="padding: 12px 8px; border: none;">
                                                    @if($invoice->status == 0)
                                                        <span class="badge badge-primary">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                    @elseif($invoice->status == 1)
                                                        <span class="badge badge-warning">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                    @elseif($invoice->status == 2)
                                                        <span class="badge badge-danger">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                    @elseif($invoice->status == 3)
                                                        <span class="badge badge-info">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                    @elseif($invoice->status == 4)
                                                        <span class="badge badge-success">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center" style="padding: 2rem; color: #9ca3af;">
                                    <i class="ti ti-file-invoice" style="font-size: 2rem; margin-bottom: 1rem; color: #d1d5db;"></i>
                                    <p style="margin: 0; font-size: 14px;">{{ __('No recent invoices') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6" style="margin-bottom: 1.5rem;">

                    <div class="card" style="border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.06); height: 100%;">
                        <div class="card-header" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); color: #1f2937; border-radius: 12px 12px 0 0; padding: 1.25rem; border-bottom: 1px solid #e5e7eb;">
                            <h5 style="margin: 0; font-weight: 600; font-size: 16px;">{{ __('Recent Bills') }}</h5>
                        </div>
                        <div class="card-body" style="padding: 1.5rem; flex: 1;">
                            @if(count($recentBill) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Bill') }}</th>
                                                <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Vendor') }}</th>
                                                <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Amount') }}</th>
                                                <th style="padding: 12px 8px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border: none;">{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentBill as $bill)
                                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                                <td style="padding: 12px 8px; font-size: 13px; color: #1f2937; border: none;">{{ \Auth::user()->billNumberFormat($bill->bill_id) }}</td>
                                                <td style="padding: 12px 8px; font-size: 13px; color: #1f2937; border: none;">{{ !empty($bill->vender) ? $bill->vender->name : '' }}</td>
                                                <td style="padding: 12px 8px; font-size: 13px; color: #1f2937; font-weight: 600; border: none;">{{ \Auth::user()->priceFormat($bill->getTotal()) }}</td>
                                                <td style="padding: 12px 8px; border: none;">
                                                    @if($bill->status == 0)
                                                        <span class="badge badge-primary">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                                    @elseif($bill->status == 1)
                                                        <span class="badge badge-warning">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                                    @elseif($bill->status == 2)
                                                        <span class="badge badge-danger">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                                    @elseif($bill->status == 3)
                                                        <span class="badge badge-info">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                                    @elseif($bill->status == 4)
                                                        <span class="badge badge-success">{{ __(\App\Models\Bill::$statues[$bill->status]) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center" style="padding: 2rem; color: #9ca3af;">
                                    <i class="ti ti-receipt" style="font-size: 2rem; margin-bottom: 1rem; color: #d1d5db;"></i>
                                    <p style="margin: 0; font-size: 14px;">{{ __('No recent bills') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6" style="margin-bottom: 1.5rem;">

                    <div class="card" style="border-radius: 12px; border: 1px solid #e5f4f1; box-shadow: 0 2px 8px rgba(0,185,141,0.06); height: 100%;">
                        <div class="card-header" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); color: #065f46; border-radius: 12px 12px 0 0; padding: 1.25rem; border-bottom: 1px solid #d1fae5;">
                            <h5 style="margin: 0; font-weight: 500; font-size: 16px;">{{ __('Latest Income') }}</h5>
                        </div>
                        <div class="card-body" style="padding: 1.5rem; flex: 1;">
                            @if(count($latestIncome) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead style="background: linear-gradient(180deg,#f9fdfb 0%, #ffffff 100%);">
                                            <tr>
                                                <th style="padding: 15px 12px; font-size: 12px; font-weight: 500; color: #374151; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Date') }}</th>
                                                <th style="padding: 15px 12px; font-size: 12px; font-weight: 500; color: #374151; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Category') }}</th>
                                                <th style="padding: 15px 12px; font-size: 12px; font-weight: 500; color: #374151; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($latestIncome as $income)
                                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                                <td style="padding: 15px 12px; font-size: 13px; color: #6b7280;">
                                                    {{ \Carbon\Carbon::parse($income->date)->format('M d, Y') }}
                                                </td>
                                                <td style="padding: 15px 12px; font-size: 13px; color: #374151;">
                                                    {{ !empty($income->category) ? $income->category->name : '-' }}
                                                </td>
                                                <td style="padding: 15px 12px; font-size: 13px; color: #374151;">
                                                    <span class="badge" style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 500;">
                                                        +{{ \Auth::user()->priceFormat($income->amount) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center" style="padding: 3rem 2rem; color: #9ca3af;">
                                    <i class="ti ti-trending-up" style="font-size: 2.5rem; margin-bottom: 1.5rem; color: #d1fae5;"></i>
                                    <p style="margin: 0; font-size: 14px;">{{ __('No recent income transactions') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6" style="margin-bottom: 1.5rem;">

                    <div class="card" style="border-radius: 12px; border: 1px solid #fef2f2; box-shadow: 0 2px 8px rgba(239,68,68,0.06); height: 100%;">
                        <div class="card-header" style="background: linear-gradient(135deg, #fef9f9 0%, #fef2f2 100%); color: #991b1b; border-radius: 12px 12px 0 0; padding: 1.25rem; border-bottom: 1px solid #fecaca;">
                            <h5 style="margin: 0; font-weight: 500; font-size: 16px;">{{ __('Latest Expense') }}</h5>
                        </div>
                        <div class="card-body" style="padding: 1.5rem; flex: 1;">
                            @if(count($latestExpense) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead style="background: linear-gradient(180deg,#fef9f9 0%, #ffffff 100%);">
                                            <tr>
                                                <th style="padding: 15px 12px; font-size: 12px; font-weight: 500; color: #374151; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Date') }}</th>
                                                <th style="padding: 15px 12px; font-size: 12px; font-weight: 500; color: #374151; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Category') }}</th>
                                                <th style="padding: 15px 12px; font-size: 12px; font-weight: 500; color: #374151; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($latestExpense as $expense)
                                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                                <td style="padding: 15px 12px; font-size: 13px; color: #6b7280;">
                                                    {{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}
                                                </td>
                                                <td style="padding: 15px 12px; font-size: 13px; color: #374151;">
                                                    {{ !empty($expense->category) ? $expense->category->name : '-' }}
                                                </td>
                                                <td style="padding: 15px 12px; font-size: 13px; color: #374151;">
                                                    <span class="badge" style="background: #fecaca; color: #991b1b; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 500;">
                                                        -{{ \Auth::user()->priceFormat($expense->amount) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center" style="padding: 3rem 2rem; color: #9ca3af;">
                                    <i class="ti ti-trending-down" style="font-size: 2.5rem; margin-bottom: 1.5rem; color: #fecaca;"></i>
                                    <p style="margin: 0; font-size: 14px;">{{ __('No recent expense transactions') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="settings" class="zameen-tab-pane">
            <div class="row g-4">
                <div class="col-md-12">
                    <h3 style="margin-bottom: 2.5rem; color: var(--zameen-text-dark); font-weight: 600;">{{ __('Quick Actions') }}</h3>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="zameen-stat-icon" style="margin: 0 auto 1rem; width: 60px; height: 60px;">
                                <i class="ti ti-plus" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5>{{ __('Create Invoice') }}</h5>
                            <a href="{{ route('invoice.create', 0) }}" class="btn btn-primary btn-sm">{{ __('Create Now') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="zameen-stat-icon" style="margin: 0 auto 1rem; width: 60px; height: 60px;">
                                <i class="ti ti-receipt" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5>{{ __('Create Bill') }}</h5>
                            <a href="{{ route('bill.create', 0) }}" class="btn btn-primary btn-sm">{{ __('Create Now') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="zameen-stat-icon" style="margin: 0 auto 1rem; width: 60px; height: 60px;">
                                <i class="ti ti-users" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5>{{ __('Add Customer') }}</h5>
                            <a href="{{ route('customer.index') }}" class="btn btn-primary btn-sm">{{ __('Go to Customers') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="zameen-stat-icon" style="margin: 0 auto 1rem; width: 60px; height: 60px;">
                                <i class="ti ti-building-store" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5>{{ __('Add Vendor') }}</h5>
                            <a href="{{ route('vender.index') }}" class="btn btn-primary btn-sm">{{ __('Go to Vendors') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabLinks = document.querySelectorAll('.zameen-tab-link');
    const tabPanes = document.querySelectorAll('.zameen-tab-pane');

    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const targetTab = this.getAttribute('data-tab');

            tabLinks.forEach(l => l.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));

            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });

    initCharts();
});

function initCharts() {
    const incExpBarCtx = document.getElementById('incomeExpenseChart');
    if (incExpBarCtx) {
        const currencySymbol = '{{ \Auth::user()->currencySymbol() ?? "$" }}';
        window.dashboardCharts = window.dashboardCharts || {};
        window.dashboardCharts['incomeExpenseChart2'] = new Chart(incExpBarCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($incExpBarChartData['month']) !!},
                datasets: [
                    {
                        label: 'Income',
                        data: {!! json_encode($incExpBarChartData['income']) !!},
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                        maxBarThickness: 40,
                    },
                    {
                        label: 'Expense',
                        data: {!! json_encode($incExpBarChartData['expense']) !!},
                        backgroundColor: '#ef4444',
                        borderRadius: 6,
                        maxBarThickness: 40,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                weight: 600
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return currencySymbol + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return currencySymbol + (value / 1000).toFixed(0) + 'K';
                                }
                                return currencySymbol + value;
                            },
                            color: '#6b7280',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: '#f3f4f6',
                            drawTicks: false,
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 12,
                                weight: 500
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    const cashflowCtx = document.getElementById('cashflowChart');
    if (cashflowCtx) {
        const currencySymbol = '{{ \Auth::user()->currencySymbol() ?? "$" }}';

        const cashflowData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            income: [{{ \Auth::user()->incomeCurrentMonth() }}, {{ \Auth::user()->incomeCurrentMonth() * 0.9 }}, {{ \Auth::user()->incomeCurrentMonth() * 1.1 }}, {{ \Auth::user()->incomeCurrentMonth() * 1.2 }}, {{ \Auth::user()->incomeCurrentMonth() * 0.8 }}, {{ \Auth::user()->incomeCurrentMonth() * 1.3 }}, {{ \Auth::user()->incomeCurrentMonth() * 1.1 }}, {{ \Auth::user()->incomeCurrentMonth() }}, {{ \Auth::user()->incomeCurrentMonth() * 1.4 }}, {{ \Auth::user()->incomeCurrentMonth() * 1.2 }}, {{ \Auth::user()->incomeCurrentMonth() * 0.9 }}, {{ \Auth::user()->incomeCurrentMonth() * 1.5 }}],
            expense: [{{ \Auth::user()->expenseCurrentMonth() }}, {{ \Auth::user()->expenseCurrentMonth() * 1.1 }}, {{ \Auth::user()->expenseCurrentMonth() * 0.9 }}, {{ \Auth::user()->expenseCurrentMonth() * 1.3 }}, {{ \Auth::user()->expenseCurrentMonth() * 0.7 }}, {{ \Auth::user()->expenseCurrentMonth() * 1.1 }}, {{ \Auth::user()->expenseCurrentMonth() * 1.2 }}, {{ \Auth::user()->expenseCurrentMonth() }}, {{ \Auth::user()->expenseCurrentMonth() * 1.1 }}, {{ \Auth::user()->expenseCurrentMonth() * 1.4 }}, {{ \Auth::user()->expenseCurrentMonth() * 0.8 }}, {{ \Auth::user()->expenseCurrentMonth() * 1.2 }}]
        };

        const netCashflow = cashflowData.income.map((income, index) => income - cashflowData.expense[index]);

        window.dashboardCharts = window.dashboardCharts || {};
        window.dashboardCharts['cashflowChart2'] = new Chart(cashflowCtx, {
            type: 'line',
            data: {
                labels: cashflowData.labels,
                datasets: [
                    {
                        label: 'Cash Inflow',
                        data: cashflowData.income,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(0, 185, 141, 0.1)',
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                    },
                    {
                        label: 'Cash Outflow',
                        data: cashflowData.expense,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                    },
                    {
                        label: 'Net Cashflow',
                        data: netCashflow,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#8b5cf6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        borderWidth: 3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                weight: 600
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return currencySymbol + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return currencySymbol + (value / 1000).toFixed(0) + 'K';
                                }
                                return currencySymbol + value;
                            },
                            color: '#6b7280',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: '#f3f4f6',
                            drawTicks: false,
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 12,
                                weight: 500
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const currencySymbol = '{{ \Auth::user()->currencySymbol() ?? "$" }}';
        window.dashboardCharts = window.dashboardCharts || {};
        window.dashboardCharts['revenueChart'] = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($incExpBarChartData['month']) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($incExpBarChartData['income']) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(0, 185, 141, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return currencySymbol + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return currencySymbol + (value / 1000).toFixed(0) + 'K';
                                }
                                return currencySymbol + value;
                            },
                            color: '#6b7280'
                        },
                        grid: {
                            color: '#f0f0f0'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6b7280'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    const expenseCtx = document.getElementById('expenseChart');
    if (expenseCtx) {
        const currencySymbol = '{{ \Auth::user()->currencySymbol() ?? "$" }}';
        window.dashboardCharts = window.dashboardCharts || {};
        window.dashboardCharts['expenseChart2'] = new Chart(expenseCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($incExpBarChartData['month']) !!},
                datasets: [{
                    label: 'Expenses',
                    data: {!! json_encode($incExpBarChartData['expense']) !!},
                    backgroundColor: '#ef4444',
                    borderRadius: 6,
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return currencySymbol + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return currencySymbol + (value / 1000).toFixed(0) + 'K';
                                }
                                return currencySymbol + value;
                            },
                            color: '#6b7280'
                        },
                        grid: {
                            color: '#f0f0f0'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6b7280'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const invoiceFilterBtns = document.querySelectorAll('.invoice-period-btn');
    invoiceFilterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            invoiceFilterBtns.forEach(b => {
                b.classList.remove('active');
                b.style.background = 'transparent';
                b.style.color = '#6b7280';
            });

            this.classList.add('active');
            this.style.background = '#ef4444';
            this.style.color = 'white';

            const period = this.getAttribute('data-period');
            console.log('Invoice period changed to:', period);
        });
    });

    const billFilterBtns = document.querySelectorAll('.bill-period-btn');
    billFilterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            billFilterBtns.forEach(b => {
                b.classList.remove('active');
                b.style.background = 'transparent';
                b.style.color = '#6b7280';
            });

            this.classList.add('active');
            this.style.background = '#ef4444';
            this.style.color = 'white';

            const period = this.getAttribute('data-period');
            console.log('Bill period changed to:', period);
        });
    });
});

// Tab switching functionality with chart resize support
document.addEventListener('DOMContentLoaded', function() {
    const tabLinks = document.querySelectorAll('.zameen-tab-link');
    const tabPanes = document.querySelectorAll('.zameen-tab-pane');

    // Store all chart instances globally for resizing
    window.dashboardCharts = window.dashboardCharts || {};

    // Function to resize all charts in a container
    function resizeChartsInContainer(container) {
        const charts = container.querySelectorAll('canvas');
        charts.forEach(canvas => {
            const chartId = canvas.id;
            if (window.dashboardCharts[chartId]) {
                try {
                    // Force chart to update its size
                    window.dashboardCharts[chartId].resize();
                    // Update with specific dimensions
                    window.dashboardCharts[chartId].update('none');
                } catch (error) {
                    console.log('Chart resize failed for:', chartId, error);
                }
            }
        });
    }

    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const targetTab = this.getAttribute('data-tab');

            // Remove active class from all tabs and panes
            tabLinks.forEach(l => l.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));

            // Add active class to clicked tab and corresponding pane
            this.classList.add('active');
            const targetPane = document.getElementById(targetTab);
            targetPane.classList.add('active');

            // Resize charts in the newly visible tab after a small delay
            setTimeout(() => {
                resizeChartsInContainer(targetPane);
            }, 50);

            // Additional resize after transition completes
            setTimeout(() => {
                resizeChartsInContainer(targetPane);
            }, 300);
        });
    });

    // Also handle window resize
    window.addEventListener('resize', function() {
        Object.values(window.dashboardCharts).forEach(chart => {
            if (chart && chart.resize) {
                chart.resize();
            }
        });
    });

    // Handle Invoice period switching
    document.querySelectorAll('.invoice-period-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all invoice period buttons
            document.querySelectorAll('.invoice-period-btn').forEach(btn => {
                btn.style.background = 'transparent';
                btn.style.color = '#6b7280';
                btn.classList.remove('active');
            });

            // Add active class to clicked button
            this.style.background = '#39b549';
            this.style.color = 'white';
            this.classList.add('active');

            // Update the data based on period (you can implement AJAX calls here)
            const period = this.getAttribute('data-period');
            console.log('Invoice period changed to:', period);

            // Example: Update amounts (replace with actual AJAX call)
            if (period === 'weekly') {
                // Load weekly data
                // document.querySelector('.invoice-total-amount').textContent = 'Weekly Total';
                // document.querySelector('.invoice-paid-amount').textContent = 'Weekly Paid';
                // document.querySelector('.invoice-due-amount').textContent = 'Weekly Due';
            } else {
                // Load monthly data
                // document.querySelector('.invoice-total-amount').textContent = 'Monthly Total';
                // document.querySelector('.invoice-paid-amount').textContent = 'Monthly Paid';
                // document.querySelector('.invoice-due-amount').textContent = 'Monthly Due';
            }
        });
    });

    // Handle Bills period switching
    document.querySelectorAll('.bill-period-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all bill period buttons
            document.querySelectorAll('.bill-period-btn').forEach(btn => {
                btn.style.background = 'transparent';
                btn.style.color = '#6b7280';
                btn.classList.remove('active');
            });

            // Add active class to clicked button
            this.style.background = '#39b549';
            this.style.color = 'white';
            this.classList.add('active');

            // Update the data based on period (you can implement AJAX calls here)
            const period = this.getAttribute('data-period');
            console.log('Bill period changed to:', period);

            // Example: Update amounts (replace with actual AJAX call)
            if (period === 'weekly') {
                // Load weekly data
                // document.querySelector('.bill-total-amount').textContent = 'Weekly Total';
                // document.querySelector('.bill-paid-amount').textContent = 'Weekly Paid';
                // document.querySelector('.bill-due-amount').textContent = 'Weekly Due';
            } else {
                // Load monthly data
                // document.querySelector('.bill-total-amount').textContent = 'Monthly Total';
                // document.querySelector('.bill-paid-amount').textContent = 'Monthly Paid';
                // document.querySelector('.bill-due-amount').textContent = 'Monthly Due';
            }
        });
    });
});
</script>
@endpush
