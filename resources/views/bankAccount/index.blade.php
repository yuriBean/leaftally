@extends('layouts.admin') @section('page-title') {{ __('Manage Bank Account') }} @endsection @section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('Bank Account') }}</li>
@endsection @section('action-btn')
<div class="float-end">
    @can('create bank account')
    <a href="#" data-url="{{ route('bank-account.create') }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create New Bank Account') }}" class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        {{ __('Create Bank Account') }}
    </a>
    @endcan
</div>
@endsection @section('content')
<div class="row table-new-design">
    <div class="col-12">

        <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
            <div class="h-1 w-full" style="background:#007C38;"></div>
    
          <div class="card-body bg-white m-4">
            <div class="table-responsive">
                <table class="table datatable min-w-full text-sm text-left">
                    <thead class="bg-[#F6F6F6] text-[#323232] font-600 text-[12px] leading-[24px]">
                        <tr>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">{{ __('Chart of account') }}</th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('Name') }}</th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('Bank') }}</th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('Account number') }}</th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('Current balance') }}</th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('Contact number') }}</th>
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]"> {{ __('Bank branch') }}</th>
                            @if (Gate::check('edit bank account') || Gate::check('delete bank account'))
                            <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]" width="10%"> {{ __('Action') }}</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($accounts as $account)
                        <tr class="font-style">
                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ !empty($account->chartAccount) ? $account->chartAccount->name : '-' }}</td>
                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->holder_name }}</td>
                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->bank_name }}</td>
                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->account_number }}</td>
                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($account->opening_balance ?? '-') }}</td>
                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->contact_number ?? '-' }}</td>
                            <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $account->bank_address ?? '-' }}</td>
                            @if (Gate::check('edit bank account') || Gate::check('delete bank account'))
                            <td class="Action px-4 py-3 border relative border-[#E5E5E5] text-gray-700">
                                <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 cursor-pointer" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                                    @if ($account->holder_name != 'Cash') @can('edit bank account')

                                    <a href="#" class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]" data-url="{{ route('bank-account.edit', $account->id) }}" data-ajax-popup="true" title="{{ __('Edit') }}" data-title="{{ __('Edit Bank Account') }}"
                                    data-bs-toggle="tooltip" data-size="lg" data-original-title="{{ __('Edit') }}">
                                        <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}" alt="edit" />
                                        <span>{{ __('Edit') }}</span>
                                    </a>

                                    @endcan @can('delete bank account') {!! Form::open([ 'method' => 'DELETE', 'route' => ['bank-account.destroy', $account->id], 'id' => 'delete-form-' . $account->id, ]) !!}
                                    <a href="#" class="dropdown-item bs-pass-para flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]" data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-original-title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                    data-confirm-yes="document.getElementById('delete-form-{{ $account->id }}').submit();">
                                        <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}" alt="delete" />
                                        <span>{{ __('Delete') }}</span>
                                    </a>
                                    {!! Form::close() !!} @endcan @else - @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div></div>

    </div>
</div>
@endsection