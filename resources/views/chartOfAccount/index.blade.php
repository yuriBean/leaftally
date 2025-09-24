@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Chart of Accounts') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Chart of Account') }}</li>
@endsection
@push('script-page')
    <script>
        $(document).on('change', '#sub_type', function() {
            $('.acc_check').removeClass('d-none');
            var type = $(this).val();
            $.ajax({
                url: '{{ route('charofAccount.subType') }}',
                type: 'POST',
                data: {
                    "type": type,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#parent').empty();
                    $.each(data, function(key, value) {
                        console.log(key, value);
                        $('#parent').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                }
            });
        });
        $(document).on('click', '#account', function() {
            const element = $('#account').is(':checked');
            $('.acc_type').addClass('d-none');
            if (element == true) {
                $('.acc_type').removeClass('d-none');
            } else {
                $('.acc_type').addClass('d-none');
            }
        });
    </script>
@endpush

@section('action-btn')
    <div class="d-flex">
        @can('create chart of account')
            <a href="#" data-url="{{ route('chart-of-account.create') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                data-size="lg" data-ajax-popup="true" data-title="{{ __('Create New Account') }}" class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('Create New') }}
            </a>
        @endcan
    </div>
@endsection
@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="mt-2" id="multiCollapseExample1">
            <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3" id="show_filter">
                    <div class="h-1 w-full" style="background:#007C38;"></div>
    
                <div class="card-body">
                    {{ Form::open(['route' => ['chart-of-account.index'], 'method' => 'GET', 'id' => 'report_bill_summary']) }}
                     <div class="form-space-fix row d-flex align-items-center">
                            <div class="col-md-10 col-12">
                                <div class="row">
                                <div class="col-md-4 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('start_date', __('Start Date'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                                        {{ Form::date('start_date', $filter['startDateRange'], ['class' => 'startDate form-control appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full']) }}
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('end_date', __('End Date'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                                        {{ Form::date('end_date', $filter['endDateRange'], ['class' => 'endDate form-control appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-12">
                        <div class="col-auto d-flex justify-content-end mt-4">
                                    <a href="#" class="btn btn-sm btn-primary mr-2"
                                        onclick="document.getElementById('report_bill_summary').submit(); return false;"
                                        data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                        data-original-title="{{ __('Apply') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                    </a>

                                    <a href="{{ route('chart-of-account.index') }}" class="btn btn-sm btn-danger "
                                        data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                        data-original-title="{{ __('Reset') }}">
                                        <span class="btn-inner--icon"><i
                                                class="ti ti-refresh text-white-off"></i></span>
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
    <div class="row">
        {{-- @dd($chartAccounts); --}}
        @foreach ($chartAccounts as $type => $accounts)
            <div class="col-md-12">
                <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
                    <div class="h-1 w-full" style="background:#007C38;"></div>
                    <div class="card-header">
                    <h5 class="h3">{{ $type }}</h5>
                </div>
                <div class="card-body">
                    <div class="p-4">
                    <div class="table-responsive table-new-design">
                        <table class="table datatable table-hover">
                            <thead>
                                <tr>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Code') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Name') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Type') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]" style="min-width:200px"> {{ __('Parent Account Name') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Balance') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Status') }}</th>
                                    <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]"> {{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($accounts as $account)
                                    @php
                                        $balance = 0;
                                        $totalDebit = 0;
                                        $totalCredit = 0;
                                        $totalBalance = App\Models\Utility::getAccountBalance($account->id, $filter['startDateRange'], $filter['endDateRange']);
                                    @endphp

                                    <tr>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->code }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700"><a
                                                href="{{ route('report.ledger') }}?account={{ $account->id }}">{{ $account->name }}</a>
                                        </td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ !empty($account->subType) ? $account->subType->name : '-' }}</td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ !empty($account->parentAccount) ? $account->parentAccount->name : '-' }}
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                            @if (!empty($totalBalance))
                                                {{ \Auth::user()->priceFormat($totalBalance) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                            @if ($account->is_enabled == 1)
                                                <span
                                                    class="text-[#509A16] border rounded-full text-[12px] font-[500] leading-[24px] border-[#509A16] px-3 ">{{ __('Enabled') }}</span>
                                            @else
                                                <span
                                                    class="badge bg-danger p-2 px-3 ">{{ __('Disabled') }}</span>
                                            @endif
                                        </td>
                                        <td class="Action relative px-4 py-3 border border-[#E5E5E5] text-gray-700">
                                           
                                           <button
                                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                                        type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                            <div
                                        class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                           
                                                <a href="{{ route('report.ledger') }}?account={{ $account->id }}"
                                                    class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]" data-bs-toggle="tooltip"
                                                    title="{{ __('Transaction Summary') }}"
                                                    data-original-title="{{ __('Ledger Summary') }}">
                                                    <i class="ti ti-wave-sine text-white"></i>
                                                    <span>{{ __('Transaction Summary') }}</span>
                                                </a>
                                            
                                            @can('edit chart of account')
                                               
                                                    <a class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                        data-url="{{ route('chart-of-account.edit', $account->id) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Edit Account') }}"
                                                        data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                        data-original-title="{{ __('Edit') }}">
                                                         <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}"
                                                        alt="edit" />
                                                    <span>{{ __('Edit') }}</span>
                                                    </a>
                                                
                                            @endcan
                                            @can('delete chart of account')
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['chart-of-account.destroy', $account->id],
                                                        'id' => 'delete-form-' . $account->id,
                                                    ]) !!}
                                                    <a href="#"
                                                        class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                        data-original-title="{{ __('Delete') }}"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $account->id }}').submit();">
                                                       <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}"
                                                        alt="delete" />
                                                    <span>{{ __('Delete') }}</span>
                                                    </a>
                                                    {!! Form::close() !!}
                                                
                                            @endcan
                                        </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
