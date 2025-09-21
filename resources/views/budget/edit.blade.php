<script src="{{ asset('js/unsaved.js') }}"></script>

@extends('layouts.admin')
@section('page-title')
    {{ __('Edit Budget Planner') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('budget.index') }}">{{ __('Budget Planner') }}</a></li>
    <li class="breadcrumb-item">{{ $budget->name }}</li>
@endsection
@push('script-page')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            $( ".income_data" ).each(function( index ) {
                var el = $(this).parent().parent();

                var inputs = $(el.find('.income_data'));

                // console.log(inputs);
                var totalincome = 0;
                for (var i = 0; i < inputs.length; i++) {
                    var price = $(inputs[i]).val();
                    totalincome = parseFloat(totalincome) + parseFloat(price);
                }
                el.find('.totalIncome').html(totalincome);

                // month wise total //
                var month_income = $(this).data('month');
                var month_inputs = $(el.parent().find('.' + month_income + '_income'));
                var month_totalincome = 0;
                for (var i = 0; i < month_inputs.length; i++) {
                    var month_price = $(month_inputs[i]).val();
                    month_totalincome = parseFloat(month_totalincome) + parseFloat(month_price);
                }
                var month_total_income = month_income + '_total_income';
                el.parent().find('.' + month_total_income).html(month_totalincome);

                //all total //
                var total_inputs = $(el.parent().find('.totalIncome'));

                var income = 0;
                for (var i = 0; i < total_inputs.length; i++) {
                    var price = $(total_inputs[i]).html();
                    income = parseFloat(income) + parseFloat(price);
                }
                el.parent().find('.income').html(income);

            });


            $( ".expense_data" ).each(function( index ) {
                var el = $(this).parent().parent();
                var inputs = $(el.find('.expense_data'));

            var totalexpense = 0;
            for (var i = 0; i < inputs.length; i++) {
                var price = $(inputs[i]).val();
                totalexpense = parseFloat(totalexpense) + parseFloat(price);
            }
            el.find('.totalExpense').html(totalexpense);

            // month wise total //
            var month_expense = $(this).data('month');
            var month_inputs = $(el.parent().find('.' + month_expense + '_expense'));
            var month_totalexpense = 0;
            for (var i = 0; i < month_inputs.length; i++) {
                var month_price = $(month_inputs[i]).val();
                month_totalexpense = parseFloat(month_totalexpense) + parseFloat(month_price);
            }
            var month_total_expense = month_expense + '_total_expense';
            el.parent().find('.' + month_total_expense).html(month_totalexpense);

            //all total //
            var total_inputs = $(el.parent().find('.totalExpense'));
            var expense = 0;
            for (var i = 0; i < total_inputs.length; i++) {
                var price = $(total_inputs[i]).html();
                expense = parseFloat(expense) + parseFloat(price);
            }
            el.parent().find('.expense').html(expense);
            })


        })

        // Income Total
        $(document).on('keyup', '.income_data', function() {
            //category wise total

            var el = $(this).parent().parent();
            console.log(el);
            var inputs = $(el.find('.income_data'));

            // console.log(inputs);
            var totalincome = 0;
            for (var i = 0; i < inputs.length; i++) {
                var price = $(inputs[i]).val();
                totalincome = parseFloat(totalincome) + parseFloat(price);
            }
            el.find('.totalIncome').html(totalincome);

            // month wise total //
            var month_income = $(this).data('month');
            var month_inputs = $(el.parent().find('.' + month_income + '_income'));
            var month_totalincome = 0;
            for (var i = 0; i < month_inputs.length; i++) {
                var month_price = $(month_inputs[i]).val();
                month_totalincome = parseFloat(month_totalincome) + parseFloat(month_price);
            }
            var month_total_income = month_income + '_total_income';
            el.parent().find('.' + month_total_income).html(month_totalincome);

            //all total //
            var total_inputs = $(el.parent().find('.totalIncome'));

            var income = 0;
            for (var i = 0; i < total_inputs.length; i++) {
                var price = $(total_inputs[i]).html();
                income = parseFloat(income) + parseFloat(price);
            }
            el.parent().find('.income').html(income);

        })


        //Expense Total
        $(document).on('keyup', '.expense_data', function() {
            //category wise total
            var el = $(this).parent().parent();
            var inputs = $(el.find('.expense_data'));

            var totalexpense = 0;
            for (var i = 0; i < inputs.length; i++) {
                var price = $(inputs[i]).val();
                totalexpense = parseFloat(totalexpense) + parseFloat(price);
            }
            el.find('.totalExpense').html(totalexpense);

            // month wise total //
            var month_expense = $(this).data('month');
            var month_inputs = $(el.parent().find('.' + month_expense + '_expense'));
            var month_totalexpense = 0;
            for (var i = 0; i < month_inputs.length; i++) {
                var month_price = $(month_inputs[i]).val();
                month_totalexpense = parseFloat(month_totalexpense) + parseFloat(month_price);
            }
            var month_total_expense = month_expense + '_total_expense';
            el.parent().find('.' + month_total_expense).html(month_totalexpense);

            //all total //
            var total_inputs = $(el.parent().find('.totalExpense'));
            var expense = 0;
            for (var i = 0; i < total_inputs.length; i++) {
                var price = $(total_inputs[i]).html();
                expense = parseFloat(expense) + parseFloat(price);
            }
            el.parent().find('.expense').html(expense);

        })
        // Hide & Show
        $(document).on('change', '.period', function() {
            var period = $(this).val();

            $('.budget_plan').removeClass('d-block');
            $('.budget_plan').addClass('d-none');
            $('#' + period).removeClass('d-none');
            $('#' + period).addClass('d-block');
        });

        // trigger
        $('.period').trigger('change');
    </script>
@endpush



@section('content')
    <div class="bg-white border border-[#E5E5E5] rounded-[8px] p-4 mt-4">
        <div class="card-body">
            {{ Form::model($budget, ['route' => ['budget.update', $budget->id], 'method' => 'PUT', 'class'=>'needs-validation','novalidate']) }}
            <div class="row">
                <input type="hidden" name="type" id="type" value="{{ csrf_token() }}">

                <div class="form-group col-md-4">
                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>


                <div class="form-group col-md-4">
                    {{ Form::label('period', __('Budget Period'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::select('period', $periods, null, ['class' => 'form-control select period', 'required' => 'required']) }}

                </div>

                <div class="form-group  col-md-4">
                    <div class="btn-box">
                        {{ Form::label('year', __('Year'), ['class' => 'form-label']) }}
                        {{ Form::select('year', $yearList, isset($_GET['year']) ? $_GET['year'] : '', ['class' => 'form-control select']) }}
                    </div>
                </div>




            </div>
        </div>
    </div>

    <div class="col-12">
         <div class="bg-white border border-[#E5E5E5] rounded-[8px] p-4 mt-4">
            <div class="card-body table-border-style bg-white p-4">

                <!---Start Monthly Budget ----->
                <div class="table-responsive" id="monthly">
                    <table class="table mb-0 border rounded-[4px] p-6 border-[#E5E5E5] budget_plan d-block" id="dataTable-manual">
                        <thead>
                            <tr class="border border-[#E5E5E5] bg-[#F6F6F6]">
                                <th class="px-4 py-1  font-[700] text-[12px]">{{ __('Category') }}</th>
                                @foreach ($monthList as $month)
                                    <td class="total text-dark">{{ __($month) }}</td>
                                @endforeach
                                <th class="px-4 py-1  font-[700] text-[12px]">{{ __('Total :') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!------------------   Income Category ----------------------------------->
                            <tr>
                                <th colspan="14" class="text-[#323232] text-[12px] font-[700] leading-[24px]"><span>{{ __('Income :') }}</span></th>
                            </tr>

                            @foreach ($incomeproduct as $productService)
                                <tr>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $productService->name }}</td>

                                    @foreach ($monthList as $month)
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                            <input type="number"
                                                class="form-control pl-1 pr-1 income_data {{ $month }}_income"
                                                data-month="{{ $month }}"
                                                name="income[{{ $productService->id }}][{{ $month }}]"
                                                value="{{ !empty($budget['income_data'][$productService->id][$month]) ? $budget['income_data'][$productService->id][$month] : 0 }}"
                                                id="income_data_{{ $month }}">
                                        </td>
                                    @endforeach
                                    <td class="totalIncome text-dark">
                                        0.00
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td class="text-dark total_income">{{ __('Total :') }}</td>
                                @foreach ($monthList as $month)
                                    <td class="text-gray-700">
                                        <span class="{{ $month }}_total_income px-4 py-3 border border-[#E5E5E5] ">0.00</span>
                                    </td>
                                @endforeach
                                <td class="text-gray-700">
                                    <span class="income text-dark px-4 py-3 border border-[#E5E5E5] ">0.00</span>
                                </td>
                            </tr>

                            <!------------------   Expense Category ----------------------------------->

                            <tr>
                                <th colspan="14" class="text-[#323232] text-[12px] font-[700] leading-[24px]"><span>{{ __('Expense :') }}</span></th>
                            </tr>

                            @foreach ($expenseproduct as $productService)
                                <tr>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $productService->name }}</td>
                                    @foreach ($monthList as $month)
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                            <input type="number"
                                                class="form-control pl-1 pr-1 expense_data {{ $month }}_expense"
                                                data-month="{{ $month }}"
                                                name="expense[{{ $productService->id }}][{{ $month }}]"
                                                value="{{ !empty($budget['expense_data'][$productService->id][$month]) ? $budget['expense_data'][$productService->id][$month] : 0 }}"
                                                id="expense_data_{{ $month }}">
                                        </td>
                                    @endforeach
                                    <td class="totalExpense text-dark">
                                        0.00
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td class="text-dark">{{ __('Total :') }}</span></td>
                                @foreach ($monthList as $month)
                                    <td class="text-gray-700">
                                        <span class="{{ $month }}_total_expense text-dark px-4 rounded-[8px] py-3 border border-[#E5E5E5]">0.00</span>
                                    </td>
                                @endforeach
                                <td class="text-gray-700">
                                    <span class="expense text-dark px-4 py-3 border border-[#E5E5E5] ">0.00</span>
                                </td>

                            </tr>

                        </tbody>

                    </table>

                    <div class="modal-footer budget">
                        <input type="button" value="{{ __('Cancel') }}"
                            onclick="location.href = '{{ route('budget.index') }}';" class="btn btn-light">
                        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
                    </div>
                </div>
                <!---End Monthly Budget ----->

                <!---- Start Quarterly Budget ----->
                <div class="table-responsive budget_plan d-none" id="quarterly">
                    <table class="table border rounded-[4px] border-[#E5E5E5] p-6 mb-0" id="dataTable-manual">
                        <thead>
                            <tr class="border border-[#E5E5E5] bg-[#F6F6F6]">
                                <th class="px-4 py-1  font-[700] text-[12px]">{{ __('Category') }}</th>
                                @foreach ($quarterly_monthlist as $month)
                                    <td class="total text-dark">{{ $month }}</td>
                                @endforeach
                                <th class="px-4 py-1  font-[700] text-[12px]">{{ __('Total :') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!------------------   Income Category ----------------------------------->
                            <tr>
                                <th colspan="14" class="text-[#323232] text-[12px] font-[700] leading-[24px]"><span>{{ __('Income :') }}</span></th>
                            </tr>

                            @foreach ($incomeproduct as $productService)
                                <tr>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $productService->name }}</td>

                                    @foreach ($quarterly_monthlist as $month)
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                            <input type="number"
                                                class="form-control income_data {{ $month }}_income"
                                                data-month="{{ $month }}"
                                                name="income[{{ $productService->id }}][{{ $month }}]"
                                                value="{{ !empty($budget['income_data'][$productService->id][$month]) ? $budget['income_data'][$productService->id][$month] : 0 }}"
                                                id="income_data_{{ $month }}">
                                        </td>
                                    @endforeach
                                    <td class="text-end totalIncome text-dark">
                                        0.00
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td class="text-dark">{{ __('Total :') }}</td>
                                @foreach ($quarterly_monthlist as $month)
                                    <td class="text-gray-700">
                                        <span class="{{ $month }}_total_income text-dark px-4 rounded-[8px] py-3 border border-[#E5E5E5]">0.00</span>
                                    </td>
                                @endforeach
                                <td class="text-end">
                                    <span class="income text-dark">0.00</span>
                                </td>
                            </tr>

                            <!------------------   Expense Category ----------------------------------->

                            <tr>
                                <th colspan="14" class="text-[#323232] text-[12px] font-[700] leading-[24px]"><span>{{ __('Expense :') }}</span></th>
                            </tr>

                            @foreach ($expenseproduct as $productService)
                                <tr>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $productService->name }}</td>
                                    @foreach ($quarterly_monthlist as $month)
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                            <input type="number"
                                                class="form-control expense_data {{ $month }}_expense"
                                                data-month="{{ $month }}"
                                                name="expense[{{ $productService->id }}][{{ $month }}]"
                                                value="{{ !empty($budget['expense_data'][$productService->id][$month]) ? $budget['expense_data'][$productService->id][$month] : 0 }}"
                                                id="expense_data_{{ $month }}">
                                        </td>
                                    @endforeach
                                    <td class="text-end totalExpense text-dark">
                                        0.00
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td class="text-dark">{{ __('Total :') }}</span></td>
                                @foreach ($quarterly_monthlist as $month)
                                    <td class="text-gray-700">
                                        <span class="{{ $month }}_total_expense text-dark px-4 rounded-[8px] py-3 border border-[#E5E5E5]">0.00</span>
                                    </td>
                                @endforeach
                                <td class="text-end">
                                    <span class="expense text-dark">0.00</span>
                                </td>

                            </tr>

                        </tbody>

                    </table>
                    <div class="modal-footer budget">
                        <input type="button" value="{{ __('Cancel') }}"
                            onclick="location.href = '{{ route('budget.index') }}';" class="btn btn-light">
                        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary" >
                    </div>
                </div>

                <!---- End Quarterly Budget ----->



                <!---Start Half-Yearly Budget ----->
                <div class="table-responsive d-none" id="half-yearly">
                    <table class="table border rounded-[4px] border-[#E5E5E5] p-6 mb-0" id="dataTable-manual">
                        <thead>
                            <tr class="border border-[#E5E5E5] bg-[#F6F6F6]">
                                <th class="px-4 py-1  font-[700] text-[12px]">{{ __('Category') }}</th>
                                @foreach ($half_yearly_monthlist as $month)
                                    <td class="total text-dark">{{ $month }}</td>
                                @endforeach
                                <th class="px-4 py-1  font-[700] text-[12px]">{{ __('Total :') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!------------------   Income Category ----------------------------------->
                            <tr>
                                <th colspan="14" class="text-[#323232] text-[12px] font-[700] leading-[24px]"><span>{{ __('Income :') }}</span></th>
                            </tr>

                            @foreach ($incomeproduct as $productService)
                                <tr>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $productService->name }}</td>

                                    @foreach ($half_yearly_monthlist as $month)
                                        <td class="px-4 py-3  text-gray-700">
                                            <input type="number"
                                                class="border border-[#E5E5E5] rounded-[8px] form-control income_data {{ $month }}_income"
                                                data-month="{{ $month }}"
                                                name="income[{{ $productService->id }}][{{ $month }}]"
                                                value="{{ !empty($budget['income_data'][$productService->id][$month]) ? $budget['income_data'][$productService->id][$month] : 0 }}"
                                                id="income_data_{{ $month }}">
                                        </td>
                                    @endforeach
                                    <td class="text-end totalIncome text-dark">
                                        0.00
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td class="text-dark">{{ __('Total :') }}</td>
                                @foreach ($half_yearly_monthlist as $month)
                                    <td class="text-gray-700">
                                        <span class="border border-[#E5E5E5] rounded-[8px] {{ $month }}_total_income text-dark px-4 rounded-[8px] py-3 border border-[#E5E5E5] ">0.00</span>
                                    </td>
                                @endforeach
                                <td class="text-end">
                                    <span class="income text-dark">0.00</span>
                                </td>
                            </tr>

                            <!------------------   Expense Category ----------------------------------->

                            <tr>
                                <th colspan="14" class="text-[#323232] text-[12px] font-[700] leading-[24px]"><span>{{ __('Expense :') }}</span></th>
                            </tr>

                            @foreach ($expenseproduct as $productService)
                                <tr>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $productService->name }}</td>
                                    @foreach ($half_yearly_monthlist as $month)
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                            <input type="number"
                                                class="border border-[#E5E5E5] rounded-[8px] form-control expense_data {{ $month }}_expense"
                                                data-month="{{ $month }}"
                                                name="expense[{{ $productService->id }}][{{ $month }}]"
                                                value="{{ !empty($budget['expense_data'][$productService->id][$month]) ? $budget['expense_data'][$productService->id][$month] : 0 }}"
                                                id="expense_data_{{ $month }}">
                                        </td>
                                    @endforeach
                                    <td class="text-end totalExpense text-dark">
                                        0.00
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td class="text-dark">{{ __('Total :') }}</span></td>
                                @foreach ($half_yearly_monthlist as $month)
                                    <td class="text-gray-700">
                                        <span class="border border-[#E5E5E5] rounded-[8px] {{ $month }}_total_expense px-4 py-3 border border-[#E5E5E5] ">0.00</span>
                                    </td>
                                @endforeach
                                <td class="text-end">
                                    <span class="expense text-dark">0.00</span>
                                </td>

                            </tr>

                        </tbody>

                    </table>
                    <div class="modal-footer budget">
                        <input type="button" value="{{ __('Cancel') }}"
                            onclick="location.href = '{{ route('budget.index') }}';" class="btn btn-light">
                        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary" >
                    </div>
                </div>

                <!---End Half-Yearly Budget ----->


                <!---Start Yearly Budget ----->
                <div class="table-responsive  budget_plan d-none" id="yearly">
                    <table class="table border rounded-[4px] border-[#E5E5E5] p-6 mb-0" id="dataTable-manual">
                        <thead>
                            <tr class="border border-[#E5E5E5] bg-[#F6F6F6]">
                                <th class="px-4 py-1  font-[700] text-[12px]">{{ __('Category') }}</th>
                                @foreach ($yearly_monthlist as $month)
                                    <td class="total text-dark">{{ $month }}</td>
                                @endforeach
                                <th class="px-4 py-1  font-[700] text-[12px]">{{ __('Total :') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!------------------   Income Category ----------------------------------->
                            <tr>
                                <th colspan="14" class="text-[#323232] text-[12px] font-[700] leading-[24px]"><span>{{ __('Income :') }}</span></th>
                            </tr>

                            @foreach ($incomeproduct as $productService)
                                <tr>
                                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $productService->name }}</td>

                                    @foreach ($yearly_monthlist as $month)
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                            <input type="number"
                                                class="form-control income_data {{ $month }}_income"
                                                data-month="{{ $month }}"
                                                name="income[{{ $productService->id }}][{{ $month }}]"
                                                value="{{ !empty($budget['income_data'][$productService->id][$month]) ? $budget['income_data'][$productService->id][$month] : 0 }}"
                                                id="income_data_{{ $month }}">
                                        </td>
                                    @endforeach
                                    <td class="text-end totalIncome text-dark">
                                        0.00
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td class="text-dark">{{ __('Total :') }}</td>
                                @foreach ($yearly_monthlist as $month)
                                    <td class="text-gray-700">
                                        <span class="{{ $month }}_total_income text-dark px-4 py-3 border border-[#E5E5E5] rounded-[8px]">0.00</span>
                                    </td>
                                @endforeach
                                <td class="text-end">
                                    <span class="income text-dark">0.00</span>
                                </td>
                            </tr>

                            <!------------------   Expense Category ----------------------------------->

                            <tr>
                                <th colspan="14" class="text-[#323232] text-[12px] font-[700] leading-[24px]"><span>{{ __('Expense :') }}</span></th>
                            </tr>

                            @foreach ($expenseproduct as $productService)
                                <tr>
                                    <td class="text-gray-700">{{ $productService->name }}</td>
                                    @foreach ($yearly_monthlist as $month)
                                        <td class="text-gray-700">
                                            <input type="number"
                                                class="form-control expense_data {{ $month }}_expense"
                                                data-month="{{ $month }}"
                                                name="expense[{{ $productService->id }}][{{ $month }}]"
                                                value="{{ !empty($budget['expense_data'][$productService->id][$month]) ? $budget['expense_data'][$productService->id][$month] : 0 }}"
                                                id="expense_data_{{ $month }}">
                                        </td>
                                    @endforeach
                                    <td class="text-end totalExpense text-dark">
                                        0.00
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td class="text-dark">{{ __('Total :') }}</span></td>
                                @foreach ($yearly_monthlist as $month)
                                    <td class="text-gray-700">
                                        <span class="{{ $month }}_total_expense text-dark px-4 rounded-[8px] py-3 border border-[#E5E5E5]rounded-[8px]">0.00</span>
                                    </td>
                                @endforeach
                                <td class="text-end">
                                    <span class="expense text-dark">0.00</span>
                                </td>

                            </tr>

                        </tbody>

                    </table>
                    <div class="modal-footer budget">
                        <input type="button" value="{{ __('Cancel') }}"
                            onclick="location.href = '{{ route('budget.index') }}';" class="btn btn-light">
                        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary" >
                    </div>
                </div>
                <!---End Yearly Budget ----->


            </div>
            {{ Form::close() }}
         </div>
    </div>
@endsection
