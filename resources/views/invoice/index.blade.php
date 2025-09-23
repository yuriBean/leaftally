@extends('layouts.admin')

@section('page-title')
  {{ __('Manage Invoices') }}
@endsection

@section('breadcrumb')
  @if (\Auth::guard('customer')->check())
    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></li>
  @else
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
  @endif
  <li class="breadcrumb-item">{{ __('Invoice') }}</li>
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
  {{-- Import --}}
  <a href="#"
     data-size="md"
     data-bs-toggle="tooltip"
     title="{{ __('Import') }}"
     data-url="#"
     data-ajax-popup="true"
     data-title="{{ __('Import invoice CSV file') }}"
     style="border: 1px solid #007C38 !important"
     class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit d-none">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 712-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
      </svg>
      {{ __('Import') }}
  </a>

  {{-- Export dropdown: All / Selected --}}
    <a class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit" href="{{ route('invoice.export') }}"
             style="border: 1px solid #007C38 !important">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
      </svg>
      {{ __('Export') }}
    </a>

  @can('create invoice')
    <a href="{{ route('invoice.create', 0) }}"
       data-size="xl"
       data-bs-toggle="tooltip"
       title="{{ __('Create') }}"
       class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        {{ __('Create Invoice') }}
    </a>
  @endcan
</div>
@endsection

@section('content')
<div class="row ">
  <div class="col-md-12">

    {{-- Filters --}}
    <div class="multi-collapse mt-2" id="multiCollapseExample1">
      <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
        <div class="h-1 w-full" style="background:#007C38;"></div>
        <div class="card-body bg-white">
      @if (!\Auth::guard('customer')->check())
            {{ Form::open(['route' => ['invoice.index'], 'method' => 'GET', 'id' => 'frm_submit']) }}
          @else
            {{ Form::open(['route' => ['customer.invoice'], 'method' => 'GET', 'id' => 'frm_submit']) }}
          @endif
          <div class="form-space-fix row d-flex align-items-center">
            <div class="col-md-10 col-12">
               <div class="row">
            <div class="col-md-3 col-sm-12 col-12">
              <div class="btn-box">
                {{ Form::label('issue_date', __('Date'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                {{ Form::text('issue_date', request('issue_date', date('Y-m-d')), ['class' => 'form-control form-control block w-full pl-3 pr-3 py-2 border border-[#E5E7EB] rounded-[6px] bg-white text-[14px] placeholder-[#9CA3AF] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 month-btn', 'id' => 'pc-daterangepicker-1', 'placeholder' => 'YYYY-MM-DD']) }}
              </div>
            </div>

            @if (!\Auth::guard('customer')->check())
            <div class="col-md-4 col-sm-12 col-12">
              <div class="btn-box">
                  {{ Form::label('customer', __('Customer'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                  {{ Form::select('customer', $customer, request('customer',''), ['class' => 'form-control select appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full']) }}
                </div>
              </div>
            @endif

            <div class="col-md-4 col-sm-12 col-12">
              <div class="btn-box">
                {{ Form::label('status', __('Status'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                {{ Form::select('status', ['' => 'Select Status'] + $status, request('status',''), ['class' => 'form-control select appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full']) }}
              </div>
            </div>

            <div class="col-md-2 col-12">
              <div class="col-auto d-flex justify-content-end mt-8">
                <a href="#" class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                 onclick="document.getElementById('frm_submit').submit(); return false;">
                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
              </a>
              @if (!\Auth::guard('customer')->check())
                <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                  <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off"></i></span>
                </a>
              @else
                <a href="{{ route('customer.invoice') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                  <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off"></i></span>
                </a>
              @endif
            </div>
          </div>
          </div>
        </div>
      </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>

    @can('delete invoice')
      <x-bulk-toolbar
        :deleteRoute="route('invoice.bulk-destroy')"
        :exportRoute="route('invoice.export-selected')"
        scope="invoices"
        tableId="invoices-table"
        selectedLabel="{{ __('Invoice selected') }}"
      />
    @endcan

    <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
      <div class="h-1 w-full" style="background:#007C38;"></div>

    <div class="card-body table-border-style">
      <div class="table-responsive table-new-design bg-white p-4">
        <table id="invoices-table" class="table datatable border border-[#E5E5E5] rounded-[8px]">
          <thead>
            <tr>
              {{-- master checkbox (not sortable, html) --}}
              <th data-sortable="false" data-type="html"
                  class="input-checkbox border border-[#E5E5E5] px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                <label class="mcheck">
                  <input type="checkbox" class="jsb-master" data-scope="invoices">
                  <span class="box"></span>
                </label>
              </th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Invoice') }}</th>
              @if (!\Auth::guard('customer')->check())
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Customer') }}</th>
              @endif
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Issue Date') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Due Date') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Amount Due') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Status') }}</th>
              @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Action') }}</th>
              @endif
            </tr>
          </thead>

          <tbody>
            @foreach ($invoices as $invoice)
              <tr class="inv_tr"
                  data-url="{{ \Auth::guard('customer')->check() ? route('customer.invoice.show', \Crypt::encrypt($invoice->id)) : route('invoice.show', \Crypt::encrypt($invoice->id)) }}"
                  data-id="{{ $invoice->id }}">
                {{-- row checkbox --}}
                <td class="input-checkbox px-4 lg:px-6 py-4 text-left text-[12px] border-0 w-12">
                  <label class="mcheck">
                    <input type="checkbox"
                           class="jsb-item"
                           data-scope="invoices"
                           value="{{ $invoice->id }}"
                           data-id="{{ $invoice->id }}">
                    <span class="box"></span>
                  </label>
                </td>

                {{-- Invoice number --}}
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  @if (\Auth::guard('customer')->check())
                    <a href="{{ route('customer.invoice.show', \Crypt::encrypt($invoice->id)) }}"
                       class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                      {{ Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                    </a>
                  @else
                    <a href="{{ route('invoice.show', \Crypt::encrypt($invoice->id)) }}"
                       class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                      {{ Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                    </a>
                  @endif
                </td>

                {{-- Customer --}}
                @if (!\Auth::guard('customer')->check())
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ !empty($invoice->customer) ? $invoice->customer->name : '' }}
                  </td>
                @endif

                {{-- Dates --}}
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  {{ Auth::user()->dateFormat($invoice->issue_date) }}
                </td>
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  @if ($invoice->due_date < date('Y-m-d'))
                    <span class="text-danger">{{ \Auth::user()->dateFormat($invoice->due_date) }}</span>
                  @else
                    {{ \Auth::user()->dateFormat($invoice->due_date) }}
                  @endif
                </td>

                {{-- Amount Due --}}
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  {{ \Auth::user()->priceFormat($invoice->getDue()) }}
                </td>

                {{-- Status --}}
                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  @if ($invoice->status == 0)
                    <span class="badge fix_badges bg-secondary p-2 px-3">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                  @elseif($invoice->status == 1)
                    <span class="badge fix_badges bg-warning p-2 px-3">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                  @elseif($invoice->status == 2)
                    <span class="badge fix_badges bg-danger p-2 px-3">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                  @elseif($invoice->status == 3)
                    <span class="badge fix_badges bg-info p-2 px-3">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                  @elseif($invoice->status == 4)
                    <span class="badge fix_badges bg-primary p-2 px-3">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                  @endif
                </td>

                {{-- Actions --}}
                @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                  <td class="Action px-4 py-3 text-right relative border border-[#E5E5E5]">
                    <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                      @can('duplicate invoice')
                        <li>
                          {!! Form::open(['method' => 'get', 'route' => ['invoice.duplicate', $invoice->id]]) !!}
                            <a href="#"
                               class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm">
                              <i class="ti ti-copy"></i><span>{{ __('Duplicate') }}</span>
                            </a>
                          {!! Form::close() !!}
                        </li>
                      @endcan

                      @can('show invoice')
                        <li>
                          <a href="{{ \Auth::guard('customer')->check() ? route('customer.invoice.show', \Crypt::encrypt($invoice->id)) : route('invoice.show', \Crypt::encrypt($invoice->id)) }}"
                             class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm">
                            <i class="ti ti-eye"></i><span>{{ __('Show') }}</span>
                          </a>
                        </li>
                      @endcan

                      @can('edit invoice')
                        <li>
                          <a href="{{ route('invoice.edit', \Crypt::encrypt($invoice->id)) }}"
                             class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm">
                            <i class="ti ti-pencil"></i><span>{{ __('Edit') }}</span>
                          </a>
                        </li>
                      @endcan

                      @can('delete invoice')
                        <li>
                          {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.destroy', $invoice->id]]) !!}
                            <a href="#!" class="dropdown-item flex items-center bs-pass-para text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm">
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
  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){ e.stopPropagation(); });

  $(document).on('click', 'tr.inv_tr', function(){
    const url = $(this).data('url');
    if(url) window.location = url;
  });

  $(document).on('click','[data-export-selected][data-scope="invoices"]',function(e){
    e.preventDefault();
    const scope = $(this).data('scope');
    const route = $(this).data('route');
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
