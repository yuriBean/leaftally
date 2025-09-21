@extends('layouts.admin')

@section('page-title')
  {{ __('Manage Payments') }}
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  <li class="breadcrumb-item">{{ __('Payment') }}</li>
@endsection

@php
  $date = request('date', 0);
@endphp

@section('action-btn')
<style>
  /* material checkbox */
  .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
  .mcheck input{position:absolute;opacity:0;width:0;height:0}
  .mcheck .box{width:20px;height:20px;border:2px solid #D1D5DB;border-radius:6px;background:#fff;display:inline-block;position:relative;transition:all .15s}
  .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
  .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
  .mcheck input:checked + .box{background:#007C38;border-color:#007C38}
  .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid #fff;border-top:none;border-left:none;transform:rotate(45deg)}
</style>

<div class="flex items-center gap-2 mt-2 sm:mt-0">
  {{-- Export ALL --}}
  <a href="{{ route('payment.export', $date) }}"
     data-bs-toggle="tooltip" title="{{ __('Export') }}"
     style="border: 1px solid #007C38 !important"
     class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit">
     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
     </svg>
     {{ __('Export') }}
  </a>

  @can('create payment')
    <a href="#"
       data-url="{{ route('payment.create') }}"
       data-ajax-popup="true"
       data-bs-toggle="tooltip"
       data-size="lg"
       data-title="{{ __('Create New Payment') }}"
       title="{{ __('Create') }}"
       class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      {{ __('Create New') }}
    </a>
  @endcan
</div>
@endsection

@section('content')
<div class="row">
  <div class="col-sm-12">
    <div class="multi-collapse mt-2" id="multiCollapseExample1">
      <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
        <div class="h-1 w-full" style="background:#007C38;"></div>
          <div class="card-body">
          {{ Form::open(['route' => ['payment.index'], 'method' => 'GET', 'id' => 'payment_form']) }}
          <div class="form-space-fix row d-flex align-items-center">
            <div class="col-md-10 col-12">
              <div class="row">
                <div class="col-md-3 col-sm-12 col-12">
                  <div class="btn-box">
                    {{ Form::label('date', __('Date'), ['class' => 'text-type block text-sm font-medium text-gray-700 mb-2']) }}
                    {{ Form::text('date', request('date', date('Y-m-d')), ['class' => 'form-control appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full month-btn', 'id' => 'pc-daterangepicker-1']) }}
                  </div>
                </div>
                <div class="col-md-3 col-sm-12 col-12">
                  <div class="btn-box">
                    {{ Form::label('account', __('Account'), ['class' => 'text-type block text-sm font-medium text-gray-700 mb-2']) }}
                    {{ Form::select('account', $account, request('account',''), ['class' => 'form-control select appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full', 'id' => 'choices-multiple']) }}
                  </div>
                </div>
                <div class="col-md-3 col-sm-12 col-12">
                  <div class="btn-box">
                    {{ Form::label('vender', __('Vendor'), ['class' => 'text-type block text-sm font-medium text-gray-700 mb-2']) }}
                    {{ Form::select('vender', $vender, request('vender',''), ['class' => 'form-control select appearance-none bg-white border border-[#E5E5E5] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full', 'id' => 'choices-multiple1']) }}
                  </div>
                </div>
                <div class="col-md-3 col-sm-12 col-12">
                  <div class="btn-box">
                    {{ Form::label('category', __('Category'), ['class' => 'text-type block text-sm font-medium text-gray-700 mb-2']) }}
                    {{ Form::select('category', $category, request('category',''), ['class' => 'form-control select appearance-none bg-white border border-[#E5E5E5] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full', 'id' => 'choices-multiple2']) }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2 col-12">
              <div class="col-auto d-flex justify-content-end mt-4">
                <div class="col-auto">
                  <a href="#" class="btn btn-sm btn-primary me-2"
                     onclick="document.getElementById('payment_form').submit(); return false;"
                     data-bs-toggle="tooltip" title="{{ __('Apply') }}">
                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                  </a>
                  <a href="{{ route('payment.index') }}" class="btn btn-sm btn-danger"
                     data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                    <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off "></i></span>
                  </a>
                </div>
              </div>
            </div>
          </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>

    {{-- Bulk toolbar (appears when rows are selected) --}}
    @can('delete payment')
      <x-bulk-toolbar
        :deleteRoute="route('payment.bulk-destroy')"
        :exportRoute="route('payment.export-selected')"
        scope="payments"
        tableId="payments-table"
        selectedLabel="{{ __('Payments selected') }}"
      />
    @endcan
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
      <div class="h-1 w-full" style="background:#007C38;"></div>

    <div class="card-body bg-white m-4">
      <div class="table-responsive table-new-design">
        <table id="payments-table" class="table datatable">
          <thead>
            <tr>
              {{-- master checkbox --}}
              <th data-sortable="false" data-type="html"
                  class="input-checkbox border border-[#E5E5E5] px-4 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                <label class="mcheck">
                  <input type="checkbox" class="jsb-master" data-scope="payments">
                  <span class="box"></span>
                </label>
              </th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Date') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Amount') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Account') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Vendor') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Category') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Reference') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Description') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Payment Receipt') }}</th>
              @if (Gate::check('edit payment') || Gate::check('delete payment'))
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Action') }}</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @foreach ($payments as $payment)
              @php $paymentpath = \App\Models\Utility::get_file('uploads/payment'); @endphp
              <tr class="font-style">
                {{-- row checkbox --}}
                <td class="input-checkbox px-4 py-4 text-left text-[12px] border-0 w-12">
                  <label class="mcheck">
                    <input type="checkbox" class="jsb-item" data-scope="payments" value="{{ $payment->id }}" data-id="{{ $payment->id }}">
                    <span class="box"></span>
                  </label>
                </td>

                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ Auth::user()->dateFormat($payment->date) }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ Auth::user()->priceFormat($payment->amount) }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  {{ !empty($payment->bankAccount) ? $payment->bankAccount->bank_name . ' ' . $payment->bankAccount->holder_name : '' }}
                </td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ !empty($payment->vender) ? $payment->vender->name : '-' }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ !empty($payment->category) ? $payment->category->name : '-' }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $payment->reference ?: '-' }}</td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">{{ $payment->description ?: '-' }}</td>

                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  @if (!empty($payment->add_receipt))
                    <div class="action-btn me-2">
                      <a href="{{ $paymentpath . '/' . $payment->add_receipt }}"
                         class="mx-3 btn btn-sm align-items-center bg-primary d-inline-flex justify-content-center"
                         download data-bs-toggle="tooltip" title="{{ __('Download') }}">
                        <span><i class="ti ti-download text-white"></i></span>
                      </a>
                    </div>
                    <div class="action-btn">
                      <a href="{{ $paymentpath . '/' . $payment->add_receipt }}"
                         class="mx-3 btn btn-sm align-items-center bg-secondary d-inline-flex justify-content-center"
                         data-bs-toggle="tooltip" title="{{ __('Preview') }}" target="_blank">
                        <span><i class="ti ti-crosshair text-white"></i></span>
                      </a>
                    </div>
                  @else
                    -
                  @endif
                </td>

                @if (Gate::check('edit payment') || Gate::check('delete payment'))
                  <td class="action px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                            type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                      @can('edit payment')
                        <li>
                          <a href="#"
                             class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                             data-url="{{ route('payment.edit', $payment->id) }}"
                             data-ajax-popup="true" data-title="{{ __('Edit Payment') }}"
                             data-size="lg" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                            <i class="ti ti-pencil"></i><span>{{ __('Edit') }}</span>
                          </a>
                        </li>
                      @endcan

                      @can('delete payment')
                        <li>
                          {!! Form::open(['method' => 'DELETE','route' => ['payment.destroy', $payment->id],'id' => 'delete-form-' . $payment->id]) !!}
                            <a href="#"
                               class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                               data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                               data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                               data-confirm-yes="document.getElementById('delete-form-{{ $payment->id }}').submit();">
                              <i class="ti ti-trash"></i><span>{{ __('Delete') }}</span>
                            </a>
                          {!! Form::close() !!}
                        </li>
                      @endcan
                    </div>
                  </td>
                @endif
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>
</div>
@endsection

@push('script-page')
<script>
  // stop row click when interacting with controls (if you add row navigation later)
  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){ e.stopPropagation(); });

  // Export Selected (uses <x-bulk-toolbar/> localStorage bucket)
  $(document).on('click','[data-export-selected][data-scope="payments"]',function(e){
    e.preventDefault();
    const scope = $(this).data('scope');   // "payments"
    const route = $(this).data('route');   // export-selected route
    const key   = 'bulk:'+scope;

    let ids = [];
    try { ids = JSON.parse(localStorage.getItem(key) || '[]'); } catch(e) {}

    if(!ids.length){
      if (window.Swal) {
        Swal.fire({ icon:'info', title:'{{ __('No selection') }}', text:'{{ __('Please select at least one row.') }}' });
      } else {
        alert('{{ __('Please select at least one row.') }}');
      }
      return;
    }

    const token = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
    const $f = $('<form>', { method:'POST', action:route, target:'_blank' });
    $f.append($('<input>',{type:'hidden', name:'_token', value:token}));
    ids.forEach(id => $f.append($('<input>',{type:'hidden', name:'ids[]', value:id})));
    $(document.body).append($f);
    $f.trigger('submit').remove();
  });
</script>
@endpush
