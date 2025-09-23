@extends('layouts.admin')

@section('page-title')
  {{ __('Contract') }}
@endsection

@section('action-btn')
<style>
  .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
  .mcheck input{position:absolute;opacity:0;width:0;height:0}
  .mcheck .box{width:20px;height:20px;border:2px solid
  .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
  .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
  .mcheck input:checked + .box{background:
  .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid
</style>

<div class="flex items-center gap-2 mt-2 sm:mt-0">
  {{-- Export All --}}
  <a href="{{ route('contract.export') }}"
     data-bs-toggle="tooltip" title="{{ __('Export') }}"
     style="border: 1px solid #007C38 !important"
     class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
    </svg>
    {{ __('Export') }}
  </a>

  {{-- Create --}}
  @can('create contract')
    <a href="#"
       data-url="{{ route('contract.create') }}"
       data-bs-toggle="tooltip" title="{{ __('Create') }}"
       data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Contract') }}"
       class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      {{ __('Create New') }}
    </a>
  @endcan
</div>
@endsection

@section('breadcrumb')
  @if (\Auth::guard('customer')->check())
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></li>
  @else
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  @endif
  <li class="breadcrumb-item active" aria-current="page">{{ __('Contract') }}</li>
@endsection

@section('content')

  {{-- KPI cards (unchanged structure, condensed icons for brevity) --}}
  <div class="flex space-x-4 justify-between mt-4 contract-pg-design">
    <div class="bg-white rounded-[4px] shadow-md px-4 py-6 flex items-center space-x-4 border border-[#E5E5E5]">
      <div class="bg-[#ebf3ef] p-3 rounded-[4px]"><svg width="20" height="20"><rect width="20" height="20" fill="transparent"/></svg></div>
      <div class="ml-2">
        <p class="text-[#727272] text-[10px] font-[600] leading-[24px]">{{ __('TOTAL') }}</p>
        <p class="font-[600] leading-[18px] text-[12px] text-[#323232]">{{ __('Contracts') }}</p>
      </div>
      <div class="ml-auto"><p class="text-[#323232] text-[20px] font-[600] leading-[24px]">{{ $cnt_contract['total'] }}</p></div>
    </div>

    <div class="bg-white rounded-[4px] shadow-md px-4 py-6 flex items-center space-x-4 border border-[#E5E5E5]">
      <div class="bg-[#ebf3ef] p-3 rounded-[4px]"><svg width="20" height="20"><rect width="20" height="20" fill="transparent"/></svg></div>
      <div class="ml-2">
        <p class="text-[#727272] text-[10px] font-[600] leading-[24px]">{{ __('This Month') }}</p>
        <p class="font-[600] leading-[18px] text-[12px] text-[#323232]">{{ __('Contracts') }}</p>
      </div>
      <div class="ml-auto"><p class="text-[#323232] text-[20px] font-[600] leading-[24px]">{{ $cnt_contract['this_month'] }}</p></div>
    </div>

    <div class="bg-white rounded-[4px] shadow-md px-4 py-6 flex items-center space-x-4 border border-[#E5E5E5]">
      <div class="bg-[#ebf3ef] p-3 rounded-[4px]"><svg width="20" height="20"><rect width="20" height="20" fill="transparent"/></svg></div>
      <div class="ml-2">
        <p class="text-[#727272] text-[10px] font-[600] leading-[24px]">{{ __('This Week') }}</p>
        <p class="font-[600] leading-[18px] text-[12px] text-[#323232]">{{ __('Contracts') }}</p>
      </div>
      <div class="ml-auto"><p class="text-[#323232] text-[20px] font-[600] leading-[24px]">{{ $cnt_contract['this_week'] }}</p></div>
    </div>

    <div class="bg-white rounded-[4px] shadow-md px-4 py-6 flex items-center space-x-4 border border-[#E5E5E5]">
      <div class="bg-[#ebf3ef] p-3 rounded-[4px]"><svg width="20" height="20"><rect width="20" height="20" fill="transparent"/></svg></div>
      <div class="ml-2">
        <p class="text-[#727272] text-[10px] font-[600] leading-[24px]">{{ __('Last 30 days') }}</p>
        <p class="font-[600] leading-[18px] text-[12px] text-[#323232]">{{ __('Contracts') }}</p>
      </div>
      <div class="ml-auto"><p class="text-[#323232] text-[20px] font-[600] leading-[24px]">{{ $cnt_contract['last_30days'] }}</p></div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-xl-12">

      {{-- ✅ Use your existing toolbar component (same as Assets) --}}
      @can('delete contract')
        <x-bulk-toolbar
          :deleteRoute="route('contract.bulk-destroy')"
          :exportRoute="route('contract.export-selected')"
          scope="contracts"
          tableId="contracts-table"
          selectedLabel="{{ __('Contract(s) selected') }}"
        />
      @endcan

      <div class="card-body bg-white border border-[#E5E5E5] rounded-[8px] p-4">
        <div class="table-responsive table-new-design bg-white p-4">
          <table class="table datatable" id="contracts-table">
            <thead>
              <tr>
                {{-- master checkbox --}}
                <th data-sortable="false" data-type="html"
                    class="input-checkbox border border-[#E5E5E5] px-4 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                  <label class="mcheck">
                    <input type="checkbox" class="jsb-master" data-scope="contracts">
                    <span class="box"></span>
                  </label>
                </th>

                @if (\Auth::user()->can('show contract'))
                  <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('#') }}</th>
                @endif
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Subject') }}</th>
                @if (Gate::check('manage contract'))
                  <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Customer') }}</th>
                @endif
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Type') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Value') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Start Date') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('End Date') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Status') }}</th>
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Action') }}</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($contracts as $contract)
                <tr class="font-style">
                  {{-- row checkbox --}}
                  <td class="input-checkbox px-4 py-4 text-left text-[12px] border-0 w-12">
                    <label class="mcheck">
                      <input type="checkbox" class="jsb-item" data-scope="contracts" value="{{ $contract->id }}" data-id="{{ $contract->id }}">
                      <span class="box"></span>
                    </label>
                  </td>

                  @if (\Auth::user()->can('show contract'))
                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                      @if (\Auth::user()->type == 'company')
                        <a href="{{ route('contract.show', $contract->id) }}"
                           class="inline-flex items-center px-3 py-1 rounded-[6px] text-[13px] font-[600] bg-[#007C3810] text-[#007C38] border border-[#007C3820] hover:bg-[#007C3815] transition-all duration-200">
                          {{ \Auth::user()->contractNumberFormat($contract->id) }}
                        </a>
                      @else
                        <a href="{{ route('customer.contract.show', $contract->id) }}"
                           class="btn btn-outline-primary">
                          {{ \Auth::user()->contractNumberFormat($contract->id) }}
                        </a>
                      @endif
                    </td>
                  @endif

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $contract->subject }}</td>

                  @if (Gate::check('manage contract'))
                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ optional($contract->clients)->name }}</td>
                  @endif

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ optional($contract->types)->name }}</td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->priceFormat($contract->value) }}</td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->dateFormat($contract->start_date) }}</td>
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ \Auth::user()->dateFormat($contract->end_date) }}</td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    @if ($contract->edit_status == 'accept')
                      <span class="status_badge badge bg-primary p-2 px-3 fix_badge">{{ __('Accept') }}</span>
                    @elseif ($contract->edit_status == 'decline')
                      <span class="status_badge badge bg-danger p-2 px-3 fix_badge">{{ __('Decline') }}</span>
                    @else
                      <span class="status_badge badge bg-warning p-2 px-3 fix_badge">{{ __('Pending') }}</span>
                    @endif
                  </td>

                  <td class="Action px-4 relative py-3 border border-[#E5E5E5] text-gray-700">
                    <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                            type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                      @if ($contract->name != 'Cash')
                        @if (\Auth::user()->can('duplicate contract') && $contract->edit_status == 'accept')
                          <a href="#"
                             class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                             data-url="{{ route('contract.duplicate', $contract->id) }}"
                             data-ajax-popup="true" data-size="lg" title="{{ __('Duplicate') }}"
                             data-title="{{ __('Duplicate Contract') }}">
                            <i class="ti ti-copy text-sm"></i>
                            <span>{{ __('Duplicate') }}</span>
                          </a>
                        @endif

                        @if (\Auth::user()->type == 'company')
                          @can('show contract')
                            <a href="#"
                               class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                               data-url="{{ route('contract.show', $contract->id) }}"
                               data-ajax-popup="true" data-size="lg" title="{{ __('View') }}"
                               data-title="{{ __('View Contract') }}">
                              <img src="{{ asset('web-assets/dashboard/icons/preview.svg') }}" alt="view" />
                              <span>{{ __('View') }}</span>
                            </a>
                          @endcan
                        @else
                          @can('show contract')
                            <a href="#"
                               class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                               data-url="{{ route('customer.contract.show', $contract->id) }}"
                               data-ajax-popup="true" data-size="lg" title="{{ __('View') }}"
                               data-title="{{ __('View Contract') }}">
                              <img src="{{ asset('web-assets/dashboard/icons/preview.svg') }}" alt="view" />
                              <span>{{ __('View') }}</span>
                            </a>
                          @endcan
                        @endif

                        @can('edit contract')
                          <a href="#"
                             class="dropdown-item flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                             data-url="{{ route('contract.edit', $contract->id) }}"
                             data-ajax-popup="true" data-size="lg" title="{{ __('Edit') }}"
                             data-title="{{ __('Edit Contract') }}">
                            <img src="{{ asset('web-assets/dashboard/icons/action_icons/edit.svg') }}" alt="edit" />
                            <span>{{ __('Edit') }}</span>
                          </a>
                        @endcan

                        @can('delete contract')
                          {!! Form::open([
                            'method' => 'DELETE',
                            'route' => ['contract.destroy', $contract->id],
                            'id' => 'delete-form-' . $contract->id,
                          ]) !!}
                          <a href="#"
                             class="dropdown-item bs-pass-para flex text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812]"
                             data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                             data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                             data-confirm-yes="document.getElementById('delete-form-{{ $contract->id }}').submit();">
                            <img src="{{ asset('web-assets/dashboard/icons/action_icons/delete.svg') }}" alt="delete" />
                            <span>{{ __('Delete') }}</span>
                          </a>
                          {!! Form::close() !!}
                        @endcan
                      @else
                        <span class="dropdown-item text-center">-</span>
                      @endif
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
@endsection

@push('script-page')
<script>
  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){
    e.stopPropagation();
  });
</script>
@endpush
