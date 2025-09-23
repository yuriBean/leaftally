@extends('layouts.admin')
@section('page-title')
    {{__('Budget Vs Actual: ')}}{{ $budget->name }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('budget.index')}}">{{__('Budget Planner')}}</a></li>
    <li class="breadcrumb-item">{{ $budget->name }}</li>
@endsection
@push('script-page')
    <script src="{{asset('js/jquery-ui.min.js')}}"></script>
    <script>
        $(document).on('keyup', '.income_data', function () {
            var el = $(this).parent().parent();
            var inputs = $(el.find('.income_data'));
            var totalincome = 0;
            for (var i = 0; i < inputs.length; i++) {
                var price = $(inputs[i]).val();
                totalincome = parseFloat(totalincome) + parseFloat(price || 0);
            }
            el.find('.totalIncome').html(totalincome);

            var month_income = $(this).data('month');
            var month_inputs = $(el.parent().find('.' + month_income + '_income'));
            var month_totalincome = 0;
            for (var i = 0; i < month_inputs.length; i++) {
                var month_price = $(month_inputs[i]).val();
                month_totalincome = parseFloat(month_totalincome) + parseFloat(month_price || 0);
            }
            var month_total_income = month_income + '_total_income';
            el.parent().find('.' + month_total_income).html(month_totalincome);

            var total_inputs = $(el.parent().find('.totalIncome'));
            var income = 0;
            for (var i = 0; i < total_inputs.length; i++) {
                var price = $(total_inputs[i]).html();
                income = parseFloat(income) + parseFloat(price || 0);
            }
            el.parent().find('.income').html(income);
        });

        $(document).on('keyup', '.expense_data', function () {
            var el = $(this).parent().parent();
            var inputs = $(el.find('.expense_data'));
            var totalexpense = 0;
            for (var i = 0; i < inputs.length; i++) {
                var price = $(inputs[i]).val();
                totalexpense = parseFloat(totalexpense) + parseFloat(price || 0);
            }
            el.find('.totalExpense').html(totalexpense);

            var month_expense = $(this).data('month');
            var month_inputs = $(el.parent().find('.' + month_expense + '_expense'));
            var month_totalexpense = 0;
            for (var i = 0; i < month_inputs.length; i++) {
                var month_price = $(month_inputs[i]).val();
                month_totalexpense = parseFloat(month_totalexpense) + parseFloat(month_price || 0);
            }
            var month_total_expense = month_expense + '_total_expense';
            el.parent().find('.' + month_total_expense).html(month_totalexpense);

            var total_inputs = $(el.parent().find('.totalExpense'));
            var expense = 0;
            for (var i = 0; i < total_inputs.length; i++) {
                var price = $(total_inputs[i]).html();
                expense = parseFloat(expense) + parseFloat(price || 0);
            }
            el.parent().find('.expense').html(expense);
        });

        $(document).on('change', '.period', function () {
            var period = $(this).val();
            $('.budget_plan').removeClass('d-block').addClass('d-none');
            $('#' + period).removeClass('d-none').addClass('d-block');
        });

        $('.period').trigger('change');
    </script>
@endpush

@section('action-btn')
    
@endsection

<style type="text/css">
    .custom_temp {
        overflow-x: scroll;
    }
</style>

@section('content')
    <div class="col-xl-3 col-md-6 col-lg-3">
        <div class="card p-4 my-4">
            <h6 class="report-text mb-0">{{__('Year :')}} {{ $budget->from }}</h6>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style custom_temp cstm-tbl-data">
                    {{-- Monthly Budget --}}
                    @if($budget->period == 'monthly')
                        <table class="table table-bordered table-item data">
                            <thead>
                                <tr class="months-tablehead">
                                    <td rowspan="2"></td>
                                    @foreach($monthList as $month)
                                        <th colspan="3" scope="colgroup" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">{{__($month)}}</th>
                                    @endforeach
                                </tr>
                                <tr class="months-tabledata">
                                    @foreach($monthList as $month)
                                        <th scope="col" class="text-[10px] bg-green-700 font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">Budget</th>
                                        <th scope="col" class="text-[10px] bg-green-700 font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">Actual</th>
                                        <th scope="col" class="text-[10px] bg-green-700 font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">Over Budget</th>
                                    @endforeach
                                </tr>
                            </thead>
                            
                            <tr>
                                <th colspan="37" class="px-4 py-3 bg-green-100 border border-[#E5E5E5] text-gray-700">
                                    <span><strong>{{__('Income :')}}</strong></span>
                                </th>
                            </tr>
                            @php
                                $overBudgetTotal = [];
                            @endphp
                            @foreach ($incomeproduct as $productService)
                                <tr>
                                    <td class="text-dark bg-gray-100"><strong>{{ $productService->name }}</strong></td>
                                    @foreach($monthList as $month)
                                        @php
                                            $budgetAmount = isset($budget['income_data'][$productService->id][$month]) ? $budget['income_data'][$productService->id][$month] : 0;
                                            $actualAmount = $incomeArr[$productService->id][$month] ?? 0;
                                            $overBudgetAmount = $actualAmount - $budgetAmount;
                                            $overBudgetTotal[$productService->id][$month] = $overBudgetAmount;
                                        @endphp
                                        <td class="income_data {{$month}}_income">
                                            {{ \Auth::user()->priceFormat($budgetAmount) }}
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($actualAmount) }}
                                            <p>
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $actualAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $actualAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($overBudgetAmount) }}
                                            <p class="{{ $budgetAmount < $overBudgetAmount ? 'green-text' : ($budgetAmount > $overBudgetAmount ? 'red-text' : '') }}">
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            @php
                                $overBudgetTotalArr = [];
                                foreach($overBudgetTotal as $overBudget) {
                                    foreach($overBudget as $k => $value) {
                                        $overBudgetTotalArr[$k] = (isset($overBudgetTotalArr[$k]) ? $overBudgetTotalArr[$k] + $value : $value);
                                    }
                                }
                            @endphp
                            <tr class="total">
                                <td class="text-dark"><strong>{{__('Total :')}}</strong></td>
                                @if(!empty($budgetTotal))
                                    @foreach($monthList as $month)
                                        <td class="text-dark {{$month}}_total_income">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($budgetTotal[$month] ?? 0) }}</strong>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($incomeTotalArr[$month] ?? 0) }}</strong>
                                            <p>{{ ($budgetTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $incomeTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $incomeTotalArr[$month] ?? 0) . '%)' : '') : '' }}</p>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($overBudgetTotalArr[$month] ?? 0) }}</strong>
                                            <p class="{{ ($budgetTotal[$month] ?? 0) < ($overBudgetTotalArr[$month] ?? 0) ? 'green-text' : (($budgetTotal[$month] ?? 0) > ($overBudgetTotalArr[$month] ?? 0) ? 'red-text' : '') }}">
                                                {{ ($budgetTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $overBudgetTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $overBudgetTotalArr[$month] ?? 0) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            
                            <tr>
                                <th colspan="37" class="px-4 py-3 bg-green-100 border border-[#E5E5E5] text-gray-700">
                                    <span><strong>{{__('Expense :')}}</strong></span>
                                </th>
                            </tr>
                            @php
                                $overExpenseBudgetTotal = [];
                            @endphp
                            @foreach ($expenseproduct as $productService)
                                <tr>
                                    <td class="text-dark bg-gray-100"><strong>{{ $productService->name }}</strong></td>
                                    @foreach($monthList as $month)
                                        @php
                                            $budgetAmount = isset($budget['expense_data'][$productService->id][$month]) ? $budget['expense_data'][$productService->id][$month] : 0;
                                            $actualAmount = $expenseArr[$productService->id][$month] ?? 0;
                                            $overBudgetAmount = $actualAmount - $budgetAmount;
                                            $overExpenseBudgetTotal[$productService->id][$month] = $overBudgetAmount;
                                        @endphp
                                        <td class="expense_data {{$month}}_expense">
                                            {{ \Auth::user()->priceFormat($budgetAmount) }}
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($actualAmount) }}
                                            <p>
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $actualAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $actualAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($overBudgetAmount) }}
                                            <p class="{{ $budgetAmount < $overBudgetAmount ? 'green-text' : ($budgetAmount > $overBudgetAmount ? 'red-text' : '') }}">
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            @php
                                $overExpenseBudgetTotalArr = [];
                                foreach($overExpenseBudgetTotal as $overExpenseBudget) {
                                    foreach($overExpenseBudget as $k => $value) {
                                        $overExpenseBudgetTotalArr[$k] = (isset($overExpenseBudgetTotalArr[$k]) ? $overExpenseBudgetTotalArr[$k] + $value : $value);
                                    }
                                }
                            @endphp
                            <tr class="total">
                                <td class="text-dark"><strong>{{__('Total :')}}</strong></td>
                                @if(!empty($budgetExpenseTotal))
                                    @foreach($monthList as $month)
                                        <td class="text-dark {{$month}}_total_expense">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($budgetExpenseTotal[$month] ?? 0) }}</strong>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($expenseTotalArr[$month] ?? 0) }}</strong>
                                            <p>{{ ($budgetExpenseTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $expenseTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $expenseTotalArr[$month] ?? 0) . '%)' : '') : '' }}</p>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($overExpenseBudgetTotalArr[$month] ?? 0) }}</strong>
                                            <p class="{{ ($budgetExpenseTotal[$month] ?? 0) < ($overExpenseBudgetTotalArr[$month] ?? 0) ? 'green-text' : (($budgetExpenseTotal[$month] ?? 0) > ($overExpenseBudgetTotalArr[$month] ?? 0) ? 'red-text' : '') }}">
                                                {{ ($budgetExpenseTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $overExpenseBudgetTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $overExpenseBudgetTotalArr[$month] ?? 0) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <td></td>
                            <tfoot>
                                <tr class="total" style="background:#f8f9fd;">
                                    <td class="text-dark"><strong>{{__('Net Profit :')}}</strong></td>
                                    @php
                                        $overbudgetprofit = [];
                                        $keys = array_keys(array_merge($overBudgetTotalArr, $overExpenseBudgetTotalArr));
                                        foreach($keys as $v) {
                                            $overbudgetprofit[$v] = (empty($overBudgetTotalArr[$v]) ? 0 : $overBudgetTotalArr[$v]) - (empty($overExpenseBudgetTotalArr[$v]) ? 0 : $overExpenseBudgetTotalArr[$v]);
                                        }
                                        $data['overbudgetprofit'] = $overbudgetprofit;
                                    @endphp
                                    @if(!empty($budgetprofit))
                                        @foreach($monthList as $month)
                                            <td class="text-dark">
                                                <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($budgetprofit[$month] ?? 0) }}</strong>
                                            </td>
                                            <td class="text-dark">
                                                <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($actualprofit[$month] ?? 0) }}</strong>
                                                <p>{{ ($budgetprofit[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $actualprofit[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $actualprofit[$month] ?? 0) . '%)' : '') : '' }}</p>
                                            </td>
                                            <td class="text-dark">
                                                <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($overbudgetprofit[$month] ?? 0) }}</strong>
                                                <p class="{{ ($budgetprofit[$month] ?? 0) < ($overbudgetprofit[$month] ?? 0) ? 'green-text' : (($budgetprofit[$month] ?? 0) > ($overbudgetprofit[$month] ?? 0) ? 'red-text' : '') }}">
                                                    {{ ($budgetprofit[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $overbudgetprofit[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $overbudgetprofit[$month] ?? 0) . '%)' : '') : '' }}
                                                </p>
                                            </td>
                                        @endforeach
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    @elseif($budget->period == 'quarterly')
                        <table class="table table-bordered table-item data">
                            <thead>
                                <tr>
                                    <td rowspan="2"></td>
                                    @foreach($quarterly_monthlist as $month)
                                        <th colspan="3" scope="colgroup" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">{{__($month)}}</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach($quarterly_monthlist as $month)
                                        <th scope="col" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">Budget</th>
                                        <th scope="col" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">Actual</th>
                                        <th scope="col" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">Over Budget</th>
                                    @endforeach
                                </tr>
                            </thead>
                            
                            <tr>
                                <th colspan="37" class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                    <span class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{__('Income :')}}</span>
                                </th>
                            </tr>
                            @php
                                $overBudgetTotal = [];
                            @endphp
                            @foreach ($incomeproduct as $productService)
                                <tr>
                                    <td class="text-dark">{{ $productService->name }}</td>
                                    @foreach($quarterly_monthlist as $month)
                                        @php
                                            $budgetAmount = isset($budget['income_data'][$productService->id][$month]) ? $budget['income_data'][$productService->id][$month] : 0;
                                            $actualAmount = $incomeArr[$productService->id][$month] ?? 0;
                                            $overBudgetAmount = $actualAmount - $budgetAmount;
                                            $overBudgetTotal[$productService->id][$month] = $overBudgetAmount;
                                        @endphp
                                        <td class="income_data {{$month}}_income">
                                            {{ \Auth::user()->priceFormat($budgetAmount) }}
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($actualAmount) }}
                                            <p>
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $actualAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $actualAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($overBudgetAmount) }}
                                            <p class="{{ $budgetAmount < $overBudgetAmount ? 'green-text' : ($budgetAmount > $overBudgetAmount ? 'red-text' : '') }}">
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            @php
                                $overBudgetTotalArr = [];
                                foreach($overBudgetTotal as $overBudget) {
                                    foreach($overBudget as $k => $value) {
                                        $overBudgetTotalArr[$k] = (isset($overBudgetTotalArr[$k]) ? $overBudgetTotalArr[$k] + $value : $value);
                                    }
                                }
                            @endphp
                            <tr class="total">
                                <td class="text-dark  bg-green-100"><strong>{{__('Total :')}}</strong></td>
                                @if(!empty($budgetTotal))
                                    @foreach($quarterly_monthlist as $month)
                                        <td class="text-dark {{$month}}_total_income">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($budgetTotal[$month] ?? 0) }}</strong>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($incomeTotalArr[$month] ?? 0) }}</strong>
                                            <p>{{ ($budgetTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $incomeTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $incomeTotalArr[$month] ?? 0) . '%)' : '') : '' }}</p>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($overBudgetTotalArr[$month] ?? 0) }}</strong>
                                            <p class="{{ ($budgetTotal[$month] ?? 0) < ($overBudgetTotalArr[$month] ?? 0) ? 'green-text' : (($budgetTotal[$month] ?? 0) > ($overBudgetTotalArr[$month] ?? 0) ? 'red-text' : '') }}">
                                                {{ ($budgetTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $overBudgetTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $overBudgetTotalArr[$month] ?? 0) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            
                            <tr>
                                <th colspan="37" class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                    <span class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{__('Expense :')}}</span>
                                </th>
                            </tr>
                            @php
                                $overExpenseBudgetTotal = [];
                            @endphp
                            @foreach ($expenseproduct as $productService)
                                <tr>
                                    <td class="text-dark">{{ $productService->name }}</td>
                                    @foreach($quarterly_monthlist as $month)
                                        @php
                                            $budgetAmount = isset($budget['expense_data'][$productService->id][$month]) ? $budget['expense_data'][$productService->id][$month] : 0;
                                            $actualAmount = $expenseArr[$productService->id][$month] ?? 0;
                                            $overBudgetAmount = $actualAmount - $budgetAmount;
                                            $overExpenseBudgetTotal[$productService->id][$month] = $overBudgetAmount;
                                        @endphp
                                        <td class="expense_data {{$month}}_expense">
                                            {{ \Auth::user()->priceFormat($budgetAmount) }}
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($actualAmount) }}
                                            <p>
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $actualAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $actualAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($overBudgetAmount) }}
                                            <p class="{{ $budgetAmount < $overBudgetAmount ? 'green-text' : ($budgetAmount > $overBudgetAmount ? 'red-text' : '') }}">
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            @php
                                $overExpenseBudgetTotalArr = [];
                                foreach($overExpenseBudgetTotal as $overExpenseBudget) {
                                    foreach($overExpenseBudget as $k => $value) {
                                        $overExpenseBudgetTotalArr[$k] = (isset($overExpenseBudgetTotalArr[$k]) ? $overExpenseBudgetTotalArr[$k] + $value : $value);
                                    }
                                }
                            @endphp
                            <tr class="total">
                                <td class="text-dark"><strong>{{__('Total :')}}</strong></td>
                                @if(!empty($budgetExpenseTotal))
                                    @foreach($quarterly_monthlist as $month)
                                        <td class="text-dark {{$month}}_total_expense">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($budgetExpenseTotal[$month] ?? 0) }}</strong>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($expenseTotalArr[$month] ?? 0) }}</strong>
                                            <p>{{ ($budgetExpenseTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $expenseTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $expenseTotalArr[$month] ?? 0) . '%)' : '') : '' }}</p>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($overExpenseBudgetTotalArr[$month] ?? 0) }}</strong>
                                            <p class="{{ ($budgetExpenseTotal[$month] ?? 0) < ($overExpenseBudgetTotalArr[$month] ?? 0) ? 'green-text' : (($budgetExpenseTotal[$month] ?? 0) > ($overExpenseBudgetTotalArr[$month] ?? 0) ? 'red-text' : '') }}">
                                                {{ ($budgetExpenseTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $overExpenseBudgetTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $overExpenseBudgetTotalArr[$month] ?? 0) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <td></td>
                            <tfoot>
                                <tr class="total" style="background:#f8f9fd;">
                                    <td class="text-dark"><strong>{{__('Net Profit :')}}</strong></td>
                                    @php
                                        $overbudgetprofit = [];
                                        $keys = array_keys(array_merge($overBudgetTotalArr, $overExpenseBudgetTotalArr));
                                        foreach($keys as $v) {
                                            $overbudgetprofit[$v] = (empty($overBudgetTotalArr[$v]) ? 0 : $overBudgetTotalArr[$v]) - (empty($overExpenseBudgetTotalArr[$v]) ? 0 : $overExpenseBudgetTotalArr[$v]);
                                        }
                                        $data['overbudgetprofit'] = $overbudgetprofit;
                                    @endphp
                                    @if(!empty($budgetprofit))
                                        @foreach($quarterly_monthlist as $month)
                                            <td class="text-dark">
                                                <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($budgetprofit[$month] ?? 0) }}</strong>
                                            </td>
                                            <td class="text-dark">
                                                <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($actualprofit[$month] ?? 0) }}</strong>
                                                <p>{{ ($budgetprofit[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $actualprofit[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $actualprofit[$month] ?? 0) . '%)' : '') : '' }}</p>
                                            </td>
                                            <td class="text-dark">
                                                <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($overbudgetprofit[$month] ?? 0) }}</strong>
                                                <p class="{{ ($budgetprofit[$month] ?? 0) < ($overbudgetprofit[$month] ?? 0) ? 'green-text' : (($budgetprofit[$month] ?? 0) > ($overbudgetprofit[$month] ?? 0) ? 'red-text' : '') }}">
                                                    {{ ($budgetprofit[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $overbudgetprofit[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $overbudgetprofit[$month] ?? 0) . '%)' : '') : '' }}
                                                </p>
                                            </td>
                                        @endforeach
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    @elseif($budget->period == 'half-yearly')
                        <table class="table table-bordered table-item data">
                            <thead>
                                <tr>
                                    <td rowspan="2"></td>
                                    @foreach($half_yearly_monthlist as $month)
                                        <th colspan="3" scope="colgroup" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">{{ $month }}</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach($half_yearly_monthlist as $month)
                                        <th scope="col" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">Budget</th>
                                        <th scope="col" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">Actual</th>
                                        <th scope="col" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">Over Budget</th>
                                    @endforeach
                                </tr>
                            </thead>
                            
                            <tr>
                                <th colspan="37" class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                    <span class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{__('Income :')}}</span>
                                </th>
                            </tr>
                            @php
                                $overBudgetTotal = [];
                            @endphp
                            @foreach ($incomeproduct as $productService)
                                <tr>
                                    <td class="text-dark">{{ $productService->name }}</td>
                                    @foreach($half_yearly_monthlist as $month)
                                        @php
                                            $budgetAmount = isset($budget['income_data'][$productService->id][$month]) ? $budget['income_data'][$productService->id][$month] : 0;
                                            $actualAmount = $incomeArr[$productService->id][$month] ?? 0;
                                            $overBudgetAmount = $actualAmount - $budgetAmount;
                                            $overBudgetTotal[$productService->id][$month] = $overBudgetAmount;
                                        @endphp
                                        <td class="income_data {{$month}}_income">
                                            {{ \Auth::user()->priceFormat($budgetAmount) }}
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($actualAmount) }}
                                            <p>
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $actualAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $actualAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($overBudgetAmount) }}
                                            <p class="{{ $budgetAmount < $overBudgetAmount ? 'green-text' : ($budgetAmount > $overBudgetAmount ? 'red-text' : '') }}">
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            @php
                                $overBudgetTotalArr = [];
                                foreach($overBudgetTotal as $overBudget) {
                                    foreach($overBudget as $k => $value) {
                                        $overBudgetTotalArr[$k] = (isset($overBudgetTotalArr[$k]) ? $overBudgetTotalArr[$k] + $value : $value);
                                    }
                                }
                            @endphp
                            <tr class="total">
                                <td class="text-dark"><strong>{{__('Total :')}}</strong></td>
                                @if(!empty($budgetTotal))
                                    @foreach($half_yearly_monthlist as $month)
                                        <td class="text-dark {{$month}}_total_income">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($budgetTotal[$month] ?? 0) }}</strong>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($incomeTotalArr[$month] ?? 0) }}</strong>
                                            <p>{{ ($budgetTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $incomeTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $incomeTotalArr[$month] ?? 0) . '%)' : '') : '' }}</p>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($overBudgetTotalArr[$month] ?? 0) }}</strong>
                                            <p class="{{ ($budgetTotal[$month] ?? 0) < ($overBudgetTotalArr[$month] ?? 0) ? 'green-text' : (($budgetTotal[$month] ?? 0) > ($overBudgetTotalArr[$month] ?? 0) ? 'red-text' : '') }}">
                                                {{ ($budgetTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $overBudgetTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $overBudgetTotalArr[$month] ?? 0) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            
                            <tr>
                                <th colspan="37" class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                    <span class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{__('Expense :')}}</span>
                                </th>
                            </tr>
                            @php
                                $overExpenseBudgetTotal = [];
                            @endphp
                            @foreach ($expenseproduct as $productService)
                                <tr>
                                    <td class="text-dark">{{ $productService->name }}</td>
                                    @foreach($half_yearly_monthlist as $month)
                                        @php
                                            $budgetAmount = isset($budget['expense_data'][$productService->id][$month]) ? $budget['expense_data'][$productService->id][$month] : 0;
                                            $actualAmount = $expenseArr[$productService->id][$month] ?? 0;
                                            $overBudgetAmount = $actualAmount - $budgetAmount;
                                            $overExpenseBudgetTotal[$productService->id][$month] = $overBudgetAmount;
                                        @endphp
                                        <td class="expense_data {{$month}}_expense">
                                            {{ \Auth::user()->priceFormat($budgetAmount) }}
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($actualAmount) }}
                                            <p>
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $actualAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $actualAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($overBudgetAmount) }}
                                            <p class="{{ $budgetAmount < $overBudgetAmount ? 'green-text' : ($budgetAmount > $overBudgetAmount ? 'red-text' : '') }}">
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            @php
                                $overExpenseBudgetTotalArr = [];
                                foreach($overExpenseBudgetTotal as $overExpenseBudget) {
                                    foreach($overExpenseBudget as $k => $value) {
                                        $overExpenseBudgetTotalArr[$k] = (isset($overExpenseBudgetTotalArr[$k]) ? $overExpenseBudgetTotalArr[$k] + $value : $value);
                                    }
                                }
                            @endphp
                            <tr class="total">
                                <td class="text-dark"><strong>{{__('Total :')}}</strong></td>
                                @if(!empty($budgetExpenseTotal))
                                    @foreach($half_yearly_monthlist as $month)
                                        <td class="text-dark {{$month}}_total_expense">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($budgetExpenseTotal[$month] ?? 0) }}</strong>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($expenseTotalArr[$month] ?? 0) }}</strong>
                                            <p>{{ ($budgetExpenseTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $expenseTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $expenseTotalArr[$month] ?? 0) . '%)' : '') : '' }}</p>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($overExpenseBudgetTotalArr[$month] ?? 0) }}</strong>
                                            <p class="{{ ($budgetExpenseTotal[$month] ?? 0) < ($overExpenseBudgetTotalArr[$month] ?? 0) ? 'green-text' : (($budgetExpenseTotal[$month] ?? 0) > ($overExpenseBudgetTotalArr[$month] ?? 0) ? 'red-text' : '') }}">
                                                {{ ($budgetExpenseTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $overExpenseBudgetTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $overExpenseBudgetTotalArr[$month] ?? 0) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                            <td></td>
                            <tfoot>
                                <tr class="total" style="background:#f8f9fd;">
                                    <td class="text-dark bg-green-100"><strong>{{__('Net Profit :')}}</strong></td>
                                    @php
                                        $overbudgetprofit = [];
                                        $keys = array_keys(array_merge($overBudgetTotalArr, $overExpenseBudgetTotalArr));
                                        foreach($keys as $v) {
                                            $overbudgetprofit[$v] = (empty($overBudgetTotalArr[$v]) ? 0 : $overBudgetTotalArr[$v]) - (empty($overExpenseBudgetTotalArr[$v]) ? 0 : $overExpenseBudgetTotalArr[$v]);
                                        }
                                        $data['overbudgetprofit'] = $overbudgetprofit;
                                    @endphp
                                    @if(!empty($budgetprofit))
                                        @foreach($half_yearly_monthlist as $month)
                                            <td class="text-dark">
                                                <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($budgetprofit[$month] ?? 0) }}</strong>
                                            </td>
                                            <td class="text-dark">
                                                <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($actualprofit[$month] ?? 0) }}</strong>
                                                <p>{{ ($budgetprofit[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $actualprofit[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $actualprofit[$month] ?? 0) . '%)' : '') : '' }}</p>
                                            </td>
                                            <td class="text-dark">
                                                <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($overbudgetprofit[$month] ?? 0) }}</strong>
                                                <p class="{{ ($budgetprofit[$month] ?? 0) < ($overbudgetprofit[$month] ?? 0) ? 'green-text' : (($budgetprofit[$month] ?? 0) > ($overbudgetprofit[$month] ?? 0) ? 'red-text' : '') }}">
                                                    {{ ($budgetprofit[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $overbudgetprofit[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $overbudgetprofit[$month] ?? 0) . '%)' : '') : '' }}
                                                </p>
                                            </td>
                                        @endforeach
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <table class="table table-bordered table-item data">
                            <thead>
                                <tr>
                                    <td rowspan="2"></td>
                                    @foreach($yearly_monthlist as $month)
                                        <th colspan="3" scope="colgroup" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">{{ $month }}</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach($yearly_monthlist as $month)
                                        <th scope="col" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">Budget</th>
                                        <th scope="col" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">Actual</th>
                                        <th scope="col" class="text-[10px] font-[700] leading-[24px] text-center border-r-0 bg-[#F6F6F6]">Over Budget</th>
                                    @endforeach
                                </tr>
                            </thead>
                            
                            <tr>
                                <th colspan="37" class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                    <span class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{__('Income :')}}</span>
                                </th>
                            </tr>
                            @php
                                $overBudgetTotal = [];
                            @endphp
                            @foreach ($incomeproduct as $productService)
                                <tr>
                                    <td class="text-dark">{{ $productService->name }}</td>
                                    @foreach($yearly_monthlist as $month)
                                        @php
                                            $budgetAmount = isset($budget['income_data'][$productService->id][$month]) ? $budget['income_data'][$productService->id][$month] : 0;
                                            $actualAmount = $incomeArr[$productService->id][$month] ?? 0;
                                            $overBudgetAmount = $actualAmount - $budgetAmount;
                                            $overBudgetTotal[$productService->id][$month] = $overBudgetAmount;
                                        @endphp
                                        <td class="income_data {{$month}}_income">
                                            {{ \Auth::user()->priceFormat($budgetAmount) }}
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($actualAmount) }}
                                            <p>
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $actualAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $actualAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($overBudgetAmount) }}
                                            <p class="{{ $budgetAmount < $overBudgetAmount ? 'green-text' : ($budgetAmount > $overBudgetAmount ? 'red-text' : '') }}">
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            @php
                                $overBudgetTotalArr = [];
                                foreach($overBudgetTotal as $overBudget) {
                                    foreach($overBudget as $k => $value) {
                                        $overBudgetTotalArr[$k] = (isset($overBudgetTotalArr[$k]) ? $overBudgetTotalArr[$k] + $value : $value);
                                    }
                                }
                            @endphp
                            <tr class="total text-dark">
                                <td><strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{__('Total :')}}</strong></td>
                                @foreach($yearly_monthlist as $month)
                                    <td class="text-[12px] text-[#323232] font-[600] leading-[24px] {{$month}}_total_income">
                                        <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($budgetTotal[$month] ?? 0) }}</strong>
                                    </td>
                                    <td class="text-[12px] text-[#323232] font-[600] leading-[24px]">
                                        <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($incomeTotalArr[$month] ?? 0) }}</strong>
                                        <p>{{ ($budgetTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $incomeTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $incomeTotalArr[$month] ?? 0) . '%)' : '') : '' }}</p>
                                    </td>
                                    <td class="text-[12px] text-[#323232] font-[600] leading-[24px]">
                                        <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($overBudgetTotalArr[$month] ?? 0) }}</strong>
                                        <p class="{{ ($budgetTotal[$month] ?? 0) < ($overBudgetTotalArr[$month] ?? 0) ? 'green-text' : (($budgetTotal[$month] ?? 0) > ($overBudgetTotalArr[$month] ?? 0) ? 'red-text' : '') }}">
                                            {{ ($budgetTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $overBudgetTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetTotal[$month] ?? 0, $overBudgetTotalArr[$month] ?? 0) . '%)' : '') : '' }}
                                        </p>
                                    </td>
                                @endforeach
                            </tr>
                            
                            <tr>
                                <th colspan="37" class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                    <span class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{__('Expense :')}}</span>
                                </th>
                            </tr>
                            @php
                                $overExpenseBudgetTotal = [];
                            @endphp
                            @foreach ($expenseproduct as $productService)
                                <tr>
                                    <td class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ $productService->name }}</td>
                                    @foreach($yearly_monthlist as $month)
                                        @php
                                            $budgetAmount = isset($budget['expense_data'][$productService->id][$month]) ? $budget['expense_data'][$productService->id][$month] : 0;
                                            $actualAmount = $expenseArr[$productService->id][$month] ?? 0;
                                            $overBudgetAmount = $actualAmount - $budgetAmount;
                                            $overExpenseBudgetTotal[$productService->id][$month] = $overBudgetAmount;
                                        @endphp
                                        <td class="expense_data {{$month}}_expense">
                                            {{ \Auth::user()->priceFormat($budgetAmount) }}
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($actualAmount) }}
                                            <p>
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $actualAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $actualAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                        <td>
                                            {{ \Auth::user()->priceFormat($overBudgetAmount) }}
                                            <p class="{{ $budgetAmount < $overBudgetAmount ? 'green-text' : ($budgetAmount > $overBudgetAmount ? 'red-text' : '') }}">
                                                {{ $budgetAmount != 0 ? (\App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) != 0 ? '(' . \App\Models\Budget::percentage($budgetAmount, $overBudgetAmount) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            @php
                                $overExpenseBudgetTotalArr = [];
                                foreach($overExpenseBudgetTotal as $overExpenseBudget) {
                                    foreach($overExpenseBudget as $k => $value) {
                                        $overExpenseBudgetTotalArr[$k] = (isset($overExpenseBudgetTotalArr[$k]) ? $overExpenseBudgetTotalArr[$k] + $value : $value);
                                    }
                                }
                            @endphp
                            <tr class="total">
                                <td class="text-dark"><strong>{{__('Total :')}}</strong></td>
                                @foreach($yearly_monthlist as $month)
                                    <td class="text-[12px] text-[#323232] font-[600] leading-[24px] {{$month}}_total_expense">
                                        <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($budgetExpenseTotal[$month] ?? 0) }}</strong>
                                    </td>
                                    <td class="text-[12px] text-[#323232] font-[600] leading-[24px]">
                                        <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($expenseTotalArr[$month] ?? 0) }}</strong>
                                        <p>{{ ($budgetExpenseTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $expenseTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $expenseTotalArr[$month] ?? 0) . '%)' : '') : '' }}</p>
                                    </td>
                                    <td class="text-[12px] text-[#323232] font-[600] leading-[24px]">
                                        <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($overExpenseBudgetTotalArr[$month] ?? 0) }}</strong>
                                        <p class="{{ ($budgetExpenseTotal[$month] ?? 0) < ($overExpenseBudgetTotalArr[$month] ?? 0) ? 'green-text' : (($budgetExpenseTotal[$month] ?? 0) > ($overExpenseBudgetTotalArr[$month] ?? 0) ? 'red-text' : '') }}">
                                            {{ ($budgetExpenseTotal[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $overExpenseBudgetTotalArr[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetExpenseTotal[$month] ?? 0, $overExpenseBudgetTotalArr[$month] ?? 0) . '%)' : '') : '' }}
                                        </p>
                                    </td>
                                @endforeach
                            </tr>
                            <td></td>
                            <tfoot>
                                <tr class="total" style="background:#f8f9fd;">
                                    <td class="text-dark"><strong>{{__('Net Profit :')}}</strong></td>
                                    @php
                                        $overbudgetprofit = [];
                                        $keys = array_keys(array_merge($overBudgetTotalArr, $overExpenseBudgetTotalArr));
                                        foreach($keys as $v) {
                                            $overbudgetprofit[$v] = (empty($overBudgetTotalArr[$v]) ? 0 : $overBudgetTotalArr[$v]) - (empty($overExpenseBudgetTotalArr[$v]) ? 0 : $overExpenseBudgetTotalArr[$v]);
                                        }
                                        $data['overbudgetprofit'] = $overbudgetprofit;
                                    @endphp
                                    @foreach($yearly_monthlist as $month)
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($budgetprofit[$month] ?? 0) }}</strong>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($actualprofit[$month] ?? 0) }}</strong>
                                            <p>{{ ($budgetprofit[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $actualprofit[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $actualprofit[$month] ?? 0) . '%)' : '') : '' }}</p>
                                        </td>
                                        <td class="text-dark">
                                            <strong class="text-[12px] text-[#323232] font-[600] leading-[24px]">{{ \Auth::user()->priceFormat($overbudgetprofit[$month] ?? 0) }}</strong>
                                            <p class="{{ ($budgetprofit[$month] ?? 0) < ($overbudgetprofit[$month] ?? 0) ? 'green-text' : (($budgetprofit[$month] ?? 0) > ($overbudgetprofit[$month] ?? 0) ? 'red-text' : '') }}">
                                                {{ ($budgetprofit[$month] ?? 0) != 0 ? (\App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $overbudgetprofit[$month] ?? 0) != 0 ? '(' . \App\Models\Budget::percentage($budgetprofit[$month] ?? 0, $overbudgetprofit[$month] ?? 0) . '%)' : '') : '' }}
                                            </p>
                                        </td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection