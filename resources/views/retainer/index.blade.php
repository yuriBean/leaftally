@extends('layouts.admin')

@section('page-title')
  {{ __('Manage Retainers') }}
@endsection

@section('breadcrumb')
  @if (\Auth::guard('customer')->check())
    <li class="breadcrumb-item">
      <a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
  @else
    <li class="breadcrumb-item">
      <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
  @endif
  <li class="breadcrumb-item">{{ __('Retainers') }}</li>
@endsection

@section('action-btn')
  <style>
    .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
    .mcheck input{position:absolute;opacity:0;width:0;height:0}
    .mcheck .box{width:20px;height:20px;border:2px solid #dee2e6;border-radius:4px;position:relative;}
    .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
    .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
    .mcheck input:checked + .box{background:#007C38;border-color:#007C38;}
    .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid white;border-top:0;border-left:0;transform:rotate(45deg);}
  </style>

  <div class="flex items-center gap-2 mt-2 sm:mt-0">
    <a href="#" data-size="md" data-bs-toggle="tooltip" title="{{ __('Import') }}"
       data-url="Retailer" data-ajax-popup="true" data-title=""
       style="border: 1px solid #007C38 !important"
       class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 712-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      {{ __('Import') }}
    </a>

    <a href="{{ route('retainer.export') }}" style="border: 1px solid #007C38 !important"
       class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit"
       data-bs-toggle="tooltip" title="{{ __('Export') }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
      </svg>
      {{ __('Export') }}
    </a>

    @can('create retainer')
      <a href="{{ route('retainer.create', 0) }}"
         class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit"
         data-bs-toggle="tooltip" title="{{ __('Create') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        {{ __('Create') }}
      </a>
    @endcan
  </div>
@endsection

@push('css-page')
@endpush

@push('script-page')
@endpush

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="multi-collapse mt-2" id="multiCollapseExample1">
        <div class="card">
          <div class="card-body">
            @if (!\Auth::guard('customer')->check())
              {{ Form::open(['route' => ['retainer.index'], 'method' => 'GET', 'id' => 'frm_submit']) }}
            @else
              {{ Form::open(['route' => ['customer.retainer'], 'method' => 'GET', 'id' => 'frm_submit']) }}
            @endif
            <div class="form-space-fix row d-flex align-items-center">
              <div class="col-md-10 col-12">
                <div class="row">
                  @if (!\Auth::guard('customer')->check())
                    <div class="col-md-4 col-sm-12 col-12">
                      <div class="btn-box">
                        {{ Form::label('customer', __('Customer'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                        {{ Form::select('customer', $customer, request('customer', ''), ['class' => 'form-control select appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full']) }}
                      </div>
                    </div>
                  @endif

                  <div class="col-md-4 col-sm-12 col-12">
                    <div class="btn-box">
                      {{ Form::label('issue_date', __('Date'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                      {{ Form::text('issue_date', request('issue_date', date('Y-m-d')), ['class' => 'form-control appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full month-btn', 'id' => 'pc-daterangepicker-1', 'placeholder' => 'Select Date']) }}
                    </div>
                  </div>

                  <div class="col-md-4 col-sm-12 col-12">
                    <div class="btn-box">
                      {{ Form::label('status', __('Status'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                      {{ Form::select('status', ['' => 'Select Status'] + $status, request('status', ''), ['class' => 'form-control appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] transition-all duration-200 w-full']) }}
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-2 col-12">
                <div class="col-auto d-flex justify-content-end mt-4">
                  <a href="#" class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip"
                     title="{{ __('Apply') }}"
                     onclick="document.getElementById('frm_submit').submit(); return false;">
                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                  </a>

                  @if (\Auth::user()->type == 'company')
                    <a href="{{ route('retainer.index') }}" class="btn btn-sm btn-danger"
                       data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                      <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off "></i></span>
                    </a>
                  @else
                    <a href="{{ route('customer.retainer') }}" class="btn btn-sm btn-danger"
                       data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                      <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off "></i></span>
                    </a>
                  @endif
                </div>
              </div>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Bulk toolbar (shared component) --}}
  @can('delete retainer')
    <x-bulk-toolbar
      :deleteRoute="route('retainer.bulk-destroy')"
      :exportRoute="route('retainer.export-selected')"
      scope="retainers"
      tableId="retainers-table"
      selectedLabel="{{ __('Retainers selected') }}"
    />
  @endcan

  <div class="row">
    <div class="col-md-12">
      <div class="card-body bg-white border border-[#E5E5E5] rounded-[8px] p-4">
        <div class="table-responsive table-new-design">
          <table id="retainers-table" class="table datatable">
            <thead class="bg-[#F6F6F6] text-[#323232] font-600 text-[12px] leading-[24px]">
              <tr>
                @can('delete retainer')
                  <th data-sortable="false" data-type="html"
                      class="input-checkbox border border-[#E5E5E5] px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                    <label class="mcheck">
                      <input type="checkbox" class="jsb-master" data-scope="retainers">
                      <span class="box"></span>
                    </label>
                  </th>
                @endcan

                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
                  {{ __('Retainer') }}
                </th>

                @if (!\Auth::guard('customer')->check())
                  <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
                    {{ __('Customer') }}
                  </th>
                @endif

                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
                  {{ __('Category') }}
                </th>

                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
                  {{ __('Issue Date') }}
                </th>

                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]">
                  {{ __('Status') }}
                </th>

                @if (Gate::check('edit retainer') || Gate::check('delete retainer') || Gate::check('show retainer'))
                  <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600]" width="10%">
                    {{ __('Action') }}
                  </th>
                @endif
              </tr>
            </thead>

            <tbody>
              @foreach ($retainers as $retainer)
                <tr class="font-style">
                  @can('delete retainer')
                    <td class="input-checkbox px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                      <label class="mcheck">
                        <input type="checkbox"
                               class="jsb-item"
                               data-scope="retainers"
                               value="{{ $retainer->id }}"
                               data-id="{{ $retainer->id }}">
                        <span class="box"></span>
                      </label>
                    </td>
                  @endcan

                  <td class="Id px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    @if (\Auth::guard('customer')->check())
                      <a href="{{ route('customer.retainer.show', \Crypt::encrypt($retainer->id)) }}"
                         class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                        {{ AUth::user()->retainerNumberFormat($retainer->retainer_id) }}
                      </a>
                    @else
                      <a href="{{ route('retainer.show', \Crypt::encrypt($retainer->id)) }}"
                         class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                        {{ AUth::user()->retainerNumberFormat($retainer->retainer_id) }}
                      </a>
                    @endif
                  </td>

                  @if (!\Auth::guard('customer')->check())
                    <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                      {{ !empty($retainer->customer) ? $retainer->customer->name : '' }}
                    </td>
                  @endif

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ !empty($retainer->category) ? $retainer->category->name : '' }}
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ Auth::user()->dateFormat($retainer->issue_date) }}
                  </td>

                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    @if ($retainer->status == 0)
                      <span class="badge fix_badges bg-primary p-2 px-3">
                        {{ __(\App\Models\Retainer::$statues[$retainer->status]) }}
                      </span>
                    @elseif($retainer->status == 1)
                      <span class="badge fix_badges bg-info p-2 px-3">
                        {{ __(\App\Models\Retainer::$statues[$retainer->status]) }}
                      </span>
                    @elseif($retainer->status == 2)
                      <span class="badge fix_badges bg-secondary p-2 px-3">
                        {{ __(\App\Models\Retainer::$statues[$retainer->status]) }}
                      </span>
                    @elseif($retainer->status == 3)
                      <span class="badge fix_badges bg-warning p-2 px-3">
                        {{ __(\App\Models\Retainer::$statues[$retainer->status]) }}
                      </span>
                    @elseif($retainer->status == 4)
                      <span class="badge fix_badges bg-danger p-2 px-3">
                        {{ __(\App\Models\Retainer::$statues[$retainer->status]) }}
                      </span>
                    @endif
                  </td>

                  @if (Gate::check('edit retainer') || Gate::check('delete retainer') || Gate::check('show retainer'))
                    <td class="Action border border-[#E5E5E5]">
                      <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer"
                              type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                      </button>

                      <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                        @if ($retainer->is_convert == 0)
                          @can('convert invoice retainer')
                            <li>
                              {!! Form::open([
                                  'method' => 'get',
                                  'route' => ['retainer.convert', $retainer->id],
                                  'id' => 'proposal-form-' . $retainer->id,
                              ]) !!}
                              <a href="#"
                                 class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                                 data-bs-toggle="tooltip"
                                 title="{{ __('Convert into Invoice') }}"
                                 data-confirm="{{ __('You want to confirm convert to invoice. Press Yes to continue or Cancel to go back') }}"
                                 data-confirm-yes="document.getElementById('proposal-form-{{ $retainer->id }}').submit();">
                                <i class="ti ti-exchange"></i>
                                <span>{{ __('Convert to Invoice') }}</span>
                              </a>
                              {!! Form::close() !!}
                            </li>
                          @endcan
                        @else
                          @can('convert invoice retainer')
                            <li>
                              <a href="{{ route('invoice.show', \Crypt::encrypt($retainer->converted_invoice_id)) }}"
                                 class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                 data-bs-toggle="tooltip"
                                 title="{{ __('Already convert to Invoice') }}">
                                <i class="ti ti-file-invoice"></i>
                                <span>{{ __('View Invoice') }}</span>
                              </a>
                            </li>
                          @endcan
                        @endif

                        @can('duplicate retainer')
                          <li>
                            {!! Form::open([
                                'method' => 'get',
                                'route' => ['retainer.duplicate', $retainer->id],
                                'id' => 'duplicate-form-' . $retainer->id,
                            ]) !!}
                            <a href="#"
                               class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                               data-bs-toggle="tooltip" title="{{ __('Duplicate') }}"
                               data-confirm="{{ __('You want to confirm duplicate this invoice. Press Yes to continue or Cancel to go back') }}"
                               data-confirm-yes="document.getElementById('duplicate-form-{{ $retainer->id }}').submit();">
                              <i class="ti ti-copy"></i>
                              <span>{{ __('Duplicate') }}</span>
                            </a>
                            {!! Form::close() !!}
                          </li>
                        @endcan

                        @can('show retainer')
                          @if (\Auth::guard('customer')->check())
                            <li>
                              <a href="{{ route('customer.retainer.show', \Crypt::encrypt($retainer->id)) }}"
                                 class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                 data-bs-toggle="tooltip" title="{{ __('Show') }}">
                                <i class="ti ti-eye"></i>
                                <span>{{ __('Show') }}</span>
                              </a>
                            </li>
                          @else
                            <li>
                              <a href="{{ route('retainer.show', \Crypt::encrypt($retainer->id)) }}"
                                 class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                                 data-bs-toggle="tooltip" title="{{ __('Show') }}">
                                <i class="ti ti-eye"></i>
                                <span>{{ __('Show') }}</span>
                              </a>
                            </li>
                          @endif
                        @endcan

                        @can('edit retainer')
                          <li>
                            <a href="{{ route('retainer.edit', \Crypt::encrypt($retainer->id)) }}"
                               class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm"
                               data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                              <i class="ti ti-pencil"></i>
                              <span>{{ __('Edit') }}</span>
                            </a>
                          </li>
                        @endcan

                        @can('delete retainer')
                          <li>
                            {!! Form::open([
                                'method' => 'DELETE',
                                'route' => ['retainer.destroy', $retainer->id],
                                'id' => 'delete-form-' . $retainer->id,
                            ]) !!}
                            <a href="#"
                               class="dropdown-item flex items-center text-[#323232] gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                               data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                               data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                               data-confirm-yes="document.getElementById('delete-form-{{ $retainer->id }}').submit();">
                              <i class="ti ti-trash"></i>
                              <span>{{ __('Delete') }}</span>
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
@endsection

@push('script-page')
<script>
  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){
    e.stopPropagation();
  });

  (function(){
    const $table = $('#retainers-table');
    const tbody = $table.find('tbody').get(0);
    if (tbody && 'MutationObserver' in window) {
      new MutationObserver(function(){
        setTimeout(function(){ $(document).trigger('retainers-table-updated'); }, 0);
      }).observe(tbody, {childList:true});
    }
  })();
</script>
@endpush
