@extends('layouts.admin')
@section('page-title')
{{ __('Dashboard') }}
@endsection
<style>
   .dashboard-module-cards {
   position: relative;
   transition: opacity 0.3s;
   }
   .dashboard-module-cards-hidden {
   opacity: 0.3;
   pointer-events: none;
   }
   .dashboard-module-cards.customize-mode {
   opacity: 0.5;
   pointer-events: auto;
   display: block !important;
   }
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
   // ========== INCOME vs EXPENSE LINE CHART ==========
   const cashflowCtx = document.getElementById('cashflowChart').getContext('2d');
   const currencySymbol = '{{ \Auth::user()->currencySymbol() }}';
   new Chart(cashflowCtx, {
       type: 'line',
       data: {
           labels: {!! json_encode($incExpLineChartData['day']) !!},
           datasets: [{
                   label: 'Income',
                   data: {!! json_encode($incExpLineChartData['income']) !!},
                   borderColor: '#f59e0b',
                   backgroundColor: 'rgba(245, 158, 11, 0.1)',
                   tension: 0.4,
                   fill: false,
                   pointRadius: 0,
                   borderWidth: 2,
               },
               {
                   label: 'Expense',
                   data: {!! json_encode($incExpLineChartData['expense']) !!},
                   borderColor: '#16a34a',
                   backgroundColor: 'rgba(22, 163, 74, 0.3)',
                   tension: 0.4,
                   fill: true,
                   pointRadius: 0,
                   borderWidth: 2,
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

   // ========== INCOME vs EXPENSE BAR CHART ==========
const incExpBarCtx = document.getElementById('incomeExpenseChart').getContext('2d');
new Chart(incExpBarCtx, {
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


   // ========== EXPENSE CATEGORY DONUT CHART ==========
   const expenseDonutCtx = document.getElementById('expenseChart').getContext('2d');
   new Chart(expenseDonutCtx, {
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

   // ========== INCOME CATEGORY DONUT CHART ==========
   const incomeDonutCtx = document.getElementById('incomeChart').getContext('2d');
   new Chart(incomeDonutCtx, {
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

   // ========== STORAGE USAGE RADIAL CHART ==========
   // @if (!empty($plan))
   //     const storageUsageCtx = document.getElementById('device-chart').getContext('2d');
   //     new Chart(storageUsageCtx, {
   //         type: 'doughnut',
   //         data: {
   //             labels: ['Used', 'Remaining'],
   //             datasets: [{
   //                 data: [{{ $storage_limit }}, {{ 100 - $storage_limit }}],
   //                 backgroundColor: ['#6FD943', '#e7e7e7'],
   //                 borderWidth: 0
   //             }]
   //         },
   //         options: {
   //             cutout: '80%',
   //             plugins: {
   //                 legend: {
   //                     display: false
   //                 }
   //             }
   //         }
   //     });
   // @endif
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
<div class="flex gap-2 mb-2 justify-end ">
   <button id="customize-dashboard" class="flex gap-1 items-center btn bg-[#007C38] text-white px-4 py-1.5 rounded-md  hover:bg-green-700">
      <i class="ti ti-adjustments"></i>Customize</button>
   <button id="save-dashboard-customization" class="flex gap-1 items-center btn bg-[#007C38] text-white px-4 py-1.5 rounded-md  hover:bg-green-700" style="display:none;">Save</button>
   <button id="cancel-dashboard-customization" class="flex items-center gap-1 border border-[#007C38] text-[#007C38] px-3 py-1.5 rounded-md  hover:bg-green-50" style="display:none;">Cancel</button>
</div>


<div class="top-sction gap-10">
   <div class="row">
      <div class="col-md-3">
         <div
         class="div-box-height w-full dashboard-module-cards {{ $showBusinessOverview ? '' : 'hidden-module' }} hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300"
         data-card="business_overview"
         style="
           position:relative;
           border:1px solid #E8EEF5;
           border-radius:14px;
           background:linear-gradient(180deg,#FFFFFF 0%,#FEFFF9 45%,#F7FBF9 100%);
           box-shadow:0 10px 24px rgba(16,24,40,.06), 0 1px 0 rgba(16,24,40,.04);
           overflow:hidden;
         "
       >
            <span class="customize-toggle" style="display:none;">
            <i class="fa {{$showBusinessOverview ? 'fa-times text-danger' : 'fa-check text-success'}}"></i>
            </span>

      <div style="height:4px; background:linear-gradient(90deg,#FFE58A 0%, #FACC15 50%, #FDE68A 100%);"></div>

      <div style="padding:14px 16px 10px 16px; border-bottom:1px solid #E8EEF5; background:linear-gradient(180deg,#FFFFFF 0%,#FFFDF5 100%);">
         <h2 style="font-size:15px; line-height:22px; color:#0F172A; font-weight:800; letter-spacing:.2px; margin:0;">
         Business Overview
         </h2>
         <p style="font-size:11px; color:#6B7280; line-height:18px; font-weight:500; margin:4px 0 0;">
         Stay informed and track your growth
         </p>
      </div>                <div style="padding:18px 16px; display:flex; flex-direction:column; gap:14px;">
         <!-- Customers -->
         <div class="hover:bg-yellow-50/50 transition-colors"
              style="display:flex; align-items:center; justify-content:space-between; padding:6px 8px; border-radius:10px;">
           <div style="display:flex; align-items:center; gap:10px;">
             <div
               style="padding:10px; border-radius:10px; background:linear-gradient(135deg,#FFF9E6 0%,#FFF1BF 100%); border:1px solid #FFE8A3; box-shadow:inset 0 1px 0 rgba(255,255,255,.8);">
               <img src="{{ asset('web-assets/dashboard/icons/staff.svg') }}" alt="Customer" style="width:20px; height:20px;">
             </div>
             <div>
               <span style="color:#6B7280; font-size:12px; display:block; margin:0;">Total</span>
               <a href="{{ route('customer.index') }}" style="font-weight:700; color:#111827; text-decoration:none;">{{ __('Customers') }}</a>
             </div>
           </div>
           <span style="font-size:22px; font-weight:800; line-height:24px; color:#111827; font-variant-numeric: tabular-nums;">
             {{ \Auth::user()->countCustomers() }}
           </span>
         </div>

         <div style="height:1px; background:linear-gradient(90deg,rgba(0,0,0,0) 0%, #E8EEF5 50%, rgba(0,0,0,0) 100%);"></div>

         <!-- Vendors -->
         <div class="hover:bg-yellow-50/50 transition-colors"
              style="display:flex; align-items:center; justify-content:space-between; padding:6px 8px; border-radius:10px;">
           <div style="display:flex; align-items:center; gap:10px;">
             <div
               style="padding:10px; border-radius:10px; background:linear-gradient(135deg,#FFF9E6 0%,#FFF1BF 100%); border:1px solid #FFE8A3; box-shadow:inset 0 1px 0 rgba(255,255,255,.8);">
               <img src="{{ asset('web-assets/dashboard/icons/vendor.svg') }}" alt="Vendor" style="width:20px; height:20px;">
             </div>
             <div>
               <span style="color:#6B7280; font-size:12px; display:block; margin:0;">Total</span>
               <a href="{{ route('vender.index') }}" style="font-weight:700; color:#111827; text-decoration:none;">{{ __('Vendors') }}</a>
             </div>
           </div>
           <span style="font-size:22px; font-weight:800; line-height:24px; color:#111827; font-variant-numeric: tabular-nums;">
             {{ \Auth::user()->countVenders() }}
           </span>
         </div>

         <div style="height:1px; background:linear-gradient(90deg,rgba(0,0,0,0) 0%, #E8EEF5 50%, rgba(0,0,0,0) 100%);"></div>

         <!-- Invoices -->
         <div class="hover:bg-yellow-50/50 transition-colors"
              style="display:flex; align-items:center; justify-content:space-between; padding:6px 8px; border-radius:10px;">
           <div style="display:flex; align-items:center; gap:10px;">
             <div
               style="padding:10px; border-radius:10px; background:linear-gradient(135deg,#FFF9E6 0%,#FFF1BF 100%); border:1px solid #FFE8A3; box-shadow:inset 0 1px 0 rgba(255,255,255,.8);">
               <img src="{{ asset('web-assets/dashboard/icons/invoices.svg') }}" alt="Invoices" style="width:20px; height:20px;">
             </div>
             <div>
               <span style="color:#6B7280; font-size:12px; display:block; margin:0;">Total</span>
               <a href="{{ route('invoice.index') }}" style="font-weight:700; color:#111827; text-decoration:none;">{{ __('Invoices') }}</a>
             </div>
           </div>
           <span style="font-size:22px; font-weight:800; line-height:24px; color:#111827; font-variant-numeric: tabular-nums;">
             {{ \Auth::user()->countInvoices() }}
           </span>
         </div>

         <div style="height:1px; background:linear-gradient(90deg,rgba(0,0,0,0) 0%, #E8EEF5 50%, rgba(0,0,0,0) 100%);"></div>

         <!-- Bills -->
         <div class="hover:bg-yellow-50/50 transition-colors"
              style="display:flex; align-items:center; justify-content:space-between; padding:6px 8px; border-radius:10px;">
           <div style="display:flex; align-items:center; gap:10px;">
             <div
               style="padding:10px; border-radius:10px; background:linear-gradient(135deg,#FFF9E6 0%,#FFF1BF 100%); border:1px solid #FFE8A3; box-shadow:inset 0 1px 0 rgba(255,255,255,.8);">
               <img src="{{ asset('web-assets/dashboard/icons/bills.svg') }}" alt="Bills" style="width:20px; height:20px;">
             </div>
             <div>
               <span style="color:#6B7280; font-size:12px; display:block; margin:0;">Total</span>
               <a href="{{ route('bill.index') }}" style="font-weight:700; color:#111827; text-decoration:none;">{{ __('Bills') }}</a>
             </div>
           </div>
           <span style="font-size:22px; font-weight:800; line-height:24px; color:#111827; font-variant-numeric: tabular-nums;">
             {{ \Auth::user()->countBills() }}
           </span>
         </div>
       </div>

       <!-- Subtle bottom glow -->
       <div style="position:absolute; left:0; right:0; bottom:0; height:8px; background:linear-gradient(90deg, rgba(250,204,21,.0) 0%, rgba(250,204,21,.35) 50%, rgba(250,204,21,.0) 100%);"></div>
     </div>
   </div>


   <div class="col-md-6">
      <div
        class="div-box-height bg-white border rounded-[8px] hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300 w-full dashboard-module-cards {{ $showAccountBalance ? '' : 'hidden-module' }}"
        data-card="income_expense"
        style="
           position:relative;
           border:1px solid #E8EEF5;
           border-radius:14px;
           background:linear-gradient(180deg,#FFFFFF 0%,#FEFFF9 45%,#F7FBF9 100%);
           box-shadow:0 10px 24px rgba(16,24,40,.06), 0 1px 0 rgba(16,24,40,.04);
           overflow:hidden;
        "
      >
        <!-- blue accent strip -->
        <div style="height:4px; background:linear-gradient(90deg,#93C5FD 0%, #6d94f7 50%, #93C5FD 100%);"></div>

        <span class="customize-toggle" style="display:none">
          <i class="fa {{$showAccountBalance ? 'fa-times text-danger' : 'fa-check text-success'}}"></i>
        </span>

        <!-- header -->
        <div class="flex items-center justify-between px-4 py-3 pb-[7px]"
             style="border-bottom:1px solid #E3EAF5; background:linear-gradient(180deg,#FFFFFF 0%,#F3F7FF 100%);">
          <div>
            <h2 style="font-size:15px; line-height:24px; color:#0F172A; font-weight:800; letter-spacing:.2px; margin:0;">
              Income & Expense
            </h2>
            <p style="font-size:11px; color:#6B7280; line-height:18px; font-weight:500; margin:2px 0 0;">
              View all your income & earning
            </p>
          </div>

          <!-- filter -->
          <div x-data="{ open: false, selected: 'Last 8 Months' }" class="relative text-sm" style="position:relative;">
            <button @click="open = !open"
                    style="display:flex; align-items:center; gap:6px; border:1px solid #D7DFEA; padding:6px 10px; border-radius:10px; background:#fff; color:#0F172A;">
              <span x-text="selected"></span>
              <svg style="width:16px;height:16px;color:#6B7280" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <ul x-show="open" @click.outside="open = false"
                style="position:absolute; right:0; margin-top:6px; width:150px; background:#fff; border:1px solid #E5E7EB; border-radius:10px; box-shadow:0 14px 32px rgba(16,24,40,.14); z-index:10; overflow:hidden;">
              <li @click="selected = 'Last 8 Months'; open = false" style="padding:10px 12px; cursor:pointer;">Last 8 Months</li>
              <li @click="selected = 'Last Year'; open = false" style="padding:10px 12px; cursor:pointer;">Last Year</li>
            </ul>
          </div>
        </div>

        <!-- meta line -->
        <div style="display:flex; gap:24px; padding:8px 15px; line-height:22px; font-size:11px; font-weight:600;">
          <span style="color:#2563EB;">
            Income of <span style="color:#6B7280;">
              {{ \Carbon\Carbon::parse(\Carbon\Carbon::now()->toDateString())->format('Y-m-d') }}
            </span>
            <span style="font-weight:800; color:#0F172A;"> — {{ \Auth::user()->priceFormat(\Auth::user()->incomeCurrentMonth()) }}</span>
          </span>

          <span style="color:#2563EB;">
            Expense of <span style="color:#6B7280;">
              {{ \Carbon\Carbon::parse(\Carbon\Carbon::now()->toDateString())->format('Y-m-d') }}
            </span>
            <span style="font-weight:800; color:#0F172A;"> — {{ \Auth::user()->priceFormat(\Auth::user()->expenseCurrentMonth()) }}</span>
          </span>
        </div>

        <!-- chart -->
        <div class="chart-background" style="padding:12px 8px 16px;">
          <div style="border-radius:12px; background:linear-gradient(180deg,#F1F5FF 0%,#FFFFFF 100%); box-shadow:inset 0 1px 0 rgba(255,255,255,.9); padding:8px; border:1px solid #E3EAF5;">
            <canvas id="incomeExpenseChart" class="w-full h-48 px-[15px]" style="display:block; width:100%; height:192px;"></canvas>
          </div>
        </div>

        <!-- subtle bottom glow -->
        <div style="position:absolute; left:0; right:0; bottom:0; height:8px; background:linear-gradient(90deg, rgba(59,130,246,0) 0%, rgba(59,130,246,.35) 50%, rgba(59,130,246,0) 100%);"></div>
      </div>
    </div>




    <div class="col-md-3">
      <div
        x-data="{ tab: 'weekly' }"
        class="w-full bg-white div-box-height dashboard-module-cards {{ $showInvoices ? '' : 'hidden-module' }} hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300"
        data-card="invoices"
        style="
          position:relative;
           border-color:#E3EAF5;
          border-radius:14px;
          background:linear-gradient(180deg,#FFFFFF 0%,#FFF7F7 100%);
          box-shadow:0 10px 24px rgba(16,24,40,.06), 0 1px 0 rgba(16,24,40,.04);
          overflow:hidden;
        "
      >
        <!-- light red accent strip -->
        <div style="height:4px; background:linear-gradient(90deg,#FDE2E2 0%, #FECACA 50%, #FDE2E2 100%);"></div>

        <span class="customize-toggle"
              style="display:none">
          <i class="fa {{$showInvoices ? 'fa-times text-danger' : 'fa-check text-success'}}"></i>
        </span>

        <div class="px-4 py-3 pb-[7px]"
             style="border-bottom:1px solid #F1DADA; background:linear-gradient(180deg,#FFFFFF 0%,#FFF3F3 100%);">
          <h2 class="text-[15px] leading-[24px]" style="color:#0F172A; font-weight:800; letter-spacing:.2px; margin:0;">Invoices</h2>
          <p class="text-[11px] leading-[18px]" style="color:#6B7280; font-weight:500; margin:2px 0 0;">See all statistics</p>
        </div>

        <div class="pt-0 px-4 py-5">
          <!-- tabs -->
          <div class="flex mt-[17.5px] mb-2" style="border-radius:12px; overflow:hidden;">
            <button
              @click="tab = 'weekly'"
              :class="tab === 'weekly' ? 'shadow font-[700] text-rose-700' : 'text-rose-500'"
              style="width:50%; font-size:12px; padding:8px 16px; text-align:center; background:#FFFFFF; border:1px solid #F1DADA; border-right:none;">
              Weekly
            </button>
            <button
              @click="tab = 'monthly'"
              :class="tab === 'monthly' ? 'shadow font-[700] text-rose-700' : 'text-rose-500'"
              style="width:50%; font-size:12px; padding:8px 16px; text-align:center; background:#FFF1F2; border:1px solid #F1DADA;">
              Monthly
            </button>
          </div>

          <!-- weekly -->
          <div x-show="tab === 'weekly'" class="space-y-4 mt-[28px]">
            <div class="flex justify-between border-b pb-[10px]" style="border-color:#F3E3E3;">
              <div>
                <p class="text-xs" style="color:#6B7280; margin:0;">TOTAL</p>
                <p class="font-medium" style="color:#1F2937; margin:0;">Invoice Generated</p>
              </div>
              <p class="text-[20px] font-[700] leading-[24px]" style="color:#1F2937;">
                {{ \Auth::user()->priceFormat($weeklyInvoice['invoiceTotal']) }}
              </p>
            </div>
            <div class="flex justify-between border-b pb-[10px]" style="border-color:#F3E3E3;">
              <div>
                <p class="text-xs" style="color:#6B7280; margin:0;">TOTAL</p>
                <p class="font-medium" style="color:#1F2937; margin:0;">Paid</p>
              </div>
              <p class="text-[20px] font-[700] leading-[24px]" style="color:#1F2937;">
                {{ \Auth::user()->priceFormat($weeklyInvoice['invoicePaid']) }}
              </p>
            </div>
            <div class="flex justify-between">
              <div>
                <p class="text-xs" style="color:#6B7280; margin:0;">TOTAL</p>
                <p class="font-medium" style="color:#1F2937; margin:0;">Due</p>
              </div>
              <p class="text-[20px] font-[700] leading-[24px]" style="color:#1F2937;">
                {{ \Auth::user()->priceFormat($weeklyInvoice['invoiceDue']) }}
              </p>
            </div>
          </div>

          <!-- monthly -->
          <div x-show="tab === 'monthly'" x-cloak class="space-y-4 mt-[28px]">
            <div class="flex justify-between border-b pb-[10px]" style="border-color:#F3E3E3;">
              <div>
                <p class="text-xs" style="color:#6B7280; margin:0;">TOTAL</p>
                <p class="font-medium" style="color:#1F2937; margin:0;">Invoice Generated</p>
              </div>
              <p class="text-[20px] font-[700] leading-[24px]" style="color:#1F2937;">
                {{ \Auth::user()->priceFormat($monthlyInvoice['invoiceTotal']) }}
              </p>
            </div>
            <div class="flex justify-between border-b pb-[10px]" style="border-color:#F3E3E3;">
              <div>
                <p class="text-xs" style="color:#6B7280; margin:0;">TOTAL</p>
                <p class="font-medium" style="color:#1F2937; margin:0;">Paid</p>
              </div>
              <p class="text-[20px] font-[700] leading-[24px]" style="color:#1F2937;">
                {{ \Auth::user()->priceFormat($monthlyInvoice['invoicePaid']) }}
              </p>
            </div>
            <div class="flex justify-between">
              <div>
                <p class="text-xs" style="color:#6B7280; margin:0;">TOTAL</p>
                <p class="font-medium" style="color:#1F2937; margin:0;">Due</p>
              </div>
              <p class="text-[20px] font-[700] leading-[24px]" style="color:#1F2937;">
                {{ \Auth::user()->priceFormat($monthlyInvoice['invoiceDue']) }}
              </p>
            </div>
          </div>
        </div>

        <!-- subtle bottom glow -->
        <div style="position:absolute; left:0; right:0; bottom:0; height:8px; background:linear-gradient(90deg, rgba(244,63,94,0) 0%, rgba(244,63,94,.28) 50%, rgba(244,63,94,0) 100%);"></div>
      </div>
    </div>




   </div>


</div>



<div class="sec-sction mt-10 gap-10">
   <div class="row">


      <div class="col-md-9">
         <div
           class="w-full div-box-height bg-white border rounded-[8px] dashboard-module-cards {{ $showCashflow ? '' : 'hidden-module' }} hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300"
           data-card="cashflow"
           style="
             position:relative;
             border:1px solid #E8EEF5;
             border-radius:14px;
             background:linear-gradient(180deg,#FFFFFF 0%,#FFFEF5 100%);
             box-shadow:0 10px 24px rgba(16,24,40,.06), 0 1px 0 rgba(16,24,40,.04);
             transition:transform .25s ease, box-shadow .25s ease;
             overflow:hidden;
           "
         >
           <!-- yellow accent strip -->
           <div style="height:4px; background:linear-gradient(90deg,#FFE58A 0%, #FACC15 50%, #FDE68A 100%);"></div>

           <span class="customize-toggle" style="display:none;">
             <i class="fa {{$showCashflow ? 'fa-times text-danger' : 'fa-check text-success'}}"></i>
           </span>

           <div class="px-4 py-3 pb-[7px]"
                style="border-bottom:1px solid #F3E8A3; background:linear-gradient(180deg,#FFFFFF 0%,#FFFBEB 100%);">
             <h2 class="text-[15px] leading-[24px] font-[800]" style="color:#0F172A; letter-spacing:.2px; margin:0;">Cashflow</h2>
             <p class="text-[11px] leading-[18px] font-[500]" style="color:#6B7280; margin:2px 0 0;">View all your current cash flow</p>
           </div>

           <div class="flex justify-end text-sm mt-4 px-8" style="gap:16px;">
             <div class="flex items-center" style="gap:6px;">
               <span class="w-2 h-2 rounded-full" style="background:#F59E0B;"></span>
               <span class="text-[10px] leading-[20px] font-[600]" style="color:#6B7280;">INCOME</span>
             </div>
             <div class="flex items-center" style="gap:6px;">
               <span class="w-2 h-2 rounded-full" style="background:#10B981;"></span>
               <span class="text-[10px] leading-[20px] font-[600]" style="color:#6B7280;">EXPENSE</span>
             </div>
           </div>

           <div style="padding:12px 8px 16px;">
             <div style="border-radius:12px; background:linear-gradient(180deg,#FFF7E6 0%,#FFFFFF 100%); box-shadow:inset 0 1px 0 rgba(255,255,255,.9); padding:8px; border:1px solid #F3E8A3;">
               <canvas id="cashflowChart" class="w-full h-48 px-[15px] pb-8" style="display:block; width:100%; height:192px;"></canvas>
             </div>
           </div>

           <!-- subtle bottom glow -->
           <div style="position:absolute; left:0; right:0; bottom:0; height:8px; background:linear-gradient(90deg, rgba(250,204,21,0) 0%, rgba(250,204,21,.28) 50%, rgba(250,204,21,0) 100%);"></div>
         </div>
       </div>



       <div class="col-md-3">
         <div
           x-data="{ tab: 'weekly' }"
           class="w-full div-box-height bg-white border rounded-[8px] dashboard-module-cards {{ $showBills ? '' : 'hidden-module' }} hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300"
           data-card="bills"
           style="
             position:relative;
           border-color:#E3EAF5;
             border-radius:14px;
             background:linear-gradient(180deg,#FFFFFF 0%,#FFF7F7 100%);
             box-shadow:0 10px 24px rgba(16,24,40,.06), 0 1px 0 rgba(16,24,40,.04);
             overflow:hidden;
           "
         >
           <!-- light red accent strip -->
           <div style="height:4px; background:linear-gradient(90deg,#FDE2E2 0%, #FECACA 50%, #FDE2E2 100%);"></div>

           <span class="customize-toggle" style="display:none;">
             <i class="fa {{$showBills ? 'fa-times text-danger' : 'fa-check text-success'}}"></i>
           </span>

           <div class="px-4 py-3 pb-[7px]" style="border-bottom:1px solid #F1DADA; background:linear-gradient(180deg,#FFFFFF 0%,#FFF3F3 100%);">
             <h2 class="text-[15px] leading-[24px]" style="color:#0F172A; font-weight:800; letter-spacing:.2px; margin:0;">Bills</h2>
             <p class="text-[11px] leading-[18px]" style="color:#6B7280; font-weight:500; margin:2px 0 0;">See all statistics</p>
           </div>

           <div class="pt-0 px-4 py-5">
             <!-- tabs -->
             <div class="flex mt-[17.5px] mb-2" style="border-radius:12px; overflow:hidden;">
               <button
                 @click="tab = 'weekly'"
                 :class="tab === 'weekly' ? 'shadow font-[700] text-rose-700' : 'text-rose-500'"
                 style="width:50%; font-size:12px; padding:8px 16px; text-align:center; background:#FFFFFF; border:1px solid #F1DADA; border-right:none;">
                 Weekly
               </button>
               <button
                 @click="tab = 'monthly'"
                 :class="tab === 'monthly' ? 'shadow font-[700] text-rose-700' : 'text-rose-500'"
                 style="width:50%; font-size:12px; padding:8px 16px; text-align:center; background:#FFF1F2; border:1px solid #F1DADA;">
                 Monthly
               </button>
             </div>

             <!-- weekly -->
             <div x-show="tab === 'weekly'" class="space-y-4 mt-[28px]">
               <div class="flex justify-between border-b pb-[10px]" style="border-color:#F3E3E3;">
                 <div>
                   <p class="text-xs" style="color:#6B7280; margin:0;">TOTAL</p>
                   <p class="font-medium" style="color:#1F2937; margin:0;">Bills generated</p>
                 </div>
                 <p class="text-[20px] font-[700] leading-[24px]" style="color:#1F2937;">
                   {{ \Auth::user()->priceFormat($weeklyBill['billTotal']) }}
                 </p>
               </div>
               <div class="flex justify-between border-b pb-[10px]" style="border-color:#F3E3E3;">
                 <div>
                   <p class="text-xs" style="color:#6B7280; margin:0;">TOTAL</p>
                   <p class="font-medium" style="color:#1F2937; margin:0;">Paid</p>
                 </div>
                 <p class="text-[20px] font-[700] leading-[24px]" style="color:#1F2937;">
                   {{ \Auth::user()->priceFormat($weeklyBill['billPaid']) }}
                 </p>
               </div>
               <div class="flex justify-between">
                 <div>
                   <p class="text-xs" style="color:#6B7280; margin:0;">TOTAL</p>
                   <p class="font-medium" style="color:#1F2937; margin:0;">Due</p>
                 </div>
                 <p class="text-[20px] font-[700] leading-[24px]" style="color:#1F2937;">
                   {{ \Auth::user()->priceFormat($weeklyBill['billDue']) }}
                 </p>
               </div>
             </div>

             <!-- monthly -->
             <div x-show="tab === 'monthly'" x-cloak class="space-y-4 mt-[28px]">
               <div class="flex justify-between border-b pb-[10px]" style="border-color:#F3E3E3;">
                 <div>
                   <p class="text-xs" style="color:#6B7280; margin:0;">TOTAL</p>
                   <p class="font-medium" style="color:#1F2937; margin:0;">Bills generated</p>
                 </div>
                 <p class="text-[20px] font-[700] leading-[24px]" style="color:#1F2937;">
                   {{ \Auth::user()->priceFormat($monthlyBill['billTotal']) }}
                 </p>
               </div>
               <div class="flex justify-between border-b pb-[10px]" style="border-color:#F3E3E3;">
                 <div>
                   <p class="text-xs" style="color:#6B7280; margin:0;">TOTAL</p>
                   <p class="font-medium" style="color:#1F2937; margin:0;">Paid</p>
                 </div>
                 <p class="text-[20px] font-[700] leading-[24px]" style="color:#1F2937;">
                   {{ \Auth::user()->priceFormat($monthlyBill['billPaid']) }}
                 </p>
               </div>
               <div class="flex justify-between">
                 <div>
                   <p class="text-xs" style="color:#6B7280; margin:0;">TOTAL</p>
                   <p class="font-medium" style="color:#1F2937; margin:0;">Due</p>
                 </div>
                 <p class="text-[20px] font-[700] leading-[24px]" style="color:#1F2937;">
                   {{ \Auth::user()->priceFormat($monthlyBill['billDue']) }}
                 </p>
               </div>
             </div>
           </div>

           <!-- subtle bottom glow -->
           <div style="position:absolute; left:0; right:0; bottom:0; height:8px; background:linear-gradient(90deg, rgba(244,63,94,0) 0%, rgba(244,63,94,.28) 50%, rgba(244,63,94,0) 100%);"></div>
         </div>
       </div>


   </div>
</div>



<div class="third-sction mt-10">
   <div class="row">
     <!-- Recent Bills (Blue) -->
     <div class="col-md-8">
       <div
         class="w-full div-box-height bg-white border rounded-[8px] dashboard-module-cards {{ $showRecentBills ? '' : 'hidden-module' }} hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300"
         data-card="recent_bills"
         style="
           position:relative;
           border-color:#E3EAF5;
           border-radius:14px;
           background:linear-gradient(180deg,#FFFFFF 0%,#F8FBFF 100%);
           box-shadow:0 10px 24px rgba(16,24,40,.06), 0 1px 0 rgba(16,24,40,.04);
           overflow:hidden;
         "
       >
         <!-- Blue accent strip -->
         <div style="height:4px; background:linear-gradient(90deg,#93C5FD 0%, #6d94f7 50%, #93C5FD 100%);"></div>

         <span class="customize-toggle" style="display:none;">
           <i class="fa {{$showRecentBills ? 'fa-times text-danger' : 'fa-check text-success'}}"></i>
         </span>

         <div class="flex items-center justify-between px-4 py-3 pb-[7px]"
         style="border-bottom:1px solid #E3EAF5; background:linear-gradient(180deg,#FFFFFF 0%,#F3F7FF 100%);">
         <h2 class="mb-[18px] text-[16px] sm:text-[17px] leading-[24px] font-[800]" style="color:#0F172A;">
            Recent Bills
        </h2>
      </div>

      <div class="overflow-x-auto" style=" background:#FFFFFF; margin:12px;">
         <table class="dashtable min-w-full text-gray-700" style="border-collapse:separate; border-spacing:0;">
             <thead style="background:linear-gradient(180deg,#EEF4FF 0%, #FFFFFF 100%); border-bottom:1px solid #E3EAF5;">
               <tr>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">#</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Vendor') }}</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Bill Date') }}</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Due Date') }}</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Amount') }}</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Status') }}</th>
               </tr>
             </thead>
             <tbody class="divide-y" style="border-color:#EEF2FF;">
               @forelse($recentBill as $bill)
                 <tr class="hover:bg-[#F8FAFF] transition-colors" style="border-bottom:1px solid #EEF2FF;">
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ \Auth::user()->billNumberFormat($bill->bill_id) }}
                   </td>
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ !empty($bill->vender) ? $bill->vender->name : '' }}
                   </td>
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ \Carbon\Carbon::parse($bill->bill_date)->format('Y-m-d') }}
                   </td>
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ \Carbon\Carbon::parse($bill->due_date)->format('Y-m-d') }}
                   </td>
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ \Auth::user()->priceFormat($bill->getTotal()) }}
                   </td>
                   <td style="padding:10px 12px;">
                     @if ($bill->status == 0)
                       <span style="background:#E0EAFF; color:#1D4ED8; font-size:12px; font-weight:700; padding:3px 12px; border-radius:999px;">
                         {{ __(\App\Models\Bill::$statues[$bill->status]) }}
                       </span>
                     @elseif($bill->status == 1)
                       <span style="background:#E0EAFF; color:#1D4ED8; font-size:12px; font-weight:700; padding:3px 12px; border-radius:999px;">
                         {{ __(\App\Models\Bill::$statues[$bill->status]) }}
                       </span>
                     @elseif($bill->status == 2)
                       <span style="background:#E0EAFF; color:#1D4ED8; font-size:12px; font-weight:700; padding:3px 12px; border-radius:999px;">
                         {{ __(\App\Models\Bill::$statues[$bill->status]) }}
                       </span>
                     @elseif($bill->status == 3)
                       <span style="background:#E0EAFF; color:#1D4ED8; font-size:12px; font-weight:700; padding:3px 12px; border-radius:999px;">
                         {{ __(\App\Models\Bill::$statues[$bill->status]) }}
                       </span>
                     @elseif($bill->status == 4)
                       <span style="background:#E0EAFF; color:#1D4ED8; font-size:12px; font-weight:700; padding:3px 12px; border-radius:999px;">
                         {{ __(\App\Models\Bill::$statues[$bill->status]) }}
                       </span>
                     @endif
                   </td>
                 </tr>
               @empty
                 <tr>
                   <td colspan="6" style="padding:14px 12px; border-top:1px solid #E3EAF5;">
                     <div class="text-center" style="color:#6B7280;">
                       <h6 style="margin:6px 0;">{{ __('there is no recent bill') }}</h6>
                     </div>
                   </td>
                 </tr>
               @endforelse
             </tbody>
           </table>
         </div>

         <!-- subtle bottom glow -->
         <div style="position:absolute; left:0; right:0; bottom:0; height:8px; background:linear-gradient(90deg, rgba(59,130,246,0) 0%, rgba(59,130,246,.28) 50%, rgba(59,130,246,0) 100%); pointer-events:none;"></div>
      </div>
     </div>

     <!-- Latest Income (Blue) -->
     <div class="col-md-4 mt-4 mt-md-0">
       <div
         class="w-full div-box-height bg-white border rounded-[8px] dashboard-module-cards {{ $showLatestIncome ? '' : 'hidden-module' }} hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300"
         data-card="latest_income"
         style="
           position:relative;
           border-color:#E3EAF5;
           border-radius:14px;
           background:linear-gradient(180deg,#FFFFFF 0%,#F8FBFF 100%);
           box-shadow:0 10px 24px rgba(16,24,40,.06), 0 1px 0 rgba(16,24,40,.04);
           overflow:hidden;
         "
       >
         <!-- Blue accent strip -->
         <div style="height:4px; background:linear-gradient(90deg,#93C5FD 0%, #6d94f7 50%, #93C5FD 100%);"></div>

         <span class="customize-toggle" style="display:none;">
           <i class="fa {{$showLatestIncome ? 'fa-times text-danger' : 'fa-check text-success'}}"></i>
         </span>

         <div class="flex items-center justify-between px-4 py-3 pb-[7px]"
         style="border-bottom:1px solid #E3EAF5; background:linear-gradient(180deg,#FFFFFF 0%,#F3F7FF 100%);">
         <h2 class="mb-[18px] text-[16px] sm:text-[17px] leading-[24px] font-[800]" style="color:#0F172A;">
           {{ __('Latest Income') }}
         </h2>
         </div>

         <div class="overflow-x-auto" style=" background:#FFFFFF; margin:12px;">
            <table class="dashtable min-w-full text-gray-700" style="border-collapse:separate; border-spacing:0;">
             <thead style="background:linear-gradient(180deg,#EEF4FF 0%, #FFFFFF 100%); border-bottom:1px solid #E3EAF5;">
               <tr>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Date') }}</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Customer') }}</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Amount Due') }}</th>
               </tr>
             </thead>
             <tbody class="divide-y" style="border-color:#EEF2FF;">
               @forelse($latestIncome as $income)
                 <tr class="hover:bg-[#F8FAFF] transition-colors" style="border-bottom:1px solid #EEF2FF;">
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ \Carbon\Carbon::parse($income->date)->format('Y-m-d') }}
                   </td>
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ !empty($income->customer) ? $income->customer->name : '-' }}
                   </td>
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ \Auth::user()->priceFormat($income->amount) }}
                   </td>
                 </tr>
               @empty
                 <tr>
                   <td colspan="3" style="padding:14px 12px; border-top:1px solid #E3EAF5;">
                     <div class="text-center" style="color:#6B7280;">
                       <h6 style="margin:6px 0;">{{ __('there is no latest income') }}</h6>
                     </div>
                   </td>
                 </tr>
               @endforelse
             </tbody>
           </table>
         </div>

         <!-- subtle bottom glow -->
         <div style="height:8px; background:linear-gradient(90deg, rgba(59,130,246,0) 0%, rgba(59,130,246,.28) 50%, rgba(59,130,246,0) 100%); margin-top:10px;"></div>
       </div>
     </div>
   </div>
 </div>






 <div class="fifth-sction mt-10">
   <div class="row">
     <!-- Recent Invoices (Red Accent) -->
     <div class="col-md-8 p-0">
       <div
         class="w-full div-box-height bg-white border rounded-[8px] dashboard-module-cards {{ $showRecentInvoices ? '' : 'hidden-module' }} hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300"
         data-card="recent_invoices"
         style="
           position:relative;
           border-color:#E3EAF5;
           border-radius:14px;
           background:linear-gradient(180deg,#FFFFFF 0%, #FFF7F7 100%);
           box-shadow:0 10px 24px rgba(16,24,40,.06), 0 1px 0 rgba(16,24,40,.04);
           overflow:hidden;
         "
       >
         <!-- Red accent strip -->
         <div style="height:4px; background:linear-gradient(90deg,#FDE2E2 0%, #FECACA 50%, #FDE2E2 100%);"></div>

         <span class="customize-toggle" style="display:none;">
           <i class="fa {{$showRecentInvoices ? 'fa-times text-danger' : 'fa-check text-success'}}"></i>
         </span>

         <div class="px-4 py-3 pb-[7px]"
         style="border-bottom:1px solid #F1DADA; background:linear-gradient(180deg,#FFFFFF 0%,#FFF3F3 100%);">
         <h2 class="mb-[18px] text-[16px] sm:text-[17px] leading-[24px] font-[800]" style="color:#0F172A;">
           {{ __('Recent Invoices') }}
         </h2>
      </div>
      <div class="overflow-x-auto" style=" background:#FFFFFF; margin:12px;">
         <table class="dashtable min-w-full text-gray-700" style="border-collapse:separate; border-spacing:0;">
             <thead style="background:linear-gradient(180deg,#FFECEC 0%, #FFFFFF 100%); border-bottom:1px solid #FAD1D1;">
               <tr>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">#</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Vendor') }}</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Bill Date') }}</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Issue Date') }}</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Amount') }}</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Status') }}</th>
               </tr>
             </thead>
             <tbody class="divide-y" style="border-color:#FDE2E2;">
               @forelse($recentInvoice as $invoice)
                 <tr class="hover:bg-[#FFF5F5] transition-colors" style="border-bottom:1px solid #FDE2E2;">
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ \Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                   </td>
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ !empty($invoice->customer) ? $invoice->customer->name : '' }}
                   </td>
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ \Carbon\Carbon::parse($invoice->issue_date)->format('Y-m-d') }}
                   </td>
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') }}
                   </td>
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ \Auth::user()->priceFormat($invoice->getTotal()) }}
                   </td>
                   <td style="padding:10px 12px;">
                     @if ($invoice->status == 0)
                       <span style="background:#FEE2E2; color:#B91C1C; font-size:12px; font-weight:700; padding:3px 12px; border-radius:999px;">
                         {{ __(\App\Models\Invoice::$statues[$invoice->status]) }}
                       </span>
                     @elseif($invoice->status == 1)
                       <span style="background:#FEE2E2; color:#B91C1C; font-size:12px; font-weight:700; padding:3px 12px; border-radius:999px;">
                         {{ __(\App\Models\Invoice::$statues[$invoice->status]) }}
                       </span>
                     @elseif($invoice->status == 2)
                       <span style="background:#FEE2E2; color:#B91C1C; font-size:12px; font-weight:700; padding:3px 12px; border-radius:999px;">
                         {{ __(\App\Models\Invoice::$statues[$invoice->status]) }}
                       </span>
                     @elseif($invoice->status == 3)
                       <span style="background:#FEE2E2; color:#B91C1C; font-size:12px; font-weight:700; padding:3px 12px; border-radius:999px;">
                         {{ __(\App\Models\Invoice::$statues[$invoice->status]) }}
                       </span>
                     @elseif($invoice->status == 4)
                       <span style="background:#FEE2E2; color:#B91C1C; font-size:12px; font-weight:700; padding:3px 12px; border-radius:999px;">
                         {{ __(\App\Models\Invoice::$statues[$invoice->status]) }}
                       </span>
                     @endif
                   </td>
                 </tr>
               @empty
                 <tr>
                   <td colspan="6" style="padding:14px 12px; border-top:1px solid #FAD1D1;">
                     <div class="text-center" style="color:#6B7280;">
                       <h6 style="margin:6px 0;">{{ __('there is no recent invoice') }}</h6>
                     </div>
                   </td>
                 </tr>
               @endforelse
             </tbody>
           </table>
         </div>

         <!-- bottom red glow (touching card bottom) -->
         <div style="position:absolute; left:0; right:0; bottom:0; height:8px; background:linear-gradient(90deg, rgba(239,68,68,0) 0%, rgba(239,68,68,.28) 50%, rgba(239,68,68,0) 100%); pointer-events:none;"></div>
       </div>
     </div>

     <!-- Latest Expense (Red Accent) -->
     <div class="col-md-4 mt-4 mt-md-0">
       <div
         class="w-full div-box-height bg-white border rounded-[8px] dashboard-module-cards {{ $showLatestExpense ? '' : 'hidden-module' }} hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300"
         data-card="latest_expense"
         style="
           position:relative;
           border-color:#E3EAF5;
           border-radius:14px;
           background:linear-gradient(180deg,#FFFFFF 0%, #FFF7F7 100%);
           box-shadow:0 10px 24px rgba(16,24,40,.06), 0 1px 0 rgba(16,24,40,.04);
           overflow:hidden;
         "
       >
         <!-- Red accent strip -->
         <div style="height:4px; background:linear-gradient(90deg,#FDE2E2 0%, #FECACA 50%, #FDE2E2 100%);"></div>

         <span class="customize-toggle" style="display:none;">
           <i class="fa {{$showLatestExpense ? 'fa-times text-danger' : 'fa-check text-success'}}"></i>
         </span>

         <div class="px-4 py-3 pb-[7px]"
         style="border-bottom:1px solid #F1DADA; background:linear-gradient(180deg,#FFFFFF 0%,#FFF3F3 100%);">
     <h2 class="mb-[18px] text-[16px] sm:text-[17px] leading-[24px] font-[800]" style="color:#0F172A;">
           {{ __('Latest Expense') }}
         </h2>
         </div>

         <div class="overflow-x-auto" style=" background:#FFFFFF; margin:12px;">
            <table class="dashtable min-w-full text-gray-700" style="border-collapse:separate; border-spacing:0;">
             <thead style="background:linear-gradient(180deg,#FFECEC 0%, #FFFFFF 100%); border-bottom:1px solid #FAD1D1;">
               <tr>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Date') }}</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Customer') }}</th>
                 <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Amount Due') }}</th>
               </tr>
             </thead>
             <tbody class="divide-y" style="border-color:#FDE2E2;">
               @forelse($latestExpense as $expense)
                 <tr class="hover:bg-[#FFF5F5] transition-colors" style="border-bottom:1px solid #FDE2E2;">
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ \Carbon\Carbon::parse($expense->date)->format('Y-m-d') }}
                   </td>
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ !empty($expense->customer) ? $expense->customer->name : '-' }}
                   </td>
                   <td style="padding:10px 12px; font-size:13px; color:#111827;">
                     {{ \Auth::user()->priceFormat($expense->amount) }}
                   </td>
                 </tr>
               @empty
                 <tr>
                   <td colspan="3" style="padding:14px 12px; border-top:1px solid #FAD1D1;">
                     <div class="text-center" style="color:#6B7280;">
                       <h6 style="margin:6px 0;">{{ __('there is no latest expense') }}</h6>
                     </div>
                   </td>
                 </tr>
               @endforelse
             </tbody>
           </table>
         </div>

         <!-- bottom red glow (touching card bottom) -->
         <div style="position:absolute; left:0; right:0; bottom:0; height:8px; background:linear-gradient(90deg, rgba(239,68,68,0) 0%, rgba(239,68,68,.28) 50%, rgba(239,68,68,0) 100%); pointer-events:none;"></div>
       </div>
     </div>
   </div>
 </div>



<div class="forth-sction mt-10">
  <div class="row">
    <!-- Goal (Yellow Accent) -->
    <div class="col-md-8 p-0">
      <div
        class="w-full div-box-height bg-white border rounded-[8px] dashboard-module-cards {{ $showGoal ? '' : 'hidden-module' }} hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300"
        data-card="goal"
        style="
           position:relative;
           border-color:#E3EAF5;
           border-radius:14px;
           background:linear-gradient(180deg,#FFFFFF 0%,#F8FBFF 100%);
           box-shadow:0 10px 24px rgba(16,24,40,.06), 0 1px 0 rgba(16,24,40,.04);
           overflow:hidden;
        "
      >
        <!-- Yellow accent strip -->
        <div style="height:4px; background:linear-gradient(90deg,#FEF3C7 0%, #FDE68A 50%, #FEF3C7 100%);"></div>

        <span class="customize-toggle" style="display:none;">
          <i class="fa {{$showGoal ? 'fa-times text-danger' : 'fa-check text-success'}}"></i>
        </span>

        <div class="px-4 py-3 pb-[7px]"
             style="border-bottom:1px solid #F4E5A3; background:linear-gradient(180deg,#FFFFFF 0%,#FFF7D6 100%);">
          <h2 class="mb-[18px] text-[16px] sm:text-[17px] leading-[24px] font-[800]" style="color:#0F172A;">
            {{ __('Goal') }}
          </h2>
        </div>

        <div class="overflow-x-auto" style=" background:#FFFFFF; margin:12px;">
         <table class="dashtable min-w-full text-gray-700" style="border-collapse:separate; border-spacing:0;">
            <thead style="background:linear-gradient(180deg,#FFF7E6 0%, #FFFFFF 100%); border-bottom:1px solid #FDE68A;">
              <tr>
                <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Name') }}</th>
                <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Type') }}</th>
                <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Target') }}</th>
                <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Progress') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y" style="border-color:#FBE9A6;">
              @forelse($goals as $goal)
                @php
                  $total = $goal->target($goal->type, $goal->from, $goal->to, $goal->amount)['total'];
                  $percentage = $goal->target($goal->type, $goal->from, $goal->to, $goal->amount)['percentage'];
                @endphp
                <tr class="hover:bg-[#FFFDF3] transition-colors" style="border-bottom:1px solid #FBE9A6;">
                  <td style="padding:10px 12px; font-size:13px; color:#111827;">{{ $goal->name }}</td>
                  <td style="padding:10px 12px; font-size:13px; color:#111827;">{{ __(\App\Models\Goal::$goalType[$goal->type]) }}</td>
                  <td style="padding:10px 12px; font-size:13px; color:#111827;">{{ $goal->from . ' To ' . $goal->to }}</td>
                  <td style="padding:10px 12px;">
                    <div class="flex items-center gap-[6px] min-w-[140px]">
                      <div class="w-full rounded-full h-2" style="background:#E5E7EB;">
                        <div class="h-2 rounded-full" style="background:#F59E0B; width: {{ number_format($percentage, App\Models\Utility::getValByName('decimal_number'), '.', '') }}%;"></div>
                      </div>
                      <span style="font-size:12px; color:#6B7280;">
                        {{ number_format($percentage, App\Models\Utility::getValByName('decimal_number'), '.', '') }}%
                      </span>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" style="padding:14px 12px;">
                    <h6 style="margin:6px 0; color:#6B7280;">{{ __('There is no goal.') }}</h6>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- bottom yellow glow (touching card bottom) -->
        <div style="position:absolute; left:0; right:0; bottom:0; height:8px; background:linear-gradient(90deg, rgba(245,158,11,0) 0%, rgba(245,158,11,.28) 50%, rgba(245,158,11,0) 100%); pointer-events:none;"></div>
      </div>
    </div>

    <!-- Account Balance (Yellow Accent) -->
    <div class="col-md-4 mt-4 mt-md-0">
      <div
        class="w-full div-box-height bg-white border rounded-[8px] dashboard-module-cards {{ $showAccountBalance ? '' : 'hidden-module' }} hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300"
        data-card="account_balance"
        style="
           position:relative;
           border-color:#E3EAF5;
           border-radius:14px;
           background:linear-gradient(180deg,#FFFFFF 0%,#F8FBFF 100%);
           box-shadow:0 10px 24px rgba(16,24,40,.06), 0 1px 0 rgba(16,24,40,.04);
           overflow:hidden;
        "
      >
        <!-- Yellow accent strip -->
        <div style="height:4px; background:linear-gradient(90deg,#FEF3C7 0%, #FDE68A 50%, #FEF3C7 100%);"></div>

        <span class="customize-toggle" style="display:none;">
          <i class="fa {{$showAccountBalance ? 'fa-times text-danger' : 'fa-check text-success'}}"></i>
        </span>

        <div class="px-4 py-3 pb-[7px]"
             style="border-bottom:1px solid #F4E5A3; background:linear-gradient(180deg,#FFFFFF 0%,#FFF7D6 100%);">
          <h2 class="mb-[18px] text-[16px] sm:text-[17px] leading-[24px] font-[800]" style="color:#0F172A;">
            {{ __('Account Balance') }}
          </h2>
        </div>

        <div class="overflow-x-auto" style=" background:#FFFFFF; margin:12px;">
          <table class="dashtable min-w-full text-gray-700" style="border-collapse:separate; border-spacing:0;">
            <thead style="background:linear-gradient(180deg,#FFF7E6 0%, #FFFFFF 100%); border-bottom:1px solid #FDE68A;">
              <tr>
                <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Bank') }}</th>
                <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Holder Name') }}</th>
                <th class="text-left" style="padding:10px 12px; font-size:13px; font-weight:700; color:#0F172A;">{{ __('Balance') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y" style="border-color:#FBE9A6;">
              @forelse($bankAccountDetail as $bankAccount)
                <tr class="hover:bg-[#FFFDF3] transition-colors" style="border-bottom:1px solid #FBE9A6;">
                  <td style="padding:10px 12px; font-size:13px; color:#111827;">{{ $bankAccount->bank_name }}</td>
                  <td style="padding:10px 12px; font-size:13px; color:#111827;">{{ $bankAccount->holder_name }}</td>
                  <td style="padding:10px 12px; font-size:13px; color:#111827;">{{ \Auth::user()->priceFormat($bankAccount->opening_balance) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" style="padding:14px 12px; border-top:1px solid #FDE68A;">
                    <div class="text-center" style="color:#6B7280;">
                      <h6 style="margin:6px 0;">{{ __('there is no account balance') }}</h6>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- bottom yellow glow (touching card bottom) -->
        <div style="position:absolute; left:0; right:0; bottom:0; height:8px; background:linear-gradient(90deg, rgba(245,158,11,0) 0%, rgba(245,158,11,.28) 50%, rgba(245,158,11,0) 100%); pointer-events:none;"></div>
      </div>
    </div>
  </div>
</div>



@endsection
@push('script-page')
<script>
   document.getElementById('customize-dashboard').onclick = function() {
       // Show all cards in customize mode
       document.querySelectorAll('.dashboard-module-cards').forEach(card => {
           card.classList.add('customize-mode');
           card.style.display = 'block';
           // Set icon based on current state
           let icon = card.querySelector('.customize-toggle i');
           if (card.classList.contains('hidden-module')) {
               icon.className = 'fa fa-check text-success'; // Show icon
           } else {
               icon.className = 'fa fa-times text-danger'; // Hide icon
           }
           card.querySelector('.customize-toggle').style.display = 'inline-block';
       });
       document.getElementById('save-dashboard-customization').style.display = 'inline-block';
       document.getElementById('cancel-dashboard-customization').style.display = 'inline-block';
       this.style.display = 'none';
   };
   document.getElementById('save-dashboard-customization').onclick = function() {
       let cards = {};
       document.querySelectorAll('.dashboard-module-cards').forEach(card => {
           let key = card.getAttribute('data-card');
           if (key) {
               // If card has hidden-module, it's hidden
               cards[key] = !card.classList.contains('hidden-module');
           }
       });
       fetch('{{ route('dashboard.customize') }}', {
           method: 'POST',
           headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}',
               'Content-Type': 'application/json'
           },
           body: JSON.stringify({
               customization: cards
           })
       }).then(res => res.json()).then(data => {
           if (data.success) {
               location.reload();
           } else {
               alert('Failed to save customization!');
           }
       }).catch(() => alert('Failed to save customization!'));
   };

   document.getElementById('cancel-dashboard-customization').onclick = function() {
       location.reload();
   };
   document.querySelectorAll('.customize-toggle').forEach(toggle => {
       toggle.onclick = function(e) {
           let card = this.closest('.dashboard-module-cards');
           let icon = this.querySelector('i');
           if (card.classList.contains('hidden-module')) {
               // Was hidden, now show
               card.classList.remove('hidden-module');
               icon.className = 'fa fa-times text-danger';
           } else {
               // Was shown, now hide
               card.classList.add('hidden-module');
               icon.className = 'fa fa-check text-success';
           }
       };
   });
</script>
@endpush
