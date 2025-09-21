@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Bills') }}
@endsection

@section('breadcrumb')
    @if (\Auth::guard('vender')->check())
        <li class="breadcrumb-item"><a href="{{ route('vender.dashboard') }}">{{ __('Dashboard') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    @endif
    <li class="breadcrumb-item">{{ __('Bill') }}</li>
@endsection

@section('action-btn')
<style>
  .sub-title{font-weight:600;background:#F6F6F6;border-radius:6px;padding:6px 15px;margin-bottom:10px;}
  /* Material checkbox */
  .mcheck{display:inline-flex;align-items:center;cursor:pointer;user-select:none}
  .mcheck input{position:absolute;opacity:0;width:0;height:0}
  .mcheck .box{width:20px;height:20px;border:2px solid #D1D5DB;border-radius:6px;background:#fff;display:inline-block;position:relative;transition:all .15s}
  .mcheck .box:hover{box-shadow:0 1px 3px rgba(0,0,0,.08)}
  .mcheck input:focus + .box{box-shadow:0 0 0 3px rgba(0,124,56,.2)}
  .mcheck input:checked + .box{background:#007C38;border-color:#007C38}
  .mcheck input:checked + .box::after{content:"";position:absolute;left:6px;top:2px;width:5px;height:10px;border:2px solid #fff;border-top:none;border-left:none;transform:rotate(45deg)}
</style>

<div class="flex items-center gap-2 mt-2 sm:mt-0">
    {{-- Export: All --}}
    <a href="{{ route('Bill.export') }}"
       style="border: 1px solid #007C38 !important"
       class="flex items-center gap-2 border border-[#007C38] text-[#007C38] bg-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#007C3808] transition-all duration-200 shadow-sm min-w-fit"
       data-bs-toggle="tooltip" title="{{ __('Export') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 712-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        {{ __('Export') }}
    </a>



    @can('create bill')
      <a href="{{ route('bill.create', 0) }}"
         class="flex items-center gap-2 bg-[#007C38] text-white px-4 py-2 rounded-[6px] text-[14px] font-[500] hover:bg-[#005f2a] transition-all duration-200 shadow-sm min-w-fit"
         data-bs-toggle="tooltip" title="{{ __('Create') }}">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          {{ __('Create') }}
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
          @if (!\Auth::guard('vender')->check())
            {{ Form::open(['route' => ['bill.index'], 'method' => 'GET', 'id' => 'frm_submit']) }}
          @else
            {{ Form::open(['route' => ['vender.bill'], 'method' => 'GET', 'id' => 'frm_submit']) }}
          @endif
          <div class="form-space-fix row d-flex align-items-center">
            <div class="col-md-10 col-12">
              <div class="row">
                <div class="col-md-4 col-sm-12 col-12">
                  <div class="btn-box">
                    {{ Form::label('date', __('Date'), ['class' => 'text-type block text-sm font-medium text-gray-700 mb-2']) }}
                    {{ Form::date('bill_date', request('bill_date', date('Y-m-d')), ['class' => 'month-btn form-control appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] duration-200 w-full', 'id' => 'pc-daterangepicker-1', 'placeholder' => 'YYYY-MM-DD']) }}
                  </div>
                </div>

                @if (!\Auth::guard('vender')->check())
                  <div class="col-md-4 col-sm-12 col-12">
                    <div class="btn-box">
                      {{ Form::label('vender', __('Vendor'), ['class' => 'text-type block text-sm font-medium text-gray-700 mb-2']) }}
                      {{ Form::select('vender', $vender, request('vender',''), ['class' => 'form-control appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] duration-200 w-full']) }}
                    </div>
                  </div>
                @endif

                <div class="col-md-4 col-sm-12 col-12">
                  <div class="btn-box">
                    {{ Form::label('status', __('status'), ['class' => 'text-type block text-sm font-medium text-gray-700 mb-2']) }}
                    {{ Form::select('status', ['' => 'Select Status'] + $status, request('status',''), ['class' => 'form-control appearance-none bg-white border border-[#E5E7EB] rounded-[6px] px-3 py-2 pr-8 text-[14px] text-[#374151] focus:outline-none focus:ring-1 focus:ring-[#007C38] focus:border-[#007C38] duration-200 w-full']) }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2 col-12">
              <div class="col-auto d-flex justify-content-end mt-4">
                <a href="#" class="btn btn-sm btn-primary me-2"
                   onclick="document.getElementById('frm_submit').submit(); return false;"
                   data-bs-toggle="tooltip" title="{{ __('Apply') }}">
                  <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                </a>

                @if (!\Auth::guard('vender')->check())
                  <a href="{{ route('bill.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                    <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off"></i></span>
                  </a>
                @else
                  <a href="{{ route('vender.bill') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                    <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off"></i></span>
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

{{-- Bulk toolbar (delete + export-selected) --}}
@can('delete bill')
  <x-bulk-toolbar
    :deleteRoute="route('bill.bulk-destroy')"
    :exportRoute="route('bill.export-selected')"
    scope="bills"
    tableId="bills-table"
    selectedLabel="{{ __('Bill selected') }}"
  />
@endcan

<div class="row">
  <div class="col-md-12">
    <div class="card border-0 rounded-2xl shadow-md overflow-hidden my-3">
      <div class="h-1 w-full" style="background:#007C38;"></div>

    <div class="card-body table-border-style">
      <div class="table-responsive table-new-design bg-white p-4">
        <table id="bills-table" class="table datatable border border-[#E5E5E5] rounded-[8px]">
          <thead>
            <tr>
              {{-- selection --}}
              <th data-sortable="false" data-type="html"
                  class="input-checkbox border border-[#E5E5E5] px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider w-12">
                <label class="mcheck">
                  <input type="checkbox" class="jsb-master" data-scope="bills">
                  <span class="box"></span>
                </label>
              </th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Bill') }}</th>
              @if (!\Auth::guard('vender')->check())
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Vendor') }}</th>
              @endif
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Category') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Bill Date') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Due Date') }}</th>
              <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]">{{ __('Status') }}</th>
              @if (Gate::check('edit bill') || Gate::check('delete bill') || Gate::check('show bill'))
                <th class="px-4 py-1 border border-[#E5E5E5] bg-[#F6F6F6] font-[600] text-[12px]" width="10%">{{ __('Action') }}</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @foreach ($bills as $bill)
              <tr>
                {{-- row checkbox --}}
                <td class="input-checkbox px-4 lg:px-6 py-4 text-left text-[12px] font-[700] text-[#374151] uppercase tracking-wider border-0 w-12">
                  <label class="mcheck">
                    <input type="checkbox"
                           class="jsb-item"
                           data-scope="bills"
                           value="{{ $bill->id }}"
                           data-id="{{ $bill->id }}">
                    <span class="box"></span>
                  </label>
                </td>

                <td class="Id px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  @if (\Auth::guard('vender')->check())
                    <a href="{{ route('vender.bill.show', \Crypt::encrypt($bill->id)) }}"
                       class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                       {{ Auth::user()->billNumberFormat($bill->bill_id) }}
                    </a>
                  @else
                    <a href="{{ route('bill.show', \Crypt::encrypt($bill->id)) }}"
                       class="border border-[#137051] leading-[24px] text-[#137051] rounded-[4px] text-[12px] font-[500] px-5">
                       {{ Auth::user()->billNumberFormat($bill->bill_id) }}
                    </a>
                  @endif
                </td>

                @if (!\Auth::guard('vender')->check())
                  <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    {{ optional($bill->vender)->name }}
                  </td>
                @endif

                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  {{ optional($bill->category)->name }}
                </td>

                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  {{ Auth::user()->dateFormat($bill->bill_date) }}
                </td>

                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  {{ Auth::user()->dateFormat($bill->due_date) }}
                </td>

                <td class="px-4 py-3 border border-[#E5E5E5] text-gray-700">
                  @php $labels = \App\Models\Bill::$statues; @endphp
                  @if ($bill->status == 0)
                    <span class="badge fix_badges bg-secondary p-2 px-3 ">{{ __($labels[$bill->status]) }}</span>
                  @elseif($bill->status == 1)
                    <span class="badge fix_badges bg-warning p-2 px-3 ">{{ __($labels[$bill->status]) }}</span>
                  @elseif($bill->status == 2)
                    <span class="badge fix_badges bg-danger p-2 px-3 ">{{ __($labels[$bill->status]) }}</span>
                  @elseif($bill->status == 3)
                    <span class="badge fix_badges bg-info p-2 px-3 ">{{ __($labels[$bill->status]) }}</span>
                  @elseif($bill->status == 4)
                    <span class="badge fix_badges bg-primary p-2 px-3 ">{{ __($labels[$bill->status]) }}</span>
                  @endif
                </td>

                @if (Gate::check('edit bill') || Gate::check('delete bill') || Gate::check('show bill'))
                  <td class="Action px-4 py-3 border border-[#E5E5E5] text-gray-700">
                    <button class="w-100 text-gray-400 hover:text-gray-600 cursor-pointer" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mt-0 w-[190px] bg-white border rounded-md shadow-lg text-sm p-0">
                      @can('duplicate bill')
                        <li>
                          {!! Form::open(['method' => 'get', 'route' => ['bill.duplicate', $bill->id], 'id' => 'duplicate-form-' . $bill->id]) !!}
                          <a href="#" class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para"
                             data-bs-toggle="tooltip" title="{{ __('Duplicate Bill') }}"
                             data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back"
                             data-confirm-yes="document.getElementById('duplicate-form-{{ $bill->id }}').submit();">
                            <i class="ti ti-copy"></i><span>{{ __('Duplicate') }}</span>
                          </a>
                          {!! Form::close() !!}
                        </li>
                      @endcan

                      @can('show bill')
                        @if (\Auth::guard('vender')->check())
                          <li>
                            <a href="{{ route('vender.bill.show', \Crypt::encrypt($bill->id)) }}"
                               class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm">
                              <i class="ti ti-eye"></i><span>{{ __('Show') }}</span>
                            </a>
                          </li>
                        @else
                          <li>
                            <a href="{{ route('bill.show', \Crypt::encrypt($bill->id)) }}"
                               class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm">
                              <i class="ti ti-eye"></i><span>{{ __('Show') }}</span>
                            </a>
                          </li>
                        @endif
                      @endcan

                      @can('edit bill')
                        <li>
                          <a href="{{ route('bill.edit', \Crypt::encrypt($bill->id)) }}"
                             class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm">
                            <i class="ti ti-pencil"></i><span>{{ __('Edit') }}</span>
                          </a>
                        </li>
                      @endcan

                      @can('delete bill')
                        <li>
                          {!! Form::open(['method' => 'DELETE', 'route' => ['bill.destroy', $bill->id], 'id' => 'delete-form-' . $bill->id]) !!}
                          <a href="#!" class="dropdown-item flex items-center text-gray-700 gap-2 w-full px-4 py-2 text-left hover:bg-[#007C3812] text-sm bs-pass-para">
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
  // Prevent clicks on controls from toggling row selection or navigation
  $(document).on('click', 'input[type=checkbox], label.mcheck, .dropdown-menu, [data-bs-toggle="dropdown"]', function(e){ e.stopPropagation(); });

  // Top toolbar "Export Selected" uses same selection list
  $(document).on('click','[data-export-selected][data-scope="bills"]',function(e){
    e.preventDefault();
    const scope = $(this).data('scope'); // "bills"
    const route = $(this).data('route');
    const key   = 'bulk:'+scope;
    let ids = [];
    try { ids = JSON.parse(localStorage.getItem(key) || '[]'); } catch(e) {}

    if(!ids.length){
      if(typeof Swal !== 'undefined'){
        Swal.fire({ icon:'info', title:'{{ __('No selection') }}', text:'{{ __('Please select at least one row.') }}' });
      }else{
        alert('{{ __('Please select at least one row.') }}');
      }
      return;
    }

    const token = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
    const $f = $('<form>', { method:'POST', action:route, target:'_blank' });
    $f.append($('<input>',{type:'hidden', name:'_token', value:token}));
    // same param name used by server: ids[]
    ids.forEach(id => $f.append($('<input>',{type:'hidden', name:'ids[]', value:id})));
    $(document.body).append($f);
    $f.trigger('submit').remove();
  });
</script>
@endpush
